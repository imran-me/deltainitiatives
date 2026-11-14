<?php

namespace Modules\Partnerships\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Partnerships\Models\PartnerInquiry;

/** Sent to the person who submitted the Partner with Us form. */
class PartnerInquiryAcknowledgement extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PartnerInquiry $inquiry)
    {
        $this->locale($inquiry->locale ?: 'en');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank you for reaching out to Delta Initiatives',
            replyTo: [new Address((string) setting('partners_email'), (string) setting('site_name').' Partnerships')],
        );
    }

    public function content(): Content
    {
        $this->inquiry->loadMissing('partnershipType');

        return new Content(view: 'partnerships::mail.acknowledgement', with: ['inquiry' => $this->inquiry]);
    }
}
