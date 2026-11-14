@props(['for' => null, 'required' => false, 'optional' => true])
<label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => 'flex items-baseline justify-between gap-3 text-sm font-medium text-navy-800']) }}>
    <span>{{ $slot }}@if($required)<span class="text-river-700" aria-hidden="true"> *</span>@endif</span>
    @if(! $required && $optional)
        <span class="text-xs font-normal text-navy-400">{{ __('core::ui.forms.optional') }}</span>
    @endif
</label>
