<?php

namespace Tests\Feature\Partnerships;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\AuditLog;
use Modules\Core\Services\SettingsService;
use Modules\Partnerships\Mail\PartnerInquiryAcknowledgement;
use Modules\Partnerships\Mail\PartnerInquiryAlert;
use Modules\Partnerships\Models\PartnerInquiry;
use Modules\Partnerships\Models\PartnershipType;
use Modules\Partnerships\Seeders\PartnershipsSeeder;
use Tests\TestCase;

class PartnershipsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PartnershipsSeeder::class);
    }

    protected function payload(array $overrides = []): array
    {
        return $overrides + [
            'organization' => 'Padma Analytics Ltd',
            'contact_name' => 'Nusrat Jahan',
            'job_title' => 'Head of Partnerships',
            'email' => 'Nusrat@Padma.example.com',
            'phone' => '+880 1711 111111',
            'website' => 'https://padma.example.com',
            'country' => 'Bangladesh',
            'partnership_type' => 'industry-corporate',
            'message' => 'We would like to sponsor a hackathon track on supply-chain data next spring.',
            'consent' => '1',
        ] + $this->formProtection();
    }

    public function test_partner_page_renders_types_and_brochure_request(): void
    {
        $this->get(route('partner.index'))
            ->assertOk()
            ->assertSee('Why partner with us')
            ->assertSee('Research &amp; Academic', false)
            ->assertSee('Media &amp; Community', false)
            ->assertSee('Request the brochure')
            ->assertSee('data-preselect="government-policy"', false);
    }

    public function test_type_query_parameter_preselects_the_form(): void
    {
        $this->get(route('partner.index', ['type' => 'government-policy']))
            ->assertOk()
            ->assertSee('value="government-policy" selected', false);

        $this->get(route('partner.index', ['type' => 'nonsense']))
            ->assertOk()
            ->assertDontSee('selected>', false);
    }

    public function test_inquiry_is_stored_and_both_mails_are_queued(): void
    {
        Mail::fake();

        $this->post(route('partner.store'), $this->payload())
            ->assertRedirect(route('partner.index').'#inquiry')
            ->assertSessionHas('success');

        $inquiry = PartnerInquiry::query()->where('organization', 'Padma Analytics Ltd')->sole();
        $this->assertSame('nusrat@padma.example.com', $inquiry->email);
        $this->assertSame('new', $inquiry->status);
        $this->assertFalse($inquiry->is_demo);
        $this->assertSame(PartnershipType::query()->where('slug', 'industry-corporate')->value('id'), $inquiry->partnership_type_id);

        Mail::assertQueued(PartnerInquiryAcknowledgement::class, fn ($mail) => $mail->hasTo('nusrat@padma.example.com'));
        Mail::assertQueued(PartnerInquiryAlert::class, fn ($mail) => $mail->hasTo('partners@deltainitiatives.com'));
    }

    public function test_mails_render(): void
    {
        $inquiry = PartnerInquiry::query()->create([
            'organization' => 'Render Co', 'contact_name' => 'Tester', 'email' => 'r@example.com',
            'partnership_type_id' => PartnershipType::query()->value('id'), 'message' => 'Hello there, this is a message.',
        ]);

        $this->assertStringContainsString('Render Co', (new PartnerInquiryAcknowledgement($inquiry))->render());
        $this->assertStringContainsString('Open in the admin panel', (new PartnerInquiryAlert($inquiry))->render());
    }

    public function test_validation_errors_return_to_the_form(): void
    {
        Mail::fake();

        $this->post(route('partner.store'), $this->payload(['organization' => '', 'message' => 'Too short', 'partnership_type' => 'not-a-type']))
            ->assertRedirect(route('partner.index').'#inquiry')
            ->assertSessionHasErrors(['organization', 'message', 'partnership_type']);

        $this->assertSame(0, PartnerInquiry::query()->real()->count());
        Mail::assertNothingQueued();
    }

    public function test_honeypot_rejects_bots(): void
    {
        Mail::fake();

        $this->post(route('partner.store'), $this->payload(['hp_company_website' => 'http://bot.example']))
            ->assertSessionHasErrors('hp_company_website');

        $this->assertSame(0, PartnerInquiry::query()->real()->count());
        Mail::assertNothingQueued();
    }

    public function test_missing_brochure_redirects_to_the_form(): void
    {
        $this->get(route('partner.brochure'))
            ->assertRedirect(route('partner.index').'#inquiry')
            ->assertSessionHas('warning');
    }

    public function test_uploaded_brochure_is_streamed(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('brochures/brochure.pdf', '%PDF-1.4 test');
        app(SettingsService::class)->set(['brochure_path' => 'brochures/brochure.pdf']);

        $this->get(route('partner.index'))->assertSee('Download the brochure');

        $response = $this->get(route('partner.brochure'));
        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Delta-Initiatives-Partnership-Brochure.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_inquiries_require_authentication_and_inbox_permission(): void
    {
        $this->get(route('admin.resources.index', 'partner-inquiries'))->assertRedirect(route('admin.login'));

        $this->actingAsRole('reviewer');
        $this->get(route('admin.resources.index', 'partner-inquiries'))->assertForbidden();
    }

    public function test_community_role_can_read_update_and_export_inquiries(): void
    {
        $inquiry = PartnerInquiry::query()->demo()->firstOrFail();
        $this->actingAsRole('community');

        $this->get(route('admin.resources.index', 'partner-inquiries'))->assertOk()->assertSee('Riverbank Foundation');

        $this->get(route('admin.resources.show', ['partner-inquiries', $inquiry->id]))->assertOk()->assertSee($inquiry->email);
        $this->assertTrue(AuditLog::query()->where('action', 'resource.viewed')->exists());

        $this->put(route('admin.resources.update', ['partner-inquiries', $inquiry->id]), [
            'status' => 'in_conversation', 'admin_notes' => 'Call booked.',
        ])->assertRedirect(route('admin.resources.show', ['partner-inquiries', $inquiry->id]));
        $this->assertSame('in_conversation', $inquiry->fresh()->status);

        $csv = $this->get(route('admin.resources.export', ['partner-inquiries', 'format' => 'csv']));
        $csv->assertOk()->assertDownload();
        $this->assertStringContainsString('Riverbank Foundation', $csv->streamedContent());

        // Partnership types are content, not inbox.
        $this->get(route('admin.resources.index', 'partnership-types'))->assertForbidden();
    }

    public function test_editor_manages_partnership_types_but_not_inquiries(): void
    {
        $this->actingAsRole('editor');

        $this->get(route('admin.resources.index', 'partnership-types'))->assertOk()->assertSee('Corporate')->assertSee('Government');
        $this->get(route('admin.resources.index', 'partner-inquiries'))->assertForbidden();
    }
}
