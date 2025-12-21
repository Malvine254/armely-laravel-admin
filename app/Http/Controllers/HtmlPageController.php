<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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

        $titles = [
            'aws' => 'AWS Partner',
            'snowflake' => 'Snowflake Partner',
            'microsoft' => 'Microsoft Partner',
            'redhat' => 'Red Hat Partner',
            'cisco' => 'Cisco Partner',
            'guardz' => 'Guardz Partner',
            'td-synnex' => 'TD SYNNEX Partner',
            'td' => 'TD SYNNEX Partner',
        ];

        $pageTitle = $titles[$slug] ?? ucfirst($slug) . ' Partner';

        // Render within site layout so header/footer are included
        return response()->view('partner-page', [
            'content' => $content,
            'pageTitle' => $pageTitle,
        ]);
    }
}
