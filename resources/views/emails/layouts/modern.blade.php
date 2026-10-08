@php
    $emailTitle = $emailTitle ?? 'Armely Notification';
    $emailBadge = $emailBadge ?? 'Armely';
    $emailAccent = $emailAccent ?? '#2f5597';
    $logoUrl = 'https://armely.com/images/logo/logo-replace-v2.png';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $emailTitle }}</title>
    <style>
        @media only screen and (max-width:620px) {
            .mail-shell { padding:12px 8px!important }
            .mail-header,.mail-body,.mail-footer { padding:22px 18px!important }
            .mail-logo-cell { width:106px!important;padding-right:12px!important }
            .mail-logo { width:94px!important }
            .mail-title { font-size:22px!important }
            .mail-detail-label,.mail-detail-value { display:block!important;width:100%!important;text-align:left!important;box-sizing:border-box!important }
            .mail-detail-label { padding:12px 0 2px!important;border-bottom:0!important }
            .mail-detail-value { padding:2px 0 12px!important }
        }
        h1.mail-title,.mail-title,.mail-title span { color:#ffffff!important }
        [data-ogsc] .mail-header,[data-ogsb] .mail-header { background:#0f2f63!important }
        [data-ogsc] .mail-title { color:#ffffff!important }
    </style>
</head>
<body style="margin:0;padding:0;background:#eef3fa;font-family:'Segoe UI',Arial,sans-serif;color:#172033">
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" bgcolor="#eef3fa">
    <tr>
        <td class="mail-shell" align="center" style="padding:32px 16px">
            <!--[if mso]><table role="presentation" width="640" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="max-width:640px;background:#ffffff;border:1px solid #dbe7f7;border-radius:12px">
                <tr>
                    <td class="mail-header" bgcolor="#0f2f63" style="background:#0f2f63;padding:28px 32px;border-radius:12px 12px 0 0">
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td class="mail-logo-cell" width="138" style="width:138px;padding-right:18px;vertical-align:middle">
                                    <img class="mail-logo" src="{{ $logoUrl }}" width="120" alt="Armely" style="display:block;width:120px;height:auto;background:#ffffff;padding:5px;border-radius:6px">
                                </td>
                                <td style="vertical-align:middle">
                                    <p style="margin:0 0 8px;color:#bfdbfe;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.4px">{{ $emailBadge }}</p>
                                    <h1 class="mail-title" style="margin:0;color:#ffffff!important;font-size:25px;line-height:1.3;font-weight:700">{{ $emailTitle }}</h1>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td height="4" bgcolor="{{ $emailAccent }}" style="height:4px;background:{{ $emailAccent }};font-size:0;line-height:0">&nbsp;</td></tr>
                <tr><td class="mail-body" style="padding:28px 32px;color:#172033;font-size:14px;line-height:1.7">@yield('content')</td></tr>
                <tr><td class="mail-footer" bgcolor="#f8fafc" style="padding:20px 32px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:11px;line-height:1.6">@yield('footer', 'This is an automated message from Armely AI Solutions.')</td></tr>
            </table>
            <!--[if mso]></td></tr></table><![endif]-->
        </td>
    </tr>
</table>
</body>
</html>
