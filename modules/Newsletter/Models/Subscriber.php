<?php

namespace Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    public const STATUSES = ['pending' => 'Awaiting confirmation', 'subscribed' => 'Subscribed', 'unsubscribed' => 'Unsubscribed'];

    protected $table = 'newsletter_subscribers';

    protected $fillable = ['email', 'name', 'status', 'token', 'source', 'locale', 'ip_address', 'confirmed_at', 'unsubscribed_at'];

    protected function casts(): array
    {
        return ['confirmed_at' => 'datetime', 'unsubscribed_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $s) => $s->token ??= static::newToken());
    }

    public static function newToken(): string
    {
        return Str::random(48);
    }

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('status', 'subscribed');
    }
}
