<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attach production-safe security headers to every web response.
 *
 * Headers are safe for a light-mode, same-origin HTML application served over HTTPS.
 * Content-Security-Policy is intentionally permissive for V1 launch to avoid
 * breaking inline styles/scripts used in Blade templates; tighten per-route
 * as the codebase matures.
 */
final class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Only attach to HTML responses; avoid headers on JSON API, file downloads etc.
        $contentType = (string) $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html') && $contentType !== '') {
            return $response;
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Strict-Transport-Security only on production HTTPS to prevent HSTS on local dev.
        if (app()->isProduction() && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
