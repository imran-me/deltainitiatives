@php
    $seats = $event->seatsLeft();
    $acceptsRegistrations = in_array($state, ['open', 'full'], true);
@endphp
<x-layouts.public :title="$event->title" :description="$event->summary ?: $event->title" :image="$event->coverUrl()" :json-ld="$event->toJsonLd()">

    <x-page-hero :eyebrow="$event->programme?->name ?? 'Event'" :title="$event->title" :intro="$event->summary">
        @if($acceptsRegistrations)
            <x-button href="#register" icon="arrow-right">{{ $state === 'full' ? 'Join the waitlist' : 'Register — it’s free' }}</x-button>
        @endif
        <x-button :href="route('programmes.index')" variant="outline-light" icon-left="arrow-left">All events</x-button>
    </x-page-hero>

    <section class="bg-white py-16 sm:py-20">
        <div class="container-delta grid gap-12 lg:grid-cols-12">
            {{-- Key facts --}}
            <aside class="lg:order-2 lg:col-span-4">
                <div class="card-delta p-6 sm:p-7 lg:sticky lg:top-28">
                    <div class="flex items-center justify-between gap-3">
                        <p class="eyebrow">Event details</p>
                        <x-demo-badge :model="$event" />
                    </div>
                    <dl class="mt-5 space-y-5 text-sm">
                        <div class="flex gap-3">
                            <dt><x-icon name="calendar-days" class="size-5 text-river-700" /><span class="sr-only">Date</span></dt>
                            <dd class="font-medium text-navy-900">{{ $event->dateLabel() }}</dd>
                        </div>
                        <div class="flex gap-3">
                            <dt><x-icon name="clock" class="size-5 text-river-700" /><span class="sr-only">Time</span></dt>
                            <dd class="text-navy-700">{{ $event->timeLabel() }}</dd>
                        </div>
                        <div class="flex gap-3">
                            <dt><x-icon :name="$event->mode === 'online' ? 'globe' : 'map-pin'" class="size-5 text-river-700" /><span class="sr-only">Where</span></dt>
                            <dd class="text-navy-700">
                                {{ $event->whereLabel() }}
                                <span class="mt-0.5 block text-xs text-navy-400">{{ $event->modeLabel() }}</span>
                            </dd>
                        </div>
                        @if($event->capacity)
                            <div class="flex gap-3">
                                <dt><x-icon name="users" class="size-5 text-river-700" /><span class="sr-only">Seats</span></dt>
                                <dd class="text-navy-700">
                                    @if($state === 'past')
                                        {{ $event->capacity }} seats
                                    @elseif($seats > 0)
                                        <span class="font-medium text-river-700">{{ $seats }} of {{ $event->capacity }} seats left</span>
                                    @else
                                        <span class="font-medium text-silt-700">Full — waitlist open</span>
                                    @endif
                                </dd>
                            </div>
                        @endif
                        @if($event->registration_deadline && $acceptsRegistrations)
                            <div class="flex gap-3">
                                <dt><x-icon name="info" class="size-5 text-river-700" /><span class="sr-only">Deadline</span></dt>
                                <dd class="text-navy-700">Register by {{ dhaka_time($event->registration_deadline, 'j M Y, g:i A') }}</dd>
                            </div>
                        @endif
                    </dl>
                    @if($acceptsRegistrations)
                        <x-button href="#register" variant="dark" class="mt-7 w-full" icon="arrow-right">{{ $state === 'full' ? 'Join the waitlist' : 'Register' }}</x-button>
                    @endif
                    <p class="mt-5 border-t border-navy-100 pt-5 text-xs leading-relaxed text-navy-400">Questions? Write to <a href="mailto:{{ setting('contact_email') }}" class="text-river-700 underline underline-offset-2">{{ setting('contact_email') }}</a>.</p>
                </div>
            </aside>

            {{-- Description + registration --}}
            <div class="lg:order-1 lg:col-span-8">
                @if($event->coverUrl())
                    <img src="{{ $event->coverUrl() }}" alt="{{ $event->title }}" class="mb-10 aspect-[16/9] w-full rounded-[1.25rem] object-cover ring-1 ring-navy-100" decoding="async">
                @endif

                @if(filled($event->body))
                    <div class="prose-delta max-w-none">{{ markdown($event->body) }}</div>
                @elseif($event->programme)
                    <div class="prose-delta max-w-none"><p>{{ $event->programme->summary }}</p></div>
                @endif

                @if($event->photos->isNotEmpty())
                    <div class="mt-12">
                        <h2 class="text-xl font-semibold tracking-tight text-navy-900">Photos</h2>
                        <ul class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3" role="list">
                            @foreach($event->photos as $photo)
                                <li class="overflow-hidden rounded-xl bg-navy-100">
                                    <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?: $event->title }}" loading="lazy" decoding="async" class="aspect-[4/3] w-full object-cover">
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="register" class="mt-14 scroll-mt-28">
                    <div class="rounded-[1.25rem] bg-river-50 p-6 ring-1 ring-river-100 sm:p-9">
                        <p class="eyebrow">Registration</p>
                        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-navy-900 sm:text-3xl">
                            @switch($state)
                                @case('open') Save your seat @break
                                @case('full') Join the waitlist @break
                                @case('past') This event has taken place @break
                                @default Registration is closed
                            @endswitch
                        </h2>

                        <x-flash class="mt-6" />

                        @if($acceptsRegistrations)
                            <p class="mt-3 text-navy-600">
                                @if($state === 'full')
                                    Every seat is taken, but plans change. Join the waitlist and we will e-mail you if a place opens up.
                                @else
                                    Free to attend. You'll receive a confirmation e-mail with everything you need.
                                @endif
                            </p>

                            <form method="POST" action="{{ route('events.register', $event) }}" class="mt-8 space-y-6" data-guard novalidate>
                                @csrf
                                <x-honeypot />
                                <x-form.errors-summary />

                                <div class="grid gap-6 sm:grid-cols-2">
                                    <x-form.input name="name" label="Full name" required autocomplete="name" maxlength="120" />
                                    <x-form.input name="email" type="email" label="E-mail" required autocomplete="email" maxlength="255" />
                                    <x-form.input name="phone" type="tel" label="Phone" autocomplete="tel" maxlength="40" />
                                    <x-form.input name="institution" label="University / organisation" autocomplete="organization" maxlength="160" />
                                    <x-form.select name="occupation" label="What best describes you?" :options="$occupations" placeholder="Choose…" required class="sm:col-span-2" />
                                    <x-form.textarea name="notes" label="Anything we should know?" hint="Access needs, dietary requirements or a question for the organisers." rows="3" maxlength="1000" class="sm:col-span-2" />
                                </div>

                                <x-form.checkbox name="consent" required>
                                    I agree that Delta Initiatives may contact me about this event, and I have read the <a href="{{ route('legal.show', 'privacy') }}" class="font-medium text-river-700 underline underline-offset-2">Privacy Policy</a>.
                                </x-form.checkbox>

                                <x-turnstile />

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-xs text-navy-400">{{ __('core::ui.forms.required_note') }}</p>
                                    <x-button variant="dark" size="lg" icon="arrow-right" data-loading-text="{{ __('core::ui.forms.sending') }}">
                                        {{ $state === 'full' ? 'Join the waitlist' : 'Register' }}
                                    </x-button>
                                </div>
                            </form>
                        @else
                            <p class="mt-3 max-w-xl text-navy-600">
                                @if($state === 'past')
                                    Thank you to everyone who took part. Upcoming events are listed on our programmes page, and announced first in the Delta Dispatch.
                                @elseif($state === 'deadline_passed')
                                    The registration deadline for this event has passed. Keep an eye on our programmes page for the next one.
                                @else
                                    Registration for this event is not open. Keep an eye on our programmes page, or subscribe to the Delta Dispatch for announcements.
                                @endif
                            </p>
                            <x-button :href="route('programmes.index').'#upcoming'" variant="dark" class="mt-6" icon="arrow-right">See upcoming events</x-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($more->isNotEmpty())
        <section class="bg-[#f7f9fa] py-20 sm:py-24">
            <div class="container-delta">
                <x-section-heading eyebrow="Also coming up" title="More events" />
                <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($more as $other)
                        <x-programmes::event-card :event="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.public>
