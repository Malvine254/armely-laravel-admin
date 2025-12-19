@extends('layouts.public')

@section('title', 'Career Opportunities - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/career-modern.css') }}">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Career Opportunities</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Career</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<!-- Pricing Table -->
<section class="pricing-table section">
<div class="container">
<div class="row">
<div class="col-lg-12">
<div class="section-title modern-section-title">
	<div class="career-badge-tag">
		<i class="icofont-briefcase"></i> Join Our Team
	</div>
	<h2 class="career-main-title">Find Your Future Here!</h2>
	<div class="title-divider"></div>
	<p class="lead">We're hiring across disciplines. Competitive pay, great benefits, and a collaborative environment. Full-time roles include paid holidays, vacations, performance bonuses, and project-driven incentives.</p>
	<div class="career-stats">
		<div class="stat-item">
			<div class="stat-icon"><i class="icofont-users-alt-4"></i></div>
			<div class="stat-number">500+</div>
			<div class="stat-label">Team Members</div>
		</div>
		<div class="stat-item">
			<div class="stat-icon"><i class="icofont-location-pin"></i></div>
			<div class="stat-number">10+</div>
			<div class="stat-label">Office Locations</div>
		</div>
		<div class="stat-item">
			<div class="stat-icon"><i class="icofont-award"></i></div>
			<div class="stat-number">50+</div>
			<div class="stat-label">Awards Won</div>
		</div>
	</div>
</div>
</div>
</div>

</div>	

</div>
<div class="careers-listing-section">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10">
				@if(!empty($dbErrorMessage))
					<div class="alert alert-warning text-center mb-4" role="alert">
						<i class="icofont-warning-alt"></i> {{ $dbErrorMessage }}
					</div>
				@endif
				<div class="careers-header-wrap">
					<h3 class="openings-title">
						<i class="icofont-folder-open"></i> Current Openings
					</h3>
					<div class="filter-controls">
						<button class="filter-btn active" data-filter="all">
							<i class="icofont-ui-office"></i> All Positions
						</button>
						<button class="filter-btn" data-filter="full-time">
							<i class="icofont-clock-time"></i> Full Time
						</button>
						<button class="filter-btn" data-filter="part-time">
							<i class="icofont-clock-time"></i> Part Time
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
						<div class="col-12 col-md-6 col-lg-4 mb-4 career-item" data-type="{{ $jobTypeClass }}">
							<div class="card career-card">
								<div class="card-body">
									<div class="career-header-wrapper">
										<span class="career-badge">{{ $job->job_type }}</span>
									</div>
									<h5 class="role-title">{{ $job->title }}</h5>
									<div class="role-meta"><i class="fa fa-map-marker default-color"></i> {{ $job->location }}</div>
									<div class="role-meta">
										<i class="fa fa-clock-o default-color"></i> 
										{{ $job->job_deadline ? date('M d, Y', strtotime($job->job_deadline)) : 'No deadline' }} • 
										@if($status === 'Closed')
											<span class="text-danger"><i class="fa fa-info-circle text-danger"></i> {{ $status }}</span>
										@else
											<span class="text-primary"><i class="fa fa-circle text-primary"></i> {{ $status }}</span>
										@endif
									</div>
								</div>
								<div class="card-footer">
									@if($status === 'Closed')
										<button class="btn btn-danger w-100" disabled>Closed</button>
									@else
										<a href="{{ route('job-board.index') }}?job-details={{ urlencode($job->job_id) }}" class="btn default-button apply-btn w-100">View Details</a>
									@endif
								</div>
							</div>
						</div>
					@empty
						<div class="col-12">
							<div class="alert alert-info text-center">
								<i class="icofont-info-circle"></i> No open positions at this time. Check back soon!
							</div>
						</div>
					@endforelse
				</div>
			</div>
		</div>
	</div>
</div>
</section>	
<!--/ End Pricing Table -->
@endsection

@push('scripts')
<script>
// Career filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const careerItems = document.querySelectorAll('.career-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            
            careerItems.forEach(item => {
                if (filterValue === 'all') {
                    item.style.display = 'block';
                } else {
                    const itemType = item.getAttribute('data-type');
                    if (itemType === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    });
});
</script>
@endpush
