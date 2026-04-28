<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\AppSetting;
use App\Models\User;
use RuntimeException;

class QuickBooksPaymentService
{
    private function getQuickBooksSetting(string $key, mixed $fallback = ''): mixed
    {
        $stored = AppSetting::getValue($key, null);
        if ($stored === null || $stored === '') {
            return $fallback;
        }

        return $stored;
    }

    private function getQuickBooksConfig(): array
    {
        return [
            'client_id' => (string) $this->getQuickBooksSetting('integrations.quickbooks.client_id', config('services.quickbooks.client_id', '')),
            'client_secret' => (string) $this->getQuickBooksSetting('integrations.quickbooks.client_secret', config('services.quickbooks.client_secret', '')),
            'company_id' => (string) $this->getQuickBooksSetting('integrations.quickbooks.company_id', config('services.quickbooks.company_id', '')),
            'payment_url_template' => (string) $this->getQuickBooksSetting('integrations.quickbooks.payment_url_template', config('services.quickbooks.payment_url_template', '')),
            'bulk_payment_url_template' => (string) $this->getQuickBooksSetting('integrations.quickbooks.bulk_payment_url_template', config('services.quickbooks.bulk_payment_url_template', '')),
        ];
    }

    public function createInvoiceCheckout(Invoice $invoice, User $user, array $options = []): array
    {
        $remaining = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
        if ($remaining <= 0) {
            throw new RuntimeException('No remaining balance on this invoice.');
        }

        $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];
        $successUrl = (string) ($options['success_url'] ?? $this->buildResultUrl('success', [
            'invoice' => $invoice->invoice_number,
        ]));
        $cancelUrl = (string) ($options['cancel_url'] ?? $this->buildResultUrl('cancel', [
            'invoice' => $invoice->invoice_number,
        ]));

        $qbConfig = $this->getQuickBooksConfig();
        $checkoutUrl = $this->extractExistingHostedUrl($rawData);
        if (!$checkoutUrl) {
            $template = trim((string) ($qbConfig['payment_url_template'] ?? ''));
            if ($template === '') {
                throw new RuntimeException('QuickBooks payment URL is not configured. Set QUICKBOOKS_PAYMENT_URL_TEMPLATE or store a quickbooks_payment_url on the invoice.');
            }

            $checkoutUrl = $this->fillTemplate($template, $this->buildPlaceholderMap(
                $invoice,
                $user,
                $remaining,
                $successUrl,
                $cancelUrl,
                [
                    'quickbooks_client_id' => (string) ($qbConfig['client_id'] ?? ''),
                    'quickbooks_company_id' => (string) ($qbConfig['company_id'] ?? ''),
                ]
            ));
        }

        $invoice->update([
            'raw_data' => array_merge($rawData, [
                'payment_gateway' => 'quickbooks',
                'quickbooks_checkout_url' => $checkoutUrl,
                'quickbooks_checkout_created_at' => now()->toISOString(),
            ]),
            'stripe_checkout_session_id' => null,
        ]);

        return [
            'provider' => 'quickbooks',
            'session_url' => $checkoutUrl,
            'invoice_numbers' => [$invoice->invoice_number],
        ];
    }

    public function createBulkInvoiceCheckout(iterable $invoices, User $user, array $options = []): array
    {
        $invoiceCollection = collect($invoices)->values();
        if ($invoiceCollection->isEmpty()) {
            throw new RuntimeException('No invoices were provided for QuickBooks checkout.');
        }

        if ($invoiceCollection->count() === 1) {
            return $this->createInvoiceCheckout($invoiceCollection->first(), $user, $options);
        }

        $qbConfig = $this->getQuickBooksConfig();
        $template = trim((string) ($qbConfig['bulk_payment_url_template'] ?? ''));
        if ($template === '') {
            throw new RuntimeException('QuickBooks multi-invoice checkout is not configured. Combine selected invoices first, then pay the combined invoice.');
        }

        $invoiceNumbers = $invoiceCollection
            ->pluck('invoice_number')
            ->filter()
            ->values()
            ->all();

        $totalRemaining = $invoiceCollection->sum(function (Invoice $invoice) {
            return max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
        });

        if ($totalRemaining <= 0) {
            throw new RuntimeException('No payable balance found for the selected invoices.');
        }

        $successUrl = (string) ($options['success_url'] ?? $this->buildResultUrl('success', [
            'invoiceNumbers' => implode(',', $invoiceNumbers),
        ]));
        $cancelUrl = (string) ($options['cancel_url'] ?? $this->buildResultUrl('cancel', [
            'invoiceNumbers' => implode(',', $invoiceNumbers),
        ]));

        $checkoutUrl = $this->fillTemplate($template, $this->buildPlaceholderMap(
            $invoiceCollection->first(),
            $user,
            $totalRemaining,
            $successUrl,
            $cancelUrl,
            [
                'invoice_numbers' => implode(',', $invoiceNumbers),
                'invoice_count' => count($invoiceNumbers),
                'quickbooks_client_id' => (string) ($qbConfig['client_id'] ?? ''),
                'quickbooks_company_id' => (string) ($qbConfig['company_id'] ?? ''),
            ]
        ));

        foreach ($invoiceCollection as $invoice) {
            $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];
            $invoice->update([
                'raw_data' => array_merge($rawData, [
                    'payment_gateway' => 'quickbooks',
                    'quickbooks_checkout_url' => $checkoutUrl,
                    'quickbooks_checkout_created_at' => now()->toISOString(),
                ]),
                'stripe_checkout_session_id' => null,
            ]);
        }

        return [
            'provider' => 'quickbooks',
            'session_url' => $checkoutUrl,
            'invoice_numbers' => $invoiceNumbers,
        ];
    }

    private function buildPlaceholderMap(
        Invoice $invoice,
        User $user,
        float $amount,
        string $successUrl,
        string $cancelUrl,
        array $extra
    ): array {
        $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];
        $companyName = (string) optional($user->company)->name;

        return array_merge([
            'app_url' => (string) config('app.url'),
            'invoice_number' => (string) $invoice->invoice_number,
            'invoice_id' => (string) $invoice->id,
            'quickbooks_invoice_id' => (string) ($rawData['quickbooks_invoice_id'] ?? ''),
            'order_number' => (string) ($invoice->order_number ?? ''),
            'amount' => number_format($amount, 2, '.', ''),
            'amount_cents' => (string) (int) round($amount * 100),
            'customer_id' => (string) $user->id,
            'customer_name' => (string) ($user->name ?? ''),
            'customer_email' => (string) ($user->email ?? ''),
            'company_name' => $companyName,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ], $extra);
    }

    private function buildResultUrl(string $status, array $query = []): string
    {
        $configured = trim((string) env('FRONTEND_URL', ''));
        $baseUrl = rtrim($configured !== '' ? $configured : (string) config('app.url'), '/');
        $payload = array_merge(['quickbooks' => $status], $query);

        return $baseUrl . '/invoices?' . http_build_query($payload);
    }

    private function extractExistingHostedUrl(array $rawData): ?string
    {
        $candidates = [
            $rawData['quickbooks_payment_url'] ?? null,
            $rawData['quickbooks_checkout_url'] ?? null,
            $rawData['hosted_payment_url'] ?? null,
            $rawData['hosted_invoice_url'] ?? null,
            $rawData['payment_url'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value !== '' && filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
        }

        return null;
    }

    private function fillTemplate(string $template, array $placeholders): string
    {
        $replacements = [];
        foreach ($placeholders as $key => $value) {
            $stringValue = (string) $value;
            $replacements['{' . $key . '}'] = rawurlencode($stringValue);
            $replacements[':' . $key] = rawurlencode($stringValue);
            $replacements['{{' . $key . '}}'] = rawurlencode($stringValue);
        }

        $resolved = strtr($template, $replacements);
        if (!filter_var($resolved, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('QuickBooks payment URL template resolved to an invalid URL.');
        }

        return $resolved;
    }
}