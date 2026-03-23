<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active.user' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Allow admin routes to bypass maintenance mode
        $middleware->preventRequestsDuringMaintenance(except: [
            'api/v1/auth/login',
            'api/v1/admin/*',
            'admin/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            // Handle unauthenticated requests
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                if ($request->is('api/*') || $request->expectsJson()) {
                    return response()->json([
                        'message' => 'Unauthenticated',
                        'success' => false,
                    ], 401);
                }
            }
        });
    })->create();
