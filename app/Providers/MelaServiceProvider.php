<?php

namespace App\Providers;

use App\Services\Mela\Escalation\EscalationNotifier;
use App\Services\Mela\Escalation\GraphEscalationNotifier;
use App\Services\Mela\Telemetry\MelaLogger;
use App\Services\Mela\Tools\CurrentCareerOpportunitiesTool;
use App\Services\Mela\Tools\FindRelevantServicesTool;
use App\Services\Mela\Tools\RequestHumanFollowUpTool;
use App\Services\Mela\Tools\SaveVisitorDetailsTool;
use App\Services\Mela\Tools\SearchKnowledgeTool;
use App\Services\Mela\Tools\ToolRegistry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class MelaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EscalationNotifier::class, GraphEscalationNotifier::class);
        $this->app->singleton(MelaLogger::class);

        $this->app->scoped(ToolRegistry::class, fn ($app) => new ToolRegistry($app->make(MelaLogger::class), [
            $app->make(SearchKnowledgeTool::class),
            $app->make(FindRelevantServicesTool::class),
            $app->make(CurrentCareerOpportunitiesTool::class),
            $app->make(SaveVisitorDetailsTool::class),
            $app->make(RequestHumanFollowUpTool::class),
        ]));
    }

    public function boot(): void
    {
        $tooMany = fn (Request $request, array $headers) => response()->json([
            'error' => 'rate_limited',
            'message' => "You're sending messages faster than I can keep up. Please wait a moment and try again.",
        ], 429, $headers);

        RateLimiter::for('mela-message', fn (Request $request) => [
            Limit::perMinute(max(1, (int) config('mela.rate_limits.per_minute', 12)))->by('mela-m:' . $request->ip())->response($tooMany),
            Limit::perDay(max(1, (int) config('mela.rate_limits.per_day', 200)))->by('mela-d:' . $request->ip())->response($tooMany),
        ]);

        RateLimiter::for('mela-conversation', fn (Request $request) => Limit::perHour(max(1, (int) config('mela.rate_limits.conversations_per_hour', 20)))
            ->by('mela-c:' . $request->ip())
            ->response($tooMany));

        RateLimiter::for('mela-read', fn (Request $request) => Limit::perMinute(60)->by('mela-r:' . $request->ip()));
    }
}
