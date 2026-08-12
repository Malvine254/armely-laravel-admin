<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Quote;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(private AzureGraphMailService $mailer) {}

    private function emailNotificationsEnabled(string $key, bool $default = true): bool
    {
        return filter_var((string) AppSetting::getValue($key, $default), FILTER_VALIDATE_BOOL);
    }

    private function activeAdmins()
    {
        return User::where('role', 'admin')
            ->where('status', 'active')
            ->orderBy('id')
            ->get();
    }

    /**
     * Send quote created notification to admin
     */
    public function sendQuoteCreatedNotification(Quote $quote): void
    {
        try {
            if (!$this->emailNotificationsEnabled('notifications.email.new_quotes', true)) {
                return;
            }

            $requesterEmail = strtolower(trim((string) ($quote->user->email ?? '')));
            $sentAdminEmails = [];
            foreach ($this->activeAdmins() as $admin) {
                $adminEmail = strtolower(trim((string) $admin->email));
                if ($adminEmail === '' || isset($sentAdminEmails[$adminEmail])) {
                    continue;
                }
                // Do not send "requires your review" to the same user who submitted the quote.
                if ($requesterEmail !== '' && $adminEmail === $requesterEmail) {
                    continue;
                }

                $this->mailer->sendQuoteCreatedAdminEmail($admin->email, $admin->name, $quote);
                $sentAdminEmails[$adminEmail] = true;
            }

            if ($quote->user && !empty($quote->user->email)) {
                $customerEmail = strtolower(trim((string) $quote->user->email));
                if ($customerEmail !== '' && !isset($sentAdminEmails[$customerEmail])) {
                    $this->mailer->sendQuoteSubmittedCustomerEmail($quote);
                }
            }

            Log::info("Quote created notification sent for quote {$quote->quote_id}");
        } catch (\Exception $e) {
            Log::error("Failed to send quote created notification: " . $e->getMessage());
        }
    }

    /**
     * Send quote revision submitted notification to admin and customer.
     */
    public function sendQuoteRevisionNotification(Quote $quote, string $revisedFromQuoteId): void
    {
        try {
            if (!$this->emailNotificationsEnabled('notifications.email.new_quotes', true)) {
                return;
            }

            $requesterEmail = strtolower(trim((string) ($quote->user->email ?? '')));
            $sentAdminEmails = [];
            foreach ($this->activeAdmins() as $admin) {
                $adminEmail = strtolower(trim((string) $admin->email));
                if ($adminEmail === '' || isset($sentAdminEmails[$adminEmail])) {
                    continue;
                }
                // Do not send "review revision" to the same user who submitted the revision.
                if ($requesterEmail !== '' && $adminEmail === $requesterEmail) {
                    continue;
                }

                $this->mailer->sendQuoteRevisionAdminEmail($admin->email, $admin->name, $quote, $revisedFromQuoteId);
                $sentAdminEmails[$adminEmail] = true;
            }

            if ($quote->user && !empty($quote->user->email)) {
                $customerEmail = strtolower(trim((string) $quote->user->email));
                if ($customerEmail !== '' && !isset($sentAdminEmails[$customerEmail])) {
                    $this->mailer->sendQuoteRevisionCustomerEmail($quote, $revisedFromQuoteId);
                }
            }

            Log::info("Quote revision notification sent for quote {$quote->quote_id} (source {$revisedFromQuoteId})");
        } catch (\Exception $e) {
            Log::error("Failed to send quote revision notification: " . $e->getMessage());
        }
    }

    /**
     * Send quote approved notification to customer
     */
    public function sendQuoteApprovedNotification(Quote $quote): void
    {
        try {
            $this->mailer->sendQuoteApprovedEmail($quote);

            Log::info("Quote approved notification sent to user {$quote->user_id}");
        } catch (\Exception $e) {
            Log::error("Failed to send quote approved notification: " . $e->getMessage());
        }
    }

    /**
     * Send quote rejected notification to customer
     */
    public function sendQuoteRejectedNotification(Quote $quote, string $reason = null): void
    {
        try {
            $this->mailer->sendQuoteRejectedEmail($quote, $reason);

            Log::info("Quote rejected notification sent to user {$quote->user_id}");
        } catch (\Exception $e) {
            Log::error("Failed to send quote rejected notification: " . $e->getMessage());
        }
    }

    /**
     * Send order confirmation notification to customer
     */
    public function sendOrderConfirmationNotification(Order $order): void
    {
        try {
            if (!$this->emailNotificationsEnabled('notifications.email.new_orders', true)) {
                return;
            }

            $this->mailer->sendOrderConfirmationEmail($order);
            $this->mailer->sendOrderCreatedAdminEmails($order);

            Log::info("Order confirmation notification sent to user {$order->user_id}");
        } catch (\Exception $e) {
            Log::error("Failed to send order confirmation notification: " . $e->getMessage());
        }
    }

    /**
     * Send order shipped notification to customer
     */
    public function sendOrderShippedNotification(Order $order): void
    {
        try {
            if (!$this->emailNotificationsEnabled('notifications.email.new_orders', true)) {
                return;
            }

            $status = strtolower(trim((string) ($order->status ?? '')));
            if ($status === '') {
                return;
            }

            $cacheKey = 'notify:order-status:' . (string) $order->id . ':' . $status;

            // Idempotency guard: notify each order status once per order lifecycle.
            if (!Cache::add($cacheKey, 1, now()->addDays(365))) {
                return;
            }

            $this->mailer->sendOrderShippedEmail($order);

            Log::info("Order status notification sent to user {$order->user_id} for status {$status}");
        } catch (\Exception $e) {
            Log::error("Failed to send order shipped notification: " . $e->getMessage());
        }
    }

    /**
     * Send invoice notification to customer
     */
    public function sendInvoiceNotification(Invoice $invoice): void
    {
        try {
            $this->mailer->sendInvoiceEmail($invoice);

            Log::info("Invoice notification sent to user {$invoice->user_id}");
        } catch (\Exception $e) {
            Log::error("Failed to send invoice notification: " . $e->getMessage());
        }
    }

    /**
     * Send invoice payment reminder notification to customer.
     */
    public function sendInvoiceReminderNotification(Invoice $invoice, ?string $customMessage = null): bool
    {
        try {
            $recipient = User::find($invoice->user_id);
            if (!$recipient || !$recipient->email) {
                Log::warning("Invoice reminder skipped: missing user/email for invoice {$invoice->invoice_number}");
                return false;
            }

            $sent = $this->mailer->sendInvoiceReminderEmail($invoice, $recipient, $customMessage);

            if (!$sent) {
                Log::warning("Invoice reminder notification mailer returned false for user {$invoice->user_id}");
                return false;
            }

            Log::info("Invoice reminder notification sent to user {$invoice->user_id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send invoice reminder notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send quote expiring soon notification
     */
    public function sendQuoteExpiringNotification(Quote $quote): void
    {
        try {
            if ($quote->isExpired()) {
                return;
            }

            $this->mailer->sendQuoteExpiringEmail($quote);

            Log::info("Quote expiring notification sent to user {$quote->user_id}");
        } catch (\Exception $e) {
            Log::error("Failed to send quote expiring notification: " . $e->getMessage());
        }
    }
}
