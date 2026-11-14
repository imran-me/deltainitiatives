<?php

namespace Modules\Programmes\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\EventPhoto;
use Modules\Programmes\Models\Programme;

class ProgrammeController extends Controller
{
    public function index(): View
    {
        return view('programmes::index', [
            'programmes' => Programme::query()->published()->ordered()->get(),
            'upcoming' => Event::query()->published()->upcoming()
                ->with('programme')->withRegisteredCount()
                ->orderBy('starts_at')->get(),
            'past' => Event::query()->published()->past()
                ->with('programme')
                ->orderByDesc('starts_at')->limit(6)->get(),
            'photos' => EventPhoto::query()
                ->whereHas('event', fn ($q) => $q->published())
                ->with('event:id,title,slug,starts_at')
                ->orderBy('sort_order')->latest('id')->limit(12)->get(),
        ]);
    }
}
