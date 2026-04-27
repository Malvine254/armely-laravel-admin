<?php
require __DIR__ . '/../vendor/autoload.php';
ini_set('display_errors', '1');
error_reporting(E_ALL);
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('admin_activities')
    ->select('ip_address','country','created_at')
    ->orderByDesc('created_at')
    ->limit(20)
    ->get()
    ->toArray();

    $out = json_encode($rows, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/../storage/logs/inspect_admin_activities.json', $out);
    echo "Wrote output to storage/logs/inspect_admin_activities.json\n";
} catch (\Throwable $e) {
    $err = "Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    file_put_contents(__DIR__ . '/../storage/logs/inspect_admin_activities_error.txt', $err);
    echo "Error — details written to storage/logs/inspect_admin_activities_error.txt\n";
}
