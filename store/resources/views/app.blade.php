<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-eval' 'unsafe-inline' http://localhost:5173 http://localhost:5174 https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://www.googletagmanager.com https://www.googleadservices.com; style-src 'self' 'unsafe-inline' https://fonts.bunny.net http://localhost:5173 http://localhost:5174; font-src 'self' https://fonts.bunny.net; img-src 'self' data: https:; frame-src 'self' https://www.google.com https://www.recaptcha.net; connect-src 'self' http://localhost:8000 http://127.0.0.1:8000 http://localhost:8001 http://127.0.0.1:8001 http://localhost:5173 http://localhost:5174 ws://localhost:5173 ws://localhost:5174 https://www.google.com https://www.gstatic.com https://www.recaptcha.net https://www.googletagmanager.com https://analytics.google.com https://www.google-analytics.com https://region1.google-analytics.com https://stats.g.doubleclick.net https://googleads.g.doubleclick.net https://www.googleadservices.com;">

        <title>{{ config('app.name', 'Armely Store') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo/armely-store-logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/armely-store-logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Google tag (gtag.js) -->
        @php($ga4Id = config('services.google_analytics.ga4_id', ''))
        @php($adsId = config('services.google_ads.id', ''))
        @php($purchaseLabel = config('services.google_ads.purchase_conversion_label', ''))
        @php($quoteLabel = config('services.google_ads.quote_conversion_label', ''))
        @if($ga4Id || $adsId)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id ?: $adsId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                window.storeTracking = {
                    ga4Id: @json($ga4Id),
                    {{-- Prefer a conversion-specific label when one is configured; otherwise still
                         report to the base Ads account tag rather than sending nothing. --}}
                    adsPurchaseDestination: @json($adsId ? ($purchaseLabel ? $adsId.'/'.$purchaseLabel : $adsId) : ''),
                    adsQuoteDestination: @json($adsId ? ($adsId.'/'.$quoteLabel) : '')
                };
                @if($ga4Id)
                    gtag('config', @json($ga4Id), { send_page_view: false });
                @endif
                @if($adsId)
                    gtag('config', @json($adsId));
                @endif
            </script>
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div id="app"></div>
    </body>
</html>
