@php
    $emailTitle = $emailTitle ?? 'Armely Notification';
    $emailBadge = $emailBadge ?? 'Armely';
    $emailAccent = $emailAccent ?? '#2f5597';
    // Trimmed, email-sized copy (320px, ~7 KB) of the website wordmark, shown at 160px.
    $logoUrl = 'https://armely.com/images/logo/armely-logo-email.png';
    $siteUrl = 'https://armely.com';
    $brand = '#2F5597';
    $navy = '#0f2f63';
@endphp
<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $emailTitle }}</title>
    <style>
        body { margin:0 !important; padding:0 !important; width:100% !important; }
        @media only screen and (max-width:620px) {
            .mail-shell { padding:12px 8px !important; }
            .mail-pad { padding-left:18px !important; padding-right:18px !important; }
            .mail-title { font-size:21px !important; }
            .mail-detail-label,.mail-detail-value { display:block !important; width:100% !important; text-align:left !important; box-sizing:border-box !important; }
            .mail-detail-label { padding:12px 0 2px !important; border-bottom:0 !important; }
            .mail-detail-value { padding:2px 0 12px !important; }
            .mail-button { display:block !important; text-align:center !important; }
            .footer-links a { display:inline-block !important; margin:4px 6px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#eef3fa;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#172033;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#eef3fa" style="background-color:#eef3fa;">
    <tr>
        <td class="mail-shell" align="center" style="padding:28px 12px 36px;">
            <!--[if mso]><table role="presentation" width="640" cellpadding="0" cellspacing="0"><tr><td><![endif]-->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:640px;">

                {{-- Brand header: white, with the website wordmark --}}
                <tr><td class="mail-header" bgcolor="#ffffff" align="center" style="background-color:#ffffff;border:1px solid #dbe7f7;border-bottom:0;border-radius:14px 14px 0 0;padding:26px 24px 22px;">
                    <a href="{{ $siteUrl }}" style="text-decoration:none;">
                        <img class="mail-logo" src="{{ $logoUrl }}" width="160" alt="Armely" style="display:block;width:160px;max-width:160px;height:auto;border:0;margin:0 auto;">
                    </a>
                </td></tr>
                <tr><td height="4" bgcolor="{{ $brand }}" style="height:4px;background-color:{{ $brand }};font-size:0;line-height:0;border-left:1px solid #dbe7f7;border-right:1px solid #dbe7f7;">&nbsp;</td></tr>

                {{-- Title --}}
                <tr><td class="mail-pad" bgcolor="#ffffff" style="background-color:#ffffff;border-left:1px solid #dbe7f7;border-right:1px solid #dbe7f7;padding:26px 32px 4px;">
                    <span style="display:inline-block;margin:0 0 10px;padding:4px 12px;border-radius:999px;background:{{ $emailAccent }};color:#ffffff;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">{{ $emailBadge }}</span><br>
                    <h1 class="mail-title" style="margin:0;font-size:24px;line-height:1.3;font-weight:700;color:{{ $navy }};">{{ $emailTitle }}</h1>
                </td></tr>

                {{-- Body --}}
                <tr><td class="mail-pad mail-body" bgcolor="#ffffff" style="background-color:#ffffff;border-left:1px solid #dbe7f7;border-right:1px solid #dbe7f7;padding:16px 32px 28px;color:#172033;font-size:15px;line-height:1.7;">@yield('content')</td></tr>

                {{-- Why you got this / help --}}
                <tr><td class="mail-pad mail-footer" bgcolor="#f4f8ff" style="background-color:#f4f8ff;border:1px solid #dbe7f7;border-bottom:0;padding:16px 32px;color:#475569;font-size:13px;line-height:1.6;">@yield('footer', 'This is an automated message from Armely AI Solutions.')</td></tr>

                {{-- Footer --}}
                <tr><td class="mail-pad" bgcolor="{{ $navy }}" align="center" style="background-color:{{ $navy }};border-radius:0 0 14px 14px;padding:24px 32px 26px;color:#c7d6f0;font-size:12px;line-height:1.6;">
                    <p class="footer-links" style="margin:0 0 12px;font-size:13px;">
                        <a href="{{ $siteUrl }}" style="color:#ffffff;font-weight:600;text-decoration:none;">armely.com</a>
                        <span style="color:#5f7fb3;">&nbsp;&bull;&nbsp;</span>
                        <a href="{{ $siteUrl }}/services" style="color:#ffffff;font-weight:600;text-decoration:none;">Services</a>
                        <span style="color:#5f7fb3;">&nbsp;&bull;&nbsp;</span>
                        <a href="{{ $siteUrl }}/store" style="color:#ffffff;font-weight:600;text-decoration:none;">Store</a>
                    </p>
                    <p style="margin:0;color:#8fa6cf;">&copy; {{ date('Y') }} Armely. All rights reserved.</p>
                </td></tr>

            </table>
            <!--[if mso]></td></tr></table><![endif]-->
        </td>
    </tr>
</table>
</body>
</html>
