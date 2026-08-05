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
        ) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type');
        $isHtml = str_contains($contentType, 'text/html')
            || str_starts_with(ltrim($content), '<!doctype html')
            || str_starts_with(ltrim($content), '<html');

        if (! $isHtml) {
            return $response;
        }

        /** @var array<string, string> $phrases */
        $phrases = config('arabic-copy.phrases', []);

        if ($phrases !== []) {
            uksort(
                $phrases,
                static fn (string $left, string $right): int => mb_strlen($right) <=> mb_strlen($left),
            );

            $content = str_replace(array_keys($phrases), array_values($phrases), $content);
        }

        // Durable release marker used by automated and live Arabic verification.
        $content = str_replace(
            '</body>',
            '<!-- iprofixer-arabic-copy-server-rendered --></body>',
            $content,
        );

        $response->setContent($content);
        $response->headers->remove('Content-Length');

        return $response;
    }
}
