<?php

namespace Modules\Programmes\Http\Controllers;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Programmes\Http\Requests\EventRegistrationRequest;
use Modules\Programmes\Mail\EventRegistrationConfirmation;
use Modules\Programmes\Models\Event;
use Modules\Programmes\Models\EventRegistration;
use Throwable;

class EventRegistrationController extends Controller
{
    public function store(EventRegistrationRequest $request, Event $event): RedirectResponse
    {
        abort_unless($event->is_published, 404);

        $back = redirect()->to(route('events.show', $event).'#register');

        $message = match ($event->registrationState()) {
            Event::STATE_PAST => 'This event has already taken place, so registration is closed.',
            Event::STATE_CLOSED => 'Registration for this event is closed.',
            Event::STATE_DEADLINE => 'The registration deadline for this event has passed.',
            default => null,
        };

        if ($message) {
            return $back->with('error', $message);
        }

        $data = $request->payload();
        $email = mb_strtolower(trim($data['email']));

        if ($event->registrations()->where('email', $email)->exists()) {
            return $back->with('warning', 'You are already registered for this event with '.$email.'. Check your inbox for the confirmation, or reply to it if anything has changed.');
        }

        try {
            $registration = DB::transaction(function () use ($event, $data, $email, $request) {
                // Lock the event row so two people can't take the last seat at once.
                $locked = Event::query()->whereKey($event->getKey())->lockForUpdate()->firstOrFail();
                $full = $locked->capacity !== null
                    && $locked->registrations()->where('status', 'registered')->count() >= $locked->capacity;

                return $locked->registrations()->create([
                    'name' => trim($data['name']),
                    'email' => $email,
                    'phone' => $data['phone'] ?? null,
                    'institution' => $data['institution'] ?? null,
                    'occupation' => $data['occupation'],
                    'notes' => $data['notes'] ?? null,
                    'status' => $full ? 'waitlisted' : 'registered',
                    'locale' => app()->getLocale(),
                    'ip_address' => $request->ip(),
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            return $back->with('warning', 'You are already registered for this event with '.$email.'.');
        }

        try {
            Mail::to($registration->email)->queue(new EventRegistrationConfirmation($registration));
        } catch (Throwable $e) {
            report($e);
        }

        return $back->with('success', $registration->isWaitlisted()
            ? 'This event is full, so you are on the waitlist. We have e-mailed you, and will write again if a seat opens up.'
            : 'You are registered! A confirmation is on its way to '.$registration->email.'.');
    }
}
