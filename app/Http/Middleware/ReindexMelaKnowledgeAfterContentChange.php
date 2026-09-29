<?php

namespace App\Http\Middleware;

use App\Services\Mela\Knowledge\MelaKnowledgeIndexRefresh;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReindexMelaKnowledgeAfterContentChange
{
    private const CONTENT_ROUTE_PREFIXES = [
        'admin.resources.',
        'admin.resource-categories.',
        'admin.case-study-categories.',
        'admin.case-study-technologies.',
        'admin.tables.announcements.',
        'admin.tables.blogs.',
        'admin.tables.careers.',
        'admin.tables.case-studies.',
        'admin.tables.customer-stories.',
        'admin.tables.events.',
        'admin.tables.social-impact.',
        'admin.tables.team.',
        'admin.tables.videos.',
        'admin.tables.white-papers.',
        'admin.company-content.',
    ];

    public function __construct(private readonly MelaKnowledgeIndexRefresh $indexRefresh)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $routeName = (string) $request->route()?->getName();

        if (
            !$request->isMethodSafe()
            && $response->getStatusCode() < 400
            && $this->isContentRoute($routeName)
        ) {
            $this->indexRefresh->dispatchAfterResponse('admin_content');
        }

        return $response;
    }

    private function isContentRoute(string $routeName): bool
    {
        foreach (self::CONTENT_ROUTE_PREFIXES as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return true;
            }
        }

        return false;
    }
}