<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(private AzureGraphMailService $mailer) {}

    /**
     * Send quote created notification to admin
     */
    public function sendQuoteCreatedNotification(Quote $quote): void
    {
        try {
            $admins = User::where('role', 'admin')
                ->where('status', 'active')
                ->get();

            foreach ($admins as $admin) {
                $this->mailer->sendQuoteCreatedAdminEmail($admin->email, $admin->name, $quote);
            }

            if ($quote->user && !empty($quote->user->email)) {
                $this->mailer->sendQuoteSubmittedCustomerEmail($quote);
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
            $admins = User::where('role', 'admin')
                ->where('status', 'active')
                ->get();

            foreach ($admins as $admin) {
                $this->mailer->sendQuoteRevisionAdminEmail($admin->email, $admin->name, $quote, $revisedFromQuoteId);
            }

            if ($quote->user && !empty($quote->user->email)) {
                $this->mailer->sendQuoteRevisionCustomerEmail($quote, $revisedFromQuoteId);
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
            $this->mailer->sendOrderConfirmationEmail($order);

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
            $this->mailer->sendOrderShippedEmail($order);

            Log::info("Order shipped notification sent to user {$order->user_id}");
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
    public function sendInvoiceReminderNotification(Invoice $invoice): void
    {
        try {
            $recipient = User::find($invoice->user_id);
            if (!$recipient || !$recipient->email) {
                Log::warning("Invoice reminder skipped: missing user/email for invoice {$invoice->invoice_number}");
                return;
            }

            $this->mailer->sendInvoiceReminderEmail($invoice, $recipient);

            Log::info("Invoice reminder notification sent to user {$invoice->user_id}");
        } catch (\Exception $e) {
            Log::error("Failed to send invoice reminder notification: " . $e->getMessage());
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
