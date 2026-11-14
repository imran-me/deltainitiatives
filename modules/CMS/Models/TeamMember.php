<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Concerns\HasDemoFlag;
use Modules\Core\Concerns\Publishable;

class TeamMember extends Model
{
    use HasDemoFlag, Publishable;

    public const GROUPS = ['team' => 'Team', 'advisor' => 'Advisory Board'];

    protected $fillable = ['name', 'role', 'group', 'bio', 'photo_path', 'linkedin_url', 'sort_order', 'is_published', 'is_demo'];

    protected static function booted(): void
    {
        // Note: a "deleting" listener that returns false cancels the delete, so never return a value here.
        static::deleting(function (self $member): void {
            if ($member->photo_path) {
                Storage::disk('public')->delete($member->photo_path);
            }
        });
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }
}
