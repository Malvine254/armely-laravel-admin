<style>
	.dataeng-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.dataeng-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.dataeng-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.dataeng-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.dataeng-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.dataeng-section {
		padding: 60px 0;
	}
	
	.dataeng-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.dataeng-section h2::after {
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
	
	.dataeng-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.service-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.service-item {
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		padding: 25px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	
	.service-icon {
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
	
	.service-item h5 {
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.service-item p {
		font-size: 0.95rem;
		line-height: 1.6;
		color: rgba(255,255,255,0.95);
	}
	
	.platform-section {
		background: #f8f9fa;
		padding: 60px 0;
	}
	
	.platform-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.platform-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.platform-card {
		background: white;
		padding: 25px;
		border-radius: 12px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	
	.platform-card h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.platform-card p {
		color: #666;
		line-height: 1.6;
		font-size: 0.95rem;
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
		.dataeng-hero h1 {
			font-size: 2rem;
		}
		
		.dataeng-section h2 {
			font-size: 1.8rem;
		}
		
		.dataeng-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- SQL Data Engineering Hero Section -->
<section class="dataeng-hero">
	<div class="container">
		<div class="dataeng-hero-content">
			<h1 class="text-light">SQL & Data Engineering</h1>
			<p>Building Data Systems for Modern Enterprises</p>
			<p>Design, build, and maintain robust data infrastructure that empowers your organization with reliable, accessible, and optimized data for analytics and business intelligence.</p>
		</div>
	</div>
</section>

<section class="dataeng-section">
	<div class="container">
		<h2>Core Data Engineering Services</h2>
		
		<div class="dataeng-grid">
			<div class="capability-card">
				<h4><i class="fas fa-database"></i> Database Design & Management</h4>
				<p>Design and optimize relational databases with best practices for performance and scalability.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-arrows-alt"></i> ETL/ELT Pipeline Development</h4>
				<p>Build robust Extract, Transform, Load (ETL) processes to move and transform data efficiently.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-cubes"></i> Data Warehouse Architecture</h4>
				<p>Design enterprise data warehouses optimized for complex analytics and business intelligence.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-water"></i> Data Lake Implementation</h4>
				<p>Create unified data lakes combining structured and unstructured data for analytics.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-code"></i> SQL Optimization</h4>
				<p>Optimize SQL queries and database performance for high-volume data operations.</p>
			</div>
			
			<div class="capability-card">
				<h4><i class="fas fa-shield-alt"></i> Data Governance & Quality</h4>
				<p>Implement frameworks for data governance, quality management, and compliance.</p>
			</div>
		</div>
	</div>
</section>

<section class="dataeng-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Key Services</h2>
		
		<div class="service-grid">
			<div class="service-item">
				<div class="service-icon">
					<i class="fas fa-project-diagram"></i>
				</div>
				<h5 class="text-light">Design & Schema Development</h5>
				<p>Create efficient database schemas and dimensional models for analytics.</p>
			</div>
			
			<div class="service-item">
				<div class="service-icon">
					<i class="fas fa-cogs"></i>
				</div>
				<h5 class="text-light">ETL Automation</h5>
				<p>Automate data pipelines with industry-standard tools and best practices.</p>
			</div>
			
			<div class="service-item">
				<div class="service-icon">
					<i class="fas fa-chart-bar"></i>
				</div>
				<h5 class="text-light">Analytics & Reporting</h5>
				<p>Build data models and reporting infrastructure for business intelligence.</p>
			</div>
			
			<div class="service-item">
				<div class="service-icon">
					<i class="fas fa-bolt"></i>
				</div>
				<h5 class="text-light">Performance Tuning</h5>
				<p>Optimize database and query performance for large-scale data operations.</p>
			</div>
			
			<div class="service-item">
				<div class="service-icon">
					<i class="fas fa-lock"></i>
				</div>
				<h5 class="text-light">Security & Compliance</h5>
				<p>Implement security controls and compliance frameworks for data protection.</p>
			</div>
			
			<div class="service-item">
				<div class="service-icon">
					<i class="fas fa-graduation-cap"></i>
				</div>
				<h5 class="text-light">Team Training</h5>
				<p>Build your team's SQL and data engineering capabilities with expert training.</p>
			</div>
		</div>
	</div>
</section>

<section class="platform-section">
	<div class="container">
		<h2>Technologies & Platforms</h2>
		
		<div class="platform-grid">
			<div class="platform-card">
				<h5><i class="fas fa-database"></i> SQL Server & Azure SQL</h5>
				<p>Enterprise SQL Server and cloud-based Azure SQL implementations and optimization</p>
			</div>
			
			<div class="platform-card">
				<h5><i class="fas fa-cloud"></i> Cloud Data Warehouses</h5>
				<p>Snowflake, Google BigQuery, Azure Synapse, and Redshift implementations</p>
			</div>
			
			<div class="platform-card">
				<h5><i class="fas fa-code"></i> Apache Spark & Databricks</h5>
				<p>Large-scale data processing and Apache Spark-based ETL solutions</p>
			</div>
			
			<div class="platform-card">
				<h5><i class="fas fa-sitemap"></i> Data Integration Tools</h5>
				<p>Azure Data Factory, Apache Airflow, and other modern data orchestration tools</p>
			</div>
			
			<div class="platform-card">
				<h5><i class="fas fa-chart-line"></i> BI & Analytics Platforms</h5>
				<p>Power BI, Tableau, and other business intelligence and visualization tools</p>
			</div>
			
			<div class="platform-card">
				<h5><i class="fas fa-brain"></i> ML & Advanced Analytics</h5>
				<p>Integration of machine learning models and advanced analytics capabilities</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2>Ready to Build Your Data Infrastructure?</h2>
		<p class="text-light">Let our data engineering experts design and implement a robust data platform that supports your business growth.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request SQL and Data Engineering Solution</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

