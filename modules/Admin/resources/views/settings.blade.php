<x-admin::layout title="Settings">
    <x-admin::page-header title="Settings" description="Site-wide details used across every page, e-mail and the Idea Portal. Changes apply immediately." />

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" data-guard>
        @csrf @method('PUT')
        <x-form.errors-summary class="mb-6" />

        <div class="space-y-6">
            @foreach($groups as $group => $fields)
                <x-admin::panel :title="$group">
                    <div class="grid gap-6 sm:grid-cols-2">
                        @foreach($fields as $key => $def)
                            @php($value = $values[$key] ?? config('core.settings_defaults.'.$key))
                            <div @class(['sm:col-span-2' => in_array($def['type'], ['textarea', 'file', 'boolean'])])>
                                @switch($def['type'])
                                    @case('textarea')
                                        <x-form.textarea :name="$key" :label="$def['label']" :hint="$def['help'] ?? null" :value="$value" rows="3" maxlength="2000" />
                                        @break
                                    @case('boolean')
                                        <div class="rounded-xl bg-navy-50/60 px-4 py-3 ring-1 ring-navy-100">
                                            <input type="hidden" name="{{ $key }}" value="0">
                                            <x-form.checkbox :name="$key" :checked="filter_var($value, FILTER_VALIDATE_BOOLEAN)"><span class="font-medium text-navy-800">{{ $def['label'] }}</span>@isset($def['help'])<span class="block text-navy-500">{{ $def['help'] }}</span>@endisset</x-form.checkbox>
                                        </div>
                                        @break
                                    @case('file')
                                        <x-form.label :for="'f_'.$key">{{ $def['label'] }}</x-form.label>
                                        @if(filled($values[$key] ?? null))
                                            <div class="mt-2 flex items-center gap-3 rounded-xl bg-navy-50/60 p-3 text-sm ring-1 ring-navy-100">
                                                <x-icon name="file-text" class="size-5 text-navy-500" />
                                                <span class="truncate font-mono text-xs">{{ basename($values[$key]) }}</span>
                                                @if(Route::has('partner.brochure'))<a href="{{ route('partner.brochure') }}" class="text-xs font-medium text-river-700 hover:underline" target="_blank">Preview</a>@endif
                                                <label class="ml-auto flex items-center gap-2 text-xs"><input type="checkbox" name="remove_{{ $key }}" value="1" class="field-check"> Remove</label>
                                            </div>
                                        @endif
                                        <input type="file" name="{{ $key }}" id="f_{{ $key }}" accept=".pdf" class="mt-2 block w-full text-sm text-navy-600 file:mr-4 file:rounded-lg file:border-0 file:bg-navy-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white">
                                        @isset($def['help'])<p class="mt-2 text-xs text-navy-400">{{ $def['help'] }}</p>@endisset
                                        <x-form.error :for="$key" />
                                        @break
                                    @default
                                        <x-form.input :name="$key" :label="$def['label']" :hint="$def['help'] ?? null" :value="$value" :type="in_array($def['type'], ['email', 'url']) ? $def['type'] : 'text'" />
                                @endswitch
                            </div>
                        @endforeach
                    </div>
                </x-admin::panel>
            @endforeach
        </div>

        <div class="sticky bottom-0 mt-6 flex justify-end border-t border-navy-100 bg-[#f5f7fa]/95 py-4 backdrop-blur">
            <x-button variant="dark" icon-left="check" data-loading-text="Saving…">Save settings</x-button>
        </div>
    </form>
</x-admin::layout>
