<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$actions = [
    'status' => [
        'label' => 'Show Status',
        'type' => 'status',
    ],
    'storage_link' => [
        'label' => 'Run storage:link',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'storage:link', 'params' => []],
        ],
    ],
    'optimize_clear' => [
        'label' => 'Run optimize:clear',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'optimize:clear', 'params' => []],
        ],
    ],
    'cache_clear' => [
        'label' => 'Run cache:clear',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'cache:clear', 'params' => []],
        ],
    ],
    'config_clear' => [
        'label' => 'Run config:clear',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'config:clear', 'params' => []],
        ],
    ],
    'route_clear' => [
        'label' => 'Run route:clear',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'route:clear', 'params' => []],
        ],
    ],
    'view_clear' => [
        'label' => 'Run view:clear',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'view:clear', 'params' => []],
        ],
    ],
    'event_clear' => [
        'label' => 'Run event:clear',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'event:clear', 'params' => []],
        ],
    ],
    'all_clear' => [
        'label' => 'Run All Clear Commands',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'optimize:clear', 'params' => []],
            ['name' => 'cache:clear', 'params' => []],
            ['name' => 'config:clear', 'params' => []],
            ['name' => 'route:clear', 'params' => []],
            ['name' => 'view:clear', 'params' => []],
            ['name' => 'event:clear', 'params' => []],
        ],
    ],
    'storage_and_clear' => [
        'label' => 'Run storage:link + all clear commands',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'storage:link', 'params' => []],
            ['name' => 'optimize:clear', 'params' => []],
            ['name' => 'cache:clear', 'params' => []],
            ['name' => 'config:clear', 'params' => []],
            ['name' => 'route:clear', 'params' => []],
            ['name' => 'view:clear', 'params' => []],
            ['name' => 'event:clear', 'params' => []],
        ],
    ],
    'build_prep' => [
        'label' => 'Run Build Prep (Laravel caches)',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'config:cache', 'params' => []],
            ['name' => 'route:cache', 'params' => []],
            ['name' => 'view:cache', 'params' => []],
            ['name' => 'event:cache', 'params' => []],
            ['name' => 'optimize', 'params' => []],
        ],
    ],
    'sync_catalog' => [
        'label' => 'Sync PriceAvailability Catalog (sync)',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'tdsynnex:sync-priceavailability-products', 'params' => ['--sync' => true]],
        ],
    ],
    'sync_catalog_force' => [
        'label' => 'Force Catalog Sync (sync + force)',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'tdsynnex:sync-priceavailability-products', 'params' => ['--sync' => true, '--force' => true]],
        ],
    ],
    'sync_images' => [
        'label' => 'Sync Product Images (use Limit/Chunk below)',
        'type' => 'sync_images',
    ],
    'clear_tds_cache' => [
        'label' => 'Clear TD SYNNEX Cache',
        'type' => 'artisan',
        'commands' => [
            ['name' => 'tdsynnex:clear-cache', 'params' => []],
        ],
    ],
    'vendor_report' => [
        'label' => 'Vendor Diagnostics Report',
        'type' => 'vendor_report',
    ],
];

$selectedAction = isset($_POST['action']) ? (string) $_POST['action'] : 'status';
$selectedAction = array_key_exists($selectedAction, $actions) ? $selectedAction : 'status';

$results = [];

function toIntInRange(string $key, int $default, int $min, int $max): int
{
    $value = isset($_POST[$key]) ? (int) $_POST[$key] : $default;
    return max($min, min($max, $value));
}

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
        return [
            'command' => $command . (empty($params) ? '' : ' ' . json_encode($params)),
            'status' => 'ERROR',
            'output' => $e->getMessage(),
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionType = (string) ($actions[$selectedAction]['type'] ?? 'status');

    if ($actionType === 'artisan') {
        foreach ((array) ($actions[$selectedAction]['commands'] ?? []) as $commandDef) {
            $commandName = (string) ($commandDef['name'] ?? '');
            $commandParams = (array) ($commandDef['params'] ?? []);
            if ($commandName === '') {
                continue;
            }

            $results[] = runArtisanCommand($commandName, $commandParams);
        }
    } elseif ($actionType === 'sync_images') {
        $limit = toIntInRange('sync_limit', 100, 0, 50000);
        $chunk = toIntInRange('sync_chunk', 25, 1, 500);
        $results[] = runArtisanCommand('tdsynnex:enrich-priceavailability-images', [
            '--sync' => true,
            '--limit' => $limit,
            '--chunk' => $chunk,
        ]);
    } elseif ($actionType === 'vendor_report') {
        try {
            $rows = Product::query()
                ->where('vendor_id', 'TD SYNNEX')
                ->selectRaw("TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), 'UNKNOWN')) as manufacturer")
                ->selectRaw("COUNT(*) as total_count")
                ->selectRaw("SUM(CASE WHEN images IS NULL OR images = '[]' THEN 1 ELSE 0 END) as missing_images")
                ->groupBy('manufacturer')
                ->orderByDesc('missing_images')
                ->limit(40)
                ->get();

            $lines = [];
            foreach ($rows as $row) {
                $lines[] = sprintf(
                    '%s | total=%d | missing_images=%d',
                    (string) $row->manufacturer,
                    (int) $row->total_count,
                    (int) $row->missing_images
                );
            }

            $results[] = [
                'command' => 'vendor_diagnostics',
                'status' => 'OK',
                'output' => empty($lines) ? 'No vendor rows found.' : implode("\n", $lines),
            ];
        } catch (Throwable $e) {
            $results[] = [
                'command' => 'vendor_diagnostics',
                'status' => 'ERROR',
                'output' => $e->getMessage(),
            ];
        }
    }
}

$publicStoragePath = public_path('storage');
$storagePublicPath = storage_path('app/public');
$cachePath = base_path('bootstrap/cache');
$statusRows = [
    'App env' => (string) config('app.env'),
    'App debug' => config('app.debug') ? 'true' : 'false',
    'App URL' => (string) config('app.url'),
    'Frontend URL' => (string) env('FRONTEND_URL', ''),
    'Asset URL' => (string) config('app.asset_url'),
    'Default disk' => (string) config('filesystems.default'),
    'Public root' => public_path(),
    'Storage app/public' => $storagePublicPath,
    'public/storage exists' => file_exists($publicStoragePath) ? 'YES' : 'NO',
    'public/storage is link' => is_link($publicStoragePath) ? 'YES' : 'NO',
    'public/storage writable' => file_exists($publicStoragePath) ? (is_writable($publicStoragePath) ? 'YES' : 'NO') : 'NO',
    'storage/app/public exists' => file_exists($storagePublicPath) ? 'YES' : 'NO',
    'storage/app/public writable' => file_exists($storagePublicPath) ? (is_writable($storagePublicPath) ? 'YES' : 'NO') : 'NO',
    'bootstrap/cache writable' => file_exists($cachePath) ? (is_writable($cachePath) ? 'YES' : 'NO') : 'NO',
    'Catalog hardware only' => config('tdsynnex.catalog.hardware_only') ? 'true' : 'false',
    'Image sync current showing only' => config('tdsynnex.image_sync.current_showing_only') ? 'true' : 'false',
    'Image sync scope cap' => (string) config('tdsynnex.image_sync.scope_cap', 1000),
];

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armely Maintenance Utility</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }
        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px;
        }
        .card {
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }
        h1, h2 {
            margin-top: 0;
        }
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }
        .sync-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }
        .sync-options label {
            font-size: 13px;
            color: #4b5563;
            display: block;
            margin-bottom: 4px;
        }
        .sync-options input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px 14px;
            border: 0;
            border-radius: 10px;
            background: #1d4b8f;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }
        button:hover {
            background: #163a6f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }
        th {
            width: 260px;
            color: #4b5563;
        }
        pre {
            white-space: pre-wrap;
            word-break: break-word;
            background: #0f172a;
            color: #e2e8f0;
            padding: 14px;
            border-radius: 10px;
            overflow-x: auto;
        }
        .ok {
            color: #047857;
            font-weight: 700;
        }
        .error {
            color: #b91c1c;
            font-weight: 700;
        }
        .note {
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Armely Maintenance Utility</h1>
            <p class="note">Use this page to inspect storage status and run maintenance commands when terminal access is unavailable.</p>
        </div>

        <div class="card">
            <h2>Actions</h2>
            <div class="sync-options">
                <div>
                    <label for="sync_limit">Image Sync Limit (0 = all scoped)</label>
                    <input type="number" id="sync_limit" name="sync_limit" value="<?= h((string) ($_POST['sync_limit'] ?? '100')) ?>" form="sync-images-form" min="0" max="50000">
                </div>
                <div>
                    <label for="sync_chunk">Image Sync Chunk</label>
                    <input type="number" id="sync_chunk" name="sync_chunk" value="<?= h((string) ($_POST['sync_chunk'] ?? '25')) ?>" form="sync-images-form" min="1" max="500">
                </div>
            </div>
            <div class="actions">
                <?php foreach ($actions as $actionKey => $action): ?>
                    <form method="post" id="<?= $actionKey === 'sync_images' ? 'sync-images-form' : '' ?>">
                        <input type="hidden" name="action" value="<?= h($actionKey) ?>">
                        <button type="submit"><?= h($action['label']) ?></button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <h2>Status</h2>
            <table>
                <tbody>
                <?php foreach ($statusRows as $label => $value): ?>
                    <tr>
                        <th><?= h($label) ?></th>
                        <td><?= h((string) $value) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Results</h2>
            <?php if (!$results): ?>
                <p class="note">No command has been run yet. Click any action above.</p>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <p>
                        <strong><?= h($result['command']) ?></strong>
                        <span class="<?= $result['status'] === 'OK' ? 'ok' : 'error' ?>"><?= h($result['status']) ?></span>
                    </p>
                    <pre><?= h($result['output'] !== '' ? $result['output'] : '[no output]') ?></pre>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>