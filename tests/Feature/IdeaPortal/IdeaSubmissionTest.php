<?php

namespace Tests\Feature\IdeaPortal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\AuditLog;
use Modules\Core\Services\SettingsService;
use Modules\IdeaPortal\Mail\IdeaReceiptMail;
use Modules\IdeaPortal\Mail\NewIdeaAlertMail;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Services\FingerprintService;
use Modules\Users\Models\User;
use Tests\Feature\IdeaPortal\Concerns\InteractsWithIdeas;
use Tests\TestCase;

class IdeaSubmissionTest extends TestCase
{
    use InteractsWithIdeas, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
        Mail::fake();
        $this->createFocusArea();
    }

    public function test_the_portal_renders_in_english_and_bangla(): void
    {
        $this->get(route('ideas.create'))
            ->assertOk()
            ->assertSee('Submit an Idea')
            ->assertSee('Your idea stays yours.')
            ->assertSee('name="agreement"', false)
            ->assertSee('Climate &amp; Water Resilience', false)
            ->assertSee('hreflang="bn"', false);

        $this->get(route('bn.ideas.create'))
            ->assertOk()
            ->assertSee('<html lang="bn">', false)
            ->assertSee('আইডিয়া জমা দিন')
            ->assertSee('আপনার আইডিয়া আপনারই থাকবে।')
            ->assertSee(route('bn.ideas.store'), false);
    }

    public function test_a_submission_is_numbered_fingerprinted_stored_privately_and_receipted(): void
    {
        $admin = User::factory()->role('admin')->create();        // has ideas.notify
        $reviewer = User::factory()->role('reviewer')->create();  // does not
        $year = now()->setTimezone('Asia/Dhaka')->format('Y');

        $response = $this->post(route('ideas.store'), $this->formPayload(['files' => [$this->pdf('pitch deck.pdf')]]));

        $response->assertRedirect(route('ideas.received'))->assertSessionHasNoErrors();

        $idea = IdeaSubmission::query()->with('files')->sole();
        $this->assertSame("DI-{$year}-0001", $idea->code);
        $this->assertSame('received', $idea->status);
        $this->assertSame('en', $idea->locale);
        $this->assertFalse($idea->is_demo);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $idea->fingerprint);
        $this->assertSame(hash('sha256', $idea->fingerprint_payload), $idea->fingerprint);
        $this->assertTrue(app(FingerprintService::class)->verify($idea)['valid']);

        // File: private disk under ideas/{code}/, hashed, never on the public disk.
        $file = $idea->files->sole();
        $this->assertSame('pitch deck.pdf', $file->original_name);
        $this->assertStringStartsWith("ideas/{$idea->code}/", $file->stored_path);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $file->sha256);
        Storage::disk('local')->assertExists($file->stored_path);
        Storage::disk('public')->assertMissing($file->stored_path);
        $this->assertStringContainsString($file->sha256, $idea->fingerprint_payload);

        // Receipt to the lead; minimal alert only to accounts with ideas.notify.
        Mail::assertSent(IdeaReceiptMail::class, fn ($mail) => $mail->hasTo('lead@example.com') && $mail->idea->is($idea));
        Mail::assertSent(NewIdeaAlertMail::class, fn ($mail) => $mail->hasTo($admin->email));
        Mail::assertNotSent(NewIdeaAlertMail::class, fn ($mail) => $mail->hasTo($reviewer->email));
        $idea->refresh();
        $this->assertNotNull($idea->receipt_sent_at);
        $this->assertNotNull($idea->team_notified_at);

        $this->assertTrue(AuditLog::query()->where('action', 'idea.submitted')->where('subject_id', $idea->id)->exists());

        // The receipt page shows the proof once.
        $this->get(route('ideas.received'))
            ->assertOk()
            ->assertSee($idea->code)
            ->assertSee($idea->fingerprint)
            ->assertSee('lead@example.com');

        // Second submission gets the next number.
        $this->post(route('ideas.store'), $this->formPayload(['title' => 'Second idea', 'lead_email' => 'two@example.com']))
            ->assertRedirect(route('ideas.received'));
        $this->assertSame("DI-{$year}-0002", IdeaSubmission::query()->latest('id')->value('code'));
    }

    public function test_the_receipt_email_renders_with_id_time_and_fingerprint(): void
    {
        $idea = $this->makeIdea();

        $html = (new IdeaReceiptMail($idea))->render();

        $this->assertStringContainsString($idea->code, $html);
        $this->assertStringContainsString($idea->fingerprint, $html);
        $this->assertStringContainsString('BST', $html);

        $alert = (new \Modules\IdeaPortal\Mail\NewIdeaAlertMail($idea))->render();
        $this->assertStringContainsString($idea->code, $alert);
        $this->assertStringNotContainsString($idea->answer('solution'), $alert);
    }

    public function test_a_bangla_submission_keeps_its_locale_and_redirects_to_the_bangla_receipt(): void
    {
        $this->post(route('bn.ideas.store'), $this->formPayload())->assertRedirect(route('bn.ideas.received'));

        $idea = IdeaSubmission::query()->sole();
        $this->assertSame('bn', $idea->locale);
        Mail::assertSent(IdeaReceiptMail::class, fn ($mail) => $mail->locale === 'bn');

        $this->get(route('bn.ideas.received'))->assertOk()->assertSee('আপনার রসিদ')->assertSee($idea->code);

        // Validation messages are in Bangla on the Bangla form.
        $this->post(route('bn.ideas.store'), $this->formPayload(['title' => '']))->assertSessionHasErrors('title');
        $this->assertStringContainsString('পূরণ করুন', session('errors')->first('title'));
    }

    public function test_consent_to_the_contributor_agreement_is_required(): void
    {
        $payload = $this->formPayload();
        unset($payload['agreement']);

        $this->from(route('ideas.create'))->post(route('ideas.store'), $payload)
            ->assertRedirect(route('ideas.create'))
            ->assertSessionHasErrors('agreement');

        $this->assertSame(0, IdeaSubmission::query()->count());
        Mail::assertNothingSent();
    }

    public function test_required_answers_are_validated(): void
    {
        $this->post(route('ideas.store'), $this->formPayload(['problem' => 'Too short', 'stage' => 'nonsense', 'lead_email' => 'not-an-email']))
            ->assertSessionHasErrors(['problem', 'stage', 'lead_email']);

        $this->assertSame(0, IdeaSubmission::query()->count());
    }

    public function test_too_many_files_are_rejected(): void
    {
        $files = [$this->pdf('a.pdf'), $this->pdf('b.pdf'), $this->pdf('c.pdf'), $this->pdf('d.pdf')];

        $this->post(route('ideas.store'), $this->formPayload(['files' => $files]))->assertSessionHasErrors('files');

        $this->assertSame(0, IdeaSubmission::query()->count());
    }

    public function test_oversized_and_disallowed_files_are_rejected(): void
    {
        $this->post(route('ideas.store'), $this->formPayload(['files' => [$this->pdf('huge.pdf', 10241)]]))
            ->assertSessionHasErrors('files.0');

        $this->post(route('ideas.store'), $this->formPayload(['files' => [UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload')]]))
            ->assertSessionHasErrors('files.0');

        $this->assertSame(0, IdeaSubmission::query()->count());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_the_honeypot_rejects_bots(): void
    {
        $this->post(route('ideas.store'), $this->formPayload(['hp_company_website' => 'http://spam.example']))
            ->assertSessionHasErrors('hp_company_website');

        $this->assertSame(0, IdeaSubmission::query()->count());
    }

    public function test_the_receipt_page_cannot_be_opened_without_a_fresh_submission(): void
    {
        $this->get(route('ideas.received'))->assertRedirect(route('ideas.create'));
        $this->get(route('bn.ideas.received'))->assertRedirect(route('bn.ideas.create'));
    }

    public function test_submissions_can_be_paused_from_settings(): void
    {
        app(SettingsService::class)->set(['ideas_open' => '0']);

        $this->get(route('ideas.create'))->assertOk()->assertSee('Submissions are paused')->assertDontSee('name="agreement"', false);

        $this->post(route('ideas.store'), $this->formPayload())->assertRedirect(route('ideas.create'));
        $this->assertSame(0, IdeaSubmission::query()->count());
    }

    public function test_tampering_with_a_record_is_detected(): void
    {
        $idea = $this->makeIdea();
        $this->assertTrue(app(FingerprintService::class)->verify($idea)['valid']);

        $answers = $idea->answers;
        $answers['solution'] .= '!';
        $idea->forceFill(['answers' => $answers])->save();

        $check = app(FingerprintService::class)->verify($idea->fresh());
        $this->assertTrue($check['payload_intact']);
        $this->assertFalse($check['record_matches']);
        $this->assertFalse($check['valid']);
    }

    public function test_receipts_can_be_verified_publicly_without_revealing_content(): void
    {
        $idea = $this->makeIdea();

        $this->get(route('ideas.verify'))->assertOk()->assertSee('Verify an idea receipt');

        $this->post(route('ideas.verify.check'), ['code' => strtolower($idea->code), 'fingerprint' => strtoupper($idea->fingerprint)] + $this->formProtection())
            ->assertRedirect(route('ideas.verify'));
        $this->get(route('ideas.verify'))->assertSee('Receipt verified')->assertDontSee($idea->title);

        $this->post(route('ideas.verify.check'), ['code' => $idea->code, 'fingerprint' => str_repeat('a', 64)] + $this->formProtection());
        $this->get(route('ideas.verify'))->assertSee('No match');
    }

    public function test_failed_notifications_are_retried_by_the_command(): void
    {
        $idea = $this->makeIdea();
        $idea->forceFill(['submitted_at' => now()->subMinutes(10)])->save();
        User::factory()->role('admin')->create();

        $this->artisan('ideas:retry-notifications')->assertSuccessful();

        Mail::assertSent(IdeaReceiptMail::class);
        Mail::assertSent(NewIdeaAlertMail::class);
        $this->assertNotNull($idea->fresh()->receipt_sent_at);
    }
}
