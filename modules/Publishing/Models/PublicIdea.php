<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Concerns\HasDemoFlag;

/**
 * An idea the admin has approved for public view. Private Idea Portal submissions
 * are never shown; an admin publishes a curated copy here.
 */
class PublicIdea extends Model
{
    use HasDemoFlag;

    public const STAGES = ['Discover', 'Develop', 'Validate', 'Brief', 'Implement'];

    public const AREAS = ['ai' => 'AI & Computing', 'hardware' => 'Hardware & Engineering', 'ventures' => 'Startups & Ventures',
        'climate' => 'Nature & Climate', 'life' => 'Life Sciences & Health', 'governance' => 'Good Governance'];

    protected $fillable = ['slug', 'code', 'title', 'area', 'stage', 'problem', 'solution', 'how', 'sdgs', 'fingerprint', 'protected_at',
        'cover_key', 'cover_path', 'person_id', 'is_featured', 'is_published', 'is_demo'];

    protected function casts(): array
    {
        return ['protected_at' => 'datetime', 'is_published' => 'boolean', 'is_featured' => 'boolean', 'stage' => 'integer'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(IdeaVote::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function stageName(): string
    {
        return self::STAGES[min(max((int) $this->stage, 0), 4)];
    }

    public function areaName(): string
    {
        return self::AREAS[$this->area] ?? (string) $this->area;
    }

    /** @return array<int, int> */
    public function sdgList(): array
    {
        return collect(explode(',', (string) $this->sdgs))->map(fn ($n) => (int) trim($n))->filter()->values()->all();
    }

    /** @return array<int, array{0: string, 1: string}> */
    public function howSteps(): array
    {
        return collect(preg_split('/\r?\n/', (string) $this->how) ?: [])->filter()
            ->map(fn ($line) => array_pad(explode(':', $line, 2), 2, ''))
            ->map(fn ($p) => [trim($p[0]), trim($p[1])])->values()->all();
    }
}
