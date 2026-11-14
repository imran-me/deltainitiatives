<x-admin::layout title="Backups">
    <x-admin::page-header title="Backups" description="Each backup is a ZIP with a full SQL dump of the database plus every uploaded file. A backup runs automatically every night; the newest {{ config('admin.backups.keep') }} are kept.">
        <form method="POST" action="{{ route('admin.backups.store') }}" data-guard>
            @csrf
            <x-button variant="dark" size="sm" icon-left="database" data-loading-text="Creating backup…">Back up now</x-button>
        </form>
    </x-admin::page-header>

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 ring-1 ring-navy-100">
            <x-icon name="database" class="size-5 text-river-700" />
            <p class="mt-3 text-sm font-semibold">This panel</p>
            <p class="mt-1 text-sm leading-relaxed text-navy-500">Database + uploads, nightly at 02:30 BST via the scheduler. Download a copy and keep it off-site.</p>
        </div>
        <div class="rounded-2xl bg-white p-5 ring-1 ring-navy-100">
            <x-icon name="server" class="size-5 text-river-700" />
            <p class="mt-3 text-sm font-semibold">Hostinger backups</p>
            <p class="mt-1 text-sm leading-relaxed text-navy-500">Cloud Startup includes automatic daily backups of files and databases (hPanel → Backups).</p>
        </div>
        <div class="rounded-2xl bg-white p-5 ring-1 ring-navy-100">
            <x-icon name="git-branch" class="size-5 text-river-700" />
            <p class="mt-3 text-sm font-semibold">Code</p>
            <p class="mt-1 text-sm leading-relaxed text-navy-500">The application code lives in GitHub, so every change is versioned and can be rolled back.</p>
        </div>
    </div>

    <x-admin::panel :padding="false">
        @if(empty($backups))
            <x-admin::empty icon="archive" title="No backups yet" text="Create the first one now, or wait for tonight's scheduled run." />
        @else
            <x-admin::table>
                <thead class="bg-navy-50/60"><tr><th>File</th><th>Created</th><th>Size</th><th class="text-right"><span class="sr-only">Download</span></th></tr></thead>
                <tbody class="divide-y divide-navy-100 bg-white">
                    @foreach($backups as $backup)
                        <tr>
                            <td class="font-mono text-xs text-navy-800">{{ $backup['name'] }}</td>
                            <td class="text-navy-600">{{ dhaka_time($backup['created_at']) }}</td>
                            <td class="text-navy-600 tabular-nums">{{ \Illuminate\Support\Number::fileSize($backup['size'], 1) }}</td>
                            <td class="text-right"><a href="{{ route('admin.backups.download', $backup['name']) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-river-700 hover:underline"><x-icon name="download" class="size-4" /> Download</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin::table>
        @endif
    </x-admin::panel>
</x-admin::layout>
