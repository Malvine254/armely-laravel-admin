<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
.hero { position: relative; border-radius: 0px; overflow: hidden; width: 100%; min-height: 380px; display: flex; align-items: flex-start; background: #06090f; }
.hero-inner { position: relative; z-index: 2; display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(290px, 0.78fr); gap: 28px; align-items: center; padding: 34px clamp(18px, 3vw, 40px) 40px; width: 100%; max-width: 1240px; margin: 0 auto; }
.hero-copy { flex: 1; min-width: 0; max-width: 760px; }
.tagline { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
.tl { width: 28px; height: 1px; background: rgba(255,255,255,0.28); }
.tt { font-size: 9px; font-weight: 700; letter-spacing: 0.22em; text-transform: uppercase; color: rgba(255,255,255,0.42); }
.h1 { font-size: 30px; font-weight: 800; color: #fff; line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 14px; }
.h1 em { font-style: normal; font-weight: 300; color: rgba(255,255,255,0.72); }
.sub { font-size: 13px; font-weight: 300; color: rgba(255,255,255,0.82); line-height: 1.78; margin-bottom: 26px; }
.btns { display: flex; gap: 10px; margin-bottom: 32px; }
.bp { background: #2f5597; color: #fff; border: none; border-radius: 0px; padding: 12px 24px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
.bp svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.bo { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.88); border: 1px solid rgba(255,255,255,0.22); border-radius: 7px; padding: 12px 24px; font-size: 13px; font-weight: 500; cursor: pointer; }
.tr { display: flex; align-items: center; padding-top: 22px; border-top: 1px solid rgba(255,255,255,0.15); }
.trl { font-size: 9px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.35); white-space: nowrap; margin-right: 14px; flex-shrink: 0; }
.trd { width: 1px; height: 18px; background: rgba(255,255,255,0.14); margin-right: 18px; flex-shrink: 0; }
.trls { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
.trl2 { font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.65); white-space: nowrap; }
.trl2-link {
    text-decoration: none;
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.04);
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}
.trl2-link:hover,
.trl2-link:focus {
    color: #fff;
    border-color: rgba(122, 166, 232, 0.6);
    background: rgba(47, 85, 151, 0.24);
    text-decoration: none;
}

.hero-visual {
    min-width: 0;
    justify-self: end;
    width: min(100%, 320px);
}

.hero-visual-card {
    background: rgba(9, 15, 30, 0.92);
    border: 1px solid rgba(255, 255, 255, 0.11);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 18px 44px rgba(0, 0, 0, 0.22);
}

.hero-visual-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.58);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.hero-visual-head-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #6b7280;
    flex-shrink: 0;
}

.hero-visual-row {
    display: grid;
    grid-template-columns: 38px minmax(0, 1fr) 42px;
    gap: 12px;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.hero-visual-row:last-of-type {
    border-bottom: none;
}

.hero-visual-ic {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.06);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.78);
}

.hero-visual-ic svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.hero-visual-metric {
    color: #fff;
}

.hero-visual-metric-value {
    font-size: 18px;
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -0.03em;
    margin-bottom: 2px;
}

.hero-visual-metric-label {
    font-size: 11px;
    line-height: 1.35;
    color: rgba(255, 255, 255, 0.64);
}

.hero-visual-gauge {
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
    gap: 2px;
    height: 28px;
}

.hero-visual-gauge span {
    width: 4px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.24);
}

.hero-visual-gauge span:nth-child(1) { height: 8px; }
.hero-visual-gauge span:nth-child(2) { height: 14px; }
.hero-visual-gauge span:nth-child(3) { height: 20px; }
.hero-visual-gauge span:nth-child(4) { height: 26px; background: rgba(255, 255, 255, 0.40); }

.hero-visual-gauge-bars {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
    height: 28px;
}

.hero-visual-gauge-bars span {
    width: 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.22);
}

.hero-visual-gauge-bars span:nth-child(1) { height: 5px; }
.hero-visual-gauge-bars span:nth-child(2) { height: 10px; }
.hero-visual-gauge-bars span:nth-child(3) { height: 16px; }
.hero-visual-gauge-bars span:nth-child(4) { height: 22px; background: rgba(255, 255, 255, 0.38); }

.hero-visual-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.72);
    font-size: 12px;
    font-weight: 600;
}

.hero-visual-footer-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    color: inherit;
    text-decoration: none;
}

.hero-visual-footer-link:hover,
.hero-visual-footer-link:focus {
    color: #fff;
    text-decoration: none;
}

.hero-visual-footer svg {
    width: 14px;
    height: 14px;
    stroke: rgba(255, 255, 255, 0.38);
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.hero-services-wrap { position: relative; z-index: 3; width: min(100%, 1700px); margin: clamp(-66px, -4.2vw, -40px) auto 0; padding: 0 clamp(12px, 1.6vw, 26px) clamp(12px, 1.4vw, 18px); }
.hero-services { width: 100%; margin-top: 0; }
.hero-service-grid { width: 100%; display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: clamp(10px, 0.9vw, 14px); align-items: stretch; }
.hero-service-card { min-width: 0; display: flex; flex-direction: column; min-height: clamp(286px, 24vw, 330px); border-radius: 10px; text-decoration: none; color: #fff; background: #345a9b; border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 14px 28px rgba(8, 16, 35, 0.18); overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.hero-service-card:hover { transform: translateY(-3px); box-shadow: 0 22px 44px rgba(8, 16, 35, 0.28); color: #fff; }
.hero-service-media { height: clamp(96px, 8.8vw, 126px); margin: 7px 7px 0; border-radius: 8px; overflow: hidden; background: #eef2f8; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08); }
.hero-service-media img { display: block; width: 100%; height: 100%; object-fit: contain; object-position: center; padding: 4px; }
.hero-service-body { display: flex; flex-direction: column; flex: 1; padding: 7px 8px 9px; }
.hero-service-kicker { font-size: clamp(0.72rem, 0.5vw, 0.84rem); font-weight: 800; line-height: 1.2; letter-spacing: 0.04em; text-transform: none; color: rgba(255,255,255,0.92); opacity: 0.95; margin-bottom: 4px; min-height: 1.2em; }
.hero-service-title { font-size: clamp(1rem, 0.72vw, 1.14rem); line-height: 1.08; font-weight: 800; color: #fff; margin: 0 0 5px; letter-spacing: -0.01em; min-height: calc(1.08em * 2.15); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.hero-service-desc { font-size: clamp(0.84rem, 0.58vw, 0.96rem); line-height: 1.34; color: rgba(255,255,255,0.92); margin: 0; min-height: calc(1.34em * 3); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.hero-service-link { margin-top: 0; display: inline-flex; align-items: center; gap: 6px; font-size: clamp(0.68rem, 0.48vw, 0.78rem); font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: rgba(255,255,255,0.98); padding-top: 4px; }
.hero-service-link svg { width: 10px; height: 10px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

@media (max-width: 1100px) {
    .hero { min-height: 0; }
    .hero-inner { grid-template-columns: 1fr; gap: 18px; padding: 36px 24px 34px; }
    .hero-visual { justify-self: start; width: min(100%, 360px); }
    .hero-service-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .hero-services-wrap { width: min(100%, 1540px); margin: clamp(-52px, -3.8vw, -32px) auto 0; padding: 0 clamp(8px, 1.6vw, 20px) 12px; }
}

@media (max-width: 560px) {
    .hero-inner { padding: 16px 14px 18px; }
    .btns { flex-direction: column; }
    .btns .bp, .btns .bo { width: 100%; justify-content: center; }
    .hero-visual { width: 100%; }
    .hero-visual-row { grid-template-columns: 34px minmax(0, 1fr) 34px; padding: 12px 12px; gap: 10px; }
    .hero-visual-ic { width: 34px; height: 34px; border-radius: 9px; }
    .hero-visual-metric-value { font-size: 17px; }
    .hero-visual-footer { padding: 10px 12px; }
    .hero-service-media {
        margin: 7px 7px 0;
        height: auto;
        aspect-ratio: 16 / 9;
    }
    .hero-service-media img {
        padding: 6px;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .hero-service-body { padding: 7px 7px 9px; }
    .hero-service-grid { grid-template-columns: 1fr; }
    .hero-services-wrap { width: min(100%, 100%); margin: clamp(-36px, -6vw, -24px) auto 0; padding: 0 10px 10px; }
}
</style>

@php
    $heroServiceCards = [
        [
            'label' => 'Governance',
            'title' => 'M365 / Copilot Governance',
            'desc' => 'Govern Microsoft 365, Copilot, and adoption with clear controls, usage policies, data boundaries, and rollout guidance so the platform stays secure, consistent, and easy to manage.',
            'href' => route('service-details', ['name' => 'm365-governance']),
            'image' => 'images/blog/copilot.jpg',
        ],
        [
            'label' => 'Applications',
            'title' => 'Dynamics 365',
            'desc' => 'Unify sales, service, finance, and operations around one platform, then connect the workflows, reporting, and automation your teams need to work faster with less duplication.',
            'href' => url('/dynamics-365'),
            'image' => 'images/blog/design.png',
        ],
        [
            'label' => 'Data',
            'title' => 'SQL Server',
            'desc' => 'Modernize core data platforms and reporting foundations with stronger performance, better availability, safer backups, and a database layer that can support new workloads with confidence.',
            'href' => url('/sql-server'),
            'image' => 'images/blog/sqlserver2024.png',
        ],
        [
            'label' => 'Analytics',
            'title' => 'Microsoft Fabric',
            'desc' => 'Bring analytics, BI, and lakehouse experiences together so reporting, preparation, and governed access happen in one modern environment instead of scattered tools.',
            'href' => route('service-details', ['name' => 'microsoft-fabric']),
            'image' => 'images/blog/fabric.png',
        ],
        [
            'label' => 'Cloud Data',
            'title' => 'Snowflake',
            'desc' => 'Scale cloud data sharing, pipelines, and governed access with an architecture that supports analytics teams, secure collaboration, and faster delivery across business units.',
            'href' => route('service-details', ['name' => 'snowflake']),
            'image' => 'images/blog/sf.png',
        ],
        [
            'label' => 'Build',
            'title' => 'Custom / API Development',
            'desc' => 'Deliver custom apps and API integrations for real workflows, connecting your systems cleanly so manual steps shrink and the business can automate the parts that matter most.',
            'href' => url('/api-dev'),
            'image' => 'images/blog/api.png',
        ],
    ];
@endphp

<div style="padding:1rem 0;">
<div class="hero">

<svg style="position:absolute;inset:0;width:100%;height:100%;z-index:1;" viewBox="0 0 1000 380" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="bg2" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#03060e"/>
      <stop offset="100%" stop-color="#06090f"/>
    </linearGradient>
    <linearGradient id="guard2" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%"   stop-color="#04080f" stop-opacity="1.0"/>
      <stop offset="50%"  stop-color="#04080f" stop-opacity="1.0"/>
      <stop offset="65%"  stop-color="#04080f" stop-opacity="0.82"/>
      <stop offset="78%"  stop-color="#04080f" stop-opacity="0.35"/>
      <stop offset="100%" stop-color="#04080f" stop-opacity="0.05"/>
    </linearGradient>
  </defs>

  <rect width="1000" height="380" fill="url(#bg2)"/>
  <rect x="0" y="210" width="1000" height="170" fill="#03060c"/>

  <rect x="640" y="130" width="32" height="104" fill="#070d1a" rx="1"/>
  <rect x="676" y="148" width="26" height="86" fill="#060c18" rx="1"/>
  <rect x="706" y="116" width="42" height="118" fill="#060b16" rx="1"/>
  <rect x="752" y="140" width="28" height="94" fill="#070d1a" rx="1"/>
  <rect x="784" y="152" width="24" height="82" fill="#060b16" rx="1"/>
  <rect x="812" y="124" width="36" height="110" fill="#070d1a" rx="1"/>
  <rect x="852" y="158" width="30" height="76" fill="#060c18" rx="1"/>
  <rect x="886" y="136" width="38" height="98" fill="#060b16" rx="1"/>
  <rect x="928" y="148" width="28" height="86" fill="#070d1a" rx="1"/>
  <rect x="960" y="160" width="40" height="74" fill="#060c18" rx="1"/>
  <rect x="610" y="166" width="26" height="68" fill="#070d1a" rx="1"/>

  <rect x="648" y="138" width="4" height="3" fill="#f5c842" opacity="0.55"/>
  <rect x="655" y="146" width="4" height="3" fill="#f5c842" opacity="0.40"/>
  <rect x="648" y="154" width="4" height="3" fill="#f5c842" opacity="0.60"/>
  <rect x="714" y="124" width="5" height="3" fill="#f5c842" opacity="0.48"/>
  <rect x="722" y="132" width="5" height="3" fill="#f5c842" opacity="0.55"/>
  <rect x="714" y="140" width="5" height="3" fill="#f5c842" opacity="0.36"/>
  <rect x="722" y="148" width="5" height="3" fill="#f5c842" opacity="0.50"/>
  <rect x="820" y="132" width="4" height="3" fill="#f5c842" opacity="0.52"/>
  <rect x="827" y="142" width="4" height="3" fill="#f5c842" opacity="0.62"/>
  <rect x="820" y="152" width="4" height="3" fill="#f5c842" opacity="0.38"/>
  <rect x="894" y="143" width="4" height="3" fill="#f5c842" opacity="0.50"/>
  <rect x="901" y="151" width="4" height="3" fill="#f5c842" opacity="0.42"/>
  <rect x="732" y="124" width="5" height="3" fill="#6aa8f5" opacity="0.48"/>
  <rect x="732" y="136" width="5" height="3" fill="#6aa8f5" opacity="0.36"/>
  <rect x="760" y="147" width="4" height="3" fill="#6aa8f5" opacity="0.42"/>
  <rect x="616" y="172" width="4" height="3" fill="#6aa8f5" opacity="0.38"/>
  <rect x="936" y="155" width="4" height="3" fill="#6aa8f5" opacity="0.40"/>

  <rect x="620" y="44" width="280" height="240" fill="#050a14" rx="2"/>
  <rect x="622" y="46" width="276" height="236" fill="#03070e" rx="1"/>
  <line x1="760" y1="46" x2="760" y2="282" stroke="#080d1a" stroke-width="4"/>
  <line x1="622" y1="163" x2="898" y2="163" stroke="#080d1a" stroke-width="4"/>
  <rect x="622" y="163" width="276" height="119" fill="#060e1e" opacity="0.5"/>

  <rect x="0" y="280" width="1000" height="10" fill="#07101e"/>

  <rect x="-18" y="180" width="148" height="96" fill="#050a18" rx="3"/>
  <rect x="-16" y="182" width="144" height="92" fill="#030710" rx="2"/>
  <rect x="-10" y="186" width="136" height="5" fill="#0c1a30" opacity="0.7"/>
  <rect x="-10" y="194" width="66" height="24" fill="#091830" rx="2"/>
  <rect x="60" y="194" width="66" height="24" fill="#091830" rx="2"/>
  <rect x="-6" y="228" width="11" height="22" fill="#2f5597" opacity="0.75"/>
  <rect x="9"  y="220" width="11" height="30" fill="#2f5597" opacity="0.88"/>
  <rect x="24" y="212" width="11" height="38" fill="#4477bd" opacity="0.78"/>
  <rect x="39" y="222" width="11" height="28" fill="#2f5597" opacity="0.68"/>
  <rect x="54" y="216" width="11" height="34" fill="#4477bd" opacity="0.82"/>
  <rect x="69" y="225" width="11" height="25" fill="#2f5597" opacity="0.72"/>
  <rect x="84" y="219" width="11" height="31" fill="#4477bd" opacity="0.76"/>
  <rect x="99" y="227" width="11" height="23" fill="#2f5597" opacity="0.65"/>
  <polyline points="-6,252 12,242 30,246 48,234 66,239 84,230 102,235" stroke="#5dcaa5" stroke-width="1.5" fill="none" opacity="0.78"/>
  <polygon points="-6,252 12,242 30,246 48,234 66,239 84,230 102,235 102,272 -6,272" fill="#5dcaa5" opacity="0.06"/>
  <rect x="52" y="276" width="18" height="8" fill="#060c1a"/>
  <rect x="41" y="283" width="40" height="3" fill="#060c1a" rx="1"/>

  <rect x="112" y="170" width="162" height="106" fill="#050a18" rx="3"/>
  <rect x="114" y="172" width="158" height="102" fill="#030710" rx="2"/>
  <rect x="188" y="276" width="16" height="9" fill="#060c1a"/>
  <rect x="177" y="284" width="38" height="3" fill="#060c1a" rx="1"/>
  <rect x="118" y="176" width="150" height="6" fill="#0a1526" opacity="0.8"/>
  <rect x="119" y="176" width="46" height="6" fill="#18305e" opacity="0.6"/>
  <rect x="124" y="188" width="75" height="2" fill="#4477bd" opacity="0.52"/>
  <rect x="124" y="194" width="108" height="2" fill="#5dcaa5" opacity="0.46"/>
  <rect x="132" y="200" width="82" height="2" fill="#f5c842" opacity="0.40"/>
  <rect x="124" y="206" width="92" height="2" fill="#4477bd" opacity="0.48"/>
  <rect x="132" y="212" width="66" height="2" fill="#5dcaa5" opacity="0.40"/>
  <rect x="124" y="218" width="112" height="2" fill="#f5c842" opacity="0.34"/>
  <rect x="132" y="224" width="78" height="2" fill="#4477bd" opacity="0.45"/>
  <rect x="124" y="230" width="100" height="2" fill="#fff" opacity="0.13"/>
  <rect x="132" y="236" width="84" height="2" fill="#5dcaa5" opacity="0.38"/>
  <rect x="124" y="242" width="60" height="2" fill="#4477bd" opacity="0.32"/>
  <rect x="124" y="248" width="102" height="2" fill="#f5c842" opacity="0.30"/>
  <rect x="132" y="254" width="72" height="2" fill="#5dcaa5" opacity="0.36"/>
  <rect x="124" y="260" width="8" height="9" fill="#4477bd" opacity="0.58" rx="1"/>

  <ellipse cx="56" cy="284" rx="80" ry="14" fill="#1a3a6a" opacity="0.08"/>
  <ellipse cx="192" cy="282" rx="95" ry="16" fill="#1a3a6a" opacity="0.10"/>

  <rect width="1000" height="380" fill="url(#guard2)"/>
  <rect x="0" y="200" width="560" height="180" fill="#04080f" opacity="0.70"/>
</svg>

<div class="hero-inner">
  <div class="hero-copy">
    <div class="tagline"><span class="tl"></span><span class="tt">Beyond Imagination</span><span class="tl"></span></div>
    <div class="h1">Data, AI, and technology implementation <em>that delivers measurable outcomes.</em></div>
    <p class="sub">Armely implements Microsoft, Snowflake, and custom-built solutions for healthcare, energy, government, and enterprise organizations. We build it, measure it, and stand behind it.</p>
    <div class="btns">
      <button class="bp">See What We Do <svg viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></button>
      <button class="bo">Let's Talk</button>
    </div>
    <div class="tr">
      <span class="trl">Trusted by</span><span class="trd"></span>
      <div class="trls">
        <a class="trl2 trl2-link" href="{{ url('/industries#healthcare') }}">Swope Health</a>
        <a class="trl2 trl2-link" href="{{ url('/industries#healthcare') }}">UNMC</a>
        <a class="trl2 trl2-link" href="{{ url('/industries#energy') }}">Sage Butte Energy</a>
        <a class="trl2 trl2-link" href="{{ url('/industries#government') }}">City of Frisco</a>
        <a class="trl2 trl2-link" href="{{ url('/industries#transportation-logistics') }}">BNSF Railway</a>
      </div>
    </div>
  </div>
  <div class="hero-visual" aria-label="Delivery record">
    <div class="hero-visual-card">
      <div class="hero-visual-head">
        <span class="hero-visual-head-dot"></span>
        <span>Delivery record</span>
      </div>
      <div class="hero-visual-row">
        <div class="hero-visual-ic" aria-hidden="true">
          <svg viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4"/><path d="M16 3v4"/><path d="M4 10h16"/></svg>
        </div>
        <div class="hero-visual-metric">
          <div class="hero-visual-metric-value">10 yrs</div>
          <div class="hero-visual-metric-label">Delivery experience</div>
        </div>
        <div class="hero-visual-gauge-bars" aria-hidden="true">
          <span></span><span></span><span></span><span></span>
        </div>
      </div>
      <div class="hero-visual-row">
        <div class="hero-visual-ic" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
        </div>
        <div class="hero-visual-metric">
          <div class="hero-visual-metric-value">85%+</div>
          <div class="hero-visual-metric-label">Client retention</div>
        </div>
        <div class="hero-visual-gauge" aria-hidden="true">
          <span></span><span></span><span></span><span></span>
        </div>
      </div>
      <div class="hero-visual-row">
        <div class="hero-visual-ic" aria-hidden="true">
          <svg viewBox="0 0 24 24"><polygon points="12 2 15.3 8.8 23 9.7 17.4 15 18.8 22.8 12 19 5.2 22.8 6.6 15 1 9.7 8.7 8.8"/></svg>
        </div>
        <div class="hero-visual-metric">
          <div class="hero-visual-metric-value">90%+</div>
          <div class="hero-visual-metric-label">Client satisfaction</div>
        </div>
        <div class="hero-visual-gauge" aria-hidden="true">
          <span></span><span></span><span></span><span></span>
        </div>
      </div>
      <div class="hero-visual-row">
        <div class="hero-visual-ic" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="hero-visual-metric">
          <div class="hero-visual-metric-value">9 Years</div>
          <div class="hero-visual-metric-label">Years of delivery</div>
        </div>
        <div class="hero-visual-gauge-bars" aria-hidden="true">
          <span></span><span></span><span></span><span></span>
        </div>
      </div>
      <a class="hero-visual-footer hero-visual-footer-link" href="{{ route('case-studies.index') }}">
        <span>View case studies</span>
        <svg viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</div>

</div>
</div>

<div class="hero-services-wrap">
  <div class="hero-services">
    <div class="hero-service-grid">
      @foreach($heroServiceCards as $card)
        <a class="hero-service-card" href="{{ $card['href'] }}">
          <div class="hero-service-media">
            <img src="{{ asset($card['image']) }}" alt="{{ $card['title'] }}">
          </div>
          <div class="hero-service-body">
            <div class="hero-service-kicker">{{ $card['label'] }}</div>
            <h3 class="hero-service-title">{{ $card['title'] }}</h3>
            <p class="hero-service-desc">{{ $card['desc'] }}</p>
            <span class="hero-service-link">Read More <svg viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</div>
