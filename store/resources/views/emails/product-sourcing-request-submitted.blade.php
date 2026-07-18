<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New procurement request</title>
</head>
<body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#102a52">
    <div style="max-width:680px;margin:0 auto;padding:32px 16px">
        <div style="background:#ffffff;border:1px solid #dbe5f1;border-radius:14px;overflow:hidden">
            <div style="background:#0b3b82;padding:24px 28px;color:#ffffff">
                <div style="font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#cfe2ff">Procurement support</div>
                <h1 style="margin:8px 0 0;font-size:24px">New custom request #{{ $sourcingRequest->id }}</h1>
            </div>
            <div style="padding:28px">
                <p style="margin-top:0">A customer submitted a new volume-pricing or custom-sourcing request.</p>
                <table role="presentation" style="width:100%;border-collapse:collapse;font-size:14px">
                    <tr><td style="padding:9px 0;color:#64748b;width:180px">Customer</td><td style="padding:9px 0;font-weight:700">{{ $sourcingRequest->user->name }}</td></tr>
                    <tr><td style="padding:9px 0;color:#64748b">Email</td><td style="padding:9px 0"><a href="mailto:{{ $sourcingRequest->user->email }}">{{ $sourcingRequest->user->email }}</a></td></tr>
                    <tr><td style="padding:9px 0;color:#64748b">Product or solution</td><td style="padding:9px 0">{{ $sourcingRequest->search_query }}</td></tr>
                    <tr><td style="padding:9px 0;color:#64748b">Manufacturer</td><td style="padding:9px 0">{{ $sourcingRequest->manufacturer ?: 'Not specified' }}</td></tr>
                    <tr><td style="padding:9px 0;color:#64748b">Model / part number</td><td style="padding:9px 0">{{ $sourcingRequest->model_or_part_number ?: 'Not specified' }}</td></tr>
                    <tr><td style="padding:9px 0;color:#64748b">Quantity</td><td style="padding:9px 0">{{ number_format($sourcingRequest->quantity) }}</td></tr>
                    <tr><td style="padding:9px 0;color:#64748b">Submitted</td><td style="padding:9px 0">{{ $sourcingRequest->created_at?->format('M j, Y g:i A T') }}</td></tr>
                </table>
                <div style="margin-top:22px;padding:18px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0">
                    <div style="margin-bottom:8px;font-size:12px;font-weight:700;text-transform:uppercase;color:#64748b">Requirements and delivery details</div>
                    <div style="white-space:pre-wrap;line-height:1.55">{{ $sourcingRequest->notes ?: 'No additional details provided.' }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
