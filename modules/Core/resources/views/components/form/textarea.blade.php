{{-- <x-form.textarea name="message" label="Message" rows="6" maxlength="2000" required hint="..." /> --}}
@props([
    'name',
    'label' => null,
    'hint' => null,
    'value' => null,
    'required' => false,
    'rows' => 5,
    'maxlength' => null,
])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $attributes->get('id', 'f_'.str_replace('.', '_', $key));
    $hasError = $errors->has($key);
    $describedBy = trim(($hint ? $id.'_hint ' : '').($maxlength ? $id.'_count ' : '').($hasError ? $id.'_error' : ''));
@endphp
<div {{ $attributes->only('class') }}>
    @if($label)
        <x-form.label :for="$id" :required="$required">{{ $label }}</x-form.label>
    @endif
    @if($hint)
        <p id="{{ $id }}_hint" class="mt-1 text-sm leading-relaxed text-navy-400">{{ $hint }}</p>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $id }}"
        rows="{{ $rows }}"
        @if($maxlength) maxlength="{{ $maxlength }}" data-counter="{{ $id }}_count" @endif
        @required($required)
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if($hasError) aria-invalid="true" @endif
        {{ $attributes->except(['class', 'id'])->merge(['class' => 'field-control mt-2 resize-y leading-relaxed']) }}
    >{{ old($key, $value) }}</textarea>
    <div class="flex items-start justify-between gap-4">
        <x-form.error :for="$key" :id="$id.'_error'" />
        @if($maxlength)
            <p id="{{ $id }}_count" class="mt-2 ml-auto shrink-0 text-xs tabular-nums text-navy-400" aria-live="polite"></p>
        @endif
    </div>
</div>
