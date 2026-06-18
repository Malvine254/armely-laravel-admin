@php
    $serviceCards = [
        [
            'category' => 'Analytics',
            'name' => 'Microsoft Fabric',
            'description' => 'Unified data engineering, warehousing, and BI in one platform. Replace the disconnected stack.',
            'href' => route('services.show', ['name' => 'microsoft-fabric']),
            'logo' => asset('images/service-card-logos/Fabric.png'),
            'alt' => 'Microsoft Fabric logo',
        ],
        [
            'category' => 'Cloud Data',
            'name' => 'Snowflake',
            'description' => 'Cloud data warehousing and AI workloads without infrastructure overhead.',
            'href' => route('services.show', ['name' => 'snowflake']),
            'logo' => asset('images/service-card-logos/Snowflake.png'),
            'alt' => 'Snowflake logo',
        ],
        [
            'category' => 'AI',
            'name' => 'Generative and Agentic AI',
            'description' => 'Production-grade AI agents grounded in your business data and processes.',
            'href' => route('services.show', ['name' => 'generative-ai']),
            'logo' => asset('images/service-card-logos/azureai-color.png'),
            'alt' => 'Azure AI Foundry logo',
            'accent' => 'ai',
        ],
        [
            'category' => 'Productivity',
            'name' => 'Microsoft 365 Copilot',
            'description' => 'Structured Copilot deployment with governance, training, and adoption support.',
            'href' => route('services.show', ['name' => 'copilot']),
            'logo' => asset('images/service-card-logos/Copilot.png'),
            'alt' => 'Microsoft Copilot logo',
        ],
        [
            'category' => 'Applications',
            'name' => 'Dynamics 365',
            'description' => 'CRM and ERP connecting sales, service, finance, and operations in one platform.',
            'href' => route('services.show', ['name' => 'dynamics-365']),
            'logo' => asset('images/service-card-logos/' . rawurlencode('Dynamics 365.png')),
            'alt' => 'Dynamics 365 logo',
        ],
        [
            'category' => 'Automation',
            'name' => 'Power Platform',
            'description' => 'Low-code apps, workflow automation, and BI for every team in your organization.',
            'href' => route('services.show', ['name' => 'power-platform']),
            'logo' => asset('images/service-card-logos/PowerPlatform.png'),
            'alt' => 'Microsoft Power Platform logo',
            'accent' => 'warm',
        ],
    ];

    $clientCards = [
        [
            'name' => 'Swope Health Services',
            'outcome' => 'Power BI dashboards across clinical and admin operations',
            'logo' => asset('images/brand-partners/swope_health.png'),
            'href' => route('customer-stories.index'),
        ],
        [
            'name' => 'UNMC',
            'outcome' => 'Data platform and clinical workflow modernization on Azure',
            'logo' => asset('images/brand-partners/university_of_nebrask1.png'),
            'href' => route('customer-stories.index'),
        ],
        [
            'name' => 'Sage Butte Energy',
            'outcome' => 'Aries database modernization and OpenInvoice AP integration',
            'logo' => asset('images/brand-partners/sage_bute.webp'),
            'logo_scale' => 1.38,
            'href' => route('customer-stories.index'),
        ],
        [
            'name' => 'Plano ISD',
            'outcome' => 'Microsoft 365, SharePoint, and Power Platform district rollout',
            'logo' => asset('images/partners/Plano.png'),
            'logo_scale' => 1.28,
            'href' => route('customer-stories.index'),
        ],
        [
            'name' => 'City of Frisco',
            'outcome' => 'Microsoft 365 governance and adoption program',
            'logo' => asset('images/brand-partners/frisco.jpeg'),
            'logo_scale' => 1.4,
            'href' => route('customer-stories.index'),
        ],
        [
            'name' => 'BNSF Railway',
            'outcome' => 'Automated vehicle data management platform',
            'logo' => asset('images/brand-partners/bnsf.png'),
            'href' => route('customer-stories.index'),
        ],
        [
            'name' => 'Lambda Legal',
            'outcome' => "Legal operations modernization and Raiser's Edge integration",
            'logo' => asset('images/brand-partners/lambda.png'),
            'href' => route('customer-stories.index'),
        ],
        [
            'name' => 'Dallas County',
            'outcome' => 'Government data and compliance modernization',
            'logo' => asset('images/brand-partners/dallas_county.jpg'),
            'logo_scale' => 1.95,
            'href' => route('customer-stories.index'),
        ],
    ];

    $caseStudyCount = collect($industryListings ?? [])->count();
    $caseStudies = collect($industryListings ?? [])->take(6);
@endphp

@push('styles')
<style>
    .armely-home-hero,
    .armely-home-services,
    .armely-home-clients,
    .armely-home-cases {
        position: relative;
    }

    .armely-home-hero {
        background: #0d1a30;
        color: #fff;
        padding: 68px 48px 56px;
        display: flex;
        justify-content: center;
        overflow: hidden;
    }

    .armely-home-shell {
        width: min(1000px, calc(100% - 96px));
        margin: 0 auto;
    }

    .armely-home-hero .armely-home-shell {
        padding: 0;
        background: none;
        border-radius: 0;
        box-shadow: none;
        overflow: visible;
        position: relative;
        z-index: 1;
    }

    .armely-home-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 88% -6%, rgba(47, 85, 151, 0.14), transparent 24%),
            radial-gradient(circle at 34% 108%, rgba(47, 85, 151, 0.07), transparent 20%);
        pointer-events: none;
    }

    .armely-home-hero::after {
        content: '';
        position: absolute;
        left: 35%;
        bottom: -60px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: rgba(47, 85, 151, 0.05);
        pointer-events: none;
    }

    .hero-panel {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 52px;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .hero-copy {
        padding: 0;
    }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
        color: rgba(255,255,255,0.58);
        letter-spacing: 0.22em;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .hero-eyebrow::before,
    .hero-eyebrow::after {
        content: '';
        width: 34px;
        height: 1px;
        background: rgba(255,255,255,0.26);
    }

    .hero-copy h1 {
        margin: 0;
        width: 100%;
        max-width: 30ch;
        font-size: clamp(1.8rem, 3vw, 2.4rem);
        line-height: 1.1;
        letter-spacing: -0.03em;
        font-weight: 800;
        color: #fff;
    }

    .hero-copy h1 span {
        display: inline;
    }

    .hero-copy h1 .hero-title-light {
        display: inline;
        font-weight: 300;
        color: #fff;
        opacity: 0.82;
    }

    .hero-copy p {
        margin: 16px 0 0;
        max-width: 480px;
        font-size: 0.925rem;
        line-height: 1.78;
        color: rgba(255,255,255,0.65);
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 24px 0 36px;
    }

    .hero-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: auto;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        white-space: nowrap;
    }

    .hero-btn:hover {
        transform: translateY(-1px);
    }

    .hero-btn svg {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .hero-btn-primary {
        background: #2f5597;
        color: #fff;
        box-shadow: 0 12px 28px rgba(47, 85, 151, 0.28);
    }

    .hero-btn-secondary {
        color: #fff;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.16);
        backdrop-filter: blur(8px);
    }

    .hero-trusted {
        margin-top: 0;
        padding-top: 24px;
        border-top: 1px solid rgba(255,255,255,0.12);
        display: flex;
        align-items: center;
        gap: 0;
        flex-wrap: wrap;
    }

    .hero-trusted-label {
        color: rgba(255,255,255,0.28);
        letter-spacing: 0.14em;
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        white-space: nowrap;
        margin-right: 14px;
    }

    .hero-trusted-list {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .hero-trusted-pill {
        display: inline-flex;
        align-items: center;
        color: rgba(255,255,255,0.5);
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
    }

    .hero-record {
        background: rgba(8,18,42,0.82);
        border: 1px solid rgba(255,255,255,0.13);
        border-radius: 12px;
        width: 100%;
        max-width: 280px;
        overflow: hidden;
        box-shadow: 0 18px 38px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
    }

    .hero-record-top {
        padding: 12px 18px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
    }

    .hero-record-top .dot {
        width: 8px;
        height: 8px;
        display: inline-block;
        border-radius: 50%;
        background: rgba(255,255,255,0.35);
        margin-right: 9px;
        vertical-align: middle;
    }

    .hero-record-top span:last-child {
        color: rgba(255,255,255,0.42);
        font-size: 0.68rem;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        font-weight: 700;
    }

    .hero-stat {
        display: grid;
        grid-template-columns: 30px minmax(0, 1fr) 30px;
        gap: 10px;
        align-items: center;
        padding: 12px 18px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .hero-stat:last-of-type {
        border-bottom: 0;
    }

    .hero-stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.09);
        color: rgba(255,255,255,0.9);
        border: 1px solid rgba(255,255,255,0.09);
    }

    .hero-stat-icon svg {
        display: block;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .hero-stat-title {
        margin: 0;
        color: #fff;
        font-size: 1rem;
        line-height: 1;
        font-weight: 800;
    }

    .hero-stat-desc {
        margin: 2px 0 0;
        color: rgba(255,255,255,0.36);
        font-size: 0.62rem;
        line-height: 1.3;
    }

    .hero-stat-bars {
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
        gap: 3px;
        min-height: 18px;
    }

    .hero-stat-bars span {
        width: 4px;
        border-radius: 999px;
        background: rgba(255,255,255,0.14);
    }

    .hero-stat-bars span.active {
        background: rgba(255,255,255,0.52);
    }

    .hero-record-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 11px 18px;
        border-top: 1px solid rgba(255,255,255,0.07);
        background: rgba(255,255,255,0.04);
    }

    .hero-record-footer a {
        color: #fff;
        font-weight: 600;
        text-decoration: none;
    }

    .hero-record-footer svg {
        width: 12px;
        height: 12px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .armely-home-services {
        background: #ffffff;
        padding: 64px 0;
    }

    .armely-home-clients {
        background: #f6f8fc;
        padding: 64px 0;
    }

    .armely-home-cases {
        background: #ffffff;
        padding: 64px 0;
    }

    .armely-home-services .armely-home-shell,
    .armely-home-clients .armely-home-shell,
    .armely-home-cases .armely-home-shell {
        width: min(1000px, calc(100% - 96px));
    }

    .armely-home-section-head {
        text-align: center;
        margin-bottom: 32px;
    }

    .armely-home-eyebrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #2f5597;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .armely-home-section-head h2 {
        margin: 0;
        color: #13223f;
        font-size: clamp(1.45rem, 2vw, 2.2rem);
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .armely-home-section-head p {
        margin: 10px auto 0;
        max-width: 580px;
        color: #5b6472;
        font-size: 0.92rem;
        line-height: 1.72;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .service-card {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        min-height: 228px;
        padding: 18px 18px 20px;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        border: 1px solid rgba(47, 85, 151, 0.09);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        text-decoration: none;
        color: inherit;
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease, background-color 0.22s ease;
        overflow: hidden;
    }

    .service-card:hover {
        transform: translateY(-2px);
        border-color: rgba(47, 85, 151, 0.16);
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08);
    }

    .service-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 98px;
        padding: 16px;
        border-radius: 14px;
        background: #f5f9ff;
        border: 1px solid rgba(47, 85, 151, 0.08);
        margin-bottom: 14px;
    }

    .service-logo img {
        display: block;
        max-width: 100%;
        max-height: 58px;
        object-fit: contain;
    }

    .service-card.warm .service-logo {
        background: #fff8ef;
    }

    .service-card.ai .service-logo {
        background: #eef5ff;
    }

    .service-kicker {
        color: #2f5597;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .service-name {
        margin: 0 0 6px;
        color: #13223f;
        font-size: 0.98rem;
        line-height: 1.16;
        letter-spacing: -0.04em;
        font-weight: 500;
        min-height: 2.2em;
    }

    .service-desc {
        margin: 0;
        color: #526172;
        font-size: 0.86rem;
        line-height: 1.55;
        min-height: 3.5em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .service-link {
        margin-top: auto;
        padding-top: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #2f5597;
        font-weight: 800;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .service-link svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2.3;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .armely-home-services .section-footer,
    .armely-home-clients .section-footer,
    .armely-home-cases .section-footer {
        text-align: center;
        margin-top: 28px;
    }

    .armely-home-link {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        color: #55637c;
        font-weight: 600;
        text-decoration: none;
        font-size: 0.95rem;
        line-height: 1;
        padding: 13px 26px;
        border-radius: 999px;
        border: 1px solid #d4dff0;
        background: #ffffff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
        white-space: nowrap;
    }

    .armely-home-link svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2.4;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .armely-home-link:hover {
        transform: translateY(-1px);
        border-color: #bfd0ea;
        color: #2f5597;
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.07);
    }

    .armely-home-clients {
        background: linear-gradient(180deg, #f5f8ff 0%, #ffffff 100%);
        padding: 40px 0 16px;
    }

    .client-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .client-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 172px;
        padding: 16px 16px 14px;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid rgba(47, 85, 151, 0.09);
        box-shadow: 0 8px 18px rgba(47, 85, 151, 0.05);
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .client-card:hover {
        transform: translateY(-2px);
        border-color: rgba(47, 85, 151, 0.16);
        box-shadow: 0 12px 22px rgba(47, 85, 151, 0.08);
    }

    .client-logo-wrap {
        width: 100%;
        min-height: 92px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 14px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid rgba(47, 85, 151, 0.08);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
    }

    .client-logo-wrap.tinted {
        background: linear-gradient(180deg, #f6f9ff 0%, #eef4fb 100%);
        border-color: rgba(47, 85, 151, 0.12);
    }

    .client-logo-wrap.dark {
        background: linear-gradient(180deg, #1c2d49 0%, #0f1b2e 100%);
        border-color: rgba(15, 27, 46, 0.28);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .client-logo-wrap img {
        display: block;
        max-width: 100%;
        max-height: 84px;
        object-fit: contain;
        filter: saturate(0.98);
    }

    .client-logo-text {
        color: #1f3560;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.02em;
        text-align: center;
    }

    .client-label {
        display: block;
        color: #1f3560;
        font-size: 1rem;
        font-weight: 500;
        line-height: 1.15;
        text-align: center;
        letter-spacing: -0.02em;
    }

    .client-outcome {
        margin: 0;
        color: #5b6b88;
        font-size: 0.92rem;
        line-height: 1.45;
        text-align: center;
        max-width: 22ch;
    }

    .cases-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .cases-grid > .col-12 {
        grid-column: 1 / -1;
    }

    .case-card {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        padding: 24px;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        border: 1px solid rgba(47, 85, 151, 0.10);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .case-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding: 0;
    }

    .case-tag {
        display: inline-flex;
        width: fit-content;
        padding: 7px 11px;
        border-radius: 999px;
        background: rgba(47, 85, 151, 0.08);
        color: #2f5597;
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 12px;
    }

    .case-title {
        margin: 0;
        color: #13223f;
        font-size: 1.16rem;
        line-height: 1.25;
        font-weight: 500;
        letter-spacing: -0.03em;
        min-height: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .case-desc {
        margin: 10px 0 0;
        color: #5b6472;
        font-size: 0.95rem;
        line-height: 1.6;
        min-height: 4.8em;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .case-link {
        margin-top: auto;
        padding-top: 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #2f5597;
        font-weight: 800;
        font-size: 0.92rem;
        text-decoration: none;
    }

    .case-link svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2.3;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    @media (max-width: 1199px) {
        .hero-panel {
            grid-template-columns: 1fr;
        }

        .hero-copy h1 {
            max-width: 12ch;
        }

        .service-grid,
        .cases-grid,
        .client-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .armely-home-shell {
            width: min(100%, calc(100% - 28px));
        }

        .armely-home-services .armely-home-shell {
            width: min(100%, calc(100% - 28px));
        }

        .armely-home-clients .armely-home-shell,
        .armely-home-cases .armely-home-shell {
            width: min(100%, calc(100% - 28px));
        }

        .armely-home-hero {
            padding: 104px 16px 30px;
        }

        .hero-panel {
            gap: 22px;
            align-items: flex-start;
        }

        .hero-copy {
            padding-top: 0;
        }

        .hero-copy h1 {
            max-width: none;
            font-size: clamp(1.55rem, 7vw, 2rem);
            line-height: 1.04;
        }

        .hero-copy p {
            max-width: none;
            font-size: 0.92rem;
            line-height: 1.7;
        }

        .hero-actions {
            gap: 10px;
            margin: 18px 0 24px;
            align-items: flex-start;
        }

        .hero-btn {
            width: auto;
            min-height: 44px;
            padding: 10px 16px;
            font-size: 0.8rem;
        }

        .hero-record {
            width: 100%;
            max-width: none;
            border-radius: 18px;
        }

        .hero-trusted {
            padding-top: 18px;
        }

        .hero-trusted-list {
            gap: 12px;
        }

        .service-grid,
        .cases-grid {
            grid-template-columns: 1fr;
        }

        .client-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .service-card,
        .case-card,
        .client-card {
            min-height: auto;
        }
    }

    @media (max-width: 480px) {
        .armely-home-hero {
            padding-top: 96px;
        }

        .hero-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .hero-btn {
            width: 100%;
        }
    }
</style>
@endpush

<section class="armely-home-hero">
    <div class="armely-home-shell">
        <div class="hero-panel">
            <div class="hero-copy">
                <div class="hero-eyebrow">Beyond Imagination</div>
                <h1>Data, AI, and technology implementation <span class="hero-title-light">that delivers measurable outcomes.</span></h1>
                <p>Armely implements Microsoft, Snowflake, and custom-built solutions for healthcare, energy, government, and enterprise organizations. We build it, measure it, and stand behind it.</p>
                <div class="hero-actions">
                    <a href="#services" class="hero-btn hero-btn-primary">
                        <span>See What We Do</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('contact') }}" class="hero-btn hero-btn-secondary">Let's Talk</a>
                </div>
                <div class="hero-trusted" aria-label="Trusted by">
                    <span class="hero-trusted-label">Trusted by</span>
                    <div class="hero-trusted-list">
                        <a class="hero-trusted-pill" href="{{ route('case-studies.index', ['industry' => 'healthcare']) }}">Swope Health</a>
                        <a class="hero-trusted-pill" href="{{ route('case-studies.index', ['industry' => 'healthcare']) }}">UNMC</a>
                        <a class="hero-trusted-pill" href="{{ route('case-studies.index', ['industry' => 'energy']) }}">Sage Butte Energy</a>
                        <a class="hero-trusted-pill" href="{{ route('case-studies.index', ['industry' => 'state-local-government']) }}">City of Frisco</a>
                        <a class="hero-trusted-pill" href="{{ route('case-studies.index', ['industry' => 'transportation-logistics']) }}">BNSF Railway</a>
                    </div>
                </div>
            </div>

            <aside class="hero-record" aria-label="Delivery record">
                <div class="hero-record-top">
                    <span class="dot" aria-hidden="true"></span>
                    <span>Delivery record</span>
                </div>

                <div class="hero-stat">
                    <div class="hero-stat-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4"></path><path d="M8 2v4"></path><path d="M3 10h18"></path></svg>
                    </div>
                    <div>
                        <p class="hero-stat-title">10 yrs</p>
                        <p class="hero-stat-desc">Delivery experience</p>
                    </div>
                    <div class="hero-stat-bars" aria-hidden="true">
                        <span style="height:6px"></span>
                        <span style="height:10px"></span>
                        <span class="active" style="height:16px"></span>
                        <span class="active" style="height:20px"></span>
                        <span class="active" style="height:24px"></span>
                    </div>
                </div>

                <div class="hero-stat">
                    <div class="hero-stat-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <p class="hero-stat-title">85%+</p>
                        <p class="hero-stat-desc">Client retention</p>
                    </div>
                    <div class="hero-stat-bars" aria-hidden="true">
                        <span class="active" style="width:42px;height:5px"></span>
                    </div>
                </div>

                <div class="hero-stat">
                    <div class="hero-stat-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <div>
                        <p class="hero-stat-title">90%+</p>
                        <p class="hero-stat-desc">Client satisfaction</p>
                    </div>
                    <div class="hero-stat-bars" aria-hidden="true">
                        <span class="active" style="width:42px;height:5px"></span>
                    </div>
                </div>

                <div class="hero-stat">
                    <div class="hero-stat-icon">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <div>
                        <p class="hero-stat-title">16+</p>
                        <p class="hero-stat-desc">Case studies</p>
                    </div>
                    <div class="hero-stat-bars" aria-hidden="true">
                        <span style="height:6px"></span>
                        <span style="height:10px"></span>
                        <span class="active" style="height:16px"></span>
                        <span class="active" style="height:20px"></span>
                        <span class="active" style="height:24px"></span>
                    </div>
                </div>

                <div class="hero-record-footer">
                    <a href="{{ route('case-studies.index') }}">View case studies</a>
                    <a href="{{ route('case-studies.index') }}" aria-label="Open case studies">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<section id="services" class="armely-home-services">
    <div class="armely-home-shell">
        <div class="armely-home-section-head">
            <div class="armely-home-eyebrow">What We Help You Implement</div>
            <h2>Enterprise platforms, modern delivery, clear outcomes.</h2>
            <p>A focused set of services that help teams govern, modernize, automate, and scale the way they work.</p>
        </div>

        <div class="service-grid">
            @foreach($serviceCards as $card)
                <a href="{{ $card['href'] }}" class="service-card{{ !empty($card['accent']) ? ' ' . $card['accent'] : '' }}">
                    <div class="service-logo">
                        <img src="{{ $card['logo'] }}" alt="{{ $card['alt'] }}" loading="lazy" decoding="async">
                    </div>
                    <div class="service-kicker">{{ $card['category'] }}</div>
                    <h3 class="service-name">{{ $card['name'] }}</h3>
                    <p class="service-desc">{{ $card['description'] }}</p>
                    <span class="service-link">
                        <span>Learn more</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="section-footer">
            <a href="{{ route('services') }}" class="armely-home-link">
                <span>See all 13 services</span>
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</section>

<section id="clients" class="armely-home-clients">
    <div class="armely-home-shell">
        <div class="armely-home-section-head">
            <div class="armely-home-eyebrow">Organizations that trust Armely to deliver</div>
        </div>

        <div class="client-grid">
            @foreach($clientCards as $card)
                <a href="{{ $card['href'] }}" class="client-card">
                    <div class="client-logo-wrap{{ !empty($card['logo_tone']) ? ' ' . $card['logo_tone'] : '' }}">
                        <img
                            src="{{ $card['logo'] }}"
                            alt="{{ $card['name'] }} logo"
                            loading="lazy"
                            decoding="async"
                            data-fallback="{{ $card['name'] }}"
                            @if(!empty($card['logo_scale']))
                            style="transform: scale({{ $card['logo_scale'] }}); transform-origin: center;"
                            @endif
                            onerror="this.style.display='none';var fallback=document.createElement('span');fallback.className='client-logo-text';fallback.textContent=this.dataset.fallback;this.insertAdjacentElement('afterend',fallback);"
                        >
                    </div>
                    <span class="client-label">{{ $card['name'] }}</span>
                    <p class="client-outcome">{{ $card['outcome'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="section-footer">
            <a href="{{ route('customer-stories.index') }}" class="armely-home-link">
                <span>See all Clients</span>
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</section>

<section id="case-studies" class="armely-home-cases">
    <div class="armely-home-shell">
        <div class="armely-home-section-head">
            <div class="armely-home-eyebrow">Case Studies</div>
            <h2>{{ $caseStudyCount }} published outcomes. Real clients. Documented results.</h2>
            <p>Armely publishes full case studies for major engagements with named clients, clear problem statements, and documented results.</p>
        </div>

        <div class="cases-grid">
            @forelse($caseStudies as $listing)
                @php
                    $caseStudyTitle = trim((string) ($listing->title ?? ''));
                    $caseStudyTitle = $caseStudyTitle !== '' ? $caseStudyTitle : trim((string) ($listing->category ?? 'Case Study'));
                    $caseStudyTitle = $caseStudyTitle !== '' ? $caseStudyTitle : 'Case Study';
                    $caseStudyTitle = \Illuminate\Support\Str::limit($caseStudyTitle, 52, '...');
                    $caseStudyTag = trim((string) ($listing->category ?? 'Case Study'));
                    $caseStudyTag = $caseStudyTag !== '' ? $caseStudyTag : 'Case Study';
                    $caseStudyDesc = \Illuminate\Support\Str::limit((string) ($listing->excerpt ?? ''), 140, '...');
                @endphp
                <article class="case-card">
                    <div class="case-body">
                        <div class="case-tag">{{ $caseStudyTag }}</div>
                        <h3 class="case-title">{{ $caseStudyTitle }}</h3>
                        <p class="case-desc">{{ $caseStudyDesc }}</p>
                        <a href="{{ route('case-studies.show', $listing->slug) }}" class="case-link">
                            <span>See Case Study</span>
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted mb-0">Case studies will appear here once they are published.</p>
                </div>
            @endforelse
        </div>

        <div class="section-footer">
            <a href="{{ route('case-studies.index') }}" class="armely-home-link">
                <span>View {{ $caseStudyCount }} Case Studies</span>
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </a>
        </div>
    </div>
</section>
