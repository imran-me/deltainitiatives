<?php

namespace Modules\IdeaPortal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Users\Models\User;

/**
 * One reviewer's rubric scores for one idea (updatable; one row per reviewer).
 */
class IdeaScore extends Model
{
    protected $fillable = ['idea_submission_id', 'user_id', 'scores', 'average', 'comment'];

    protected function casts(): array
    {
        return ['scores' => 'array', 'average' => 'decimal:2'];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(IdeaSubmission::class, 'idea_submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'Former reviewer']);
    }

    /**
     * Weighted average of 1–5 scores using the rubric weights.
     *
     * @param  array<string, int|string>  $scores
     */
    public static function weightedAverage(array $scores): float
    {
        $total = 0;
        $weights = 0;

        foreach (config('ideaportal.rubric') as $key => $criterion) {
            if (! isset($scores[$key])) {
                continue;
            }
            $weight = (float) ($criterion['weight'] ?? 1);
            $total += (int) $scores[$key] * $weight;
            $weights += $weight;
        }

        return $weights > 0 ? round($total / $weights, 2) : 0.0;
    }
}
