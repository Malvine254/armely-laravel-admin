<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
        }
        header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .company-info h1 {
            color: #007bff;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .company-info p {
            font-size: 12px;
            color: #666;
        }
        .invoice-meta {
            text-align: right;
        .company-logo {
            max-width: 190px;
            height: auto;
            margin-bottom: 8px;
            display: block;
        }
        }
        .invoice-meta h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }
        .invoice-meta-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .invoice-meta-row strong {
            margin-right: 15px;
            width: 100px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .bill-to, .ship-to {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .bill-to {
            margin-right: 2%;
        }
        .address-block p {
            font-size: 13px;
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: bold;
            color: #333;
        }
        td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .totals-section {
            width: 50%;
            margin-left: 50%;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
            border-bottom: 1px solid #ddd;
        }
        .total-row.grand-total {
            border-bottom: 2px solid #333;
            padding: 12px 0;
            font-weight: bold;
            font-size: 16px;
            color: #007bff;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 3px solid #007bff;
            font-size: 12px;
        }
        .notes h4 {
            margin-bottom: 8px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="company-info">
                <img src="{{ 'file://' . public_path('images/logo/armely-store-logo.png') }}" alt="Armely Store" class="company-logo">
                <p>Your B2B Hardware Partner</p>
            </div>
            <div class="invoice-meta">
                <h2>INVOICE</h2>
                <div class="invoice-meta-row">
                    <strong>Invoice #:</strong>
                    <span>{{ $invoice->invoice_number }}</span>
                </div>
                <div class="invoice-meta-row">
                    <strong>Date:</strong>
                    <span>{{ $invoice->issued_at->format('M d, Y') }}</span>
                </div>
                <div class="invoice-meta-row">
                    <strong>Due Date:</strong>
                    <span>{{ $invoice->due_at?->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="invoice-meta-row">
                    <strong>Status:</strong>
                    <span class="status-badge {{ $invoice->status === 'paid' ? 'status-paid' : 'status-pending' }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </div>
            </div>
        </header>

        <!-- Bill To / Ship To -->
        <div class="section">
            <div class="bill-to">
                <h3>Bill To</h3>
                <div class="address-block">
                    <p><strong>{{ $user->name }}</strong></p>
                    <p>{{ $user->company?->name ?? 'Unknown Company' }}</p>
                    @if($order && $order->billingAddress)
                        <p>{{ $order->billingAddress->street_1 }}<br/>
                        @if($order->billingAddress->street_2)
                            {{ $order->billingAddress->street_2 }}<br/>
                        @endif
                        {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}<br/>
                        {{ $order->billingAddress->country }}
                        </p>
                        @if($order->billingAddress->contact_phone)
                            <p>{{ $order->billingAddress->contact_phone }}</p>
                        @endif
                    @else
                        <p>Address not provided</p>
                    @endif
                </div>
            </div>

            <div class="ship-to">
                <h3>Ship To</h3>
                <div class="address-block">
                    <p><strong>{{ $user->name }}</strong></p>
                    <p>{{ $user->company?->name ?? 'Unknown Company' }}</p>
                    @if($order && $order->shippingAddress)
                        <p>{{ $order->shippingAddress->street_1 }}<br/>
                        @if($order->shippingAddress->street_2)
                            {{ $order->shippingAddress->street_2 }}<br/>
                        @endif
                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br/>
                        {{ $order->shippingAddress->country }}
                        </p>
                        @if($order->shippingAddress->contact_phone)
                            <p>{{ $order->shippingAddress->contact_phone }}</p>
                        @endif
                    @else
                        <p>Address not provided</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="section">
            <h3>Invoice Items</h3>
            @php
                $invoiceItems = is_array($invoice->items) ? $invoice->items : [];
                $breakdown = is_array($invoice->raw_data) ? ($invoice->raw_data['invoice_charge_breakdown'] ?? []) : [];
                $shippingAmount = (float) ($breakdown['shipping_amount'] ?? 0);
                $targetSubtotal = (float) ($breakdown['subtotal'] ?? max(0, ($invoice->total_amount ?? 0) - ($invoice->tax_amount ?? 0) - $shippingAmount));

                $normalizedItems = array_map(function ($item, $idx) {
                    $qty = max(1, (int) ($item['quantity'] ?? 1));
                    $unit = (float) ($item['unit_price'] ?? $item['unitPrice'] ?? $item['price'] ?? 0);
                    $line = (float) ($item['line_total'] ?? $item['lineTotal'] ?? $item['extendedPrice'] ?? ($unit * $qty));

                    return [
                        'name' => $item['product_name'] ?? $item['productName'] ?? $item['name'] ?? $item['description'] ?? ('Product ' . ($idx + 1)),
                        'part_number' => $item['mfg_part_number'] ?? $item['mfgPartNo'] ?? $item['partNumber'] ?? $item['sku'] ?? '',
                        'quantity' => $qty,
                        'unit_price' => round($unit, 2),
                        'line_total' => round($line, 2),
                    ];
                }, $invoiceItems, array_keys($invoiceItems));

                $hasPricing = collect($normalizedItems)->contains(function ($row) {
                    return ((float) ($row['unit_price'] ?? 0)) > 0 || ((float) ($row['line_total'] ?? 0)) > 0;
                });

                if (!$hasPricing && $targetSubtotal > 0 && count($normalizedItems) > 0) {
                    $totalQty = array_reduce($normalizedItems, function ($sum, $row) {
                        return $sum + max(1, (int) ($row['quantity'] ?? 1));
                    }, 0);

                    if ($totalQty > 0) {
                        $running = 0.0;
                        foreach ($normalizedItems as $idx => &$row) {
                            $qty = max(1, (int) ($row['quantity'] ?? 1));
                            $isLast = $idx === count($normalizedItems) - 1;
                            $line = $isLast ? round($targetSubtotal - $running, 2) : round(($targetSubtotal * $qty) / $totalQty, 2);
                            $row['line_total'] = $line;
                            $row['unit_price'] = round($line / $qty, 2);
                            $running = round($running + $line, 2);
                        }
                        unset($row);
                    }
                }
            @endphp
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Unit Price (Incl. Tax)</th>
                        <th class="text-right">Line Total (Incl. Tax)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($normalizedItems) > 0)
                        @foreach($normalizedItems as $item)
                            <tr>
                                <td>{{ $item['name'] ?? 'Product' }}</td>
                                <td class="text-right">{{ $item['quantity'] ?? 1 }}</td>
                                <td class="text-right">${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                <td class="text-right"><strong>${{ number_format($item['line_total'] ?? 0, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-right">No items found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="totals-section">
            @php
                $breakdown = is_array($invoice->raw_data) ? ($invoice->raw_data['invoice_charge_breakdown'] ?? []) : [];
                $baseSubtotal = (float) ($breakdown['base_subtotal'] ?? max(0, ($invoice->total_amount ?? 0) - ($invoice->tax_amount ?? 0)));
                $profitAmount = (float) ($breakdown['profit_amount'] ?? 0);
                $profitRate = (float) ($breakdown['profit_rate_percent'] ?? 0);
                $shippingAmount = (float) ($breakdown['shipping_amount'] ?? 0);
                $subtotalAmount = (float) ($breakdown['subtotal'] ?? max(0, ($invoice->total_amount ?? 0) - ($invoice->tax_amount ?? 0) - $shippingAmount));
                $taxRate = (float) ($breakdown['tax_rate_percent'] ?? 0);
            @endphp
            @if($profitAmount > 0)
            <div class="total-row">
                <span>Base Subtotal:</span>
                <span>${{ number_format($baseSubtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Profit ({{ number_format($profitRate, 2) }}%):</span>
                <span>${{ number_format($profitAmount, 2) }}</span>
            </div>
            @endif
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($subtotalAmount, 2) }}</span>
            </div>
            @if($shippingAmount > 0)
            <div class="total-row">
                <span>Shipping:</span>
                <span>${{ number_format($shippingAmount, 2) }}</span>
            </div>
            @endif
            <div class="total-row">
                <span>Tax ({{ number_format($taxRate > 0 ? $taxRate : ($invoice->tax_amount > 0 && $subtotalAmount > 0 ? (($invoice->tax_amount / $subtotalAmount) * 100) : 0), 2) }}%):</span>
                <span>${{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>Total:</span>
                <span>${{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            @if($invoice->paid_amount > 0)
                <div class="total-row">
                    <span>Amount Paid:</span>
                    <span>${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Amount Due:</span>
                    <span>${{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</span>
                </div>
            @endif
        </div>

        <!-- Notes -->
        @if($invoice->notes)
            <div class="notes">
                <h4>Notes</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 12px; color: #999;">
            <p>Thank you for your business! | www.armely.com | {{ \App\Models\AppSetting::getValue('system.support_email', config('mail.from.address', 'info@armely.com')) }}</p>
        </div>
    </div>
</body>
</html>
