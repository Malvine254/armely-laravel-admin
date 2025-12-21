@extends('layouts.public')

@section('title', 'Partners - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/partners-modern.css') }}">
<style>
/* ===== MODERN CAROUSEL STYLES ===== */
.modern-carousel-wrapper {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    border-radius: 20px;
    box-shadow: 0 25px 70px rgba(47, 85, 151, 0.4);
    margin-bottom: 60px;
}

.modern-carousel-wrapper::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotateGlow 20s linear infinite;
    z-index: 1;
    pointer-events: none;
}

@keyframes rotateGlow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

#modernFadeCarousel {
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    z-index: 2;
}

.carousel-item {
    transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative;
    min-height: 550px;
    opacity: 0;
}

.carousel-item.active,
.carousel-item.pre-active {
    opacity: 1;
}

.carousel-item.active {
    display: block;
}

/* Ensure smooth cross-fade */
.carousel-fade .carousel-item {
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.carousel-fade .carousel-item.active,
.carousel-fade .carousel-item-next.carousel-item-start,
.carousel-fade .carousel-item-prev.carousel-item-end {
    opacity: 1;
    z-index: 1;
}

.carousel-fade .carousel-item-next:not(.carousel-item-start),
.carousel-fade .carousel-item-prev:not(.carousel-item-end),
.carousel-fade .active.carousel-item-start,
.carousel-fade .active.carousel-item-end {
    opacity: 0;
    transition: opacity 0s 1s;
}

/* Prevent flash on load */
#modernFadeCarousel:not(.carousel-loaded) .carousel-item:not(.active) {
    display: none !important;
}

.carousel-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(47, 85, 151, 0.6) 0%, rgba(30, 58, 109, 0.4) 100%);
    z-index: 1;
}

.carousel-item img {
    height: 550px;
    object-fit: cover;
    filter: brightness(0.7) contrast(1.15) saturate(1.2);
    animation: none;
    transform-origin: center;
    will-change: transform;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.carousel-item.active img {
    animation: slideZoom 20s ease-out forwards;
}

@keyframes slideZoom {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(1.05);
    }
}

/* Center carousel caption */
.carousel-caption {
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    text-align: center !important;
    width: 90% !important;
    max-width: 900px !important;
    bottom: auto !important;
    background: rgba(255, 255, 255, 0.95);
    padding: 50px 40px;
    border-radius: 20px;
    backdrop-filter: blur(20px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    z-index: 2;
}

.carousel-caption::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #2f5597 0%, #0d9488 100%);
    border-radius: 20px 20px 0 0;
}

/* Badge for caption */
.carousel-caption .caption-badge {
    display: inline-block;
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
    animation: fadeInDown 0.8s ease-out;
    box-shadow: 0 4px 15px rgba(47, 85, 151, 0.4);
}

/* Make title & subtitle readable */
.carousel-caption h3 {
    font-size: 52px;
    font-weight: 800;
    background: linear-gradient(135deg, #1a202c 0%, #2f5597 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 20px;
    letter-spacing: -1px;
    animation: fadeInDown 1s ease-out 0.2s both;
    line-height: 1.2;
}

.carousel-caption p {
    font-size: 18px;
    margin-top: 15px;
    color: #4a5568;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.8;
    animation: fadeInUp 1s ease-out 0.4s both;
}

.carousel-caption .caption-cta {
    display: inline-block;
    margin-top: 25px;
    padding: 12px 35px;
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    font-weight: 600;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s ease;
    animation: fadeInUp 1s ease-out 0.6s both;
    box-shadow: 0 10px 25px rgba(47, 85, 151, 0.3);
}

.carousel-caption .caption-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(47, 85, 151, 0.4);
    color: white;
    text-decoration: none;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Carousel Controls */
.carousel-control-prev,
.carousel-control-next {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.7;
    transition: all 0.3s ease;
    z-index: 10;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    background: rgba(255, 255, 255, 0.4);
    opacity: 1;
}

.carousel-control-prev {
    left: 20px;
}

.carousel-control-next {
    right: 20px;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 20px;
    height: 20px;
    background-size: 100% 100%;
}

/* Carousel Indicators */
.carousel-indicators {
    bottom: 30px;
    display: flex;
    justify-content: center;
    gap: 10px;
    z-index: 1000;
    position: absolute;
    left: 0;
    right: 0;
    margin: 0;
    padding: 0;
    list-style: none;
}

.carousel-indicators li {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    border: 2px solid rgba(255, 255, 255, 0.8);
    opacity: 0.7;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 0;
    cursor: pointer;
    text-indent: 0;
}

.carousel-indicators li.active {
    background: linear-gradient(135deg, #2f5597 0%, #0d9488 100%);
    width: 40px;
    border-radius: 10px;
    opacity: 1;
    border-color: white;
    box-shadow: 0 4px 15px rgba(47, 85, 151, 0.5);
}

/* Responsive Carousel */
@media (max-width: 768px) {
    .carousel-item {
        min-height: 350px;
    }
    
    .carousel-item img {
        height: 350px;
    }
    
    .carousel-caption h3 {
        font-size: 28px;
        padding: 0;
    }
    
    .carousel-caption p {
        font-size: 14px;
        margin-top: 10px;
    }
    
    .carousel-caption {
        padding: 25px 20px;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 40px;
        height: 40px;
    }
}

@media (max-width: 480px) {
    .carousel-item {
        min-height: 280px;
    }
    
    .carousel-item img {
        height: 280px;
    }
    
    .carousel-caption h3 {
        font-size: 20px;
    }
    
    .carousel-caption p {
        font-size: 12px;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 35px;
        height: 35px;
        display: none;
    }
}

/* ===== BENEFIT CARD STYLES ===== */
.benefit-card {
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.benefit-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
}

.benefit-icon {
    transition: all 0.3s ease;
}

.benefit-card:hover .benefit-icon {
    transform: scale(1.1);
}

/* ===== TIER CARD STYLES ===== */
.tier-card {
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.tier-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
}

.tier-header {
    position: relative;
    overflow: hidden;
}

.tier-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    transition: left 0.5s ease;
}

.tier-card:hover .tier-header::before {
    left: 100%;
}

.tier-features {
    list-style: none;
    padding: 0;
}

.tier-features li {
    transition: all 0.2s ease;
}

.tier-features li:hover {
    transform: translateX(5px);
}

/* ===== STORY CARD STYLES ===== */
.story-card {
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.story-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.story-badge {
    padding: 0;
    min-height: 60px;
    display: flex;
    align-items: center;
}

.story-content {
    display: flex;
    flex-direction: column;
}

.story-metrics {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    padding-top: 15px;
    margin-top: auto;
}

/* ===== SECTION TITLE STYLES ===== */
.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 10px;
    letter-spacing: -0.5px;
}

@media (max-width: 768px) {
    .section-title {
        font-size: 1.8rem;
    }
    
    .benefit-card,
    .tier-card,
    .story-card {
        margin-bottom: 15px;
    }
}

.partner-logos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    align-items: center;
    justify-items: center;
    margin-top: 50px;
}

.partner-item {
    width: 100%;
    max-width: 200px;
}

.partner-card {
    background: var(--bs-body-bg);
    padding: 25px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 150px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
}

.partner-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.partner-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    border-color: currentColor;
}

.partner-card:hover::before {
    left: 100%;
}

.partner-item img {
    width: 100%;
    max-width: 160px;
    max-height: 90px;
    object-fit: contain;
    transition: all 0.3s ease;
    filter: grayscale(100%);
}

.partner-card:hover img {
    filter: grayscale(0%);
    transform: scale(1.08);
}

.partner-item a {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

@media (max-width: 768px) {
    .partner-logos {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }
    
    .partner-card {
        min-height: 120px;
        padding: 20px;
    }
    
    .partner-item img {
        max-width: 120px;
        max-height: 70px;
    }
}

@media (max-width: 480px) {
    .partner-logos {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
    }
    
    .partner-card {
        min-height: 100px;
        padding: 15px;
    }
    
    .partner-item img {
        max-width: 100px;
        max-height: 60px;
    }
}

/* ===== EXPERTISE CARD ENHANCEMENTS ===== */
.expertise-card:hover {
    transform: translateY(-5px);
    background: rgba(255,255,255,0.2) !important;
}

.expertise-box {
    animation: fadeInUp 0.8s ease-out;
}

/* Enhanced Partner Grid Styles */
.modern-partner-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-top: 3rem;
}

.modern-partner-grid .partner-card {
    position: relative;
    overflow: hidden;
    background: white;
    border-radius: 16px;
    padding: 30px;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border: 2px solid transparent;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-partner-grid .partner-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 1;
    pointer-events: none;
}

.modern-partner-grid .partner-card:hover::after {
    opacity: 0.05;
}

.modern-partner-grid .partner-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(47, 85, 151, 0.2);
    border-color: #2f5597;
}

.modern-partner-grid .partner-item {
    position: relative;
    z-index: 2;
    width: 100%;
    height: 100%;
}

.modern-partner-grid .partner-item a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    text-decoration: none;
    position: relative;
    z-index: 2;
}

.modern-partner-grid .partner-item img {
    max-width: 160px;
    max-height: 90px;
    object-fit: contain;
    width: 100%;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    filter: grayscale(0%) opacity(1);
}

.modern-partner-grid .partner-card:hover img {
    filter: grayscale(0%) opacity(1) brightness(1.1);
    transform: scale(1.1);
}

/* Responsive adjustments for expertise section */
@media (max-width: 768px) {
    .modern-partner-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }
    
    .expertise-box {
        padding: 2rem !important;
    }
    
    .expertise-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="modern-carousel-wrapper">
  <div id="modernFadeCarousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="8000">

    <ol class="carousel-indicators">
      <li data-target="#modernFadeCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#modernFadeCarousel" data-slide-to="1"></li>
      <li data-target="#modernFadeCarousel" data-slide-to="2"></li>
    </ol>

    <div class="carousel-inner">

      <!-- Slide 1 -->
      <div class="carousel-item active">
        <img src="{{ asset('images/services/service5.jpg') }}" class="d-block w-100" alt="Partner Ecosystem">
        <div class="carousel-caption">
          <span class="caption-badge"><i class="fas fa-handshake me-2"></i> Strategic Partnerships</span>
          <h3>Microsoft • AWS • Snowflake • Red Hat</h3>
          <p>Trusted partnerships powering enterprise innovation across cloud, AI, and digital transformation.</p>
          <a href="#clients" class="caption-cta"> Explore Partners <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item">
        <img src="{{ asset('images/services/service3.jpg') }}" class="d-block w-100" alt="AI and Cloud Partners">
        <div class="carousel-caption">
          <span class="caption-badge"><i class="fas fa-cloud me-2"></i> Elite Technology Partners</span>
          <h3>AI, Cloud & Data Excellence</h3>
          <p>Accelerating business outcomes through deep expertise across Microsoft, Snowflake, AWS, and Red Hat ecosystems.</p>
          <a href="#clients" class="caption-cta">Learn More <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="carousel-item">
        <img src="{{ asset('images/services/service4.jpg') }}" class="d-block w-100" alt="Security and Infrastructure Partners">
        <div class="carousel-caption">
          <span class="caption-badge"><i class="fas fa-shield-alt me-2 p-1"></i> Security & Infrastructure</span>
          <h3>Cisco • Guardz • Enterprise Security</h3>
          <p>Strengthening your cloud, data, and infrastructure with world-class security and connectivity partners.</p>
          <a href="#clients" class="caption-cta">View Solutions <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
      </div>

    </div>

    <a class="carousel-control-prev" href="#modernFadeCarousel" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </a>
    <a class="carousel-control-next" href="#modernFadeCarousel" role="button" data-slide="next">
      <span class="carousel-control-next-icon"></span>
    </a>

  </div>
</div>

<section id="clients" class="partner-section py-5" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <div class="section-header modern-section-title text-center mb-5">
           
            <h2 class="section-title fw-bold mb-3" style="font-size: 2.5rem; color: #1a202c;">Technology Partners</h2>
            <div class="d-flex justify-content-center mb-4">
                <hr class="default-background" style="width: 100px; height: 4px; opacity: 1; border-radius: 2px;">
            </div>

            <p class="lead mx-auto" style="max-width: 900px; font-size: 1.1rem; color: #4a5568; line-height: 1.8;">
                In today's fast-paced digital landscape, success is often achieved through strategic 
                technology choices and seamless integrations. Our consulting approach is built upon a 
                deep understanding of the global technology ecosystem, allowing us to architect and 
                implement solutions that deliver maximum value, innovation, and compatibility for our clients.
            </p>

            <p class="mx-auto" style="max-width: 900px; color: #4a5568; line-height: 1.7;">
                We don't just recommend technology; we leverage our expertise across leading platforms 
                to ensure your investments are integrated, optimized, and aligned with your core business 
                objectives.
            </p>
        </div>

        <!-- Expertise Section -->
        <div class="row mb-5">
            <div class="col-lg-11 mx-auto">
                <div class="expertise-box p-5 rounded-4 shadow-lg" style="background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%); border: 2px solid rgba(255,255,255,0.1);">
                    <h4 class="text-white fw-bold mb-4 text-center" style="font-size: 1.75rem;">
                        <i class="fas fa-lightbulb me-2"></i> The Power of Informed Expertise
                    </h4>

                    <p class="text-white text-center mb-4" style="font-size: 1.05rem; opacity: 0.95;">
                        As consultants, our commitment is to your success, which is why we meticulously maintain 
                        proficiencies across a wide range of industry-leading tools.
                    </p>

                    <div class="row g-4 mt-3">
                        <div class="col-md-4">
                            <div class="expertise-card text-center p-4 rounded-3 h-100" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); transition: transform 0.3s ease, background 0.3s ease;">
                                <div class="icon-wrapper mb-3">
                                    <i class="fas fa-balance-scale fa-3x text-white" style="opacity: 0.9;"></i>
                                </div>
                                <h6 class="text-white fw-bold mb-2">Vendor Agnostic</h6>
                                <p class="text-white small mb-0" style="opacity: 0.85;">We choose the right technology for your needs, not just the one we partner with, guaranteeing unbiased recommendations.</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="expertise-card text-center p-4 rounded-3 h-100" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); transition: transform 0.3s ease, background 0.3s ease;">
                                <div class="icon-wrapper mb-3">
                                    <i class="fas fa-network-wired fa-3x text-white" style="opacity: 0.9;"></i>
                                </div>
                                <h6 class="text-white fw-bold mb-2">Deep Integration Knowledge</h6>
                                <p class="text-white small mb-0" style="opacity: 0.85;">Our teams specialize in creating flawless interoperability, turning disparate tools into a single, cohesive business engine.</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="expertise-card text-center p-4 rounded-3 h-100" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); transition: transform 0.3s ease, background 0.3s ease;">
                                <div class="icon-wrapper mb-3">
                                    <i class="fas fa-certificate fa-3x text-white" style="opacity: 0.9;"></i>
                                </div>
                                <h6 class="text-white fw-bold mb-2">Certified Mastery</h6>
                                <p class="text-white small mb-0" style="opacity: 0.85;">Our consultants hold advanced certifications, ensuring that implementation and optimization are handled with the highest level of technical competence.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRID OF LOGOS (replaces carousel) -->
        <div class="partner-logos modern-partner-grid">
            
            <div class="partner-item partner-card">
                <a href="{{ route('partners.show', ['slug' => 'aws']) }}" aria-label="AWS">
                    <img src="{{ asset('images/partners/aws.png') }}" alt="AWS">
                </a>
            </div>

            <div class="partner-item partner-card">
                <a href="{{ route('partners.show', ['slug' => 'td-synnex']) }}" aria-label="TD SYNNEX">
                    <img src="{{ asset('images/partners/td.png') }}" alt="TD SYNNEX">
                </a>
            </div>

            <div class="partner-item partner-card">
                <a href="{{ route('partners.show', ['slug' => 'snowflake']) }}" aria-label="Snowflake">
                    <img src="{{ asset('images/partners/snowflake1.png') }}" alt="Snowflake">
                </a>
            </div>

            <div class="partner-item partner-card">
                <a href="{{ route('partners.show', ['slug' => 'microsoft']) }}" aria-label="Microsoft">
                    <img src="{{ asset('images/partners/ms.png') }}" alt="Microsoft">
                </a>
            </div>

            <div class="partner-item partner-card">
                <a href="{{ route('partners.show', ['slug' => 'redhat']) }}" aria-label="Red Hat">
                    <img src="{{ asset('images/partners/redhat.jpg') }}" alt="Red Hat">
                </a>
            </div>
            
            <div class="partner-item partner-card">
                <a href="{{ route('partners.show', ['slug' => 'cisco']) }}" aria-label="Cisco">
                    <img src="{{ asset('images/partners/cisco.png') }}" alt="Cisco">
                </a>
            </div>
            
            <div class="partner-item partner-card">
                <a href="{{ route('partners.show', ['slug' => 'guardz']) }}" aria-label="Guardz">
                    <img src="{{ asset('images/partners/quardz.png') }}" alt="Guardz">
                </a>
            </div>
        </div> 

    </div>
</section>

<!-- ===== Partnership Benefits Section ===== -->
<section class="partnership-benefits py-5" style="background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h2 class="section-title mb-3 text-light">Why Partner With Us</h2>
                <p class="lead text-light">Unlock unprecedented value through our strategic partnerships</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Benefit 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card default-background rounded-lg p-4 h-100">
                    <div class="benefit-icon mb-3 text-light">
                        <i class="fas fa-rocket text-light" style="font-size: 2.5rem; color: #667eea;"></i>
                    </div>
                    <h4 class="text-light fw-bold mb-3">Accelerated Innovation</h4>
                    <p class="text-light">Leverage cutting-edge technologies and best practices to accelerate your digital transformation journey with proven methodologies.</p>
                </div>
            </div>

            <!-- Benefit 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card default-background rounded-lg p-4 h-100">
                    <div class="benefit-icon mb-3 text-light">
                        <i class="fas fa-shield-alt text-light" style="font-size: 2.5rem; color: #667eea;"></i>
                    </div>
                    <h4 class="text-light fw-bold mb-3">Enterprise Security</h4>
                    <p class="text-light">Benefit from robust security solutions and compliance frameworks that protect your critical business assets and data.</p>
                </div>
            </div>

            <!-- Benefit 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="benefit-card default-background rounded-lg p-4 h-100">
                    <div class="benefit-icon mb-3 ">
                        <i class="fas fa-chart-line text-light" style="font-size: 2.5rem; color: #667eea;"></i>
                    </div>
                    <h4 class="text-light fw-bold mb-3">Scalable Solutions</h4>
                    <p class="text-light">Build flexible, scalable architectures that grow with your business needs while maintaining optimal performance and reliability.</p>
                </div>
            </div>

            <!-- Benefit 4 -->
            <div class="col-lg-4 col-md-6 mt-4">
                <div class="benefit-card default-background rounded-lg p-4 h-100">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-users text-light" style="font-size: 2.5rem; color: #667eea;"></i>
                    </div>
                    <h4 class="text-light fw-bold mb-3">Expert Support</h4>
                    <p class="text-light">Access our team of certified experts available 24/7 to ensure successful implementation and ongoing optimization.</p>
                </div>
            </div>

            <!-- Benefit 5 -->
            <div class="col-lg-4 col-md-6 mt-4">
                <div class="benefit-card default-background rounded-lg p-4 h-100">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-cogs text-light" style="font-size: 2.5rem; color: #667eea;"></i>
                    </div>
                    <h4 class="text-light fw-bold mb-3">Seamless Integration</h4>
                    <p class="text-light">Connect all your systems effortlessly with our comprehensive integration expertise across multiple platforms and services.</p>
                </div>
            </div>

            <!-- Benefit 6 -->
            <div class="col-lg-4 col-md-6 mt-4">
                <div class="benefit-card default-background rounded-lg p-4 h-100">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-award text-light" style="font-size: 2.5rem; color: #667eea;"></i>
                    </div>
                    <h4 class="text-light fw-bold mb-3">Proven Excellence</h4>
                    <p class="text-light">Trust our track record of successful implementations with Fortune 500 companies and industry leaders worldwide.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== Partnership Tiers Section ===== -->
<section class="partnership-tiers py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h2 class="section-title mb-3 default-color">Our Partnership Ecosystem</h2>
                <p class="lead default-color">Multi-tiered partnerships designed to maximize your success</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Tier 1 -->
            <div class="col-lg-6">
                <div class="tier-card default-background rounded-lg overflow-hidden shadow-sm h-100">
                    <div class="tier-header p-4" style="background: linear-gradient(135deg, #4a7bc1 0%, #2f5597 100%);">
                        <h3 class="text-white mb-2">Strategic Partners</h3>
                        <p class="text-white-50 mb-0 text-light">Enterprise-grade solutions</p>
                    </div>
                    <div class="tier-body p-4">
                        <ul class="tier-features mb-0">
                            <li class="mb-3 text-light"><i class="fas fa-check me-2" ></i> Priority support and escalation</li>
                            <li class=" mb-3 text-light"><i class="fas fa-check me-2"></i> Dedicated account management</li>
                            <li class=" mb-3 text-light"><i class="fas fa-check me-2" ></i> Custom solution architecture</li>
                            <li class=" mb-3 text-light"><i class="fas fa-check me-2" ></i> Joint go-to-market opportunities</li>
                            <li class=" mb-3 text-light"><i class="fas fa-check me-2"></i> Advanced training programs</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tier 2 -->
            <div class="col-lg-6">
                <div class="tier-card default-background rounded-lg overflow-hidden shadow-sm h-100">
                    <div class="tier-header p-4" style="background: linear-gradient(135deg, #0d9488 0%, #14453f 100%);">
                        <h3 class="text-white mb-2 ">Technology Partners</h3>
                        <p class="text-white-50 mb-0 text-light">Innovative integrations</p>
                    </div>
                    <div class="tier-body p-4">
                        <ul class="tier-features mb-0 ">
                            <li class=" mb-3 text-light"><i class="fas fa-check me-2" ></i> Standard technical support</li>
                            <li class=" mb-3 text-light"><i class="fas fa-check me-2" ></i> API integration resources</li>
                            <li class=" mb-3 text-light"><i class="fas fa-check me-2" ></i> Community forum access</li>
                            <li class=" mb-3 text-light"><i class="fas fa-check me-2"></i> Regular product updates</li>
                            <li class=" text-light"><i class="fas fa-check me-2" ></i> Certification programs</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== Success Stories Section ===== -->
<section class="success-stories py-5" style="background: linear-gradient(135deg, #1e3a6d 0%, #0d9488 100%);">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h2 class="section-title mb-3 text-light">Partner Success Stories</h2>
                <p class="lead text-light">Real results from our strategic partnerships</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Story 1 -->
            <div class="col-lg-4 col-md-6" id="aws-story">
                <div class="story-card default-background rounded-lg overflow-hidden shadow-sm h-100 d-flex flex-column">
                    <div class="story-badge" style="background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);">
                        <span class="text-white fw-bold ps-3 py-2 d-block">AWS Partnership</span>
                    </div>
                    <div class="story-content p-4 flex-grow-1">
                        <h5 class="text-light fw-bold mb-2">Cloud Infrastructure Modernization</h5>
                        <p class="text-light mb-3">Migrated 500+ servers to AWS cloud, reducing infrastructure costs by 40% and improving system uptime to 99.99%.</p>
                        <div class="story-metrics">
                            <small class="text-light d-block mb-2"><strong>Result:</strong> $2M annual savings</small>
                            <small class="text-light"><strong>Timeline:</strong> 6 months</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Story 2 -->
            <div class="col-lg-4 col-md-6" id="snowflake-story">
                <div class="story-card default-background rounded-lg overflow-hidden shadow-sm h-100 d-flex flex-column">
                    <div class="story-badge" style="background: linear-gradient(135deg, #0d9488 0%, #14453f 100%);">
                        <span class="text-white fw-bold ps-3 py-2 d-block">Snowflake Partnership</span>
                    </div>
                    <div class="story-content p-4 flex-grow-1">
                        <h5 class="text-light fw-bold mb-2">Data Warehouse Consolidation</h5>
                        <p class="text-light mb-3">Unified 15+ data sources into Snowflake, enabling real-time analytics and reducing query times by 85%.</p>
                        <div class="story-metrics">
                            <small class="text-light d-block mb-2"><strong>Result:</strong> Real-time insights</small>
                            <small class="text-light"><strong>Timeline:</strong> 4 months</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Story 3 -->
            <div class="col-lg-4 col-md-6" id="microsoft-story">
                <div class="story-card default-background rounded-lg overflow-hidden shadow-sm h-100 d-flex flex-column">
                    <div class="story-badge" style="background: linear-gradient(135deg, #4a7bc1 0%, #2f5597 100%);">
                        <span class="text-white fw-bold ps-3 py-2 d-block">Microsoft Partnership</span>
                    </div>
                    <div class="story-content p-4 flex-grow-1">
                        <h5 class="text-light fw-bold mb-2">Digital Transformation</h5>
                        <p class="text-light mb-3">Implemented Azure services ecosystem including AI, ML, and automation, increasing productivity by 60%.</p>
                        <div class="story-metrics">
                            <small class="text-light d-block mb-2"><strong>Result:</strong> 60% productivity gain</small>
                            <small class="text-light"><strong>Timeline:</strong> 8 months</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== Partner CTA Section ===== -->
<section class="partner-cta py-5 default-background" style=" margin: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="text-white mb-3">Ready to Join Our Partner Ecosystem?</h2>
                <p class="text-white-50 lead mb-0 text-light">Unlock new revenue streams and expand your market reach through strategic partnership with Armely.</p>
            </div>
            <div class="col-lg-4 text-lg-end text-center mt-3 mt-lg-0">
                <a href="{{ route('contact') }}" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-light" style="color: #667eea;">
                    <i class="fas fa-handshake me-2 text-light"></i> Become a Partner
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Smooth Carousel Transition Fix
(function() {
    'use strict';
    
    var carousel = document.getElementById('modernFadeCarousel');
    
    if (carousel) {
        // Prevent default Bootstrap fade behavior issues
        var items = carousel.querySelectorAll('.carousel-item');
        
        // Preload all images to prevent flashing
        items.forEach(function(item) {
            var img = item.querySelector('img');
            if (img && img.complete) {
                img.style.opacity = '1';
            } else if (img) {
                img.addEventListener('load', function() {
                    img.style.opacity = '1';
                });
            }
        });
        
        // Enhanced fade transition with proper timing
        $(carousel).on('slide.bs.carousel', function(e) {
            var $animatingItem = $(e.relatedTarget);
            var $activeItem = $(carousel).find('.carousel-item.active');
            
            // Ensure smooth transition
            $animatingItem.addClass('pre-active');
            
            setTimeout(function() {
                $animatingItem.removeClass('pre-active');
            }, 50);
        });
        
        // Clean up after transition
        $(carousel).on('slid.bs.carousel', function(e) {
            $(carousel).find('.carousel-item').removeClass('pre-active');
        });
        
        // Ensure first load doesn't flash
        setTimeout(function() {
            carousel.classList.add('carousel-loaded');
        }, 100);
    }
})();
</script>
@endpush
