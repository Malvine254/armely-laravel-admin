<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$blogs = DB::table('blogs')->select('author', 'title')->limit(5)->get();
foreach ($blogs as $blog) {
    echo "Blog: {$blog->title} | Author: {$blog->author}\n";
}

$team = DB::table('team')->select('team_name', 'team_image')->limit(5)->get();
foreach ($team as $member) {
    echo "Team: {$member->team_name} | Image: {$member->team_image}\n";
}
