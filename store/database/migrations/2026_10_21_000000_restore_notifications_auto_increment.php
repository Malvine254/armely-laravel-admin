<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('notifications')) {
            return;
        }

        $column = collect(Schema::getColumns('notifications'))->firstWhere('name', 'id');
        if ($column === null || ! in_array(strtolower($column['type_name']), ['bigint', 'int', 'mediumint', 'smallint', 'tinyint'], true)) {
            throw new RuntimeException('Expected a numeric notifications.id column; review the imported schema before migrating.');
        }
        if ($column['auto_increment']) {
            return;
        }

        $primary = collect(Schema::getIndexes('notifications'))->firstWhere('primary', true);
        if ($primary !== null && $primary['columns'] !== ['id']) {
            throw new RuntimeException('Expected notifications primary key on id; review the imported schema before migrating.');
        }

        DB::statement('ALTER TABLE `notifications` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'
            .($primary === null ? ' PRIMARY KEY' : ''));
    }

    public function down(): void
    {
        // Keep the repaired ID generation when rolling back application code.
    }
};
