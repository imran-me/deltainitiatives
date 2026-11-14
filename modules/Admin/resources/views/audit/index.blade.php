<x-admin::layout title="Audit log">
    <x-admin::page-header title="Audit log" description="An append-only record of who viewed or changed what, and when. Times are Bangladesh Standard Time.">
        <x-button variant="outline" size="sm" icon-left="download" :href="route('admin.audit.export', request()->query() + ['format' => 'csv'])">CSV</x-button>
        <x-button variant="outline" size="sm" icon-left="file-down" :href="route('admin.audit.export', request()->query() + ['format' => 'xlsx'])">Excel</x-button>
    </x-admin::page-header>

    <x-admin::panel :padding="false">
        <x-admin::filters :action="route('admin.audit.index')" placeholder="Search description, action, IP…">
            <label class="sm:w-44"><span class="sr-only">User</span>
                <select name="user" class="field-control mt-0 py-2 text-sm" data-autosubmit>
                    <option value="">All users</option>
                    @foreach($users as $id => $name)<option value="{{ $id }}" @selected(request('user') == $id)>{{ $name }}</option>@endforeach
                </select>
            </label>
            <label class="sm:w-40"><span class="sr-only">Category</span>
                <select name="category" class="field-control mt-0 py-2 text-sm" data-autosubmit>
                    <option value="">All categories</option>
                    @foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ ucfirst($category) }}</option>@endforeach
                </select>
            </label>
            <label class="flex items-center gap-2 text-sm text-navy-500"><span>From</span><input type="date" name="from" value="{{ request('from') }}" class="field-control mt-0 w-auto py-2 text-sm"></label>
            <label class="flex items-center gap-2 text-sm text-navy-500"><span>To</span><input type="date" name="to" value="{{ request('to') }}" class="field-control mt-0 w-auto py-2 text-sm"></label>
        </x-admin::filters>

        @if($logs->isEmpty())
            <x-admin::empty icon="history" title="No matching activity" />
        @else
            <x-admin::table>
                <thead class="bg-navy-50/60">
                    <tr><th>When</th><th>Who</th><th>Action</th><th>What</th><th>IP</th></tr>
                </thead>
                <tbody class="divide-y divide-navy-100 bg-white">
                    @foreach($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-navy-600"><span title="{{ $log->created_at?->diffForHumans() }}">{{ dhaka_time($log->created_at, 'j M Y, H:i:s') }}</span></td>
                            <td class="whitespace-nowrap font-medium text-navy-900">{{ $log->user->name }}</td>
                            <td><x-admin::badge :tone="$log->tone()" class="font-mono">{{ $log->action }}</x-admin::badge></td>
                            <td class="min-w-72 text-navy-700">
                                {{ $log->description }}
                                @if($log->properties)
                                    <details class="mt-1">
                                        <summary class="cursor-pointer text-xs text-navy-400 hover:text-navy-700">Details</summary>
                                        <pre class="mt-2 max-w-xl overflow-x-auto rounded-lg bg-navy-50 p-3 font-mono text-[0.7rem] leading-relaxed text-navy-700">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td class="font-mono text-xs whitespace-nowrap text-navy-500">{{ $log->ip_address }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin::table>
            <div class="border-t border-navy-100 px-4 py-3">{{ $logs->links('core::pagination') }}</div>
        @endif
    </x-admin::panel>
</x-admin::layout>
