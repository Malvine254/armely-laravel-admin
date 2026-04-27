<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your AI Data Readiness Report</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:28px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Armely AI Readiness</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:24px;line-height:1.2;">Your Assessment Report Is Ready</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px 20px;">
                        <p style="margin:0 0 14px;font-size:16px;line-height:1.6;">Dear {{ e($firstName) }},</p>
                        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#324a73;">Thank you for completing Armely's AI Data Readiness assessment. Your current score indicates how prepared your data foundation is for reliable AI delivery.</p>
                        <div style="margin:0 0 18px;padding:18px 20px;background:#f5f8ff;border:1px solid #dbe7ff;border-radius:14px;text-align:center;">
                            <p style="margin:0;color:#33507f;font-size:12px;font-weight:700;letter-spacing:1.1px;text-transform:uppercase;">Overall Readiness</p>
                            <p style="margin:8px 0 0;color:#153462;font-size:40px;font-weight:800;line-height:1;">{{ e($scorePercent) }}%</p>
                            <p style="margin:10px 0 0;display:inline-block;padding:6px 14px;background:#e4efff;border-radius:999px;color:#1f4d99;font-size:13px;font-weight:700;">{{ e($tier['label']) }}</p>
                            <p style="margin:12px 0 0;color:#4f6690;font-size:14px;line-height:1.7;">{{ e($tier['summary']) }}</p>
                            <p style="margin:10px 0 0;color:#60779c;font-size:13px;">Score: {{ e($overallScore) }} / 360</p>
                        </div>
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
                                            <td align="right" style="color:#34517d;font-size:13px;font-weight:700;padding:0 0 4px;">{{ e($dimension['percent']) }}%</td>
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
                        <div style="margin:18px 0 0;padding:20px;background:linear-gradient(135deg,#153462 0%,#2672b8 100%);border-radius:14px;">
                            <p style="margin:0 0 8px;color:#d7e8ff;font-size:12px;font-weight:700;letter-spacing:1.1px;text-transform:uppercase;">Next Step</p>
                            <p style="margin:0 0 14px;color:#ffffff;font-size:15px;line-height:1.7;">If you want help turning this assessment into a practical roadmap, our team can review your results and recommend the right next moves.</p>
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="border-radius:10px;background:#ffffff;">
                                        <a href="{{ e($contactUrl) }}" style="display:inline-block;padding:12px 18px;color:#1f4d99;text-decoration:none;font-size:14px;font-weight:700;border-radius:10px;">Book a Strategy Session</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <p style="margin:18px 0 0;font-size:15px;line-height:1.6;color:#2f4468;">Warm regards,<br><strong>Team Armely</strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 30px 24px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;color:#7b8fad;font-size:12px;line-height:1.5;">This is an automated assessment summary from Armely.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>