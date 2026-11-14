<?php

namespace Modules\IdeaPortal\Policies;

use Modules\IdeaPortal\Models\IdeaSubmission;
use Modules\Users\Models\User;

/**
 * Idea submissions are confidential: every action requires an active account
 * whose role grants the matching permission (Super Admins pass via Gate::before).
 */
class IdeaSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'ideas.view');
    }

    public function view(User $user, IdeaSubmission $idea): bool
    {
        return $this->allows($user, 'ideas.view');
    }

    public function downloadFile(User $user, IdeaSubmission $idea): bool
    {
        return $this->allows($user, 'ideas.view');
    }

    public function score(User $user, IdeaSubmission $idea): bool
    {
        return $this->allows($user, 'ideas.score');
    }

    public function changeStatus(User $user, IdeaSubmission $idea): bool
    {
        return $this->allows($user, 'ideas.status');
    }

    public function export(User $user): bool
    {
        return $this->allows($user, 'ideas.export');
    }

    protected function allows(User $user, string $permission): bool
    {
        return $user->is_active && $user->hasPermission($permission);
    }
}
