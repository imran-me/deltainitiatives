<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    public const SERVICES = [
        'tech-support' => 'Tech Support',
        'business-systems' => 'Business Systems & ERP',
        'custom-software' => 'Custom Software',
        'prototypes' => 'Prototypes & MVPs',
        'brand' => 'Brand Building',
        'expert-network' => 'Delta Expert Network',
    ];

    public const STATUSES = ['new' => 'New', 'in_progress' => 'In progress', 'proposal_sent' => 'Proposal sent', 'closed' => 'Closed'];

    protected $fillable = ['service', 'name', 'organization', 'email', 'phone', 'budget', 'message', 'status', 'ip_address'];
}
