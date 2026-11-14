{{-- Navy header for inner pages. --}}
@props(['eyebrow' => null, 'title', 'intro' => null, 'bangla' => null])
<section {{ $attributes->merge(['class' => 'on-dark relative isolate overflow-hidden bg-navy-900 pt-36 pb-20 text-white sm:pt-44 sm:pb-24']) }}>
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(60%_80%_at_80%_0%,rgb(31_162_166/0.16),transparent_70%)]"></div>
    <x-delta-lines variant="page" class="-z-10 opacity-70" />
    <div class="container-delta">
        <div class="hero-enter max-w-3xl">
            @if($eyebrow)
                <p class="eyebrow">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-5 text-4xl font-semibold tracking-[-0.03em] text-balance sm:text-5xl lg:text-6xl lg:leading-[1.04]">{{ $title }}</h1>
            @if($bangla)
                <p class="font-bangla mt-4 text-lg text-river-300" lang="bn">{{ $bangla }}</p>
            @endif
            @if($intro)
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-pretty text-white/70 sm:text-xl">{{ $intro }}</p>
            @endif
            @if(trim($slot))
                <div class="mt-9 flex flex-wrap gap-3">{{ $slot }}</div>
            @endif
        </div>
    </div>
</section>
