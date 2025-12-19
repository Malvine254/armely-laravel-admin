@extends('layouts.public')

@section('title', 'Services - Armely')

@push('styles')
<style>
	/* Service Navigation */
	.service-navigation {
		background: white;
		padding: 20px 0;
		box-shadow: 0 2px 8px rgba(0,0,0,0.05);
		margin-bottom: 30px;
	}

	.service-search-box {
		max-width: 600px;
		margin: 0 auto 15px;
		position: relative;
	}

	.service-search-box input {
		width: 100%;
		padding: 12px 45px 12px 20px;
		border: 2px solid #e0e0e0;
		border-radius: 8px;
		font-size: 1rem;
		transition: border-color 0.3s ease;
	}

	.service-search-box input:focus {
		outline: none;
		border-color: #2f5597;
	}

	.service-search-box i {
		position: absolute;
		right: 20px;
		top: 50%;
		transform: translateY(-50%);
		color: #999;
	}

	.category-filters {
		display: flex;
		justify-content: center;
		gap: 8px;
		flex-wrap: wrap;
	}

	.filter-btn {
		padding: 8px 20px;
		border: 1px solid #ddd;
		background: white;
		color: #666;
		border-radius: 20px;
		cursor: pointer;
		transition: all 0.3s ease;
		font-size: 0.9rem;
	}

	.filter-btn:hover {
		border-color: #2f5597;
		color: #2f5597;
	}

	.filter-btn.active {
		background: #2f5597;
		color: white;
		border-color: #2f5597;
	}

	.service-count {
		margin-left: 5px;
		opacity: 0.8;
		font-size: 0.85rem;
	}

	.no-results {
		text-align: center;
		padding: 40px 20px;
		color: #999;
	}

	.no-results i {
		font-size: 3rem;
		color: #ddd;
		margin-bottom: 15px;
	}

	@media (max-width: 768px) {
		.category-filters {
			overflow-x: auto;
			justify-content: flex-start;
			flex-wrap: nowrap;
			padding-bottom: 10px;
		}

		.filter-btn {
			white-space: nowrap;
		}
	}

	@keyframes fadeIn {
		from { opacity: 0; transform: translateY(10px); }
		to { opacity: 1; transform: translateY(0); }
	}
</style>
@endpush

@section('content')

<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Services</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Services</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<!-- Service Navigation & Filters -->
<section class="service-navigation">
	<div class="container">
		<div class="service-search-box">
			<input type="text" id="serviceSearch" placeholder="Search services...">
			<i class="fa fa-search"></i>
		</div>
		<div class="category-filters">
			<button class="filter-btn active" data-filter="all">
				All <span class="service-count" id="count-all">0</span>
			</button>
			<button class="filter-btn" data-filter="data">
				Data <span class="service-count" id="count-data">0</span>
			</button>
			<button class="filter-btn" data-filter="digital">
				Digital <span class="service-count" id="count-digital">0</span>
			</button>
			<button class="filter-btn" data-filter="ai">
				AI & ML <span class="service-count" id="count-ai">0</span>
			</button>
			<button class="filter-btn" data-filter="managed">
				Managed <span class="service-count" id="count-managed">0</span>
			</button>
			<button class="filter-btn" data-filter="advisory">
				Advisory <span class="service-count" id="count-advisory">0</span>
			</button>
		</div>
	</div>
</section>

<!-- Pricing Table -->
<section class="pricing-table mt-5" id="services-list">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="section-title"> 
					<h2>Empowering Your Tech Journey</h2>
					<center><hr class="default-background hr"></center>
					<p>Our experts provide tailored guidance in areas such as business planning, product development, marketing, financial management, and risk management, ensuring your company's competitiveness and sustainable growth.</p>
				</div>
			</div>
		</div>

		<!-- No Results Message -->
		<div id="noResults" class="no-results" style="display: none;">
			<i class="fa fa-search"></i>
			<h3>No Services Found</h3>
			<p>Try adjusting your search or filter to find what you're looking for.</p>
		</div>

		<div class="row" id="servicesContainer">
			@forelse($services as $service)
				<div class="col-lg-4 col-md-12 col-12">
					<div class="single-table card-shadow default-background" style="max-block-size: 350px; min-block-size: 340px;">
						<a class="text-light" href="{{ route('service-details', ['name' => Str::slug($service->title)]) }}" style="text-decoration: none;">
							<div class="table-head">
								<div class="icon text-light">
									<i class="icofont text-light {{ $service->image }}"></i>
								</div>
								<h4 class="title text-light">{{ $service->title }}</h4>
								<div class="price text-light">
									<p class="text-light">{{ Str::limit($service->body, 150) }}</p>
								</div>
							</div>
						</a>
					</div>
				</div>
			@empty
				<div class="col-12">
					<div class="alert alert-info text-center">
						<i class="fa fa-info-circle"></i> No services available at this time.
					</div>
				</div>
			@endforelse
		</div>
	</div>
</section>
<!--/ End Pricing Table -->

<!-- Start Appointment -->
<section class="appointment">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="section-title">
					<h2 id="consultation-form">Schedule a Consultation Today</h2>
					<center><hr class="default-background hr"></center>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 col-md-6 col-12 d-flex default-background mb-5">
				<form class="form p-5" id="consultation-form-action" method="post" action="{{ route('submit-consultation') }}">
					@csrf
					<p class="p-3 alert" id="ConsultationMessage"></p>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-12">
							<div class="form-group input-with-background">
								<label class="text-start text-light">Full Name *</label>
								<input required class="remove-input-background" name="name" type="text" placeholder="Name" value="{{ old('name') }}">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-12">
							<div class="form-group">
								<label class="text-start text-light">Email Address *</label>
								<input required class="remove-input-background" name="email" type="email" placeholder="Email" value="{{ old('email') }}">
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="form-group">
								<label class="text-start text-light">Phone Number *</label>
								<input required class="remove-input-background" name="phone" type="text" placeholder="Phone" value="{{ old('phone') }}">
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="form-group">
								<label class="text-start text-light">Organization Name *</label>
								<input required class="remove-input-background" name="organization" type="text" placeholder="Organization Name" value="{{ old('organization') }}">
							</div>
						</div>
						<div class="col-lg-4 col-md-4 col-12">
							<div class="form-group">
								<label class="text-start text-light">Service of Interest *</label>
								<select required name="service_type" class="form-control remove-input-background">
									<option value="" disabled selected>Select Service of Interest</option> 
									<option value="Data Services" {{ old('service_type') === 'Data Services' ? 'selected' : '' }}>Data Services</option>
									<option value="Web Development" {{ old('service_type') === 'Web Development' ? 'selected' : '' }}>Web Development</option>
									<option value="Business Intelligence" {{ old('service_type') === 'Business Intelligence' ? 'selected' : '' }}>Business Intelligence</option>
									<option value="Managed Services" {{ old('service_type') === 'Managed Services' ? 'selected' : '' }}>Managed Services</option>
									<option value="Advisory Services" {{ old('service_type') === 'Advisory Services' ? 'selected' : '' }}>Advisory Services</option>
								</select>
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-12">
							<div class="form-group">
								<label class="text-start text-light">Message *</label>
								<textarea required class="remove-input-background" name="message" placeholder="Write Your Message Here.....">{{ old('message') }}</textarea>
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
								<button type="submit" class="btn send-message-btn" name="submit_form">Send Message</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!-- End Appointment -->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('serviceSearch');
	const filterBtns = document.querySelectorAll('.filter-btn');
	const servicesContainer = document.getElementById('servicesContainer');
	const noResults = document.getElementById('noResults');
	
	let currentFilter = 'all';
	let searchTerm = '';

	// Search functionality
	searchInput.addEventListener('input', function() {
		searchTerm = this.value.toLowerCase();
		filterServices();
	});

	// Filter buttons
	filterBtns.forEach(btn => {
		btn.addEventListener('click', function() {
			const filter = this.getAttribute('data-filter');
			activateFilter(filter);
		});
	});

	function activateFilter(filter) {
		currentFilter = filter;
		
		// Update active button
		filterBtns.forEach(btn => btn.classList.remove('active'));
		document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
		
		// Filter services
		filterServices();
	}

	function filterServices() {
		const services = servicesContainer.querySelectorAll('.single-table');
		let visibleCount = 0;

		// Service mapping based on exact header menu structure - optimized for database content
		const serviceMap = {
			'ai': [
				'ai consulting',
				'ai advisory',
				'generative',
				'poc',
				'copilot'
			],
			'data': [
				'fabric',
				'data science',
				'analytics',
				'data strategy',
				'databricks',
				'snowflake',
				'sql',
				'warehouse'
			],
			'digital': [
				'api',
				'powerapps',
				'power automate',
				'automate',
				'virtual agent',
				'power pages',
				'dynamics',
				'robotic',
				'rpa',
				'sharepoint'
			],
			'managed': [
				'sql server support',
				'sql support',
				'support',
				'managed'
			]
		};

		services.forEach(service => {
			const title = service.querySelector('h4') ? service.querySelector('h4').textContent.toLowerCase() : '';
			const description = service.querySelector('p') ? service.querySelector('p').textContent.toLowerCase() : '';
			const fullText = (title + ' ' + description).toLowerCase();
			
			// Determine category by checking service map
			let category = 'other';
			
			for (const [cat, keywords] of Object.entries(serviceMap)) {
				if (keywords.some(keyword => fullText.includes(keyword))) {
					category = cat;
					break;
				}
			}
			
			// Freemiums goes to advisory
			if (title.includes('freemium')) {
				category = 'advisory';
			}

			// Check if service matches current filter and search
			const matchesFilter = currentFilter === 'all' || category === currentFilter;
			const matchesSearch = searchTerm === '' || 
			                     title.includes(searchTerm) || 
			                     description.includes(searchTerm);

			if (matchesFilter && matchesSearch) {
				service.style.display = '';
				visibleCount++;
				
				// Add fade-in animation
				service.style.animation = 'fadeIn 0.5s ease';
			} else {
				service.style.display = 'none';
			}
		});

		// Show/hide no results message
		if (visibleCount === 0) {
			noResults.style.display = 'block';
			noResults.style.animation = 'fadeIn 0.5s ease';
		} else {
			noResults.style.display = 'none';
		}

		// Update counts
		updateCounts();
	}

	function updateCounts() {
		const services = servicesContainer.querySelectorAll('.single-table');
		const counts = {
			all: 0,
			data: 0,
			digital: 0,
			ai: 0,
			managed: 0,
			advisory: 0
		};

		// Service mapping based on exact header menu structure - optimized for database content
		const serviceMap = {
			'ai': [
				'ai consulting',
				'ai advisory',
				'generative',
				'poc',
				'copilot'
			],
			'data': [
				'fabric',
				'data science',
				'analytics',
				'data strategy',
				'databricks',
				'snowflake',
				'sql',
				'warehouse'
			],
			'digital': [
				'api',
				'powerapps',
				'power automate',
				'automate',
				'virtual agent',
				'power pages',
				'dynamics',
				'robotic',
				'rpa',
				'sharepoint'
			],
			'managed': [
				'sql server support',
				'sql support',
				'support',
				'managed'
			]
		};

		services.forEach(service => {
			const title = service.querySelector('h4') ? service.querySelector('h4').textContent.toLowerCase() : '';
			const description = service.querySelector('p') ? service.querySelector('p').textContent.toLowerCase() : '';
			const fullText = (title + ' ' + description).toLowerCase();
			
			counts.all++;
			
			// Categorize by checking service map
			for (const [cat, keywords] of Object.entries(serviceMap)) {
				if (keywords.some(keyword => fullText.includes(keyword))) {
					counts[cat]++;
					return;
				}
			}
			
			// Freemiums goes to advisory
			if (title.includes('freemium')) {
				counts.advisory++;
			}
		});

		// Update count badges
		Object.keys(counts).forEach(key => {
			const countElement = document.getElementById(`count-${key}`);
			if (countElement) {
				countElement.textContent = counts[key];
			}
		});
	}

	// Initial count update
	updateCounts();

	// Smooth scroll for all internal links
	document.querySelectorAll('a[href^="#"]').forEach(anchor => {
		anchor.addEventListener('click', function (e) {
			const href = this.getAttribute('href');
			if (href !== '#' && href !== '#consultation-form') {
				e.preventDefault();
				const target = document.querySelector(href);
				if (target) {
					target.scrollIntoView({ behavior: 'smooth', block: 'start' });
				}
			}
		});
	});
});
</script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('consultation-form-action');
	if (!form) return;

	const messageDiv = document.getElementById('ConsultationMessage');
	const submitBtn = form.querySelector('button[name="submit_form"]');

	form.addEventListener('submit', function(e) {
		e.preventDefault();
		e.stopPropagation();

		const originalBtnText = submitBtn.textContent;
		messageDiv.textContent = '';
		messageDiv.className = 'p-3 alert';
		messageDiv.style.display = 'none';

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

@endsection
