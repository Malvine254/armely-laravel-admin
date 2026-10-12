<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Services\AzureGraphMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LifecycleDoctorCommand extends Command
{
    protected $signature = 'lifecycle:doctor';

    protected $description = 'Read-only cart and email health checks; never sends mail or prints credentials';

    public function handle(AzureGraphMailService $mailer): int
    {
        $healthy = true;
        try {
            DB::connection()->getPdo();
        } catch (\Illuminate\Database\QueryException|\PDOException $error) {
            $this->error('Database unavailable. Verify DB configuration and database service before testing carts or lifecycle mail.');

            return self::FAILURE;
        }
        foreach (['user_cart_snapshots', 'reminder_subscriptions', 'search_profiles', 'price_alert_subscriptions', 'email_preferences', 'suppression_events', 'unsubscribe_tokens', 'app_settings'] as $table) {
            $exists = Schema::hasTable($table);
            $this->line($table.': '.($exists ? 'present' : 'MISSING (run migrations)'));
            $healthy = $healthy && $exists;
        }
        if (! $healthy) {
            return self::FAILURE;
        }
        $revision = Schema::hasColumn('user_cart_snapshots', 'revision');
        $this->line('Cart stale-write protection: '.($revision ? 'ready' : 'MISSING (run migrations)'));
        $mail = $mailer->configurationHealth();
        $this->line('Graph mail: '.($mail['configured'] ? 'configured (delivery not tested)' : 'missing '.implode(', ', $mail['missing_fields'])));
        foreach (['reminders', 'price_drop'] as $campaign) {
            $lastRun = AppSetting::getValue("lifecycle.{$campaign}.last_run_at", null);
            $recent = $lastRun && abs(now()->diffInMinutes(Carbon::parse($lastRun))) <= 90;
            $this->line($campaign.' scheduler: '.($recent ? 'recent run' : 'NO recent run; check cron for store/artisan schedule:run'));
            $this->line($campaign.' metrics: '.json_encode(AppSetting::getValue("lifecycle.{$campaign}.last_metrics", [])));
            $healthy = $healthy && $recent;
        }
        $this->line('Queued jobs: '.(Schema::hasTable('jobs') ? DB::table('jobs')->count() : 'table missing'));
        $this->line('Failed jobs: '.(Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 'table missing'));
        $this->line('Graph 202 means accepted by Microsoft, not proof of inbox delivery. Check Exchange message trace for actual delivery.');

        return $healthy && $revision && $mail['configured'] ? self::SUCCESS : self::FAILURE;
    }
}
