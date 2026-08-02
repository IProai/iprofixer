<?php

use App\Http\Middleware\HandleRedirectsMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies by default — narrow via TRUSTED_PROXIES env var in production.
        $middleware->trustProxies(
            at: env('TRUSTED_PROXIES', '*'),
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO,
        );
        $middleware->append(HandleRedirectsMiddleware::class);
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->web(append: [SetLocale::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Exception reporting and rendering policies are added by bounded packets.
    })
    ->create();
