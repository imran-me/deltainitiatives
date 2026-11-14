{{--
    Home-page section #7 — "Upcoming events". Self-contained: queries its own data.
    <x-programmes::upcoming-events />   or   <x-programmes::upcoming-events :limit="3" />
--}}
@props(['limit' => 3])
@php
    $events = \Modules\Programmes\Models\Event::query()
        ->published()->upcoming()
        ->with('programme')->withRegisteredCount()
        ->orderBy('starts_at')->limit($limit)->get();
@endphp
<section {{ $attributes->merge(['class' => 'bg-white py-20 sm:py-28']) }} id="upcoming-events">
    <div class="container-delta">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <x-section-heading eyebrow="Programmes & Events" title="Upcoming events"
                intro="Challenges, hackathons, dialogues and talks — open to students and young researchers across Bangladesh, and free to attend." />
            <a href="{{ route('programmes.index') }}" class="group inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-navy-900 hover:text-river-700" data-reveal>
                All programmes &amp; events
                <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
            </a>
        </div>

        @if($events->isNotEmpty())
            <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($events as $event)
                    <x-programmes::event-card :event="$event" style="--delay: {{ $loop->index * 0.08 }}s" />
                @endforeach
            </div>
        @else
            <div class="mt-12 flex flex-col gap-6 rounded-[1.25rem] bg-river-50 p-7 ring-1 ring-river-100 sm:flex-row sm:items-center sm:justify-between sm:p-8" data-reveal>
                <div class="flex items-start gap-4">
                    <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white text-river-700 ring-1 ring-river-200">
                        <x-icon name="calendar-days" class="size-6" />
                    </span>
                    <div>
                        <p class="text-lg font-semibold text-navy-900">The next dates are being finalised</p>
                        <p class="mt-1 max-w-xl text-navy-600">New challenges, hackathons and talks are announced first in the Delta Dispatch, our monthly newsletter.</p>
                    </div>
                </div>
                <div class="flex shrink-0 flex-wrap gap-3">
                    <x-button href="#newsletter-home" variant="dark" size="sm" icon="mail">Get the newsletter</x-button>
                    <x-button :href="route('programmes.index')" variant="outline" size="sm">See our programmes</x-button>
                </div>
            </div>
        @endif
    </div>
</section>
