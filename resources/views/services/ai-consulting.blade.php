<style>
	.ai-consulting-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		margin-bottom: 40px;
		position: relative;
		overflow: hidden;
	}
	
	.ai-consulting-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.ai-consulting-hero-content {
		position: relative;
		z-index: 1;
	}
	
	.ai-consulting-hero h2 {
		font-size: 2.5rem;
		font-weight: bold;
		color: #ffffff;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.ai-consulting-hero p {
		font-size: 1.1rem;
		color: rgba(255,255,255,0.95);
		line-height: 1.8;
		margin: 15px 0;
	}
	
	/* Video Section */
	.video-showcase-section {
		margin: 50px 0;
		padding: 40px 0;
	}
	
	.video-showcase-title {
		text-align: center;
		margin-bottom: 40px;
	}
	
	.video-showcase-title h3 {
		font-size: 1.8rem;
		color: #2f5597;
		font-weight: 600;
		position: relative;
		display: inline-block;
		padding-bottom: 15px;
	}
	
	.video-showcase-title h3::after {
		content: '';
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translateX(-50%);
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		border-radius: 2px;
	}
	
	.video-container-modern {
		display: flex;
		justify-content: center;
		gap: 30px;
		margin-bottom: 30px;
		flex-wrap: wrap;
	}
	
	.video-item-modern {
		position: relative;
		width: 280px;
		height: 200px;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.2);
		transition: transform 0.3s ease, box-shadow 0.3s ease;
		cursor: pointer;
	}
	
	.video-item-modern:hover {
		transform: translateY(-10px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.3);
	}
	
	.video-item-modern .play-overlay {
		width: 100%;
		height: 100%;
		position: relative;
	}
	
	.video-item-modern .play-button {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		width: 60px;
		height: 60px;
		background: #2f5597;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		transition: all 0.3s ease;
		box-shadow: 0 5px 20px rgba(47, 85, 151, 0.4);
	}
	
	.video-item-modern .play-button::after {
		content: '';
		width: 0;
		height: 0;
		border-left: 15px solid white;
		border-top: 10px solid transparent;
		border-bottom: 10px solid transparent;
		margin-left: 3px;
	}
	
	.video-item-modern:hover .play-button {
		background: #1e3a6d;
		transform: translate(-50%, -50%) scale(1.1);
	}
	
	.video-item-modern .lazy-thumb {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}
	
	.video-link-section {
		text-align: center;
	}
	
	.video-link-section a {
		display: inline-block;
		color: #2f5597;
		font-weight: 600;
		font-size: 1.05rem;
		text-decoration: none;
		padding: 10px 20px;
		border: 2px solid #2f5597;
		border-radius: 50px;
		transition: all 0.3s ease;
		background: transparent;
	}
	
	.video-link-section a:hover {
		background: #2f5597;
		color: white;
		box-shadow: 0 5px 15px rgba(47, 85, 151, 0.3);
	}
	
	/* Consulting Process Section */
	.consulting-process-section {
		padding: 60px 0;
		background: #f8f9fa;
		margin-top: 40px;
		border-radius: 12px;
	}
	
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
	
	/* Modern Cards */
	.modern-process-card {
		background: white;
		border-radius: 12px;
		padding: 35px;
		text-align: center;
		height: 100%;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		position: relative;
		overflow: hidden;
	}
	
	.modern-process-card::before {
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
	
	.modern-process-card:hover::before {
		transform: scaleX(1);
	}
	
	.modern-process-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
	}
	
	.process-card-icon {
		font-size: 3rem;
		color: #2f5597;
		margin-bottom: 20px;
		transition: all 0.3s ease;
		display: inline-block;
	}
	
	.modern-process-card:hover .process-card-icon {
		transform: scale(1.1) rotate(5deg);
		filter: drop-shadow(0 5px 15px rgba(47, 85, 151, 0.3));
	}
	
	.process-card-title {
		font-size: 1.4rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 15px;
		transition: all 0.3s ease;
	}
	
	.modern-process-card:hover .process-card-title {
		color: #1e3a6d;
	}
	
	.process-card-desc {
		color: #555;
		font-size: 0.95rem;
		line-height: 1.6;
		margin: 0;
	}
	
	/* Use Cases Section */
	.use-cases-section {
		padding: 60px 0;
		background: white;
	}
	
	.use-cases-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.use-case-item {
		background: #f8f9fa;
		padding: 30px;
		border-radius: 12px;
		border-left: 4px solid #2f5597;
		transition: all 0.3s ease;
	}
	
	.use-case-item:hover {
		background: white;
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
		transform: translateX(10px);
	}
	
	.use-case-icon {
		font-size: 2.5rem;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.use-case-title {
		font-size: 1.2rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 12px;
	}
	
	.use-case-desc {
		color: #666;
		font-size: 0.95rem;
		line-height: 1.6;
	}
	
	/* Benefits Section */
	.benefits-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		margin: 60px 0;
		position: relative;
		overflow: hidden;
	}
	
	.benefits-section::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.benefits-content {
		position: relative;
		z-index: 1;
	}
	
	.benefits-title {
		color: white;
		font-size: 2.2rem;
		font-weight: 700;
		margin-bottom: 50px;
		text-align: center;
	}
	
	.benefit-item {
		background: rgba(255,255,255,0.95);
		padding: 25px;
		border-radius: 10px;
		margin-bottom: 20px;
		display: flex;
		gap: 20px;
		align-items: flex-start;
		transition: all 0.3s ease;
	}
	
	.benefit-item:hover {
		background: white;
		box-shadow: 0 10px 30px rgba(0,0,0,0.15);
		transform: translateX(5px);
	}
	
	.benefit-icon {
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
	
	.benefit-content h4 {
		color: #2f5597;
		font-weight: 700;
		margin: 0 0 8px 0;
		font-size: 1.1rem;
	}
	
	.benefit-content p {
		color: #666;
		margin: 0;
		font-size: 0.95rem;
		line-height: 1.5;
	}
	
	/* Expertise Section */
	.expertise-section {
		padding: 60px 0;
		background: #f8f9fa;
	}
	
	.expertise-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.expertise-badge {
		background: white;
		padding: 25px;
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
	}
	
	.expertise-badge-icon {
		font-size: 2.5rem;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.expertise-badge-title {
		font-size: 1.1rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.expertise-badge-text {
		color: #666;
		font-size: 0.9rem;
		line-height: 1.5;
	}
	
	/* CTA Section */
	.consulting-cta-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		text-align: center;
		margin-top: 60px;
		border-radius: 12px;
		position: relative;
		overflow: hidden;
	}
	
	.consulting-cta-section::before {
		content: '';
		position: absolute;
		top: -50%;
		right: -50%;
		width: 500px;
		height: 500px;
		background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
		border-radius: 50%;
	}
	
	.consulting-cta-content {
		position: relative;
		z-index: 1;
	}
	
	.consulting-cta-section h2 {
		color: white;
		font-size: 2rem;
		font-weight: 700;
		margin-bottom: 20px;
	}
	
	.consulting-cta-section p {
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
	
	/* FAQ Section */
	.faq-section {
		padding: 60px 0;
	}
	
	.faq-container {
		max-width: 800px;
		margin: 0 auto;
	}
	
	.faq-item {
		margin-bottom: 20px;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 3px 15px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
	}
	
	.faq-question {
		background: #f8f9fa;
		padding: 20px;
		cursor: pointer;
		font-weight: 600;
		color: #2f5597;
		display: flex;
		justify-content: space-between;
		align-items: center;
		transition: all 0.3s ease;
	}
	
	.faq-question:hover {
		background: #e8edf5;
	}
	
	.faq-question.active {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
	}
	
	.faq-toggle-icon {
		font-size: 1.2rem;
		transition: transform 0.3s ease;
	}
	
	.faq-question.active .faq-toggle-icon {
		transform: rotate(180deg);
	}
	
	.faq-answer {
		display: none;
		background: white;
		padding: 20px;
		color: #666;
		line-height: 1.8;
	}
	
	.faq-answer.active {
		display: block;
	}
	
	/* Responsive */
	@media (max-width: 768px) {
		.ai-consulting-hero h2 {
			font-size: 1.8rem;
		}
		
		.section-header h2 {
			font-size: 1.6rem;
		}
		
		.modern-process-card {
			padding: 25px;
		}
		
		.video-item-modern {
			width: 100%;
			max-width: 280px;
		}
		
		.benefits-title {
			font-size: 1.6rem;
		}
		
		.consulting-cta-section h2 {
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
<section class="ai-consulting-hero">
	<div class="container">
		<div class="ai-consulting-hero-content">
			<h2>AI Consulting Services</h2>
			<p>Uncertain how Artificial Intelligence (AI) can transform your business? You're not alone. AI holds immense potential for innovation and growth, but navigating its complexities can be daunting. That's where we come in.</p>
			<div class="row">
                <div class="col-md-8">
                    <p>We offer a comprehensive AI consulting service designed to empower your business to leverage the transformative power of AI. Our team of experts will guide you through every step of the AI adoption journey, ensuring a smooth and successful integration that delivers real results.</p>
		
                </div>
                <div class="col-md-4">
                            
                <div class="video-container-modern">
                    <div class="video-item-modern">
                        <div class="lazy-video" data-src="https://www.youtube.com/embed/LU3q52S26P8?autoplay=1">
                            <div class="play-overlay" onclick="loadVideo(this)">
                                <img src="https://img.youtube.com/vi/LU3q52S26P8/hqdefault.jpg" class="lazy-thumb" alt="AI Consulting Video">
                                <div class="play-button"></div>
                            </div>
                        </div>
                    </div>
                </div>
        
		
                </div>
            </div>
        </div>
	</div>
</section>



<!-- Consulting Process Section -->
<section class="consulting-process-section">
	<div class="container">
		<div class="section-header">
			<h2>Our AI Consulting Process</h2>
		</div>
		
		<div class="row g-4">
			<!-- Card 1 -->
			<div class="col-lg-4 col-md-6">
				<div class="modern-process-card">
					<div class="process-card-icon">
						<i class="fa-solid fa-stethoscope"></i>
					</div>
					<h4 class="process-card-title">Assessment</h4>
					<p class="process-card-desc">
						We begin by collaborating with your team to understand your unique business goals, challenges and data landscape. We collaborate with all the stakeholders to identify use case and priorities considering cost, time, and revenue impact.
					</p>
				</div>
			</div>

			<!-- Card 2 -->
			<div class="col-lg-4 col-md-6">
				<div class="modern-process-card">
					<div class="process-card-icon">
						<i class="fa-solid fa-brain"></i>
					</div>
					<h4 class="process-card-title">Define Technology</h4>
					<p class="process-card-desc">
						Following the assessment, we gain a deep understanding of your business which helps our vendor agnostic approach recommendation of the best AI technology.
					</p>
				</div>
			</div>

			<!-- Card 3 -->
			<div class="col-lg-4 col-md-6">
				<div class="modern-process-card">
					<div class="process-card-icon">
						<i class="fa-solid fa-database"></i>
					</div>
					<h4 class="process-card-title">Data Management</h4>
					<p class="process-card-desc">
						AI thrives on data, our team assists with identifying the right, clean, formatted, complete and ready to use data for effective model development.
					</p>
				</div>
			</div>

			<!-- Card 4 -->
			<div class="col-lg-4 col-md-6 mt-3">
				<div class="modern-process-card">
					<div class="process-card-icon">
						<i class="fa-solid fa-robot"></i>
					</div>
					<h4 class="process-card-title">Model Deployment</h4>
					<p class="process-card-desc">
						Our team in collaboration with your organization develops and trains custom AI models or guides in selection of pre-trained AI models that can easily be customized to meet your needs.
					</p>
				</div>
			</div>

			<!-- Card 5 -->
			<div class="col-lg-4 col-md-6 mt-3">
				<div class="modern-process-card">
					<div class="process-card-icon">
						<i class="fa-solid fa-cloud-arrow-up"></i>
					</div>
					<h4 class="process-card-title">Deployment</h4>
					<p class="process-card-desc">
						After development, we deploy the models into your production environment ensuring smooth integration with your existing workflows.
					</p>
				</div>
			</div>

			<!-- Card 6 -->
			<div class="col-lg-4 col-md-6 mt-3">
				<div class="modern-process-card">
					<div class="process-card-icon">
						<i class="fa-solid fa-life-ring"></i>
					</div>
					<h4 class="process-card-title">Ongoing Support</h4>
					<p class="process-card-desc">
						After deployment, we provide ongoing support and training to your team empowering them to confidently deep dive into AI tools and maximize benefits.
					</p>
				</div>
			</div>

			<!-- Card 7 -->
			<div class="col-lg-4 col-md-6 mt-3">
				<div class="modern-process-card">
					<div class="process-card-icon">
						<i class="fa-solid fa-lock"></i>
					</div>
					<h4 class="process-card-title">Monitoring & Optimization</h4>
					<p class="process-card-desc">
						Our support does not stop there, we continue working with you and your team to optimize the models, re-train them to accommodate new datasets and integrate with other sources as data grows.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Use Cases Section -->
<section class="use-cases-section">
	<div class="container">
		<div class="section-header">
			<h2>AI Consulting Use Cases</h2>
		</div>
		
		<div class="use-cases-grid">
			<!-- Use Case 1 -->
			<div class="use-case-item">
				<div class="use-case-icon">
					<i class="fa-solid fa-chart-line"></i>
				</div>
				<h4 class="use-case-title">Predictive Analytics</h4>
				<p class="use-case-desc">Leverage AI to forecast trends, anticipate customer behavior, and make data-driven decisions that drive revenue growth and competitive advantage.</p>
			</div>
			
			<!-- Use Case 2 -->
			<div class="use-case-item">
				<div class="use-case-icon">
					<i class="fa-solid fa-user-tie"></i>
				</div>
				<h4 class="use-case-title">Customer Intelligence</h4>
				<p class="use-case-desc">Personalize customer experiences through AI-driven insights, segmentation, and recommendations that increase engagement and loyalty.</p>
			</div>
			
			<!-- Use Case 3 -->
			<div class="use-case-item">
				<div class="use-case-icon">
					<i class="fa-solid fa-cogs"></i>
				</div>
				<h4 class="use-case-title">Process Automation</h4>
				<p class="use-case-desc">Streamline operations and reduce costs by automating repetitive tasks, allowing your team to focus on strategic initiatives.</p>
			</div>
			
			<!-- Use Case 4 -->
			<div class="use-case-item">
				<div class="use-case-icon">
					<i class="fa-solid fa-shield"></i>
				</div>
				<h4 class="use-case-title">Fraud Detection</h4>
				<p class="use-case-desc">Protect your business with AI-powered anomaly detection that identifies suspicious patterns and prevents fraudulent transactions in real-time.</p>
			</div>
			
			<!-- Use Case 5 -->
			<div class="use-case-item">
				<div class="use-case-icon">
					<i class="fa-solid fa-comments"></i>
				</div>
				<h4 class="use-case-title">Natural Language Processing</h4>
				<p class="use-case-desc">Extract insights from unstructured text data, automate customer support, and improve communication through intelligent chatbots and sentiment analysis.</p>
			</div>
			
			<!-- Use Case 6 -->
			<div class="use-case-item">
				<div class="use-case-icon">
					<i class="fa-solid fa-image"></i>
				</div>
				<h4 class="use-case-title">Computer Vision</h4>
				<p class="use-case-desc">Harness the power of image recognition and analysis to improve quality control, enhance security, and unlock new business opportunities.</p>
			</div>
		</div>
	</div>
</section>

<!-- Benefits Section -->
<section class="benefits-section">
	<div class="container">
		<div class="benefits-content">
			<h2 class="benefits-title">Why Choose Armely for AI Consulting?</h2>
			
			<div class="row">
				<div class="col-lg-6">
					<div class="benefit-item">
						<div class="benefit-icon">
							<i class="fa-solid fa-star"></i>
						</div>
						<div class="benefit-content">
							<h4>Expert Team</h4>
							<p>Our consultants have deep expertise across multiple industries and AI domains, ensuring best-in-class guidance tailored to your business.</p>
						</div>
					</div>
					
					<div class="benefit-item">
						<div class="benefit-icon">
							<i class="fa-solid fa-bolt"></i>
						</div>
						<div class="benefit-content">
							<h4>Fast Implementation</h4>
							<p>We accelerate your AI journey with proven methodologies and agile delivery approaches that minimize time-to-value.</p>
						</div>
					</div>
					
					<div class="benefit-item">
						<div class="benefit-icon">
							<i class="fa-solid fa-check-circle"></i>
						</div>
						<div class="benefit-content">
							<h4>Vendor Agnostic</h4>
							<p>We recommend the best solutions for your needs without vendor lock-in, ensuring you have complete flexibility and control.</p>
						</div>
					</div>
				</div>
				
				<div class="col-lg-6">
					<div class="benefit-item">
						<div class="benefit-icon">
							<i class="fa-solid fa-shield-check"></i>
						</div>
						<div class="benefit-content">
							<h4>Security & Compliance</h4>
							<p>We prioritize data security, privacy, and regulatory compliance throughout the entire AI implementation lifecycle.</p>
						</div>
					</div>
					
					<div class="benefit-item">
						<div class="benefit-icon">
							<i class="fa-solid fa-chart-bar"></i>
						</div>
						<div class="benefit-content">
							<h4>Measurable ROI</h4>
							<p>We focus on outcomes that matter: cost savings, revenue growth, and operational efficiency gains backed by clear metrics.</p>
						</div>
					</div>
					
					<div class="benefit-item">
						<div class="benefit-icon">
							<i class="fa-solid fa-handshake"></i>
						</div>
						<div class="benefit-content">
							<h4>Long-term Partnership</h4>
							<p>Our commitment extends beyond implementation with ongoing support, optimization, and evolution of your AI solutions.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Expertise Section -->
<section class="expertise-section">
	<div class="container">
		<div class="section-header">
			<h2>Our AI Expertise</h2>
		</div>
		
		<div class="expertise-grid">
			<div class="expertise-badge">
				<div class="expertise-badge-icon">
					<i class="fa-solid fa-brain"></i>
				</div>
				<h4 class="expertise-badge-title">Machine Learning</h4>
				<p class="expertise-badge-text">Supervised, unsupervised, and reinforcement learning solutions for complex business problems.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-badge-icon">
					<i class="fa-solid fa-wand-magic-sparkles"></i>
				</div>
				<h4 class="expertise-badge-title">Generative AI</h4>
				<p class="expertise-badge-text">LLMs, GPT integration, and custom generative AI models for content and code generation.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-badge-icon">
					<i class="fa-solid fa-image"></i>
				</div>
				<h4 class="expertise-badge-title">Computer Vision</h4>
				<p class="expertise-badge-text">Image classification, object detection, and visual analytics for enterprise applications.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-badge-icon">
					<i class="fa-solid fa-language"></i>
				</div>
				<h4 class="expertise-badge-title">NLP & LLMs</h4>
				<p class="expertise-badge-text">Text analytics, sentiment analysis, and conversational AI powered by large language models.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-badge-icon">
					<i class="fa-solid fa-database"></i>
				</div>
				<h4 class="expertise-badge-title">Data Engineering</h4>
				<p class="expertise-badge-text">Data pipelines, ETL processes, and data quality management for AI readiness.</p>
			</div>
			
			<div class="expertise-badge">
				<div class="expertise-badge-icon">
					<i class="fa-solid fa-cloud"></i>
				</div>
				<h4 class="expertise-badge-title">Cloud AI Services</h4>
				<p class="expertise-badge-text">Azure AI, AWS SageMaker, and Google Cloud AI for scalable and managed AI solutions.</p>
			</div>
		</div>
	</div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
	<div class="container">
		<div class="section-header">
			<h2>Frequently Asked Questions</h2>
		</div>
		
		<div class="faq-container">
			<div class="faq-item">
				<div class="faq-question" onclick="toggleFaq(this)">
					<span>How long does an AI consulting engagement typically take?</span>
					<i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
				</div>
				<div class="faq-answer">
					<p>The timeline depends on your specific needs and complexity. A typical engagement ranges from 4-12 weeks for assessment and proof-of-concept phases, with longer timelines for full implementation. We'll provide a detailed roadmap after the initial assessment phase.</p>
				</div>
			</div>
			
			<div class="faq-item">
				<div class="faq-question" onclick="toggleFaq(this)">
					<span>What if we don't have mature data infrastructure?</span>
					<i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
				</div>
				<div class="faq-answer">
					<p>That's a common challenge we help address. Our data engineering team will work with you to establish the necessary data foundation, including data collection, cleaning, and organization to prepare your organization for AI implementation.</p>
				</div>
			</div>
			
			<div class="faq-item">
				<div class="faq-question" onclick="toggleFaq(this)">
					<span>How do you handle AI ethics and bias?</span>
					<i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
				</div>
				<div class="faq-answer">
					<p>Responsible AI is core to our approach. We implement fairness checks, bias detection, and explainability mechanisms in all our AI models. We also provide training and governance frameworks to ensure ethical AI adoption across your organization.</p>
				</div>
			</div>
			
			<div class="faq-item">
				<div class="faq-question" onclick="toggleFaq(this)">
					<span>Will your team train my staff on AI technologies?</span>
					<i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
				</div>
				<div class="faq-answer">
					<p>Yes, knowledge transfer is a critical part of our engagement. We provide comprehensive training to your team covering AI concepts, model operations, best practices, and ongoing maintenance. This ensures your organization can sustain and evolve the AI solutions long-term.</p>
				</div>
			</div>
			
			<div class="faq-item">
				<div class="faq-question" onclick="toggleFaq(this)">
					<span>What makes your AI consulting different?</span>
					<i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
				</div>
				<div class="faq-answer">
					<p>We combine deep technical expertise with business acumen. We focus on outcomes, not just technology. Our vendor-agnostic approach, proven methodologies, and commitment to long-term success differentiate us. We treat your success as our success.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- CTA Section -->
<section class="consulting-cta-section">
	<div class="container">
		<div class="consulting-cta-content">
			<h2>Ready to Transform Your Business with AI?</h2>
			<p>Let's discuss how our AI consulting services can unlock new opportunities and drive measurable business value for your organization.</p>
			
			<div class="cta-button-group">
				<a href="{{ route('contact') }}" class="cta-button cta-button-primary">Schedule a Consultation</a>
				<a href="{{ route('contact') }}" class="cta-button cta-button-secondary">Contact Us Today</a>
			</div>
		</div>
	</div>
</section>

<script>
function toggleFaq(element) {
	const question = element;
	const answer = element.nextElementSibling;
	
	// Close all other open FAQs
	document.querySelectorAll('.faq-question.active').forEach(q => {
		if (q !== question) {
			q.classList.remove('active');
			q.nextElementSibling.classList.remove('active');
		}
	});
	
	// Toggle current FAQ
	question.classList.toggle('active');
	answer.classList.toggle('active');
}
</script>
