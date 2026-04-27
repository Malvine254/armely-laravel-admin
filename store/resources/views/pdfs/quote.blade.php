<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote {{ $quote->quote_id }}</title>
    <style>
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
            border-bottom: 3px solid #2563eb;
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
            color: #2563eb;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
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
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 12px 8px;
            border-bottom: 2px solid #d1d5db;
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
            color: #2563eb;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            clear: both;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-submitted {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-expired {
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
                    <div class="document-title">QUOTE</div>
                    <div style="margin-top: 10px;">
                        <span class="status-badge status-{{ strtolower($quote->status) }}">
                            {{ ucfirst($quote->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quote and Customer Information -->
        <div class="info-grid">
            <div class="info-column">
                <div class="info-label">Quote ID</div>
                <div class="info-value">{{ $quote->quote_id }}</div>

                <div class="info-label">Quote Date</div>
                <div class="info-value">{{ $quote->created_at->format('F d, Y') }}</div>

                @if($quote->expires_at)
                <div class="info-label">Valid Until</div>
                <div class="info-value">{{ $quote->expires_at->format('F d, Y') }}</div>
                @endif
            </div>

            <div class="info-column">
                <div class="info-label">Customer</div>
                <div class="info-value">{{ $user->name }}</div>

                <div class="info-label">Company</div>
                <div class="info-value">{{ $company->name ?? 'N/A' }}</div>

                <div class="info-label">Email</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Item #</th>
                    <th style="width: 40%;">Product</th>
                    <th style="width: 15%;" class="text-right">Qty</th>
                    <th style="width: 17.5%;" class="text-right">Unit Price</th>
                    <th style="width: 17.5%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item['product_name'] ?? 'N/A' }}</div>
                        @if(isset($item['mfg_part_number']))
                        <div class="item-sku">SKU: {{ $item['mfg_part_number'] }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ $item['quantity'] ?? 1 }}</td>
                    <td class="text-right">${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                    <td class="text-right">${{ number_format(($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">${{ number_format($quote->subtotal ?? 0, 2) }}</div>
            </div>
            
            @if(isset($quote->tax_amount) && $quote->tax_amount > 0)
            <div class="totals-row">
                <div class="totals-label">Tax:</div>
                <div class="totals-value">${{ number_format($quote->tax_amount, 2) }}</div>
            </div>
            @endif

            <div class="totals-row grand-total">
                <div class="totals-label">Total:</div>
                <div class="totals-value">${{ number_format($quote->total_amount ?? 0, 2) }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Terms & Conditions:</strong></p>
            <p>• This quote is valid for 30 days from the date of issue.</p>
            <p>• Prices are subject to change based on vendor pricing at the time of order.</p>
            <p>• Payment terms: Net 30 days from invoice date.</p>
            <p>• All sales are final unless otherwise specified.</p>
            <br>
            <p style="text-align: center;">Generated on {{ $generatedDate }}</p>
            <p style="text-align: center;">Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
