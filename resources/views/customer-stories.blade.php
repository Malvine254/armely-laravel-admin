@extends('layouts.public')

@section('title', 'Customer Stories')

@push('styles')
<style>
/* Modern Section Header */
.customer-stories-section {
    background: radial-gradient(circle at 20% 20%, rgba(93,110,130,0.06), transparent 30%),
                radial-gradient(circle at 80% 10%, rgba(118,120,140,0.06), transparent 28%),
    linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f5f6f7 100%);
}

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
    background: #ffffff;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.modern-story-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.15);
}

.card-header-section {
    padding: 35px 24px 25px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    position: relative;
    background: linear-gradient(135deg, var(--default-background) 0%, var(--default-color) 100%);
    min-height: 240px;
}

.customer-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar-image {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.6);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.avatar-placeholder {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    font-weight: 700;
    color: #fff;
    border: 4px solid rgba(255,255,255,0.6);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    position: relative;
}

.avatar-placeholder::before {
    content: attr(data-initials);
}

.modern-story-card:hover .avatar-image,
.modern-story-card:hover .avatar-placeholder {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
}

.verified-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 28px;
    height: 28px;
    background: #10b981;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.verified-badge i {
    font-size: 13px;
    color: #fff;
}

.customer-meta {
    flex: 1;
    min-width: 0;
    text-align: center;
}

.customer-name {
    font-size: 19px;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 6px 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 50px;
}

.customer-position {
    font-size: 14px;
    color: #ffffff !important;
    margin: 0 0 10px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 40px;
}

.rating-stars {
    display: flex;
    gap: 4px;
    justify-content: center;
}

.rating-stars i {
    color: #fbbf24;
    font-size: 14px;
}

.divider-line {
    height: 1px;
    background: rgba(0,0,0,0.06);
    margin: 0;
}

.story-body {
    padding: 24px 22px 26px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    background: #ffffff;
}

.quote-wrapper {
    position: relative;
    flex-grow: 1;
    padding: 0 6px;
    display: flex;
    flex-direction: column;
}

.testimonial-content {
    flex-grow: 1;
}
.quote-mark-right {
    position: absolute;
    font-size: 40px;
    opacity: 0.08;
    color: var(--default-color);
    pointer-events: none;
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
    color: #222222;
    font-size: 15px;
    line-height: 1.7;
    font-style: normal;
    text-align: left;
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.testimonial-content .shorten-content {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.testimonial-content.expanded .shorten-content {
    -webkit-line-clamp: unset;
    overflow: visible;
}

.shorten-content {
    display: block;
    margin-bottom: 15px;
    color: inherit;
}

.read-more-btn {
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 0.3px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 9px 24px;
    border: 1.5px solid var(--default-color);
    border-radius: 6px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: transparent;
    color: var(--default-color) !important;
    align-self: flex-start;
    margin-top: auto;
}

.read-more-btn.default-color {
    color: var(--default-color) !important;
    border-color: var(--default-color);
}

.read-more-btn.default-color:hover {
    background: var(--default-color) !important;
    border-color: var(--default-color) !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.read-more-btn.default-color:hover strong {
    color: #ffffff !important;
}

.read-more-btn:hover strong.default-color {
    color: #222222 !important;
}

.read-more-btn:hover {
    background: var(--default-color) !important;
    border-color: var(--default-color) !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.read-more-btn:hover strong {
    color: #ffffff !important;
}

.read-more-btn strong {
    white-space: nowrap;
}

.trust-stat {
    padding: 30px 20px;
    background: linear-gradient(160deg, rgba(255,255,255,0.98) 0%, rgba(248,249,251,0.99) 50%, rgba(255,255,255,0.98) 100%);
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.trust-stat.default-background {
    color: #333;
    background: linear-gradient(160deg, rgba(255,255,255,0.98) 0%, rgba(248,249,251,0.99) 50%, rgba(255,255,255,0.98) 100%);
    border-color: rgba(74, 90, 117, 0.15);
}

.trust-stat.default-background .stat-icon {
    background: var(--default-background);
}

.trust-stat.default-background .stat-number,
.trust-stat.default-background .stat-label {
    color: #ffffff !important;
}

.trust-stat:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 48px rgba(0,0,0,0.16), 0 8px 16px rgba(0,0,0,0.12);
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
    color: #ffffff;
    margin-bottom: 5px;
}

.stat-label {
    color: rgba(255,255,255,0.9);
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
                                <h5 class="customer-name" title="{{ $testimonial->name }}">{{ $testimonial->name }}</h5>
                                <p class="customer-position" title="{{ $testimonial->position }}">{{ $testimonial->position }}</p>
                            </div>
                        </div>
                        <div class="divider-line"></div>
                        <div class="story-body">
                            <div class="rating-stars mb-3">
                                <i class="icofont-star"></i>
                                <i class="icofont-star"></i>
                                <i class="icofont-star"></i>
                                <i class="icofont-star"></i>
                                <i class="icofont-star"></i>
                            </div>
                            <div class="quote-wrapper">
                                <i class="icofont-quote-left quote-mark-left"></i>
                                <div class="testimonial-content">
                                    <span class="shorten-content">{!! $testimonial->body_content !!}</span>
                                    <a id="{{ $testimonial->id }}" class="read-more-btn " href="#">
                                        <strong class="default-color">Read More</strong>
                                    </a>
                                </div>
                                <i class="icofont-quote-right quote-mark-right"></i>
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
                    <h3 class="stat-number">500+</h3>
                    <p class="stat-label">Happy Clients</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="trust-stat text-center default-background">
                    <div class="stat-icon mb-3">
                        <i class="icofont-star"></i>
                    </div>
                    <h3 class="stat-number">4.9/5</h3>
                    <p class="stat-label">Average Rating</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4 ">
                <div class="trust-stat text-center default-background">
                    <div class="stat-icon mb-3">
						<i class="fas fa-book text-light"></i>
                    </div>
                    <h3 class="stat-number">98%</h3>
                    <p class="stat-label">Success Rate</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="trust-stat text-center default-background">
                    <div class="stat-icon mb-3">
                        <i class="icofont-check-circled"></i>
                    </div>
                    <h3 class="stat-number">1000+</h3>
                    <p class="stat-label">Projects Delivered</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/End Customer Stories Section -->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.read-more-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const content = btn.closest('.testimonial-content');
            if (!content) return;
            const isExpanded = content.classList.toggle('expanded');
            btn.querySelector('strong').textContent = isExpanded ? 'Show Less' : 'Read More';
        });
    });
});
</script>
@endpush
