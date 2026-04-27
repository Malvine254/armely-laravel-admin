<!-- ============================================================
     VEEAM – FULL PAGE (Based on existing partner layout)
=============================================================== -->

<style>
/* ============================
   VEEAM MODERN PAGE STYLING
   ============================ */

/* Brand Colors */
:root {
    --veeam-green: #00b32d; /* Primary Veeam green */
    --veeam-dark: #0b2e13;  /* Deep green for contrast */
    --veeam-light: #f5fff8; /* Soft background tint */
}

/* Page container spacing */
.v-section { padding: 40px 0; }

/* Titles */
.section-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--veeam-dark);
    margin-bottom: 1.2rem;
    letter-spacing: -0.5px;
}

.v-list-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--veeam-green);
    margin-top: 40px;
}

/* Lead text */
.partner-lead { font-size: 1.05rem; line-height: 1.7; color: #334155; }

/* Cards */
.v-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 35px 40px;
    margin-bottom: 40px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.06);
    transition: .3s;
    border-left: 5px solid var(--veeam-green);
}
.v-card:hover { transform: translateY(-4px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }

/* Lists */
.v-list { padding-left: 22px; }
.v-list li { margin-bottom: 8px; font-size: 1rem; line-height: 1.55; }

/* Badge */
.partner-badge {
    background: var(--veeam-green);
    color: #fff;
    padding: 8px 14px;
    border-radius: 30px;
    margin: 5px;
    font-weight: 600;
    display: inline-block;
    font-size: 0.85rem;
}

/* CTA */
.btn-cta {
    background: var(--veeam-green);
    color: #ffffff !important;
    padding: 13px 32px;
    font-size: 1.05rem;
    font-weight: 700;
    border-radius: 999px;
    text-decoration: none;
    transition: 0.3s;
}
.btn-cta:hover { background: #019c27; transform: translateY(-3px); }

/* Hero */
.partner-hero {
    background: linear-gradient(135deg, var(--veeam-dark) 0%, var(--veeam-green) 55%, var(--veeam-dark) 100%);
    color: #fff;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}
.partner-hero::before {
    content: '';
    position: absolute; top: -25%; right: -25%; width: 60%; height: 160%;
    background: radial-gradient(closest-side, rgba(255,255,255,0.12), rgba(255,255,255,0));
    filter: blur(30px);
}
.hero-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.hero-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center; }
.hero-title { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 800; line-height: 1.15; margin-bottom: 18px; color: #fff; }
.hero-sub { font-size: 1.1rem; line-height: 1.8; opacity: 0.95; color: #fff; }
.hero-ctas { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 24px; }
.hero-cta { background: transparent; color: #ffffff; border: 1.6px solid rgba(255,255,255,0.6); border-radius: 999px; padding: 12px 18px; font-weight: 700; text-decoration: none; }
.hero-cta:hover { background: rgba(255,255,255,0.12); }
.hero-logo { max-width: 260px; filter: drop-shadow(0 12px 32px rgba(0,0,0,0.4)); opacity: 0.95; transition: all 0.3s ease; border-radius: 16px; }
@media(max-width: 992px){ .hero-grid { grid-template-columns: 1fr; text-align: center; } .hero-logo { margin: 20px auto 0; } }

/* Grid (Solution cards) */
.v-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 28px; margin-bottom: 40px; }
.v-s-card {
    background: linear-gradient(135deg, var(--veeam-light) 0%, #ffffff 100%);
    border: 1px solid rgba(0,179,45,0.18);
    border-radius: 14px; padding: 32px; transition: all .35s; position: relative; overflow: hidden;
}
.v-s-card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background: linear-gradient(90deg, var(--veeam-green), #34d399); transition:.3s; }
.v-s-card:hover { transform: translateY(-8px); border-color: var(--veeam-green); box-shadow: 0 16px 48px rgba(0,179,45,0.18); }
.v-s-card:hover::before { height:6px; }
.v-icon { font-size: 2.2rem; margin-bottom: 12px; color: var(--veeam-green); }
.v-title { font-size: 1.15rem; font-weight: 700; color: var(--veeam-dark); margin-bottom: 12px; }
.v-desc { font-size: 0.95rem; color: #475569; line-height: 1.6; margin-bottom: 12px; }
.v-feats { list-style: none; padding: 0; margin: 0; }
.v-feats li { font-size: 0.9rem; color: #5a6270; margin-bottom: 8px; padding-left: 22px; position: relative; }
.v-feats li::before { content: '✓'; position: absolute; left: 0; color: var(--veeam-green); font-weight: bold; font-size: 1.1rem; }

hr { border-color: #e5e7eb; }
</style>

<!-- Hero -->
<section class="partner-hero">
  <div class="hero-inner">
    <div class="hero-grid">
      <div>
        <h1 class="hero-title">Resilient. Recoverable. Ready.</h1>
        <p class="hero-sub">Protect, backup, and rapidly recover your critical workloads across on-prem, cloud, and SaaS. As a Veeam partner, we help you build a modern data protection strategy that withstands ransomware, reduces downtime, and meets compliance requirements.</p>
        <div class="hero-ctas">
          <a class="hero-cta" href="#solutions">Data Protection</a>
          <a class="hero-cta" href="#m365">Microsoft 365 Backup</a>
          <a class="hero-cta" href="/contact">Talk to an Expert</a>
        </div>
      </div>
      <div class="text-center">
        <img class="hero-logo" alt="Veeam Logo" src="/images/partners/veem-logo-white.png">
      </div>
    </div>
  </div>
</section>

<div class="container">

  <h3 class="section-title mt-4">Why Partner with Us?</h3>
  <p class="partner-lead">We design and operate end-to-end Veeam-powered backup and recovery solutions: from VMware and Hyper-V to AWS, Azure, Microsoft 365, and Kubernetes. Our architects focus on immutability, zero-trust, and rapid recovery objectives so you can bounce back quickly from incidents.</p>
  <p class="partner-lead">With certified engineers and proven runbooks, we align RPO/RTO to business goals, automate testing, and deliver continuous resilience—without over-complicating operations or inflating costs.</p>

  <h4 id="solutions" class="v-list-title mt-4">Core Solutions</h4>
  <ul class="v-list">
    <li>Immutable backups with secure repositories and air-gapped tiers</li>
    <li>Disaster recovery orchestration with tested failover plans</li>
    <li>Hybrid and multi-cloud protection across AWS, Azure, GCP</li>
    <li>Instant VM recovery, granular item restore, and automated validation</li>
    <li>Ransomware resilience with 3-2-1-1-0 strategy</li>
  </ul>

  <hr class="my-5">

  <div class="v-grid">
    <!-- Data Center Backup -->
    <div class="v-s-card">
      <div class="v-icon">🏢</div>
      <div class="v-title">Data Center Backup & Recovery</div>
      <div class="v-desc">Protect VMware/Hyper-V workloads with immutable backups and instant recovery.</div>
      <ul class="v-feats">
        <li>Per-VM backup chains and storage efficiency</li>
        <li>Instant VM Recovery to production</li>
        <li>Secure repositories and hardened Linux</li>
      </ul>
    </div>

    <!-- Cloud Backup -->
    <div class="v-s-card">
      <div class="v-icon">☁️</div>
      <div class="v-title">Cloud Backup (AWS/Azure/GCP)</div>
      <div class="v-desc">Right-size, automate, and secure backups for cloud-native workloads.</div>
      <ul class="v-feats">
        <li>Policy-based protection and lifecycle tiers</li>
        <li>Cross-region replication & DR patterns</li>
        <li>Cost-aware storage and immutability</li>
      </ul>
    </div>

    <!-- M365 Backup -->
    <div id="m365" class="v-s-card">
      <div class="v-icon">📧</div>
      <div class="v-title">Microsoft 365 Backup</div>
      <div class="v-desc">Protect Exchange, SharePoint, OneDrive, and Teams with fast restore.</div>
      <ul class="v-feats">
        <li>Granular item recovery and eDiscovery</li>
        <li>Flexible retention, compliance-friendly</li>
        <li>Multi-tenant and role-based access</li>
      </ul>
    </div>

    <!-- Kubernetes (Kasten) -->
    <div class="v-s-card">
      <div class="v-icon">🧩</div>
      <div class="v-title">Kubernetes Backup (Kasten)</div>
      <div class="v-desc">Protect containerized apps and stateful data across clusters.</div>
      <ul class="v-feats">
        <li>Application-centric backup and restore</li>
        <li>Multi-cluster policy management</li>
        <li>Portable backups across clouds</li>
      </ul>
    </div>

    <!-- DR Orchestration -->
    <div class="v-s-card">
      <div class="v-icon">🛡️</div>
      <div class="v-title">Ransomware Resilience & DR</div>
      <div class="v-desc">Hardened repositories, immutable storage, and automated DR runbooks.</div>
      <ul class="v-feats">
        <li>3-2-1-1-0 strategy implementation</li>
        <li>Automated verification with SureBackup</li>
        <li>Periodic DR testing and documentation</li>
      </ul>
    </div>
  </div>

  <div class="text-center my-4">
    <a href="/contact" class="btn-cta">Start a Backup Assessment</a>
  </div>
</div>
