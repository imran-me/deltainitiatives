<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * An editable piece of page copy, addressed by key (e.g. "home.hero").
 * Read on the site with block('home.hero')->title.
 */
class ContentBlock extends Model
{
    protected $fillable = ['key', 'label', 'eyebrow', 'title', 'body', 'help'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('delta.blocks'));
        static::deleted(fn () => Cache::forget('delta.blocks'));
    }
}
