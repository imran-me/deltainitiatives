{{-- Renders one admin Field as a form control. Expects $field and $record. --}}
@php
    /** @var \Modules\Admin\Support\Field $field */
    $value = $field->formValue($record);
    $common = ['name' => $field->name, 'label' => $field->label, 'hint' => $field->help, 'required' => $field->required, 'value' => $value];
@endphp
<div @class(['sm:col-span-2' => $field->span === 'full', 'sm:col-span-1' => $field->span === 'half'])>
    @switch($field->type)
        @case('textarea')
        @case('markdown')
            <x-form.textarea :name="$field->name" :label="$field->label" :hint="$field->help ?? ($field->type === 'markdown' ? 'Markdown: **bold**, *italic*, ## Heading, - list, [link](https://…)' : null)" :required="$field->required" :value="$value" :rows="$field->rows" :maxlength="$field->maxLength" :class="$field->type === 'markdown' ? '[&_textarea]:font-mono [&_textarea]:text-sm' : ''" />
            @break

        @case('select')
            <x-form.select :name="$field->name" :label="$field->label" :hint="$field->help" :required="$field->required" :value="$value" :options="$field->options" :placeholder="$field->required ? 'Choose…' : '—'" />
            @break

        @case('boolean')
            <div class="rounded-xl bg-navy-50/60 px-4 py-3 ring-1 ring-navy-100">
                <input type="hidden" name="{{ $field->name }}" value="0">
                <x-form.checkbox :name="$field->name" :checked="(bool) $value"><span class="font-medium text-navy-800">{{ $field->label }}</span>@if($field->help)<span class="block text-navy-500">{{ $field->help }}</span>@endif</x-form.checkbox>
            </div>
            @break

        @case('image')
        @case('file')
            <x-form.label :for="'f_'.$field->name" :required="$field->required && blank($record->{$field->name})">{{ $field->label }}</x-form.label>
            @if(filled($record->{$field->name}))
                <div class="mt-2 flex items-center gap-4 rounded-xl bg-navy-50/60 p-3 ring-1 ring-navy-100">
                    @if($field->type === 'image')
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk($field->disk)->url($record->{$field->name}) }}" alt="" class="h-16 w-auto max-w-40 rounded-lg bg-white object-contain ring-1 ring-navy-100">
                    @else
                        <span class="inline-flex size-10 items-center justify-center rounded-lg bg-white ring-1 ring-navy-100"><x-icon name="file-text" class="size-5 text-navy-500" /></span>
                        <span class="truncate font-mono text-xs text-navy-600">{{ basename($record->{$field->name}) }}</span>
                    @endif
                    <label class="ml-auto flex items-center gap-2 text-xs text-navy-600"><input type="checkbox" name="remove_{{ $field->name }}" value="1" class="field-check"> Remove</label>
                </div>
            @endif
            <input type="file" name="{{ $field->name }}" id="f_{{ $field->name }}" accept="{{ collect($field->mimes)->map(fn ($m) => '.'.$m)->implode(',') }}"
                class="mt-2 block w-full text-sm text-navy-600 file:mr-4 file:rounded-lg file:border-0 file:bg-navy-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-navy-700">
            <p class="mt-2 text-xs text-navy-400">{{ $field->help ? $field->help.' · ' : '' }}{{ strtoupper(implode(', ', $field->mimes)) }} · max {{ round($field->maxKb / 1024) }} MB</p>
            <x-form.error :for="$field->name" />
            @break

        @default
            <x-form.input :name="$field->name" :label="$field->label" :hint="$field->help" :required="$field->required" :value="$value" :placeholder="$field->placeholder"
                :type="match ($field->type) { 'email' => 'email', 'url' => 'url', 'number' => 'number', 'date' => 'date', 'datetime' => 'datetime-local', default => 'text' }"
                :readonly="$field->readonly" :maxlength="$field->type === 'text' ? ($field->maxLength ?? 255) : null" />
    @endswitch
</div>
