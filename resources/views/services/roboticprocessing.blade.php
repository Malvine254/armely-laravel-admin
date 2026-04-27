<style>
.rpa-hero {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.rpa-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: -1;
}
.rpa-hero h1 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 15px;
}
.rpa-hero p {
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}
.rpa-section {
    padding: 60px 0;
}
.rpa-section h2 {
    color: #2f5597;
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 40px;
    text-align: center;
}
.automation-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border-left: 5px solid #2f5597;
}
.automation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(47, 85, 151, 0.2);
}
.automation-card h3 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 1.25rem;
}
.automation-card p {
    color: #555;
    line-height: 1.6;
    font-size: 0.95rem;
}
.automation-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}
.process-item {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    text-align: center;
    transition: all 0.3s ease;
}
.process-item:hover {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    transform: scale(1.05);
}
.process-item h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 10px;
    font-size: 1.1rem;
}
.process-item:hover h4 {
    color: white;
}
.process-item p {
    font-size: 0.9rem;
    margin: 0;
}
.benefit-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-top: 4px solid #2f5597;
    transition: all 0.3s ease;
}
.benefit-card:hover {
    box-shadow: 0 6px 14px rgba(47, 85, 151, 0.15);
    transform: translateY(-3px);
}
.benefit-card h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 12px;
}
.benefit-card p {
    color: #555;
    font-size: 0.9rem;
    line-height: 1.5;
}
.benefit-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
    .rpa-hero h1 {
        font-size: 1.8rem;
    }
    .rpa-hero p {
        font-size: 1rem;
    }
    .automation-grid {
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

<div class="rpa-hero">
    <h1 class="text-light">Robotic Process Automation</h1>
    <p class="text-light">Automate Manual Tasks with Ease and Eliminate Repetitive Work</p>
</div>

<div class="container-fluid rpa-section">
    <div class="container">
        <h2>Streamline Your Business Processes with RPA</h2>
        <p style="text-align: center; color: #555; font-size: 1.05rem; max-width: 800px; margin: 0 auto 40px;">Robotic Process Automation (RPA) with Microsoft Power Automate enables you to automate repetitive, rule-based tasks by mimicking human interactions with digital systems. RPA bots handle high-volume tasks with 100% accuracy.</p>

        <div class="automation-grid">
            <div class="automation-card">
                <h3>Automate Manual Processes</h3>
                <p>Create bots that perform repetitive tasks quickly and accurately. Eliminate human error and accelerate task completion.</p>
            </div>
            <div class="automation-card">
                <h3>Increase Efficiency</h3>
                <p>Free up employees to focus on strategic, high-value work. Reduce operational costs while improving productivity.</p>
            </div>
            <div class="automation-card">
                <h3>Scalable Automation</h3>
                <p>Deploy RPA bots across your organization regardless of scale. Manage thousands of processes simultaneously.</p>
            </div>
            <div class="automation-card">
                <h3>24/7 Operations</h3>
                <p>Bots work around the clock without fatigue or breaks. Ensure consistent processing across all time zones.</p>
            </div>
            <div class="automation-card">
                <h3>Minimal Learning Curve</h3>
                <p>Easy-to-use platform requires minimal technical expertise. Business users can build and manage automations.</p>
            </div>
            <div class="automation-card">
                <h3>Real-Time Monitoring</h3>
                <p>Track bot performance, success rates, and process metrics. Identify bottlenecks and optimize workflows.</p>
            </div>
        </div>

        <h2 style="margin-top: 50px;">Automation Capabilities</h2>
        <div class="automation-grid">
            <div class="process-item">
                <h4>Data Entry & Migration</h4>
                <p>Automate high-volume data entry and migration between systems</p>
            </div>
            <div class="process-item">
                <h4>Report Generation</h4>
                <p>Generate, format, and distribute reports automatically and on schedule</p>
            </div>
            <div class="process-item">
                <h4>Invoice Processing</h4>
                <p>Extract, validate, and process invoices with near-perfect accuracy</p>
            </div>
            <div class="process-item">
                <h4>Transaction Processing</h4>
                <p>Handle financial transactions and reconciliations automatically</p>
            </div>
            <div class="process-item">
                <h4>Customer Onboarding</h4>
                <p>Automate verification, documentation, and account setup processes</p>
            </div>
            <div class="process-item">
                <h4>System Integration</h4>
                <p>Connect and synchronize data across multiple business systems</p>
            </div>
        </div>

        <h2 style="margin-top: 50px;">Key Benefits</h2>
        <div class="benefit-grid">
            <div class="benefit-card">
                <h4>Cost Reduction</h4>
                <p>Reduce operational costs by 30-50% through process automation and efficiency gains</p>
            </div>
            <div class="benefit-card">
                <h4>Error Elimination</h4>
                <p>Achieve near-zero error rates and ensure consistent, compliant processing</p>
            </div>
            <div class="benefit-card">
                <h4>Faster Turnaround</h4>
                <p>Complete tasks in hours instead of days or weeks, improving customer satisfaction</p>
            </div>
            <div class="benefit-card">
                <h4>Employee Satisfaction</h4>
                <p>Remove tedious, repetitive work and let employees focus on meaningful tasks</p>
            </div>
            <div class="benefit-card">
                <h4>Compliance Assurance</h4>
                <p>Maintain audit trails and ensure complete regulatory compliance automatically</p>
            </div>
            <div class="benefit-card">
                <h4>Rapid Deployment</h4>
                <p>Implement automation projects in weeks, not months—start seeing ROI quickly</p>
            </div>
        </div>

        <div class="cta-section">
            <h3 class="text-light">Ready to Automate Your Processes?</h3>
            <p class="text-light">Transform your operations with enterprise-grade Robotic Process Automation</p>
            <div class="cta-buttons">
                <a href="#consultation" class="btn-primary-cta">Schedule Demo</a>
                <a href="#contact" class="btn-secondary-cta">Learn More</a>
            </div>
        </div>
    </div>
</div>
