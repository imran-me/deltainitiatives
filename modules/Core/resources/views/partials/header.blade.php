{{-- v3 site header: sticky, shrinks after scroll (site.js), Research mega-menu, Ctrl K search, language. --}}
@php
    $areas = [
        ['ai', 'AI & Computing', 'Bangla language technology'],
        ['hardware', 'Hardware & Engineering', 'Sensors, devices, low-cost machines'],
        ['ventures', 'Startups & Ventures', 'Research that becomes companies'],
        ['climate', 'Nature & Climate', 'Floods, salinity, cyclones'],
        ['life', 'Life Sciences & Health', 'Diagnostics and water safety'],
        ['governance', 'Good Governance', 'Evidence and civic technology'],
    ];
    $nav = [
        ['initiatives.index', 'Initiatives', 'initiatives.*'],
        ['insights.index', 'Insights', 'insights.*'],
        ['ideas.create', 'Idea Portal', 'ideas.*|bn.ideas.*'],
        ['impact', 'Impact', 'impact'],
        ['partner.index', 'Collaborate', 'partner.*'],
        ['about', 'About', 'about|join.*|bn.join.*'],
    ];
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
        <div class="has-mega">
            <button type="button" @class(['mega-btn', 'cur' => $isActive('research.*|what-we-do')]) aria-expanded="false" aria-controls="mega">Research</button>
            <div class="mega" id="mega">
                @foreach($areas as [$key, $label, $blurb])
                    <a href="{{ route('research.'.$key) }}"><b>{{ $label }}</b><span>{{ $blurb }}</span></a>
                @endforeach
                <div class="mega-foot"><a href="{{ route('research.index') }}">All six research areas →</a><a href="{{ route('insights.index') }}">Latest insights →</a></div>
            </div>
        </div>
        @foreach($nav as [$route, $label, $patterns])
            <a href="{{ localized_route($route) }}" @if($isActive($patterns)) aria-current="page" @endif>{{ $label }}</a>
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
        <a class="btn pri" href="{{ localized_route('ideas.create') }}#submit-form">{{ __('core::ui.nav.submit_idea') }}</a>
    </nav>
</div></header>
