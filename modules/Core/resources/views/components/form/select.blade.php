{{-- <x-form.select name="role" label="Role" :options="['a' => 'A']" placeholder="Choose…" required /> --}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'placeholder' => null,
    'hint' => null,
    'value' => null,
    'required' => false,
])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $attributes->get('id', 'f_'.str_replace('.', '_', $key));
    $hasError = $errors->has($key);
    $current = (string) old($key, $value);
    $describedBy = trim(($hint ? $id.'_hint ' : '').($hasError ? $id.'_error' : ''));
@endphp
<div {{ $attributes->only('class') }}>
    @if($label)
        <x-form.label :for="$id" :required="$required">{{ $label }}</x-form.label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $id }}"
        @required($required)
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if($hasError) aria-invalid="true" @endif
        {{ $attributes->except(['class', 'id'])->merge(['class' => 'field-control mt-2']) }}
    >
        @if($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if($hint)
        <p id="{{ $id }}_hint" class="mt-2 text-sm text-navy-400">{{ $hint }}</p>
    @endif
    <x-form.error :for="$key" :id="$id.'_error'" />
</div>
