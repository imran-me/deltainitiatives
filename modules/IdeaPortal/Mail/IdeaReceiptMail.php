<?php

namespace Modules\IdeaPortal\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Modules\IdeaPortal\Models\IdeaSubmission;

/**
 * The submitter's official receipt: Idea ID, exact time and SHA-256 fingerprint.
 * Sent immediately (not queued) from SendIdeaNotifications.
 */
class IdeaReceiptMail extends Mailable
{
    public function __construct(public IdeaSubmission $idea) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('ideaportal::portal.mail.receipt_subject', ['code' => $this->idea->code]),
            replyTo: [new Address((string) setting('ideas_email'), (string) setting('site_name'))],
            tags: ['idea-receipt'],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'ideaportal::mail.receipt', with: [
            'idea' => $this->idea,
            'files' => $this->idea->relationLoaded('files') ? $this->idea->files : $this->idea->files()->get(),
            'verifyUrl' => route('ideas.verify'),
        ]);
    }
}
