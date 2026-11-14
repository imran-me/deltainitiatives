<?php

namespace Modules\Admin\Support;

class ResourceRegistry
{
    /** @var array<string, class-string<Resource>> */
    protected static array $resources = [];

    /** @param class-string<Resource> $resource */
    public static function register(string $resource): void
    {
        static::$resources[$resource::$slug] = $resource;

        if ($resource::$showInNav) {
            AdminNav::add([
                'group' => $resource::$group,
                'label' => $resource::$label,
                'route' => 'admin.resources.index',
                'params' => ['resource' => $resource::$slug],
                'active' => 'admin.resources.*',
                'icon' => $resource::$icon,
                'permission' => $resource::$permission,
                'order' => $resource::$navOrder,
                'badge' => fn () => app($resource)->badgeCount(),
            ]);
        }
    }

    /** @return class-string<Resource>|null */
    public static function find(string $slug): ?string
    {
        return static::$resources[$slug] ?? null;
    }

    /** @return array<string, class-string<Resource>> */
    public static function all(): array
    {
        return static::$resources;
    }
}
