<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class HtmlPageController extends Controller
{
    /**
     * Serve whitelisted partner pages from html-pages directory.
     */
    public function show(string $slug)
    {
        $map = [
            'aws' => 'aws-full.php',
            'snowflake' => 'snowflake-full.php',
            'microsoft' => 'microsoft-full.php',
            'redhat' => 'redhat-full.php',
            'cisco' => 'cisco-full.php',
            'guardz' => 'guardz-full.php',
            'td-synnex' => 'td-full.php',
            'td' => 'td-full.php',
            'veeam' => 'veeam-full.php',
        ];

        if (!array_key_exists($slug, $map)) {
            return response()->view('errors.404', [], 404);
        }

        $file = base_path('html-pages/' . $map[$slug]);
        if (!is_file($file)) {
            Log::warning('Partner page file missing', ['file' => $file, 'slug' => $slug]);
            return response()->view('errors.404', [], 404);
        }

        // Execute the PHP file and capture its output safely
        try {
            ob_start();
            include $file;
            $content = ob_get_clean();
        } catch (\Throwable $e) {
            Log::error('Failed rendering partner page', ['error' => $e->getMessage(), 'file' => $file]);
            return response()->view('errors.500', [], 500);
        }

        $seo = [
            'aws' => [
                'title' => 'AWS Partner Services | Cloud Migration and Modernization | Armely',
                'description' => 'Explore Armely AWS partner capabilities across migration, modernization, data, security, and cloud operations for enterprise and public sector organizations.',
                'keywords' => 'AWS partner, AWS migration, AWS modernization, cloud operations, Armely',
            ],
            'snowflake' => [
                'title' => 'Snowflake Partner Services | Data Cloud and Analytics | Armely',
                'description' => 'Discover Armely Snowflake partner services for data platform modernization, analytics, governance, and AI-ready data architecture across regulated industries.',
                'keywords' => 'Snowflake partner, data cloud, analytics, data governance, Armely',
            ],
            'microsoft' => [
                'title' => 'Microsoft Partner Services | Data, AI, and Business Apps | Armely',
                'description' => 'See how Armely delivers Microsoft partner solutions across Fabric, Power Platform, Copilot, and Azure to improve operations, insight, and business outcomes.',
                'keywords' => 'Microsoft partner, Microsoft Fabric, Power Platform, Copilot, Azure, Armely',
            ],
            'redhat' => [
                'title' => 'Red Hat Partner Services | Hybrid Cloud and Platform Engineering | Armely',
                'description' => 'Review Armely Red Hat partner services for hybrid cloud architecture, automation, platform reliability, and secure enterprise modernization.',
                'keywords' => 'Red Hat partner, hybrid cloud, platform engineering, automation, Armely',
            ],
            'cisco' => [
                'title' => 'Cisco Partner Services | Network, Security, and Infrastructure | Armely',
                'description' => 'Learn how Armely Cisco partner capabilities support secure network modernization, infrastructure resilience, and operational performance at scale.',
                'keywords' => 'Cisco partner, network modernization, infrastructure security, enterprise networking, Armely',
            ],
            'guardz' => [
                'title' => 'Guardz Partner Services | Managed Cybersecurity for SMB | Armely',
                'description' => 'Explore Armely Guardz partner services for proactive cybersecurity operations, risk reduction, and managed protection for growing organizations.',
                'keywords' => 'Guardz partner, cybersecurity services, managed security, SMB security, Armely',
            ],
            'td-synnex' => [
                'title' => 'TD SYNNEX Partner Services | Technology Solutions and Delivery | Armely',
                'description' => 'See how Armely works with TD SYNNEX to accelerate technology sourcing, implementation, and partner-led delivery across core business platforms.',
                'keywords' => 'TD SYNNEX partner, technology solutions, IT delivery, enterprise platforms, Armely',
            ],
            'td' => [
                'title' => 'TD SYNNEX Partner Services | Technology Solutions and Delivery | Armely',
                'description' => 'See how Armely works with TD SYNNEX to accelerate technology sourcing, implementation, and partner-led delivery across core business platforms.',
                'keywords' => 'TD SYNNEX partner, technology solutions, IT delivery, enterprise platforms, Armely',
            ],
            'veeam' => [
                'title' => 'Veeam Partner Services | Backup, Recovery, and Resilience | Armely',
                'description' => 'Evaluate Armely Veeam partner services for modern backup architecture, cyber resilience, disaster recovery, and business continuity readiness.',
                'keywords' => 'Veeam partner, backup recovery, cyber resilience, disaster recovery, Armely',
            ],
        ];

        $meta = $seo[$slug] ?? [
            'title' => ucfirst($slug) . ' Partner | Armely',
            'description' => 'Explore Armely partner capabilities and solution offerings.',
            'keywords' => 'Armely partner services',
        ];
        $canonicalUrl = URL::to('/all-partners/' . $slug);

        // Render within site layout so header/footer are included
        return response()->view('partner-page', [
            'content' => $content,
            'pageTitle' => $meta['title'],
            'metaDescription' => $meta['description'],
            'metaKeywords' => $meta['keywords'],
            'canonicalUrl' => $canonicalUrl,
        ]);
    }
}
