<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Model;

class IdeaVote extends Model
{
    protected $fillable = ['public_idea_id', 'visitor'];
}
