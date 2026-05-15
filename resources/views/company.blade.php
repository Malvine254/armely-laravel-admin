@extends('layouts.public')

@section('title', 'Company - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/company-modern.css') }}">
<style>
  /* Theme Variables */
  :root {
    --default-color: #2f5597;
    --default-background: #2f5597;
  }
  
  /* Clean modern design */
  body {
    background: #f8f9fa;
  }
  
  .company-section {
    padding: 60px 0;
    background: #fff;
  }
  
  .company-section.default-background {
    background: transparent;
  }
  
  .section-title {
    text-align: center;
    margin-bottom: 40px;
  }
  
  .section-title h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
  }
  
  .section-title hr {
    width: 80px;
    height: 3px;
    margin: 0 auto;
    border: none;
  }
  
  .story-content {
    background: #fff;
    padding: 50px;
    border-radius: 16px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
  }
  
  .story-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: linear-gradient(to bottom, var(--default-color), var(--default-background));
  }
  
  .story-content p {
    font-size: 1.1rem;
    line-height: 1.9;
    color: #555;
    margin-bottom: 25px;
    text-align: justify;
    position: relative;
  }
  
  .story-content p:first-of-type::first-letter {
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1;
    float: left;
    margin: 5px 12px 0 0;
    color: var(--default-color);
  }
  
  .story-header {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
  }
  
  .story-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--default-color), var(--default-background));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    box-shadow: 0 4px 15px rgba(47, 85, 151, 0.3);
  }
  
  .story-icon i {
    font-size: 2rem;
    color: #fff;
  }
  
  .story-title-text {
    flex: 1;
  }
  
  .story-title-text h3 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 5px 0;
  }
  
  .story-title-text .subtitle {
    font-size: 1rem;
    color: #777;
    margin: 0;
  }
  
  .modern-card {
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
    border: 2px solid #f0f0f0;
    position: relative;
    overflow: hidden;
  }
  
  .modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--default-color), var(--default-background));
  }
  
  .modern-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    border-color: var(--default-color);
  }
  
  .entity-logo-wrapper {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  }
  
  .entity-logo-wrapper img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }
  
  .modern-card h5 {
    font-size: 1.4rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
  }
  
  .modern-card p {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #666;
    margin-bottom: 15px;
  }
  
  .modern-card h6 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-top: 20px;
    margin-bottom: 12px;
  }
  
  .modern-card ul {
    list-style: none;
    padding-left: 0;
  }
  
  .modern-card ul li {
    font-size: 1rem;
    color: #555;
    margin-bottom: 10px;
    padding-left: 5px;
  }
  
  .modern-card ul li i {
    margin-right: 10px;
    font-size: 1.1rem;
  }
  
  .modern-card img {
    border-radius: 8px;
  }
  
  .affiliation-section {
    background: #f8f9fa;
    padding: 60px 0;
  }
  
  .affiliation-section img {
    transition: transform 0.3s ease;
    filter: grayscale(20%);
  }
  
  .affiliation-section img:hover {
    transform: scale(1.05);
    filter: grayscale(0%);
  }
  
  .values-section {
    background: #fff;
    padding: 70px 0 80px;
  }
  
  .values-section .single-service {
    background: #fff;
    padding: 42px 34px 38px;
    border-radius: 10px;
    box-shadow: 0 16px 38px rgba(23, 39, 67, 0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    height: 100%;
    border: 1px solid #e8edf5;
    position: relative;
    overflow: hidden;
    text-align: center;
  }
  
  .values-section .single-service::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--default-background);
    opacity: 0;
    transition: opacity 0.25s ease;
  }

  .values-section .single-service:hover {
    transform: translateY(-5px);
    box-shadow: 0 22px 45px rgba(23, 39, 67, 0.13);
    border-color: rgba(47, 85, 151, 0.28);
  }

  .values-section .single-service:hover::before {
    opacity: 1;
  }
  
  .value-icon-wrap {
    width: 76px;
    height: 76px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--default-background);
    color: #fff;
    box-shadow: 0 12px 24px rgba(47, 85, 151, 0.24);
    margin-bottom: 26px;
    position: relative;
    z-index: 1;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  
  .values-section .single-service:hover .value-icon-wrap {
    transform: translateY(-3px);
    box-shadow: 0 16px 30px rgba(47, 85, 151, 0.32);
  }

  .values-section .single-service i {
    font-size: 2rem;
    line-height: 1 !important;
    color: inherit;
  }
  
  .values-section .single-service h4 {
    font-size: 1.35rem;
    font-weight: 700;
    margin: 0 0 16px 0;
    color: #2c3e50;
    position: relative;
    z-index: 1;
  }
  
  .values-section .single-service h4 a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  
  .values-section .single-service:hover h4 a {
    color: var(--default-color);
  }
  
  .values-section .single-service p {
    font-size: 1.02rem;
    line-height: 1.75;
    color: #68717d;
    margin: 0;
    position: relative;
    z-index: 1;
  }
  
  .values-section .col-lg-4 {
    margin-bottom: 30px;
  }
  
  @media (max-width: 768px) {
    .section-title h2 {
      font-size: 2rem;
    }
    
    .story-content {
      padding: 25px;
    }
    
    .modern-card {
      padding: 25px;
      margin-bottom: 20px;
    }

    .values-section .single-service {
      padding: 34px 24px;
    }
  }
</style>
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Company</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Company</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<section class="company-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="section-title">
          <h2>Our Story</h2>
          <hr class="default-background">
        </div>
        <div class="story-content">
          <div class="story-header">
            <div class="story-icon">
              <i class="fa fa-book"></i>
            </div>
            <div class="story-title-text">
              <h3>From Humble Beginnings to Industry Leaders</h3>
              <p class="subtitle">A Journey of Innovation & Excellence Since 2017</p>
            </div>
          </div>
          <p>What started as a small operation serving a single client has blossomed into a thriving enterprise delivering a wide range of cutting-edge solutions. Our company began humbly in January 2017, providing specialized data management services to local businesses. However, it wasn't long before word of our expertise and personalized approach spread, leading to rapid growth.</p>
          <p>Today, we proudly serve over 50 clients across multiple industries, offering innovative capabilities in data analytics, artificial intelligence, digital collaboration tools, and large-scale digital transformation projects. This remarkable expansion has been fueled by strategic partnerships with leading technology providers, allowing us to integrate best-in-class solutions and stay at the forefront of the ever-evolving business landscape.</p>
          <p>Our goal is to become an extension of your team, striving to be a trusted strategic partner that helps you navigate complex challenges and unlock new opportunities. Through our collaborative approach and commitment to delivering measurable results, we've become a trusted advisor to organizations seeking to harness the power of data, automation, and digital enablement. Our story is one of humble beginnings, tireless innovation, and a relentless pursuit of client success.</p>
@if(!empty($dbErrorMessage))
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-warning text-center" role="alert">
                <i class="icofont-warning-alt"></i> {{ $dbErrorMessage }}
            </div>
        </div>
    </div>
@endif
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Start portfolio -->
<section class="affiliation-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="section-title">
          <h2>Our Affiliations</h2>
          <hr class="default-background">
        </div>
      </div>
    </div>
    <div class="row justify-content-center align-items-center">
      <div class="col-md-2 col-6 mb-4 text-center">
        <img width="180" height="180" class="img-fluid" src="{{ asset('images/affiliation/mbe.svg') }}">
      </div>
      <div class="col-md-2 col-6 mb-4 text-center">
        <img width="180" height="180" class="img-fluid" src="{{ asset('images/affiliation/smb.svg') }}">
      </div>
      <div class="col-md-2 col-6 mb-4 text-center">
        <img width="160" height="160" class="img-fluid" src="{{ asset('images/affiliation/affliation1.png') }}">
      </div>
      <div class="col-md-2 col-6 mb-4 text-center">
        <img width="160" height="160" class="img-fluid" src="{{ asset('images/affiliation/fid.png') }}">
      </div>
      <div class="col-md-3 col-12 mb-4 text-center">
        <img width="200" class="img-fluid" src="{{ asset('images/affiliation/partner.png') }}">
      </div>
    </div>
  </div>
</section>
<!--/ End portfolio -->

<section class="company-section default-background">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="section-title">
          <h2 class="text-light">Our Innovation Brands</h2>
          <hr class="default-background">
          <p style="font-size: 1.1rem;  margin-top: 15px; max-width: 800px; margin-left: auto; margin-right: auto;" class="text-light">Explore the brands and solution experiences we use to showcase cutting-edge technology in action</p>
        </div>
        <div class="row g-4">
          <!-- Mela Card -->
          <div class="col-md-6" id="mela-ai">
            <div class="modern-card h-100">
              <div class="d-flex align-items-start mb-4">
                <div class="entity-logo-wrapper me-3">
                  <img src="{{ asset('images/logo/mela-logo.jpg') }}" alt="Mela Logo" />
                </div>
                <div>
                  <h5 class="mb-2">Mela – Your AI CoPilot</h5>
                  <span class="badge" style="background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%); color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem;">AI & Machine Learning</span>
                </div>
              </div>
              <p><strong>Mela</strong> represents Armely's AI experience for demonstrating how intelligent copilots, automation, and generative AI can be embedded into modern business workflows.</p>
              <p>It showcases how organizations embed AI in workflows—from building copilots with <strong>Copilot Studio</strong> to deploying machine learning and generative AI solutions.</p>
              <h6 class="mt-4 mb-3" style="color: #2c3e50; font-weight: 600; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Use Cases Demonstrated:</h6>
              <ul class="list-unstyled">
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Copilot Studio development</li>
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Retrieval-Augmented Generation (RAG)</li>
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Natural Language Processing (NLP)</li>
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> AI governance and security</li>
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Azure OpenAI integration</li>
              </ul>
            </div>
          </div>
          <!-- Step & Sip Card -->
          <div class="col-md-6" id="armely-store">
            <div class="modern-card h-100">
              <div class="d-flex align-items-start mb-4">
                <div class="entity-logo-wrapper me-3">
                  <img src="{{ asset('images/logo/logo-step.png') }}" alt="Step & Sip Logo" />
                </div>
                <div>
                  <h5 class="mb-2">Step & Sip – Data-Driven Coffee</h5>
                  <span class="badge" style="background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%); color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem;">Data Analytics & BI</span>
                </div>
              </div>
              <p><strong>Step & Sip</strong> represents Armely's data and analytics experience, showing how modern retail operations can be improved through connected insights, forecasting, and automation.</p>
              <p>We demonstrate how coffee and data blend through real-time insights, automation, and intelligence—powered by <strong>Microsoft Fabric</strong> and <strong>Power Platform</strong>.</p>
              <h6 class="mt-4 mb-3" style="color: #2c3e50; font-weight: 600; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Use Cases Demonstrated:</h6>
              <ul class="list-unstyled">
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Microsoft Fabric Lakehouse architecture</li>
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Power BI dashboards and insights</li>
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Customer segmentation and behavior</li>
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Inventory and sales forecasting</li>
                <li style="margin-bottom: 12px;"><i class="fa fa-check-circle default-color" style="margin-right: 10px;"></i> Workflow automation with Power Automate</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Start service -->
<section class="values-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="section-title">
          <h2>Our Values</h2>
          <hr class="default-background">
        </div>
      </div>
    </div>
    <div class="row justify-content-center">
      @forelse($coreValues as $value)
        @php
          $titleKey = strtolower((string) ($value->title ?? ''));
          $titleIcons = [
              'integrity' => 'fa fa-shield',
              'innovation' => 'fa fa-lightbulb-o',
              'collaboration' => 'fa fa-users',
              'accountability' => 'fa fa-check-circle',
              'empowerment' => 'fa fa-rocket',
              'customer centricity' => 'fa fa-heart',
              'customer-centricity' => 'fa fa-heart',
              'customer focus' => 'fa fa-heart',
              'customer success' => 'fa fa-handshake-o',
              'excellence' => 'fa fa-star',
              'ownership' => 'fa fa-flag',
              'trust' => 'fa fa-shield',
              'teamwork' => 'fa fa-users',
              'service' => 'fa fa-heart',
          ];
          $approvedIcons = [
              'fa fa-shield',
              'fa fa-lightbulb-o',
              'fa fa-users',
              'fa fa-handshake-o',
              'fa fa-check-circle',
              'fa fa-star',
              'fa fa-rocket',
              'fa fa-heart',
              'fa fa-flag',
              'fa fa-bullseye',
              'fa fa-line-chart',
              'fa fa-trophy',
              'icofont-shield-alt',
              'icofont-light-bulb',
              'icofont-users-alt-5',
              'icofont-handshake-deal',
              'icofont-rocket-alt-2',
              'icofont-star',
          ];
          $fallbackIcons = ['fa fa-shield', 'fa fa-lightbulb-o', 'fa fa-users', 'fa fa-check-circle', 'fa fa-rocket', 'fa fa-heart'];
          $rawIcon = trim((string) ($value->icon ?? ''));
          $iconClass = $fallbackIcons[$loop->index % count($fallbackIcons)];
          $hasTitleFallback = false;

          foreach ($titleIcons as $needle => $className) {
              if (str_contains($titleKey, $needle)) {
                  $iconClass = $className;
                  $hasTitleFallback = true;
                  break;
              }
          }

          if (!$hasTitleFallback && $rawIcon !== '') {
              $normalizedRawIcon = $rawIcon;

              if (!str_contains($normalizedRawIcon, ' ')) {
                  if (str_starts_with($normalizedRawIcon, 'fa-')) {
                      $normalizedRawIcon = 'fa ' . $normalizedRawIcon;
                  } elseif (!str_starts_with($normalizedRawIcon, 'icofont-')) {
                      $normalizedRawIcon = 'icofont-' . ltrim($normalizedRawIcon, '-');
                  }
              }

              if (in_array($normalizedRawIcon, $approvedIcons, true)) {
                  $iconClass = $normalizedRawIcon;
              }
          }
        @endphp
        <div class="col-md-6 col-lg-4">
          <div class="single-service">
            <span class="value-icon-wrap" aria-hidden="true">
              <i class="{{ $iconClass }}"></i>
            </span>
            <h4><a href="#">{{ $value->title }}</a></h4>
            <p>{{ $value->body }}</p>
          </div>
        </div>
      @empty
        <div class="col-12">
          <p class="text-center text-muted">No core values available at this time.</p>
        </div>
      @endforelse
    </div>
  </div>
</section>
<!--/ End service -->
@endsection
