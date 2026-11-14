<?php

namespace Modules\Contact\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Concerns\HasDemoFlag;

class ContactMessage extends Model
{
    use HasDemoFlag;

    public const TOPICS = ['general', 'ideas', 'partnership', 'media', 'events', 'other'];

    public const STATUSES = [
        'new' => 'New',
        'read' => 'Read',
        'replied' => 'Replied',
        'archived' => 'Archived',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'organization', 'topic', 'subject', 'message',
        'status', 'admin_notes', 'locale', 'ip_address', 'user_agent', 'is_demo',
    ];

    public function reference(): string
    {
        return 'MSG-'.str_pad((string) $this->getKey(), 5, '0', STR_PAD_LEFT);
    }

    public function topicLabel(): string
    {
        return __('contact::contact.topics.'.$this->topic);
    }

    /** @return array<string, string> */
    public static function topicOptions(): array
    {
        return collect(self::TOPICS)->mapWithKeys(fn ($t) => [$t => __("contact::contact.topics.{$t}")])->all();
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }
}
