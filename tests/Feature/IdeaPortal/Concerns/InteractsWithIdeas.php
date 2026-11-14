<?php

namespace Tests\Feature\IdeaPortal\Concerns;

use Illuminate\Http\UploadedFile;
use Modules\CMS\Models\FocusArea;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Services\SubmissionService;

trait InteractsWithIdeas
{
    protected function createFocusArea(): FocusArea
    {
        return FocusArea::query()->create([
            'name' => 'Climate & Water Resilience',
            'slug' => 'climate-water',
            'icon' => 'waves',
            'summary' => 'Living with rivers, floods and salinity.',
            'sort_order' => 1,
            'is_published' => true,
        ]);
    }

    /** @return array<string, mixed> */
    protected function ideaData(array $overrides = []): array
    {
        $long = 'This is a sufficiently detailed answer that explains the point clearly and specifically.';

        return array_merge([
            'lead_name' => 'Ayesha Rahman',
            'lead_email' => 'lead@example.com',
            'lead_phone' => '+880 1700 000000',
            'institution' => 'Example University',
            'team_name' => 'Team River',
            'team_size' => 3,
            'team_members' => "Karim — engineering\nNadia — research",
            'focus_area' => 'climate-water',
            'title' => 'Voice flood alerts',
            'summary' => 'Automated Bangla voice calls warning char households about floods.',
            'problem' => $long,
            'evidence' => $long,
            'solution' => $long,
            'novelty' => $long,
            'impact' => $long,
            'stage' => 'prototype',
            'support' => $long,
            'next_90_days' => $long,
        ], $overrides);
    }

    /** Full public form payload including consent and anti-spam fields. */
    protected function formPayload(array $overrides = []): array
    {
        return array_merge($this->ideaData(), ['agreement' => '1'], $this->formProtection(), $overrides);
    }

    protected function pdf(string $name = 'deck.pdf', int $kb = 120): UploadedFile
    {
        return UploadedFile::fake()->create($name, $kb, 'application/pdf');
    }

    /** Create a submission directly through the service (no e-mails). */
    protected function makeIdea(array $overrides = [], array $files = []): IdeaSubmission
    {
        return app(SubmissionService::class)->submit($this->ideaData($overrides), $files, null, ['notify' => false]);
    }
}
