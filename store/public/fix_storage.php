<?php
declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$actions = [
    'status' => [
        'label' => 'Show Status',
        'commands' => [],
    ],
    'storage_link' => [
        'label' => 'Run storage:link',
        'commands' => ['storage:link'],
    ],
    'optimize_clear' => [
        'label' => 'Run optimize:clear',
        'commands' => ['optimize:clear'],
    ],
    'cache_clear' => [
        'label' => 'Run cache:clear',
        'commands' => ['cache:clear'],
    ],
    'config_clear' => [
        'label' => 'Run config:clear',
        'commands' => ['config:clear'],
    ],
    'route_clear' => [
        'label' => 'Run route:clear',
        'commands' => ['route:clear'],
    ],
    'view_clear' => [
        'label' => 'Run view:clear',
        'commands' => ['view:clear'],
    ],
    'event_clear' => [
        'label' => 'Run event:clear',
        'commands' => ['event:clear'],
    ],
    'all_clear' => [
        'label' => 'Run All Clear Commands',
        'commands' => ['optimize:clear', 'cache:clear', 'config:clear', 'route:clear', 'view:clear', 'event:clear'],
    ],
    'storage_and_clear' => [
        'label' => 'Run storage:link + all clear commands',
        'commands' => ['storage:link', 'optimize:clear', 'cache:clear', 'config:clear', 'route:clear', 'view:clear', 'event:clear'],
    ],
];

$selectedAction = isset($_POST['action']) ? (string) $_POST['action'] : 'status';
$selectedAction = array_key_exists($selectedAction, $actions) ? $selectedAction : 'status';

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($actions[$selectedAction]['commands'] as $command) {
        try {
            Artisan::call($command);
            $results[] = [
                'command' => $command,
                'status' => 'OK',
                'output' => trim(Artisan::output()),
            ];
        } catch (Throwable $e) {
            $results[] = [
                'command' => $command,
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
            <div class="actions">
                <?php foreach ($actions as $actionKey => $action): ?>
                    <form method="post">
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