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

/** Internal alert to partners@ when a new inquiry arrives. */
class PartnerInquiryAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PartnerInquiry $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New partner inquiry: '.$this->inquiry->organization,
            replyTo: [new Address($this->inquiry->email, $this->inquiry->contact_name)],
        );
    }

    public function content(): Content
    {
        $this->inquiry->loadMissing('partnershipType');

        return new Content(view: 'partnerships::mail.alert', with: ['inquiry' => $this->inquiry]);
    }
}
