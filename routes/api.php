<?php

use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('resources')->group(function (): void {
    Route::get('/', [ResourceController::class, 'apiIndex'])->name('api.resources.index');
    Route::get('/{slug}', [ResourceController::class, 'apiShow'])->name('api.resources.show');
    Route::post('/{slug}/access-links', [ResourceController::class, 'apiAccessLinks'])->name('api.resources.access-links');
});
