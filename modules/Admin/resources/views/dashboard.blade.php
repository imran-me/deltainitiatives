@php($hour = (int) now()->setTimezone('Asia/Dhaka')->format('G'))
<x-admin::layout title="Dashboard">
    <x-admin::page-header
        :eyebrow="now()->setTimezone('Asia/Dhaka')->format('l, j F Y')"
        :title="($hour >= 5 && $hour < 12 ? 'Good morning' : ($hour >= 12 && $hour < 17 ? 'Good afternoon' : 'Good evening')).', '.\Illuminate\Support\Str::before(auth()->user()->name, ' ')"
        description="Here is what needs attention across the Idea Portal, programmes and inboxes.">
        @can('ideas.view')
            @if(Route::has('admin.ideas.index'))
                <x-button variant="dark" size="sm" icon-left="lightbulb" :href="route('admin.ideas.index')">Review ideas</x-button>
            @endif
        @endcan
    </x-admin::page-header>

    @if($demoCount > 0)
        <div class="mb-8 flex flex-col gap-4 rounded-2xl bg-silt-100/70 p-5 ring-1 ring-silt/30 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <x-icon name="info" class="mt-0.5 size-5 text-silt-700" />
                <div>
                    <p class="text-sm font-semibold text-navy-900">{{ $demoCount }} sample records are live on the site</p>
                    <p class="mt-0.5 text-sm text-navy-600">They are marked “Sample” publicly. Replace them with real content, then remove all samples before launch.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.demo.purge') }}" data-confirm="Permanently delete all {{ $demoCount }} sample records? Real content is not affected.">
                @csrf @method('DELETE')
                <x-button variant="outline" size="sm" icon-left="trash-2">Remove samples</x-button>
            </form>
        </div>
    @endif

    @if(count($stats))
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($stats as $stat)
                <x-admin::stat :label="$stat['label']" :value="$stat['resolved']" :icon="$stat['icon']" :hint="$stat['hint_resolved']"
                    :href="$stat['route'] && Route::has($stat['route']) ? route($stat['route'], $stat['route_params'] ?? []) : null" />
            @endforeach
        </div>
    @endif

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        @foreach($panels as $panel)
            <div @class(['lg:col-span-2' => $panel['span'] === 2, 'lg:col-span-3' => $panel['span'] === 3])>
                @include($panel['view'], $panel['resolved'])
            </div>
        @endforeach

        @if($recentActivity->isNotEmpty())
            <x-admin::panel title="Recent activity" description="From the audit log" :padding="false" class="lg:col-span-1">
                <x-slot:actions><a href="{{ route('admin.audit.index') }}" class="text-xs font-medium text-river-700 hover:underline">View all</a></x-slot:actions>
                <ul class="divide-y divide-navy-100" role="list">
                    @foreach($recentActivity as $log)
                        <li class="flex gap-3 px-5 py-3">
                            <x-avatar :name="$log->user->name" size="size-7" class="rounded-lg text-[0.6rem]" />
                            <div class="min-w-0">
                                <p class="text-sm text-navy-800"><span class="font-medium">{{ $log->user->name }}</span> · {{ $log->description }}</p>
                                <p class="text-xs text-navy-400" title="{{ dhaka_time($log->created_at) }}">{{ $log->created_at?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </x-admin::panel>
        @endif
    </div>
</x-admin::layout>
