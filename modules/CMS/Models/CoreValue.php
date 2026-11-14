<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Concerns\Publishable;

class CoreValue extends Model
{
    use Publishable;

    protected $fillable = ['title', 'summary', 'icon', 'sort_order', 'is_published'];
}
