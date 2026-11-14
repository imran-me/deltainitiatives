<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds security headers, including a nonce-based Content-Security-Policy
 * that allows only our own assets plus Cloudflare Turnstile and Web Analytics.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if (config('core.csp.enabled') && ! $headers->has('Content-Security-Policy')) {
            $headers->set('Content-Security-Policy', $this->policy());
        }

        return $response;
    }

    protected function policy(): string
    {
        $nonce = Vite::cspNonce();
        $dev = app()->environment('local') && is_file(public_path('hot'));
        $viteDev = $dev ? ' http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173 http://[::1]:5173 ws://[::1]:5173' : '';

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' https://challenges.cloudflare.com https://static.cloudflareinsights.com".$viteDev,
            "style-src 'self' 'unsafe-inline'".$viteDev,
            "img-src 'self' data: blob:",
            "font-src 'self' data:".$viteDev,
            "connect-src 'self' https://cloudflareinsights.com".$viteDev,
            'frame-src https://challenges.cloudflare.com',
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);
    }
}
