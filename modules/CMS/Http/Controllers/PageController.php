<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\CMS\Models\Arm;
use Modules\CMS\Models\CoreValue;
use Modules\CMS\Models\FocusArea;
use Modules\CMS\Models\Partner;
use Modules\CMS\Models\PipelineStep;
use Modules\CMS\Models\Stat;
use Modules\CMS\Models\TeamMember;

class PageController extends Controller
{
    public function home(): View
    {
        return view('cms::home', [
            'arms' => Arm::query()->published()->ordered()->get(),
            'steps' => PipelineStep::query()->published()->ordered()->get(),
            'focusAreas' => FocusArea::query()->published()->ordered()->get(),
            'stats' => Stat::forHomepage(),
            'partners' => Partner::query()->displayable()->ordered()->get(),
            'numbers' => $this->liveNumbers(),
        ]);
    }

    /** Live counts for the home numbers band; any failure falls back to a sensible figure. */
    private function liveNumbers(): array
    {
        $count = function (callable $q): int {
            try { $n = (int) $q(); } catch (\Throwable) { $n = 0; }
            return $n;
        };

        return [
            'ideas' => $count(fn () => \Modules\IdeaPortal\Models\IdeaSubmission::query()->where('is_demo', false)->count()),
            'contributors' => $count(fn () => \Modules\Publishing\Models\Person::query()->where('is_published', true)->count()),
            'partners' => $count(fn () => Partner::query()->displayable()->count()),
            'projects' => $count(fn () => \Modules\Programmes\Models\Programme::query()->count()),
        ];
    }

    public function about(): View
    {
        $people = TeamMember::query()->published()->ordered()->get()->groupBy('group');

        return view('cms::about', [
            'values' => CoreValue::query()->published()->ordered()->get(),
            'team' => $people->get('team', collect()),
            'advisors' => $people->get('advisor', collect()),
        ]);
    }

    public function whatWeDo(): View
    {
        return view('cms::what-we-do', [
            'arms' => Arm::query()->published()->ordered()->get(),
            'steps' => PipelineStep::query()->published()->ordered()->get(),
            'focusAreas' => FocusArea::query()->published()->ordered()->get(),
        ]);
    }
}
