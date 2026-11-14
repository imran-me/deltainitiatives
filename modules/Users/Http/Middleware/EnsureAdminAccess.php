<?php

namespace Modules\Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applied to every /admin route (alias "admin"):
 *  - signs out deactivated accounts immediately,
 *  - forces a password change on first login,
 *  - stops browsers and proxies caching confidential pages.
 */
class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', 'Your account is not active. Please contact a Super Admin.');
        }

        if ($user->must_change_password && ! $request->routeIs('admin.profile.*', 'admin.logout')) {
            return redirect()->route('admin.profile.edit')
                ->with('warning', 'Please choose a new password before continuing.');
        }

        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
