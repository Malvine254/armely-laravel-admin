@extends('layouts.public')

@section('title', 'Armely - Microsoft Dynamics 365')
@section('meta_description', 'Dynamics 365 CRM and ERP implementation, customization, integration, training, and support by Armely.')
@section('meta_keywords', 'Dynamics 365, Business Central, CRM, ERP, Microsoft partner, Armely')
@section('canonical_url', url('/dynamics-365'))

@push('head')
<meta property="og:title" content="Armely - Microsoft Dynamics 365">
<meta property="og:description" content="Dynamics 365 CRM and ERP implementation, customization, integration, training, and support by Armely.">
<meta property="og:url" content="{{ url('/dynamics-365') }}">
<meta name="twitter:title" content="Armely - Microsoft Dynamics 365">
<meta name="twitter:description" content="Dynamics 365 CRM and ERP implementation, customization, integration, training, and support by Armely.">
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
                <div class="as-eyebrow"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Microsoft Dynamics 365 services</div>
                <h1>CRM and ERP, finally working as one.</h1>
                <p>Armely implements and customizes Microsoft Dynamics 365 so your sales, finance, operations, and customer service teams share the same data, and the same source of truth.</p>
                <div class="as-actions">
                    <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Book a discovery call <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="#overview" class="as-btn as-btn-secondary">Explore the service</a>
                </div>
            </div>
            <div class="as-visual-card" aria-label="Microsoft Dynamics 365 overview">
                <div class="as-card-label">What Armely helps you connect</div>
                <div class="as-stack">
                    <div class="as-stack-item">
                        <strong>CRM</strong>
                        <span>Sales, service, customer insight</span>
                    </div>
                    <div class="as-stack-item">
                        <strong>ERP</strong>
                        <span>Finance, inventory, operations</span>
                    </div>
                    <div class="as-stack-item">
                        <strong>Automation</strong>
                        <span>Power Platform and Copilot</span>
                    </div>
                    <div class="as-stack-item as-dark">
                        <strong>One source of truth</strong>
                        <span>Shared business data</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="as-proof">
        <div class="as-container">
            <span><strong>CRM and ERP alignment</strong></span>
            <span><strong>Microsoft stack expertise</strong></span>
            <span><strong>Training and support included</strong></span>
        </div>
    </div>

    <section class="as-section as-section-soft" id="overview">
        <div class="as-container">
            <div class="as-label">Overview</div>
            <h2 class="as-heading">One platform for every team that touches your customer or your numbers.</h2>
            <p class="as-sub">Dynamics 365 is Microsoft&#x27;s cloud platform that unifies CRM and ERP into a single, modular system. You choose the apps your business needs today and add more as you grow, all sharing the same data, the same security model, and the same Copilot AI layer.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">💼</div>
                    <h3>Business Central</h3>
                    <p>All-in-one ERP for SMBs covering finance, inventory, purchasing, projects, and manufacturing in one place. Forbes&#x27; #1 cloud ERP for SMBs in 2025.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🎯</div>
                    <h3>Sales</h3>
                    <p>AI-powered CRM that automates lead research, drafts emails, summarises opportunities, and surfaces deal risks before they cost you.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🎧</div>
                    <h3>Customer Service</h3>
                    <p>Case management, AI routing, and autonomous agents that resolve issues faster across voice, chat, and digital channels.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🔧</div>
                    <h3>Field Service</h3>
                    <p>AI scheduling, work order management, and proactive maintenance for teams that send technicians to customers, with 65%+ first-time fix rate improvements reported.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📣</div>
                    <h3>Customer Insights</h3>
                    <p>Unified customer profiles and AI-driven marketing journeys that engage the right person at the right moment across every channel.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section">
        <div class="as-container">
            <div class="as-label">What we deliver</div>
            <h2 class="as-heading">Implementation that fits your business, not the other way around.</h2>
            <p class="as-sub">Dynamics 365 is powerful out of the box and endlessly configurable. Armely makes sure you get the right modules, the right configuration, and the right training without months of scope creep.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">🗺️</div>
                    <h3>Business Process Discovery</h3>
                    <p>Before touching a single setting, we map how your business actually works: your sales process, finance workflows, service operations, and reporting needs, and design a Dynamics 365 configuration that fits them.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">⚙️</div>
                    <h3>Configuration &amp; Customization</h3>
                    <p>We configure Dynamics 365 to match your workflows, terminology, and approval processes. Where standard configuration isn&#x27;t enough, we extend with Power Apps and Power Automate rather than bespoke code that breaks on upgrades.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🔗</div>
                    <h3>System Integration</h3>
                    <p>We connect Dynamics 365 to your existing tools, including accounting software, e-commerce platforms, marketing systems, and data sources, so information flows automatically and your team stops re-entering the same data twice.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📦</div>
                    <h3>Data Migration</h3>
                    <p>We migrate your customer records, financial history, open orders, and contact data from your legacy system into Dynamics 365, clean, validated, and complete. No fresh starts, no lost history.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🤖</div>
                    <h3>Copilot AI Configuration</h3>
                    <p>Dynamics 365 Copilot agents are activated and tuned for your team, including drafting sales emails, summarizing service cases, proposing journal entries, and automating scheduling. AI that works in your context, not a generic demo.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🎓</div>
                    <h3>Training &amp; Ongoing Support</h3>
                    <p>Role-specific training for every team, plus a dedicated Armely account manager for post-go-live support, new module rollouts, and the inevitable &quot;can we add this?&quot; requests that come six months in.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-soft" id="process">
        <div class="as-container">
            <div class="as-label">Delivery approach</div>
            <h2 class="as-heading">From messy spreadsheets and disconnected tools to one system that works.</h2>
            <p class="as-sub">We follow Microsoft&#x27;s Success by Design methodology, refined through real implementations across healthcare, education, and professional services, so your go-live is an event, not a crisis.</p>
            <div class="as-grid-3">
                <div class="as-step">
                    <div class="as-step-num">Step 01</div>
                    <h3>Discovery &amp; Scoping</h3>
                    <p>We document your processes, pain points, and must-haves. You get a clear module recommendation and implementation plan before committing.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 02</div>
                    <h3>Licensing &amp; Design</h3>
                    <p>We source the right licenses at partner pricing and design your Dynamics 365 environment, data model, and integration architecture.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 03</div>
                    <h3>Build &amp; Configure</h3>
                    <p>Configuration, customization, integrations, and data migration, built iteratively with your team&#x27;s input at every checkpoint.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 04</div>
                    <h3>Test &amp; Go Live</h3>
                    <p>User acceptance testing, parallel running where needed, and a managed go-live with Armely on hand for every issue on day one.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 05</div>
                    <h3>Optimise &amp; Grow</h3>
                    <p>Post-go-live support, adoption tracking, release wave updates, and new modules added as your business evolves.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="as-section" id="use-cases">
        <div class="as-container">
            <div class="as-label">Use cases</div>
            <h2 class="as-heading">Real business problems, solved with Dynamics 365.</h2>
            <p class="as-sub">Every Dynamics 365 engagement is different, but these are the situations we hear most often, and where a well-implemented system delivers the clearest, fastest return.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">📋</div>
                    <h3>Replace Disconnected Tools</h3>
                    <p>Retiring a mix of Sage, spreadsheets, and a legacy CRM into one platform. Sales sees customer history. Finance sees open orders. Service sees account status. Everyone works from the same data.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">💰</div>
                    <h3>Automate Finance &amp; Reporting</h3>
                    <p>Business Central automates AP/AR, period-end closing, bank reconciliation, and cash flow forecasting, so your finance team stops spending three days on month-end and starts spending an hour.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🎯</div>
                    <h3>Give Sales Teams an Edge</h3>
                    <p>Dynamics 365 Sales with Copilot researches leads, drafts outreach emails, surfaces deal risks, and keeps CRM updated automatically, so your sellers sell instead of administrate.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🔧</div>
                    <h3>Field Service That Predicts Problems</h3>
                    <p>AI scheduling dispatches the right technician with the right parts. IoT-connected assets trigger work orders automatically. Customers get proactive updates before they call you.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📦</div>
                    <h3>Take Control of Inventory</h3>
                    <p>Real-time inventory, purchase order automation, and demand forecasting in Business Central mean you stop running out of stock and avoid tying up cash in stock you don&#x27;t need.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📊</div>
                    <h3>Leadership Dashboards That Update Themselves</h3>
                    <p>Power BI connected to live Dynamics 365 data gives leadership real-time visibility across sales pipeline, service performance, cash position, and operations without weekly report runs.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-blue">
        <div class="as-container as-grid-2">
            <div>
                <div class="as-label">Why Armely</div>
                <h2 class="as-heading">Dynamics 365 implementations succeed when the partner knows your industry.</h2>
                <p class="as-sub">Most Dynamics 365 projects that struggle do so because of poor requirements gathering, generic configuration, and training that didn&#x27;t match how people actually work. We&#x27;ve built our process to fix all three.</p>
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
                    <li>🎯 Certified Dynamics 365 Implementors Our team holds Microsoft Dynamics 365 certifications across Business Central, Sales, and Customer Service, with hands-on delivery experience, not just exam passes.</li>
                    <li>🏥 Proven Across Healthcare &amp; Education We&#x27;ve delivered Microsoft solutions for Swope Health Systems, Plano ISD, and UNMC, organizations where data governance, compliance, and user adoption all matter equally.</li>
                    <li>🔗 Full Microsoft Stack Expertise Dynamics 365 works best alongside Microsoft 365, Power BI, Power Platform, and Azure. Armely covers the whole stack so your ERP, CRM, AI, and analytics are designed to work together from day one.</li>
                    <li>💰 Right Licenses at Partner Pricing As a Microsoft-authorized CSP partner, we access Business Central and Dynamics 365 licensing at rates not available through direct purchase, and we help you start with exactly what you need, not a bundle you&#x27;ll never use.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="as-section as-section-blue as-final-cta" id="contact">
        <div class="as-container">
            <div class="as-label">Start the conversation</div>
            <h2 class="as-heading">Let&#x27;s find the right Dynamics 365 fit for your business.</h2>
            <p class="as-sub">Book a free 30-minute discovery call. We&#x27;ll understand your current tools and processes, identify which Dynamics 365 modules apply to your situation, and come back with a clear implementation proposal and licensing quote, with no obligation.</p>
            <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Book a discovery call <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</div>
@endsection
