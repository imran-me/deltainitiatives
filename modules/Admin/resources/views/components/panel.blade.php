@props(['title' => null, 'description' => null, 'padding' => true])
<section {{ $attributes->merge(['class' => 'rounded-2xl bg-white shadow-[0_1px_2px_rgb(11_31_58/0.04)] ring-1 ring-navy-100']) }}>
    @if($title || isset($actions))
        <header class="flex items-start justify-between gap-4 border-b border-navy-100 px-5 py-4 sm:px-6">
            <div>
                @if($title)<h2 class="text-sm font-semibold text-navy-900">{{ $title }}</h2>@endif
                @if($description)<p class="mt-0.5 text-xs leading-relaxed text-navy-500">{{ $description }}</p>@endif
            </div>
            @isset($actions)<div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>@endisset
        </header>
    @endif
    <div @class(['px-5 py-5 sm:px-6' => $padding])>{{ $slot }}</div>
</section>
