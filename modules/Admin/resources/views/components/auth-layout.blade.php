{{-- Split-screen layout for sign-in and password screens. --}}
@props(['title' => 'Sign in', 'heading' => null, 'intro' => null])
<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · {{ setting('site_name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}?v=20261128" type="image/svg+xml">
    <link rel="icon" href="{{ asset('images/favicon-32.png') }}?v=20261128" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=20261128" sizes="16x16 32x32 48x48">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=20261128">
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="grid min-h-full lg:grid-cols-2">
        <div class="on-dark relative isolate hidden overflow-hidden bg-navy-900 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <x-delta-lines class="-z-10" />
            <a href="{{ route('home') }}" class="inline-flex"><x-logo /></a>
            <div class="max-w-md">
                <p class="eyebrow">Admin &amp; review panel</p>
                <p class="mt-4 text-4xl font-semibold tracking-tight">Engineering Change<span class="text-river-400">.</span></p>
                <p class="mt-4 leading-relaxed text-white/60">Every idea here was shared in confidence. Access is personal, permission-based and recorded in the audit log.</p>
            </div>
            <p class="text-xs text-white/40">© {{ now()->year }} {{ setting('site_name') }}</p>
        </div>

        <div class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-20">
            <div class="mx-auto w-full max-w-sm">
                <a href="{{ route('home') }}" class="inline-flex text-navy-900 lg:hidden"><x-delta-mark class="h-10 w-auto" lines="#0B1F3A" /></a>
                <h1 class="mt-8 text-2xl font-semibold tracking-tight lg:mt-0">{{ $heading ?? $title }}</h1>
                @if($intro)
                    <p class="mt-2 text-sm leading-relaxed text-navy-500">{{ $intro }}</p>
                @endif
                <x-flash class="mt-6" />
                <div class="mt-8">{{ $slot }}</div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
