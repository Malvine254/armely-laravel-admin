<?php

namespace App\Http\Middleware;

use App\Services\PriceSyncSchedulerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KickOffDuePriceSync
{
    public function handle(Request $request, Closure $next): Response
    {
        app(PriceSyncSchedulerService::class)->kickOffDueScheduledRun();

        return $next($request);
    }
}
