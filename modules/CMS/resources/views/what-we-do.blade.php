@php
    $intro = block('whatwedo.intro');
    $armCtas = [
        'delta-labs' => [localized_route('ideas.create'), __('core::ui.nav.submit_idea')],
        'delta-policy' => [route('partner.index'), __('core::ui.nav.partner')],
        'delta-challenge' => [route('programmes.index'), __('core::ui.nav.programmes')],
    ];
@endphp
<x-layouts.public title="What We Do" :description="strip_tags((string) $intro->body)">
    <x-page-hero :eyebrow="$intro->eyebrow ?: 'What we do'" :title="$intro->title ?: 'From research to prototype to policy'" :intro="strip_tags((string) $intro->body)" bangla="আমরা যা করি">
        @foreach($arms as $arm)
            <a href="#{{ $arm->slug }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium text-white/80 ring-1 ring-white/20 transition hover:bg-white/5 hover:text-white">
                <x-icon :name="$arm->icon" class="size-4 text-river-400" /> {{ $arm->name }}
            </a>
        @endforeach
    </x-page-hero>

    {{-- The three arms --}}
    @foreach($arms as $arm)
        <section id="{{ $arm->slug }}" @class(['scroll-mt-20 py-24 sm:py-28', 'bg-mist' => $loop->even]) aria-labelledby="{{ $arm->slug }}-title">
            <div class="container-delta grid gap-12 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-5" data-reveal>
                    <div class="flex items-center gap-4">
                        <span class="inline-flex size-14 items-center justify-center rounded-2xl bg-navy-900 text-river-400"><x-icon :name="$arm->icon" class="size-7" /></span>
                        <span class="font-mono text-sm text-navy-400">0{{ $loop->iteration }} / 0{{ $loop->count }}</span>
                    </div>
                    <p class="mt-8 eyebrow">{{ $arm->tagline }}</p>
                    <h2 id="{{ $arm->slug }}-title" class="mt-3 text-4xl font-semibold tracking-tight text-navy-900 sm:text-5xl">{{ $arm->name }}</h2>
                    <p class="mt-5 text-lg leading-relaxed text-navy-600">{{ $arm->summary }}</p>
                    @isset($armCtas[$arm->slug])
                        <x-button :href="$armCtas[$arm->slug][0]" variant="dark" icon="arrow-right" class="mt-8">{{ $armCtas[$arm->slug][1] }}</x-button>
                    @endisset
                </div>
                <div class="lg:col-span-7" data-reveal style="--delay:.1s">
                    @if($arm->body)
                        <div class="prose-delta">{{ markdown($arm->body) }}</div>
                    @endif
                    @if($highlights = $arm->highlightList())
                        <ul class="mt-8 grid gap-3 sm:grid-cols-2" role="list">
                            @foreach($highlights as $highlight)
                                <li class="flex items-start gap-3 rounded-xl bg-white p-4 text-sm font-medium text-navy-800 ring-1 ring-navy-100">
                                    <x-icon name="circle-check" class="mt-px size-5 text-river-600" /> {{ $highlight }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>
    @endforeach

    {{-- Pipeline, in detail --}}
    @if($steps->isNotEmpty())
        <section class="on-dark relative isolate overflow-hidden bg-navy-900 py-24 text-white sm:py-32" aria-labelledby="pipeline-title" id="pipeline">
            <x-delta-lines variant="page" class="-z-10 opacity-40" />
            <div class="container-delta grid gap-14 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <x-section-heading eyebrow="The pipeline" title="How an idea becomes impact" intro="Every submission follows the same transparent path. Not every idea goes all the way — but every idea is read, scored fairly and answered." :dark="true" id="pipeline-title" class="lg:sticky lg:top-28" />
                </div>
                <ol class="relative space-y-4 lg:col-span-7 lg:col-start-6" role="list">
                    @foreach($steps as $step)
                        <li class="flex gap-5 rounded-2xl bg-white/[0.04] p-6 ring-1 ring-white/10" data-reveal style="--delay: {{ $loop->index * 0.05 }}s">
                            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-full bg-river-500/15 text-river-400 ring-1 ring-river-400/30"><x-icon :name="$step->icon" class="size-5" /></span>
                            <div>
                                <p class="font-mono text-xs text-river-400">Step {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                <h3 class="mt-1 text-lg font-semibold">{{ $step->title }}</h3>
                                <p class="mt-1.5 leading-relaxed text-white/65">{{ $step->summary }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    {{-- Focus areas with examples --}}
    @if($focusAreas->isNotEmpty())
        <section class="scroll-mt-20 py-24 sm:py-32" id="focus-areas" aria-labelledby="focus-title">
            <div class="container-delta">
                <x-section-heading eyebrow="Focus areas" :title="$focusAreas->count() === 8 ? 'Eight focus areas' : 'Our focus areas'" intro="Where we look for ideas, build evidence and convene policy conversations. Example problems are illustrative — bring us the one you know best." id="focus-title" />
                <div class="mt-14 grid gap-5 md:grid-cols-2">
                    @foreach($focusAreas as $area)
                        <article class="card-delta flex gap-5 p-6 sm:p-7" data-reveal style="--delay: {{ ($loop->index % 2) * 0.06 }}s">
                            <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-2xl bg-river-50 text-river-700 ring-1 ring-river-100"><x-icon :name="$area->icon" class="size-6" /></span>
                            <div>
                                <h3 class="text-lg font-semibold text-navy-900">{{ $area->name }}</h3>
                                <p class="mt-1.5 leading-relaxed text-navy-600">{{ $area->summary }}</p>
                                @if($examples = $area->exampleList())
                                    <ul class="mt-4 flex flex-wrap gap-2" role="list" aria-label="Example problems">
                                        @foreach($examples as $example)
                                            <li class="rounded-full bg-navy-50 px-3 py-1 text-xs font-medium text-navy-600 ring-1 ring-navy-100">{{ $example }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Call to action --}}
    <section class="pb-24 sm:pb-32">
        <div class="container-delta">
            <div class="grid items-center gap-8 rounded-3xl bg-river-50 p-8 ring-1 ring-river-100 sm:p-12 lg:grid-cols-12" data-reveal>
                <div class="lg:col-span-7">
                    <h2 class="text-3xl font-semibold tracking-tight text-navy-900">Have an idea in one of these areas?</h2>
                    <p class="mt-3 text-lg text-navy-600">Submit it through the Idea Portal. You get an ID, a timestamp and a fingerprint the moment you press send.</p>
                </div>
                <div class="flex flex-wrap gap-3 lg:col-span-5 lg:justify-end">
                    <x-button :href="localized_route('ideas.create')" variant="dark" size="lg" icon="arrow-right">{{ __('core::ui.nav.submit_idea') }}</x-button>
                    <x-button :href="route('programmes.index')" variant="outline" size="lg">{{ __('core::ui.nav.programmes') }}</x-button>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
