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
        $this->line('Active watches: price alerts '.DB::table('price_alert_subscriptions')->where('is_active', true)->whereNotNull('user_id')->count()
            .', reminders '.DB::table('reminder_subscriptions')->where('is_active', true)->whereNotNull('user_id')->count().' (signed-in customers only)');
        $this->line('Customers opted out: all marketing '.DB::table('email_preferences')->where('marketing_enabled', false)->count()
            .', price alerts '.DB::table('email_preferences')->where('price_alerts_enabled', false)->count()
            .', browse reminders '.DB::table('email_preferences')->where('browse_reminders_enabled', false)->count()
            .', cart reminders '.DB::table('email_preferences')->where('cart_reminders_enabled', false)->count());

        // Unsubscribes recorded within two minutes of a marketing email are
        // almost always a mail security scanner opening the link, not a person.
        $recentUnsubscribes = DB::table('suppression_events')->where('event_type', 'unsubscribe')
            ->where('occurred_at', '>=', now()->subDays(90))->get(['user_id', 'occurred_at']);
        $likelyScanner = $recentUnsubscribes->filter(fn ($event) => DB::table('suppression_events')
            ->where('user_id', $event->user_id)->where('event_type', 'marketing_sent')
            ->whereBetween('occurred_at', [Carbon::parse($event->occurred_at)->subMinutes(2), Carbon::parse($event->occurred_at)])
            ->exists())->count();
        $this->line("Unsubscribes (90 days): {$recentUnsubscribes->count()}, of which {$likelyScanner} happened within 2 minutes of a send (likely automated link scanning; review before re-enabling)");

        $this->line('Queued jobs: '.(Schema::hasTable('jobs') ? DB::table('jobs')->count() : 'table missing'));
        $this->line('Failed jobs: '.(Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 'table missing'));
        $this->line('Graph 202 means accepted by Microsoft, not proof of inbox delivery. Check Exchange message trace for actual delivery.');

        return $healthy && $revision && $mail['configured'] ? self::SUCCESS : self::FAILURE;
    }
}
