<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$actions = [
    'status' => [
        'label' => 'Status',
        'help' => 'Show app, runtime, cache, storage, and frontend build status information.',
        'commands' => [],
    ],
    'clear_all' => [
        'label' => 'Clear All Caches',
        'help' => 'Run all Laravel cache clear commands.',
        'commands' => [
            ['name' => 'optimize:clear', 'params' => []],
            ['name' => 'cache:clear', 'params' => []],
            ['name' => 'config:clear', 'params' => []],
            ['name' => 'route:clear', 'params' => []],
            ['name' => 'view:clear', 'params' => []],
            ['name' => 'event:clear', 'params' => []],
        ],
    ],
    'rebuild_caches' => [
        'label' => 'Rebuild Laravel Caches',
        'help' => 'Warm config, route, view, event caches and optimize.',
        'commands' => [
            ['name' => 'config:cache', 'params' => []],
            ['name' => 'route:cache', 'params' => []],
            ['name' => 'view:cache', 'params' => []],
            ['name' => 'event:cache', 'params' => []],
            ['name' => 'optimize', 'params' => []],
        ],
    ],
    'storage_link' => [
        'label' => 'Recreate Storage Link',
        'help' => 'Run storage:link for the main website.',
        'commands' => [
            ['name' => 'storage:link', 'params' => []],
        ],
    ],
    'migrate_force' => [
        'label' => 'Run Migrations (Force)',
        'help' => 'Run php artisan migrate --force in production.',
        'commands' => [
            ['name' => 'migrate', 'params' => ['--force' => true]],
        ],
    ],
    'build_frontend' => [
        'label' => 'Build Frontend Assets',
        'help' => 'Run npm run build inside the main website app.',
        'commands' => [],
    ],
    'git_pull_deploy' => [
        'label' => 'Git Pull & Deploy',
        'help' => 'Pull latest code from GitHub (origin main), then clear caches and run migrations.',
        'commands' => [],
    ],
    'test_email' => [
        'label'    => 'Send Test Email',
        'help'     => 'Send a test email via AzureMailService to verify mail is working on this server.',
        'commands' => [],
    ],
    'test_resources_api' => [
        'label' => 'Test Resources API',
        'help' => 'Call the new /api/resources endpoints and show sample JSON request and response data for admin use.',
        'commands' => [],
    ],
    'full_rebuild' => [
        'label' => 'Full Production Rebuild',
        'help' => 'Clear caches, relink storage, run migrations, then rebuild caches.',
        'commands' => [
            ['name' => 'optimize:clear', 'params' => []],
            ['name' => 'cache:clear', 'params' => []],
            ['name' => 'config:clear', 'params' => []],
            ['name' => 'route:clear', 'params' => []],
            ['name' => 'view:clear', 'params' => []],
            ['name' => 'event:clear', 'params' => []],
            ['name' => 'storage:link', 'params' => []],
            ['name' => 'migrate', 'params' => ['--force' => true]],
            ['name' => 'config:cache', 'params' => []],
            ['name' => 'route:cache', 'params' => []],
            ['name' => 'view:cache', 'params' => []],
            ['name' => 'event:cache', 'params' => []],
            ['name' => 'optimize', 'params' => []],
        ],
    ],
];

$selectedAction = isset($_POST['action']) ? (string) $_POST['action'] : 'status';
if (!array_key_exists($selectedAction, $actions)) {
    $selectedAction = 'status';
}

$results = [];

function runArtisanCommand(string $command, array $params = []): array
{
    $label = $command . (empty($params) ? '' : ' ' . json_encode($params));

    try {
        Artisan::call($command, $params);

        return [
            'command' => $label,
            'status'  => 'OK',
            'output'  => trim(Artisan::output()),
        ];
    } catch (Throwable $e) {
        $message = $e->getMessage();

        // storage:link complains if the symlink already exists — that is fine.
        if ($command === 'storage:link' && stripos($message, 'already exists') !== false) {
            return ['command' => $label, 'status' => 'OK', 'output' => $message];
        }

        // Migrations fail with "table already exists" when a table was created
        // outside of artisan. Treat as a warning, not a hard error.
        if ($command === 'migrate' && stripos($message, 'already exists') !== false) {
            return [
                'command' => $label,
                'status'  => 'WARNING',
                'output'  => 'One or more tables already existed and were skipped. ' . $message,
            ];
        }

        return ['command' => $label, 'status' => 'ERROR', 'output' => $message];
    }
}

function isWindowsHost(): bool
{
    return DIRECTORY_SEPARATOR === '\\';
}

function findAvailableBinary(array $candidates): ?string
{
    $pathDirs = array_filter(array_map('trim', explode(PATH_SEPARATOR, (string) getenv('PATH'))));

    foreach ($candidates as $candidate) {
        if ($candidate === '') {
            continue;
        }

        if (strpbrk($candidate, '\\/') !== false && is_file($candidate)) {
            return $candidate;
        }

        foreach ($pathDirs as $dir) {
            $fullPath = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . $candidate;
            if (is_file($fullPath)) {
                return $fullPath;
            }
        }
    }

    return null;
}

function runProcessCommand(string $command, string $workingDirectory): array
{
    if (!function_exists('proc_open')) {
        return [
            'ok' => false,
            'exit_code' => null,
            'stdout' => '',
            'stderr' => 'proc_open is disabled on this PHP host.',
        ];
    }

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = @proc_open($command, $descriptors, $pipes, $workingDirectory);
    if (!is_resource($process)) {
        return [
            'ok' => false,
            'exit_code' => null,
            'stdout' => '',
            'stderr' => 'Failed to start process: ' . $command,
        ];
    }

    fwrite($pipes[0], '');
    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    return [
        'ok' => $exitCode === 0,
        'exit_code' => $exitCode,
        'stdout' => trim((string) $stdout),
        'stderr' => trim((string) $stderr),
    ];
}

function getFrontendToolingStatus(): array
{
    $nodeCandidates = isWindowsHost() ? ['node.exe', 'node'] : ['node'];
    $npmCandidates = isWindowsHost() ? ['npm.cmd', 'npm.exe', 'npm'] : ['npm'];

    return [
        'node_binary' => findAvailableBinary($nodeCandidates),
        'npm_binary' => findAvailableBinary($npmCandidates),
        'node_modules_found' => is_dir(__DIR__ . '/../node_modules'),
    ];
}

function getFrontendBuildStatus(): array
{
    $manifestPath = __DIR__ . '/build/manifest.json';
    $tooling = getFrontendToolingStatus();

    if (!file_exists($manifestPath)) {
        return [
            'manifest_found' => false,
            'message' => 'public/build/manifest.json not found',
            ...$tooling,
        ];
    }

    $raw = @file_get_contents($manifestPath);
    if ($raw === false) {
        return [
            'manifest_found' => true,
            'message' => 'manifest.json exists but could not be read',
            ...$tooling,
        ];
    }

    $manifest = json_decode($raw, true);
    if (!is_array($manifest)) {
        return [
            'manifest_found' => true,
            'message' => 'manifest.json is invalid JSON',
            ...$tooling,
        ];
    }

    $entry = $manifest['resources/js/app.js'] ?? null;
    $assetFile = is_array($entry) ? ($entry['file'] ?? null) : null;
    $assetPath = is_string($assetFile) ? (__DIR__ . '/build/' . $assetFile) : null;

    return [
        'manifest_found' => true,
        'manifest_mtime' => date('Y-m-d H:i:s', (int) @filemtime($manifestPath)),
        'app_asset' => $assetFile,
        'asset_exists' => $assetPath ? file_exists($assetPath) : false,
        'asset_mtime' => ($assetPath && file_exists($assetPath)) ? date('Y-m-d H:i:s', (int) @filemtime($assetPath)) : null,
        ...$tooling,
    ];
}

function buildFrontendAssets(): array
{
    $appRoot = realpath(__DIR__ . '/..');
    if (!is_string($appRoot) || $appRoot === '') {
        return [
            'command' => 'npm run build',
            'status' => 'ERROR',
            'output' => 'Unable to resolve the main application root.',
        ];
    }

    $tooling = getFrontendToolingStatus();
    $npmBinary = $tooling['npm_binary'] ?? null;

    if (!is_string($npmBinary) || $npmBinary === '') {
        return [
            'command' => 'npm run build',
            'status' => 'ERROR',
            'output' => 'npm is not available on this host. Build locally and upload public/build, or install Node.js and npm on the server.',
        ];
    }

    if (empty($tooling['node_modules_found'])) {
        return [
            'command' => 'npm run build',
            'status' => 'ERROR',
            'output' => 'node_modules is missing in the main app root. Install dependencies first, or build locally and upload public/build.',
        ];
    }

    $versionResult = runProcessCommand('"' . $npmBinary . '" --version', $appRoot);
    if (!$versionResult['ok']) {
        return [
            'command' => 'npm --version',
            'status' => 'ERROR',
            'output' => trim(($versionResult['stderr'] ?? '') . "\n" . ($versionResult['stdout'] ?? '')),
        ];
    }

    $buildResult = runProcessCommand('"' . $npmBinary . '" run build', $appRoot);

    return [
        'command' => 'npm run build',
        'status' => $buildResult['ok'] ? 'OK' : 'ERROR',
        'output' => trim(implode("\n\n", array_filter([
            'NPM_VERSION=' . trim((string) ($versionResult['stdout'] ?? '')),
            $buildResult['stdout'] ?? '',
            $buildResult['stderr'] ?? '',
        ]))) ?: 'No output returned.',
    ];
}

function getStorageStatus(): array
{
    $storageLink = __DIR__ . '/storage';

    return [
        'storage_link_exists' => file_exists($storageLink),
        'storage_link_is_link' => is_link($storageLink),
        'storage_app_public_exists' => is_dir(__DIR__ . '/../storage/app/public'),
        'bootstrap_cache_writable' => is_writable(__DIR__ . '/../bootstrap/cache'),
        'storage_writable' => is_writable(__DIR__ . '/../storage'),
    ];
}

function runInternalJsonRequest(string $path, string $method = 'GET', array $payload = []): array
{
    $server = [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ];

    $request = \Illuminate\Http\Request::create(
        $path,
        strtoupper($method),
        [],
        [],
        [],
        $server,
        empty($payload) ? null : json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );

    $response = app()->handle($request);
    $content = (string) $response->getContent();
    $decoded = json_decode($content, true);

    return [
        'status_code' => $response->getStatusCode(),
        'content' => $content,
        'json' => is_array($decoded) ? $decoded : null,
    ];
}

function buildResourcesApiSamplePayload(): array
{
    return [
        'name' => 'Admin API Test',
        'email' => 'admin.api.test@example.com',
        'organization' => 'Armely Admin',
        'job_title' => 'Platform Administrator',
        'message' => 'Sample JSON payload from fix_storage admin test.',
    ];
}

function runResourcesApiTest(string $slug): array
{
    $baseUrl = rtrim((string) config('app.url'), '/');
    $slug = trim($slug) !== '' ? trim($slug) : 'modern-data-platform-brief';
    $samplePayload = buildResourcesApiSamplePayload();
    $routeCachePath = realpath(__DIR__ . '/../bootstrap/cache/routes-v7.php') ?: (__DIR__ . '/../bootstrap/cache/routes-v7.php');
    $routeNames = [
        'api.resources.index',
        'api.resources.show',
        'api.resources.access-links',
    ];
    $routeFlags = [];
    foreach ($routeNames as $routeName) {
        $routeFlags[] = $routeName . '=' . (Route::has($routeName) ? 'true' : 'false');
    }

    $listResponse = runInternalJsonRequest('/api/resources', 'GET');
    $detailResponse = runInternalJsonRequest('/api/resources/' . rawurlencode($slug), 'GET');
    $accessResponse = runInternalJsonRequest('/api/resources/' . rawurlencode($slug) . '/access-links', 'POST', $samplePayload);

    $allRoutesPresent = Route::has('api.resources.index') && Route::has('api.resources.show') && Route::has('api.resources.access-links');
    $diagnosticMessage = $allRoutesPresent
        ? 'Resource API routes are registered in this app instance.'
        : 'Resource API routes are NOT registered in this app instance. Deploy bootstrap/app.php and routes/api.php, then run route:clear and route:cache or Full Production Rebuild.';

    return [
        'command' => 'resources_api_test',
        'status' => ($listResponse['status_code'] === 200 && $detailResponse['status_code'] === 200 && $accessResponse['status_code'] === 200) ? 'OK' : 'ERROR',
        'output' => implode("\n\n", [
            'BASE_URL=' . $baseUrl,
            'APP_ENV=' . (string) config('app.env'),
            'ROUTE_CACHE_FILE=' . $routeCachePath,
            'ROUTE_CACHE_EXISTS=' . (file_exists($routeCachePath) ? 'true' : 'false'),
            'ROUTE_REGISTRATION=' . implode(', ', $routeFlags),
            'ROUTE_DIAGNOSTIC=' . $diagnosticMessage,
            'LIST_ENDPOINT=' . $baseUrl . '/api/resources',
            'DETAIL_ENDPOINT=' . $baseUrl . '/api/resources/' . $slug,
            'ACCESS_LINKS_ENDPOINT=' . $baseUrl . '/api/resources/' . $slug . '/access-links',
            'SAMPLE_REQUEST_JSON=' . json_encode($samplePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'LIST_STATUS=' . $listResponse['status_code'],
            'LIST_RESPONSE_JSON=' . json_encode($listResponse['json'] ?? ['raw' => $listResponse['content']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'DETAIL_STATUS=' . $detailResponse['status_code'],
            'DETAIL_RESPONSE_JSON=' . json_encode($detailResponse['json'] ?? ['raw' => $detailResponse['content']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'ACCESS_LINKS_STATUS=' . $accessResponse['status_code'],
            'ACCESS_LINKS_RESPONSE_JSON=' . json_encode($accessResponse['json'] ?? ['raw' => $accessResponse['content']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($selectedAction === 'status') {
        $build = getFrontendBuildStatus();
        $storage = getStorageStatus();

        $results[] = [
            'command' => 'status',
            'status' => 'OK',
            'output' => implode("\n", [
                'APP_NAME=' . (string) config('app.name'),
                'APP_ENV=' . (string) config('app.env'),
                'APP_DEBUG=' . (config('app.debug') ? 'true' : 'false'),
                'APP_URL=' . (string) config('app.url'),
                'PHP_VERSION=' . PHP_VERSION,
                'LARAVEL_VERSION=' . app()->version(),
                'BOOTSTRAP_CACHE_WRITABLE=' . ($storage['bootstrap_cache_writable'] ? 'true' : 'false'),
                'STORAGE_WRITABLE=' . ($storage['storage_writable'] ? 'true' : 'false'),
                'PUBLIC_STORAGE_EXISTS=' . ($storage['storage_link_exists'] ? 'true' : 'false'),
                'PUBLIC_STORAGE_IS_LINK=' . ($storage['storage_link_is_link'] ? 'true' : 'false'),
                'STORAGE_APP_PUBLIC_EXISTS=' . ($storage['storage_app_public_exists'] ? 'true' : 'false'),
                'FRONTEND_MANIFEST_FOUND=' . (($build['manifest_found'] ?? false) ? 'true' : 'false'),
                'NODE_BINARY=' . (!empty($build['node_binary']) ? (string) $build['node_binary'] : 'NOT_FOUND'),
                'NPM_BINARY=' . (!empty($build['npm_binary']) ? (string) $build['npm_binary'] : 'NOT_FOUND'),
                'NODE_MODULES_FOUND=' . (($build['node_modules_found'] ?? false) ? 'true' : 'false'),
                'FRONTEND_STATUS=' . (!empty($build['message']) ? (string) $build['message'] : 'OK'),
                'FRONTEND_MANIFEST_MTIME=' . (!empty($build['manifest_mtime']) ? (string) $build['manifest_mtime'] : 'N/A'),
                'FRONTEND_APP_ASSET=' . (!empty($build['app_asset']) ? (string) $build['app_asset'] : 'N/A'),
                'FRONTEND_APP_ASSET_EXISTS=' . (($build['asset_exists'] ?? false) ? 'true' : 'false'),
                'FRONTEND_APP_ASSET_MTIME=' . (!empty($build['asset_mtime']) ? (string) $build['asset_mtime'] : 'N/A'),
            ]),
        ];
    } elseif ($selectedAction === 'git_pull_deploy') {
        $repoRoot = realpath(__DIR__ . '/..');
        if (!is_string($repoRoot) || $repoRoot === '') {
            $results[] = ['command' => 'git pull', 'status' => 'ERROR', 'output' => 'Cannot resolve repo root path.'];
        } else {
            $gitBinary = findAvailableBinary(['git']) ?? 'git';
            $pullResult = runProcessCommand('"' . $gitBinary . '" pull origin main', $repoRoot);
            $results[] = [
                'command' => 'git pull origin main (in ' . $repoRoot . ')',
                'status' => $pullResult['ok'] ? 'OK' : 'ERROR',
                'output' => trim(($pullResult['stdout'] ?: '') . "\n" . ($pullResult['stderr'] ?: '')),
            ];

            if ($pullResult['ok']) {
                foreach ([
                    ['optimize:clear', []],
                    ['cache:clear', []],
                    ['config:clear', []],
                    ['route:clear', []],
                    ['view:clear', []],
                    ['migrate', ['--force' => true]],
                    ['config:cache', []],
                    ['route:cache', []],
                    ['optimize', []],
                ] as [$cmd, $params]) {
                    $results[] = runArtisanCommand($cmd, $params);
                }
            }
        }
    } elseif ($selectedAction === 'test_email') {
        $to      = isset($_POST['test_email_to']) ? trim((string) $_POST['test_email_to']) : '';
        $fromEnv = \App\Services\AzureMailService::outboundFromEmail();

        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $results[] = ['command' => 'test_email', 'status' => 'ERROR', 'output' => 'Please enter a valid recipient email address in the field below.'];
        } else {
            try {
                $mailer  = app(\App\Services\AzureMailService::class);
                $subject = 'Armely Mail Test — ' . date('Y-m-d H:i:s');
                $body    = '<p style="font-family:Arial,sans-serif;">This is a test email sent from <strong>fix_storage.php</strong> on <strong>' . htmlspecialchars((string) config('app.url')) . '</strong> at ' . date('Y-m-d H:i:s') . '.</p>';
                $sent    = $mailer->sendEmail($fromEnv, $to, $subject, $body);

                $results[] = [
                    'command' => 'test_email → ' . $to,
                    'status'  => $sent ? 'OK' : 'ERROR',
                    'output'  => $sent
                        ? 'Email accepted by Microsoft Graph. Check inbox/spam at ' . $to . '. FROM=' . $fromEnv
                        : 'sendEmail() returned false. Check Laravel logs (storage/logs/laravel.log) for details. FROM=' . $fromEnv,
                ];
            } catch (\Throwable $e) {
                $results[] = ['command' => 'test_email', 'status' => 'ERROR', 'output' => $e->getMessage()];
            }
        }
    } elseif ($selectedAction === 'test_resources_api') {
        $slug = isset($_POST['resource_api_slug']) ? trim((string) $_POST['resource_api_slug']) : 'modern-data-platform-brief';
        $results[] = runResourcesApiTest($slug);
    } elseif ($selectedAction === 'build_frontend') {
        $results[] = buildFrontendAssets();

        $build = getFrontendBuildStatus();
        $results[] = [
            'command' => 'frontend_status_after_build',
            'status' => 'OK',
            'output' => implode("\n", [
                'FRONTEND_MANIFEST_FOUND=' . (($build['manifest_found'] ?? false) ? 'true' : 'false'),
                'FRONTEND_MANIFEST_MTIME=' . (!empty($build['manifest_mtime']) ? (string) $build['manifest_mtime'] : 'N/A'),
                'FRONTEND_APP_ASSET=' . (!empty($build['app_asset']) ? (string) $build['app_asset'] : 'N/A'),
                'FRONTEND_APP_ASSET_EXISTS=' . (($build['asset_exists'] ?? false) ? 'true' : 'false'),
                'FRONTEND_APP_ASSET_MTIME=' . (!empty($build['asset_mtime']) ? (string) $build['asset_mtime'] : 'N/A'),
            ]),
        ];
    } else {
        foreach ((array) ($actions[$selectedAction]['commands'] ?? []) as $commandDef) {
            $commandName = (string) ($commandDef['name'] ?? '');
            $commandParams = (array) ($commandDef['params'] ?? []);
            if ($commandName !== '') {
                $results[] = runArtisanCommand($commandName, $commandParams);
            }
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Armely Main Website Rebuild Tool</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f7fb; color: #1e293b; margin: 0; }
    .wrap { max-width: 980px; margin: 32px auto; padding: 0 16px; }
    .card { background: #fff; border: 1px solid #dbe2ea; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
    h1 { margin: 0 0 12px; font-size: 22px; }
    p { margin: 0 0 12px; }
    .actions { display: grid; gap: 10px; }
    .action { border: 1px solid #dbe2ea; border-radius: 10px; padding: 12px; display: flex; gap: 10px; align-items: flex-start; }
    .action input { margin-top: 4px; }
    .action strong { display: block; margin-bottom: 4px; }
    .btn { background: #2f5597; color: #fff; border: 0; border-radius: 8px; padding: 10px 14px; cursor: pointer; font-weight: 600; }
    .btn:hover { background: #254780; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { text-align: left; border-bottom: 1px solid #e7edf4; padding: 10px; vertical-align: top; font-size: 14px; }
    .ok  { color: #0f766e; font-weight: 700; }
    .err { color: #b91c1c; font-weight: 700; }
    .warn { color: #b45309; font-weight: 700; }
    pre { white-space: pre-wrap; word-break: break-word; margin: 0; }
    .notice { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; border-radius: 10px; padding: 10px 12px; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Armely Main Website Rebuild Tool</h1>
      <p>This page runs production maintenance commands for the main website.</p>
      <p class="notice">Frontend build works only if this server has Node.js, npm, and installed node_modules for the main app. If not, build locally and upload public/build.</p>

      <form method="post">
        <div class="actions">
          <?php foreach ($actions as $key => $action): ?>
            <label class="action">
              <input type="radio" name="action" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" <?= $selectedAction === $key ? 'checked' : '' ?>>
              <span>
                <strong><?= htmlspecialchars((string) $action['label'], ENT_QUOTES, 'UTF-8') ?></strong>
                <?= htmlspecialchars((string) $action['help'], ENT_QUOTES, 'UTF-8') ?>
              </span>
            </label>
          <?php endforeach; ?>
        </div>
        <div id="test-email-field" style="display:none;margin-top:10px;">
          <label style="font-size:14px;font-weight:600;">Recipient email for test:&nbsp;
            <input type="email" name="test_email_to" placeholder="you@example.com"
              style="padding:7px 10px;border:1px solid #d1d5db;border-radius:7px;font-size:14px;width:260px;">
          </label>
        </div>
                <div id="resources-api-field" style="display:none;margin-top:10px;">
                    <label style="font-size:14px;font-weight:600;">Resource slug to test:&nbsp;
                        <input type="text" name="resource_api_slug" placeholder="modern-data-platform-brief"
                            value="<?= htmlspecialchars((string) ($_POST['resource_api_slug'] ?? 'modern-data-platform-brief'), ENT_QUOTES, 'UTF-8') ?>"
                            style="padding:7px 10px;border:1px solid #d1d5db;border-radius:7px;font-size:14px;width:320px;">
                    </label>
                    <p style="margin:8px 0 0;font-size:13px;color:#475569;">This will test <code>/api/resources</code>, <code>/api/resources/{slug}</code>, and <code>/api/resources/{slug}/access-links</code> and print sample JSON for admin reference.</p>
                </div>
        <p style="margin-top:12px;"><button class="btn" type="submit">Run Selected Action</button></p>
      </form>
      <script>
                function syncActionFields(value) {
                    document.getElementById('test-email-field').style.display = value === 'test_email' ? 'block' : 'none';
                    document.getElementById('resources-api-field').style.display = value === 'test_resources_api' ? 'block' : 'none';
                }
        document.querySelectorAll('input[name="action"]').forEach(function(r){
          r.addEventListener('change', function(){
                        syncActionFields(this.value);
          });
        });
        // Show on page load if pre-selected
        (function(){
          var sel = document.querySelector('input[name="action"]:checked');
                    if (sel) syncActionFields(sel.value);
        })();
      </script>
    </div>

    <?php if (!empty($results)): ?>
      <div class="card">
        <h1>Results</h1>
        <table>
          <thead>
            <tr>
              <th>Command</th>
              <th>Status</th>
              <th>Output</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($results as $row): ?>
              <tr>
                <td><pre><?= htmlspecialchars((string) ($row['command'] ?? ''), ENT_QUOTES, 'UTF-8') ?></pre></td>
                <td class="<?= match($row['status'] ?? '') { 'OK' => 'ok', 'WARNING' => 'warn', default => 'err' } ?>"><?= htmlspecialchars((string) ($row['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><pre><?= htmlspecialchars((string) ($row['output'] ?? ''), ENT_QUOTES, 'UTF-8') ?></pre></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
