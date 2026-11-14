<?php

namespace Modules\Programmes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Concerns\HasDemoFlag;

class EventRegistration extends Model
{
    use HasDemoFlag;

    public const STATUSES = [
        'registered' => 'Registered',
        'waitlisted' => 'Waitlisted',
        'cancelled' => 'Cancelled',
        'attended' => 'Attended',
    ];

    public const OCCUPATIONS = [
        'student' => 'Student',
        'researcher' => 'Researcher / academic',
        'professional' => 'Professional',
        'other' => 'Other',
    ];

    protected $fillable = [
        'event_id', 'name', 'email', 'phone', 'institution', 'occupation', 'notes',
        'status', 'locale', 'ip_address', 'is_demo',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function isWaitlisted(): bool
    {
        return $this->status === 'waitlisted';
    }
}
