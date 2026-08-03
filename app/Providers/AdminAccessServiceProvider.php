<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class AdminAccessServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')->group(function (): void {
            Route::get('/login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

            Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('throttle:5,1')
                ->name('login.store');

            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');

            Route::get('/admin', fn () => redirect()->route('admin.rfqs.index'))
                ->middleware('auth')
                ->name('admin.home');
        });
    }
}
