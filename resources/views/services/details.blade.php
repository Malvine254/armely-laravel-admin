@extends('layouts.public')

@php
	$servicePageTitles = [
		'training' => 'Microsoft and AI Training for Teams | Armely',
		'managed-services' => 'Microsoft Managed Services | Armely',
		'sql-data-warehousing' => 'SQL Server Migration and Management | Armely',
	];
@endphp

@section('title', $servicePageTitles[$serviceName ?? ''] ?? (($service->title ?? 'Service') . ' | Armely'))
@section('meta_description', 'View detailed Armely service capabilities, delivery approach, and engagement options for Microsoft-focused transformation initiatives.')
@section('canonical_url', \App\Support\ServiceUrl::url($serviceName ?? ($service->title ?? '')))

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

    .cta-section .service-contact-form {
        display: block;
    }

    .cta-section .service-contact-message {
        display: none;
        margin: 0 0 16px;
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .cta-section .service-contact-message.is-success {
        display: block;
        background: rgba(34, 197, 94, 0.1);
        color: #166534;
        border: 1px solid rgba(34, 197, 94, 0.22);
    }

    .cta-section .service-contact-message.is-error {
        display: block;
        background: rgba(239, 68, 68, 0.1);
        color: #991b1b;
        border: 1px solid rgba(239, 68, 68, 0.22);
    }

    .cta-section .service-contact-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 14px;
    }

    .cta-section .service-contact-row {
        margin-bottom: 0;
    }

    .cta-section .service-contact-row.service-contact-row-full {
        grid-column: 1 / -1;
    }

    .cta-section .service-contact-row label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6B7FA3;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }

    .cta-section .service-contact-field {
        width: 100%;
        display: block;
        background: #FFFFFF;
        border: 1px solid rgba(41, 78, 139, 0.15);
        border-radius: 7px;
        padding: 11px 14px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.875rem;
        color: #1A2540;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .cta-section .service-contact-field:focus {
        border-color: rgba(41, 78, 139, 0.45);
        box-shadow: 0 0 0 3px rgba(41, 78, 139, 0.08);
    }

    .cta-section .service-contact-textarea {
        min-height: 128px;
        resize: vertical;
    }

    .cta-section .service-contact-field option {
        background: #fff;
        color: #1A2540;
    }

    .cta-section .service-contact-recaptcha {
        min-height: 78px;
    }

    .cta-section .service-contact-submit {
        width: 100%;
        background: #294e8b;
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 14px 16px;
        margin-top: 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s;
    }

    .cta-section .service-contact-submit:hover {
        background: #3d6ab5;
        transform: translateY(-1px);
    }

    .cta-section .service-contact-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .cta-section .service-contact-note {
        text-align: center;
        margin-top: 12px;
        font-size: 0.75rem;
        color: #6B7FA3;
    }

    @media (max-width: 768px) {
        .cta-section .service-contact-grid {
            grid-template-columns: 1fr;
        }

        .cta-section .service-contact-row.service-contact-row-full {
            grid-column: auto;
        }
    }
</style>
@endpush

@section('content')

@php
	$modernServiceIncludes = [
		'generative-ai' => 'services.generative-ai',
		'data-strategy' => 'services.data-strategy',
		'microsoft-fabric' => 'services.fabric',
		'snowflake' => 'services.snowflake',
		'microsoft-dynamics-365' => 'services.dynamics365',
		'sql-data-warehousing' => 'services.sql-data-warehousing',
		'api-data-access' => 'services.apidataaccess',
		'sharepoint-online' => 'services.sharepointonline',
		'm365-governance' => 'services.m365-governance',
		'fractional-dba' => 'services.fractional_dba_blade',
		'training' => 'services.training_blade',
		'managed-services' => 'services.managed-services',
		'copilot' => 'services.copilot',
		'microsoft-power-pages' => 'services.powerplatform',
		'custom-development' => 'services.custom-development',
	];
@endphp

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

@if($serviceName && isset($modernServiceIncludes[$serviceName]))
	@include($modernServiceIncludes[$serviceName])
@elseif($serviceName)
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
						@if($serviceName === 'sql-data-warehousing')
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
								<label class="text-start text-light">Organization Name</label>
								<div class="form-group">
									<input class="remove-input-background" name="organization" type="text" placeholder="Organization Name" value="{{ old('organization') }}">
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
	document.addEventListener('DOMContentLoaded', function() {
		const cards = Array.from(document.querySelectorAll('.cta-section .cta-form'));
		if (!cards.length) {
			return;
		}

		const csrfToken = @json(csrf_token());
		const submitUrl = @json(route('submit-consultation'));
		const recaptchaSiteKey = @json($recaptchaSiteKey ?? '');
		const defaultServiceTitle = @json($servicePageTitles[$serviceName ?? ''] ?? (($service->title ?? 'Service') . ' | Armely'));
		const defaultServiceTypeLabel = defaultServiceTitle.replace(/\s*\|\s*Armely$/, '');
		const defaultServiceName = @json($serviceName ?? '');

		const optionMap = {
			'managed-services': [
				'Managed Services',
				'Managed services assessment',
				'Need ongoing support',
				'Not sure, need a recommendation'
			],
			'm365-governance': [
				'M365 Governance',
				'Tenant health check',
				'Copilot readiness',
				'Not sure, need a recommendation'
			],
			'training': [
				'Training',
				'Team enablement session',
				'Workshop or lunch and learn',
				'Not sure, need a recommendation'
			],
			'data-strategy': [
				'Data & AI Strategy',
				'Roadmap and assessment',
				'Governance review',
				'Not sure, need a recommendation'
			],
			'copilot': [
				'Microsoft Copilot',
				'Copilot readiness assessment',
				'Adoption planning',
				'Not sure, need a recommendation'
			],
			'custom-development': [
				'Custom Development',
				'Application discovery',
				'Build a new workflow',
				'Not sure, need a recommendation'
			]
		};

		function escapeHtml(value) {
			return String(value || '')
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#39;');
		}

		function firstErrorMessage(errors) {
			if (!errors || typeof errors !== 'object') {
				return '';
			}

			for (const key of Object.keys(errors)) {
				const value = errors[key];
				if (Array.isArray(value) && value.length) {
					return value[0];
				}
				if (typeof value === 'string' && value) {
					return value;
				}
			}

			return '';
		}

		function buildOptions(serviceName) {
			const options = optionMap[serviceName] || [];
			const values = [defaultServiceTypeLabel];

			options.forEach(option => {
				if (!values.includes(option)) {
					values.push(option);
				}
			});

			if (!values.includes('Need a recommendation')) {
				values.push('Need a recommendation');
			}

			return values;
		}

		function buildForm(card) {
			const existingTitle = card.querySelector('.form-title')?.textContent?.trim() || 'Book Your Free Assessment';
			const existingSub = card.querySelector('.form-sub')?.textContent?.trim() || 'Tell us about your situation.';
			const existingButton = card.querySelector('.form-submit')?.textContent?.trim() || 'Request Consultation';
			const serviceName = defaultServiceName || '';
			const serviceOptions = buildOptions(serviceName);

			const optionMarkup = serviceOptions.map((option, index) => {
				const selected = index === 0 ? ' selected' : '';
				return `<option value="${escapeHtml(option)}"${selected}>${escapeHtml(option)}</option>`;
			}).join('');

			card.innerHTML = `
				<form class="service-contact-form" method="post" action="${escapeHtml(submitUrl)}" data-service-contact-form>
					<input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
					<input type="hidden" name="website" value="">
					<p class="service-contact-message" role="alert" aria-live="polite"></p>
					<div class="form-title">${escapeHtml(existingTitle)}</div>
					<div class="form-sub">${escapeHtml(existingSub)}</div>
					<div class="service-contact-grid">
						<div class="service-contact-row">
							<label>Full Name *</label>
							<input class="service-contact-field" name="name" type="text" placeholder="Jane Smith" required value="">
						</div>
						<div class="service-contact-row">
							<label>Business Email *</label>
							<input class="service-contact-field" name="email" type="email" placeholder="jane@yourcompany.com" required value="">
						</div>
						<div class="service-contact-row">
							<label>Organization</label>
							<input class="service-contact-field" name="organization" type="text" placeholder="Acme Corp" value="">
						</div>
						<div class="service-contact-row">
							<label>Phone</label>
							<input class="service-contact-field" name="phone" type="tel" placeholder="Optional" value="">
						</div>
						<div class="service-contact-row service-contact-row-full">
							<label>Primary Need *</label>
							<select class="service-contact-field" name="service_type" required>
								<option value="" disabled>Select one</option>
								${optionMarkup}
							</select>
						</div>
						<div class="service-contact-row service-contact-row-full">
							<label>Message *</label>
							<textarea class="service-contact-field service-contact-textarea" name="message" placeholder="Tell us what you need help with..." required></textarea>
						</div>
						<div class="service-contact-row service-contact-row-full">
							<label>Confirm you are not a robot *</label>
							${recaptchaSiteKey ? `<div class="service-contact-recaptcha" data-sitekey="${escapeHtml(recaptchaSiteKey)}"></div>` : '<div class="service-contact-message is-error" style="display:block;">reCAPTCHA is not configured. Please contact support.</div>'}
						</div>
					</div>
					<button type="submit" class="service-contact-submit">${escapeHtml(existingButton)}</button>
					<div class="service-contact-note">No spam. No sales pressure. Just a useful conversation.</div>
				</form>
			`;
		}

		cards.forEach(buildForm);

		function renderRecaptchaWidgets() {
			if (typeof grecaptcha === 'undefined' || !grecaptcha.render) {
				return false;
			}

			let renderedAny = false;

			document.querySelectorAll('.service-contact-recaptcha').forEach(function(widget) {
				if (widget.dataset.rendered === '1' || !widget.getAttribute('data-sitekey')) {
					return;
				}

				try {
					const widgetId = grecaptcha.render(widget, {
						sitekey: widget.getAttribute('data-sitekey')
					});
					widget.dataset.rendered = '1';
					widget.dataset.widgetId = String(widgetId);
					renderedAny = true;
				} catch (error) {
					// Ignore re-render attempts.
				}
			});

			return renderedAny;
		}

		const recaptchaLoader = document.createElement('script');
		recaptchaLoader.src = 'https://www.google.com/recaptcha/api.js?render=explicit';
		recaptchaLoader.async = true;
		recaptchaLoader.defer = true;
		document.head.appendChild(recaptchaLoader);

		const recaptchaTimer = window.setInterval(function() {
			if (renderRecaptchaWidgets()) {
				window.clearInterval(recaptchaTimer);
			}
		}, 250);

		window.setTimeout(function() {
			window.clearInterval(recaptchaTimer);
		}, 10000);

		document.addEventListener('submit', function(event) {
			const form = event.target.closest ? event.target.closest('.service-contact-form') : null;
			if (!form) {
				return;
			}

			event.preventDefault();

			const messageEl = form.querySelector('.service-contact-message');
			const submitBtn = form.querySelector('.service-contact-submit');
			const recaptchaEl = form.querySelector('.service-contact-recaptcha');
			const originalLabel = submitBtn.textContent;

			messageEl.className = 'service-contact-message';
			messageEl.style.display = 'none';
			messageEl.textContent = '';

			if (!recaptchaSiteKey) {
				messageEl.className = 'service-contact-message is-error';
				messageEl.style.display = 'block';
				messageEl.textContent = 'reCAPTCHA is not configured. Please contact support.';
				return;
			}

			const widgetId = recaptchaEl ? recaptchaEl.dataset.widgetId : null;
			const recaptchaResponse = widgetId && typeof grecaptcha !== 'undefined' && grecaptcha.getResponse
				? grecaptcha.getResponse(Number(widgetId))
				: '';

			if (recaptchaEl && !recaptchaResponse) {
				messageEl.className = 'service-contact-message is-error';
				messageEl.style.display = 'block';
				messageEl.textContent = 'Please verify that you are not a robot.';
				return;
			}

			submitBtn.disabled = true;
			submitBtn.textContent = 'Sending...';

			const formData = new FormData(form);
			formData.append('g-recaptcha-response', recaptchaResponse);

			fetch(submitUrl, {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || csrfToken,
					'Accept': 'application/json'
				},
				body: formData
			})
			.then(async response => {
				const data = await response.json().catch(() => ({}));

				if (!response.ok || data.success === false) {
					const message = data.message || firstErrorMessage(data.errors) || 'An error occurred. Please try again.';
					throw new Error(message);
				}

				return data;
			})
			.then(data => {
				messageEl.className = 'service-contact-message is-success';
				messageEl.style.display = 'block';
				messageEl.textContent = data.message || 'Your consultation request has been submitted successfully!';
				form.reset();

				if (recaptchaEl && typeof grecaptcha !== 'undefined' && grecaptcha.reset && recaptchaEl.dataset.widgetId) {
					grecaptcha.reset(Number(recaptchaEl.dataset.widgetId));
				}
			})
			.catch(error => {
				messageEl.className = 'service-contact-message is-error';
				messageEl.style.display = 'block';
				messageEl.textContent = error && error.message ? error.message : 'An error occurred. Please try again.';

				if (recaptchaEl && typeof grecaptcha !== 'undefined' && grecaptcha.reset && recaptchaEl.dataset.widgetId) {
					grecaptcha.reset(Number(recaptchaEl.dataset.widgetId));
				}
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.textContent = originalLabel;
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
