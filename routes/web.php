<?php

declare(strict_types=1);

use App\Http\Controllers\RfqSubmissionController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', fn (): View => view('home'))->name('home');

Route::post('/rfq', RfqSubmissionController::class)
    ->middleware('throttle:5,1')
    ->name('rfq.store');

Route::get('/health', function (): JsonResponse {
    return response()->json([
        'status' => 'ok',
        'service' => config('app.name', 'IProFixer'),
        'environment' => app()->environment(),
        'locale' => app()->getLocale(),
    ]);
})->name('health');

Route::get('/ready', function (): JsonResponse {
    try {
        DB::select('select 1');
    } catch (Throwable $exception) {
        report($exception);

        return response()->json([
            'status' => 'not_ready',
            'database' => 'unavailable',
        ], 503);
    }

    return response()->json([
        'status' => 'ready',
        'database' => 'available',
    ]);
})->name('ready');

Route::post('/locale/{locale}', function (string $locale): RedirectResponse {
    abort_unless(in_array($locale, ['en', 'ar'], true), 404);

    session(['locale' => $locale]);

    return back();
})->name('locale.update');
