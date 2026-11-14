<?php

namespace Modules\Programmes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Concerns\Publishable;

/**
 * A programme type: Delta Challenge, Hackathon, Dialogue, Fellows, Talks, Demo Day.
 */
class Programme extends Model
{
    use Publishable;

    protected $fillable = ['name', 'slug', 'tagline', 'summary', 'body', 'icon', 'cadence', 'sort_order', 'is_published'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /** @return array<int, string> id => name, for admin selects */
    public static function options(): array
    {
        return static::query()->ordered()->pluck('name', 'id')->all();
    }
}
