<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeController extends Controller
{
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

        $successUrl = config('app.url') . '/invoices?stripe=success';
        $cancelUrl = config('app.url') . '/invoices?stripe=cancel';

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
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
        $amountCents = (int)round($remaining * 100);

        $successUrl = config('app.url') . '/invoices?stripe=success&invoice=' . urlencode($invoice->invoice_number);
        $cancelUrl = config('app.url') . '/invoices?stripe=cancel&invoice=' . urlencode($invoice->invoice_number);

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
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
                        if ($invoice->status === 'paid') {
                            continue;
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
                    }
                }
            }

            if ($invoiceNumber) {
                $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
                if ($invoice && $invoice->status !== 'paid') {
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
                }
            }
        }

        return response()->json(['success' => true]);
    }
}
