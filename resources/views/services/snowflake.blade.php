<style>
	.snowflake-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.snowflake-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.snowflake-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.snowflake-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.snowflake-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.snowflake-section {
		padding: 60px 0;
	}
	
	.snowflake-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.snowflake-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.feature-card {
		background: white;
		border-radius: 12px;
		padding: 30px;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
	}
	
	.feature-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
		border-top-color: #2f5597;
	}
	
	.feature-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}
	
	.feature-card p {
		color: #666;
		line-height: 1.7;
	}
	
	.snowflake-grid {
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
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		padding: 25px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	
	.capability-icon {
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
	
	.capability-card h5 {
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.capability-card p {
		font-size: 0.95rem;
		line-height: 1.6;
		color: rgba(255,255,255,0.95);
	}
	
	.use-case-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.use-case-card {
		background: white;
		padding: 25px;
		border-radius: 12px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	
	.use-case-card h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.use-case-card p {
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
		.snowflake-hero h1 {
			font-size: 2rem;
		}
		
		.snowflake-section h2 {
			font-size: 1.8rem;
		}
		
		.snowflake-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- Snowflake Hero Section -->
<section class="snowflake-hero">
	<div class="container">
		<div class="snowflake-hero-content">
			<h1 class="text-light">Snowflake</h1>
			<p>Cloud Data Platform for Modern Analytics</p>
			<p>Revolutionize your data management with Snowflake—a scalable, secure cloud platform designed for data warehousing, data lakes, and real-time analytics.</p>
		</div>
	</div>
</section>

<section class="snowflake-section">
	<div class="container">
		<h2 class="text-light">Snowflake Solutions & Services</h2>
		
		<div class="snowflake-grid">
			<div class="feature-card">
				<h4><i class="fas fa-database"></i> Data Warehouse</h4>
				<p>Build enterprise-grade data warehouses with automatic scaling, instant elasticity, and zero maintenance complexity.</p>
			</div>
			
			<div class="feature-card">
				<h4><i class="fas fa-water"></i> Data Lake</h4>
				<p>Create unified data lakes that combine structured and semi-structured data for comprehensive analytics.</p>
			</div>
			
			<div class="feature-card">
				<h4><i class="fas fa-project-diagram"></i> Data Sharing</h4>
				<p>Enable secure, real-time data sharing across organizations without data replication or complex management.</p>
			</div>
			
			<div class="feature-card">
				<h4><i class="fas fa-chart-bar"></i> Analytics & BI</h4>
				<p>Deliver real-time insights with high-performance queries and advanced analytics capabilities.</p>
			</div>
			
			<div class="feature-card">
				<h4><i class="fas fa-code"></i> Data Engineering</h4>
				<p>Build robust data pipelines with Snowflake's native SQL and integration with modern data tools.</p>
			</div>
			
			<div class="feature-card">
				<h4><i class="fas fa-brain"></i> ML & AI Integration</h4>
				<p>Leverage machine learning within Snowflake for predictive analytics and advanced data science.</p>
			</div>
		</div>
	</div>
</section>

<section class="snowflake-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Key Features</h2>
		
		<div class="capability-grid">
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-cloud"></i>
				</div>
				<h5 class="text-light">Cloud-Native Architecture</h5>
				<p>Run seamlessly on AWS, Azure, or Google Cloud with built-in flexibility.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-expand"></i>
				</div>
				<h5 class="text-light">Compute-Storage Separation</h5>
				<p>Scale compute and storage independently for cost-effective operations.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-handshake"></i>
				</div>
				<h5 class="text-light">Secure Data Sharing</h5>
				<p>Share data instantly in real-time without replication or copies.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-lock"></i>
				</div>
				<h5 class="text-light">Enterprise Security</h5>
				<p>Advanced encryption, role-based access, and compliance support built-in.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-bolt"></i>
				</div>
				<h5 class="text-light">High Performance</h5>
				<p>Query terabytes of data in seconds with automatic query optimization.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-chart-line"></i>
				</div>
				<h5 class="text-light">Zero Maintenance</h5>
				<p>Automatic updates, patches, and optimization require no manual intervention.</p>
			</div>
		</div>
	</div>
</section>

<section class="snowflake-section">
	<div class="container">
		<h2>Common Use Cases</h2>
		
		<div class="use-case-grid">
			<div class="use-case-card">
				<h5><i class="fas fa-sitemap"></i> Multi-Cloud Data Management</h5>
				<p>Consolidate data across multiple cloud providers into a single Snowflake instance.</p>
			</div>
			
			<div class="use-case-card">
				<h5><i class="fas fa-people-arrows"></i> Cross-Organizational Data Sharing</h5>
				<p>Enable secure, real-time data access for partners and stakeholders.</p>
			</div>
			
			<div class="use-case-card">
				<h5><i class="fas fa-search"></i> Real-Time Analytics</h5>
				<p>Analyze streaming and real-time data alongside historical data.</p>
			</div>
			
			<div class="use-case-card">
				<h5><i class="fas fa-cubes"></i> Data Consolidation</h5>
				<p>Unify data from multiple sources for comprehensive business analysis.</p>
			</div>
			
			<div class="use-case-card">
				<h5><i class="fas fa-network-wired"></i> Data Governance</h5>
				<p>Implement comprehensive data governance and quality frameworks.</p>
			</div>
			
			<div class="use-case-card">
				<h5><i class="fas fa-rocket"></i> Digital Transformation</h5>
				<p>Enable modern analytics capabilities as part of cloud transformation initiatives.</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2>Ready to Transform Your Data Platform?</h2>
		<p class="text-light">Let our Snowflake experts help you implement a modern, scalable data platform for your organization.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request A Snowflake Solution</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

