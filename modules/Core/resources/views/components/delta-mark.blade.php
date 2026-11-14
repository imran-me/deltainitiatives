{{-- The Δ mark: a delta triangle with the river and its distributaries. --}}
@props(['lines' => 'currentColor', 'frame' => '#1FA2A6'])
<svg {{ $attributes->merge(['class' => 'h-9 w-auto']) }} viewBox="0 0 48 42" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
    <path d="M24 3.5 44.5 38.5h-41L24 3.5Z" stroke="{{ $frame }}" stroke-width="2.6" stroke-linejoin="round"/>
    <path d="M24 15v23.5" stroke="{{ $lines }}" stroke-width="2.2" stroke-linecap="round"/>
    <path d="M24 19.5c-2.6 6.4-5.6 12-9.4 19" stroke="{{ $lines }}" stroke-width="2.2" stroke-linecap="round"/>
    <path d="M24 19.5c2.6 6.4 5.6 12 9.4 19" stroke="{{ $lines }}" stroke-width="2.2" stroke-linecap="round"/>
</svg>
