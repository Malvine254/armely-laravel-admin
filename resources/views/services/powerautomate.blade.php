<style>
	.automation-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.automation-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.automation-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.automation-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.automation-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.automation-section {
		padding: 60px 0;
	}
	
	.automation-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.automation-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.workflow-card {
		background: white;
		border-radius: 12px;
		padding: 30px;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
	}
	
	.workflow-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
		border-top-color: #2f5597;
	}
	
	.workflow-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}
	
	.workflow-card p {
		color: #666;
		line-height: 1.7;
		margin-bottom: 15px;
	}
	
	.automation-grid {
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
	
	.integration-section {
		background: #f8f9fa;
		padding: 60px 0;
	}
	
	.integration-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.integration-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
		margin-top: 40px;
	}
	
	.integration-item {
		background: white;
		padding: 20px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
	}
	
	.integration-item i {
		font-size: 2rem;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.integration-item h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.integration-item p {
		color: #666;
		font-size: 0.9rem;
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
		.automation-hero h1 {
			font-size: 2rem;
		}
		
		.automation-section h2 {
			font-size: 1.8rem;
		}
		
		.automation-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- Automation Hero Section -->
<section class="automation-hero">
	<div class="container">
		<div class="automation-hero-content">
			<h1 class="text-light">Power Automate</h1>
			<p>Work Smarter, Not Harder</p>
			<p>Automate repetitive tasks and workflows to save time, reduce errors, and boost productivity across your entire organization.</p>
		</div>
	</div>
</section>

<section class="automation-section">
	<div class="container">
		<h2>Power Automate Workflow Solutions</h2>
		
		<div class="automation-grid">
			<div class="workflow-card">
				<h4><i class="fas fa-envelope"></i> Email Automation</h4>
				<p>Automate email workflows including notifications, approvals, and mass communications.</p>
				<ul style="list-style: none; padding: 0;">
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Automated notifications</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Email parsing & processing</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Scheduled mass sends</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Approval workflows</li>
				</ul>
			</div>
			
			<div class="workflow-card">
				<h4><i class="fas fa-file-csv"></i> CSV & Excel Automation</h4>
				<p>Streamline data management with automated Excel and CSV file processing.</p>
				<ul style="list-style: none; padding: 0;">
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> File import/export</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Data transformation</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Bulk operations</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Scheduled syncing</li>
				</ul>
			</div>
			
			<div class="workflow-card">
				<h4><i class="fas fa-tasks"></i> Task & Project Automation</h4>
				<p>Keep your team synchronized with automated task creation and project updates.</p>
				<ul style="list-style: none; padding: 0;">
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Automated task creation</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Status updates</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Deadline reminders</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Escalation logic</li>
				</ul>
			</div>
			
			<div class="workflow-card">
				<h4><i class="fas fa-database"></i> Data Sync & Integration</h4>
				<p>Keep systems synchronized with bidirectional data flow and real-time updates.</p>
				<ul style="list-style: none; padding: 0;">
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Multi-system sync</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Real-time data flow</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Data validation</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Error handling</li>
				</ul>
			</div>
			
			<div class="workflow-card">
				<h4><i class="fas fa-check-circle"></i> Approval Workflows</h4>
				<p>Streamline decision-making with intelligent routing and approval chains.</p>
				<ul style="list-style: none; padding: 0;">
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Multi-level approvals</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Conditional routing</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Audit trails</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Escalation rules</li>
				</ul>
			</div>
			
			<div class="workflow-card">
				<h4><i class="fas fa-bell"></i> Notification & Alerts</h4>
				<p>Keep everyone informed with intelligent notifications across multiple channels.</p>
				<ul style="list-style: none; padding: 0;">
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Multi-channel alerts</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Smart scheduling</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> Escalation triggers</li>
					<li style="padding: 8px 0; color: #666;"><i style="color: #2f5597; margin-right: 8px;">→</i> User preferences</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class="automation-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Key Capabilities</h2>
		
		<div class="capability-grid">
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-cogs"></i>
				</div>
				<h5 class="text-light">Automate Repetitive Tasks</h5>
				<p>Save countless hours by automating routine, manual processes.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-brain"></i>
				</div>
				<h5 class="text-light">AI-Powered Workflows</h5>
				<p>Leverage artificial intelligence to enhance decision-making and optimize processes.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-plug"></i>
				</div>
				<h5 class="text-light">Hundreds of Integrations</h5>
				<p>Connect with 500+ apps and services to create powerful automation.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-mobile-alt"></i>
				</div>
				<h5 class="text-light">Mobile Workflows</h5>
				<p>Create workflows accessible on any device for productivity on the go.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-lock"></i>
				</div>
				<h5 class="text-light">Security & Compliance</h5>
				<p>Enterprise-grade security with compliance support for regulatory requirements.</p>
			</div>
			
			<div class="capability-card">
				<div class="capability-icon">
					<i class="fas fa-chart-line"></i>
				</div>
				<h5 class="text-light">Monitoring & Analytics</h5>
				<p>Track workflow performance with detailed analytics and insights.</p>
			</div>
		</div>
	</div>
</section>

<section class="integration-section">
	<div class="container">
		<h2>Integration Capabilities</h2>
		
		<div class="integration-grid">
			<div class="integration-item">
				<i class="fas fa-microsoft"></i>
				<h5>Microsoft 365</h5>
				<p>Teams, Outlook, SharePoint, OneDrive</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-list"></i>
				<h5>SharePoint Online</h5>
				<p>List automation & workflows</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-cloud"></i>
				<h5>Cloud Services</h5>
				<p>Azure, AWS, Salesforce</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-database"></i>
				<h5>Databases</h5>
				<p>SQL, Dynamics 365, CDS</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-envelope"></i>
				<h5>Email & Communication</h5>
				<p>Outlook, Gmail, Slack, Teams</p>
			</div>
			
			<div class="integration-item">
				<i class="fas fa-file"></i>
				<h5>Document Systems</h5>
				<p>OneDrive, Google Drive, Box</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2>Ready to Automate Your Workflows?</h2>
		<p class="text-light">Let our automation experts help you streamline operations and boost productivity with Power Automate solutions.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Request A Power Automate Solution</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

