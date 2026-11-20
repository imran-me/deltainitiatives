<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedComment extends Model
{
    public const STATUSES = ['pending' => 'Awaiting moderation', 'approved' => 'Approved', 'rejected' => 'Rejected'];

    protected $fillable = ['feed_post_id', 'name', 'body', 'status', 'visitor'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class, 'feed_post_id');
    }
}
