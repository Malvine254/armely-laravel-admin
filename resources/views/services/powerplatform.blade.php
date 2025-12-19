<style>
	.platform-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.platform-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.platform-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.platform-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.platform-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.platform-section {
		padding: 60px 0;
	}
	
	.platform-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.platform-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.page-card {
		background: white;
		border-radius: 12px;
		padding: 30px;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
	}
	
	.page-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
		border-top-color: #2f5597;
	}
	
	.page-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}
	
	.page-card p {
		color: #666;
		line-height: 1.7;
	}
	
	.platform-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.feature-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 60px 0;
		margin-top: 60px;
	}
	
	.feature-section h2 {
		color: white;
		text-align: center;
		margin-bottom: 50px;
		font-size: 2.2rem;
	}
	
	.feature-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
	}
	
	.feature-item {
		background: rgba(255,255,255,0.1);
		padding: 25px;
		border-radius: 12px;
		border: 1px solid rgba(255,255,255,0.2);
	}
	
	.feature-item h5 {
		font-weight: 700;
		margin-bottom: 10px;
		font-size: 1.1rem;
	}
	
	.feature-item p {
		font-size: 0.95rem;
		color: rgba(255,255,255,0.9);
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
		.platform-hero h1 {
			font-size: 2rem;
		}
		
		.platform-section h2 {
			font-size: 1.8rem;
		}
		
		.platform-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- Power Pages Hero Section -->
<section class="platform-hero">
	<div class="container">
		<div class="platform-hero-content">
			<h1 class="text-light">Power Pages</h1>
			<p>Create Professional Websites Without Limits</p>
			<p>Build and launch stunning, responsive websites that showcase your brand and engage customers—no coding required.</p>
		</div>
	</div>
</section>

<section class="platform-section">
	<div class="container">
		<h2>Power Pages Website Solutions</h2>
		
		<div class="platform-grid">
			<div class="page-card">
				<h4><i class="fas fa-globe"></i> Corporate Websites</h4>
				<p>Professional corporate sites that showcase your company, values, and offerings with modern design and mobile responsiveness.</p>
			</div>
			
			<div class="page-card">
				<h4><i class="fas fa-store"></i> Business Portals</h4>
				<p>Create customer-facing portals for support, knowledge bases, and service interactions.</p>
			</div>
			
			<div class="page-card">
				<h4><i class="fas fa-shopping-cart"></i> E-Commerce Sites</h4>
				<p>Build online stores with product catalogs, shopping carts, and integrated payment processing.</p>
			</div>
			
			<div class="page-card">
				<h4><i class="fas fa-handshake"></i> Partner Portals</h4>
				<p>Enable partners and customers with self-service portals for collaboration and support.</p>
			</div>
			
			<div class="page-card">
				<h4><i class="fas fa-graduation-cap"></i> Learning Platforms</h4>
				<p>Create educational websites with course content, tracking, and certification capabilities.</p>
			</div>
			
			<div class="page-card">
				<h4><i class="fas fa-event"></i> Event Websites</h4>
				<p>Launch event sites with registration, scheduling, and attendee management features.</p>
			</div>
		</div>
	</div>
</section>

<section class="feature-section">
	<div class="container">
		<h2>Key Features</h2>
		
		<div class="feature-grid">
			<div class="feature-item">
				<h5 class="text-light"><i class="fas fa-palette"></i> Customizable Templates</h5>
				<p>Choose from industry-specific templates designed for different business needs and use cases.</p>
			</div>
			
			<div class="feature-item">
				<h5 class="text-light"><i class="fas fa-mobile-alt"></i> Responsive Design</h5>
				<p>Automatically optimize your website for all devices—desktop, tablet, and mobile.</p>
			</div>
			
			<div class="feature-item">
				<h5 class="text-light"><i class="fas fa-lock"></i> Secure & Compliant</h5>
				<p>Built-in security and compliance features to protect your data and meet regulations.</p>
			</div>
			
			<div class="feature-item">
				<h5 class="text-light"><i class="fas fa-code"></i> Low-Code Development</h5>
				<p>Add advanced features with minimal coding using Power Pages customization tools.</p>
			</div>
			
			<div class="feature-item">
				<h5 class="text-light" ><i class="fas fa-chart-line"></i> Analytics & Insights</h5>
				<p>Get real-time insights into visitor behavior and website performance metrics.</p>
			</div>
			
			<div class="feature-item">
				<h5 class="text-light"><i class="fas fa-plug"></i> Easy Integration</h5>
				<p>Connect with Microsoft 365, Dynamics 365, and other business systems seamlessly.</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section mt-4">
	<div class="container">
		<h2>Ready to Launch Your Website?</h2>
		<p class="text-light">Let our experts help you create a professional Power Pages website that drives business results.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Design Your Website</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

