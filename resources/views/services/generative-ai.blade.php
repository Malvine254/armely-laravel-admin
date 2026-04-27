<style>
	/* Hero Section */
	.generative-ai-hero {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		margin-bottom: 40px;
		position: relative;
		overflow: hidden;
	}
	
	.generative-ai-hero::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
		pointer-events: none;
	}
	
	.generative-ai-hero-content {
		position: relative;
		z-index: 1;
	}
	
	.generative-ai-hero h2 {
		font-size: 2.5rem;
		font-weight: bold;
		color: #ffffff;
		margin-bottom: 20px;
		text-shadow: 0 2px 10px rgba(0,0,0,0.2);
	}
	
	.generative-ai-hero p {
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
	
	/* Use Cases Grid */
	.use-cases-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.use-case-card {
		background: white;
		border-radius: 12px;
		padding: 35px;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		position: relative;
		overflow: hidden;
	}
	
	.use-case-card::before {
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
	
	.use-case-card:hover::before {
		transform: scaleX(1);
	}
	
	.use-case-card:hover {
		transform: translateY(-12px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
	}
	
	.use-case-icon {
		font-size: 3rem;
		color: #2f5597;
		margin-bottom: 20px;
		transition: all 0.3s ease;
		display: inline-block;
	}
	
	.use-case-card:hover .use-case-icon {
		transform: scale(1.15) rotate(-5deg);
		filter: drop-shadow(0 5px 15px rgba(47, 85, 151, 0.3));
	}
	
	.use-case-title {
		font-size: 1.3rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.use-case-desc {
		color: #666;
		font-size: 0.95rem;
		line-height: 1.6;
		margin: 0;
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
		grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
	
	/* Models Section */
	.models-section {
		padding: 60px 0;
		background: #f8f9fa;
	}
	
	.models-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 25px;
		margin-top: 40px;
	}
	
	.model-badge {
		background: white;
		padding: 30px;
		border-radius: 12px;
		text-align: center;
		box-shadow: 0 3px 15px rgba(0,0,0,0.08);
		transition: all 0.3s ease;
		border-top: 3px solid #2f5597;
	}
	
	.model-badge:hover {
		transform: translateY(-8px);
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
		border-top-color: #1e3a6d;
	}
	
	.model-icon {
		font-size: 2.5rem;
		color: #2f5597;
		margin-bottom: 15px;
	}
	
	.model-title {
		font-size: 1.1rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 10px;
	}
	
	.model-text {
		color: #666;
		font-size: 0.9rem;
		line-height: 1.5;
	}
	
	/* Capabilities Section */
	.capabilities-section {
		padding: 60px 0;
		background: white;
	}
	
	.capabilities-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.capability-item {
		background: linear-gradient(135deg, rgba(47, 85, 151, 0.05) 0%, rgba(30, 58, 109, 0.05) 100%);
		padding: 30px;
		border-radius: 12px;
		border-left: 4px solid #2f5597;
		transition: all 0.3s ease;
	}
	
	.capability-item:hover {
		background: linear-gradient(135deg, rgba(47, 85, 151, 0.1) 0%, rgba(30, 58, 109, 0.1) 100%);
		transform: translateX(10px);
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.15);
	}
	
	.capability-title {
		font-size: 1.15rem;
		font-weight: 700;
		color: #2f5597;
		margin-bottom: 12px;
	}
	
	.capability-desc {
		color: #555;
		font-size: 0.9rem;
		line-height: 1.6;
	}
	
	/* CTA Section */
	.generative-ai-cta-section {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		padding: 60px 0;
		text-align: center;
		margin-top: 60px;
		border-radius: 12px;
		position: relative;
		overflow: hidden;
	}
	
	.generative-ai-cta-section::before {
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
	
	.generative-ai-cta-section h2 {
		color: white;
		font-size: 2rem;
		font-weight: 700;
		margin-bottom: 20px;
	}
	
	.generative-ai-cta-section p {
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
	
	@media (max-width: 768px) {
		.generative-ai-hero h2 {
			font-size: 1.8rem;
		}
		
		.section-header h2 {
			font-size: 1.6rem;
		}
		
		.generative-ai-cta-section h2 {
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
<section class="generative-ai-hero">
	<div class="container">
		<div class="generative-ai-hero-content">
			<h2>Generative AI Solutions</h2>
			<p>Unleash Creativity: The Power of Generative AI</p>
			<p>Transform your business with cutting-edge generative AI that creates entirely new content—from text and images to music and code. Using GPT-4, LLAMA, and PaLM-2 models, we help you unlock the full potential of AI technology.</p>
		</div>
	</div>
</section>

<!-- Use Cases Section -->
<section class="use-cases-grid" style="padding: 60px 0; background: white;">
	<div class="container">
		<div class="section-header">
			<h2>Generative AI Use Cases</h2>
		</div>
		
		<div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
			<!-- Use Case 1 -->
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fa-solid fa-wand-magic-sparkles"></i>
				</div>
				<h4 class="use-case-title">Content Generation</h4>
				<p class="use-case-desc">Automatically generate high-quality marketing copy, blog articles, email campaigns, and product descriptions that save time and maintain brand voice consistency.</p>
			</div>
			
			<!-- Use Case 2 -->
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fa-solid fa-image"></i>
				</div>
				<h4 class="use-case-title">Image & Design Generation</h4>
				<p class="use-case-desc">Create stunning visuals, product mockups, and design concepts without requiring graphic design expertise or expensive tools.</p>
			</div>
			
			<!-- Use Case 3 -->
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fa-solid fa-code"></i>
				</div>
				<h4 class="use-case-title">Code Generation</h4>
				<p class="use-case-desc">Accelerate software development with AI-assisted code generation, debugging, and optimization that boosts developer productivity.</p>
			</div>
			
			<!-- Use Case 4 -->
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fa-solid fa-comments"></i>
				</div>
				<h4 class="use-case-title">Customer Support Automation</h4>
				<p class="use-case-desc">Deploy intelligent chatbots and virtual agents that handle customer queries, provide personalized responses, and improve satisfaction.</p>
			</div>
			
			<!-- Use Case 5 -->
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fa-solid fa-lightbulb"></i>
				</div>
				<h4 class="use-case-title">Business Intelligence</h4>
				<p class="use-case-desc">Generate insights, reports, and strategic recommendations from complex data to support decision-making.</p>
			</div>
			
			<!-- Use Case 6 -->
			<div class="use-case-card">
				<div class="use-case-icon">
					<i class="fa-solid fa-rocket"></i>
				</div>
				<h4 class="use-case-title">Product Innovation</h4>
				<p class="use-case-desc">Ideate new products and features, generate design variations, and accelerate innovation cycles.</p>
			</div>
		</div>
	</div>
</section>

<!-- Process Section -->
<section class="process-section">
	<div class="container">
		<div class="process-content">
			<div class="section-header" style="color: white; margin-bottom: 40px;">
				<h2 style="color: white;">Our Generative AI Process</h2>
			</div>
			
			<div class="process-steps">
				<div class="process-step">
					<div class="step-number">1</div>
					<h4 class="step-title">Consult</h4>
					<p class="step-desc">We collaborate with key stakeholders to understand unique business needs, challenges, and data landscape requirements.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">2</div>
					<h4 class="step-title">Strategize</h4>
					<p class="step-desc">Develop a comprehensive roadmap including discovery, design, development, deployment, and monitoring plans.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">3</div>
					<h4 class="step-title">Design</h4>
					<p class="step-desc">Create system architecture, define workflows, and prepare data for model integration and training.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">4</div>
					<h4 class="step-title">Implement</h4>
					<p class="step-desc">Deploy generative AI models, fine-tune for your use case, and integrate with existing systems.</p>
				</div>
				
				<div class="process-step">
					<div class="step-number">5</div>
					<h4 class="step-title">Optimize</h4>
					<p class="step-desc">Monitor performance, gather feedback, and continuously improve model outputs and business value.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Models Section -->
<section class="models-section">
	<div class="container">
		<div class="section-header">
			<h2>AI Models We Work With</h2>
		</div>
		
		<div class="models-grid">
			<div class="model-badge">
				<div class="model-icon">
					<i class="fa-solid fa-brain"></i>
				</div>
				<h4 class="model-title">GPT-4</h4>
				<p class="model-text">State-of-the-art language model for text generation, analysis, and complex reasoning tasks.</p>
			</div>
			
			<div class="model-badge">
				<div class="model-icon">
					<i class="fa-solid fa-cube"></i>
				</div>
				<h4 class="model-title">LLAMA</h4>
				<p class="model-text">Open-source large language model offering flexibility and cost-effectiveness for enterprise deployments.</p>
			</div>
			
			<div class="model-badge">
				<div class="model-icon">
					<i class="fa-solid fa-sparkles"></i>
				</div>
				<h4 class="model-title">PaLM-2</h4>
				<p class="model-text">Google's advanced language model with superior multilingual capabilities and specialized domain knowledge.</p>
			</div>
			
			<div class="model-badge">
				<div class="model-icon">
					<i class="fa-solid fa-image"></i>
				</div>
				<h4 class="model-title">DALL-E 3</h4>
				<p class="model-text">Advanced image generation model for creating high-quality visuals from text descriptions.</p>
			</div>
			
			<div class="model-badge">
				<div class="model-icon">
					<i class="fa-solid fa-music"></i>
				</div>
				<h4 class="model-title">Audio & Music</h4>
				<p class="model-text">Generate, transform, and enhance audio and music content with AI-powered models.</p>
			</div>
			
			<div class="model-badge">
				<div class="model-icon">
					<i class="fa-solid fa-code"></i>
				</div>
				<h4 class="model-title">Code Models</h4>
				<p class="model-text">Copilot and specialized code generation models for accelerating software development.</p>
			</div>
		</div>
	</div>
</section>

<!-- Capabilities Section -->
<section class="capabilities-section">
	<div class="container">
		<div class="section-header">
			<h2>Our Generative AI Capabilities</h2>
		</div>
		
		<div class="capabilities-grid">
			<div class="capability-item">
				<h4 class="capability-title">Custom Model Fine-tuning</h4>
				<p class="capability-desc">Adapt pre-trained models to your specific domain, industry terminology, and business requirements for optimal performance.</p>
			</div>
			
			<div class="capability-item">
				<h4 class="capability-title">Prompt Engineering</h4>
				<p class="capability-desc">Develop sophisticated prompts that extract maximum value from generative models for your unique use cases.</p>
			</div>
			
			<div class="capability-item">
				<h4 class="capability-title">Integration & Deployment</h4>
				<p class="capability-desc">Seamlessly integrate AI models into your existing applications, workflows, and infrastructure.</p>
			</div>
			
			<div class="capability-item">
				<h4 class="capability-title">Quality & Safety</h4>
				<p class="capability-desc">Implement guardrails, content moderation, and quality controls to ensure responsible AI output.</p>
			</div>
			
			<div class="capability-item">
				<h4 class="capability-title">Cost Optimization</h4>
				<p class="capability-desc">Optimize model usage, API calls, and resource allocation to maximize ROI and minimize costs.</p>
			</div>
			
			<div class="capability-item">
				<h4 class="capability-title">Performance Monitoring</h4>
				<p class="capability-desc">Track model performance, user satisfaction, and business metrics to ensure continuous improvement.</p>
			</div>
		</div>
	</div>
</section>

<!-- CTA Section -->
<section class="generative-ai-cta-section">
	<div class="container">
		<div class="cta-content">
			<h2>Ready to Harness the Power of Generative AI?</h2>
			<p>Let's discuss how generative AI can accelerate innovation, boost productivity, and create new business opportunities for your organization.</p>
			
			<div class="cta-button-group">
				<a href="{{ route('contact') }}" class="cta-button cta-button-primary">Schedule a Gen AI Consultation</a>
				<a href="{{ route('contact') }}" class="cta-button cta-button-secondary text-light">Explore Use Cases</a>
			</div>
		</div>
	</div>
</section>

			</td>
		</tr>
	</table>
</section>
