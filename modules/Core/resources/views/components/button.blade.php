{{--
    <x-button href="..." variant="primary|dark|outline|outline-light|ghost|light|danger" size="sm|md|lg" icon="arrow-right">Label</x-button>
    Without href it renders a <button> (type="submit" by default).
--}}
@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconLeft' => null,
    'type' => 'submit',
])
@php
    // If the caller sets its own display (e.g. "hidden sm:inline-flex"), don't force inline-flex,
    // otherwise the base class would override "hidden" in the generated CSS.
    $customDisplay = preg_match('/(^|\s)(hidden|block|flex|grid|inline-block|contents)(\s|$)/', (string) $attributes->get('class', ''));
    $base = ($customDisplay ? '' : 'inline-flex ').'group/btn items-center justify-center gap-2 rounded-full font-semibold whitespace-nowrap transition duration-200 ease-[var(--ease-river)] focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60';
    $sizes = [
        'xs' => 'px-3 py-1.5 text-xs',
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-5.5 py-2.5 text-[0.9375rem]',
        'lg' => 'px-7 py-3.5 text-base',
    ];
    $variants = [
        // River Teal with Deep Navy text: 5.3:1 contrast (WCAG AA).
        'primary' => 'bg-river-500 text-navy-950 hover:bg-river-400 focus-visible:outline-river-500 shadow-[0_8px_24px_-12px_rgb(31_162_166/0.7)]',
        'dark' => 'bg-navy-900 text-white hover:bg-navy-700 focus-visible:outline-navy-900',
        'outline' => 'bg-white text-navy-900 ring-1 ring-inset ring-navy-200 hover:ring-navy-400 hover:bg-navy-50',
        'outline-light' => 'text-white ring-1 ring-inset ring-white/30 hover:ring-white/70 hover:bg-white/5',
        'ghost' => 'text-navy-700 hover:bg-navy-50 hover:text-navy-900',
        'light' => 'bg-white text-navy-900 hover:bg-river-100',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:outline-red-600',
    ];
    $classes = $base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
    $iconSize = $size === 'lg' ? 'size-5' : 'size-4';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($iconLeft)<x-icon :name="$iconLeft" :class="$iconSize" />@endif
        <span>{{ $slot }}</span>
        @if($icon)<x-icon :name="$icon" :class="$iconSize.' transition-transform duration-200 group-hover/btn:translate-x-0.5'" />@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($iconLeft)<x-icon :name="$iconLeft" :class="$iconSize" />@endif
        <span>{{ $slot }}</span>
        @if($icon)<x-icon :name="$icon" :class="$iconSize.' transition-transform duration-200 group-hover/btn:translate-x-0.5'" />@endif
    </button>
@endif
