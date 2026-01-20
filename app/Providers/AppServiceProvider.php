<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register login/logout listeners
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);
        // Auto-clear compiled Blade views when view sources change.
        // This avoids having to run `php artisan view:clear` after pushing updated views
        // (useful for hosts like GoDaddy where manual cache clearing is inconvenient).
        try {
            $viewsPath = resource_path('views');
            $hashFile = storage_path('framework/cache/views_hash.txt');

            if (File::exists($viewsPath)) {
                $files = File::allFiles($viewsPath);
                $acc = '';
                foreach ($files as $f) {
                    $acc .= $f->getRealPath() . '|' . $f->getMTime();
                }
                $hash = md5($acc);

                if (!File::exists($hashFile) || File::get($hashFile) !== $hash) {
                    // Clear compiled views so Blade recompiles with the new templates
                    Artisan::call('view:clear');
                    File::put($hashFile, $hash);
                    Log::info('Blade view cache cleared automatically due to view changes.');
                }
            }
        } catch (\Throwable $e) {
            // Don't break the app if this check fails; log for diagnostics.
            Log::warning('Automatic view cache check failed: ' . $e->getMessage());
        }
    }
}
