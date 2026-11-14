<?php

namespace Modules\Applications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Concerns\HasDemoFlag;

/**
 * A "Join Us" application: Member, Fellow, Campus Ambassador or Volunteer.
 * The optional CV lives on the private "local" disk and is only reachable
 * through the authorised, audited admin download route.
 */
class JoinApplication extends Model
{
    use HasDemoFlag;

    public const ROLES = ['member', 'fellow', 'ambassador', 'volunteer'];

    public const STUDY_LEVELS = ['college', 'undergraduate', 'postgraduate', 'early_career', 'other'];

    public const AVAILABILITY = ['1-3', '4-6', '7-10', '10+'];

    public const STATUSES = [
        'new' => 'New',
        'reviewing' => 'Reviewing',
        'accepted' => 'Accepted',
        'declined' => 'Declined',
    ];

    protected $fillable = [
        'role', 'name', 'email', 'phone', 'institution', 'study_level', 'department', 'city',
        'linkedin_url', 'skills', 'motivation', 'availability', 'cv_path', 'locale', 'consent_at',
        'status', 'admin_notes', 'ip_address', 'is_demo',
    ];

    protected function casts(): array
    {
        return ['consent_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $application) {
            if ($application->cv_path) {
                Storage::disk('local')->delete($application->cv_path);
            }
        });
    }

    public function reference(): string
    {
        return 'JOIN-'.str_pad((string) $this->getKey(), 5, '0', STR_PAD_LEFT);
    }

    public function roleLabel(): string
    {
        return __('applications::join.roles.'.$this->role.'.name');
    }

    /** @return array<string, string> */
    public static function roleOptions(): array
    {
        return collect(self::ROLES)->mapWithKeys(fn ($r) => [$r => __("applications::join.roles.{$r}.name")])->all();
    }

    /** @return array<string, string> */
    public static function studyLevelOptions(): array
    {
        return collect(self::STUDY_LEVELS)->mapWithKeys(fn ($l) => [$l => __("applications::join.form.study_levels.{$l}")])->all();
    }

    /** @return array<string, string> */
    public static function availabilityOptions(): array
    {
        return collect(self::AVAILABILITY)->mapWithKeys(fn ($a) => [$a => __("applications::join.form.availability_options.{$a}")])->all();
    }
}
