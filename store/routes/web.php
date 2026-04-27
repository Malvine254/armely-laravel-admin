<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SharePreviewController;

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

// Store admin is handled by the Vue SPA. Authentication is token-based via
// /api/v1/auth/*, so these paths should render the SPA entrypoint.
Route::get('/admin', function () {
    return view('app');
})->name('store.admin.entry');

Route::get('/admin-login', function () {
    return view('app');
})->name('store.admin.login.alias');

Route::get('/admin/{any}', function () {
    return view('app');
})->where('any', '.*');

Route::get('/', function () {
    return view('app');
})->name('store.home');

// Named login route fallback for framework auth redirects.
Route::get('/login', function () {
    return view('app');
})->name('login');

// Store SPA catch-all - serve the Vue.js SPA for all unmatched routes
// BUT exclude API routes (both /api and /store/api) and system routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api/|store/api/|upload/).*$')->name('store.catchall');

