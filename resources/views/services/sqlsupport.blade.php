<style>
	.sqlsupport-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.sqlsupport-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.sqlsupport-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.sqlsupport-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.sqlsupport-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.sqlsupport-section {
		padding: 60px 0;
	}
	
	.sqlsupport-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.sqlsupport-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.support-card {
		background: white;
		border-radius: 12px;
		padding: 30px;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
	}
	
	.support-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
		border-top-color: #2f5597;
	}
	
	.support-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}
	
	.support-card p {
		color: #666;
		line-height: 1.7;
	}
	
	.sqlsupport-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.tier-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.tier-card {
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		padding: 25px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	
	.tier-icon {
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
	
	.tier-card h5 {
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.tier-card p {
		font-size: 0.95rem;
		line-height: 1.6;
		color: rgba(255,255,255,0.95);
	}
	
	.benefit-section {
		background: #f8f9fa;
		padding: 60px 0;
	}
	
	.benefit-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
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
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
	}
	
	.benefit-item i {
		font-size: 2rem;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.benefit-item h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.benefit-item p {
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
		.sqlsupport-hero h1 {
			font-size: 2rem;
		}
		
		.sqlsupport-section h2 {
			font-size: 1.8rem;
		}
		
		.sqlsupport-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- SQL Server Support Hero Section -->
<section class="sqlsupport-hero">
	<div class="container">
		<div class="sqlsupport-hero-content">
			<h1 class="text-light">SQL Server Support</h1>
			<p>Managed Support Services for SQL Databases</p>
			<p>Ensure smooth database operations with expert SQL Server support including query optimization, troubleshooting, maintenance, and performance tuning.</p>
		</div>
	</div>
</section>

<section class="sqlsupport-section">
	<div class="container">
		<h2 class="text-light">SQL Server Support Services</h2>
		
		<div class="sqlsupport-grid">
			<div class="support-card">
				<h4><i class="fas fa-wrench"></i> 24/7 Technical Support</h4>
				<p>Round-the-clock support for critical issues, troubleshooting, and emergency assistance.</p>
			</div>
			
			<div class="support-card">
				<h4><i class="fas fa-tachometer-alt"></i> Query Optimization</h4>
				<p>Analyze and optimize slow queries to improve database performance significantly.</p>
			</div>
			
			<div class="support-card">
				<h4><i class="fas fa-heartbeat"></i> Health Monitoring</h4>
				<p>Continuous monitoring of database health with proactive alerts and preventive maintenance.</p>
			</div>
			
			<div class="support-card">
				<h4><i class="fas fa-sync-alt"></i> Backup & Disaster Recovery</h4>
				<p>Comprehensive backup strategies and disaster recovery planning for business continuity.</p>
			</div>
			
			<div class="support-card">
				<h4><i class="fas fa-lock"></i> Security & Compliance</h4>
				<p>Database security audits, updates, and compliance management for regulatory requirements.</p>
			</div>
			
			<div class="support-card">
				<h4><i class="fas fa-graduation-cap"></i> Training & Knowledge Transfer</h4>
				<p>Team training and documentation to build internal SQL Server expertise.</p>
			</div>
		</div>
	</div>
</section>

<section class="sqlsupport-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Support Tiers</h2>
		
		<div class="tier-grid">
			<div class="tier-card">
				<div class="tier-icon">
					<i class="fas fa-user"></i>
				</div>
				<h5 class="text-light">Basic Support</h5>
				<p>Business hours support with response times up to 8 hours for non-critical issues.</p>
			</div>
			
			<div class="tier-card">
				<div class="tier-icon">
					<i class="fas fa-users"></i>
				</div>
				<h5 class="text-light">Standard Support</h5>
				<p>Extended hours support with 4-hour response times for critical production issues.</p>
			</div>
			
			<div class="tier-card">
				<div class="tier-icon">
					<i class="fas fa-shield-alt"></i>
				</div>
				<h5 class="text-light">Premium Support</h5>
				<p>24/7 support with 1-hour response time and dedicated account manager.</p>
			</div>
		</div>
	</div>
</section>

<section class="benefit-section">
	<div class="container">
		<h2>Why Choose Our SQL Support?</h2>
		
		<div class="benefit-grid">
			<div class="benefit-item">
				<i class="fas fa-check-circle"></i>
				<h5>Certified Experts</h5>
				<p>Microsoft certified professionals with years of SQL Server experience.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-handshake"></i>
				<h5>Proactive Maintenance</h5>
				<p>Prevent issues before they impact your business with proactive monitoring.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-chart-line"></i>
				<h5>Performance Improvement</h5>
				<p>Optimize database performance and reduce operational costs.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-shield-alt"></i>
				<h5>Security & Compliance</h5>
				<p>Meet regulatory requirements and industry standards for data protection.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-clock"></i>
				<h5>Rapid Response Times</h5>
				<p>Quick resolution of critical issues to minimize downtime.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-plus-circle"></i>
				<h5>Additional Services</h5>
				<p>Upgrades, migrations, and custom development services available.</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2 class="text-light">Ready for Enterprise SQL Server Support?</h2>
		<p class="text-light">Let our SQL Server experts provide the support and optimization your database infrastructure needs.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request A SQL Server Support Solution</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>
