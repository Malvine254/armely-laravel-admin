<style>
/* ============================
   SNOWFLAKE MODERN PAGE STYLING
   ============================ */

/* Brand Colors */
:root {
  --ms-blue: #0f6cbd;
  --ms-dark: #1a1f36;
  --ms-soft-dark: #2c3248;
  --ms-light: #f5f8fc;
  --ms-accent: #c7d92a;
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
  padding-left: 22px;
  position: relative;
}
.ms-list li::before {
  content: '▢';
  position: absolute;
  left: 0;
  color: var(--ms-blue);
  font-weight: bold;
  font-size: 1rem;
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
  background: var(--ms-blue);
  color: #ffffff !important;
  padding: 13px 32px;
  font-size: 1.05rem;
  font-weight: 600;
  border-radius: 10px;
  text-decoration: none;
  transition: 0.3s;
}
.btn-cta:hover {
  background: #0c5aa2;
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
  border-color: #e5e7eb;
}

/* ----------------------------
   Gradient Highlight Wrapper
   ---------------------------- */
.ms-highlight {
  background: linear-gradient(135deg, #0f6cbd 0%, #5ea0ef 100%);
  color: #fff;
  border-radius: 14px;
  padding: 24px 26px;
  margin-bottom: 40px;
  box-shadow: 0 12px 32px rgba(15,108,189,0.2);
}

/* ----------------------------
   MODERN CARD GRID
   ---------------------------- */
.modern-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 28px;
  margin-bottom: 40px;
}

.modern-card {
  background: linear-gradient(135deg, #f5f8fc 0%, #ffffff 100%);
  border: 1px solid rgba(15, 108, 189, 0.1);
  border-radius: 14px;
  padding: 32px;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.modern-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #0f6cbd, #c7d92a);
  transition: height 0.35s ease;
}

.modern-card:hover {
  transform: translateY(-8px);
  border-color: #0f6cbd;
  box-shadow: 0 16px 48px rgba(15, 108, 189, 0.15);
}

.modern-card:hover::before {
  height: 6px;
}

.card-icon {
  font-size: 2.4rem;
  margin-bottom: 14px;
}

.card-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--ms-dark);
  margin-bottom: 14px;
  line-height: 1.4;
}

.card-desc {
  font-size: 0.95rem;
  color: #4b5563;
  line-height: 1.6;
  margin-bottom: 16px;
}

.card-features {
  list-style: none;
  padding: 0;
  margin: 0;
}

.card-features li {
  font-size: 0.9rem;
  color: #5a6270;
  margin-bottom: 8px;
  padding-left: 22px;
  position: relative;
  line-height: 1.5;
}

.card-features li::before {
  content: '▢';
  position: absolute;
  left: 0;
  color: var(--ms-blue);
  font-weight: bold;
  font-size: 1.1rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 24px;
  margin: 40px 0;
}

.stat-card {
  background: linear-gradient(135deg, #0f6cbd, #2e7dd4);
  color: white;
  border-radius: 12px;
  padding: 28px;
  text-align: center;
  box-shadow: 0 8px 24px rgba(15, 108, 189, 0.2);
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-6px);
}

.stat-number {
  font-size: 2.8rem;
  font-weight: 800;
  margin-bottom: 8px;
  color: white;
}

.stat-label {
  font-size: 1rem;
  font-weight: 600;
  line-height: 1.5;
}

.section-header {
  text-align: center;
  margin-bottom: 50px;
}

.section-header .section-title {
  margin-bottom: 16px;
}

.section-header .partner-lead {
  max-width: 600px;
  margin: 0 auto;
  color: #666;
}

/* ----------------------------
   Responsive Tweaks
   ---------------------------- */
@media(max-width: 768px){
  .ms-card { padding: 25px 22px; }
  .section-title { font-size: 1.65rem; }
  .modern-grid { grid-template-columns: 1fr; }
  .stats-grid { grid-template-columns: 1fr; }
}

/* ===== Full-width Partner Hero ===== */
.partner-hero {
  background: linear-gradient(135deg, #1f2a44 0%, #0f6cbd 55%, #2e7dd4 100%);
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
  background: radial-gradient(closest-side, rgba(255,255,255,0.15), rgba(255,255,255,0));
  filter: blur(30px);
}
.hero-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.hero-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center; }
.hero-title { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 800; line-height: 1.15; margin-bottom: 18px; color: #fff; }
.hero-sub { font-size: 1.1rem; line-height: 1.8; opacity: 0.95; color: #fff; }
.hero-ctas { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 24px; }
.hero-cta { background: transparent; color: #ffffff; border: 1.6px solid rgba(255,255,255,0.6); border-radius: 999px; padding: 12px 18px; font-weight: 700; box-shadow: 0 6px 18px rgba(0,0,0,0.2); text-decoration: none; }
.hero-cta:hover { background: rgba(255,255,255,0.12); }
.hero-logo { max-width: 240px; opacity: 0.95; filter: drop-shadow(0 12px 32px rgba(0,0,0,0.4)) drop-shadow(0 4px 12px rgba(0,0,0,0.3)); transition: all 0.3s ease; border-radius: 16px; }
@media(max-width: 992px){ .hero-grid { grid-template-columns: 1fr; text-align: center; } .hero-logo { margin: 20px auto 0; } }
.container { padding: 50px 24px; }

</style>

<!-- Full-width Hero -->
<section class="partner-hero">
  <div class="hero-inner">
    <div class="hero-grid">
      <div>
        <h1 class="hero-title">Cisco Licensing Services Partner</h1>
        <p class="hero-sub">Simplify Smart Licensing, optimize subscriptions and Enterprise Agreements, and maintain audit-ready compliance. We maximize value from every Cisco license dollar spent with end-to-end assessment, management, and optimization.</p>
        <div class="hero-ctas">
          <a class="hero-cta" href="#smart-licensing">Smart Licensing</a>
          <a class="hero-cta" href="#ea-programs">Enterprise Agreements</a>
          <a class="hero-cta" href="/contact">Reduce Costs</a>
        </div>
      </div>
      <div class="text-center">
        <img class="hero-logo" alt="Cisco" src="/images/partners/cisco.png">
      </div>
    </div>
  </div>
</section>

<div class="container">

  <hr class="my-5">
  <h3 class="section-title">Why Partner With Us for Cisco Licensing?</h3>
<p class="partner-lead">Cisco licensing has evolved significantly with Smart Licensing, subscription models, and Enterprise Agreements. Organizations need expert guidance to navigate licensing complexities, avoid compliance risks, and optimize costs. Our licensing expertise ensures you purchase the right licenses, deploy them efficiently, and maintain ongoing compliance while minimizing total cost of ownership.</p>
<p class="partner-lead">We help you at every stage of the licensing lifecycle—from initial assessment and procurement through deployment, management, optimization, and renewal. Taking a consultative approach, we align licensing decisions with your technical architecture and business objectives to ensure you're never over-licensed or under-compliant.</p>

<h4 class="ms-list-title mt-4">Our Comprehensive Approach</h4>
<p class="partner-lead">From license assessment and procurement through Smart Account management, optimization, compliance, and renewal, we deliver end-to-end licensing services that maximize value and minimize risk.</p>

<hr class="my-5">
<h3 class="section-title" id="smart-licensing">Cisco Smart Licensing Expertise</h3>

<div class="modern-grid">
  <div class="modern-card">
    <div class="card-title">Understanding Cisco Smart Licensing</div>
    <p class="card-desc">Modern, flexible licensing for the cloud era. Cisco Smart Licensing replaces traditional PAKs with an account-based approach that provides visibility and simplifies activation.</p>
    <ul class="card-features">
      <li>Simplified license activation and deployment</li>
      <li>Real-time visibility into license consumption</li>
      <li>Flexible license pooling and sharing</li>
      <li>Automatic license renewal every 30 days</li>
      <li>Cloud-based or on-premises deployment options</li>
      <li>Reduced administrative overhead and better compliance visibility</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Smart Account Architecture</div>
    <p class="card-desc">Organize and manage licenses efficiently. Smart Accounts provide a central repository for license visibility, access control, and account structure best practices.</p>
    <ul class="card-features">
      <li>Customer Smart Accounts, Partner Holding Accounts, and Virtual Accounts</li>
      <li>Default Virtual Account creation and management</li>
      <li>User roles and granular permissions</li>
      <li>Smart Account setup, migration, and consolidation services</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Smart Licensing Deployment Options</div>
    <p class="card-desc">Flexible deployment models for different security and connectivity needs, including cloud, on-premises, policy-based, and SLR options for air-gapped environments.</p>
    <ul class="card-features">
      <li>Direct Cloud Access (CSSM) — real-time sync and simplest deployment</li>
      <li>CSSM On-Premises — for disconnected or air-gapped networks</li>
      <li>Policy-based licensing for modern IOS XE devices</li>
      <li>Specific License Reservation (SLR) for fully disconnected systems</li>
    </ul>
  </div>
</div>

<hr class="my-5">
<h3 class="section-title" id="ea-programs">Cisco Licensing Models & Programs</h3>

<div class="modern-grid">
  <div class="modern-card">
    <div class="card-title">Subscription Licensing</div>
    <p class="card-desc">Predictable costs with flexible terms — monthly to multi-year subscriptions with tiers and co-terming options.</p>
    <ul class="card-features">
      <li>Monthly, 1, 3, 5, and 7-year terms</li>
      <li>Essentials, Advantage, Premier tiers; user/device/capacity metrics</li>
      <li>Subscription strategy, right-sizing, and renewal management</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Perpetual Licensing</div>
    <p class="card-desc">Traditional perpetual rights remain for select products; we help manage SWSS, upgrades, and migration planning.</p>
    <ul class="card-features">
      <li>Perpetual license inventory and SWSS renewal management</li>
      <li>Perpetual to subscription migration planning</li>
      <li>License transfer, rehosting, and EOL planning</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Enterprise Agreement (EA)</div>
    <p class="card-desc">Consolidate software and services under one agreement for predictable costs and simplified procurement.</p>
    <ul class="card-features">
      <li>EA evaluation, commitment sizing, and negotiation support</li>
      <li>True-up planning, usage tracking, and renewal management</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Cisco Plus (As-a-Service)</div>
    <p class="card-desc">Consume Cisco technology as-a-service for predictable billing and lifecycle management.</p>
    <ul class="card-features">
      <li>Hybrid Cloud, Secure Connect, and Collaboration offerings</li>
      <li>Pay-as-you-go or committed pricing with lifecycle management</li>
    </ul>
  </div>
</div>

<hr class="my-5">
<h3 class="section-title">Product-Specific Licensing Expertise</h3>

<div class="modern-grid">
  <div class="modern-card">
    <div class="card-title">Networking Licensing</div>
    <p class="card-desc">DNA, SD-WAN, wireless and traditional IOS licensing — right-tiering and automation benefits.</p>
    <ul class="card-features">
      <li>DNA Essentials/Advantage/Premier, SD-Access, term-based models</li>
      <li>SD-WAN subscriptions, Edge compute and Viptela licensing</li>
      <li>Wireless subscriptions and DNA Spaces services</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Security Licensing</div>
    <p class="card-desc">Licensing for firewalls, AMP, Umbrella, Secure Endpoint and more with subscription tiers and feature bundles.</p>
    <ul class="card-features">
      <li>Firepower Threat Defense, URL filtering, Talos subscriptions</li>
      <li>Umbrella, SWG, DLP and cloud security licensing models</li>
      <li>Endpoint licensing tiers and advanced capabilities</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Collaboration & Contact Center</div>
    <p class="card-desc">Webex, UWL, UCL and Contact Center licensing tailored for modern collaboration needs.</p>
    <ul class="card-features">
      <li>UWL, UCL, Webex packages, named vs concurrent licensing</li>
      <li>Contact Center subscription models and digital channel licensing</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Data Center & Compute</div>
    <p class="card-desc">UCS, HyperFlex, Intersight licensing for management, automation and per-node models.</p>
    <ul class="card-features">
      <li>Intersight tiers, per-server subscriptions, UCS feature licensing</li>
      <li>HyperFlex per-node models and data platform services</li>
    </ul>
  </div>
</div>

<hr class="my-5">
<h3 class="section-title">Licensing Services & Solutions</h3>

<div class="modern-grid">
  <div class="modern-card">
    <div class="card-title">License Assessment & Discovery</div>
    <p class="card-desc">Complete inventory, utilization analysis, and compliance gap identification to uncover optimization opportunities.</p>
    <ul class="card-features">
      <li>Complete license inventory and Smart Account analysis</li>
      <li>Utilization and consumption reporting, shelfware discovery</li>
      <li>Compliance gap identification and migration path analysis</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">License Procurement & Acquisition</div>
    <p class="card-desc">Guidance through quoting, sizing, and Smart Account assignment to ensure optimal procurement.</p>
    <ul class="card-features">
      <li>Requirements gathering, sizing and quote validation</li>
      <li>Pricing optimization, negotiation support, and Smart Account assignment</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Smart Account Management</div>
    <p class="card-desc">Ongoing administration to keep Smart Accounts organized and optimized with role and pool management.</p>
    <ul class="card-features">
      <li>User administration, virtual account creation, token generation</li>
      <li>License pool management, device registration, audit & cleanup</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">License Compliance Management</div>
    <p class="card-desc">Proactive monitoring, alerting, and audit support to maintain compliance and readiness.</p>
    <ul class="card-features">
      <li>Real-time compliance monitoring and alert management</li>
      <li>Audit preparation, remediation, and true-up management</li>
    </ul>
  </div>
</div>

<hr class="my-5">
<h3 class="section-title">Migration, Renewal & Optimization</h3>

<div class="modern-grid">
  <div class="modern-card">
    <div class="card-title">Migration Services</div>
    <p class="card-desc">Seamless migrations from classic licensing to Smart Licensing and subscription models with planning and validation.</p>
    <ul class="card-features">
      <li>Classic to Smart migration, PAK inventory and token application</li>
      <li>Perpetual to subscription migration planning and modeling</li>
      <li>Cutover planning, validation and post-migration checks</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">Renewal Management</div>
    <p class="card-desc">Proactive renewal planning and execution to avoid interruptions and optimize costs.</p>
    <ul class="card-features">
      <li>Renewal tracking, usage analysis and co-terming strategies</li>
      <li>Negotiation support and post-renewal reconciliation</li>
    </ul>
  </div>

  <div class="modern-card">
    <div class="card-title">License Optimization & Cost Reduction</div>
    <p class="card-desc">Identify shelfware, reallocate licenses, and optimize tiers to reduce spend while preserving capability.</p>
    <ul class="card-features">
      <li>Shelfware recovery, license harvesting, and right-tiering</li>
      <li>Term optimization, pooling strategies and consolidation</li>
    </ul>
  </div>
</div>

<hr class="my-5">
<h3 class="section-title">Licensing Tools & Portals</h3>

<div class="modern-grid">
  <div class="modern-card">
    <div class="card-title">Cisco Smart Software Manager (CSSM)</div>
    <p class="card-desc">Central management for Smart Licensing — Smart Accounts, tokens, reporting and device lifecycle management.</p>
  </div>
  <div class="modern-card">
    <div class="card-title">License Central</div>
    <p class="card-desc">Next-gen license management with unified views, search, reporting and integrations for large estates.</p>
  </div>
  <div class="modern-card">
    <div class="card-title">License Registration Portal (LRP) & CCW</div>
    <p class="card-desc">Support for legacy license generation and order management best practices for correct Smart Account assignment.</p>
  </div>
</div>

<hr class="my-5">
<h3 class="section-title">Industry-Specific Licensing Solutions</h3>

<div class="modern-grid">
  <div class="modern-card"><div class="card-title">Financial Services</div><p class="card-desc">Compliance-focused licensing for trading floors, data centers and branch networks.</p></div>
  <div class="modern-card"><div class="card-title">Healthcare</div><p class="card-desc">HIPAA-aware licensing for telehealth, clinical and administrative systems.</p></div>
  <div class="modern-card"><div class="card-title">Education</div><p class="card-desc">Budget-friendly licensing strategies, E-Rate compliance and campus-wide management.</p></div>
  <div class="modern-card"><div class="card-title">Government</div><p class="card-desc">GSA and FedRAMP-aware licensing, SLR usage for classified networks.</p></div>
  <div class="modern-card"><div class="card-title">Manufacturing</div><p class="card-desc">OT/IT convergence licensing and IoT device licensing strategies.</p></div>
  <div class="modern-card"><div class="card-title">Service Providers</div><p class="card-desc">Multi-tenant licensing practices and customer provisioning at scale.</p></div>
</div>

<hr class="my-5">
<h3 class="section-title">Training & Enablement</h3>
<div class="modern-grid">
  <div class="modern-card">
    <div class="card-title">Licensing Training Programs</div>
    <p class="card-desc">Role-based training for Smart Licensing, CSSM, License Central and procurement best practices.</p>
    <ul class="card-features">
      <li>Smart Licensing fundamentals and Smart Account administration</li>
      <li>CSSM and License Central hands-on training</li>
      <li>Procurement, optimization and audit readiness workshops</li>
    </ul>
  </div>
  <div class="modern-card">
    <div class="card-title">Documentation & Resources</div>
    <p class="card-desc">Comprehensive guides, quick references and knowledge base to support ongoing licensing management.</p>
  </div>
</div>

<hr class="my-5">
<h3 class="section-title">Why Cisco Licensing Expertise Matters</h3>
<p class="partner-lead">Cisco licensing complexity can create cost, compliance and operational risks. Our focused licensing services reduce risk, optimize spend, and align licensing to your strategic roadmap.</p>

<div class="modern-grid">
  <div class="modern-card"><div class="card-title">Deep Licensing Expertise</div><p class="card-desc">Experienced team across portfolios and models.</p></div>
  <div class="modern-card"><div class="card-title">Vendor-Neutral Advocacy</div><p class="card-desc">Recommendations that prioritize your outcomes.</p></div>
  <div class="modern-card"><div class="card-title">Proven Methodologies</div><p class="card-desc">Structured assessment, optimization and management practices.</p></div>
  <div class="modern-card"><div class="card-title">End-to-End Services</div><p class="card-desc">From assessment to ongoing management and renewals.</p></div>
  <div class="modern-card"><div class="card-title">Tool Expertise</div><p class="card-desc">CSSM, License Central, CCW and portal knowledge.</p></div>
  <div class="modern-card"><div class="card-title">Relationship Management</div><p class="card-desc">Strong vendor relationships for faster resolution.</p></div>
</div>

<hr class="my-5">
<h3 class="section-title">Success Metrics</h3>
<div class="modern-grid">
  <div class="modern-card"><div class="card-title">Proven Results</div>
    <ul class="card-features">
      <li>Average 20-25% licensing cost reduction through optimization</li>
      <li>500+ Smart Accounts managed for customers globally</li>
      <li>100% compliance rate for managed accounts</li>
      <li>95% renewal on-time rate preventing service interruptions</li>
    </ul>
  </div>
</div>

<hr class="my-5">
<h3 class="section-title">Ready to Optimize Your Cisco Licensing?</h3>
<p class="partner-lead">Stop overpaying for Cisco licenses. Ensure compliance. Simplify management. Whether you're struggling with Smart Account administration, preparing for an EA negotiation, or simply want to reduce licensing costs, we have the expertise to help you succeed.</p>

<a  href="/contact" class="btn btn-info mt-4">Get Started Today</a>

<div class=" mt-4">
  <h5 class="ms-list-title">Connect with our Cisco licensing experts to discuss your licensing challenges and discover how we can help you:</h5>
  <ul class="ms-list mt-3">
    <li>Reduce licensing costs by 15-30% through optimization</li>
    <li>Ensure ongoing license compliance and audit readiness</li>
    <li>Simplify Smart Account and license management</li>
    <li>Navigate Cisco licensing complexity with expert guidance</li>
    <li>Optimize subscription tiers and term lengths</li>
    <li>Manage renewals proactively and strategically</li>
    <li>Migrate from classic to Smart Licensing seamlessly</li>
    <li>Build internal licensing expertise through training</li>
  </ul>
  <p class="partner-lead mt-3">Contact us to schedule a complimentary Cisco licensing assessment and learn how our expertise can maximize the value of your Cisco investment.</p>
</div>

<p class="partner-lead">With deep Cisco licensing expertise across all products, models, and programs, we're committed to helping organizations optimize their Cisco software investments. Our vendor-neutral approach, proven methodologies, and comprehensive services ensure you purchase the right licenses, maintain compliance, and minimize total cost of ownership.</p>

</div>
