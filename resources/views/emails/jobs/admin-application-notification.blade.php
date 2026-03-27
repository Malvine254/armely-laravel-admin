<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Application</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:700px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:28px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Armely Hiring Alerts</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:24px;line-height:1.2;">New Job Application Submitted</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:26px 30px 8px;">
                        <p style="margin:0 0 12px;font-size:16px;line-height:1.6;">Hello Hiring Team,</p>
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334b74;">A new candidate has submitted an application through the careers page.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 30px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 8px;">
                            <tr><td style="width:210px;padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Name</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($name) }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Email</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($email) }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Phone</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($phone ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Position</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($position ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Job Type</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($jobType ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Job ID</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($jobId ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">City</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($city ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Address</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($address ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">State / Zip</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e(($state ?: 'N/A') . ' / ' . ($zip ?: 'N/A')) }}</td></tr>
                        </table>
                    </td>
                </tr>
                @if(!empty($cvUrl))
                <tr>
                    <td style="padding:0 30px 16px;">
                        <div style="padding:12px 14px;background:#eef6ff;border:1px solid #d8e8ff;border-radius:10px;">
                            <p style="margin:0 0 8px;color:#34517d;font-size:13px;font-weight:700;">Candidate CV</p>
                            <a href="{{ e($cvUrl) }}" target="_blank" rel="noopener" style="display:inline-block;padding:10px 14px;background:#1f4d99;color:#ffffff;text-decoration:none;border-radius:8px;font-size:13px;font-weight:700;">Download CV</a>
                        </div>
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding:16px 30px 24px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;font-size:13px;color:#60779c;line-height:1.6;">This is an automated notification from the Armely website.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
