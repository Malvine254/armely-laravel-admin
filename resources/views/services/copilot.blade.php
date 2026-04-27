<style>
	.copilot-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		position: relative;
		overflow: hidden;
	}
	
	.copilot-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.copilot-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.copilot-hero h1 {
		font-size: 2.8rem;
		font-weight: 700;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.copilot-hero p {
		font-size: 1.1rem;
		max-width: 800px;
		margin: 0 auto 30px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
	}
	
	.copilot-section {
		padding: 60px 0;
	}
	
	.copilot-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.copilot-section h2::after {
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
	}
	
	.copilot-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.use-case-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.use-case-card {
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		padding: 25px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
	}
	
	.use-case-icon {
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
	
	.use-case-card h5 {
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.use-case-card p {
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
	
	.feature-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 60px 0;
		margin-top: 60px;
	}
	
	.feature-section h2 {
		color: white;
		text-align: center;
		margin-bottom: 50px;
		font-size: 2.2rem;
	}
	
	.feature-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
	}
	
	.feature-item {
		background: rgba(255,255,255,0.1);
		padding: 20px;
		border-radius: 12px;
		text-align: center;
		border: 1px solid rgba(255,255,255,0.2);
	}
	
	.feature-item h5 {
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.feature-item p {
		font-size: 0.9rem;
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
		.copilot-hero h1 {
			font-size: 2rem;
		}
		
		.copilot-section h2 {
			font-size: 1.8rem;
		}
		
		.copilot-grid {
			grid-template-columns: 1fr;
		}
		
		.cta-buttons {
			flex-direction: column;
		}
	}
</style>

<!-- Copilot Hero Section -->
<section class="copilot-hero">
	<div class="container">
		<div class="copilot-hero-content">
			<h1>Microsoft 365 Copilot</h1>
			<p>Your AI-Powered Assistant</p>
			<p>Work smarter with AI-driven assistance integrated into your favorite Microsoft 365 apps. From drafting documents to analyzing data, Copilot enhances your productivity.</p>
		</div>
	</div>
</section>

<section class="copilot-section">
	<div class="container">
		<h2>Copilot Capabilities Across Microsoft 365</h2>
		
		<div class="copilot-grid">
			<div class="app-card">
				<h4><i class="fas fa-file-word"></i> Word & Documents</h4>
				<p>Copilot assists in drafting, editing, and improving your writing with intelligent suggestions and content refinement.</p>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-table"></i> Excel & Analytics</h4>
				<p>Analyze data faster with AI-powered formulas, insights, and predictive analytics recommendations.</p>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-presentation"></i> PowerPoint & Presentations</h4>
				<p>Create professional presentations effortlessly with AI-assisted design suggestions and content generation.</p>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-envelope"></i> Outlook & Email</h4>
				<p>Copilot helps draft emails, summarize conversations, and prioritize your inbox intelligently.</p>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-chat"></i> Teams & Collaboration</h4>
				<p>Summarize meetings, capture action items, and enhance team collaboration with AI-powered assistance.</p>
			</div>
			
			<div class="app-card">
				<h4><i class="fas fa-list-check"></i> Project & Task Management</h4>
				<p>Manage projects efficiently with AI-assisted planning, resource allocation, and progress tracking.</p>
			</div>
		</div>
	</div>
</section>

<section class="benefit-section">
	<div class="container">
		<h2>Key Benefits</h2>
		
		<div class="benefit-grid">
			<div class="benefit-item">
				<i class="fas fa-bolt"></i>
				<h5>Boost Productivity</h5>
				<p>Save time on repetitive tasks and focus on high-value work that drives business results.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-lightbulb"></i>
				<h5>Intelligent Suggestions</h5>
				<p>Get AI-powered recommendations and insights while you work across Microsoft 365 apps.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-users"></i>
				<h5>Enhanced Collaboration</h5>
				<p>Copilot helps teams work together more effectively with better communication and insights.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-chart-bar"></i>
				<h5>Data-Driven Insights</h5>
				<p>Make smarter decisions with AI-assisted analytics and real-time business intelligence.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-lock"></i>
				<h5>Enterprise Security</h5>
				<p>Built on Microsoft's security infrastructure with compliance support and data protection.</p>
			</div>
			
			<div class="benefit-item">
				<i class="fas fa-graduation-cap"></i>
				<h5>Easy to Use</h5>
				<p>Copilot is integrated seamlessly into apps you already use—no new tools to learn.</p>
			</div>
		</div>
	</div>
</section>

<section class="use-case-grid" style="padding: 60px 0;">
	<div class="container" style="width: 100%; max-width: 100%;">
		<h2 style="font-size: 2.2rem; color: #2f5597; font-weight: 700; margin-bottom: 50px; text-align: center;">Common Use Cases</h2>
		
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px;">
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fas fa-pen-fancy"></i>
				</div>
				<h5>Content Creation</h5>
				<p>Copilot helps draft emails, reports, and documents faster than ever before.</p>
			</div>
			
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fas fa-tasks"></i>
				</div>
				<h5>Task Automation</h5>
				<p>Automate routine tasks and focus on strategic, high-impact work.</p>
			</div>
			
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fas fa-graduation-cap"></i>
				</div>
				<h5>Research & Learning</h5>
				<p>Quickly gather information and understand complex data with AI assistance.</p>
			</div>
			
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fas fa-users-cog"></i>
				</div>
				<h5>Team Coordination</h5>
				<p>Summarize meetings and keep everyone aligned on priorities and action items.</p>
			</div>
			
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fas fa-chart-line"></i>
				</div>
				<h5>Data Analysis</h5>
				<p>Analyze spreadsheets and gain insights from data with natural language queries.</p>
			</div>
			
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fas fa-presentation"></i>
				</div>
				<h5>Presentation Design</h5>
				<p>Create professional presentations with AI-assisted design and content suggestions.</p>
			</div>
		</div>
	</div>
</section>

<section class="feature-section">
	<div class="container">
		<h2>What Makes Copilot Different</h2>
		
		<div class="feature-grid">
			<div class="feature-item">
				<h5><i class="fas fa-brain"></i> Advanced AI</h5>
				<p>Powered by latest language models for accurate, contextual assistance.</p>
			</div>
			
			<div class="feature-item">
				<h5><i class="fas fa-shield-alt"></i> Your Data Stays Safe</h5>
				<p>Enterprise-grade security with no data retention for model training.</p>
			</div>
			
			<div class="feature-item">
				<h5><i class="fas fa-laptop"></i> Integrated Experience</h5>
				<p>Works seamlessly within apps you use daily—no context switching needed.</p>
			</div>
			
			<div class="feature-item">
				<h5><i class="fas fa-cog"></i> Customizable</h5>
				<p>Configure Copilot to align with your organization's policies and preferences.</p>
			</div>
		</div>
	</div>
</section>

<section class="cta-section">
	<div class="container">
		<h2>Ready to Transform Your Work with Copilot?</h2>
		<p>Let our experts help you implement Microsoft 365 Copilot to boost your team's productivity and innovation.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary">Get Started with Copilot</a>
			<a href="#contact" class="btn-secondary">Learn More</a>
		</div>
	</div>
</section>

<h3 class="">Digital Services: Your AI-Powered Assistant in Microsoft 365 	<center><hr class="default-background hr" ></center></h3>
<h5 class="mt-2 mb-2">Work Smarter with CoPilot - AI-Powered Assistance</h5>
<p>CoPilot is the AI-driven assistant integrated into Microsoft 365 apps, designed to help you work more efficiently. Whether you're drafting documents, creating presentations, or analyzing data, CoPilot can suggest ideas, automate tasks, and provide insights, allowing you to focus on what matters most.</p>
<blockquote>
	<p> 
	<h5 class="text-light"><i class="icofont-quote-left"></i><strong>Key Features</strong></h5> 
</p>

<ol class="ml-5">
	<li class="mt-1">Automate Manual Processes: Create bots that perform repetitive tasks quickly and accurately.  </li>
	<li class="mt-1">Intelligent Suggestions: Receive AI-powered recommendations while you work. </li>
	<li class="mt-1">Task Automation: Let CoPilot handle routine tasks, from email drafting to data analysis.  </li>
	<li class="mt-1">Data-Driven Insights: Get real-time insights to make informed decisions quickly. </li>
	
</ol>	
									
</blockquote>
