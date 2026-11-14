<?php

namespace Modules\IdeaPortal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Core\Concerns\HasDemoFlag;

/**
 * One idea submitted through the Idea Portal.
 *
 * The record is evidence: submitted_at, fingerprint and fingerprint_payload are
 * written once and never edited. Dates use microsecond precision so the
 * fingerprint payload can be rebuilt exactly from the database.
 */
class IdeaSubmission extends Model
{
    use HasDemoFlag;

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'code', 'uuid', 'status', 'title', 'summary', 'focus_area_slug', 'focus_area_name',
        'lead_name', 'lead_email', 'lead_phone', 'institution', 'team_name', 'team_size', 'team_members',
        'answers', 'locale', 'agreement_version', 'consented_at', 'submitted_at',
        'fingerprint', 'fingerprint_payload', 'ip_address', 'user_agent', 'is_demo',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'team_size' => 'integer',
            'consented_at' => 'datetime',
            'submitted_at' => 'datetime',
            'receipt_sent_at' => 'datetime',
            'team_notified_at' => 'datetime',
            'average_score' => 'decimal:2',
            'scores_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $idea) {
            $idea->uuid ??= (string) Str::uuid();
            $idea->status ??= 'received';
        });

        // Remove private uploads with the record (e.g. when sample content is purged).
        // Must not return a value: returning false from "deleting" cancels the delete.
        static::deleting(function (self $idea): void {
            Storage::disk('local')->deleteDirectory('ideas/'.$idea->code);
        });
    }

    public function files(): HasMany
    {
        return $this->hasMany(IdeaFile::class)->orderBy('id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(IdeaScore::class)->orderByDesc('updated_at');
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(IdeaStatusChange::class)->orderByDesc('id');
    }

    public function answer(string $key): ?string
    {
        $value = $this->answers[$key] ?? null;

        return $value === null ? null : (string) $value;
    }

    public function statusLabel(): string
    {
        return config("ideaportal.statuses.{$this->status}.label", Str::headline((string) $this->status));
    }

    public function statusTone(): string
    {
        return config("ideaportal.statuses.{$this->status}.tone", 'slate');
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return collect(config('ideaportal.statuses'))->map(fn ($s) => $s['label'])->all();
    }

    /** Recalculate the cached average across all reviewers' scores. */
    public function refreshScoreSummary(): void
    {
        $averages = $this->scores()->pluck('average');

        $this->forceFill([
            'average_score' => $averages->isEmpty() ? null : round((float) $averages->avg(), 2),
            'scores_count' => $averages->count(),
        ])->save();
    }
}
