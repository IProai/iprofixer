<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ContentPageController;
use App\Http\Controllers\Admin\RfqController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\RfqSubmissionController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', fn (): View => view('home'))->name('home');

Route::get('/services', [PublicContentController::class, 'page'])->defaults('page', 'services')->name('services');
Route::get('/industries', [PublicContentController::class, 'page'])->defaults('page', 'industries')->name('industries');
Route::get('/process', [PublicContentController::class, 'page'])->defaults('page', 'process')->name('process');
Route::get('/results', [PublicContentController::class, 'page'])->defaults('page', 'results')->name('results');
Route::get('/about', [PublicContentController::class, 'page'])->defaults('page', 'about')->name('about');
Route::get('/resources', [PublicContentController::class, 'page'])->defaults('page', 'resources')->name('resources');
Route::get('/contact', [PublicContentController::class, 'page'])->defaults('page', 'contact')->name('contact');
Route::get('/portal', [PublicContentController::class, 'page'])->defaults('page', 'portal')->name('portal');

Route::get('/services/{service}', [PublicContentController::class, 'service'])
    ->whereIn('service', [
        'cutlery-restoration',
        'hollowware-care',
        'asset-condition-review',
        'recurring-care-plans',
    ])->name('services.show');

Route::get('/industries/{industry}', [PublicContentController::class, 'industry'])
    ->whereIn('industry', [
        'hotels-resorts',
        'restaurants-groups',
        'catering-events',
        'procurement-operations',
    ])->name('industries.show');

Route::post('/rfq', RfqSubmissionController::class)
    ->middleware('throttle:5,1')
    ->name('rfq.store');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::resource('content-pages', ContentPageController::class)->except('show');
    Route::get('rfqs', [RfqController::class, 'index'])->name('rfqs.index');
    Route::get('rfqs/{rfq}', [RfqController::class, 'show'])->name('rfqs.show');
    Route::put('rfqs/{rfq}', [RfqController::class, 'update'])->name('rfqs.update');
});

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
