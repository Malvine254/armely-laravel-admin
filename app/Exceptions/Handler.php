<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed when re-validating with validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (QueryException $e, $request) {
            // Catch database connection errors
            if (str_contains($e->getMessage(), 'No connection could be made') ||
                str_contains($e->getMessage(), 'Connection refused') ||
                str_contains($e->getMessage(), 'SQLSTATE[HY000] [2002]')) {
                
                Log::error('Database Connection Error', [
                    'message' => $e->getMessage(),
                    'connection' => $e->getConnectionName() ?? 'default',
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
    }
}
