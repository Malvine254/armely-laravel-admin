@extends('layouts.public')

@section('title', 'Services - Armely')

@push('styles')
<style>
	/* Service Navigation */
	.service-navigation {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 50px 0;
		box-shadow: 0 8px 30px rgba(47, 85, 151, 0.15);
		margin-bottom: 40px;
	}

	.service-search-box {
		max-width: 650px;
		margin: 0 auto 35px;
		position: relative;
		animation: slideDown 0.6s cubic-bezier(0.4, 0, 0.2, 1);
	}

	@keyframes slideDown {
		from {
			opacity: 0;
			transform: translateY(-20px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.service-search-box input {
		width: 100%;
		padding: 16px 50px 16px 28px;
		border: none;
		border-radius: 12px;
		font-size: 1.05rem;
		background: white;
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		color: #1a1f36;
	}

	.service-search-box input::placeholder {
		color: #999;
		font-weight: 500;
	}

	.service-search-box input:focus {
		outline: none;
		box-shadow: 0 8px 30px rgba(47, 85, 151, 0.25);
		transform: translateY(-2px);
		background: white;
	}

	.service-search-box i {
		position: absolute;
		right: 18px;
		top: 50%;
		transform: translateY(-50%);
		color: #2f5597;
		font-size: 1.1rem;
		pointer-events: none;
		transition: all 0.3s ease;
	}

	.service-search-box input:focus ~ i {
		color: #1e3a6d;
		transform: translateY(-50%) scale(1.1);
	}

	.category-filters {
		display: flex;
		justify-content: center;
		gap: 12px;
		flex-wrap: wrap;
		animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.1s backwards;
	}

	@keyframes slideUp {
		from {
			opacity: 0;
			transform: translateY(20px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.filter-btn {
		padding: 11px 24px;
		border: 2px solid rgba(255, 255, 255, 0.3);
		background: transparent;
		color: white;
		border-radius: 25px;
		cursor: pointer;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		font-size: 0.95rem;
		font-weight: 600;
		position: relative;
		overflow: hidden;
	}

	.filter-btn::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: rgba(255, 255, 255, 0.15);
		transition: left 0.3s ease;
		z-index: -1;
	}

	.filter-btn:hover {
		border-color: white;
		background: rgba(255, 255, 255, 0.1);
		transform: translateY(-2px);
	}

	.filter-btn:hover::before {
		left: 100%;
	}

	.filter-btn.active {
		background: white;
		color: #2f5597;
		border-color: white;
		box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
		font-weight: 700;
	}

	.filter-btn.active:hover {
		transform: translateY(-3px);
		box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
	}

	.service-count {
		margin-left: 6px;
		opacity: 0.85;
		font-size: 0.85rem;
		font-weight: 600;
	}

	/* ===== MODERN PAGINATION STYLING ===== */
	.modern-pagination-wrapper {
		margin-top: 60px;
		margin-bottom: 40px;
	}

	.modern-pagination {
		display: flex;
		justify-content: center;
		align-items: center;
		gap: 15px;
		flex-wrap: wrap;
		padding: 0;
		list-style: none;
	}

	.modern-pagination .page-btn {
		padding: 12px 28px;
		border-radius: 10px;
		font-weight: 600;
		font-size: 1rem;
		text-decoration: none;
		transition: all 0.3s ease;
		border: 2px solid rgba(255, 255, 255, 0.2);
		min-width: 120px;
		text-align: center;
	}

	.modern-pagination .page-btn:hover:not(.disabled) {
		transform: translateY(-3px);
		box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
		border-color: rgba(255, 255, 255, 0.4);
	}

	.modern-pagination .page-btn.disabled {
		opacity: 0.4;
		cursor: not-allowed;
	}

	/* ===== OLD PAGINATION STYLING (HIDDEN) ===== */
	.pagination {
		display: flex;
		justify-content: center;
		align-items: center;
		gap: 20px;
		margin-top: 60px;
		margin-bottom: 20px;
		list-style: none;
		padding: 0;
		flex-wrap: wrap;
	}

	.pagination li {
		list-style: none;
	}

	/* FORCE HIDE page numbers and arrows */
	.pagination li:not(:first-child):not(:last-child) {
		display: none !important;
		visibility: hidden !important;
		width: 0 !important;
		height: 0 !important;
		opacity: 0 !important;
	}

	/* Hide ALL content inside pagination links and set font-size 0 */
	.pagination a,
	.pagination span {
		font-size: 0 !important;
		color: transparent !important;
	}

	.pagination a *,
	.pagination span * {
		display: none !important;
		visibility: hidden !important;
		font-size: 0 !important;
		width: 0 !important;
		height: 0 !important;
	}

	.pagination svg {
		display: none !important;
		width: 0 !important;
		height: 0 !important;
		visibility: hidden !important;
		position: absolute !important;
	}

	/* Style only the button containers */
	.pagination a, .pagination span {
		display: inline-flex !important;
		align-items: center;
		justify-content: center;
		padding: 14px 32px;
		border: 2px solid #e5e7eb;
		background: white;
		text-decoration: none;
		border-radius: 12px;
		font-weight: 600;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
		min-width: 140px;
	}

	/* Add text ONLY via ::after */
	.pagination a[rel="prev"]::after {
		content: "« Previous";
		font-size: 1rem !important;
		color: #2f5597 !important;
		font-weight: 600;
	}

	.pagination a[rel="next"]::after {
		content: "Next »";
		font-size: 1rem !important;
		color: #2f5597 !important;
		font-weight: 600;
	}

	.pagination .disabled span::after {
		content: "« Previous";
		font-size: 1rem !important;
		color: #d1d5db !important;
		font-weight: 600;
	}

	.pagination .disabled:last-child span::after {
		content: "Next »";
		font-size: 1rem !important;
		color: #d1d5db !important;
		font-weight: 600;
	}

	.pagination a:hover {
		border-color: #2f5597;
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		transform: translateY(-3px);
		box-shadow: 0 10px 25px rgba(47, 85, 151, 0.25);
	}

	.pagination a:hover::after {
		color: white !important;
	}

	.pagination .disabled span {
		border-color: #f3f4f6;
		background: #fafafa;
		cursor: not-allowed;
		opacity: 0.5;
		box-shadow: none;
	}

	.pagination-summary {
		text-align: center;
		color: #6b7280;
		font-size: 1.05rem;
		margin-top: 25px;
		margin-bottom: 40px;
		font-weight: 500;
		letter-spacing: 0.3px;
		padding: 14px 28px;
		background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
		border-radius: 10px;
		border: 1px solid #e5e7eb;
		display: inline-block;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
	}

	.pagination-summary strong {
		color: #2f5597;
		font-weight: 700;
		font-size: 1.1rem;
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

	/* Responsive breakpoints for form elements */
	@media (max-width: 992px) {
		.service-search-box {
			max-width: 85%;
		}

		.category-filters {
			gap: 10px;
		}

		.filter-btn {
			padding: 10px 20px;
			font-size: 0.9rem;
		}
	}

	@media (max-width: 768px) {
		.service-navigation {
			padding: 35px 0;
		}

		.service-search-box {
			max-width: 95%;
			margin: 0 auto 25px;
		}

		.service-search-box input {
			padding: 14px 45px 14px 20px;
			font-size: 1rem;
		}

		.service-search-box i {
			font-size: 1rem;
			right: 15px;
		}

		.category-filters {
			overflow-x: auto;
			justify-content: flex-start;
			flex-wrap: nowrap;
			padding: 0 15px 10px;
			-webkit-overflow-scrolling: touch;
			scrollbar-width: thin;
			gap: 8px;
		}

		.category-filters::-webkit-scrollbar {
			height: 6px;
		}

		.category-filters::-webkit-scrollbar-track {
			background: rgba(255, 255, 255, 0.1);
			border-radius: 3px;
		}

		.category-filters::-webkit-scrollbar-thumb {
			background: rgba(255, 255, 255, 0.3);
			border-radius: 3px;
		}

		.filter-btn {
			white-space: nowrap;
			padding: 9px 18px;
			font-size: 0.85rem;
			flex-shrink: 0;
		}

		.modern-pagination .page-btn {
			padding: 10px 20px;
			font-size: 0.9rem;
			min-width: 100px;
		}
	}

	@media (max-width: 576px) {
		.service-navigation {
			padding: 25px 0;
		}

		.service-search-box {
			max-width: 100%;
			padding: 0 15px;
		}

		.service-search-box input {
			padding: 12px 40px 12px 18px;
			font-size: 0.95rem;
		}

		.filter-btn {
			padding: 8px 16px;
			font-size: 0.8rem;
		}

		.modern-pagination {
			gap: 10px;
			flex-direction: column;
			width: 100%;
		}

		.modern-pagination .page-btn {
			width: 90%;
			max-width: 280px;
			margin: 0 auto;
		}
	}

	@keyframes fadeIn {
		from { opacity: 0; transform: translateY(10px); }
		to { opacity: 1; transform: translateY(0); }
	}

	/* ===== SERVICE CARD ICON FIXES ===== */
	.pricing-table .single-table {
		height: 100%;
		min-height: auto !important;
		max-height: none !important;
		display: flex !important;
		flex-direction: column;
		padding: 1.4rem 1.25rem !important;
		gap: 0.55rem;
		text-align: left !important;
	}

	.pricing-table .single-table .table-head {
		text-align: left !important;
		display: flex;
		flex-direction: column;
		align-items: flex-start !important;
		justify-content: flex-start;
		padding: 0 !important;
		flex: 1;
		gap: 0.4rem;
	}

	.pricing-table .single-table .icon {
		width: 60px;
		height: 60px;
		display: flex !important;
		align-items: center;
		justify-content: center;
		margin: 0 0 6px;
		flex-shrink: 0;
		border-radius: 12px;
	}

	.pricing-table .single-table .icon i {
		font-size: 2.2rem !important;
		line-height: 1 !important;
		display: block;
		color: #fff !important;
	}

	.pricing-table .single-table .title {
		margin: 4px 0 0 !important;
		font-size: 1.05rem !important;
		line-height: 1.3;
		flex-shrink: 0;
		min-height: auto;
		display: block;
		text-align: left !important;
	}

	.pricing-table .single-table .price {
		margin-top: 4px;
		flex: 1;
		display: flex;
		align-items: flex-start;
		overflow: visible;
	}

	.pricing-table .single-table .price p {
		font-size: 0.98rem;
		line-height: 1.55;
		margin: 0;
		display: -webkit-box;
		-webkit-line-clamp: 7;
		-webkit-box-orient: vertical;
		overflow: hidden;
		text-overflow: ellipsis;
		text-align: left;
	}
</style>
@endpush

@section('content')

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
						@php
							$iconClass = $service->image && trim($service->image) !== '' ? $service->image : 'icofont-ui-settings';
						@endphp
						<div class="col-lg-4 col-md-12 col-12">
							<div class="single-table card-shadow default-background" style="max-block-size: 350px; min-block-size: 340px;">
								<a class="text-light" href="{{ route('service-details', ['name' => Str::slug($service->title)]) }}" style="text-decoration: none;">
									<div class="table-head">
										<div class="icon text-light">
											<i class="icofont text-light {{ $iconClass }}"></i>
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

		<!-- Modern Pagination -->
		@if($services instanceof \Illuminate\Pagination\LengthAwarePaginator && $services->lastPage() > 1)
			<div class="modern-pagination-wrapper">
				<div class="row">
					<div class="col-12">
						<div class="modern-pagination">
							@if ($services->onFirstPage())
								<span class="page-btn disabled default-background text-light">« Previous</span>
							@else
								<a href="{{ $services->previousPageUrl() }}" class="page-btn default-background text-light">« Previous</a>
							@endif

							@if ($services->hasMorePages())
								<a href="{{ $services->nextPageUrl() }}" class="page-btn default-background text-light">Next »</a>
							@else
								<span class="page-btn disabled default-background text-light">Next »</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-12 text-center">
						<div class="default-background text-light" style="display: inline-block; padding: 12px 24px; border-radius: 8px; font-size: 1rem; font-weight: 500;">
							Showing <strong>{{ $services->firstItem() }}</strong> to <strong>{{ $services->lastItem() }}</strong> of <strong>{{ $services->total() }}</strong> services
						</div>
					</div>
				</div>
			</div>
		@endif
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
