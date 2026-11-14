<?php

namespace Modules\IdeaPortal\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\IdeaPortal\Mail\IdeaReceiptMail;
use Modules\IdeaPortal\Mail\NewIdeaAlertMail;
use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\Users\Models\User;
use Throwable;

/**
 * Sends the submitter's receipt and the team alert, recording when each
 * succeeded so failures can be retried by `ideas:retry-notifications`.
 */
class IdeaNotifier
{
    public function sendReceipt(IdeaSubmission $idea): bool
    {
        try {
            Mail::to($idea->lead_email, $idea->lead_name)
                ->locale($idea->locale ?: 'en')
                ->send(new IdeaReceiptMail($idea));

            $idea->forceFill(['receipt_sent_at' => now()])->save();

            return true;
        } catch (Throwable $e) {
            Log::error('Idea receipt e-mail failed', ['code' => $idea->code, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function sendTeamAlert(IdeaSubmission $idea): bool
    {
        $recipients = $this->teamRecipients();

        if ($recipients === []) {
            $idea->forceFill(['team_notified_at' => now()])->save();

            return true;
        }

        try {
            foreach ($recipients as $email) {
                Mail::to($email)->locale('en')->send(new NewIdeaAlertMail($idea));
            }

            $idea->forceFill(['team_notified_at' => now()])->save();

            return true;
        } catch (Throwable $e) {
            Log::error('New idea alert e-mail failed', ['code' => $idea->code, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Active accounts whose role grants "ideas.notify", plus the extra
     * addresses in Settings → Idea Portal (comma-separated).
     *
     * @return array<int, string>
     */
    public function teamRecipients(): array
    {
        $users = User::query()->active()->withPermission('ideas.notify')->pluck('email')->all();

        $extra = array_map('trim', explode(',', (string) setting('ideas_notify_emails', '')));

        return collect(array_merge($users, $extra))
            ->map(fn ($email) => mb_strtolower(trim((string) $email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }
}
