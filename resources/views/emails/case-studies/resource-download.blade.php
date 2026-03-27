<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Secure Armely Download</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:28px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Armely Resources</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:26px;line-height:1.2;">Your Secure Download Link Is Ready</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px 22px;">
                        <p style="margin:0 0 14px;font-size:16px;line-height:1.55;">Hello {{ e($name) }},</p>
                        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#324a73;">Thank you for your interest in Armely. Your requested {{ e($resourceTypeLabel) }} is ready to download.</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 18px;background:#f5f8ff;border:1px solid #dbe7ff;border-radius:12px;">
                            <tr>
                                <td style="padding:14px 16px;">
                                    <p style="margin:0 0 6px;color:#1f3a69;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Requested Resource</p>
                                    <p style="margin:0;color:#233f71;font-size:16px;font-weight:700;">{{ e($resourceTitle) }}</p>
                                    <p style="margin:10px 0 0;color:#6b7fa2;font-size:13px;">This secure link expires at {{ e($expiresAt) }}.</p>
                                </td>
                            </tr>
                        </table>
                        <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 18px;">
                            <tr>
                                <td align="center" style="border-radius:10px;background:#1f4d99;">
                                    <a href="{{ e($downloadUrl) }}" style="display:inline-block;padding:14px 22px;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;border-radius:10px;">Download Your Resource</a>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:0 0 10px;font-size:13px;line-height:1.65;color:#5b7096;">For your security, this link is time-limited and cannot be reused after expiry. If the link expires, you can request a fresh one from the Case Studies page.</p>
                        <p style="margin:0;font-size:15px;line-height:1.65;color:#2f4468;">Warm regards,<br><strong>Armely Team</strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 30px 24px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;color:#7b8fad;font-size:12px;line-height:1.5;">If you did not request this file, please ignore this email.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
