@extends('layouts.public')

@section('title', 'Job Board - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/job-board-modern.css') }}">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Job Board</h2>
					<ul class="bread-list">
						<li><a href="/">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Job Board</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<!-- Job Details Section -->
<section class="job-details-section mt-5 pb-5 pt-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-12">
				<div class="card job-details-card">
					<div class="card-body p-4">
						@if($job)
							<div class='job-header mb-4'>
								<span class='job-type-badge'>{{ $job->job_type }}</span>
								<h2 class='job-title mt-3'>{{ $job->job_title }}</h2>
								<div class='job-meta'>
									<i class='fa fa-map-marker default-color'></i> {{ $job->job_location }}
								</div>
								@if($job->job_deadline)
									<div class='job-meta mt-2'>
										<i class='fa fa-calendar default-color'></i> Deadline: {{ date('F d, Y', strtotime($job->job_deadline)) }}
									</div>
								@endif
							</div>
							<div class='job-description-content'>
								{!! $job->job_description !!}
							</div>
							<div class='job-apply-section mt-4'>
								<a href="{{ route('applications.index') }}?job-details={{ $job->job_id }}&application=true&title={{ urlencode($job->job_title) }}" 
								   class='btn default-button btn-lg apply-now-btn'>
									<i class='fa fa-paper-plane'></i> Apply Now
								</a>
								<a href="{{ route('career.index') }}" class='btn btn-outline-light btn-lg ms-3 text-light'>
									<i class='fa fa-home'></i> Back to Careers
								</a>
							</div>
						@else
							<p class='text-center text-danger'>Job not found!</p>
							<div class="text-center mt-4">
								<a href="{{ route('career.index') }}" class='btn default-button'>Back to Careers</a>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>	
</section>	
<!--/ End Job Details -->

<!-- Start Appointment -->
<section class="appointment">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="section-title modern-section-title">
					<div class="title-head">
						<h2 class="mt-3" id="consultation">Schedule a consultation today</h2>
					</div>
					<center><hr class="hr default-background"></center>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 col-md-6 col-12 d-flex default-background mb-5">
				<form class="form p-5" action="{{ route('contact.submit') }}" method="post">
					@csrf
					<div class="row">
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Name *</label>
							<div class="form-group input-with-background">
								<input required class="remove-input-background" name="name" type="text" placeholder="Name">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Email *</label>
							<div class="form-group">
								<input required class="remove-input-background" name="email" type="email" placeholder="Email">
							</div> 
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Phone Number *</label>
							<div class="form-group">
								<input required class="remove-input-background" name="phone" type="text" placeholder="Phone">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Organization Name *</label>
							<div class="form-group">
								<input required class="remove-input-background" name="organization" type="text" placeholder="Organization Name">
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-12">
							<label class="text-start text-light">Message *</label>
							<div class="form-group">
								<textarea required class="remove-input-background" name="message" placeholder="Write Your Message Here....."></textarea>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="text-start text-light">Confirm you are not a robot *</label>
								<div class="g-recaptcha" data-sitekey="6Ld0Z0krAAAAAFCwIDiunmU9l68kT4Vm2cB7U7px"></div>
							</div>
						</div>
						<div class="form-group ml-3">
							<div class="button">
								<button type="submit" class="btn send-message-btn">Send Message</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!-- End Appointment -->
@endsection

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
