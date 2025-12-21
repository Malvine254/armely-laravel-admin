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
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('style7.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icofont.css') }}">
    
    <!-- Enhanced Search & Bot Styles -->
    <link rel="stylesheet" href="{{ asset('css/search-enhanced.css') }}">
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <!-- Non-Critical CSS - Deferred Loading -->
    <link rel="preload" href="{{ asset('css/nice-select.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/nice-select.css') }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/slicknav.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/slicknav.min.css') }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/owl-carousel.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/owl-carousel.css') }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/datepicker.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/datepicker.css') }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/animate.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/animate.min.css') }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/magnific-popup.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/normalize_2.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/normalize_2.css') }}"></noscript>
    
    <link rel="preload" href="{{ asset('css/responsive.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('css/responsive.css') }}"></noscript>
    
    <!-- Lozad.js for Lazy Loading -->
        <script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js" defer></script>
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
</style>
</head>
<body>

{{-- Include Search Modal --}}
@include('partials.search-modal')

{{-- Include Bot Interface --}}
@include('partials.bot-interface')

{{-- Include Cookies Consent --}}
@include('partials.cookies-consent')

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
                <div class="row">
                    <div class="col-lg-3 col-md-23 col-12">
                        <div class="logo"><a href="{{ route('home') }}"><span class="logo-font">armely</span></a></div>
                        <div class="mobile-nav"></div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-12">
                        <div class="main-menu">
                            <nav class="navigation">
                                <ul class="nav menu">
                                    <li class="{{ request()->is('company','career','job-board','applications','social-impact','social-impact-details') ? 'active' : '' }}"><a>Who We Are <i class="icofont-rounded-down"></i></a>
                                        <ul class="dropdown">
                                            <li><a href="{{ route('company.index') }}">Company Overview</a></li>
                                            <li><a href="{{ route('career.index') }}">Career Opportunities</a></li>
                                            <li><a href="{{ route('customer-stories.index') }}">Customer Stories</a></li>
                                            <li><a href="{{ route('team.index') }}">Our Team</a></li>
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
                                            <li><a href="{{ route('service-details', ['name' => 'freemiums']) }}">Freemiums</a></li>
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
