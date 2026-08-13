<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Support\FrontendUrl;

class AzureGraphMailService
{
    private array $productNameLookupCache = [];

    /**
     * Returns the frontend base URL (no trailing slash).
     * Prefers FRONTEND_URL env var so production domains never need code changes.
     */
    private function frontendUrl(): string
    {
        return FrontendUrl::base();
    }

    private function logoUrl(): string
    {
        return rtrim($this->frontendUrl(), '/') . '/images/logo/armely-store-logo.png';
    }

    public function sendTestEmail(string $recipientEmail, string $recipientName = 'Admin'): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName = e($recipientName ?: 'Admin');
        $subject = 'Azure Graph Email Test';
        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.5;color:#1f2937'>
                <h2 style='margin:0 0 12px;color:#2F5597'>Azure Graph Email Test</h2>
                <p>Hello {$safeName},</p>
                <p>This is a test message from Armely Admin Settings.</p>
                <p>If you received this email, Azure Graph configuration is working.</p>
            </div>
        ";
        $text = "Hello {$recipientName},\n\nThis is a test message from Armely Admin Settings.\nAzure Graph configuration is working.";

        return $this->sendEmail($recipientEmail, $subject, $html, $text);
    }

    private function getAzureSetting(string $settingKey, mixed $fallback = ''): mixed
    {
        try {
            $stored = AppSetting::getValue($settingKey, null);
            if ($stored !== null && $stored !== '') {
                return $stored;
            }
        } catch (\Throwable $e) {
            // Keep mail service resilient if app_settings table is temporarily unavailable.
        }

        return $fallback;
    }

    private function getAzureConfig(): array
    {
        return [
            'tenant_id' => (string) $this->getAzureSetting('integrations.azure_graph.tenant_id', config('services.azure.tenant_id', '')),
            'client_id' => (string) $this->getAzureSetting('integrations.azure_graph.client_id', config('services.azure.client_id', '')),
            'client_secret' => (string) $this->getAzureSetting('integrations.azure_graph.client_secret', config('services.azure.client_secret', '')),
            'from_email' => (string) $this->getAzureSetting('integrations.azure_graph.from_email', config('services.azure.from_email', '')),
            'from_name' => (string) $this->getAzureSetting('integrations.azure_graph.from_name', config('services.azure.from_name', config('mail.from.name', 'Armely Store'))),
            'subject_prefix' => (string) $this->getAzureSetting('integrations.azure_graph.subject_prefix', config('services.azure.subject_prefix', config('app.name', 'Armely Store'))),
        ];
    }

    public function sendActivationEmail(string $recipientEmail, string $recipientName, string $activationUrl): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $subject = 'Activate your Armely Store account';
        $safeName = e($recipientName ?: 'there');
        $html = $this->buildModernNotificationEmail(
            'Activate Your Account',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 16px;color:#475569'>Welcome to Armely Store. Please confirm your email address to activate your account and continue setup.</p>
                <div style='background:#f8fbff;border:1px solid #dbe7f7;border-radius:10px;padding:12px 14px;margin:14px 0'>
                    <p style='margin:0;font-size:13px;color:#334155'><strong>Security note:</strong> This activation link expires in 24 hours.</p>
                </div>
                <p style='margin:0;color:#64748b;font-size:13px'>If you did not request this account, you can safely ignore this message.</p>
            ",
            'Activate Account',
            $activationUrl,
            'Need help? Our support team can assist if activation fails.'
        );

        $text = "Hello {$recipientName},\n\n"
            . "Thank you for registering with Armely Store. Activate your account using the link below:\n"
            . "{$activationUrl}\n\n"
            . "This activation link expires in 24 hours.";

        return $this->sendEmail($recipientEmail, $subject, $html, $text);
    }

    public function sendCustomerInviteEmail(\App\Models\User $user, string $plainPassword): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName = e($user->name ?: 'there');
        $safeEmail = e($user->email);
        $companyName = e($user->company->name ?? '');
        $companyRow = $companyName
            ? "<tr><td style='padding:6px 0;color:#64748b'>Company</td><td style='padding:6px 0 6px 16px;font-weight:600'>{$companyName}</td></tr>"
            : '';
        $safePassword = e($plainPassword);
        $loginUrl = $this->frontendUrl() . '/login';
        $appName = e(config('app.name', 'Armely Store'));
        $subject = "Your {$appName} Account Invitation";

        $html = $this->buildModernNotificationEmail(
            'Your Customer Account Is Ready',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 16px;color:#475569'>An Armely customer account has been created for you. Use these temporary credentials to sign in.</p>
                <div style='background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:16px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:6px 0;color:#64748b'>Email</td><td style='padding:6px 0 6px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b'>Temporary Password</td><td style='padding:6px 0 6px 16px;font-weight:700;font-family:Consolas,monospace'>{$safePassword}</td></tr>
                        {$companyRow}
                    </table>
                </div>
                <div style='background:#fff8e1;border:1px solid #f6c948;border-radius:8px;padding:12px;margin-bottom:8px'>
                    <p style='margin:0;color:#92400e;font-size:13px'><strong>Action required:</strong> this password expires in 48 hours and must be changed at first sign-in.</p>
                </div>
            ",
            'Sign In',
            $loginUrl,
            'If you were not expecting this invitation, please contact support immediately.'
        );

        $text = "Hello {$user->name},\n\n"
            . "An Armely customer account has been created for you.\n"
            . "Email: {$user->email}\n"
            . "Temporary password: {$plainPassword}\n"
            . "Sign in: {$this->frontendUrl()}/login\n\n"
            . "This temporary password expires in 48 hours. You will be asked to change it after signing in.";

        return $this->sendEmail($user->email, $subject, $html, $text);
    }

    public function sendAdminInviteEmail(\App\Models\User $user, string $plainPassword): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName = e($user->name ?: 'there');
        $safeEmail = e($user->email);
        $safePassword = e($plainPassword);
        $role = e('Admin');
        $loginUrl = rtrim(config('app.url'), '/') . '/admin/login';
        $appName = e(config('app.name', 'Armely Store'));
        $subject = "Your {$appName} Admin Account Invitation";

        $html = $this->buildModernNotificationEmail(
            'Admin Access Granted',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 16px;color:#475569'>Your administrator account is ready. Use the credentials below to access the Armely Admin Portal.</p>
                <div style='background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:16px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:6px 0;color:#64748b'>Email</td><td style='padding:6px 0 6px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b'>Temporary Password</td><td style='padding:6px 0 6px 16px;font-weight:700;font-family:Consolas,monospace'>{$safePassword}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b'>Role</td><td style='padding:6px 0 6px 16px;font-weight:600'>{$role}</td></tr>
                    </table>
                </div>
                <div style='background:#fff8e1;border:1px solid #f6c948;border-radius:8px;padding:12px;margin-bottom:8px'>
                    <p style='margin:0;color:#92400e;font-size:13px'><strong>Security:</strong> this temporary password expires in 48 hours and must be changed after login.</p>
                </div>
            ",
            'Sign In To Admin Portal',
            $loginUrl,
            'Use a secure network/device when accessing admin tools.'
        );

        $text = "Hello {$user->name},\n\n"
            . "An administrator account has been created for you.\n"
            . "Email: {$user->email}\n"
            . "Temporary password: {$plainPassword}\n"
            . "Role: {$role}\n"
            . "Sign in: " . rtrim(config('app.url'), '/') . "/admin/login\n\n"
            . "This temporary password expires in 48 hours. You will be asked to change it after signing in.";

        return $this->sendEmail($user->email, $subject, $html, $text);
    }

    public function sendPasswordResetEmail(string $recipientEmail, string $recipientName, string $resetUrl): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $subject = 'Reset your Armely Store password';
        $safeName = e($recipientName ?: 'there');
        $html = $this->buildModernNotificationEmail(
            'Password Reset Request',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 16px;color:#475569'>We received a request to reset your Armely Store password.</p>
                <div style='background:#f8fbff;border:1px solid #dbe7f7;border-radius:10px;padding:12px 14px;margin:14px 0'>
                    <p style='margin:0;font-size:13px;color:#334155'><strong>Security note:</strong> this reset link expires in 60 minutes.</p>
                </div>
                <p style='margin:0;color:#64748b;font-size:13px'>If you did not request this, you can safely ignore this email.</p>
            ",
            'Reset Password',
            $resetUrl,
            'If this was not you, we recommend changing your password from account settings once signed in.'
        );

        $text = "Hello {$recipientName},\n\n"
            . "We received a request to reset your Armely Store password.\n"
            . "Reset link: {$resetUrl}\n\n"
            . "This reset link expires in 60 minutes.";

        return $this->sendEmail($recipientEmail, $subject, $html, $text);
    }

    public function sendAccountApprovedEmail(\App\Models\User $user): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName    = e($user->name ?: 'there');
        $safeEmail   = e($user->email);
        $companyName = e($user->company->name ?? '');
        $loginUrl    = $this->frontendUrl() . '/login';
        $appName     = e(config('app.name', 'Armely Store'));
        $supportEmail = e(\App\Models\AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')));
        $subject     = "Your {$appName} Account Has Been Approved";

        $companyRow = $companyName
            ? "<tr><td style='padding:4px 0;color:#64748b'>Company</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$companyName}</td></tr>"
            : '';

        $html = $this->buildModernNotificationEmail(
            'Account Approved',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 16px;color:#475569'>Great news. Your <strong>{$appName}</strong> account has been approved and is now active.</p>
                <div style='background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:16px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:4px 0;color:#64748b'>Account</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        {$companyRow}
                    </table>
                </div>
                <p style='margin:0;color:#475569'>You can now request quotes, place orders, track shipments, and manage invoices from your dashboard.</p>
            ",
            'Log In To Your Account',
            $loginUrl,
            "Questions? Contact us at {$supportEmail}.",
            '#15803d',
            'Approved',
            '#15803d'
        );

        $text = "Hello {$user->name},\n\n"
            . "Great news — your {$appName} account has been approved!\n\n"
            . "Account: {$user->email}\n"
            . ($companyName ? "Company: {$user->company->name}\n" : '')
            . "\nLog in here: {$loginUrl}\n\n"
            . "Questions? Contact {$supportEmail}";

        return $this->sendEmail($user->email, $subject, $html, $text);
    }

    public function sendAccountSuspendedEmail(\App\Models\User $user): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName    = e($user->name ?: 'there');
        $safeEmail   = e($user->email);
        $companyName = e($user->company->name ?? '');
        $appName     = e(config('app.name', 'Armely Store'));
        $supportEmail = e(\App\Models\AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')));
        $subject     = "Your {$appName} Account Has Been Suspended";

        $companyRow = $companyName
            ? "<tr><td style='padding:4px 0;color:#64748b'>Company</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$companyName}</td></tr>"
            : '';

        $html = $this->buildModernNotificationEmail(
            'Account Suspended',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 16px;color:#475569'>Your <strong>{$appName}</strong> account is currently suspended and login access is temporarily disabled.</p>
                <div style='background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:16px;margin:16px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:4px 0;color:#64748b'>Account</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        {$companyRow}
                    </table>
                </div>
                <p style='margin:0;color:#475569'>If you believe this is an error, please contact support and our team will review your account status.</p>
            ",
            'Contact Support',
            "mailto:{$supportEmail}",
            'We are available to help resolve access issues promptly.',
            '#b91c1c',
            'Suspended',
            '#b91c1c'
        );

        $text = "Hello {$user->name},\n\n"
            . "Your {$appName} account ({$user->email}) has been suspended.\n\n"
            . "If you believe this is a mistake, contact us at {$supportEmail}.";

        return $this->sendEmail($user->email, $subject, $html, $text);
    }

    public function sendAccountReactivatedEmail(\App\Models\User $user): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName    = e($user->name ?: 'there');
        $safeEmail   = e($user->email);
        $companyName = e($user->company->name ?? '');
        $loginUrl    = $this->frontendUrl() . '/login';
        $appName     = e(config('app.name', 'Armely Store'));
        $supportEmail = e(\App\Models\AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')));
        $subject     = "Your {$appName} Account Has Been Reactivated";

        $companyRow = $companyName
            ? "<tr><td style='padding:4px 0;color:#64748b'>Company</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$companyName}</td></tr>"
            : '';

        $html = $this->buildModernNotificationEmail(
            'Account Reactivated',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 16px;color:#475569'>Your <strong>{$appName}</strong> account has been reactivated and access has been restored.</p>
                <div style='background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:16px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:4px 0;color:#64748b'>Account</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        {$companyRow}
                    </table>
                </div>
                <p style='margin:0;color:#475569'>You can sign in now and continue with quotes, orders, and invoice management.</p>
            ",
            'Log In To Your Account',
            $loginUrl,
            "Questions? Contact us at {$supportEmail}.",
            '#15803d',
            'Reactivated',
            '#15803d'
        );

        $text = "Hello {$user->name},\n\n"
            . "Your {$appName} account ({$user->email}) has been reactivated. You can log in here: {$loginUrl}\n\n"
            . "Questions? Contact {$supportEmail}";

        return $this->sendEmail($user->email, $subject, $html, $text);
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
        $appUrl    = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Quote ID', 'value' => $quoteId],
            ['label' => 'Customer', 'value' => $customer],
            ['label' => 'Estimated Total', 'value' => '$' . $amount],
        ]);
        $itemsHtml = $this->buildQuoteItemsTable($quote->items, $quote->total_amount);

        $html = $this->buildModernNotificationEmail(
            'New Quote Request',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>A new quote has been submitted and requires your review.</p>
                {$summaryHtml}
                {$itemsHtml}
            ",
            'Review Quote',
            $appUrl . '/admin/quotes',
            'Please review this quote as soon as possible to keep response times fast.'
        );

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
        $appUrl = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Quote ID', 'value' => $quoteId],
            ['label' => 'Status', 'value' => $status],
            ['label' => 'Items', 'value' => (string) $itemCount],
            ['label' => 'Estimated Total', 'value' => '$' . $amount],
        ]);
        $itemsHtml = $this->buildQuoteItemsTable($quote->items, $quote->total_amount);

        $html = $this->buildModernNotificationEmail(
            'Quote Submitted Successfully',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>Thank you for your request. We have received your quote and our team will review it shortly.</p>
                {$summaryHtml}
                {$itemsHtml}
            ",
            'View Quote',
            $appUrl . '/quotes/' . $quote->id,
            'You will receive another email once your quote is approved or if additional details are needed.'
        );

        $text = "Hello {$safeName},\n\n"
            . "Your quote {$quoteId} has been submitted and is pending review.\n"
            . "Items: {$itemCount}\n"
            . "Estimated total: \${$amount}\n"
            . "View quote: {$appUrl}/quotes/{$quote->id}";

        return $this->sendEmail($customer->email, "Quote Submitted: {$quoteId}", $html, $text);
    }

    public function sendQuoteRevisionAdminEmail(string $adminEmail, string $adminName, \App\Models\Quote $quote, string $revisedFromQuoteId): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName = e($adminName ?: 'Admin');
        $quoteId = e($quote->quote_id);
        $sourceQuoteId = e($revisedFromQuoteId);
        $customer = e($quote->user->name ?? 'Unknown');
        $amount = number_format((float) ($quote->total_amount ?? 0), 2);
        $itemCount = is_countable($quote->items) ? count($quote->items) : 0;
        $appUrl = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'New Quote ID', 'value' => $quoteId],
            ['label' => 'Revised From', 'value' => $sourceQuoteId],
            ['label' => 'Customer', 'value' => $customer],
            ['label' => 'Items', 'value' => (string) $itemCount],
            ['label' => 'Estimated Total', 'value' => '$' . $amount],
        ]);
        $itemsHtml = $this->buildQuoteItemsTable($quote->items, $quote->total_amount);

        $html = $this->buildModernNotificationEmail(
            'Quote Revision Request',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>A customer submitted a revision request for an existing quote.</p>
                {$summaryHtml}
                {$itemsHtml}
            ",
            'Review Revision',
            $appUrl . '/admin/quotes',
            'Please review this revision and update the quote decision when ready.'
        );

        $text = "Hello {$adminName},\n\n"
            . "A quote revision was submitted.\n"
            . "New quote: {$quote->quote_id}\n"
            . "Revised from: {$revisedFromQuoteId}\n"
            . "Customer: {$customer}\n"
            . "Review: {$appUrl}/admin/quotes";

        return $this->sendEmail($adminEmail, "Quote Revision Request: {$quoteId}", $html, $text);
    }

    public function sendQuoteRevisionCustomerEmail(\App\Models\Quote $quote, string $revisedFromQuoteId): bool
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
        $sourceQuoteId = e($revisedFromQuoteId);
        $amount = number_format((float) ($quote->total_amount ?? 0), 2);
        $itemCount = is_countable($quote->items) ? count($quote->items) : 0;
        $status = 'Pending Review';
        $appUrl = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'New Quote ID', 'value' => $quoteId],
            ['label' => 'Revised From', 'value' => $sourceQuoteId],
            ['label' => 'Status', 'value' => $status],
            ['label' => 'Items', 'value' => (string) $itemCount],
            ['label' => 'Estimated Total', 'value' => '$' . $amount],
        ]);
        $itemsHtml = $this->buildQuoteItemsTable($quote->items, $quote->total_amount);

        $html = $this->buildModernNotificationEmail(
            'Quote Revision Submitted',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>Your quote revision has been submitted successfully and is now under review.</p>
                {$summaryHtml}
                {$itemsHtml}
            ",
            'View Quotes',
            $appUrl . '/quotes',
            'You will receive another email once your revised quote is reviewed.'
        );

        $text = "Hello {$safeName},\n\n"
            . "Your quote revision has been submitted.\n"
            . "New quote: {$quote->quote_id}\n"
            . "Revised from: {$revisedFromQuoteId}\n"
            . "Items: {$itemCount}\n"
            . "Estimated total: \${$amount}\n"
            . "View quotes: {$appUrl}/quotes";

        return $this->sendEmail($customer->email, "Quote Revision Submitted: {$quoteId}", $html, $text);
    }

    public function sendQuoteApprovedEmail(\App\Models\Quote $quote): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer   = $quote->user;
        $safeName   = e($customer->name ?? 'Customer');
        $quoteId    = e($quote->quote_id);
        $amount     = number_format((float)($quote->total_amount ?? 0), 2);
        $itemCount  = count($quote->items ?? []);
        $validUntil = $quote->expires_at?->format('M d, Y') ?? 'N/A';
        $appUrl     = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Quote ID',     'value' => $quoteId],
            ['label' => 'Total Amount', 'value' => '$' . $amount],
            ['label' => 'Items',        'value' => (string) $itemCount],
            ['label' => 'Valid Until',  'value' => $validUntil],
        ]);
        $itemsHtml = $this->buildQuoteItemsTable($quote->items, $quote->total_amount);

        $html = $this->buildModernNotificationEmail(
            'Quote Approved',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>Good news! Your quote has been approved and is ready to be converted into an order.</p>
                {$summaryHtml}
                {$itemsHtml}
                <p style='margin:18px 0 0;color:#4b5563'>Ready to place an order? Log in and convert this quote to an order directly from your account.</p>
            ",
            'View Quote',
            $appUrl . '/quotes/' . $quote->id,
            'This quote is valid until ' . $validUntil . '. Please place your order before it expires.',
            '#15803d',
            'Approved',
            '#15803d'
        );

        $text = "Hello {$safeName},\n\nYour quote {$quoteId} has been approved. Total: \${$amount}.\nValid until: {$validUntil}\n{$appUrl}/quotes/{$quote->id}";

        return $this->sendEmail($customer->email, "Your Quote Has Been Approved: {$quoteId}", $html, $text);
    }

    public function sendQuoteRejectedEmail(\App\Models\Quote $quote, ?string $reason): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer   = $quote->user;
        $safeName   = e($customer->name ?? 'Customer');
        $quoteId    = e($quote->quote_id);
        $safeReason = $reason ? e($reason) : 'No reason provided.';
        $appUrl     = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Quote ID', 'value' => $quoteId],
            ['label' => 'Reason',   'value' => $safeReason],
        ]);

        $html = $this->buildModernNotificationEmail(
            'Quote Not Approved',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>We are sorry to inform you that your quote could not be approved at this time.</p>
                {$summaryHtml}
                <p style='margin:18px 0 0;color:#4b5563'>Please feel free to submit a new quote or contact us for assistance.</p>
            ",
            'Submit New Quote',
            $appUrl . '/quotes/create',
            'Our team is happy to work with you to find the right solution.',
            '#b91c1c',
            'Not Approved',
            '#b91c1c'
        );

        $text = "Hello {$safeName},\n\nYour quote {$quoteId} was not approved. Reason: {$safeReason}\n{$appUrl}/quotes";

        return $this->sendEmail($customer->email, "Quote Update: {$quoteId}", $html, $text);
    }

    public function sendQuoteExpiringEmail(\App\Models\Quote $quote): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer   = $quote->user;
        $safeName   = e($customer->name ?? 'Customer');
        $quoteId    = e($quote->quote_id);
        $amount     = number_format((float)($quote->total_amount ?? 0), 2);
        $validUntil = $quote->expires_at?->format('M d, Y') ?? 'N/A';
        $appUrl     = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Quote ID',     'value' => $quoteId],
            ['label' => 'Total Amount', 'value' => '$' . $amount],
            ['label' => 'Expires On',   'value' => $validUntil],
        ]);

        $html = $this->buildModernNotificationEmail(
            'Quote Expiring Soon',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>Your approved quote is expiring soon. Please convert it to an order before it expires.</p>
                {$summaryHtml}
            ",
            'Place Order Now',
            $appUrl . '/quotes/' . $quote->id,
            'This quote expires on ' . $validUntil . '. Act now to secure your pricing.',
            '#b45309',
            'Expiring Soon',
            '#b45309'
        );

        $text = "Hello {$safeName},\n\nYour quote {$quoteId} is expiring on {$validUntil}. Total: \${$amount}\n{$appUrl}/quotes/{$quote->id}";

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
        $amount      = number_format((float)($order->total_amount ?? 0), 2);
        $itemCount   = is_countable($order->items ?? []) ? count($order->items ?? []) : 0;
        $appUrl      = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Order Number', 'value' => $orderNumber],
            ['label' => 'Items',        'value' => (string) $itemCount],
            ['label' => 'Total',        'value' => '$' . $amount],
        ]);

        $html = $this->buildModernNotificationEmail(
            'Order Confirmed',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>Thank you! Your order has been confirmed and is now being processed.</p>
                {$summaryHtml}
                <p style='margin:18px 0 0;color:#4b5563'>We will send you another email once your order has shipped.</p>
            ",
            'View Order',
            $appUrl . '/orders/' . $order->id,
            'Thank you for choosing Armely Store.',
            '#15803d',
            'Order Placed',
            '#15803d'
        );

        $text = "Hello {$safeName},\n\nYour order {$orderNumber} has been confirmed. Total: \${$amount}.\n{$appUrl}/orders/{$order->id}";

        return $this->sendEmail($customer->email, "Order Confirmed: {$orderNumber}", $html, $text);
    }

    public function sendOrderCreatedAdminEmails(\App\Models\Order $order): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer    = $order->user;
        $customerName = e($customer->name ?? 'Customer');
        $orderNumber = e($order->order_number);
        $amount      = number_format((float)($order->total_amount ?? 0), 2);
        $itemCount   = is_countable($order->items ?? []) ? count($order->items ?? []) : 0;
        $appUrl      = $this->frontendUrl();

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Order Number', 'value' => $orderNumber],
            ['label' => 'Customer',     'value' => $customerName],
            ['label' => 'Items',        'value' => (string) $itemCount],
            ['label' => 'Total',        'value' => '$' . $amount],
        ]);

        $html = $this->buildModernNotificationEmail(
            'New Order Placed',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>A new order has been confirmed on Armely Store.</p>
                {$summaryHtml}
            ",
            'View Orders',
            $appUrl . '/admin/orders',
            'This notice was sent to all active admins.',
            '#15803d',
            'Order Placed',
            '#15803d'
        );

        $text = "A new order has been confirmed.\n\nOrder: {$orderNumber}\nCustomer: {$customerName}\nItems: {$itemCount}\nTotal: \${$amount}\n{$appUrl}/admin/orders";
        $sent = false;

        foreach ($this->activeAdminEmails() as $adminEmail) {
            $sent = $this->sendEmail($adminEmail, "New Order Placed: {$orderNumber}", $html, $text) || $sent;
        }

        return $sent;
    }

    public function sendOrderShippedEmail(\App\Models\Order $order): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $customer    = $order->user;
        $safeName    = e($customer->name ?? 'Customer');
        $orderNumber = e($order->order_number);
        $appUrl      = $this->frontendUrl();
        $status      = strtolower(trim((string) ($order->status ?? 'shipped')));
        $tracking    = is_array($order->tracking_info) ? $order->tracking_info : [];

        $items = is_array($order->items) ? $order->items : [];
        $primaryItem = 'your item';
        if (!empty($items) && is_array($items[0])) {
            $primaryItem = $this->resolveLineItemName($items[0], 'your item');
        }
        $additionalCount = max(count($items) - 1, 0);
        $itemSummary = $additionalCount > 0
            ? e($primaryItem . ' +' . $additionalCount . ' more')
            : e($primaryItem);

        $trackingNumber = e((string) ($tracking['tracking_number'] ?? ''));
        $trackingUrl = e((string) ($tracking['carrier_tracking_url'] ?? ($tracking['tracking_url'] ?? '')));

        $title = match ($status) {
            'pending' => 'Your Order Has Been Placed',
            'accepted' => 'Your Order Was Accepted',
            'backordered' => 'Your Order Is Backordered',
            'invoiced' => 'Your Order Is Invoiced',
            'delivered' => 'Your Order Was Delivered',
            'in_transit' => 'Your Order Is In Transit',
            default => 'Your Order Has Shipped',
        };

        $intro = match ($status) {
            'pending' => 'Your order has been placed and is waiting for supplier acknowledgment.',
            'accepted' => 'Your supplier has accepted the order and fulfillment is now in progress.',
            'backordered' => 'Your order is currently backordered. We will keep you updated as stock changes.',
            'invoiced' => 'Your order has been invoiced and is now in fulfillment with our logistics partners.',
            'delivered' => 'Your shipment has been delivered. Thank you for choosing Armely Store.',
            'in_transit' => 'Your order is in transit and on its way to your delivery address.',
            default => 'Great news! Your order has shipped and is now on its way.',
        };

        $badgeLabel = match ($status) {
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'backordered' => 'Backordered',
            'invoiced' => 'Invoiced',
            'delivered' => 'Delivered',
            'in_transit' => 'In Transit',
            default => 'Shipped',
        };

        $trackingHtml = '';
        if ($trackingNumber !== '') {
            $trackingHtml = "<p style='margin:6px 0 0'><strong>Tracking #:</strong> {$trackingNumber}</p>";
        }
        if ($trackingUrl !== '') {
            $trackingHtml .= "<p style='margin:10px 0 0'><a href='{$trackingUrl}' style='color:#2F5597;text-decoration:none'>View carrier tracking</a></p>";
        }

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Order Number', 'value' => $orderNumber],
            ['label' => 'Status', 'value' => $badgeLabel],
            ['label' => 'Item', 'value' => $itemSummary],
        ]);

        $html = $this->buildModernNotificationEmail(
            $title,
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>{$intro}</p>
                {$summaryHtml}
                <div style='margin:16px 0;padding:14px 16px;border:1px solid #dbe5f5;background:#f8fbff;border-radius:10px;color:#334155;font-size:14px'>
                    {$trackingHtml}
                </div>
            ",
            'View Order Details',
            $appUrl . '/orders/' . $order->id,
            'This notification is generated from live order status updates.',
            '#2F5597',
            $badgeLabel,
            '#2F5597'
        );

        $text = "Hello {$safeName},\n\n{$title}\nOrder: {$orderNumber}\nStatus: {$badgeLabel}\nItem: {$itemSummary}";
        if ($trackingNumber !== '') {
            $text .= "\nTracking #: {$trackingNumber}";
        }
        if ($trackingUrl !== '') {
            $text .= "\nCarrier Tracking: {$trackingUrl}";
        }
        $text .= "\nOrder Details: {$appUrl}/orders/{$order->id}";

        return $this->sendEmail($customer->email, "{$title}: {$orderNumber}", $html, $text);
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
        $appUrl    = $this->frontendUrl();

        $html = $this->buildFullInvoiceHtml($invoice, $customer, false);
        $text = "Hello {$safeName},\n\nYour invoice #{$invNumber} is available for your records. As a B2B account, payment is expected after delivery based on your agreed terms.\n{$appUrl}/invoices";

        return $this->sendEmail($customer->email, "Invoice #{$invNumber}", $html, $text);
    }

    public function sendInvoiceReminderEmail(\App\Models\Invoice $invoice, \App\Models\User $recipient, ?string $customMessage = null): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $invNumber = e($invoice->invoice_number);
        $safeName  = e($recipient->name ?? 'Customer');
        $balance   = number_format(($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0), 2);
        $appUrl    = $this->frontendUrl();

        $safeCustomMessage = trim((string) $customMessage);
        $html = $this->buildFullInvoiceHtml($invoice, $recipient, true, $safeCustomMessage !== '' ? $safeCustomMessage : null);
        $text = "Hello {$safeName},\n\nPayment reminder: Invoice #{$invNumber} has an outstanding balance of \${$balance}.";
        if ($safeCustomMessage !== '') {
            $text .= "\n\nAdditional message from your account manager:\n{$safeCustomMessage}";
        }
        $text .= "\n{$appUrl}/invoices";

        return $this->sendEmail($recipient->email, "Payment Reminder: Invoice #{$invNumber}", $html, $text);
    }

    private function buildModernNotificationEmail(
        string $title,
        string $contentHtml,
        string $buttonLabel,
        string $buttonUrl,
        ?string $footerNote = null,
        string $accentColor = '#2f5597',
        ?string $badgeLabel = null,
        ?string $badgeColor = null
    ): string {
        $safeTitle       = e($title);
        $safeButtonLabel = e($buttonLabel);
        $safeButtonUrl   = e($buttonUrl);
        $safeLogoUrl     = e($this->logoUrl());
        $safeFooterNote  = $footerNote ? e($footerNote) : '';
        $year            = date('Y');
        $supportEmail    = e((string) AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')));

        $footerNoteHtml = $footerNote
            ? "<p style='margin:0 0 12px;color:#64748b;font-size:13px;line-height:1.6'>{$safeFooterNote}</p>"
            : '';

        $badgeHtml = $badgeLabel
            ? "<span style='display:inline-block;margin-bottom:10px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;background:" . e($badgeColor ?? $accentColor) . ";color:#fff'>" . e($badgeLabel) . "</span>"
            : '';

        return "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1.0'><title>{$safeTitle}</title></head>
<body style='margin:0;padding:0;background:#eef3fa;font-family:\"Segoe UI\",Arial,sans-serif'>
<div style='max-width:680px;margin:0 auto;padding:32px 16px 48px'>

  <!-- Logo bar -->
  <div style='text-align:center;margin-bottom:20px'>
        <img src='{$safeLogoUrl}' alt='Armely Store' style='max-width:190px;height:auto;display:inline-block'>
  </div>

  <!-- Card -->
    <div style='background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 28px rgba(15,47,99,0.12);border:1px solid #dbe7f7'>

    <!-- Header band -->
    <div style='background:linear-gradient(135deg,#0f2f63 0%,#2f5597 100%);padding:28px 32px 24px'>
      {$badgeHtml}
            <p style='margin:0 0 8px;font-size:11px;color:#dbe7ff;letter-spacing:0.08em;text-transform:uppercase;font-weight:700'>Armely Store Notifications</p>
      <h1 style='margin:0;color:#ffffff;font-size:26px;font-weight:700;line-height:1.2'>{$safeTitle}</h1>
    </div>

    <!-- Body -->
    <div style='padding:28px 32px'>
      {$contentHtml}

            <div style='margin-top:24px;padding-top:20px;border-top:1px solid #e8eef8'>
        <a href='{$safeButtonUrl}'
                     style='display:inline-block;padding:13px 28px;background:" . e($accentColor) . ";color:#ffffff;text-decoration:none;border-radius:8px;font-weight:700;font-size:14px;letter-spacing:0.02em;box-shadow:0 8px 16px rgba(15,47,99,0.16)'>
          {$safeButtonLabel} &rarr;
        </a>
      </div>
    </div>

    <!-- Footer note -->
    <div style='padding:16px 32px 20px;background:#f8fbff;border-top:1px solid #e3ebf8'>
      {$footerNoteHtml}
            <p style='margin:0;font-size:12px;color:#94a3b8'>You received this email because your account is subscribed to operational account notifications.
        &nbsp;&bull;&nbsp; <a href='mailto:{$supportEmail}' style='color:#2f5597;text-decoration:none'>{$supportEmail}</a>
      </p>
    </div>
  </div>

  <!-- Bottom -->
  <p style='text-align:center;margin-top:20px;font-size:11px;color:#94a3b8'>&copy; {$year} Armely Store. All rights reserved.</p>
</div>
</body></html>";
    }

    private function buildQuoteSummaryCard(array $rows): string
    {
        $rowsHtml = '';
        $lastIndex = count($rows) - 1;

        foreach ($rows as $index => $row) {
            $label = e((string) ($row['label'] ?? ''));
            $value = e((string) ($row['value'] ?? ''));
            $border = $index === $lastIndex ? 'none' : '1px solid #e5ebf6';

            $rowsHtml .= "
                <div style='display:block;padding:10px 0;border-bottom:{$border}'>
                    <p style='margin:0 0 4px;font-size:12px;letter-spacing:0.03em;text-transform:uppercase;color:#6b7280'>{$label}</p>
                    <p style='margin:0;font-size:18px;color:#111827;font-weight:600'>{$value}</p>
                </div>
            ";
        }

        return "
            <div style='border:1px solid #d8e4f6;background:#f4f8ff;border-radius:12px;padding:12px 16px;margin:0 0 8px'>
                {$rowsHtml}
            </div>
        ";
    }

    private function hasAnyLinePricing(array $items, array $unitKeys, array $lineKeys): bool
    {
        foreach ($items as $item) {
            $line = is_array($item) ? $item : [];

            foreach ($unitKeys as $key) {
                if ((float) ($line[$key] ?? 0) > 0) {
                    return true;
                }
            }

            foreach ($lineKeys as $key) {
                if ((float) ($line[$key] ?? 0) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    private function moneyOrUnavailable(float $amount): string
    {
        return $amount > 0
            ? ('$' . number_format($amount, 2))
            : 'Unavailable';
    }

    private function buildQuoteItemsTable($items, $quoteTotal = null): string
    {
        $normalizedItems = is_array($items) ? $items : [];
        if (!count($normalizedItems)) {
            return "
                <div style='margin:14px 0 10px;border:1px solid #d8e4f6;border-radius:12px;overflow:hidden;background:#ffffff'>
                    <div style='padding:12px 14px;background:#f4f8ff;border-bottom:1px solid #d8e4f6'>
                        <p style='margin:0;font-size:13px;font-weight:700;color:#1e3a6e'>Submitted Items</p>
                    </div>
                    <p style='margin:0;padding:14px;color:#6b7280;font-size:13px'>No item details were attached to this quote.</p>
                </div>
            ";
        }

        $hasAnyPrices = $this->hasAnyLinePricing(
            $normalizedItems,
            ['unitPrice', 'unit_price'],
            ['lineTotal', 'line_total']
        );

        $qtotal = (float)($quoteTotal ?? 0);

        $rows = '';
        $runningTotal = 0.0;

        foreach ($normalizedItems as $index => $item) {
            $line = is_array($item) ? $item : [];
            $name = $this->resolveLineItemName($line, 'Unknown Product');
            $productRef = (string) ($line['mfgPartNo'] ?? $line['mfg_part_no'] ?? $line['sku'] ?? $line['product_id'] ?? 'N/A');
            $quantity = max(1, (int) ($line['quantity'] ?? 1));

            $unitPrice = (float)($line['unitPrice'] ?? $line['unit_price'] ?? 0);
            $lineTotal = isset($line['lineTotal']) || isset($line['line_total'])
                ? (float)($line['lineTotal'] ?? $line['line_total'])
                : (($unitPrice > 0) ? ($quantity * $unitPrice) : 0.0);
            $runningTotal += $lineTotal;

            $safeName = e($name);
            $safeProductRef = e($productRef);
            $displayUnit = $this->moneyOrUnavailable($unitPrice);
            $displayLineTotal = $this->moneyOrUnavailable($lineTotal);

            $rows .= "
                <tr>
                    <td style='padding:10px;border-bottom:1px solid #edf2fb;color:#111827;font-size:13px'>
                        <p style='margin:0 0 2px;font-weight:600'>{$safeName}</p>
                        <p style='margin:0;color:#6b7280;font-size:12px'>Ref: {$safeProductRef}</p>
                    </td>
                    <td style='padding:10px;border-bottom:1px solid #edf2fb;color:#111827;font-size:13px;text-align:center'>{$quantity}</td>
                    <td style='padding:10px;border-bottom:1px solid #edf2fb;color:#111827;font-size:13px;text-align:right'>{$displayUnit}</td>
                    <td style='padding:10px;border-bottom:1px solid #edf2fb;color:#111827;font-size:13px;text-align:right;font-weight:700'>{$displayLineTotal}</td>
                </tr>
            ";
        }

        $displayTotal = '$' . number_format((float) ($quoteTotal ?? $runningTotal), 2);
        $pricingNote = !$hasAnyPrices
            ? "<p style='margin:10px 14px 0;color:#92400e;font-size:12px'>Line-item prices were unavailable in the source record. Totals shown are from the persisted quote total.</p>"
            : '';

        return "
            <div style='margin:14px 0 10px;border:1px solid #d8e4f6;border-radius:12px;overflow:hidden;background:#ffffff'>
                <div style='padding:12px 14px;background:#f4f8ff;border-bottom:1px solid #d8e4f6'>
                    <p style='margin:0;font-size:13px;font-weight:700;color:#1e3a6e'>Submitted Items</p>
                </div>
                <table role='presentation' cellpadding='0' cellspacing='0' width='100%' style='border-collapse:collapse'>
                    <thead>
                        <tr>
                            <th style='padding:10px;text-align:left;font-size:12px;color:#6b7280;border-bottom:1px solid #edf2fb'>Item</th>
                            <th style='padding:10px;text-align:center;font-size:12px;color:#6b7280;border-bottom:1px solid #edf2fb'>Qty</th>
                            <th style='padding:10px;text-align:right;font-size:12px;color:#6b7280;border-bottom:1px solid #edf2fb'>Unit</th>
                            <th style='padding:10px;text-align:right;font-size:12px;color:#6b7280;border-bottom:1px solid #edf2fb'>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                </table>
                <div style='padding:12px 14px;background:#f8fbff;border-top:1px solid #d8e4f6;text-align:right'>
                    <p style='margin:0;font-size:12px;color:#6b7280'>Estimated Total</p>
                    <p style='margin:2px 0 0;font-size:20px;font-weight:700;color:#1e3a6e'>{$displayTotal}</p>
                </div>
                {$pricingNote}
            </div>
        ";
    }

    private function buildFullInvoiceHtml(\App\Models\Invoice $invoice, \App\Models\User $user, bool $isReminder, ?string $customReminderMessage = null): string
    {
        $appUrl        = $this->frontendUrl();
        $logoUrl       = e($this->logoUrl());
        $supportEmail  = e((string) AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')));
        $invNumber   = e($invoice->invoice_number);
        $issuedAt    = $invoice->issued_at?->format('M d, Y') ?? 'N/A';
        $dueDate     = $invoice->due_at?->format('M d, Y') ?? 'Pending delivery';
        $totalAmt    = (float)($invoice->total_amount ?? 0);
        $taxAmt      = (float)($invoice->tax_amount ?? 0);
        $paidAmt     = (float)($invoice->paid_amount ?? 0);

        // Best-effort: load related order for billing/shipping addresses and freight fallback.
        $order = \App\Models\Order::where('order_number', $invoice->order_number)
            ->with(['billingAddress', 'shippingAddress'])
            ->first();

        $breakdownRaw = is_array($invoice->raw_data) ? $invoice->raw_data : [];
        $breakdown = is_array($breakdownRaw['invoice_charge_breakdown'] ?? null)
            ? $breakdownRaw['invoice_charge_breakdown']
            : [];
        $tdChargeLines = is_array($breakdown['td_charge_lines'] ?? null)
            ? $breakdown['td_charge_lines']
            : [];

        $sumTdCharges = function (array $needles) use ($tdChargeLines): float {
            $sum = 0.0;
            foreach ($tdChargeLines as $line) {
                if (!is_array($line)) {
                    continue;
                }

                $label = strtolower(trim((string) ($line['label'] ?? '')));
                $amount = (float) ($line['amount'] ?? 0);
                if ($label === '') {
                    continue;
                }

                foreach ($needles as $needle) {
                    if (str_contains($label, strtolower($needle))) {
                        $sum += $amount;
                        break;
                    }
                }
            }

            return round($sum, 2);
        };

        $shippingAmt = (float) ($breakdown['shipping_amount']
            ?? ($order?->shipping_amount
            ?? ($this->findFirstNumericValue($order?->raw_data, [
                'freight', 'Freight', 'freightAmount', 'FreightAmount', 'poFreight', 'shippingAmount', 'ShippingAmount', 'shipping_amount', 'totalFreight', 'TotalFreight', 'shipCharge', 'ShipCharge',
            ]) ?? 0)));
        if ($shippingAmt <= 0) {
            $shippingAmt = $sumTdCharges(['shipping', 'freight', 'handling', 'ship charge']);
        }

        if ($taxAmt <= 0) {
            $taxAmt = $sumTdCharges(['tax']);
        }

        $subtotalValue = max(0, $totalAmt - $taxAmt - $shippingAmt);
        $subtotal    = number_format($subtotalValue, 2);
        $shipping    = number_format($shippingAmt, 2);
        $tax         = number_format($taxAmt, 2);
        $total       = number_format($totalAmt, 2);
        $paid        = number_format($paidAmt, 2);
        $balanceDueValue = max(0, $totalAmt - $paidAmt);
        $creditBalanceValue = max(0, $paidAmt - $totalAmt);
        $balance     = number_format($balanceDueValue, 2);
        $storedTaxRate = $breakdown['tax_rate_percent']
            ?? $breakdownRaw['tax_rate_percent']
            ?? data_get($breakdownRaw, 'pricing.tax_rate_percent');
        $taxableSubtotal = (float) ($breakdown['subtotal']
            ?? $breakdownRaw['subtotal']
            ?? max(0, $totalAmt - $taxAmt - $shippingAmt));
        $resolvedTaxRate = is_numeric($storedTaxRate)
            ? max(0, (float) $storedTaxRate)
            : ($taxableSubtotal > 0 ? max(0, ($taxAmt / $taxableSubtotal) * 100) : 0);
        $taxRate = rtrim(rtrim(number_format($resolvedTaxRate, 2, '.', ''), '0'), '.');
        $statusText  = strtoupper($invoice->status ?? 'PENDING');
        $statusBg    = ($invoice->status === 'paid') ? '#d4edda' : '#fff3cd';
        $statusColor = ($invoice->status === 'paid') ? '#155724' : '#856404';
        $safeName    = e($user->name ?? 'Customer');
        $companyName = e($user->company?->name ?? '');
        $companyHtml = $companyName ? "<p style='margin:0 0 3px;font-size:13px;'>{$companyName}</p>" : '';

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
        $invHasAnyPrices = false;
        if ($items && is_array($items) && count($items)) {
            // Detect if items carry price data. If missing, keep line values as
            // unavailable and rely on persisted invoice totals instead of
            // fabricating per-line prices.
            $invHasAnyPrices = $this->hasAnyLinePricing($items, ['unit_price'], ['line_total']);

            foreach ($items as $item) {
                $line = is_array($item) ? $item : [];
                $n  = e($this->resolveLineItemName($line, 'Unknown Product'));
                $q  = max(1, (int)($item['quantity'] ?? 1));
                $rawUp = (float)($item['unit_price'] ?? 0);
                $rawLt = (float)($item['line_total'] ?? (($rawUp > 0) ? ($rawUp * $q) : 0));
                $td = "border:1px solid #ddd;padding:10px 12px;font-size:13px;";
                $itemsRows .= "<tr>"
                    . "<td style='{$td}'>{$n}</td>"
                    . "<td style='{$td}text-align:right;'>{$q}</td>"
                    . "<td style='{$td}text-align:right;'>" . $this->moneyOrUnavailable($rawUp) . "</td>"
                    . "<td style='{$td}text-align:right;font-weight:bold;'>" . $this->moneyOrUnavailable($rawLt) . "</td>"
                    . "</tr>";
            }
        } else {
            $itemsRows = "<tr><td colspan='4' style='border:1px solid #ddd;padding:10px;text-align:center;font-size:13px;color:#999;'>No items found</td></tr>";
        }

        $invoicePricingNote = (is_array($items) && count($items) > 0 && !$invHasAnyPrices)
            ? "<p style='margin:10px 0 0;color:#92400e;font-size:12px'>Line-item prices were unavailable in the source record. Financial totals below come from the persisted invoice totals.</p>"
            : '';

        // Paid / balance rows (only shown when there is a payment)
        $paidRows = '';
        if ($paidAmt > 0) {
            $paidRows = "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>Amount Paid:</td>"
                . "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$paid}</td></tr>"
                . "<tr><td style='padding:8px 0;font-size:13px;font-weight:bold;'>Amount Due:</td>"
                . "<td style='padding:8px 0;font-size:13px;font-weight:bold;text-align:right;'>\${$balance}</td></tr>";

            if ($creditBalanceValue > 0) {
                $creditBalance = number_format($creditBalanceValue, 2);
                $paidRows .= "<tr><td style='padding:8px 0;font-size:13px;font-weight:bold;color:#155724;'>Credit Balance:</td>"
                    . "<td style='padding:8px 0;font-size:13px;font-weight:bold;text-align:right;color:#155724;'>\${$creditBalance}</td></tr>";
            }
        }

        $knownChargeNeedles = ['shipping', 'freight', 'handling', 'tax', 'adult signature', 'minimum order fee', 'recycling'];
        $extraTdChargeRows = '';
        foreach ($tdChargeLines as $line) {
            if (!is_array($line)) {
                continue;
            }

            $label = trim((string) ($line['label'] ?? ''));
            $amount = (float) ($line['amount'] ?? 0);
            if ($label === '') {
                continue;
            }

            $labelLower = strtolower($label);
            $isKnown = false;
            foreach ($knownChargeNeedles as $needle) {
                if (str_contains($labelLower, $needle)) {
                    $isKnown = true;
                    break;
                }
            }

            if ($isKnown) {
                continue;
            }

            $displayLabel = e(ucwords(strtolower($label)));
            $displayAmount = number_format($amount, 2);
            $extraTdChargeRows .= "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>{$displayLabel}:</td>"
                . "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$displayAmount}</td></tr>";
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

            $safeReminderMessage = trim((string) $customReminderMessage);
            if ($safeReminderMessage !== '') {
                $safeReminderMessage = nl2br(e($safeReminderMessage));
                $reminderBanner .= "<div style='background:#eef4ff;border:1px solid #bcd0f3;border-radius:6px;padding:12px 14px;margin-bottom:24px;'>"
                    . "<p style='margin:0 0 6px;font-size:12px;font-weight:700;letter-spacing:0.04em;text-transform:uppercase;color:#1f4788;'>Message From Armely Team</p>"
                    . "<p style='margin:0;font-size:14px;color:#1e3a6e;line-height:1.55;'>{$safeReminderMessage}</p>"
                    . "</div>";
            }
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
            . "<img src='{$logoUrl}' alt='Armely Store' style='max-width:190px;height:auto;display:block;margin:0 0 8px;'>"
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
            . $invoicePricingNote

            // ── Totals ─────────────────────────────────────────────────────────
            . "<table cellpadding='0' cellspacing='0' align='right' style='width:260px;margin-bottom:30px;'>"
            . "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>Subtotal:</td>"
            .     "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$subtotal}</td></tr>"
            . "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>Shipping &amp; Handling:</td>"
            .     "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$shipping}</td></tr>"
            . $extraTdChargeRows
            . "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>Tax ({$taxRate}%):</td>"
            .     "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$tax}</td></tr>"
            . "<tr><td style='padding:12px 0;font-size:16px;font-weight:bold;color:#2F5597;border-bottom:2px solid #333;'>Total:</td>"
            .     "<td style='padding:12px 0;font-size:16px;font-weight:bold;color:#2F5597;border-bottom:2px solid #333;text-align:right;'>\${$total}</td></tr>"
            . $paidRows
            . "</table>"

            . $notesHtml

            // ── CTA & Footer ───────────────────────────────────────────────────
            . "<div style='clear:both;margin-top:30px;padding-top:20px;'>"
            . "<a href='{$appUrl}/invoices' style='display:inline-block;padding:12px 24px;background:#2F5597;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;font-size:14px;'>View Invoice Details</a>"
            . "</div>"
            . "<div style='margin-top:50px;padding-top:20px;border-top:1px solid #ddd;text-align:center;font-size:12px;color:#999;'>"
            . "<p style='margin:0;'>Thank you for your business! | www.armely.com | {$supportEmail}</p>"
            . "</div>"
            . "</div></div></body></html>";
    }

    private function findFirstNumericValue(mixed $data, array $keys): ?float
    {
        if (!is_array($data)) {
            return null;
        }

        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $value = $data[$key];
            if (is_numeric((string) $value)) {
                return (float) $value;
            }
        }

        foreach ($data as $value) {
            if (!is_array($value)) {
                continue;
            }

            $found = $this->findFirstNumericValue($value, $keys);
            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    private function resolveLineItemName(array $line, string $fallback = 'Unknown Product'): string
    {
        $inlineName = $line['productName']
            ?? $line['product_name']
            ?? $line['partDescription']
            ?? $line['productDescription']
            ?? $line['name']
            ?? $line['description']
            ?? null;

        if (is_string($inlineName) && trim($inlineName) !== '') {
            return trim($inlineName);
        }

        $lookupKey = trim((string) (
            $line['product_id']
            ?? $line['productId']
            ?? $line['id']
            ?? $line['sku']
            ?? $line['partNumber']
            ?? $line['mfg_part_no']
            ?? $line['mfg_part_number']
            ?? $line['mfgPartNo']
            ?? ''
        ));

        if ($lookupKey !== '') {
            if (array_key_exists($lookupKey, $this->productNameLookupCache)) {
                return $this->productNameLookupCache[$lookupKey] ?: "{$fallback} ({$lookupKey})";
            }

            $productQuery = Product::query()
                ->select('product_name')
                ->where('tdsynnex_product_id', $lookupKey)
                ->orWhere('tdsynnex_sku_no', $lookupKey)
                ->orWhere('mfg_part_no', $lookupKey);

            if (ctype_digit($lookupKey)) {
                $productQuery->orWhere('id', (int) $lookupKey);
            }

            $name = $productQuery->value('product_name');

            $resolved = is_string($name) ? trim($name) : '';
            $this->productNameLookupCache[$lookupKey] = $resolved;

            if ($resolved !== '') {
                return $resolved;
            }

            return "{$fallback} ({$lookupKey})";
        }

        return $fallback;
    }

    private function isConfigured(): bool
    {
        $config = $this->getAzureConfig();

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

            $subject = $this->withSubjectPrefix($subject);

            $azure = $this->getAzureConfig();
            $fromEmail = (string) ($azure['from_email'] ?? '');
            $fromName = (string) ($azure['from_name'] ?? config('mail.from.name', 'Armely Store'));
            $url = 'https://graph.microsoft.com/v1.0/users/' . rawurlencode($fromEmail) . '/sendMail';

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($url, [
                    'message' => [
                        'subject' => $subject,
                        'from' => [
                            'emailAddress' => [
                                'address' => $fromEmail,
                                'name' => $fromName,
                            ],
                        ],
                        'sender' => [
                            'emailAddress' => [
                                'address' => $fromEmail,
                                'name' => $fromName,
                            ],
                        ],
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

            if ($this->isRecipientDeliveryFailure($response->status(), $response->body())) {
                $this->markEmailAsSuppressed($normalizedToEmail);
            }

            return false;
        } catch (\Throwable $e) {
            Log::warning('Azure Graph email send exception: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function withSubjectPrefix(string $subject): string
    {
        $cleanSubject = trim($subject);
        $prefix = trim((string) ($this->getAzureConfig()['subject_prefix'] ?? config('app.name', 'Armely Store')));

        if ($prefix === '' || $cleanSubject === '') {
            return $cleanSubject;
        }

        if (Str::startsWith(Str::lower($cleanSubject), Str::lower($prefix))) {
            return $cleanSubject;
        }

        return $prefix . ' - ' . $cleanSubject;
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

    public function sendCartShareEmail(
        string $toEmail,
        string $senderName,
        string $shareUrl,
        int $itemCount,
        string $note = ''
    ): bool {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeSender   = e($senderName ?: 'A user');
        $safeUrl      = e($shareUrl);
        $safeNote     = $note ? e($note) : '';
        $appName      = e(config('app.name', 'Armely Store'));
        $noteHtml     = $safeNote
            ? "<div style='background:#f4f8ff;border-left:3px solid #2F5597;border-radius:0 6px 6px 0;padding:12px 16px;margin:16px 0'>
                   <p style='margin:0 0 4px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em'>Note from {$safeSender}</p>
                   <p style='margin:0;font-size:14px;color:#1f2937'>{$safeNote}</p>
               </div>"
            : '';

        $subject = "{$senderName} shared a quote cart with you";

        $html = $this->buildModernNotificationEmail(
            'Shared Quote Cart',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'><strong>{$safeSender}</strong> has shared a quote cart with you on <strong>{$appName}</strong>.</p>
                {$this->buildQuoteSummaryCard([
                    ['label' => 'Shared by', 'value' => $safeSender],
                    ['label' => 'Items',     'value' => (string) $itemCount],
                ])}
                {$noteHtml}
                <p style='margin:16px 0 0;color:#4b5563;font-size:13px'>Click the button below to open the shared cart and import the items into your own quote.</p>
                <p style='margin:8px 0 0;color:#6b7280;font-size:12px;word-break:break-all'>Or copy this link: <a href='{$safeUrl}' style='color:#2F5597'>{$safeUrl}</a></p>
            ",
            'Open Shared Cart',
            $shareUrl,
            'You can import or merge these items directly into your account.'
        );

        $text = "Hello,\n\n{$senderName} shared a quote cart with you on {$appName}.\n"
            . "Items: {$itemCount}\n"
            . ($note ? "Note: {$note}\n" : '')
            . "\nOpen the shared cart: {$shareUrl}";

        return $this->sendEmail($toEmail, $subject, $html, $text);
    }

    public function sendChatEscalationAdminEmail(string $adminEmail, string $adminName, array $payload): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeAdminName = e($adminName ?: 'Admin');
        $sessionId = (int) ($payload['session_id'] ?? 0);
        $reopened = (bool) ($payload['reopened'] ?? false);
        $source = e((string) ($payload['source'] ?? 'manual_escalation'));
        $note = trim((string) ($payload['note'] ?? ''));
        $customerName = e((string) data_get($payload, 'customer.name', 'Customer'));
        $customerEmail = e((string) data_get($payload, 'customer.email', ''));
        $customerId = (int) data_get($payload, 'customer.id', 0);
        $storeChatUrl = $this->frontendUrl() . '/admin/chat';
        $subject = ($reopened ? 'Reopened' : 'New') . " Chat Escalation: #{$sessionId}";

        $noteHtml = $note !== ''
            ? "<div style='margin-top:14px;padding:12px;border-left:4px solid #2F5597;background:#f4f8ff;border-radius:8px'>"
                . "<p style='margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:#64748b;font-weight:700'>Customer note</p>"
                . "<p style='margin:0;font-size:14px;color:#1f2937'>" . e($note) . "</p>"
              . "</div>"
            : '';

        $html = $this->buildModernNotificationEmail(
            'Chat Escalation Alert',
            "
                <p style='margin:0 0 12px;font-size:15px;color:#1f2937'>Hello {$safeAdminName},</p>
                <p style='margin:0 0 14px;font-size:15px;color:#1f2937'>A customer " . ($reopened ? 'reopened and re-escalated' : 'escalated') . " a Mela AI chat and is waiting for a human response.</p>
                {$this->buildQuoteSummaryCard([
                    ['label' => 'Chat Session', 'value' => '#' . $sessionId],
                    ['label' => 'Customer', 'value' => $customerName],
                    ['label' => 'Customer Email', 'value' => $customerEmail !== '' ? $customerEmail : 'N/A'],
                    ['label' => 'Customer ID', 'value' => $customerId > 0 ? (string) $customerId : 'N/A'],
                    ['label' => 'Source', 'value' => $source],
                ])}
                {$noteHtml}
                <p style='margin:14px 0 0;color:#4b5563;font-size:13px'>Open the chat inbox to continue this conversation.</p>
            ",
            'Open Chat Escalations',
            $storeChatUrl,
            'Replying from admin chat will notify the customer in real time.'
        );

        $text = "Hello {$adminName},\n\n"
            . "A customer " . ($reopened ? 'reopened and re-escalated' : 'escalated') . " a Mela AI chat.\n"
            . "Chat Session: #{$sessionId}\n"
            . "Customer: " . strip_tags((string) data_get($payload, 'customer.name', 'Customer')) . "\n"
            . "Customer Email: " . strip_tags((string) data_get($payload, 'customer.email', 'N/A')) . "\n"
            . "Source: " . (string) data_get($payload, 'source', 'manual_escalation') . "\n"
            . ($note !== '' ? "Customer note: {$note}\n" : '')
            . "\nOpen Chat Escalations: {$storeChatUrl}";

        return $this->sendEmail($adminEmail, $subject, $html, $text);
    }

    public function sendSyncStatusEmail(string $jobName, string $status, array $details = []): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeJob    = e($jobName);
        $safeStatus = e($status);
        $supportEmail = env('SUPPORT_EMAIL', 'info@armely.com');

        $statusColors = [
            'started'   => ['#1d4ed8', '#dbeafe', 'Started'],
            'completed' => ['#15803d', '#dcfce7', 'Completed'],
            'failed'    => ['#b91c1c', '#fee2e2', 'Failed'],
        ];
        [$headerColor, $badgeBg, $badgeLabel] = $statusColors[$status] ?? ['#374151', '#f3f4f6', ucfirst($status)];

        $rows = '';
        foreach ($details as $label => $value) {
            if ($label === 'log' && is_array($value)) {
                continue;
            }
            $safeLabel = e((string) $label);
            $safeValue = e((string) $value);
            $rows .= "<tr><td style='padding:6px 12px;color:#6b7280;font-size:13px;border-bottom:1px solid #f3f4f6'>{$safeLabel}</td>"
                   . "<td style='padding:6px 12px;font-size:13px;font-weight:600;border-bottom:1px solid #f3f4f6'>{$safeValue}</td></tr>";
        }

        $tableBlock = $rows ? "<table style='width:100%;border-collapse:collapse;margin-top:16px;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden'>{$rows}</table>" : '';

        $html = $this->buildModernNotificationEmail(
            "{$safeJob} — {$badgeLabel}",
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Armely Store sync job status update.</p>
                <p style='margin:0 0 8px;color:#4b5563'><strong>Job:</strong> {$safeJob}</p>
                <p style='margin:0 0 18px;color:#4b5563'><strong>Status:</strong> <span style='background:{$badgeBg};color:{$headerColor};padding:2px 10px;border-radius:999px;font-size:12px;font-weight:700'>{$badgeLabel}</span></p>
                {$tableBlock}
                <p style='margin:18px 0 0;font-size:12px;color:#9ca3af'>Sent from Armely Store backend at " . now()->format('Y-m-d H:i:s T') . "</p>
            ",
            'Go to Admin Panel',
            $this->frontendUrl() . '/admin',
            "Contact {$supportEmail} if you have questions about this sync.",
            $headerColor,
            $badgeLabel,
            $headerColor
        );

        $text = "{$safeJob} — {$badgeLabel}\n\n";
        foreach ($details as $label => $value) {
            if ($label !== 'log') {
                $text .= "{$label}: {$value}\n";
            }
        }

        $sent = false;
        foreach ($this->syncStatusEmails() as $adminEmail) {
            $sent = $this->sendEmail($adminEmail, "Armely Sync: {$safeJob} {$badgeLabel}", $html, $text) || $sent;
        }

        return $sent;
    }

    public function sendPriceDropAlertEmail(
        \App\Models\User $user,
        \App\Models\Product $product,
        float $previousPrice,
        float $currentPrice,
        float $dropAmount,
        float $dropPercent
    ): bool {
        if (!$this->isConfigured()) {
            return false;
        }

        if (trim((string) ($user->email ?? '')) === '' || $currentPrice <= 0 || $previousPrice <= 0) {
            return false;
        }

        $productName = trim((string) ($product->product_name ?? 'Product'));
        $partNumber = trim((string) ($product->mfg_part_no ?? ''));
        $productId = (string) ($product->id ?? '');

        $safeName = e((string) ($user->name ?: 'Customer'));
        $safeProductName = e($productName);
        $safePartNumber = e($partNumber !== '' ? $partNumber : 'N/A');
        $safePrevious = '$' . number_format($previousPrice, 2);
        $safeCurrent = '$' . number_format($currentPrice, 2);
        $safeDropAmount = '$' . number_format($dropAmount, 2);
        $safeDropPercent = number_format(max(0, $dropPercent), 2) . '%';
        $productUrl = $this->frontendUrl() . '/products/' . rawurlencode($productId);
        $unsubscribeUrl = $this->marketingUnsubscribeUrl($user, 'price_alerts');
        $unsubscribeHtml = $this->marketingUnsubscribeHtml($unsubscribeUrl);
        $unsubscribeText = $this->marketingUnsubscribeText($unsubscribeUrl);

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Product', 'value' => $safeProductName],
            ['label' => 'Part Number', 'value' => $safePartNumber],
            ['label' => 'Previous Price', 'value' => $safePrevious],
            ['label' => 'Current Price', 'value' => $safeCurrent],
            ['label' => 'Drop', 'value' => $safeDropAmount . ' (' . $safeDropPercent . ')'],
        ]);

        $html = $this->buildModernNotificationEmail(
            'Price Drop Alert',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>A product you are tracking just dropped in price.</p>
                {$summaryHtml}
                {$unsubscribeHtml}
            ",
            'View Product',
            $productUrl,
            'You are receiving this email because price alerts are enabled for this item.',
            '#15803d',
            'Price Drop',
            '#15803d'
        );

        $text = "Hello {$user->name},\n\n"
            . "Price drop alert for {$productName}" . ($partNumber !== '' ? " ({$partNumber})" : '') . "\n"
            . "Previous: {$safePrevious}\n"
            . "Current: {$safeCurrent}\n"
            . "Drop: {$safeDropAmount} ({$safeDropPercent})\n"
            . "View product: {$productUrl}\n\n"
            . $unsubscribeText;

        return $this->sendEmail($user->email, "Price Drop: {$productName}", $html, $text);
    }

    public function sendAbandonedCartReminderEmail(\App\Models\User $user, array $items, \Illuminate\Support\Carbon $lastSyncedAt): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        if (trim((string) ($user->email ?? '')) === '') {
            return false;
        }

        $safeName = e((string) ($user->name ?: 'Customer'));
        $itemCount = count($items);
        if ($itemCount === 0) {
            return false;
        }

        $rows = '';
        $total = 0.0;
        foreach (array_slice($items, 0, 10) as $item) {
            $name = e((string) ($item['product_name'] ?? 'Product'));
            $part = e((string) ($item['mfg_part_no'] ?? 'N/A'));
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $unit = (float) ($item['unit_price'] ?? 0);
            $line = (float) ($item['line_total'] ?? ($unit > 0 ? $unit * $qty : 0));
            $total += $line;

            $rows .= "
                <tr>
                    <td style='padding:10px;border-bottom:1px solid #edf2fb;color:#111827;font-size:13px'>
                        <p style='margin:0 0 2px;font-weight:600'>{$name}</p>
                        <p style='margin:0;color:#6b7280;font-size:12px'>Part: {$part}</p>
                    </td>
                    <td style='padding:10px;border-bottom:1px solid #edf2fb;color:#111827;font-size:13px;text-align:center'>{$qty}</td>
                    <td style='padding:10px;border-bottom:1px solid #edf2fb;color:#111827;font-size:13px;text-align:right'>" . ($unit > 0 ? ('$' . number_format($unit, 2)) : 'Unavailable') . "</td>
                    <td style='padding:10px;border-bottom:1px solid #edf2fb;color:#111827;font-size:13px;text-align:right;font-weight:700'>" . ($line > 0 ? ('$' . number_format($line, 2)) : 'Unavailable') . "</td>
                </tr>
            ";
        }

        $table = "
            <div style='margin:14px 0 10px;border:1px solid #d8e4f6;border-radius:12px;overflow:hidden;background:#ffffff'>
                <div style='padding:12px 14px;background:#f4f8ff;border-bottom:1px solid #d8e4f6'>
                    <p style='margin:0;font-size:13px;font-weight:700;color:#1e3a6e'>Items in Your Cart</p>
                </div>
                <table role='presentation' cellpadding='0' cellspacing='0' width='100%' style='border-collapse:collapse'>
                    <thead>
                        <tr>
                            <th style='padding:10px;text-align:left;font-size:12px;color:#6b7280;border-bottom:1px solid #edf2fb'>Item</th>
                            <th style='padding:10px;text-align:center;font-size:12px;color:#6b7280;border-bottom:1px solid #edf2fb'>Qty</th>
                            <th style='padding:10px;text-align:right;font-size:12px;color:#6b7280;border-bottom:1px solid #edf2fb'>Unit</th>
                            <th style='padding:10px;text-align:right;font-size:12px;color:#6b7280;border-bottom:1px solid #edf2fb'>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>{$rows}</tbody>
                </table>
                <div style='padding:12px 14px;background:#f8fbff;border-top:1px solid #d8e4f6;text-align:right'>
                    <p style='margin:0;font-size:12px;color:#6b7280'>Estimated Current Total</p>
                    <p style='margin:2px 0 0;font-size:20px;font-weight:700;color:#1e3a6e'>$" . number_format($total, 2) . "</p>
                </div>
            </div>
        ";

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Items', 'value' => (string) $itemCount],
            ['label' => 'Last Updated', 'value' => $lastSyncedAt->format('M d, Y H:i')],
        ]);

        $cartUrl = $this->frontendUrl() . '/cart';
        $unsubscribeUrl = $this->marketingUnsubscribeUrl($user, 'cart_reminders');
        $unsubscribeHtml = $this->marketingUnsubscribeHtml($unsubscribeUrl);
        $unsubscribeText = $this->marketingUnsubscribeText($unsubscribeUrl);
        $html = $this->buildModernNotificationEmail(
            'You Left Items In Your Cart',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>You still have items in your cart waiting for your quote request.</p>
                {$summaryHtml}
                {$table}
                {$unsubscribeHtml}
            ",
            'Return to Cart',
            $cartUrl,
            'Prices shown are current at send time and may change based on live catalog updates.',
            '#2F5597',
            'Cart Reminder',
            '#2F5597'
        );

        $text = "Hello {$user->name},\n\nYou still have {$itemCount} item(s) in your cart.\n"
            . "Last updated: " . $lastSyncedAt->format('Y-m-d H:i') . "\n"
            . "Return to cart: {$cartUrl}\n\n"
            . $unsubscribeText;

        return $this->sendEmail($user->email, 'Reminder: Items waiting in your cart', $html, $text);
    }

    public function sendViewedProductReminderEmail(
        \App\Models\User $user,
        \App\Models\Product $product,
        \Illuminate\Support\Carbon $viewedAt,
        float $currentPrice
    ): bool {
        if (!$this->isConfigured()) {
            return false;
        }

        if (trim((string) ($user->email ?? '')) === '') {
            return false;
        }

        $safeName = e((string) ($user->name ?: 'Customer'));
        $productName = trim((string) ($product->product_name ?? 'Product'));
        $safeProductName = e($productName);
        $safePartNumber = e((string) ($product->mfg_part_no ?? 'N/A'));
        $priceLabel = $currentPrice > 0 ? ('$' . number_format($currentPrice, 2)) : 'Unavailable';
        $productUrl = $this->frontendUrl() . '/products/' . rawurlencode((string) ($product->id ?? ''));
        $unsubscribeUrl = $this->marketingUnsubscribeUrl($user, 'browse_reminders');
        $unsubscribeHtml = $this->marketingUnsubscribeHtml($unsubscribeUrl);
        $unsubscribeText = $this->marketingUnsubscribeText($unsubscribeUrl);

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Product', 'value' => $safeProductName],
            ['label' => 'Part Number', 'value' => $safePartNumber],
            ['label' => 'Current Price', 'value' => $priceLabel],
            ['label' => 'Viewed On', 'value' => $viewedAt->format('M d, Y H:i')],
        ]);

        $html = $this->buildModernNotificationEmail(
            'Still Interested In This Product?',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>You recently viewed this item. If you are still evaluating options, you can quickly add it to your quote cart.</p>
                {$summaryHtml}
                {$unsubscribeHtml}
            ",
            'View Product',
            $productUrl,
            'You are receiving this because viewed-item reminders are active for your account.',
            '#2F5597',
            'Viewed Item Reminder',
            '#2F5597'
        );

        $text = "Hello {$user->name},\n\nYou recently viewed {$productName}.\n"
            . "Current price: {$priceLabel}\n"
            . "Viewed on: " . $viewedAt->format('Y-m-d H:i') . "\n"
            . "View product: {$productUrl}\n\n"
            . $unsubscribeText;

        return $this->sendEmail($user->email, "Reminder: {$productName}", $html, $text);
    }

    public function sendFavoriteProductReminderEmail(
        \App\Models\User $user,
        \App\Models\Product $product,
        \Illuminate\Support\Carbon $favoritedAt,
        float $currentPrice
    ): bool {
        if (!$this->isConfigured()) {
            return false;
        }

        if (trim((string) ($user->email ?? '')) === '') {
            return false;
        }

        $safeName = e((string) ($user->name ?: 'Customer'));
        $productName = trim((string) ($product->product_name ?? 'Product'));
        $safeProductName = e($productName);
        $safePartNumber = e((string) ($product->mfg_part_no ?? 'N/A'));
        $priceLabel = $currentPrice > 0 ? ('$' . number_format($currentPrice, 2)) : 'Unavailable';
        $productUrl = $this->frontendUrl() . '/products/' . rawurlencode((string) ($product->id ?? ''));
        $unsubscribeUrl = $this->marketingUnsubscribeUrl($user, 'browse_reminders');
        $unsubscribeHtml = $this->marketingUnsubscribeHtml($unsubscribeUrl);
        $unsubscribeText = $this->marketingUnsubscribeText($unsubscribeUrl);

        $summaryHtml = $this->buildQuoteSummaryCard([
            ['label' => 'Favorite Item', 'value' => $safeProductName],
            ['label' => 'Part Number', 'value' => $safePartNumber],
            ['label' => 'Current Price', 'value' => $priceLabel],
            ['label' => 'Saved On', 'value' => $favoritedAt->format('M d, Y H:i')],
        ]);

        $html = $this->buildModernNotificationEmail(
            'Your Saved Item Is Still Available',
            "
                <p style='margin:0 0 14px;font-size:16px;color:#1f2937'>Hello {$safeName},</p>
                <p style='margin:0 0 18px;color:#4b5563'>You saved this product to favorites. If you are ready, you can move it into your quote cart now.</p>
                {$summaryHtml}
                {$unsubscribeHtml}
            ",
            'View Favorite Item',
            $productUrl,
            'You are receiving this because favorite-item reminders are active for your account.',
            '#2F5597',
            'Favorite Item Reminder',
            '#2F5597'
        );

        $text = "Hello {$user->name},\n\nYou saved {$productName} to favorites.\n"
            . "Current price: {$priceLabel}\n"
            . "Saved on: " . $favoritedAt->format('Y-m-d H:i') . "\n"
            . "View item: {$productUrl}\n\n"
            . $unsubscribeText;

        return $this->sendEmail($user->email, "Favorite Reminder: {$productName}", $html, $text);
    }

    private function marketingUnsubscribeUrl(User $user, string $scope): string
    {
        try {
            return app(UserEmailPreferenceService::class)->unsubscribeUrl($user, $scope);
        } catch (\Throwable) {
            return '';
        }
    }

    private function marketingUnsubscribeHtml(string $url): string
    {
        if (trim($url) === '') {
            return '';
        }

        $safeUrl = e($url);

        return "
            <p style='margin:16px 0 0;color:#6b7280;font-size:12px'>
                Prefer fewer messages? <a href='{$safeUrl}' style='color:#2563eb'>Unsubscribe from this category</a>.
            </p>
        ";
    }

    private function marketingUnsubscribeText(string $url): string
    {
        if (trim($url) === '') {
            return '';
        }

        return "Unsubscribe from this category: {$url}";
    }

    private function activeAdminEmails(): array
    {
        try {
            $admins = User::where('role', 'admin')
                ->where('status', 'active')
                ->orderBy('id')
                ->pluck('email')
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Unable to load active admin email recipients: ' . $e->getMessage());
            return [];
        }

        return $this->uniqueEmails($admins);
    }

    private function syncStatusEmails(): array
    {
        $emails = $this->activeAdminEmails();

        try {
            $emails[] = (string) AppSetting::getValue(
                'price_sync.email',
                config('mail.sync_status_email', env('SYNC_STATUS_EMAIL', 'malvine.owuor@armely.com'))
            );
        } catch (\Throwable) {
            $emails[] = config('mail.sync_status_email', env('SYNC_STATUS_EMAIL', 'malvine.owuor@armely.com'));
        }

        return $this->uniqueEmails($emails);
    }

    private function uniqueEmails(array $emails): array
    {
        $unique = [];

        foreach ($emails as $email) {
            $normalized = $this->normalizeEmail((string) $email);
            if ($normalized === '' || isset($unique[$normalized])) {
                continue;
            }

            $unique[$normalized] = $normalized;
        }

        return array_values($unique);
    }

    private function isRecipientDeliveryFailure(int $status, string $body): bool
    {
        if (!in_array($status, [400, 404, 422], true)) {
            return false;
        }

        $normalizedBody = Str::lower($body);

        return Str::contains($normalizedBody, [
            'invalid recipient',
            'invalidrecipients',
            'recipient not found',
            'errorinvalidrecipients',
            'recipientnotfound',
        ]);
    }

    private function getAccessToken(): ?string
    {
        $azure = $this->getAzureConfig();
        $tenantId = (string) ($azure['tenant_id'] ?? '');
        $clientId = (string) ($azure['client_id'] ?? '');
        $clientSecret = (string) ($azure['client_secret'] ?? '');

        $cacheKey = 'azure_graph_mail_access_token:' . sha1($tenantId . '|' . $clientId);

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($tenantId, $clientId, $clientSecret) {
            try {
                if ($tenantId === '' || $clientId === '' || $clientSecret === '') {
                    return null;
                }

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
