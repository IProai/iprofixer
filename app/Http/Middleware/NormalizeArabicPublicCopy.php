<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class NormalizeArabicPublicCopy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            app()->getLocale() !== 'ar'
            || $request->is('admin/*')
            || $request->is('login')
            || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')
        ) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        /** @var array<string, string> $phrases */
        $phrases = config('arabic-copy.phrases', []);

        uksort($phrases, static fn (string $left, string $right): int => mb_strlen($right) <=> mb_strlen($left));

        $response->setContent(str_replace(array_keys($phrases), array_values($phrases), $content));
        $response->headers->remove('Content-Length');

        return $response;
    }
}
