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
        $loginUrl = e($this->frontendUrl() . '/login');
        $appName = e(config('app.name', 'Armely Store'));
        $subject = "Your {$appName} Account Invitation";

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.6;color:#1f2937;max-width:620px'>
                <h2 style='margin:0 0 8px;color:#2F5597'>You have been invited to {$appName}</h2>
                <p>Hello {$safeName},</p>
                <p>An Armely customer account has been created for you. Use the temporary credentials below to sign in.</p>
                <div style='background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:24px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:6px 0;color:#64748b'>Email</td><td style='padding:6px 0 6px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b'>Password</td><td style='padding:6px 0 6px 16px;font-weight:700;font-family:Consolas,monospace'>{$safePassword}</td></tr>
                        {$companyRow}
                    </table>
                </div>
                <p style='background:#fff8e1;border:1px solid #f6c948;border-radius:8px;padding:12px;color:#92610a'>
                    This temporary password expires in 48 hours. You will be asked to change it after signing in.
                </p>
                <p style='margin:28px 0 8px'>
                    <a href='{$loginUrl}' style='background:#2F5597;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;display:inline-block;font-weight:600'>Sign in</a>
                </p>
            </div>
        ";

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
        $loginUrl = e(rtrim(config('app.url'), '/') . '/admin/login');
        $appName = e(config('app.name', 'Armely Store'));
        $subject = "Your {$appName} Admin Account Invitation";

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.6;color:#1f2937;max-width:620px'>
                <h2 style='margin:0 0 8px;color:#2F5597'>Admin account created</h2>
                <p>Hello {$safeName},</p>
                <p>An administrator account has been created for you on the Armely Store Admin Portal.</p>
                <div style='background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:24px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:6px 0;color:#64748b'>Email</td><td style='padding:6px 0 6px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b'>Password</td><td style='padding:6px 0 6px 16px;font-weight:700;font-family:Consolas,monospace'>{$safePassword}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b'>Role</td><td style='padding:6px 0 6px 16px;font-weight:600'>{$role}</td></tr>
                    </table>
                </div>
                <p style='background:#fff8e1;border:1px solid #f6c948;border-radius:8px;padding:12px;color:#92610a'>
                    This temporary password expires in 48 hours. You will be asked to change it after signing in.
                </p>
                <p style='margin:28px 0 8px'>
                    <a href='{$loginUrl}' style='background:#2F5597;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;display:inline-block;font-weight:600'>Sign in to Admin Portal</a>
                </p>
            </div>
        ";

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

    public function sendAccountApprovedEmail(\App\Models\User $user): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $safeName    = e($user->name ?: 'there');
        $safeEmail   = e($user->email);
        $companyName = e($user->company->name ?? '');
        $loginUrl    = e($this->frontendUrl() . '/login');
        $appName     = e(config('app.name', 'Armely Store'));
        $supportEmail = e(\App\Models\AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')));
        $subject     = "Your {$appName} Account Has Been Approved";

        $companyRow = $companyName
            ? "<tr><td style='padding:4px 0;color:#64748b'>Company</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$companyName}</td></tr>"
            : '';

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.6;color:#1f2937;max-width:600px'>
                <div style='text-align:center;margin-bottom:24px'>
                    <div style='display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:50%;background:#d1fae5;font-size:28px;line-height:56px'>&#10003;</div>
                </div>
                <h2 style='margin:0 0 8px;color:#2F5597;text-align:center'>Your account has been approved!</h2>
                <p>Hello {$safeName},</p>
                <p>Great news — your <strong>{$appName}</strong> account has been reviewed and approved by our team. You can now log in and start using the platform.</p>
                <div style='background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:24px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:4px 0;color:#64748b'>Account</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        {$companyRow}
                    </table>
                </div>
                <p>With your approved account you can:</p>
                <ul style='padding-left:20px;line-height:1.8'>
                    <li>Request and manage quotes</li>
                    <li>Place and track orders</li>
                    <li>View invoices and payment history</li>
                    <li>Chat with our Mela AI assistant</li>
                </ul>
                <p style='margin:28px 0 8px;text-align:center'>
                    <a href='{$loginUrl}' style='background:#2F5597;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:8px;display:inline-block;font-weight:600'>Log in to your account</a>
                </p>
                <p style='margin-top:28px;color:#64748b;font-size:12px'>
                    Questions? Contact us at <a href='mailto:{$supportEmail}' style='color:#2F5597'>{$supportEmail}</a>.<br>
                    &copy; " . date('Y') . " {$appName}. All rights reserved.
                </p>
            </div>
        ";

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

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.6;color:#1f2937;max-width:600px'>
                <div style='text-align:center;margin-bottom:24px'>
                    <div style='display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:50%;background:#fee2e2;font-size:28px;line-height:56px'>&#9888;</div>
                </div>
                <h2 style='margin:0 0 8px;color:#b91c1c;text-align:center'>Your account has been suspended</h2>
                <p>Hello {$safeName},</p>
                <p>Your <strong>{$appName}</strong> account has been suspended by our team. You will not be able to log in until the suspension is lifted.</p>
                <div style='background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:16px;margin:24px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:4px 0;color:#64748b'>Account</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        {$companyRow}
                    </table>
                </div>
                <p>If you believe this is a mistake or have questions, please reach out to us.</p>
                <p style='margin-top:28px;color:#64748b;font-size:12px'>
                    Contact us at <a href='mailto:{$supportEmail}' style='color:#2F5597'>{$supportEmail}</a>.<br>
                    &copy; " . date('Y') . " {$appName}. All rights reserved.
                </p>
            </div>
        ";

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
        $loginUrl    = e($this->frontendUrl() . '/login');
        $appName     = e(config('app.name', 'Armely Store'));
        $supportEmail = e(\App\Models\AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')));
        $subject     = "Your {$appName} Account Has Been Reactivated";

        $companyRow = $companyName
            ? "<tr><td style='padding:4px 0;color:#64748b'>Company</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$companyName}</td></tr>"
            : '';

        $html = "
            <div style='font-family:Segoe UI,Arial,sans-serif;line-height:1.6;color:#1f2937;max-width:600px'>
                <div style='text-align:center;margin-bottom:24px'>
                    <div style='display:inline-flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:50%;background:#d1fae5;font-size:28px;line-height:56px'>&#10003;</div>
                </div>
                <h2 style='margin:0 0 8px;color:#2F5597;text-align:center'>Your account has been reactivated</h2>
                <p>Hello {$safeName},</p>
                <p>Good news — your <strong>{$appName}</strong> account has been reactivated. You can now log in and use the platform again.</p>
                <div style='background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:24px 0'>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:4px 0;color:#64748b'>Account</td><td style='padding:4px 0 4px 16px;font-weight:600'>{$safeEmail}</td></tr>
                        {$companyRow}
                    </table>
                </div>
                <p style='margin:28px 0 8px;text-align:center'>
                    <a href='{$loginUrl}' style='background:#2F5597;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:8px;display:inline-block;font-weight:600'>Log in to your account</a>
                </p>
                <p style='margin-top:28px;color:#64748b;font-size:12px'>
                    Questions? Contact us at <a href='mailto:{$supportEmail}' style='color:#2F5597'>{$supportEmail}</a>.<br>
                    &copy; " . date('Y') . " {$appName}. All rights reserved.
                </p>
            </div>
        ";

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
        $appUrl    = $this->frontendUrl();

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
        $appUrl    = $this->frontendUrl();

        $html = $this->buildFullInvoiceHtml($invoice, $recipient, true);
        $text = "Hello {$safeName},\n\nPayment reminder: Invoice #{$invNumber} has an outstanding balance of \${$balance}.\n{$appUrl}/invoices";

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
    <span style='display:inline-block;background:#0f2f63;color:#fff;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase'>Armely Store</span>
  </div>

  <!-- Card -->
  <div style='background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(15,47,99,0.10)'>

    <!-- Header band -->
    <div style='background:linear-gradient(135deg,#0f2f63 0%,#2f5597 100%);padding:28px 32px 24px'>
      {$badgeHtml}
      <h1 style='margin:0;color:#ffffff;font-size:26px;font-weight:700;line-height:1.2'>{$safeTitle}</h1>
    </div>

    <!-- Body -->
    <div style='padding:28px 32px'>
      {$contentHtml}

      <div style='margin-top:24px'>
        <a href='{$safeButtonUrl}'
           style='display:inline-block;padding:13px 28px;background:" . e($accentColor) . ";color:#ffffff;text-decoration:none;border-radius:8px;font-weight:700;font-size:14px;letter-spacing:0.02em'>
          {$safeButtonLabel} &rarr;
        </a>
      </div>
    </div>

    <!-- Footer note -->
    <div style='padding:16px 32px 20px;background:#f8fbff;border-top:1px solid #e3ebf8'>
      {$footerNoteHtml}
      <p style='margin:0;font-size:12px;color:#94a3b8'>You received this email because you have an account with Armely Store.
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

        // Detect whether any item carries price data.
        $hasAnyPrices = false;
        foreach ($normalizedItems as $item) {
            $l = is_array($item) ? $item : [];
            if ((float)($l['unitPrice'] ?? $l['unit_price'] ?? 0) > 0
                || (float)($l['lineTotal'] ?? $l['line_total'] ?? 0) > 0) {
                $hasAnyPrices = true;
                break;
            }
        }

        // When items have no prices but a quote total exists, distribute proportionally.
        $qtotal = (float)($quoteTotal ?? 0);
        $distributeTotal = !$hasAnyPrices && $qtotal > 0;
        $totalQty = 0;
        if ($distributeTotal) {
            foreach ($normalizedItems as $item) {
                $totalQty += max(1, (int)((is_array($item) ? $item : [])['quantity'] ?? 1));
            }
            if ($totalQty === 0) $totalQty = count($normalizedItems);
        }

        $rows = '';
        $runningTotal = 0.0;

        foreach ($normalizedItems as $index => $item) {
            $line = is_array($item) ? $item : [];
            $name = $this->resolveLineItemName($line, 'Unknown Product');
            $productRef = (string) ($line['mfgPartNo'] ?? $line['mfg_part_no'] ?? $line['sku'] ?? $line['product_id'] ?? 'N/A');
            $quantity = max(1, (int) ($line['quantity'] ?? 1));

            if ($distributeTotal) {
                $unitPrice = $totalQty > 0 ? $qtotal / $totalQty : 0.0;
                $lineTotal = $unitPrice * $quantity;
            } else {
                $unitPrice = (float)($line['unitPrice'] ?? $line['unit_price'] ?? 0);
                $lineTotal = isset($line['lineTotal']) || isset($line['line_total'])
                    ? (float)($line['lineTotal'] ?? $line['line_total'])
                    : ($quantity * $unitPrice);
            }
            $runningTotal += $lineTotal;

            $safeName = e($name);
            $safeProductRef = e($productRef);
            $displayUnit = '$' . number_format($unitPrice, 2);
            $displayLineTotal = '$' . number_format($lineTotal, 2);

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
            </div>
        ";
    }

    private function buildFullInvoiceHtml(\App\Models\Invoice $invoice, \App\Models\User $user, bool $isReminder): string
    {
        $appUrl        = $this->frontendUrl();
        $supportEmail  = e((string) AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')));
        $invNumber   = e($invoice->invoice_number);
        $issuedAt    = $invoice->issued_at?->format('M d, Y') ?? 'N/A';
        $dueDate     = $invoice->due_at?->format('M d, Y') ?? 'On Demand';
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
        $shippingAmt = (float) ($breakdown['shipping_amount'] ?? ($order?->shipping_amount ?? 0));
        $subtotalValue = max(0, $totalAmt - $taxAmt - $shippingAmt);
        $subtotal    = number_format($subtotalValue, 2);
        $shipping    = number_format($shippingAmt, 2);
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
            // Detect if items carry price data; if not, distribute invoice total by qty.
            $invHasAnyPrices = false;
            foreach ($items as $item) {
                if ((float)(is_array($item) ? ($item['unit_price'] ?? 0) : 0) > 0
                    || (float)(is_array($item) ? ($item['line_total'] ?? 0) : 0) > 0) {
                    $invHasAnyPrices = true;
                    break;
                }
            }
            $invDistribute = !$invHasAnyPrices && $totalAmt > 0;
            $invTotalQty = 0;
            if ($invDistribute) {
                foreach ($items as $item) {
                    $invTotalQty += max(1, (int)((is_array($item) ? $item : [])['quantity'] ?? 1));
                }
                if ($invTotalQty === 0) $invTotalQty = count($items);
            }

            foreach ($items as $item) {
                $line = is_array($item) ? $item : [];
                $n  = e($this->resolveLineItemName($line, 'Unknown Product'));
                $q  = max(1, (int)($item['quantity'] ?? 1));
                if ($invDistribute) {
                    $rawUp = $invTotalQty > 0 ? $totalAmt / $invTotalQty : 0.0;
                    $rawLt = $rawUp * $q;
                } else {
                    $rawUp = (float)($item['unit_price'] ?? 0);
                    $rawLt = (float)($item['line_total'] ?? ($rawUp * $q));
                }
                $up = number_format($rawUp, 2);
                $lt = number_format($rawLt, 2);
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
            . "<tr><td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;'>Shipping (TD SYNNEX):</td>"
            .     "<td style='padding:8px 0;font-size:13px;border-bottom:1px solid #ddd;text-align:right;'>\${$shipping}</td></tr>"
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
            . "<p style='margin:0;'>Thank you for your business! | www.armely.com | {$supportEmail}</p>"
            . "</div>"
            . "</div></div></body></html>";
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
