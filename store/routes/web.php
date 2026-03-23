<?php

use Illuminate\Support\Facades\Route;

// Explicitly disable legacy user dashboard route.
Route::get('/dashboard', function () {
    abort(404);
});

// Serve the Vue.js SPA for all routes (Vue Router will handle routing)
// BUT exclude API routes and other system routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|upload).*$');

