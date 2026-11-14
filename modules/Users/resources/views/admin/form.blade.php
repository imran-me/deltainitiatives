@php($editing = $user->exists)
<x-admin::layout :title="$editing ? 'Edit '.$user->name : 'Add user'">
    <x-admin::page-header :title="$editing ? $user->name : 'Add a user'" :description="$editing ? $user->email : 'They will receive an e-mail with a secure link to choose their own password.'" :back="route('admin.users.index')" back-label="Users & roles" />

    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="lg:col-span-2" data-guard>
            @csrf
            @if($editing) @method('PUT') @endif
            <x-admin::panel title="Account">
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-form.input name="name" label="Full name" :value="$user->name" required />
                    <x-form.input name="title" label="Title / position" :value="$user->title" />
                    <x-form.input name="email" type="email" label="E-mail" :value="$user->email" required class="sm:col-span-2" />
                    <fieldset class="sm:col-span-2">
                        <legend class="text-sm font-medium text-navy-800">Role <span class="text-river-700">*</span></legend>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            @foreach($roles as $key => $role)
                                @continue($key === 'super_admin' && ! auth()->user()->isSuperAdmin())
                                <label class="flex cursor-pointer gap-3 rounded-xl p-4 ring-1 ring-navy-100 has-[:checked]:bg-river-50 has-[:checked]:ring-2 has-[:checked]:ring-river-500">
                                    <input type="radio" name="role" value="{{ $key }}" class="mt-1 accent-river-600" @checked(old('role', $user->role) === $key) required>
                                    <span>
                                        <span class="block text-sm font-semibold text-navy-900">{{ $role['label'] }}</span>
                                        <span class="mt-0.5 block text-xs leading-relaxed text-navy-500">{{ $role['description'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-form.error for="role" />
                    </fieldset>
                    @if($editing)
                        <div class="rounded-xl bg-navy-50/60 px-4 py-3 ring-1 ring-navy-100 sm:col-span-2">
                            <input type="hidden" name="is_active" value="0">
                            <x-form.checkbox name="is_active" :checked="$user->is_active"><span class="font-medium text-navy-800">Account active</span><span class="block text-navy-500">Deactivated users are signed out immediately and cannot sign in.</span></x-form.checkbox>
                        </div>
                    @else
                        <div class="rounded-xl bg-navy-50/60 px-4 py-3 ring-1 ring-navy-100 sm:col-span-2">
                            <input type="hidden" name="invite" value="0">
                            <x-form.checkbox name="invite" :checked="true"><span class="font-medium text-navy-800">E-mail an invitation link</span><span class="block text-navy-500">Recommended. Untick to show a one-time temporary password instead.</span></x-form.checkbox>
                        </div>
                    @endif
                </div>
                <div class="mt-6 flex justify-end">
                    <x-button variant="dark" icon-left="check">{{ $editing ? 'Save changes' : 'Create account' }}</x-button>
                </div>
            </x-admin::panel>
        </form>

        @if($editing)
            <div class="space-y-6">
                <x-admin::panel title="Password">
                    <p class="text-sm text-navy-500">Send {{ $user->name }} a secure link to choose a new password.</p>
                    <form method="POST" action="{{ route('admin.users.send-reset', $user) }}" class="mt-4">
                        @csrf
                        <x-button variant="outline" size="sm" icon-left="mail">Send reset link</x-button>
                    </form>
                </x-admin::panel>
                <x-admin::panel title="Activity">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-navy-500">Created</dt><dd>{{ dhaka_time($user->created_at, 'j M Y') }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-navy-500">Last sign-in</dt><dd>{{ $user->last_login_at ? dhaka_time($user->last_login_at) : 'Never' }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-navy-500">From IP</dt><dd class="font-mono text-xs">{{ $user->last_login_ip ?? '—' }}</dd></div>
                    </dl>
                    @can('audit.view')
                        <a href="{{ route('admin.audit.index', ['user' => $user->id]) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-river-700 hover:underline">View their audit trail <x-icon name="arrow-right" class="size-3.5" /></a>
                    @endcan
                </x-admin::panel>
            </div>
        @endif
    </div>
</x-admin::layout>
