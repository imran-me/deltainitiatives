<?php

namespace Modules\Core\Support;

class Icons
{
    /** @var array<string, string>|null */
    protected static ?array $icons = null;

    public static function get(string $name): string
    {
        static::$icons ??= require base_path('modules/Core/resources/icons.php');

        return static::$icons[$name] ?? static::$icons['circle-dot'];
    }

    public static function has(string $name): bool
    {
        static::$icons ??= require base_path('modules/Core/resources/icons.php');

        return isset(static::$icons[$name]);
    }

    /** @return array<int, string> */
    public static function names(): array
    {
        static::$icons ??= require base_path('modules/Core/resources/icons.php');

        return array_keys(static::$icons);
    }
}
