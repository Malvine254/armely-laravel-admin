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
    <link rel="preload" as="image" href="{{ asset('images/sliders/slider-1.webp') }}">

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
    
    <link rel="preload" href="{{ asset('css/owl-carousel.css') }}?v={{ file_exists(public_path('css/owl-carousel.css')) ? filemtime(public_path('css/owl-carousel.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/owl-carousel.css') }}?v={{ file_exists(public_path('css/owl-carousel.css')) ? filemtime(public_path('css/owl-carousel.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/datepicker.css') }}?v={{ file_exists(public_path('css/datepicker.css')) ? filemtime(public_path('css/datepicker.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/datepicker.css') }}?v={{ file_exists(public_path('css/datepicker.css')) ? filemtime(public_path('css/datepicker.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/animate.min.css') }}?v={{ file_exists(public_path('css/animate.min.css')) ? filemtime(public_path('css/animate.min.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/animate.min.css') }}?v={{ file_exists(public_path('css/animate.min.css')) ? filemtime(public_path('css/animate.min.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/magnific-popup.css') }}?v={{ file_exists(public_path('css/magnific-popup.css')) ? filemtime(public_path('css/magnific-popup.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}?v={{ file_exists(public_path('css/magnific-popup.css')) ? filemtime(public_path('css/magnific-popup.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/normalize_2.css') }}?v={{ file_exists(public_path('css/normalize_2.css')) ? filemtime(public_path('css/normalize_2.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/normalize_2.css') }}?v={{ file_exists(public_path('css/normalize_2.css')) ? filemtime(public_path('css/normalize_2.css')) : '' }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/responsive.css') }}?v={{ file_exists(public_path('css/responsive.css')) ? filemtime(public_path('css/responsive.css')) : '' }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v={{ file_exists(public_path('css/responsive.css')) ? filemtime(public_path('css/responsive.css')) : '' }}"></noscript>
    
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
/* Force mobile menu earlier to prevent menu overflow */
@media (max-width: 1300px) {
	.main-menu {
		display: none !important;
	}
	
	.mobile-nav {
		display: block !important;
	}
	
	.slicknav_menu {
		display: block !important;
		background: transparent !important;
	}
	
	.slicknav_btn {
		background: transparent !important;
	}
	
	.slicknav_icon-bar {
		background-color: #2f5597 !important;
		height: 4px !important;
		border-radius: 2px !important;
	}
	
	.slicknav_nav a,
	.slicknav_nav .slicknav_item a {
		color: #000000 !important;
	}
	
	.slicknav_nav a:hover {
		color: #2f5597 !important;
	}
}

/* Add padding to menu items */
.navigation .nav.menu > li > a {
	padding: 20px 0 !important;
}

/* Ensure mobile menu icon vertically centers next to logo */
.mobile-nav { display: flex; align-items: center; }
.mobile-nav .slicknav_menu { display: inline-flex; align-items: center; }
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
                        <div class="mobile-nav d-lg-none ms-3 me-2 align-self-center"></div>
                        <div class="main-menu d-flex align-items-center justify-content-end w-100" style="height: 100%;">
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

<main>
    @yield('content')
</main>

@include('partials.footer')
@stack('scripts')
</body>
</html>
