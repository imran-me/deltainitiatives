<x-admin::layout title="Users & roles">
    <x-admin::page-header title="Users & roles" description="Every reviewer and team member has their own account — passwords are never shared. Deactivate accounts instead of deleting them so the audit trail stays intact.">
        <x-button variant="dark" size="sm" icon-left="user-plus" :href="route('admin.users.create')">Add user</x-button>
    </x-admin::page-header>

    <x-admin::panel :padding="false">
        <x-admin::filters :action="route('admin.users.index')" placeholder="Search name or e-mail…">
            <label class="sm:w-52"><span class="sr-only">Role</span>
                <select name="role" class="field-control mt-0 py-2 text-sm" data-autosubmit>
                    <option value="">All roles</option>
                    @foreach($roles as $key => $role)<option value="{{ $key }}" @selected(request('role') === $key)>{{ $role['label'] }}</option>@endforeach
                </select>
            </label>
        </x-admin::filters>
        <x-admin::table>
            <thead class="bg-navy-50/60"><tr><th>Name</th><th>Role</th><th>Status</th><th>Last sign-in</th><th class="text-right"><span class="sr-only">Actions</span></th></tr></thead>
            <tbody class="divide-y divide-navy-100 bg-white">
                @foreach($users as $u)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <x-avatar :name="$u->name" size="size-9" class="rounded-xl text-xs" />
                                <div>
                                    <a href="{{ route('admin.users.edit', $u) }}" class="font-medium text-navy-900 hover:text-river-700">{{ $u->name }}</a>
                                    @if($u->is(auth()->user()))<x-admin::badge tone="teal" class="ml-1">You</x-admin::badge>@endif
                                    <p class="text-xs text-navy-500">{{ $u->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td><x-admin::badge :tone="$u->isSuperAdmin() ? 'navy' : 'slate'">{{ $u->roleLabel() }}</x-admin::badge></td>
                        <td>
                            @if(! $u->is_active)<x-admin::badge tone="red">Deactivated</x-admin::badge>
                            @elseif($u->must_change_password)<x-admin::badge tone="amber">Invited</x-admin::badge>
                            @else<x-admin::badge tone="green">Active</x-admin::badge>@endif
                        </td>
                        <td class="text-navy-500">{{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}</td>
                        <td class="text-right"><a href="{{ route('admin.users.edit', $u) }}" class="inline-flex size-8 items-center justify-center rounded-lg text-navy-500 hover:bg-navy-100 hover:text-navy-900"><x-icon name="pencil" class="size-4" /><span class="sr-only">Edit {{ $u->name }}</span></a></td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin::table>
        @if($users->hasPages())<div class="border-t border-navy-100 px-4 py-3">{{ $users->links('core::pagination') }}</div>@endif
    </x-admin::panel>

    <x-admin::panel title="Roles" class="mt-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($roles as $key => $role)
                <div class="rounded-xl bg-navy-50/50 p-4 ring-1 ring-navy-100">
                    <p class="text-sm font-semibold text-navy-900">{{ $role['label'] }}</p>
                    <p class="mt-1 text-xs leading-relaxed text-navy-500">{{ $role['description'] }}</p>
                </div>
            @endforeach
        </div>
    </x-admin::panel>
</x-admin::layout>
