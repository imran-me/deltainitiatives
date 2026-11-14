<x-admin::auth-layout title="Reset password" heading="Reset your password" intro="Enter the e-mail address of your admin account and we'll send you a secure link to choose a new password.">
    <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5" data-guard>
        @csrf
        <x-form.input name="email" type="email" label="E-mail" required autocomplete="username" autofocus :optional-label="false" />
        <x-turnstile />
        <x-button variant="dark" class="w-full" icon="send" data-loading-text="Sending…">Send reset link</x-button>
        <p class="text-center text-sm"><a href="{{ route('admin.login') }}" class="text-navy-500 hover:text-navy-900">Back to sign in</a></p>
    </form>
</x-admin::auth-layout>
