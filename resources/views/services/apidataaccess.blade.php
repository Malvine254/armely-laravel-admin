<style>
	.api-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.api-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.api-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.api-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.api-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.api-section {
		padding: 60px 0;
	}
	
	.api-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.api-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.capability-card {
		background: white;
		border-radius: 12px;
		padding: 30px;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
	}
	
	.capability-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
		border-top-color: #2f5597;
	}
	
	.capability-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}
	
	.capability-card p {
		color: #666;
		line-height: 1.7;
	}
	
	.api-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.type-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.type-card {
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		padding: 25px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	
	.type-icon {
		width: 60px;
		height: 60px;
		background: rgba(255,255,255,0.2);
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 1.8rem;
		margin: 0 auto 15px;
	}
	
	.type-card h5 {
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.type-card p {
		font-size: 0.95rem;
		line-height: 1.6;
		color: rgba(255,255,255,0.95);
	}
	
	.integration-section {
		background: #f8f9fa;
		padding: 60px 0;
	}
	
	.integration-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.integration-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.integration-item {
		background: white;
		padding: 25px;
		border-radius: 12px;
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
		text-align: center;
	}
	
	.integration-item i {
		font-size: 2rem;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.integration-item h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.integration-item p {
		color: #666;
		font-size: 0.9rem;
		line-height: 1.6;
	}
	
	.cta-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 60px 0;
		text-align: center;
	}
	
	.cta-section h2 {
		color: white;
		font-size: 2.2rem;
		margin-bottom: 20px;
	}
	
	.cta-section p {
		font-size: 1.1rem;
		margin-bottom: 30px;
		max-width: 600px;
		margin-left: auto;
		margin-right: auto;
	}
	
	.cta-buttons {
		display: flex;
		gap: 20px;
		justify-content: center;
		flex-wrap: wrap;
	}
	
	.btn-primary {
		padding: 14px 35px;
		background: white;
		color: #2f5597;
		text-decoration: none;
		border-radius: 50px;
		font-weight: 600;
		transition: all 0.3s ease;
		border: none;
		cursor: pointer;
	}
	
	.btn-primary:hover {
		transform: scale(1.05);
		box-shadow: 0 10px 30px rgba(0,0,0,0.2);
	}
	
	.btn-secondary {
		padding: 14px 35px;
		background: transparent;
		color: white;
		border: 2px solid white;
		text-decoration: none;
		border-radius: 50px;
		font-weight: 600;
		transition: all 0.3s ease;
		cursor: pointer;
	}
	
	.btn-secondary:hover {
		background: white;
		color: #2f5597;
	}
	
	@media (max-width: 768px) {
		.api-hero h1 {
			font-size: 2rem;
		}
		
		.api-section h2 {
			font-size: 1.8rem;
		}
		
		.api-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- API Data Access Hero Section -->
<section class="api-hero">
	<div class="container">
		<div class="api-hero-content">
			<h1 class="text-light">API Data Access</h1>
			<p>Seamless Integration & Data Connectivity</p>
			<p>Enable real-time data access and seamless integration with APIs that connect disparate systems, applications, and external services.</p>
		</div>
	</div>
</section>

<section class="api-section">
	<div class="container">
		<h2>API Data Access Services</h2>
		
		<div class="api-grid">
			<div class="capability-card">
				<h4><i class="fas fa-plug"></i> API Design & Development</h4>
				<p>Design and build custom APIs tailored to your integration needs and architecture.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-sync"></i> API Integration</h4>
				<p>Integrate with third-party APIs and internal systems for seamless data flow.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-cloud"></i> Cloud API Solutions</h4>
				<p>Leverage cloud-based APIs for scalable and reliable data access.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-lock"></i> Security & Authentication</h4>
				<p>Implement OAuth, JWT, and other modern authentication and authorization mechanisms.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-heartbeat"></i> API Monitoring & Health</h4>
				<p>Continuous monitoring, logging, and performance optimization for APIs.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-graduation-cap"></i> API Documentation & Training</h4>
				<p>Comprehensive documentation and developer training for API adoption.</p>
			</div>
		</div>
	</div>
</section>

<section class="api-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>API Types & Approaches</h2>
		
		<div class="type-grid">
			<div class="type-card">
				<div class="type-icon">
					<i class="fas fa-project-diagram"></i>
				</div>
				<h5 class="text-light">REST APIs</h5>
				<p>RESTful web services for simple, scalable data access and integration.</p>
			</div>
			
			<div class="type-card">
				<div class="type-icon">
					<i class="fas fa-database"></i>
				</div>
				<h5 class="text-light">GraphQL APIs</h5>
				<p>Flexible query language for precise data retrieval and efficient APIs.</p>
			</div>
			
			<div class="type-card">
				<div class="type-icon">
					<i class="fas fa-exchange-alt"></i>
				</div>
				<h5 class="text-light">Webhook Integration</h5>
				<p>Event-driven integrations for real-time data notifications and updates.</p>
			</div>
			
			<div class="type-card">
				<div class="type-icon">
					<i class="fas fa-stream"></i>
				</div>
				<h5 class="text-light">Streaming APIs</h5>
				<p>Real-time data streaming for continuous data flow and analytics.</p>
			</div>
		</div>
	</div>
</section>

<section class="integration-section">
	<div class="container">
		<h2>Common Integrations</h2>
		
		<div class="integration-grid">
			<div class="integration-item">
				<i class="fas fa-cloud"></i>
				<h5>Cloud Platforms</h5>
				<p>AWS, Azure, Google Cloud integration and data access</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-database"></i>
				<h5>Databases</h5>
				<p>SQL Server, Oracle, PostgreSQL, MongoDB connectivity</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-cube"></i>
				<h5>Enterprise Systems</h5>
				<p>Salesforce, Dynamics 365, SAP integration</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-shopping-cart"></i>
				<h5>E-Commerce Platforms</h5>
				<p>Shopify, WooCommerce, magento APIs</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-heartbeat"></i>
				<h5>Healthcare Systems</h5>
				<p>FHIR, HL7, and other healthcare data standards</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-mobile-alt"></i>
				<h5>Mobile Backends</h5>
				<p>Firebase, custom mobile API solutions</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2>Ready to Integrate Your Systems?</h2>
		<p class="text-light">Let our API experts design and implement solutions for seamless data access and integration.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request API Data Access Solution</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

