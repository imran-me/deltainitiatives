<?php

namespace Modules\IdeaPortal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

class IdeaFile extends Model
{
    protected $fillable = ['idea_submission_id', 'original_name', 'stored_path', 'mime', 'size', 'sha256'];

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(IdeaSubmission::class, 'idea_submission_id');
    }

    public function humanSize(): string
    {
        return Number::fileSize($this->size, 1);
    }
}
