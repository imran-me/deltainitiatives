<?php

namespace Modules\Partnerships\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Concerns\HasDemoFlag;

class PartnerInquiry extends Model
{
    use HasDemoFlag;

    public const STATUSES = [
        'new' => 'New',
        'in_conversation' => 'In conversation',
        'closed' => 'Closed',
    ];

    protected $fillable = [
        'organization', 'contact_name', 'job_title', 'email', 'phone', 'website', 'country',
        'partnership_type_id', 'message', 'status', 'admin_notes', 'locale', 'ip_address', 'is_demo',
    ];

    public function partnershipType(): BelongsTo
    {
        return $this->belongsTo(PartnershipType::class);
    }
}
