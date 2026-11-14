{{--
    Event card used on Home, /programmes and the event page.
    <x-programmes::event-card :event="$event" />   (eager-load programme + withRegisteredCount())
--}}
@props(['event', 'past' => false])
@php
    $start = $event->localStart();
    $state = $past ? 'past' : $event->registrationState();
    $seats = $past ? null : $event->seatsLeft();
@endphp
<article {{ $attributes->merge(['class' => 'card-delta is-interactive group flex h-full flex-col']) }} data-reveal>
    <div class="flex flex-1 gap-5 p-6 sm:p-7">
        <div @class([
            'flex w-16 shrink-0 flex-col items-center self-start rounded-2xl py-3 text-center',
            'bg-navy-900 text-white' => ! $past,
            'bg-navy-50 text-navy-700 ring-1 ring-navy-100' => $past,
        ])>
            <span @class(['text-[0.68rem] font-semibold tracking-[0.18em] uppercase', 'text-river-400' => ! $past, 'text-navy-500' => $past])>{{ $start->format('M') }}</span>
            <span class="text-2xl leading-tight font-semibold tabular-nums">{{ $start->format('j') }}</span>
            <span @class(['text-[0.68rem]', 'text-white/60' => ! $past, 'text-navy-400' => $past])>{{ $start->format('Y') }}</span>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                @if($event->programme)
                    <p class="eyebrow">{{ $event->programme->name }}</p>
                @endif
                <x-demo-badge :model="$event" />
            </div>
            <h3 class="mt-2 text-lg leading-snug font-semibold tracking-tight text-navy-900 text-balance">
                <a href="{{ route('events.show', $event) }}" class="after:absolute after:inset-0 after:rounded-[1.25rem] focus-visible:outline-none">{{ $event->title }}</a>
            </h3>
            <ul class="mt-3 space-y-1.5 text-sm text-navy-500" role="list">
                <li class="flex items-start gap-2"><x-icon name="clock" class="mt-0.5 size-4 text-navy-400" /><span>{{ $start->format('D, j M') }} · {{ $event->timeLabel() }}</span></li>
                <li class="flex items-start gap-2">
                    <x-icon :name="$event->mode === 'online' ? 'globe' : 'map-pin'" class="mt-0.5 size-4 text-navy-400" />
                    <span>{{ $event->whereLabel() }}</span>
                </li>
            </ul>
        </div>
    </div>
    @unless($past)
        <div class="flex items-center justify-between gap-3 border-t border-navy-100 px-6 py-4 sm:px-7">
            <p class="text-sm">
                @switch($state)
                    @case('open')
                        @if($seats !== null)
                            <span class="font-medium text-river-700">{{ $seats }} {{ \Illuminate\Support\Str::plural('seat', $seats) }} left</span>
                        @else
                            <span class="font-medium text-river-700">Free · open to all</span>
                        @endif
                        @break
                    @case('full')
                        <span class="font-medium text-silt-700">Full · waitlist open</span>
                        @break
                    @default
                        <span class="text-navy-400">Registration closed</span>
                @endswitch
            </p>
            <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-navy-900 transition group-hover:text-river-700" aria-hidden="true">
                {{ in_array($state, ['open', 'full'], true) ? 'Register' : 'Details' }}
                <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
            </span>
        </div>
    @endunless
</article>
