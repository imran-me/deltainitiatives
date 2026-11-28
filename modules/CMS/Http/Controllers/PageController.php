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
            'faces' => $this->faces(),
        ]);
    }

    /** Published contributors for the home strip (pinned top contributors first). */
    private function faces(): \Illuminate\Support\Collection
    {
        try {
            $rows = \Modules\Publishing\Models\Person::query()->published()->ordered()->where('kind', 'contributor')
                ->with(['ideas' => fn ($q) => $q->visible()->withCount('votes')])->get()
                ->map(fn ($p) => ['person' => $p, 'points' => $p->points()])->sortByDesc('points')->values();
            $pinned = $rows->filter(fn ($r) => $r['person']->is_top_contributor)->values();

            return ($pinned->isNotEmpty() ? $pinned : $rows)->take(6);
        } catch (\Throwable) {
            return collect();
        }
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
            'contributors' => $count(fn () => \Modules\Publishing\Models\Person::query()->where('is_published', true)->where('is_demo', false)->count()),
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
