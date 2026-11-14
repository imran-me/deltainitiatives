<?php

namespace Modules\IdeaPortal\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Modules\IdeaPortal\Models\IdeaSubmission;

/**
 * Alert to the team. Deliberately minimal — ID, title, focus area and time only.
 * The idea itself is readable only inside the admin panel by authorised reviewers.
 */
class NewIdeaAlertMail extends Mailable
{
    public function __construct(public IdeaSubmission $idea) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New idea received: '.$this->idea->code,
            tags: ['idea-alert'],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'ideaportal::mail.alert', with: [
            'idea' => $this->idea,
            'adminUrl' => route('admin.ideas.show', $this->idea->code),
        ]);
    }
}
