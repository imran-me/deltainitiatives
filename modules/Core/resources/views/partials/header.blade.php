@php
    $nav = [
        ['about', 'about', 'about'],
        ['what_we_do', 'what-we-do', 'what-we-do'],
        ['programmes_short', 'programmes.index', 'programmes.*|events.*'],
        ['partner', 'partner.index', 'partner.*'],
        ['join', 'join.create', 'join.*|bn.join.*'],
        ['contact', 'contact.create', 'contact.*'],
    ];
    $isActive = fn (string $patterns) => collect(explode('|', $patterns))->contains(fn ($p) => request()->routeIs($p));
@endphp
<header data-site-header @if($solid ?? false) data-solid @endif @class(['site-header fixed inset-x-0 top-0 z-50', 'is-scrolled' => $solid ?? false])>
    <div class="container-delta flex h-18 items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="-m-1 rounded-lg p-1" aria-label="{{ setting('site_name') }} — {{ __('core::ui.nav.home') }}">
            <x-logo />
        </a>

        <nav aria-label="{{ __('core::ui.nav.primary') }}" class="hidden items-center gap-0.5 xl:flex">
            @foreach($nav as [$label, $route, $patterns])
                <a href="{{ localized_route($route) }}" class="nav-link" @if($isActive($patterns)) aria-current="page" @endif>{{ __('core::ui.nav.'.$label) }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @if(count($alternates ?? []) > 1)
                <div class="hidden items-center rounded-full p-0.5 text-xs font-semibold ring-1 ring-current/20 sm:flex" role="group" aria-label="{{ __('core::ui.language.label') }}">
                    @foreach($alternates as $code => $href)
                        <a href="{{ $href }}" hreflang="{{ $code }}" lang="{{ $code }}"
                           @class(['rounded-full px-2.5 py-1 transition', 'bg-river-500 text-navy-950' => app()->getLocale() === $code, 'opacity-75 hover:opacity-100' => app()->getLocale() !== $code])
                           @if(app()->getLocale() === $code) aria-current="true" @endif>{{ __('core::ui.language.'.$code) }}</a>
                    @endforeach
                </div>
            @endif
            <x-button :href="localized_route('ideas.create')" size="sm" class="hidden sm:inline-flex" icon="arrow-right">{{ __('core::ui.nav.submit_idea') }}</x-button>
            <button type="button" class="-mr-2 inline-flex size-11 items-center justify-center rounded-full xl:hidden" data-nav-toggle aria-controls="mobile-nav" aria-expanded="false">
                <span class="sr-only">{{ __('core::ui.nav.menu') }}</span>
                <x-icon name="menu" class="size-6" data-nav-icon="open" />
                <x-icon name="x" class="size-6" data-nav-icon="close" hidden />
            </button>
        </div>
    </div>

    {{-- Mobile navigation panel --}}
    <div id="mobile-nav" hidden class="fixed inset-x-0 top-18 bottom-0 overflow-y-auto bg-navy-950 text-white xl:hidden">
        <nav aria-label="{{ __('core::ui.nav.primary') }}" class="container-delta flex min-h-full flex-col py-8">
            <ul class="divide-y divide-white/10 border-y border-white/10">
                @foreach($nav as [$label, $route, $patterns])
                    <li>
                        <a href="{{ localized_route($route) }}" data-nav-close class="flex items-center justify-between py-4 text-lg font-medium {{ $isActive($patterns) ? 'text-river-400' : 'text-white' }}" @if($isActive($patterns)) aria-current="page" @endif>
                            {{ __('core::ui.nav.'.$label) }}
                            <x-icon name="arrow-right" class="size-5 opacity-50" />
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-8 grid gap-3">
                <x-button :href="localized_route('ideas.create')" size="lg" icon="arrow-right">{{ __('core::ui.nav.submit_idea') }}</x-button>
                @if(count($alternates ?? []) > 1)
                    <div class="mt-2 flex justify-center gap-2 text-sm">
                        @foreach($alternates as $code => $href)
                            <a href="{{ $href }}" lang="{{ $code }}" @class(['rounded-full px-4 py-2 ring-1 ring-white/20', 'bg-white/10' => app()->getLocale() === $code])>{{ __('core::ui.language.'.$code) }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
            <p class="mt-auto pt-10 text-sm text-white/50">{{ setting('site_descriptor') }} · <span class="italic text-white/70">{{ setting('site_tagline') }}</span></p>
        </nav>
    </div>
</header>
