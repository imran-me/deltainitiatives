<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Concerns\Publishable;

class WorkedWith extends Model
{
    use Publishable;

    protected $table = 'worked_with';

    protected $fillable = ['title', 'client_name', 'summary', 'url', 'logo_path', 'is_confirmed', 'sort_order', 'is_published'];

    protected function casts(): array
    {
        return ['is_confirmed' => 'boolean'];
    }
}
