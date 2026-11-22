<x-layouts.public title="Gallery" description="Photographs from the licensed Delta library: rivers, fields, solar and the places our research works in. Open any photo in the viewer.">
    <x-page-hero eyebrow="Gallery" title="Places we work in." intro="Licensed and public-domain photographs. Open one to see it large; use the arrow keys or swipe to move, Esc to close.">
        <a class="btn ghost" href="{{ route('feed.index') }}">Open the feed</a>
    </x-page-hero>
    <section class="sec"><div class="wrap">
        <div class="masonry" data-lightbox id="g-grid">
            @foreach($photos as $p)
                <a class="g-item" href="{{ $p['src'] }}" data-lb data-item data-caption="{{ $p['caption'] }}" data-credit="{{ $p['credit'] }}" @if($p['source']) data-source="{{ $p['source'] }}" @endif>
                    <figure class="pic"><img src="{{ $p['thumb'] }}" width="{{ $p['w'] }}" height="{{ $p['h'] }}" alt="{{ $p['alt'] }}" loading="lazy" decoding="async"></figure>
                    <span class="g-cap">{{ $p['caption'] }}</span>
                </a>
            @endforeach
        </div>
        <p class="hint">Credits are shown in the viewer. Photos are not Delta work unless stated.</p>
    </div></section>
</x-layouts.public>
