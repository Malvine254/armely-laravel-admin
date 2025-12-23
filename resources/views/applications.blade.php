@extends('layouts.public')

@section('title', 'Job Application - Armely')

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Job Application</h2>
					<ul class="bread-list">
						<li><a href="/">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Job Application</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<!-- Start Appointment -->
<section class="appointment mt-0">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-8 col-12 d-flex default-background mb-5">
				<form class="form p-4" id="job-application-form" action="{{ route('applications.submit') }}" method="post" enctype="multipart/form-data">
					@csrf
					@if(session('success'))
						<div class="alert alert-success">{{ session('success') }}</div>
					@endif
					@if($errors->any())
						<div class="alert alert-danger">
							@foreach($errors->all() as $error)
								<div>{{ $error }}</div>
							@endforeach
						</div>
					@endif
					<div id="JobSubmitMessage" class="alert p-3" style="display: none;"></div>
					<h2 class="text-light mt-2 mb-3">Complete the following form</h2>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Name *</label>
							<div class="form-group input-with-background">
								<input id="name" required class="remove-input-background" name="name" type="text" placeholder="Name" value="{{ old('name') }}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Email *</label>
							<div class="form-group">
								<input id="email" required class="remove-input-background" name="email" type="email" placeholder="Email" value="{{ old('email') }}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Phone Number *</label>
							<div class="form-group">
								<input id="phone" required class="remove-input-background" name="phone" type="text" placeholder="Phone" value="{{ old('phone') }}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Address *</label>
							<div class="form-group">
								<input id="address" required class="remove-input-background" name="address" type="text" placeholder="Address" value="{{ old('address') }}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">City *</label>
							<div class="form-group">
								<input id="city" required class="remove-input-background" name="city" type="text" placeholder="City" value="{{ old('city') }}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">ZIP Code *</label>
							<div class="form-group">
								<input id="zip" required class="remove-input-background" name="zip" type="text" placeholder="Zip Code" value="{{ old('zip') }}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">State *</label>
							<div class="form-group">
								<input id="state" required class="remove-input-background" name="state" type="text" placeholder="State" value="{{ old('state') }}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">CV - .pdf format only *</label>
							<div class="form-group">
								<input id="cv" required class="remove-input-background p-2" name="cv" type="file" accept=".pdf" placeholder="Upload CV">
							</div>
						</div>
						<div class="col-lg-6 col-md-4 col-12">
							<label class="text-start text-light">Job Type *</label>
							<div class="form-group">
								<select required name="type" class="form-control remove-input-background" id="type">
									<option value="Full Time" {{ old('type') === 'Full Time' ? 'selected' : '' }}>Full Time</option>
									<option value="Part Time" {{ old('type') === 'Part Time' ? 'selected' : '' }}>Part Time</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6 col-md-4 col-12">
							<label class="text-start text-light">Job Position *</label>
							<div class="form-group">
								<input type="text" readonly class="form-control remove-input-background" value="{{ $jobTitle }}" name="position">
								<input type="hidden" name="job_id" value="{{ $jobId }}">
							</div>
						</div>
						<input type="text" name="website" class="honeypot" style="display: none;">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="text-start text-light">Confirm you are not a robot *</label>
								<div class="g-recaptcha" data-sitekey="{{ env('CAPTURE_SITE_KEY') }}"></div>
							</div>
						</div>
						<div class="form-group ml-3">
							<div class="button">
								<button type="submit" class="btn btn-light text-light">Complete Application</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!-- End Appointment -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
$(function() {
	const form = $('#job-application-form');
	if (!form.length) return;

	const messageBox = $('#JobSubmitMessage');
	const submitBtn = form.find('button[type="submit"]');

	form.on('submit', function(e) {
		e.preventDefault();
		e.stopPropagation();

		messageBox.hide().removeClass('alert-success alert-danger').text('');

		const recaptchaResponse = typeof grecaptcha !== 'undefined' ? grecaptcha.getResponse() : '';
		if (!recaptchaResponse) {
			messageBox.addClass('alert alert-danger').html('<strong>Error:</strong> Please verify that you are not a robot.').show();
			return;
		}

		const formData = new FormData(this);
		formData.append('g-recaptcha-response', recaptchaResponse);

		submitBtn.prop('disabled', true).text('Submitting...');

		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': form.find('input[name="_token"]').val(),
				'Accept': 'application/json'
			},
			success: function(resp) {
				messageBox.addClass('alert alert-success').text(resp.message || 'Application submitted successfully!').show();
				
				// Google Ads Conversion Tracking
				if (typeof gtag === 'function') {
					gtag('event', 'conversion', {
						'send_to': '{{ env("GOOGLE_ADS_ID") }}/job_application_submit',
						'event_callback': function() {
							console.log('Job application conversion tracked');
						}
					});
				}
				
				form[0].reset();
				if (typeof grecaptcha !== 'undefined') {
					grecaptcha.reset();
				}
			},
			error: function(xhr) {
				let msg = 'An error occurred. Please try again.';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					msg = xhr.responseJSON.message;
				}
				messageBox.addClass('alert alert-danger').text('❌ ' + msg).show();
			},
			complete: function() {
				submitBtn.prop('disabled', false).text('Complete Application');
			}
		});
	});
});
</script>
@endsection
