<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Store SPA public routes - disable legacy dashboard
Route::get('/dashboard', function () {
    abort(404);
});

// Lightweight public ping for deployment health checks (no auth)
Route::get('/store/admin/ping', [AdminController::class, 'ping'])->name('store.admin.ping');

// Store admin is handled by the Vue SPA. Authentication is token-based via
// /api/v1/auth/*, so these paths should render the SPA entrypoint instead of
// relying on undefined session guards or legacy controller actions.
Route::get('/store/admin', function () {
    return view('app');
})->name('store.admin.entry');

Route::get('/store/admin-login', function () {
    return view('app');
})->name('store.admin.login.alias');

Route::get('/store/admin/{any}', function () {
    return view('app');
})->where('any', '.*');

// Company Website Admin - redirect to main admin portal
Route::prefix('admin')->group(function () {
    Route::get('/{any?}', function () {
        // Extract path after /admin/ prefix
        $path = request()->path();
        $pathAfterAdmin = preg_replace('~^admin/?~', '', $path);

        // In production, never fall back to localhost. If ADMIN_URL is not set,
        // use the current request host/scheme as the company admin base.
        $adminUrl = rtrim((string) env('ADMIN_URL', request()->getSchemeAndHttpHost()), '/');
        return redirect($adminUrl . '/admin/' . ltrim((string) $pathAfterAdmin, '/'));
    })->where('any', '.*');
});

// Store user page - serve SPA at root and /store
Route::get('/store', function () {
    return view('app');
})->name('store.home');

// Store user SPA routes when deployed under the /store base path.
Route::get('/store/{any}', function () {
    return view('app');
})->where('any', '.*');

// Store SPA catch-all - serve the Vue.js SPA for all unmatched routes
// BUT exclude API routes and system routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|upload|store(?:/|$)).*$')->name('store.catchall');

