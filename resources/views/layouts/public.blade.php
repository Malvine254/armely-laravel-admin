<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="Site keywords here">
    <meta name="description" content="">
    <title>{{ $title ?? 'Armely' }}</title>

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
    left: 0;
    right: 0;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9998;
    max-height: 80vh;
    overflow-y: auto;
    padding: 15px 0;
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
    border-bottom: 1px solid #eee;
}
.mobile-dropdown-menu a {
    display: block;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    font-size: 15px;
}
.mobile-dropdown-menu a:hover {
    background: #f5f5f5;
    color: #2f5597;
}
.mobile-dropdown-menu .sub-toggle {
    cursor: pointer;
}
.mobile-dropdown-menu .dropdown {
    display: none;
    background: #f9f9f9;
}
.mobile-dropdown-menu .dropdown.open {
    display: block;
}
.mobile-dropdown-menu .dropdown a {
    padding-left: 35px;
    font-size: 14px;
}
.mobile-dropdown-menu .dropdown .dropdown a {
    padding-left: 50px;
    font-size: 13px;
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
    .mobile-dropdown-menu a {
        font-size: 14px;
        padding: 10px 15px;
    }
}

/* Add padding to menu items */
.navigation .nav.menu > li > a {
    padding: 20px 0 !important;
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
        🏆 <b>We Won Best AI Application – Global Hackathon!</b>
        Explore how we built our Smart Waste Management AI solution.
        <a target="_blank" href="https://github.com/Sammychesire/Smart-City-Waste-Management">Read More</a>
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
                            <a href="{{ route('home') }}" class="d-inline-flex align-items-center" style="margin: 0; padding: 0;">
                                <img src="{{ asset('images/logo/logo-replace.png') }}" alt="Armely logo" class="img-fluid" style="max-height: 52px; width: auto; display: block;" />
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
                                    <li class="{{ request()->is('company','career','job-board','applications','social-impact','social-impact-details') ? 'active' : '' }}"><a>Who We Are <i class="icofont-rounded-down"></i></a>
                                        <ul class="dropdown">
                                            <li><a href="{{ route('company.index') }}">Company Overview</a></li>
                                            <li><a href="{{ route('career.index') }}">Career Opportunities</a></li>
                                            <li><a href="{{ route('customer-stories.index') }}">Customer Stories</a></li>
                                            {{-- <li><a href="{{ route('team.index') }}">Our Team</a></li> --}}
                                            <li><a href="{{ route('social-impact.index') }}">Social Impact</a></li>
                                        </ul>
                                    </li>
                                    <li class="{{ request()->is('services','service-details*') ? 'active' : '' }}"><a>What We Do <i class="icofont-rounded-down"></i></a>
                                        <ul class="dropdown">
                                            <li><a href="{{ route('services') }}">All Services</a></li>
                                            <li><a>AI Services <i class="icofont-rounded-right"></i></a>
                                                <ul class="dropdown">
                                                    <li><a href="{{ route('service-details', ['name' => 'ai-consulting']) }}">AI Consulting</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'ai-advisory']) }}">AI Advisory</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'generative-ai']) }}">Generative AI</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'ai-poc-starter']) }}">AI PoC Starter</a></li>
                                                </ul>
                                            </li>
                                            <li><a>Data Services <i class="icofont-rounded-right"></i></a>
                                                <ul class="dropdown">
                                                    <li><a href="{{ route('service-details', ['name' => 'estimate-your-fabric-capacity']) }}">Estimate your Fabric Capacity</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'microsoft-fabric']) }}">Microsoft Fabric</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'data-science-and-analytics']) }}">Data Science and Analytics</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'data-strategy']) }}">Data Strategy</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'databricks']) }}">Databricks</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'snowflake']) }}">Snowflake</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'sql-&-data-warehousing']) }}">SQL & Data Warehousing</a></li>
                                                </ul>
                                            </li>
                                            <li><a>Digital Transformation <i class="icofont-rounded-right"></i></a>
                                                <ul class="dropdown">
                                                    <li><a href="{{ route('service-details', ['name' => 'api-data-access']) }}">API Data Access</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'microsoft-powerapps']) }}">Microsoft Powerapps</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'microsoft-power-automate']) }}">Microsoft Power Automate</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'microsoft-power-virtual-agents']) }}">Microsoft Power Virtual Agents</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'microsoft-power-pages']) }}">Microsoft Power Pages</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'microsoft-dynamics-365']) }}">Microsoft Dynamics 365</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'robotic-processing-automation']) }}">Robotic Processing Automation</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'sharepoint-online']) }}">Sharepoint Online</a></li>
                                                </ul>
                                            </li>
                                            {{-- Hidden per request --}}
                                            {{-- <li><a href="{{ route('service-details', ['name' => 'freemiums']) }}">Freemiums</a></li> --}}
                                            <li><a>Managed Services <i class="icofont-rounded-right"></i></a>
                                                <ul class="dropdown">
                                                    <li><a href="{{ route('service-details', ['name' => 'sql-server-support']) }}">SQL Server Support</a></li>
                                                    <li><a href="{{ route('service-details', ['name' => 'applications-support']) }}">Applications Support</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="{{ request()->is('industries') ? 'active' : '' }}"><a href="{{ route('industries.index') }}">Who We Serve</a></li>
                                    <li class="{{ request()->is('blog','customer-stories','case-studies') ? 'active' : '' }}"><a>Knowledge Hub <i class="icofont-rounded-down"></i></a>
                                        <ul class="dropdown">
                                            <li><a href="{{ route('blog.index') }}">Blog Articles</a></li>
                                            <li><a href="{{ route('customer-stories.index') }}">Customer Stories</a></li>
                                            <li><a href="{{ route('case-studies.index') }}">Case Studies</a></li>
                                            <li><a href="{{ route('case-studies.index') }}#white-papers">White Papers</a></li>
                                        </ul>
                                    </li>
                                        <li class="{{ request()->is('events') ? 'active' : '' }}"><a href="{{ route('events.index') }}">Events</a></li>
                                    <li class="{{ request()->is('all-partners') ? 'active' : '' }}"><a href="{{ route('partners.index') }}">Partners</a></li>
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
            <a class="sub-toggle">Who We Are &#9660;</a>
            <ul class="dropdown">
                <li><a href="{{ route('company.index') }}">Company</a></li>
                <li><a href="{{ route('career.index') }}">Career</a></li>
                <li><a href="{{ route('social-impact.index') }}">Social Impact</a></li>
            </ul>
        </li>
        <li>
            <a class="sub-toggle">What We Do &#9660;</a>
            <ul class="dropdown">
                <li><a href="{{ route('services') }}">All Services</a></li>
                <li><a href="{{ route('service-details', ['name' => 'ai-consulting']) }}">AI Consulting</a></li>
                <li><a href="{{ route('service-details', ['name' => 'ai-advisory']) }}">AI Advisory</a></li>
                <li><a href="{{ route('service-details', ['name' => 'generative-ai']) }}">Generative AI</a></li>
                <li><a href="{{ route('service-details', ['name' => 'ai-poc-starter']) }}">AI PoC Starter</a></li>
                <li><a href="{{ route('service-details', ['name' => 'estimate-your-fabric-capacity']) }}">Estimate your Fabric Capacity</a></li>
                <li><a href="{{ route('service-details', ['name' => 'microsoft-fabric']) }}">Microsoft Fabric</a></li>
                <li><a href="{{ route('service-details', ['name' => 'data-science-and-analytics']) }}">Data Science and Analytics</a></li>
                <li><a href="{{ route('service-details', ['name' => 'data-strategy']) }}">Data Strategy</a></li>
                <li><a href="{{ route('service-details', ['name' => 'databricks']) }}">Databricks</a></li>
                <li><a href="{{ route('service-details', ['name' => 'snowflake']) }}">Snowflake</a></li>
                <li><a href="{{ route('service-details', ['name' => 'sql-&-data-warehousing']) }}">SQL & Data Warehousing</a></li>
                <li><a href="{{ route('service-details', ['name' => 'api-data-access']) }}">API Data Access</a></li>
                <li><a href="{{ route('service-details', ['name' => 'microsoft-powerapps']) }}">Microsoft Powerapps</a></li>
                <li><a href="{{ route('service-details', ['name' => 'microsoft-power-automate']) }}">Microsoft Power Automate</a></li>
                <li><a href="{{ route('service-details', ['name' => 'microsoft-dynamics-365']) }}">Microsoft Dynamics 365</a></li>
                <li><a href="{{ route('service-details', ['name' => 'sharepoint-online']) }}">Sharepoint Online</a></li>
                <li><a href="{{ route('service-details', ['name' => 'sql-server-support']) }}">SQL Server Support</a></li>
                <li><a href="{{ route('service-details', ['name' => 'applications-support']) }}">Applications Support</a></li>
            </ul>
        </li>
        <li><a href="{{ route('industries.index') }}">Who We Serve</a></li>
        <li>
            <a class="sub-toggle">Knowledge Hub &#9660;</a>
            <ul class="dropdown">
                <li><a href="{{ route('blog.index') }}">Blog Articles</a></li>
                <li><a href="{{ route('customer-stories.index') }}">Customer Stories</a></li>
                <li><a href="{{ route('case-studies.index') }}">Case Studies</a></li>
                <li><a href="{{ route('case-studies.index') }}#white-papers">White Papers</a></li>
            </ul>
        </li>
        <li><a href="{{ route('events.index') }}">Events</a></li>
        <li><a href="{{ route('partners.index') }}">Partners</a></li>
        <li><a href="{{ route('contact') }}">Let's Talk</a></li>
    </ul>
</div>

<script>
// Mobile hamburger toggle - runs immediately, no jQuery dependency
(function() {
    var btn = document.getElementById('mobileHamburger');
    var menu = document.getElementById('mobileDropdownMenu');
    if (btn && menu) {
        // Position menu below header on open
        function positionMenu() {
            var header = document.querySelector('header.header');
            if (header) {
                menu.style.top = header.getBoundingClientRect().bottom + 'px';
            }
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
            } else if (menu.classList.contains('open')) {
                positionMenu();
            }
        });
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.remove('open');
            }
        });
        // Sub-menu toggles
        var toggles = menu.querySelectorAll('.sub-toggle');
        for (var i = 0; i < toggles.length; i++) {
            toggles[i].addEventListener('click', function(e) {
                e.preventDefault();
                var dd = this.nextElementSibling;
                if (dd) dd.classList.toggle('open');
            });
        }
    }
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
