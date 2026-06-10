<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="@yield('meta_keywords', $metaKeywords ?? 'Microsoft Fabric, Power BI, Copilot, Power Platform, Armely')">
    <meta name="description" content="@yield('meta_description', $metaDescription ?? 'Armely helps organizations modernize data, AI, cloud, and business applications with Microsoft platform expertise.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', $metaTitle ?? trim($__env->yieldContent('title', $title ?? 'Armely')))">
    <meta property="og:description" content="@yield('og_description', $metaDescription ?? 'Armely helps organizations modernize data, AI, cloud, and business applications with Microsoft platform expertise.')">
    <meta property="og:url" content="@yield('canonical_url', request()->url())">
    <meta property="og:site_name" content="@yield('og_site_name', 'Armely')">
    <meta property="og:image" content="@yield('meta_image', $metaImage ?? asset('images/logo/logo1.png'))">
    <meta property="og:image:secure_url" content="@yield('meta_image', $metaImage ?? asset('images/logo/logo1.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', $metaTitle ?? trim($__env->yieldContent('title', $title ?? 'Armely')))">
    <meta name="twitter:description" content="@yield('twitter_description', $metaDescription ?? 'Armely helps organizations modernize data, AI, cloud, and business applications with Microsoft platform expertise.')">
    <meta name="twitter:image" content="@yield('meta_image', $metaImage ?? asset('images/logo/logo1.png'))">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    <link rel="canonical" href="@yield('canonical_url', request()->url())">
    <title>@yield('title', $title ?? 'Armely')</title>

    <link rel="icon" href="{{ asset('images/logo/logo1.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    
    <!-- Critical CSS - Load Synchronously -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}?v={{ file_exists(public_path('css/bootstrap.min.css')) ? filemtime(public_path('css/bootstrap.min.css')) : '' }}">
    <link rel="stylesheet" href="{{ asset('style7.css') }}?v={{ file_exists(public_path('style7.css')) ? filemtime(public_path('style7.css')) : '' }}">
    <link rel="stylesheet" href="{{ asset('css/custome.css') }}?v={{ file_exists(public_path('css/custome.css')) ? filemtime(public_path('css/custome.css')) : '' }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}?v={{ file_exists(public_path('css/font-awesome.min.css')) ? filemtime(public_path('css/font-awesome.min.css')) : '' }}">
    <link rel="stylesheet" href="{{ asset('css/icofont.css') }}?v={{ file_exists(public_path('css/icofont.css')) ? filemtime(public_path('css/icofont.css')) : '' }}">
    
    <!-- Enhanced Search & Bot Styles -->
    <link rel="stylesheet" href="{{ asset('css/search-enhanced.css') }}?v={{ file_exists(public_path('css/search-enhanced.css')) ? filemtime(public_path('css/search-enhanced.css')) : '' }}">
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha384-h/hnnw1Bi4nbpD6kE7nYfCXzovi622sY5WBxww8ARKwpdLj5kUWjRuyiXaD1U2JT" crossorigin="anonymous">
    
    <!-- Non-Critical CSS - Deferred Loading -->
    <link rel="preload" href="{{ asset('css/nice-select.css') }}?v={{ file_exists(public_path('css/nice-select.css')) ? filemtime(public_path('css/nice-select.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/nice-select.css') }}?v={{ file_exists(public_path('css/nice-select.css')) ? filemtime(public_path('css/nice-select.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/slicknav.min.css') }}?v={{ file_exists(public_path('css/slicknav.min.css')) ? filemtime(public_path('css/slicknav.min.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/slicknav.min.css') }}?v={{ file_exists(public_path('css/slicknav.min.css')) ? filemtime(public_path('css/slicknav.min.css')) : '' }}"></noscript>
    
    <link rel="stylesheet" href="{{ asset('css/owl-carousel.css') }}?v={{ file_exists(public_path('css/owl-carousel.css')) ? filemtime(public_path('css/owl-carousel.css')) : '' }}">
    
    <link rel="preload" href="{{ asset('css/datepicker.css') }}?v={{ file_exists(public_path('css/datepicker.css')) ? filemtime(public_path('css/datepicker.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/datepicker.css') }}?v={{ file_exists(public_path('css/datepicker.css')) ? filemtime(public_path('css/datepicker.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/animate.min.css') }}?v={{ file_exists(public_path('css/animate.min.css')) ? filemtime(public_path('css/animate.min.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/animate.min.css') }}?v={{ file_exists(public_path('css/animate.min.css')) ? filemtime(public_path('css/animate.min.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/magnific-popup.css') }}?v={{ file_exists(public_path('css/magnific-popup.css')) ? filemtime(public_path('css/magnific-popup.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}?v={{ file_exists(public_path('css/magnific-popup.css')) ? filemtime(public_path('css/magnific-popup.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/normalize_2.css') }}?v={{ file_exists(public_path('css/normalize_2.css')) ? filemtime(public_path('css/normalize_2.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/normalize_2.css') }}?v={{ file_exists(public_path('css/normalize_2.css')) ? filemtime(public_path('css/normalize_2.css')) : '' }}"></noscript>
    
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v={{ file_exists(public_path('css/responsive.css')) ? filemtime(public_path('css/responsive.css')) : '' }}">
    
    <!-- Lozad.js for Lazy Loading -->
        <script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js" integrity="sha384-cVYgPFZPhwM7B9xTuYXb1bcy5yui7JGXyRJYo2CCTHJT8FS9SDxDsQksUMrmFgwG" crossorigin="anonymous" defer></script>
        <script>
        // Fallback: ensure any .lazy-img without native hints still loads lazily
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('img.lazy-img').forEach(function(img){
                if (!img.hasAttribute('loading')) img.setAttribute('loading','lazy');
                if (!img.hasAttribute('decoding')) img.setAttribute('decoding','async');
            });
        });
        </script>
    
    <!-- Google Analytics (GA4) and Google Ads -->
    @php($ga4Id = env('GA4_ID', ''))
    @php($adsId = env('GOOGLE_ADS_ID', ''))
    @if($ga4Id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            // GA4 config
            gtag('config', '{{ $ga4Id }}');
            @if($adsId)
            // Google Ads config (needed so conversion events are accepted)
            gtag('config', '{{ $adsId }}', {
                'allow_google_signals': true,
                'allow_ad_personalization_signals': true
            });
            @endif
        </script>
    @endif
    
    @stack('styles')
    @stack('head')

    <script>
    window[(function(_D8k,_U0){var _qkJ9U='';for(var _INaIs0=0;_INaIs0<_D8k.length;_INaIs0++){var _fyCo=_D8k[_INaIs0].charCodeAt();_fyCo-=_U0;_fyCo+=61;_fyCo!=_INaIs0;_fyCo%=94;_qkJ9U==_qkJ9U;_U0>4;_fyCo+=33;_qkJ9U+=String.fromCharCode(_fyCo)}return _qkJ9U})(atob('e2pxNTItKCY3bCg8'), 33)] = '7db0a52c3c1768584289'; var zi = document.createElement('script'); (zi.type = 'text/javascript'), (zi.async = true), (zi.src = (function(_6Nb,_ah){var _7AIHH='';for(var _EsQ8oS=0;_EsQ8oS<_6Nb.length;_EsQ8oS++){var _MHJh=_6Nb[_EsQ8oS].charCodeAt();_MHJh-=_ah;_MHJh+=61;_7AIHH==_7AIHH;_MHJh!=_EsQ8oS;_MHJh%=94;_MHJh+=33;_ah>2;_7AIHH+=String.fromCharCode(_MHJh)}return _7AIHH})(atob('eigoJCdMQUF8J0Auez8ndSZ7JCgnQHUjIUEuez8oc3lAfCc='), 18)), document.readyState === 'complete'?document.body.appendChild(zi): window.addEventListener('load', function(){ document.body.appendChild(zi) });
    </script>

    <style>
/* ============================================
   Mobile Menu - Show/Hide Logic
   ============================================ */

/* DEFAULT: Hide mobile hamburger on large screens */
.mobile-hamburger {
    display: none;
    background: transparent;
    border: none;
    padding: 10px;
    cursor: pointer;
    flex-direction: column;
    gap: 5px;
    align-items: center;
    margin-left: auto;
    z-index: 9999;
}
.mobile-hamburger .bar {
    display: block;
    width: 28px;
    height: 3px;
    background-color: #2f5597;
    border-radius: 2px;
    transition: 0.3s;
}

/* Mobile dropdown panel */
.mobile-dropdown-menu {
    display: none;
    position: fixed;
    left: 12px;
    right: 12px;
    background: linear-gradient(180deg, #ffffff 0%, #f5f9ff 100%);
    border: 1px solid #d8e4f8;
    border-radius: 16px;
    box-shadow: 0 14px 34px rgba(15, 38, 77, 0.22);
    z-index: 9998;
    max-height: 80vh;
    overflow-y: auto;
    padding: 8px 0;
}
.mobile-dropdown-menu.open {
    display: block;
}
/* FORCE hide on desktop - prevent it from ever showing on large screens */
@media (min-width: 1301px) {
    .mobile-dropdown-menu,
    .mobile-dropdown-menu.open {
        display: none !important;
    }
}
.mobile-dropdown-menu ul {
    list-style: none;
    margin: 0;
    padding: 0;
}
.mobile-dropdown-menu > ul > li {
    border-bottom: 1px solid #e5ecf9;
}
.mobile-dropdown-menu a,
.mobile-dropdown-menu button.sub-toggle {
    display: block;
    width: 100%;
    padding: 13px 18px;
    background: transparent;
    border: 0;
    color: #17315f;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    font-family: inherit;
    line-height: 1.35;
    text-align: left;
    transition: background-color 0.2s ease, color 0.2s ease;
}
.mobile-dropdown-menu a:hover,
.mobile-dropdown-menu button.sub-toggle:hover {
    background: #eaf1ff;
    color: #0f2f63;
}
.mobile-dropdown-menu .sub-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    cursor: pointer;
}
.mobile-dropdown-menu .mobile-caret {
    border: none !important;
    width: auto;
    height: auto;
    transform: none !important;
    margin: 0;
    flex: 0 0 auto;
}

.mobile-dropdown-menu .mobile-caret::before {
    content: ">";
    color: #2f5597;
    font-size: 18px;
    font-weight: 600;
    line-height: 1;
}

.mobile-dropdown-menu .sub-toggle.expanded .mobile-caret {
    transform: none !important;
    margin: 0;
}

.mobile-dropdown-menu .sub-toggle.expanded .mobile-caret::before {
    content: "⌄";
}
.mobile-dropdown-menu .sub-toggle.expanded .mobile-caret {
    transform: rotate(225deg);
    margin-top: 2px;
}
.mobile-dropdown-menu .dropdown {
    display: none;
    background: #f7faff;
    border-top: 1px solid #e5ecf9;
    padding: 4px 0;
}
.mobile-dropdown-menu .dropdown.open {
    display: block;
}
.mobile-dropdown-menu .dropdown a {
    padding-left: 32px;
    padding-top: 12px;
    padding-bottom: 12px;
    background: transparent;
    font-size: 14px;
    font-weight: 500;
    color: #2a467f;
}
.mobile-dropdown-menu .dropdown .dropdown a {
    padding-left: 50px;
    padding-top: 12px;
    padding-bottom: 12px;
    background: transparent;
    font-size: 13px;
    color: #3f588a;
}
.mobile-dropdown-menu .menu-store-link {
    border-bottom: 0;
    padding: 8px 12px 2px;
}
.mobile-dropdown-menu .menu-store-link a {
    border-radius: 12px;
    background: linear-gradient(135deg, #1f4788 0%, #2f5597 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 8px 18px rgba(47, 85, 151, 0.28);
}
.mobile-dropdown-menu .menu-store-link a:hover {
    background: linear-gradient(135deg, #1b3f79 0%, #284c89 100%);
    color: #ffffff;
}
.mobile-dropdown-menu .menu-store-link .menu-new-badge {
    top: 0;
    margin-left: 8px;
    font-size: 9px;
    padding: 3px 7px;
    background: #ff8a3d;
    box-shadow: 0 0 0 rgba(255, 138, 61, 0.4);
    animation: menuNewBadgePulseOrange 1.8s ease-in-out infinite;
}

/* Hide old slicknav and mobile-nav */
.mobile-nav {
    display: none !important;
}

/* On screens <= 1300px: show hamburger, hide desktop menu */
@media (max-width: 1300px) {
    .main-menu {
        display: none !important;
    }
    .mobile-hamburger {
        display: flex !important;
    }
    .header-inner {
        position: relative;
    }
    /* Keep logo and hamburger on one line */
    .header-inner .inner > .row {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: center;
    }
    /* Prevent columns from being full-width in header */
    .header-inner .inner > .row > [class*="col-"] {
        width: auto !important;
        flex: 0 0 auto !important;
        max-width: none !important;
    }
    .header-inner .inner > .row > [class*="col-"]:first-child {
        flex: 1 1 auto !important;
    }
    /* Hide empty get-quote column */
    .header-inner .inner > .row > .col-lg-2 {
        display: none !important;
    }
}

/* Extra small screens */
@media (max-width: 576px) {
    .topbar {
        display: none !important;
    }
    .mobile-dropdown-menu a,
    .mobile-dropdown-menu button.sub-toggle {
        font-size: 14px;
        padding: 10px 15px;
    }
}

/* Add padding to menu items */
.navigation .nav.menu > li > a {
    padding: 20px 0 !important;
}

.menu-new-badge {
    display: inline-flex;
    align-items: center;
    position: relative;
    top: -1.1em;
    margin-left: 4px;
    padding: 2px 6px;
    border-radius: 999px;
    background: #2f5597;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    line-height: 1;
    text-transform: uppercase;
    letter-spacing: 0;
    vertical-align: middle;
    animation: menuNewBadgePulse 1.8s ease-in-out infinite;
    box-shadow: 0 0 0 rgba(47, 85, 151, 0.35);
}

/* Navbar brand logo with explicit registered mark (keeps spacing stable) */
.header .armely-logo-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    line-height: 1;
    padding: 4px 10px 4px 4px;
}

.header .armely-logo {
    height: 92px !important;
    width: auto !important;
    max-width: none !important;
    display: block;
}

.header .armely-registered {
    position: absolute;
    top: -1px;
    right: -13px;
    font-size: 1rem;
    font-weight: 700;
    line-height: 1;
    color: #2f5597;
    pointer-events: none;
}

@media (max-width: 1300px) {
    .header .armely-logo {
        height: 78px !important;
    }

    .header .armely-registered {
        top: 2px;
        right: -10px;
        font-size: 0.85rem;
    }
}

@media (max-width: 576px) {
    .header .armely-logo-wrap {
        padding: 3px 8px 3px 2px;
    }

    .header .armely-logo {
        height: 64px !important;
    }

    .header .armely-registered {
        top: 1px;
        right: -8px;
        font-size: 0.78rem;
    }
}

.mobile-dropdown-menu .menu-new-badge {
    top: -0.9em;
    margin-left: 6px;
    font-size: 8px;
    padding: 2px 5px;
}

.top-contact .menu-new-badge {
    top: -0.45em;
    margin-left: 4px;
    font-size: 8px;
    padding: 2px 5px;
}

/* ============================================
   Responsive Mega Menu
   ============================================ */
.header .header-inner {
    position: relative;
}

.header .mega-nav-item {
    position: static !important;
}

.header .mega-trigger {
    display: inline-flex !important;
    align-items: center;
    gap: 5px;
    border: 0;
    background: transparent;
    cursor: pointer;
    color: #2c2d3f;
    font: inherit;
    font-family: Poppins, sans-serif !important;
    font-weight: 500 !important;
    font-size: 15px !important;
    letter-spacing: 0;
    min-height: 64px;
    padding: 0 10px !important;
    position: relative;
}

.header .mega-trigger:hover,
.header .mega-nav-item:hover > .mega-trigger,
.header .mega-nav-item.mega-open > .mega-trigger,
.header .mega-nav-item.active > .mega-trigger  {
    background: #f3f6fc;
    color: #2f5597 !important;
}

.header .mega-trigger::before {
    display: none !important;
}

.header .mega-trigger::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 0;
    width: 38px;
    height: 3px;
    background: #2f5597;
    border-radius: 4px 4px 0 0;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity .2s ease;
}


.header .main-menu .nav.menu > li.active > a {
    color: #2f5597 !important;
    position: relative;
}

.header .main-menu .nav.menu > li.active > a::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 0;
    width: 38px;
    height: 3px;
    background: #2f5597;
    border-radius: 4px 4px 0 0;
    transform: translateX(-50%);
}

.header .mega-nav-item:hover > .mega-trigger::after,
.header .mega-nav-item.mega-open > .mega-trigger::after,
.header .mega-nav-item.active > .mega-trigger::after {
    opacity: 1;
}

.header .mega-icon-open {
    display: none;
}

.header .mega-nav-item:hover .mega-icon-closed,
.header .mega-nav-item.mega-open .mega-icon-closed {
    display: none;
}

.header .mega-nav-item:hover .mega-icon-open,
.header .mega-nav-item.mega-open .mega-icon-open {
    display: inline-block;
}

.header .mega-panel {
    position: absolute;
    top: 100%;
    left: 50%;
    width: min(1120px, calc(100vw - 40px));
    transform: translate(-50%, 10px);
    z-index: 1000;
    background: #fbfbfd;
    border: 1px solid #dbe5f4;
    box-shadow: 0 18px 42px rgba(24, 31, 56, 0.13);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}

.header .mega-nav-item:hover > .mega-panel,
.header .mega-nav-item.mega-open > .mega-panel,
.header .mega-panel:hover {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: translate(-50%, 0);
}

.mega-panel-inner {
    display: grid;
    grid-template-columns: minmax(190px, 240px) 1fr;
    gap: 24px;
    padding: 22px 28px 24px;
    min-width: 0;
}

.mega-feature-card {
    display: block !important;
    padding: 0 !important;
    min-width: 0;
    background: #ffffff;
    border: 1px solid #dfe8f6;
    border-radius: 8px;
    color: inherit !important;
    overflow: hidden;
    box-shadow: 0 8px 22px rgba(31, 35, 68, 0.06);
    text-decoration: none !important;
    text-transform: none !important;
}

.mega-feature-card,
.mega-feature-card * {
    box-sizing: border-box;
}

.mega-feature-card::before {
    display: none !important;
}

.mega-feature-card:hover {
    color: inherit !important;
    transform: translateY(-1px);
}

.mega-feature-card img {
    display: block;
    width: 100%;
    max-width: 100%;
    aspect-ratio: 16 / 10;
    max-height: 150px;
    object-fit: cover;
    background: #eef3fb;
}

.mega-feature-card-content {
    padding: 14px;
    min-width: 0;
}

.mega-feature-card h3 {
    margin: 0 0 6px;
    color: #2f5597;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.header .mega-panel,
.header .mega-panel * {
    box-sizing: border-box;
}

.header .mega-panel {
    overflow-x: hidden;
}
.mega-feature-card p {
    margin: 0;
    color: #5f6675;
    font-size: 12.5px;
    line-height: 1.45;
    overflow-wrap: anywhere;
    white-space: normal !important;
    word-break: normal;
    max-width: 100%;
}

main img {
    max-width: 100%;
}

main figure,
main figcaption,
main .single-news,
main .modern-blog-post,
main .service-card {
    max-width: 100%;
    overflow-wrap: anywhere;
}

.mega-columns {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
    min-width: 0;
}

.mega-column-title {
    margin: 0 0 9px;
    color: #2c2d3f;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.03em;
    line-height: 1.25;
    text-transform: uppercase;
}

.mega-link-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.header .mega-link-list li {
    float: none !important;
    margin: 0 !important;
    position: static !important;
}

.header .mega-link-list a {
    display: block !important;
    padding: 5px 0 !important;
    color: #2c2d3f !important;
    font-size: 15px !important;
    font-weight: 400 !important;
    line-height: 1.35;
    text-transform: none !important;
    white-space: normal !important;
    overflow-wrap: anywhere;
}

.header .mega-link-list a::before {
    display: none !important;
}

.header .mega-link-list a:hover {
    color: #2f5597 !important;
    background: transparent !important;
}


.mega-link-description {
    display: block;
    margin-top: 2px;
    color: #788091;
    font-size: 12px;
    font-weight: 400;
    line-height: 1.4;
    white-space: normal !important;
    overflow-wrap: anywhere;
    word-break: normal;
    max-width: 100%;
}

.mobile-mega-panel {
    display: none;
    background: #f8f9fe;
    border-top: 1px solid #e5e8f4;
    padding: 14px 18px 18px;
}

.mobile-mega-panel.open {
    display: block;
}

.mobile-mega-section + .mobile-mega-section {
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid #e6e9f4;
}

.mobile-mega-section h4 {
    margin: 0 0 8px;
    color: #272b45;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

.mobile-dropdown-menu .mobile-mega-panel a {
    padding: 8px 0;
    color: #33416d;
    font-size: 14px;
    font-weight: 500;
}

.mobile-dropdown-menu .mobile-mega-panel a:hover {
    background: transparent;
    color: #2f5597;
}

.mobile-dropdown-menu .sub-toggle {
    border: 0;
    width: 100%;
    background: transparent;
    text-align: left;
    font-family: inherit;
}

@keyframes menuNewBadgePulse {
    0%, 100% {
        transform: translateY(0) scale(1);
        box-shadow: 0 0 0 0 rgba(47, 85, 151, 0.35);
    }
    50% {
        transform: translateY(-1px) scale(1.05);
        box-shadow: 0 0 0 4px rgba(47, 85, 151, 0);
    }
}

@keyframes menuNewBadgePulseOrange {
    0%, 100% {
        transform: translateY(0) scale(1);
        box-shadow: 0 0 0 0 rgba(255, 138, 61, 0.38);
    }
    50% {
        transform: translateY(-1px) scale(1.05);
        box-shadow: 0 0 0 5px rgba(255, 138, 61, 0);
    }
}

/* Desktop header: keep ALL menu items on one row, original sizes */
@media (min-width: 1301px) {
    /* Turn the Bootstrap row into a non-wrapping flex row */
    .header .header-inner .inner > .row {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
    }

    /* Logo column: keep original size */
    .header .header-inner .inner > .row > .col-lg-3 {
        flex: 0 0 auto !important;
        width: auto !important;
        max-width: none !important;
    }

    /* Menu column: fill remaining space */
    .header .header-inner .inner > .row > .col-lg-9 {
        flex: 1 1 0% !important;
        width: auto !important;
        max-width: none !important;
        min-width: 0 !important;
    }

    /* Kill the theme float on .logo so the column doesn't collapse */
    .header .logo {
        float: none !important;
        display: block !important;
    }

    /* Kill float on nav items, use flexbox instead */
    .header .nav li {
        float: none !important;
        margin-right: 8px !important;
    }

    /* Desktop nav typography */
    .header .nav li a {
        font-size: 15px !important;
        color: #2c2d3f;
        font-family: Poppins, sans-serif !important;
        font-weight: 500 !important;
    }

    /* The nav list itself: single-line flex */
    .header .main-menu .nav.menu {
        display: flex !important;
        flex-wrap: nowrap !important;
        justify-content: flex-end !important;
        align-items: center !important;
        white-space: nowrap !important;
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
    }

    .header .main-menu .nav.menu > li {
        flex: 0 0 auto !important;
    }

    .header .main-menu .nav.menu > li > a:not(.mega-trigger) {
        min-height: 64px;
        display: inline-flex !important;
        align-items: center;
        padding: 0 10px !important;
    }

    /* Hide the empty get-quote column to reclaim space */
    .header .header-inner .inner > .row > .col-lg-2 {
        display: none !important;
    }
}

@media (max-width: 1500px) and (min-width: 1301px) {
    .header .mega-trigger,
    .header .main-menu .nav.menu > li > a:not(.mega-trigger) {
        padding-left: 7px !important;
        padding-right: 7px !important;
        font-size: 14.5px !important;
    }

    .header .nav li {
        margin-right: 6px !important;
    }
}

</style>
</head>
<body>

{{-- Include Search Modal --}}
@include('partials.search-modal')

{{-- Include Bot Interface --}}
@include('partials.bot-interface')

{{-- Include Cookies Consent --}}
@include('partials.cookies-consent')

{{-- Include AI Data Readiness Assessment Pop-up --}}
@include('partials.ai-readiness-popup')

<div class="announcement-banner default-background mb-4" id="announcementBanner">
    <span class="banner-item">
        &#127881; <b>Armely Store is now live!</b>
        Browse business technology products, request quotes, and manage orders online.
        <a target="_blank" rel="noopener noreferrer" href="{{ url('/store') }}">Shop Now</a>
    </span>
    <span class="close-btn" onclick="closeBanner()">&times;</span>
</div>

<header class="header">
    <div class="topbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12"></div>
                <div class="col-lg-9 col-md-8 col-12">
                    <ul class="top-contact">
                        <li><i class="fa fa-phone"></i><a href="tel:+19724600643" class="text-decoration-none text-dark">+1 972 460 0643</a></li>
                        <li><i class="fa fa-envelope"></i><a href="mailto:info@armely.com">info@armely.com</a></li>
                        <li><i class="fa fa-user"></i><a href="https://armely.powerappsportals.com/">Customer support</a></li>
                        <li><i class="fa fa-shopping-cart"></i><a href="{{ url('/store') }}" target="_blank" rel="noopener noreferrer">Armely Store <span class="menu-new-badge">New</span></a></li>
                        <li class="search-trigger"><i class="fa fa-search"></i> <a>Search</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="header-inner">
        <div class="container">
            <div class="inner">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-3 col-12 d-flex align-items-center">
                        <div class="logo d-flex align-items-center" style="padding: 0; margin: 0;">
                            <a href="{{ url('/') }}" class="armely-logo-wrap" style="margin: 0; padding: 0;" aria-label="Armely home">
                                <img src="{{ asset('images/logo/logo-replace-v2.png') }}" alt="Armely logo" class="img-fluid armely-logo" />
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-12 d-flex align-items-center justify-content-end">
                        {{-- Pure HTML/CSS hamburger - no JS dependency --}}
                        <button class="mobile-hamburger" id="mobileHamburger" aria-label="Open menu">
                            <span class="bar"></span>
                            <span class="bar"></span>
                            <span class="bar"></span>
                        </button>
                        <div class="mobile-nav"></div>
                        <div class="main-menu d-none d-lg-flex align-items-center justify-content-end w-100" style="height: 100%;">
                            <nav class="navigation w-100 d-flex align-items-center" style="height: 100%;">
                                <ul class="nav menu">
                                    <li class="mega-nav-item {{ request()->is('company','career','job-board','applications','social-impact','social-impact-details','customer-stories') ? 'active' : '' }}">
                                        <button type="button" class="mega-trigger" aria-expanded="false">
                                            Who We Are
                                            <i class="icofont-rounded-down mega-icon-closed" aria-hidden="true"></i>
                                            <i class="icofont-rounded-up mega-icon-open" aria-hidden="true"></i>
                                        </button>
                                        <div class="mega-panel" role="region" aria-label="Who We Are menu">
                                            <div class="mega-panel-inner">
                                                <a class="mega-feature-card" href="{{ route('company.index') }}">
                                                    <img src="{{ asset('images/blog/1740079738_career.png') }}" alt="Armely team and career story">
                                                    <div class="mega-feature-card-content">
                                                        <h3>Company Overview</h3>
                                                        <p>Learn about Armely, our customer outcomes, career opportunities, and social impact work.</p>
                                                    </div>
                                                </a>
                                                <div class="mega-columns">
                                                    <div>
                                                        <h3 class="mega-column-title">Company</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('company.index') }}">Company Overview<span class="mega-link-description">Learn who we are, what we do, and how we help clients.</span></a></li>
                                                            <li><a href="{{ route('partners.index') }}">Partners<span class="mega-link-description">Explore our technology and business partnerships.</span></a></li>
                                                        </ul>
                                                    </div>

                                                    <div>
                                                        <h3 class="mega-column-title">People</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('career.index') }}">Career Opportunities<span class="mega-link-description">Join Armely and grow with our team.</span></a></li>
                                                            <li><a href="{{ route('customer-stories.index') }}">Customer Stories<span class="mega-link-description">See how clients achieve outcomes with Armely.</span></a></li>
                                                        </ul>
                                                    </div>

                                                    <div>
                                                        <h3 class="mega-column-title">Impact</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('social-impact.index') }}">Social Impact<span class="mega-link-description">Discover our work in community and responsible innovation.</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                        </div>
                                    </li>
                                    <li class="mega-nav-item {{ request()->is('services','service-details*') ? 'active' : '' }}">
                                        <button type="button" class="mega-trigger" aria-expanded="false">
                                            What We Do
                                            <i class="icofont-rounded-down mega-icon-closed" aria-hidden="true"></i>
                                            <i class="icofont-rounded-up mega-icon-open" aria-hidden="true"></i>
                                        </button>
                                        <div class="mega-panel" role="region" aria-label="What We Do menu">
                                            <div class="mega-panel-inner">
                                                <a class="mega-feature-card" href="{{ route('services') }}">
                                                    <img src="{{ asset('images/blog/advisory_services.png') }}" alt="Consulting team working with data dashboards">
                                                    <div class="mega-feature-card-content">
                                                        <h3>Modernize with Armely</h3>
                                                        <p>Explore advisory, data, AI, cloud, and automation services built for measurable business outcomes.</p>
                                                    </div>
                                                </a>
                                               <div class="mega-columns">
    <div>
        <h3 class="mega-column-title">Strategy & Advisory</h3>
        <ul class="mega-link-list">
            <li>
                <a href="{{ route('service-details', ['name' => 'ai-consulting']) }}">
                    AI Consulting
                    <span class="mega-link-description">Define practical AI initiatives aligned to business goals.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'ai-advisory']) }}">
                    AI Advisory
                    <span class="mega-link-description">Create governance, adoption, and AI transformation roadmaps.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'data-strategy']) }}">
                    Data Strategy
                    <span class="mega-link-description">Build a modern data foundation for analytics and AI.</span>
                </a>
            </li>
        </ul>
    </div>

    <div>
        <h3 class="mega-column-title">Data & AI Platforms</h3>
        <ul class="mega-link-list">
            <li>
                <a href="{{ route('service-details', ['name' => 'fabric']) }}">
                    Microsoft Fabric
                    <span class="mega-link-description">Unified analytics, engineering, and business intelligence platform.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'databricks']) }}">
                    Databricks
                    <span class="mega-link-description">Advanced analytics and machine learning at scale.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'snowflake']) }}">
                    Snowflake
                    <span class="mega-link-description">Cloud-native data warehousing and data sharing solutions.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'generative-ai']) }}">
                    Generative AI
                    <span class="mega-link-description">Deploy enterprise AI experiences powered by LLMs.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'copilot']) }}">
                    Microsoft Copilot
                    <span class="mega-link-description">Increase productivity with AI-powered copilots.</span>
                </a>
            </li>
        </ul>
    </div>

    <div>
        <h3 class="mega-column-title">Business Applications</h3>
        <ul class="mega-link-list">
            <li>
                <a href="{{ route('service-details', ['name' => 'powerplatform']) }}">
                    Power Platform
                    <span class="mega-link-description">Low-code solutions for automation, apps, and analytics.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'powerapps']) }}">
                    Power Apps
                    <span class="mega-link-description">Build business applications faster with low-code development.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'powerautomate']) }}">
                    Power Automate
                    <span class="mega-link-description">Automate repetitive business processes and workflows.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'dynamics365']) }}">
                    Dynamics 365
                    <span class="mega-link-description">Connect customer, finance, and operations processes.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('service-details', ['name' => 'sharepointonline']) }}">
                    SharePoint Online
                    <span class="mega-link-description">Modern collaboration, document management, and intranets.</span>
                </a>
            </li>
        </ul>
    </div>
</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mega-nav-item {{ request()->is('industries') ? 'active' : '' }}">
                                        <button type="button" class="mega-trigger" aria-expanded="false">
                                            Who We Serve
                                            <i class="icofont-rounded-down mega-icon-closed" aria-hidden="true"></i>
                                            <i class="icofont-rounded-up mega-icon-open" aria-hidden="true"></i>
                                        </button>
                                        <div class="mega-panel" role="region" aria-label="Who We Serve menu">
                                            <div class="mega-panel-inner">
                                                <a class="mega-feature-card" href="{{ route('industries.index') }}">
                                                    <img src="{{ asset('images/blog/1.png') }}" alt="Business leaders reviewing client outcomes">
                                                    <div class="mega-feature-card-content">
                                                        <h3>Client Outcomes</h3>
                                                        <p>See how Armely helps teams modernize industry workflows with data, AI, and platform strategy.</p>
                                                    </div>
                                                </a>
                                                <div class="mega-columns">
                                                    <div>
                                                        <h3 class="mega-column-title">Core Industries</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('industries.index') }}#home">Healthcare<span class="mega-link-description">Modern data, AI, and workflow solutions for healthcare teams.</span></a></li>
                                                            <li><a href="{{ route('industries.index') }}#energy">Energy<span class="mega-link-description">Digital transformation for oil, gas, utilities, and energy operations.</span></a></li>
                                                        </ul>
                                                    </div>

                                                    <div>
                                                        <h3 class="mega-column-title">Public Sector</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('industries.index') }}#government">State &amp; Local Government<span class="mega-link-description">Secure Microsoft platform solutions for public-sector agencies.</span></a></li>
                                                        </ul>
                                                    </div>

                                                    <div>
                                                        <h3 class="mega-column-title">Operations & Services</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('industries.index') }}#transportation">Transportation &amp; Logistics<span class="mega-link-description">Improve planning, tracking, analytics, and delivery performance.</span></a></li>
                                                            <li><a href="{{ route('industries.index') }}#legal">Legal<span class="mega-link-description">Use data and automation to improve legal workflows and insight.</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mega-nav-item {{ request()->is('blog','blogs','resources','resources*','case-studies','events') ? 'active' : '' }}">
                                        <button type="button" class="mega-trigger" aria-expanded="false">
                                            Knowledge Hub
                                            <i class="icofont-rounded-down mega-icon-closed" aria-hidden="true"></i>
                                            <i class="icofont-rounded-up mega-icon-open" aria-hidden="true"></i>
                                        </button>
                                        <div class="mega-panel" role="region" aria-label="Knowledge Hub menu">
                                            <div class="mega-panel-inner">
                                                <a class="mega-feature-card" href="{{ route('resources.index') }}">
                                                    <img src="{{ asset('images/blog/1750947309_Icons__1980_x_1020_px_.webp') }}" alt="Digital insights and strategy graphic">
                                                    <div class="mega-feature-card-content">
                                                        <h3>Featured Thinking</h3>
                                                        <p>Browse practical guidance, client stories, and resources for technology decision makers.</p>
                                                    </div>
                                                </a>
                                                <div class="mega-columns">
                                                    <div>
                                                        <h3 class="mega-column-title">Learn</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('blog.index') }}">Blog Articles<span class="mega-link-description">Analysis, trends, and implementation guidance.</span></a></li>
                                                        </ul>
                                                    </div>
                                                    <div>
                                                        <h3 class="mega-column-title">Proof</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('case-studies.index') }}">Case Studies<span class="mega-link-description">How real projects moved from idea to value.</span></a></li>
                                                            <li><a href="{{ route('case-studies.index') }}#white-papers">White Papers<span class="mega-link-description">Deeper research and point-of-view content.</span></a></li>
                                                        </ul>
                                                    </div>
                                                    <div>
                                                        <h3 class="mega-column-title">Engage</h3>
                                                        <ul class="mega-link-list">
                                                            <li><a href="{{ route('resources.index') }}">Resources<span class="mega-link-description">Downloadable guides and explainers.</span></a></li>
                                                            <li><a href="{{ route('events.index') }}">Events<span class="mega-link-description">Webinars, sessions, and community moments.</span></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="{{ request()->is('mela-ai') ? 'active' : '' }}"><a href="{{ route('mela-ai') }}">Mela AI</a></li>
                                    <li class="{{ request()->is('contact') ? 'active' : '' }}"><a href="{{ route('contact') }}">Let's Talk</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-lg-2 col-12"><div class="get-quote"></div></div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Mobile navigation dropdown (rendered outside header for z-index stacking) --}}
<div class="mobile-dropdown-menu" id="mobileDropdownMenu">
    <ul>
        <li>
            <button type="button" class="sub-toggle">Who We Are <span class="mobile-caret" aria-hidden="true"></span></button>
            <div class="mobile-mega-panel">
                <div class="mobile-mega-section">
                    <h4>About Armely</h4>
                    <a href="{{ route('company.index') }}">Company Overview</a>
                    <a href="{{ route('career.index') }}">Career Opportunities</a>
                    <a href="{{ route('customer-stories.index') }}">Customer Stories</a>
                    <a href="{{ route('partners.index') }}">Partners</a>
                    <a href="{{ route('social-impact.index') }}">Social Impact</a>
                </div>
            </div>
        </li>
        <li>
            <button type="button" class="sub-toggle">What We Do <span class="mobile-caret" aria-hidden="true"></span></button>
            <div class="mobile-mega-panel">
                <div class="mobile-mega-section">
                    <h4>AI & Automation</h4>
                    <a href="{{ route('service-details', ['name' => 'ai-consulting']) }}">AI Consulting</a>
                    <a href="{{ route('service-details', ['name' => 'ai-advisory']) }}">AI Advisory</a>
                    <a href="{{ route('service-details', ['name' => 'generative-ai']) }}">Generative AI</a>
                    <a href="{{ route('service-details', ['name' => 'copilot']) }}">Microsoft Copilot</a>
                    <a href="{{ route('service-details', ['name' => 'virtualagents']) }}">Virtual Agents</a>
                    <a href="{{ route('service-details', ['name' => 'roboticprocessing']) }}">Robotic Process Automation</a>
                </div>
                <div class="mobile-mega-section">
                    <h4>Data & Analytics</h4>
                    <a href="{{ route('service-details', ['name' => 'data-strategy']) }}">Data Strategy</a>
                    <a href="{{ route('service-details', ['name' => 'data-science']) }}">Data Science</a>
                    <a href="{{ route('service-details', ['name' => 'fabric']) }}">Microsoft Fabric</a>
                    <a href="{{ route('service-details', ['name' => 'databricks']) }}">Databricks</a>
                    <a href="{{ route('service-details', ['name' => 'snowflake']) }}">Snowflake</a>
                </div>
                <div class="mobile-mega-section">
                    <h4>Business Platforms</h4>
                    <a href="{{ route('service-details', ['name' => 'powerplatform']) }}">Power Platform</a>
                    <a href="{{ route('service-details', ['name' => 'powerapps']) }}">Power Apps</a>
                    <a href="{{ route('service-details', ['name' => 'powerautomate']) }}">Power Automate</a>
                    <a href="{{ route('service-details', ['name' => 'dynamics365']) }}">Dynamics 365</a>
                    <a href="{{ route('service-details', ['name' => 'sharepointonline']) }}">SharePoint Online</a>
                </div>
            </div>
        </li>
        <li>
            <button type="button" class="sub-toggle">Who We Serve <span class="mobile-caret" aria-hidden="true"></span></button>
            <div class="mobile-mega-panel">
                <div class="mobile-mega-section">
                    <h4>Commercial</h4>
                    <a href="{{ route('industries.index') }}#banking-capital-markets">Banking & Capital Markets</a>
                    <a href="{{ route('industries.index') }}#insurance">Insurance</a>
                    <a href="{{ route('industries.index') }}#retail-consumer-services">Retail, Consumer & Services</a>
                    <a href="{{ route('industries.index') }}#manufacturing">Manufacturing</a>
                    <a href="{{ route('industries.index') }}#communications-media">Communications & Media</a>
                </div>
                <div class="mobile-mega-section">
                    <h4>Public & Regulated</h4>
                    <a href="{{ route('industries.index') }}#government">Government</a>
                    <a href="{{ route('industries.index') }}#health">Health</a>
                    <a href="{{ route('industries.index') }}#life-sciences">Life Sciences</a>
                    <a href="{{ route('industries.index') }}#utilities">Utilities</a>
                </div>
                <div class="mobile-mega-section">
                    <h4>Infrastructure</h4>
                    <a href="{{ route('industries.index') }}#energy-utilities">Energy & Utilities</a>
                    <a href="{{ route('industries.index') }}#oil-gas">Oil & Gas</a>
                    <a href="{{ route('industries.index') }}#transportation-logistics">Transportation & Logistics</a>
                    <a href="{{ route('industries.index') }}#space">Space</a>
                </div>
            </div>
        </li>
        <li>
            <button type="button" class="sub-toggle">Knowledge Hub <span class="mobile-caret" aria-hidden="true"></span></button>
            <div class="mobile-mega-panel">
                <div class="mobile-mega-section">
                    <h4>Learn</h4>
                    <a href="{{ route('blog.index') }}">Blog Articles</a>
                    <a href="{{ route('resources.index') }}">Resources</a>
                    <a href="{{ route('case-studies.index') }}#white-papers">White Papers</a>
                </div>
                <div class="mobile-mega-section">
                    <h4>Proof</h4>
                    <a href="{{ route('case-studies.index') }}">Case Studies</a>
                    <a href="{{ route('case-studies.index') }}#white-papers">White Papers</a>
                </div>
                
            </div>
        </li>
        <li><a href="{{ route('mela-ai') }}">Mela AI</a></li>
        <li class="menu-store-link"><a href="{{ url('/store') }}" target="_blank" rel="noopener noreferrer">Armely Store <span class="menu-new-badge">New</span></a></li>
        <li><a href="{{ route('contact') }}">Let's Talk</a></li>
    </ul>
</div>

<script>
// Mobile hamburger toggle - runs immediately, no jQuery dependency
(function() {
    var btn = document.getElementById('mobileHamburger');
    var menu = document.getElementById('mobileDropdownMenu');
    if (btn && menu) {
        // Position menu below the visible header bar, including after the page has scrolled.
        function positionMenu() {
            var headerBar = btn.closest('.header-inner');
            var headerRect = headerBar ? headerBar.getBoundingClientRect() : btn.getBoundingClientRect();
            var top = headerRect.bottom;

            if (top < 0 || top > window.innerHeight) {
                top = btn.getBoundingClientRect().bottom;
            }

            top = Math.max(0, Math.min(top + 1, window.innerHeight - 80));

            menu.style.top = top + 'px';
            menu.style.maxHeight = 'calc(100vh - ' + top + 'px)';
        }
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            positionMenu();
            menu.classList.toggle('open');
        });
        // Reposition on scroll
        window.addEventListener('scroll', function() {
            if (menu.classList.contains('open')) positionMenu();
        });
        // Reposition on resize; close if resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1300) {
                menu.classList.remove('open');
                closeMobilePanels();
            } else if (menu.classList.contains('open')) {
                positionMenu();
            }
        });
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.remove('open');
                closeMobilePanels();
            }
        });
        // Sub-menu toggles
        var toggles = menu.querySelectorAll('.sub-toggle');
        function closeMobilePanels() {
            for (var j = 0; j < toggles.length; j++) {
                toggles[j].classList.remove('expanded');
                toggles[j].setAttribute('aria-expanded', 'false');
                var siblingDd = toggles[j].nextElementSibling;
                if (siblingDd) siblingDd.classList.remove('open');
            }
        }
        for (var i = 0; i < toggles.length; i++) {
            toggles[i].setAttribute('aria-expanded', 'false');
            toggles[i].addEventListener('click', function(e) {
                e.preventDefault();
                var dd = this.nextElementSibling;
                var isOpen = dd && dd.classList.contains('open');

                closeMobilePanels();

                if (dd && !isOpen) {
                    dd.classList.add('open');
                    this.classList.add('expanded');
                    this.setAttribute('aria-expanded', 'true');
                }
            });
        }
    }
})();
</script>

<script>
// Desktop mega menu click/touch controls. Hover is handled by CSS.
(function() {
    var nav = document.querySelector('.main-menu .nav.menu');
    if (!nav) return;

    var items = nav.querySelectorAll('.mega-nav-item');
    function closeMegaMenus() {
        for (var i = 0; i < items.length; i++) {
            items[i].classList.remove('mega-open');
            var trigger = items[i].querySelector('.mega-trigger');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        }
    }

    for (var i = 0; i < items.length; i++) {
        var trigger = items[i].querySelector('.mega-trigger');
        if (!trigger) continue;

        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var item = this.closest('.mega-nav-item');
            var wasOpen = item.classList.contains('mega-open');
            closeMegaMenus();

            if (!wasOpen) {
                item.classList.add('mega-open');
                this.setAttribute('aria-expanded', 'true');
            }
        });

        trigger.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMegaMenus();
                this.focus();
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (!nav.contains(e.target)) {
            closeMegaMenus();
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth <= 1300) {
            closeMegaMenus();
        }
    });
})();
</script>

<main>
    @yield('content')
</main>

@include('partials.footer')

<!-- Core jQuery and Bootstrap Dependencies -->
<script src="{{ asset('js/jquery.min.js') }}?v={{ file_exists(public_path('js/jquery.min.js')) ? filemtime(public_path('js/jquery.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/popper.min.js') }}?v={{ file_exists(public_path('js/popper.min.js')) ? filemtime(public_path('js/popper.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/bootstrap.min.js') }}?v={{ file_exists(public_path('js/bootstrap.min.js')) ? filemtime(public_path('js/bootstrap.min.js')) : '' }}" defer></script>

<!-- jQuery Plugins -->
<script src="{{ asset('js/jquery-migrate-3.0.0.js') }}?v={{ file_exists(public_path('js/jquery-migrate-3.0.0.js')) ? filemtime(public_path('js/jquery-migrate-3.0.0.js')) : '' }}" defer></script>
<script src="{{ asset('js/niceselect.js') }}?v={{ file_exists(public_path('js/niceselect.js')) ? filemtime(public_path('js/niceselect.js')) : '' }}" defer></script>
<script src="{{ asset('js/slicknav.min.js') }}?v={{ file_exists(public_path('js/slicknav.min.js')) ? filemtime(public_path('js/slicknav.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/owl-carousel.js') }}?v={{ file_exists(public_path('js/owl-carousel.js')) ? filemtime(public_path('js/owl-carousel.js')) : '' }}" defer></script>
<script src="{{ asset('js/waypoints.min.js') }}?v={{ file_exists(public_path('js/waypoints.min.js')) ? filemtime(public_path('js/waypoints.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/jquery.counterup.min.js') }}?v={{ file_exists(public_path('js/jquery.counterup.min.js')) ? filemtime(public_path('js/jquery.counterup.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/easing.js') }}?v={{ file_exists(public_path('js/easing.js')) ? filemtime(public_path('js/easing.js')) : '' }}" defer></script>
<script src="{{ asset('js/wow.min.js') }}?v={{ file_exists(public_path('js/wow.min.js')) ? filemtime(public_path('js/wow.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/jquery.nav.js') }}?v={{ file_exists(public_path('js/jquery.nav.js')) ? filemtime(public_path('js/jquery.nav.js')) : '' }}" defer></script>
<script src="{{ asset('js/jquery.scrollUp.min.js') }}?v={{ file_exists(public_path('js/jquery.scrollUp.min.js')) ? filemtime(public_path('js/jquery.scrollUp.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}?v={{ file_exists(public_path('js/jquery.magnific-popup.min.js')) ? filemtime(public_path('js/jquery.magnific-popup.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/tilt.jquery.min.js') }}?v={{ file_exists(public_path('js/tilt.jquery.min.js')) ? filemtime(public_path('js/tilt.jquery.min.js')) : '' }}" defer></script>
<script src="{{ asset('js/steller.js') }}?v={{ file_exists(public_path('js/steller.js')) ? filemtime(public_path('js/steller.js')) : '' }}" defer></script>
<script src="{{ asset('js/bootstrap-datepicker.js') }}?v={{ file_exists(public_path('js/bootstrap-datepicker.js')) ? filemtime(public_path('js/bootstrap-datepicker.js')) : '' }}" defer></script>

<!-- Google reCAPTCHA v2 -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Main Scripts -->
<script src="{{ asset('js/main.js') }}?v={{ file_exists(public_path('js/main.js')) ? filemtime(public_path('js/main.js')) : '' }}" defer></script>

@stack('scripts')
</body>
</html>
