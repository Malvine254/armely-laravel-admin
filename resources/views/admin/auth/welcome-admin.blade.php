<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Armely Admin</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:28px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Armely Admin Access</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:24px;line-height:1.2;">Welcome to Armely Admin</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px 22px;">
                        <p style="margin:0 0 14px;font-size:16px;line-height:1.6;">Hello {{ e($name ?? 'Admin') }},</p>
                        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#324a73;">Your administrator account is ready. Activate your access by creating your password from the secure link below.</p>
                        <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 14px;">
                            <tr>
                                <td align="center" style="border-radius:10px;background:#1f4d99;">
                                    <a href="{{ $activationLink }}" style="display:inline-block;padding:14px 22px;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;border-radius:10px;">Activate Your Account</a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:0 0 12px;font-size:14px;line-height:1.7;color:#4f6690;">After activation, sign in to your admin workspace:</p>
                        <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 14px;">
                            <tr>
                                <td align="center" style="border-radius:10px;background:#1a3f7f;">
                                    <a href="{{ $loginLink }}" style="display:inline-block;padding:13px 20px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;border-radius:10px;">Go To Admin Login</a>
                                </td>
                            </tr>
                        </table>
                        <div style="margin:0 0 14px;padding:12px 14px;background:#fff7df;border:1px solid #f6dda5;border-radius:10px;">
                            <p style="margin:0;color:#7a5a13;font-size:13px;line-height:1.6;"><strong>Security notice:</strong> This activation link expires in 60 minutes.</p>
                        </div>
                        <div style="margin-top:18px;padding-top:18px;border-top:1px solid #e5edff;">
                            <p style="margin:0 0 6px;font-size:13px;color:#5e7399;"><strong>Button not working?</strong> Copy this activation link into your browser:</p>
                            <p style="margin:0;font-size:13px;line-height:1.6;word-break:break-all;"><a href="{{ $activationLink }}" style="color:#2f5597;">{{ $activationLink }}</a></p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 30px 24px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;color:#6d81a4;font-size:12px;line-height:1.6;">Armely Admin Panel | Automated notification. Please do not reply.</p>
                        <p style="margin:8px 0 0;color:#96a7c0;font-size:12px;line-height:1.5;">© {{ date('Y') }} Armely. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
