<style>
	/* Hero Section */
	.data-science-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		margin-bottom: 40px;
		position: relative;
		overflow: hidden;
	}
	
	.data-science-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.data-science-hero-content {
		position: relative;
		z-index: 1;
	}
	
	.data-science-hero h2 {
		font-size: 2.5rem;
		font-weight: bold;
		color: #ffffff;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.data-science-hero p {
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
	
	/* Services Section */
	.services-section {
		padding: 60px 0;
		background: white;
	}
	
	.services-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.service-card {
		background: white;
		border-radius: 12px;
		padding: 35px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		position: relative;
		overflow: hidden;
	}
	
	.service-card::before {
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
	
	.service-card:hover::before {
		transform: scaleX(1);
	}
	
	.service-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
	}
	
	.service-icon {
		font-size: 3rem;
		color: #2f5597;
		margin-bottom: 20px;
		transition: all 0.3s ease;
		display: inline-block;
	}
	
	.service-card:hover .service-icon {
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
	
	.service-features {
		margin-top: 20px;
		padding-top: 20px;
		border-top: 1px solid #e8edf5;
	}
	
	.service-features li {
		color: #555;
		font-size: 0.9rem;
		margin-bottom: 8px;
		padding-left: 20px;
		position: relative;
	}
	
	.service-features li::before {
		content: '✓';
		position: absolute;
		left: 0;
		color: #2f5597;
		font-weight: bold;
	}
	
	/* Capabilities Section */
	.capabilities-section {
		padding: 60px 0;
		background: #f8f9fa;
	}
	
	.capabilities-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.capability-badge {
		background: white;
		padding: 30px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 3px 15px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
		border-top: 3px solid #2f5597;
	}
	
	.capability-badge:hover {
		transform: translateY(-8px);
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
		border-top-color: #1e3a6d;
	}
	
	.capability-icon {
		font-size: 2.5rem;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.capability-title {
		font-size: 1.1rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.capability-text {
		color: #666;
		font-size: 0.9rem;
		line-height: 1.5;
	}
	
	/* Use Cases Section */
	.use-cases-section {
		padding: 60px 0;
		background: white;
	}
	
	.use-cases-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.use-case-card {
		background: linear-gradient(135deg, rgba(47, 85, 151, 0.05) 0%, rgba(30, 58, 109, 0.05) 100%);
		padding: 30px;
		border-radius: 12px;
		border-left: 4px solid #2f5597;
		transition: all 0.3s ease;
	}
	
	.use-case-card:hover {
		background: linear-gradient(135deg, rgba(47, 85, 151, 0.1) 0%, rgba(30, 58, 109, 0.1) 100%);
		transform: translateX(10px);
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
	}
	
	.use-case-title {
		font-size: 1.15rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 12px;
	}
	
	.use-case-desc {
		color: #555;
		font-size: 0.9rem;
		line-height: 1.6;
		margin-bottom: 15px;
	}
	
	.use-case-impact {
		background: white;
		padding: 12px;
		border-radius: 6px;
		color: #2f5597;
		font-weight: 600;
		font-size: 0.85rem;
		text-align: center;
	}
	
	/* Process Section */
	.process-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		position: relative;
		overflow: hidden;
	}
	
	.process-section::before {
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
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
	
	/* Tools & Technologies */
	.tools-section {
		padding: 60px 0;
		background: #f8f9fa;
	}
	
	.tools-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
		margin-top: 40px;
	}
	
	.tool-item {
		background: white;
		padding: 20px;
		border-radius: 10px;
		text-align: center;
		box-shadow: 0 3px 15px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
	}
	
	.tool-item:hover {
		transform: translateY(-5px);
		box-shadow: 0 8px 25px rgba(47, 85, 151, 0.15);
	}
	
	.tool-icon {
		font-size: 2rem;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.tool-name {
		font-weight: 700;
		color: #2f5597;
		font-size: 0.95rem;
	}
	
	/* CTA Section */
	.data-science-cta-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		text-align: center;
		margin-top: 60px;
		border-radius: 12px;
		position: relative;
		overflow: hidden;
	}
	
	.data-science-cta-section::before {
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
	
	.data-science-cta-section h2 {
		color: white;
		font-size: 2rem;
		font-weight: 700;
		margin-bottom: 20px;
	}
	
	.data-science-cta-section p {
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
		.data-science-hero h2 {
			font-size: 1.8rem;
		}
		
		.section-header h2 {
			font-size: 1.6rem;
		}
		
		.data-science-cta-section h2 {
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
<section class="data-science-hero">
	<div class="container">
		<div class="data-science-hero-content">
			<h2>Data Science & Analytics Solutions</h2>
			<p>Transform raw data into strategic business insights that drive growth, efficiency, and competitive advantage.</p>
			<p>Our team of expert data scientists and analytics professionals combines advanced techniques with business acumen to unlock the full potential of your data.</p>
		</div>
	</div>
</section>

<!-- Services Section -->
<section class="services-section">
	<div class="container">
		<div class="section-header">
			<h2>Our Data Science Services</h2>
		</div>
		
		<div class="services-grid">
			<!-- Service 1 -->
			<div class="service-card">
				<div class="service-icon">
					<i class="fa-solid fa-chart-line"></i>
				</div>
				<h4 class="service-title">Predictive Modeling</h4>
				<p class="service-desc">Leverage advanced machine learning techniques to build predictive models that forecast future trends, identify risks, and support strategic decision-making.</p>
				<ul class="service-features">
					<li>Time series forecasting</li>
					<li>Demand prediction</li>
					<li>Risk assessment models</li>
					<li>Customer lifetime value prediction</li>
				</ul>
			</div>
			
			<!-- Service 2 -->
			<div class="service-card">
				<div class="service-icon">
					<i class="fa-solid fa-lightbulb"></i>
				</div>
				<h4 class="service-title">Prescriptive Analytics</h4>
				<p class="service-desc">Combine data-driven insights with optimization algorithms to generate actionable recommendations that enhance business processes and drive better outcomes.</p>
				<ul class="service-features">
					<li>Optimization algorithms</li>
					<li>Decision trees</li>
					<li>Recommendation systems</li>
					<li>Resource allocation</li>
				</ul>
			</div>
			
			<!-- Service 3 -->
			<div class="service-card">
				<div class="service-icon">
					<i class="fa-solid fa-chart-bar"></i>
				</div>
				<h4 class="service-title">Data Visualization & Reporting</h4>
				<p class="service-desc">Bring your data to life with stunning, interactive dashboards and reports that enable data-driven decision-making across your organization.</p>
				<ul class="service-features">
					<li>Interactive dashboards</li>
					<li>Real-time reporting</li>
					<li>Custom visualizations</li>
					<li>Executive summaries</li>
				</ul>
			</div>
			
			<!-- Service 4 -->
			<div class="service-card">
				<div class="service-icon">
					<i class="fa-solid fa-comments"></i>
				</div>
				<h4 class="service-title">Natural Language Processing</h4>
				<p class="service-desc">Unlock the value of unstructured data through advanced NLP techniques to extract meaningful insights from customer feedback and market intelligence.</p>
				<ul class="service-features">
					<li>Sentiment analysis</li>
					<li>Text classification</li>
					<li>Topic modeling</li>
					<li>Information extraction</li>
				</ul>
			</div>
			
			<!-- Service 5 -->
			<div class="service-card">
				<div class="service-icon">
					<i class="fa-solid fa-database"></i>
				</div>
				<h4 class="service-title">Advanced Analytics</h4>
				<p class="service-desc">Deploy sophisticated analytical techniques including clustering, anomaly detection, and association rules to uncover hidden patterns and relationships.</p>
				<ul class="service-features">
					<li>Clustering analysis</li>
					<li>Anomaly detection</li>
					<li>Market basket analysis</li>
					<li>Pattern discovery</li>
				</ul>
			</div>
			
			<!-- Service 6 -->
			<div class="service-card">
				<div class="service-icon">
					<i class="fa-solid fa-microscope"></i>
				</div>
				<h4 class="service-title">Experimental Design & Testing</h4>
				<p class="service-desc">Design and execute statistically rigorous experiments to validate hypotheses, optimize processes, and maximize ROI from analytical initiatives.</p>
				<ul class="service-features">
					<li>A/B testing</li>
					<li>Multivariate testing</li>
					<li>Statistical analysis</li>
					<li>Hypothesis validation</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- Capabilities Section -->
<section class="capabilities-section">
	<div class="container">
		<div class="section-header">
			<h2>Our Analytical Capabilities</h2>
		</div>
		
		<div class="capabilities-grid">
			<div class="capability-badge">
				<div class="capability-icon">
					<i class="fa-solid fa-brain"></i>
				</div>
				<h4 class="capability-title">Machine Learning</h4>
				<p class="capability-text">Supervised and unsupervised learning models including regression, classification, and deep learning.</p>
			</div>
			
			<div class="capability-badge">
				<div class="capability-icon">
					<i class="fa-solid fa-database"></i>
				</div>
				<h4 class="capability-title">Big Data Processing</h4>
				<p class="capability-text">Scalable data pipelines and processing frameworks for handling massive datasets.</p>
			</div>
			
			<div class="capability-badge">
				<div class="capability-icon">
					<i class="fa-solid fa-chart-pie"></i>
				</div>
				<h4 class="capability-title">Statistical Analysis</h4>
				<p class="capability-text">Advanced statistical methods for hypothesis testing and insight generation.</p>
			</div>
			
			<div class="capability-badge">
				<div class="capability-icon">
					<i class="fa-solid fa-cube"></i>
				</div>
				<h4 class="capability-title">Data Warehousing</h4>
				<p class="capability-text">Enterprise data warehouse design and optimization for analytical queries.</p>
			</div>
			
			<div class="capability-badge">
				<div class="capability-icon">
					<i class="fa-solid fa-code"></i>
				</div>
				<h4 class="capability-title">Programming & Scripting</h4>
				<p class="capability-text">Python, R, SQL, and other languages for data manipulation and analysis.</p>
			</div>
			
			<div class="capability-badge">
				<div class="capability-icon">
					<i class="fa-solid fa-cloud"></i>
				</div>
				<h4 class="capability-title">Cloud Analytics</h4>
				<p class="capability-text">Azure, AWS, and Google Cloud analytics platforms and services.</p>
			</div>
		</div>
	</div>
</section>

<!-- Use Cases Section -->
<section class="use-cases-section">
	<div class="container">
		<div class="section-header">
			<h2>Common Use Cases</h2>
		</div>
		
		<div class="use-cases-grid">
			<div class="use-case-card">
				<h4 class="use-case-title">Revenue Optimization</h4>
				<p class="use-case-desc">Identify high-value customer segments, optimize pricing strategies, and maximize revenue through data-driven insights.</p>
				<div class="use-case-impact">💰 10-30% revenue increase</div>
			</div>
			
			<div class="use-case-card">
				<h4 class="use-case-title">Customer Retention</h4>
				<p class="use-case-desc">Predict churn risk, identify retention opportunities, and personalize engagement to increase customer lifetime value.</p>
				<div class="use-case-impact">📈 15-25% churn reduction</div>
			</div>
			
			<div class="use-case-card">
				<h4 class="use-case-title">Operational Efficiency</h4>
				<p class="use-case-desc">Optimize resource allocation, reduce waste, and streamline processes through analytical insights.</p>
				<div class="use-case-impact">⚙️ 20-40% cost savings</div>
			</div>
			
			<div class="use-case-card">
				<h4 class="use-case-title">Risk Management</h4>
				<p class="use-case-desc">Identify and mitigate business risks, detect fraud, and ensure compliance through advanced analytics.</p>
				<div class="use-case-impact">🛡️ 30-50% risk reduction</div>
			</div>
			
			<div class="use-case-card">
				<h4 class="use-case-title">Market Intelligence</h4>
				<p class="use-case-desc">Analyze market trends, competitive positioning, and emerging opportunities to inform strategic decisions.</p>
				<div class="use-case-impact">🎯 Enhanced market positioning</div>
			</div>
			
			<div class="use-case-card">
				<h4 class="use-case-title">Performance Analytics</h4>
				<p class="use-case-desc">Monitor KPIs, measure campaign effectiveness, and track business metrics in real-time.</p>
				<div class="use-case-impact">📊 Real-time insights</div>
			</div>
		</div>
	</div>
</section>

<!-- Process Section -->
<section class="process-section">
	<div class="container">
		<div class="process-content">
			<div class="section-header" style="color: white; margin-bottom: 40px;">
				<h2 style="color: white;">Our Data Science Process</h2>
			</div>
			
			<div class="process-steps">
				<div class="process-step">
					<div class="step-number">1</div>
					<h4 class="step-title">Problem Definition</h4>
					<p class="step-desc">Collaborate with stakeholders to define objectives, success metrics, and data requirements.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">2</div>
					<h4 class="step-title">Data Collection</h4>
					<p class="step-desc">Gather, consolidate, and organize data from multiple sources and systems.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">3</div>
					<h4 class="step-title">Data Preparation</h4>
					<p class="step-desc">Clean, transform, and engineer features to prepare data for analysis.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">4</div>
					<h4 class="step-title">Model Development</h4>
					<p class="step-desc">Build, train, and validate models using advanced algorithms and techniques.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">5</div>
					<h4 class="step-title">Insights & Reporting</h4>
					<p class="step-desc">Generate actionable insights and present findings through visualizations and reports.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Tools & Technologies -->
<section class="tools-section">
	<div class="container">
		<div class="section-header">
			<h2>Tools & Technologies We Use</h2>
		</div>
		
		<div class="tools-grid">
			<div class="tool-item">
				<div class="tool-icon">
					<i class="fa-brands fa-python"></i>
				</div>
				<div class="tool-name">Python & Pandas</div>
			</div>
			
			<div class="tool-item">
				<div class="tool-icon">
					<i class="fa-solid fa-database"></i>
				</div>
				<div class="tool-name">R & ggplot2</div>
			</div>
			
			<div class="tool-item">
				<div class="tool-icon">
					<i class="fa-solid fa-code"></i>
				</div>
				<div class="tool-name">SQL & Databases</div>
			</div>
			
			<div class="tool-item">
				<div class="tool-icon">
					<i class="fa-solid fa-chart-bar"></i>
				</div>
				<div class="tool-name">Tableau & PowerBI</div>
			</div>
			
			<div class="tool-item">
				<div class="tool-icon">
					<i class="fa-solid fa-brain"></i>
				</div>
				<div class="tool-name">TensorFlow & PyTorch</div>
			</div>
			
			<div class="tool-item">
				<div class="tool-icon">
					<i class="fa-solid fa-cloud"></i>
				</div>
				<div class="tool-name">Azure ML & AWS SageMaker</div>
			</div>
			
			<div class="tool-item">
				<div class="tool-icon">
					<i class="fa-solid fa-cube"></i>
				</div>
				<div class="tool-name">Apache Spark</div>
			</div>
			
			<div class="tool-item">
				<div class="tool-icon">
					<i class="fa-solid fa-layer-group"></i>
				</div>
				<div class="tool-name">Git & Version Control</div>
			</div>
		</div>
	</div>
</section>

<!-- CTA Section -->
<section class="data-science-cta-section">
	<div class="container">
		<div class="cta-content">
			<h2>Ready to Unlock Your Data's Potential?</h2>
			<p>Let's discuss how our data science and analytics solutions can drive business value and competitive advantage for your organization.</p>
			
			<div class="cta-button-group">
				<a href="{{ route('contact') }}" class="cta-button cta-button-primary">Schedule a Data Analysis</a>
				<a href="{{ route('contact') }}" class="cta-button cta-button-secondary">Explore Our Services</a>
			</div>
		</div>
	</div>
</section>
