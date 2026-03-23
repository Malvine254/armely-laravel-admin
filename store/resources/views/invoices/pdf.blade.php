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
                <h1>ARMELY STORE</h1>
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
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invoice->items && is_array($invoice->items))
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item['product_name'] ?? $item['name'] ?? 'Product' }}</td>
                                <td class="text-right">{{ $item['quantity'] ?? 1 }}</td>
                                <td class="text-right">${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                <td class="text-right"><strong>${{ number_format($item['line_total'] ?? ($item['unit_price'] * $item['quantity'] ?? 0), 2) }}</strong></td>
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
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($invoice->total_amount - $invoice->tax_amount, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Tax ({{ $invoice->tax_amount > 0 ? round(($invoice->tax_amount / ($invoice->total_amount - $invoice->tax_amount)) * 100) : 0 }}%):</span>
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
            <p>Thank you for your business! | www.armely.com | support@armely.com</p>
        </div>
    </div>
</body>
</html>
