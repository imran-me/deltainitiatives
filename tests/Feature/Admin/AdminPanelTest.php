<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\Models\TeamMember;
use Modules\CMS\Seeders\CMSSeeder;
use Modules\Core\Models\AuditLog;
use Modules\Core\Services\SettingsService;
use Modules\Newsletter\Models\Subscriber;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CMSSeeder::class);
    }

    public function test_every_role_reaches_a_dashboard_tailored_to_its_permissions(): void
    {
        foreach (['super_admin', 'admin', 'reviewer', 'editor', 'community'] as $role) {
            $this->asRole($role)->get('/admin')->assertOk()->assertSee('Dashboard');
        }

        $this->asRole('reviewer')->get('/admin')->assertDontSee('Team &amp; advisors', false);
        $this->asRole('editor')->get('/admin')->assertSee('Team &amp; advisors', false);
    }

    public function test_editors_manage_content_through_resources_with_uploads_and_audit(): void
    {
        Storage::fake('public');
        $this->actingAsRole('editor');

        $this->get('/admin/manage/team')->assertOk()->assertSee('Head of Delta Labs');
        $this->get('/admin/manage/team/create')->assertOk();

        $this->post('/admin/manage/team', [
            'name' => 'Farhana Rahman',
            'role' => 'Research Fellow',
            'group' => 'team',
            'sort_order' => 9,
            'is_published' => '1',
            'photo_path' => UploadedFile::fake()->image('farhana.jpg', 800, 800),
        ])->assertRedirect(route('admin.resources.index', 'team'));

        $member = TeamMember::query()->where('name', 'Farhana Rahman')->firstOrFail();
        Storage::disk('public')->assertExists($member->photo_path);
        $this->assertTrue(AuditLog::query()->where('action', 'resource.created')->exists());

        $this->put("/admin/manage/team/{$member->id}", [
            'name' => 'Farhana Rahman', 'role' => 'Senior Research Fellow', 'group' => 'team', 'sort_order' => 9, 'is_published' => '1',
        ])->assertRedirect();
        $this->assertSame('Senior Research Fellow', $member->fresh()->role);

        $this->delete("/admin/manage/team/{$member->id}")->assertRedirect();
        $this->assertModelMissing($member);
        Storage::disk('public')->assertMissing($member->photo_path);
    }

    public function test_resource_validation_rejects_bad_input(): void
    {
        $this->actingAsRole('editor');

        $this->post('/admin/manage/team', ['name' => '', 'role' => 'X', 'group' => 'nonsense', 'linkedin_url' => 'javascript:alert(1)'])
            ->assertSessionHasErrors(['name', 'group', 'linkedin_url']);
    }

    public function test_reviewers_cannot_touch_content(): void
    {
        $this->actingAsRole('reviewer');

        $this->get('/admin/manage/team')->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
        $this->get('/admin/audit-log')->assertForbidden();
        $this->get('/admin/backups')->assertForbidden();
    }

    public function test_settings_can_be_updated_by_admins(): void
    {
        $this->actingAsRole('admin');

        $this->get('/admin/settings')->assertOk();
        $this->put('/admin/settings', ['contact_phone' => '+880 2 0000 0000', 'ideas_open' => '0', 'social_linkedin' => 'https://www.linkedin.com/company/delta'])
            ->assertSessionHasNoErrors()->assertRedirect();

        $settings = app(SettingsService::class);
        $settings->flush();
        $this->assertSame('+880 2 0000 0000', $settings->get('contact_phone'));
        $this->assertFalse($settings->bool('ideas_open', true));
        $this->assertTrue(AuditLog::query()->where('action', 'settings.updated')->exists());
    }

    public function test_audit_log_is_searchable_and_exportable(): void
    {
        $this->actingAsRole('admin');
        audit('test.event', null, 'Something notable happened');

        $this->get('/admin/audit-log?q=notable')->assertOk()->assertSee('Something notable happened');
        $this->get('/admin/audit-log/export?format=csv')->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->get('/admin/audit-log/export?format=xlsx')->assertOk();
    }

    public function test_newsletter_subscribers_export_as_csv_and_xlsx(): void
    {
        Subscriber::query()->create(['email' => 'a@example.com', 'status' => 'subscribed', 'confirmed_at' => now()]);
        $this->actingAsRole('community');

        $csv = $this->get('/admin/manage/subscribers/export?format=csv')->assertOk();
        $this->assertStringContainsString('a@example.com', $csv->streamedContent());

        $this->get('/admin/manage/subscribers/export?format=xlsx')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_super_admin_can_purge_sample_content(): void
    {
        $this->actingAsRole('super_admin');
        $this->assertGreaterThan(0, TeamMember::query()->demo()->count());

        $this->get('/admin')->assertSee('sample records are live on the site');
        $this->delete('/admin/demo-content')->assertRedirect(route('admin.dashboard'));

        $this->assertSame(0, TeamMember::query()->demo()->count());
    }

    public function test_backups_can_be_created_and_downloaded(): void
    {
        Storage::fake('local');
        $this->actingAsRole('super_admin');

        $this->post('/admin/backups')->assertSessionHas('success');
        $name = basename(Storage::disk('local')->files('backups')[0]);

        $this->get('/admin/backups')->assertOk()->assertSee($name);
        $this->get('/admin/backups/'.$name)->assertOk()->assertDownload($name);
        $this->get('/admin/backups/..%2F.env')->assertNotFound();
    }
}
