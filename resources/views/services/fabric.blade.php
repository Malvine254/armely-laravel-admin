<style>
	.fabric-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 100px 0;
		position: relative;
		overflow: hidden;
	}
	
	.fabric-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: 
			radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
			radial-gradient(circle at 80% 80%, rgba(255,255,255,0.08) 0%, transparent 40%);
		pointer-events: none;
	}
	
	.fabric-hero-content {
		position: relative;
		z-index: 1;
		text-align: center;
	}
	
	.fabric-hero h1 {
		font-size: 3.5rem;
		font-weight: 700;
		margin-bottom: 25px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
		animation: fadeInDown 0.8s ease;
	}
	
	.fabric-hero p {
		font-size: 1.2rem;
		max-width: 850px;
		margin: 0 auto 15px;
		line-height: 1.8;
		color: rgba(255,255,255,0.95);
		animation: fadeInUp 0.8s ease;
	}

	.fabric-hero .lead {
		font-size: 1.4rem;
		font-weight: 600;
		margin-bottom: 30px;
	}
	
	@keyframes fadeInDown {
		from {
			opacity: 0;
			transform: translateY(-30px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(30px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.stats-banner {
		background: white;
		padding: 40px 0;
		box-shadow: 0 5px 30px rgba(0,0,0,0.1);
		margin-top: -40px;
		position: relative;
		z-index: 10;
	}

	.stat-item {
		text-align: center;
		padding: 20px;
	}

	.stat-number {
		font-size: 2.5rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 5px;
	}

	.stat-label {
		color: #666;
		font-size: 0.95rem;
		text-transform: uppercase;
		letter-spacing: 1px;
	}
	
	.fabric-section {
		padding: 80px 0;
	}
	
	.fabric-section h2 {
		font-size: 2.5rem;
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 20px;
		text-align: center;
	}

	.section-subtitle {
		text-align: center;
		color: #666;
		font-size: 1.1rem;
		max-width: 700px;
		margin: 0 auto 50px;
		line-height: 1.6;
	}
	
	.fabric-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.fabric-card {
		background: white;
		border-radius: 15px;
		padding: 35px;
		height: 100%;
		box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
		transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		border-top: 4px solid transparent;
		position: relative;
		overflow: hidden;
	}

	.fabric-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		transform: scaleX(0);
		transition: transform 0.4s ease;
	}
	
	.fabric-card:hover::before {
		transform: scaleX(1);
	}

	.fabric-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 45px rgba(47, 85, 151, 0.25);
	}
	
	.fabric-card h4 {
		color: #2f5597;
		font-size: 1.4rem;
		font-weight: 700;
		margin-bottom: 15px;
		margin-top: 10px;
	}
	
	.fabric-card p {
		color: #666;
		line-height: 1.8;
		margin-bottom: 15px;
	}
	
	.fabric-services-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.service-icon {
		width: 60px;
		height: 60px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		border-radius: 15px;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 1.8rem;
		margin-bottom: 20px;
		box-shadow: 0 5px 15px rgba(47, 85, 151, 0.3);
	}
	
	.features-list {
		list-style: none;
		padding: 0;
		margin: 20px 0;
	}
	
	.features-list li {
		padding: 15px 0;
		color: #555;
		border-bottom: 1px solid #f0f0f0;
		display: flex;
		align-items: flex-start;
		gap: 12px;
		line-height: 1.6;
	}
	
	.features-list li::before {
		content: '✓';
		color: #2f5597;
		font-weight: bold;
		font-size: 1.3rem;
		flex-shrink: 0;
		margin-top: 2px;
	}
	
	.features-list li:last-child {
		border-bottom: none;
	}

	.feature-section {
		margin-bottom: 80px;
	}

	.feature-content {
		background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
		border-radius: 15px;
		padding: 50px;
		position: relative;
		overflow: hidden;
	}

	.feature-image-container {
		position: relative;
		border-radius: 10px;
		overflow: hidden;
		box-shadow: 0 10px 30px rgba(0,0,0,0.15);
		margin-bottom: 30px;
	}

	.feature-image-container img {
		width: 100%;
		height: auto;
		display: block;
		transition: transform 0.5s ease;
	}

	.feature-image-container:hover img {
		transform: scale(1.05);
	}

	.feature-number {
		font-size: 5rem;
		font-weight: 700;
		color: rgba(47, 85, 151, 0.1);
		position: absolute;
		top: -20px;
		right: 20px;
		line-height: 1;
	}
	
	.process-steps {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
		gap: 25px;
		margin-top: 50px;
	}
	
	.process-step {
		background: white;
		padding: 35px 25px;
		border-radius: 15px;
		text-align: center;
		box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
		transition: all 0.3s ease;
		position: relative;
	}

	.process-step:hover {
		transform: translateY(-8px);
		box-shadow: 0 10px 35px rgba(47, 85, 151, 0.2);
	}
	
	.step-number {
		width: 70px;
		height: 70px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		font-size: 2rem;
		font-weight: 700;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 20px;
		box-shadow: 0 5px 15px rgba(47, 85, 151, 0.3);
	}
	
	.process-step h5 {
		color: #2f5597;
		font-weight: 700;
		margin-bottom: 12px;
		font-size: 1.1rem;
	}
	
	.process-step p {
		color: #666;
		font-size: 0.95rem;
		line-height: 1.6;
	}
	
	.fabric-benefits {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		margin-top: 80px;
		position: relative;
		overflow: hidden;
	}

	.fabric-benefits::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: 
			radial-gradient(circle at 30% 30%, rgba(255,255,255,0.1) 0%, transparent 50%),
			radial-gradient(circle at 70% 70%, rgba(255,255,255,0.08) 0%, transparent 40%);
		pointer-events: none;
	}
	
	.fabric-benefits h2 {
		color: white;
		text-align: center;
		margin-bottom: 60px;
		font-size: 2.5rem;
		position: relative;
		z-index: 1;
	}

	.fabric-benefits h2::after {
		background: white;
	}
	
	.benefit-item {
		text-align: center;
		padding: 30px 20px;
		position: relative;
		z-index: 1;
	}

	.benefit-icon {
		width: 80px;
		height: 80px;
		background: rgba(255,255,255,0.15);
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 20px;
		font-size: 2rem;
		backdrop-filter: blur(10px);
		border: 2px solid rgba(255,255,255,0.2);
	}
	
	.benefit-item h5 {
		font-size: 1.3rem;
		margin-bottom: 12px;
		font-weight: 700;
	}
	
	.benefit-item p {
		color: rgba(255,255,255,0.9);
		font-size: 1rem;
		line-height: 1.7;
	}

	.workload-cards {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 30px;
		margin-top: 50px;
	}

	.workload-card {
		background: white;
		border-radius: 15px;
		padding: 30px;
		text-align: center;
		box-shadow: 0 5px 25px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
		border: 2px solid transparent;
	}

	.workload-card:hover {
		border-color: #2f5597;
		transform: translateY(-8px);
		box-shadow: 0 10px 35px rgba(47, 85, 151, 0.2);
	}

	.workload-icon {
		width: 70px;
		height: 70px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 2rem;
		margin: 0 auto 20px;
	}

	.workload-card h4 {
		color: #2f5597;
		font-size: 1.3rem;
		font-weight: 700;
		margin-bottom: 15px;
	}

	.workload-card p {
		color: #666;
		line-height: 1.7;
		font-size: 0.95rem;
	}
	
	.cta-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 80px 0;
		text-align: center;
		position: relative;
		overflow: hidden;
	}

	.cta-section::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.1) 0%, transparent 70%);
		pointer-events: none;
	}
	
	.cta-section h2 {
		color: white;
		font-size: 2.8rem;
		margin-bottom: 20px;
		position: relative;
		z-index: 1;
	}
	
	.cta-section p {
		font-size: 1.2rem;
		margin-bottom: 40px;
		max-width: 700px;
		margin-left: auto;
		margin-right: auto;
		position: relative;
		z-index: 1;
		line-height: 1.8;
	}
	
	.cta-buttons {
		display: flex;
		gap: 20px;
		justify-content: center;
		flex-wrap: wrap;
		position: relative;
		z-index: 1;
	}
	
	.btn-primary-light {
		padding: 16px 40px;
		background: white;
		color: #2f5597;
		text-decoration: none;
		border-radius: 50px;
		font-weight: 600;
		transition: all 0.3s ease;
		border: none;
		cursor: pointer;
		font-size: 1.05rem;
		box-shadow: 0 5px 15px rgba(0,0,0,0.2);
	}
	
	.btn-primary-light:hover {
		transform: scale(1.08);
		box-shadow: 0 10px 30px rgba(0,0,0,0.3);
		color: #2f5597;
	}
	
	.btn-secondary-light {
		padding: 16px 40px;
		background: transparent;
		color: white;
		border: 2px solid white;
		text-decoration: none;
		border-radius: 50px;
		font-weight: 600;
		transition: all 0.3s ease;
		cursor: pointer;
		font-size: 1.05rem;
	}
	
	.btn-secondary-light:hover {
		background: white;
		color: #2f5597;
		transform: scale(1.08);
	}

	.comparison-table {
		background: white;
		border-radius: 15px;
		overflow: hidden;
		box-shadow: 0 5px 25px rgba(0,0,0,0.08);
		margin-top: 40px;
	}

	.comparison-table table {
		width: 100%;
		border-collapse: collapse;
	}

	.comparison-table th {
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		padding: 20px;
		text-align: left;
		font-weight: 600;
	}

	.comparison-table td {
		padding: 18px 20px;
		border-bottom: 1px solid #f0f0f0;
		color: #555;
	}

	.comparison-table tr:hover {
		background: #f8f9fa;
	}
	
	@media (max-width: 768px) {
		.fabric-hero h1 {
			font-size: 2.2rem;
		}

		.fabric-hero .lead {
			font-size: 1.1rem;
		}

		.fabric-hero p {
			font-size: 1rem;
		}
		
		.fabric-section h2 {
			font-size: 2rem;
		}

		.cta-section h2 {
			font-size: 2rem;
		}
		
		.fabric-services-grid {
			grid-template-columns: 1fr;
		}
		
		.process-steps {
			grid-template-columns: 1fr;
		}

		.workload-cards {
			grid-template-columns: 1fr;
		}

		.stat-number {
			font-size: 2rem;
		}

		.feature-content {
			padding: 30px 20px;
		}

		.feature-number {
			font-size: 3rem;
			top: -10px;
			right: 10px;
		}
	}
</style>

<!-- Fabric Hero Section -->
<section class="fabric-hero">
	<div class="container">
		<div class="fabric-hero-content">
			<h1 class="text-light">Microsoft Fabric</h1>
			<p class="lead">End-to-End Analytics Platform for Data-Driven Excellence</p>
			<p>Transform your data infrastructure with Microsoft Fabric—a unified analytics platform that brings together data engineering, data warehousing, real-time intelligence, and business analytics in one integrated environment.</p>
			<p>Empower your entire organization with AI-driven insights, seamless collaboration, and enterprise-grade security.</p>
		</div>
	</div>
</section>

<!-- Stats Banner -->
<section class="stats-banner">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-6">
				<div class="stat-item">
					<div class="stat-number">10+</div>
					<div class="stat-label">Integrated Services</div>
				</div>
			</div>
			<div class="col-md-3 col-6">
				<div class="stat-item">
					<div class="stat-number">100%</div>
					<div class="stat-label">SaaS Platform</div>
				</div>
			</div>
			<div class="col-md-3 col-6">
				<div class="stat-item">
					<div class="stat-number">60+</div>
					<div class="stat-label">Data Connectors</div>
				</div>
			</div>
			<div class="col-md-3 col-6">
				<div class="stat-item">
					<div class="stat-number">AI</div>
					<div class="stat-label">Infused at Every Layer</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- What is Microsoft Fabric -->
<section class="fabric-section">
	<div class="container">
		<h2>What is Microsoft Fabric?</h2>
		<p class="section-subtitle">Microsoft Fabric is an all-in-one analytics solution that provides a unified experience for data professionals and businesses. It combines data movement, data science, real-time analytics, and business intelligence into a single, cohesive platform.</p>

		<div class="fabric-services-grid">
			<div class="fabric-card">
				<div class="service-icon">
					<i class="fa fa-database"></i>
				</div>
				<h4>Data Engineering</h4>
				<p>Build scalable data pipelines with Spark notebooks, data flows, and ETL/ELT processes to transform raw data into actionable insights.</p>
			</div>

			<div class="fabric-card">
				<div class="service-icon">
					<i class="fa fa-warehouse"></i>
				</div>
				<h4>Data Warehousing</h4>
				<p>Create enterprise-grade data warehouses with T-SQL support, automatic scaling, and seamless integration with Power BI.</p>
			</div>

			<div class="fabric-card">
				<div class="service-icon">
					<i class="fa fa-bolt"></i>
				</div>
				<h4>Real-Time Intelligence</h4>
				<p>Process streaming data in real-time with KQL databases, event streams, and instant analytics for time-sensitive decisions.</p>
			</div>

			<div class="fabric-card">
				<div class="service-icon">
					<i class="fa fa-brain"></i>
				</div>
				<h4>Data Science</h4>
				<p>Build, train, and deploy machine learning models with integrated notebooks, MLflow, and automated ML capabilities.</p>
			</div>

			<div class="fabric-card">
				<div class="service-icon">
					<i class="fa fa-chart-line"></i>
				</div>
				<h4>Business Intelligence</h4>
				<p>Create stunning reports and dashboards with Power BI, embedded analytics, and self-service data exploration.</p>
			</div>

			<div class="fabric-card">
				<div class="service-icon">
					<i class="fa fa-plug"></i>
				</div>
				<h4>Data Integration</h4>
				<p>Connect to 100+ data sources with native connectors, data factory pipelines, and automated data orchestration.</p>
			</div>
		</div>
	</div>
</section>

<!-- Core Capabilities Features -->
<section class="fabric-section" style="background: #f8f9fa; padding: 80px 0;">
	<div class="container">
		<h2>Four Pillars of Microsoft Fabric</h2>
		<p class="section-subtitle">Discover how Microsoft Fabric transforms your data ecosystem with these four foundational capabilities</p>

		<!-- Feature 1: Complete Data Platform -->
		<div class="feature-section">
			<div class="feature-content">
				<span class="feature-number">01</span>
				<div class="row align-items-center">
					<div class="col-md-6">
						<div class="feature-image-container">
							<img src="images/services/fabric_main_header.png" alt="Complete Data Platform" class="img-fluid">
						</div>
					</div>
					<div class="col-md-6">
						<h3 style="color: #2f5597; font-size: 2rem; font-weight: 700; margin-bottom: 20px;">Complete Data Platform</h3>
						<p style="color: #555; font-size: 1.05rem; line-height: 1.8; margin-bottom: 25px;">Give your data teams all the tools they need in a unified experience that helps reduce the cost and effort of data integration, governance, and security.</p>
						<ul class="features-list">
							<li><strong>Single Unified Experience</strong> - Help data engineers, data scientists, analysts, and business users achieve shared goals with SaaS built for collaboration</li>
							<li><strong>Governance and Security</strong> - Gain end-to-end visibility, usage insights, and industry-leading governance and compliance capabilities</li>
							<li><strong>Unified Capacity</strong> - Simplify billing and reduce costs with a single pool of capacity and storage for every workload</li>
							<li><strong>Integrated Tools</strong> - Access all analytics capabilities from one unified portal with consistent user experience</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Feature 2: Lake Centric Open -->
		<div class="feature-section">
			<div class="feature-content">
				<span class="feature-number">02</span>
				<div class="row align-items-center">
					<div class="col-md-6 order-md-2">
						<div class="feature-image-container">
							<img src="images/services/fabric_two.png" alt="Lake Centric Open" class="img-fluid">
						</div>
					</div>
					<div class="col-md-6 order-md-1">
						<h3 style="color: #2f5597; font-size: 2rem; font-weight: 700; margin-bottom: 20px;">Lake-Centric & Open</h3>
						<p style="color: #555; font-size: 1.05rem; line-height: 1.8; margin-bottom: 25px;">Simplify data integration from nearly any source into a single, multi-cloud data lake for your entire organization, and work from the same copy of data across analytics engines.</p>
						<ul class="features-list">
							<li><strong>No Data Movement</strong> - Create shortcuts between data items across clouds like Azure and AWS without duplication or ownership change</li>
							<li><strong>One Copy Across Engines</strong> - Reduce data duplication by using a single copy of data to power all your workloads</li>
							<li><strong>OneLake Data Hub</strong> - Bring your data together in an intuitive hub that's automatically indexed for discovery and governance</li>
							<li><strong>Open Standards</strong> - Built on Delta Lake format with support for Apache Spark, ensuring compatibility and flexibility</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Feature 3: Real-Time Analytics -->
		<div class="feature-section">
			<div class="feature-content">
				<span class="feature-number">03</span>
				<div class="row align-items-center">
					<div class="col-md-6">
						<div class="feature-image-container">
							<img src="images/services/fabric_three.png" alt="Real-Time Analytics" class="img-fluid">
						</div>
					</div>
					<div class="col-md-6">
						<h3 style="color: #2f5597; font-size: 2rem; font-weight: 700; margin-bottom: 20px;">Real-Time Analytics</h3>
						<p style="color: #555; font-size: 1.05rem; line-height: 1.8; margin-bottom: 25px;">Empower everyone to uncover insights with relevant data, easy-to-use tools, and stunning visuals embedded in Microsoft 365 apps they use every day.</p>
						<ul class="features-list">
							<li><strong>Direct Lake Mode</strong> - Save time and provide up-to-date insights with fast, real-time connection to your data in OneLake</li>
							<li><strong>Built into Microsoft 365</strong> - Foster a data-driven culture by seamlessly flowing insights into Teams, Excel, PowerPoint, and Outlook</li>
							<li><strong>Data-Driven Actions</strong> - Easily set up end-to-end solutions so teams can act quickly in response to time-sensitive events</li>
							<li><strong>Event Streaming</strong> - Process millions of events per second with low-latency streaming analytics</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Feature 4: AI-Driven Insights -->
		<div class="feature-section">
			<div class="feature-content">
				<span class="feature-number">04</span>
				<div class="row align-items-center">
					<div class="col-md-6 order-md-2">
						<div class="feature-image-container">
							<img src="images/services/fabric_four.png" alt="AI-Driven Insights" class="img-fluid">
						</div>
					</div>
					<div class="col-md-6 order-md-1">
						<h3 style="color: #2f5597; font-size: 2rem; font-weight: 700; margin-bottom: 20px;">AI-Driven Insights</h3>
						<p style="color: #555; font-size: 1.05rem; line-height: 1.8; margin-bottom: 25px;">Adopt a data platform that's infused with AI at every layer to help you get more done, faster with intelligent automation and copilot assistance.</p>
						<ul class="features-list">
							<li><strong>Create Impactful Reports Faster</strong> - Describe what you need and Copilot generates reports, summaries, and calculations automatically</li>
							<li><strong>Do More with Copilot in Fabric</strong> - Get AI assistance for data transformations, query writing, and insights discovery</li>
							<li><strong>Build Custom AI Models</strong> - Fuel tailor-made generative AI experiences in Azure AI Studio with curated data from Fabric</li>
							<li><strong>Automated ML</strong> - Train and deploy machine learning models without extensive coding or data science expertise</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Fabric Workloads -->
<section class="fabric-section">
	<div class="container">
		<h2>Integrated Workloads</h2>
		<p class="section-subtitle">Microsoft Fabric provides specialized workloads for every data and analytics need, all working together seamlessly</p>

		<div class="workload-cards">
			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-exchange-alt"></i>
				</div>
				<h4>Data Factory</h4>
				<p>Modern data integration with 100+ connectors, visual pipeline design, and automated orchestration for ETL/ELT workflows.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-tools"></i>
				</div>
				<h4>Synapse Engineering</h4>
				<p>Apache Spark-based data engineering with notebooks, libraries, and distributed compute for large-scale transformations.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-server"></i>
				</div>
				<h4>Synapse Warehouse</h4>
				<p>SQL-based analytics with automatic scaling, columnar storage, and seamless Power BI integration for BI workloads.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-flask"></i>
				</div>
				<h4>Data Science</h4>
				<p>Build ML models with Python, R, and Spark ML, track experiments with MLflow, and deploy to production endpoints.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-stream"></i>
				</div>
				<h4>Real-Time Analytics</h4>
				<p>KQL-based streaming analytics for IoT, log analytics, and time-series data with millisecond query performance.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-chart-bar"></i>
				</div>
				<h4>Power BI</h4>
				<p>Industry-leading business intelligence with interactive reports, dashboards, and embedded analytics capabilities.</p>
			</div>
		</div>
	</div>
</section>

<!-- Key Benefits -->
<section class="fabric-benefits">
	<div class="container">
		<h2>Why Choose Microsoft Fabric?</h2>
		<div class="row">
			<div class="col-md-4">
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa fa-rocket"></i>
					</div>
					<h5>Faster Time to Insights</h5>
					<p>Reduce development time by up to 70% with integrated tools and automated workflows. Get from raw data to actionable insights in hours, not weeks.</p>
				</div>
			</div>
			<div class="col-md-4">
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa fa-dollar-sign"></i>
					</div>
					<h5>Lower Total Cost of Ownership</h5>
					<p>Eliminate data silos and reduce infrastructure costs with unified capacity pools. Pay for what you use with flexible consumption pricing.</p>
				</div>
			</div>
			<div class="col-md-4">
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa fa-shield-alt"></i>
					</div>
					<h5>Enterprise-Grade Security</h5>
					<p>Built-in security, compliance, and governance with Azure Active Directory, encryption at rest and in transit, and comprehensive audit logs.</p>
				</div>
			</div>
			<div class="col-md-4">
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa fa-users"></i>
					</div>
					<h5>Collaborative Workspace</h5>
					<p>Enable seamless collaboration between data engineers, scientists, analysts, and business users in a single unified environment.</p>
				</div>
			</div>
			<div class="col-md-4">
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa fa-expand-arrows-alt"></i>
					</div>
					<h5>Infinite Scale</h5>
					<p>Auto-scale compute and storage to handle any workload size. Process petabytes of data without infrastructure management overhead.</p>
				</div>
			</div>
			<div class="col-md-4">
				<div class="benefit-item">
					<div class="benefit-icon">
						<i class="fa fa-magic"></i>
					</div>
					<h5>AI-Powered Innovation</h5>
					<p>Leverage Copilot assistance and built-in AI capabilities to accelerate development and discover insights faster than ever before.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Implementation Process -->
<section class="fabric-section">
	<div class="container">
		<h2>Our Implementation Approach</h2>
		<p class="section-subtitle">Partner with us for a proven, structured approach to Microsoft Fabric adoption that delivers measurable business value</p>

		<div class="process-steps">
			<div class="process-step">
				<div class="step-number">1</div>
				<h5>Discovery & Assessment</h5>
				<p>Evaluate current data landscape, identify use cases, and define success metrics aligned with business objectives.</p>
			</div>

			<div class="process-step">
				<div class="step-number">2</div>
				<h5>Architecture Design</h5>
				<p>Design scalable data architecture, define data models, and establish governance frameworks and security policies.</p>
			</div>

			<div class="process-step">
				<div class="step-number">3</div>
				<h5>Pilot Implementation</h5>
				<p>Build proof of concept with high-value use case, validate approach, and demonstrate quick wins to stakeholders.</p>
			</div>

			<div class="process-step">
				<div class="step-number">4</div>
				<h5>Data Migration</h5>
				<p>Migrate data sources to OneLake, implement pipelines and transformations, and ensure data quality and consistency.</p>
			</div>

			<div class="process-step">
				<div class="step-number">5</div>
				<h5>Analytics Development</h5>
				<p>Build reports, dashboards, and ML models. Implement real-time analytics and create self-service capabilities.</p>
			</div>

			<div class="process-step">
				<div class="step-number">6</div>
				<h5>Training & Adoption</h5>
				<p>Conduct user training, create documentation, and establish center of excellence for ongoing support and optimization.</p>
			</div>
		</div>
	</div>
</section>

<!-- Use Cases -->
<section class="fabric-section" style="background: #f8f9fa;">
	<div class="container">
		<h2>Industry Use Cases</h2>
		<p class="section-subtitle">See how organizations across industries leverage Microsoft Fabric to drive digital transformation</p>

		<div class="workload-cards">
			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-shopping-cart"></i>
				</div>
				<h4>Retail & E-Commerce</h4>
				<p>Real-time inventory management, customer behavior analytics, personalized recommendations, and supply chain optimization.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-heartbeat"></i>
				</div>
				<h4>Healthcare</h4>
				<p>Patient data integration, predictive analytics for readmission risk, operational efficiency, and regulatory compliance reporting.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-industry"></i>
				</div>
				<h4>Manufacturing</h4>
				<p>IoT sensor analytics, predictive maintenance, quality control, production optimization, and supply chain visibility.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-university"></i>
				</div>
				<h4>Financial Services</h4>
				<p>Fraud detection, risk analytics, customer 360 view, regulatory reporting, and real-time transaction monitoring.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-broadcast-tower"></i>
				</div>
				<h4>Telecommunications</h4>
				<p>Network performance monitoring, customer churn prediction, usage analytics, and service quality optimization.</p>
			</div>

			<div class="workload-card">
				<div class="workload-icon">
					<i class="fa fa-bolt"></i>
				</div>
				<h4>Energy & Utilities</h4>
				<p>Smart grid analytics, consumption forecasting, asset monitoring, renewable energy optimization, and outage prediction.</p>
			</div>
		</div>
	</div>
</section>

<!-- Comparison Table -->
<section class="fabric-section">
	<div class="container">
		<h2>Microsoft Fabric vs Traditional Approaches</h2>
		<p class="section-subtitle">Discover how Fabric simplifies your data analytics stack</p>

		<div class="comparison-table">
			<table>
				<thead>
					<tr>
						<th>Feature</th>
						<th>Traditional Approach</th>
						<th>Microsoft Fabric</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong>Data Integration</strong></td>
						<td>Multiple ETL tools, complex configurations</td>
						<td>Unified Data Factory with 100+ connectors</td>
					</tr>
					<tr>
						<td><strong>Data Storage</strong></td>
						<td>Separate data lakes, warehouses, and databases</td>
						<td>OneLake - single unified storage layer</td>
					</tr>
					<tr>
						<td><strong>Analytics Tools</strong></td>
						<td>Disparate BI, data science, and engineering tools</td>
						<td>Integrated workloads in one platform</td>
					</tr>
					<tr>
						<td><strong>Governance</strong></td>
						<td>Fragmented policies across systems</td>
						<td>Centralized governance with Microsoft Purview</td>
					</tr>
					<tr>
						<td><strong>Collaboration</strong></td>
						<td>Data silos between teams</td>
						<td>Unified workspace for all data professionals</td>
					</tr>
					<tr>
						<td><strong>Pricing Model</strong></td>
						<td>Complex pricing for each service</td>
						<td>Simple capacity-based consumption pricing</td>
					</tr>
					<tr>
						<td><strong>Time to Value</strong></td>
						<td>Months of setup and integration</td>
						<td>Days to weeks with SaaS deployment</td>
					</tr>
					<tr>
						<td><strong>AI Capabilities</strong></td>
						<td>Separate ML platforms and tools</td>
						<td>Built-in AI, Copilot, and AutoML</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</section>

<!-- Call to Action -->
<section class="cta-section">
	<div class="container">
		<h2>Ready to Transform Your Data Platform?</h2>
		<p>Join thousands of organizations worldwide leveraging Microsoft Fabric to unlock the full potential of their data. Let's build your modern analytics platform together.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn-primary-light">Schedule Free Consultation</a>
			<a href="#contact" class="btn-secondary-light">Download Solution Brief</a>
		</div>
	</div>
</section>
