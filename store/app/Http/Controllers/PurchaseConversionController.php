<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseConversionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $input = $request->validate([
            'invoice_numbers' => ['required', 'array', 'min:1', 'max:100'],
            'invoice_numbers.*' => ['required', 'string', 'max:255'],
        ]);

        $purchases = Invoice::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('invoice_number', $input['invoice_numbers'])
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->where('total_amount', '>', 0)
            ->whereColumn('paid_amount', '>=', 'total_amount')
            ->get()
            ->map(function (Invoice $invoice) {
                // Invoice amounts are stored in USD; display currency conversion is separate.
                $tax = round((float) $invoice->tax_amount, 2);
                $shipping = $invoice->shipping_amount;
                $value = round(max(0, (float) $invoice->total_amount - $tax - $shipping), 2);

                return [
                    'transaction_id' => 'armely-store-invoice-'.$invoice->id,
                    'currency' => 'USD',
                    'value' => $value,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => (float) $invoice->total_amount,
                    'items' => [[
                        'item_id' => (string) $invoice->invoice_number,
                        'item_name' => 'Store order '.$invoice->order_number,
                        'price' => $value,
                        'quantity' => 1,
                    ]],
                ];
            })->values();

        return response()->json(['purchases' => $purchases])->header('Cache-Control', 'no-store');
    }
}
