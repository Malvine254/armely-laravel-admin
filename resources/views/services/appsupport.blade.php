<style>
.appsupport-hero {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.appsupport-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: -1;
}
.appsupport-hero h1 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 15px;
}
.appsupport-hero p {
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}
.appsupport-section {
    padding: 60px 0;
}
.appsupport-section h2 {
    color: #2f5597;
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 40px;
    text-align: center;
}
.support-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border-left: 5px solid #2f5597;
}
.support-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(47, 85, 151, 0.2);
}
.support-card h3 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 1.25rem;
}
.support-card p {
    color: #555;
    line-height: 1.6;
    font-size: 0.95rem;
}
.support-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}
.tier-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.tier-card:hover {
    border-color: #2f5597;
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(47, 85, 151, 0.15);
}
.tier-card h3 {
    color: #2f5597;
    font-weight: bold;
    font-size: 1.3rem;
    margin-bottom: 15px;
}
.tier-card .price {
    font-size: 2rem;
    color: #2f5597;
    font-weight: bold;
    margin: 15px 0;
}
.tier-card ul {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    text-align: left;
}
.tier-card li {
    padding: 8px 0;
    color: #555;
    border-bottom: 1px solid #eee;
    font-size: 0.9rem;
}
.tier-card li:before {
    content: "✓ ";
    color: #2f5597;
    font-weight: bold;
    margin-right: 10px;
}
.tier-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}
.benefit-item {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    text-align: center;
    transition: all 0.3s ease;
}
.benefit-item:hover {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    transform: scale(1.05);
}
.benefit-item h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 10px;
    font-size: 1.1rem;
}
.benefit-item:hover h4 {
    color: white;
}
.benefit-item p {
    font-size: 0.9rem;
    margin: 0;
}
.benefit-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.cta-section {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    padding: 50px;
    border-radius: 8px;
    text-align: center;
    margin-top: 60px;
}
.cta-section h3 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    font-weight: bold;
}
.cta-section p {
    font-size: 1.05rem;
    margin-bottom: 30px;
    line-height: 1.6;
}
.cta-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-primary-cta {
    background: white;
    color: #2f5597;
    border: none;
    padding: 12px 30px;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}
.btn-primary-cta:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
.btn-secondary-cta {
    background: transparent;
    color: white;
    border: 2px solid white;
    padding: 12px 30px;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}
.btn-secondary-cta:hover {
    background: white;
    color: #2f5597;
}
@media (max-width: 768px) {
    .appsupport-hero h1 {
        font-size: 1.8rem;
    }
    .appsupport-hero p {
        font-size: 1rem;
    }
    .support-grid {
        grid-template-columns: 1fr;
    }
    .tier-grid {
        grid-template-columns: 1fr;
    }
    .cta-buttons {
        flex-direction: column;
    }
    .btn-primary-cta, .btn-secondary-cta {
        width: 100%;
    }
}
</style>

<div class="appsupport-hero">
    <h1 class="text-light">Managed Application Support</h1>
    <p class="text-light">Ensure Your Business-Critical Applications Operate at Peak Performance</p>
</div>

<div class="container-fluid appsupport-section">
    <div class="container">
        <h2>Enterprise-Grade Application Support Services</h2>
        <p style="text-align: center; color: #555; font-size: 1.05rem; max-width: 800px; margin: 0 auto 40px;">Application support is a specialized service that ensures your business-critical applications operate efficiently, securely, and without interruptions. From troubleshooting issues to enhancing performance and providing regular updates, our support helps maximize ROI on your software investments.</p>

        <div class="support-grid">
            <div class="support-card">
                <h3>24/7 Help Desk Support</h3>
                <p>Round-the-clock expert support for all application issues. Respond quickly to minimize downtime and business impact.</p>
            </div>
            <div class="support-card">
                <h3>Proactive Monitoring</h3>
                <p>Continuous monitoring of application health and performance. Detect and resolve issues before they impact users.</p>
            </div>
            <div class="support-card">
                <h3>Performance Optimization</h3>
                <p>Analyze and optimize application performance. Improve speed, reliability, and user experience continuously.</p>
            </div>
            <div class="support-card">
                <h3>Security & Patching</h3>
                <p>Regular security updates and patch management. Keep applications protected from the latest threats.</p>
            </div>
            <div class="support-card">
                <h3>Troubleshooting & Resolution</h3>
                <p>Expert diagnosis and resolution of complex application issues. Root cause analysis and prevention.</p>
            </div>
            <div class="support-card">
                <h3>Backup & Disaster Recovery</h3>
                <p>Comprehensive backup strategies and recovery procedures. Ensure business continuity in all scenarios.</p>
            </div>
        </div>

        <h2 style="margin-top: 50px;">Support Tiers</h2>
        <div class="tier-grid">
            <div class="tier-card">
                <h3>Basic Support</h3>
                <p class="price">$2,999<span style="font-size: 0.6em;">/month</span></p>
                <ul>
                    <li>Email & Phone Support</li>
                    <li>Business Hours (8am-5pm)</li>
                    <li>Issue Response: 4 hours</li>
                    <li>Monthly Performance Reports</li>
                    <li>Emergency Patches</li>
                    <li>Up to 3 Dedicated Resources</li>
                </ul>
            </div>
            <div class="tier-card">
                <h3>Standard Support</h3>
                <p class="price">$5,999<span style="font-size: 0.6em;">/month</span></p>
                <ul>
                    <li>24/7 Phone & Chat Support</li>
                    <li>Priority Issue Routing</li>
                    <li>Issue Response: 2 hours</li>
                    <li>Weekly Performance Reviews</li>
                    <li>Proactive Monitoring</li>
                    <li>Up to 5 Dedicated Resources</li>
                </ul>
            </div>
            <div class="tier-card">
                <h3>Premium Support</h3>
                <p class="price">$9,999<span style="font-size: 0.6em;">/month</span></p>
                <ul>
                    <li>24/7/365 Critical Support</li>
                    <li>Dedicated Account Manager</li>
                    <li>Issue Response: 1 hour Critical</li>
                    <li>Daily Performance Analysis</li>
                    <li>Strategic Optimization</li>
                    <li>Up to 10 Dedicated Resources</li>
                </ul>
            </div>
        </div>

        <h2 style="margin-top: 50px;">Key Benefits</h2>
        <div class="benefit-grid">
            <div class="benefit-item">
                <h4>Reduced Downtime</h4>
                <p>Minimize application outages and business disruptions</p>
            </div>
            <div class="benefit-item">
                <h4>Lower TCO</h4>
                <p>Reduce total cost of ownership through efficient management</p>
            </div>
            <div class="benefit-item">
                <h4>Expert Resources</h4>
                <p>Access to highly skilled support professionals and specialists</p>
            </div>
            <div class="benefit-item">
                <h4>Compliance Management</h4>
                <p>Maintain regulatory compliance and audit readiness</p>
            </div>
            <div class="benefit-item">
                <h4>Improved Performance</h4>
                <p>Continuous optimization for better user experience</p>
            </div>
            <div class="benefit-item">
                <h4>Predictable Costs</h4>
                <p>Fixed monthly pricing with no surprise expenses</p>
            </div>
        </div>

        
    </div>
</div>
	        
	<section class="cta-section">
	<div class="container">
		<h2 class="text-light">Ready for Enterprise SQL Server Support?</h2>
		<p class="text-light">Let our SQL Server experts provide the support and optimization your database infrastructure needs.</p>
		<div class="cta-buttons">
			<a href="#consultation" class="btn btn-outline-primary">Request A SQL Server Support Solution</a>
			<a href="#contact" class=" btn btn-outline-light">Learn More</a>
		</div>
	</div>
</section>           