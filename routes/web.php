<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ContentPageController;
use App\Http\Controllers\Admin\MediaAssetController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\RfqController;
use App\Http\Controllers\Admin\RfqReportController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\RfqSubmissionController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
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

Route::get('robots.txt', RobotsController::class)->name('robots');
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('content-pages/{content_page}/preview', [ContentPageController::class, 'preview'])->name('content-pages.preview');
    Route::post('content-pages/{content_page}/approve', [ContentPageController::class, 'approve'])->name('content-pages.approve');
    Route::post('content-pages/{content_page}/publish', [ContentPageController::class, 'publish'])->name('content-pages.publish');
    Route::post('content-pages/{content_page}/unpublish', [ContentPageController::class, 'unpublish'])->name('content-pages.unpublish');
    Route::post('content-pages/{content_page}/revisions/{revision}/restore', [ContentPageController::class, 'restore'])->name('content-pages.revisions.restore');
    Route::get('media/picker', [MediaAssetController::class, 'picker'])->name('media.picker');
    Route::post('media/{id}/restore', [MediaAssetController::class, 'restore'])->name('media.restore');
    Route::delete('media/{id}/force', [MediaAssetController::class, 'forceDelete'])->name('media.force-delete');
    Route::get('navigation', [NavigationController::class, 'index'])->name('navigation.index');
    Route::post('navigation/items', [NavigationController::class, 'storeItem'])->name('navigation.items.store');
    Route::get('navigation/items/{item}/edit', [NavigationController::class, 'editItem'])->name('navigation.items.edit');
    Route::put('navigation/items/{item}', [NavigationController::class, 'updateItem'])->name('navigation.items.update');
    Route::post('navigation/items/{item}/toggle', [NavigationController::class, 'toggleItem'])->name('navigation.items.toggle');
    Route::delete('navigation/items/{item}', [NavigationController::class, 'destroyItem'])->name('navigation.items.destroy');
    Route::get('redirects', [RedirectController::class, 'index'])->name('redirects.index');
    Route::post('redirects', [RedirectController::class, 'store'])->name('redirects.store');
    Route::get('redirects/{redirect}/edit', [RedirectController::class, 'edit'])->name('redirects.edit');
    Route::put('redirects/{redirect}', [RedirectController::class, 'update'])->name('redirects.update');
    Route::post('redirects/{redirect}/toggle', [RedirectController::class, 'toggle'])->name('redirects.toggle');
    Route::delete('redirects/{redirect}', [RedirectController::class, 'destroy'])->name('redirects.destroy');
    Route::post('redirects/test-resolution', [RedirectController::class, 'testResolution'])->name('redirects.test-resolution');
    Route::resource('media', MediaAssetController::class);
    Route::resource('content-pages', ContentPageController::class)->except('show');
    Route::get('rfqs/report', RfqReportController::class)->name('rfqs.report');
    Route::get('rfqs', [RfqController::class, 'index'])->name('rfqs.index');
    Route::get('rfqs/{rfq}', [RfqController::class, 'show'])->name('rfqs.show');
    Route::post(
        'rfqs/{rfq}/notes',
        [RfqController::class, 'storeNote'],
    )->name('rfqs.notes.store');
    Route::get('rfqs/{rfq}/attachments/{attachment}', [RfqController::class, 'downloadAttachment'])
        ->name('rfqs.attachments.download');
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
