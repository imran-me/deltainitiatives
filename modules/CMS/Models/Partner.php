<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Concerns\HasDemoFlag;
use Modules\Core\Concerns\Publishable;

/**
 * A collaborating organisation. Shown publicly ONLY when written permission
 * to use the name and logo has been confirmed (Build Plan, Home §8).
 */
class Partner extends Model
{
    use HasDemoFlag, Publishable;

    protected $fillable = ['name', 'logo_path', 'website_url', 'permission_confirmed', 'permission_note', 'sort_order', 'is_published', 'is_demo'];

    protected function casts(): array
    {
        return ['permission_confirmed' => 'boolean'];
    }

    protected static function booted(): void
    {
        // Note: a "deleting" listener that returns false cancels the delete, so never return a value here.
        static::deleting(function (self $partner): void {
            if ($partner->logo_path) {
                Storage::disk('public')->delete($partner->logo_path);
            }
        });
    }

    /** Published AND permission confirmed. */
    public function scopeDisplayable(Builder $query): Builder
    {
        return $query->published()->where('permission_confirmed', true);
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }
}
