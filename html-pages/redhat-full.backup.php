<!-- ============================================================
     RED HAT – FULL PAGE (Microsoft Template Version)
=============================================================== -->

<style>
/* ============================
   MICROSOFT MODERN PAGE STYLING
   USED FOR RED HAT
============================ */

/* Brand Colors */
:root {
    --ms-blue: #0f6cbd;
    --ms-dark: #1a1f36;
    --ms-soft-dark: #2c3248;
    --ms-light: #f5f8fc;
    --ms-accent: #c7d92a;

    /* Red Hat Colors */
    --rh-red: #ee0000;
    --rh-red-light: #ff6b6b;
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
    color: var(--rh-red);
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
    border-left: 5px solid var(--rh-red);
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
    color: var(--rh-red);
    font-weight: bold;
    font-size: 1rem;
}

/* ----------------------------
   Badge Styling
---------------------------- */
.partner-badge {
    background: var(--rh-red);
    color: #fff;
    padding: 8px 14px;
    border-radius: 30px;
    margin: 5px;
    font-weight: 600;
    display: inline-block;
    font-size: 0.85rem;
}

/* CTA BUTTON */
.btn-cta {
    background: var(--rh-red);
    color: #ffffff !important;
    padding: 13px 32px;
    font-size: 1.05rem;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
    transition: 0.3s;
}
.btn-cta:hover {
    background: #c90000;
    transform: translateY(-3px);
}

/* Partner Logo */
.partner-brand-logo {
    max-width: 220px;
    filter: drop-shadow(0px 4px 10px rgba(0,0,0,0.1));
}

/* Divider */
hr {
    border-color: #e5e7eb;
}

/* ============================
   Grid sections (Dynamics style)
============================ */
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
    background: linear-gradient(135deg, #fff 0%, #fef2f2 100%);
    border: 1px solid rgba(255,0,0,0.15);
    border-radius: 14px;
    padding: 32px;
    transition: all .35s;
    position: relative;
    overflow: hidden;
}

.dynamics-card::before {
    content:'';
    position:absolute;
    top:0; left:0; right:0;
    height:4px;
    background: linear-gradient(90deg, var(--rh-red), var(--rh-red-light));
    transition:.3s;
}

.dynamics-card:hover {
    transform: translateY(-8px);
    border-color: var(--rh-red);
    box-shadow: 0 16px 48px rgba(255,0,0,0.15);
}

.dynamics-card:hover::before { height:6px; }

.dynamics-icon {
    font-size:2.4rem;
    margin-bottom:14px;
    color: var(--rh-red);
}

.dynamics-card-title {
    font-size:1.2rem;
    font-weight:700;
    color:var(--ms-dark);
    margin-bottom:14px;
}

.dynamics-card-desc {
    font-size:0.95rem;
    color:#4b5563;
    margin-bottom:16px;
}

.dynamics-features li {
    font-size:0.9rem;
    margin-bottom:8px;
    padding-left:22px;
    position:relative;
}

.dynamics-features li::before {
    content:'▢';
    position:absolute;
    left:0;
    color:var(--rh-red);
    font-weight:bold;
    font-size:1rem;
}

/* ===== Full-width Partner Hero ===== */
.partner-hero {
    background: linear-gradient(135deg, #1a1f36 0%, #ee0000 55%, #c90000 100%);
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
.hero-logo { max-width: 260px; filter: drop-shadow(0 8px 22px rgba(0,0,0,0.25)); }
@media(max-width: 992px){ .hero-grid { grid-template-columns: 1fr; text-align: center; } .hero-logo { margin: 20px auto 0; } }
</style>





<!-- ============================================================
     HERO SECTION
=============================================================== -->
<div class="mb-5">

    <div class="row align-items-center">
        <h4 class="section-title">Red Hat Premier Business Partner</h4>

        <div class="col-lg-7">
            <h4 class="partner-lead">Build Open. Run Anywhere. Innovate Everywhere.</h4>

            <p class="partner-lead">
                As a Red Hat Premier Business Partner—Red Hat’s highest level of partnership—we deliver enterprise-grade
                open-source solutions built for scale, performance, and hybrid cloud innovation. From Kubernetes platforms 
                to automation and cloud-native development, we help organizations unlock the full value of their IT investments.
            </p>
        </div>

        <div class="col-lg-5 text-center">
            <img alt="redhat-logo" class=" mb-3" src="images/partners/redhat.jpg">

            <div class="partner-badges mt-3 mx-auto" style="max-width:320px;">
                <div class="partner-badge">Premier Partner</div>
                <div class="partner-badge">Open  Leader</div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================
     WHY PARTNER WITH US
=============================================================== -->
<h3 class="section-title">Why Partner with Us?</h3>

<p class="partner-lead">
    As a Red Hat Premier Partner, we bring elite technical expertise, advanced certifications, and deep experience 
    delivering open-source transformation at scale. Our solutions are built for security, agility, cost optimization, 
    and freedom from vendor lock-in.
</p>

<p class="partner-lead">
    Whether you're modernizing legacy systems, adopting Kubernetes, implementing enterprise automation, or scaling 
    hybrid cloud workloads, we provide end-to-end Red Hat-powered solutions aligned to your strategic goals.
</p>

<h4 class="ms-list-title mt-4">Our Comprehensive Approach</h4>
<p class="partner-lead">
    From architecture and deployment to automation, integration, and managed services, we deliver Red Hat 
    platforms with precision and long-term value.
</p>



<!-- ============================================================
     ACCREDITATIONS
=============================================================== -->
<h2 class="mt-4">Red Hat Partner Accreditations & Specializations</h2>

<p class="">Our Red Hat partner accreditations demonstrate deep capability across the ecosystem.</p>

<h3 class="ms-list-title">Sales Accreditation</h3>
<ul class="ms-list">
    <li>Value proposition design</li>
    <li>Competitive analysis</li>
    <li>Pricing & qualification excellence</li>
</ul>

<h3 class="ms-list-title">Sales Engineer Accreditation</h3>
<ul class="ms-list">
    <li>Technical demos & proof-of-concepts</li>
    <li>Solution architecture mastery</li>
    <li>Technical competitive positioning</li>
</ul>

<h3 class="ms-list-title">Delivery Accreditation</h3>
<ul class="ms-list">
    <li>Enterprise implementation</li>
    <li>Application modernization</li>
    <li>Platform architecture & deployment</li>
</ul>





<!-- ============================================================
     RED HAT PRODUCT EXPERTISE
=============================================================== -->
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Red Hat Product Expertise</h3>
        <p class="text-center">Enterprise open-source technologies powering modern infrastructure.</p>
    </div>

    <div class="dynamics-grid">

        <!-- OpenShift -->
        <div class="dynamics-card">
            <div class="dynamics-icon">☸️</div>
            <h4 class="dynamics-card-title">Red Hat OpenShift</h4>
            <p class="dynamics-card-desc">Enterprise Kubernetes platform.</p>
            <ul class="dynamics-features">
                <li>Container orchestration</li>
                <li>Built-in CI/CD pipelines</li>
                <li>Service mesh & serverless</li>
                <li>Multi-cluster management</li>
                <li>Zero-trust security</li>
            </ul>
        </div>

        <!-- Ansible -->
        <div class="dynamics-card">
            <div class="dynamics-icon">⚙️</div>
            <h4 class="dynamics-card-title">Ansible Automation</h4>
            <p class="dynamics-card-desc">Unified automation for IT operations.</p>
            <ul class="dynamics-features">
                <li>Infrastructure as Code</li>
                <li>Cloud provisioning</li>
                <li>Security automation</li>
                <li>Event-driven automation</li>
                <li>Configuration management</li>
            </ul>
        </div>

        <!-- RHEL -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🐧</div>
            <h4 class="dynamics-card-title">Red Hat Enterprise Linux</h4>
            <p class="dynamics-card-desc">The trusted enterprise OS.</p>
            <ul class="dynamics-features">
                <li>10+ year lifecycle</li>
                <li>Enterprise-grade security</li>
                <li>Live kernel patching</li>
                <li>Stable application foundation</li>
            </ul>
        </div>

        <!-- OpenStack -->
        <div class="dynamics-card">
            <div class="dynamics-icon">☁️</div>
            <h4 class="dynamics-card-title">Red Hat OpenStack Platform</h4>
            <p class="dynamics-card-desc">Private cloud infrastructure.</p>
            <ul class="dynamics-features">
                <li>Self-service provisioning</li>
                <li>Software-defined networking</li>
                <li>Workload orchestration</li>
            </ul>
        </div>

        <!-- Virtualization -->
        <div class="dynamics-card">
            <div class="dynamics-icon">💻</div>
            <h4 class="dynamics-card-title">Red Hat Virtualization</h4>
            <p class="dynamics-card-desc">Enterprise virtualization platform.</p>
            <ul class="dynamics-features">
                <li>KVM-based virtualization</li>
                <li>Live migration</li>
                <li>VMware migration support</li>
            </ul>
        </div>

        <!-- JBoss -->
        <div class="dynamics-card">
            <div class="dynamics-icon">🔗</div>
            <h4 class="dynamics-card-title">Red Hat JBoss Middleware</h4>
            <p class="dynamics-card-desc">Enterprise integration & application platform.</p>
            <ul class="dynamics-features">
                <li>JBoss EAP</li>
                <li>Red Hat Fuse integration</li>
                <li>API management (3scale)</li>
                <li>Quarkus cloud-native apps</li>
            </ul>
        </div>

    </div>
</div>



<!-- ============================================================
     HYBRID & MULTI-CLOUD
=============================================================== -->
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Hybrid Cloud & Multi-Cloud Solutions</h3>
        <p class="text-center">Consistent architecture across any environment—public, private, or edge.</p>
    </div>

    <div class="dynamics-grid">

        <div class="dynamics-card">
            <div class="dynamics-icon">🏗️</div>
            <h4 class="dynamics-card-title">Open Hybrid Cloud</h4>
            <ul class="dynamics-features">
                <li>Unified platform with OpenShift</li>
                <li>Ansible automation</li>
                <li>Secure RHEL foundation</li>
                <li>Cluster governance</li>
            </ul>
        </div>

        <div class="dynamics-card">
            <div class="dynamics-icon">🌐</div>
            <h4 class="dynamics-card-title">Multi-Cloud Management</h4>
            <ul class="dynamics-features">
                <li>Cross-cloud orchestration</li>
                <li>Policy & compliance automation</li>
                <li>Cost optimization</li>
                <li>Disaster recovery</li>
            </ul>
        </div>

    </div>
</div>



<!-- ============================================================
     APPLICATION MODERNIZATION & DEVOPS
=============================================================== -->
<div class="dynamics-section">
    <div class="dynamics-header">
        <h3 class="section-title">Application Modernization & DevOps</h3>
        <p class="text-center">Transform legacy applications into secure, scalable, cloud-native systems.</p>
    </div>

    <div class="dynamics-grid">

        <div class="dynamics-card">
            <div class="dynamics-icon">📦</div>
            <h4 class="dynamics-card-title">Containerization</h4>
            <ul class="dynamics-features">
                <li>Replatforming legacy apps</li>
                <li>Microservices architecture</li>
                <li>12-factor applications</li>
            </ul>
        </div>

        <div class="dynamics-card">
            <div class="dynamics-icon">🚀</div>
            <h4 class="dynamics-card-title">DevOps Transformation</h4>
            <ul class="dynamics-features">
                <li>CI/CD pipelines</li>
                <li>GitOps workflows</li>
                <li>Automated testing & scanning</li>
            </ul>
        </div>

        <div class="dynamics-card">
            <div class="dynamics-icon">🔀</div>
            <h4 class="dynamics-card-title">Microservices</h4>
            <ul class="dynamics-features">
                <li>Domain-driven design</li>
                <li>Event-driven architecture</li>
                <li>API gateway patterns</li>
            </ul>
        </div>

    </div>
</div>



<!-- ============================================================
     SECURITY & COMPLIANCE
=============================================================== -->
<div class="dynamics-section">

    <div class="dynamics-header">
        <h3 class="section-title">Security & Compliance</h3>
        <p class="text-center">Enterprise-grade, security-first architecture across all Red Hat platforms.</p>
    </div>

    <div class="dynamics-grid">

        <div class="dynamics-card">
            <div class="dynamics-icon">🔒</div>
            <h4 class="dynamics-card-title">Security Hardening</h4>
            <ul class="dynamics-features">
                <li>SELinux enforcement</li>
                <li>Kubernetes security controls</li>
                <li>Isolation & encryption</li>
                <li>Secrets management</li>
            </ul>
        </div>

        <div class="dynamics-card">
            <div class="dynamics-icon">🛡️</div>
            <h4 class="dynamics-card-title">Compliance Automation</h4>
            <ul class="dynamics-features">
                <li>NIST, HIPAA, PCI compliance</li>
                <li>Automated remediation</li>
                <li>Policy-as-code</li>
                <li>Continuous scanning</li>
            </ul>
        </div>

    </div>
</div>



<!-- ============================================================
     MIGRATION SERVICES
=============================================================== -->
<div class="dynamics-section">

    <div class="dynamics-header">
        <h3 class="section-title">Migration Services</h3>
        <p class="text-center">Minimize risk, accelerate modernization, and reduce cost.</p>
    </div>

    <div class="dynamics-grid">

        <div class="dynamics-card">
            <div class="dynamics-icon">🖥️</div>
            <h4 class="dynamics-card-title">VMware Migration</h4>
            <ul class="dynamics-features">
                <li>VMware → RHV transitions</li>
                <li>Cost-benefit analysis</li>
                <li>Automated migration tools</li>
            </ul>
        </div>

        <div class="dynamics-card">
            <div class="dynamics-icon">🔄</div>
            <h4 class="dynamics-card-title">Legacy Modernization</h4>
            <ul class="dynamics-features">
                <li>Unix → Linux migration</li>
                <li>CentOS → RHEL conversion</li>
                <li>Monolith to microservices</li>
            </ul>
        </div>

        <div class="dynamics-card">
            <div class="dynamics-icon">☁️</div>
            <h4 class="dynamics-card-title">Cloud Migration</h4>
            <ul class="dynamics-features">
                <li>AWS / Azure / GCP migration</li>
                <li>Hybrid cloud architecture</li>
                <li>Cost optimization</li>
            </ul>
        </div>

    </div>
</div>



<!-- ============================================================
     MANAGED SERVICES
=============================================================== -->
<div class="dynamics-section">

    <div class="dynamics-header">
        <h3 class="section-title">Managed Services & Training</h3>
        <p class="text-center">Always-on operational excellence for your Red Hat ecosystem.</p>
    </div>

    <div class="dynamics-grid">

        <div class="dynamics-card">
            <div class="dynamics-icon">🛡️</div>
            <h4 class="dynamics-card-title">Managed Services</h4>
            <ul class="dynamics-features">
                <li>24/7 monitoring</li>
                <li>Performance optimization</li>
                <li>Security patching</li>
                <li>Incident response</li>
            </ul>
        </div>

        <div class="dynamics-card">
            <div class="dynamics-icon">⚡</div>
            <h4 class="dynamics-card-title">Automation Operations</h4>
            <ul class="dynamics-features">
                <li>Automation Center of Excellence</li>
                <li>Playbook development</li>
                <li>Operational automation</li>
            </ul>
        </div>

        <div class="dynamics-card">
            <div class="dynamics-icon">🎓</div>
            <h4 class="dynamics-card-title">Training & Enablement</h4>
            <ul class="dynamics-features">
                <li>RHCSA & RHCE training</li>
                <li>OpenShift & Ansible workshops</li>
                <li>Hands-on labs</li>
            </ul>
        </div>

    </div>

</div>



<!-- ============================================================
     INDUSTRY SOLUTIONS
=============================================================== -->
<div class="dynamics-section">

    <div class="dynamics-header">
        <h3 class="section-title">Industry Solutions</h3>
        <p class="text-center">Open-source excellence across key sectors.</p>
    </div>

    <div class="dynamics-grid">
        <div class="dynamics-card"><div class="dynamics-icon">🏦</div><h4 class="dynamics-card-title">Financial Services</h4></div>
        <div class="dynamics-card"><div class="dynamics-icon">⚕️</div><h4 class="dynamics-card-title">Healthcare</h4></div>
        <div class="dynamics-card"><div class="dynamics-icon">📡</div><h4 class="dynamics-card-title">Telecom</h4></div>
        <div class="dynamics-card"><div class="dynamics-icon">🏛️</div><h4 class="dynamics-card-title">Government</h4></div>
        <div class="dynamics-card"><div class="dynamics-icon">🏭</div><h4 class="dynamics-card-title">Manufacturing</h4></div>
        <div class="dynamics-card"><div class="dynamics-icon">🛍️</div><h4 class="dynamics-card-title">Retail</h4></div>
    </div>

</div>



<!-- ============================================================
     CTA
=============================================================== -->
<h3 class="section-title">Ready to Break Free from Vendor Lock-In?</h3>
<p class="partner-lead">
    Start your open hybrid cloud journey with Red Hat. Modernize applications, automate operations, migrate workloads, 
    and scale with secure, enterprise-grade open-source technologies.
</p>

<a href="/contact" class="btn btn-cta mb-5">Get Started Today</a>
