<?php

namespace Modules\Core\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Core\Services\SettingsService;
use Modules\Core\Services\TurnstileVerifier;
use Modules\Core\Support\ModuleServiceProvider;
use Modules\Core\Support\Sitemap;

class CoreServiceProvider extends ModuleServiceProvider
{
    protected string $module = 'Core';

    protected string $alias = 'core';

    public function register(): void
    {
        parent::register();

        $this->app->singleton(SettingsService::class);
        $this->app->singleton(TurnstileVerifier::class);
        $this->app->singleton(Sitemap::class);
    }

    public function boot(): void
    {
        parent::boot();

        // Core design-system components are available without a prefix: <x-button>, <x-icon>, ...
        Blade::anonymousComponentPath(base_path('modules/Core/resources/views/components'));

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Contact, partner, join, newsletter and event registration forms.
        RateLimiter::for('public-forms', fn (Request $request) => [
            Limit::perMinute(5)->by('pf-min|'.$request->ip()),
            Limit::perHour(30)->by('pf-hour|'.$request->ip()),
        ]);

        // Idea Portal submissions (heavier: file uploads).
        RateLimiter::for('idea-submissions', fn (Request $request) => [
            Limit::perMinute(2)->by('idea-min|'.$request->ip()),
            Limit::perDay(15)->by('idea-day|'.$request->ip()),
        ]);

        // Public receipt verification.
        RateLimiter::for('verify', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        // Admin login: per e-mail + IP, and per IP overall.
        RateLimiter::for('admin-login', fn (Request $request) => [
            Limit::perMinute(5)->by(mb_strtolower((string) $request->input('email')).'|'.$request->ip()),
            Limit::perMinute(20)->by('login-ip|'.$request->ip()),
        ]);
    }
}
