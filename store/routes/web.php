<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Store SPA public routes - disable legacy dashboard
Route::get('/dashboard', function () {
    abort(404);
});

// Admin Authentication Routes (store admin - guest only)
Route::prefix('store/admin')->name('store.admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/forgot-password', [AuthController::class, 'showReset'])->name('reset');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('reset.post');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Lightweight public ping for deployment health checks (no auth)
Route::get('/store/admin/ping', [AdminController::class, 'ping'])->name('store.admin.ping');

// Store Admin Protected Routes
Route::prefix('store/admin')->middleware(['auth:admin'])->name('store.admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
});

// Company Website Admin - serve from main app
Route::prefix('admin')->group(function () {
    Route::get('/{any}', function () {
        // Proxy to the admin app or serve admin views
        return redirect(env('ADMIN_URL', 'http://127.0.0.1:8000') . '/admin/' . request()->path());
    })->where('any', '.*');
});

// Store user page - serve SPA at root and /store
Route::get('/store', function () {
    return view('app');
})->name('store.home');

// Store SPA catch-all - serve the Vue.js SPA for all unmatched routes
// BUT exclude API routes and system routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|upload|store/admin).*$')->name('store.catchall');

