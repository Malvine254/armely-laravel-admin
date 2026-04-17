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
        'help' => 'Show app and runtime status information.',
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
        return [
            'command' => $command . (empty($params) ? '' : ' ' . json_encode($params)),
            'status' => 'ERROR',
            'output' => $e->getMessage(),
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($selectedAction === 'status') {
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
            ]),
        ];
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
      <p>This page now includes only core Artisan commands for production rebuild and cache refresh.</p>
      <p class="notice">If frontend JS/CSS changes are missing, this tool cannot run npm builds. You still need to run your deployment build pipeline for Vite assets.</p>

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
