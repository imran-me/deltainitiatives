<x-admin::layout :title="$res::$label">
    <x-admin::page-header :title="$res::$label" :description="$res::$description">
        @if($res::$exportable)
            <x-button variant="outline" size="sm" icon-left="download" :href="route('admin.resources.export', [$res::$slug] + request()->only(array_keys($res->filters())) + ['format' => 'csv'])">CSV</x-button>
            <x-button variant="outline" size="sm" icon-left="file-down" :href="route('admin.resources.export', [$res::$slug] + request()->only(array_keys($res->filters())) + ['format' => 'xlsx'])">Excel</x-button>
        @endif
        @if($res::$creatable)
            <x-button variant="dark" size="sm" icon-left="plus" :href="route('admin.resources.create', $res::$slug)">New {{ \Illuminate\Support\Str::lower($res::$singular) }}</x-button>
        @endif
    </x-admin::page-header>

    @if($notice = $res->notice())
        <div class="mb-6 flex items-start gap-3 rounded-xl bg-river-50 px-4 py-3 text-sm leading-relaxed text-river-900 ring-1 ring-river-200">
            <x-icon name="info" class="mt-0.5 size-4.5 text-river-700" />
            <p>{{ $notice }}</p>
        </div>
    @endif

    <x-admin::panel :padding="false">
        @if($res->searchable() || $res->filters())
            <x-admin::filters :action="route('admin.resources.index', $res::$slug)" :search="(bool) $res->searchable()">
                @foreach($res->filters() as $column => $filter)
                    <label class="sm:w-48">
                        <span class="sr-only">{{ $filter['label'] }}</span>
                        <select name="{{ $column }}" class="field-control mt-0 py-2 text-sm" data-autosubmit>
                            <option value="">All {{ \Illuminate\Support\Str::lower($filter['label']) }}</option>
                            @foreach($filter['options'] as $value => $label)
                                <option value="{{ $value }}" @selected(request($column) === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                @endforeach
            </x-admin::filters>
        @endif

        @if($records->isEmpty())
            <x-admin::empty :icon="$res::$icon" :title="'No '.\Illuminate\Support\Str::lower($res::$label).' found'" :text="request()->hasAny(['q']) ? 'Try a different search or clear the filters.' : null">
                @if($res::$creatable && ! request()->hasAny(['q']))
                    <x-button variant="dark" size="sm" icon-left="plus" :href="route('admin.resources.create', $res::$slug)">Add the first one</x-button>
                @endif
            </x-admin::empty>
        @else
            <x-admin::table>
                <thead class="bg-navy-50/60">
                    <tr>
                        @foreach($res->indexFields() as $field)
                            <th scope="col">{{ $field->label }}</th>
                        @endforeach
                        <th scope="col" class="text-right"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-navy-100 bg-white">
                    @foreach($records as $record)
                        @php($url = $res->rowUrl($record))
                        <tr>
                            @foreach($res->indexFields() as $i => $field)
                                <td class="{{ $i === 0 ? 'font-medium text-navy-900' : 'text-navy-600' }}">
                                    @if($i === 0 && $url)
                                        <a href="{{ $url }}" class="hover:text-river-700 hover:underline">{{ $field->display($record) }}</a>
                                        @if(isset($record->is_demo) && $record->is_demo)<x-admin::badge tone="gold" class="ml-1.5">Sample</x-admin::badge>@endif
                                    @else
                                        {{ $field->display($record) }}
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    @if($res::$hasDetail)
                                        <a href="{{ route('admin.resources.show', [$res::$slug, $record->getKey()]) }}" class="inline-flex size-8 items-center justify-center rounded-lg text-navy-500 hover:bg-navy-100 hover:text-navy-900" title="View"><x-icon name="eye" class="size-4" /><span class="sr-only">View</span></a>
                                    @endif
                                    @if($res::$editable)
                                        <a href="{{ route('admin.resources.edit', [$res::$slug, $record->getKey()]) }}" class="inline-flex size-8 items-center justify-center rounded-lg text-navy-500 hover:bg-navy-100 hover:text-navy-900" title="Edit"><x-icon name="pencil" class="size-4" /><span class="sr-only">Edit</span></a>
                                    @endif
                                    @if($res::$deletable)
                                        <form method="POST" action="{{ route('admin.resources.destroy', [$res::$slug, $record->getKey()]) }}" data-confirm="Delete this {{ \Illuminate\Support\Str::lower($res::$singular) }}? This cannot be undone.">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex size-8 items-center justify-center rounded-lg text-navy-400 hover:bg-red-50 hover:text-red-600" title="Delete"><x-icon name="trash-2" class="size-4" /><span class="sr-only">Delete</span></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin::table>
            @if($records->hasPages())
                <div class="border-t border-navy-100 px-4 py-3">{{ $records->links('core::pagination') }}</div>
            @endif
        @endif
    </x-admin::panel>
</x-admin::layout>
