<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$teams = DB::table('team')->select('id', 'team_name', 'team_title')->orderBy('id')->get();

echo "Team Members (in current order):\n";
echo str_repeat("=", 60) . "\n";
foreach($teams as $team) {
    echo sprintf("%3d | %-30s | %s\n", $team->id, $team->team_name, $team->team_title);
}
