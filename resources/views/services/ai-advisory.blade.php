<style>
	/* Hero Section */
	.advisory-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		margin-bottom: 40px;
		position: relative;
		overflow: hidden;
	}
	
	.advisory-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.advisory-hero-content {
		position: relative;
		z-index: 1;
	}
	
	.advisory-hero h2 {
		font-size: 2.5rem;
		font-weight: bold;
		color: #ffffff;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.advisory-hero p {
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
	
	/* Why Trust Us - Two Column */
	.trust-section {
		padding: 60px 0;
		background: white;
	}
	
	.trust-content {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 50px;
		align-items: center;
	}
	
	.trust-left h3 {
		font-size: 1.8rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 30px;
		line-height: 1.4;
	}
	
	.trust-item {
		display: flex;
		gap: 20px;
		margin-bottom: 25px;
	}
	
	.trust-icon {
		flex-shrink: 0;
		width: 50px;
		height: 50px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 1.5rem;
	}
	
	.trust-text h4 {
		color: #2f5597;
		font-weight: 700;
		margin: 0 0 8px 0;
		font-size: 1.05rem;
	}
	
	.trust-text p {
		color: #666;
		margin: 0;
		font-size: 0.95rem;
		line-height: 1.5;
	}
	
	.trust-right {
		background: linear-gradient(135deg, rgba(47, 85, 151, 0.1) 0%, rgba(30, 58, 109, 0.05) 100%);
		padding: 40px;
		border-radius: 12px;
		border-left: 4px solid #2f5597;
	}
	
	.trust-right h4 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 20px;
		font-size: 1.2rem;
	}
	
	.trust-right ul {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	
	.trust-right li {
		color: #555;
		margin-bottom: 15px;
		padding-left: 30px;
		position: relative;
		line-height: 1.6;
	}
	
	.trust-right li::before {
		content: '✓';
		position: absolute;
		left: 0;
		color: #2f5597;
		font-weight: bold;
		font-size: 1.1rem;
	}
	
	/* Advisory Services Grid */
	.advisory-services-section {
		padding: 60px 0;
		background: #f8f9fa;
	}
	
	.services-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.service-card-modern {
		background: white;
		border-radius: 12px;
		padding: 35px;
		text-align: center;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		position: relative;
		overflow: hidden;
	}
	
	.service-card-modern::before {
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
	
	.service-card-modern:hover::before {
		transform: scaleX(1);
	}
	
	.service-card-modern:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
	}
	
	.service-icon-modern {
		font-size: 3rem;
		color: #2f5597;
		margin-bottom: 20px;
		transition: all 0.3s ease;
		display: inline-block;
	}
	
	.service-card-modern:hover .service-icon-modern {
		transform: scale(1.15) rotate(-5deg);
		filter: drop-shadow(0 5px 15px rgba(47, 85, 151, 0.3));
	}
	
	.service-title {
		font-size: 1.3rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.service-desc {
		color: #666;
		font-size: 0.95rem;
		line-height: 1.6;
		margin: 0;
	}
	
	/* Expertise Areas */
	.expertise-areas-section {
		padding: 60px 0;
		background: white;
	}
	
	.expertise-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.expertise-badge {
		background: #f8f9fa;
		padding: 30px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 3px 15px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
		border-top: 3px solid #2f5597;
	}
	
	.expertise-badge:hover {
		transform: translateY(-8px);
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
		border-top-color: #1e3a6d;
		background: white;
	}
	
	.expertise-icon {
		font-size: 2.5rem;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.expertise-title {
		font-size: 1.1rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.expertise-text {
		color: #666;
		font-size: 0.9rem;
		line-height: 1.5;
	}
	
	/* Process Section */
	.advisory-process-section {
		padding: 60px 0;
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		position: relative;
		overflow: hidden;
	}
	
	.advisory-process-section::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.process-content {
		position: relative;
		z-index: 1;
	}
	
	.process-steps {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
		gap: 20px;
		margin-top: 40px;
	}
	
	.process-step {
		background: rgba(255,255,255,0.95);
		padding: 30px;
		border-radius: 10px;
		text-align: center;
		position: relative;
		transition: all 0.3s ease;
	}
	
	.process-step:hover {
		background: white;
		transform: translateY(-5px);
		box-shadow: 0 10px 30px rgba(0,0,0,0.15);
	}
	
	.step-number {
		width: 50px;
		height: 50px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		border-radius: 50%;
		color: white;
		font-size: 1.5rem;
		font-weight: bold;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 15px;
	}
	
	.step-title {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.step-desc {
		color: #555;
		font-size: 0.9rem;
		line-height: 1.5;
	}
	
	/* Success Stories */
	.success-stories-section {
		padding: 60px 0;
		background: #f8f9fa;
	}
	
	.story-item {
		background: white;
		padding: 40px;
		border-radius: 12px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s ease;
		margin-bottom: 30px;
	}
	
	.story-item:hover {
		box-shadow: 0 10px 35px rgba(47, 85, 151, 0.15);
		transform: translateY(-5px);
	}
	
	.story-header {
		display: flex;
		align-items: center;
		margin-bottom: 20px;
		gap: 15px;
	}
	
	.story-icon {
		width: 50px;
		height: 50px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 1.5rem;
		flex-shrink: 0;
	}
	
	.story-meta h4 {
		color: #2f5597;
		font-weight: 700;
		margin: 0 0 5px 0;
		font-size: 1.1rem;
	}
	
	.story-meta p {
		color: #999;
		margin: 0;
		font-size: 0.85rem;
	}
	
	.story-content {
		color: #555;
		line-height: 1.8;
		margin-bottom: 15px;
	}
	
	.story-result {
		background: #e8edf5;
		padding: 15px;
		border-left: 4px solid #2f5597;
		border-radius: 4px;
		color: #2f5597;
		font-weight: 600;
		font-size: 0.95rem;
	}
	
	/* CTA Section */
	.advisory-cta-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		text-align: center;
		margin-top: 60px;
		border-radius: 12px;
		position: relative;
		overflow: hidden;
	}
	
	.advisory-cta-section::before {
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
	
	.advisory-cta-section h2 {
		color: white;
		font-size: 2rem;
		font-weight: 700;
		margin-bottom: 20px;
	}
	
	.advisory-cta-section p {
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
	
	/* Responsive */
	@media (max-width: 768px) {
		.advisory-hero h2 {
			font-size: 1.8rem;
		}
		
		.section-header h2 {
			font-size: 1.6rem;
		}
		
		.trust-content {
			grid-template-columns: 1fr;
			gap: 30px;
		}
		
		.advisory-cta-section h2 {
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
<section class="advisory-hero">
	<div class="container">
		<div class="advisory-hero-content">
			<h2>AI Advisory Services</h2>
			<p><strong>Armely's Advisory Hub</strong> provides insightful guidance, industry expertise, and a proven track record of success.</p>
			<p>Navigate the complexities of AI transformation with confidence. Our expert advisors collaborate with you to create, oversee, and implement enduring solutions tailored to your organization's unique needs.</p>
		</div>
	</div>
</section>

<!-- Why Trust Us Section -->
<section class="trust-section">
	<div class="container">
		<div class="trust-content">
			<div class="trust-left">
				<h3>Why Organizations Trust Armely</h3>
				
				<div class="trust-item">
					<div class="trust-icon">
						<i class="fa-solid fa-users"></i>
					</div>
					<div class="trust-text">
						<h4>Expert Advisory Team</h4>
						<p>Seasoned professionals with decades of combined experience in digital transformation, AI strategy, and enterprise technology.</p>
					</div>
				</div>
				
				<div class="trust-item">
					<div class="trust-icon">
						<i class="fa-solid fa-lightbulb"></i>
					</div>
					<div class="trust-text">
						<h4>Industry Insights</h4>
						<p>Deep understanding of industry-specific challenges and best practices across various sectors and business models.</p>
					</div>
				</div>
				
				<div class="trust-item">
					<div class="trust-icon">
						<i class="fa-solid fa-comments"></i>
					</div>
					<div class="trust-text">
						<h4>Clear Communication</h4>
						<p>Transparent guidance and effective communication throughout every advisory engagement and transformation journey.</p>
					</div>
				</div>
				
				<div class="trust-item">
					<div class="trust-icon">
						<i class="fa-solid fa-chart-line"></i>
					</div>
					<div class="trust-text">
						<h4>Proven Success</h4>
						<p>Track record of delivering tangible business value through strategic guidance and technology solutions.</p>
					</div>
				</div>
			</div>
			
			<div class="trust-right">
				<h4>Our Advisory Approach</h4>
				<ul>
					<li>Comprehensive assessment of your current technology landscape</li>
					<li>Clear roadmap for digital transformation and AI adoption</li>
					<li>Risk identification and mitigation strategies</li>
					<li>Change management and organizational readiness support</li>
					<li>Vendor-agnostic technology recommendations</li>
					<li>Ongoing advisory support and optimization</li>
					<li>Executive alignment and stakeholder management</li>
					<li>ROI tracking and success metrics definition</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- Advisory Services Section -->
<section class="advisory-services-section">
	<div class="container">
		<div class="section-header">
			<h2>Our Advisory Services</h2>
		</div>
		
		<div class="services-grid">
			<div class="service-card-modern">
				<div class="service-icon-modern">
					<i class="fa-solid fa-route"></i>
				</div>
				<h4 class="service-title">Digital Transformation Strategy</h4>
				<p class="service-desc">Develop a comprehensive roadmap for your digital transformation journey, aligning technology initiatives with business objectives and market opportunities.</p>
			</div>
			
			<div class="service-card-modern">
				<div class="service-icon-modern">
					<i class="fa-solid fa-brain"></i>
				</div>
				<h4 class="service-title">AI Strategy & Roadmapping</h4>
				<p class="service-desc">Create a strategic AI vision for your organization, identify high-impact use cases, and establish a phased implementation plan.</p>
			</div>
			
			<div class="service-card-modern">
				<div class="service-icon-modern">
					<i class="fa-solid fa-check"></i>
				</div>
				<h4 class="service-title">Technology Assessment</h4>
				<p class="service-desc">Evaluate your current technology stack, identify gaps, and provide recommendations for modernization and optimization.</p>
			</div>
			
			<div class="service-card-modern">
				<div class="service-icon-modern">
					<i class="fa-solid fa-users"></i>
				</div>
				<h4 class="service-title">Organizational Change Management</h4>
				<p class="service-desc">Prepare your teams for transformation with change management strategies, training programs, and cultural alignment initiatives.</p>
			</div>
			
			<div class="service-card-modern">
				<div class="service-icon-modern">
					<i class="fa-solid fa-shield"></i>
				</div>
				<h4 class="service-title">Risk & Compliance Advisory</h4>
				<p class="service-desc">Navigate regulatory requirements, security concerns, and risk mitigation strategies specific to AI and digital technologies.</p>
			</div>
			
			<div class="service-card-modern">
				<div class="service-icon-modern">
					<i class="fa-solid fa-chart-bar"></i>
				</div>
				<h4 class="service-title">Performance & ROI Optimization</h4>
				<p class="service-desc">Define metrics, track business value, optimize performance, and maximize return on investment from technology initiatives.</p>
			</div>
		</div>
	</div>
</section>

<!-- Expertise Areas -->
<section class="expertise-areas-section">
	<div class="container">
		<div class="section-header">
			<h2>Areas of Expertise</h2>
		</div>
		
		<div class="expertise-grid">
			<div class="expertise-badge">
				<div class="expertise-icon">
					<i class="fa-solid fa-industry"></i>
				</div>
				<h4 class="expertise-title">Manufacturing</h4>
				<p class="expertise-text">AI solutions for supply chain, quality control, predictive maintenance, and operational excellence.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-icon">
					<i class="fa-solid fa-hospital"></i>
				</div>
				<h4 class="expertise-title">Healthcare</h4>
				<p class="expertise-text">Patient analytics, clinical decision support, and compliance-driven digital transformation.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-icon">
					<i class="fa-solid fa-landmark"></i>
				</div>
				<h4 class="expertise-title">Financial Services</h4>
				<p class="expertise-text">Risk management, fraud detection, customer analytics, and regulatory technology implementation.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-icon">
					<i class="fa-solid fa-shopping-cart"></i>
				</div>
				<h4 class="expertise-title">Retail & E-commerce</h4>
				<p class="expertise-text">Customer experience optimization, demand forecasting, and personalization at scale.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-icon">
					<i class="fa-solid fa-graduation-cap"></i>
				</div>
				<h4 class="expertise-title">Education</h4>
				<p class="expertise-text">Personalized learning paths, student success prediction, and institutional efficiency.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-icon">
					<i class="fa-solid fa-leaf"></i>
				</div>
				<h4 class="expertise-title">Sustainability</h4>
				<p class="expertise-text">Environmental impact optimization and responsible AI governance practices.</p>
			</div>
		</div>
	</div>
</section>

<!-- Advisory Process -->
<section class="advisory-process-section">
	<div class="container">
		<div class="process-content">
			<div class="section-header" style="color: white; margin-bottom: 40px;">
				<h2 style="color: white;">Our Advisory Process</h2>
			</div>
			<div style="text-align: center; color: rgba(255,255,255,0.9); margin-bottom: 40px; font-size: 1.05rem;">
				A structured approach to ensuring your success
			</div>
			
			<div class="process-steps">
				<div class="process-step">
					<div class="step-number">1</div>
					<h4 class="step-title">Discover</h4>
					<p class="step-desc">Understanding your organization, challenges, goals, and current state.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">2</div>
					<h4 class="step-title">Assess</h4>
					<p class="step-desc">Evaluate technology landscape, capabilities, and opportunities.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">3</div>
					<h4 class="step-title">Strategize</h4>
					<p class="step-desc">Develop comprehensive roadmap and strategic recommendations.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">4</div>
					<h4 class="step-title">Execute</h4>
					<p class="step-desc">Guide implementation and navigate transformation journey.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">5</div>
					<h4 class="step-title">Optimize</h4>
					<p class="step-desc">Monitor progress, optimize solutions, and drive continuous improvement.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Success Stories -->
<section class="success-stories-section">
	<div class="container">
		<div class="section-header">
			<h2>Success Stories</h2>
		</div>
		
		<div class="row">
			<div class="col-lg-6">
				<div class="story-item">
					<div class="story-header">
						<div class="story-icon">
							<i class="fa-solid fa-building"></i>
						</div>
						<div class="story-meta">
							<h4>Fortune 500 Manufacturing</h4>
							<p>Industrial & Operations</p>
						</div>
					</div>
					<div class="story-content">
						Guided a major manufacturing company through AI-powered predictive maintenance implementation, reducing equipment downtime and improving operational efficiency.
					</div>
					<div class="story-result">
						✓ 35% reduction in maintenance costs
					</div>
				</div>
			</div>
			
			<div class="col-lg-6">
				<div class="story-item">
					<div class="story-header">
						<div class="story-icon">
							<i class="fa-solid fa-hospital"></i>
						</div>
						<div class="story-meta">
							<h4>Healthcare Provider Network</h4>
							<p>Healthcare & Life Sciences</p>
						</div>
					</div>
					<div class="story-content">
						Developed and implemented AI strategy for clinical decision support and patient analytics, improving care quality and operational efficiency.
					</div>
					<div class="story-result">
						✓ 28% improvement in patient outcomes prediction
					</div>
				</div>
			</div>
			
			<div class="col-lg-6">
				<div class="story-item">
					<div class="story-header">
						<div class="story-icon">
							<i class="fa-solid fa-landmark"></i>
						</div>
						<div class="story-meta">
							<h4>Financial Services Institution</h4>
							<p>Banking & Finance</p>
						</div>
					</div>
					<div class="story-content">
						Architected comprehensive fraud detection and risk management platform using advanced AI, protecting customers and reducing fraud losses significantly.
					</div>
					<div class="story-result">
						✓ 89% improvement in fraud detection accuracy
					</div>
				</div>
			</div>
			
			<div class="col-lg-6">
				<div class="story-item">
					<div class="story-header">
						<div class="story-icon">
							<i class="fa-solid fa-shopping-bag"></i>
						</div>
						<div class="story-meta">
							<h4>E-commerce Retailer</h4>
							<p>Retail & Consumer Goods</p>
						</div>
					</div>
					<div class="story-content">
						Implemented AI-driven personalization and recommendation engine, transforming customer experience and driving significant revenue growth.
					</div>
					<div class="story-result">
						✓ 42% increase in customer engagement
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- CTA Section -->
<section class="advisory-cta-section">
	<div class="container">
		<div class="cta-content">
			<h2>Ready to Transform Your Organization?</h2>
			<p>Let's discuss how our AI advisory services can guide your digital transformation and unlock new business opportunities.</p>
			
			<div class="cta-button-group">
				<a href="{{ route('contact') }}" class="cta-button cta-button-primary">Schedule an Advisory Consultation</a>
				<a href="{{ route('contact') }}" class="cta-button cta-button-secondary">Contact Our Advisors</a>
			</div>
		</div>
	</div>
</section>
