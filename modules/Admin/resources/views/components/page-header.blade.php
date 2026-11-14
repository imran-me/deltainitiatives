@props(['title', 'description' => null, 'back' => null, 'backLabel' => 'Back', 'eyebrow' => null])
<div {{ $attributes->merge(['class' => 'mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @if($back)
            <a href="{{ $back }}" class="mb-3 inline-flex items-center gap-1.5 text-sm text-navy-500 hover:text-navy-900"><x-icon name="arrow-left" class="size-4" /> {{ $backLabel }}</a>
        @endif
        @if($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-1 text-2xl font-semibold tracking-tight text-navy-900 sm:text-[1.75rem]">{{ $title }}</h1>
        @if($description)
            <p class="mt-1.5 max-w-3xl text-sm leading-relaxed text-navy-500">{{ $description }}</p>
        @endif
    </div>
    @if(trim($slot))
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
