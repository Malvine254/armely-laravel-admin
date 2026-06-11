@extends('layouts.public')

@section('title', 'Armely - Microsoft SQL Server')
@section('meta_description', 'SQL Server consulting, migration, performance tuning, security, and managed DBA services from Armely.')
@section('meta_keywords', 'SQL Server, Azure SQL, database migration, DBA services, performance tuning, Armely')
@section('canonical_url', url('/sql-server'))

@push('head')
<meta property="og:title" content="Armely - Microsoft SQL Server">
<meta property="og:description" content="SQL Server consulting, migration, performance tuning, security, and managed DBA services from Armely.">
<meta property="og:url" content="{{ url('/sql-server') }}">
<meta name="twitter:title" content="Armely - Microsoft SQL Server">
<meta name="twitter:description" content="SQL Server consulting, migration, performance tuning, security, and managed DBA services from Armely.">
@endpush

@push('styles')
<style>

.armely-solution-page {
    --as-blue: #2f5597;
    --as-blue-dark: #1e3a6d;
    --as-ink: #18233f;
    --as-muted: #5f6f89;
    --as-line: #dfe8f6;
    --as-soft: #f4f8ff;
    --as-accent: #f4a51c;
    color: var(--as-ink);
    background: #ffffff;
}
.armely-solution-page a { text-decoration: none; }
.as-container { width: min(1120px, calc(100% - 32px)); margin: 0 auto; }
.as-hero { position: relative; overflow: hidden; padding: 92px 0 70px; background: linear-gradient(135deg, #1e3a6d 0%, #2f5597 56%, #5f83c3 100%); }
.as-hero::before { content: ""; position: absolute; inset: auto -12% -45% auto; width: 620px; height: 620px; background: radial-gradient(circle, rgba(255,255,255,0.13), transparent 68%); pointer-events: none; }
.as-hero-grid { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0, 1fr) 440px; gap: 52px; align-items: center; }
.as-eyebrow { display: inline-flex; align-items: center; gap: 8px; padding: 7px 14px; border: 1px solid rgba(255,255,255,0.22); border-radius: 999px; background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.9); font-size: .78rem; font-weight: 700; text-transform: uppercase; margin-bottom: 22px; }
.as-hero h1 { color: #fff; font-size: clamp(2.25rem, 4vw, 3.5rem); line-height: 1.12; font-weight: 800; margin: 0 0 20px; }
.as-hero h1 span { color: #ffd166; }
.as-hero p { color: rgba(255,255,255,.88); font-size: 1.08rem; line-height: 1.8; margin: 0 0 28px; max-width: 610px; }
.as-actions { display: flex; flex-wrap: wrap; gap: 14px; align-items: center; }
.as-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 48px; padding: 12px 22px; border-radius: 8px; font-weight: 700; transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease; }
.as-btn:hover { transform: translateY(-2px); }
.as-btn-primary { background: #fff; color: var(--as-blue); box-shadow: 0 14px 28px rgba(17,39,83,.22); }
.as-btn-secondary { color: #fff; border: 1px solid rgba(255,255,255,.35); }
.as-btn-secondary:hover { color: #fff; background: rgba(255,255,255,.12); }
.as-visual-card { background: #fff; border: 1px solid rgba(255,255,255,.35); border-radius: 8px; padding: 28px; box-shadow: 0 24px 60px rgba(10,24,55,.28); }
.as-card-label { color: var(--as-muted); font-size: .78rem; font-weight: 800; text-transform: uppercase; margin-bottom: 18px; }
.as-stack { display: grid; gap: 10px; }
.as-stack-item { border-radius: 8px; background: var(--as-soft); border: 1px solid var(--as-line); padding: 14px 16px; }
.as-stack-item strong { display: block; color: var(--as-blue-dark); font-size: .92rem; margin-bottom: 4px; }
.as-stack-item span { display: block; color: var(--as-muted); font-size: .82rem; line-height: 1.55; }
.as-stack-item.as-dark { background: var(--as-blue); border-color: var(--as-blue); }
.as-stack-item.as-dark strong, .as-stack-item.as-dark span { color: #fff; }
.as-proof { background: #172b52; color: rgba(255,255,255,.74); padding: 18px 0; font-size: .92rem; }
.as-proof .as-container { display: flex; justify-content: center; gap: 18px; flex-wrap: wrap; text-align: center; }
.as-proof strong { color: #fff; }
.as-section { padding: 76px 0; }
.as-section-soft { background: var(--as-soft); }
.as-section-blue { background: linear-gradient(135deg, #1e3a6d, #2f5597); color: #fff; }
.as-label { color: var(--as-blue); font-size: .78rem; font-weight: 800; text-transform: uppercase; margin-bottom: 10px; }
.as-section-blue .as-label { color: #ffd166; }
.as-heading { color: var(--as-ink); font-size: clamp(1.8rem, 3vw, 2.45rem); line-height: 1.22; font-weight: 800; margin: 0 0 14px; max-width: 780px; }
.as-section-blue .as-heading, .as-section-blue h3, .as-section-blue h4 { color: #fff; }
.as-sub { color: var(--as-muted); font-size: 1rem; line-height: 1.78; max-width: 710px; margin: 0 0 36px; }
.as-section-blue .as-sub, .as-section-blue p { color: rgba(255,255,255,.78); }
.as-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 22px; }
.as-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 34px; align-items: center; }
.as-card { height: 100%; background: #fff; border: 1px solid var(--as-line); border-radius: 8px; padding: 28px; box-shadow: 0 8px 24px rgba(31,64,121,.06); }
.as-card-icon { width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; background: #eaf2ff; color: var(--as-blue); font-size: 1.2rem; margin-bottom: 18px; }
.as-card h3, .as-step h3 { color: var(--as-ink); font-size: 1.08rem; font-weight: 800; line-height: 1.4; margin: 0 0 10px; }
.as-card p, .as-step p, .as-faq p { color: var(--as-muted); font-size: .94rem; line-height: 1.72; margin: 0; }
.as-step { border-left: 3px solid #cddcf3; padding-left: 22px; }
.as-step-num { color: var(--as-blue); font-size: .76rem; font-weight: 800; text-transform: uppercase; margin-bottom: 9px; }
.as-case-list { display: grid; gap: 14px; }
.as-case { border: 1px solid rgba(255,255,255,.15); border-radius: 8px; background: rgba(255,255,255,.08); padding: 20px; }
.as-case-label { color: #ffd166; font-size: .75rem; font-weight: 800; text-transform: uppercase; margin-bottom: 7px; }
.as-list { list-style: none; margin: 22px 0 0; padding: 0; display: grid; gap: 14px; }
.as-list li { color: #33435f; font-size: .94rem; line-height: 1.65; padding-left: 24px; position: relative; }
.as-list li::before { content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900; color: var(--as-blue); position: absolute; left: 0; top: 1px; font-size: .78rem; }
.as-faq { max-width: 820px; }
.as-faq-item { border-bottom: 1px solid var(--as-line); padding: 22px 0; }
.as-faq h3 { color: var(--as-ink); font-size: 1rem; font-weight: 800; margin: 0 0 8px; }
.as-final-cta { text-align: center; }
.as-final-cta .as-heading, .as-final-cta .as-sub { margin-left: auto; margin-right: auto; }
@media (max-width: 991px) { .as-hero-grid, .as-grid-2 { grid-template-columns: 1fr; } .as-grid-3 { grid-template-columns: 1fr; } .as-hero { padding: 70px 0 54px; } }
@media (max-width: 575px) { .as-container { width: min(100% - 24px, 1120px); } .as-section { padding: 56px 0; } .as-visual-card, .as-card { padding: 22px; } .as-actions { align-items: stretch; } .as-btn { width: 100%; } }

</style>
@endpush

@section('content')
<div class="armely-solution-page">
    <section class="as-hero">
        <div class="as-container as-hero-grid">
            <div>
                <div class="as-eyebrow"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Microsoft SQL Server services</div>
                <h1>Your database, running the way your business demands.</h1>
                <p>Armely designs, implements, migrates, and manages Microsoft SQL Server environments so your critical data is fast, secure, always available, and ready for AI workloads.</p>
                <div class="as-actions">
                    <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Book a discovery call <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="#overview" class="as-btn as-btn-secondary">Explore the service</a>
                </div>
            </div>
            <div class="as-visual-card" aria-label="Microsoft SQL Server overview">
                <div class="as-card-label">What Armely helps you connect</div>
                <div class="as-stack">
                    <div class="as-stack-item">
                        <strong>Assess</strong>
                        <span>Current estate, risk, performance</span>
                    </div>
                    <div class="as-stack-item">
                        <strong>Modernize</strong>
                        <span>Upgrade, migrate, secure</span>
                    </div>
                    <div class="as-stack-item">
                        <strong>Optimize</strong>
                        <span>Tune, monitor, support</span>
                    </div>
                    <div class="as-stack-item as-dark">
                        <strong>AI-ready data</strong>
                        <span>Analytics and vector search</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="as-proof">
        <div class="as-container">
            <span><strong>Production database expertise</strong></span>
            <span><strong>Azure migration ready</strong></span>
            <span><strong>Security and performance focused</strong></span>
        </div>
    </div>

    <section class="as-section as-section-soft" id="overview">
        <div class="as-container">
            <div class="as-label">Overview</div>
            <h2 class="as-heading">The enterprise database platform behind some of the world&#x27;s most demanding workloads.</h2>
            <p class="as-sub">Microsoft SQL Server is a relational database management system used by organizations of every size to store, manage, and analyze business-critical data. The latest release extends that foundation with built-in AI capabilities, native vector search, real-time event streaming, and deep integration with Microsoft Fabric and Azure, making it one of the most capable data platforms available on-premises or in the cloud.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">🧠</div>
                    <h3>Built-In AI and Vector Search</h3>
                    <p>Run AI models and vector search directly in T-SQL. Build retrieval-augmented generation apps on your existing SQL data without moving it to a separate system.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🛡️</div>
                    <h3>Enterprise-Grade Security</h3>
                    <p>Always Encrypted, row-level security, dynamic data masking, and transparent data encryption protect sensitive data at rest and in transit.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">⚡</div>
                    <h3>High Availability</h3>
                    <p>Always On Availability Groups, failover clustering, and automated backups keep your databases online even during planned maintenance or unexpected failures.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🔗</div>
                    <h3>Fabric and Azure Integration</h3>
                    <p>SQL Server connects natively to Microsoft Fabric, Azure Synapse, and Power BI, making it the on-premises anchor for a hybrid analytics architecture.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section">
        <div class="as-container">
            <div class="as-label">What we deliver</div>
            <h2 class="as-heading">SQL Server expertise across the full database lifecycle.</h2>
            <p class="as-sub">From a first-time SQL Server installation to a complex migration off aging infrastructure, Armely covers every stage of your database environment with certified engineers and a structured delivery methodology.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">🗺️</div>
                    <h3>Environment Assessment</h3>
                    <p>We audit your existing SQL Server environment, including instance configurations, database sizes, query performance, backup strategies, and support status. You receive a clear picture of your current risk profile and a prioritized action plan.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">⚙️</div>
                    <h3>Installation and Configuration</h3>
                    <p>New SQL Server deployments configured to production standards from day one, including memory allocation, tempdb optimization, backup schedules, maintenance plans, and security hardening aligned to Microsoft best practices.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🚀</div>
                    <h3>Upgrades and Migration</h3>
                    <p>We plan and execute SQL Server upgrades and migrations, including end-of-support remediation, lift-and-shift to Azure, and migrations to Azure SQL Managed Instance, with compatibility testing and minimal downtime cutover plans.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📈</div>
                    <h3>Performance Tuning</h3>
                    <p>We identify and resolve performance bottlenecks through query analysis, index optimization, execution plan review, and wait statistics analysis. Slow queries and blocked processes are diagnosed with precision, not guesswork.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🛡️</div>
                    <h3>Security and Compliance</h3>
                    <p>We implement SQL Server security controls including role-based access, data classification, auditing, encryption at rest and in transit, and vulnerability assessments to meet HIPAA, SOC 2, and other compliance requirements.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🤝</div>
                    <h3>Managed DBA Services</h3>
                    <p>Ongoing database administration on a managed basis, covering monitoring, alerting, patching, backup verification, capacity planning, and a dedicated Armely contact available when issues arise.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-soft" id="process">
        <div class="as-container">
            <div class="as-label">Delivery approach</div>
            <h2 class="as-heading">From assessment to production, on a timeline you can plan around.</h2>
            <p class="as-sub">Whether you need an urgent upgrade before an end-of-support deadline or a planned migration to the cloud, we deliver against a structured timeline with clear milestones and no surprises at go-live.</p>
            <div class="as-grid-3">
                <div class="as-step">
                    <div class="as-step-num">Step 01</div>
                    <h3>Discovery Assessment</h3>
                    <p>We audit your SQL Server estate, identify version risk, review performance and security posture, and document your business-critical databases.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 02</div>
                    <h3>Planning and Design</h3>
                    <p>We design your target environment, whether that is an in-place upgrade, a new server build, or a migration to Azure, and confirm the licensing approach at partner pricing.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 03</div>
                    <h3>Build and Test</h3>
                    <p>The target environment is built and configured. Databases are migrated or upgraded in a staging environment and validated for application compatibility.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 04</div>
                    <h3>Cutover and Validation</h3>
                    <p>Production cutover is executed during a planned maintenance window with rollback procedures in place. Armely validates performance and availability post-cutover.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 05</div>
                    <h3>Managed Support</h3>
                    <p>Ongoing monitoring, patching, performance management, and a dedicated Armely DBA contact for day-to-day database operations and strategic planning.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="as-section" id="use-cases">
        <div class="as-container">
            <div class="as-label">Use cases</div>
            <h2 class="as-heading">The database situations we see most, and solve every time.</h2>
            <p class="as-sub">Every SQL Server environment is different, but the business problems that drive organizations to call Armely tend to follow recognizable patterns.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">⏰</div>
                    <h3>End-of-Support Remediation</h3>
                    <p>Running a version of SQL Server past its extended support end date exposes your organization to unpatched security vulnerabilities. We assess the risk, plan the path forward, and execute the upgrade or migration before a breach or audit finding forces your hand.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🐢</div>
                    <h3>Slow Database Performance</h3>
                    <p>Application slowdowns, report timeouts, and blocked transactions often trace back to missing indexes, poor query plans, or misconfigured memory settings. We diagnose the root cause and resolve it, not just the symptom.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">☁️</div>
                    <h3>Cloud Migration</h3>
                    <p>We migrate SQL Server workloads to Azure, including lift-and-shift to Azure VMs, migration to Azure SQL Managed Instance, and hybrid configurations with Azure Arc, with full compatibility testing before cutover.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🔒</div>
                    <h3>Security and Compliance Gaps</h3>
                    <p>We conduct SQL Server vulnerability assessments against HIPAA, SOC 2, and CIS benchmark standards, implement required controls, and produce the audit documentation your compliance team needs.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📊</div>
                    <h3>Connect SQL Server to Analytics</h3>
                    <p>We configure SQL Server as the on-premises data source for Microsoft Fabric, Power BI, and Azure Synapse, establishing the data pipelines that give your analytics team access to live operational data.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🧠</div>
                    <h3>AI Readiness on Existing Data</h3>
                    <p>The latest SQL Server release introduces native vector search and AI model integration directly in T-SQL. We help organizations evaluate and implement these capabilities on their existing SQL Server data estate without a full platform migration.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-blue">
        <div class="as-container as-grid-2">
            <div>
                <div class="as-label">Why Armely</div>
                <h2 class="as-heading">Database expertise backed by real delivery experience.</h2>
                <p class="as-sub">SQL Server is the engine behind some of your most critical business processes. The partner you choose to manage or migrate it needs proven credentials and a track record in environments where getting it wrong is not an option.</p>
                <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Talk with Armely</a>
            </div>
            <div class="as-case-list">
                <article class="as-case">
                    <div class="as-case-label">Partner advantage</div>
                    <h4>Certified delivery with practical implementation discipline</h4>
                    <p>Armely combines platform expertise, business process design, governance, and ongoing support so your solution is adopted after launch.</p>
                </article>
                <article class="as-case">
                    <div class="as-case-label">What matters</div>
                    <h4>Built around your workflows</h4>
                    <p>We configure the platform around how your teams actually work, then document and train so ownership remains with your organization.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-soft" id="faq">
        <div class="as-container">
            <div class="as-label">Key strengths</div>
            <h2 class="as-heading">What you get with Armely</h2>
            <div class="as-card">
                <ul class="as-list">
                    <li>🎯 Certified SQL Server Engineers Our engineers hold Microsoft certifications in SQL Server administration and Azure database services, with production experience across on-premises, hybrid, and cloud deployments.</li>
                    <li>🏥 Experience in Regulated Industries We have delivered database projects for Swope Health Systems and the University of Nebraska Medical Center, where HIPAA compliance, audit readiness, and zero-downtime requirements are non-negotiable.</li>
                    <li>🔗 Full Microsoft Stack Coverage SQL Server does not exist in isolation. Armely covers the surrounding Microsoft ecosystem, including Azure, Microsoft Fabric, Power BI, and Microsoft 365, so your database architecture is designed to work with your analytics and application stack from the start.</li>
                    <li>💰 Licensing at Partner Pricing As a Microsoft-authorized CSP partner, we source SQL Server and Azure SQL licensing at rates not available through direct purchase, and we help you select the right edition and deployment model for your workload and budget.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="as-section as-section-blue as-final-cta" id="contact">
        <div class="as-container">
            <div class="as-label">Start the conversation</div>
            <h2 class="as-heading">Let&#x27;s review your SQL Server environment.</h2>
            <p class="as-sub">Book a free 30-minute discovery call. We will review your current SQL Server deployment, identify any end-of-support risk or performance concerns, and provide a clear recommendation and pricing proposal with no obligation.</p>
            <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Book a discovery call <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</div>
@endsection
