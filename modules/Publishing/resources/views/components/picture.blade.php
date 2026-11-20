{{--
    Responsive library or uploaded image with optional credit line.
    <x-publishing::picture image="padma-fishing-boat" sizes="(max-width: 760px) 100vw, 50vw" :credit="true" />
--}}
@props(['image' => null, 'upload' => null, 'alt' => '', 'sizes' => '100vw', 'credit' => false, 'eager' => false])
@php
    $pic = \Modules\Publishing\Support\Library::image($image, $upload, (string) $alt);
    $cr = $credit ? \Modules\Publishing\Support\Library::credit($image) : null;
@endphp
@if($pic)
    <figure {{ $attributes->merge(['class' => 'pic']) }}>
        <img src="{{ $pic['src'] }}" @if($pic['srcset'] !== '') srcset="{{ $pic['srcset'] }}" sizes="{{ $sizes }}" @endif
             width="{{ $pic['width'] }}" height="{{ $pic['height'] }}" alt="{{ $pic['alt'] }}"
             @if($eager) fetchpriority="high" @else loading="lazy" @endif decoding="async">
        @if($cr)
            <figcaption class="credit">{{ $cr['caption'] }}. Photo: {{ $cr['author'] }}, <a href="{{ $cr['source'] }}" rel="noopener" target="_blank">{{ $cr['licence'] }}</a>, via Wikimedia Commons.</figcaption>
        @endif
    </figure>
@endif
