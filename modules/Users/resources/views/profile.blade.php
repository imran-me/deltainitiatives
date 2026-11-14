<x-admin::layout title="Your profile">
    <x-admin::page-header title="Your profile" :description="'Signed in as '.$user->roleLabel().'. Last sign-in '.($user->last_login_at ? dhaka_time($user->last_login_at) : 'just now').'.'" />

    <div class="grid gap-6 lg:grid-cols-2">
        <x-admin::panel title="Details">
            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5" data-guard>
                @csrf @method('PUT')
                <x-form.input name="name" label="Full name" :value="$user->name" required />
                <x-form.input name="title" label="Title / position" :value="$user->title" />
                <x-form.input name="email" type="email" label="E-mail" :value="$user->email" required hint="Changing your e-mail requires your current password." />
                <x-form.input name="current_password" type="password" label="Current password" autocomplete="current-password" />
                <x-button variant="dark" size="sm" icon-left="check">Save details</x-button>
            </form>
        </x-admin::panel>

        <x-admin::panel title="Change password" :description="$user->must_change_password ? 'You must choose a new password before using the admin panel.' : 'Changing your password signs out your other sessions.'">
            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-5" data-guard>
                @csrf @method('PUT')
                <x-form.input name="current_password" type="password" label="Current password" required autocomplete="current-password" id="pw_current" />
                <x-form.input name="password" type="password" label="New password" required autocomplete="new-password" hint="At least 12 characters with upper- and lower-case letters, a number and a symbol." />
                <x-form.input name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" />
                <x-button variant="dark" size="sm" icon-left="key-round">Update password</x-button>
            </form>
        </x-admin::panel>

        <x-admin::panel title="What you can do" class="lg:col-span-2">
            <ul class="grid gap-2 sm:grid-cols-2" role="list">
                @foreach(config('users.permissions') as $key => $label)
                    @php($has = $user->can($key))
                    <li class="flex items-start gap-2 text-sm {{ $has ? 'text-navy-800' : 'text-navy-300' }}">
                        <x-icon :name="$has ? 'circle-check' : 'x'" class="mt-0.5 size-4 {{ $has ? 'text-river-600' : '' }}" />
                        {{ $label }}
                    </li>
                @endforeach
            </ul>
        </x-admin::panel>
    </div>
</x-admin::layout>
