<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        :root {
            --brand-primary: #2F5597;
            --brand-primary-dark: #1f4788;
            --brand-primary-soft: #edf3fb;
            --brand-border: #d9e6f7;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            padding: 30px;
        }
        .header {
            border-bottom: 3px solid var(--brand-primary);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
        }
        .header-left {
            width: 60%;
        }
        .header-right {
            width: 40%;
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: var(--brand-primary);
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: var(--brand-primary-dark);
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .info-value {
            color: #111827;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: var(--brand-primary-soft);
            color: var(--brand-primary-dark);
            font-weight: bold;
            text-align: left;
            padding: 12px 8px;
            border-bottom: 2px solid var(--brand-border);
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .item-name {
            font-weight: 600;
            color: #111827;
        }
        .item-sku {
            color: #6b7280;
            font-size: 10px;
            margin-top: 2px;
        }
        .text-right {
            text-align: right;
        }
        .totals-section {
            margin-top: 30px;
            float: right;
            width: 300px;
        }
        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .totals-label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            color: #6b7280;
        }
        .totals-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            width: 120px;
        }
        .grand-total {
            border-top: 2px solid #d1d5db;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 16px;
            color: var(--brand-primary);
        }
        .payment-info {
            margin-top: 40px;
            background-color: var(--brand-primary-soft);
            border-left: 4px solid var(--brand-primary);
            padding: 15px;
            clear: both;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-issued {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="company-name">Armely Store</div>
                    <div>IT Hardware & Cloud Solutions Provider</div>
                </div>
                <div class="header-right">
                    <div class="document-title">INVOICE</div>
                    <div style="margin-top: 10px;">
                        <span class="status-badge status-{{ strtolower($invoice->status) }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice and Customer Information -->
        <div class="info-grid">
            <div class="info-column">
                <div class="info-label">Invoice Number</div>
                <div class="info-value">{{ $invoice->invoice_number }}</div>

                <div class="info-label">Invoice Date</div>
                <div class="info-value">{{ $invoice->created_at->format('F d, Y') }}</div>

                @if($invoice->due_at)
                <div class="info-label">Due Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($invoice->due_at)->format('F d, Y') }}</div>
                @endif

                @if($order)
                <div class="info-label">Order Number</div>
                <div class="info-value">{{ $order->order_number }}</div>
                @endif
            </div>

            <div class="info-column">
                <div class="info-label">Bill To</div>
                <div class="info-value">{{ $user->name }}</div>

                <div class="info-label">Company</div>
                <div class="info-value">{{ $company->name ?? 'N/A' }}</div>

                <div class="info-label">Email</div>
                <div class="info-value">{{ $user->email }}</div>

                @if($user->phone)
                <div class="info-label">Phone</div>
                <div class="info-value">{{ $user->phone }}</div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Item #</th>
                    <th style="width: 40%;">Product</th>
                    <th style="width: 15%;" class="text-right">Qty</th>
                    <th style="width: 17.5%;" class="text-right">Unit Price (Incl. Tax)</th>
                    <th style="width: 17.5%;" class="text-right">Total (Incl. Tax)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $invoiceTax = (float) ($invoice->tax_amount ?? 0);
                    $preTaxSubtotal = collect($items)->sum(function ($item) {
                        return (float) ($item['line_total'] ?? 0);
                    });
                    $runningTax = 0.0;
                @endphp
                @foreach($items as $index => $item)
                @php
                    $qty = max(1, (int) ($item['quantity'] ?? 1));
                    $linePreTax = (float) ($item['line_total'] ?? (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0)));
                    $isLast = $index === count($items) - 1;
                    $lineTax = $invoiceTax > 0
                        ? ($isLast
                            ? round($invoiceTax - $runningTax, 2)
                            : round(($preTaxSubtotal > 0 ? ($invoiceTax * $linePreTax) / $preTaxSubtotal : ($invoiceTax / max(1, count($items)))), 2)
                        )
                        : 0.0;
                    $runningTax = round($runningTax + $lineTax, 2);
                    $lineTotalWithTax = round($linePreTax + $lineTax, 2);
                    $unitWithTax = round($lineTotalWithTax / $qty, 2);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item['product_name'] ?? $item['name'] ?? 'N/A' }}</div>
                        @if(isset($item['mfg_part_number']))
                        <div class="item-sku">SKU: {{ $item['mfg_part_number'] }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ $qty }}</td>
                    <td class="text-right">${{ number_format($unitWithTax, 2) }}</td>
                    <td class="text-right">${{ number_format($lineTotalWithTax, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">${{ number_format($subtotal ?? 0, 2) }}</div>
            </div>
            
            @if(isset($invoice->tax_amount) && $invoice->tax_amount > 0)
            <div class="totals-row">
                <div class="totals-label">Tax:</div>
                <div class="totals-value">${{ number_format($invoice->tax_amount, 2) }}</div>
            </div>
            @endif

            <div class="totals-row grand-total">
                <div class="totals-label">Amount Due:</div>
                <div class="totals-value">${{ number_format($invoice->total_amount ?? 0, 2) }}</div>
            </div>

            @if(isset($invoice->paid_amount) && $invoice->paid_amount > 0)
            <div class="totals-row">
                <div class="totals-label">Paid:</div>
                <div class="totals-value">-${{ number_format($invoice->paid_amount, 2) }}</div>
            </div>
            <div class="totals-row grand-total">
                <div class="totals-label">Balance Due:</div>
                <div class="totals-value">${{ number_format(($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0), 2) }}</div>
            </div>
            @endif
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <strong>Payment Information:</strong><br>
            Payment terms: Net 30 days from invoice date.<br>
            Please include invoice number {{ $invoice->invoice_number }} with your payment.<br>
            For payment inquiries, contact: billing@armely.com
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Notes:</strong></p>
            <p>• Thank you for your business!</p>
            <p>• Please retain this invoice for your records.</p>
            <p>• For questions about this invoice, please contact us at unfo@armely.com</p>
            <br>
            <p style="text-align: center;">Generated on {{ $generatedDate }}</p>
        </div>
    </div>
</body>
</html>
