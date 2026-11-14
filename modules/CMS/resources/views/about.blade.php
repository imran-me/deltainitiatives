@php
    $intro = block('about.intro');
    $story = block('about.story');
    $vision = block('about.vision');
    $mission = block('about.mission');
    $origin = block('about.origin');
@endphp
<x-layouts.public title="About" :description="strip_tags((string) $intro->body)">
    <x-page-hero :eyebrow="$intro->eyebrow ?: 'About'" :title="$intro->title ?: 'About Delta Initiatives'" :intro="strip_tags((string) $intro->body)" bangla="আমাদের সম্পর্কে" />

    {{-- The name --}}
    <section class="py-24 sm:py-32" aria-labelledby="name-title">
        <div class="container-delta grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5" data-reveal>
                <p class="eyebrow">{{ $story->eyebrow ?: 'The story of our name' }}</p>
                <h2 id="name-title" class="mt-4 text-3xl font-semibold tracking-tight text-balance text-navy-900 sm:text-4xl">{{ $story->title }}</h2>
            </div>
            <div class="prose-delta lg:col-span-7" data-reveal style="--delay:.1s">{{ markdown($story->body) }}</div>
        </div>
    </section>

    {{-- Vision & mission --}}
    <section class="pb-24 sm:pb-32" aria-label="Vision and mission">
        <div class="container-delta grid gap-5 lg:grid-cols-2">
            <article class="on-dark relative isolate overflow-hidden rounded-3xl bg-navy-900 p-8 text-white sm:p-12" data-reveal>
                <x-delta-mark class="absolute -right-6 -bottom-8 -z-10 h-48 w-auto opacity-10" />
                <p class="eyebrow">{{ $vision->eyebrow ?: 'Vision' }}</p>
                <p class="mt-5 text-2xl leading-snug font-medium tracking-tight text-balance sm:text-[1.75rem]">{{ $vision->title }}</p>
                @if($vision->body)<div class="prose-delta on-dark mt-5">{{ markdown($vision->body) }}</div>@endif
            </article>
            <article class="rounded-3xl bg-river-50 p-8 ring-1 ring-river-100 sm:p-12" data-reveal style="--delay:.1s">
                <p class="eyebrow">{{ $mission->eyebrow ?: 'Mission' }}</p>
                <p class="mt-5 text-2xl leading-snug font-medium tracking-tight text-balance text-navy-900 sm:text-[1.75rem]">{{ $mission->title }}</p>
                @if($mission->body)<div class="prose-delta mt-5">{{ markdown($mission->body) }}</div>@endif
            </article>
        </div>
    </section>

    {{-- Values --}}
    @if($values->isNotEmpty())
        <section class="border-y border-navy-100 bg-navy-50/50 py-24 sm:py-32" aria-labelledby="values-title">
            <div class="container-delta">
                <x-section-heading eyebrow="Values" title="What we hold ourselves to" id="values-title" />
                <ul role="list" @class(['mt-14 grid gap-5 sm:grid-cols-2', $values->count() >= 5 ? 'lg:grid-cols-5' : 'lg:grid-cols-4'])>
                    @foreach($values as $value)
                        <li class="card-delta p-6" data-reveal style="--delay: {{ $loop->index * 0.06 }}s">
                            <x-icon :name="$value->icon" class="size-6 text-river-700" />
                            <h3 class="mt-5 font-semibold text-navy-900">{{ $value->title }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-navy-500">{{ $value->summary }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Team --}}
    @if($team->isNotEmpty())
        <section class="py-24 sm:py-32" aria-labelledby="team-title" id="team">
            <div class="container-delta">
                <x-section-heading eyebrow="Team" title="The people building Delta Initiatives" intro="Students, researchers and practitioners who believe Bangladesh's next ideas deserve a real path to impact." id="team-title" />
                <ul class="mt-14 grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-4" role="list">
                    @foreach($team as $member)
                        @include('cms::partials.person', ['member' => $member])
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Advisory Board --}}
    @if($advisors->isNotEmpty())
        <section class="border-t border-navy-100 bg-mist py-24 sm:py-32" aria-labelledby="advisors-title" id="advisory-board">
            <div class="container-delta">
                <x-section-heading eyebrow="Advisory Board" title="Guidance from experience" intro="Independent advisors who keep our research rigorous and our policy work grounded." id="advisors-title" />
                <ul class="mt-14 grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-4" role="list">
                    @foreach($advisors as $member)
                        @include('cms::partials.person', ['member' => $member])
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Our origin --}}
    <section class="py-24 sm:py-32" aria-labelledby="origin-title">
        <div class="container-delta grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5" data-reveal>
                <p class="eyebrow">{{ $origin->eyebrow ?: 'Our origin' }}</p>
                <h2 id="origin-title" class="mt-4 text-3xl font-semibold tracking-tight text-balance text-navy-900 sm:text-4xl">{{ $origin->title }}</h2>
            </div>
            <div class="lg:col-span-7" data-reveal style="--delay:.1s">
                <div class="prose-delta">{{ markdown($origin->body) }}</div>
                <div class="mt-10 flex flex-wrap gap-3">
                    <x-button :href="localized_route('join.create')" variant="dark" icon="arrow-right">{{ __('core::ui.nav.join') }}</x-button>
                    <x-button :href="route('partner.index')" variant="outline">{{ __('core::ui.nav.partner') }}</x-button>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
