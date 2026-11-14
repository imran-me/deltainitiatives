{{-- Session status messages: session('success'), session('error'), session('status'). --}}
@php
    $messages = array_filter([
        'success' => session('success') ?? session('status'),
        'error' => session('error'),
        'warning' => session('warning'),
    ]);
    $styles = [
        'success' => ['bg-river-50 ring-river-200 text-river-900', 'circle-check', 'text-river-600'],
        'error' => ['bg-red-50 ring-red-200 text-red-900', 'circle-alert', 'text-red-600'],
        'warning' => ['bg-silt-100 ring-silt/40 text-silt-700', 'info', 'text-silt-700'],
    ];
@endphp
@foreach($messages as $type => $message)
    <div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl px-4 py-3 text-sm ring-1 '.$styles[$type][0]]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}" data-flash>
        <x-icon :name="$styles[$type][1]" class="mt-0.5 size-5 {{ $styles[$type][2] }}" />
        <p class="flex-1 leading-relaxed">{{ $message }}</p>
        <button type="button" class="-m-1 rounded p-1 opacity-60 hover:opacity-100" data-dismiss aria-label="Dismiss">
            <x-icon name="x" class="size-4" />
        </button>
    </div>
@endforeach
