{{-- Admin shell: navy sidebar + content area. <x-admin::layout title="Ideas"> --}}
@props(['title' => 'Admin'])
@php($user = auth()->user())
@php($groups = \Modules\Admin\Support\AdminNav::groupsFor($user))
<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f5f7fa]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · Admin · {{ setting('site_name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full text-navy-900">
    <a href="#admin-main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold">Skip to content</a>

    {{-- Mobile backdrop --}}
    <div class="fixed inset-0 z-30 hidden bg-navy-950/60 lg:hidden" data-admin-backdrop></div>

    <aside data-admin-sidebar class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-navy-950 text-white transition-transform duration-300 lg:translate-x-0" aria-label="Admin navigation">
        <div class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-white/10 px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <x-delta-mark class="h-7 w-auto" />
                <span class="text-[0.7rem] leading-tight tracking-[0.28em]"><span class="font-bold">DELTA</span><br><span class="text-river-400">ADMIN</span></span>
            </a>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs text-white/60 ring-1 ring-white/15 hover:text-white hover:ring-white/30">
                View site <x-icon name="arrow-up-right" class="size-3.5" />
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-5">
            @foreach($groups as $group => $items)
                <div class="mb-6">
                    <p class="px-3 text-[0.68rem] font-semibold tracking-[0.18em] text-white/40 uppercase">{{ $group }}</p>
                    <ul class="mt-2 space-y-0.5" role="list">
                        @foreach($items as $item)
                            <li>
                                <a href="{{ $item['url'] }}" @class([
                                    'group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition',
                                    'bg-white/10 font-medium text-white' => $item['is_active'],
                                    'text-white/70 hover:bg-white/5 hover:text-white' => ! $item['is_active'],
                                ]) @if($item['is_active']) aria-current="page" @endif>
                                    @if($item['is_active'])<span class="absolute top-1.5 bottom-1.5 left-0 w-0.5 rounded-full bg-river-400"></span>@endif
                                    <x-icon :name="$item['icon']" class="size-4.5 {{ $item['is_active'] ? 'text-river-400' : 'text-white/50 group-hover:text-white/80' }}" />
                                    <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                    @if($item['badge_value'])
                                        <span class="rounded-full bg-river-500 px-2 py-0.5 text-[0.68rem] font-semibold text-navy-950 tabular-nums">{{ $item['badge_value'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-white/10 p-4">
            <div class="flex items-center gap-3">
                <x-avatar :name="$user->name" size="size-9" class="rounded-xl text-xs" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                    <p class="truncate text-xs text-white/50">{{ $user->roleLabel() }}</p>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="{{ route('admin.profile.edit') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs text-white/70 ring-1 ring-white/15 hover:bg-white/5 hover:text-white"><x-icon name="user" class="size-3.5" /> Profile</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs text-white/70 ring-1 ring-white/15 hover:bg-white/5 hover:text-white"><x-icon name="log-out" class="size-3.5" /> Sign out</button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:pl-72">
        {{-- Mobile top bar --}}
        <div class="sticky top-0 z-20 flex h-16 items-center justify-between gap-3 border-b border-navy-100 bg-white/95 px-4 backdrop-blur lg:hidden">
            <button type="button" class="-ml-1 inline-flex size-10 items-center justify-center rounded-lg text-navy-700 hover:bg-navy-50" data-admin-sidebar-toggle aria-expanded="false" aria-label="Open navigation">
                <x-icon name="menu" class="size-6" />
            </button>
            <span class="flex items-center gap-2 text-sm font-semibold"><x-delta-mark class="h-6 w-auto" lines="#0B1F3A" /> Admin</span>
            <x-avatar :name="$user->name" size="size-8" class="rounded-lg text-[0.65rem]" />
        </div>

        <main id="admin-main" tabindex="-1" class="mx-auto max-w-7xl px-4 py-8 outline-none sm:px-6 lg:px-10 lg:py-10">
            <x-flash class="mb-6" />
            {{ $slot }}
        </main>
    </div>
    @stack('scripts')
</body>
</html>
