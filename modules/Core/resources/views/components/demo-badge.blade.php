@props(['model' => null, 'force' => false])
@if($force || show_demo_badge($model))
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full bg-silt-100 px-2 py-0.5 text-[0.6875rem] font-semibold tracking-wide text-silt-700 uppercase']) }} title="Sample content — replace before launch">
        {{ __('core::ui.demo') }}
    </span>
@endif
