<?php

namespace Modules\IdeaPortal\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Services\IdeaNotifier;

/**
 * Dispatched with ->afterResponse(): runs in the same PHP process right after
 * the submitter's browser has received the "Idea received" page, so the
 * receipt arrives within seconds without needing a queue worker.
 */
class SendIdeaNotifications
{
    use Dispatchable;

    public function __construct(public int $submissionId) {}

    public function handle(IdeaNotifier $notifier): void
    {
        $idea = IdeaSubmission::query()->with('files')->find($this->submissionId);

        if (! $idea) {
            return;
        }

        if ($idea->receipt_sent_at === null) {
            $notifier->sendReceipt($idea);
        }

        if ($idea->team_notified_at === null) {
            $notifier->sendTeamAlert($idea);
        }
    }
}
