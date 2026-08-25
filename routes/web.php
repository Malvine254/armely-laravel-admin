<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\TablesController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CompanyContentController;
use App\Http\Controllers\Admin\CaseStudyCategoryController;
use App\Http\Controllers\Admin\CaseStudyTechnologyController;
use App\Http\Controllers\Admin\ResourceCategoryController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CaseStudiesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HtmlPageController;
use App\Http\Controllers\DataReadinessLeadController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\SitemapController;
use App\Support\ServiceUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/mela-meeting-assistant/help', function () {
    return view('help.help');
})->name('help');

// Backward-compatible redirect for legacy activation links that missed
// the /store base path in older outbound emails.
Route::get('/activate-account', function (Request $request) {
    $queryString = $request->getQueryString();
    $target = '/store/activate-account' . ($queryString ? ('?' . $queryString) : '');

    return redirect()->to($target);
})->name('store.activation.legacy.redirect');

Route::get('/ui-responsiveness', function (\Illuminate\Http\Request $request) {
    $url = trim((string) $request->query('url', ''));

    return view('system.ui-responsiveness', [
        'initialUrl' => $url,
    ]);
})->name('ui-responsiveness');
Route::get('/ui-responsiveness/proxy-asset', function (Request $request) {
    $rawUrl = trim((string) $request->query('url', ''));
    $referer = trim((string) $request->query('referer', ''));

    if ($rawUrl === '') {
        return response('Missing url query parameter.', 422);
    }

    if (!preg_match('/^https?:\/\//i', $rawUrl)) {
        return response('Only absolute http(s) URLs are allowed for assets.', 422);
    }

    if (!filter_var($rawUrl, FILTER_VALIDATE_URL)) {
        return response('Invalid URL.', 422);
    }

    $sourceParts = parse_url($rawUrl);
    $sourceScheme = (string) ($sourceParts['scheme'] ?? 'https');
    $sourceHost = (string) ($sourceParts['host'] ?? '');
    $sourcePort = isset($sourceParts['port']) ? ':' . $sourceParts['port'] : '';
    $sourceOrigin = $sourceScheme . '://' . $sourceHost . $sourcePort;
    $sourcePath = (string) ($sourceParts['path'] ?? '/');
    $sourceDir = ($sourcePath === '' || str_ends_with($sourcePath, '/')) ? $sourcePath : dirname($sourcePath) . '/';
    $proxyAssetEndpoint = $request->getSchemeAndHttpHost()
        . route('ui-responsiveness.proxy-asset', [], false);

    try {
        $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'Accept' => '*/*',
            ];

        if (filter_var($referer, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $referer)) {
            $headers['Referer'] = $referer;
        }

        $upstream = Http::connectTimeout(6)
            ->timeout(20)
            ->retry(2, 150)
            ->withHeaders($headers)
            ->get($rawUrl);
    } catch (\Throwable $exception) {
        return response('Could not load asset URL: ' . $exception->getMessage(), 502);
    }

    if (!$upstream->successful()) {
        return response('Upstream returned status ' . $upstream->status() . ' for asset request.', 502);
    }

    $contentType = strtolower((string) $upstream->header('Content-Type', 'application/octet-stream'));
    $body = (string) $upstream->body();

    if (str_contains($contentType, 'text/css')) {
        $toAbsolute = function (string $candidate) use ($sourceScheme, $sourceOrigin, $sourceDir): string {
            if (preg_match('/^https?:\/\//i', $candidate)) {
                return $candidate;
            }

            if (str_starts_with($candidate, '//')) {
                return $sourceScheme . ':' . $candidate;
            }

            if (str_starts_with($candidate, '/')) {
                return $sourceOrigin . $candidate;
            }

            return $sourceOrigin . rtrim($sourceDir, '/') . '/' . ltrim($candidate, '/');
        };

        $body = preg_replace_callback(
            '/url\((\s*["\']?)([^)"\']+)(["\']?\s*)\)/i',
            function (array $matches) use ($toAbsolute, $rawUrl, $proxyAssetEndpoint): string {
                $rawCandidate = trim($matches[2]);
                if ($rawCandidate === '' || preg_match('/^(data:|blob:|javascript:|#)/i', $rawCandidate)) {
                    return $matches[0];
                }

                $absolute = $toAbsolute($rawCandidate);
                $absoluteHost = strtolower((string) (parse_url($absolute, PHP_URL_HOST) ?? ''));
                if ($absoluteHost !== '') {
                    $absolute = $proxyAssetEndpoint . '?' . http_build_query([
                        'rev' => 2,
                        'url' => $absolute,
                        'referer' => $rawUrl,
                    ]);
                }

                return 'url(' . $matches[1] . $absolute . $matches[3] . ')';
            },
            $body
        ) ?? $body;
    }

    return response($body, $upstream->status(), [
        'Content-Type' => $upstream->header('Content-Type', 'application/octet-stream'),
        'Cache-Control' => 'public, max-age=600',
    ]);
})->name('ui-responsiveness.proxy-asset');
Route::get('/ui-responsiveness/proxy', function (Request $request) {
    $rawUrl = trim((string) $request->query('url', ''));

    if ($rawUrl === '') {
        return response('Missing url query parameter.', 422);
    }

    if (!preg_match('/^https?:\/\//i', $rawUrl)) {
        $rawUrl = 'https://' . $rawUrl;
    }

    if (!filter_var($rawUrl, FILTER_VALIDATE_URL)) {
        return response('Invalid URL.', 422);
    }

    try {
        $upstream = Http::timeout(20)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml',
            ])
            ->get($rawUrl);
    } catch (\Throwable $exception) {
        return response('Could not load URL: ' . $exception->getMessage(), 502);
    }

    if (!$upstream->successful()) {
        return response('Upstream returned status ' . $upstream->status() . '.', 502);
    }

    $contentType = strtolower((string) $upstream->header('Content-Type', ''));
    if (!str_contains($contentType, 'text/html') && !str_contains($contentType, 'application/xhtml+xml')) {
        return response('Only HTML pages can be previewed.', 415);
    }

    $parts = parse_url($rawUrl);
    $scheme = (string) ($parts['scheme'] ?? 'https');
    $host = (string) ($parts['host'] ?? '');
    $port = isset($parts['port']) ? ':' . $parts['port'] : '';
    $origin = $scheme . '://' . $host . $port;
    $path = (string) ($parts['path'] ?? '/');
    $directory = ($path === '' || str_ends_with($path, '/')) ? $path : dirname($path);
    $directory = '/' . trim((string) $directory, '/');
    $directory = rtrim($directory, '/') . '/';
    $baseHref = $origin . $directory;

    $html = (string) $upstream->body();
    $proxyAssetEndpoint = $request->getSchemeAndHttpHost()
        . route('ui-responsiveness.proxy-asset', [], false);

    $resolveUrl = static function (string $candidate) use ($rawUrl): string {
        $candidate = html_entity_decode(trim($candidate), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($candidate === '' || preg_match('/^(?:data:|blob:|javascript:|mailto:|tel:|#)/i', $candidate)) {
            return $candidate;
        }

        try {
            return (string) UriResolver::resolve(new Uri($rawUrl), new Uri($candidate));
        } catch (\Throwable $exception) {
            return $candidate;
        }
    };

    $proxyAssetUrl = static function (string $candidate) use ($resolveUrl, $rawUrl, $proxyAssetEndpoint): string {
        $absolute = $resolveUrl($candidate);

        if (!preg_match('/^https?:\/\//i', $absolute)) {
            return $candidate;
        }

        return $proxyAssetEndpoint . '?' . http_build_query([
            'rev' => 2,
            'url' => $absolute,
            'referer' => $rawUrl,
        ]);
    };

    // A CSP copied from the upstream page describes its origin, not this local
    // preview origin, and would consequently block the assets rewritten below.
    $html = preg_replace(
        '/<meta\b(?=[^>]*\bhttp-equiv\s*=\s*(["\'])?content-security-policy\1)[^>]*>/i',
        '',
        $html
    ) ?? $html;

    // Keep all browser-fetched assets on the preview origin. This handles both
    // first-party files and third-party dependencies such as jQuery/CDN scripts.
    $assetAttributes = [
        'script' => ['src'],
        'link' => ['href'],
        'img' => ['src'],
        'source' => ['src'],
        'video' => ['src', 'poster'],
        'audio' => ['src'],
        'track' => ['src'],
        'input' => ['src'],
        'object' => ['data'],
        'embed' => ['src'],
    ];

    foreach ($assetAttributes as $tag => $attributes) {
        $html = preg_replace_callback(
            '/<' . $tag . '\b[^>]*>/is',
            static function (array $tagMatch) use ($attributes, $proxyAssetUrl): string {
                $tagHtml = $tagMatch[0];

                foreach ($attributes as $attribute) {
                    $tagHtml = preg_replace_callback(
                        '/(\s' . $attribute . '\s*=\s*)(["\'])(.*?)\2/is',
                        static fn (array $attributeMatch): string => $attributeMatch[1]
                            . $attributeMatch[2]
                            . e($proxyAssetUrl($attributeMatch[3]))
                            . $attributeMatch[2],
                        $tagHtml
                    ) ?? $tagHtml;
                }

                if (str_starts_with(strtolower($tagHtml), '<script')) {
                    $isClassicExternalScript = preg_match('/\ssrc\s*=/i', $tagHtml)
                        && !preg_match('/\stype\s*=\s*(["\'])module\1/i', $tagHtml);
                    $isDependencyScript = preg_match(
                        '/(?:jquery|bootstrap|popper|datatables|owl(?:\.|-|_)|slick|waypoints|counterup|magnific|datepicker|niceselect)/i',
                        $tagHtml
                    );

                    // Async scripts execute as soon as their individual download
                    // finishes. Dependency libraries must instead retain document
                    // order (jQuery before its plugins, Popper before Bootstrap).
                    if ($isClassicExternalScript && $isDependencyScript) {
                        $tagHtml = preg_replace('/\sasync(?:\s*=\s*(["\']).*?\1)?/i', '', $tagHtml) ?? $tagHtml;
                    }
                }

                return $tagHtml;
            },
            $html
        ) ?? $html;
    }

    $html = preg_replace_callback(
        '/(\ssrcset\s*=\s*)(["\'])(.*?)\2/is',
        static function (array $matches) use ($proxyAssetUrl): string {
            $candidates = preg_split('/\s*,\s*/', trim($matches[3])) ?: [];
            $rewritten = array_map(static function (string $candidate) use ($proxyAssetUrl): string {
                $parts = preg_split('/\s+/', trim($candidate), 2);
                return e($proxyAssetUrl($parts[0])) . (isset($parts[1]) ? ' ' . $parts[1] : '');
            }, $candidates);

            return $matches[1] . $matches[2] . implode(', ', $rewritten) . $matches[2];
        },
        $html
    ) ?? $html;

    $runtimeProxyConfig = json_encode([
        'endpoint' => $proxyAssetEndpoint,
        'revision' => 2,
        'sourceUrl' => $rawUrl,
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    $runtimeProxyScript = <<<'HTML'
<script data-ui-responsiveness-proxy>
(function (config) {
    'use strict';

    var assetAttributes = {
        SCRIPT: ['src'], LINK: ['href'], IMG: ['src'], SOURCE: ['src'],
        VIDEO: ['src', 'poster'], AUDIO: ['src'], TRACK: ['src'],
        INPUT: ['src'], OBJECT: ['data'], EMBED: ['src']
    };
    var proxyEndpoint = new URL(config.endpoint, location.origin);

    function proxify(value) {
        if (typeof value !== 'string' || value === '' || /^(?:data:|blob:|javascript:|mailto:|tel:|#)/i.test(value)) {
            return value;
        }

        try {
            // Static HTML assets have already been rewritten by PHP. Resolve
            // against the preview origin first so those URLs are not proxied a
            // second time against the upstream page's <base> URL.
            var localCandidate = new URL(value, location.origin);
            if (localCandidate.origin === proxyEndpoint.origin && localCandidate.pathname === proxyEndpoint.pathname) {
                return value;
            }

            var absolute = new URL(value, config.sourceUrl);
            if (!/^https?:$/.test(absolute.protocol)) return value;
            return config.endpoint + '?rev=' + encodeURIComponent(config.revision)
                + '&url=' + encodeURIComponent(absolute.href)
                + '&referer=' + encodeURIComponent(config.sourceUrl);
        } catch (error) {
            return value;
        }
    }

    var nativeSetAttribute = Element.prototype.setAttribute;
    Element.prototype.setAttribute = function (name, value) {
        var attributes = assetAttributes[this.tagName];
        if (attributes && attributes.indexOf(String(name).toLowerCase()) !== -1) {
            value = proxify(String(value));
        }
        return nativeSetAttribute.call(this, name, value);
    };

    [
        [HTMLScriptElement, 'src'], [HTMLLinkElement, 'href'],
        [HTMLImageElement, 'src'], [HTMLSourceElement, 'src'],
        [HTMLMediaElement, 'src'], [HTMLVideoElement, 'poster'],
        [HTMLTrackElement, 'src'], [HTMLInputElement, 'src'],
        [HTMLObjectElement, 'data'], [HTMLEmbedElement, 'src']
    ].forEach(function (entry) {
        var prototype = entry[0] && entry[0].prototype;
        var property = entry[1];
        var descriptor = prototype && Object.getOwnPropertyDescriptor(prototype, property);
        if (!descriptor || !descriptor.get || !descriptor.set || descriptor.configurable === false) return;

        Object.defineProperty(prototype, property, {
            configurable: descriptor.configurable,
            enumerable: descriptor.enumerable,
            get: descriptor.get,
            set: function (value) { descriptor.set.call(this, proxify(String(value))); }
        });
    });
})($UI_RESPONSIVENESS_PROXY_CONFIG$);
</script>
HTML;
    $runtimeProxyScript = str_replace('$UI_RESPONSIVENESS_PROXY_CONFIG$', $runtimeProxyConfig ?: '{}', $runtimeProxyScript);

    if (preg_match('/<head\b[^>]*>/i', $html) === 1) {
        $html = preg_replace_callback(
            '/<head\b[^>]*>/i',
            static fn (array $matches): string => $matches[0]
                . '<base href="' . e($baseHref) . '">'
                . $runtimeProxyScript,
            $html,
            1
        ) ?? $html;
    } else {
        $html = '<head><base href="' . e($baseHref) . '">' . $runtimeProxyScript . '</head>' . $html;
    }

    return response($html, 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        'CDN-Cache-Control' => 'no-store',
        'Pragma' => 'no-cache',
    ]);
})->name('ui-responsiveness.proxy');
// Legacy homepage paths used by old static builds.
Route::redirect('/index', '/', 301);
Route::redirect('/home', '/', 301);
Route::redirect('/home/', '/', 301);
Route::redirect('/index.php', '/', 301);
Route::redirect('/index.html', '/', 301);

// Legacy marketing paths consolidated to canonical pages.
Route::redirect('/products', '/services', 301);
Route::redirect('/case-study', '/case-studies', 301);
Route::redirect('/case_study', '/case-studies', 301);
Route::redirect('/case study', '/case-studies', 301);

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.xml');
Route::get('/sitemap-index.xml', [SitemapController::class, 'sitemapIndex'])->name('sitemap.index');
Route::get('/blog-sitemap.xml', [SitemapController::class, 'blog'])->name('blog.sitemap.xml');
Route::get('/services-sitemap.xml', [SitemapController::class, 'services'])->name('services.sitemap.xml');
Route::get('/industries-sitemap.xml', [SitemapController::class, 'industries'])->name('industries.sitemap.xml');
Route::get('/partners-sitemap.xml', [SitemapController::class, 'partners'])->name('partners.sitemap.xml');
Route::get('/customer-stories-sitemap.xml', [SitemapController::class, 'customerStories'])->name('customer-stories.sitemap.xml');
// Backward-compatible sitemap URLs from older SEO plugins/configurations.
Route::redirect('/sitemap_index.xml', '/sitemap-index.xml', 301);
Route::redirect('/page-sitemap.xml', '/sitemap-index.xml', 301);
Route::redirect('/post-sitemap.xml', '/blog-sitemap.xml', 301);
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/announcements', [HomeController::class, 'announcements'])->name('announcements');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/admin-unsubscribe', [NewsletterController::class, 'unsubscribeAdmin'])
    ->middleware('signed')
    ->name('newsletter.admin.unsubscribe');
Route::get('/newsletter/unsubscribed', [NewsletterController::class, 'unsubscribeConfirmation'])
    ->name('newsletter.unsubscribe.confirmation');
Route::get('/services/invoicelens', [HomeController::class, 'invoiceLens'])->name('invoice-lens');
Route::redirect('/services/invoice-lens', '/services/invoicelens', 301);
Route::redirect('/invoice-lens', '/services/invoicelens', 301);
// Thank you page - only accessible via form submission with valid session token
Route::get('/contact/thank-you', [HomeController::class, 'contactThankYou'])
    ->middleware(\App\Http\Middleware\ContactSubmissionMiddleware::class)
    ->name('contact.thank-you');

Route::post('/data-readiness/submit', [DataReadinessLeadController::class, 'submit'])->name('data-readiness.submit');

// Services listing page
Route::get('/services', [ServicesController::class, 'index'])->name('services');
Route::view('/services/assessments', 'services.assessments_blade')->name('assessments');
Route::redirect('/assessments', '/services/assessments', 301);
Route::redirect('/services/sql-server', '/services/sql-data-warehousing', 301);
Route::get('/services/{name}', [HomeController::class, 'serviceDetails'])->name('services.show');

Route::view('/solutions/protective-order-digitization', 'solutions.protective-order')->name('protective-order-solution');
Route::redirect('/protective-order-solution', '/solutions/protective-order-digitization', 301);

// Accept query parameter format: /service-details?name=ai-consulting
Route::get('/service-details', function(\Illuminate\Http\Request $request) {
    $name = trim((string) $request->query('name', ''));
    $canonical = ServiceUrl::canonicalSlug($name) ?: 'ai-consulting';

    return redirect()->route('services.show', ['name' => $canonical], 301);
});

// Standard path parameter format: /service-details/ai-consulting
Route::get('/service-details/{name}', function (string $name) {
    $canonical = ServiceUrl::canonicalSlug($name) ?: 'ai-consulting';

    return redirect()->route('services.show', ['name' => $canonical], 301);
})->where('name', '[^/]+');
Route::post('/submit-consultation', [HomeController::class, 'submitConsultation'])->name('submit-consultation');

// Backward-compatible service paths from standalone service pages.
foreach ([
    'ai-data-strategy' => 'data-strategy',
    'api-development' => 'api-data-access',
    'api-dev' => 'api-data-access',
    'copilot' => 'copilot',
    'custom-development' => 'custom-development',
    'dynamics365' => 'microsoft-dynamics-365',
    'dynamics-365' => 'microsoft-dynamics-365',
    'fabric' => 'microsoft-fabric',
    'genai' => 'generative-ai',
    'generative-ai' => 'generative-ai',
    'm365-governance' => 'm365-governance',
    'managed-services' => 'managed-services',
    'powerapps' => 'microsoft-powerapps',
    'power-apps' => 'microsoft-powerapps',
    'powerautomate' => 'microsoft-power-automate',
    'power-platform' => 'microsoft-power-pages',
    'power-automate' => 'microsoft-power-automate',
    'powerplatform' => 'microsoft-power-pages',
    'sharepoint' => 'sharepoint-online',
    'sharepointonline' => 'sharepoint-online',
    'snowflake' => 'snowflake',
    'sql-server' => 'sql-data-warehousing',
] as $legacyServicePath => $serviceSlug) {
    Route::get('/' . $legacyServicePath, function () use ($serviceSlug) {
        return redirect()->route('services.show', ['name' => ServiceUrl::canonicalSlug($serviceSlug) ?: $serviceSlug], 301);
    });
}

Route::get('/case-studies', [CaseStudiesController::class, 'index'])->name('case-studies.index');
Route::post('/case-studies/lead', [CaseStudiesController::class, 'submitLead'])->name('case-studies.lead.submit');
Route::get('/case-studies/access/{caseStudy}', [CaseStudiesController::class, 'accessCaseStudy'])->name('case-studies.access');
Route::get('/white-papers/access/{paper}', [CaseStudiesController::class, 'accessWhitePaper'])->name('white-papers.access');
Route::get('/white-papers/view/{slug}', [ResourceController::class, 'show'])->name('white-papers.view');
Route::get('/resources', fn () => abort(404))->name('resources.index');
Route::redirect('/Resources', '/resources', 301);
Route::redirect('/Resources/', '/resources', 301);
Route::get('/whitepapers/{slug}', [ResourceController::class, 'show'])->name('whitepapers.show');
Route::post('/whitepapers/{slug}/request', [ResourceController::class, 'requestResource'])->name('whitepapers.request');
Route::get('/whitepapers/{slug}/download', [ResourceController::class, 'download'])->name('whitepapers.download');
Route::post('/resources/{slug}/request', [ResourceController::class, 'requestResource'])->name('resources.request');
Route::get('/resources/{slug}/download', [ResourceController::class, 'download'])->name('resources.download');
Route::get('/resources/{slug}', [ResourceController::class, 'show'])->name('resources.show');
Route::get('/case_docs/{file}', [CaseStudiesController::class, 'legacyCaseDoc'])
    ->where('file', '.*')
    ->name('case-studies.legacy-doc');
Route::get('/white_paper_docs/{file}', [CaseStudiesController::class, 'legacyWhitePaperDoc'])
    ->where('file', '.*')
    ->name('white-papers.legacy-doc');
Route::get('/case-studies/{slug}', [CaseStudiesController::class, 'showCaseStudy'])->name('case-studies.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blog}', [BlogController::class, 'show'])
    ->where('blog', '[^/]+')
    ->name('blog.show');
Route::get('/blog.php', function (\Illuminate\Http\Request $request) {
    $blogId = trim((string) $request->query('blogId', ''));

    return $blogId !== ''
        ? redirect()->route('blog.show', ['blog' => $blogId], 301)
        : redirect()->route('blog.index', [], 301);
});
// Keep the legacy plural URL as a permanent redirect so search signals stay on /blog.
Route::get('/blogs/{blog?}', function (?string $blog = null) {
    return $blog
        ? redirect()->route('blog.show', ['blog' => $blog], 301)
        : redirect()->route('blog.index', [], 301);
})->where('blog', '.*');
Route::post('/blog/{blogId}/increment-clicks', [BlogController::class, 'incrementClicks'])->name('blog.increment-clicks');
Route::post('/blog/{blogId}/request-download', [BlogController::class, 'requestDownload'])->name('blog.request-download');
Route::get('/blog/{blogId}/download-pdf', [BlogController::class, 'downloadPdf'])->name('blog.download-pdf');
Route::get('/all-partners', [HomeController::class, 'allPartners'])->name('partners.index');
Route::get('/all-partners/{slug}', [HtmlPageController::class, 'show'])
    ->where('slug', '^(aws|snowflake|microsoft|redhat|cisco|guardz|td-synnex|td|veeam)$')
    ->name('partners.show');

// Backward-compatible redirect for old path
Route::get('/partners/{slug}', function ($slug) {
    return redirect()->route('partners.show', ['slug' => $slug]);
});







Route::get('/events', [HomeController::class, 'events'])->name('events.index');

// Intentionally unlinked invite-only registration page.
Route::get('/events/sovereign-data-cloud-executive-briefing/register', [EventRegistrationController::class, 'create'])
    ->name('events.sovereign-data-cloud.register');
Route::post('/events/sovereign-data-cloud-executive-briefing/register', [EventRegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('events.sovereign-data-cloud.register.store');
Route::get('/private-events/{slug}/register', [EventRegistrationController::class, 'createPrivate'])
    ->name('events.private.register');
Route::post('/private-events/{slug}/register', [EventRegistrationController::class, 'storePrivate'])
    ->middleware('throttle:10,1')
    ->name('events.private.register.store');
Route::get('/event-emails/unsubscribe/{token}', [EventRegistrationController::class, 'unsubscribe'])
    ->middleware('signed')
    ->name('events.emails.unsubscribe');

Route::get('/company', [HomeController::class, 'company'])->name('company.index');
Route::get('/career', [HomeController::class, 'career'])->name('career.index');
// Route::get('/team', [HomeController::class, 'team'])->name('team.index'); // Hidden per request
Route::get('/customer-stories', [HomeController::class, 'customerStories'])->name('customer-stories.index');
Route::get('/customer-stories/{story}', [HomeController::class, 'customerStoryShow'])
    ->whereNumber('story')
    ->name('customer-stories.show');
Route::get('/social-impact', [HomeController::class, 'socialImpact'])->name('social-impact.index');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/support', [HomeController::class, 'support'])->name('support');
// IP / Country test endpoints
Route::get('/visit/geoip', [\App\Http\Controllers\VisitController::class, 'geoip'])->name('visit.geoip');
Route::get('/visit/ipapi', [\App\Http\Controllers\VisitController::class, 'ipapi'])->name('visit.ipapi');
Route::get('/visit/cloudflare', [\App\Http\Controllers\VisitController::class, 'cloudflare'])->name('visit.cloudflare');
Route::get('/social-impact-details/{secure_id}', [HomeController::class, 'socialImpactDetails'])->name('social-impact-details');
Route::get('/industries', [HomeController::class, 'industries'])->name('industries.index');
Route::get('/industries/{industry}', [HomeController::class, 'industryShow'])
    ->where('industry', '[A-Za-z0-9\-]+')
    ->name('industries.show');

//Route::get('/mela-ai', [HomeController::class, 'melaAi'])->name('mela-ai');

Route::get('/mela-meeting-assistant', [HomeController::class, 'melaMeetingAssistant'])->name('mela-meeting-assistant');
Route::view('/mela-ai-terms-of-use', 'legal.mela-terms-of-use')->name('mela.terms');
Route::view('/mela-ai-privacy-policy', 'legal.mela-ai-privacy-policy')->name('mela.privacy');
Route::redirect('/mela-meeting-assistant-terms-of-use', '/mela-ai-terms-of-use', 301);
Route::redirect('/mela-meeting-assistant-privacy-policy', '/mela-ai-privacy-policy', 301);
Route::redirect('/mela-meeting-assistant/terms-of-use', '/mela-ai-terms-of-use', 301);
Route::redirect('/mela-meeting-assistant/privacy-policy', '/mela-ai-privacy-policy', 301);

$storeBaseUrl = trim((string) env('STORE_URL', ''));

// Path-based deployments (e.g. https://armely.com/store or http://localhost/store)
// should be handled by the web server directly, not by the iframe bridge routes below.
$storeBridgeDisabledForPathMode = false;
if ($storeBaseUrl !== '') {
    $parsedStoreUrl = parse_url($storeBaseUrl);
    $storeHost = strtolower((string) ($parsedStoreUrl['host'] ?? ''));
    $storePath = '/' . trim((string) ($parsedStoreUrl['path'] ?? ''), '/');

    $isPathBasedStore = str_starts_with($storePath, '/store');
    $isLocalOrMainHost = in_array($storeHost, ['', 'localhost', '127.0.0.1', 'armely.com', 'www.armely.com'], true);

    $storeBridgeDisabledForPathMode = $isPathBasedStore && $isLocalOrMainHost;
}

$buildStoreTarget = function (string $baseUrl, string $path = '', ?string $queryString = null): ?string {
    if ($baseUrl === '') {
        return null;
    }

    $baseUrl = rtrim($baseUrl, '/');
    $path = ltrim($path, '/');

    $target = $path !== '' ? $baseUrl . '/' . $path : $baseUrl;

    if ($queryString !== null && $queryString !== '') {
        $target .= (str_contains($target, '?') ? '&' : '?') . $queryString;
    }

    return $target;
};

if (!$storeBridgeDisabledForPathMode) {
    Route::get('/store', function (\Illuminate\Http\Request $request) use ($storeBaseUrl, $buildStoreTarget) {
        $targetUrl = $buildStoreTarget($storeBaseUrl, '', $request->getQueryString());
        if ($targetUrl === null) {
            return redirect('/');
        }

        return response()->view('system.store-bridge', ['targetUrl' => $targetUrl]);
    })->name('armely-store');

    Route::get('/store/{path}', function (\Illuminate\Http\Request $request, string $path) use ($storeBaseUrl, $buildStoreTarget) {
        $targetUrl = $buildStoreTarget($storeBaseUrl, $path, $request->getQueryString());
        if ($targetUrl === null) {
            return redirect('/');
        }

        return response()->view('system.store-bridge', ['targetUrl' => $targetUrl]);
    })->where('path', '.*');
} else {
    Route::get('/store', function (\Illuminate\Http\Request $request) use ($storeBaseUrl, $buildStoreTarget) {
        $targetUrl = $buildStoreTarget($storeBaseUrl, '', $request->getQueryString());
        if ($targetUrl === null) {
            return redirect('/');
        }

        return redirect()->away($targetUrl);
    })->name('armely-store');

    Route::get('/store/{path}', function (\Illuminate\Http\Request $request, string $path) use ($storeBaseUrl, $buildStoreTarget) {
        $targetUrl = $buildStoreTarget($storeBaseUrl, $path, $request->getQueryString());
        if ($targetUrl === null) {
            return redirect('/');
        }

        return redirect()->away($targetUrl);
    })->where('path', '.*');
}

Route::get('/armely-store', function () {
    return redirect()->route('armely-store');
});

Route::get('/store/public/{path?}', function (?string $path = null) {
    return $path
        ? redirect('/store/' . ltrim($path, '/'))
        : redirect('/store');
})->where('path', '.*');
Route::get('/job-board', [HomeController::class, 'jobBoard'])->name('job-board.index');
Route::get('/applications', [HomeController::class, 'applications'])->name('applications.index');
Route::post('/applications', [HomeController::class, 'submitApplication'])->name('applications.submit');

// Search routes
Route::get('/api/search', [SearchController::class, 'search'])->name('search.api');
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// Analytics API (requires admin auth)
Route::get('/api/analytics/summary', [AnalyticsController::class, 'apiSummary'])->name('api.analytics.summary')->middleware('auth:admin');

// Admin Authentication Routes (guest only)
Route::get('/admin', [AuthController::class, 'showLogin']);

// Alias route to avoid local php artisan serve collision with public/admin/* assets path.
Route::get('/admin-login', [AuthController::class, 'showLogin'])->name('admin.login.alias');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::get('/forgot-password', [AuthController::class, 'showReset'])->name('admin.reset');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.reset.post');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.update');
});

// Lightweight public ping for deployment health checks (no auth)
Route::get('/admin/tables/ping', [TablesController::class, 'ping'])->name('admin.tables.ping');

// Admin Protected Routes
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    // Graceful fallback: allow GET logout to handle cases where JS/form submission fails
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout.get');

    // Resource Management
    Route::get('/resources', [AdminResourceController::class, 'index'])->name('admin.resources.index');
    Route::get('/resources/create', [AdminResourceController::class, 'create'])->name('admin.resources.create');
    Route::post('/resources', [AdminResourceController::class, 'store'])->name('admin.resources.store');
    Route::get('/resources/{resource}/file', [AdminResourceController::class, 'download'])->name('admin.resources.download');
    Route::get('/resources/{resource}/edit', [AdminResourceController::class, 'edit'])->name('admin.resources.edit');
    Route::put('/resources/{resource}', [AdminResourceController::class, 'update'])->name('admin.resources.update');
    Route::delete('/resources/{resource}', [AdminResourceController::class, 'destroy'])->name('admin.resources.destroy');
    Route::get('/resource-categories', [ResourceCategoryController::class, 'index'])->name('admin.resource-categories.index');
    Route::post('/resource-categories', [ResourceCategoryController::class, 'store'])->name('admin.resource-categories.store');
    Route::delete('/resource-categories/{resourceCategory}', [ResourceCategoryController::class, 'destroy'])->name('admin.resource-categories.destroy');
    Route::get('/case-study-categories', [CaseStudyCategoryController::class, 'index'])->name('admin.case-study-categories.index');
    Route::post('/case-study-categories', [CaseStudyCategoryController::class, 'store'])->name('admin.case-study-categories.store');
    Route::delete('/case-study-categories/{caseStudyCategory}', [CaseStudyCategoryController::class, 'destroy'])->name('admin.case-study-categories.destroy');
    Route::get('/case-study-technologies', [CaseStudyTechnologyController::class, 'index'])->name('admin.case-study-technologies.index');
    Route::post('/case-study-technologies', [CaseStudyTechnologyController::class, 'store'])->name('admin.case-study-technologies.store');
    Route::delete('/case-study-technologies/{caseStudyTechnology}', [CaseStudyTechnologyController::class, 'destroy'])->name('admin.case-study-technologies.destroy');
    
    // Tables Management (CRUD for content)
    Route::get('/tables', [TablesController::class, 'index'])->name('admin.tables');
    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('admin.csrf-token');
    
    // List endpoints for AJAX table reload
    Route::get('/tables/blogs/list', [TablesController::class, 'listBlogs'])->name('admin.tables.blogs.list');
    Route::get('/tables/videos/list', [TablesController::class, 'listVideos'])->name('admin.tables.videos.list');
    Route::get('/tables/careers/list', [TablesController::class, 'listCareers'])->name('admin.tables.careers.list');
    Route::get('/tables/social-impact/list', [TablesController::class, 'listSocialImpact'])->name('admin.tables.social-impact.list');
    Route::get('/tables/customer-stories/list', [TablesController::class, 'listCustomerStories'])->name('admin.tables.customer-stories.list');
    Route::get('/tables/case-studies/list', [TablesController::class, 'listCaseStudies'])->name('admin.tables.case-studies.list');
    Route::get('/tables/events/list', [TablesController::class, 'listEvents'])->name('admin.tables.events.list');
    Route::get('/tables/team/list', [TablesController::class, 'listTeam'])->name('admin.tables.team.list');
    Route::get('/tables/contacts/list', [TablesController::class, 'listContacts'])->name('admin.tables.contacts.list');
    
    // Blogs
    Route::get('/tables/blogs/{id}', [TablesController::class, 'showBlog'])->name('admin.tables.blogs.show');
    Route::post('/tables/blogs', [TablesController::class, 'storeOrUpdateBlog'])->name('admin.tables.blogs.store');
    Route::post('/tables/blogs/{id}', [TablesController::class, 'updateBlog'])->name('admin.tables.blogs.update.post');
    Route::put('/tables/blogs/{id}', [TablesController::class, 'updateBlog'])->name('admin.tables.blogs.update');
    Route::delete('/tables/blogs/{id}', [TablesController::class, 'deleteBlog'])->name('admin.tables.blogs.delete');
    
    // Videos
    Route::get('/tables/videos/{id}', [TablesController::class, 'showVideo'])->name('admin.tables.videos.show');
    Route::post('/tables/videos', [TablesController::class, 'storeOrUpdateVideo'])->name('admin.tables.videos.store');
    Route::put('/tables/videos/{id}', [TablesController::class, 'updateVideo'])->name('admin.tables.videos.update');
    Route::delete('/tables/videos/{id}', [TablesController::class, 'deleteVideo'])->name('admin.tables.videos.delete');
    
    // Careers
    Route::get('/tables/careers/{id}', [TablesController::class, 'showCareer'])->name('admin.tables.careers.show');
    Route::post('/tables/careers', [TablesController::class, 'storeOrUpdateCareer'])->name('admin.tables.careers.store');
    Route::put('/tables/careers/{id}', [TablesController::class, 'updateCareer'])->name('admin.tables.careers.update');
    Route::delete('/tables/careers/{id}', [TablesController::class, 'deleteCareer'])->name('admin.tables.careers.delete');
    
    // Social Impact
    Route::get('/tables/social-impact/{id}', [TablesController::class, 'showSocialImpact'])->name('admin.tables.social-impact.show');
    Route::post('/tables/social-impact', [TablesController::class, 'storeOrUpdateSocialImpact'])->name('admin.tables.social-impact.store');
    Route::put('/tables/social-impact/{id}', [TablesController::class, 'updateSocialImpact'])->name('admin.tables.social-impact.update');
    Route::delete('/tables/social-impact/{id}', [TablesController::class, 'deleteSocialImpact'])->name('admin.tables.social-impact.delete');
    
    // Customer Stories
    Route::get('/tables/customer-stories/{id}', [TablesController::class, 'showCustomerStory'])->name('admin.tables.customer-stories.show');
    Route::post('/tables/customer-stories', [TablesController::class, 'storeOrUpdateCustomerStory'])->name('admin.tables.customer-stories.store');
    Route::put('/tables/customer-stories/{id}', [TablesController::class, 'updateCustomerStory'])->name('admin.tables.customer-stories.update');
    Route::delete('/tables/customer-stories/{id}', [TablesController::class, 'deleteCustomerStory'])->name('admin.tables.customer-stories.delete');

    // Announcements
    Route::post('/tables/announcements', [TablesController::class, 'storeAnnouncement'])->name('admin.tables.announcements.store');
    Route::put('/tables/announcements/{id}', [TablesController::class, 'updateAnnouncement'])->name('admin.tables.announcements.update');
    Route::post('/tables/announcements/{id}/toggle', [TablesController::class, 'toggleAnnouncementStatus'])->name('admin.tables.announcements.toggle');
    Route::delete('/tables/announcements/{id}', [TablesController::class, 'deleteAnnouncement'])->name('admin.tables.announcements.delete');

    // Case Studies
    Route::post('/tables/case-studies', [TablesController::class, 'storeOrUpdateCaseStudy'])->name('admin.tables.case-studies.store');
    Route::put('/tables/case-studies/{id}', [TablesController::class, 'storeOrUpdateCaseStudy'])->name('admin.tables.case-studies.update');
    Route::delete('/tables/case-studies/{id}', [TablesController::class, 'deleteCaseStudy'])->name('admin.tables.case-studies.delete');

    // White Papers
    Route::post('/tables/white-papers', [TablesController::class, 'storeOrUpdateWhitePaper'])->name('admin.tables.white-papers.store');
    Route::put('/tables/white-papers/{id}', [TablesController::class, 'storeOrUpdateWhitePaper'])->name('admin.tables.white-papers.update');
    Route::delete('/tables/white-papers/{id}', [TablesController::class, 'deleteWhitePaper'])->name('admin.tables.white-papers.delete');
    
    // Events
    Route::post('/tables/events', [TablesController::class, 'storeOrUpdateEvent'])->name('admin.tables.events.store');
    Route::delete('/tables/events/{id}', [TablesController::class, 'deleteEvent'])->name('admin.tables.events.delete');
    Route::get('/tables/event-registrations/list', [TablesController::class, 'listEventRegistrations'])->name('admin.tables.event-registrations.list');
    Route::post('/tables/event-registrations/{id}/status', [TablesController::class, 'updateEventRegistrationStatus'])->name('admin.tables.event-registrations.status');
    Route::post('/tables/event-registrations/send-link', [TablesController::class, 'sendEventLinkToVerified'])->name('admin.tables.event-registrations.send-link');
    Route::post('/tables/event-registrations/send-thank-you', [TablesController::class, 'sendEventThankYou'])->name('admin.tables.event-registrations.send-thank-you');
    
    // Team
    Route::post('/tables/team', [TablesController::class, 'storeOrUpdateTeam'])->name('admin.tables.team.store');
    Route::delete('/tables/team/{id}', [TablesController::class, 'deleteTeam'])->name('admin.tables.team.delete');
    
    // Contacts
    Route::post('/tables/contacts', [TablesController::class, 'storeOrUpdateContact'])->name('admin.tables.contacts.store');
    Route::delete('/tables/contacts/{id}', [TablesController::class, 'deleteContact'])->name('admin.tables.contacts.delete');

    // Newsletter Subscribers
    Route::post('/tables/newsletter/{id}/unsubscribe', [TablesController::class, 'unsubscribeNewsletterSubscriber'])->name('admin.tables.newsletter.unsubscribe');
    Route::post('/tables/newsletter/{id}/resubscribe', [TablesController::class, 'resubscribeNewsletterSubscriber'])->name('admin.tables.newsletter.resubscribe');
    Route::delete('/tables/newsletter/{id}', [TablesController::class, 'deleteNewsletterSubscriber'])->name('admin.tables.newsletter.delete');
    
    // Admin User Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');

    Route::get('/admins', [AdminsController::class, 'index'])->name('admin.admins');
    Route::post('/admins', [AdminsController::class, 'store'])->name('admin.admins.store');
    Route::put('/admins/{id}', [AdminsController::class, 'update'])->name('admin.admins.update');
    Route::delete('/admins/{id}', [AdminsController::class, 'destroy'])->name('admin.admins.delete');
    
    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('admin.reports');
    Route::post('/reports/export', [ReportsController::class, 'export'])->name('admin.reports.export');
    Route::post('/reports/export-pdf', [ReportsController::class, 'exportActivityPdf'])->name('admin.reports.export.pdf');
    Route::post('/reports/export-excel', [ReportsController::class, 'exportActivityExcel'])->name('admin.reports.export.excel');
    Route::get('/reports/chart-data', [ReportsController::class, 'getChartDataAjax'])->name('admin.reports.chart-data');
    
    // Analytics Routes (keep full dashboard for detailed views)
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/analytics/export/csv', [AnalyticsController::class, 'exportCsv'])->name('admin.analytics.export.csv');
    Route::get('/analytics/export/pdf', [AnalyticsController::class, 'exportPdf'])->name('admin.analytics.export.pdf');

    // Company Content Management
    Route::get('/company-content', [CompanyContentController::class, 'index'])->name('admin.company-content');
    Route::post('/company-content/portfolios', [CompanyContentController::class, 'storePortfolio'])->name('admin.company-content.portfolios.store');
    Route::put('/company-content/portfolios/{id}', [CompanyContentController::class, 'updatePortfolio'])->name('admin.company-content.portfolios.update');
    Route::delete('/company-content/portfolios/{id}', [CompanyContentController::class, 'deletePortfolio'])->name('admin.company-content.portfolios.delete');
    Route::post('/company-content/banners', [CompanyContentController::class, 'storeBanner'])->name('admin.company-content.banners.store');
    Route::put('/company-content/banners/{id}', [CompanyContentController::class, 'updateBanner'])->name('admin.company-content.banners.update');
    Route::delete('/company-content/banners/{id}', [CompanyContentController::class, 'deleteBanner'])->name('admin.company-content.banners.delete');
    Route::post('/company-content/announcements', [CompanyContentController::class, 'storeAnnouncement'])->name('admin.company-content.announcements.store');
    Route::put('/company-content/announcements/{id}', [CompanyContentController::class, 'updateAnnouncement'])->name('admin.company-content.announcements.update');
    Route::post('/company-content/announcements/{id}/toggle', [CompanyContentController::class, 'toggleAnnouncementStatus'])->name('admin.company-content.announcements.toggle');
    Route::delete('/company-content/announcements/{id}', [CompanyContentController::class, 'deleteAnnouncement'])->name('admin.company-content.announcements.delete');
    
    // File Upload Handlers
    Route::post('/upload/image', [TablesController::class, 'uploadImage'])->name('admin.upload.image');
    Route::post('/upload/pdf', [TablesController::class, 'uploadPdf'])->name('admin.upload.pdf');
    
    // Career Management - Job Applications
    Route::get('/career/list-applications', [CareerController::class, 'listApplications'])->name('admin.career.list-applications');
    Route::get('/career/list-shortlisted', [CareerController::class, 'listShortlisted'])->name('admin.career.list-shortlisted');
    Route::get('/career/list-employees', [CareerController::class, 'listEmployees'])->name('admin.career.list-employees');
    Route::get('/career/list-rejected', [CareerController::class, 'listRejected'])->name('admin.career.list-rejected');
    Route::get('/career/locations', [CareerController::class, 'getLocations'])->name('admin.career.locations');
    Route::get('/career/cv/{id}', [CareerController::class, 'downloadCv'])->name('admin.career.cv');     Route::delete('/career/cv/{id}', [CareerController::class, 'deleteCv'])->name('admin.career.cv.delete');    Route::post('/career/shortlist/{id}', [CareerController::class, 'shortlist'])->name('admin.career.shortlist');
    Route::post('/career/hire/{id}', [CareerController::class, 'hire'])->name('admin.career.hire');
    Route::post('/career/reject/{id}', [CareerController::class, 'reject'])->name('admin.career.reject');
    Route::post('/career/delete-application/{id}', [CareerController::class, 'deleteApplication'])->name('admin.career.delete-application');
});
