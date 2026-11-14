<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Concerns\Publishable;

class LegalPage extends Model
{
    use Publishable;

    protected $fillable = ['slug', 'title', 'summary', 'body', 'version', 'effective_date', 'is_published'];

    protected function casts(): array
    {
        return ['effective_date' => 'date'];
    }
}
