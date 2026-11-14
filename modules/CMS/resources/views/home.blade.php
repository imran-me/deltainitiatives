@php
    $hero = block('home.hero');
    $story = block('home.story');
    $headline = $hero->title ?: setting('site_tagline');
    $endsWithDot = str_ends_with($headline, '.');
@endphp
<x-layouts.public :newsletter="false">

    {{-- 1 · Hero --}}
    <section class="on-dark relative isolate overflow-hidden bg-navy-900 text-white" aria-labelledby="hero-title">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(55%_70%_at_78%_8%,rgb(31_162_166/0.20),transparent_70%)]"></div>
        <div class="bg-contours on-dark absolute inset-0 -z-10 opacity-40 [mask-image:linear-gradient(to_bottom,black,transparent_75%)]"></div>
        <x-delta-lines class="-z-10" />

        <div class="container-delta flex min-h-[94svh] flex-col justify-end pt-36 pb-14 sm:pb-20">
            <div class="hero-enter max-w-4xl">
                <p class="eyebrow">{{ $hero->eyebrow ?: setting('site_descriptor') }}</p>
                <h1 id="hero-title" class="mt-6 text-display font-semibold">
                    @if($endsWithDot){{ \Illuminate\Support\Str::beforeLast($headline, '.') }}<span class="text-river-400">.</span>@else{{ $headline }}@endif
                </h1>
                <div class="mt-8 max-w-2xl text-lg leading-relaxed text-pretty text-white/75 sm:text-xl [&_p]:m-0">{{ markdown($hero->body) }}</div>
                <div class="mt-10 flex flex-wrap gap-3">
                    <x-button :href="localized_route('ideas.create')" size="lg" icon="arrow-right">{{ __('core::ui.nav.submit_idea') }}</x-button>
                    <x-button :href="route('partner.index')" size="lg" variant="outline-light">{{ __('core::ui.nav.partner') }}</x-button>
                </div>
            </div>

            {{-- What every submission receives — the Idea Portal's promise, at a glance --}}
            <ul role="list" class="hero-enter mt-16 grid overflow-hidden rounded-2xl bg-white/[0.04] ring-1 ring-white/10 backdrop-blur-sm sm:mt-20 sm:grid-cols-3">
                @foreach([
                    ['hash', 'A unique Idea ID', 'DI-'.now()->year.'-0001'],
                    ['clock', 'A precise timestamp', now()->setTimezone('Asia/Dhaka')->format('j M Y · H:i:s').' BST'],
                    ['fingerprint', 'A SHA-256 fingerprint', '9f2c 41d7 … e0b3 a41e'],
                ] as [$icon, $label, $sample])
                    <li class="flex items-center gap-4 border-white/10 px-6 py-5 max-sm:border-b max-sm:last:border-b-0 sm:border-r sm:last:border-r-0">
                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-river-500/15 text-river-400 ring-1 ring-river-400/25"><x-icon :name="$icon" class="size-5" /></span>
                        <div class="min-w-0">
                            <p class="text-sm text-white/60">{{ $label }}</p>
                            <p class="mt-0.5 truncate font-mono text-sm text-white/90">{{ $sample }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
            <p class="mt-4 text-sm text-white/50">Every idea submitted through the Idea Portal is recorded, receipted and kept confidential. <span class="text-white/80">Your idea stays yours.</span></p>
        </div>
    </section>

    {{-- 2 · The story --}}
    <section class="relative py-24 sm:py-32" aria-labelledby="story-title">
        <div class="container-delta grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-4" data-reveal>
                <p class="eyebrow">{{ $story->eyebrow ?: 'Our name' }}</p>
                <div class="mt-8 hidden select-none lg:block" aria-hidden="true">
                    <x-delta-mark class="h-40 w-auto text-navy-900" lines="#0B1F3A" />
                </div>
            </div>
            <div class="lg:col-span-8">
                <h2 id="story-title" class="text-3xl leading-tight font-semibold tracking-[-0.03em] text-balance text-navy-900 sm:text-4xl lg:text-[3.25rem] lg:leading-[1.08]" data-reveal>{{ $story->title }}</h2>
                <div class="prose-delta mt-8 max-w-2xl" data-reveal style="--delay:.1s">{{ markdown($story->body) }}</div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2" data-reveal style="--delay:.2s">
                    <div class="rounded-2xl bg-river-50 p-6 ring-1 ring-river-100">
                        <p class="font-mono text-3xl text-river-700">Δ</p>
                        <p class="mt-3 text-sm font-semibold tracking-wide text-navy-900 uppercase">In science</p>
                        <p class="mt-1 text-navy-600">The symbol for change — the difference between where things are and where they could be.</p>
                    </div>
                    <div class="rounded-2xl bg-navy-900 p-6 text-white">
                        <x-icon name="waves" class="size-8 text-river-400" />
                        <p class="mt-3 text-sm font-semibold tracking-wide uppercase">In geography</p>
                        <p class="mt-1 text-white/70">The Ganges–Brahmaputra–Meghna delta — the restless, fertile land Bangladesh is built on.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3 · Three arms --}}
    @if($arms->isNotEmpty())
        <section class="bg-mist py-24 sm:py-32" aria-labelledby="arms-title">
            <div class="container-delta">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <x-section-heading eyebrow="What we do" title="Three arms, one purpose" intro="Research that is built, tested and carried into policy — so good ideas do not stop at the idea stage." id="arms-title" />
                    <x-button :href="route('what-we-do')" variant="outline" icon="arrow-right" class="self-start lg:self-auto" data-reveal>How we work</x-button>
                </div>
                <div class="mt-14 grid gap-5 md:grid-cols-3">
                    @foreach($arms as $arm)
                        <a href="{{ route('what-we-do') }}#{{ $arm->slug }}" class="card-delta group flex flex-col p-7 sm:p-8" data-reveal style="--delay: {{ $loop->index * 0.08 }}s">
                            <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-navy-900 text-river-400"><x-icon :name="$arm->icon" class="size-6" /></span>
                            <p class="mt-7 text-xs font-semibold tracking-[0.18em] text-river-700 uppercase">{{ $arm->tagline }}</p>
                            <h3 class="mt-2 text-2xl font-semibold tracking-tight text-navy-900">{{ $arm->name }}</h3>
                            <p class="mt-3 flex-1 leading-relaxed text-navy-600">{{ $arm->summary }}</p>
                            <span class="mt-7 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-900">
                                Explore {{ $arm->name }}
                                <x-icon name="arrow-right" class="size-4 text-river-600 transition-transform duration-300 group-hover:translate-x-1" />
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 4 · Idea-to-Impact pipeline --}}
    @if($steps->isNotEmpty())
        <section class="py-24 sm:py-32" aria-labelledby="pipeline-title">
            <div class="container-delta">
                <x-section-heading eyebrow="Idea to impact" title="From first draft to real-world change, in six steps" intro="A clear, published path — so every contributor knows what happens to their idea and when." align="center" id="pipeline-title" />

                <ol class="relative mt-16 grid gap-10 md:grid-cols-3 md:gap-x-8 md:gap-y-14 xl:grid-cols-6 xl:gap-6" data-reveal role="list">
                    {{-- Connector line (desktop) --}}
                    <div class="pipeline-line absolute top-6 right-[8%] left-[8%] hidden h-px bg-gradient-to-r from-river-200 via-river-500 to-river-200 xl:block" aria-hidden="true"></div>
                    @foreach($steps as $step)
                        <li class="relative flex gap-5 xl:flex-col xl:gap-0">
                            @unless($loop->last)
                                <span class="absolute top-12 bottom-[-2.5rem] left-6 w-px bg-river-200 md:hidden" aria-hidden="true"></span>
                            @endunless
                            <span class="relative z-10 inline-flex size-12 shrink-0 items-center justify-center rounded-full bg-white text-river-700 shadow-[0_0_0_6px_white] ring-1 ring-river-200 xl:mx-0">
                                <x-icon :name="$step->icon" class="size-5" />
                            </span>
                            <div class="xl:mt-6">
                                <p class="font-mono text-xs font-medium text-river-700">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                <h3 class="mt-1 text-lg font-semibold text-navy-900">{{ $step->title }}</h3>
                                <p class="mt-2 text-[0.9375rem] leading-relaxed text-navy-500">{{ $step->summary }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>

                {{-- Proof band --}}
                <div class="on-dark relative isolate mt-20 overflow-hidden rounded-3xl bg-navy-900 px-6 py-10 text-white sm:px-12 sm:py-14" data-reveal>
                    <div class="absolute inset-y-0 right-0 -z-10 w-1/2 bg-[radial-gradient(60%_80%_at_100%_50%,rgb(31_162_166/0.22),transparent_70%)]"></div>
                    <div class="grid items-center gap-10 lg:grid-cols-12">
                        <div class="lg:col-span-7">
                            <p class="eyebrow">The Idea Portal</p>
                            <h3 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Your idea stays yours.</h3>
                            <p class="mt-4 max-w-xl leading-relaxed text-white/70">The moment you submit, we record exactly what you sent and when — with a unique ID and a SHA-256 digital fingerprint — and e-mail you a receipt. If anyone ever claims your idea, the record speaks for you.</p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row lg:col-span-5 lg:justify-end">
                            <x-button :href="localized_route('ideas.create')" size="lg" icon="arrow-right">{{ __('core::ui.nav.submit_idea') }}</x-button>
                            <x-button :href="route('legal.show', 'contributor-agreement')" size="lg" variant="outline-light">Contributor Agreement</x-button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 5 · Focus areas --}}
    @if($focusAreas->isNotEmpty())
        <section class="border-y border-navy-100 bg-navy-50/50 py-24 sm:py-32" aria-labelledby="focus-title">
            <div class="container-delta">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <x-section-heading eyebrow="Focus areas" :title="$focusAreas->count() === 8 ? 'Eight areas where change matters most' : 'Where change matters most'" intro="We prioritise problems where Bangladesh's young researchers have a real advantage — and where evidence can move policy." id="focus-title" />
                    <x-button :href="route('what-we-do').'#focus-areas'" variant="outline" icon="arrow-right" class="self-start lg:self-auto" data-reveal>See examples</x-button>
                </div>
                <ul class="mt-14 grid gap-px overflow-hidden rounded-3xl bg-navy-100 ring-1 ring-navy-100 sm:grid-cols-2 lg:grid-cols-4" role="list">
                    @foreach($focusAreas as $area)
                        <li class="group bg-white p-7 transition-colors duration-300 hover:bg-river-50/60" data-reveal style="--delay: {{ ($loop->index % 4) * 0.06 }}s">
                            <span class="inline-flex size-11 items-center justify-center rounded-xl bg-river-50 text-river-700 ring-1 ring-river-100 transition group-hover:bg-white"><x-icon :name="$area->icon" class="size-5.5" /></span>
                            <h3 class="mt-5 font-semibold text-navy-900">{{ $area->name }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-navy-500">{{ $area->summary }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- 6 · Numbers — only verified or live-counted figures, never invented ones --}}
    @if($stats->isNotEmpty())
        <section class="on-dark bg-navy-900 py-20 text-white sm:py-24" aria-labelledby="numbers-title">
            <div class="container-delta">
                <h2 id="numbers-title" class="sr-only">Delta Initiatives in numbers</h2>
                <dl @class(['grid gap-10 sm:grid-cols-2', [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3'][$stats->count()] ?? 'lg:grid-cols-4'])>
                    @foreach($stats as $stat)
                        <div class="flex flex-col-reverse border-l border-river-500/40 pl-6" data-reveal style="--delay: {{ $loop->index * 0.08 }}s">
                            <dt class="mt-2 text-white/60">{{ $stat->label }}</dt>
                            <dd class="text-5xl font-semibold tracking-tight tabular-nums">{{ number_format($stat->resolvedValue()) }}<span class="text-river-400">{{ $stat->suffix }}</span></dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>
    @endif

    {{-- 7 · Upcoming events --}}
    <x-programmes::upcoming-events />

    {{-- 8 · Partners — only logos with written permission --}}
    <section class="border-t border-navy-100 py-20 sm:py-24" aria-labelledby="partners-title">
        <div class="container-delta">
            @if($partners->isNotEmpty())
                <p id="partners-title" class="text-center text-xs font-semibold tracking-[0.22em] text-navy-400 uppercase" data-reveal>In collaboration with</p>
                <ul class="mt-10 flex flex-wrap items-center justify-center gap-x-14 gap-y-10" role="list">
                    @foreach($partners as $partner)
                        <li data-reveal>
                            @php($logo = $partner->logoUrl())
                            <a @if($partner->website_url) href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer" @endif class="block opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0" title="{{ $partner->name }}">
                                @if($logo)
                                    <img src="{{ $logo }}" alt="{{ $partner->name }}" class="h-10 w-auto max-w-44 object-contain sm:h-12" loading="lazy" decoding="async">
                                @else
                                    <span class="text-lg font-semibold text-navy-700">{{ $partner->name }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="grid items-center gap-8 rounded-3xl bg-river-50 p-8 ring-1 ring-river-100 sm:p-12 lg:grid-cols-12" data-reveal>
                    <div class="lg:col-span-8">
                        <p id="partners-title" class="eyebrow">In collaboration with</p>
                        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-navy-900 sm:text-3xl">Founding partner places are open.</h2>
                        <p class="mt-3 max-w-2xl leading-relaxed text-navy-600">Universities, companies, government bodies and development partners can help shape Delta Initiatives from day one — co-designing challenges, hosting pilots and putting evidence to work.</p>
                    </div>
                    <div class="flex lg:col-span-4 lg:justify-end">
                        <x-button :href="route('partner.index')" variant="dark" size="lg" icon="arrow-right">{{ __('core::ui.nav.partner') }}</x-button>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- 9 · Newsletter --}}
    <section class="bg-mist py-20 sm:py-24" aria-labelledby="newsletter-title">
        <div class="container-delta grid items-center gap-10 lg:grid-cols-12">
            <div class="lg:col-span-5" data-reveal>
                <p class="eyebrow">{{ __('core::ui.footer.newsletter_eyebrow') }}</p>
                <h2 id="newsletter-title" class="mt-3 text-3xl font-semibold tracking-tight text-navy-900 sm:text-4xl">{{ __('core::ui.footer.newsletter_title') }}</h2>
                <p class="mt-3 text-lg text-navy-600">{{ __('core::ui.footer.newsletter_text') }}</p>
            </div>
            <div class="lg:col-span-6 lg:col-start-7" data-reveal style="--delay:.1s">
                <x-newsletter::signup variant="light" source="home" />
            </div>
        </div>
    </section>
</x-layouts.public>
