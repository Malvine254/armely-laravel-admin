<style>
/* ============================
   MICROSOFT MODERN PAGE STYLING
   ============================ */

/* Brand Colors */
:root {
    --ms-blue: #2f5597;
    --ms-dark: #1e3a6d;
    --ms-soft-dark: #2c3248;
    --ms-light: #f5f8fc;
    --ms-accent: #2f5597;
}

/* Page container spacing */
.ms-section {
    padding: 40px 0;
}

/* Titles */
.section-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--ms-dark);
    margin-bottom: 1.2rem;
    letter-spacing: -0.5px;
}

.ms-list-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--ms-blue);
    margin-top: 40px;
}

/* Lead text */
.partner-lead {
    font-size: 1.05rem;
    line-height: 1.65;
    color: #4b5563;
}

/* ============================
   CARD STYLE SECTIONS
   ============================ */
.ms-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 35px 40px;
    margin-bottom: 40px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.06);
    transition: .3s;
}
.ms-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
}

/* ----------------------------
   UL LIST MODERNIZATION
   ---------------------------- */
.ms-list {
    padding-left: 22px;
}
.ms-list li {
    margin-bottom: 6px;
    font-size: 1rem;
    line-height: 1.55;
}

/* ----------------------------
   Badge Styling
   ---------------------------- */
.partner-badge {
    background: var(--ms-blue);
    color: #fff;
    padding: 8px 14px;
    border-radius: 30px;
    margin: 5px;
    font-weight: 600;
    display: inline-block;
    font-size: 0.85rem;
}

/* ----------------------------
   CTA BUTTON
   ---------------------------- */
.btn-cta {
    background: #2f5597;
    color: #ffffff !important;
    padding: 13px 32px;
    font-size: 1.05rem;
    font-weight: 600;
    border-radius: 50px;
    text-decoration: none;
    transition: 0.3s;
}
.btn-cta:hover {
    background: #1e3a6d;
    transform: translateY(-3px);
}

/* ----------------------------
   Hero Image + Branding
   ---------------------------- */
.partner-brand-logo {
    max-width: 220px;
    filter: drop-shadow(0px 4px 10px rgba(0,0,0,0.1));
}

/* ----------------------------
   Section Divider line
   ---------------------------- */
hr {
    border: none;
    height: 0;
    background: transparent;
}

/* ----------------------------
   Gradient Highlight Wrapper
   ---------------------------- */
.ms-highlight {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
    color: #fff;
    border-radius: 14px;
    padding: 24px 26px;
    margin-bottom: 40px;
    box-shadow: 0 12px 32px rgba(47,85,151,0.2);
}

/* ----------------------------
   Responsive Tweaks
   ---------------------------- */
@media(max-width: 768px){
    .ms-card { padding: 25px 22px; }
    .section-title { font-size: 1.65rem; }
}

/* ===== Full-width Partner Hero ===== */
.partner-hero {
    background: linear-gradient(135deg, #1e3a6d 0%, #2f5597 55%, #1e3a6d 100%);
    color: #fff;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}
.partner-hero::before {
    content: '';
    position: absolute;
    top: -25%;
    right: -25%;
    width: 60%;
    height: 160%;
    background: radial-gradient(closest-side, rgba(255,255,255,0.12), rgba(255,255,255,0));
    filter: blur(30px);
}
.hero-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.hero-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center; }
.hero-title { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 800; line-height: 1.15; margin-bottom: 18px; color: #fff; }
.hero-sub { font-size: 1.1rem; line-height: 1.8; opacity: 0.95; color: #fff; }
.hero-ctas { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 24px; }
.hero-cta { background: transparent; color: #ffffff; border: 1.6px solid rgba(255,255,255,0.6); border-radius: 999px; padding: 12px 18px; font-weight: 700; box-shadow: 0 6px 18px rgba(0,0,0,0.2); text-decoration: none; }
.hero-cta:hover { background: rgba(255,255,255,0.12); }
.hero-logo { max-width: 260px; filter: drop-shadow(0 12px 32px rgba(0,0,0,0.4)) drop-shadow(0 4px 12px rgba(0,0,0,0.3)); opacity: 0.95; transition: all 0.3s ease; border-radius: 16px; }
@media(max-width: 992px){ .hero-grid { grid-template-columns: 1fr; text-align: center; } .hero-logo { margin: 20px auto 0; } }
.container { padding: 50px 24px; }

</style>

<!-- Full-width Hero -->
<section class="partner-hero">
    <div class="hero-inner">
        <div class="hero-grid">
            <div>
                <h1 class="hero-title">Transform. Innovate. Accelerate.</h1>
                <p class="hero-sub">Your business deserves technology that works as hard as you do. As a certified Microsoft Solutions Partner across multiple designations, we deliver comprehensive solutions that drive measurable business outcomes.</p>
                <div class="hero-ctas">
                    <a class="hero-cta" href="#solutions">Solutions Partner</a>
                    <a class="hero-cta" href="#dynamics">Dynamics 365</a>
                    <a class="hero-cta" href="/contact">Start Transforming</a>
                </div>
            </div>
            <div class="text-center">
                <img class="hero-logo" alt="Microsoft Logo" src="/images/partners/ms.png">
            </div>
        </div>
    </div>
</section>

<div class="container">

<h3 class="section-title mt-4">Why Partner with Us?</h3>
<p class="partner-lead">
    Microsoft Solutions Partners represent the highest level of capability, expertise, and commitment within the
    Microsoft ecosystem. Our multi-designation status demonstrates proven success across the full Microsoft technology
    stack and our ability to deliver end-to-end business transformation.
</p>
<p class="partner-lead">
    We meet you where you are—modernizing legacy systems, implementing Dynamics 365, building AI-powered solutions, or
    optimizing Microsoft licensing investments. We align technology with your strategic objectives to accelerate growth,
    enhance efficiency, and create competitive advantage.
</p>

<h4 class="ms-list-title mt-4">Our Comprehensive Approach</h4>
<p class="partner-lead">
    From strategy and assessment through implementation, adoption, and managed services, we deliver integrated solutions
    that maximize your Microsoft technology investment and drive lasting business value.
</p>

<h2 class="section-title">Microsoft Solutions Partner Designations</h2>
<p class="partner-lead">
    Our comprehensive Microsoft Solutions Partner designations demonstrate deep expertise across the entire Microsoft
    Cloud ecosystem, enabling us to deliver unified, end-to-end solutions tailored to your business needs.
</p>

<!-- ====================== DATA & AI ====================== -->
<h3 class="ms-list-title">Data & AI (Azure)</h3>
<p class="partner-lead">Unlock the power of your data and accelerate AI adoption.</p>

<ul class="ms-list">
    <li>Data platform modernization and migration to Azure</li>
    <li>Microsoft Fabric implementation and data lakehouse architecture</li>
    <li>Advanced analytics with Power BI and Azure Synapse Analytics</li>
    <li>AI and machine learning solutions with Azure OpenAI Service</li>
    <li>Data governance, security, and compliance frameworks</li>
    <li>Real-time analytics and streaming data solutions</li>
</ul>

<!-- ====================== DIGITAL & APP INNOVATION ====================== -->
<h3 class="ms-list-title\">Digital & App Innovation (Azure)</h3>
<p class="partner-lead">Modernize applications and accelerate innovation.</p>

<ul class="ms-list">
    <li>Application modernization and cloud migration</li>
    <li>Cloud-native app development with Azure services</li>
    <li>Microservices architecture and containerization</li>
    <li>DevOps transformation and CI/CD automation</li>
    <li>API management and enterprise integration</li>
    <li>Low-code/no-code development with Power Platform</li>
</ul>

<!-- ====================== BUSINESS APPLICATIONS ====================== -->
<style>
.dynamics-section {
    padding: 50px 0;
}

.dynamics-header {
    text-align: center;
    margin-bottom: 50px;
}

.dynamics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 28px;
    margin-bottom: 40px;
}

.dynamics-card {
    background: linear-gradient(135deg, #f5f8fc 0%, #ffffff 100%);
    border: 1px solid rgba(47, 85, 151, 0.1);
    border-radius: 14px;
    padding: 32px;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.dynamics-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2f5597, #2f5597);
    transition: height 0.35s ease;
}

.dynamics-card:hover {
    transform: translateY(-8px);
    border-color: #2f5597;
    box-shadow: 0 16px 48px rgba(47, 85, 151, 0.15);
}

.dynamics-card:hover::before {
    height: 6px;
}

.dynamics-icon {
    font-size: 2.4rem;
    margin-bottom: 14px;
    color: var(--ms-blue);
}

.dynamics-card-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--ms-dark);
    margin-bottom: 14px;
    line-height: 1.4;
}

.dynamics-card-desc {
    font-size: 0.95rem;
    color: #4b5563;
    line-height: 1.6;
    margin-bottom: 16px;
}

.dynamics-features {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dynamics-features li {
    font-size: 0.9rem;
    color: #5a6270;
    margin-bottom: 8px;
    padding-left: 22px;
    position: relative;
    line-height: 1.5;
}

.dynamics-features li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #2f5597;
    font-weight: bold;
    font-size: 1.1rem;
    filter: drop-shadow(0px 2px 4px rgba(47,85,151,0.3));
}
}
@media (max-width: 768px) {
    .dynamics-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<hr class="my-5">
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Business Applications (Dynamics 365)</h3>
        <p class="partner-lead">Unify business operations with intelligent applications powered by AI and automation.</p>
    </div>

    <div class="dynamics-grid">
        <!-- Sales -->
        <div class="dynamics-card">
            <div class="dynamics-icon">📊</div>
            <h4 class="dynamics-card-title">Dynamics 365 Sales</h4>
            <p class="dynamics-card-desc">Accelerate sales cycles and close deals faster.</p>
            <ul class="dynamics-features">
                <li>Lead & opportunity management</li>
                <li>Sales forecasting and analytics</li>
                <li>Predictive insights and Copilot</li>
                <li>LinkedIn Sales Navigator integration</li>
            </ul>
        </div>

        <!-- Customer Service -->
        <div class="dynamics-card">
            <div class="dynamics-icon">💬</div>
            <h4 class="dynamics-card-title">Dynamics 365 Customer Service</h4>
            <p class="dynamics-card-desc">Deliver exceptional service across all channels.</p>
            <ul class="dynamics-features">
                <li>Omnichannel service</li>
                <li>AI-powered case management</li>
                <li>Knowledge management & self-service</li>
                <li>Copilot for agent productivity</li>
            </ul>
        </div>

        <!-- Field Service -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🛠️</div>
            <h4 class="dynamics-card-title">Dynamics 365 Field Service</h4>
            <p class="dynamics-card-desc">Optimize field operations with intelligent scheduling.</p>
            <ul class="dynamics-features">
                <li>Intelligent scheduling & dispatch</li>
                <li>IoT-enabled predictive maintenance</li>
                <li>HoloLens / Remote Assist integration</li>
            </ul>
        </div>

        <!-- Finance -->
        <div class="dynamics-card">
            <div class="dynamics-icon">💰</div>
            <h4 class="dynamics-card-title">Dynamics 365 Finance</h4>
            <p class="dynamics-card-desc">Gain real-time financial visibility and control.</p>
            <ul class="dynamics-features">
                <li>Financial reporting & analytics</li>
                <li>Automation of AP/AR</li>
                <li>Multi-currency & multi-entity operations</li>
            </ul>
        </div>

        <!-- Supply Chain -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🚚</div>
            <h4 class="dynamics-card-title">Dynamics 365 Supply Chain Management</h4>
            <p class="dynamics-card-desc">Build resilient and responsive supply chains.</p>
            <ul class="dynamics-features">
                <li>Master planning and forecasting</li>
                <li>Warehouse & inventory automation</li>
                <li>Manufacturing execution systems</li>
            </ul>
        </div>

        <!-- Business Central -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🏢</div>
            <h4 class="dynamics-card-title">Dynamics 365 Business Central</h4>
            <p class="dynamics-card-desc">Complete ERP solution for SMBs.</p>
            <ul class="dynamics-features">
                <li>All-in-one SMB ERP platform</li>
                <li>Financials, sales, projects, inventory</li>
                <li>Microsoft 365 & Power Platform integration</li>
            </ul>
        </div>

        <!-- Customer Insights -->
        <div class="dynamics-card">
            <div class="dynamics-icon">👥</div>
            <h4 class="dynamics-card-title">Customer Insights</h4>
            <p class="dynamics-card-desc">Create unified customer experiences.</p>
            <ul class="dynamics-features">
                <li>Unified customer profiles</li>
                <li>Predictive analytics & AI segmentation</li>
                <li>Journey orchestration & engagement</li>
            </ul>
        </div>
    </div>
</div>

<!-- ====================== FABRIC / AI / ANALYTICS ====================== -->
<hr class="my-5">
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Data & AI Solutions</h3>
        <p class="partner-lead">Transform data into actionable intelligence with Microsoft's leading AI and analytics platforms.</p>
    </div>
    
    <div class="dynamics-grid">
        <!-- Fabric -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🏗️</div>
            <h4 class="dynamics-card-title">Microsoft Fabric</h4>
            <p class="dynamics-card-desc">Unified analytics and data platform.</p>
            <ul class="dynamics-features">
                <li>OneLake lakehouse implementation</li>
                <li>Data Factory pipelines & integration</li>
                <li>Synapse Data Engineering & Warehouse</li>
                <li>Power BI unified analytics</li>
                <li>Real-time analytics</li>
            </ul>
        </div>
        
        <!-- Azure AI -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🤖</div>
            <h4 class="dynamics-card-title">Azure AI & OpenAI</h4>
            <p class="dynamics-card-desc">Enterprise AI solution development.</p>
            <ul class="dynamics-features">
                <li>Custom Copilots with Copilot Studio</li>
                <li>Document intelligence & vision</li>
                <li>Conversational AI & chatbots</li>
                <li>Responsible AI governance</li>
                <li>Prompt engineering & optimization</li>
            </ul>
        </div>
        
        <!-- Power BI -->
        <div class="dynamics-card">
            <div class="dynamics-icon">📈</div>
            <h4 class="dynamics-card-title">Power BI & Analytics</h4>
            <p class="dynamics-card-desc">Enterprise business intelligence.</p>
            <ul class="dynamics-features">
                <li>Enterprise BI implementation</li>
                <li>Data modeling & DAX optimization</li>
                <li>Real-time dashboards</li>
                <li>Embedded analytics</li>
                <li>AI-powered insights</li>
            </ul>
        </div>
        
        <!-- Data Governance -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🔐</div>
            <h4 class="dynamics-card-title">Data Governance</h4>
            <p class="dynamics-card-desc">Governance, compliance & security.</p>
            <ul class="dynamics-features">
                <li>Microsoft Purview governance</li>
                <li>Data classification & labeling</li>
                <li>DLP automation</li>
                <li>Data lineage tracking</li>
                <li>Catalog management</li>
            </ul>
        </div>
    </div>

</div>

<!-- ====================== COPILOT ====================== -->
<hr class="my-5">
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Microsoft Copilot Solutions</h3>
        <p class="partner-lead">Accelerate productivity with AI-powered Copilot implementations across your organization.</p>
    </div>
    
    <div class="dynamics-grid">
        <!-- Microsoft 365 Copilot -->
        <div class="dynamics-card">
            <div class="dynamics-icon">💼</div>
            <h4 class="dynamics-card-title">Microsoft 365 Copilot</h4>
            <p class="dynamics-card-desc">AI assistant for Microsoft 365 apps.</p>
            <ul class="dynamics-features">
                <li>Readiness assessment</li>
                <li>Infrastructure & security prep</li>
                <li>Prompt engineering & training</li>
                <li>Adoption programs & analytics</li>
            </ul>
        </div>
        
        <!-- Dynamics 365 Copilot -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🎯</div>
            <h4 class="dynamics-card-title">Dynamics 365 Copilot</h4>
            <p class="dynamics-card-desc">AI-powered business apps.</p>
            <ul class="dynamics-features">
                <li>Sales Copilot</li>
                <li>Service Copilot</li>
                <li>Marketing Copilot</li>
                <li>Finance & Supply Chain Copilot</li>
            </ul>
        </div>
        
        <!-- Custom Copilots -->
        <div class="dynamics-card">
            <div class="dynamics-icon">✨</div>
            <h4 class="dynamics-card-title">Custom Copilots</h4>
            <p class="dynamics-card-desc">Tailored AI solutions for your business.</p>
            <ul class="dynamics-features">
                <li>Copilot Studio development</li>
                <li>Custom connectors & plugins</li>
                <li>Enterprise knowledge integration</li>
                <li>AI workflow orchestration</li>
            </ul>
        </div>
    </div>

</div>

<!-- ====================== LICENSING ====================== -->
<hr class="my-5">
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Microsoft Licensing Expertise</h3>
        <p class="partner-lead">Optimize your Microsoft investments and ensure compliance with expert licensing guidance.</p>
    </div>
    
    <div class="dynamics-grid">
        <!-- Licensing Services -->
        <div class="dynamics-card">
            <div class="dynamics-icon">📋</div>
            <h4 class="dynamics-card-title">Licensing Services</h4>
            <p class="dynamics-card-desc">Strategic license management.</p>
            <ul class="dynamics-features">
                <li>Microsoft 365 & Dynamics 365 optimization</li>
                <li>Azure consumption & FinOps</li>
                <li>EA guidance and CSP services</li>
                <li>License compliance & audits</li>
            </ul>
        </div>
        
        <!-- Cost Optimization -->
        <div class="dynamics-card">
            <div class="dynamics-icon">💵</div>
            <h4 class="dynamics-card-title">Cost Optimization</h4>
            <p class="dynamics-card-desc">Reduce your cloud spend.</p>
            <ul class="dynamics-features">
                <li>Reserved Instances optimization</li>
                <li>Savings Plans strategy</li>
                <li>Unused license reclamation</li>
                <li>Budget planning & forecasting</li>
            </ul>
        </div>
        
        <!-- Compliance & Governance -->
        <div class="dynamics-card">
            <div class="dynamics-icon">✅</div>
            <h4 class="dynamics-card-title">Compliance & Governance</h4>
            <p class="dynamics-card-desc">Stay audit-ready and compliant.</p>
            <ul class="dynamics-features">
                <li>SAM program implementation</li>
                <li>Audit preparation & defense</li>
                <li>Governance frameworks</li>
                <li>Compliance monitoring</li>
            </ul>
        </div>
    </div>

</div>

<!-- ====================== DIGITAL TRANSFORMATION ====================== -->
<hr class="my-5">
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Digital Transformation Services</h3>
        <p class="text-center">End-to-end transformation journey from strategy through optimization and ongoing support.</p>
    </div>
    
    <div class="dynamics-grid">
        <!-- Strategy & Advisory -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🗺️</div>
            <h4 class="dynamics-card-title">Strategy & Advisory</h4>
            <p class="dynamics-card-desc">Chart your transformation path.</p>
            <ul class="dynamics-features">
                <li>Digital transformation roadmap</li>
                <li>Technology stack assessment</li>
                <li>Change management readiness</li>
                <li>Executive alignment workshops</li>
            </ul>
        </div>
        
        <!-- Implementation & Integration -->
        <div class="dynamics-card">
            <div class="dynamics-icon">⚙️</div>
            <h4 class="dynamics-card-title">Implementation & Integration</h4>
            <p class="dynamics-card-desc">Deploy with precision and confidence.</p>
            <ul class="dynamics-features">
                <li>Solution architecture & deployment</li>
                <li>Custom development & integrations</li>
                <li>UAT & quality assurance</li>
                <li>Go-live support</li>
            </ul>
        </div>
        
        <!-- Managed Services -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🔧</div>
            <h4 class="dynamics-card-title">Managed Services</h4>
            <p class="dynamics-card-desc">24/7 monitoring and optimization.</p>
            <ul class="dynamics-features">
                <li>24/7 monitoring & support</li>
                <li>Performance optimization</li>
                <li>Security updates & patching</li>
                <li>Health checks & reporting</li>
            </ul>
        </div>
        
        <!-- Training & Adoption -->
        <div class="dynamics-card">
            <div class="dynamics-icon">👨‍🎓</div>
            <h4 class="dynamics-card-title">Training & Adoption</h4>
            <p class="dynamics-card-desc">Drive user adoption and proficiency.</p>
            <ul class="dynamics-features">
                <li>Role-based learning programs</li>
                <li>Champions programs</li>
                <li>Adoption analytics & tracking</li>
                <li>Ongoing enablement</li>
            </ul>
        </div>
    </div>

</div>

<!-- ====================== INDUSTRIES ====================== -->
<hr class="my-5">
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Industry Solutions</h3>
        <p class="text-center">Specialized expertise across verticals with proven solutions.</p>
    </div>
    
    <div class="dynamics-grid">
        <div class="dynamics-card">
            <div class="dynamics-icon">🏭</div>
            <h4 class="dynamics-card-title">Manufacturing</h4>
            <ul class="dynamics-features">
                <li>Production planning</li>
                <li>Supply chain optimization</li>
                <li>IoT & predictive maintenance</li>
            </ul>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">🛒</div>
            <h4 class="dynamics-card-title">Retail & Consumer</h4>
            <ul class="dynamics-features">
                <li>Omnichannel commerce</li>
                <li>Customer analytics</li>
                <li>Inventory optimization</li>
            </ul>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">🏦</div>
            <h4 class="dynamics-card-title">Financial Services</h4>
            <ul class="dynamics-features">
                <li>Compliance & risk</li>
                <li>Customer analytics</li>
                <li>Fraud detection</li>
            </ul>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">⚕️</div>
            <h4 class="dynamics-card-title">Healthcare</h4>
            <ul class="dynamics-features">
                <li>Patient engagement</li>
                <li>Care coordination</li>
                <li>Analytics & insights</li>
            </ul>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">💼</div>
            <h4 class="dynamics-card-title">Professional Services</h4>
            <ul class="dynamics-features">
                <li>Project management</li>
                <li>Resource planning</li>
                <li>Client collaboration</li>
            </ul>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">🚚</div>
            <h4 class="dynamics-card-title">Distribution & Wholesale</h4>
            <ul class="dynamics-features">
                <li>Order management</li>
                <li>Warehouse automation</li>
                <li>B2B commerce</li>
            </ul>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">🏛️</div>
            <h4 class="dynamics-card-title">Government & Public</h4>
            <ul class="dynamics-features">
                <li>Citizen services</li>
                <li>Compliance & security</li>
                <li>Digital transformation</li>
            </ul>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">❤️</div>
            <h4 class="dynamics-card-title">Nonprofit</h4>
            <ul class="dynamics-features">
                <li>Donor management</li>
                <li>Program tracking</li>
                <li>Financial stewardship</li>
            </ul>
        </div>
    </div>

</div>

<!-- ====================== DIFFERENTIATORS ====================== -->
<hr class="my-5">
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Our Differentiators</h3>
        <p class="text-center">Why organizations choose us as their trusted Microsoft partner.</p>
    </div>
    
    <div class="dynamics-grid">
        <div class="dynamics-card">
            <div class="dynamics-icon">🏆</div>
            <h4 class="dynamics-card-title">Certified Professionals</h4>
            <p class="dynamics-card-desc">Microsoft-certified experts on staff.</p>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">📚</div>
            <h4 class="dynamics-card-title">Proven Methodology</h4>
            <p class="dynamics-card-desc">Battle-tested implementation approach.</p>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">🔗</div>
            <h4 class="dynamics-card-title">End-to-End Capabilities</h4>
            <p class="dynamics-card-desc">Full spectrum of services.</p>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">🎯</div>
            <h4 class="dynamics-card-title">Industry Expertise</h4>
            <p class="dynamics-card-desc">Deep vertical knowledge.</p>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">⭐</div>
            <h4 class="dynamics-card-title">Microsoft Partnership</h4>
            <p class="dynamics-card-desc">Premier partner status.</p>
        </div>
        <div class="dynamics-card">
            <div class="dynamics-icon">🚀</div>
            <h4 class="dynamics-card-title">Innovation-Focused</h4>
            <p class="dynamics-card-desc">Cutting-edge solutions.</p>
        </div>
    </div>

</div>

<!-- ====================== SUCCESS STORIES ====================== -->
<hr class="my-5">
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Success Stories & Recognition</h3>
        <p class="partner-lead">Recognized leadership and proven delivery track record.</p>
    </div>
    
    <style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    
.stat-card {
    background: linear-gradient(135deg, #2f5597, #1e3a6d);
    color: white;
    border-radius: 12px;
    padding: 28px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(47, 85, 151, 0.2);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-6px);
}

.stat-number {
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 8px;
    color: #ffffff;
}    .stat-label {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.5;
    }
    </style>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">500+</div>
            <div class="stat-label">Microsoft Implementations</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">800K+</div>
            <div class="stat-label">Users Supported</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">98%</div>
            <div class="stat-label">Customer Satisfaction</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">🏅</div>
            <div class="stat-label">Partner of Year Finalist</div>
        </div>
    </div>
</div>

<h3 class="section-title">Ready to Transform Your Business?</h3>
<p class="partner-lead">
    Connect with our Microsoft experts to modernize applications, unlock insights, enable AI, optimize licensing,
    strengthen security, and maximize your Microsoft investments.
</p>

<a href="/contact" class="btn btn-cta mb-5">Get Started Today</a>

<h3 class="section-title">Why Microsoft Cloud?</h3>

<ul class="ms-list">
    <li>Comprehensive platform spanning data, AI, apps, productivity, and security</li>
    <li>Industry-leading AI capabilities with Azure OpenAI Service</li>
    <li>Zero-Trust security and global compliance</li>
    <li>Global scale across 60+ Azure regions</li>
    <li>Continuous innovation with $20B+ R&D investment</li>
    <li>The world's largest enterprise ecosystem</li>
</ul>

<p class="partner-lead mt-4">
    As a multi-designation Microsoft Solutions Partner, we empower organizations of all sizes to drive measurable business
    outcomes through world-class technologies, deep expertise, and a customer-first approach.
</p>
