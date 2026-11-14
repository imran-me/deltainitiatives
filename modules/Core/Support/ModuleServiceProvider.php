<?php

namespace Modules\Core\Support;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Admin\Support\ResourceRegistry;

/**
 * Base class for every module's service provider.
 *
 * Conventions (all optional, picked up automatically if present):
 *   modules/{Module}/config/config.php        -> config('{alias}.*')
 *   modules/{Module}/resources/views          -> view('{alias}::...')
 *   modules/{Module}/resources/views/components -> <x-{alias}::...>
 *   modules/{Module}/lang                      -> __('{alias}::file.key')
 *   modules/{Module}/database/migrations       -> php artisan migrate
 *   modules/{Module}/routes/web.php            -> public routes ("web" middleware)
 *   modules/{Module}/routes/admin.php          -> /admin/* routes ("web", "auth", "admin" middleware, "admin." names)
 *   $resources                                 -> admin CRUD resources shown in the admin sidebar
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /** Module directory name, e.g. "IdeaPortal". */
    protected string $module;

    /** View, translation and config namespace, e.g. "ideaportal". */
    protected string $alias;

    /** @var array<int, class-string<\Modules\Admin\Support\Resource>> */
    protected array $resources = [];

    public function register(): void
    {
        $config = $this->path('config/config.php');

        if (is_file($config)) {
            $this->mergeConfigFrom($config, $this->alias);
        }
    }

    public function boot(): void
    {
        if (is_dir($views = $this->path('resources/views'))) {
            $this->loadViewsFrom($views, $this->alias);
        }

        if (is_dir($components = $this->path('resources/views/components'))) {
            Blade::anonymousComponentPath($components, $this->alias);
        }

        if (is_dir($lang = $this->path('lang'))) {
            $this->loadTranslationsFrom($lang, $this->alias);
        }

        if (is_dir($migrations = $this->path('database/migrations'))) {
            $this->loadMigrationsFrom($migrations);
        }

        foreach ($this->resources as $resource) {
            ResourceRegistry::register($resource);
        }

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        if (is_file($web = $this->path('routes/web.php'))) {
            Route::middleware('web')->group($web);
        }

        if (is_file($admin = $this->path('routes/admin.php'))) {
            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->as('admin.')
                ->group($admin);
        }
    }

    protected function path(string $relative = ''): string
    {
        return base_path('modules/'.$this->module.($relative ? '/'.$relative : ''));
    }
}
