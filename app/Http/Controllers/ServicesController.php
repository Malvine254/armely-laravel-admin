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

    public function index()
    {
        $services = DB::table('services_lists')
            ->select('title', 'image', 'body')
            ->get()
            ->map(function ($service) {
                $service->url_name = $this->titleToUrl[$service->title] ?? Str::slug($service->title);
                return $service;
            });

        return view('services.index', [
            'services' => $services,
            'titleToUrl' => $this->titleToUrl,
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
