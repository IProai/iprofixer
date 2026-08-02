<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;

final class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $isProduction = config('app.env') === 'production';
        $baseUrl = rtrim((string) config('app.url', 'http://127.0.0.1:8000'), '/');

        if (! $isProduction) {
            $content = "User-agent: *\nDisallow: /\n";
        } else {
            $content = "User-agent: *\nDisallow: /admin/\nDisallow: /portal/\nDisallow: /login\nDisallow: /register\nDisallow: /api/\n\nSitemap: {$baseUrl}/sitemap.xml\n";
        }

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
