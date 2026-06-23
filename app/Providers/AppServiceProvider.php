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
use Illuminate\Support\Facades\URL;

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
        // Keep generated URLs and assets on the same host as the incoming request.
        // If a canonical host is configured, generated URLs and assets follow it;
        // otherwise they stay on the incoming request host to avoid asset drift.
        if (!$this->app->runningInConsole()) {
            $requestHost = strtolower((string) request()->getHost());
            $canonicalHost = strtolower(trim((string) config('app.canonical_host', '')));
            $requestRoot = $canonicalHost !== '' && !in_array($requestHost, ['localhost', '127.0.0.1', '::1'], true)
                ? request()->getScheme() . '://' . $canonicalHost
                : request()->getSchemeAndHttpHost();
            $assetUrl = (string) config('app.asset_url', '');
            $assetHost = strtolower((string) (parse_url($assetUrl, PHP_URL_HOST) ?? ''));
            $assetPath = (string) (parse_url($assetUrl, PHP_URL_PATH) ?? '');
            $isStoreRequest = request()->is('store*');

            if (!$isStoreRequest) {
                URL::forceRootUrl($requestRoot);

                // If ASSET_URL is empty, points to a different host, or points into /store,
                // force it to the current request origin for consistent asset loading.
                if (
                    $assetUrl === ''
                    || ($assetHost !== '' && $assetHost !== $requestHost)
                    || ($assetUrl !== '' && str_contains($assetPath, '/store'))
                ) {
                    config(['app.asset_url' => $requestRoot]);
                }
            }

            if ($assetUrl !== '' && str_contains($assetPath, '/store') && !$isStoreRequest) {
                config(['app.asset_url' => $requestRoot]);
            }
        }

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
