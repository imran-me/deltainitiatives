<?php

namespace Modules\Programmes\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Concerns\HasDemoFlag;

class Event extends Model
{
    use HasDemoFlag;

    public const MODES = [
        'in_person' => 'In person',
        'online' => 'Online',
        'hybrid' => 'Hybrid (in person + online)',
    ];

    /** Registration states returned by registrationState(). */
    public const STATE_OPEN = 'open';
    public const STATE_FULL = 'full';          // still accepts sign-ups, as waitlist
    public const STATE_CLOSED = 'closed';
    public const STATE_DEADLINE = 'deadline_passed';
    public const STATE_PAST = 'past';

    protected $fillable = [
        'programme_id', 'title', 'slug', 'summary', 'body', 'starts_at', 'ends_at', 'venue', 'city',
        'mode', 'online_note', 'capacity', 'registration_open', 'registration_deadline', 'cover_path',
        'is_published', 'is_demo',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_deadline' => 'datetime',
            'registration_open' => 'boolean',
            'is_published' => 'boolean',
            'capacity' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // A "deleting" listener that returns false cancels the delete, so never return a value here.
        static::deleting(function (self $event): void {
            if ($event->cover_path) {
                Storage::disk('public')->delete($event->cover_path);
            }
            foreach ($event->photos()->pluck('path') as $path) {
                Storage::disk('public')->delete($path);
            }
        });
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(EventPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------------------------------------------------------------- scopes */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Events that have not finished yet (an event without an end is treated as ~3 hours long). */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->where('ends_at', '>=', now())
            ->orWhere(fn (Builder $w) => $w->whereNull('ends_at')->where('starts_at', '>=', now()->subHours(3))));
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q
            ->where('ends_at', '<', now())
            ->orWhere(fn (Builder $w) => $w->whereNull('ends_at')->where('starts_at', '<', now()->subHours(3))));
    }

    /** Adds `registered_count` (confirmed seats, excluding waitlist and cancellations). */
    public function scopeWithRegisteredCount(Builder $query): Builder
    {
        return $query->withCount(['registrations as registered_count' => fn (Builder $q) => $q->where('status', 'registered')]);
    }

    /* --------------------------------------------------------------- helpers */

    public function isPast(): bool
    {
        $end = $this->ends_at ?? $this->starts_at?->copy()->addHours(3);

        return $end !== null && $end->isPast();
    }

    public function registeredCount(): int
    {
        if (array_key_exists('registered_count', $this->attributes)) {
            return (int) $this->attributes['registered_count'];
        }

        return $this->registrations()->where('status', 'registered')->count();
    }

    public function seatsLeft(): ?int
    {
        return $this->capacity ? max(0, $this->capacity - $this->registeredCount()) : null;
    }

    public function registrationState(): string
    {
        return match (true) {
            $this->isPast() => self::STATE_PAST,
            ! $this->registration_open => self::STATE_CLOSED,
            $this->registration_deadline !== null && $this->registration_deadline->isPast() => self::STATE_DEADLINE,
            $this->capacity !== null && $this->seatsLeft() === 0 => self::STATE_FULL,
            default => self::STATE_OPEN,
        };
    }

    public function acceptsRegistrations(): bool
    {
        return in_array($this->registrationState(), [self::STATE_OPEN, self::STATE_FULL], true);
    }

    public function localStart(): Carbon
    {
        return $this->starts_at->copy()->setTimezone(config('app.display_timezone', 'Asia/Dhaka'));
    }

    public function localEnd(): ?Carbon
    {
        return $this->ends_at?->copy()->setTimezone(config('app.display_timezone', 'Asia/Dhaka'));
    }

    public function dateLabel(): string
    {
        $start = $this->localStart();
        $end = $this->localEnd();

        if (! $end || $start->isSameDay($end)) {
            return $start->format('l, j F Y');
        }

        if ($start->isSameMonth($end)) {
            return $start->format('j').'–'.$end->format('j F Y');
        }

        return $start->format('j M').' – '.$end->format('j M Y');
    }

    public function timeLabel(): string
    {
        $start = $this->localStart();
        $end = $this->localEnd();

        if ($end && ! $start->isSameDay($end)) {
            return 'Starts '.$start->format('g:i A').' BST';
        }

        return $start->format('g:i A').($end ? ' – '.$end->format('g:i A') : '').' BST';
    }

    public function modeLabel(): string
    {
        return self::MODES[$this->mode] ?? ucfirst((string) $this->mode);
    }

    public function whereLabel(): string
    {
        $place = collect([$this->venue, $this->city])->filter()->implode(', ');

        return match ($this->mode) {
            'online' => 'Online',
            'hybrid' => ($place ?: 'Venue to be confirmed').' + online',
            default => $place ?: 'Venue to be confirmed',
        };
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }

    /** schema.org Event structured data for search engines. */
    public function toJsonLd(): array
    {
        $place = [
            '@type' => 'Place',
            'name' => $this->venue ?: 'Venue to be confirmed',
            'address' => array_filter(['@type' => 'PostalAddress', 'addressLocality' => $this->city, 'addressCountry' => 'BD']),
        ];
        $virtual = ['@type' => 'VirtualLocation', 'url' => route('events.show', $this)];

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $this->title,
            'description' => $this->summary,
            'startDate' => $this->starts_at->toIso8601String(),
            'endDate' => $this->ends_at?->toIso8601String(),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => match ($this->mode) {
                'online' => 'https://schema.org/OnlineEventAttendanceMode',
                'hybrid' => 'https://schema.org/MixedEventAttendanceMode',
                default => 'https://schema.org/OfflineEventAttendanceMode',
            },
            'location' => match ($this->mode) {
                'online' => $virtual,
                'hybrid' => [$place, $virtual],
                default => $place,
            },
            'image' => $this->coverUrl() ? [$this->coverUrl()] : null,
            'url' => route('events.show', $this),
            'isAccessibleForFree' => true,
            'organizer' => ['@type' => 'Organization', 'name' => setting('site_name'), 'url' => url('/')],
        ]);
    }

    /** @return array<int, string> id => "title (date)", for admin selects */
    public static function options(): array
    {
        return static::query()->orderByDesc('starts_at')->get(['id', 'title', 'starts_at'])
            ->mapWithKeys(fn (self $e) => [$e->id => $e->title.' · '.$e->localStart()->format('j M Y')])
            ->all();
    }
}
