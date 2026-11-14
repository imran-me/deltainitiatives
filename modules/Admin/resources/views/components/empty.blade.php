@props(['icon' => 'inbox', 'title' => 'Nothing here yet', 'text' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-16 text-center']) }}>
    <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-navy-50 text-navy-400 ring-1 ring-navy-100"><x-icon :name="$icon" class="size-6" /></span>
    <p class="mt-4 text-sm font-semibold text-navy-900">{{ $title }}</p>
    @if($text)<p class="mt-1 max-w-sm text-sm text-navy-500">{{ $text }}</p>@endif
    @if(trim($slot))<div class="mt-5">{{ $slot }}</div>@endif
</div>
