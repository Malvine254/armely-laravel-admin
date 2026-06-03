@extends('layouts.public')

@section('title', 'Industries | Microsoft Data, AI, and Cloud Solutions | Armely')
@section('meta_description', 'Explore Armely industry solutions for healthcare, energy, state and local government, transportation and logistics, and legal organizations using Microsoft Fabric, Power Platform, Azure AI, and data strategy.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/industries-modern.css') }}">
<style>
.industries-page-refresh {
	background: #f7f9fc;
	color: #172033;
}
.industries-hero {
	background: #f7f9fc;
	border-bottom: 1px solid #dbe6f3;
	padding: 54px 0 42px;
}
.industries-hero-grid {
	display: grid;
	grid-template-columns: minmax(0, 1.25fr) minmax(280px, .75fr);
	gap: 30px;
	align-items: center;
}
.industries-kicker {
	color: #2f5597;
	font-size: .78rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
	margin-bottom: 12px;
}
.industries-hero h1 {
	color: #172033;
	font-size: 2.45rem;
	font-weight: 900;
	line-height: 1.16;
	margin: 0 0 14px;
	max-width: 760px;
}
.industries-hero-copy {
	color: #5f6f86;
	font-size: 1.02rem;
	line-height: 1.75;
	margin: 0 0 22px;
	max-width: 700px;
}
.industries-hero-actions {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
}
.industries-primary-btn,
.industries-secondary-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
	min-height: 44px;
	padding: 11px 16px;
	font-weight: 800;
	border: 1px solid transparent;
	text-decoration: none;
}
.industries-primary-btn {
	background: #2f5597;
	color: #fff;
}
.industries-primary-btn:hover {
	background: #1e3a6d;
	color: #fff;
	text-decoration: none;
}
.industries-secondary-btn {
	background: #fff;
	color: #1e3a6d;
	border-color: #cbd9ea;
}
.industries-secondary-btn:hover {
	color: #2f5597;
	border-color: #aebfda;
	text-decoration: none;
}
.industries-hero-panel {
	background: #fff;
	border: 1px solid #dbe6f3;
	box-shadow: 0 14px 34px rgba(28, 54, 93, .07);
	padding: 22px;
}
.industries-panel-top {
	border-bottom: 1px solid #dbe6f3;
	padding-bottom: 14px;
	margin-bottom: 14px;
}
.industries-panel-top h2 {
	color: #172033;
	font-size: 1.08rem;
	font-weight: 900;
	margin: 0 0 6px;
}
.industries-panel-top p {
	color: #5f6f86;
	font-size: .94rem;
	line-height: 1.65;
	margin: 0;
}
.industries-panel-body h3 {
	color: #2f5597;
	font-size: .78rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
	margin: 0 0 12px;
}
.industries-focus-list {
	display: grid;
	gap: 10px;
	list-style: none;
	margin: 0;
	padding: 0;
}
.industries-focus-list li {
	display: grid;
	grid-template-columns: 34px minmax(0, 1fr);
	gap: 10px;
	align-items: start;
}
.industries-focus-icon {
	width: 34px;
	height: 34px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	background: #f2f6fc;
	border: 1px solid #dbe6f3;
	color: #2f5597;
	font-size: .9rem;
	font-weight: 900;
}
.industries-focus-list strong {
	display: block;
	color: #172033;
	font-size: .95rem;
	font-weight: 900;
	margin-bottom: 2px;
}
.industries-focus-list span {
	color: #5f6f86;
	font-size: .88rem;
	line-height: 1.5;
}
.industries-section-modern {
	background: #f7f9fc;
	padding: 46px 0 66px;
}
.industries-section-modern > .mt-5 {
	max-width: 1140px;
	margin: 0 auto !important;
	padding: 0 15px;
}
.modern-tabs-industries,
.modern-tab-content,
.modern-solution-card,
.benefit-card,
.feature-item {
	border-radius: 0 !important;
	border: 1px solid #dbe6f3 !important;
	box-shadow: 0 12px 30px rgba(28, 54, 93, .06) !important;
}
.modern-tabs-industries {
	margin-bottom: 22px !important;
}
.modern-tabs-industries .nav-link {
	border-radius: 0 !important;
	color: #1e3357 !important;
	font-weight: 800 !important;
}
.modern-tabs-industries .nav-link.active {
	background: #2f5597 !important;
	box-shadow: none !important;
	color: #fff !important;
}
.tab-content-wrapper {
	padding: 32px !important;
}
.industry-tag {
	border-radius: 0 !important;
	background: #f2f6fc !important;
	border: 1px solid #dbe6f3 !important;
	color: #2f5597 !important;
}
.industry-title {
	color: #172033 !important;
	font-size: 2.25rem !important;
}
.industry-description,
.industry-intro-content p,
.solution-description,
.benefit-description,
.feature-description {
	color: #5f6f86 !important;
}
.industry-img,
.modern-solution-card,
.benefit-card,
.feature-item {
	border-radius: 0 !important;
}
.modern-solution-card {
	background: #fff !important;
	min-height: 270px !important;
	height: 100% !important;
	padding: 24px !important;
	display: flex !important;
	flex-direction: column;
	gap: 12px;
}
.modern-solution-card.mt-4 {
	margin-top: 0 !important;
}
.modern-tab-content .row.g-4 > [class*="col-"] {
	margin-bottom: 24px;
}
.modern-solution-card::before {
	transform: none !important;
	height: 3px !important;
	background: #2f5597 !important;
}
.modern-solution-card:hover {
	transform: translateY(-6px) !important;
	box-shadow: 0 18px 42px rgba(28, 54, 93, .11) !important;
}
.modern-solution-card .solution-icon-wrapper {
	width: 52px !important;
	height: 52px !important;
	border-radius: 0 !important;
	background: #eef4fb !important;
	border: 1px solid #dbe6f3;
	box-shadow: none !important;
	margin: 0 0 4px !important;
	display: inline-flex !important;
	align-items: center;
	justify-content: center;
}
.modern-solution-card:hover .solution-icon-wrapper {
	transform: none !important;
	box-shadow: none !important;
}
.modern-solution-card .solution-icon-wrapper i {
	color: #2f5597;
	font-size: 1.35rem;
	line-height: 1;
}
.modern-solution-card .solution-title {
	color: #172033 !important;
	font-size: 1.05rem !important;
	font-weight: 900 !important;
	line-height: 1.35 !important;
	margin: 0 !important;
}
.modern-solution-card .solution-description {
	color: #5f6f86 !important;
	font-size: .94rem !important;
	line-height: 1.65 !important;
	margin: 0 !important;
	flex: 1;
}
.modern-solution-card .btn-modern-cta {
	align-self: flex-start;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: auto;
	min-width: 0;
	padding-left: 22px;
	padding-right: 22px;
}
.government-credentials-panel {
	background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
	border: 1px solid #dbe6f3;
	box-shadow: 0 18px 42px rgba(28, 54, 93, .08);
	padding: 26px;
	margin-top: 18px;
}
.government-credentials-copy {
	color: #40516c !important;
	font-size: 1rem;
	line-height: 1.7;
	margin: 0 0 22px;
	max-width: 820px;
}
.government-credentials-grid {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 14px;
	margin: 0;
	padding: 0;
	list-style: none;
}
.government-credential-item {
	display: flex;
	align-items: flex-start;
	gap: 12px;
	background: #ffffff;
	border: 1px solid #e0e9f6;
	padding: 14px 15px;
	min-height: 76px;
}
.government-credential-icon {
	width: 34px;
	height: 34px;
	flex: 0 0 34px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	background: #eef4fb;
	border: 1px solid #d3e2f4;
	color: #2f5597;
	font-size: .95rem;
}
.government-credential-text {
	display: block;
}
.government-credential-text strong {
	display: block;
	color: #172033;
	font-size: .96rem;
	line-height: 1.35;
	margin-bottom: 3px;
}
.government-credential-text span {
	display: block;
	color: #66758c;
	font-size: .84rem;
	line-height: 1.45;
}
.benefit-card {
	background: #fff !important;
	min-height: 220px !important;
	height: 100% !important;
	padding: 24px !important;
	display: flex !important;
	flex-direction: column;
	gap: 12px;
	position: relative;
}
.benefit-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 3px;
	background: #2f5597;
}
.benefit-card:hover {
	transform: translateY(-6px) !important;
	box-shadow: 0 18px 42px rgba(28, 54, 93, .11) !important;
}
.benefit-card .benefit-icon {
	width: 52px !important;
	height: 52px !important;
	border-radius: 0 !important;
	background: #eef4fb !important;
	border: 1px solid #dbe6f3;
	box-shadow: none !important;
	margin: 0 0 4px !important;
	display: inline-flex !important;
	align-items: center;
	justify-content: center;
}
.benefit-card .benefit-icon i {
	color: #2f5597 !important;
	font-size: 1.35rem !important;
	line-height: 1;
}
.benefit-card .benefit-title {
	color: #172033 !important;
	font-size: 1.05rem !important;
	font-weight: 900 !important;
	line-height: 1.35 !important;
	margin: 0 !important;
}
.benefit-card .benefit-description {
	color: #5f6f86 !important;
	font-size: .94rem !important;
	line-height: 1.65 !important;
	margin: 0 !important;
	flex: 1;
}
.industry-img {
	border: 1px solid #dbe6f3;
	box-shadow: 0 14px 34px rgba(28, 54, 93, .09) !important;
}
.features-section-industries {
	background: #fff !important;
	padding: 68px 0 !important;
	border-top: 1px solid #dbe6f3;
	border-bottom: 1px solid #dbe6f3;
}
.features-section-industries .features-section-title {
	color: #172033 !important;
	font-size: 2rem !important;
	font-weight: 900 !important;
}
.features-section-industries .features-section-subtitle {
	color: #5f6f86 !important;
	max-width: 620px;
	margin: 0 auto !important;
	line-height: 1.7;
}
.features-section-industries .feature-item {
	padding: 24px !important;
	height: 100%;
	text-align: left !important;
}
.features-section-industries .feature-gradient-icon {
	border-radius: 0 !important;
	background: #eef4fb !important;
	border: 1px solid #dbe6f3;
	box-shadow: none !important;
	width: 54px !important;
	height: 54px !important;
	display: inline-flex !important;
	align-items: center;
	justify-content: center;
	color: #2f5597;
	font-size: 1.35rem;
}
.features-section-industries .feature-gradient-icon i {
	color: #2f5597;
	line-height: 1;
}
.features-section-industries .feature-title {
	color: #172033 !important;
	font-weight: 900 !important;
	margin-bottom: 8px !important;
}
.cta-section-industries {
	background: #f5f8fc !important;
}

/* Quiet professional pass */
.industries-hero {
	background: #fff;
	padding: 48px 0 34px;
}
.industries-hero-grid {
	display: block;
	max-width: 900px;
}
.industries-kicker {
	color: #2f5597;
	font-size: .76rem;
	font-weight: 800;
	margin-bottom: 10px;
}
.industries-hero h1 {
	font-size: 2.15rem;
	font-weight: 800;
	line-height: 1.22;
	max-width: 820px;
	margin-bottom: 12px;
}
.industries-hero-copy {
	font-size: 1rem;
	line-height: 1.72;
	max-width: 760px;
	margin-bottom: 20px;
}
.industries-hero-panel {
	display: none;
}
.industries-primary-btn,
.industries-secondary-btn {
	min-height: 42px;
	padding: 10px 14px;
	font-weight: 700;
}
.industries-primary-btn {
	background: #2f5597;
}
.industries-secondary-btn {
	background: transparent;
}
.industries-section-modern {
	background: #f8fafc;
	padding: 42px 0 64px;
}
.industries-section-modern::after {
	content: "";
	display: block;
	clear: both;
}
.industries-section-modern > .container {
	max-width: 1180px;
}
.industries-layout {
	align-items: flex-start;
}
.industries-sidebar-col {
	align-self: flex-start;
	position: relative;
	z-index: 20;
}
.industry-nav-panel {
	background: #fff;
	border: 1px solid #dbe6f3;
	box-shadow: 0 10px 22px rgba(24, 54, 107, .08);
	padding: 16px;
	position: sticky;
	top: 90px;
	align-self: flex-start;
	height: auto !important;
	min-height: 0 !important;
	z-index: 21;
}
.industry-nav-kicker {
	color: #2f5597;
	font-size: .76rem;
	font-weight: 800;
	letter-spacing: .08em;
	text-transform: uppercase;
	margin-bottom: 8px;
}
.industry-nav-title {
	color: #172033;
	font-size: 1.12rem;
	font-weight: 800;
	line-height: 1.35;
	margin-bottom: 8px;
}
.industry-nav-copy {
	color: #5f6f86;
	font-size: .92rem;
	line-height: 1.6;
	margin-bottom: 16px;
}
.modern-tabs-industries {
	background: transparent !important;
	border: 0 !important;
	box-shadow: none !important;
	display: grid;
	gap: 10px;
	padding: 0 !important;
	margin: 0 !important;
	height: auto !important;
	min-height: 0 !important;
}
.modern-tabs-industries .nav-item {
	margin: 0 !important;
}
.modern-tabs-industries .nav-link {
	background: #fff !important;
	border: 1px solid #dbe6f3 !important;
	color: #1e3357 !important;
	display: flex;
	align-items: flex-start;
	gap: 10px;
	justify-content: flex-start;
	padding: 11px 12px !important;
	font-weight: 700 !important;
	font-size: .92rem;
	line-height: 1.28;
	width: 100%;
	white-space: normal;
	overflow-wrap: anywhere;
}
.modern-tabs-industries .nav-link i {
	color: #2f5597;
	font-size: 1rem !important;
	flex: 0 0 18px;
	width: 18px;
	margin-top: 1px;
	text-align: center;
}
.modern-tabs-industries .nav-link strong {
	display: block;
	min-width: 0;
	white-space: normal;
	overflow-wrap: anywhere;
}
.modern-tabs-industries .nav-link.active {
	background: #2f5597 !important;
	border-color: #2f5597 !important;
	color: #fff !important;
}
.modern-tabs-industries .nav-link.active i {
	color: #fff;
}
.modern-tab-content {
	box-shadow: none !important;
	min-height: 0 !important;
	background: transparent !important;
	border: 0 !important;
}
.tab-content-wrapper {
	padding: 28px !important;
	min-height: 0 !important;
}
.tab-content-wrapper > .mt-5:first-child {
	margin-top: 0 !important;
}
.industries-section-modern .tab-pane {
	background: transparent !important;
	min-height: 0 !important;
}
.industries-tab-inner {
	height: auto !important;
	min-height: 0 !important;
}
.industry-tag {
	font-size: .72rem !important;
	font-weight: 800 !important;
	letter-spacing: .08em !important;
	padding: 6px 10px !important;
}
.industry-title {
	font-size: 2rem !important;
	font-weight: 800 !important;
}
.title-underline,
.subtitle-underline {
	height: 2px !important;
	width: 54px !important;
	background: #2f5597 !important;
}
.industry-img {
	box-shadow: none !important;
}
.modern-solution-card,
.benefit-card,
.feature-item {
	box-shadow: none !important;
}
.modern-solution-card:hover,
.benefit-card:hover,
.feature-item:hover {
	transform: none !important;
	box-shadow: 0 10px 24px rgba(28, 54, 93, .08) !important;
}
.modern-solution-card::before,
.benefit-card::before {
	display: none;
}
.modern-solution-card,
.benefit-card {
	min-height: 0 !important;
	padding: 20px !important;
}
.modern-solution-card .solution-icon-wrapper,
.benefit-card .benefit-icon,
.features-section-industries .feature-gradient-icon {
	width: 44px !important;
	height: 44px !important;
	background: #f7f9fc !important;
}
.modern-solution-card .solution-icon-wrapper i,
.benefit-card .benefit-icon i,
.features-section-industries .feature-gradient-icon i {
	font-size: 1.05rem !important;
}
.modern-solution-card .solution-title,
.benefit-card .benefit-title,
.features-section-industries .feature-title {
	font-size: 1rem !important;
	font-weight: 800 !important;
}
.modern-solution-card .solution-description,
.benefit-card .benefit-description,
.features-section-industries .feature-description {
	font-size: .92rem !important;
	line-height: 1.62 !important;
}
.modern-solution-card,
.benefit-card {
	border-radius: 12px !important;
	border: 1px solid #dce7fb !important;
	background: #fff !important;
	box-shadow: 0 10px 22px rgba(24, 54, 107, .08) !important;
	gap: 10px;
	overflow: hidden;
	position: relative;
	transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease !important;
}
.modern-solution-card::after,
.benefit-card::after {
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 3px;
	background: #2f5597;
}
.modern-solution-card:hover,
.benefit-card:hover {
	border-color: #bdd0ef !important;
	box-shadow: 0 18px 34px rgba(24, 54, 107, .14) !important;
	transform: translateY(-4px) !important;
}
.modern-solution-card .solution-icon-wrapper,
.benefit-card .benefit-icon {
	border-radius: 10px !important;
	border: 1px solid #dce7fb !important;
	background: #f4f8ff !important;
	color: #2f5597 !important;
}
.modern-solution-card .solution-title,
.benefit-card .benefit-title {
	color: #172033 !important;
	line-height: 1.34 !important;
	margin-bottom: 2px !important;
}
.features-section-industries {
	padding: 56px 0 !important;
	background: #fff !important;
}
.features-section-industries .features-section-title {
	font-size: 1.75rem !important;
	font-weight: 800 !important;
}
.features-section-industries .feature-item {
	border-radius: 12px !important;
	border: 1px solid #dce7fb !important;
	background: #fff !important;
	box-shadow: 0 10px 22px rgba(24, 54, 107, .08) !important;
	padding: 22px !important;
	min-height: 220px;
	height: 100%;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	text-align: left !important;
	overflow: hidden;
	position: relative;
	transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease !important;
}
.features-section-industries .row.g-4 > [class*="col-"] {
	display: flex;
}
.features-section-industries .row.g-4 > [class*="col-"] > .feature-item {
	width: 100%;
}
.features-section-industries .feature-item::after {
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 3px;
	background: #2f5597;
}
.features-section-industries .feature-item:hover {
	border-color: #bdd0ef !important;
	box-shadow: 0 18px 34px rgba(24, 54, 107, .14) !important;
	transform: translateY(-4px) !important;
}
.features-section-industries .feature-icon-wrapper-large {
	margin-bottom: 14px;
	min-height: 46px;
}
.features-section-industries .feature-gradient-icon {
	border-radius: 10px !important;
	border: 1px solid #dce7fb !important;
	background: #f4f8ff !important;
	color: #2f5597 !important;
	width: 46px !important;
	height: 46px !important;
}
.features-section-industries .feature-title {
	color: #172033 !important;
	font-size: 1rem !important;
	line-height: 1.34 !important;
	margin-bottom: 8px !important;
	min-height: 2.8em;
	display: flex;
	align-items: flex-start;
}
.features-section-industries .feature-description {
	color: #5f6f86 !important;
	font-size: .92rem !important;
	line-height: 1.62 !important;
	margin: 0 !important;
	flex: 1;
}
.cta-section-industries {
	background: #f5f8fc !important;
	padding: 72px 0 !important;
	border-top: 1px solid #dbe6f3;
}
.industries-contact-panel {
	background: #fff;
	border: 1px solid #dbe6f3;
	box-shadow: 0 22px 54px rgba(28, 54, 93, .10);
	padding: 34px;
}
.industries-contact-copy {
	padding-right: 18px;
}
.industries-contact-kicker {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	color: #2f5597;
	font-size: .78rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
	margin-bottom: 14px;
}
.cta-section-industries .cta-heading {
	color: #172033 !important;
	font-size: 2rem;
	font-weight: 900 !important;
	line-height: 1.2;
	margin-bottom: 16px;
}
.cta-section-industries .cta-description {
	color: #5f6f86 !important;
	font-size: 1rem;
	line-height: 1.75;
	margin-bottom: 22px;
}
.industries-contact-points {
	display: grid;
	gap: 12px;
	margin: 0;
	padding: 0;
	list-style: none;
}
.industries-contact-points li {
	display: flex;
	gap: 10px;
	color: #40516c;
	font-weight: 700;
	line-height: 1.45;
}
.industries-contact-points i {
	color: #2f5597;
	margin-top: 4px;
}
.industries-contact-form {
	background: #f8fbff !important;
	border: 1px solid #dce7f6;
	padding: 26px !important;
	margin: 0 !important;
}
.industries-contact-form label {
	color: #1e3357 !important;
	font-weight: 800;
	font-size: .9rem;
	margin-bottom: 8px;
}
.industries-contact-form .form-group {
	margin-bottom: 16px;
}
.industries-contact-form .remove-input-background,
.industries-contact-form textarea.remove-input-background {
	width: 100%;
	background: #fff !important;
	border: 1px solid #cbd9ea !important;
	color: #172033 !important;
	min-height: 50px;
	padding: 12px 14px;
	border-radius: 0;
	transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}
.industries-contact-form textarea.remove-input-background {
	min-height: 132px;
	resize: vertical;
}
.industries-contact-form .remove-input-background::placeholder {
	color: #7a8799 !important;
}
.industries-contact-form .remove-input-background:focus {
	outline: none;
	background: #fff !important;
	border-color: #2f5597 !important;
	box-shadow: 0 0 0 4px rgba(47, 85, 151, .12);
}
.industries-contact-form .send-message-btn {
	background: #2f5597 !important;
	color: #fff !important;
	border-radius: 0;
	min-height: 48px;
	padding: 12px 24px;
	font-weight: 900;
	border: 0;
}
.industries-contact-form .send-message-btn:hover {
	background: #24457c !important;
	color: #fff !important;
}
@media (max-width: 991px) {
	.industries-hero-grid {
		grid-template-columns: 1fr;
	}
	.industries-hero h1 {
		font-size: 2rem;
	}
	.tab-content-wrapper {
		padding: 22px !important;
	}
	.industries-contact-copy {
		padding-right: 0;
		margin-bottom: 28px;
	}
	.industry-nav-panel {
		position: static;
	}
	.modern-tabs-industries {
		grid-template-columns: repeat(2, minmax(0, 1fr));
	}
	.government-credentials-grid {
		grid-template-columns: 1fr;
	}
}
@media (min-width: 992px) {
	.industries-layout {
		display: block;
		margin-left: 0;
		margin-right: 0;
	}
	.industries-sidebar-col {
		float: left;
		width: 280px;
		max-width: 280px;
		padding-left: 0;
		padding-right: 0;
		margin-right: 40px;
		margin-bottom: 28px !important;
	}
	.industries-content-col {
		display: block;
		float: none;
		width: auto;
		max-width: none;
		padding-left: 0;
		padding-right: 0;
		position: relative;
		z-index: 1;
	}
	.tab-content-wrapper {
		overflow: visible;
		padding-top: 0 !important;
	}
	.tab-content-wrapper > .mt-5,
	.industries-tab-inner > .mt-4 {
		margin-top: 0 !important;
	}
	.tab-content-wrapper > .row.mt-5,
	.industries-tab-inner > .row.mt-5,
	.tab-content-wrapper > .mt-5 > .mt-5 {
		clear: none;
	}
}
@media (max-width: 575px) {
	.industries-hero {
		padding: 38px 0 34px;
	}
	.industries-hero h1 {
		font-size: 1.72rem;
	}
	.industries-hero-actions {
		flex-direction: column;
	}
	.industries-primary-btn,
	.industries-secondary-btn {
		width: 100%;
	}
	.modern-tabs-industries {
		grid-template-columns: minmax(0, 1fr);
	}
	.government-credentials-panel {
		padding: 18px;
	}
	.industries-contact-panel,
	.industries-contact-form {
		padding: 20px !important;
	}
	.cta-section-industries .cta-heading {
		font-size: 1.55rem;
	}
}
</style>
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Industries</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Industries</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<main class="industries-page-refresh">
<section class="industries-section-modern">
	<div class="container">
		<div class="row industries-layout justify-content-center">
			<div class="col-12 col-lg-4 col-xl-3 mb-4 mb-lg-0 industries-sidebar-col">
				<aside class="industry-nav-panel">
					<div class="industry-nav-kicker">Industries</div>
					<h2 class="industry-nav-title">Choose an industry</h2>
					<p class="industry-nav-copy">Explore Microsoft platform solutions shaped for regulated, data-heavy teams.</p>
					<button class="btn btn-modern-toggle default-background btn-block d-lg-none mb-3" type="button" data-toggle="collapse" data-target="#tabsMenu" aria-expanded="false" aria-controls="tabsMenu">
						<i class="fa fa-bars"></i> Select industry
					</button>
					<div class="collapse d-lg-block" id="tabsMenu">
					<ul class="nav nav-tabs modern-tabs-industries" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
								<i class="icofont-doctor"></i>
								<strong>Healthcare</strong>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="energy-tab-nav" data-toggle="tab" href="#energy" role="tab" aria-controls="energy" aria-selected="false">
								<i class="icofont-fire-burn"></i>
								<strong>Energy</strong>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="government-tab" data-toggle="tab" href="#government" role="tab" aria-controls="government" aria-selected="false">
								<i class="icofont-building-alt"></i>
								<strong>State &amp; Local Government</strong>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="transportation-tab" data-toggle="tab" href="#transportation" role="tab" aria-controls="transportation" aria-selected="false">
								<i class="icofont-delivery-time"></i>
								<strong>Transportation &amp; Logistics</strong>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="legal-tab" data-toggle="tab" href="#legal" role="tab" aria-controls="legal" aria-selected="false">
								<i class="icofont-law-document"></i>
								<strong>Legal</strong>
							</a>
						</li>
					</ul>
				</div>
				</aside>
			</div>
			<div class="col-12 col-lg-8 col-xl-9 industries-content-col">
				<div class="tab-content modern-tab-content" id="myTabContent">
					<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
						<div class="tab-content-wrapper">
							<div class="mt-5">
								<div class="row align-items-center industry-intro-section">
									<div class="col-md-6">
										<div class="industry-intro-content">
											<span class="industry-tag">HEALTHCARE SOLUTIONS</span>
											<h2 class="industry-title">Healthcare</h2>
											<div class="title-underline"></div>
											<p class="industry-description">Data empowers personalized healthcare journeys. Analyzing medical history and wearables allows providers to tailor treatments and optimize workflows, leading to better patient outcomes.</p>
											<p class="industry-description">We partner with you to unlock this potential. Our expertise in data analytics and EHR integration empowers you to consolidate patient data, generate actionable insights for personalized care, and make data-driven decisions for better resource allocation and cost reduction.</p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="industry-image-wrapper">
											<img class="img-fluid industry-img" src="{{ asset('images/industry/nurse.png') }}" alt="Healthcare Industry">
										</div>
									</div>
								</div>
							</div>
							<div class="row mt-5 g-4">
								<div class="col-12 col-md-6 col-xl-4 mb-3">
									<div class="card modern-solution-card p-4">
										<div class="solution-icon-wrapper default-background">
											<i class="fa fa-database"></i>
										</div>
										<h6 class="solution-title"><strong>Microsoft Fabric in healthcare</strong></h6>
										<p class="solution-description">Unlock powerful analytics in healthcare data. As Microsoft Fabric Partners we empower organizations build data-driven decisions solutions</p>
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="card modern-solution-card p-4">
										<div class="solution-icon-wrapper default-background">
											<i class="fa fa-layer-group"></i>
										</div>
										<h6 class="solution-title"><strong>Power Platform in healthcare</strong></h6>
										<p class="solution-description">Streamline workflows and automates tasks, building low-code/no-code solutions that empower organizations to transform data into actionable insights.</p>
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="card modern-solution-card p-4">
										<div class="solution-icon-wrapper default-background">
											<i class="fa fa-cloud"></i>
										</div>
										<h6 class="solution-title"><strong>Microsoft Cloud for healthcare</strong></h6>
										<p class="solution-description">Cloud for healthcare brings together secure, scalable cloud services to empower healthcare organizations with data-driven insights.</p>
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="card modern-solution-card p-4 mt-4">
										<div class="solution-icon-wrapper default-background">
											<i class="fa fa-brain"></i>
										</div>
										<h6 class="solution-title"><strong>Azure AI in healthcare</strong></h6>
										<p class="solution-description">Empowers organizations with intelligent capabilities, enabling tasks like analyzing medical images for faster diagnoses, predicting patient outcomes and extracting insights from unstructured data to improve research and development.</p>
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="card modern-solution-card p-4 mt-4">
										<div class="solution-icon-wrapper default-background">
											<i class="fa fa-code-branch"></i>
										</div>
										<h6 class="solution-title"><strong>FHIR Integration</strong></h6>
										<p class="solution-description">Unlocks seamless data exchange within healthcare ecosystems, enabling secure sharing of patient information across different applications and platforms for improved care coordination and decision-making.</p>
									</div>
								</div>
								<div class="col-12 col-md-6 col-xl-4">
									<div class="card modern-solution-card p-4 mt-4">
										<div class="solution-icon-wrapper default-background">
											<i class="fa fa-chart-column"></i>
										</div>
										<h6 class="solution-title"><strong>Tableau in healthcare</strong></h6>
										<p class="solution-description">Transform complex healthcare data into clear, insightful visualizations allowing organization to identify trends, patterns leading to better patient care, optimized resource allocation, and informed decision-making.</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="energy" role="tabpanel" aria-labelledby="energy-tab-nav">
						<div class="tab-content-wrapper">
							<div class="industries-tab-inner">
								<div class="mt-4">
									<div class="row align-items-center industry-intro-section">
										<div class="col-md-6">
											<div class="industry-image-wrapper">
												<img class="img-fluid industry-img" src="{{ asset('images/industry/oil.png') }}" alt="Oil & Gas Industry">
											</div>
										</div>
										<div class="col-md-6">
											<div class="industry-intro-content">
												<span class="industry-tag">ENERGY SOLUTIONS</span>
												<h2 class="industry-title mt-2">Oil & Gas</h2>
												<div class="title-underline"></div>
												<p>Data in Oil and Gas industry fuels the revolution of how the industry operates. With forward thinking solutions, players explore the transformative opportunities presented by today’s technology.</p>
												<p>Armely as a partner has a track record in helping organizations in this industry realize the value of digital transformation. </p>
											</div>
										</div>
									</div>
								</div>
								<div class="row mt-5 g-4">
									<div class="col-12 col-md-6 col-xl-4">
										<div class="card modern-solution-card p-4">
											<div class="solution-icon-wrapper default-background">
												<i class="fa fa-database"></i>
											</div>
											<h6 class="solution-title"><strong>Microsoft Fabric in oil & gas</strong></h6>
											<p class="solution-description">Unlock powerful analytics in oil & gas data. As Microsoft Fabric Partners we empower organizations build data-driven decisions solutions </p>
										</div>
									</div>
									<div class="col-12 col-md-6 col-xl-4">
										<div class="card modern-solution-card p-4">
											<div class="solution-icon-wrapper default-background">
												<i class="fa fa-layer-group"></i>
											</div>
											<h6 class="solution-title"><strong>Power Platform in oil & gas</strong></h6>
											<p class="solution-description">Streamline workflows and automates tasks, building low-code/no-code solutions that empower organizations to transform data into actionable insights</p>
										</div>
									</div>
									<div class="col-12 col-md-6 col-xl-4">
										<div class="card modern-solution-card p-4">
											<div class="solution-icon-wrapper default-background">
												<i class="fa fa-brain"></i>
											</div>
											<h6 class="solution-title"><strong>Azure AI in oil & gas</strong></h6>
											<p class="solution-description">Empowers organizations with intelligent capabilities, enabling tasks like analyzing medical images for faster diagnoses, predicting patient outcomes and extracting insights from unstructured data to improve research and development.</p>
										</div>
									</div>
									<div class="col-12 col-md-6 col-xl-4">
										<div class="card modern-solution-card p-4">
											<div class="solution-icon-wrapper default-background">
												<i class="fa fa-plug"></i>
											</div>
											<h6 class="solution-title"><strong>API Integration</strong></h6>
											<p class="solution-description">Transform complex oil & gas data into clear, insightful visualizations allowing organization to identify trends by integrating with external data</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="government" role="tabpanel" aria-labelledby="government-tab">
						<div class="tab-content-wrapper">
							<div class="mt-5">
								<div class="row align-items-center industry-intro-section">
									<div class="col-md-6">
										<div class="industry-intro-content">
											<span class="industry-tag">GOVERNMENT SOLUTIONS</span>
											<h2 class="industry-title">State &amp; Local Government</h2>
											<div class="title-underline"></div>
											<p class="industry-description">In today's data-driven world, state and local governments are undergoing a revolution. Data is no longer just numbers; it's the fuel that propels smarter decision-making, improved service delivery, and a more responsive government.</p>
											<p class="industry-description">Armely, a trusted partner with a proven track record, empowers state and local agencies to unlock the transformative potential of data through forward-thinking solutions.</p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="industry-image-wrapper">
											<img class="img-fluid industry-img" src="{{ asset('images/industry/government.png') }}" alt="State and Local Government">
										</div>
									</div>
								</div>
								<div class="mt-5">
									<h5 class="section-subtitle">Government Contracting Credentials</h5>
									<div class="subtitle-underline"></div>
										<div class="government-credentials-panel">
											<p class="government-credentials-copy">Armely is a qualified government contractor with active certifications, partner registrations, and procurement experience for public-sector technology programs.</p>
											<ul class="government-credentials-grid">
												<li class="government-credential-item">
													<span class="government-credential-icon"><i class="fa fa-id-badge"></i></span>
													<span class="government-credential-text"><strong>Active SAM.gov Registration</strong><span>Registered with Unique Entity ID for public-sector contracting.</span></span>
												</li>
												<li class="government-credential-item">
													<span class="government-credential-icon"><i class="fa fa-certificate"></i></span>
													<span class="government-credential-text"><strong>MWBE Certified</strong><span>Minority-owned business certification for supplier diversity goals.</span></span>
												</li>
												<li class="government-credential-item">
													<span class="government-credential-icon"><i class="fa fa-shield"></i></span>
													<span class="government-credential-text"><strong>CJIS Certified</strong><span>Prepared for law enforcement data handling requirements.</span></span>
												</li>
												<li class="government-credential-item">
													<span class="government-credential-icon"><i class="fa fa-windows"></i></span>
													<span class="government-credential-text"><strong>Microsoft Fabric Engineering Partner</strong><span>Modern data platform delivery with Microsoft technologies.</span></span>
												</li>
												<li class="government-credential-item">
													<span class="government-credential-icon"><i class="fa fa-cloud"></i></span>
													<span class="government-credential-text"><strong>TD Synnex CSP</strong><span>Government cloud licensing support through TD Synnex.</span></span>
												</li>
												<li class="government-credential-item">
													<span class="government-credential-icon"><i class="fa fa-handshake-o"></i></span>
													<span class="government-credential-text"><strong>Carahsoft Partner</strong><span>Technology Solutions partner for public-sector channels.</span></span>
												</li>
												<li class="government-credential-item">
													<span class="government-credential-icon"><i class="fa fa-file-text-o"></i></span>
													<span class="government-credential-text"><strong>Procurement Experience</strong><span>Experienced with state and municipal procurement processes.</span></span>
												</li>
											</ul>
										</div>
								</div>
								<div class="mt-5">
									<h5 class="section-subtitle">Here's how Armely makes a difference:</h5>
									<div class="subtitle-underline"></div>
									<div class="row mt-4 g-4">
										<div class="col-12 col-md-6 col-xl-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="fa fa-chart-line"></i>
												</div>
												<h6 class="benefit-title">Harnessing Data Insights</h6>
												<p class="benefit-description">We help you collect, analyze, and utilize data to gain valuable insights into your community's needs.</p>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="fa fa-gauge-high"></i>
												</div>
												<h6 class="benefit-title">Streamlined Operations</h6>
												<p class="benefit-description">Our solutions optimize processes, improve efficiency, and free up valuable resources for what matters most – serving your citizens.</p>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="fa fa-arrow-trend-up"></i>
												</div>
												<h6 class="benefit-title">Data-Driven Decisions</h6>
												<p class="benefit-description">Empower your leaders with real-time information to make informed decisions that truly benefit your community.</p>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="fa fa-users"></i>
												</div>
												<h6 class="benefit-title">Enhanced Citizen Engagement</h6>
												<p class="benefit-description">We help foster transparency and build trust through open data initiatives and citizen-friendly applications.</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="transportation" role="tabpanel" aria-labelledby="transportation-tab">
						<div class="tab-content-wrapper">
							<div class="mt-5">
								<div class="row align-items-center industry-intro-section">
									<div class="col-md-6">
										<div class="industry-intro-content">
											<span class="industry-tag">TRANSPORTATION SOLUTIONS</span>
											<h2 class="industry-title">Transportation &amp; Logistics</h2>
											<div class="title-underline"></div>
											<p class="industry-description">Transportation and logistics teams rely on accurate operations data, dependable reporting, and fast coordination across dispatch, warehouse, and field operations.</p>
											<p class="industry-description">Armely helps organizations modernize planning, tracking, and analytics using Microsoft Fabric, Power Platform, and Azure AI so teams can reduce delays and improve delivery performance.</p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="industry-image-wrapper">
											<img class="img-fluid industry-img" src="{{ asset('images/industry/logistic.png') }}" alt="Transportation and Logistics">
										</div>
									</div>
								</div>
								<div class="mt-5">
									<h5 class="section-subtitle">Featured Client Example</h5>
									<div class="subtitle-underline"></div>
									<div class="card modern-solution-card p-4">
										<div class="solution-icon-wrapper default-background">
											<i class="fa fa-truck"></i>
										</div>
										<h6 class="solution-title"><strong>MHC Case Study</strong></h6>
										<p class="solution-description">See how Armely supports transportation operations with modern Microsoft data and workflow solutions that improve visibility, speed, and execution quality.</p>
										<a href="{{ route('case-studies.index', ['industry' => 'transportation-logistics', 'case_industry' => 'transportation-logistics']) }}" class="btn default-background text-light btn-modern-cta mt-3">View Case Study</a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="legal" role="tabpanel" aria-labelledby="legal-tab">
						<div class="tab-content-wrapper">
							<div class="mt-5">
								<div class="row align-items-center industry-intro-section ">
									<div class="col-md-6">
										<div class="industry-intro-content">
											<span class="industry-tag">LEGAL SOLUTIONS</span>
											<h2 class="industry-title">Legal</h2>
											<div class="title-underline"></div>
											<p class="industry-description">The legal industry is undergoing a seismic shift, driven by the transformative power of data. Data has been revolutionizing how legal services are delivered. At Armely, we're at the forefront, partnering with forward-thinking legal organizations to unlock the immense potential of this data revolution.</p>
											<p class="industry-description">Armely, a trusted partner with a proven track record, empowers local governments to unlock the transformative potential of data through forward-thinking solutions.</p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="industry-image-wrapper">
											<img class="img-fluid industry-img" src="{{ asset('images/industry/legal.png') }}" alt="Legal Industry">
										</div>
									</div>
								</div>
								<div class="mt-5">
									<h5 class="section-subtitle">Harnessing the Power of Information</h5>
									<div class="subtitle-underline"></div>
									<p class="section-text">We understand the challenges of navigating the vast amount of data that permeates every aspect of the legal industry. From case law and contracts to due diligence and client communication, data holds the key to unlocking new levels of efficiency, accuracy, and insight.</p>
									<h6 class="subsection-title">Armely: Your Trusted Partner in Legal Data Transformation</h6>
									<p class="section-text">Our proven track record speaks for itself. We partner with legal organizations to:</p>
									<div class="row mt-4 g-4">
										<div class="col-12 col-md-6 col-xl-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="fa fa-database"></i>
												</div>
												<h6 class="benefit-title">Extract value from data</h6>
												<p class="benefit-description">We help implement solutions to collect, organize, and analyze legal data, turning it into actionable insights.</p>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="fa fa-robot"></i>
												</div>
												<h6 class="benefit-title">Automate routine tasks</h6>
												<p class="benefit-description">By leveraging data and automation, we free up valuable time for legal professionals to focus on high-value strategy and client service.</p>
											</div>
										</div>
										<div class="col-12 col-md-6 col-xl-4">
											<div class="benefit-card">
												<div class="benefit-icon default-background">
													<i class="fa fa-trophy"></i>
												</div>
												<h6 class="benefit-title">Gain a competitive edge</h6>
												<p class="benefit-description">Data-driven insights empower lawyers to build stronger cases, deliver more efficient services, and gain a competitive advantage in the market.</p>
											</div>
										</div>
										<div class="mt-4">
											<a href="case study" class="btn default-background text-light btn-modern-cta">
												See our Case Studies for Legal
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="features-section-industries">
	<div class="container">
		<div class="row justify-content-center text-center">
			<div class="col-12">
				<h3 class="features-section-title">Why Partner With Armely?</h3>
				<p class="features-section-subtitle">Industry teams choose Armely when they need secure Microsoft platform delivery, clearer data, and practical automation that survives real operations.</p>
			</div>
		</div>
		<div class="row justify-content-center align-items-center mt-5 g-4">
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="feature-item">
					<div class="feature-icon-wrapper-large">
						<div class="feature-gradient-icon">
							<i class="fa fa-plug"></i>
						</div>
					</div>
					<h5 class="feature-title">Connected systems</h5>
					<p class="feature-description">Seamlessly integrate systems and data across your organization</p>
				</div>
			</div>
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="feature-item">
					<div class="feature-icon-wrapper-large">
						<div class="feature-gradient-icon">
							<i class="fa fa-shield-halved"></i>
						</div>
					</div>
					<h5 class="feature-title">Secure by design</h5>
					<p class="feature-description">Enterprise-grade security with compliance and data protection</p>
				</div>
			</div>
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="feature-item">
					<div class="feature-icon-wrapper-large">
						<div class="feature-gradient-icon">
							<i class="fa fa-gears"></i>
						</div>
					</div>
					<h5 class="feature-title">Productive teams</h5>
					<p class="feature-description">Streamline workflows and boost team efficiency instantly</p>
				</div>
			</div>
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="feature-item">
					<div class="feature-icon-wrapper-large">
						<div class="feature-gradient-icon">
							<i class="fa fa-chart-line"></i>
						</div>
					</div>
					<h5 class="feature-title">Decision-ready insights</h5>
					<p class="feature-description">Data-driven intelligence to power strategic decisions</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="cta-section-industries">
	<div class="container">
		<div class="industries-contact-panel">
			<div class="row align-items-start">
				<div class="col-lg-5">
					<div class="industries-contact-copy">
						<span class="industries-contact-kicker"><i class="fa fa-paper-plane"></i> Start a conversation</span>
						<h3 class="cta-heading">Ready to Transform Your Business?</h3>
						<p class="cta-description">Tell us what you are trying to modernize. Armely will help shape the right path across data, AI, cloud, and Microsoft platform delivery.</p>
						<ul class="industries-contact-points">
							<li><i class="fa fa-check"></i><span>Industry-aware discovery and solution planning</span></li>
							<li><i class="fa fa-check"></i><span>Microsoft Fabric, Power Platform, Azure AI, and cloud expertise</span></li>
							<li><i class="fa fa-check"></i><span>Practical next steps for procurement, delivery, and adoption</span></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-7">
					<form class="form industries-contact-form" id="industries-contact-form" method="post" action="{{ route('contact.submit') }}">
						@csrf
						<p class="p-3 alert" id="IndustriesSubmitMessage" style="display:none;"></p>
						@if($errors->any())
							<div class="alert alert-danger">
								@foreach($errors->all() as $error)
									<div>{{ $error }}</div>
								@endforeach
							</div>
						@endif
						<div class="row">
							<div class="col-lg-6 col-md-6 col-12">
								<label class="text-start">Name *</label>
								<div class="form-group"><input required class="remove-input-background" name="name" type="text" placeholder="Name" value="{{ old('name') }}"></div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<label class="text-start">Email *</label>
								<div class="form-group"><input required class="remove-input-background" name="email" type="email" placeholder="Email" value="{{ old('email') }}"></div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<label class="text-start">Organization Name</label>
								<div class="form-group"><input class="remove-input-background" name="organization" type="text" placeholder="Organization Name" value="{{ old('organization') }}"></div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<label class="text-start">Subject *</label>
								<div class="form-group"><input required class="remove-input-background" name="subject" type="text" placeholder="Subject" value="{{ request('subject') ?? old('subject', 'Industry transformation consultation') }}"></div>
							</div>
							<div class="col-lg-12 col-md-12 col-12">
								<label class="text-start">Message *</label>
								<div class="form-group"><textarea required class="remove-input-background" name="message" placeholder="Tell us about your industry, goals, timeline, or current platform needs.">{{ old('message') }}</textarea></div>
							</div>
							<input style="display: none;" type="text" name="website" class="honeypot">
							<div class="col-lg-12">
								<div class="form-group">
									<label class="text-start">Confirm you are not a robot *</label>
									@if(!empty(config('services.recaptcha.site_key')))
										<div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
									@else
										<div class="alert alert-warning">reCAPTCHA is not configured. Please set <strong>CAPTURE_SITE_KEY</strong>.</div>
									@endif
								</div>
							</div>
							<div class="col-12">
								<div class="form-group mb-0">
									<button type="submit" class="btn send-message-btn" name="submit_form">Send Message</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
	var recaptchaEl = document.querySelector('#industries-contact-form .g-recaptcha');
	if (!recaptchaEl) {
		return;
	}

	var scriptLoaded = false;
	var loadRecaptcha = function () {
		if (scriptLoaded) {
			return;
		}
		scriptLoaded = true;
		var script = document.createElement('script');
		script.src = 'https://www.google.com/recaptcha/api.js';
		script.async = true;
		script.defer = true;
		document.body.appendChild(script);
	};

	if ('IntersectionObserver' in window) {
		var observer = new IntersectionObserver(function(entries) {
			entries.forEach(function(entry) {
				if (entry.isIntersecting) {
					loadRecaptcha();
					observer.disconnect();
				}
			});
		}, { rootMargin: '200px 0px' });
		observer.observe(recaptchaEl);
	} else {
		loadRecaptcha();
	}
});

document.addEventListener('DOMContentLoaded', function() {
	var form = document.getElementById('industries-contact-form');
	if (!form) {
		return;
	}

	form.addEventListener('submit', function(event) {
		event.preventDefault();
		event.stopPropagation();

		var submitBtn = form.querySelector('button[name="submit_form"]');
		var messageDiv = document.getElementById('IndustriesSubmitMessage');
		var originalBtnText = submitBtn ? submitBtn.textContent : 'Send Message';

		messageDiv.textContent = '';
		messageDiv.className = 'p-3 alert';
		messageDiv.style.display = 'none';

		var recaptchaResponse = (form.querySelector('textarea[name="g-recaptcha-response"]')?.value || '').trim();
		if (!recaptchaResponse) {
			messageDiv.className = 'p-3 alert alert-danger';
			messageDiv.textContent = 'Please verify that you are not a robot.';
			messageDiv.style.display = 'block';
			return false;
		}

		submitBtn.disabled = true;
		submitBtn.textContent = 'Sending...';

		var formData = new FormData(form);
		formData.append('g-recaptcha-response', recaptchaResponse);

		fetch(form.action, {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
				'Accept': 'application/json'
			},
			body: formData
		})
			.then(function(response) {
				return response.json();
			})
			.then(function(data) {
				messageDiv.className = 'p-3 alert';
				if (data.success) {
					messageDiv.classList.add('alert-success');
					messageDiv.textContent = data.message || 'Message sent successfully.';
					form.reset();
					if (window.grecaptcha && typeof window.grecaptcha.reset === 'function') {
						window.grecaptcha.reset();
					}
				} else {
					messageDiv.classList.add('alert-danger');
					messageDiv.textContent = data.message || 'An error occurred. Please try again.';
				}
				messageDiv.style.display = 'block';
			})
			.catch(function(error) {
				console.error('Error:', error);
				messageDiv.className = 'p-3 alert alert-danger';
				messageDiv.textContent = 'An error occurred. Please try again.';
				messageDiv.style.display = 'block';
			})
			.finally(function() {
				submitBtn.disabled = false;
				submitBtn.textContent = originalBtnText;
			});

		return false;
	}, true);
});

function initIndustryTabs() {
	var tabLinks = Array.prototype.slice.call(document.querySelectorAll('#myTab .nav-link[href^="#"]'));
	var tabPanes = Array.prototype.slice.call(document.querySelectorAll('#myTabContent .tab-pane'));

	if (!tabLinks.length || !tabPanes.length) {
		return;
	}

	function activateIndustryTab(link) {
		var targetSelector = link.getAttribute('href');
		var targetPane = targetSelector ? document.querySelector(targetSelector) : null;

		if (!targetPane) {
			return;
		}

		tabLinks.forEach(function (tabLink) {
			var isActive = tabLink === link;
			tabLink.classList.toggle('active', isActive);
			tabLink.setAttribute('aria-selected', isActive ? 'true' : 'false');
		});

		tabPanes.forEach(function (pane) {
			var isActive = pane === targetPane;
			pane.classList.toggle('active', isActive);
			pane.classList.toggle('show', isActive);
		});
	}

	tabLinks.forEach(function (link) {
		link.addEventListener('click', function (event) {
			event.preventDefault();
			event.stopPropagation();
			activateIndustryTab(link);
		});
	});

	document.addEventListener('click', function (event) {
		var link = event.target.closest('#myTab .nav-link[href^="#"]');
		if (!link) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();
		activateIndustryTab(link);
	}, true);
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initIndustryTabs);
} else {
	initIndustryTabs();
}
</script>
@endpush
