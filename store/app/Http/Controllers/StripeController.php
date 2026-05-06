<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Message;
use App\Models\Order;
use App\Models\Quote;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TDSynnexService;
use App\Support\FrontendUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeController extends Controller
{
    private function markInvoiceAsPaid(Invoice $invoice, ?string $paymentIntentId): void
    {
        if ($invoice->status === 'paid') {
            return;
        }

        $invoice->update([
            'status' => 'paid',
            'paid_amount' => $invoice->total_amount,
            'paid_at' => now(),
            'stripe_payment_intent_id' => $paymentIntentId,
        ]);

        Message::createMessage(
            $invoice->user_id,
            'invoice',
            'Payment received',
            "Payment received for invoice {$invoice->invoice_number}.",
            $invoice->invoice_number,
            'normal',
            [
                'order_number' => $invoice->order_number,
                'invoice_id' => $invoice->id,
            ]
        );

        $this->autoCreateOrderFromPaidQuoteInvoice($invoice);
    }

    private function findOrderNumber($data): ?string
    {
        if (is_array($data)) {
            $commonFields = [
                'orderNumber', 'orderId', 'order_number', 'order_id',
                'poNumber', 'po_number', 'salesOrderNumber', 'soNumber',
                'confirmationNumber', 'referenceNumber', 'id',
            ];

            foreach ($commonFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    return (string) $data[$field];
                }
            }

            foreach ($data as $value) {
                if (is_array($value) || is_object($value)) {
                    $found = $this->findOrderNumber($value);
                    if ($found) {
                        return $found;
                    }
                }
            }
        } elseif (is_object($data)) {
            return $this->findOrderNumber((array) $data);
        }

        return null;
    }

    private function resolveTdPartNumber(array $item): string
    {
        $candidates = [
            $item['partNumber'] ?? null,
            $item['sku'] ?? null,
            $item['mfgPartNo'] ?? null,
            $item['mfg_part_number'] ?? null,
            $item['tdsynnex_sku_no'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value !== '' && !str_starts_with(strtoupper($value), 'PART-')) {
                return $value;
            }
        }

        return '';
    }

    private function resolveTdUnitPrice(array $item): float
    {
        $candidates = [
            $item['unitPrice'] ?? null,
            $item['unit_price'] ?? null,
            $item['price'] ?? null,
            $item['customer_price'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null || $candidate === '') {
                continue;
            }

            $value = (float) $candidate;
            if ($value > 0) {
                return $value;
            }
        }

        return 0.0;
    }

    private function mapTdStatusToLocal(string $tdStatus): string
    {
        $value = strtolower(trim($tdStatus));

        if (str_contains($value, 'reject') || str_contains($value, 'fail')) return 'failed';
        if (str_contains($value, 'cancel')) return 'cancelled';
        if (str_contains($value, 'ship') || str_contains($value, 'deliver')) return 'shipped';
        if (str_contains($value, 'accept') || str_contains($value, 'confirm') || str_contains($value, 'process')) return 'processing';

        return 'processing';
    }

    private function extractQuoteIdFromInvoice(Invoice $invoice): ?string
    {
        $rawData = is_array($invoice->raw_data) ? $invoice->raw_data : [];
        $fromRaw = trim((string) ($rawData['quote_id'] ?? ''));
        if ($fromRaw !== '') {
            return $fromRaw;
        }

        $notes = (string) ($invoice->notes ?? '');
        if (preg_match('/QUOTE:([A-Za-z0-9\-]+)/', $notes, $matches)) {
            return trim((string) ($matches[1] ?? '')) ?: null;
        }

        return null;
    }

    private function autoCreateOrderFromPaidQuoteInvoice(Invoice $invoice): void
    {
        try {
            $quoteId = $this->extractQuoteIdFromInvoice($invoice);
            if (!$quoteId) {
                return;
            }

            $quote = Quote::with('user', 'user.company.addresses')
                ->where('user_id', $invoice->user_id)
                ->where('quote_id', $quoteId)
                ->first();

            if (!$quote) {
                Log::warning("Auto-convert skipped: quote {$quoteId} not found for invoice {$invoice->invoice_number}");
                return;
            }

            $existingOrder = Order::where('quote_id', $quote->quote_id)->first();
            if ($existingOrder && $existingOrder->status !== 'failed') {
                if (empty($invoice->order_number)) {
                    $invoice->update(['order_number' => $existingOrder->order_number]);
                }
                return;
            }

            $items = is_array($quote->items) ? $quote->items : [];
            $lineItems = array_map(function ($item, $index) {
                if (!is_array($item)) {
                    $item = [];
                }

                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $partNumber = $this->resolveTdPartNumber($item);
                $description = (string) ($item['name'] ?? $item['description'] ?? 'Product');
                $unitPrice = $this->resolveTdUnitPrice($item);

                return [
                    'lineNumber' => (string) ($index + 1),
                    'partNumber' => $partNumber,
                    'partDescription' => $description,
                    'quantity' => $qty,
                    'unitPrice' => (string) number_format($unitPrice, 2, '.', ''),
                    'extendedPrice' => (string) number_format(($unitPrice * $qty), 2, '.', ''),
                ];
            }, $items, array_keys($items));

            $invalidSkuLines = array_values(array_filter($lineItems, function ($line) {
                $sku = trim((string) ($line['partNumber'] ?? ''));
                return $sku === '' || str_starts_with(strtoupper($sku), 'PART-');
            }));

            if (!empty($invalidSkuLines)) {
                Log::warning('Auto-convert blocked due to invalid SKU lines', [
                    'quote_id' => $quote->quote_id,
                    'invoice_number' => $invoice->invoice_number,
                    'invalid_lines' => $invalidSkuLines,
                ]);

                Message::createMessage(
                    $quote->user_id,
                    'order',
                    'Payment received, order pending validation',
                    "Payment for invoice {$invoice->invoice_number} was received, but order submission needs SKU validation by support.",
                    $quote->quote_id,
                    'high'
                );

                return;
            }

            $company = $quote->user->company ?? null;
            $shipAddr = $company ? $company->getDefaultShippingAddress() : null;
            $billAddr = $company ? $company->getDefaultBillingAddress() : null;
            $effectiveShipAddr = $shipAddr ?? $billAddr;
            $effectiveBillAddr = $billAddr ?? $shipAddr;

            $shippingAmount = (float) ((is_array($invoice->raw_data) ? ($invoice->raw_data['shipping_amount'] ?? 0) : 0));

            $orderData = [
                'poNumber' => $quote->quote_id,
                'poDate' => now()->format('Y-m-d'),
                'shipDate' => now()->addDays(7)->format('Y-m-d'),
                'vendor' => [
                    'vendorId' => '12345',
                    'vendorName' => 'Armely Store',
                ],
                'billTo' => [
                    'companyName' => $company->name ?? 'Company',
                    'address1' => $effectiveBillAddr->street_1 ?? '',
                    'address2' => $effectiveBillAddr->street_2 ?? '',
                    'city' => $effectiveBillAddr->city ?? '',
                    'state' => $effectiveBillAddr->state ?? '',
                    'postalCode' => $effectiveBillAddr->postal_code ?? '',
                    'country' => $effectiveBillAddr->country ?? 'US',
                    'contactEmail' => $quote->user->email ?? '',
                    'contactPhone' => $effectiveBillAddr->contact_phone ?? $quote->user->phone ?? '',
                ],
                'shipTo' => [
                    'companyName' => $company->name ?? 'Company',
                    'address1' => $effectiveShipAddr->street_1 ?? '',
                    'address2' => $effectiveShipAddr->street_2 ?? '',
                    'city' => $effectiveShipAddr->city ?? '',
                    'state' => $effectiveShipAddr->state ?? '',
                    'postalCode' => $effectiveShipAddr->postal_code ?? '',
                    'country' => $effectiveShipAddr->country ?? 'US',
                    'contactName' => $effectiveShipAddr->contact_name ?? $quote->user->name ?? '',
                    'contactPhone' => $effectiveShipAddr->contact_phone ?? $quote->user->phone ?? '',
                ],
                'poLine' => $lineItems,
                'poTotal' => (string) number_format((float) ($quote->total_amount ?? 0), 2, '.', ''),
                'poTax' => (string) number_format((float) ($quote->tax_amount ?? 0), 2, '.', ''),
                'poFreight' => (string) number_format($shippingAmount, 2, '.', ''),
            ];

            /** @var TDSynnexService $tdService */
            $tdService = app(TDSynnexService::class);
            $tdResponse = $tdService->placeOrder($orderData, 'us', false);

            $orderNumber = $this->findOrderNumber($tdResponse);
            if (!$orderNumber) {
                $orderNumber = 'ORD-' . now()->format('Y') . '-' . strtoupper(substr(md5($quote->quote_id . time()), 0, 6));
            }

            $tdStatus = strtolower((string) ($tdResponse['OrderResponse']['Code'] ?? 'accepted'));
            $localStatus = $this->mapTdStatusToLocal($tdStatus);

            $orderPayload = [
                'user_id' => $quote->user_id,
                'order_number' => $orderNumber,
                'quote_id' => $quote->quote_id,
                'tdsynnex_order_id' => $orderNumber,
                'status' => $localStatus,
                'payment_status' => 'paid',
                'total_amount' => $quote->total_amount,
                'tax_amount' => $quote->tax_amount,
                'discount_amount' => $quote->discount_amount,
                'shipping_amount' => $shippingAmount,
                'items' => $quote->items,
                'raw_data' => $tdResponse,
                'ordered_at' => now(),
            ];

            if ($existingOrder && $existingOrder->status === 'failed') {
                $existingOrder->update($orderPayload);
                $order = $existingOrder;
            } else {
                $order = Order::create($orderPayload);
            }

            if ($quote->status !== 'approved') {
                $quote->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);
            }

            $invoice->update(['order_number' => $order->order_number]);

            /** @var NotificationService $notificationService */
            $notificationService = app(NotificationService::class);
            $notificationService->sendOrderConfirmationNotification($order);

            Message::createMessage(
                $order->user_id,
                'order',
                'Order submitted automatically',
                "Your payment is complete. Order {$order->order_number} has been submitted.",
                $order->order_number,
                'normal',
                [
                    'quote_id' => $quote->quote_id,
                    'invoice_id' => $invoice->id,
                    'order_number' => $order->order_number,
                ]
            );
        } catch (\Throwable $e) {
            Log::error('Automatic order conversion after payment failed: ' . $e->getMessage(), [
                'invoice_number' => $invoice->invoice_number,
                'invoice_id' => $invoice->id,
            ]);
        }
    }

    private function resolvePayableInvoicesForUser(int $userId, array $invoiceNumbers)
    {
        $normalized = array_values(array_unique(array_filter(array_map(static function ($value) {
            return trim((string) $value);
        }, $invoiceNumbers))));

        if (empty($normalized)) {
            return collect();
        }

        return Invoice::where('user_id', $userId)
            ->whereIn('invoice_number', $normalized)
            ->get()
            ->filter(function (Invoice $invoice) {
                if ($invoice->status === 'paid') {
                    return false;
                }

                $remaining = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
                return $remaining > 0;
            })
            ->values();
    }

    public function payInvoiceWithDefaultCard(Request $request, string $invoiceNumber): JsonResponse
    {
        $user = $request->user();
        $stripe = $this->buildStripeClient();

        if (!$stripe) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        try {
            $invoice = Invoice::where('user_id', $user->id)
                ->where('invoice_number', $invoiceNumber)
                ->firstOrFail();

            if ($invoice->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice is already paid.',
                ], 400);
            }

            $remaining = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
            if ($remaining <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No remaining balance on this invoice.',
                ], 400);
            }

            $customerId = $this->ensureStripeCustomer($user, $stripe);
            $customer = $stripe->customers->retrieve($customerId, []);
            $defaultPaymentMethod = (string) ($customer->invoice_settings->default_payment_method ?? '');

            if ($defaultPaymentMethod === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'No default saved card found. Add and set a default card in Account.',
                ], 422);
            }

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int) round($remaining * 100),
                'currency' => 'usd',
                'customer' => $customerId,
                'payment_method' => $defaultPaymentMethod,
                'off_session' => true,
                'confirm' => true,
                'metadata' => [
                    'invoice_number' => $invoice->invoice_number,
                    'order_number' => (string) ($invoice->order_number ?? ''),
                    'user_id' => (string) $user->id,
                    'payment_mode' => 'default_saved_card',
                ],
            ]);

            $this->markInvoiceAsPaid($invoice, (string) ($paymentIntent->id ?? ''));

            return response()->json([
                'success' => true,
                'message' => 'Invoice paid successfully with your default card.',
                'data' => [
                    'invoice_number' => $invoice->invoice_number,
                    'payment_intent_id' => $paymentIntent->id ?? null,
                ],
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            $code = (string) ($e->getStripeCode() ?? 'card_error');
            return response()->json([
                'success' => false,
                'message' => 'Default card charge could not be completed. Please use checkout flow to authenticate this payment.',
                'code' => $code,
            ], 402);
        } catch (\Throwable $e) {
            Log::error('Failed to pay invoice with default card: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment with default card.',
            ], 500);
        }
    }

    public function payBulkInvoicesWithDefaultCard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_numbers' => ['required', 'array', 'min:1'],
            'invoice_numbers.*' => ['required', 'string'],
        ]);

        $user = $request->user();
        $stripe = $this->buildStripeClient();

        if (!$stripe) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        try {
            $payable = $this->resolvePayableInvoicesForUser($user->id, $validated['invoice_numbers']);
            if ($payable->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payable invoices found for selected invoice numbers.',
                ], 400);
            }

            $quoteMapByOrderNumber = $this->getInvoiceQuoteMap($user->id, $payable);
            $blockedInvoices = $payable
                ->filter(function (Invoice $invoice) use ($quoteMapByOrderNumber) {
                    $quote = $quoteMapByOrderNumber[(string) ($invoice->order_number ?? '')] ?? null;
                    return $this->isQuoteExpiredForPayment($quote);
                })
                ->pluck('invoice_number')
                ->values()
                ->all();

            if (!empty($blockedInvoices)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some invoices are linked to expired approved quotes. Re-open those quotes and submit for approval again.',
                    'data' => ['blocked_invoice_numbers' => $blockedInvoices],
                ], 422);
            }

            $customerId = $this->ensureStripeCustomer($user, $stripe);
            $customer = $stripe->customers->retrieve($customerId, []);
            $defaultPaymentMethod = (string) ($customer->invoice_settings->default_payment_method ?? '');

            if ($defaultPaymentMethod === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'No default saved card found. Add and set a default card in Account.',
                ], 422);
            }

            $totalRemaining = $payable->sum(function (Invoice $invoice) {
                return max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
            });

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (int) round($totalRemaining * 100),
                'currency' => 'usd',
                'customer' => $customerId,
                'payment_method' => $defaultPaymentMethod,
                'off_session' => true,
                'confirm' => true,
                'metadata' => [
                    'invoice_numbers' => implode(',', $payable->pluck('invoice_number')->all()),
                    'invoice_count' => (string) $payable->count(),
                    'user_id' => (string) $user->id,
                    'payment_mode' => 'default_saved_card_bulk',
                ],
            ]);

            foreach ($payable as $invoice) {
                $this->markInvoiceAsPaid($invoice, (string) ($paymentIntent->id ?? ''));
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoices paid successfully with your default card.',
                'data' => [
                    'payment_intent_id' => $paymentIntent->id ?? null,
                    'invoice_numbers' => $payable->pluck('invoice_number')->all(),
                ],
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            $code = (string) ($e->getStripeCode() ?? 'card_error');
            return response()->json([
                'success' => false,
                'message' => 'Default card charge could not be completed. Please use checkout flow to authenticate this payment.',
                'code' => $code,
            ], 402);
        } catch (\Throwable $e) {
            Log::error('Failed to pay invoices with default card: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process bulk payment with default card.',
            ], 500);
        }
    }

    private function getPublishableKey(): ?string
    {
        return config('services.stripe.key');
    }

    private function buildStripeClient(): ?StripeClient
    {
        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            return null;
        }

        return new StripeClient($stripeSecret);
    }

    private function ensureStripeCustomer(User $user, StripeClient $stripe): string
    {
        $customerId = (string) ($user->stripe_customer_id ?? '');

        if ($customerId !== '') {
            try {
                $customer = $stripe->customers->retrieve($customerId, []);
                if (!empty($customer->id)) {
                    return $customer->id;
                }
            } catch (\Throwable $e) {
                Log::warning('Stripe customer lookup failed, creating a new customer.', [
                    'user_id' => $user->id,
                    'old_customer_id' => $customerId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $customer = $stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => (string) $user->id,
            ],
        ]);

        $user->stripe_customer_id = $customer->id;
        $user->save();

        return (string) $customer->id;
    }

    private function getPaymentMethodsPayload(StripeClient $stripe, string $customerId): array
    {
        $customer = $stripe->customers->retrieve($customerId, []);
        $defaultPaymentMethodId = (string) ($customer->invoice_settings->default_payment_method ?? '');

        $methods = $stripe->paymentMethods->all([
            'customer' => $customerId,
            'type' => 'card',
        ]);

        $cards = [];
        foreach ($methods->data as $method) {
            $cards[] = [
                'id' => $method->id,
                'brand' => $method->card->brand ?? 'card',
                'last4' => $method->card->last4 ?? '----',
                'exp_month' => $method->card->exp_month ?? null,
                'exp_year' => $method->card->exp_year ?? null,
                'is_default' => $defaultPaymentMethodId !== '' && $defaultPaymentMethodId === $method->id,
            ];
        }

        return [
            'default_payment_method_id' => $defaultPaymentMethodId !== '' ? $defaultPaymentMethodId : null,
            'cards' => $cards,
        ];
    }

    public function listSavedPaymentMethods(Request $request): JsonResponse
    {
        $user = $request->user();
        $stripe = $this->buildStripeClient();
        $publishableKey = $this->getPublishableKey();

        if (!$stripe || !$publishableKey) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        try {
            $customerId = (string) ($user->stripe_customer_id ?? '');
            if ($customerId === '') {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'consent' => (bool) ($user->payment_methods_consent ?? false),
                        'publishable_key' => $publishableKey,
                        'default_payment_method_id' => null,
                        'cards' => [],
                    ],
                ]);
            }

            $paymentData = $this->getPaymentMethodsPayload($stripe, $customerId);

            return response()->json([
                'success' => true,
                'data' => array_merge($paymentData, [
                    'consent' => (bool) ($user->payment_methods_consent ?? false),
                    'publishable_key' => $publishableKey,
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to list saved payment methods: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load saved payment methods.',
            ], 500);
        }
    }

    public function createSetupIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consent' => ['required', 'boolean'],
        ]);

        if (!$validated['consent']) {
            return response()->json([
                'success' => false,
                'message' => 'Consent is required to save card details.',
            ], 422);
        }

        $user = $request->user();
        $stripe = $this->buildStripeClient();
        $publishableKey = $this->getPublishableKey();

        if (!$stripe || !$publishableKey) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        try {
            $customerId = $this->ensureStripeCustomer($user, $stripe);
            $setupIntent = $stripe->setupIntents->create([
                'customer' => $customerId,
                'usage' => 'off_session',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => (string) $user->id,
                ],
            ]);

            $user->payment_methods_consent = true;
            $user->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'client_secret' => $setupIntent->client_secret,
                    'publishable_key' => $publishableKey,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to create setup intent: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize secure card setup.',
            ], 500);
        }
    }

    public function attachPaymentMethod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_method_id' => ['required', 'string'],
            'set_default' => ['nullable', 'boolean'],
            'consent' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $stripe = $this->buildStripeClient();

        if (!$stripe) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        try {
            $customerId = $this->ensureStripeCustomer($user, $stripe);

            try {
                $stripe->paymentMethods->attach($validated['payment_method_id'], [
                    'customer' => $customerId,
                ]);
            } catch (\Throwable $e) {
                $message = strtolower($e->getMessage());
                if (!str_contains($message, 'already') && !str_contains($message, 'attached')) {
                    throw $e;
                }
            }

            if (($validated['set_default'] ?? true) === true) {
                $stripe->customers->update($customerId, [
                    'invoice_settings' => [
                        'default_payment_method' => $validated['payment_method_id'],
                    ],
                ]);
            }

            if (($validated['consent'] ?? true) === true) {
                $user->payment_methods_consent = true;
                $user->save();
            }

            $paymentData = $this->getPaymentMethodsPayload($stripe, $customerId);

            return response()->json([
                'success' => true,
                'message' => 'Payment method saved securely.',
                'data' => array_merge($paymentData, [
                    'consent' => (bool) ($user->payment_methods_consent ?? false),
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to attach payment method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save payment method.',
            ], 500);
        }
    }

    public function updateDefaultPaymentMethod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_method_id' => ['required', 'string'],
        ]);

        $user = $request->user();
        $stripe = $this->buildStripeClient();

        if (!$stripe) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        try {
            $customerId = $this->ensureStripeCustomer($user, $stripe);
            $stripe->customers->update($customerId, [
                'invoice_settings' => [
                    'default_payment_method' => $validated['payment_method_id'],
                ],
            ]);

            $paymentData = $this->getPaymentMethodsPayload($stripe, $customerId);

            return response()->json([
                'success' => true,
                'message' => 'Default payment method updated.',
                'data' => array_merge($paymentData, [
                    'consent' => (bool) ($user->payment_methods_consent ?? false),
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to update default payment method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update default payment method.',
            ], 500);
        }
    }

    public function removePaymentMethod(Request $request, string $paymentMethodId): JsonResponse
    {
        $user = $request->user();
        $stripe = $this->buildStripeClient();

        if (!$stripe) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        try {
            $customerId = $this->ensureStripeCustomer($user, $stripe);
            $customer = $stripe->customers->retrieve($customerId, []);
            $currentDefault = (string) ($customer->invoice_settings->default_payment_method ?? '');

            $stripe->paymentMethods->detach($paymentMethodId, []);

            if ($currentDefault !== '' && $currentDefault === $paymentMethodId) {
                $remaining = $stripe->paymentMethods->all([
                    'customer' => $customerId,
                    'type' => 'card',
                    'limit' => 1,
                ]);

                $nextDefault = $remaining->data[0]->id ?? null;
                $stripe->customers->update($customerId, [
                    'invoice_settings' => [
                        'default_payment_method' => $nextDefault,
                    ],
                ]);
            }

            $paymentData = $this->getPaymentMethodsPayload($stripe, $customerId);

            return response()->json([
                'success' => true,
                'message' => 'Payment method removed.',
                'data' => array_merge($paymentData, [
                    'consent' => (bool) ($user->payment_methods_consent ?? false),
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to remove payment method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove payment method.',
            ], 500);
        }
    }

    public function updatePaymentMethodConsent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consent' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $user->payment_methods_consent = (bool) $validated['consent'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment method consent updated.',
            'data' => [
                'consent' => (bool) $user->payment_methods_consent,
            ],
        ]);
    }

    private function isQuoteExpiredForPayment(?Quote $quote): bool
    {
        if (!$quote) {
            return false;
        }

        if ($quote->status === 'expired') {
            return true;
        }

        return $quote->status === 'approved'
            && $quote->expires_at
            && $quote->expires_at->isPast();
    }

    private function getInvoiceQuoteMap($userId, $invoices): array
    {
        $orderNumbers = $invoices
            ->pluck('order_number')
            ->filter()
            ->unique()
            ->values();

        if ($orderNumbers->isEmpty()) {
            return [];
        }

        $orderRows = Order::query()
            ->select(['order_number', 'quote_id'])
            ->whereIn('order_number', $orderNumbers)
            ->get();

        $quoteIds = $orderRows
            ->pluck('quote_id')
            ->filter()
            ->unique()
            ->values();

        if ($quoteIds->isEmpty()) {
            return [];
        }

        $quotesById = Quote::query()
            ->where('user_id', $userId)
            ->whereIn('quote_id', $quoteIds)
            ->get()
            ->keyBy('quote_id');

        $invoiceQuoteMap = [];
        foreach ($orderRows as $orderRow) {
            if (!empty($orderRow->order_number) && !empty($orderRow->quote_id)) {
                $invoiceQuoteMap[(string) $orderRow->order_number] = $quotesById->get((string) $orderRow->quote_id);
            }
        }

        return $invoiceQuoteMap;
    }

    public function createBulkInvoiceCheckoutSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_numbers' => ['required', 'array', 'min:1'],
            'invoice_numbers.*' => ['required', 'string'],
        ]);

        $user = $request->user();
        $invoiceNumbers = array_values(array_unique(array_map('trim', $validated['invoice_numbers'])));

        $invoices = Invoice::where('user_id', $user->id)
            ->whereIn('invoice_number', $invoiceNumbers)
            ->get();

        $foundNumbers = $invoices->pluck('invoice_number')->all();
        $missing = array_values(array_diff($invoiceNumbers, $foundNumbers));
        if (!empty($missing)) {
            return response()->json([
                'success' => false,
                'message' => 'Some invoices were not found for your account.',
                'data' => ['missing' => $missing],
            ], 404);
        }

        $payable = $invoices->filter(function (Invoice $invoice) {
            if ($invoice->status === 'paid') {
                return false;
            }

            $remaining = max(0, (float)$invoice->total_amount - (float)$invoice->paid_amount);
            return $remaining > 0;
        })->values();

        $quoteMapByOrderNumber = $this->getInvoiceQuoteMap($user->id, $payable);
        $blockedInvoices = $payable
            ->filter(function (Invoice $invoice) use ($quoteMapByOrderNumber) {
                $quote = $quoteMapByOrderNumber[(string) ($invoice->order_number ?? '')] ?? null;
                return $this->isQuoteExpiredForPayment($quote);
            })
            ->pluck('invoice_number')
            ->values()
            ->all();

        if (!empty($blockedInvoices)) {
            return response()->json([
                'success' => false,
                'message' => 'Some invoices are linked to expired approved quotes. Re-open those quotes and submit for approval again.',
                'data' => ['blocked_invoice_numbers' => $blockedInvoices],
            ], 422);
        }

        if ($payable->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No payable balance found for selected invoices.',
            ], 400);
        }

        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        $stripe = new StripeClient($stripeSecret);
        $customerId = $this->ensureStripeCustomer($user, $stripe);
        $totalRemaining = $payable->sum(function (Invoice $invoice) {
            return max(0, (float)$invoice->total_amount - (float)$invoice->paid_amount);
        });

        $amountCents = (int) round($totalRemaining * 100);
        $metadataNumbers = $payable->pluck('invoice_number')->all();
        $metadataPreview = implode(', ', array_slice($metadataNumbers, 0, 3));
        if (count($metadataNumbers) > 3) {
            $metadataPreview .= ' +' . (count($metadataNumbers) - 3) . ' more';
        }

        // Single consolidated line item so combined invoices appear as one at checkout.
        $lineItems = [[
            'quantity' => 1,
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $amountCents,
                'product_data' => [
                    'name' => 'Combined invoice payment (' . count($metadataNumbers) . ' invoices)',
                    'description' => $metadataPreview,
                ],
            ],
        ]];
        $joinedNumbers = implode(',', $metadataNumbers);

        $frontendBase = FrontendUrl::base();
        $successUrl = $frontendBase . '/invoices?stripe=success';
        $cancelUrl  = $frontendBase . '/invoices?stripe=cancel';

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'metadata' => [
                'invoice_numbers' => $joinedNumbers,
                'invoice_count' => (string) count($metadataNumbers),
                'user_id' => (string)$user->id,
            ],
        ]);

        Invoice::whereIn('id', $payable->pluck('id')->all())->update([
            'stripe_checkout_session_id' => $session->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $session->id,
                'session_url' => $session->url,
                'invoice_numbers' => $metadataNumbers,
            ],
        ]);
    }

    public function createInvoiceCheckoutSession(Request $request, string $invoiceNumber): JsonResponse
    {
        $user = $request->user();
        $invoice = Invoice::where('user_id', $user->id)
            ->where('invoice_number', $invoiceNumber)
            ->firstOrFail();

        $quoteMapByOrderNumber = $this->getInvoiceQuoteMap($user->id, collect([$invoice]));
        $quote = $quoteMapByOrderNumber[(string) ($invoice->order_number ?? '')] ?? null;
        if ($this->isQuoteExpiredForPayment($quote)) {
            if ($quote && $quote->status !== 'expired') {
                $quote->update(['status' => 'expired']);
            }

            return response()->json([
                'success' => false,
                'message' => 'This quote has expired for payment. Re-open the quote and submit it for approval again.',
            ], 422);
        }

        if ($invoice->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is already paid.',
            ], 400);
        }

        $remaining = max(0, (float)$invoice->total_amount - (float)$invoice->paid_amount);
        if ($remaining <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No remaining balance on this invoice.',
            ], 400);
        }

        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured.',
            ], 500);
        }

        $stripe = new StripeClient($stripeSecret);
        $customerId = $this->ensureStripeCustomer($user, $stripe);
        $amountCents = (int)round($remaining * 100);

        $frontendBase = FrontendUrl::base();
        $successUrl = $frontendBase . '/invoices?stripe=success&invoice=' . urlencode($invoice->invoice_number);
        $cancelUrl  = $frontendBase . '/invoices?stripe=cancel&invoice=' . urlencode($invoice->invoice_number);

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $amountCents,
                    'product_data' => [
                        'name' => 'Invoice ' . $invoice->invoice_number,
                        'description' => 'Order ' . ($invoice->order_number ?? 'N/A'),
                    ],
                ],
            ]],
            'metadata' => [
                'invoice_number' => $invoice->invoice_number,
                'user_id' => (string)$invoice->user_id,
                'order_number' => (string)($invoice->order_number ?? ''),
            ],
        ]);

        $invoice->update([
            'stripe_checkout_session_id' => $session->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $session->id,
                'session_url' => $session->url,
            ],
        ]);
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            if (!$secret) {
                throw new \Exception('Stripe webhook secret not configured.');
            }

            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature error: ' . $e->getMessage());
            return response()->json(['success' => false], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $invoiceNumber = $session->metadata->invoice_number ?? null;
            $invoiceNumbersRaw = $session->metadata->invoice_numbers ?? null;
            $paymentIntentId = $session->payment_intent ?? null;

            if ($invoiceNumbersRaw) {
                $invoiceNumbers = array_filter(array_map('trim', explode(',', (string)$invoiceNumbersRaw)));

                if (!empty($invoiceNumbers)) {
                    $invoices = Invoice::whereIn('invoice_number', $invoiceNumbers)->get();

                    foreach ($invoices as $invoice) {
                        if (!$invoice instanceof Invoice) {
                            continue;
                        }

                        $this->markInvoiceAsPaid($invoice, $paymentIntentId ? (string) $paymentIntentId : null);
                    }
                }
            }

            if ($invoiceNumber) {
                $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
                if ($invoice) {
                    $this->markInvoiceAsPaid($invoice, $paymentIntentId ? (string) $paymentIntentId : null);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}
