<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\RedirectRule;
use App\Services\RedirectService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HandleRedirectsMiddleware
{
    public function __construct(private readonly RedirectService $redirectService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        if (RedirectRule::isProtectedPath($path)) {
            return $next($request);
        }

        $rule = $this->redirectService->findActiveRedirect($path);

        if ($rule) {
            $destinationUrl = $rule->resolveDestinationUrl();

            if (RedirectRule::normalizePath($destinationUrl) === RedirectRule::normalizePath($path)) {
                return $next($request);
            }

            $rule->recordHit();

            return redirect($destinationUrl, $rule->status_code);
        }

        return $next($request);
    }
}
