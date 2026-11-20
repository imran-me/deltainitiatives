<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertApplication extends Model
{
    public const STATUSES = ['new' => 'New', 'reviewing' => 'Reviewing', 'accepted' => 'Accepted', 'declined' => 'Declined'];

    protected $fillable = ['name', 'email', 'field', 'institution', 'expertise', 'profile_url', 'status', 'ip_address'];
}
