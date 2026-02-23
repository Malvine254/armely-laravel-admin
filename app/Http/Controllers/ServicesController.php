<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    private array $titleToUrl = [
        'AI Consulting' => 'ai-consulting',
        'AI Advisory' => 'ai-advisory',
        'Generative AI' => 'generative-ai',
        'AI PoC Starter' => 'pocstarter-ai',
        'Estimate your Fabric Capacity' => 'fabric_capacity',
        'Microsoft Fabric' => 'fabric',
        'Data Science and Analytics' => 'data-science',
        'Data Strategy' => 'data-strategy',
        'Databricks' => 'databricks',
        'Snowflake' => 'snowflake',
        'SQL & Data Warehousing' => 'sql-data-warehousing',
        'API Data Access' => 'apidataaccess',
        'Microsoft PowerApps' => 'powerapps',
        'Microsoft Power Automate' => 'powerautomate',
        'Microsoft Power Virtual Agents' => 'virtualagents',
        'Microsoft Power Pages' => 'powerplatform',
        'Microsoft Dynamics 365' => 'dynamics365',
        'Robotic Processing Automation' => 'roboticprocessing',
        'SharePoint Online' => 'sharepointonline',
        'SQL Server Support' => 'sqlsupport',
        'Applications Support' => 'appsupport',
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
        $services_query = DB::table('services_lists')
            ->select('title', 'image', 'body');

        // Note: For now we fetch all since the frontend has a JS-based filter 
        // that expects all items to be present in the DOM. 
        // If we want real server-side filtering, we'd need to adjust the JS.
        $all_services = $services_query->get()
            ->map(function ($service) {
                $service->url_name = $this->titleToUrl[$service->title] ?? Str::slug($service->title);
                
                // Assign category based on title
                $titleLower = strtolower($service->title);
                $service->category = 'other';
                
                foreach ($this->serviceMap as $cat => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (str_contains($titleLower, $keyword)) {
                            $service->category = $cat;
                            break 2;
                        }
                    }
                }
                
                return $service;
            });

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
            'counts' => $counts
        ]);
    }

    public function show(string $name)
    {
        $urlToTitle = array_flip($this->titleToUrl);
        $title = $urlToTitle[$name] ?? null;

        // Handle freemiums listing page
        if ($name === 'freemiums') {
            $freemiums = DB::table('freemium')
                ->select('title', 'body', 'image_url', 'url_get_name', 'snippet')
                ->orderByDesc('id')
                ->get();

            return view('services.freemiums', [
                'freemiums' => $freemiums,
                'title' => 'Freemiums',
            ]);
        }

        // Resolve content from freemium table first
        $content = DB::table('freemium')
            ->where('title', $title)
            ->orWhere('url_get_name', $name)
            ->first();

        return view('services.show', [
            'title' => $title ?? Str::headline(str_replace('-', ' ', $name)),
            'content' => $content,
            'name' => $name,
        ]);
    }
}
