<?php

namespace Modules\Applications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Applications\Models\JoinApplication;

/** Team alert for a new Join Us application (reply goes straight to the applicant). */
class NewApplicationAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public JoinApplication $application)
    {
        $this->locale('en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Join Us application: '.$this->application->roleLabel().' — '.$this->application->name,
            replyTo: [new Address($this->application->email, $this->application->name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'applications::mail.alert', with: [
            'application' => $this->application,
            'adminUrl' => route('admin.resources.show', ['join-applications', $this->application->getKey()]),
        ]);
    }
}
