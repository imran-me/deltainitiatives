<?php

namespace Tests\Feature\Programmes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Admin\Services\DemoContent;
use Modules\Programmes\Mail\EventRegistrationConfirmation;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\EventRegistration;
use Modules\Programmes\Models\Programme;
use Modules\Programmes\Seeders\ProgrammesSeeder;
use Tests\TestCase;

class ProgrammesTest extends TestCase
{
    use RefreshDatabase;

    protected function makeEvent(array $attributes = []): Event
    {
        return Event::query()->create($attributes + [
            'title' => 'Test Event '.Str::random(4),
            'slug' => 'test-event-'.Str::lower(Str::random(6)),
            'summary' => 'A short summary of the test event.',
            'starts_at' => now()->addDays(7),
            'ends_at' => now()->addDays(7)->addHours(2),
            'mode' => 'in_person',
            'venue' => 'Main Hall',
            'city' => 'Dhaka',
            'registration_open' => true,
            'is_published' => true,
        ]);
    }

    protected function registrationPayload(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Ayesha Rahman',
            'email' => 'Ayesha@Example.com',
            'phone' => '+880 1700 000000',
            'institution' => 'University of Dhaka',
            'occupation' => 'student',
            'notes' => 'Wheelchair access, please.',
            'consent' => '1',
        ] + $this->formProtection();
    }

    public function test_programmes_page_renders_programme_types_and_events(): void
    {
        $this->seed(ProgrammesSeeder::class);

        $this->get(route('programmes.index'))
            ->assertOk()
            ->assertSee('Six ways to take part')
            ->assertSee('Delta Challenge')
            ->assertSee('Demo Day')
            ->assertSee('Delta Challenge 2027 — Launch Session')
            ->assertSee('Past events')
            ->assertSee('Sample');
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(ProgrammesSeeder::class);
        $this->seed(ProgrammesSeeder::class);

        $this->assertSame(6, Programme::query()->count());
        $this->assertSame(5, Event::query()->count());
        $this->assertSame(3, EventRegistration::query()->count());
    }

    public function test_event_page_renders_with_structured_data(): void
    {
        $event = $this->makeEvent(['title' => 'Flood Data Talk', 'body' => "## Agenda\n\nOpening remarks"]);

        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('Flood Data Talk')
            ->assertSee('Agenda')
            ->assertSee('Save your seat')
            ->assertSee('"@type":"Event"', false);
    }

    public function test_unpublished_event_is_not_found(): void
    {
        $event = $this->makeEvent(['is_published' => false]);

        $this->get(route('events.show', $event))->assertNotFound();
        $this->post(route('events.register', $event), $this->registrationPayload())->assertNotFound();
    }

    public function test_visitor_can_register_and_receives_confirmation(): void
    {
        Mail::fake();
        $event = $this->makeEvent();

        $this->post(route('events.register', $event), $this->registrationPayload())
            ->assertRedirect(route('events.show', $event).'#register')
            ->assertSessionHas('success');

        $registration = EventRegistration::query()->sole();
        $this->assertSame('ayesha@example.com', $registration->email);
        $this->assertSame('registered', $registration->status);
        $this->assertSame($event->id, $registration->event_id);

        Mail::assertQueued(EventRegistrationConfirmation::class, fn ($mail) => $mail->hasTo('ayesha@example.com'));
    }

    public function test_confirmation_mail_renders(): void
    {
        $event = $this->makeEvent(['mode' => 'online', 'online_note' => 'Link sent the day before.']);
        $registration = $event->registrations()->create(['name' => 'Test', 'email' => 't@example.com', 'occupation' => 'student', 'status' => 'registered']);

        $html = (new EventRegistrationConfirmation($registration))->render();

        $this->assertStringContainsString($event->title, $html);
        $this->assertStringContainsString('Link sent the day before.', $html);
    }

    public function test_duplicate_registration_is_not_created(): void
    {
        Mail::fake();
        $event = $this->makeEvent();
        $event->registrations()->create(['name' => 'Ayesha', 'email' => 'ayesha@example.com', 'occupation' => 'student', 'status' => 'registered']);

        $this->post(route('events.register', $event), $this->registrationPayload())
            ->assertRedirect(route('events.show', $event).'#register')
            ->assertSessionHas('warning');

        $this->assertSame(1, EventRegistration::query()->count());
        Mail::assertNothingQueued();
    }

    public function test_full_event_puts_people_on_the_waitlist(): void
    {
        Mail::fake();
        $event = $this->makeEvent(['capacity' => 1]);
        $event->registrations()->create(['name' => 'First', 'email' => 'first@example.com', 'occupation' => 'student', 'status' => 'registered']);

        $this->get(route('events.show', $event))->assertSee('Join the waitlist');

        $this->post(route('events.register', $event), $this->registrationPayload())
            ->assertSessionHas('success', fn ($message) => str_contains($message, 'waitlist'));

        $this->assertSame('waitlisted', EventRegistration::query()->where('email', 'ayesha@example.com')->value('status'));
        Mail::assertQueued(EventRegistrationConfirmation::class);
    }

    public function test_closed_deadline_passed_and_past_events_reject_registrations(): void
    {
        Mail::fake();
        $closed = $this->makeEvent(['registration_open' => false]);
        $deadline = $this->makeEvent(['registration_deadline' => now()->subDay()]);
        $past = $this->makeEvent(['starts_at' => now()->subDays(10), 'ends_at' => now()->subDays(10)->addHours(2)]);

        foreach ([$closed, $deadline, $past] as $event) {
            $this->post(route('events.register', $event), $this->registrationPayload())
                ->assertRedirect(route('events.show', $event).'#register')
                ->assertSessionHas('error');
        }

        $this->get(route('events.show', $past))->assertOk()->assertSee('This event has taken place');
        $this->assertSame(0, EventRegistration::query()->count());
        Mail::assertNothingQueued();
    }

    public function test_registration_requires_consent_and_valid_fields(): void
    {
        $event = $this->makeEvent();

        $this->post(route('events.register', $event), $this->registrationPayload(['consent' => null, 'email' => 'not-an-email', 'occupation' => 'pilot']))
            ->assertRedirect(route('events.show', $event).'#register')
            ->assertSessionHasErrors(['consent', 'email', 'occupation']);

        $this->assertSame(0, EventRegistration::query()->count());
    }

    public function test_honeypot_blocks_bots(): void
    {
        $event = $this->makeEvent();

        $this->post(route('events.register', $event), $this->registrationPayload(['hp_company_website' => 'https://spam.example']))
            ->assertSessionHasErrors('hp_company_website');

        $this->assertSame(0, EventRegistration::query()->count());
    }

    public function test_upcoming_events_component_renders_empty_state(): void
    {
        $this->makeEvent(['starts_at' => now()->subMonth(), 'ends_at' => now()->subMonth()->addHour(), 'title' => 'Old Event']);

        $this->blade('<x-programmes::upcoming-events />')
            ->assertSee('Upcoming events')
            ->assertSee('The next dates are being finalised')
            ->assertDontSee('Old Event');
    }

    public function test_upcoming_events_component_lists_next_events(): void
    {
        $this->makeEvent(['title' => 'Soonest Event', 'starts_at' => now()->addDays(2), 'ends_at' => null]);
        $this->makeEvent(['title' => 'Hidden Event', 'is_published' => false]);

        $this->blade('<x-programmes::upcoming-events />')
            ->assertSee('Soonest Event')
            ->assertDontSee('Hidden Event')
            ->assertDontSee('The next dates are being finalised');
    }

    public function test_admin_registrations_require_authentication_and_permission(): void
    {
        $this->get(route('admin.resources.index', 'event-registrations'))->assertRedirect(route('admin.login'));

        $this->actingAsRole('reviewer');
        $this->get(route('admin.resources.index', 'event-registrations'))->assertForbidden();
        $this->get(route('admin.resources.index', 'events'))->assertForbidden();
    }

    public function test_community_role_can_view_and_export_registrations(): void
    {
        $event = $this->makeEvent(['title' => 'Export Me']);
        $registration = $event->registrations()->create(['name' => 'Rafi', 'email' => 'rafi@example.com', 'occupation' => 'researcher', 'status' => 'registered']);

        $this->actingAsRole('community');

        $this->get(route('admin.resources.index', ['event-registrations', 'event_id' => $event->id]))
            ->assertOk()->assertSee('Rafi');

        $this->get(route('admin.resources.show', ['event-registrations', $registration->id]))
            ->assertOk()->assertSee('rafi@example.com');

        $csv = $this->get(route('admin.resources.export', ['event-registrations', 'format' => 'csv', 'event_id' => $event->id]));
        $csv->assertOk()->assertDownload();
        $this->assertStringContainsString('Export Me', $csv->streamedContent());

        $this->get(route('admin.resources.export', ['event-registrations', 'format' => 'xlsx']))->assertOk()->assertDownload();

        $this->put(route('admin.resources.update', ['event-registrations', $registration->id]), ['status' => 'attended'])
            ->assertRedirect(route('admin.resources.show', ['event-registrations', $registration->id]));
        $this->assertSame('attended', $registration->fresh()->status);
    }

    public function test_editor_can_manage_programmes_and_events(): void
    {
        $this->actingAsRole('editor');

        $this->post(route('admin.resources.store', 'programmes'), [
            'name' => 'Policy Lab', 'slug' => 'policy-lab', 'tagline' => 'Short sprints', 'cadence' => 'Quarterly',
            'icon' => 'landmark', 'sort_order' => 70, 'summary' => 'A new programme.', 'body' => '', 'is_published' => '1',
        ])->assertRedirect(route('admin.resources.index', 'programmes'));

        $programme = Programme::query()->where('slug', 'policy-lab')->sole();

        $this->post(route('admin.resources.store', 'events'), [
            'title' => 'Policy Lab Kick-off', 'slug' => 'policy-lab-kick-off', 'programme_id' => $programme->id,
            'summary' => 'First session.', 'body' => '', 'starts_at' => now('Asia/Dhaka')->addDays(9)->format('Y-m-d\T15:00'),
            'ends_at' => now('Asia/Dhaka')->addDays(9)->format('Y-m-d\T17:00'), 'mode' => 'hybrid', 'city' => 'Dhaka', 'venue' => 'Room 101',
            'online_note' => '', 'registration_open' => '1', 'capacity' => '40', 'registration_deadline' => '', 'is_published' => '1',
        ])->assertRedirect(route('admin.resources.index', 'events'));

        $event = Event::query()->where('slug', 'policy-lab-kick-off')->sole();
        $this->assertSame(40, $event->capacity);
        $this->assertSame('15:00', $event->localStart()->format('H:i'));

        $this->get(route('admin.resources.index', 'events'))->assertOk()->assertSee('Policy Lab Kick-off');
        $this->get(route('events.show', $event))->assertOk()->assertSee('40 of 40 seats left');

        // programmes.manage covers registrations, but not the partnership inbox.
        $this->get(route('admin.resources.index', 'event-registrations'))->assertOk();
        $this->get(route('admin.resources.index', 'partner-inquiries'))->assertForbidden();
    }

    public function test_demo_purge_removes_sample_events_and_registrations(): void
    {
        $this->seed(ProgrammesSeeder::class);
        $real = $this->makeEvent(['title' => 'Real Event']);

        app(DemoContent::class)->purge();

        $this->assertSame(0, Event::query()->where('is_demo', true)->count());
        $this->assertSame(0, EventRegistration::query()->where('is_demo', true)->count());
        $this->assertTrue($real->fresh() !== null);
        $this->assertSame(6, Programme::query()->count());
    }

    public function test_sitemap_lists_programmes_and_events(): void
    {
        $event = $this->makeEvent(['slug' => 'sitemap-event']);

        $this->get(route('sitemap'))->assertOk()
            ->assertSee(route('programmes.index'), false)
            ->assertSee(route('events.show', $event), false);
    }
}
