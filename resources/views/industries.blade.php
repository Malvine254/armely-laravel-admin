@extends('layouts.public')

@section('title', 'Industries We Transform - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/industries-modern.css') }}">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Industries We Transform</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Industries We Transform</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<section class="industries-section-modern ">
	<div class="mt-5 ">
		<div class="row"> <div class="col-12"> <button class="btn btn-modern-toggle default-background btn-block d-md-none mb-3" type="button" data-toggle="collapse" data-target="#tabsMenu" aria-expanded="false" aria-controls="tabsMenu">
					<i class="fa fa-bars"></i> SELECT INDUSTRY
				</button>
				<div class="collapse d-md-block" id="tabsMenu">
					<ul class="nav nav-tabs modern-tabs-industries" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
								<i class="icofont-doctor"></i>
								<strong>Healthcare</strong>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="energy-tab-nav" data-toggle="tab" href="#energy" role="tab" aria-controls="energy" aria-selected="false">
								<i class="icofont-fire-burn"></i>
								<strong>Energy</strong>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="government-tab" data-toggle="tab" href="#government" role="tab" aria-controls="government" aria-selected="false">
								<i class="icofont-building-alt"></i>
								<strong>Local Government</strong>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="legal-tab" data-toggle="tab" href="#legal" role="tab" aria-controls="legal" aria-selected="false">
								<i class="icofont-law-document"></i>
								<strong>Legal</strong>
							</a>
						</li>
					</ul>
				</div>
				<div class="tab-content modern-tab-content" id="myTabContent">
					<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
						<div class="tab-content-wrapper">
							<div class="mt-5">
								<div class="row align-items-center industry-intro-section">
									<div class="col-md-6">
										<div class="industry-intro-content">
											<span class="industry-tag">HEALTHCARE SOLUTIONS</span>
											<h2 class="industry-title">Healthcare</h2>
											<div class="title-underline"></div>
											<p class="industry-description">Data empowers personalized healthcare journeys. Analyzing medical history and wearables allows providers to tailor treatments and optimize workflows, leading to better patient outcomes.</p>
											<p class="industry-description">We partner with you to unlock this potential. Our expertise in data analytics and EHR integration empowers you to consolidate patient data, generate actionable insights for personalized care, and make data-driven decisions for better resource allocation and cost reduction.</p>
											<p class="industry-description">Accelerate innovation while improving healthcare experience with AI-powered Solutions, actionable insights and trustworthy capabilities</p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="industry-image-wrapper">
											<img class="img-fluid industry-img" src="{{ asset('images/industry/nurse.png') }}" alt="Healthcare Industry">
										</div>
									</div>
								</div>
							</div>
							<div class="row mt-5 g-4">
								<div class="col-md-4 mb-3">
									<div class="card modern-solution-card p-4">
										<div class="solution-icon-wrapper default-background">
											<img class="img-fluid" width="48" height="48" src="https://www.microsoft.com/content/dam/microsoft/final/en-us/microsoft-brand/icons/Icon-Microsoft-fabric-24x24.svg" alt="microsoft-fabric"/>
										</div>
										<h6 class="solution-title"><strong>Microsoft Fabric in healthcare</strong></h6>
										<p class="solution-description">Unlock powerful analytics in healthcare data. As Microsoft Fabric Partners we empower organizations build data-driven decisions solutions</p>
									</div>
								</div>
								<div class="col-md-4">
									<div class="card modern-solution-card p-4">
										<div class="solution-icon-wrapper default-background">
											<img class="img-fluid" width="48" height="48" src="https://www.microsoft.com/content/dam/microsoft/final/en-us/microsoft-brand/icons/Icon-Power-Platform-24x24.svg" alt="power-platform"/>
										</div>
										<h6 class="solution-title"><strong>Power Platform in healthcare</strong></h6>
										<p class="solution-description">Streamline workflows and automates tasks, building low-code/no-code solutions that empower organizations to transform data into actionable insights.</p>
									</div>
								</div>
								<div class="col-md-4">
									<div class="card modern-solution-card p-4">
										<div class="solution-icon-wrapper default-background">
											<img class="img-fluid" width="48" height="48" src="https://www.microsoft.com/content/dam/microsoft/final/en-us/microsoft-brand/icons/Icon-Microsft-Cloud-for-Healthcare-24x24.svg" alt="cloud-healthcare"/>
										</div>
										<h6 class="solution-title"><strong>Microsoft Cloud for healthcare</strong></h6>
										<p class="solution-description">Cloud for healthcare brings together secure, scalable cloud services to empower healthcare organizations with data-driven insights.</p>
									</div>
								</div>
								<div class="col-md-4">
									<div class="card modern-solution-card p-4 mt-4">
										<div class="solution-icon-wrapper default-background">
											<img class="img-fluid" width="48" height="48" src="https://img.icons8.com/fluency/48/microsoft-power-automate-2020.png" alt="azure-ai"/>
										</div>
										<h6 class="solution-title"><strong>Azure AI in healthcare</strong></h6>
										<p class="solution-description">Empowers organizations with intelligent capabilities, enabling tasks like analyzing medical images for faster diagnoses, predicting patient outcomes and extracting insights from unstructured data to improve research and development.</p>
									</div>
								</div>
								<div class="col-md-4">
									<div class="card modern-solution-card p-4 mt-4">
										<div class="solution-icon-wrapper default-background">
											<img class="img-fluid" width="48" height="48" src="https://www.bridgeheadsoftware.com/wp-content/uploads/2023/12/FHIR-Logo.png" alt="fhir"/>
										</div>
										<h6 class="solution-title"><strong>FHIR Integration</strong></h6>
										<p class="solution-description">Unlocks seamless data exchange within healthcare ecosystems, enabling secure sharing of patient information across different applications and platforms for improved care coordination and decision-making.</p>
									</div>
								</div>
								<div class="col-md-4">
									<div class="card modern-solution-card p-4 mt-4">
										<div class="solution-icon-wrapper default-background">
											<img class="img-fluid" width="48" height="48" src="https://www.microsoft.com/content/dam/microsoft/final/en-us/microsoft-brand/icons/Icon-Azure-AI-24x24-1.svg" alt="tableau"/>
										</div>
										<h6 class="solution-title"><strong>Tableau in healthcare</strong></h6>
										<p class="solution-description">Transform complex healthcare data into clear, insightful visualizations allowing organization to identify trends, patterns leading to better patient care, optimized resource allocation, and informed decision-making.</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="energy" role="tabpanel" aria-labelledby="energy-tab-nav">
						<div class="tab-content-wrapper">
							<div style="min-height: 65vh; height: auto;">
								<div class="mt-4">
									<div class="row align-items-center industry-intro-section">
										<div class="col-md-6">
											<div class="industry-image-wrapper">
												<img class="img-fluid industry-img" src="{{ asset('images/industry/oil.png') }}" alt="Oil & Gas Industry">
											</div>
										</div>
										<div class="col-md-6">
											<div class="industry-intro-content">
												<span class="industry-tag">ENERGY SOLUTIONS</span>
												<h2 class="industry-title mt-2">Oil & Gas</h2>
												<div class="title-underline"></div>
												<p>Data in Oil and Gas industry fuels the revolution of how the industry operates. With forward thinking solutions, players explore the transformative opportunities presented by today’s technology.</p>
												<p>Armely as a partner has a track record in helping organizations in this industry realize the value of digital transformation. </p>
											</div>
										</div>
									</div>
								</div>
								<div class="row mt-5 g-4">
									<div class="col-md-3">
										<div class="card modern-solution-card p-4">
											<div class="solution-icon-wrapper default-background">
												<img class="img-fluid" width="48" height="48" src="https://www.microsoft.com/content/dam/microsoft/final/en-us/microsoft-brand/icons/Icon-Microsoft-fabric-24x24.svg" alt="microsoft-fabric"/>
											</div>
											<h6 class="solution-title"><strong>Microsoft Fabric in oil & gas</strong></h6>
											<p class="solution-description">Unlock powerful analytics in oil & gas data. As Microsoft Fabric Partners we empower organizations build data-driven decisions solutions </p>
										</div>
									</div>
									<div class="col-md-3">
										<div class="card modern-solution-card p-4">
											<div class="solution-icon-wrapper default-background">
												<img class="img-fluid" width="48" height="48" src="https://www.microsoft.com/content/dam/microsoft/final/en-us/microsoft-brand/icons/Icon-Power-Platform-24x24.svg" alt="power-platform"/>
											</div>
											<h6 class="solution-title"><strong>Power Platform in oil & gas</strong></h6>
											<p class="solution-description">Streamline workflows and automates tasks, building low-code/no-code solutions that empower organizations to transform data into actionable insights</p>
										</div>
									</div>
									<div class="col-md-3">
										<div class="card modern-solution-card p-4">
											<div class="solution-icon-wrapper default-background">
												<img class="img-fluid" width="48" height="48" src="https://img.icons8.com/fluency/48/azure-1.png" alt="azure-ai"/>
											</div>
											<h6 class="solution-title"><strong>Azure AI in oil & gas</strong></h6>
											<p class="solution-description">Empowers organizations with intelligent capabilities, enabling tasks like analyzing medical images for faster diagnoses, predicting patient outcomes and extracting insights from unstructured data to improve research and development.</p>
										</div>
									</div>
									<div class="col-md-3">
										<div class="card modern-solution-card p-4">
											<div class="solution-icon-wrapper default-background">
												<img class="img-fluid" width="48" height="48" src="https://img.icons8.com/bubbles/50/azure-api-manager.png" alt="api-integration"/>
											</div>
											<h6 class="solution-title"><strong>API Integration</strong></h6>
											<p class="solution-description">Transform complex oil & gas data into clear, insightful visualizations allowing organization to identify trends by integrating with external data</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="government" role="tabpanel" aria-labelledby="government-tab">
						<div class="tab-content-wrapper">
							<div class="mt-5">
								<div class="row align-items-center industry-intro-section">
									<div class="col-md-6">
										<div class="industry-intro-content">
											<span class="industry-tag">GOVERNMENT SOLUTIONS</span>
											<h2 class="industry-title">Local Government</h2>
											<div class="title-underline"></div>
											<p class="industry-description">In today's data-driven world, local governments are undergoing a revolution. Data is no longer just numbers; it's the fuel that propels smarter decision-making, improved service delivery, and a more responsive government.</p>
											<p class="industry-description">Armely, a trusted partner with a proven track record, empowers local governments to unlock the transformative potential of data through forward-thinking solutions.</p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="industry-image-wrapper">
											<img class="img-fluid industry-img" src="{{ asset('images/industry/government.png') }}" alt="Local Government">
										</div>
									</div>
								</div>
								<div class="mt-5">
									<h5 class="section-subtitle">Here's how Armely makes a difference:</h5>
									<div class="subtitle-underline"></div>
									<div class="row mt-4 g-4">
										<div class="col-md-6">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="icofont-chart-line"></i>
												</div>
												<h6 class="benefit-title">Harnessing Data Insights</h6>
												<p class="benefit-description">We help you collect, analyze, and utilize data to gain valuable insights into your community's needs.</p>
											</div>
										</div>
										<div class="col-md-6">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="icofont-speed-meter"></i>
												</div>
												<h6 class="benefit-title">Streamlined Operations</h6>
												<p class="benefit-description">Our solutions optimize processes, improve efficiency, and free up valuable resources for what matters most – serving your citizens.</p>
											</div>
										</div>
										<div class="col-md-6">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="icofont-chart-growth"></i>
												</div>
												<h6 class="benefit-title">Data-Driven Decisions</h6>
												<p class="benefit-description">Empower your leaders with real-time information to make informed decisions that truly benefit your community.</p>
											</div>
										</div>
										<div class="col-md-6">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="icofont-users-alt-5"></i>
												</div>
												<h6 class="benefit-title">Enhanced Citizen Engagement</h6>
												<p class="benefit-description">We help foster transparency and build trust through open data initiatives and citizen-friendly applications.</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="legal" role="tabpanel" aria-labelledby="legal-tab">
						<div class="tab-content-wrapper">
							<div class="mt-5">
								<div class="row align-items-center industry-intro-section ">
									<div class="col-md-6">
										<div class="industry-intro-content">
											<span class="industry-tag">LEGAL SOLUTIONS</span>
											<h2 class="industry-title">Legal</h2>
											<div class="title-underline"></div>
											<p class="industry-description">The legal industry is undergoing a seismic shift, driven by the transformative power of data. Data has been revolutionizing how legal services are delivered. At Armely, we're at the forefront, partnering with forward-thinking legal organizations to unlock the immense potential of this data revolution.</p>
											<p class="industry-description">Armely, a trusted partner with a proven track record, empowers local governments to unlock the transformative potential of data through forward-thinking solutions.</p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="industry-image-wrapper">
											<img class="img-fluid industry-img" src="{{ asset('images/industry/legal.png') }}" alt="Legal Industry">
										</div>
									</div>
								</div>
								<div class="mt-5">
									<h5 class="section-subtitle">Harnessing the Power of Information</h5>
									<div class="subtitle-underline"></div>
									<p class="section-text">We understand the challenges of navigating the vast amount of data that permeates every aspect of the legal industry. From case law and contracts to due diligence and client communication, data holds the key to unlocking new levels of efficiency, accuracy, and insight.</p>
									<h6 class="subsection-title">Armely: Your Trusted Partner in Legal Data Transformation</h6>
									<p class="section-text">Our proven track record speaks for itself. We partner with legal organizations to:</p>
									<div class="row mt-4 g-4">
										<div class="col-md-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="icofont-database"></i>
												</div>
												<h6 class="benefit-title">Extract value from data</h6>
												<p class="benefit-description">We help implement solutions to collect, organize, and analyze legal data, turning it into actionable insights.</p>
											</div>
										</div>
										<div class="col-md-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="icofont-robot"></i>
												</div>
												<h6 class="benefit-title">Automate routine tasks</h6>
												<p class="benefit-description">By leveraging data and automation, we free up valuable time for legal professionals to focus on high-value strategy and client service.</p>
											</div>
										</div>
										<div class="col-md-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="icofont-trophy-alt"></i>
												</div>
												<h6 class="benefit-title">Gain a competitive edge</h6>
												<p class="benefit-description">Data-driven insights empower lawyers to build stronger cases, deliver more efficient services, and gain a competitive advantage in the market.</p>
											</div>
										</div>
										<div class="mt-4">
											<a href="case study" class="btn default-background text-light btn-modern-cta">
												See our Case Studies for Legal
											</a>
										</div>
										<p class="section-text mt-4">Get started today, work with Armely to extend and develop solutions that will transform your business today. Using our knowledge and expertise in the industry, we will partner in transform your business</p>
									</div>
								</div>
							</div>
						</div>
					</div>					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="features-section-industries">
	<div class="container">
		<div class="row justify-content-center text-center">
			<div class="col-12">
				<h3 class="features-section-title">Why Partner With Armely?</h3>
				<p class="features-section-subtitle">Delivering industry-leading solutions across all sectors</p>
			</div>
		</div>
		<div class="row justify-content-center align-items-center mt-5 g-4">
			<div class="col-md-3 col-6">
				<div class="feature-item">
					<div class="feature-icon-wrapper-large">
						<div class="feature-gradient-icon">
							<img class="img-fluid" width="60" height="60" src="{{ asset('images/industry/svg1.svg') }}" alt="Connected">
						</div>
					</div>
					<h5 class="feature-title mt-3">Connected</h5>
					<p class="feature-description">Seamlessly integrate systems and data across your organization</p>
				</div>
			</div>
			<div class="col-md-3 col-6">
				<div class="feature-item">
					<div class="feature-icon-wrapper-large">
						<div class="feature-gradient-icon">
							<img class="img-fluid" width="60" height="60" src="{{ asset('images/industry/svg2.svg') }}" alt="Secure">
						</div>
					</div>
					<h5 class="feature-title mt-3">Secure</h5>
					<p class="feature-description">Enterprise-grade security with compliance and data protection</p>
				</div>
			</div>
			<div class="col-md-3 col-6">
				<div class="feature-item">
					<div class="feature-icon-wrapper-large">
						<div class="feature-gradient-icon">
							<img class="img-fluid" width="60" height="60" src="{{ asset('images/industry/svg3.svg') }}" alt="Productive">
						</div>
					</div>
					<h5 class="feature-title mt-3">Productive</h5>
					<p class="feature-description">Streamline workflows and boost team efficiency instantly</p>
				</div>
			</div>
			<div class="col-md-3 col-6">
				<div class="feature-item">
					<div class="feature-icon-wrapper-large">
						<div class="feature-gradient-icon">
							<img class="img-fluid" width="60" height="60" src="{{ asset('images/industry/svg4.svg') }}" alt="Insights">
						</div>
					</div>
					<h5 class="feature-title mt-3">Insights</h5>
					<p class="feature-description">Data-driven intelligence to power strategic decisions</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="cta-section-industries default-background">
	<div class="container">
		<div class="row align-items-center justify-content-center text-center">
			<div class="col-lg-8 col-md-10 col-sm-12">
				<h3 class="cta-heading">Ready to Transform Your Business?</h3>
				<p class="cta-description text-light">Get started today, work with Armely to extend and develop solutions that will transform your business. Using our knowledge and expertise in the industry, we will partner to transform your business.</p>
				<a href="{{ route('contact') }}" class="btn btn-modern-cta-white">
					<i class="fa fa-phone"></i> Contact Us Today
				</a>
			</div>
		</div>
	</div>
</section>
@endsection
