<?php

namespace Modules\Admin\Support;

use Closure;
use Illuminate\Support\Facades\Gate;
use Throwable;

/**
 * Dashboard registry. Modules contribute headline stats and panels:
 *
 *   AdminDashboard::stat([
 *       'label' => 'New ideas', 'value' => fn () => IdeaSubmission::where('status', 'received')->count(),
 *       'icon' => 'lightbulb', 'route' => 'admin.ideas.index', 'permission' => 'ideas.view', 'order' => 10,
 *       'hint' => fn () => '3 this week',
 *   ]);
 *
 *   AdminDashboard::panel([
 *       'view' => 'ideaportal::admin.dashboard-panel', 'data' => fn () => [...],
 *       'permission' => 'ideas.view', 'order' => 10, 'span' => 2,   // span: 1 or 2 columns of 3
 *   ]);
 */
class AdminDashboard
{
    /** @var array<int, array<string, mixed>> */
    protected static array $stats = [];

    /** @var array<int, array<string, mixed>> */
    protected static array $panels = [];

    /** @param array<string, mixed> $stat */
    public static function stat(array $stat): void
    {
        // Keyed by label so re-booting the app never duplicates widgets.
        static::$stats[$stat['label']] = $stat + ['order' => 100, 'icon' => 'circle-dot', 'permission' => null, 'route' => null, 'hint' => null];
    }

    /** @param array<string, mixed> $panel */
    public static function panel(array $panel): void
    {
        static::$panels[$panel['view']] = $panel + ['order' => 100, 'permission' => null, 'data' => null, 'span' => 1];
    }

    /** @return array<int, array<string, mixed>> */
    public static function statsFor($user): array
    {
        return collect(static::$stats)
            ->filter(fn ($s) => $s['permission'] === null || Gate::forUser($user)->allows($s['permission']))
            ->sortBy('order')
            ->map(function ($s) {
                $s['resolved'] = static::resolve($s['value']);
                $s['hint_resolved'] = static::resolve($s['hint']);

                return $s;
            })
            ->values()
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    public static function panelsFor($user): array
    {
        return collect(static::$panels)
            ->filter(fn ($p) => $p['permission'] === null || Gate::forUser($user)->allows($p['permission']))
            ->sortBy('order')
            ->map(function ($p) {
                $p['resolved'] = static::resolve($p['data']) ?? [];

                return $p;
            })
            ->values()
            ->all();
    }

    protected static function resolve(mixed $value): mixed
    {
        if (! $value instanceof Closure) {
            return $value;
        }

        try {
            return $value();
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
