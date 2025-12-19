<style>
	/* Hero Section */
	.data-strategy-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		margin-bottom: 40px;
		position: relative;
		overflow: hidden;
	}
	
	.data-strategy-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.data-strategy-hero-content {
		position: relative;
		z-index: 1;
	}
	
	.data-strategy-hero h2 {
		font-size: 2.5rem;
		font-weight: bold;
		color: #ffffff;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.data-strategy-hero p {
		font-size: 1.1rem;
		color: rgba(255,255,255,0.95);
		line-height: 1.8;
		margin: 15px 0;
	}
	
	/* Section Headers */
	.section-header {
		text-align: center;
		margin-bottom: 50px;
	}
	
	.section-header h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.section-header::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	/* Pillars Section */
	.pillars-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.pillar-card {
		background: white;
		border-radius: 12px;
		padding: 35px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		position: relative;
		overflow: hidden;
	}
	
	.pillar-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		transform: scaleX(0);
		transform-origin: left;
		transition: transform 0.3s ease;
	}
	
	.pillar-card:hover::before {
		transform: scaleX(1);
	}
	
	.pillar-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
	}
	
	.pillar-icon {
		font-size: 3rem;
		color: #2f5597;
		margin-bottom: 20px;
		transition: all 0.3s ease;
		display: inline-block;
	}
	
	.pillar-card:hover .pillar-icon {
		transform: scale(1.15) rotate(-5deg);
		filter: drop-shadow(0 5px 15px rgba(47, 85, 151, 0.3));
	}
	
	.pillar-title {
		font-size: 1.3rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.pillar-desc {
		color: #666;
		font-size: 0.95rem;
		line-height: 1.6;
		margin: 0;
	}
	
	/* Key Components */
	.components-section {
		padding: 60px 0;
		background: #f8f9fa;
	}
	
	.components-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.component-badge {
		background: white;
		padding: 30px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 3px 15px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
		border-top: 3px solid #2f5597;
	}
	
	.component-badge:hover {
		transform: translateY(-8px);
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
		border-top-color: #1e3a6d;
	}
	
	.component-icon {
		font-size: 2.5rem;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.component-title {
		font-size: 1.1rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.component-text {
		color: #666;
		font-size: 0.9rem;
		line-height: 1.5;
	}
	
	/* Best Practices */
	.practices-section {
		padding: 60px 0;
		background: white;
	}
	
	.practices-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.practice-item {
		background: linear-gradient(135deg, rgba(47, 85, 151, 0.05) 0%, rgba(30, 58, 109, 0.05) 100%);
		padding: 30px;
		border-radius: 12px;
		border-left: 4px solid #2f5597;
		transition: all 0.3s ease;
	}
	
	.practice-item:hover {
		background: linear-gradient(135deg, rgba(47, 85, 151, 0.1) 0%, rgba(30, 58, 109, 0.1) 100%);
		transform: translateX(10px);
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
	}
	
	.practice-title {
		font-size: 1.15rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 12px;
	}
	
	.practice-desc {
		color: #555;
		font-size: 0.9rem;
		line-height: 1.6;
	}
	
	/* Benefits Section */
	.benefits-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		position: relative;
		overflow: hidden;
	}
	
	.benefits-section::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.benefits-content {
		position: relative;
		z-index: 1;
	}
	
	.benefits-list {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
		gap: 20px;
		margin-top: 40px;
	}
	
	.benefit-item {
		background: rgba(255,255,255,0.95);
		padding: 25px;
		border-radius: 10px;
		text-align: center;
		position: relative;
		transition: all 0.3s ease;
	}
	
	.benefit-item:hover {
		background: white;
		transform: translateY(-5px);
		box-shadow: 0 10px 30px rgba(0,0,0,0.15);
	}
	
	.benefit-icon {
		width: 50px;
		height: 50px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		border-radius: 50%;
		color: white;
		font-size: 1.5rem;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 15px;
	}
	
	.benefit-title {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.benefit-desc {
		color: #555;
		font-size: 0.9rem;
		line-height: 1.5;
	}
	
	/* CTA Section */
	.data-strategy-cta-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		text-align: center;
		margin-top: 60px;
		border-radius: 12px;
		position: relative;
		overflow: hidden;
	}
	
	.data-strategy-cta-section::before {
		content: '';
		position: absolute;
		top: -50%;
		right: -50%;
		width: 500px;
		height: 500px;
		background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
		border-radius: 50%;
	}
	
	.cta-content {
		position: relative;
		z-index: 1;
	}
	
	.data-strategy-cta-section h2 {
		color: white;
		font-size: 2rem;
		font-weight: 700;
		margin-bottom: 20px;
	}
	
	.data-strategy-cta-section p {
		color: rgba(255,255,255,0.95);
		font-size: 1.1rem;
		margin-bottom: 30px;
	}
	
	.cta-button-group {
		display: flex;
		gap: 15px;
		justify-content: center;
		flex-wrap: wrap;
	}
	
	.cta-button {
		display: inline-block;
		padding: 14px 35px;
		border-radius: 50px;
		font-weight: 600;
		font-size: 1rem;
		text-decoration: none;
		transition: all 0.3s ease;
		cursor: pointer;
		border: none;
	}
	
	.cta-button-primary {
		background: white;
		color: #2f5597;
	}
	
	.cta-button-primary:hover {
		transform: translateY(-3px);
		box-shadow: 0 10px 30px rgba(0,0,0,0.2);
	}
	
	.cta-button-secondary {
		background: transparent;
		color: white;
		border: 2px solid white;
	}
	
	.cta-button-secondary:hover {
		background: white;
		color: #2f5597;
	}
	
	@media (max-width: 768px) {
		.data-strategy-hero h2 {
			font-size: 1.8rem;
		}
		
		.section-header h2 {
			font-size: 1.6rem;
		}
		
		.data-strategy-cta-section h2 {
			font-size: 1.5rem;
		}
		
		.cta-button-group {
			flex-direction: column;
		}
		
		.cta-button {
			width: 100%;
		}
	}
</style>

<!-- Hero Section -->
<section class="data-strategy-hero">
	<div class="container">
		<div class="data-strategy-hero-content">
			<h2>Data Strategy Consulting</h2>
			<p>Transform your organization into a data-driven enterprise with a strategic roadmap that aligns technology, people, and processes.</p>
			<p>We help you harness data as a competitive asset, creating sustainable advantages through thoughtful strategy and execution.</p>
		</div>
	</div>
</section>

<!-- Pillars Section -->
<section style="padding: 60px 0; background: white;">
	<div class="container">
		<div class="section-header">
			<h2>Data Strategy Pillars</h2>
		</div>
		
		<div class="pillars-grid">
			<!-- Pillar 1 -->
			<div class="pillar-card">
				<div class="pillar-icon">
					<i class="fa-solid fa-map"></i>
				</div>
				<h4 class="pillar-title">Strategic Planning</h4>
				<p class="pillar-desc">Develop a comprehensive roadmap aligned with business objectives, identifying high-impact data initiatives and sequencing implementation priorities.</p>
			</div>
			
			<!-- Pillar 2 -->
			<div class="pillar-card">
				<div class="pillar-icon">
					<i class="fa-solid fa-layer-group"></i>
				</div>
				<h4 class="pillar-title">Data Architecture</h4>
				<p class="pillar-desc">Design scalable, flexible data infrastructure that supports current and future analytics, AI, and reporting requirements.</p>
			</div>
			
			<!-- Pillar 3 -->
			<div class="pillar-card">
				<div class="pillar-icon">
					<i class="fa-solid fa-users"></i>
				</div>
				<h4 class="pillar-title">Data Governance</h4>
				<p class="pillar-desc">Establish frameworks for data quality, security, privacy, and compliance across the organization.</p>
			</div>
			
			<!-- Pillar 4 -->
			<div class="pillar-card">
				<div class="pillar-icon">
					<i class="fa-solid fa-graduation-cap"></i>
				</div>
				<h4 class="pillar-title">Organizational Design</h4>
				<p class="pillar-desc">Build data-driven culture with proper team structures, skills development, and change management.</p>
			</div>
			
			<!-- Pillar 5 -->
			<div class="pillar-card">
				<div class="pillar-icon">
					<i class="fa-solid fa-chart-line"></i>
				</div>
				<h4 class="pillar-title">Business Intelligence</h4>
				<p class="pillar-desc">Create analytics capabilities and reporting frameworks that drive decision-making at all levels.</p>
			</div>
			
			<!-- Pillar 6 -->
			<div class="pillar-card">
				<div class="pillar-icon">
					<i class="fa-solid fa-handshake"></i>
				</div>
				<h4 class="pillar-title">Stakeholder Management</h4>
				<p class="pillar-desc">Align executives, ensure adoption, and manage change across the organization.</p>
			</div>
		</div>
	</div>
</section>

<!-- Key Components -->
<section class="components-section">
	<div class="container">
		<div class="section-header">
			<h2>Key Components of Our Strategy</h2>
		</div>
		
		<div class="components-grid">
			<div class="component-badge">
				<div class="component-icon">
					<i class="fa-solid fa-magnifying-glass"></i>
				</div>
				<h4 class="component-title">Assessment</h4>
				<p class="component-text">Evaluate current state, identify gaps, and benchmark against industry best practices.</p>
			</div>
			
			<div class="component-badge">
				<div class="component-icon">
					<i class="fa-solid fa-lightbulb"></i>
				</div>
				<h4 class="component-title">Vision Setting</h4>
				<p class="component-text">Define future state aspirations and data maturity targets for the organization.</p>
			</div>
			
			<div class="component-badge">
				<div class="component-icon">
					<i class="fa-solid fa-road"></i>
				</div>
				<h4 class="component-title">Roadmapping</h4>
				<p class="component-text">Create detailed implementation timeline with quick wins and long-term initiatives.</p>
			</div>
			
			<div class="component-badge">
				<div class="component-icon">
					<i class="fa-solid fa-cogs"></i>
				</div>
				<h4 class="component-title">Governance Setup</h4>
				<p class="component-text">Establish decision-making processes, policies, and controls for data management.</p>
			</div>
			
			<div class="component-badge">
				<div class="component-icon">
					<i class="fa-solid fa-chart-bar"></i>
				</div>
				<h4 class="component-title">Metrics & Tracking</h4>
				<p class="component-text">Define KPIs and monitoring mechanisms to measure strategy execution and ROI.</p>
			</div>
			
			<div class="component-badge">
				<div class="component-icon">
					<i class="fa-solid fa-people-group"></i>
				</div>
				<h4 class="component-title">Team Development</h4>
				<p class="component-text">Build capabilities through training, hiring, and organizational restructuring.</p>
			</div>
		</div>
	</div>
</section>

<!-- Best Practices -->
<section class="practices-section">
	<div class="container">
		<div class="section-header">
			<h2>Data Strategy Best Practices</h2>
		</div>
		
		<div class="practices-grid">
			<div class="practice-item">
				<h4 class="practice-title">Business-Driven Focus</h4>
				<p class="practice-desc">Start with business problems and opportunities, not technology solutions.</p>
			</div>
			
			<div class="practice-item">
				<h4 class="practice-title">Executive Alignment</h4>
				<p class="practice-desc">Ensure leadership commitment and accountability for strategy execution.</p>
			</div>
			
			<div class="practice-item">
				<h4 class="practice-title">Phased Approach</h4>
				<p class="practice-desc">Deliver quick wins early to build momentum and demonstrate value.</p>
			</div>
			
			<div class="practice-item">
				<h4 class="practice-title">Data Quality First</h4>
				<p class="practice-desc">Prioritize data quality and governance as foundation for analytics.</p>
			</div>
			
			<div class="practice-item">
				<h4 class="practice-title">Agile Execution</h4>
				<p class="practice-desc">Use iterative approaches to adapt strategy as business needs evolve.</p>
			</div>
			
			<div class="practice-item">
				<h4 class="practice-title">Continuous Improvement</h4>
				<p class="practice-desc">Monitor results, gather feedback, and optimize strategy regularly.</p>
			</div>
		</div>
	</div>
</section>

<!-- Benefits Section -->
<section class="benefits-section">
	<div class="container">
		<div class="benefits-content">
			<div class="section-header" style="color: white; margin-bottom: 40px;">
				<h2 style="color: white;">Benefits of Data Strategy</h2>
			</div>
			
			<div class="benefits-list">
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa-solid fa-rocket"></i>
					</div>
					<h4 class="benefit-title">Faster Decision Making</h4>
					<p class="benefit-desc">Real-time insights enable quick, informed business decisions.</p>
				</div>
				
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa-solid fa-chart-line"></i>
					</div>
					<h4 class="benefit-title">Revenue Growth</h4>
					<p class="benefit-desc">Data-driven initiatives unlock new revenue streams and markets.</p>
				</div>
				
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa-solid fa-percent"></i>
					</div>
					<h4 class="benefit-title">Cost Reduction</h4>
					<p class="benefit-desc">Optimize operations and reduce waste through data insights.</p>
				</div>
				
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa-solid fa-shield"></i>
					</div>
					<h4 class="benefit-title">Risk Mitigation</h4>
					<p class="benefit-desc">Identify and proactively manage business risks with data.</p>
				</div>
				
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa-solid fa-users"></i>
					</div>
					<h4 class="benefit-title">Team Enablement</h4>
					<p class="benefit-desc">Empower employees with data and analytical skills.</p>
				</div>
				
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa-solid fa-trophy"></i>
					</div>
					<h4 class="benefit-title">Competitive Advantage</h4>
					<p class="benefit-desc">Differentiate through superior data capabilities and insights.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- CTA Section -->
<section class="data-strategy-cta-section">
	<div class="container">
		<div class="cta-content">
			<h2>Ready to Develop Your Data Strategy?</h2>
			<p>Let's work together to create a strategic roadmap that transforms your organization into a data-driven enterprise.</p>
			
			<div class="cta-button-group">
				<a href="{{ route('contact') }}" class="cta-button cta-button-primary">Schedule Strategy Workshop</a>
				<a href="{{ route('contact') }}" class="cta-button cta-button-secondary">Get Started Today</a>
			</div>
		</div>
	</div>
</section>

