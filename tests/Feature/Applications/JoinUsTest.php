<?php

namespace Tests\Feature\Applications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\Applications\Mail\ApplicationReceived;
use Modules\Applications\Mail\NewApplicationAlert;
use Modules\Applications\Models\JoinApplication;
use Tests\TestCase;

class JoinUsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'role' => 'fellow',
            'name' => 'Nusrat Jahan',
            'email' => 'Nusrat@Example.com',
            'phone' => '+880 1711-000000',
            'institution' => 'University of Dhaka',
            'study_level' => 'postgraduate',
            'department' => 'Environmental Science',
            'city' => 'Dhaka',
            'linkedin_url' => 'https://www.linkedin.com/in/example',
            'skills' => 'GIS, Python, field research',
            'motivation' => str_repeat('I want to build a flood early-warning tool for coastal communities. ', 2),
            'availability' => '7-10',
            'consent' => '1',
        ], $this->formProtection(), $overrides);
    }

    /** Model attributes (payload without form-only fields). */
    protected function attrs(array $overrides = []): array
    {
        return \Illuminate\Support\Arr::except($this->validPayload($overrides), ['hp_company_website', 'hp_rendered', 'consent']);
    }

    public function test_join_page_renders_in_english(): void
    {
        $this->get(route('join.create'))
            ->assertOk()
            ->assertSee('Find the role that fits you')
            ->assertSee('Campus Ambassador')
            ->assertSee('name="consent"', false)
            ->assertSee('hreflang="bn"', false);
    }

    public function test_join_page_renders_in_bangla(): void
    {
        $this->get(route('bn.join.create'))
            ->assertOk()
            ->assertSee('lang="bn"', false)
            ->assertSee('যুক্ত হতে আবেদন করুন')
            ->assertSee('ক্যাম্পাস অ্যাম্বাসেডর')
            ->assertSee(route('bn.join.store'), false);
    }

    public function test_role_query_string_preselects_the_role(): void
    {
        $this->get(route('join.create', ['role' => 'volunteer']))
            ->assertOk()
            ->assertSee('<option value="volunteer" selected', false);
    }

    public function test_application_with_cv_is_stored_privately_and_mails_are_queued(): void
    {
        Mail::fake();
        Storage::fake('local');
        Storage::fake('public');

        $this->post(route('join.store'), $this->validPayload([
            'cv' => UploadedFile::fake()->create('my-cv.pdf', 300, 'application/pdf'),
        ]))->assertRedirect(route('join.create').'#apply')->assertSessionHas('success');

        $application = JoinApplication::query()->sole();
        $this->assertSame('nusrat@example.com', $application->email);
        $this->assertSame('fellow', $application->role);
        $this->assertSame('new', $application->status);
        $this->assertSame('en', $application->locale);
        $this->assertNotNull($application->consent_at);
        $this->assertFalse($application->is_demo);
        $this->assertStringStartsWith('applications/', $application->cv_path);
        Storage::disk('local')->assertExists($application->cv_path);
        $this->assertSame([], Storage::disk('public')->allFiles());

        Mail::assertQueued(ApplicationReceived::class, fn ($mail) => $mail->hasTo('nusrat@example.com'));
        Mail::assertQueued(NewApplicationAlert::class, fn ($mail) => $mail->hasTo(setting('contact_email')) && $mail->hasReplyTo('nusrat@example.com'));
    }

    public function test_bangla_submission_records_bangla_locale_and_redirects_to_bangla_page(): void
    {
        Mail::fake();

        $this->post(route('bn.join.store'), $this->validPayload(['phone' => '০১৭১১-০০০০০০']))
            ->assertRedirect(route('bn.join.create').'#apply');

        $application = JoinApplication::query()->sole();
        $this->assertSame('bn', $application->locale);
        $this->assertNull($application->cv_path);

        Mail::assertQueued(ApplicationReceived::class, fn ($mail) => $mail->locale === 'bn');
    }

    public function test_acknowledgement_mail_renders_in_bangla(): void
    {
        $application = JoinApplication::query()->create($this->attrs(['email' => 'a@example.com', 'locale' => 'bn', 'status' => 'new']));
        $application->refresh();

        $html = (new ApplicationReceived($application))->render();

        $this->assertStringContainsString($application->reference(), $html);
        $this->assertStringContainsString('আবেদন', $html);
    }

    public function test_validation_errors_are_reported(): void
    {
        Mail::fake();

        $this->from(route('join.create'))
            ->post(route('join.store'), $this->validPayload([
                'role' => 'astronaut',
                'email' => 'not-an-email',
                'motivation' => 'Too short',
                'consent' => null,
                'cv' => UploadedFile::fake()->create('cv.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ]))
            ->assertRedirect(route('join.create'))
            ->assertSessionHasErrors(['role', 'email', 'motivation', 'consent', 'cv']);

        $this->assertSame(0, JoinApplication::query()->count());
        Mail::assertNothingQueued();
    }

    public function test_bangla_validation_messages_are_in_bangla(): void
    {
        $this->from(route('bn.join.create'))
            ->post(route('bn.join.store'), $this->validPayload(['name' => '']))
            ->assertSessionHasErrors(['name' => 'পূর্ণ নাম পূরণ করা আবশ্যক।']);
    }

    public function test_honeypot_rejects_bots(): void
    {
        $this->from(route('join.create'))
            ->post(route('join.store'), $this->validPayload(['hp_company_website' => 'https://spam.example']))
            ->assertSessionHasErrors('hp_company_website');

        $this->assertSame(0, JoinApplication::query()->count());
    }

    public function test_cv_download_requires_inbox_permission_and_is_audited(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('applications/test.pdf', '%PDF-1.4 test');
        $application = JoinApplication::query()->create($this->attrs(['email' => 'a@example.com', 'cv_path' => 'applications/test.pdf', 'locale' => 'en', 'status' => 'new']));

        $this->actingAsRole('reviewer');
        $this->get(route('admin.applications.cv', $application))->assertForbidden();

        $this->actingAsRole('community');
        $this->get(route('admin.applications.cv', $application))
            ->assertOk()
            ->assertDownload();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'application.cv_downloaded',
            'subject_id' => $application->id,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $application = JoinApplication::query()->create($this->attrs(['email' => 'a@example.com', 'locale' => 'en', 'status' => 'new']));

        $this->get(route('admin.applications.cv', $application))->assertRedirect(route('admin.login'));
        $this->get(route('admin.resources.index', 'join-applications'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_list_view_update_and_export_applications(): void
    {
        $application = JoinApplication::query()->create($this->attrs(['email' => 'a@example.com', 'locale' => 'en', 'status' => 'new']));
        $this->actingAsRole('community');

        $this->get(route('admin.resources.index', ['join-applications', 'role' => 'fellow']))
            ->assertOk()
            ->assertSee('Nusrat Jahan');

        $this->get(route('admin.resources.show', ['join-applications', $application->id]))
            ->assertOk()
            ->assertSee($application->reference())
            ->assertSee('Not provided');

        $this->assertDatabaseHas('audit_logs', ['action' => 'resource.viewed', 'subject_id' => $application->id]);

        $this->put(route('admin.resources.update', ['join-applications', $application->id]), [
            'status' => 'reviewing',
            'admin_notes' => 'Strong candidate.',
        ])->assertRedirect(route('admin.resources.show', ['join-applications', $application->id]));

        $this->assertSame('reviewing', $application->fresh()->status);

        $csv = $this->get(route('admin.resources.export', ['join-applications', 'format' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('a@example.com', $csv->streamedContent());
        $this->assertStringContainsString('JOIN-', $csv->streamedContent());

        $this->get(route('admin.resources.export', ['join-applications', 'format' => 'xlsx']))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_reviewer_cannot_open_the_applications_inbox(): void
    {
        $this->actingAsRole('reviewer');

        $this->get(route('admin.resources.index', 'join-applications'))->assertForbidden();
    }

    public function test_deleting_an_application_removes_its_cv(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('applications/x.pdf', '%PDF');
        $application = JoinApplication::query()->create($this->attrs(['email' => 'a@example.com', 'cv_path' => 'applications/x.pdf', 'locale' => 'en', 'status' => 'new']));

        $application->delete();

        Storage::disk('local')->assertMissing('applications/x.pdf');
    }

    public function test_demo_seeder_is_idempotent(): void
    {
        $this->seed(\Modules\Applications\Seeders\ApplicationsSeeder::class);
        $this->seed(\Modules\Applications\Seeders\ApplicationsSeeder::class);

        $this->assertSame(3, JoinApplication::query()->demo()->count());
    }
}
