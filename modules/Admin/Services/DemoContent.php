<?php

namespace Modules\Admin\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Tracks models that can hold seeded sample content (`is_demo` column) so the
 * dashboard can show how much demo content remains and purge it before launch.
 * Modules register their models: DemoContent::register(Event::class);
 */
class DemoContent
{
    /** @var array<int, class-string<Model>> */
    protected static array $models = [];

    /** @param class-string<Model> $model */
    public static function register(string $model): void
    {
        static::$models[] = $model;
    }

    public function count(): int
    {
        $total = 0;

        foreach (array_unique(static::$models) as $model) {
            try {
                $instance = new $model;
                if (Schema::hasColumn($instance->getTable(), 'is_demo')) {
                    $total += $model::query()->where('is_demo', true)->count();
                }
            } catch (Throwable) {
                // Table not migrated yet.
            }
        }

        return $total;
    }

    /** Delete every demo row (model events run, so related files are removed too). */
    public function purge(): int
    {
        $deleted = 0;

        foreach (array_unique(static::$models) as $model) {
            $instance = new $model;
            if (! Schema::hasColumn($instance->getTable(), 'is_demo')) {
                continue;
            }
            $model::query()->where('is_demo', true)->get()->each(function (Model $row) use (&$deleted) {
                if ($row->delete()) {
                    $deleted++;
                }
            });
        }

        return $deleted;
    }
}
