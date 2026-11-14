<?php

namespace Tests\Feature\Contact;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Contact\Mail\MessageReceived;
use Modules\Contact\Mail\NewContactMessageAlert;
use Modules\Contact\Models\ContactMessage;
use Modules\Core\Services\SettingsService;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'topic' => 'partnership',
            'name' => 'Arif Rahman',
            'email' => 'Arif@Example.org',
            'phone' => '+880 1811 000000',
            'organization' => 'Example Foundation',
            'subject' => 'Co-hosting a dialogue',
            'message' => 'We would love to co-host a policy dialogue with you next spring.',
        ], $this->formProtection(), $overrides);
    }

    /** Model attributes (payload without form-only fields). */
    protected function attrs(array $overrides = []): array
    {
        return \Illuminate\Support\Arr::except($this->validPayload($overrides), ['hp_company_website', 'hp_rendered']);
    }

    public function test_contact_page_shows_configured_channels_only(): void
    {
        $this->get(route('contact.create'))
            ->assertOk()
            ->assertSee('hello@deltainitiatives.com')
            ->assertSee('ideas@deltainitiatives.com')
            ->assertSee('partners@deltainitiatives.com')
            ->assertDontSee('tel:', false)
            ->assertDontSee('Follow our work');
    }

    public function test_phone_and_social_links_appear_once_configured(): void
    {
        app(SettingsService::class)->set([
            'contact_phone' => '+880 2 5566 7788',
            'social_linkedin' => 'https://www.linkedin.com/company/delta-initiatives',
        ]);

        $this->get(route('contact.create'))
            ->assertOk()
            ->assertSee('tel:+880255667788', false)
            ->assertSee('Follow our work')
            ->assertSee('https://www.linkedin.com/company/delta-initiatives', false);
    }

    public function test_topic_query_string_preselects_topic(): void
    {
        $this->get(route('contact.create', ['topic' => 'media']))
            ->assertOk()
            ->assertSee('<option value="media" selected', false);
    }

    public function test_message_is_stored_and_both_mails_are_queued(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), $this->validPayload())
            ->assertRedirect(route('contact.create').'#message')
            ->assertSessionHas('success');

        $message = ContactMessage::query()->sole();
        $this->assertSame('arif@example.org', $message->email);
        $this->assertSame('new', $message->status);
        $this->assertSame('partnership', $message->topic);

        Mail::assertQueued(MessageReceived::class, fn ($mail) => $mail->hasTo('arif@example.org'));
        Mail::assertQueued(NewContactMessageAlert::class, fn ($mail) => $mail->hasTo(setting('contact_email')) && $mail->hasReplyTo('arif@example.org'));
    }

    public function test_mails_render(): void
    {
        $message = ContactMessage::query()->create($this->attrs(['status' => 'new', 'locale' => 'en']));

        $this->assertStringContainsString($message->reference(), (new MessageReceived($message))->render());
        $this->assertStringContainsString('co-host a policy dialogue', (new NewContactMessageAlert($message))->render());
    }

    public function test_validation_errors_are_reported(): void
    {
        Mail::fake();

        $this->from(route('contact.create'))
            ->post(route('contact.store'), $this->validPayload(['topic' => 'spam', 'email' => 'nope', 'message' => 'hi', 'phone' => 'call me']))
            ->assertRedirect(route('contact.create'))
            ->assertSessionHasErrors(['topic', 'email', 'message', 'phone']);

        $this->assertSame(0, ContactMessage::query()->count());
        Mail::assertNothingQueued();
    }

    public function test_honeypot_and_time_trap_reject_bots(): void
    {
        $this->from(route('contact.create'))
            ->post(route('contact.store'), $this->validPayload(['hp_company_website' => 'buy-now']))
            ->assertSessionHasErrors('hp_company_website');

        $this->from(route('contact.create'))
            ->post(route('contact.store'), array_merge($this->validPayload(), ['hp_rendered' => 'tampered']))
            ->assertSessionHasErrors('hp_rendered');

        $this->assertSame(0, ContactMessage::query()->count());
    }

    public function test_opening_a_message_marks_it_read_and_is_audited(): void
    {
        $message = ContactMessage::query()->create($this->attrs(['status' => 'new', 'locale' => 'en']));
        $this->actingAsRole('community');

        $this->get(route('admin.resources.index', 'messages'))
            ->assertOk()
            ->assertSee('Co-hosting a dialogue');

        $this->get(route('admin.resources.show', ['messages', $message->id]))
            ->assertOk()
            ->assertSee($message->reference())
            ->assertSee('We would love to co-host');

        $this->assertSame('read', $message->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'resource.viewed',
            'subject_type' => $message->getMorphClass(),
            'subject_id' => $message->id,
        ]);
    }

    public function test_status_and_notes_can_be_updated(): void
    {
        $message = ContactMessage::query()->create($this->attrs(['status' => 'read', 'locale' => 'en']));
        $this->actingAsRole('admin');

        $this->put(route('admin.resources.update', ['messages', $message->id]), ['status' => 'replied', 'admin_notes' => 'Call booked.'])
            ->assertRedirect(route('admin.resources.show', ['messages', $message->id]));

        $this->assertSame('replied', $message->fresh()->status);
        $this->assertSame('Call booked.', $message->fresh()->admin_notes);
    }

    public function test_community_role_can_export_csv_and_xlsx(): void
    {
        ContactMessage::query()->create($this->attrs(['status' => 'new', 'locale' => 'en']));
        $this->actingAsRole('community');

        $csv = $this->get(route('admin.resources.export', ['messages', 'format' => 'csv', 'topic' => 'partnership']));
        $csv->assertOk();
        $this->assertStringContainsString('MSG-00001', $csv->streamedContent());
        $this->assertStringContainsString('Arif Rahman', $csv->streamedContent());

        $this->get(route('admin.resources.export', ['messages', 'format' => 'xlsx']))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->assertDatabaseHas('audit_logs', ['action' => 'resource.exported']);
    }

    public function test_reviewer_and_guest_cannot_access_messages(): void
    {
        $message = ContactMessage::query()->create($this->attrs(['status' => 'new', 'locale' => 'en']));

        $this->get(route('admin.resources.show', ['messages', $message->id]))->assertRedirect(route('admin.login'));

        $this->actingAsRole('reviewer');
        $this->get(route('admin.resources.show', ['messages', $message->id]))->assertForbidden();
        $this->assertSame('new', $message->fresh()->status);
    }

    public function test_demo_seeder_is_idempotent(): void
    {
        $this->seed(\Modules\Contact\Seeders\ContactSeeder::class);
        $this->seed(\Modules\Contact\Seeders\ContactSeeder::class);

        $this->assertSame(2, ContactMessage::query()->demo()->count());
    }
}
