@php($editing = $record->exists)
<x-admin::layout :title="($editing ? 'Edit ' : 'New ').$res::$singular">
    <x-admin::page-header
        :title="$editing ? $res->title($record) : 'New '.\Illuminate\Support\Str::lower($res::$singular)"
        :description="$editing ? 'Editing '.\Illuminate\Support\Str::lower($res::$singular).' · last updated '.($record->updated_at?->diffForHumans() ?? '—') : $res::$description"
        :back="$res::$hasDetail && $editing ? route('admin.resources.show', [$res::$slug, $record->getKey()]) : route('admin.resources.index', $res::$slug)"
        :back-label="$res::$label">
        @if($editing && isset($record->is_demo) && $record->is_demo)
            <x-admin::badge tone="gold">Sample content</x-admin::badge>
        @endif
    </x-admin::page-header>

    <form method="POST" enctype="multipart/form-data" data-guard
          action="{{ $editing ? route('admin.resources.update', [$res::$slug, $record->getKey()]) : route('admin.resources.store', $res::$slug) }}">
        @csrf
        @if($editing) @method('PUT') @endif

        <x-form.errors-summary class="mb-6" />

        @foreach(collect($res->formFields())->groupBy(fn ($f) => $f->section ?? '') as $section => $fields)
            <x-admin::panel :title="$section ?: null" class="mb-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach($fields as $field)
                        @include('admin::resources.field', ['field' => $field, 'record' => $record])
                    @endforeach
                </div>
            </x-admin::panel>
        @endforeach

        <div class="sticky bottom-0 -mx-4 flex items-center justify-between gap-3 border-t border-navy-100 bg-[#f5f7fa]/95 px-4 py-4 backdrop-blur sm:mx-0 sm:rounded-2xl sm:border sm:bg-white sm:px-6">
            <a href="{{ route('admin.resources.index', $res::$slug) }}" class="text-sm text-navy-500 hover:text-navy-900">Cancel</a>
            <x-button variant="dark" icon-left="check" data-loading-text="Saving…">{{ $editing ? 'Save changes' : 'Create '.\Illuminate\Support\Str::lower($res::$singular) }}</x-button>
        </div>
    </form>

    @if($editing && $res::$deletable)
        <form method="POST" action="{{ route('admin.resources.destroy', [$res::$slug, $record->getKey()]) }}" class="mt-6 text-right" data-confirm="Delete this {{ \Illuminate\Support\Str::lower($res::$singular) }}? This cannot be undone.">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700"><x-icon name="trash-2" class="size-4" /> Delete {{ \Illuminate\Support\Str::lower($res::$singular) }}</button>
        </form>
    @endif
</x-admin::layout>
