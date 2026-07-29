<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Application bindings are added by bounded implementation packets.
    }

    public function boot(): void
    {
        // Runtime policies are added by bounded implementation packets.
    }
}
