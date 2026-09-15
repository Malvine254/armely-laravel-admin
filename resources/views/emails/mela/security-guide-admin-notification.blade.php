<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Mela Documentation Request</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:24px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Mela Meeting Assistant</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:22px;line-height:1.25;">New Documentation Request</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 30px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;color:#233f71;">
                            <tr><td style="padding:6px 0;width:140px;color:#6b7fa2;">Name</td><td style="padding:6px 0;font-weight:600;">{{ e($name) }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Email</td><td style="padding:6px 0;font-weight:600;">{{ e($email) }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Organization</td><td style="padding:6px 0;">{{ $organization !== '' ? e($organization) : 'N/A' }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Job title</td><td style="padding:6px 0;">{{ $jobTitle !== '' ? e($jobTitle) : 'N/A' }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Phone</td><td style="padding:6px 0;">{{ $phone !== '' ? e($phone) : 'N/A' }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Link expires</td><td style="padding:6px 0;">{{ e($expiresAt) }}</td></tr>
                        </table>
                        <p style="margin:16px 0 0;font-size:13px;color:#6b7fa2;text-transform:uppercase;letter-spacing:.5px;font-weight:700;">Notes</p>
                        <p style="margin:6px 0 0;font-size:14px;line-height:1.6;color:#324a73;">{{ $message !== '' ? nl2br(e($message)) : 'N/A' }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 30px 22px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;color:#7b8fad;font-size:12px;line-height:1.5;">The user has already been emailed a secure download link automatically.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
