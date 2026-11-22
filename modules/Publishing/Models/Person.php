<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Concerns\HasDemoFlag;
use Modules\Core\Concerns\Publishable;

class Person extends Model
{
    use HasDemoFlag, Publishable;

    public const KINDS = ['team' => 'Team', 'contributor' => 'Contributor', 'board' => 'Board'];

    /** Initiator titles by the furthest stage reached by any of the person's published ideas. */
    public const TITLES = ['Young Initiator', 'Delta Initiator', 'Research Initiator', 'Policy Initiator', 'Impact Initiator'];

    protected $table = 'people';

    protected $fillable = ['slug', 'name', 'kind', 'role', 'area', 'bio', 'photo_path', 'sort_order', 'is_published', 'is_top_contributor', 'is_demo'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(PublicIdea::class);
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim((string) preg_replace('/[^\pL\s]/u', ' ', $this->name))) ?: [];

        return mb_strtoupper(collect($words)->filter()->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode(''));
    }

    /** Earned Initiator title, or null for people without published ideas. */
    public function initiatorTitle(): ?string
    {
        $stage = $this->ideas->where('is_published', true)->max('stage');

        return $stage === null ? null : self::TITLES[min((int) $stage, 4)];
    }

    /** Leaderboard points: 100 per published idea, 150 per stage reached, 5 per supporter vote. */
    public function points(): int
    {
        return (int) $this->ideas->where('is_published', true)
            ->sum(fn (PublicIdea $i) => 100 + 150 * (int) $i->stage + 5 * (int) ($i->votes_count ?? 0));
    }
}
