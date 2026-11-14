<x-admin::layout :title="$res->title($record)">
    <x-admin::page-header :title="$res->title($record)" :description="$res->subtitle($record)" :back="route('admin.resources.index', $res::$slug)" :back-label="$res::$label">
        @if(isset($record->is_demo) && $record->is_demo)
            <x-admin::badge tone="gold">Sample content</x-admin::badge>
        @endif
    </x-admin::page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-admin::panel :title="$res::$singular.' details'" class="lg:col-span-2">
            <dl class="divide-y divide-navy-100">
                @foreach($res->detailFields() as $field)
                    <div class="grid gap-1 py-3.5 first:pt-0 last:pb-0 sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm text-navy-500">{{ $field->label }}</dt>
                        <dd class="text-sm leading-relaxed break-words text-navy-900 sm:col-span-2">{{ $field->display($record, true) }}</dd>
                    </div>
                @endforeach
                <div class="grid gap-1 py-3.5 last:pb-0 sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm text-navy-500">Received</dt>
                    <dd class="text-sm text-navy-900 sm:col-span-2">{{ dhaka_time($record->created_at) }}</dd>
                </div>
            </dl>
        </x-admin::panel>

        <div class="space-y-6">
            @if($res::$editable && count($res->formFields()))
                <x-admin::panel title="Manage">
                    <form method="POST" action="{{ route('admin.resources.update', [$res::$slug, $record->getKey()]) }}" enctype="multipart/form-data" class="space-y-5" data-guard>
                        @csrf @method('PUT')
                        <div class="grid gap-5">
                            @foreach($res->formFields() as $field)
                                @include('admin::resources.field', ['field' => tap(clone $field, fn ($f) => $f->span = 'full'), 'record' => $record])
                            @endforeach
                        </div>
                        <x-button variant="dark" size="sm" icon-left="check" class="w-full">Save</x-button>
                    </form>
                </x-admin::panel>
            @endif

            @if(filled($record->email ?? null))
                <x-admin::panel title="Reply">
                    <a href="mailto:{{ $record->email }}" class="inline-flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-sm font-semibold text-navy-900 ring-1 ring-navy-200 hover:bg-navy-50"><x-icon name="mail" class="size-4" /> E-mail {{ \Illuminate\Support\Str::limit($record->name ?? $record->email, 24) }}</a>
                </x-admin::panel>
            @endif

            @if($res::$deletable)
                <form method="POST" action="{{ route('admin.resources.destroy', [$res::$slug, $record->getKey()]) }}" data-confirm="Delete this record permanently?">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700"><x-icon name="trash-2" class="size-4" /> Delete</button>
                </form>
            @endif
        </div>
    </div>
</x-admin::layout>
