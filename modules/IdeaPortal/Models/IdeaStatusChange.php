<?php

namespace Modules\IdeaPortal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Users\Models\User;

class IdeaStatusChange extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['idea_submission_id', 'user_id', 'from_status', 'to_status', 'note'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(IdeaSubmission::class, 'idea_submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'System']);
    }

    public function toLabel(): string
    {
        return config("ideaportal.statuses.{$this->to_status}.label", $this->to_status);
    }

    public function fromLabel(): ?string
    {
        return $this->from_status ? config("ideaportal.statuses.{$this->from_status}.label", $this->from_status) : null;
    }
}
