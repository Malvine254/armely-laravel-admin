<style>
	.dynamics-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.dynamics-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.dynamics-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.dynamics-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.dynamics-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.dynamics-section {
		padding: 60px 0;
	}
	
	.dynamics-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.dynamics-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.solution-card {
		background: white;
		border-radius: 12px;
		padding: 30px;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
	}
	
	.solution-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
		border-top-color: #2f5597;
	}
	
	.solution-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}
	
	.solution-card p {
		color: #666;
		line-height: 1.7;
		margin-bottom: 15px;
	}
	
	.solution-card ul {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	
	.solution-card li {
		padding: 8px 0;
		color: #666;
		font-size: 0.9rem;
		display: flex;
		align-items: center;
		gap: 8px;
	}
	
	.solution-card li::before {
		content: '→';
		color: #2f5597;
		font-weight: bold;
	}
	
	.dynamics-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.capability-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.capability-card {
		background: white;
		padding: 25px;
		border-radius: 12px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		text-align: center;
	}
	
	.capability-icon {
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
	
	.capability-card h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.capability-card p {
		color: #666;
		font-size: 0.95rem;
		line-height: 1.6;
	}
	
	.implementation-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 60px 0;
		margin-top: 60px;
	}
	
	.implementation-section h2 {
		color: white;
		text-align: center;
		margin-bottom: 50px;
		font-size: 2.2rem;
	}
	
	.implementation-steps {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
	}
	
	.implementation-step {
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
	
	.implementation-step h5 {
		font-weight: 700;
		margin-bottom: 10px;
		font-size: 1.1rem;
	}
	
	.implementation-step p {
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
		.dynamics-hero h1 {
			font-size: 2rem;
		}
		
		.dynamics-section h2 {
			font-size: 1.8rem;
		}
		
		.dynamics-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- Dynamics 365 Hero Section -->
<section class="dynamics-hero">
	<div class="container">
		<div class="dynamics-hero-content">
			<h1 class="text-light">Microsoft Dynamics 365</h1>
			<p>Unified CRM & ERP Platform</p>
			<p>Transform your business with intelligent cloud applications that combine CRM and ERP capabilities to drive innovation and deliver exceptional customer experiences.</p>
		</div>
	</div>
</section>

<section class="dynamics-section">
	<div class="container">
		<h2>Dynamics 365 Solutions</h2>
		
		<div class="dynamics-grid">
			<div class="solution-card">
				<h4><i class="fas fa-users"></i> Sales Cloud</h4>
				<p>Empower your sales team with AI-powered insights to close deals faster and build stronger customer relationships.</p>
				<ul>
					<li>Lead & opportunity management</li>
					<li>Sales forecasting & analytics</li>
					<li>Activity tracking & scheduling</li>
					<li>Mobile sales capabilities</li>
				</ul>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-headset"></i> Customer Service</h4>
				<p>Deliver exceptional customer service with omnichannel support and AI-powered case management.</p>
				<ul>
					<li>Case & ticket management</li>
					<li>Omnichannel support</li>
					<li>Knowledge management</li>
					<li>Customer self-service</li>
				</ul>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-briefcase"></i> Finance Operations</h4>
				<p>Streamline financial processes with intelligent automation and real-time financial insights.</p>
				<ul>
					<li>General ledger management</li>
					<li>Accounts payable & receivable</li>
					<li>Financial reporting</li>
					<li>Expense management</li>
				</ul>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-industry"></i> Supply Chain</h4>
				<p>Optimize supply chain operations with visibility and intelligent forecasting capabilities.</p>
				<ul>
					<li>Demand planning</li>
					<li>Inventory management</li>
					<li>Supplier collaboration</li>
					<li>Logistics optimization</li>
				</ul>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-cube"></i> Project Operations</h4>
				<p>Manage complex projects with resource planning, time tracking, and billing capabilities.</p>
				<ul>
					<li>Project planning & execution</li>
					<li>Resource management</li>
					<li>Time & expense tracking</li>
					<li>Project billing</li>
				</ul>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-handshake"></i> Partner Relationship</h4>
				<p>Build and manage partner ecosystems with collaborative tools and performance management.</p>
				<ul>
					<li>Partner portal</li>
					<li>Co-selling capabilities</li>
					<li>Partner programs</li>
					<li>Performance tracking</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class="dynamics-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Key Capabilities</h2>
		
		<div class="capability-grid">
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-brain"></i>
				</div>
				<h5>AI & Machine Learning</h5>
				<p>Leverage AI-driven analytics and predictive insights for smarter decision-making.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-plug"></i>
				</div>
				<h5>Unified Platform</h5>
				<p>Seamlessly integrate CRM and ERP functionalities for a connected approach.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-chart-line"></i>
				</div>
				<h5>Real-Time Insights</h5>
				<p>Get actionable business intelligence with advanced analytics and dashboards.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-mobile-alt"></i>
				</div>
				<h5>Mobile First</h5>
				<p>Enable your teams with responsive mobile applications for on-the-go productivity.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-lock"></i>
				</div>
				<h5>Security & Compliance</h5>
				<p>Enterprise-grade security with compliance support for regulatory requirements.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-users"></i>
				</div>
				<h5>Collaboration Tools</h5>
				<p>Foster teamwork with integrated communication and collaboration features.</p>
			</div>
		</div>
	</div>
</section>

<section class="implementation-section">
	<div class="container">
		<h2>Our Implementation Approach</h2>
		
		<div class="implementation-steps">
			<div class="implementation-step">
				<div class="step-number">1</div>
				<h5 class="text-light">Discovery & Assessment</h5>
				<p>Analyze your business needs and current systems to design the right solution.</p>
			</div>
			
			<div class="implementation-step">
				<div class="step-number">2</div>
				<h5 class="text-light">Solution Design</h5>
				<p>Create a comprehensive solution design aligned with your business objectives.</p>
			</div>
			
			<div class="implementation-step">
				<div class="step-number">3</div>
				<h5 class="text-light">Configuration & Development</h5>
				<p>Configure and customize Dynamics 365 to your specific business processes.</p>
			</div>
			
			<div class="implementation-step">
				<div class="step-number">4</div>
				<h5 class="text-light">Testing & QA</h5>
				<p>Rigorous testing to ensure quality, performance, and security.</p>
			</div>
			
			<div class="implementation-step">
				<div class="step-number">5</div>
				<h5 class="text-light">Deployment & Training</h5>
				<p>Deploy your solution and train your team for successful adoption.</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2>Ready to Transform Your Business?</h2>
		<p class="text-light">Let our Dynamics 365 experts help you implement a solution that drives growth and innovation.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request A Dynamics 365 Solution</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

