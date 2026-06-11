@extends('layouts.public')

@section('title', 'Armely - Microsoft Power Platform')
@section('meta_description', 'Power Platform solutions for apps, workflow automation, dashboards, portals, and AI agents delivered by Armely.')
@section('meta_keywords', 'Power Platform, Power Apps, Power Automate, Power BI, Power Pages, Copilot Studio, Armely')
@section('canonical_url', url('/power-platform'))

@push('head')
<meta property="og:title" content="Armely - Microsoft Power Platform">
<meta property="og:description" content="Power Platform solutions for apps, workflow automation, dashboards, portals, and AI agents delivered by Armely.">
<meta property="og:url" content="{{ url('/power-platform') }}">
<meta name="twitter:title" content="Armely - Microsoft Power Platform">
<meta name="twitter:description" content="Power Platform solutions for apps, workflow automation, dashboards, portals, and AI agents delivered by Armely.">
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
                <div class="as-eyebrow"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Microsoft Power Platform services</div>
                <h1>Build apps. Automate work. Eliminate the tools gap.</h1>
                <p>Armely designs and delivers Microsoft Power Platform solutions that replace manual processes, connect your business systems, and put the right information in front of the right people, without months of custom development.</p>
                <div class="as-actions">
                    <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Book a discovery call <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="#overview" class="as-btn as-btn-secondary">Explore the service</a>
                </div>
            </div>
            <div class="as-visual-card" aria-label="Microsoft Power Platform overview">
                <div class="as-card-label">What Armely helps you connect</div>
                <div class="as-stack">
                    <div class="as-stack-item">
                        <strong>Power Apps</strong>
                        <span>Custom apps</span>
                    </div>
                    <div class="as-stack-item">
                        <strong>Power Automate</strong>
                        <span>Workflow automation</span>
                    </div>
                    <div class="as-stack-item">
                        <strong>Power BI</strong>
                        <span>Live dashboards</span>
                    </div>
                    <div class="as-stack-item as-dark">
                        <strong>Power Pages + Copilot Studio</strong>
                        <span>Portals and AI agents</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="as-proof">
        <div class="as-container">
            <span><strong>Microsoft low-code delivery</strong></span>
            <span><strong>Governance built in</strong></span>
            <span><strong>Apps live in weeks</strong></span>
        </div>
    </div>

    <section class="as-section as-section-soft" id="overview">
        <div class="as-container">
            <div class="as-label">Overview</div>
            <h2 class="as-heading">Five products that work individually and are far more powerful together.</h2>
            <p class="as-sub">Power Platform is Microsoft&#x27;s low-code suite for building applications, automating workflows, analyzing data, and creating external-facing websites, all sharing the same data layer, security model, and Copilot AI capabilities. Most organizations start with one product and expand as they see what is possible.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">📱</div>
                    <h3>Power Apps</h3>
                    <p>Build custom business applications without extensive development. Canvas apps for flexible UI design, model-driven apps for data-centric workflows.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">⚡</div>
                    <h3>Power Automate</h3>
                    <p>Automate repetitive tasks and multi-system workflows using cloud flows, desktop flows for RPA, and AI-assisted process mining.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📊</div>
                    <h3>Power BI</h3>
                    <p>Transform raw data into interactive dashboards and reports that give every team in your organization clear, real-time visibility.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🌐</div>
                    <h3>Power Pages</h3>
                    <p>Build secure, professional external-facing websites and portals connected to your business data, with low-code design tools and built-in governance.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🤖</div>
                    <h3>Copilot Studio</h3>
                    <p>Build AI agents and custom copilots that answer questions, take actions, and automate workflows across your business, connected to your own data and systems.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section">
        <div class="as-container">
            <div class="as-label">What we deliver</div>
            <h2 class="as-heading">Solutions that solve real business problems, not technical exercises.</h2>
            <p class="as-sub">Armely builds Power Platform solutions around your workflows and business outcomes. Every engagement starts with understanding the problem before selecting the product.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">📱</div>
                    <h3>Custom Application Development</h3>
                    <p>We build Power Apps solutions that replace paper forms, spreadsheet-based processes, and legacy internal tools. From simple data capture apps to complex model-driven applications connected to Dataverse, built and delivered in weeks.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">⚡</div>
                    <h3>Workflow and Process Automation</h3>
                    <p>We automate approval workflows, document routing, data synchronization across systems, and repetitive desktop tasks using Power Automate cloud flows and RPA. We identify the highest-value processes to automate first, so you see return quickly.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📊</div>
                    <h3>Power BI Dashboards and Reports</h3>
                    <p>We connect Power BI to your data sources, build semantic models, and design dashboards that give leadership and operations teams real-time visibility across the business. Reports that get opened every morning, not quarterly.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🌐</div>
                    <h3>External Portals with Power Pages</h3>
                    <p>We build customer portals, partner sites, and application forms using Power Pages, connected to your Dataverse or Dynamics 365 data. Secure external access to business data without custom web development costs.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🤖</div>
                    <h3>AI Agents and Copilot Studio</h3>
                    <p>We design and deploy AI agents using Copilot Studio that answer employee or customer questions, trigger workflows, and surface business data in natural language conversations, connected to your own systems and data sources.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🛡️</div>
                    <h3>Governance and Center of Excellence</h3>
                    <p>Power Platform grows fast and ungoverned environments become liabilities. We implement the Microsoft Center of Excellence Starter Kit, DLP policies, managed environments, and admin tooling so your investment scales safely.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-soft" id="process">
        <div class="as-container">
            <div class="as-label">Delivery approach</div>
            <h2 class="as-heading">From process pain point to working solution, on a clear timeline.</h2>
            <p class="as-sub">Power Platform&#x27;s strength is speed. Our delivery approach is designed to get you to a working solution fast, validate it with real users, and expand from there rather than spending months in design before anyone sees anything.</p>
            <div class="as-grid-3">
                <div class="as-step">
                    <div class="as-step-num">Step 01</div>
                    <h3>Discovery Workshop</h3>
                    <p>We identify your highest-value automation and application opportunities, map your data sources, and confirm which Power Platform products apply to your situation.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 02</div>
                    <h3>Solution Design</h3>
                    <p>We design the solution architecture, confirm licensing requirements at partner pricing, and align on scope before any build work begins.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 03</div>
                    <h3>Build and Review</h3>
                    <p>We build iteratively with regular checkpoints so you see working software throughout the project, not just at the end.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 04</div>
                    <h3>Training and Launch</h3>
                    <p>User training, administrator documentation, and a managed go-live with Armely available to support your team through the first weeks of adoption.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 05</div>
                    <h3>Expand and Govern</h3>
                    <p>Additional solutions built on the same platform foundation, with governance controls and admin tooling that keep your Power Platform environment healthy as it grows.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="as-section" id="use-cases">
        <div class="as-container">
            <div class="as-label">Use cases</div>
            <h2 class="as-heading">What organizations build and automate with Power Platform.</h2>
            <p class="as-sub">These are the scenarios we deliver most frequently, across industries and organization sizes. Most start with one clear problem and expand from there.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">📋</div>
                    <h3>Replace Paper and Spreadsheet Processes</h3>
                    <p>Inspection forms, expense submissions, time tracking, onboarding checklists, and incident reports built as Power Apps and submitted directly to a central data store, with automated routing and notifications.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">✅</div>
                    <h3>Multi-Step Approval Workflows</h3>
                    <p>Purchase order approvals, contract reviews, leave requests, and capital expenditure sign-offs automated end to end, with Teams notifications, audit trails, and escalation paths when approvers do not respond.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📊</div>
                    <h3>Executive and Operational Dashboards</h3>
                    <p>Live Power BI reports connected to your ERP, CRM, or operational systems, giving leadership and team managers a single view of performance without waiting for weekly extracts or manual report runs.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🔗</div>
                    <h3>Cross-System Data Synchronization</h3>
                    <p>Automated flows that keep customer records, order data, and employee information consistent across Dynamics 365, SharePoint, external databases, and third-party systems without manual re-entry or batch file imports.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🌐</div>
                    <h3>Customer and Partner Portals</h3>
                    <p>Secure external portals where customers can submit requests, view their account status, upload documents, or complete applications, connected directly to your Dynamics 365 or Dataverse data.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">💬</div>
                    <h3>Internal AI Assistants</h3>
                    <p>Custom AI agents built in Copilot Studio that answer employee questions about HR policies, IT procedures, or product information by searching your actual documentation, not generic web content.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-blue">
        <div class="as-container as-grid-2">
            <div>
                <div class="as-label">Why Armely</div>
                <h2 class="as-heading">Power Platform works best when the implementor understands your business, not just the technology.</h2>
                <p class="as-sub">Low-code does not mean no skill required. The difference between a Power Platform solution that gets used and one that gets abandoned is whether it was designed around how people actually work.</p>
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
                    <li>🎯 Certified Power Platform Developers Our team holds Microsoft Power Platform certifications across Power Apps, Power Automate, and Power BI, with production delivery experience across healthcare, education, and professional services organizations.</li>
                    <li>🔗 Full Microsoft Ecosystem Coverage Power Platform integrates most deeply with Microsoft 365, Dynamics 365, Azure, and SQL Server. Armely covers all of these, so your Power Platform solutions are designed to work with your existing Microsoft environment from day one.</li>
                    <li>🛡️ Governance Built In from the Start Ungoverned Power Platform environments accumulate technical debt quickly. We implement DLP policies, naming standards, environment strategy, and admin tooling alongside every solution we deliver, not as an afterthought.</li>
                    <li>💰 Licensing at Partner Pricing As a Microsoft-authorized CSP partner, we source Power Platform licensing at rates not available through direct purchase and help you select the right license tier for your use case rather than overbuying.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="as-section as-section-blue as-final-cta" id="contact">
        <div class="as-container">
            <div class="as-label">Start the conversation</div>
            <h2 class="as-heading">Tell us the process you want to fix. We will show you what is possible.</h2>
            <p class="as-sub">Book a free 30-minute discovery call. We will review your current tools and workflows, identify the right Power Platform products for your situation, and come back with a solution proposal and licensing recommendation at no obligation.</p>
            <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Book a discovery call <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</div>
@endsection
