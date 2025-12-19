<style>
.pocai-hero {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.pocai-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: -1;
}
.pocai-hero h1 {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 15px;
}
.pocai-hero p {
    font-size: 1.1rem;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}
.pocai-section {
    padding: 60px 0;
}
.pocai-section h2 {
    color: #2f5597;
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 40px;
    text-align: center;
}
.intro-text {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 40px;
    border-left: 5px solid #2f5597;
}
.intro-text p {
    color: #555;
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 15px;
}
.intro-text p:last-child {
    margin-bottom: 0;
}
.value-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border-left: 5px solid #2f5597;
}
.value-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(47, 85, 151, 0.2);
}
.value-card h3 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 1.25rem;
}
.value-card p {
    color: #555;
    line-height: 1.6;
    font-size: 0.95rem;
}
.value-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}
.implementation-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-top: 4px solid #2f5597;
    transition: all 0.3s ease;
}
.implementation-card:hover {
    box-shadow: 0 6px 14px rgba(47, 85, 151, 0.15);
    transform: translateY(-3px);
}
.implementation-card h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 12px;
}
.implementation-card p {
    color: #555;
    font-size: 0.9rem;
    line-height: 1.5;
}
.implementation-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}
.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 30px;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 30px;
    height: 30px;
    background: #2f5597;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}
.timeline-item h4 {
    color: #2f5597;
    font-weight: bold;
    margin-bottom: 8px;
}
.timeline-item p {
    color: #555;
    font-size: 0.95rem;
    line-height: 1.5;
}
.timeline-step-1::before {
    content: '1';
}
.timeline-step-2::before {
    content: '2';
}
.timeline-step-3::before {
    content: '3';
}
.timeline-step-4::before {
    content: '4';
}
.timeline-step-5::before {
    content: '5';
}
.feature-grid {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    padding: 40px;
    border-radius: 8px;
    color: white;
    margin: 40px 0;
}
.feature-grid h3 {
    color: white;
    margin-bottom: 30px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
}
.feature-item {
    padding: 15px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
}
.feature-item:last-child {
    border-bottom: none;
}
.feature-item::before {
    content: '✓';
    font-weight: bold;
    margin-right: 15px;
    font-size: 1.2rem;
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
    .pocai-hero h1 {
        font-size: 1.8rem;
    }
    .pocai-hero p {
        font-size: 1rem;
    }
    .value-grid {
        grid-template-columns: 1fr;
    }
    .implementation-grid {
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

<div class="pocai-hero">
    <h1  class="text-light">AI Proof of Concept Starter Pack</h1>
    <p class="text-light">See AI in Action with Zero Risk and Zero Budget</p>
</div>

<div class="container-fluid pocai-section">
    <div class="container">
        <h2>Ready to Experience AI's Real Impact?</h2>
        
        <div class="intro-text">
            <p>Have you been wondering how AI could actually help your business? It's time to stop wondering and start seeing for yourself! Our AI Proof of Concept (PoC) Starter Pack is your friendly, fast-track ticket to understanding how AI can make a real difference, specifically for what you do.</p>
            <p>We get it – jumping into AI can seem like a huge leap. That's exactly why we made this Starter Pack super easy to try out. It's low risk, packed with potential, and designed to give you awesome insights. You'll get a clear, hands-on look at AI solving a specific challenge in your company, all with ZERO commitment and ZERO budget required.</p>
        </div>

        <h2 style="margin-top: 50px;">What You'll Gain</h2>
        <div class="value-grid">
            <div class="value-card">
                <h3>See Immediate Value</h3>
                <p>Witness AI solve a real problem for your business in weeks, not months. Get tangible results quickly to demonstrate ROI.</p>
            </div>
            <div class="value-card">
                <h3>De-Risk Your AI Journey</h3>
                <p>Test the waters with a clear scope and predefined outcomes, minimizing uncertainty and building confidence.</p>
            </div>
            <div class="value-card">
                <h3>Gain Actionable Insights</h3>
                <p>Understand exactly how AI can optimize your operations, boost efficiency, or unlock new opportunities specific to your needs.</p>
            </div>
            <div class="value-card">
                <h3>Empower Your Team</h3>
                <p>Provide concrete examples of AI's capabilities, fostering internal buy-in, adoption, and innovation across your organization.</p>
            </div>
            <div class="value-card">
                <h3>Build Strong Foundations</h3>
                <p>Lay the groundwork for future, larger-scale AI initiatives with proven approaches and documented best practices.</p>
            </div>
            <div class="value-card">
                <h3>Competitive Advantage</h3>
                <p>Get ahead of competitors by understanding and implementing AI solutions proven to drive business value.</p>
            </div>
        </div>

        <div class="feature-grid">
            <h3 class="text-light">Starter Pack Includes</h3>
            <div class="feature-item">Discovery Session: Identify your highest-impact AI opportunity</div>
            <div class="feature-item">Scoped Project Definition: Clear objectives and measurable outcomes</div>
            <div class="feature-item">4-Week Implementation: Rapid prototype and proof of concept</div>
            <div class="feature-item">Expert Team: Access to AI specialists and solution architects</div>
            <div class="feature-item">Full Documentation: Detailed findings and implementation roadmap</div>
            <div class="feature-item">Presentation & Demo: Executive summary with live proof of concept</div>
        </div>

        <h2>Implementation Timeline</h2>
        <div style="max-width: 800px; margin: 0 auto 50px;">
            <div class="timeline-item timeline-step-1">
                <h4>Week 1: Discovery & Planning</h4>
                <p>Meet with your team to understand business challenges, current processes, and AI opportunities. Define project scope and success metrics.</p>
            </div>
            <div class="timeline-item timeline-step-2">
                <h4>Week 2: Solution Design</h4>
                <p>Our AI experts design a tailored solution addressing your specific needs. Create detailed implementation plan and data strategy.</p>
            </div>
            <div class="timeline-item timeline-step-3">
                <h4>Week 3: Development & Testing</h4>
                <p>Build the proof of concept with real data and processes. Test thoroughly to ensure accuracy and reliability.</p>
            </div>
            <div class="timeline-item timeline-step-4">
                <h4>Week 4: Refinement & Documentation</h4>
                <p>Fine-tune the solution based on feedback. Create comprehensive documentation and implementation guide for your team.</p>
            </div>
            <div class="timeline-item timeline-step-5">
                <h4>Week 4: Presentation & Handoff</h4>
                <p>Present findings to leadership with live demo. Provide roadmap for full-scale implementation with cost-benefit analysis.</p>
            </div>
        </div>

        <h2>Perfect For These Scenarios</h2>
        <div class="implementation-grid">
            <div class="implementation-card">
                <h4>Process Automation</h4>
                <p>Automate manual, repetitive tasks to improve efficiency and reduce costs dramatically</p>
            </div>
            <div class="implementation-card">
                <h4>Predictive Analytics</h4>
                <p>Forecast trends, demand, or risks using historical data patterns and AI insights</p>
            </div>
            <div class="implementation-card">
                <h4>Customer Intelligence</h4>
                <p>Understand customer behavior and preferences to improve targeting and personalization</p>
            </div>
            <div class="implementation-card">
                <h4>Document Processing</h4>
                <p>Extract insights from large volumes of documents, contracts, and records automatically</p>
            </div>
            <div class="implementation-card">
                <h4>Quality Assurance</h4>
                <p>Use AI to detect defects, anomalies, or quality issues with higher accuracy</p>
            </div>
            <div class="implementation-card">
                <h4>Revenue Optimization</h4>
                <p>Optimize pricing, recommendations, and upselling strategies with AI-driven insights</p>
            </div>
        </div>

        <div class="cta-section">
            <h3  class="text-light">Start Your AI Journey Today</h3>
            <p  class="text-light">See concrete results in just 4 weeks with our AI Proof of Concept Starter Pack</p>
            <div class="cta-buttons">
                <a href="#consultation" class="btn-primary-cta">Schedule Free Discovery</a>
                <a href="#contact" class="btn-secondary-cta">Get Details</a>
            </div>
        </div>
    </div>
</div>
      <h4>Just imagine…</h4>
      <div class="boxed-list">
        <div class="boxed-item">Quickly draft personalized internal communications, summarize documents, or generate training materials from your secure, internal data.</div>
        <div class="boxed-item">Automate tedious manual processes like sorting inquiries or processing reports, freeing your team for strategic tasks, all without exposing sensitive data externally.</div>
        <div class="boxed-item">Power a smart internal chatbot with AI to answer HR questions or provide policy access, all from your internal knowledge base.</div>
        <div class="boxed-item">Streamlining data entry and validation for critical internal systems with Intelligent Automation, slashing errors and speeding up workflows, all while keeping your data strictly confidential.</div>
      </div>
    </div>

    <div class="section text-center">
      <p class="fs-5">This isn't a demo; it's a personalized exploration of AI's potential for your business.</p>
      <a href="{{ route('contact') }}?subject=Request+your+AI+Proof+of+Concept+Starter+Pack" class="btn btn-lg text-white cta-btn mt-3  default-background" style="color: white !important;">Click here to request your AI Proof of Concept Starter Pack</a>
    </div>
  </div>
