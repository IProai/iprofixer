<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $requestedLocale = $request->query('lang');

        if (is_string($requestedLocale) && in_array($requestedLocale, self::SUPPORTED_LOCALES, true)) {
            $request->session()->put('locale', $requestedLocale);
            cookie()->queue(cookie('iprofixer_locale', $requestedLocale, 60 * 24 * 365, '/', null, true, true, false, 'Lax'));
        }

        $locale = $request->session()->get(
            'locale',
            $request->cookie('iprofixer_locale', config('app.locale', 'en')),
        );

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'en';
        }

        App::setLocale($locale);

        $response = $next($request);
        $response->headers->set('Vary', 'Cookie, Accept-Encoding');
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');

        return $response;
    }
}
