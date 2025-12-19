<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle database connection errors gracefully
        $exceptions->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            if (str_contains($e->getMessage(), 'No connection could be made') ||
                str_contains($e->getMessage(), 'Connection refused') ||
                str_contains($e->getMessage(), 'SQLSTATE[HY000] [2002]')) {
                
                \Illuminate\Support\Facades\Log::error('Database Connection Error', [
                    'message' => $e->getMessage(),
                    'url' => $request->getRequestUri(),
                ]);

                if ($request->acceptsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Service temporarily unavailable. Please try again in a few moments.',
                        'error' => 'database_unavailable',
                    ], 503);
                }

                return response()->view('errors.service-unavailable', [], 503);
            }
        });
    })->create();
