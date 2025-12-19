<style>
	.sharepoint-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.sharepoint-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.sharepoint-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.sharepoint-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.sharepoint-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.sharepoint-section {
		padding: 60px 0;
	}
	
	.sharepoint-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.sharepoint-section h2::after {
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
	}
	
	.sharepoint-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.feature-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.feature-item {
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		padding: 25px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	
	.feature-icon {
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
	
	.feature-item h5 {
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.feature-item p {
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
	
	.benefit-card {
		background: white;
		padding: 25px;
		border-radius: 12px;
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
		text-align: center;
	}
	
	.benefit-icon {
		font-size: 2rem;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.benefit-card h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.benefit-card p {
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
		.sharepoint-hero h1 {
			font-size: 2rem;
		}
		
		.sharepoint-section h2 {
			font-size: 1.8rem;
		}
		
		.sharepoint-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- SharePoint Online Hero Section -->
<section class="sharepoint-hero">
	<div class="container">
		<div class="sharepoint-hero-content">
			<h1 class="text-light">SharePoint Online</h1>
			<p>Empower Team Collaboration & Content Management</p>
			<p>Build secure, collaborative workspaces with SharePoint Online—your platform for document management, team sites, and intranet solutions.</p>
		</div>
	</div>
</section>

<section class="sharepoint-section">
	<div class="container">
		<h2>SharePoint Solutions</h2>
		
		<div class="sharepoint-grid">
			<div class="solution-card">
				<h4><i class="fas fa-file-alt"></i> Document Management</h4>
				<p>Centralized repository for storing, organizing, and sharing documents securely with version control.</p>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-sitemap"></i> Intranet Platforms</h4>
				<p>Create modern employee intranets that facilitate communication and information sharing.</p>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-users"></i> Team Sites</h4>
				<p>Dedicated collaborative spaces for teams to work together on projects and initiatives.</p>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-book"></i> Knowledge Management</h4>
				<p>Build knowledge bases and resource centers for enterprise-wide information access.</p>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-list-check"></i> Workflow Automation</h4>
				<p>Automate business processes and workflows with Power Automate integration.</p>
			</div>
			
			<div class="solution-card">
				<h4><i class="fas fa-chart-bar"></i> Analytics & Reporting</h4>
				<p>Track usage, engagement, and document lifecycle with built-in analytics.</p>
			</div>
		</div>
	</div>
</section>

<section class="sharepoint-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Key Features</h2>
		
		<div class="feature-grid">
			<div class="feature-item">
				<div class="feature-icon">
					<i class="fas fa-lock"></i>
				</div>
				<h5 class="text-light">Advanced Security</h5>
				<p>Granular permissions and access control with encryption and compliance support.</p>
			</div>
			
			<div class="feature-item">
				<div class="feature-icon">
					<i class="fas fa-users-cog"></i>
				</div>
				<h5 class="text-light">Team Collaboration</h5>
				<p>Seamless integration with Teams for unified collaboration experience.</p>
			</div>
			
			<div class="feature-item">
				<div class="feature-icon">
					<i class="fas fa-search"></i>
				</div>
				<h5 class="text-light">Enterprise Search</h5>
				<p>Powerful search capabilities to find documents and information quickly.</p>
			</div>
			
			<div class="feature-item">
				<div class="feature-icon">
					<i class="fas fa-sync-alt"></i>
				</div>
				<h5 class="text-light">Version Control</h5>
				<p>Track changes with version history and restore previous document versions.</p>
			</div>
			
			<div class="feature-item">
				<div class="feature-icon">
					<i class="fas fa-mobile-alt"></i>
				</div>
				<h5 class="text-light">Mobile Access</h5>
				<p>Access documents and collaborate from anywhere with mobile apps.</p>
			</div>
			
			<div class="feature-item">
				<div class="feature-icon">
					<i class="fas fa-plug"></i>
				</div>
				<h5 class="text-light">M365 Integration</h5>
				<p>Seamless integration with Microsoft 365 apps and services.</p>
			</div>
		</div>
	</div>
</section>

<section class="benefit-section">
	<div class="container">
		<h2>Why Choose Our SharePoint Services?</h2>
		
		<div class="benefit-grid">
			<div class="benefit-card">
				<i class="fas fa-star default-color"></i>
				<h5>Expert Deployment</h5>
				<p>Professional implementation tailored to your organization's specific needs.</p>
			</div>
			
			<div class="benefit-card">
				<i class="fas fa-graduation-cap default-color"></i>
				<h5>Team Training</h5>
				<p>Comprehensive training to ensure user adoption and effective utilization.</p>
			</div>
			
			<div class="benefit-card">
				<i class="fas fa-handshake default-color"></i>
				<h5>Custom Development</h5>
				<p>Custom solutions including web parts, workflows, and extensions.</p>
			</div>
			
			<div class="benefit-card">
				<i class="fas fa-chart-line default-color"></i>
				<h5>Migration Services</h5>
				<p>Seamless migration from on-premises or legacy systems to SharePoint Online.</p>
			</div>
			
			<div class="benefit-card">
				<i class="fas fa-headset default-color"></i>
				<h5>Ongoing Support</h5>
				<p>Dedicated support and optimization for your SharePoint environment.</p>
			</div>
			
			<div class="benefit-card">
				<i class="fas fa-rocket default-color"></i>
				<h5>Best Practices</h5>
				<p>Implementation following Microsoft and industry best practices.</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2 >Ready to Enhance Your Collaboration?</h2>
		<p class="text-light">Let our SharePoint experts help you build a modern collaboration platform for your organization.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request SharePoint Solution</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

