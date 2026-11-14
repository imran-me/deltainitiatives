<?php

namespace Modules\Programmes\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\EventRegistration;

class EventController extends Controller
{
    public function show(Event $event): View
    {
        abort_unless($event->is_published, 404);

        $event->load(['programme', 'photos']);
        $event->loadCount(['registrations as registered_count' => fn ($q) => $q->where('status', 'registered')]);

        return view('programmes::event', [
            'event' => $event,
            'state' => $event->registrationState(),
            'occupations' => EventRegistration::OCCUPATIONS,
            'more' => Event::query()->published()->upcoming()->whereKeyNot($event->getKey())
                ->with('programme')->withRegisteredCount()->orderBy('starts_at')->limit(3)->get(),
        ]);
    }
}
