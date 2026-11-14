<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Concerns\Publishable;

class FocusArea extends Model
{
    use Publishable;

    protected $fillable = ['name', 'slug', 'icon', 'summary', 'examples', 'sort_order', 'is_published'];

    /** @return array<int, string> */
    public function exampleList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $this->examples))));
    }

    /** @return array<string, string> slug => name, for form selects */
    public static function options(): array
    {
        return static::query()->published()->ordered()->pluck('name', 'slug')->all();
    }
}
