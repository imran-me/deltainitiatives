<?php

namespace Modules\Contact\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Contact\Models\ContactMessage;

/** Auto-reply to the sender confirming we received their message. */
class MessageReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
        $this->locale($contactMessage->locale ?: 'en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('contact::contact.mail.subject', ['reference' => $this->contactMessage->reference()]));
    }

    public function content(): Content
    {
        return new Content(view: 'contact::mail.received', with: ['msg' => $this->contactMessage]);
    }
}
