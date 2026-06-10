<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    private array $titleToUrl = [
        'AI Consulting' => 'ai-consulting',
        'AI Advisory' => 'ai-advisory',
        'Generative AI' => 'generative-ai',
        'AI PoC Starter' => 'ai-poc-starter',
        'Estimate your Fabric Capacity' => 'estimate-your-fabric-capacity',
        'Microsoft Fabric' => 'microsoft-fabric',
        'Data Science and Analytics' => 'data-science-and-analytics',
        'Data Strategy' => 'data-strategy',
        'Databricks' => 'databricks',
        'Snowflake' => 'snowflake',
        'SQL & Data Warehousing' => 'sql-&-data-warehousing',
        'API Data Access' => 'api-data-access',
        'Microsoft PowerApps' => 'microsoft-powerapps',
        'Microsoft Power Automate' => 'microsoft-power-automate',
        'Microsoft Power Virtual Agents' => 'microsoft-power-virtual-agents',
        'Microsoft Power Pages' => 'microsoft-power-pages',
        'Microsoft Dynamics 365' => 'microsoft-dynamics-365',
        'Robotic Processing Automation' => 'robotic-processing-automation',
        'SharePoint Online' => 'sharepoint-online',
        'SQL Server Support' => 'sql-server-support',
        'Applications Support' => 'applications-support',
        'Freemiums' => 'freemiums',
    ];

    private array $serviceMap = [
        'ai' => [
            'ai consulting', 'ai advisory', 'generative ai', 'ai poc', 'virtual agents'
        ],
        'data' => [
            'estimate your fabric', 'microsoft fabric', 'data science', 'data strategy', 
            'databricks', 'snowflake', 'sql & data', 'analytics', 'data warehousing',
            'api data access'
        ],
        'digital' => [
            'powerapps', 'power automate', 'power pages', 'dynamics 365', 
            'robotic processing', 'sharepoint'
        ],
        'managed' => [
            'sql server support', 'applications support', 'sql support', 'appsupport'
        ],
        'advisory' => [
            'freemium', 'consulting', 'strategy'
        ]
    ];

    public function index()
    {
        $dbErrorMessage = null;

        // Note: For now we fetch all since the frontend has a JS-based filter 
        // that expects all items to be present in the DOM. 
        // If we want real server-side filtering, we'd need to adjust the JS.
        try {
            $all_services = DB::table('services_lists')
                ->select('title', 'image', 'body')
                ->get()
                ->map(function ($service) {
                    return $this->prepareService($service);
                });
        } catch (\Throwable $e) {
            Log::warning('Services list query failed; using fallback services', ['error' => $e->getMessage()]);
            $dbErrorMessage = 'Live service content is temporarily unavailable. Showing our standard service overview.';
            $all_services = collect();
        }

        if ($all_services->isEmpty()) {
            $all_services = collect($this->fallbackServices())
                ->map(fn ($service) => $this->prepareService((object) $service));
        }

        $counts = [
            'all' => $all_services->count(),
            'data' => $all_services->where('category', 'data')->count(),
            'digital' => $all_services->where('category', 'digital')->count(),
            'ai' => $all_services->where('category', 'ai')->count(),
            'managed' => $all_services->where('category', 'managed')->count(),
            'advisory' => $all_services->where('category', 'advisory')->count(),
        ];

        return view('services', [
            'services' => $all_services,
            'titleToUrl' => $this->titleToUrl,
            'counts' => $counts,
            'dbErrorMessage' => $dbErrorMessage,
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
        ]);
    }

    private function prepareService(object $service): object
    {
        $service->url_name = $this->titleToUrl[$service->title] ?? Str::slug($service->title);

        $titleLower = strtolower((string) $service->title);
        $bodyLower = strtolower(strip_tags((string) ($service->body ?? '')));
        $service->category = 'other';

        foreach ($this->serviceMap as $cat => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($titleLower, $keyword) || str_contains($bodyLower, $keyword)) {
                    $service->category = $cat;
                    break 2;
                }
            }
        }

        return $service;
    }

    private function fallbackServices(): array
    {
        return [
            ['title' => 'AI Consulting', 'image' => 'fa fa-brain', 'body' => 'Strategy, solution design, and implementation support for practical AI use cases across Microsoft Azure and Copilot.'],
            ['title' => 'AI Advisory', 'image' => 'fa fa-compass', 'body' => 'Executive guidance for AI readiness, governance, operating model design, and responsible adoption.'],
            ['title' => 'Generative AI', 'image' => 'fa fa-wand-magic-sparkles', 'body' => 'Custom copilots, knowledge assistants, retrieval workflows, and generative AI experiences for business teams.'],
            ['title' => 'Microsoft Fabric', 'image' => 'fa fa-database', 'body' => 'Modern analytics architecture, lakehouse design, Power BI reporting, and Microsoft Fabric implementation.'],
            ['title' => 'Data Strategy', 'image' => 'fa fa-chart-line', 'body' => 'Roadmaps for data platforms, governance, analytics maturity, and measurable business outcomes.'],
            ['title' => 'Data Science and Analytics', 'image' => 'fa fa-chart-pie', 'body' => 'Predictive analytics, dashboards, reporting automation, and operational insight delivery.'],
            ['title' => 'Microsoft PowerApps', 'image' => 'fa fa-mobile-screen-button', 'body' => 'Low-code business applications built for process modernization, field operations, and workflow improvement.'],
            ['title' => 'Microsoft Power Automate', 'image' => 'fa fa-gears', 'body' => 'Workflow automation, approval flows, integrations, and productivity improvements across Microsoft 365.'],
            ['title' => 'SharePoint Online', 'image' => 'fa fa-share-nodes', 'body' => 'Collaboration portals, document management, intranet modernization, and governance.'],
            ['title' => 'SQL Server Support', 'image' => 'fa fa-server', 'body' => 'Database administration, performance tuning, monitoring, and managed SQL support.'],
            ['title' => 'Applications Support', 'image' => 'fa fa-headset', 'body' => 'Managed application support, issue triage, enhancements, and operational continuity.'],
            ['title' => 'Freemiums', 'image' => 'fa fa-file-lines', 'body' => 'Guides, templates, and practical resources for leaders planning Microsoft platform initiatives.'],
        ];
    }

    public function show(string $name)
    {
        $urlToTitle = array_flip($this->titleToUrl);
        $title = $urlToTitle[$name] ?? null;

        // Handle freemiums listing page
        if ($name === 'freemiums') {
            try {
                $freemiums = DB::table('freemium')
                    ->select('title', 'body', 'image_url', 'url_get_name', 'snippet')
                    ->orderByDesc('id')
                    ->get();
            } catch (\Throwable $e) {
                Log::warning('Freemiums query failed; showing empty fallback', ['error' => $e->getMessage()]);
                $freemiums = collect();
            }

            return view('services.freemiums', [
                'freemiums' => $freemiums,
                'title' => 'Freemiums',
            ]);
        }

        // Resolve content from freemium table first
        try {
            $content = DB::table('freemium')
                ->where('title', $title)
                ->orWhere('url_get_name', $name)
                ->first();
        } catch (\Throwable $e) {
            Log::warning('Service detail content query failed; showing static title fallback', [
                'service' => $name,
                'error' => $e->getMessage(),
            ]);
            $content = null;
        }

        return view('services.show', [
            'title' => $title ?? Str::headline(str_replace('-', ' ', $name)),
            'content' => $content,
            'name' => $name,
        ]);
    }
}
