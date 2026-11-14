<?php

namespace Modules\Newsletter\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Modules\Newsletter\Models\Subscriber;

class ConfirmSubscription extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Subscriber $subscriber)
    {
        $this->locale($subscriber->locale ?: 'en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('newsletter::newsletter.mail_subject'));
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<'.route('newsletter.unsubscribe', $this->subscriber->token).'>',
        ]);
    }

    public function content(): Content
    {
        return new Content(view: 'newsletter::mail.confirm', with: [
            'confirmUrl' => route('newsletter.confirm', $this->subscriber->token),
            'unsubscribeUrl' => route('newsletter.unsubscribe', $this->subscriber->token),
        ]);
    }
}
