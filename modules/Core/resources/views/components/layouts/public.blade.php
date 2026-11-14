{{--
    Public site layout.
    <x-layouts.public title="About" description="..." :alternates="['en' => url, 'bn' => url]" :json-ld="[...]">
--}}
@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'noindex' => false,
    'newsletter' => true,
    'alternates' => [],
    'jsonLd' => null,
    'solidHeader' => false,
    'ogType' => 'website',
])
@php
    $siteName = setting('site_name');
    $fullTitle = $title ? $title.' · '.$siteName : $siteName.' — '.setting('site_tagline');
    $metaDescription = \Illuminate\Support\Str::limit(trim(strip_tags($description ?: setting('site_description'))), 158);
    $canonicalUrl = $canonical ?: url()->current();
    $ogImage = $image ?: asset('images/og-default.png');
    $locale = app()->getLocale();
    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'ResearchOrganization',
        'name' => $siteName,
        'slogan' => setting('site_tagline'),
        'description' => setting('site_description'),
        'url' => url('/'),
        'logo' => asset('images/logo-square.png'),
        'email' => setting('contact_email'),
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Dhaka', 'addressCountry' => 'BD'],
        'sameAs' => array_values(array_filter([setting('social_linkedin'), setting('social_facebook'), setting('social_x'), setting('social_youtube'), setting('social_instagram')])),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if($noindex || config('app.noindex'))
        <meta name="robots" content="noindex, nofollow">
    @endif
    @foreach($alternates as $hreflang => $href)
        <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $href }}">
    @endforeach
    @if(isset($alternates['en']))
        <link rel="alternate" hreflang="x-default" href="{{ $alternates['en'] }}">
    @endif

    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $title ?: $siteName }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ $locale === 'bn' ? 'bn_BD' : 'en_GB' }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#0B1F3A">
    @if($verification = setting('google_site_verification'))
        <meta name="google-site-verification" content="{{ $verification }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @if($jsonLd)
        <script type="application/ld+json" nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endif
    @stack('head')
</head>
<body class="min-h-screen bg-white">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-navy-900 focus:shadow-lg">{{ __('core::ui.skip') }}</a>

    @include('core::partials.header', ['solid' => $solidHeader, 'alternates' => $alternates])

    <main id="main" tabindex="-1" class="outline-none">
        {{ $slot }}
    </main>

    @include('core::partials.footer', ['newsletter' => $newsletter])

    @stack('scripts')
    @if($beacon = setting('cf_analytics_token'))
        <script defer src="https://static.cloudflareinsights.com/beacon.min.js" data-cf-beacon='{"token": "{{ $beacon }}"}' nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}"></script>
    @endif
</body>
</html>
