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
    // Email-sized copy (240px, ~25 KB) of the storefront logo, shown at 120px.
    $logoUrl = $storeUrl . '/images/logo/armely-store-logo-email.png';
    $accountUrl = $storeUrl . '/account';
    $brand = '#2F5597';
    $navy = '#173b73';
@endphp
<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <title>{{ $emailTitle }}</title>
    <style>
        body { margin:0 !important; padding:0 !important; width:100% !important; }
        @media only screen and (max-width:620px) {
            .email-container { width:100% !important; }
            .email-pad { padding-left:18px !important; padding-right:18px !important; }
            .email-title { font-size:21px !important; }
            .email-button { display:block !important; text-align:center !important; }
            .mobile-block { display:block !important; width:100% !important; box-sizing:border-box !important; }
            .footer-links a { display:inline-block !important; margin:4px 6px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#eef3fa;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eef3fa" style="background-color:#eef3fa;">
<tr><td align="center" style="padding:24px 10px 32px;">

    <table role="presentation" class="email-container" width="680" cellpadding="0" cellspacing="0" border="0" style="width:680px;max-width:680px;">

        {{-- Brand header: same white logo treatment as the website --}}
        <tr><td class="email-logo-cell" bgcolor="#ffffff" align="center" style="background-color:#ffffff;border-radius:16px 16px 0 0;border:1px solid #dbe7f7;border-bottom:0;padding:22px 24px 16px;">
            <a href="{{ $storeUrl }}" style="text-decoration:none;">
                <img class="email-logo" src="{{ $logoUrl }}" width="120" alt="Armely Store - Smart technology. Seamless Procurement" style="display:block;width:120px;max-width:120px;height:auto;border:0;margin:0 auto;">
            </a>
        </td></tr>
        <tr><td bgcolor="{{ $brand }}" height="4" style="background-color:{{ $brand }};height:4px;font-size:0;line-height:0;border-left:1px solid #dbe7f7;border-right:1px solid #dbe7f7;">&nbsp;</td></tr>

        {{-- Title --}}
        <tr><td class="email-pad" bgcolor="#ffffff" style="background-color:#ffffff;border-left:1px solid #dbe7f7;border-right:1px solid #dbe7f7;padding:26px 32px 4px;">
            <span style="display:inline-block;margin:0 0 10px;padding:4px 12px;border-radius:999px;background:{{ $emailAccent }};color:#ffffff;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">{{ $emailBadge }}</span><br>
            <h1 class="email-title" style="margin:0;font-size:24px;line-height:1.3;font-weight:700;color:{{ $navy }};">{{ $emailTitle }}</h1>
        </td></tr>

        {{-- Body --}}
        <tr><td class="email-pad email-body" bgcolor="#ffffff" style="background-color:#ffffff;border-left:1px solid #dbe7f7;border-right:1px solid #dbe7f7;padding:16px 32px 28px;font-size:15px;line-height:1.6;color:#374151;">
            @yield('content')
        </td></tr>

        {{-- Help strip --}}
        <tr><td class="email-pad" bgcolor="#f4f8ff" style="background-color:#f4f8ff;border:1px solid #dbe7f7;border-bottom:0;padding:16px 32px;font-size:13px;line-height:1.6;color:#475569;">
            @hasSection('footer-note')
                <div style="margin:0 0 8px;">@yield('footer-note')</div>
            @endif
            Need help? Reply to this email or contact
            <a href="mailto:{{ $supportEmail }}" style="color:{{ $brand }};font-weight:600;text-decoration:none;">{{ $supportEmail }}</a>.
        </td></tr>

        {{-- Footer --}}
        <tr><td class="email-pad email-footer" bgcolor="{{ $navy }}" align="center" style="background-color:{{ $navy }};border-radius:0 0 16px 16px;padding:24px 32px 26px;color:#c7d6f0;font-size:12px;line-height:1.6;">
            <p class="footer-links" style="margin:0 0 12px;font-size:13px;">
                <a href="{{ $storeUrl }}" style="color:#ffffff;font-weight:600;text-decoration:none;">Shop the store</a>
                <span style="color:#5f7fb3;">&nbsp;&bull;&nbsp;</span>
                <a href="{{ $accountUrl }}" style="color:#ffffff;font-weight:600;text-decoration:none;">My account</a>
            </p>
            <p style="margin:0;color:#8fa6cf;">&copy; {{ date('Y') }} Armely Store &middot; Smart technology. Seamless Procurement.</p>
        </td></tr>

    </table>

</td></tr>
</table>
</body>
</html>
