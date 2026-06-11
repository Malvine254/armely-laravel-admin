@extends('layouts.public')

@section('title', 'Armely - Snowflake AI Data Cloud')
@section('meta_description', 'Snowflake architecture, implementation, data pipelines, BI, Cortex AI, and governance services from Armely.')
@section('meta_keywords', 'Snowflake, AI Data Cloud, data warehouse, Cortex AI, data engineering, Armely')
@section('canonical_url', url('/snowflake'))

@push('head')
<meta property="og:title" content="Armely - Snowflake AI Data Cloud">
<meta property="og:description" content="Snowflake architecture, implementation, data pipelines, BI, Cortex AI, and governance services from Armely.">
<meta property="og:url" content="{{ url('/snowflake') }}">
<meta name="twitter:title" content="Armely - Snowflake AI Data Cloud">
<meta name="twitter:description" content="Snowflake architecture, implementation, data pipelines, BI, Cortex AI, and governance services from Armely.">
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
                <div class="as-eyebrow"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Snowflake AI Data Cloud services</div>
                <h1>Your data, your cloud, no limits on scale.</h1>
                <p>Armely architects, implements, and manages Snowflake environments that give your business a fast, governed, AI-ready data platform — without the infrastructure headaches.</p>
                <div class="as-actions">
                    <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Book a discovery call <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="#overview" class="as-btn as-btn-secondary">Explore the service</a>
                </div>
            </div>
            <div class="as-visual-card" aria-label="Snowflake AI Data Cloud overview">
                <div class="as-card-label">What Armely helps you connect</div>
                <div class="as-stack">
                    <div class="as-stack-item">
                        <strong>Ingest</strong>
                        <span>Apps, databases, files, streams</span>
                    </div>
                    <div class="as-stack-item">
                        <strong>Transform</strong>
                        <span>dbt, Snowpark, governed models</span>
                    </div>
                    <div class="as-stack-item">
                        <strong>Analyze</strong>
                        <span>BI dashboards and live reporting</span>
                    </div>
                    <div class="as-stack-item as-dark">
                        <strong>AI-ready</strong>
                        <span>Cortex AI and secure sharing</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="as-proof">
        <div class="as-container">
            <span><strong>Cloud-native data platform</strong></span>
            <span><strong>AI-ready architecture</strong></span>
            <span><strong>Cost-aware implementation</strong></span>
        </div>
    </div>

    <section class="as-section as-section-soft" id="overview">
        <div class="as-container">
            <div class="as-label">Overview</div>
            <h2 class="as-heading">The cloud data platform built for analytics, AI, and scale.</h2>
            <p class="as-sub">Snowflake is the AI Data Cloud — a fully managed platform that separates compute from storage so you can scale each independently, query structured and unstructured data in the same place, and run AI workloads directly on your data using Cortex AI. No infrastructure to manage. No performance tuning. No data silos.</p>
            <div class="as-grid-3">

            </div>
        </div>
    </section>

    <section class="as-section">
        <div class="as-container">
            <div class="as-label">What we deliver</div>
            <h2 class="as-heading">End-to-end Snowflake implementation — from first query to production.</h2>
            <p class="as-sub">Armely handles every layer of your Snowflake environment — architecture, ingestion, transformation, analytics, and AI — so your team spends time on insights, not infrastructure.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">🗺️</div>
                    <h3>Architecture &amp; Environment Setup</h3>
                    <p>We design your Snowflake account structure, virtual warehouse sizing, role hierarchy, and network policies before writing a single query — so performance and cost are right from day one.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🔌</div>
                    <h3>Data Ingestion &amp; Pipelines</h3>
                    <p>We connect your source systems — databases, SaaS apps, files, and streams — into Snowflake using Snowpipe, Fivetran, dbt, or custom pipelines. Fresh data, on schedule, automatically.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🏗️</div>
                    <h3>Data Modelling &amp; Transformation</h3>
                    <p>We build clean, governed data models using dbt or Snowpark so every dashboard and report draws from a consistent, trusted source. No more conflicting numbers across teams.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">📊</div>
                    <h3>Analytics &amp; BI Dashboards</h3>
                    <p>We connect your BI tool of choice — Power BI, Tableau, Sigma, Looker — to Snowflake and build the dashboards your business actually needs. Fast, accurate, and always live.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🤖</div>
                    <h3>Cortex AI Implementation</h3>
                    <p>We configure Snowflake Cortex so your analysts can run sentiment analysis, LLM completions, and natural-language queries directly in SQL — AI on your data, with no data leaving Snowflake.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🛡️</div>
                    <h3>Governance &amp; Ongoing Management</h3>
                    <p>Horizon governance, dynamic data masking, row-level access policies, and cost monitoring — configured from the start. Plus a dedicated Armely contact for ongoing optimisation and support.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-soft" id="process">
        <div class="as-container">
            <div class="as-label">Delivery approach</div>
            <h2 class="as-heading">From legacy data stack to cloud-native analytics — on a clear timeline.</h2>
            <p class="as-sub">Whether you&#x27;re migrating from an on-premise warehouse, consolidating cloud tools, or starting fresh, we follow a proven methodology that gets you to production fast and right.</p>
            <div class="as-grid-3">
                <div class="as-step">
                    <div class="as-step-num">Step 01</div>
                    <h3>Discovery &amp; Assessment</h3>
                    <p>We audit your current data stack, sources, and analytics needs. Free for new clients — results in a clear Snowflake migration or build plan.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 02</div>
                    <h3>Architecture &amp; Licensing</h3>
                    <p>We design your Snowflake environment and source the right capacity at partner pricing — sized for today, scalable for tomorrow.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 03</div>
                    <h3>Build &amp; Migrate</h3>
                    <p>Pipelines, data models, and initial dashboards built and validated against your real data. Migrations handled without downtime.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 04</div>
                    <h3>Handover &amp; Training</h3>
                    <p>Full documentation, runbooks, and role-specific training so your team owns the environment and can extend it independently.</p>
                </div>
                <div class="as-step">
                    <div class="as-step-num">Step 05</div>
                    <h3>Managed Support</h3>
                    <p>Cost optimisation, performance tuning, new workload onboarding, and a single Armely contact as your Snowflake environment grows.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="as-section" id="use-cases">
        <div class="as-container">
            <div class="as-label">Use cases</div>
            <h2 class="as-heading">Snowflake in practice — across every industry.</h2>
            <p class="as-sub">From consolidating a fragmented data stack to running AI models on live business data, here&#x27;s what Armely-built Snowflake environments deliver.</p>
            <div class="as-grid-3">
                <article class="as-card">
                    <div class="as-card-icon">🏢</div>
                    <h3>Data Warehouse Modernisation</h3>
                    <p>Migrate off on-premise SQL Server, Oracle, or Teradata to a fully managed cloud warehouse that scales automatically and costs a fraction of what you&#x27;re paying today.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🔗</div>
                    <h3>Multi-Cloud Data Consolidation</h3>
                    <p>Pull data from AWS, Azure, and GCP into a single governed platform. Snowflake runs across all three clouds — your data follows your business, not the other way round.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🧠</div>
                    <h3>AI &amp; Machine Learning</h3>
                    <p>Use Snowpark to train ML models on your Snowflake data without moving it. Deploy Cortex AI functions to enrich records with sentiment, classification, and LLM completions — all in SQL.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">🤝</div>
                    <h3>Secure Data Sharing</h3>
                    <p>Share live data with partners, suppliers, or customers without copying or moving it. Snowflake&#x27;s zero-copy sharing means collaborators see the same data you do, in real time.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">⚡</div>
                    <h3>Real-Time Analytics</h3>
                    <p>Snowpipe Streaming and Dynamic Tables ingest and transform data in near real time. Operations, finance, and customer teams get dashboards that reflect what&#x27;s happening now.</p>
                </article>
                <article class="as-card">
                    <div class="as-card-icon">💬</div>
                    <h3>Natural Language Queries</h3>
                    <p>Snowflake Intelligence lets anyone ask questions in plain English and get answers from your live Snowflake data — no SQL skills needed. The right insight, to the right person, instantly.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="as-section as-section-blue">
        <div class="as-container as-grid-2">
            <div>
                <div class="as-label">Why Armely</div>
                <h2 class="as-heading">Snowflake expertise, delivered at the pace your business needs.</h2>
                <p class="as-sub">We&#x27;re not a generalist IT firm that dabbles in data. Armely has built data platforms for healthcare, education, and enterprise clients — and we bring that depth to every Snowflake engagement.</p>
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
                    <li>🎯 Certified Snowflake Implementors Our team carries Snowflake implementation certifications and hands-on experience across data engineering, Snowpark, Cortex AI, and dbt — not just SnowPro Core.</li>
                    <li>🏥 Proven in Regulated Industries We&#x27;ve delivered data projects for Swope Health Systems and UNMC — environments with strict HIPAA and data governance requirements. We know how to build secure by design.</li>
                    <li>💰 Cost Optimisation From Day One Snowflake bills by compute consumption. We right-size warehouses, implement auto-suspend, and monitor query efficiency so your bill reflects your usage — not our oversight.</li>
                    <li>🤝 You Own Everything Full documentation, source-controlled pipelines, and team training from day one. We build to hand over — not to create a support dependency.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="as-section as-section-blue as-final-cta" id="contact">
        <div class="as-container">
            <div class="as-label">Start the conversation</div>
            <h2 class="as-heading">Let&#x27;s talk about your data stack.</h2>
            <p class="as-sub">Book a free 30-minute discovery call. We&#x27;ll review your current environment, understand what you need to answer with your data, and come back with a clear Snowflake implementation proposal — no obligation.</p>
            <a href="{{ route('contact') }}" class="as-btn as-btn-primary">Book a discovery call <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</div>
@endsection
