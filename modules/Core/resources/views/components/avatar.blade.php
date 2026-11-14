{{-- Real photo if provided; otherwise a monogram on a Δ-patterned tile (never stock photos). --}}
@props(['name' => '', 'src' => null, 'size' => 'size-14'])
@if($src)
    <img src="{{ $src }}" alt="{{ $name }}" loading="lazy" decoding="async" {{ $attributes->merge(['class' => $size.' rounded-2xl object-cover bg-navy-100']) }}>
@else
    <span {{ $attributes->merge(['class' => $size.' relative inline-flex items-center justify-center overflow-hidden rounded-2xl bg-navy-900 font-semibold tracking-wide text-white']) }} aria-hidden="true">
        <svg class="absolute inset-0 h-full w-full opacity-30" viewBox="0 0 40 40" fill="none"><path d="M20 6 36 36H4L20 6Z" stroke="#1FA2A6" stroke-width="1"/></svg>
        <span class="relative">{{ initials($name) }}</span>
    </span>
@endif
