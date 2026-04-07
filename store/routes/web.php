<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Store SPA public routes - disable legacy dashboard
Route::get('/dashboard', function () {
    abort(404);
});

// Lightweight public ping for deployment health checks (no auth)
Route::get('/admin/ping', [AdminController::class, 'ping'])->name('store.admin.ping');

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

// Store SPA catch-all - serve the Vue.js SPA for all unmatched routes
// BUT exclude API routes (both /api and /store/api) and system routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api/|store/api/|upload/).*$')->name('store.catchall');

