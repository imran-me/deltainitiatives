<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Concerns\Publishable;

class Arm extends Model
{
    use Publishable;

    protected $fillable = ['name', 'slug', 'tagline', 'summary', 'body', 'highlights', 'icon', 'sort_order', 'is_published'];

    /** @return array<int, string> */
    public function highlightList(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) $this->highlights))));
    }
}
