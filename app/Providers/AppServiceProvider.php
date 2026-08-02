<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Application bindings are added by bounded implementation packets.
    }

    public function boot(): void
    {
        // Force HTTPS scheme for URL generation in production (behind proxy/CDN).
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
