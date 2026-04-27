<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Invoice</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; color: #111827;">
	<div style="max-width: 640px; margin: 0 auto; padding: 24px; background: #ffffff;">
		<h2 style="margin: 0 0 12px;">Invoice Ready</h2>
		<p style="margin: 0 0 16px;">Hello {{ $customer->name ?? 'Customer' }},</p>
		<p style="margin: 0 0 16px;">
			Your invoice has been generated and is ready for payment.
		</p>

		<div style="border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
			<p style="margin: 0 0 8px;"><strong>Invoice:</strong> {{ $invoice->invoice_number }}</p>
			<p style="margin: 0 0 8px;"><strong>Order:</strong> {{ $invoice->order_number }}</p>
			<p style="margin: 0 0 8px;"><strong>Total:</strong> ${{ number_format($invoice->total_amount ?? 0, 2) }}</p>
			<p style="margin: 0;"><strong>Due:</strong> {{ $invoice->due_at?->format('M d, Y') ?? 'On Demand' }}</p>
		</div>

		<a
			href="{{ config('app.url') }}/invoices"
			style="display: inline-block; padding: 10px 18px; background: #1f4b99; color: #ffffff; text-decoration: none; border-radius: 8px;"
		>
			View Invoice
		</a>

		<p style="margin: 20px 0 0; color: #6b7280; font-size: 12px;">
			If you have any questions about your invoice, please contact our accounting team.
		</p>
	</div>
</body>
</html>
