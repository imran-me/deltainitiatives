<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\CMS\Support\StatSources;
use Modules\Core\Concerns\Publishable;

/**
 * A headline number for the home page. Either entered by hand (source "manual",
 * shown only once marked verified) or counted live from real records
 * (e.g. source "ideas" = non-demo idea submissions). Zero values are never shown.
 */
class Stat extends Model
{
    use Publishable;

    protected $fillable = ['label', 'source', 'value', 'suffix', 'is_verified', 'verified_note', 'sort_order', 'is_published'];

    protected function casts(): array
    {
        return ['is_verified' => 'boolean', 'value' => 'integer'];
    }

    public function resolvedValue(): int
    {
        if ($this->source === 'manual') {
            return (int) $this->value;
        }

        return (int) (StatSources::resolve($this->source) ?? 0);
    }

    public function isDisplayable(): bool
    {
        $trusted = $this->source !== 'manual' || $this->is_verified;

        return $this->is_published && $trusted && $this->resolvedValue() > 0;
    }

    /** @return Collection<int, self> */
    public static function forHomepage(): Collection
    {
        return static::query()->published()->ordered()->get()->filter->isDisplayable()->values();
    }
}
