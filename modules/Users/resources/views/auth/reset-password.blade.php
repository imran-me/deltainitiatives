<x-admin::auth-layout title="Choose a password" heading="Choose a new password" intro="Use at least 12 characters with upper- and lower-case letters, a number and a symbol. A password manager makes this easy.">
    <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-5" data-guard>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-form.input name="email" type="email" label="E-mail" :value="$email" required autocomplete="username" :optional-label="false" />
        <x-form.input name="password" type="password" label="New password" required autocomplete="new-password" :optional-label="false" />
        <x-form.input name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" :optional-label="false" />
        <x-button variant="dark" class="w-full" icon="check" data-loading-text="Saving…">Save password</x-button>
    </form>
</x-admin::auth-layout>
