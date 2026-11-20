<?php

namespace Modules\Publishing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedReaction extends Model
{
    protected $fillable = ['feed_post_id', 'type', 'visitor'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class, 'feed_post_id');
    }
}
