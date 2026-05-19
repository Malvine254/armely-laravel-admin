@extends('layouts.public')

@section('title', 'Mela AI')

@push('styles')
<style>
/* ============================================
   Mela AI Page Styles
   ============================================ */

/* Hero */
.mela-hero {
    background: linear-gradient(135deg, #1e3a6d 0%, #2f5597 50%, #4a72b5 100%);
    padding: 80px 0 60px;
    position: relative;
    overflow: hidden;
}
.mela-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
    pointer-events: none;
}
.mela-hero::after {
    content: '';
    position: absolute;
    bottom: -40%;
    left: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
    pointer-events: none;
}
.mela-hero-content {
    position: relative;
    z-index: 1;
}
.mela-hero h1 {
    font-size: 2.8rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 20px;
    line-height: 1.2;
}
.mela-hero h1 .highlight {
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.mela-hero p {
    font-size: 1.15rem;
    color: rgba(255,255,255,0.9);
    max-width: 600px;
    line-height: 1.7;
}
.mela-hero .badge-ai {
    display: inline-block;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    padding: 6px 18px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 20px;
    letter-spacing: 0.5px;
}
.mela-hero-img {
    text-align: center;
}
.mela-hero-img img {
    max-width: 280px;
    filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
    animation: floatBot 3s ease-in-out infinite;
}
@keyframes floatBot {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}

/* Section shared */
.mela-section {
    padding: 70px 0;
}
.mela-section .section-title {
    margin-bottom: 50px;
}
.mela-section .section-title h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #1e3a6d;
    margin-bottom: 12px;
}
.mela-section .section-title p {
    font-size: 1.05rem;
    color: #555;
    max-width: 700px;
    margin: 0 auto;
}
.mela-section .section-title hr {
    width: 60px;
    height: 3px;
    border: none;
    background: #2f5597;
    margin: 15px auto;
    border-radius: 2px;
}

/* Capability cards */
.capability-card {
    background: #fff;
    border-radius: 12px;
    padding: 35px 28px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
    border: 1px solid #f0f0f0;
    position: relative;
    overflow: hidden;
}
.capability-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2f5597, #667eea);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s;
}
.capability-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 35px rgba(47,85,151,0.12);
}
.capability-card:hover::before {
    transform: scaleX(1);
}
.capability-card .card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: #fff;
    margin-bottom: 20px;
}
.capability-card h4 {
    font-size: 1.15rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 12px;
}
.capability-card p {
    font-size: 0.95rem;
    color: #666;
    line-height: 1.65;
    margin: 0;
}

/* Chat demo section */
.mela-chat-section {
    background: #f8fafd;
}
.mela-chat-wrapper {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid #e8edf5;
}
.mela-chat-wrapper iframe {
    width: 100%;
    height: 500px;
    border: none;
}

/* How it works */
.step-card {
    text-align: center;
    padding: 30px 20px;
}
.step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2f5597, #4a72b5);
    color: #fff;
    font-size: 1.3rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
}
.step-card h5 {
    font-size: 1.05rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}
.step-card p {
    font-size: 0.92rem;
    color: #666;
    line-height: 1.6;
}

/* Use-case badges */
.use-case-badge {
    display: inline-block;
    background: #f0f4ff;
    color: #2f5597;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.92rem;
    font-weight: 500;
    margin: 6px;
    border: 1px solid #dce4f5;
    transition: all 0.2s;
}
.use-case-badge:hover {
    background: #2f5597;
    color: #fff;
    border-color: #2f5597;
}
.use-case-badge i {
    margin-right: 6px;
}

/* Powered-by section */
.powered-by {
    background: #fff;
}
.powered-by .tech-item {
    text-align: center;
    padding: 20px;
}
.powered-by .tech-item i {
    font-size: 2.5rem;
    color: #2f5597;
    margin-bottom: 12px;
    display: block;
}
.powered-by .tech-item h6 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}
.powered-by .tech-item p {
    font-size: 0.85rem;
    color: #888;
    margin: 0;
}

/* Video showcase */
.mela-video-showcase {
    background: linear-gradient(180deg, #f5f9ff 0%, #ffffff 100%);
}
.video-showcase-card {
    background: #fff;
    border: 1px solid #e7eef9;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 26px rgba(19, 52, 107, 0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    height: 100%;
}
.video-showcase-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 34px rgba(19, 52, 107, 0.14);
}
.video-embed {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    background: #0f172a;
}
.video-embed iframe {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: 0;
}
.video-meta {
    padding: 18px 18px 20px;
}
.video-meta h5 {
    font-size: 1rem;
    font-weight: 700;
    color: #18335e;
    margin-bottom: 8px;
}
.video-meta p {
    font-size: 0.92rem;
    color: #5b6780;
    margin: 0;
    line-height: 1.55;
}
.video-chip {
    display: inline-block;
    font-size: 0.78rem;
    font-weight: 600;
    color: #2f5597;
    background: #edf3ff;
    border: 1px solid #dbe6fb;
    border-radius: 999px;
    padding: 5px 10px;
    margin-bottom: 10px;
}

/* Compact suite update */
.mela-suite-kicker {
    display: inline-block;
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #2f5597;
    background: #eef4ff;
    border: 1px solid #d8e4fb;
    border-radius: 999px;
    padding: 6px 12px;
    margin-bottom: 16px;
}
.mela-product-card {
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    border: 1px solid #dbe6f8;
    border-radius: 16px;
    padding: 26px 24px;
    box-shadow: 0 12px 28px rgba(18, 48, 100, 0.08);
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}
.mela-product-card::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    height: 4px;
    background: linear-gradient(90deg, #2f5597, #4a72b5);
}
.mela-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 34px rgba(18, 48, 100, 0.13);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.mela-product-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.mela-product-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #2f5597;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 8px 18px rgba(47, 85, 151, 0.28);
}
.mela-product-label {
    font-size: 0.86rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #2f5597;
    text-transform: uppercase;
    margin-bottom: 10px;
}
.mela-product-card h4 {
    font-size: 1.28rem;
    color: #193c71;
    margin-bottom: 8px;
    font-weight: 800;
}
.mela-product-card h5 {
    font-size: 1.04rem;
    color: #2f5597;
    font-weight: 700;
    margin-bottom: 12px;
    min-height: 3.2em;
}
.mela-product-card p {
    font-size: 1.02rem;
    color: #40526d;
    line-height: 1.7;
    margin-bottom: 0;
}
.mela-mini-list {
    margin: 14px 0 0;
    padding-left: 18px;
}
.mela-mini-list li {
    font-size: 1rem;
    margin-bottom: 8px;
    color: #3a4d67;
    line-height: 1.65;
}
.mela-product-card details {
    margin-top: auto;
    border-top: 1px dashed #dce6f6;
    padding-top: 12px;
}
.mela-product-card summary {
    cursor: pointer;
    color: #2f5597;
    font-weight: 700;
}
.mela-product-card details p {
    margin-top: 10px;
}
.mela-integrations-wrap {
    margin-top: 26px;
    background: #f7faff;
    border: 1px solid #e1eaf8;
    border-radius: 12px;
    padding: 18px 16px;
}
.mela-integrations-title {
    font-size: 1rem;
    color: #2f5597;
    font-weight: 800;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.mela-integrations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 8px;
}
.mela-integration-chip {
    text-align: center;
    border: 1px solid #d8e4fb;
    background: #fff;
    color: #244b84;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 0.96rem;
    font-weight: 600;
}

.mela-products-row > [class*="col-"] {
    display: flex;
}

/* Suite icons use theme color */
.mela-product-card summary::marker,
.mela-product-card summary {
    color: #2f5597;
}

.mela-product-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
    margin-bottom: 6px;
}
.mela-product-tag {
    font-size: 0.8rem;
    font-weight: 700;
    color: #2f5597;
    background: #edf3ff;
    border: 1px solid #d8e4fb;
    border-radius: 999px;
    padding: 4px 10px;
}

@media (max-width: 768px) {
    .mela-hero { padding: 50px 0 40px; }
    .mela-hero h1 { font-size: 2rem; }
    .mela-hero-img img { max-width: 180px; margin-top: 30px; }
    .mela-chat-wrapper iframe { height: 400px; }
}
</style>
@endpush

@section('content')



<!-- Hero Section -->
<section class="mela-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-7">
                <div class="mela-hero-content">
                    <h1>Meet <span class="highlight">Mela AI</span> &mdash; Your Intelligent AI Agent</h1>
                    <p>Mela is Armely's AI-powered virtual agent, built to help you explore our services, get instant answers, and experience the power of conversational AI &mdash; available 24/7.</p>
                    <a href="#try-mela" class="btn" style="background: #fff; color: #2f5597; padding: 12px 30px; border-radius: 8px; font-weight: 600; margin-top: 20px; display: inline-block; text-decoration: none; transition: all 0.3s;">
                        <i class="fa fa-comments" style="margin-right: 8px;"></i>Chat with Mela
                    </a>
                </div>
            </div>
            <div class="col-lg-5 col-md-5">
                <div class="mela-hero-img">
                    <img src="{{ asset('images/bot-image/bot.png') }}" alt="Mela AI Agent">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- What Mela Can Do -->
<section class="mela-section">
    <div class="container">
        <div class="section-title text-center">
            <span class="mela-suite-kicker">THE MELA SUITE</span>
            <h2>What Mela AI Can Do</h2>
            <hr>
            <p>The AI suite built for how organizations actually work. Mela embeds artificial intelligence into your business workflows — from meetings and decisions to organization-wide knowledge and collaboration.</p>
        </div>

        <div class="row g-4 mela-products-row">
            <div class="col-lg-6">
                <div class="mela-product-card">
                    <div class="mela-product-head">
                        <span class="mela-product-icon"><i class="fa fa-video"></i></span>
                        <div class="mela-product-label">PRODUCT 01 · MEETING ASSISTANT</div>
                    </div>
                    <h4>Mela Meetings</h4>
                    <h5>AI-powered transcription, summaries, and action item extraction — synced to your business tools automatically.</h5>
                    <div class="mela-product-tags">
                        <span class="mela-product-tag">Transcription</span>
                        <span class="mela-product-tag">Action Items</span>
                        <span class="mela-product-tag">Meeting Intelligence</span>
                    </div>
                    <ul class="mela-mini-list">
                        <li>Real-time, speaker-attributed transcription across Teams, Zoom, and Google Meet</li>
                        <li>Structured meeting summaries with decisions, owners, and follow-ups</li>
                        <li>Automatic action item extraction pushed to Jira, Asana, or Salesforce</li>
                        <li>Searchable meeting archive indexed across your organization</li>
                        <li>Role-based access and enterprise-grade permissions</li>
                    </ul>
                    <details>
                        <summary>Who it is for + example workflow</summary>
                        <p>Mela Meetings is built for any business team that runs on recurring meetings — sales calls, sprint planning, executive briefings, board reviews, recruiting interviews, and cross-functional standups. It eliminates manual note-taking and ensures decisions and action items are captured, attributed, and routed to the right place automatically.</p>
                        <p>A sales rep finishes a customer call. Mela Meetings automatically transcribes the conversation, extracts follow-up commitments, and logs them to Salesforce — all before the rep has closed their laptop.</p>
                    </details>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="mela-product-card">
                    <div class="mela-product-head">
                        <span class="mela-product-icon"><i class="fa fa-comments"></i></span>
                        <div class="mela-product-label">PRODUCT 02 · ORGANIZATION CHATS</div>
                    </div>
                    <h4>Mela Org Chat</h4>
                    <h5>An AI layer across your organization's knowledge, documents, and communications — answering business questions instantly.</h5>
                    <div class="mela-product-tags">
                        <span class="mela-product-tag">Knowledge Chat</span>
                        <span class="mela-product-tag">Source Citations</span>
                        <span class="mela-product-tag">Governance</span>
                    </div>
                    <ul class="mela-mini-list">
                        <li>Query across internal documents, wikis, past meetings, and policy files</li>
                        <li>AI answers grounded in your organization's own data — with source citations</li>
                        <li>Connects to SharePoint, Confluence, Slack, and Google Drive</li>
                        <li>Role-based access ensures employees only see content they're permitted to view</li>
                        <li>Audit trail for all queries, supporting compliance and governance requirements</li>
                    </ul>
                    <details>
                        <summary>Who it is for + example query</summary>
                        <p>Mela Org Chat is for any employee who needs to find information quickly without digging through filing systems, Slack archives, or asking a colleague. It's especially valuable for onboarding new hires, keeping distributed teams aligned, and helping leadership surface the right context before decisions.</p>
                        <p>An employee asks Mela Org Chat: "What did leadership decide about the Q4 hiring freeze?" Mela retrieves the answer from the all-hands transcript, cross-references the HR policy update, and responds with a cited, accurate summary — in seconds.</p>
                    </details>
                </div>
            </div>
        </div>

        <div class="mela-integrations-wrap">
            <div class="mela-integrations-title">Works with your existing stack</div>
            <div class="mela-integrations-grid">
                <div class="mela-integration-chip">Microsoft Teams</div>
                <div class="mela-integration-chip">Zoom</div>
                <div class="mela-integration-chip">Google Meet</div>
                <div class="mela-integration-chip">SharePoint</div>
                <div class="mela-integration-chip">Confluence</div>
                <div class="mela-integration-chip">Slack</div>
                <div class="mela-integration-chip">Salesforce</div>
                <div class="mela-integration-chip">Jira</div>
                <div class="mela-integration-chip">Asana</div>
                <div class="mela-integration-chip">Copilot Studio</div>
                <div class="mela-integration-chip">Azure OpenAI</div>
                <div class="mela-integration-chip">Google Drive</div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="mela-section" style="background: #f8fafd;">
    <div class="container">
        <div class="section-title text-center">
            <h2>How It Works</h2>
            <hr>
            <p>Getting started with Mela is as simple as typing a question.</p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h5>Ask a Question</h5>
                    <p>Type your question in natural language &mdash; about services, data strategy, AI, or anything Armely offers.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h5>AI Processes</h5>
                    <p>Mela analyzes your query using Azure OpenAI, retrieves relevant context from our knowledge base via RAG.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h5>Get Smart Answers</h5>
                    <p>Receive accurate, contextual answers grounded in Armely's expertise &mdash; not generic AI hallucinations.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h5>Take Action</h5>
                    <p>Follow direct links to services, book consultations, or dive deeper into topics that matter to you.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Use Cases -->
<section class="mela-section">
    <div class="container">
        <div class="section-title text-center">
            <h2>Use Cases & Capabilities</h2>
            <hr>
            <p>Mela demonstrates how AI agents can transform customer engagement and business operations.</p>
        </div>
        <div class="text-center">
            <span class="use-case-badge"><i class="fa fa-comments"></i> Customer Support</span>
            <span class="use-case-badge"><i class="fa fa-graduation-cap"></i> Copilot Studio Development</span>
            <span class="use-case-badge"><i class="fa fa-database"></i> RAG-Based Knowledge Retrieval</span>
            <span class="use-case-badge"><i class="fa fa-robot"></i> Conversational AI Demos</span>
            <span class="use-case-badge"><i class="fa fa-cloud"></i> Azure OpenAI Integration</span>
            <span class="use-case-badge"><i class="fa fa-lock"></i> AI Governance</span>
            <span class="use-case-badge"><i class="fa fa-chart-line"></i> Data Strategy Guidance</span>
            <span class="use-case-badge"><i class="fa fa-handshake"></i> Partner Information</span>
            <span class="use-case-badge"><i class="fa fa-briefcase"></i> Service Discovery</span>
            <span class="use-case-badge"><i class="fa fa-globe"></i> Multi-Language Support</span>
            <span class="use-case-badge"><i class="fa fa-bolt"></i> Real-Time Responses</span>
            <span class="use-case-badge"><i class="fa fa-user-shield"></i> Responsible AI</span>
        </div>
        <div class="text-center mt-4">
            <p style="font-size: 1rem; color: #2c3e50; margin-bottom: 14px;">
                For more demos on agentic capabilities, including a Meeting Assistant, please contact us.
            </p>
            <a href="{{ route('contact') }}" class="btn" style="background: #2f5597; color: #fff; padding: 10px 26px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                <i class="fa fa-envelope" style="margin-right: 8px;"></i>Contact Us
            </a>
        </div>
    </div>
</section>

<!-- Agentic Video Showcase -->
<section class="mela-section mela-video-showcase">
    <div class="container">
        <div class="section-title text-center">
            <h2>Agentic Capabilities in Action</h2>
            <hr>
            <p>Watch short demos of practical AI agent workflows, from autonomous assistance to collaborative meeting scenarios.</p>
        </div>

        @if(!empty($demoVideos) && $demoVideos->count() > 0)
            <div class="row g-4">
                @foreach($demoVideos as $index => $video)
                    <div class="col-lg-4 col-md-6">
                        <div class="video-showcase-card">
                            <div class="video-embed">
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $video->video_id }}"
                                    title="Mela AI Demo {{ $index + 1 }}"
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="video-meta">
                                <span class="video-chip">Short Demo</span>
                                <h5>Agentic Demo {{ $index + 1 }}</h5>
                                <p>See how Mela handles contextual prompts, executes guided actions, and supports real-world business interactions.</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="video-showcase-card">
                        <div class="video-meta text-center" style="padding: 34px 24px;">
                            <span class="video-chip">Coming Soon</span>
                            <h5 style="font-size: 1.25rem;">Short Video Demos Are Being Prepared</h5>
                            <p style="margin-bottom: 18px;">We are curating demos for Meeting Assistant, autonomous workflows, and multi-agent collaboration.</p>
                            <a href="{{ route('contact') }}" class="btn" style="background: #2f5597; color: #fff; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                                <i class="fa fa-envelope" style="margin-right: 8px;"></i>Request a Live Demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Powered By -->
<section class="mela-section powered-by" style="background: #f8fafd;">
    <div class="container">
        <div class="section-title text-center">
            <h2>Powered By</h2>
            <hr>
            <p>Mela is built on Microsoft's enterprise AI stack for reliability, security, and scale.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-2 col-md-4 col-6">
                <div class="tech-item">
                    <i class="fa fa-cloud"></i>
                    <h6>Azure OpenAI</h6>
                    <p>GPT Models</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="tech-item">
                    <i class="fa fa-robot"></i>
                    <h6>Copilot Studio</h6>
                    <p>Bot Framework</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="tech-item">
                    <i class="fa fa-brain"></i>
                    <h6>AI Search</h6>
                    <p>Knowledge Index</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="tech-item">
                    <i class="fa fa-shield-alt"></i>
                    <h6>Responsible AI</h6>
                    <p>Governance</p>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="tech-item">
                    <i class="fa fa-database"></i>
                    <h6>Azure Cosmos DB</h6>
                    <p>Data Layer</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Try Mela (Live Chat Embed) -->
<section class="mela-section mela-chat-section" id="try-mela">
    <div class="container">
        <div class="section-title text-center">
            <h2>Try Mela AI Now</h2>
            <hr>
            <p>Experience the power of AI-driven conversations. Ask Mela anything about Armely's services, data solutions, or AI capabilities.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mela-chat-wrapper">
                    <iframe
                        src="https://copilotstudio.preview.microsoft.com/environments/Default-b783208a-8014-4829-9589-5324f76470c8/bots/cr44c_agent/webchat?__version__=2"
                        frameborder="0"
                        allow="microphone"
                        title="Mela AI Chat">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
