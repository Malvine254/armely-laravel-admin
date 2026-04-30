<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$actions = [
    'status' => [
        'label' => 'Status',
        'help' => 'Show app, runtime, and frontend build status information.',
        'commands' => [],
    ],
    'git_pull_deploy' => [
        'label' => 'Git Pull & Deploy',
        'help' => 'Pull latest code from GitHub (origin main), then clear all caches and run migrations.',
        'commands' => [],
    ],
    'clear_all' => [
        'label' => 'Clear All Caches',
        'help' => 'Run all cache clear commands before rebuild.',
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
        'help' => 'Run storage:link.',
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
    'seed_menu_categories' => [
        'label' => 'Seed Menu Categories',
        'help' => 'Run CategorySeeder to populate categories menu data.',
        'commands' => [
            ['name' => 'db:seed', 'params' => ['--class' => 'CategorySeeder', '--force' => true]],
        ],
    ],
    'reindex_products' => [
        'label' => 'Re-index Products',
        'help' => 'Backfill category_segment and is_hardware for all rows, then clear all browse caches.',
        'commands' => [],
    ],
    'price_sync' => [
        'label' => 'Price & Catalog Sync',
        'help' => 'Run TD SYNNEX PriceAvailability catalog sync (updates product list from TD SYNNEX).',
        'commands' => [
            ['name' => 'tdsynnex:sync-priceavailability-products', 'params' => []],
        ],
    ],
    'run_live_price_refresh' => [
        'label' => 'Run Live Price Refresh Now',
        'help' => 'Immediately poll TD SYNNEX for latest prices and availability, and send status email. Same job the scheduler runs at your configured sync time.',
        'commands' => [
            ['name' => 'tdsynnex:refresh-live-prices', 'params' => []],
        ],
    ],
    'build_frontend' => [
        'label' => 'Build Frontend Assets',
        'help' => 'Run npm run build inside the store app to regenerate public/build assets.',
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
    try {
        Artisan::call($command, $params);

        return [
            'command' => $command . (empty($params) ? '' : ' ' . json_encode($params)),
            'status' => 'OK',
            'output' => trim(Artisan::output()),
        ];
    } catch (Throwable $e) {
        $message = $e->getMessage();

        if ($command === 'storage:link' && stripos($message, 'already exists') !== false) {
            return [
                'command' => $command . (empty($params) ? '' : ' ' . json_encode($params)),
                'status' => 'OK',
                'output' => $message,
            ];
        }

        return [
            'command' => $command . (empty($params) ? '' : ' ' . json_encode($params)),
            'status' => 'ERROR',
            'output' => $message,
        ];
    }
}

function isWindowsHost(): bool
{
    return DIRECTORY_SEPARATOR === '\\';
}

function findAvailableBinary(array $candidates): ?string
{
    $pathEnv = (string) getenv('PATH');
    $pathDirs = array_filter(array_map('trim', explode(PATH_SEPARATOR, $pathEnv)));

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

function buildFrontendAssets(): array
{
    $appRoot = realpath(__DIR__ . '/..');
    if (!is_string($appRoot) || $appRoot === '') {
        return [
            'command' => 'npm run build',
            'status' => 'ERROR',
            'output' => 'Unable to resolve the store application root.',
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
            'output' => 'node_modules is missing in the store root. Install dependencies first, or build locally and upload public/build.',
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($selectedAction === 'status') {
        $build = getFrontendBuildStatus();

        $buildLines = [
            'FRONTEND_MANIFEST_FOUND=' . (($build['manifest_found'] ?? false) ? 'true' : 'false'),
            'NODE_BINARY=' . (!empty($build['node_binary']) ? (string) $build['node_binary'] : 'NOT_FOUND'),
            'NPM_BINARY=' . (!empty($build['npm_binary']) ? (string) $build['npm_binary'] : 'NOT_FOUND'),
            'NODE_MODULES_FOUND=' . (($build['node_modules_found'] ?? false) ? 'true' : 'false'),
        ];

        if (!empty($build['message'])) {
            $buildLines[] = 'FRONTEND_STATUS=' . (string) $build['message'];
        }

        if (!empty($build['manifest_mtime'])) {
            $buildLines[] = 'FRONTEND_MANIFEST_MTIME=' . (string) $build['manifest_mtime'];
        }

        if (!empty($build['app_asset'])) {
            $buildLines[] = 'FRONTEND_APP_ASSET=' . (string) $build['app_asset'];
        }

        if (array_key_exists('asset_exists', $build)) {
            $buildLines[] = 'FRONTEND_APP_ASSET_EXISTS=' . (($build['asset_exists'] ?? false) ? 'true' : 'false');
        }

        if (!empty($build['asset_mtime'])) {
            $buildLines[] = 'FRONTEND_APP_ASSET_MTIME=' . (string) $build['asset_mtime'];
        }

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
                ...$buildLines,
            ]),
        ];
    } elseif ($selectedAction === 'git_pull_deploy') {
        // Resolve the git repo root (two levels up from store/public)
        $repoRoot = realpath(__DIR__ . '/../..');
        if (!is_string($repoRoot) || $repoRoot === '') {
            $results[] = ['command' => 'git pull', 'status' => 'ERROR', 'output' => 'Cannot resolve repo root path.'];
        } else {
            $gitCandidates = ['git'];
            $gitBinary = findAvailableBinary($gitCandidates) ?? 'git';

            $pullResult = runProcessCommand('"' . $gitBinary . '" pull origin main', $repoRoot);
            $results[] = [
                'command' => 'git pull origin main (in ' . $repoRoot . ')',
                'status'  => $pullResult['ok'] ? 'OK' : 'ERROR',
                'output'  => trim(($pullResult['stdout'] ?: '') . "\n" . ($pullResult['stderr'] ?: '')),
            ];

            if ($pullResult['ok']) {
                // After a successful pull, clear caches and run migrations
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
    } elseif ($selectedAction === 'build_frontend') {
        $results[] = buildFrontendAssets();

        $build = getFrontendBuildStatus();
        $results[] = [
            'command' => 'frontend_status_after_build',
            'status' => 'OK',
            'output' => implode("\n", [
                'FRONTEND_MANIFEST_FOUND=' . (($build['manifest_found'] ?? false) ? 'true' : 'false'),
                'NODE_BINARY=' . (!empty($build['node_binary']) ? (string) $build['node_binary'] : 'NOT_FOUND'),
                'NPM_BINARY=' . (!empty($build['npm_binary']) ? (string) $build['npm_binary'] : 'NOT_FOUND'),
                'NODE_MODULES_FOUND=' . (($build['node_modules_found'] ?? false) ? 'true' : 'false'),
                'FRONTEND_MANIFEST_MTIME=' . (!empty($build['manifest_mtime']) ? (string) $build['manifest_mtime'] : 'N/A'),
                'FRONTEND_APP_ASSET=' . (!empty($build['app_asset']) ? (string) $build['app_asset'] : 'N/A'),
                'FRONTEND_APP_ASSET_EXISTS=' . (($build['asset_exists'] ?? false) ? 'true' : 'false'),
                'FRONTEND_APP_ASSET_MTIME=' . (!empty($build['asset_mtime']) ? (string) $build['asset_mtime'] : 'N/A'),
            ]),
        ];
    } elseif ($selectedAction === 'reindex_products') {
        try {
            $job = new \App\Jobs\ReindexProductsJob(true);
            $job->handle(app(\App\Services\CatalogOperationStateService::class));
            $results[] = [
                'command' => 'reindex_products',
                'status' => 'OK',
                'output' => 'Re-index complete. category_segment and is_hardware backfilled, all browse caches cleared.',
            ];
        } catch (\Throwable $e) {
            $results[] = [
                'command' => 'reindex_products',
                'status' => 'ERROR',
                'output' => $e->getMessage(),
            ];
        }
    } else {
        foreach ((array) ($actions[$selectedAction]['commands'] ?? []) as $commandDef) {
            $commandName = (string) ($commandDef['name'] ?? '');
            $commandParams = (array) ($commandDef['params'] ?? []);
            if ($commandName === '') {
                continue;
            }

            $results[] = runArtisanCommand($commandName, $commandParams);
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Armely Production Rebuild Tool</title>
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
    .ok { color: #0f766e; font-weight: 700; }
    .err { color: #b91c1c; font-weight: 700; }
    pre { white-space: pre-wrap; word-break: break-word; margin: 0; }
    .notice { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; border-radius: 10px; padding: 10px 12px; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Armely Production Rebuild Tool</h1>
      <p>This page includes production-safe Laravel rebuild utilities plus a frontend asset build trigger.</p>
      <p class="notice">Frontend build works only if this server has Node.js, npm, and installed node_modules for the store app. If not, build locally and upload public/build.</p>

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
        <p style="margin-top:12px;"><button class="btn" type="submit">Run Selected Action</button></p>
      </form>
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
                <td class="<?= (($row['status'] ?? '') === 'OK') ? 'ok' : 'err' ?>"><?= htmlspecialchars((string) ($row['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
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
