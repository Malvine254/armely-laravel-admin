@extends('layouts.public')

@section('title', 'InvoiceLens | Pre-ERP Invoice Intelligence | Armely')
@section('meta_description', 'InvoiceLens gives Enverus OpenInvoice operators visibility into pending invoice spend, accrual exposure, and approval workflows before invoices reach the ERP.')
@section('meta_keywords', 'InvoiceLens, Enverus OpenInvoice, invoice analytics, accrual visibility, Microsoft Fabric, oil and gas analytics')
@section('canonical_url', url('/services/invoicelens'))

@push('styles')
<style>
    .il-hero { padding: 96px 0 82px; color: #fff; background: linear-gradient(125deg,#102952 0%,#194c79 55%,#087f8c 100%); position: relative; overflow: hidden; }
    .il-hero::after { content: ''; position: absolute; width: 460px; height: 460px; right: -120px; top: -190px; border: 70px solid rgba(255,255,255,.07); border-radius: 50%; }
    .il-hero .container { position: relative; z-index: 1; }
    .il-eyebrow { display: inline-block; padding: 7px 14px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,.3); border-radius: 999px; font-size: .78rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .il-hero h1 { color: #fff; max-width: 760px; font-size: clamp(2.3rem,5vw,4.5rem); line-height: 1.06; font-weight: 800; }
    .il-hero p { max-width: 760px; margin: 24px 0 30px; color: rgba(255,255,255,.9); font-size: 1.12rem; line-height: 1.8; }
    .il-button { display: inline-block; padding: 13px 24px; border-radius: 6px; background: #f2b134; color: #17243a; font-weight: 700; transition: .2s ease; }
    .il-button:hover { background: #fff; color: #17243a; transform: translateY(-2px); }
    .il-section { padding: 78px 0; }
    .il-section-alt { background: #f4f7fa; }
    .il-heading { max-width: 760px; margin-bottom: 36px; }
    .il-heading h2 { color: #17345d; font-size: clamp(1.9rem,3vw,2.65rem); font-weight: 800; }
    .il-heading p { color: #5d6878; line-height: 1.75; }
    .il-card { height: 100%; padding: 28px; border: 1px solid #e1e8ef; border-radius: 12px; background: #fff; box-shadow: 0 8px 24px rgba(20,47,80,.06); }
    .il-card i { width: 44px; height: 44px; display: grid; place-items: center; margin-bottom: 18px; border-radius: 10px; background: #e9f6f6; color: #087f8c; font-size: 1.15rem; }
    .il-card h3 { color: #17345d; font-size: 1.1rem; font-weight: 700; }
    .il-card p { margin: 0; color: #667284; line-height: 1.7; }
    .il-flow { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; align-items: stretch; }
    .il-step { padding: 28px; border-radius: 12px; background: #17345d; color: #fff; }
    .il-step small { color: #7ed5dc; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; }
    .il-step h3 { margin: 9px 0; color: #fff; font-size: 1.15rem; }
    .il-step p { margin: 0; color: rgba(255,255,255,.78); line-height: 1.65; }
    .il-cta { padding: 68px 0; color: #fff; text-align: center; background: #102952; }
    .il-cta h2 { color: #fff; font-weight: 800; }
    .il-cta p { max-width: 690px; margin: 14px auto 26px; color: rgba(255,255,255,.8); }
    @media (max-width: 767px) { .il-hero { padding: 70px 0 60px; } .il-section { padding: 56px 0; } .il-flow { grid-template-columns: 1fr; } .il-card { margin-bottom: 18px; } }
</style>
@endpush

@section('content')
<section class="il-hero">
    <div class="container">
        <span class="il-eyebrow">Oil &amp; Gas Invoice Intelligence</span>
        <h1>See invoice exposure before it reaches the ERP.</h1>
        <p>InvoiceLens gives Enverus OpenInvoice operators a governed, real-time view of pending invoice spend, accrual exposure, and approval workflow status—while the work is still actionable.</p>
        <a class="il-button" href="{{ route('contact') }}">Talk to an InvoiceLens specialist</a>
    </div>
</section>

<section class="il-section">
    <div class="container">
        <div class="il-heading">
            <h2>Turn pre-ERP invoice activity into operational insight</h2>
            <p>Bring invoice and workflow signals together so finance and operations teams can understand current exposure without waiting for the next ERP posting cycle.</p>
        </div>
        <div class="row">
            <div class="col-md-4"><div class="il-card"><i class="fa-solid fa-receipt"></i><h3>Pending spend visibility</h3><p>Track submitted invoices and amounts that have not yet completed the approval process.</p></div></div>
            <div class="col-md-4"><div class="il-card"><i class="fa-solid fa-chart-line"></i><h3>Stronger accrual insight</h3><p>See developing financial exposure earlier and support more confident period-end decisions.</p></div></div>
            <div class="col-md-4"><div class="il-card"><i class="fa-solid fa-route"></i><h3>Workflow transparency</h3><p>Identify where invoices are in the approval process and where attention may be needed.</p></div></div>
        </div>
    </div>
</section>

<section class="il-section il-section-alt">
    <div class="container">
        <div class="il-heading">
            <h2>Built inside your Microsoft data estate</h2>
            <p>InvoiceLens uses Microsoft Fabric within your Azure tenant, keeping governance and ownership aligned with your organization.</p>
        </div>
        <div class="il-flow">
            <div class="il-step"><small>Connect</small><h3>Enverus OpenInvoice</h3><p>Bring invoice and approval workflow data into a repeatable, managed data process.</p></div>
            <div class="il-step"><small>Govern</small><h3>Microsoft Fabric</h3><p>Transform and organize the data in your tenant with enterprise security and governance.</p></div>
            <div class="il-step"><small>Understand</small><h3>Actionable reporting</h3><p>Give finance and operations teams a consistent view of spend, exposure, and status.</p></div>
        </div>
    </div>
</section>

<section class="il-cta">
    <div class="container">
        <h2>Bring pending invoice exposure into focus.</h2>
        <p>Armely deploys and supports InvoiceLens so your teams can move from delayed reporting to timely operational visibility.</p>
        <a class="il-button" href="{{ route('contact') }}">Start a conversation</a>
    </div>
</section>
@endsection
