<style>
	.powerapps-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.powerapps-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.powerapps-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.powerapps-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.powerapps-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.powerapps-section {
		padding: 60px 0;
	}
	
	.powerapps-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.powerapps-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.app-card {
		background: white;
		border-radius: 12px;
		padding: 30px;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
	}
	
	.app-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
		border-top-color: #2f5597;
	}
	
	.app-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}
	
	.app-card p {
		color: #666;
		line-height: 1.7;
		margin-bottom: 15px;
	}
	
	.app-card .feature-list {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	
	.app-card .feature-list li {
		padding: 8px 0;
		color: #666;
		font-size: 0.9rem;
		display: flex;
		align-items: center;
		gap: 8px;
	}
	
	.app-card .feature-list li::before {
		content: '→';
		color: #2f5597;
		font-weight: bold;
	}
	
	.powerapps-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.benefits-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.benefit-card {
		background: white;
		padding: 25px;
		border-radius: 12px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		text-align: center;
	}
	
	.benefit-icon {
		width: 60px;
		height: 60px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 1.8rem;
		margin: 0 auto 15px;
	}
	
	.benefit-card h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.benefit-card p {
		color: #666;
		font-size: 0.95rem;
		line-height: 1.6;
	}
	
	.process-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 60px 0;
		margin-top: 60px;
	}
	
	.process-section h2 {
		color: white;
		text-align: center;
		margin-bottom: 50px;
		font-size: 2.2rem;
	}
	
	.process-steps {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
	}
	
	.process-step {
		background: rgba(255,255,255,0.1);
		padding: 30px;
		border-radius: 12px;
		text-align: center;
		border: 1px solid rgba(255,255,255,0.2);
	}
	
	.step-number {
		width: 60px;
		height: 60px;
		background: white;
		color: #2f5597;
		font-size: 1.8rem;
		font-weight: 700;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 15px;
	}
	
	.process-step h5 {
		font-weight: 700;
		margin-bottom: 10px;
		font-size: 1.1rem;
	}
	
	.process-step p {
		font-size: 0.95rem;
		color: rgba(255,255,255,0.9);
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
		.powerapps-hero h1 {
			font-size: 2rem;
		}
		
		.powerapps-section h2 {
			font-size: 1.8rem;
		}
		
		.powerapps-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- Power Apps Hero Section -->
<section class="powerapps-hero">
	<div class="container">
		<div class="powerapps-hero-content">
			<h1 class="text-light">Microsoft Power Apps</h1>
			<p>Build Custom Applications Without Coding</p>
			<p>Empower your team with low-code/no-code solutions. Create powerful business applications in days, not months, with an intuitive drag-and-drop interface.</p>
		</div>
	</div>
</section>

<section class="powerapps-section">
	<div class="container">
		<h2>Power Apps Solutions</h2>
		
		<div class="powerapps-grid">
			<div class="app-card">
				<h4><i class="fas fa-clock"></i> Timesheet Applications</h4>
				<p>Automate employee time tracking and project billing with custom timesheet apps designed for your specific workflow.</p>
				<ul class="feature-list">
					<li>Mobile time entry</li>
					<li>Real-time approvals</li>
					<li>Project integration</li>
					<li>Export & reporting</li>
				</ul>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-boxes"></i> Inventory Management</h4>
				<p>Streamline inventory operations with intelligent tracking, forecasting, and automated reorder capabilities.</p>
				<ul class="feature-list">
					<li>Real-time tracking</li>
					<li>Automated alerts</li>
					<li>Barcode integration</li>
					<li>Multi-location support</li>
				</ul>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-file-contract"></i> Approval Workflows</h4>
				<p>Implement flexible approval workflows that route requests to the right people at the right time.</p>
				<ul class="feature-list">
					<li>Multi-level approvals</li>
					<li>Conditional routing</li>
					<li>Audit trails</li>
					<li>Escalation rules</li>
				</ul>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-users-cog"></i> Customer Management</h4>
				<p>Build comprehensive customer relationship solutions tailored to your business processes.</p>
				<ul class="feature-list">
					<li>Contact management</li>
					<li>Pipeline tracking</li>
					<li>Activity logging</li>
					<li>Reporting dashboards</li>
				</ul>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-chart-bar"></i> Business Analytics</h4>
				<p>Create custom analytics applications that provide real-time insights into your business metrics.</p>
				<ul class="feature-list">
					<li>Real-time dashboards</li>
					<li>Custom visualizations</li>
					<li>Data aggregation</li>
					<li>Predictive analytics</li>
				</ul>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-tasks"></i> Project Management</h4>
				<p>Centralize project management with task tracking, resource allocation, and team collaboration.</p>
				<ul class="feature-list">
					<li>Task management</li>
					<li>Resource planning</li>
					<li>Team collaboration</li>
					<li>Progress tracking</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class="powerapps-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Why Choose Our Power Apps Solutions?</h2>
		
		<div class="benefits-grid">
			<div class="benefit-card">
				<div class="benefit-icon">
					<i class="fas fa-bolt"></i>
				</div>
				<h5>Rapid Development</h5>
				<p>Deploy applications in days instead of months with our proven Power Apps expertise.</p>
			</div>
			
			<div class="benefit-card">
				<div class="benefit-icon">
					<i class="fas fa-lock"></i>
				</div>
				<h5>Enterprise Security</h5>
				<p>Built-in compliance, data encryption, and role-based access control for your peace of mind.</p>
			</div>
			
			<div class="benefit-card">
				<div class="benefit-icon">
					<i class="fas fa-mobile-alt"></i>
				</div>
				<h5>Mobile Ready</h5>
				<p>Responsive applications that work seamlessly across devices and platforms.</p>
			</div>
			
			<div class="benefit-card">
				<div class="benefit-icon">
					<i class="fas fa-plug"></i>
				</div>
				<h5>Seamless Integration</h5>
				<p>Connect with Microsoft 365, Dynamics 365, and your existing data sources.</p>
			</div>
			
			<div class="benefit-card">
				<div class="benefit-icon">
					<i class="fas fa-chart-line"></i>
				</div>
				<h5>Scalable Solutions</h5>
				<p>Grow your applications with your business, from startup to enterprise scale.</p>
			</div>
			
			<div class="benefit-card">
				<div class="benefit-icon">
					<i class="fas fa-headset"></i>
				</div>
				<h5>Expert Support</h5>
				<p>Ongoing support and optimization from our Power Apps certified professionals.</p>
			</div>
		</div>
	</div>
</section>

<section class="process-section">
	<div class="container">
		<h2>Our Power Apps Development Process</h2>
		
		<div class="process-steps">
			<div class="process-step">
				<div class="step-number">1</div>
				<h5 class="text-light">Discovery & Planning</h5>
				<p>Understand your requirements and design the perfect solution for your business.</p>
			</div>
			
			<div class="process-step">
				<div class="step-number">2</div>
				<h5 class="text-light">UI/UX Design</h5>
				<p>Create intuitive interfaces that your users will love to work with.</p>
			</div>
			
			<div class="process-step">
				<div class="step-number">3</div>
				<h5 class="text-light">Development</h5>
				<p>Build custom applications with our team of Power Apps experts.</p>
			</div>
			
			<div class="process-step">
				<div class="step-number">4</div>
				<h5 class="text-light">Testing & QA</h5>
				<p>Rigorous testing to ensure quality, performance, and security.</p>
			</div>
			
			<div class="process-step">
				<div class="step-number">5</div>
				<h5 class="text-light">Deployment & Training</h5>
				<p>Launch your app and train your team for successful adoption.</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2>Ready to Build Your Custom Application?</h2>
		<p class="text-light">Let our Power Apps experts transform your business processes into powerful, efficient applications.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request A Power Apps Solution</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

