<?php

namespace Modules\Applications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Applications\Models\JoinApplication;

/** Acknowledgement to the applicant, in the language they applied in. */
class ApplicationReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public JoinApplication $application)
    {
        $this->locale($application->locale ?: 'en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('applications::join.mail.subject', ['reference' => $this->application->reference()]));
    }

    public function content(): Content
    {
        return new Content(view: 'applications::mail.received', with: [
            'application' => $this->application,
        ]);
    }
}
