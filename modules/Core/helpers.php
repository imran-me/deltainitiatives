<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Modules\Core\Models\AuditLog;
use Modules\Core\Services\AuditLogger;
use Modules\Core\Services\SettingsService;

if (! function_exists('setting')) {
    /** Read a site setting (Admin → Settings), falling back to config('core.settings_defaults'). */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}

if (! function_exists('audit')) {
    /** Write an entry to the audit trail. */
    function audit(string $action, ?Model $subject = null, string $description = '', array $properties = []): ?AuditLog
    {
        return app(AuditLogger::class)->log($action, $subject, $description, $properties);
    }
}

if (! function_exists('markdown')) {
    /** Render admin-managed Markdown safely (raw HTML is escaped, unsafe links dropped). */
    function markdown(?string $text): HtmlString
    {
        return new HtmlString(Str::markdown((string) $text, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]));
    }
}

if (! function_exists('localized_route')) {
    /** Route URL in the current locale when a localized twin exists (e.g. "bn.ideas.create"). */
    function localized_route(string $name, mixed $parameters = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($locale !== 'en' && Route::has("{$locale}.{$name}")) {
            return route("{$locale}.{$name}", $parameters);
        }

        return route($name, $parameters);
    }
}

if (! function_exists('show_demo_badge')) {
    function show_demo_badge(?Model $model): bool
    {
        return $model !== null && config('core.demo_badges') && (bool) ($model->is_demo ?? false);
    }
}

if (! function_exists('initials')) {
    function initials(?string $name): string
    {
        $words = preg_split('/\s+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $words = array_values(array_filter($words, fn ($w) => ! str_starts_with($w, '[')));

        $letters = collect($words)->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        return $letters !== '' ? $letters : 'Δ';
    }
}

if (! function_exists('dhaka_time')) {
    /** Format a timestamp in Bangladesh time for display. */
    function dhaka_time(?\DateTimeInterface $date, string $format = 'j M Y, g:i A'): string
    {
        if (! $date) {
            return '—';
        }

        return \Illuminate\Support\Carbon::instance($date)->setTimezone(config('app.display_timezone', 'Asia/Dhaka'))->format($format).' BST';
    }
}

if (! function_exists('hreflang_alternates')) {
    /** en/bn alternate URLs for the current route when both language versions exist, else []. */
    function hreflang_alternates(): array
    {
        $pairs = [
            'home' => 'bn.home', 'about' => 'bn.about', 'research.index' => 'bn.research',
            'public-ideas.index' => 'bn.ideas.list', 'insights.index' => 'bn.insights',
            'people' => 'bn.people', 'partners' => 'bn.partners', 'ideas.create' => 'bn.ideas.create',
            'join.create' => 'bn.join.create', 'services.index' => 'bn.services.index',
            'eon' => 'bn.eon', 'feed.index' => 'bn.feed.index',
        ];
        $current = Route::currentRouteName();
        if (! $current || ! request()->isMethod('GET')) {
            return [];
        }
        $en = array_search($current, $pairs, true) ?: (isset($pairs[$current]) ? $current : null);
        if (! $en || ! Route::has($en) || ! Route::has($pairs[$en])) {
            return [];
        }

        return ['en' => route($en), 'bn' => route($pairs[$en])];
    }
}
