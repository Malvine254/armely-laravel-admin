<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New AI Data Readiness Assessment</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:700px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:28px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Armely Lead Alerts</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:24px;line-height:1.2;">New AI Data Readiness Assessment</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:26px 30px 8px;">
                        <p style="margin:0 0 12px;font-size:16px;line-height:1.6;">Hello Armely Team,</p>
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334b74;">A visitor has completed the AI Data Readiness assessment. Review the lead profile and score summary below.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 30px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 8px;">
                            <tr><td style="width:220px;padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Name</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($fullName ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Email</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($email) }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Company</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($company ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Role</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($role ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Overall Score</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($overallScore) }} / 360 ({{ e($scorePercent) }}%)</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Location</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e(($city ?: 'Unknown') . ', ' . ($country ?: 'Unknown')) }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">IP Address</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($ipAddress ?: 'N/A') }}</td></tr>
                            <tr><td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Submitted At</td><td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($submittedAt ?: 'N/A') }}</td></tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:4px 30px 24px;">
                        <div style="padding:14px 16px;background:#f8fbff;border:1px solid #e2edff;border-radius:12px;">
                            <p style="margin:0 0 12px;color:#34517d;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Dimension Breakdown</p>
                            @foreach($dimensions as $dimension)
                                @if($loop->last)
                                <div style="margin:0;">
                                @else
                                <div style="margin:0 0 10px;">
                                @endif
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="color:#1f3458;font-size:14px;font-weight:700;padding:0 0 4px;">{{ e($dimension['label']) }}</td>
                                            <td align="right" style="color:#34517d;font-size:13px;font-weight:700;padding:0 0 4px;">{{ e($dimension['percent']) }}% ({{ e($dimension['score']) }}/{{ e($dimension['max']) }})</td>
                                        </tr>
                                    </table>
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#dce8fb;border-radius:999px;overflow:hidden;">
                                        <tr>
                                            <td width="{{ $dimension['percent'] }}%" style="height:8px;background:#1f4d99;font-size:0;line-height:0;">&nbsp;</td>
                                            <td style="height:8px;font-size:0;line-height:0;">&nbsp;</td>
                                        </tr>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
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