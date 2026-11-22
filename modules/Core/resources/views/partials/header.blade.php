{{-- Site header: Services · Ideas · Submit · Feed · People · Partners, search, language, "Work with us". --}}
@php
    $nav = [
        ['services.index', 'Services', 'services.*'],
        ['public-ideas.index', 'Ideas', 'public-ideas.*'],
        ['ideas.create', 'Submit', 'ideas.*|bn.ideas.*'],
        ['feed.index', 'Feed', 'feed.*'],
        ['people', 'People', 'people'],
        ['partners', 'Partners', 'partners|partner.*'],
    ];
    $bn = app()->getLocale() === 'bn';
    if ($bn) {
        $nav = [
            ['services.index', 'সেবা', 'services.*|bn.services.*'],
            ['public-ideas.index', 'আইডিয়া', 'public-ideas.*|bn.ideas.list'],
            ['ideas.create', 'জমা দিন', 'ideas.*|bn.ideas.*'],
            ['feed.index', 'ফিড', 'feed.*|bn.feed.*'],
            ['people', 'মানুষ', 'people|bn.people'],
            ['partners', 'অংশীদার', 'partners|partner.*|bn.partners'],
        ];
    }
    $bnRoutes = ['public-ideas.index' => 'bn.ideas.list'];
    $navUrl = fn (string $r) => $bn && isset($bnRoutes[$r]) ? route($bnRoutes[$r]) : localized_route($r);
    $isActive = fn (string $patterns) => collect(explode('|', $patterns))->contains(fn ($p) => request()->routeIs($p));
    $alts = $alternates ?? [];
    $searchIcon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>';
@endphp
<header class="hdr"><div class="wrap">
    <a class="logo" href="{{ route('home') }}" aria-label="{{ setting('site_name', 'Delta Initiatives') }}, home">
        <svg width="38" height="38" viewBox="0 0 40 40" aria-hidden="true"><path d="M20 4 37 35H3Z" fill="none" stroke="#0B1F3A" stroke-width="3" stroke-linejoin="round"/><path d="M20 14v21M20 22l-8 13M20 22l8 13" stroke="#1FA2A6" stroke-width="2.4" stroke-linecap="round"/><circle cx="20" cy="12" r="2.6" fill="#C9A227"/></svg>
        <span><b>Delta Initiatives</b><small>Engineering Change</small></span>
    </a>
    <div class="tools-m">
        <button class="icon-btn" type="button" data-open-search aria-label="Search">{!! $searchIcon !!}</button>
        <button class="menu-btn" type="button" aria-expanded="false" aria-controls="nav" aria-label="Menu"><span></span><span></span><span></span></button>
    </div>
    <nav class="nav" id="nav" aria-label="Main">
        @foreach($nav as [$route, $label, $patterns])
            @if($route === 'feed.index')
                <a href="{{ $navUrl('feed.index') }}" class="feed-link" data-feed-link data-latest-url="{{ route('feed.latest') }}" @if($isActive($patterns)) aria-current="page" @endif>{{ $bn ? 'ফিড' : 'Feed' }}<span class="dot" hidden><span class="sr">New updates</span></span></a>
            @else
                <a href="{{ $navUrl($route) }}" @if($isActive($patterns)) aria-current="page" @endif>{{ $label }}</a>
            @endif
        @endforeach
        <button class="search-btn" type="button" data-open-search aria-label="Search the site">{!! $searchIcon !!}<span>Search</span><kbd>Ctrl K</kbd></button>
        @if(count($alts) > 1)
            <div class="lang-links" role="group" aria-label="{{ __('core::ui.language.label') }}">
                @foreach($alts as $code => $href)
                    <a href="{{ $href }}" hreflang="{{ $code }}" lang="{{ $code }}" @if(app()->getLocale() === $code) aria-current="true" @endif>{{ __('core::ui.language.'.$code) }}</a>
                @endforeach
            </div>
        @else
            <button class="lang-btn" type="button" data-lang-toggle aria-pressed="false" title="Switch headlines to Bangla"><span class="bn">বাংলা</span></button>
        @endif
        <a class="btn pri" href="{{ $bn ? route('bn.services.index') : route('services.index') }}#proposal">{{ $bn ? 'আমাদের সঙ্গে কাজ করুন' : 'Work with us' }}</a>
    </nav>
</div></header>
