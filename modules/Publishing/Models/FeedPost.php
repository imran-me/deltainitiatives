<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Concerns\HasDemoFlag;

class FeedPost extends Model
{
    use HasDemoFlag;

    public const REACTIONS = ['like' => 'Like', 'insightful' => 'Insightful', 'support' => 'Support'];

    protected $fillable = ['body', 'image_key', 'image_path', 'link_url', 'link_title', 'published_at', 'is_published', 'is_demo'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean'];
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(FeedReaction::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FeedComment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(FeedComment::class)->where('status', 'approved')->oldest();
    }

    /** Published and not scheduled in the future. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /** @return array<string, int> */
    public function reactionCounts(): array
    {
        $counts = $this->reactions()->selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type');

        return collect(self::REACTIONS)->mapWithKeys(fn ($label, $type) => [$type => (int) ($counts[$type] ?? 0)])->all();
    }
}
