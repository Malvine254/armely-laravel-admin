<style>
	.pricing-section {
		padding: 60px 0;
		background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
	}
	
	.pricing-section h2 {
		font-size: 2.2rem;
		color: #2f5597;
		font-weight: 700;
		text-align: center;
		margin-bottom: 50px;
	}
	
	.pricing-section h2::after {
		content: '';
		display: block;
		width: 80px;
		height: 4px;
		background: linear-gradient(90deg, #2f5597, #1e3a6d);
		margin: 20px auto 0;
		border-radius: 2px;
	}
	
	.pricing-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 30px;
		margin-top: 40px;
	}
	
	.pricing-card {
		background: white;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
		position: relative;
	}
	
	.pricing-header {
		background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
		color: white;
		padding: 30px;
		text-align: center;
	}
	
	.pricing-card h4 {
		color: white;
		font-size: 1.4rem;
		font-weight: 700;
		margin-bottom: 10px;
	}
	
	.pricing-card p {
		color: rgba(255,255,255,0.9);
		font-size: 0.9rem;
	}
	
	.pricing-card:hover {
		transform: translateY(-15px);
		box-shadow: 0 15px 40px rgba(47, 85, 151, 0.2);
	}
	
	.pricing-body {
		padding: 30px;
	}
	
	.pricing-features {
		list-style: none;
		padding: 0;
		margin: 20px 0;
	}
	
	.pricing-features li {
		padding: 12px 0;
		color: #555;
		font-size: 0.95rem;
		border-bottom: 1px solid #f0f0f0;
		display: flex;
		align-items: center;
		gap: 10px;
	}
	
	.pricing-features li::before {
		content: '✓';
		color: #2f5597;
		font-weight: bold;
		font-size: 1.2rem;
	}
	
	.pricing-features li:last-child {
		border-bottom: none;
	}
	
	.pricing-cta {
		display: inline-block;
		width: 100%;
		padding: 14px 20px;
		background: linear-gradient(135deg, #2f5597, #1e3a6d);
		color: white;
		text-align: center;
		text-decoration: none;
		border-radius: 50px;
		font-weight: 600;
		transition: all 0.3s ease;
		border: none;
		cursor: pointer;
		margin-top: 20px;
	}
	
	.pricing-cta:hover {
		transform: scale(1.05);
		box-shadow: 0 10px 30px rgba(47, 85, 151, 0.3);
	}
</style>

<!-- Pricing Section -->
<section class="pricing-section">
	<div class="container">
		<h2>Freemium & Pricing Plans</h2>
		
		<div class="pricing-grid">
			<div class="pricing-card">
				<div class="pricing-header">
					<h4>Starter</h4>
					<p>Perfect for getting started</p>
				</div>
				<div class="pricing-body">
					<ul class="pricing-features">
						<li>Basic features</li>
						<li>Email support</li>
						<li>Community access</li>
					</ul>
					<button class="pricing-cta" onclick="downloadOffer('starter')">Get Started</button>
				</div>
			</div>
			
			<div class="pricing-card">
				<div class="pricing-header">
					<h4>Professional</h4>
					<p>For growing teams</p>
				</div>
				<div class="pricing-body">
					<ul class="pricing-features">
						<li>All starter features</li>
						<li>Priority support</li>
						<li>Advanced tools</li>
						<li>API access</li>
					</ul>
					<button class="pricing-cta" onclick="downloadOffer('professional')">Upgrade</button>
				</div>
			</div>
			
			<div class="pricing-card">
				<div class="pricing-header">
					<h4>Enterprise</h4>
					<p>For large organizations</p>
				</div>
				<div class="pricing-body">
					<ul class="pricing-features">
						<li>All features included</li>
						<li>24/7 dedicated support</li>
						<li>Custom integrations</li>
						<li>SLA guaranteed</li>
						<li>Training included</li>
					</ul>
					<button class="pricing-cta" onclick="downloadOffer('enterprise')">Contact Sales</button>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	function downloadOffer(plan) {
		alert('Thank you for your interest in our ' + plan + ' plan. Please contact our sales team.');
	}
</script>
