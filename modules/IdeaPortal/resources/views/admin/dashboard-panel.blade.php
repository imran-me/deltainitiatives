{{-- Admin dashboard: latest idea submissions. Expects $latestIdeas. --}}
<x-admin::panel title="Latest ideas" description="Newest submissions from the Idea Portal" :padding="false">
    <x-slot:actions><a href="{{ route('admin.ideas.index') }}" class="text-xs font-medium text-river-700 hover:underline">All submissions</a></x-slot:actions>
    @if($latestIdeas->isEmpty())
        <x-admin::empty icon="lightbulb" title="No ideas yet" text="New submissions appear here as soon as they arrive." />
    @else
        <ul class="divide-y divide-navy-100" role="list">
            @foreach($latestIdeas as $idea)
                <li>
                    <a href="{{ route('admin.ideas.show', $idea->code) }}" class="flex items-center gap-4 px-5 py-3.5 transition hover:bg-navy-50/60">
                        <span class="hidden size-9 shrink-0 items-center justify-center rounded-xl bg-river-50 text-river-700 ring-1 ring-river-100 sm:inline-flex"><x-icon name="lightbulb" class="size-4" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-navy-900">{{ $idea->title }}</p>
                            <p class="truncate text-xs text-navy-500"><span class="font-mono text-river-700">{{ $idea->code }}</span> · {{ $idea->lead_name }} · {{ $idea->submitted_at?->diffForHumans() }}</p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            <x-ideaportal::status-badge :status="$idea->status" />
                            @if($idea->is_demo)<span class="text-[0.65rem] font-semibold tracking-wide text-silt-700 uppercase">Sample</span>@endif
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</x-admin::panel>
