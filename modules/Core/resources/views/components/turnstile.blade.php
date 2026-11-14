{{-- Cloudflare Turnstile widget. Renders nothing when Turnstile is disabled (e.g. tests). --}}
@php($turnstile = app(\Modules\Core\Services\TurnstileVerifier::class))
@if($turnstile->enabled())
    <div {{ $attributes->merge(['class' => 'cf-turnstile']) }} data-sitekey="{{ $turnstile->siteKey() }}" data-theme="light" data-size="flexible" data-language="{{ app()->getLocale() }}"></div>
    @error('cf-turnstile-response')
        <p class="mt-2 text-sm font-medium text-red-600" role="alert">{{ $message }}</p>
    @enderror
    @once
        @push('scripts')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}"></script>
        @endpush
    @endonce
@endif
