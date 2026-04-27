<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Quote;
use App\Services\QuickBooksPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuickBooksPaymentController extends Controller
{
    public function __construct(private readonly QuickBooksPaymentService $quickBooksPaymentService)
    {
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

        try {
            $result = $this->quickBooksPaymentService->createInvoiceCheckout($invoice, $user);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
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

            return max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount) > 0;
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

        try {
            $result = $this->quickBooksPaymentService->createBulkInvoiceCheckout($payable, $user);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function listSavedPaymentMethods(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'provider' => 'quickbooks',
                'supports_saved_methods' => false,
                'consent' => false,
                'cards' => [],
                'default_payment_method_id' => null,
            ],
            'message' => 'QuickBooks payment methods are managed in hosted checkout.',
        ]);
    }

    public function payInvoiceWithDefaultCard(): JsonResponse
    {
        return $this->unsupportedSavedMethodAction();
    }

    public function payBulkInvoicesWithDefaultCard(): JsonResponse
    {
        return $this->unsupportedSavedMethodAction();
    }

    public function updatePaymentMethodConsent(): JsonResponse
    {
        return $this->unsupportedSavedMethodAction();
    }

    public function createSetupIntent(): JsonResponse
    {
        return $this->unsupportedSavedMethodAction();
    }

    public function attachPaymentMethod(): JsonResponse
    {
        return $this->unsupportedSavedMethodAction();
    }

    public function updateDefaultPaymentMethod(): JsonResponse
    {
        return $this->unsupportedSavedMethodAction();
    }

    public function removePaymentMethod(): JsonResponse
    {
        return $this->unsupportedSavedMethodAction();
    }

    private function unsupportedSavedMethodAction(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'QuickBooks saved payment methods are not managed in Armely. Start payment from the invoice to use QuickBooks checkout.',
        ], 422);
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
}