<?php

use App\Http\Controllers\MelaChatController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::prefix('resources')->group(function (): void {
    Route::get('/', [ResourceController::class, 'apiIndex'])->name('api.resources.index');
    Route::get('/{slug}', [ResourceController::class, 'apiShow'])->name('api.resources.show');
    Route::post('/{slug}/access-links', [ResourceController::class, 'apiAccessLinks'])->name('api.resources.access-links');
});

Route::prefix('mela/conversations')->group(function (): void {
    Route::post('/', [MelaChatController::class, 'start'])->middleware('throttle:mela-conversation')->name('api.mela.start');
    Route::get('/{conversation}', [MelaChatController::class, 'show'])->middleware('throttle:mela-read')->name('api.mela.show');
    Route::post('/{conversation}/messages', [MelaChatController::class, 'message'])->middleware('throttle:mela-message')->name('api.mela.message');
});
