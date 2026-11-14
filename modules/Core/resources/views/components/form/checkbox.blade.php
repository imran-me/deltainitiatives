{{-- <x-form.checkbox name="consent" required>I agree to the <a ...>terms</a></x-form.checkbox> --}}
@props(['name', 'value' => '1', 'checked' => false, 'required' => false])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $attributes->get('id', 'f_'.str_replace('.', '_', $key));
    $hasError = $errors->has($key);
    $isChecked = old() ? (bool) old($key) : $checked;
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="flex cursor-pointer items-start gap-3 text-sm leading-relaxed text-navy-700">
        <input type="checkbox" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" @checked($isChecked) @required($required)
            @if($hasError) aria-invalid="true" aria-describedby="{{ $id }}_error" @endif
            {{ $attributes->except(['class', 'id'])->merge(['class' => 'field-check mt-0.5']) }}>
        <span>{{ $slot }}</span>
    </label>
    <x-form.error :for="$key" :id="$id.'_error'" />
</div>
