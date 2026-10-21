<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class NotificationAutoIncrementRepairTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::swap(Mockery::mock(\Illuminate\Database\Schema\Builder::class));
    }

    public function test_imported_mysql_notification_ids_are_repaired_without_replacing_records(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('mysql');
        Schema::shouldReceive('hasTable')->once()->with('notifications')->andReturn(true);
        Schema::shouldReceive('getColumns')->once()->with('notifications')->andReturn([
            ['name' => 'id', 'type_name' => 'bigint', 'auto_increment' => false],
        ]);
        Schema::shouldReceive('getIndexes')->once()->with('notifications')->andReturn([
            ['primary' => true, 'columns' => ['id']],
        ]);
        DB::shouldReceive('statement')->once()
            ->with('ALTER TABLE `notifications` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT')
            ->andReturn(true);

        $migration = require database_path('migrations/2026_10_21_000000_restore_notifications_auto_increment.php');
        $migration->up();
        $migration->down();
    }

    public function test_correct_mysql_notification_schema_is_unchanged(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('mysql');
        Schema::shouldReceive('hasTable')->once()->with('notifications')->andReturn(true);
        Schema::shouldReceive('getColumns')->once()->with('notifications')->andReturn([
            ['name' => 'id', 'type_name' => 'bigint', 'auto_increment' => true],
        ]);
        DB::shouldNotReceive('statement');

        (require database_path('migrations/2026_10_21_000000_restore_notifications_auto_increment.php'))->up();
    }

    public function test_non_numeric_ids_are_not_silently_converted(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('mysql');
        Schema::shouldReceive('hasTable')->once()->with('notifications')->andReturn(true);
        Schema::shouldReceive('getColumns')->once()->with('notifications')->andReturn([
            ['name' => 'id', 'type_name' => 'varchar', 'auto_increment' => false],
        ]);
        DB::shouldNotReceive('statement');
        $this->expectException(\RuntimeException::class);

        (require database_path('migrations/2026_10_21_000000_restore_notifications_auto_increment.php'))->up();
    }

    public function test_missing_primary_key_is_restored_alongside_auto_increment(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('mysql');
        Schema::shouldReceive('hasTable')->once()->with('notifications')->andReturn(true);
        Schema::shouldReceive('getColumns')->once()->with('notifications')->andReturn([
            ['name' => 'id', 'type_name' => 'bigint', 'auto_increment' => false],
        ]);
        Schema::shouldReceive('getIndexes')->once()->with('notifications')->andReturn([]);
        DB::shouldReceive('statement')->once()
            ->with('ALTER TABLE `notifications` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY')
            ->andReturn(true);

        (require database_path('migrations/2026_10_21_000000_restore_notifications_auto_increment.php'))->up();
    }

    public function test_non_mysql_database_is_unchanged(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('sqlite');
        DB::shouldNotReceive('statement');

        (require database_path('migrations/2026_10_21_000000_restore_notifications_auto_increment.php'))->up();
    }
}
