<style>
	.databricks-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.databricks-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.databricks-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.databricks-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.databricks-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.databricks-section {
		padding: 60px 0;
	}
	
	.databricks-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.databricks-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.service-card {
		background: white;
		border-radius: 12px;
		padding: 30px;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
	}
	
	.service-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
		border-top-color: #2f5597;
	}
	
	.service-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}
	
	.service-card p {
		color: #666;
		line-height: 1.7;
	}
	
	.databricks-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.benefit-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.benefit-item {
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
	
	.benefit-item h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.benefit-item p {
		color: #666;
		font-size: 0.95rem;
		line-height: 1.6;
	}
	
	.platform-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 60px 0;
		margin-top: 60px;
	}
	
	.platform-section h2 {
		color: white;
		text-align: center;
		margin-bottom: 50px;
		font-size: 2.2rem;
	}
	
	.platform-items {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
	}
	
	.platform-item {
		background: rgba(255,255,255,0.1);
		padding: 25px;
		border-radius: 12px;
		text-align: center;
		border: 1px solid rgba(255,255,255,0.2);
	}
	
	.platform-item h5 {
		font-weight: 700;
		margin-bottom: 10px;
		font-size: 1.1rem;
	}
	
	.platform-item p {
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
		.databricks-hero h1 {
			font-size: 2rem;
		}
		
		.databricks-section h2 {
			font-size: 1.8rem;
		}
		
		.databricks-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- Databricks Hero Section -->
<section class="databricks-hero">
	<div class="container">
		<div class="databricks-hero-content">
			<h1 class="text-light">Databricks</h1>
			<p>Unified Analytics & AI Platform</p>
			<p>Transform your data and AI initiatives with Databricks—a unified platform combining data engineering, analytics, and machine learning.</p>
		</div>
	</div>
</section>

<section class="databricks-section">
	<div class="container">
		<h2>Our Databricks Services</h2>
		
		<div class="databricks-grid">
			<div class="service-card">
				<h4><i class="fas fa-cogs"></i> Databricks Implementation</h4>
				<p>End-to-end implementation of Databricks solutions tailored to your organization's needs and objectives.</p>
			</div>
			
			<div class="service-card">
				<h4><i class="fas fa-code"></i> Data Engineering</h4>
				<p>Build robust data pipelines and ETL processes optimized for Databricks and Apache Spark.</p>
			</div>
			
			<div class="service-card">
				<h4><i class="fas fa-chart-bar"></i> Data Analytics & Visualization</h4>
				<p>Create powerful dashboards and analytical solutions for data-driven decision-making.</p>
			</div>
			
			<div class="service-card">
				<h4><i class="fas fa-brain"></i> ML & AI Solutions</h4>
				<p>Develop machine learning models and AI solutions on Databricks' unified platform.</p>
			</div>
			
			<div class="service-card">
				<h4><i class="fas fa-headset"></i> Support & Optimization</h4>
				<p>Ongoing support and optimization to ensure peak performance and ROI from your Databricks investment.</p>
			</div>
			
			<div class="service-card">
				<h4><i class="fas fa-graduation-cap"></i> Training & Development</h4>
				<p>Comprehensive training programs to upskill your team on Databricks best practices.</p>
			</div>
		</div>
	</div>
</section>

<section class="databricks-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Why Choose Our Databricks Services?</h2>
		
		<div class="benefit-grid">
			<div class="benefit-item">
				<div class="benefit-icon">
					<i class="fas fa-handshake"></i>
				</div>
				<h5>Databricks Partnership</h5>
				<p>Our partnership with Databricks ensures best-in-class implementation and support.</p>
			</div>
			
			<div class="benefit-item">
				<div class="benefit-icon">
					<i class="fas fa-users"></i>
				</div>
				<h5>Expert Team</h5>
				<p>Experienced data engineers guiding you through each step of implementation.</p>
			</div>
			
			<div class="benefit-item">
				<div class="benefit-icon">
					<i class="fas fa-puzzle-piece"></i>
				</div>
				<h5>Customized Solutions</h5>
				<p>Tailored solutions that fit your specific goals and address your challenges.</p>
			</div>
			
			<div class="benefit-item">
				<div class="benefit-icon">
					<i class="fas fa-chart-line"></i>
				</div>
				<h5>Orchestration & Integration</h5>
				<p>Seamless integration and orchestration with your existing data ecosystem.</p>
			</div>
			
			<div class="benefit-item">
				<div class="benefit-icon">
					<i class="fas fa-rocket"></i>
				</div>
				<h5>Performance Optimization</h5>
				<p>Maximize performance and efficiency across your data and AI workloads.</p>
			</div>
			
			<div class="benefit-item">
				<div class="benefit-icon">
					<i class="fas fa-check-circle"></i>
				</div>
				<h5>Best Practices</h5>
				<p>Implementation following Databricks and industry best practices.</p>
			</div>
		</div>
	</div>
</section>

<section class="platform-section">
	<div class="container">
		<h2>Databricks Platform Capabilities</h2>
		
		<div class="platform-items">
			<div class="platform-item">
				<h5 class="text-light"><i class="fas fa-code"></i> Data Engineering</h5>
				<p>Apache Spark-based pipelines and ETL processes</p>
			</div>
			
			<div class="platform-item">
				<h5 class="text-light"><i class="fas fa-chart-bar"></i> SQL Analytics</h5>
				<p>Interactive SQL querying and analytics</p>
			</div>
			
			<div class="platform-item">
				<h5 class="text-light"><i class="fas fa-brain"></i> Machine Learning</h5>
				<p>MLflow and Databricks AutoML capabilities</p>
			</div>
			
			<div class="platform-item">
				<h5 class="text-light"><i class="fas fa-sync"></i> Unified Governance</h5>
				<p>Data governance, lineage, and quality management</p>
			</div>
			
			<div class="platform-item">
				<h5 class="text-light"><i class="fas fa-lock"></i> Security & Compliance</h5>
				<p>Enterprise security and compliance features</p>
			</div>
			
			<div class="platform-item">
				<h5 class="text-light"><i class="fas fa-cloud"></i> Cloud Integration</h5>
				<p>Seamless integration with AWS, Azure, and GCP</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section mt-4">
	<div class="container">
		<h2>Ready to Transform Your Data & AI Strategy?</h2>
		<p class="text-light">Let our Databricks experts help you implement a platform that drives innovation and business value.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request Databricks Services</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

