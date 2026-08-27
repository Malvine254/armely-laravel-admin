<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SharePreviewController;

// The SPA shell contains Vite's content-hashed asset names. It must be
// revalidated on every navigation so a deployment cannot leave visitors on an
// old CSS/JS bundle until they perform a hard refresh.
$spaEntrypoint = static function () {
    return response()
        ->view('app')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
};

// Store SPA public routes - disable legacy dashboard
Route::get('/dashboard', function () {
    abort(404);
});

// Lightweight public ping for deployment health checks (no auth)
Route::get('/admin/ping', [AdminController::class, 'ping'])->name('store.admin.ping');

// Public share preview pages for WhatsApp/Teams/chat link unfurls.
Route::get('/share/product/{productId}', [SharePreviewController::class, 'product'])
    ->name('store.share.product.preview');

Route::get('/share/cart/{messageId}', [SharePreviewController::class, 'cart'])
    ->middleware('signed')
    ->name('store.share.cart.preview');

Route::get('/share/cart/public/{token}', [SharePreviewController::class, 'cartPublic'])
    ->name('store.share.cart.preview.public');

// Direct product links need OG metadata for chat unfurls. Serve preview HTML
// to crawler user agents, but keep SPA behavior for normal browsers.
Route::get('/products/{productId}', function (Request $request, string $productId) use ($spaEntrypoint) {
    if (SharePreviewController::isPreviewCrawler($request->userAgent())) {
        return app(SharePreviewController::class)->product($productId);
    }

    return $spaEntrypoint();
})->where('productId', '[^/]+');

// Store admin is handled by the Vue SPA. Authentication is token-based via
// /api/v1/auth/*, so these paths should render the SPA entrypoint.
Route::get('/admin', $spaEntrypoint)->name('store.admin.entry');

Route::get('/admin-login', $spaEntrypoint)->name('store.admin.login.alias');

Route::get('/admin/{any}', $spaEntrypoint)->where('any', '.*');

Route::get('/', $spaEntrypoint)->name('store.home');

// Named login route fallback for framework auth redirects.
Route::get('/login', $spaEntrypoint)->name('login');

// Store SPA catch-all - serve the Vue.js SPA for all unmatched routes
// BUT exclude API routes (both /api and /store/api) and system routes
Route::get('/{any}', $spaEntrypoint)
    ->where('any', '^(?!api/|store/api/|upload/|images/|storage/|build/).*$')
    ->name('store.catchall');
