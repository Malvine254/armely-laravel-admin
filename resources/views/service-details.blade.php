@extends('layouts.public')

@section('title', isset($service) ? $service->title . ' Details - Armely' : 'Service Details - Armely')

@push('styles')
<style>
	*{
		 font-size: 16px;
	}
	 blockquote {
        position: relative;
        padding: 1em;
        margin: 1em 0;
        color: #fff;
        border-left: 10px solid maroon;
        background: #2f5597;
    }
    
    blockquote p {
        display: inline;
        color: #fff;
        margin-top: 10px !important;
    }
    .vertical-line{
        border-left: 4px solid #2f5597;
    }
</style>
@endpush

@section('content')

{{-- <!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Service Details</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li><a href="{{ route('services') }}">Services</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">{{ $serviceName ?? 'Service Details' }}</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs --> --}}

@if($serviceName)
	<!-- Start Portfolio Details Area -->
	<section class="pf-details mt-5">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="inner-content">
						<div class="body-text">
							
							<!-- AI Advisory Service -->
							@if($serviceName === 'ai-advisory')
								@include('services.ai-advisory')
							@endif

							<!-- AI Consulting Service -->
							@if($serviceName === 'ai-consulting')
								@include('services.ai-consulting')
							@endif

							<!-- Generative AI Service -->
							@if($serviceName === 'generative-ai')
								@include('services.generative-ai')
							@endif

							<!-- Data Strategy Service -->
							@if($serviceName === 'data-strategy')
								@include('services.data-strategy')
							@endif

							<!-- Data Science Service -->
						@if($serviceName === 'data-science-and-analytics')
							@include('services.data-science')
						@endif

						<!-- Microsoft Fabric Service -->
						@if($serviceName === 'microsoft-fabric')
							@include('services.fabric')
						@endif

						<!-- Fabric Capacity Service -->
						@if($serviceName === 'estimate-your-fabric-capacity')
							@include('services.fabric_capacity')
						@endif

						<!-- SQL Support Service -->
						@if($serviceName === 'sql-server-support')
							@include('services.sqlsupport')
						@endif

						<!-- App Support Service -->
						@if($serviceName === 'applications-support')
							@include('services.appsupport')
						@endif

						<!-- Power Apps Service -->
						@if($serviceName === 'microsoft-powerapps')
							@include('services.powerapps')
						@endif

						<!-- Power Automate Service -->
						@if($serviceName === 'microsoft-power-automate')
							@include('services.powerautomate')
						@endif
							<!-- Snowflake Service -->
							@if($serviceName === 'snowflake')
								@include('services.snowflake')
							@endif

							<!-- Dynamics 365 Service -->
						@if($serviceName === 'microsoft-dynamics-365')
							@include('services.dynamics365')
						@endif

						<!-- SQL Data Warehousing Service -->
						@if($serviceName === 'sql-&-data-warehousing')
							@include('services.sql-data-warehousing')
						@endif

						<!-- API Data Access Service -->
						@if($serviceName === 'api-data-access')
							@include('services.apidataaccess')
						@endif

						<!-- Virtual Agents Service -->
						@if($serviceName === 'microsoft-power-virtual-agents')
							@include('services.virtualagents')
						@endif

						<!-- Robotic Process Automation Service -->
						@if($serviceName === 'robotic-processing-automation')
							@include('services.roboticprocessing')
						@endif

						<!-- SharePoint Online Service -->
						@if($serviceName === 'sharepoint-online')
							@include('services.sharepointonline')
						@endif
							<!-- Copilot Service -->
							@if($serviceName === 'copilot')
								@include('services.copilot')
							@endif

							<!-- Power Platform Service -->
						@if($serviceName === 'microsoft-power-pages')
							@include('services.powerplatform')
						@endif
							<!-- Databricks Service -->
							@if($serviceName === 'databricks')
								@include('services.databricks')
							@endif

							<!-- POC Starter AI Service -->
							@if($serviceName === 'ai-poc-starter')
								@include('services.pocstarter-ai')
							@endif

							<!-- Freemiums Service -->
							@if($serviceName === 'freemiums')
								@include('services.freemiums')
							@endif

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End Portfolio Details Area -->

	<!-- Start Appointment -->
	<section class="appointment">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="section-title">
						<h2 id="consultation">Schedule a Consultation Today</h2>
						<center><hr class="default-background hr"></center>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12 col-md-6 col-12 d-flex default-background mb-5">
					<form class="form p-5" id="contact-form" method="post" action="{{ route('submit-consultation') }}">
						@csrf
						<div id="ServiceDetailsMessage" style="display: none;"></div>
						<div class="row">
							<input type="hidden" name="service_type" value="{{ isset($service) && !empty($service->title) ? $service->title : ($serviceName ?? 'Service Inquiry') }}">
							<input type="text" name="website" class="honeypot" style="display:none;" tabindex="-1" autocomplete="off">
							<div class="col-lg-6 col-md-6 col-12">
								<label class="text-start text-light">Name *</label>
								<div class="form-group input-with-background">
									<input required class="remove-input-background" name="name" type="text" placeholder="Name" value="{{ old('name') }}">
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<label class="text-start text-light">Email *</label>
								<div class="form-group">
									<input required class="remove-input-background" name="email" type="email" placeholder="Email" value="{{ old('email') }}">
								</div> 
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<label class="text-start text-light">Phone Number *</label>
								<div class="form-group">
									<input required class="remove-input-background" name="phone" type="text" placeholder="Phone" value="{{ old('phone') }}">
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<label class="text-start text-light">Organization Name *</label>
								<div class="form-group">
									<input required class="remove-input-background" name="organization" type="text" placeholder="Organization Name" value="{{ old('organization') }}">
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-12">
								<label class="text-start text-light">Message *</label>
								<div class="form-group">
									<textarea required class="remove-input-background" name="message" placeholder="Write Your Message Here.....">{{ old('message') }}</textarea>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-12">
								<label class="text-start text-light">Confirm you are not a robot *</label>
								<div class="form-group">
									@if(!empty($recaptchaSiteKey))
										<div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
									@else
										<div class="alert alert-warning">reCAPTCHA is not configured. Please set <strong>CAPTURE_SITE_KEY</strong>.</div>
									@endif
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

@else
	<div class="container mt-5">
		<div class="alert alert-warning text-center">
			<h3><i class="fa fa-info-circle"></i> Service Not Found</h3>
			<p>The requested service could not be found. Please check the URL or <a href="{{ route('services') }}">view all services</a>.</p>
		</div>
	</div>
@endif

<!-- Floating Chat Button -->
<section>	
	<div class="floating-btn">
		<button id="myBtn" style="border-radius: 50%; height: 60px; width: 60px; background-color: rgb(47,85,151);" type="button" class="btn btn-primary btn-lg h1">
			<i class="fa fa-comments"></i>
		</button>
	</div>
	<div id="myModal" class="modal-chat">
		<div class="modal-content-chat col-lg-4">
			<span class="close">&times;</span>
			<iframe src="https://copilotstudio.microsoft.com/environments/Default-588cadf4-9902-4465-86c0-8bcf04f4f102/bots/crc65_armelyCom/webchat?__version__=2"
			frameborder="0" style="width: 100%; height: 80%;"></iframe>  
		</div>
	</div>
</section>


@push('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const form = document.getElementById('contact-form');
		if (!form) return;

		const messageDiv = document.getElementById('ServiceDetailsMessage');
		const submitBtn = form.querySelector('button[type="submit"]');
		const recaptchaEl = form.querySelector('.g-recaptcha');

		if (recaptchaEl && typeof grecaptcha !== 'undefined' && recaptchaEl.childElementCount === 0) {
			try {
				grecaptcha.render(recaptchaEl, {
					sitekey: recaptchaEl.getAttribute('data-sitekey')
				});
			} catch (e) {
				// ignore if already rendered by auto-render
			}
		}

		form.addEventListener('submit', function(e) {
			e.preventDefault();
			e.stopPropagation();

			const originalBtnText = submitBtn.textContent;
			messageDiv.textContent = '';
			messageDiv.className = 'p-3 alert';
			messageDiv.style.display = 'none';

			if (!recaptchaEl) {
				messageDiv.className = 'p-3 alert alert-danger alert-dismissible fade show';
				messageDiv.innerHTML = '<strong>Error:</strong> reCAPTCHA is not configured. Please contact support.' +
					'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
				messageDiv.style.display = 'block';
				return;
			}

			const recaptchaResponse = typeof grecaptcha !== 'undefined' ? grecaptcha.getResponse() : '';
			if (!recaptchaResponse) {
				messageDiv.className = 'p-3 alert alert-danger alert-dismissible fade show';
				messageDiv.innerHTML = '<strong>Error:</strong> Please verify that you are not a robot.' +
					'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
				messageDiv.style.display = 'block';
				return;
			}

			submitBtn.disabled = true;
			submitBtn.textContent = 'Sending...';

			const formData = new FormData(form);
			formData.append('g-recaptcha-response', recaptchaResponse);

			fetch('{{ route("submit-consultation") }}', {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
					'Accept': 'application/json'
				},
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				messageDiv.className = 'p-3 alert';
				if (data.success) {
					messageDiv.classList.add('alert-success');
					messageDiv.textContent = '✅ ' + data.message;
					form.reset();
					if (typeof grecaptcha !== 'undefined') {
						grecaptcha.reset();
					}
				} else {
					messageDiv.classList.add('alert-danger');
					messageDiv.textContent = '❌ ' + (data.message || 'An error occurred. Please try again.');
				}
				messageDiv.style.display = 'block';
			})
			.catch(error => {
				console.error('Error:', error);
				messageDiv.className = 'p-3 alert alert-danger';
				messageDiv.textContent = '❌ An error occurred. Please try again.';
				messageDiv.style.display = 'block';
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.textContent = originalBtnText;
			});
		}, true);
	});
</script>
@endpush

@push('scripts')
<script>
	// Chat modal functionality
	var modal = document.getElementById("myModal");
	var btn = document.getElementById("myBtn");
	var span = document.getElementsByClassName("close")[0];

	btn.onclick = function() {
		modal.style.display = "block";
	}

	span.onclick = function() {
		modal.style.display = "none";
	}

	window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}
</script>
@endpush

@endsection
