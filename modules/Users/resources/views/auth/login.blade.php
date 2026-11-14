<x-admin::auth-layout title="Sign in" heading="Sign in to the admin panel" intro="For Delta Initiatives team members and reviewers. Your access is personal — please don't share it.">
    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5" data-guard>
        @csrf
        <x-form.input name="email" type="email" label="E-mail" required autocomplete="username" autofocus :optional-label="false" />
        <div>
            <x-form.input name="password" type="password" label="Password" required autocomplete="current-password" :optional-label="false" />
            <div class="mt-2 text-right"><a href="{{ route('admin.password.request') }}" class="text-sm text-river-700 hover:underline">Forgot your password?</a></div>
        </div>
        <x-form.checkbox name="remember">Keep me signed in on this device</x-form.checkbox>
        <x-turnstile />
        <x-button variant="dark" class="w-full" icon="arrow-right" data-loading-text="Signing in…">Sign in</x-button>
    </form>
</x-admin::auth-layout>
