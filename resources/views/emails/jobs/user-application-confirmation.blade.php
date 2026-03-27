<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Received</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:28px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Armely Careers</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:24px;line-height:1.2;">Your Application Has Been Received</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px 20px;">
                        <p style="margin:0 0 14px;font-size:16px;line-height:1.6;">Dear {{ e($name) }},</p>
                        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#324a73;">Thank you for applying to Armely for the role of <strong>{{ e($position) }}</strong>. Your application has been submitted successfully.</p>
                        <div style="padding:14px 16px;background:#f5f8ff;border:1px solid #dbe7ff;border-radius:12px;">
                            <p style="margin:0 0 6px;color:#1f3a69;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Application Snapshot</p>
                            <p style="margin:0;color:#2f4468;font-size:14px;line-height:1.7;">
                                Position: {{ e($position) }}<br>
                                Job ID: {{ e($jobId) }}
                            </p>
                        </div>
                        <p style="margin:16px 0 0;font-size:14px;line-height:1.7;color:#4f6690;">Our recruiting team will review your profile and contact you within one month if your application is shortlisted.</p>
                        <p style="margin:18px 0 0;font-size:15px;line-height:1.6;color:#2f4468;">Best regards,<br><strong>Armely HR Team</strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 30px 24px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;color:#7b8fad;font-size:12px;line-height:1.5;">This is an automated acknowledgement from Armely.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
