<x-layouts.public
    title="Programmes & Events"
    description="Delta Challenge, hackathons, policy dialogues, fellowships, talks and Demo Day — programmes that bring Bangladesh's students and young researchers together to build, test and present ideas.">

    <x-page-hero
        eyebrow="Programmes & Events"
        title="Where ideas meet the people who can build them."
        intro="Six programmes, one purpose: to bring students, young researchers, mentors and decision-makers into the same room — and turn good conversations into working solutions.">
        <x-button href="#upcoming" icon="arrow-right">See upcoming events</x-button>
        <x-button :href="localized_route('ideas.create')" variant="outline-light">Submit an idea</x-button>
    </x-page-hero>

    {{-- Programme types --}}
    <section class="bg-white py-20 sm:py-28" id="programmes">
        <div class="container-delta">
            <x-section-heading eyebrow="Our programmes" title="Six ways to take part"
                intro="Each programme is a different door into the same pipeline — from a first idea, to a prototype in Delta Labs, to evidence that informs policy." />

            <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($programmes as $programme)
                    <article id="{{ $programme->slug }}" class="card-delta flex scroll-mt-28 flex-col p-7" data-reveal style="--delay: {{ ($loop->index % 3) * 0.08 }}s">
                        <div class="flex items-start justify-between gap-4">
                            <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-navy-900 text-river-400">
                                <x-icon :name="$programme->icon" class="size-6" />
                            </span>
                            @if($programme->cadence)
                                <span class="rounded-full bg-river-50 px-3 py-1 text-xs font-medium text-river-800 ring-1 ring-river-200">{{ $programme->cadence }}</span>
                            @endif
                        </div>
                        <h3 class="mt-6 text-xl font-semibold tracking-tight text-navy-900">{{ $programme->name }}</h3>
                        @if($programme->tagline)
                            <p class="mt-1 text-sm font-medium text-river-700">{{ $programme->tagline }}</p>
                        @endif
                        <p class="mt-3 flex-1 leading-relaxed text-navy-600">{{ $programme->summary }}</p>
                        @if(filled($programme->body))
                            <details class="group mt-5 border-t border-navy-100 pt-4">
                                <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-navy-900 hover:text-river-700 [&::-webkit-details-marker]:hidden">
                                    How it works
                                    <x-icon name="chevron-down" class="size-4 transition-transform group-open:rotate-180" />
                                </summary>
                                <div class="prose-delta mt-3 text-[0.95rem]">{{ markdown($programme->body) }}</div>
                            </details>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Upcoming events --}}
    <section class="bg-river-50 py-20 sm:py-28" id="upcoming">
        <div class="container-delta">
            <x-section-heading eyebrow="Calendar" title="Upcoming events"
                intro="All events are free. Register to save your seat — when an event is full you can still join the waitlist." />

            @if($upcoming->isNotEmpty())
                <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($upcoming as $event)
                        <x-programmes::event-card :event="$event" style="--delay: {{ ($loop->index % 3) * 0.08 }}s" />
                    @endforeach
                </div>
            @else
                <div class="mt-12 grid gap-8 rounded-[1.25rem] bg-white p-7 ring-1 ring-navy-100 sm:p-10 lg:grid-cols-2 lg:items-center" data-reveal>
                    <div>
                        <p class="text-xl font-semibold text-navy-900">The next dates are being finalised</p>
                        <p class="mt-2 leading-relaxed text-navy-600">New challenges, hackathons, dialogues and talks are announced first in the Delta Dispatch. Subscribe and you'll hear the moment registration opens.</p>
                    </div>
                    <x-newsletter::signup variant="light" source="programmes" />
                </div>
            @endif
        </div>
    </section>

    {{-- Past events --}}
    @if($photos->isNotEmpty() || $past->isNotEmpty())
        <section class="bg-white py-20 sm:py-28" id="past">
            <div class="container-delta">
                <x-section-heading eyebrow="Archive" title="Past events"
                    intro="A record of what we have hosted so far — real moments from real events." />

                @if($photos->isNotEmpty())
                    <ul class="mt-12 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4" role="list">
                        @foreach($photos as $photo)
                            <li @class(['group relative overflow-hidden rounded-2xl bg-navy-100', 'col-span-2 row-span-2' => $loop->first]) data-reveal style="--delay: {{ ($loop->index % 4) * 0.06 }}s">
                                <a href="{{ route('events.show', $photo->event) }}" class="block h-full">
                                    <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?: $photo->event->title }}" loading="lazy" decoding="async"
                                         class="aspect-[4/3] h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]">
                                    <span class="absolute inset-x-0 bottom-0 bg-linear-to-t from-navy-950/85 to-transparent p-4 pt-10 text-sm text-white">
                                        <span class="block font-medium">{{ $photo->event->title }}</span>
                                        @if($photo->caption)<span class="mt-0.5 block text-xs text-white/70">{{ $photo->caption }}</span>@endif
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($past->isNotEmpty())
                    <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($past as $event)
                            <x-programmes::event-card :event="$event" :past="true" />
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Call to action --}}
    <section class="on-dark relative isolate overflow-hidden bg-navy-900 py-20 text-white sm:py-24">
        <x-delta-lines variant="page" class="-z-10 opacity-50" />
        <div class="container-delta grid gap-10 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-7" data-reveal>
                <p class="eyebrow">Get involved</p>
                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-balance sm:text-4xl">Have an idea that can't wait for the next event?</h2>
                <p class="mt-4 max-w-xl text-lg leading-relaxed text-white/70">Submit it through the Idea Portal today — it is timestamped, fingerprinted and receipted the moment you send it. Or host a programme with us as a partner.</p>
            </div>
            <div class="flex flex-wrap gap-3 lg:col-span-5 lg:justify-end" data-reveal>
                <x-button :href="localized_route('ideas.create')" size="lg" icon="arrow-right">Submit an idea</x-button>
                <x-button :href="route('partner.index')" variant="outline-light" size="lg">Host with us</x-button>
            </div>
        </div>
    </section>
</x-layouts.public>
