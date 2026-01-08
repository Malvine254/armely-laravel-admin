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

<style>
	/* Override default white text for Job Type and Job Position fields */
	.form-group select.remove-input-background,
	.form-group input[readonly].remove-input-background {
		color: #000 !important;
	}

	.form-group select.remove-input-background {
		background-color: #fff !important;
		border: 1px solid #ddd !important;
		padding: 10px 12px !important;
		border-radius: 6px !important;
		width: 100% !important;
		font-size: 14px !important;
		line-height: 1.4 !important;
		color: #000 !important;
		cursor: pointer;
	}

	.form-group select.remove-input-background:focus {
		outline: none;
		border-color: #2f5597 !important;
		box-shadow: 0 0 0 3px rgba(47, 85, 151, 0.1);
	}

	/* Ensure dropdown items also display black text */
	.form-group select.remove-input-background option {
		color: #000 !important;
		background-color: #fff !important;
	}

	.form-group input[readonly].remove-input-background {
		background-color: #f5f5f5 !important;
		border: 1px solid #ddd !important;
		padding: 12px !important;
		border-radius: 4px !important;
	}

	.form-group input[readonly].remove-input-background::placeholder {
		color: #999;
	}

	/* Submit button loading state */
	#submit-btn {
		transition: all 0.3s ease;
		position: relative;
	}

	#submit-btn.loading {
		background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
		color: #fff !important;
		pointer-events: none;
		padding-left: 45px !important;
	}

	#submit-btn.loading::before {
		content: '';
		position: absolute;
		left: 15px;
		top: 50%;
		transform: translateY(-50%);
		width: 18px;
		height: 18px;
		border: 3px solid rgba(255, 255, 255, 0.3);
		border-top-color: #fff;
		border-radius: 50%;
		animation: spin 0.8s linear infinite;
	}

	@keyframes spin {
		0% { transform: translateY(-50%) rotate(0deg); }
		100% { transform: translateY(-50%) rotate(360deg); }
	}
</style>

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
								<small class="text-light d-block mt-1">Max file size: 5MB</small>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Job Type *</label>
							<div class="form-group">
								<select required name="type" class=" text-dark" id="type">
								
									<option value="Full Time" {{ old('type') === 'Full Time' ? 'selected' : '' }}>Full Time</option>
									<option value="Part Time" {{ old('type') === 'Part Time' ? 'selected' : '' }}>Part Time</option>
									<option value="Contract" {{ old('type') === 'Contract' ? 'selected' : '' }}>Contract</option>
									<option value="Temporary" {{ old('type') === 'Temporary' ? 'selected' : '' }}>Temporary</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<label class="text-start text-light">Job Position *</label>
							<div class="form-group">
								<input type="text" readonly class="remove-input-background" value="{{ $jobTitle }}" name="position">
								<input type="hidden" name="job_id" value="{{ $jobId }}">
							</div>
						</div>
						<input type="text" name="website" class="honeypot" style="display: none;">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="text-start text-light">Confirm you are not a robot *</label>
								<div class="g-recaptcha" data-sitekey="6Ld0Z0krAAAAAFCwIDiunmU9l68kT4Vm2cB7U7px"></div>
							</div>
						</div>
						<div class="col-lg-12 form-group mt-3">
							<div class="button">
								<button type="submit" id="submit-btn" class="btn btn-light text-light" style="min-width: 200px; padding: 12px 30px;">Complete Application</button>
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
	const cvInput = $('#cv');

	// Validate PDF file on change
	cvInput.on('change', function() {
		const file = this.files[0];
		if (file) {
			// Check file type - accept both MIME type and extension
			const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
			if (!isPdf) {
				messageBox.removeClass('alert-success').addClass('alert alert-danger')
					.html('<strong>Error:</strong> Please upload a valid PDF file.').show();
				$(this).val('');
				return;
			}
			
			// Check file size (5MB = 5242880 bytes)
			if (file.size > 5242880) {
				messageBox.removeClass('alert-success').addClass('alert alert-danger')
					.html('<strong>Error:</strong> File size exceeds 5MB limit. Please choose a smaller PDF.').show();
				$(this).val('');
				return;
			}
			
			messageBox.hide().removeClass('alert-success alert-danger').text('');
		}
	});

	form.on('submit', function(e) {
		e.preventDefault();
		e.stopPropagation();

		messageBox.hide().removeClass('alert-success alert-danger').text('');

		// Validate CV file
		const cvFile = cvInput[0].files[0];
		if (!cvFile) {
			messageBox.addClass('alert alert-danger').html('<strong>Error:</strong> Please upload a CV file.').show();
			return;
		}

		// More lenient PDF check - accept if file name ends with .pdf
		const isPdf = cvFile.name.toLowerCase().endsWith('.pdf');
		if (!isPdf) {
			messageBox.addClass('alert alert-danger').html('<strong>Error:</strong> The cv field must be a file of type: pdf.').show();
			return;
		}

		if (cvFile.size > 5242880) {
			messageBox.addClass('alert alert-danger').html('<strong>Error:</strong> File size exceeds 5MB limit.').show();
			return;
		}

		// Check if reCAPTCHA is loaded and get response
		let recaptchaResponse = '';
		if (typeof grecaptcha !== 'undefined') {
			try {
				recaptchaResponse = grecaptcha.getResponse();
			} catch (e) {
				console.error('reCAPTCHA error:', e);
				messageBox.addClass('alert alert-danger').html('<strong>Error:</strong> reCAPTCHA is still loading. Please wait a moment and try again.').show();
				return;
			}
		}
		
		if (!recaptchaResponse) {
			messageBox.addClass('alert alert-danger').html('<strong>Error:</strong> Please verify that you are not a robot.').show();
			return;
		}

		const formData = new FormData(this);
		formData.append('g-recaptcha-response', recaptchaResponse);

		submitBtn.prop('disabled', true)
			.addClass('loading')
			.text('Submitting...')
			.css('visibility', 'visible');

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
					try {
						grecaptcha.reset();
					} catch (e) {
						console.error('reCAPTCHA reset error:', e);
					}
				}
			},
			error: function(xhr) {
				console.log('Error response:', xhr.responseJSON); // Debug log
				let msg = 'An error occurred. Please try again.';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					msg = xhr.responseJSON.message;
				} else if (xhr.responseJSON && xhr.responseJSON.errors) {
					// Handle validation errors
					const errors = xhr.responseJSON.errors;
					const errorMessages = [];
					for (let field in errors) {
						errorMessages.push('<strong>' + field + ':</strong> ' + errors[field].join(', '));
					}
					msg = errorMessages.join('<br>');
				}
				messageBox.addClass('alert alert-danger').html('❌ ' + msg).show();
			},
			complete: function() {
				submitBtn.prop('disabled', false)
					.removeClass('loading')
					.text('Complete Application')
					.css('visibility', 'visible');
			}
		});
	});
});
</script>
@endsection
