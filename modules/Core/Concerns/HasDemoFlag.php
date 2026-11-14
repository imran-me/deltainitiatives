<?php

namespace Modules\Core\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Models with an `is_demo` column. Demo rows are seeded sample content:
 * they show a "Sample" badge on the public site and can be purged with
 * `php artisan delta:purge-demo` or from the admin dashboard.
 */
trait HasDemoFlag
{
    public function initializeHasDemoFlag(): void
    {
        $this->mergeCasts(['is_demo' => 'boolean']);
    }

    public function scopeDemo(Builder $query): Builder
    {
        return $query->where('is_demo', true);
    }

    public function scopeReal(Builder $query): Builder
    {
        return $query->where('is_demo', false);
    }
}
