@extends('layouts.public')

@section('title', 'Jobs')
@section('meta_description', 'Review job details and open opportunities at Armely, then apply to join our consulting and technology delivery teams.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/job-board-modern.css') }}?v={{ filemtime(public_path('css/job-board-modern.css')) }}">
@endpush

@section('content')

@if($job)
	<section class="job-board-hero">
		<div class="container">
			<div class="job-board-hero-shell">
				<div class="job-board-hero-copy">
					<h1 class="job-board-title">{{ $job->job_title }}</h1>
					<p class="job-board-subtitle text-light">Explore the role details, responsibilities, and application path for this opening at Armely.</p>
					<div class="job-board-pills">
						<span class="job-pill">{{ $job->job_type }}</span>
					</div>
				</div>

				<div class="job-board-hero-card">
					<div class="job-board-hero-card-label">Quick summary</div>
					<div class="job-summary-grid">
						<div class="job-summary-item">
							<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M12 21s7-6.1 7-12a7 7 0 1 0-14 0c0 5.9 7 12 7 12Z" />
								<circle cx="12" cy="9" r="2.5" />
							</svg>
							<div>
								<span>Location</span>
								<strong>{{ $job->job_location }}</strong>
							</div>
						</div>
						@if($job->job_deadline)
							<div class="job-summary-item">
								<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
									<rect x="3" y="4.5" width="18" height="16" rx="4" />
									<path d="M8 3v4M16 3v4M3 9.5h18" />
								</svg>
								<div>
									<span>Deadline</span>
									<strong>{{ date('F d, Y', strtotime($job->job_deadline)) }}</strong>
								</div>
							</div>
						@endif
						<div class="job-summary-item">
							<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<circle cx="12" cy="12" r="9" />
								<path d="M12 7v5l3 2" />
							</svg>
							<div>
								<span>Status</span>
								<strong class="job-status {{ $job->job_deadline && strtotime($job->job_deadline) < time() ? 'is-closed' : 'is-open' }}">
									{{ $job->job_deadline && strtotime($job->job_deadline) < time() ? 'Closed' : 'Open' }}
								</strong>
							</div>
						</div>
					</div>
					<div class="job-board-actions">
						<a href="#apply" class="btn default-button apply-now-btn">
							<span>Apply Now</span>
							<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M5 12h14" />
								<path d="m13 6 6 6-6 6" />
							</svg>
						</a>
						<a href="{{ route('career.index') }}" class="btn job-back-btn">
							<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M19 12H5" />
								<path d="m11 6-6 6 6 6" />
							</svg>
							<span>Back to Careers</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="job-board-content-section">
		<div class="container">
			<div class="job-board-layout">
				<div class="job-board-main-card">
					<div class="job-section-label">Role details</div>
					<div class="job-description-content">
						{!! $job->job_description !!}
					</div>
				</div>

				<div class="job-board-side-card">
					<div class="job-section-label">How to apply</div>
					<h3>Ready to move forward?</h3>
					<p>Scroll to the application section below and submit your details for this role.</p>
					<a href="#apply" class="btn default-button apply-now-btn w-100">
						<span class="text-light">Apply for this role</span>
						<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<path d="M5 12h14" />
							<path d="m13 6 6 6-6 6" />
						</svg>
					</a>
					<ul class="job-side-points">
						<li>Your application stays tied to this exact posting.</li>
						<li>Upload a PDF CV under 5MB.</li>
						<li>Open roles are reviewed as submissions come in.</li>
					</ul>
				</div>
			</div>
		</div>
	</section>

	<section id="apply" class="job-application-section">
		<div class="container">
			<div class="job-application-header">
				<div class="job-section-label">Apply now</div>
				<h2>Complete your application for {{ $job->job_title }}</h2>
				<p>Submit your details and CV below. We'll send this application directly to our hiring workflow for the selected job.</p>
			</div>

			<div class="job-application-grid">
				<div class="job-application-card">
					@if(session('success'))
						<div class="alert alert-success job-alert">{{ session('success') }}</div>
					@endif
					@if($errors->any())
						<div class="alert alert-danger job-alert">
							@foreach($errors->all() as $error)
								<div>{{ $error }}</div>
							@endforeach
						</div>
					@endif

					<form class="job-application-form" id="job-application-form" action="{{ route('applications.submit') }}" method="post" enctype="multipart/form-data">
						@csrf
						<div id="JobSubmitMessage" class="alert p-3" style="display:none;"></div>
						<div class="job-application-fields">
							<div class="job-field">
								<label for="name">Name *</label>
								<input id="name" required name="name" type="text" class="job-input" placeholder="Name" value="{{ old('name') }}">
							</div>
							<div class="job-field">
								<label for="email">Email *</label>
								<input id="email" required name="email" type="email" class="job-input" placeholder="Email" value="{{ old('email') }}">
							</div>
							<div class="job-field">
								<label for="address">Address *</label>
								<input id="address" required name="address" type="text" class="job-input" placeholder="Address" value="{{ old('address') }}">
							</div>
							<div class="job-field">
								<label for="city">City *</label>
								<input id="city" required name="city" type="text" class="job-input" placeholder="City" value="{{ old('city') }}">
							</div>
							<div class="job-field">
								<label for="zip">ZIP Code *</label>
								<input id="zip" required name="zip" type="text" class="job-input" placeholder="Zip Code" value="{{ old('zip') }}">
							</div>
							<div class="job-field">
								<label for="state">State *</label>
								<input id="state" required name="state" type="text" class="job-input" placeholder="State" value="{{ old('state') }}">
							</div>
							<div class="job-field">
								<label for="phone">Phone</label>
								<input id="phone" name="phone" type="text" class="job-input" placeholder="Phone" value="{{ old('phone') }}">
							</div>
							<div class="job-field">
								<label for="cv">CV - PDF only *</label>
								<input id="cv" required name="cv" type="file" class="job-input job-input-file" accept=".pdf">
								<small class="job-help">Max file size: 5MB</small>
							</div>
							<div class="job-field">
								<label for="type">Job Type *</label>
								<select required name="type" id="type" class="job-input job-select">
									<option value="Full Time" {{ old('type') === 'Full Time' ? 'selected' : '' }}>Full Time</option>
									<option value="Part Time" {{ old('type') === 'Part Time' ? 'selected' : '' }}>Part Time</option>
									<option value="Contract" {{ old('type') === 'Contract' ? 'selected' : '' }}>Contract</option>
									<option value="Temporary" {{ old('type') === 'Temporary' ? 'selected' : '' }}>Temporary</option>
								</select>
							</div>
							<div class="job-field">
								<label for="position">Job Position *</label>
								<input id="position" type="text" readonly class="job-input job-input-readonly" value="{{ $job->job_title }}" name="position">
								<input type="hidden" name="job_id" value="{{ $job->job_id }}">
							</div>
							<input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off" style="display:none;">
							<div class="job-field job-field-wide">
								<label>Confirm you are not a robot *</label>
								@if(!empty(config('services.recaptcha.site_key')))
									<div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
								@else
									<div class="alert alert-warning">reCAPTCHA is not configured.</div>
								@endif
							</div>
							<div class="job-field job-field-wide job-submit-wrap">
								<button type="submit" id="submit-btn" class="btn job-submit-btn">
									<span>Complete Application</span>
									<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
										<path d="M5 12h14" />
										<path d="m13 6 6 6-6 6" />
									</svg>
								</button>
							</div>
						</div>
					</form>
				</div>

				<div class="job-application-sidecard">
					<div class="job-section-label text-light">Before you submit</div>
					<h3>Keep it simple</h3>
					<ul class="job-side-points">
						<li>Upload a PDF CV under 5MB.</li>
						<li>The job title is locked to the posting you opened.</li>
						<li>We review applications as they come in.</li>
					</ul>
					<a href="{{ route('career.index') }}" class="btn job-back-btn job-back-btn--dark w-100">
						<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<path d="M19 12H5" />
							<path d="m11 6-6 6 6 6" />
						</svg>
						<span>Back to Careers</span>
					</a>
				</div>
			</div>
		</div>
	</section>
@else
	<section class="job-board-hero">
		<div class="container">
			<div class="job-board-hero-shell">
				<div class="job-board-hero-copy">
					<h1 class="job-board-title">Job not found</h1>
					<p class="job-board-subtitle">We couldn't load the posting you requested. Head back to the careers page to browse current openings.</p>
					<a href="{{ route('career.index') }}" class="btn default-button apply-now-btn">
						<span>Back to Careers</span>
						<svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<path d="M19 12H5" />
							<path d="m11 6-6 6 6 6" />
						</svg>
					</a>
				</div>
			</div>
		</div>
	</section>
@endif

@endsection

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
