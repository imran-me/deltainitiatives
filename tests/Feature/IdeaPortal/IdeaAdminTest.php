<?php

namespace Tests\Feature\IdeaPortal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\AuditLog;
use Modules\IdeaPortal\Models\IdeaScore;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Services\FingerprintService;
use Tests\Feature\IdeaPortal\Concerns\InteractsWithIdeas;
use Tests\TestCase;

class IdeaAdminTest extends TestCase
{
    use InteractsWithIdeas, RefreshDatabase;

    protected IdeaSubmission $idea;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Mail::fake();
        $this->createFocusArea();
        $this->idea = $this->makeIdea([], [$this->pdf('evidence.pdf')]);
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get(route('admin.ideas.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.ideas.show', $this->idea->code))->assertRedirect(route('admin.login'));
    }

    public function test_accounts_without_idea_access_are_forbidden(): void
    {
        $this->actingAsRole('editor');

        $this->get(route('admin.ideas.index'))->assertForbidden();
        $this->get(route('admin.ideas.show', $this->idea->code))->assertForbidden();
        $this->get(route('admin.ideas.files.download', [$this->idea->code, $this->idea->files->first()->id]))->assertForbidden();
        $this->get(route('admin.ideas.export'))->assertForbidden();
    }

    public function test_reviewers_can_list_filter_and_view_ideas_and_views_are_audited(): void
    {
        $reviewer = $this->actingAsRole('reviewer');
        $other = $this->makeIdea(['title' => 'Compost loops', 'lead_email' => 'other@example.com']);

        $this->get(route('admin.ideas.index'))->assertOk()->assertSee($this->idea->code)->assertSee($other->code);
        $this->get(route('admin.ideas.index', ['q' => 'Compost']))->assertOk()->assertSee($other->code)->assertDontSee($this->idea->code);
        $this->get(route('admin.ideas.index', ['status' => 'screening']))->assertOk()->assertDontSee($this->idea->code);
        $this->get(route('admin.ideas.index', ['scored' => 'mine', 'sort' => 'score']))->assertOk()->assertSee($this->idea->code);

        $this->get(route('admin.ideas.show', $this->idea->code))
            ->assertOk()
            ->assertSee($this->idea->title)
            ->assertSee($this->idea->fingerprint)
            ->assertSee('Record unchanged since submission')
            ->assertSee('evidence.pdf')
            ->assertSee('Your score');

        $this->assertTrue(AuditLog::query()->where('action', 'idea.viewed')->where('user_id', $reviewer->id)->where('subject_id', $this->idea->id)->exists());
    }

    public function test_reviewers_can_score_ideas_and_the_average_is_recalculated(): void
    {
        $reviewer = $this->actingAsRole('reviewer');
        $scores = ['problem_significance' => 5, 'originality' => 4, 'feasibility' => 3, 'impact_potential' => 4, 'team_capability' => 4, 'alignment' => 5];

        $this->post(route('admin.ideas.score', $this->idea->code), ['scores' => $scores, 'comment' => 'Promising.'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $score = IdeaScore::query()->sole();
        $this->assertSame($reviewer->id, (int) $score->user_id);
        $this->assertEquals(4.17, (float) $score->average);
        $this->assertEquals(4.17, (float) $this->idea->fresh()->average_score);
        $this->assertSame(1, $this->idea->fresh()->scores_count);

        // Updating keeps one row per reviewer.
        $this->post(route('admin.ideas.score', $this->idea->code), ['scores' => array_map(fn () => 2, $scores)]);
        $this->assertSame(1, IdeaScore::query()->count());
        $this->assertEquals(2.0, (float) $this->idea->fresh()->average_score);

        // A second reviewer's score is averaged in.
        $this->actingAsRole('reviewer');
        $this->post(route('admin.ideas.score', $this->idea->code), ['scores' => array_map(fn () => 4, $scores)]);
        $this->assertEquals(3.0, (float) $this->idea->fresh()->average_score);
        $this->assertSame(2, $this->idea->fresh()->scores_count);

        $this->assertSame(3, AuditLog::query()->where('action', 'idea.scored')->count());
    }

    public function test_scores_must_cover_every_criterion_within_range(): void
    {
        $this->actingAsRole('reviewer');

        $this->post(route('admin.ideas.score', $this->idea->code), ['scores' => ['problem_significance' => 6]])
            ->assertSessionHasErrors(['scores.problem_significance', 'scores.originality']);

        $this->assertSame(0, IdeaScore::query()->count());
    }

    public function test_reviewers_move_ideas_through_the_workflow_without_breaking_the_fingerprint(): void
    {
        $reviewer = $this->actingAsRole('reviewer');

        $this->post(route('admin.ideas.status', $this->idea->code), ['status' => 'screening', 'note' => 'Looks complete.'])
            ->assertRedirect(route('admin.ideas.show', $this->idea->code));
        $this->post(route('admin.ideas.status', $this->idea->code), ['status' => 'shortlisted']);

        $idea = $this->idea->fresh();
        $this->assertSame('shortlisted', $idea->status);
        $this->assertSame(2, $idea->statusChanges()->count());
        $this->assertDatabaseHas('idea_status_changes', ['to_status' => 'screening', 'from_status' => 'received', 'note' => 'Looks complete.', 'user_id' => $reviewer->id]);
        $this->assertSame(2, AuditLog::query()->where('action', 'idea.status_changed')->count());
        $this->assertTrue(app(FingerprintService::class)->verify($idea)['valid']);

        $this->post(route('admin.ideas.status', $this->idea->code), ['status' => 'approved'])->assertSessionHasErrors('status');
    }

    public function test_reviewers_cannot_export(): void
    {
        $this->actingAsRole('reviewer');

        $this->get(route('admin.ideas.export'))->assertForbidden();
        $this->get(route('admin.ideas.index'))->assertDontSee('CSV');
    }

    public function test_admins_can_export_csv_and_excel(): void
    {
        $this->actingAsRole('admin');

        $csv = $this->get(route('admin.ideas.export', ['format' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('.csv', (string) $csv->headers->get('content-disposition'));
        $body = $csv->streamedContent();
        $this->assertStringContainsString('Idea ID', $body);
        $this->assertStringContainsString($this->idea->code, $body);
        $this->assertStringContainsString($this->idea->fingerprint, $body);

        // Filters apply to exports.
        $filtered = $this->get(route('admin.ideas.export', ['format' => 'csv', 'status' => 'in_delta_labs']))->streamedContent();
        $this->assertStringNotContainsString($this->idea->code, $filtered);

        if (! class_exists(\ZipArchive::class)) {
            $this->markTestSkipped('zip extension not available for XLSX export.');
        }

        $xlsx = $this->get(route('admin.ideas.export', ['format' => 'xlsx']));
        $xlsx->assertOk();
        $this->assertStringContainsString('spreadsheetml', (string) $xlsx->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', (string) $xlsx->headers->get('content-disposition'));

        $this->assertSame(3, AuditLog::query()->where('action', 'idea.exported')->count());
    }

    public function test_file_downloads_require_permission_and_are_audited(): void
    {
        $file = $this->idea->files->first();
        $reviewer = $this->actingAsRole('reviewer');

        $response = $this->get(route('admin.ideas.files.download', [$this->idea->code, $file->id]));
        $response->assertOk();
        $this->assertStringContainsString('evidence.pdf', (string) $response->headers->get('content-disposition'));

        $this->assertTrue(AuditLog::query()->where('action', 'idea.file_downloaded')->where('user_id', $reviewer->id)->exists());

        // A file can only be fetched through its own idea.
        $other = $this->makeIdea(['lead_email' => 'x@example.com']);
        $this->get(route('admin.ideas.files.download', [$other->code, $file->id]))->assertNotFound();
    }

    public function test_the_dashboard_shows_idea_widgets(): void
    {
        $this->actingAsRole('admin');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Latest ideas')
            ->assertSee('New ideas')
            ->assertSee($this->idea->code);
    }

    public function test_purging_sample_content_removes_sample_ideas_and_their_files(): void
    {
        $sample = app(\Modules\IdeaPortal\Services\SubmissionService::class)->submit(
            $this->ideaData(['lead_email' => 's@example.org']),
            [$this->pdf('sample.pdf')],
            null,
            ['is_demo' => true, 'notify' => false, 'code' => 'DI-SAMPLE-0001'],
        );
        $path = $sample->files->first()->stored_path;
        Storage::disk('local')->assertExists($path);

        app(\Modules\Admin\Services\DemoContent::class)->purge();

        $this->assertNull(IdeaSubmission::query()->find($sample->id));
        $this->assertNotNull(IdeaSubmission::query()->find($this->idea->id));
        Storage::disk('local')->assertMissing($path);
    }
}
