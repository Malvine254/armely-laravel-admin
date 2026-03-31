<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AzureGraphMailService
{
    public function sendActivationEmail(string $recipientEmail, string $recipientName, string $activationUrl): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $subject = 'Activate your Armely Store account';
        $safeName = e($recipientName ?: 'there');
        $safeUrl = e($activationUrl);

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Activate your account</h2>
                <p>Hello {$safeName},</p>
                <p>Thank you for registering with Armely Store. Please activate your account to continue.</p>
                <p style='margin:24px 0'>
                    <a href='{$safeUrl}' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        Activate account
                    </a>
                </p>
                <p>This activation link expires in 24 hours.</p>
                <p>If you did not request this, you can safely ignore this email.</p>
            </div>
        ";

        $text = "Hello {$recipientName},\n\n"
            . "Thank you for registering with Armely Store. Activate your account using the link below:\n"
            . "{$activationUrl}\n\n"
            . "This activation link expires in 24 hours.";

        return $this->sendEmail($recipientEmail, $subject, $html, $text);
    }

    public function sendPasswordResetEmail(string $recipientEmail, string $recipientName, string $resetUrl): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $subject = 'Reset your Armely Store password';
        $safeName = e($recipientName ?: 'there');
        $safeUrl = e($resetUrl);

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Password reset request</h2>
                <p>Hello {$safeName},</p>
                <p>We received a request to reset your Armely Store password.</p>
                <p style='margin:24px 0'>
                    <a href='{$safeUrl}' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        Reset password
                    </a>
                </p>
                <p>This reset link expires in 60 minutes.</p>
                <p>If you did not request this, you can safely ignore this email.</p>
            </div>
        ";

        $text = "Hello {$recipientName},\n\n"
            . "We received a request to reset your Armely Store password.\n"
            . "Reset link: {$resetUrl}\n\n"
            . "This reset link expires in 60 minutes.";

        return $this->sendEmail($recipientEmail, $subject, $html, $text);
    }

    public function sendQuoteCreatedAdminEmail(string $adminEmail, string $adminName, \App\Models\Quote $quote): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName  = e($adminName ?: 'Admin');
        $quoteId   = e($quote->quote_id);
        $customer  = e($quote->user->name ?? 'Unknown');
        $amount    = number_format((float) ($quote->total_amount ?? 0), 2);
        $appUrl    = config('app.url');

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>New Quote Request</h2>
                <p>Hello {$safeName},</p>
                <p>A new quote has been submitted and requires your review.</p>
                <div style='border:1px solid #d9e6f7;background:#edf3fb;border-radius:8px;padding:14px;margin:16px 0'>
                    <p style='margin:0 0 6px'><strong>Quote ID:</strong> {$quoteId}</p>
                    <p style='margin:0 0 6px'><strong>Customer:</strong> {$customer}</p>
                    <p style='margin:0'><strong>Total Amount:</strong> \${$amount}</p>
                </div>
                <p style='margin:24px 0'>
                    <a href='{$appUrl}/admin/quotes' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        Review Quote
                    </a>
                </p>
            </div>
        ";

        $text = "Hello {$adminName},\n\nA new quote {$quoteId} from {$customer} requires your review.\n{$appUrl}/admin/quotes";

        return $this->sendEmail($adminEmail, "New Quote Request: {$quoteId}", $html, $text);
    }

    public function sendQuoteSubmittedCustomerEmail(\App\Models\Quote $quote): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer = $quote->user;
        if (!$customer || !$customer->email) {
            return false;
        }

        $safeName = e($customer->name ?? 'Customer');
        $quoteId = e($quote->quote_id);
        $amount = number_format((float) ($quote->total_amount ?? 0), 2);
        $itemCount = is_countable($quote->items) ? count($quote->items) : 0;
        $status = 'Pending Review';
        $appUrl = config('app.url');

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Quote Submitted Successfully</h2>
                <p>Hello {$safeName},</p>
                <p>Thank you for your request. We have received your quote and our team will review it shortly.</p>
                <div style='border:1px solid #d9e6f7;background:#edf3fb;border-radius:8px;padding:14px;margin:16px 0'>
                    <p style='margin:0 0 6px'><strong>Quote ID:</strong> {$quoteId}</p>
                    <p style='margin:0 0 6px'><strong>Status:</strong> {$status}</p>
                    <p style='margin:0 0 6px'><strong>Items:</strong> {$itemCount}</p>
                    <p style='margin:0'><strong>Estimated Total:</strong> \${$amount}</p>
                </div>
                <p style='margin:24px 0'>
                    <a href='{$appUrl}/quotes/{$quote->id}' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        View Quote
                    </a>
                </p>
                <p>You will receive another email once your quote is approved or if additional details are needed.</p>
            </div>
        ";

        $text = "Hello {$safeName},\n\n"
            . "Your quote {$quoteId} has been submitted and is pending review.\n"
            . "Items: {$itemCount}\n"
            . "Estimated total: \${$amount}\n"
            . "View quote: {$appUrl}/quotes/{$quote->id}";

        return $this->sendEmail($customer->email, "Quote Submitted: {$quoteId}", $html, $text);
    }

    public function sendQuoteApprovedEmail(\App\Models\Quote $quote): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer  = $quote->user;
        $safeName  = e($customer->name ?? 'Customer');
        $quoteId   = e($quote->quote_id);
        $amount    = number_format((float) ($quote->total_amount ?? 0), 2);
        $items     = count($quote->items ?? []);
        $validUntil = $quote->expires_at?->format('M d, Y') ?? 'N/A';
        $appUrl    = config('app.url');

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Quote Approved</h2>
                <p>Hello {$safeName},</p>
                <p>Good news! Your quote has been approved and is ready to be converted into an order.</p>
                <div style='border:1px solid #d9e6f7;background:#edf3fb;border-radius:8px;padding:14px;margin:16px 0'>
                    <p style='margin:0 0 6px'><strong>Quote ID:</strong> {$quoteId}</p>
                    <p style='margin:0 0 6px'><strong>Total Amount:</strong> \${$amount}</p>
                    <p style='margin:0 0 6px'><strong>Items:</strong> {$items}</p>
                    <p style='margin:0'><strong>Valid Until:</strong> {$validUntil}</p>
                </div>
                <p style='margin:24px 0'>
                    <a href='{$appUrl}/quotes/{$quote->id}' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        View Quote
                    </a>
                </p>
                <p>Ready to place an order? Simply log in to your account and convert this quote to an order.</p>
            </div>
        ";

        $text = "Hello {$safeName},\n\nYour quote {$quoteId} has been approved. Total: \${$amount}.\n{$appUrl}/quotes/{$quote->id}";

        return $this->sendEmail($customer->email, "Your Quote Has Been Approved: {$quoteId}", $html, $text);
    }

    public function sendQuoteRejectedEmail(\App\Models\Quote $quote, ?string $reason): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer  = $quote->user;
        $safeName  = e($customer->name ?? 'Customer');
        $quoteId   = e($quote->quote_id);
        $safeReason = $reason ? e($reason) : 'No reason provided.';
        $appUrl    = config('app.url');

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Quote Update</h2>
                <p>Hello {$safeName},</p>
                <p>We are sorry to inform you that your quote has not been approved at this time.</p>
                <div style='border:1px solid #d9e6f7;background:#edf3fb;border-radius:8px;padding:14px;margin:16px 0'>
                    <p style='margin:0 0 6px'><strong>Quote ID:</strong> {$quoteId}</p>
                    <p style='margin:0'><strong>Reason:</strong> {$safeReason}</p>
                </div>
                <p>Please feel free to submit a new quote or contact us for assistance.</p>
                <p style='margin:24px 0'>
                    <a href='{$appUrl}/quotes' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        View Quotes
                    </a>
                </p>
            </div>
        ";

        $text = "Hello {$safeName},\n\nYour quote {$quoteId} was not approved. Reason: {$safeReason}\n{$appUrl}/quotes";

        return $this->sendEmail($customer->email, "Quote Update: {$quoteId}", $html, $text);
    }

    public function sendQuoteExpiringEmail(\App\Models\Quote $quote): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer  = $quote->user;
        $safeName  = e($customer->name ?? 'Customer');
        $quoteId   = e($quote->quote_id);
        $validUntil = $quote->expires_at?->format('M d, Y') ?? 'N/A';
        $appUrl    = config('app.url');

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Your Quote is Expiring Soon</h2>
                <p>Hello {$safeName},</p>
                <p>Your approved quote is expiring soon. Please take action before it expires.</p>
                <div style='border:1px solid #d9e6f7;background:#edf3fb;border-radius:8px;padding:14px;margin:16px 0'>
                    <p style='margin:0 0 6px'><strong>Quote ID:</strong> {$quoteId}</p>
                    <p style='margin:0'><strong>Expires:</strong> {$validUntil}</p>
                </div>
                <p style='margin:24px 0'>
                    <a href='{$appUrl}/quotes/{$quote->id}' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        View Quote
                    </a>
                </p>
            </div>
        ";

        $text = "Hello {$safeName},\n\nYour quote {$quoteId} is expiring on {$validUntil}.\n{$appUrl}/quotes/{$quote->id}";

        return $this->sendEmail($customer->email, "Quote Expiring Soon: {$quoteId}", $html, $text);
    }

    public function sendOrderConfirmationEmail(\App\Models\Order $order): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer    = $order->user;
        $safeName    = e($customer->name ?? 'Customer');
        $orderNumber = e($order->order_number);
        $amount      = number_format($order->total_amount ?? 0, 2);
        $appUrl      = config('app.url');

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Order Confirmed</h2>
                <p>Hello {$safeName},</p>
                <p>Thank you! Your order has been confirmed and is being processed.</p>
                <div style='border:1px solid #d9e6f7;background:#edf3fb;border-radius:8px;padding:14px;margin:16px 0'>
                    <p style='margin:0 0 6px'><strong>Order Number:</strong> {$orderNumber}</p>
                    <p style='margin:0'><strong>Total:</strong> \${$amount}</p>
                </div>
                <p style='margin:24px 0'>
                    <a href='{$appUrl}/orders/{$order->id}' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        View Order
                    </a>
                </p>
            </div>
        ";

        $text = "Hello {$safeName},\n\nYour order {$orderNumber} has been confirmed. Total: \${$amount}.\n{$appUrl}/orders/{$order->id}";

        return $this->sendEmail($customer->email, "Order Confirmed: {$orderNumber}", $html, $text);
    }

    public function sendOrderShippedEmail(\App\Models\Order $order): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer    = $order->user;
        $safeName    = e($customer->name ?? 'Customer');
        $orderNumber = e($order->order_number);
        $appUrl      = config('app.url');

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Your Order Has Shipped</h2>
                <p>Hello {$safeName},</p>
                <p>Great news! Your order has been shipped and is on its way to you.</p>
                <div style='border:1px solid #d9e6f7;background:#edf3fb;border-radius:8px;padding:14px;margin:16px 0'>
                    <p style='margin:0'><strong>Order Number:</strong> {$orderNumber}</p>
                </div>
                <p style='margin:24px 0'>
                    <a href='{$appUrl}/orders/{$order->id}' style='background:#2F5597;color:#ffffff;padding:10px 16px;border-radius:6px;text-decoration:none;display:inline-block'>
                        Track Order
                    </a>
                </p>
            </div>
        ";

        $text = "Hello {$safeName},\n\nYour order {$orderNumber} has shipped.\n{$appUrl}/orders/{$order->id}";

        return $this->sendEmail($customer->email, "Your Order Has Shipped: {$orderNumber}", $html, $text);
    }

    public function sendInvoiceEmail(\App\Models\Invoice $invoice): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer = $invoice->user;
        if (!$customer || !$customer->email) {
            return false;
        }

        $invNumber = e($invoice->invoice_number);
        $safeName  = e($customer->name ?? 'Customer');
        $appUrl    = config('app.url');

        $html = $this->buildFullInvoiceHtml($invoice, $customer, false);
        $text = "Hello {$safeName},\n\nYour invoice #{$invNumber} is ready for payment.\n{$appUrl}/invoices";

        return $this->sendEmail($customer->email, "Invoice #{$invNumber}", $html, $text);
    }

    public function sendInvoiceReminderEmail(\App\Models\Invoice $invoice, \App\Models\User $recipient): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $invNumber = e($invoice->invoice_number);
        $safeName  = e($recipient->name ?? 'Customer');
        $balance   = number_format(($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0), 2);
        $appUrl    = config('app.url');

        $html = $this->buildFullInvoiceHtml($invoice, $recipient, true);
        $text = "Hello {$safeName},\n\nPayment reminder: Invoice #{$invNumber} has an outstanding balance of \${$balance}.\n{$appUrl}/invoices";

        return $this->sendEmail($recipient->email, "Payment Reminder: Invoice #{$invNumber}", $html, $text);
    }

    private function buildFullInvoiceHtml(\App\Models\Invoice $invoice, \App\Models\User $user, bool $isReminder): string
    {
        $appUrl      = config('app.url');
        $invNumber   = e($invoice->invoice_number);
        $issuedAt    = $invoice->issued_at?->format('M d, Y') ?? 'N/A';
        $dueDate     = $invoice->due_at?->format('M d, Y') ?? 'On Demand';
        $totalAmt    = (float)($invoice->total_amount ?? 0);
        $taxAmt      = (float)($invoice->tax_amount ?? 0);
        $paidAmt     = (float)($invoice->paid_amount ?? 0);
        $subtotal    = number_format($totalAmt - $taxAmt, 2);
        $tax         = number_format($taxAmt, 2);
        $total       = number_format($totalAmt, 2);
        $paid        = number_format($paidAmt, 2);
        $balance     = number_format($totalAmt - $paidAmt, 2);
        $taxRate     = ($totalAmt - $taxAmt) > 0 ? round(($taxAmt / ($totalAmt - $taxAmt)) * 100) : 0;
        $statusText  = strtoupper($invoice->status ?? 'PENDING');
        $statusBg    = ($invoice->status === 'paid') ? '#d4edda' : '#fff3cd';
        $statusColor = ($invoice->status === 'paid') ? '#155724' : '#856404';
        $safeName    = e($user->name ?? 'Customer');
        $companyName = e($user->company?->name ?? '');
        $companyHtml = $companyName ? "<p style='margin:0 0 3px;font-size:13px;'>{$companyName}</p>" : '';

        // Best-effort: load related order for billing/shipping addresses
        $order = \App\Models\Order::where('order_number', $invoice->order_number)
            ->with(['billingAddress', 'shippingAddress'])
            ->first();

        $billAddr = 'Address not provided';
        if ($order && $order->billingAddress) {
            $ba = $order->billingAddress;
            $billAddr = e($ba->street_1 ?? '');
            if (!empty($ba->street_2)) $billAddr .= '<br>' . e($ba->street_2);
            $billAddr .= '<br>' . e($ba->city ?? '') . ', ' . e($ba->state ?? '') . ' ' . e($ba->postal_code ?? '');
            $billAddr .= '<br>' . e($ba->country ?? '');
            if (!empty($ba->contact_phone)) $billAddr .= '<br>' . e($ba->contact_phone);
        }

        $shipAddr = 'Address not provided';
        if ($order && $order->shippingAddress) {
            $sa = $order->shippingAddress;
            $shipAddr = e($sa->street_1 ?? '');
            if (!empty($sa->street_2)) $shipAddr .= '<br>' . e($sa->street_2);
            $shipAddr .= '<br>' . e($sa->city ?? '') . ', ' . e($sa->state ?? '') . ' ' . e($sa->postal_code ?? '');
            $shipAddr .= '<br>' . e($sa->country ?? '');
            if (!empty($sa->contact_phone)) $shipAddr .= '<br>' . e($sa->contact_phone);
        }

        // Line items rows
        $itemsRows = '';
        $items = $invoice->items;
        if ($items && is_array($items) && count($items)) {
            foreach ($items as $item) {
                $n  = e($item['product_name'] ?? $item['name'] ?? 'Product');
                $q  = (int)($item['quantity'] ?? 1);
                $up = number_format((float)($item['unit_price'] ?? 0), 2);
                $lt = number_format((float)($item['line_total'] ?? (($item['unit_price'] ?? 0) * ($item['quantity'] ?? 1))), 2);
                $td = "border:1px solid #ddd;padding:10px 12px;font-size:13px;";
                $itemsRows .= "<tr>"
                    . "<td style='{$td}'>{$n}</td>"
                    . "<td style='{$td}text-align:right;'>{$q}</td>"
                    . "<td style='{$td}text-align:right;'>\${$up}</td>"
                    . "<td style='{$td}text-align:right;font-weight:bold;'>\${$lt}</td>"
                    . "</tr>";
            }
        } else {
            $itemsRows = "<tr><td colspan='4' style='border:1px solid #ddd;padding:10px;text-align:center;font-size:13px;color:#999;'>No items found</td></tr>";
        }

        // Paid / balance rows (only shown when there is a payment)
        $paidRows = '';
        if ($paidAmt > 0) {
            $paidRows = "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>Amount Paid:</td>"
                . "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$paid}</td></tr>"
                . "<tr><td style='padding:8px 0;font-size:13px;font-weight:bold;'>Amount Due:</td>"
                . "<td style='padding:8px 0;font-size:13px;font-weight:bold;text-align:right;'>\${$balance}</td></tr>";
        }

        // Notes block
        $notesHtml = '';
        if (!empty($invoice->notes)) {
            $n = e($invoice->notes);
            $notesHtml = "<div style='clear:both;margin-top:30px;padding:15px;background:#f9f9f9;border-left:3px solid #2F5597;font-size:12px;'>"
                . "<h4 style='margin:0 0 8px;color:#333;'>Notes</h4><p style='margin:0;'>{$n}</p></div>";
        }

        // Reminder banner (only for payment reminder emails)
        $reminderBanner = '';
        if ($isReminder) {
            $reminderBanner = "<div style='background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:14px 16px;margin-bottom:24px;'>"
                . "<p style='margin:0;font-size:14px;font-weight:bold;color:#856404;'>&#9888; Payment Reminder &mdash; Outstanding balance: \${$balance}</p>"
                . "</div>";
        }

        return "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'>"
            . "<meta name='viewport' content='width=device-width,initial-scale=1.0'>"
            . "<title>Invoice #{$invNumber}</title></head>"
            . "<body style='margin:0;padding:0;font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;line-height:1.6;color:#333;background:#f8fafc;'>"
            . "<div style='max-width:720px;margin:0 auto;padding:40px 20px;'>"
            . "{$reminderBanner}"
            . "<div style='background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:40px;'>"

            // ── Header ────────────────────────────────────────────────────────
            . "<table width='100%' cellpadding='0' cellspacing='0' style='border-bottom:2px solid #2F5597;padding-bottom:20px;margin-bottom:30px;'><tr>"
            . "<td valign='top'>"
            . "<h1 style='margin:0 0 4px;color:#2F5597;font-size:26px;'>ARMELY STORE</h1>"
            . "<p style='margin:0;font-size:12px;color:#666;'>Your B2B Hardware Partner</p>"
            . "</td>"
            . "<td valign='top' align='right'>"
            . "<h2 style='margin:0 0 10px;font-size:22px;color:#333;'>INVOICE</h2>"
            . "<table cellpadding='0' cellspacing='0' align='right'>"
            . "<tr><td style='font-size:13px;font-weight:bold;padding-right:12px;color:#555;'>Invoice #:</td><td style='font-size:13px;'>{$invNumber}</td></tr>"
            . "<tr><td style='font-size:13px;font-weight:bold;padding-right:12px;color:#555;'>Date:</td><td style='font-size:13px;'>{$issuedAt}</td></tr>"
            . "<tr><td style='font-size:13px;font-weight:bold;padding-right:12px;color:#555;'>Due Date:</td><td style='font-size:13px;'>{$dueDate}</td></tr>"
            . "<tr><td style='font-size:13px;font-weight:bold;padding-right:12px;color:#555;'>Status:</td>"
            . "<td><span style='display:inline-block;padding:3px 10px;border-radius:3px;font-size:12px;font-weight:bold;background:{$statusBg};color:{$statusColor};'>{$statusText}</span></td></tr>"
            . "</table></td></tr></table>"

            // ── Bill To / Ship To ──────────────────────────────────────────────
            . "<table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom:30px;'><tr>"
            . "<td width='48%' valign='top' style='padding-right:2%;'>"
            . "<h3 style='font-size:13px;font-weight:bold;color:#333;text-transform:uppercase;border-bottom:1px solid #ddd;padding-bottom:8px;margin:0 0 12px;'>Bill To</h3>"
            . "<p style='margin:0 0 3px;font-size:13px;font-weight:bold;'>{$safeName}</p>{$companyHtml}"
            . "<p style='margin:0;font-size:13px;'>{$billAddr}</p>"
            . "</td>"
            . "<td width='48%' valign='top' style='padding-left:2%;'>"
            . "<h3 style='font-size:13px;font-weight:bold;color:#333;text-transform:uppercase;border-bottom:1px solid #ddd;padding-bottom:8px;margin:0 0 12px;'>Ship To</h3>"
            . "<p style='margin:0 0 3px;font-size:13px;font-weight:bold;'>{$safeName}</p>{$companyHtml}"
            . "<p style='margin:0;font-size:13px;'>{$shipAddr}</p>"
            . "</td></tr></table>"

            // ── Items Table ────────────────────────────────────────────────────
            . "<h3 style='font-size:14px;font-weight:bold;color:#333;text-transform:uppercase;border-bottom:1px solid #ddd;padding-bottom:8px;margin:0 0 15px;'>Invoice Items</h3>"
            . "<table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;margin-bottom:20px;'>"
            . "<thead><tr style='background:#f5f5f5;'>"
            . "<th style='border:1px solid #ddd;padding:12px;text-align:left;font-size:13px;font-weight:bold;color:#333;'>Description</th>"
            . "<th style='border:1px solid #ddd;padding:12px;text-align:right;font-size:13px;font-weight:bold;color:#333;'>Qty</th>"
            . "<th style='border:1px solid #ddd;padding:12px;text-align:right;font-size:13px;font-weight:bold;color:#333;'>Unit Price</th>"
            . "<th style='border:1px solid #ddd;padding:12px;text-align:right;font-size:13px;font-weight:bold;color:#333;'>Line Total</th>"
            . "</tr></thead>"
            . "<tbody>{$itemsRows}</tbody>"
            . "</table>"

            // ── Totals ─────────────────────────────────────────────────────────
            . "<table cellpadding='0' cellspacing='0' align='right' style='width:260px;margin-bottom:30px;'>"
            . "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>Subtotal:</td>"
            .     "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$subtotal}</td></tr>"
            . "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>Tax ({$taxRate}%):</td>"
            .     "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$tax}</td></tr>"
            . "<tr><td style='padding:12px 0;font-size:16px;font-weight:bold;color:#2F5597;border-bottom:2px solid #333;'>Total:</td>"
            .     "<td style='padding:12px 0;font-size:16px;font-weight:bold;color:#2F5597;border-bottom:2px solid #333;text-align:right;'>\${$total}</td></tr>"
            . $paidRows
            . "</table>"

            . $notesHtml

            // ── CTA & Footer ───────────────────────────────────────────────────
            . "<div style='clear:both;margin-top:30px;padding-top:20px;'>"
            . "<a href='{$appUrl}/invoices' style='display:inline-block;padding:12px 24px;background:#2F5597;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;font-size:14px;'>View &amp; Pay Invoice</a>"
            . "</div>"
            . "<div style='margin-top:50px;padding-top:20px;border-top:1px solid #ddd;text-align:center;font-size:12px;color:#999;'>"
            . "<p style='margin:0;'>Thank you for your business! | www.armely.com | support@armely.com</p>"
            . "</div>"
            . "</div></div></body></html>";
    }

    private function isConfigured(): bool
    {
        $config = config('services.azure');

        return !empty($config['tenant_id'])
            && !empty($config['client_id'])
            && !empty($config['client_secret'])
            && !empty($config['from_email']);
    }

    private function sendEmail(string $toEmail, string $subject, string $htmlBody, string $textBody): bool
    {
        try {
            $normalizedToEmail = $this->normalizeEmail($toEmail);
            if (!$this->isDeliverableEmail($normalizedToEmail)) {
                Log::warning('Azure Graph send blocked: undeliverable recipient', [
                    'to' => $normalizedToEmail,
                    'subject' => $subject,
                ]);
                return false;
            }

            $token = $this->getAccessToken();
            if (!$token) {
                return false;
            }

            $fromEmail = config('services.azure.from_email');
            $url = 'https://graph.microsoft.com/v1.0/users/' . rawurlencode($fromEmail) . '/sendMail';

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($url, [
                    'message' => [
                        'subject' => $subject,
                        'body' => [
                            'contentType' => 'HTML',
                            'content' => $htmlBody,
                        ],
                        'toRecipients' => [
                            [
                                'emailAddress' => [
                                    'address' => $normalizedToEmail,
                                ],
                            ],
                        ],
                    ],
                    'saveToSentItems' => true,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Azure Graph sendMail failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $this->markEmailAsSuppressed($normalizedToEmail);

            return false;
        } catch (\Throwable $e) {
            $this->markEmailAsSuppressed($toEmail);
            Log::warning('Azure Graph email send exception: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function isDeliverableEmail(string $email): bool
    {
        $email = $this->normalizeEmail($email);
        if ($email === '') {
            return false;
        }

        if (Cache::get($this->suppressionKey($email), false) === true) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = (string) Str::after($email, '@');
        if ($domain === '' || !str_contains($email, '@')) {
            return false;
        }

        $disposableDomains = [
            'mailinator.com',
            'tempmail.com',
            'guerrillamail.com',
            '10minutemail.com',
            'yopmail.com',
            'trashmail.com',
        ];

        if (in_array($domain, $disposableDomains, true)) {
            return false;
        }

        if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            return false;
        }

        return true;
    }

    private function markEmailAsSuppressed(string $email, int $hours = 24): void
    {
        $normalized = $this->normalizeEmail($email);
        if ($normalized === '') {
            return;
        }

        Cache::put($this->suppressionKey($normalized), true, now()->addHours($hours));
    }

    private function suppressionKey(string $email): string
    {
        return 'store_mail_suppressed:' . sha1($this->normalizeEmail($email));
    }

    private function getAccessToken(): ?string
    {
        return Cache::remember('azure_graph_mail_access_token', now()->addMinutes(50), function () {
            try {
                $tenantId = config('services.azure.tenant_id');
                $clientId = config('services.azure.client_id');
                $clientSecret = config('services.azure.client_secret');

                $url = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";

                $response = Http::asForm()
                    ->acceptJson()
                    ->post($url, [
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret,
                        'scope' => 'https://graph.microsoft.com/.default',
                        'grant_type' => 'client_credentials',
                    ]);

                if (!$response->successful()) {
                    Log::warning('Azure Graph token request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return null;
                }

                return $response->json('access_token');
            } catch (\Throwable $e) {
                Log::warning('Azure Graph token exception: ' . $e->getMessage());
                return null;
            }
        });
    }
}
