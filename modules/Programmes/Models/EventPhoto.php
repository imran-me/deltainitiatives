<?php

namespace Modules\Programmes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * A real photo from a past event (never stock imagery).
 */
class EventPhoto extends Model
{
    protected $fillable = ['event_id', 'path', 'caption', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    protected static function booted(): void
    {
        // A "deleting" listener that returns false cancels the delete, so never return a value here.
        static::deleting(function (self $photo): void {
            if ($photo->path) {
                Storage::disk('public')->delete($photo->path);
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
