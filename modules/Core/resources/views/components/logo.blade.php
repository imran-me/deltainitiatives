{{-- Delta Initiatives lock-up. Colours follow currentColor so it works on navy and white. --}}
@props(['compact' => false])
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <x-delta-mark class="h-8 w-auto sm:h-9" />
    @unless($compact)
        <span class="flex items-baseline gap-2 text-[0.8rem] leading-none tracking-[0.32em] sm:text-[0.85rem]">
            <span class="font-bold">DELTA</span>
            <span class="logo-word-2 font-normal">INITIATIVES</span>
        </span>
    @endunless
</span>
