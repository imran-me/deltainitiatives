<?php

namespace Modules\IdeaPortal\Console;

use Illuminate\Console\Command;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\IdeaPortal\Services\IdeaNotifier;

/**
 * Safety net (scheduled every five minutes): re-sends receipts and team alerts
 * whose first delivery failed, e.g. during a temporary SMTP outage.
 */
class RetryNotificationsCommand extends Command
{
    protected $signature = 'ideas:retry-notifications';

    protected $description = 'Retry idea receipts and team alerts that have not been delivered yet';

    public function handle(IdeaNotifier $notifier): int
    {
        $pending = IdeaSubmission::query()
            ->real()
            ->with('files')
            ->where(fn ($q) => $q->whereNull('receipt_sent_at')->orWhereNull('team_notified_at'))
            ->where('submitted_at', '<=', now()->subMinutes((int) config('ideaportal.retry_after_minutes', 2)))
            ->where('submitted_at', '>=', now()->subDays(3))
            ->orderBy('id')
            ->limit(50)
            ->get();

        $sent = 0;

        foreach ($pending as $idea) {
            if ($idea->receipt_sent_at === null && $notifier->sendReceipt($idea)) {
                $sent++;
            }
            if ($idea->team_notified_at === null && $notifier->sendTeamAlert($idea)) {
                $sent++;
            }
        }

        $this->components->info("Checked {$pending->count()} submissions; delivered {$sent} e-mails.");

        return self::SUCCESS;
    }
}
