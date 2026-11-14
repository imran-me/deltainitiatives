@php
    $statuses = config('ideaportal.statuses');
    $exportQuery = array_merge(request()->except(['page', 'format']), []);
    $tabQuery = request()->except(['page', 'status']);
@endphp
<x-admin::layout title="Idea submissions">
    <x-admin::page-header eyebrow="Idea Portal" title="Submissions"
        description="Every idea is recorded with its Idea ID, exact timestamp and SHA-256 fingerprint. Submissions are confidential — opening one is written to the audit log.">
        @can('ideas.export')
            <x-button variant="outline" size="sm" icon-left="download" :href="route('admin.ideas.export', $exportQuery + ['format' => 'csv'])">CSV</x-button>
            <x-button variant="outline" size="sm" icon-left="file-down" :href="route('admin.ideas.export', $exportQuery + ['format' => 'xlsx'])">Excel</x-button>
        @endcan
    </x-admin::page-header>

    {{-- Status tabs --}}
    <nav class="mb-4 -mx-1 flex gap-1 overflow-x-auto pb-1" aria-label="Filter by status">
        <a href="{{ route('admin.ideas.index', $tabQuery) }}" @class([
            'inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-sm transition',
            'bg-navy-900 font-medium text-white' => ! request('status'),
            'text-navy-600 hover:bg-white hover:text-navy-900' => request('status'),
        ]) @if(! request('status')) aria-current="page" @endif>All <span class="tabular-nums opacity-70">{{ $total }}</span></a>
        @foreach($statuses as $key => $status)
            <a href="{{ route('admin.ideas.index', $tabQuery + ['status' => $key]) }}" @class([
                'inline-flex shrink-0 items-center gap-2 rounded-full px-3.5 py-1.5 text-sm transition',
                'bg-navy-900 font-medium text-white' => request('status') === $key,
                'text-navy-600 hover:bg-white hover:text-navy-900' => request('status') !== $key,
            ]) @if(request('status') === $key) aria-current="page" @endif>{{ $status['label'] }} <span class="tabular-nums opacity-70">{{ $statusCounts[$key] ?? 0 }}</span></a>
        @endforeach
    </nav>

    <x-admin::panel :padding="false">
        <x-admin::filters :action="route('admin.ideas.index')" placeholder="ID, title, name, e-mail, institution…">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            @if(count($focusAreas))
                <label class="sm:w-48"><span class="sr-only">Focus area</span>
                    <select name="focus" class="field-control mt-0 py-2 text-sm" data-autosubmit>
                        <option value="">All focus areas</option>
                        @foreach($focusAreas as $slug => $name)<option value="{{ $slug }}" @selected(request('focus') === $slug)>{{ $name }}</option>@endforeach
                    </select>
                </label>
            @endif
            <label class="sm:w-48"><span class="sr-only">Scoring</span>
                <select name="scored" class="field-control mt-0 py-2 text-sm" data-autosubmit>
                    <option value="">Any scoring</option>
                    <option value="mine" @selected(request('scored') === 'mine')>Not yet scored by me</option>
                    <option value="no" @selected(request('scored') === 'no')>Not scored yet</option>
                    <option value="yes" @selected(request('scored') === 'yes')>Scored</option>
                </select>
            </label>
            <label class="sm:w-44"><span class="sr-only">Sort</span>
                <select name="sort" class="field-control mt-0 py-2 text-sm" data-autosubmit>
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
                    <option value="score" @selected(request('sort') === 'score')>Highest score</option>
                </select>
            </label>
            <label class="sm:w-40"><span class="sr-only">Sample content</span>
                <select name="sample" class="field-control mt-0 py-2 text-sm" data-autosubmit>
                    <option value="">Include samples</option>
                    <option value="hide" @selected(request('sample') === 'hide')>Real ideas only</option>
                    <option value="only" @selected(request('sample') === 'only')>Samples only</option>
                </select>
            </label>
            <label class="flex items-center gap-2 text-sm text-navy-500"><span>From</span><input type="date" name="from" value="{{ request('from') }}" class="field-control mt-0 w-auto py-2 text-sm"></label>
            <label class="flex items-center gap-2 text-sm text-navy-500"><span>To</span><input type="date" name="to" value="{{ request('to') }}" class="field-control mt-0 w-auto py-2 text-sm"></label>
        </x-admin::filters>

        @if($ideas->isEmpty())
            <x-admin::empty icon="lightbulb" title="No ideas match these filters"
                :text="request()->hasAny(['q', 'status', 'focus', 'scored', 'from', 'to', 'sample']) ? 'Try clearing the filters.' : 'Submissions from the Idea Portal will appear here the moment they arrive.'" />
        @else
            <x-admin::table>
                <thead class="bg-navy-50/60">
                    <tr><th>Idea</th><th>Lead</th><th>Focus area</th><th>Submitted</th><th>Status</th><th class="text-right">Score</th></tr>
                </thead>
                <tbody class="divide-y divide-navy-100 bg-white">
                    @foreach($ideas as $idea)
                        <tr>
                            <td class="min-w-72">
                                <a href="{{ route('admin.ideas.show', $idea->code) }}" class="group block">
                                    <span class="font-mono text-xs font-semibold text-river-700">{{ $idea->code }}</span>
                                    @if($idea->is_demo)<x-admin::badge tone="gold" class="ml-1.5">Sample</x-admin::badge>@endif
                                    @if($idea->locale === 'bn')<x-admin::badge tone="slate" class="ml-1">বাংলা</x-admin::badge>@endif
                                    <span class="mt-0.5 block font-medium text-navy-900 group-hover:text-river-700 group-hover:underline">{{ \Illuminate\Support\Str::limit($idea->title, 70) }}</span>
                                    @if($idea->files_count)<span class="mt-0.5 inline-flex items-center gap-1 text-xs text-navy-400"><x-icon name="file-text" class="size-3.5" /> {{ $idea->files_count }} {{ \Illuminate\Support\Str::plural('file', $idea->files_count) }}</span>@endif
                                </a>
                            </td>
                            <td>
                                <p class="text-navy-800">{{ $idea->lead_name }}</p>
                                <p class="text-xs text-navy-500">{{ \Illuminate\Support\Str::limit($idea->institution, 36) }}</p>
                            </td>
                            <td class="text-navy-600">{{ $idea->focus_area_name ?? '—' }}</td>
                            <td class="whitespace-nowrap text-navy-600"><span title="{{ dhaka_time($idea->submitted_at) }}">{{ dhaka_time($idea->submitted_at, 'j M Y') }}</span></td>
                            <td><x-ideaportal::status-badge :status="$idea->status" /></td>
                            <td class="text-right whitespace-nowrap">
                                @if($idea->scores_count)
                                    <span class="font-semibold text-navy-900 tabular-nums">{{ number_format((float) $idea->average_score, 2) }}</span>
                                    <span class="text-xs text-navy-400">/ 5 · {{ $idea->scores_count }}</span>
                                @else
                                    <span class="text-xs text-navy-400">Not scored</span>
                                @endif
                                @if($idea->scored_by_me)
                                    <span class="mt-0.5 flex items-center justify-end gap-1 text-xs text-river-700"><x-icon name="check" class="size-3.5" /> You</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin::table>
            @if($ideas->hasPages())
                <div class="border-t border-navy-100 px-4 py-3">{{ $ideas->links('core::pagination') }}</div>
            @endif
        @endif
    </x-admin::panel>
</x-admin::layout>
