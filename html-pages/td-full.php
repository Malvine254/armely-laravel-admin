<style>
/* ============================
   TD SYNNEX MODERN PAGE STYLING
   ============================ */

:root {
    --td-blue: #0066b2;
    --td-dark: #003d71;
    --ms-dark: #1a1f36;
    --ms-light: #f5f8fc;
}

.ms-section { padding: 40px 0; }
.section-title { font-size: 2rem; font-weight: 800; color: var(--ms-dark); margin-bottom: 1.2rem; letter-spacing: -0.5px; }
.ms-list-title { font-size: 1.4rem; font-weight: 700; color: var(--td-blue); margin-top: 40px; }
.partner-lead { font-size: 1.05rem; line-height: 1.65; color: #4b5563; }
.ms-card { background: #ffffff; border-radius: 16px; padding: 35px 40px; margin-bottom: 40px; box-shadow: 0 8px 28px rgba(0,0,0,0.06); transition: .3s; }
.ms-card:hover { transform: translateY(-4px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
.ms-list { padding-left: 22px; }
.ms-list li { margin-bottom: 6px; font-size: 1rem; line-height: 1.55; padding-left: 22px; position: relative; }
.ms-list li::before { content: '▢'; position: absolute; left: 0; color: var(--td-blue); font-weight: bold; font-size: 1rem; }
.partner-badge { background: var(--td-blue); color: #fff; padding: 8px 14px; border-radius: 30px; margin: 5px; font-weight: 600; display: inline-block; font-size: 0.85rem; }
.btn-cta { background: var(--td-blue); color: #ffffff !important; padding: 13px 32px; font-size: 1.05rem; font-weight: 600; border-radius: 10px; text-decoration: none; transition: 0.3s; }
.btn-cta:hover { background: var(--td-dark); transform: translateY(-3px); }
.partner-brand-logo { max-width: 220px; filter: drop-shadow(0px 4px 10px rgba(0,0,0,0.1)); }
hr { border-color: #e5e7eb; }
.ms-highlight { background: linear-gradient(135deg, var(--td-blue) 0%, var(--td-dark) 100%); color: #fff; border-radius: 14px; padding: 24px 26px; margin-bottom: 40px; box-shadow: 0 12px 32px rgba(0,102,178,0.2); }
.modern-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 28px; margin-bottom: 40px; }
.modern-card { background: linear-gradient(135deg, #f5f8fc 0%, #ffffff 100%); border: 1px solid rgba(0, 102, 178, 0.1); border-radius: 14px; padding: 32px; transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; }
.modern-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--td-blue), var(--td-dark)); transition: height 0.35s ease; }
.modern-card:hover { transform: translateY(-8px); border-color: var(--td-blue); box-shadow: 0 16px 48px rgba(0, 102, 178, 0.15); }
.modern-card:hover::before { height: 6px; }
.card-icon { font-size: 2.4rem; margin-bottom: 14px; }
.card-title { font-size: 1.2rem; font-weight: 700; color: var(--ms-dark); margin-bottom: 14px; line-height: 1.4; }
.card-desc { font-size: 0.95rem; color: #4b5563; line-height: 1.6; margin-bottom: 16px; }
.card-features { list-style: none; padding: 0; margin: 0; }
.card-features li { font-size: 0.9rem; color: #5a6270; margin-bottom: 8px; padding-left: 22px; position: relative; line-height: 1.5; }
.card-features li::before { content: '▢'; position: absolute; left: 0; color: var(--td-blue); font-weight: bold; font-size: 1rem; }
.section-header { text-align: center; margin-bottom: 50px; }
.section-header .section-title { margin-bottom: 16px; }
.section-header .partner-lead { max-width: 600px; margin: 0 auto; color: #666; }
@media(max-width: 768px){ .ms-card { padding: 25px 22px; } .section-title { font-size: 1.65rem; } .modern-grid { grid-template-columns: 1fr; } }

/* ===== Full-width Partner Hero ===== */
.partner-hero {
    background: linear-gradient(135deg, #003d71 0%, #0066b2 55%, #0082d9 100%);
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
                <h1 class="hero-title">Connect. Simplify. Accelerate.</h1>
                <p class="hero-sub">As a TD SYNNEX Registered Partner, we connect you with the world's leading technology brands and solutions. From procurement and logistics to technical support and business development, we deliver the resources and expertise you need to grow your business.</p>
                <div class="hero-ctas">
                    <a class="hero-cta" href="#solutions">Partner Solutions</a>
                    <a class="hero-cta" href="#services">Value-Added Services</a>
                    <a class="hero-cta" href="/contact">Become a Partner</a>
                </div>
            </div>
            <div class="text-center">
                <img class="hero-logo" alt="TD SYNNEX Logo" src="/images/partners/td.png">
            </div>
        </div>
    </div>
</section>

<div class="container">

<h3 class="section-title">TD SYNNEX Partnership</h3>
<p class="partner-lead">TD SYNNEX is the world's leading IT distributor and solutions aggregator, connecting technology partners with customers across the globe. As a registered TD SYNNEX partner, we leverage their extensive portfolio, supply chain expertise, and value-added services to deliver comprehensive technology solutions that meet your business needs.</p>

<h4 class="ms-list-title mt-4">Why TD SYNNEX?</h4>
<p class="partner-lead">TD SYNNEX provides unparalleled access to technology vendors, streamlined procurement processes, and comprehensive support services that enable us to deliver the right solutions at the right time.</p>

<div class="modern-grid">
    <div class="modern-card">
        <div class="card-icon">🌐</div>
        <h5 class="card-title">Global Distribution Network</h5>
        <p class="card-desc">Access to the world's leading technology brands and solutions through TD SYNNEX's extensive global network.</p>
        <ul class="card-features">
            <li>150,000+ technology products</li>
            <li>1,500+ vendor partnerships</li>
            <li>Global logistics and fulfillment</li>
            <li>Local market expertise</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">⚡</div>
        <h5 class="card-title">Streamlined Procurement</h5>
        <p class="card-desc">Simplified ordering, competitive pricing, and flexible financing options for faster project delivery.</p>
        <ul class="card-features">
            <li>Competitive volume pricing</li>
            <li>Flexible payment terms</li>
            <li>Single-source procurement</li>
            <li>Real-time inventory visibility</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-icon">🛠️</div>
        <h5 class="card-title">Value-Added Services</h5>
        <p class="card-desc">Pre-sale and post-sale support services that enhance solution delivery and customer satisfaction.</p>
        <ul class="card-features">
            <li>Technical pre-sales support</li>
            <li>Configuration and imaging</li>
            <li>Warranty and support services</li>
            <li>Training and enablement</li>
        </ul>
    </div>
</div>

<h3 class="section-title" id="solutions">Partner Solutions Portfolio</h3>
<p class="partner-lead">Through TD SYNNEX, we deliver a comprehensive range of technology solutions across infrastructure, security, cloud, and data center categories.</p>

<div class="ms-highlight">
    <h4 class="ms-list-title text-white">Our Solution Categories</h4>
    <ul class="ms-list text-white">
        <li><strong>Data Center & Cloud Infrastructure:</strong> Servers, storage, networking, and virtualization solutions</li>
        <li><strong>Cybersecurity:</strong> Endpoint protection, network security, identity management, and threat intelligence</li>
        <li><strong>Collaboration & Productivity:</strong> Unified communications, video conferencing, and workspace solutions</li>
        <li><strong>Business Applications:</strong> ERP, CRM, business intelligence, and enterprise software</li>
        <li><strong>Client Devices:</strong> Workstations, laptops, tablets, and mobile devices</li>
        <li><strong>Professional Services:</strong> Consulting, implementation, managed services, and support</li>
    </ul>
</div>

<h4 class="ms-list-title mt-5">Featured Vendor Partners</h4>
<p class="partner-lead">We work with leading technology vendors through TD SYNNEX to deliver best-in-class solutions:</p>

<div class="modern-grid">
    <div class="modern-card">
        <div class="card-title">Infrastructure</div>
        <ul class="card-features">
            <li>Cisco, HPE, Dell Technologies</li>
            <li>Lenovo, NetApp, Pure Storage</li>
            <li>VMware, Nutanix, Red Hat</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-title">Security</div>
        <ul class="card-features">
            <li>Palo Alto, Fortinet, Check Point</li>
            <li>CrowdStrike, SentinelOne, Trend Micro</li>
            <li>Microsoft Security, Cisco Security</li>
        </ul>
    </div>

    <div class="modern-card">
        <div class="card-title">Cloud & Software</div>
        <ul class="card-features">
            <li>Microsoft, AWS, Google Cloud</li>
            <li>Adobe, Autodesk, Oracle</li>
            <li>Salesforce, ServiceNow, SAP</li>
        </ul>
    </div>
</div>

<h3 class="section-title mt-5">Partner Benefits & Support</h3>
<p class="partner-lead">As a TD SYNNEX partner, we receive comprehensive support services that enhance our ability to deliver value to your organization.</p>

<ul class="ms-list">
    <li><strong>Technical Enablement:</strong> Product training, certifications, and technical resources</li>
    <li><strong>Sales Support:</strong> Pre-sales engineering, solution design, and proof-of-concept assistance</li>
    <li><strong>Marketing Resources:</strong> Co-marketing programs, demand generation, and lead sharing</li>
    <li><strong>Business Development:</strong> Market insights, business planning, and growth strategies</li>
    <li><strong>Financial Services:</strong> Flexible financing, leasing options, and credit programs</li>
    <li><strong>Logistics & Fulfillment:</strong> Fast shipping, inventory management, and order tracking</li>
</ul>

<hr class="my-5">

<h3 class="section-title">Ready to Simplify Your Technology Procurement?</h3>
<p class="partner-lead">Let us leverage our TD SYNNEX partnership to deliver the technology solutions your business needs with competitive pricing, reliable supply chain, and comprehensive support.</p>

<a href="/contact" class="btn-cta mb-5">Contact Us Today</a>

</div>
