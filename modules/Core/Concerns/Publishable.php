<?php

namespace Modules\Core\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Models with `is_published` and `sort_order` columns.
 */
trait Publishable
{
    public function initializePublishable(): void
    {
        $this->mergeCasts(['is_published' => 'boolean', 'sort_order' => 'integer']);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('is_published'), true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy($this->qualifyColumn('sort_order'))->orderBy($this->qualifyColumn('id'));
    }
}
