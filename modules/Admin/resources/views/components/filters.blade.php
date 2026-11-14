{{-- GET filter bar: search box + any selects passed in the slot. --}}
@props(['action', 'search' => true, 'placeholder' => 'Search…'])
<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'flex flex-col gap-3 border-b border-navy-100 p-4 sm:flex-row sm:flex-wrap sm:items-center']) }} role="search">
    @if($search)
        <label class="relative min-w-0 flex-1 sm:max-w-xs">
            <span class="sr-only">Search</span>
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-navy-400" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" class="field-control mt-0 py-2 pl-9 text-sm">
        </label>
    @endif
    {{ $slot }}
    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-navy-900 px-3.5 py-2 text-sm font-medium text-white hover:bg-navy-700"><x-icon name="funnel" class="size-4" /> Apply</button>
        @if(collect(request()->except('page'))->filter()->isNotEmpty())
            <a href="{{ $action }}" class="rounded-lg px-3 py-2 text-sm text-navy-500 hover:bg-navy-50 hover:text-navy-900">Clear</a>
        @endif
    </div>
</form>
