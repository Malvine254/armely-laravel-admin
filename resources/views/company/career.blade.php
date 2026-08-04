@extends('layouts.public')

@section('title', 'Careers')
@section('meta_description', 'Explore careers at Armely and discover open roles in data, AI, Microsoft platform delivery, and consulting services.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/career-modern.css') }}?v={{ filemtime(public_path('css/career-modern.css')) }}">
@endpush

@section('content')

<section class="career-hero section" style="border-radius:0px !important">
	<div class="container">
		<div class="career-hero-shell">
			<div class="row align-items-center g-4">
				<div class="col-lg-7">
					<div class="section-title modern-section-title career-hero-copy">
						<div class="career-badge-tag career-badge-tag--hero">
							<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1" />
								<path d="M4 8h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8Z" />
								<path d="M4 12h16" />
							</svg>
							Join Our Team
						</div>
						<h2 class="career-main-title text-light">Find Your Future Here!</h2>
						<p class="lead">We're hiring across disciplines. Competitive pay, great benefits, and a collaborative environment. Full-time roles include paid holidays, vacations, performance bonuses, and project-driven incentives.</p>
					</div>
				</div>
				<div class="col-lg-5">
					<div class="career-hero-panel">
						<h3>Why people join Armely</h3>
						<ul class="career-hero-points">
							<li>Meaningful project work across Microsoft, cloud, and data delivery.</li>
							<li>A collaborative team culture with room to grow.</li>
							<li>Competitive benefits and performance incentives.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="careers-listing-section">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10">
				@if(!empty($dbErrorMessage))
					<div class="alert alert-warning text-center mb-4" role="alert">
						<svg class="icon-svg alert-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<path d="M12 9v4" />
							<path d="M12 17h.01" />
							<path d="M10.3 4.3 2.4 18a2 2 0 0 0 1.7 3h16a2 2 0 0 0 1.7-3l-7.9-13.7a2 2 0 0 0-3.6 0Z" />
						</svg>
						{{ $dbErrorMessage }}
					</div>
				@endif
				<div class="careers-header-wrap">
					<h3 class="openings-title">
						<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<path d="M4 7h16v11H4z" />
							<path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
							<path d="M4 12h16" />
						</svg>
						Current Openings
					</h3>
					<div class="filter-controls">
						<button class="filter-btn active" data-filter="all">
							<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M4 6h16M7 12h10M10 18h4" />
							</svg>
							All Positions
						</button>
						<button class="filter-btn" data-filter="full-time">
							<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M4 7h16v12H4z" />
								<path d="M9 4h6v3H9z" />
							</svg>
							Full Time
						</button>
						<button class="filter-btn" data-filter="part-time">
							<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<circle cx="12" cy="12" r="9" />
								<path d="M12 7v5l3 2" />
							</svg>
							Part Time
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="row justify-content-center">
			<div class="col-lg-10">
				<div class="row careers-grid">
					@forelse($careerListings as $job)
						@php
							$jobTypeClass = strtolower(str_replace(' ', '-', $job->job_type));
							$deadlineDate = $job->job_deadline ? \DateTime::createFromFormat('Y-m-d', $job->job_deadline) : null;
							$currentDate = new \DateTime();
							$currentDate->setTime(0, 0);
							$status = ($deadlineDate && $deadlineDate < $currentDate) ? 'Closed' : 'Open';
						@endphp
						<div class="career-item" data-type="{{ $jobTypeClass }}">
							<div class="card career-card">
								<div class="card-body">
									<div class="career-card-top">
										<div class="career-card-icon" aria-hidden="true">
											<svg class="icon-svg" viewBox="0 0 24 24" fill="none">
												<path d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1" />
												<path d="M4 8h16v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8Z" />
												<path d="M4 12h16" />
												<path d="M9 12v2a3 3 0 0 0 6 0v-2" />
											</svg>
										</div>
										<div class="career-card-top-copy">
											<span class="career-badge">{{ $job->job_type }}</span>
											<span class="career-status {{ $status === 'Closed' ? 'is-closed' : 'is-open' }}">
												<span class="career-status-dot"></span>
												{{ $status }}
											</span>
										</div>
									</div>

									<h5 class="role-title">{{ $job->title }}</h5>

									<div class="career-meta-list">
										<div class="career-meta-row">
											<span class="career-meta-icon" aria-hidden="true">
												<svg class="icon-svg" viewBox="0 0 24 24" fill="none">
													<path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" />
													<circle cx="12" cy="9" r="2.5" />
												</svg>
											</span>
											<span>{{ $job->location }}</span>
										</div>

										<div class="career-meta-row">
											<span class="career-meta-icon" aria-hidden="true">
												<svg class="icon-svg" viewBox="0 0 24 24" fill="none">
													<rect x="3" y="4.5" width="18" height="16" rx="4" />
													<path d="M8 3v4M16 3v4M3 9.5h18" />
												</svg>
											</span>
											<span>{{ $job->job_deadline ? date('M d, Y', strtotime($job->job_deadline)) : 'No deadline' }}</span>
										</div>
									</div>
								</div>

								<div class="card-footer">
									@if($status === 'Closed')
										<button class="btn career-closed-btn w-100" disabled>Closed</button>
									@else
										<a href="{{ route('job-board.index') }}?job-details={{ urlencode($job->job_id) }}" class="btn default-button apply-btn w-100">
											<span>View Details</span>
											<svg class="icon-svg btn-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
												<path d="M5 12h14" />
												<path d="m13 6 6 6-6 6" />
											</svg>
										</a>
									@endif
								</div>
							</div>
						</div>
					@empty
						<div class="careers-empty">
							<div class="alert alert-info text-center">
								<svg class="icon-svg alert-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<circle cx="12" cy="12" r="9" />
									<path d="M12 8.5h.01" />
									<path d="M12 11v5" />
								</svg>
								No open positions at this time. Check back soon!
							</div>
						</div>
					@endforelse
				</div>
			</div>
		</div>
	</div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const careerItems = document.querySelectorAll('.career-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            careerItems.forEach(item => {
                if (filterValue === 'all') {
                    item.style.display = 'block';
                } else {
                    const itemType = item.getAttribute('data-type');
                    item.style.display = itemType === filterValue ? 'block' : 'none';
                }
            });
        });
    });
});
</script>
@endpush
