@php
    $emailTitle = $emailTitle ?? config('app.name', 'Armely Store');
    $emailBadge = $emailBadge ?? 'Notification';
    $emailAccent = $emailAccent ?? '#2F5597';
    $supportEmail = \App\Models\AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com'));
    $storeUrl = rtrim(\App\Support\FrontendUrl::base(), '/');
    $storeHost = strtolower((string) parse_url($storeUrl, PHP_URL_HOST));
    if (in_array($storeHost, ['127.0.0.1', 'localhost', ''], true)) {
        $storeUrl = rtrim((string) env('PUBLIC_STOREFRONT_URL', 'https://armely.com/store'), '/');
    }
    $logoUrl = $storeUrl . '/images/logo/armely-store-logo.png';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $emailTitle }}</title>
    <style>
        @media only screen and (max-width:620px) {
            .email-shell { padding:10px 6px 20px !important; }
            .email-header,.email-body,.email-footer { padding:18px 14px !important; }
            .email-title { font-size:22px !important; }
            .email-logo-cell { width:56px !important; padding-right:9px !important; }
            .email-logo-box { width:48px !important; height:40px !important; padding:3px !important; }
            .email-logo { width:48px !important; max-height:40px !important; }
            .email-title { font-size:19px !important; }
            .email-button { display:block !important; text-align:center !important; }
            .mobile-block { display:block !important; width:100% !important; box-sizing:border-box !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#eef3fa;font-family:'Segoe UI',Arial,sans-serif;color:#1f2937;">
<div class="email-shell" style="max-width:760px;margin:0 auto;padding:22px 12px 32px;">
    <div style="background:#ffffff;border:1px solid #dbe7f7;border-radius:16px;overflow:hidden;box-shadow:0 10px 28px rgba(15,47,99,.10);">
        <div class="email-header" style="background-color:#0f2f63;padding:22px 26px;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;width:100%;">
                <tr>
                    <td class="email-logo-cell" width="92" style="width:92px;padding-right:18px;vertical-align:middle;">
                        <div class="email-logo-box" style="width:78px;height:64px;padding:5px;background:#ffffff;border-radius:10px;text-align:center;">
                            <img class="email-logo" src="{{ $logoUrl }}" width="78" alt="Armely Store" style="display:block;width:78px;max-height:64px;height:auto;margin:0 auto;">
                        </div>
                    </td>
                    <td style="vertical-align:middle;">
                        <span style="display:inline-block;margin-bottom:7px;padding:4px 11px;border-radius:20px;background:{{ $emailAccent }};color:#ffffff;font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">{{ $emailBadge }}</span>
                        <p style="margin:0 0 5px;color:#dbe7ff;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Armely Store</p>
                        <h1 class="email-title" style="margin:0;color:#ffffff!important;font-size:25px;line-height:1.2;font-weight:700;">{{ $emailTitle }}</h1>
                    </td>
                </tr>
            </table>
        </div>
        <div class="email-body" style="padding:26px 30px;font-size:15px;line-height:1.6;">
            @yield('content')
        </div>
        <div class="email-footer" style="padding:16px 30px 20px;background:#f8fbff;border-top:1px solid #e3ebf8;">
            @hasSection('footer-note')
                <div style="margin:0 0 10px;color:#64748b;font-size:12px;line-height:1.55;">@yield('footer-note')</div>
            @endif
            <p style="margin:0;color:#94a3b8;font-size:11px;line-height:1.5;">Need help? <a href="mailto:{{ $supportEmail }}" style="color:#2F5597;text-decoration:none;">{{ $supportEmail }}</a> &nbsp;&bull;&nbsp; &copy; {{ date('Y') }} Armely Store</p>
        </div>
    </div>
</div>
</body>
</html>
