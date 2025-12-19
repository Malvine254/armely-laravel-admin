@extends('layouts.public')

@section('title', 'Customer Stories - Armely')

@push('styles')
<style>
/* Modern Section Header */
.modern-section-header {
    position: relative;
}

.header-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.badge-dot {
    width: 8px;
    height: 8px;
    background: var(--default-background);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.badge-text {
    color: var(--default-color);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 2px;
}

.section-main-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    position: relative;
}

.title-underline {
    width: 80px;
    height: 4px;
    background: var(--default-background);
    position: relative;
}

.title-underline::before {
    content: '';
    position: absolute;
    left: 50%;
        @if(!empty($dbErrorMessage))
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="icofont-warning-alt"></i> {{ $dbErrorMessage }}
                    </div>
                </div>
            </div>
        @endif
    top: 50%;
    transform: translate(-50%, -50%);
    width: 12px;
    height: 12px;
    background: var(--default-color);
    border-radius: 50%;
}

.section-description {
    color: #666;
    font-size: 16px;
    line-height: 1.8;
}

/* Modern Story Cards */
.modern-story-card {
    background: #fff;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.modern-story-card.default-background {
    color: #fff;
}

.modern-story-card.default-background .customer-name,
.modern-story-card.default-background .customer-position,
.modern-story-card.default-background .testimonial-content,
.modern-story-card.default-background .shorten-content,
.modern-story-card.default-background .quote-mark-left,
.modern-story-card.default-background .quote-mark-right,
.modern-story-card.default-background .rating-stars i {
    color: #fff !important;
    opacity: 1;
}

.modern-story-card.default-background .divider-line {
    background: rgba(255, 255, 255, 0.35);
}

.modern-story-card.default-background .read-more-btn {
    color: #fff !important;
    border-color: #fff;
}

.modern-story-card.default-background .read-more-btn:hover {
    background: #fff;
    color: var(--default-color) !important;
}

.modern-story-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--default-background), var(--default-color));
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.modern-story-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    border-color: var(--default-background);
}

.modern-story-card:hover::before {
    transform: translateX(0);
}

.card-header-section {
    padding: 30px 25px 20px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
    background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0) 100%);
}

.customer-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar-image {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--default-background);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.avatar-placeholder {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: var(--default-background);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    border: 4px solid var(--default-background);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.avatar-placeholder::before {
    content: attr(data-initials);
}

.modern-story-card:hover .avatar-image,
.modern-story-card:hover .avatar-placeholder {
    transform: rotate(5deg) scale(1.1);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.verified-badge {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 24px;
    height: 24px;
    background: var(--default-background);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.verified-badge i {
    font-size: 12px;
    color: #fff;
}

.customer-meta {
    flex: 1;
    min-width: 0;
    padding-top: 5px;
}

.customer-name {
    font-size: 17px;
    font-weight: 700;
    color: #222;
    margin: 0 0 5px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}

.customer-position {
    font-size: 13px;
    color: #666;
    margin: 0 0 8px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
}

.rating-stars {
    display: flex;
    gap: 2px;
}

.rating-stars i {
    font-size: 15px;
    color: #ffc107;
    animation: starPulse 2s ease-in-out infinite;
}

.rating-stars i:nth-child(1) { animation-delay: 0s; }
.rating-stars i:nth-child(2) { animation-delay: 0.1s; }
.rating-stars i:nth-child(3) { animation-delay: 0.2s; }
.rating-stars i:nth-child(4) { animation-delay: 0.3s; }
.rating-stars i:nth-child(5) { animation-delay: 0.4s; }

@keyframes starPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

.modern-story-card:hover .rating-stars i {
    animation: none;
    transform: scale(1.1);
}

.divider-line {
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--default-background), transparent);
    margin: 0 25px;
    opacity: 0.3;
}

.story-body {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.quote-wrapper {
    position: relative;
    flex-grow: 1;
    padding: 0 10px;
}

.quote-mark-left,
.quote-mark-right {
    position: absolute;
    font-size: 40px;
    opacity: 0.15;
    color: var(--default-color);
    pointer-events: none;
}

.modern-story-card.default-background .quote-mark-left,
.modern-story-card.default-background .quote-mark-right {
    color: #fff !important;
    opacity: 0.3;
}

.quote-mark-left {
    top: -10px;
    left: -5px;
}

.quote-mark-right {
    bottom: -10px;
    right: -5px;
}

.testimonial-content {
    color: #555;
    font-size: 15px;
    line-height: 1.9;
    font-style: italic;
    text-align: justify;
}

.modern-story-card.default-background .testimonial-content {
    color: #fff !important;
}

.modern-story-card.default-background .testimonial-content * {
    color: #fff !important;
}

.shorten-content {
    display: block;
    margin-bottom: 15px;
    color: inherit;
}

.modern-story-card.default-background .shorten-content {
    color: #fff !important;
}

.modern-story-card.default-background .shorten-content * {
    color: #fff !important;
}

.read-more-btn {
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    display: inline-block;
    padding: 10px 20px;
    border: 2px solid var(--default-background);
    border-radius: 25px;
    transition: all 0.3s ease;
    background: transparent;
}

.read-more-btn:hover {
    background: var(--default-background);
    color: #fff !important;
    text-decoration: none;
    transform: translateX(5px);
}

.read-more-btn strong {
    display: flex;
    align-items: center;
    gap: 8px;
}

.read-more-btn i {
    transition: transform 0.3s ease;
}

.read-more-btn:hover i {
    transform: translateX(5px);
}

.trust-stat {
    padding: 30px 20px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.trust-stat:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border-color: var(--default-background);
}

.stat-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--default-background);
    border-radius: 50%;
    transition: all 0.3s ease;
}

.stat-icon i {
    font-size: 32px;
    color: #fff;
}

.trust-stat:hover .stat-icon {
    transform: rotateY(360deg);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--default-color);
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 14px;
    margin: 0;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.2);
    }
}

@media (max-width: 768px) {
    .section-main-title {
        font-size: 2rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .card-header-section {
        padding: 25px 20px 15px;
        gap: 12px;
    }
    
    .avatar-image, .avatar-placeholder {
        width: 60px;
        height: 60px;
    }
    
    .customer-name {
        font-size: 15px;
    }
    
    .customer-position {
        font-size: 12px;
    }
    
    .story-body {
        padding: 20px;
    }
    
    .testimonial-content {
        font-size: 14px;
    }
    
    .quote-mark-left,
    .quote-mark-right {
        font-size: 30px;
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
				<div class="col-12 ">
					<h2 class=" mt-5">Customer Stories</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Customer Stories</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<!-- Start Customer Stories Section -->
<section class="customer-stories-section py-5">
    <div class="container">
        <!-- Section Header -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="modern-section-header text-center">
                    <div class="header-badge mb-3">
                        <span class="badge-dot"></span>
                        <span class="badge-dot"></span>
                    </div>
                    <h2 class="section-main-title mb-4">What Our Customers Say</h2>
                    <div class="title-underline mx-auto mb-4"></div>
                    <p class="section-description mx-auto" style="max-width: 800px;">
                        Here, you'll find the voices of our satisfied customers sharing their experiences with our products and services. 
                        Dive into these authentic reviews to get a glimpse of the quality, reliability, and exceptional service we strive to deliver. 
                        Discover why our customers trust us and why you should too.
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Stories Grid -->
        <div class="row g-4">
            @forelse($testimonials as $testimonial)
                @php
                    $nameParts = explode(' ', $testimonial->name);
                    $initials = '';
                    foreach ($nameParts as $part) {
                        if (!empty($part)) {
                            $initials .= strtoupper(substr($part, 0, 1));
                            if (strlen($initials) >= 2) break;
                        }
                    }
                    if (empty($initials)) $initials = 'U';
                    
                    $profilePath = $testimonial->profile ? asset('images/customer-stories/' . $testimonial->profile) : '';
                @endphp
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="modern-story-card default-background">
                        <div class="card-header-section">
                            <div class="customer-avatar">
                                @if($profilePath)
                                    <img src="{{ $profilePath }}" alt="{{ $testimonial->name }}" class="avatar-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="avatar-placeholder" style="display:none;" data-initials="{{ $initials }}"></div>
                                @else
                                    <div class="avatar-placeholder" data-initials="{{ $initials }}"></div>
                                @endif
                                <div class="verified-badge">
                                    <i class="icofont-check default-color"></i>
                                </div>
                            </div>
                            <div class="customer-meta">
                                <h5 class="customer-name text-light" title="{{ $testimonial->name }}">{{ $testimonial->name }}</h5>
                                <p class="text-light" title="{{ $testimonial->position }}">{{ $testimonial->position }}</p>
                                <div class="rating-stars">
                                    <i class="icofont-star"></i>
                                    <i class="icofont-star"></i>
                                    <i class="icofont-star"></i>
                                    <i class="icofont-star"></i>
                                    <i class="icofont-star"></i>
                                </div>
                            </div>
                        </div>
                        <div class="divider-line"></div>
                        <div class="story-body">
                            <div class="quote-wrapper">
                                <i class="icofont-quote-left quote-mark-left text-light"></i>
                                <div class="testimonial-content">
                                    <span class="shorten-content">{!! $testimonial->body_content !!}</span>
                                    <a id="{{ $testimonial->id }}" class="read-more-btn" href="#">
                                        <strong>READ MORE <i class="fa fa-long-arrow-right"></i></strong>
                                    </a>
                                </div>
                                <i class="icofont-quote-right quote-mark-right text-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="icofont-info-circle"></i> Customer testimonials coming soon!
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Trust Indicators -->
        <div class="row mt-5 pt-4">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="trust-stat text-center default-background">
                    <div class="stat-icon mb-3">
                        <i class="icofont-users-alt-4"></i>
                    </div>
                    <h3 class="stat-number text-light">500+</h3>
                    <p class="stat-label text-light">Happy Clients</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="trust-stat text-center default-background">
                    <div class="stat-icon mb-3">
                        <i class="icofont-star"></i>
                    </div>
                    <h3 class="stat-number text-light">4.9/5</h3>
                    <p class="stat-label text-light">Average Rating</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4 ">
                <div class="trust-stat text-center default-background">
                    <div class="stat-icon mb-3">
						<i class="fas fa-book text-light"></i>
                    </div>
                    <h3 class="stat-number text-light">98%</h3>
                    <p class="stat-label text-light">Success Rate</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="trust-stat text-center default-background">
                    <div class="stat-icon mb-3">
                        <i class="icofont-check-circled"></i>
                    </div>
                    <h3 class="stat-number text-light">1000+</h3>
                    <p class="stat-label text-light">Projects Delivered</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/End Customer Stories Section -->
@endsection
