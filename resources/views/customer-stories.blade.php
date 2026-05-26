@extends('layouts.public')

@section('title', 'Customer Stories')

@push('styles')
<style>
    .customer-stories-section {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 12% 14%, rgba(47, 85, 151, 0.08), transparent 28%),
            radial-gradient(circle at 88% 10%, rgba(15, 23, 42, 0.05), transparent 25%),
            linear-gradient(180deg, #f7f9fc 0%, #ffffff 46%, #f4f7fb 100%);
    }

    .customer-stories-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: linear-gradient(rgba(47, 85, 151, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(47, 85, 151, 0.03) 1px, transparent 1px);
        background-size: 32px 32px;
        mask-image: linear-gradient(180deg, rgba(0,0,0,0.12), transparent 70%);
        pointer-events: none;
    }

    .modern-section-header {
        position: relative;
        max-width: 860px;
        margin: 0 auto;
    }

    .header-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        border-radius: 999px;
        background: rgba(47, 85, 151, 0.08);
        color: var(--default-color);
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .badge-dot {
        width: 8px;
        height: 8px;
        background: var(--default-background);
        border-radius: 50%;
        box-shadow: 0 0 0 6px rgba(47, 85, 151, 0.09);
    }

    .badge-text {
        color: inherit;
    }

    .section-main-title {
        font-size: clamp(2.2rem, 4vw, 3.8rem);
        font-weight: 800;
        color: #172033;
        letter-spacing: -0.03em;
        line-height: 1.05;
    }

    .title-underline {
        width: 110px;
        height: 4px;
        background: linear-gradient(90deg, rgba(47, 85, 151, 0.14), rgba(47, 85, 151, 1), rgba(47, 85, 151, 0.14));
        border-radius: 999px;
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
        background: #fff;
        border: 3px solid var(--default-background);
        border-radius: 50%;
    }

    .section-description {
        color: #5f6b7d;
        font-size: 1.02rem;
        line-height: 1.9;
        max-width: 760px;
    }

    .modern-story-card {
        background: rgba(255, 255, 255, 0.92);
        border-radius: 20px;
        position: relative;
        box-shadow: 0 14px 32px rgba(16, 24, 40, 0.08);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
        border: 1px solid rgba(47, 85, 151, 0.08);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        isolation: isolate;
    }

    .modern-story-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(47, 85, 151, 0.06) 0%, transparent 30%);
        pointer-events: none;
        z-index: -1;
    }

    .modern-story-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 42px rgba(16, 24, 40, 0.12);
        border-color: rgba(47, 85, 151, 0.18);
    }

    .card-header-section {
        padding: 20px 18px 18px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        position: relative;
        min-height: 188px;
        background: linear-gradient(135deg, #305596 0%, #2f5597 48%, #1d3560 100%);
    }

    .card-header-section::after {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.2), transparent 34%),
            radial-gradient(circle at 80% 0%, rgba(255, 255, 255, 0.1), transparent 28%);
        pointer-events: none;
    }

    .customer-avatar {
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }

    .avatar-image,
    .avatar-placeholder {
        width: 78px;
        height: 78px;
        border-radius: 20px;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.72);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
        transition: transform 0.28s ease, box-shadow 0.28s ease;
        position: relative;
        overflow: hidden;
    }

    .avatar-placeholder {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.12));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: 0.04em;
    }

    .avatar-placeholder::before {
        content: attr(data-initials);
    }

    .modern-story-card:hover .avatar-image,
    .modern-story-card:hover .avatar-placeholder {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 20px 36px rgba(0, 0, 0, 0.24);
    }

    .verified-badge {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #10b981, #22c55e);
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        border: 3px solid #fff;
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.22);
        z-index: 2;
    }

    .verified-badge i {
        font-size: 13px;
        color: #fff;
    }

    .customer-meta {
        flex: 1;
        min-width: 0;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .customer-name {
        font-size: 0.94rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 6px 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: 42px;
    }

    .customer-position {
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.86) !important;
        margin: 0;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: 32px;
    }

    .divider-line {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(47, 85, 151, 0.12), transparent);
        margin: 0;
    }

    .story-body {
        padding: 16px 16px 18px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .rating-stars {
        display: flex;
        gap: 5px;
        justify-content: center;
        margin-bottom: 10px;
    }

    .rating-stars i {
        color: #f4b840;
        font-size: 14px;
    }

    .quote-wrapper {
        position: relative;
        flex-grow: 1;
        padding: 0 6px 0;
        display: flex;
        flex-direction: column;
    }

    .quote-mark-left,
    .quote-mark-right {
        position: absolute;
        font-size: 22px;
        opacity: 0.08;
        color: var(--default-color);
        pointer-events: none;
    }

    .quote-mark-left {
        top: -8px;
        left: -2px;
    }

    .quote-mark-right {
        bottom: -8px;
        right: -2px;
    }

    .testimonial-content {
        color: #243044;
        font-size: 0.88rem;
        line-height: 1.65;
        font-style: normal;
        text-align: left;
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 10px;
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
        color: inherit;
    }

    .read-more-btn {
        font-weight: 700;
        font-size: 0.78rem;
        letter-spacing: 0.02em;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border: 1px solid rgba(47, 85, 151, 0.2);
        border-radius: 999px;
        transition: transform 0.25s ease, background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease;
        background: #f5f8ff;
        color: var(--default-color) !important;
        align-self: flex-start;
        margin-top: auto;
    }

    .read-more-btn:hover {
        background: var(--default-background) !important;
        border-color: var(--default-background) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(47, 85, 151, 0.16);
    }

    .read-more-btn strong {
        white-space: nowrap;
    }

    .trust-stat {
        padding: 22px 18px;
        background: linear-gradient(135deg, #355fa8 0%, #2f5597 48%, #1d3560 100%);
        border-radius: 20px;
        box-shadow: 0 14px 34px rgba(16, 24, 40, 0.12);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
        border: 1px solid rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
    }

    .trust-stat.default-background {
        color: #ffffff;
        background: linear-gradient(135deg, #355fa8 0%, #2f5597 48%, #1d3560 100%);
        border-color: rgba(255, 255, 255, 0.14);
    }

    .trust-stat.default-background .stat-icon {
        background: rgba(255, 255, 255, 0.18);
    }

    .trust-stat.default-background .stat-number,
    .trust-stat.default-background .stat-label {
        color: #ffffff !important;
    }

    .trust-stat:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 44px rgba(16, 24, 40, 0.12);
        border-color: rgba(255, 255, 255, 0.22);
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.18);
        border-radius: 50%;
        transition: transform 0.28s ease;
    }

    .stat-icon i {
        font-size: 28px;
        color: #fff;
    }

    .trust-stat:hover .stat-icon {
        transform: scale(1.05);
    }

    .stat-number {
        font-size: 1.85rem;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 6px;
        letter-spacing: -0.03em;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.9rem;
        margin: 0;
        font-weight: 600;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.55;
            transform: scale(1.18);
        }
    }

    @media (max-width: 768px) {
        .section-main-title {
            font-size: 2rem;
        }

        .section-description {
            font-size: 0.95rem;
        }

        .card-header-section {
            padding: 18px 16px 14px;
            min-height: 178px;
        }

        .avatar-image,
        .avatar-placeholder {
            width: 64px;
            height: 64px;
        }

        .customer-name {
            font-size: 0.9rem;
            min-height: auto;
        }

        .customer-position {
            font-size: 0.75rem;
            min-height: auto;
        }

        .story-body {
            padding: 14px 14px 16px;
        }

        .testimonial-content {
            font-size: 0.86rem;
        }

        .quote-mark-left,
        .quote-mark-right {
            font-size: 20px;
        }

        .trust-stat {
            padding: 20px 16px;
            border-radius: 18px;
        }

        .stat-number {
            font-size: 1.45rem;
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
                        <span class="badge-text">Verified customer voices</span>
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
                    $testimonialBody = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($testimonial->body_content ?? ''))));
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
                                    <a id="{{ $testimonial->id }}" class="read-more-btn" href="#" aria-label="Toggle full testimonial for {{ $testimonial->name }}">
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
        <div class="row mt-4 pt-3">
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
            <div class="col-lg-3 col-md-6 col-12 mb-4">
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
