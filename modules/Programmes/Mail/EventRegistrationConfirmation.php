<?php

namespace Modules\Programmes\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Programmes\Models\EventRegistration;

class EventRegistrationConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public EventRegistration $registration)
    {
        $this->locale($registration->locale ?: 'en');
    }

    public function envelope(): Envelope
    {
        $this->registration->loadMissing('event');
        $prefix = $this->registration->isWaitlisted() ? 'You’re on the waitlist' : 'You’re registered';

        return new Envelope(
            subject: $prefix.': '.$this->registration->event->title,
            replyTo: [new Address((string) setting('contact_email'), (string) setting('site_name'))],
        );
    }

    public function content(): Content
    {
        $this->registration->loadMissing('event');

        return new Content(view: 'programmes::mail.registration', with: [
            'registration' => $this->registration,
            'event' => $this->registration->event,
        ]);
    }
}
