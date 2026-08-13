<?php

namespace App\Jobs;

use App\Models\AppSetting;
use App\Models\Invoice;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessInvoicePaymentRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function handle(NotificationService $notificationService): void
    {
        $startedAt = microtime(true);
        $now = now();
        $scanned = 0;
        $sent = 0;
        $markedOverdue = 0;

        $invoices = Invoice::query()
            ->whereNotIn('status', ['paid', 'cancelled', 'merged'])
            ->whereRaw('COALESCE(total_amount, 0) > COALESCE(paid_amount, 0)')
            ->orderBy('due_at')
            ->limit(500)
            ->get();

        foreach ($invoices as $invoice) {
            $scanned++;

            try {
                $dueAt = $invoice->due_at instanceof Carbon
                    ? $invoice->due_at->copy()
                    : null;
                $issuedAt = $invoice->issued_at instanceof Carbon
                    ? $invoice->issued_at->copy()
                    : $invoice->created_at?->copy() ?? $now->copy();

                if (!$dueAt) {
                    $dueDays = max(1, min(90, (int) round(AppSetting::getNumber('billing.invoice_due_days', 14))));
                    $dueAt = $issuedAt->copy()->addDays($dueDays);
                    $invoice->due_at = $dueAt;
                }

                if ($dueAt->isPast() && $invoice->status !== 'overdue') {
                    $invoice->status = 'overdue';
                    $markedOverdue++;
                }

                $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];
                $meta = is_array($rawData['reminder_meta'] ?? null) ? $rawData['reminder_meta'] : [];
                $stage = max(0, (int) ($meta['stage'] ?? 0));
                $lastSentAt = isset($meta['last_sent_at']) ? Carbon::parse((string) $meta['last_sent_at']) : null;

                // Cadence: spaced reminders around due date; never more than one reminder per 24h.
                $schedule = [
                    [
                        'key' => 'pre_due_2d',
                        'at' => $dueAt->copy()->subDays(2),
                        'message' => 'Friendly reminder: your invoice will be due soon.',
                    ],
                    [
                        'key' => 'due_today',
                        'at' => $dueAt->copy(),
                        'message' => 'Your invoice is due today. Please arrange payment at your earliest convenience.',
                    ],
                    [
                        'key' => 'overdue_3d',
                        'at' => $dueAt->copy()->addDays(3),
                        'message' => 'Payment reminder: this invoice is now overdue. Please complete payment as soon as possible.',
                    ],
                    [
                        'key' => 'overdue_7d',
                        'at' => $dueAt->copy()->addDays(7),
                        'message' => 'Final reminder: your invoice remains overdue. Please resolve payment to avoid disruption.',
                    ],
                ];

                if ($stage < count($schedule)) {
                    $nextReminder = $schedule[$stage];
                    $readyToSend = $now->greaterThanOrEqualTo($nextReminder['at']);
                    $outsideCooldown = !$lastSentAt || $lastSentAt->lte($now->copy()->subHours(24));

                    if ($readyToSend && $outsideCooldown) {
                        $didSend = $notificationService->sendInvoiceReminderNotification($invoice, $nextReminder['message']);
                        if ($didSend) {
                            $sent++;

                            $history = is_array($meta['history'] ?? null) ? $meta['history'] : [];
                            $history[] = [
                                'key' => $nextReminder['key'],
                                'sent_at' => $now->toISOString(),
                            ];

                            $meta['stage'] = $stage + 1;
                            $meta['last_sent_at'] = $now->toISOString();
                            $meta['history'] = array_slice($history, -10);
                        }
                    }
                }

                $rawData['reminder_meta'] = $meta;
                $invoice->raw_data = $rawData;
                $invoice->save();
            } catch (\Throwable $e) {
                Log::warning('Invoice reminder processing failed', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

        AppSetting::setValue('lifecycle.invoice_reminders.last_run_at', $now->toISOString());
        AppSetting::setValue('lifecycle.invoice_reminders.last_metrics', [
            'scanned' => $scanned,
            'sent' => $sent,
            'marked_overdue' => $markedOverdue,
            'duration_ms' => $durationMs,
        ]);

        Log::info('Processed invoice payment reminders', [
            'scanned' => $scanned,
            'sent' => $sent,
            'marked_overdue' => $markedOverdue,
            'duration_ms' => $durationMs,
        ]);
    }
}
