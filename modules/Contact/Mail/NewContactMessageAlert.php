<?php

namespace Modules\Contact\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Contact\Models\ContactMessage;

/** Team alert for a new contact message — replying goes straight to the sender. */
class NewContactMessageAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
        $this->locale('en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Contact · '.$this->contactMessage->topicLabel().'] '.$this->contactMessage->subject,
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'contact::mail.alert', with: [
            'msg' => $this->contactMessage,
            'adminUrl' => route('admin.resources.show', ['messages', $this->contactMessage->getKey()]),
        ]);
    }
}
