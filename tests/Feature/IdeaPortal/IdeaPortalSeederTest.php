<?php

namespace Tests\Feature\IdeaPortal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Seeders\IdeaPortalSeeder;
use Modules\IdeaPortal\Services\FingerprintService;
use Modules\IdeaPortal\Services\IdeaIdGenerator;
use Modules\Users\Models\User;
use Tests\Feature\IdeaPortal\Concerns\InteractsWithIdeas;
use Tests\TestCase;

class IdeaPortalSeederTest extends TestCase
{
    use InteractsWithIdeas, RefreshDatabase;

    public function test_sample_ideas_are_seeded_once_with_valid_fingerprints_and_do_not_use_the_real_sequence(): void
    {
        Mail::fake();
        $this->createFocusArea();
        User::factory()->role('reviewer')->count(2)->create();

        $this->seed(IdeaPortalSeeder::class);
        $this->seed(IdeaPortalSeeder::class); // idempotent

        $samples = IdeaSubmission::query()->with('files')->get();
        $this->assertCount(6, $samples);
        $this->assertTrue($samples->every(fn ($idea) => $idea->is_demo && str_starts_with($idea->code, 'DI-SAMPLE-') && str_starts_with($idea->title, '[Sample]')));
        $this->assertTrue($samples->every(fn ($idea) => app(FingerprintService::class)->verify($idea)['valid']));
        $this->assertSame(['in_delta_labs', 'shortlisted', 'screening', 'received', 'screening', 'not_progressed'], $samples->sortBy('id')->pluck('status')->all());
        $this->assertGreaterThan(0, IdeaSubmission::query()->where('scores_count', '>', 0)->count());
        Mail::assertNothingSent();

        // The first real idea is still DI-{year}-0001.
        $year = now()->setTimezone('Asia/Dhaka')->format('Y');
        $this->assertSame("DI-{$year}-0001", app(IdeaIdGenerator::class)->next());
    }
}
