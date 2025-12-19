<style>
.virtualagents-hero {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.virtualagents-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: -1;
}
.virtualagents-hero h1 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 15px;
}
.virtualagents-hero p {
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}
.virtualagents-section {
    padding: 60px 0;
}
.virtualagents-section h2 {
    color: #2f5597;
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 40px;
    text-align: center;
}
.agent-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border-left: 5px solid #2f5597;
}
.agent-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(47, 85, 151, 0.2);
}
.agent-card h3 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 1.25rem;
}
.agent-card p {
    color: #555;
    line-height: 1.6;
    font-size: 0.95rem;
}
.agent-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}
.capability-item {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    text-align: center;
    transition: all 0.3s ease;
}
.capability-item:hover {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    transform: scale(1.05);
}
.capability-item h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 10px;
    font-size: 1.1rem;
}
.capability-item:hover h4 {
    color: white;
}
.capability-item p {
    font-size: 0.9rem;
    margin: 0;
}
.use-case-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-top: 4px solid #2f5597;
    transition: all 0.3s ease;
}
.use-case-card:hover {
    box-shadow: 0 6px 14px rgba(47, 85, 151, 0.15);
    transform: translateY(-3px);
}
.use-case-card h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 12px;
}
.use-case-card p {
    color: #555;
    font-size: 0.9rem;
    line-height: 1.5;
}
.use-case-grid {
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
    .virtualagents-hero h1 {
        font-size: 1.8rem;
    }
    .virtualagents-hero p {
        font-size: 1rem;
    }
    .agent-grid {
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

<div class="virtualagents-hero">
    <h1 class="text-light">Power Virtual Agents</h1>
    <p class="text-light">Engage Customers with Intelligent AI-Driven Chatbots - No Coding Required</p>
</div>

<div class="container-fluid virtualagents-section">
    <div class="container">
        <h2>Create Smart Chatbots to Enhance Customer Engagement</h2>
        <p style="text-align: center; color: #555; font-size: 1.05rem; max-width: 800px; margin: 0 auto 40px;">Power Virtual Agents allows you to create powerful AI-driven chatbots that can interact with customers, answer questions, and resolve issues—all without the need for coding. Deploy across multiple channels for consistent, 24/7 support.</p>

        <div class="agent-grid">
            <div class="agent-card">
                <h3>No-Code Bot Building</h3>
                <p>Design sophisticated chatbots with a simple drag-and-drop interface. No programming knowledge required—just intuitive visual design.</p>
            </div>
            <div class="agent-card">
                <h3>AI-Powered Conversations</h3>
                <p>Utilize advanced AI to understand and respond to customer inquiries effectively. Natural language processing ensures meaningful interactions.</p>
            </div>
            <div class="agent-card">
                <h3>Omni-Channel Deployment</h3>
                <p>Deploy your chatbot on websites, Microsoft Teams, social media, and messaging platforms for consistent customer experience.</p>
            </div>
            <div class="agent-card">
                <h3>Multi-Language Support</h3>
                <p>Reach global audiences with built-in multi-language capabilities. Serve customers in their preferred language automatically.</p>
            </div>
            <div class="agent-card">
                <h3>Intelligent Escalation</h3>
                <p>Automatically route complex issues to human agents. Seamless handoff ensures customers always get the help they need.</p>
            </div>
            <div class="agent-card">
                <h3>Real-Time Analytics</h3>
                <p>Track bot performance, customer satisfaction, and conversation metrics. Data-driven insights for continuous improvement.</p>
            </div>
        </div>

        <h2 style="margin-top: 50px;">Key Capabilities</h2>
        <div class="agent-grid">
            <div class="capability-item">
                <h4>Conversation Design</h4>
                <p>Build complex conversation flows with conditional logic and branching scenarios</p>
            </div>
            <div class="capability-item">
                <h4>Entity Recognition</h4>
                <p>Extract relevant information from user input automatically and intelligently</p>
            </div>
            <div class="capability-item">
                <h4>Integration Enabled</h4>
                <p>Connect with CRM, databases, and business systems for seamless operations</p>
            </div>
            <div class="capability-item">
                <h4>Sentiment Analysis</h4>
                <p>Understand customer mood and adjust responses appropriately in real-time</p>
            </div>
            <div class="capability-item">
                <h4>Bot Analytics</h4>
                <p>Comprehensive dashboards for monitoring bot health and performance metrics</p>
            </div>
            <div class="capability-item">
                <h4>Security & Compliance</h4>
                <p>Enterprise-grade security with data encryption and compliance management</p>
            </div>
        </div>

        <h2 style="margin-top: 50px;">Use Cases</h2>
        <div class="use-case-grid">
            <div class="use-case-card">
                <h4>Customer Support</h4>
                <p>Handle common inquiries 24/7, reduce support costs, and improve response times dramatically</p>
            </div>
            <div class="use-case-card">
                <h4>Lead Qualification</h4>
                <p>Automatically qualify and route leads to sales teams, improving conversion rates</p>
            </div>
            <div class="use-case-card">
                <h4>FAQ Automation</h4>
                <p>Answer frequently asked questions instantly without human intervention</p>
            </div>
            <div class="use-case-card">
                <h4>Order Processing</h4>
                <p>Guide customers through ordering process and provide instant order status updates</p>
            </div>
            <div class="use-case-card">
                <h4>Employee Assistance</h4>
                <p>Provide internal employees with instant access to HR policies and procedures</p>
            </div>
            <div class="use-case-card">
                <h4>Appointment Scheduling</h4>
                <p>Allow customers to book appointments directly through conversational interface</p>
            </div>
        </div>

        <div class="cta-section">
            <h3 class="text-light">Ready to Transform Customer Engagement?</h3>
            <p class="text-light"> Start creating intelligent chatbots today with Power Virtual Agents</p>
            <div class="cta-buttons">
                <a href="#consultation" class="btn-primary-cta">Schedule Demo</a>
                <a href="#contact" class="btn-secondary-cta">Learn More</a>
            </div>
        </div>
    </div>
</div>
