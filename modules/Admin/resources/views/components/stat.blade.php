@props(['label', 'value', 'icon' => 'circle-dot', 'href' => null, 'hint' => null])
@php($tag = $href ? 'a' : 'div')
<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'group flex flex-col rounded-2xl bg-white p-5 ring-1 ring-navy-100 transition '.($href ? 'hover:ring-river-300 hover:shadow-[0_12px_32px_-20px_rgb(11_31_58/0.4)]' : '')]) }}>
    <div class="flex items-center justify-between">
        <p class="text-sm text-navy-500">{{ $label }}</p>
        <span class="inline-flex size-9 items-center justify-center rounded-xl bg-river-50 text-river-700 ring-1 ring-river-100"><x-icon :name="$icon" class="size-4.5" /></span>
    </div>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-navy-900 tabular-nums">{{ $value ?? '—' }}</p>
    @if($hint)
        <p class="mt-1 text-xs text-navy-400">{{ $hint }}</p>
    @endif
</{{ $tag }}>
