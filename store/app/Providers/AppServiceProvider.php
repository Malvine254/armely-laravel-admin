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
        $basePath = trim((string) env('VITE_APP_BASE_PATH', '/'), '/');
        $buildDirectory = $basePath !== '' ? $basePath.'/build' : 'build';

        Vite::useBuildDirectory($buildDirectory);
    }
}
