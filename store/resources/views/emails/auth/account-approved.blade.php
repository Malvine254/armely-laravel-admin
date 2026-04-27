<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Segoe UI,Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:32px;">

        <div style="text-align:center;margin-bottom:24px;">
            <div style="display:inline-block;background:#d1fae5;border-radius:50%;width:56px;height:56px;line-height:56px;font-size:28px;">&#10003;</div>
        </div>

        <h2 style="margin-top:0;color:#2F5597;text-align:center;">Your account has been approved!</h2>

        <p>Hello {{ $user->name }},</p>

        <p>
            Great news — your <strong>{{ config('app.name') }}</strong> account has been reviewed and approved
            by our team. You can now log in and start using the platform.
        </p>

        <div style="background:#edf3fb;border:1px solid #d9e6f7;border-radius:10px;padding:16px;margin:24px 0;">
            <p style="margin:0 0 6px;"><strong>Account:</strong> {{ $user->email }}</p>
            @if($user->company)
            <p style="margin:0;"><strong>Company:</strong> {{ $user->company->name }}</p>
            @endif
        </div>

        <p>With your approved account you can:</p>
        <ul style="padding-left:20px;line-height:1.8;">
            <li>Request and manage quotes</li>
            <li>Place and track orders</li>
            <li>View invoices and payment history</li>
            <li>Chat with our Mela AI assistant</li>
        </ul>

        <p style="margin:28px 0 8px;text-align:center;">
            <a href="{{ config('app.url') }}/login"
               style="background:#2F5597;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:8px;display:inline-block;font-weight:600;">
                Log in to your account
            </a>
        </p>

        <p style="margin-top:28px;color:#64748b;font-size:12px;">
            @php $supportEmail = \App\Models\AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')) @endphp
            If you have any questions, contact us at <a href="mailto:{{ $supportEmail }}" style="color:#2F5597;">{{ $supportEmail }}</a>.<br>
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
