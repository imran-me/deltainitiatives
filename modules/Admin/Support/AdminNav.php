<?php

namespace Modules\Admin\Support;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Throwable;

/**
 * Admin sidebar registry. Modules add links from their service providers:
 *
 *   AdminNav::add([
 *       'group' => 'Idea Portal', 'label' => 'Submissions', 'route' => 'admin.ideas.index',
 *       'active' => 'admin.ideas.*', 'icon' => 'lightbulb', 'permission' => 'ideas.view',
 *       'order' => 10, 'badge' => fn () => IdeaSubmission::where('status', 'received')->count(),
 *   ]);
 */
class AdminNav
{
    /** Display order of sidebar groups. */
    public const GROUPS = ['Overview', 'Idea Portal', 'Inbox', 'Programmes', 'Content', 'Community', 'System'];

    /** @var array<int, array<string, mixed>> */
    protected static array $items = [];

    /** @param array<string, mixed> $item */
    public static function add(array $item): void
    {
        $item += ['group' => 'Content', 'order' => 100, 'icon' => 'circle-dot', 'permission' => null, 'params' => [], 'badge' => null];

        // Keyed so re-booting the app (tests, queue workers) never duplicates links.
        static::$items[$item['route'].'|'.json_encode($item['params']).'|'.$item['label']] = $item;
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    public static function groupsFor(?\Modules\Users\Models\User $user): array
    {
        $visible = collect(static::$items)
            ->filter(fn ($item) => Route::has($item['route']))
            ->filter(fn ($item) => $item['permission'] === null || ($user && Gate::forUser($user)->allows($item['permission'])))
            ->sortBy([['order', 'asc'], ['label', 'asc']])
            ->map(function ($item) {
                $item['url'] = route($item['route'], $item['params']);
                $item['is_active'] = request()->routeIs($item['active'] ?? $item['route'])
                    && (! isset($item['params']['resource']) || request()->route('resource') === $item['params']['resource']);
                $item['badge_value'] = null;
                if ($item['badge'] instanceof Closure) {
                    try {
                        $item['badge_value'] = ($item['badge'])() ?: null;
                    } catch (Throwable) {
                        $item['badge_value'] = null;
                    }
                }

                return $item;
            })
            ->groupBy('group');

        return collect(self::GROUPS)
            ->merge($visible->keys())
            ->unique()
            ->filter(fn ($g) => $visible->has($g))
            ->mapWithKeys(fn ($g) => [$g => $visible->get($g)->values()->all()])
            ->all();
    }

    public static function flush(): void
    {
        static::$items = [];
    }
}
