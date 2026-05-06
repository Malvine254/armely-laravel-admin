<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Account Invitation</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Segoe UI,Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#2F5597,#1e3a6b);padding:24px 32px;">
            <h2 style="margin:0;color:#ffffff;font-size:20px;">Armely Store</h2>
            <p style="margin:4px 0 0;color:#c7d7f5;font-size:13px;">Customer Account Invitation</p>
        </div>

        <div style="padding:32px;">
            <p style="margin-top:0;">Hello <strong>{{ $customer->name }}</strong>,</p>
            <p>An Armely Store customer account has been created for you. Use the temporary credentials below to sign in.</p>

            <div style="background:#f1f5fb;border:1px solid #c8d8f5;border-radius:8px;padding:20px;margin:24px 0;">
                <p style="margin:0 0 10px;font-size:13px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;font-weight:600;">Your Login Details</p>
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:6px 0;font-size:14px;color:#6b7280;width:120px;">Email</td>
                        <td style="padding:6px 0;font-size:14px;font-weight:600;color:#1f2937;">{{ $customer->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;font-size:14px;color:#6b7280;">Password</td>
                        <td style="padding:6px 0;font-size:14px;font-weight:600;color:#1f2937;font-family:monospace;">{{ $plainPassword }}</td>
                    </tr>
                    @if($customer->company)
                        <tr>
                            <td style="padding:6px 0;font-size:14px;color:#6b7280;">Company</td>
                            <td style="padding:6px 0;font-size:14px;font-weight:600;color:#1f2937;">{{ $customer->company->name }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <div style="background:#fff8e1;border:1px solid #f6c948;border-radius:8px;padding:14px 18px;margin-bottom:24px;">
                <p style="margin:0;font-size:13px;color:#92610a;">
                    <strong>Important:</strong> You will be asked to change this temporary password after signing in. The temporary password expires in 48 hours.
                </p>
            </div>

            <p style="margin:24px 0;">
                <a href="{{ rtrim(\App\Support\FrontendUrl::base(), '/') }}/login"
                   style="background:linear-gradient(90deg,#2F5597,#1e3a6b);color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;display:inline-block;font-weight:600;font-size:14px;">
                    Sign In
                </a>
            </p>

            <p style="color:#6b7280;font-size:13px;">If you did not expect this invitation, please contact Armely support.</p>
        </div>

        <div style="padding:16px 32px;background:#f8fafc;border-top:1px solid #e2e8f0;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">Armely Store</p>
        </div>
    </div>
</body>
</html>
