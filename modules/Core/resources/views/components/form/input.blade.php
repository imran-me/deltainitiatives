{{-- <x-form.input name="email" type="email" label="Email" required hint="..." /> --}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'hint' => null,
    'value' => null,
    'required' => false,
    'optionalLabel' => true,
])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $attributes->get('id', 'f_'.str_replace('.', '_', $key));
    $hasError = $errors->has($key);
    $describedBy = trim(($hint ? $id.'_hint ' : '').($hasError ? $id.'_error' : ''));
@endphp
<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
        <x-form.label :for="$id" :required="$required" :optional="$optionalLabel">{{ $label }}</x-form.label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        @if($type !== 'password' && $type !== 'file') value="{{ old($key, $value) }}" @endif
        @required($required)
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if($hasError) aria-invalid="true" @endif
        {{ $attributes->except(['class', 'id'])->merge(['class' => 'field-control mt-2']) }}
    >
    @if($hint)
        <p id="{{ $id }}_hint" class="mt-2 text-sm text-navy-400">{{ $hint }}</p>
    @endif
    <x-form.error :for="$key" :id="$id.'_error'" />
</div>
