{{-- v3 editorial page head for server-rendered pages (forms, receipts, events, legal). --}}
@props(['eyebrow' => null, 'title', 'intro' => null, 'bangla' => null])
<section {{ $attributes->merge(['class' => 'phead'.(isset($visual) && trim($visual) !== '' ? ' has-visual' : '')]) }}>
    <svg class="contours" viewBox="0 0 1440 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><g fill="none" stroke="#E6F5F5" stroke-width="1.2">@for($i = 0; $i < 10; $i++)<path d="M-50 {{ 120 + $i * 70 }}C300 {{ 60 + $i * 70 }} 520 {{ 200 + $i * 64 }} 820 {{ 150 + $i * 66 }}S1300 {{ 90 + $i * 72 }} 1500 {{ 160 + $i * 70 }}"/>@endfor</g></svg>
    <div class="wrap phead-in"><div>
        @if($eyebrow)
            <div class="eyebrow">{{ $eyebrow }}</div>
        @endif
        <h1>{{ $title }}</h1>
        @if($bangla)
            <p class="bn hero-bn" lang="bn">{{ $bangla }}</p>
        @endif
        @if($intro)
            <p class="lead">{{ $intro }}</p>
        @endif
        @if(trim($slot))
            <div class="phead-slot">{{ $slot }}</div>
        @endif
    </div>
    @if(isset($visual) && trim($visual) !== "")
        <div class="phead-visual">{{ $visual }}</div>
    @endif
    </div>
</section>
