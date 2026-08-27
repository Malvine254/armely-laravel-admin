<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // The browser URL prefix (/store/) is independent from the physical
        // manifest location inside this Laravel application's public folder.
        Vite::useBuildDirectory((string) env('VITE_BUILD_DIRECTORY', 'build'));
    }
}
