<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We received your request</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:24px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Armely</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:22px;line-height:1.25;">Thanks{{ $name ? ', ' . $name : '' }}. We've got your request.</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 30px;font-size:14px;line-height:1.65;color:#324a73;">
                        <p style="margin:0 0 12px;">You asked Mela AI on armely.com to have someone from our team follow up about:</p>
                        <p style="margin:0 0 12px;padding:12px 14px;background:#f4f7fc;border-radius:10px;font-weight:600;color:#1f4d99;">{{ $topic }}</p>
                        <p style="margin:0 0 12px;">A member of the Armely team will be in touch. Your reference is <strong>{{ $reference }}</strong>.</p>
                        <p style="margin:0;">If anything changes, just reply to this email or reach us at <a href="mailto:{{ $contactEmail }}" style="color:#1f4d99;">{{ $contactEmail }}</a>.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 30px 22px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;color:#7b8fad;font-size:12px;line-height:1.5;">You received this because you requested follow-up in a chat on armely.com.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
