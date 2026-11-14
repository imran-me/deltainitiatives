<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Users\Models\User;

/**
 * Append-only record of who viewed or changed what, and when.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id', 'description',
        'properties', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['properties' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'System / visitor']);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query->where('subject_type', $subject->getMorphClass())->where('subject_id', $subject->getKey());
    }

    /** Human label for the action's category, e.g. "idea.viewed" -> "Idea". */
    public function category(): string
    {
        return ucfirst(str_replace(['_', '-'], ' ', explode('.', $this->action)[0]));
    }

    public function tone(): string
    {
        return match (true) {
            str_contains($this->action, 'failed'), str_contains($this->action, 'deleted') => 'red',
            str_contains($this->action, 'viewed'), str_contains($this->action, 'downloaded'), str_contains($this->action, 'exported') => 'slate',
            str_contains($this->action, 'login') || str_contains($this->action, 'logout') => 'navy',
            default => 'teal',
        };
    }
}
