<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the application locale for a route group, e.g. ->middleware('locale:bn').
 */
class SetLocale
{
    public function handle(Request $request, Closure $next, string $locale = 'en'): Response
    {
        if (in_array($locale, config('core.locales', ['en']), true)) {
            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
