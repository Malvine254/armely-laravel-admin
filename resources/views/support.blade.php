@extends('layouts.public')

@section('title', 'Support — Mela AI | Armely')
@section('meta_description', 'Get help with Mela AI and Armely solutions — contact support, response times, and quick troubleshooting.')
@section('meta_keywords', 'Mela AI support, Armely support, help, troubleshooting, contact support, technical support')
@section('canonical_url', url('/support'))

@push('head')
<meta name="robots" content="index,follow">
<meta property="og:type" content="website">
<meta property="og:title" content="Support — Mela AI | Armely">
<meta property="og:description" content="Get help with Mela AI and Armely solutions — contact support, response times, and quick troubleshooting.">
<meta property="og:url" content="{{ url('/support') }}">
<meta property="og:site_name" content="Armely">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Support — Mela AI | Armely">
<meta name="twitter:description" content="Get help with Mela AI and Armely solutions — contact support, response times, and quick troubleshooting.">
@endpush

@push('styles')
<style>
/***** Support Page Styles *****/
:root { --sp-accent: #2f5597; }
.policy-hero { background: linear-gradient(180deg, rgba(47,85,151,.08), transparent); padding: 40px 0 10px; }
.policy-wrapper { display: grid; grid-template-columns: 300px 1fr; gap: 24px; }
@media (max-width: 992px) { .policy-wrapper { grid-template-columns: 1fr; } }

.toc-card { position: sticky; top: 90px; align-self: start; border-radius: 12px; border: 1px solid #e9ecef; background: #fff; box-shadow: 0 6px 18px rgba(0,0,0,.06); }
.toc-card .toc-header { padding: 16px 18px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; gap: 10px; }
.toc-card .toc-header i { color: var(--sp-accent); }
.toc-card .toc-list { list-style: none; margin: 0; padding: 8px 0 14px; }
.toc-card .toc-list li { margin: 0; }
.toc-card .toc-list a { display: block; padding: 10px 18px; color: #333; text-decoration: none; border-left: 3px solid transparent; transition: all .2s ease; }
.toc-card .toc-list a:hover { background: #f7f9fc; border-left-color: var(--sp-accent); color: var(--sp-accent); }
.toc-card .toc-list a.active { background: #eef3fb; border-left-color: var(--sp-accent); color: var(--sp-accent); font-weight: 600; }

.policy-card { border-radius: 12px; border: 1px solid #e9ecef; background: #fff; box-shadow: 0 6px 18px rgba(0,0,0,.06); padding: 28px; }
.policy-card h3 { color: var(--sp-accent); margin-top: 28px; margin-bottom: 10px; font-weight: 700; }
.policy-card h4 { color: #22314f; margin-top: 22px; margin-bottom: 8px; font-weight: 700; font-size: 1.05rem; }
.policy-card p { color: #4a5568; line-height: 1.8; }
.policy-card ol, .policy-card ul { padding-left: 20px; color: #4a5568; line-height: 1.8; }
.policy-card ul li { margin-bottom: 6px; }
.policy-card a { color: var(--sp-accent); }
.anchor-offset { scroll-margin-top: 100px; }

.section-heading-modern { font-weight: 800; letter-spacing: .3px; }
.title-divider { width: 80px; height: 4px; background: var(--sp-accent); border-radius: 2px; margin: 12px 0 0; }

.contact-table { width: 100%; border-collapse: collapse; margin: 14px 0 20px; }
.contact-table th, .contact-table td { text-align: left; padding: 12px 14px; border: 1px solid #e9ecef; vertical-align: top; }
.contact-table th { background: #f7f9fc; color: #22314f; font-weight: 700; }
.contact-table td a { font-weight: 600; }

.support-meta { list-style: none; padding: 0; margin: 0 0 8px; }
.support-meta li { margin-bottom: 8px; color: #4a5568; }
.support-meta li strong { color: #22314f; }

.support-note { border-left: 4px solid var(--sp-accent); background: #f5f8fd; padding: 14px 16px; border-radius: 6px; margin: 16px 0; color: #34455f; }
</style>
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay policy-hero">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Support</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Support</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<section class="contact-us mt-4">
	<div class="container">
		<div class="policy-wrapper">
			<!-- Table of Contents -->
			<aside class="toc-card">
				<div class="toc-header">
					<i class="icofont-listing-number"></i>
					<strong>On this page</strong>
				</div>
				<ol class="toc-list">
					<li><a href="#contact">Contact us</a></li>
					<li><a href="#what-to-include">What to include</a></li>
					<li><a href="#troubleshooting">Quick troubleshooting</a></li>
					<li><a href="#administrators">For administrators</a></li>
					<li><a href="#security">Security issues</a></li>
					<li><a href="#feedback">Feedback &amp; feature requests</a></li>
					<li><a href="#related">Related links</a></li>
				</ol>
			</aside>

			<!-- Support Content -->
			<div class="policy-card">
				<div id="intro" class="anchor-offset">
					<h3 class="section-heading-modern">Support — Mela AI</h3>
					<div class="title-divider"></div>
					<p>Need help with <strong>Mela AI</strong> or another Armely solution? We're here for you. Most issues can be resolved in a few minutes using the quick troubleshooting below — and our team is one email away if you need us.</p>
				</div>

				<div id="contact" class="anchor-offset">
					<h3>Contact us</h3>
					<table class="contact-table">
						<thead>
							<tr>
								<th>Reason</th>
								<th>Email</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong>Product help &amp; technical issues</strong> (Mela AI or any Armely solution)</td>
								<td><a href="mailto:support@armely.com">support@armely.com</a></td>
							</tr>
							<tr>
								<td><strong>General &amp; sales inquiries</strong></td>
								<td><a href="mailto:info@armely.com">info@armely.com</a></td>
							</tr>
						</tbody>
					</table>
					<ul class="support-meta">
						<li><strong>Support hours:</strong> Monday–Friday, 9:00am–6:00pm CT (excluding US holidays)</li>
						<li><strong>Typical first response:</strong> within 1 business day</li>
						<li><strong>Phone:</strong> <a href="tel:+19724600643">+1 972 460 0643</a></li>
					</ul>
					<p>When you email <a href="mailto:support@armely.com">support@armely.com</a>, we'll get back to you within 1 business day.</p>
				</div>

				<div id="what-to-include" class="anchor-offset">
					<h3>What to include when you contact support</h3>
					<p>Giving us these details up front helps us resolve your issue faster:</p>
					<ol>
						<li><strong>What happened</strong> — a short description of the problem.</li>
						<li><strong>What you expected</strong> to happen instead.</li>
						<li><strong>Steps to reproduce</strong> — what you clicked or typed leading up to it.</li>
						<li><strong>Screenshots or a screen recording</strong>, if possible.</li>
						<li><strong>When it happened</strong> — date, time, and your time zone.</li>
						<li><strong>Your environment</strong> — browser and version (e.g. Chrome 126), operating system, and whether you're on desktop or mobile.</li>
						<li><strong>Any error message</strong> shown on screen (copy the exact text).</li>
						<li><strong>Your organization / tenant</strong> (company name) and the account email you signed in with — never send us your password.</li>
					</ol>
					<div class="support-note">
						<strong>Please don't include passwords, secret keys, or other credentials in your message.</strong> Our team will never ask for your password.
					</div>
				</div>

				<div id="troubleshooting" class="anchor-offset">
					<h3>Quick troubleshooting</h3>

					<h4>I can't sign in / I'm stuck in a login loop</h4>
					<ul>
						<li>Make sure you're using your <strong>organization's Microsoft (Entra ID) account</strong>, not a personal Microsoft account.</li>
						<li>Allow <strong>pop-ups</strong> for the Mela AI site (sign-in may open a Microsoft window).</li>
						<li>Clear your browser cache or try a <strong>private/incognito window</strong>.</li>
						<li>If you see a <strong>"needs admin approval"</strong> message, your IT administrator must grant consent for Mela AI in your tenant. Contact your admin (or <a href="mailto:support@armely.com">support@armely.com</a>).</li>
					</ul>

					<h4>The page is blank or won't load after signing in</h4>
					<ul>
						<li><strong>Refresh</strong> the page.</li>
						<li>Ensure <strong>cookies and JavaScript</strong> are enabled for the site.</li>
						<li>Try a different modern browser (Chrome, Edge, or Firefox, latest version).</li>
						<li>Disable browser extensions that may block scripts, then reload.</li>
					</ul>

					<h4>Mela AI isn't responding / I get an error when I send a message</h4>
					<ul>
						<li>Check your <strong>internet connection</strong> and try again — most transient errors clear on a retry.</li>
						<li>If a specific AI model is unavailable, try again shortly or switch models; Mela AI will usually fall back to an available model automatically.</li>
						<li>If you hit a <strong>usage limit</strong>, wait for the limit window to reset or ask your admin to adjust limits.</li>
					</ul>

					<h4>Responses are slow</h4>
					<ul>
						<li>Large documents and complex questions take longer to process.</li>
						<li>During periods of heavy load, responses may be delayed briefly — please retry.</li>
					</ul>

					<h4>File upload isn't working</h4>
					<ul>
						<li>Confirm the file type is supported (common documents, spreadsheets, PDFs, images, and text/code files).</li>
						<li>Very large files may exceed the upload limit — try a smaller file or split it.</li>
						<li>Reload the page and try the upload again.</li>
					</ul>

					<h4>Voice input isn't working</h4>
					<ul>
						<li>Grant the site permission to use your <strong>microphone</strong> when prompted.</li>
						<li>Check that the correct microphone is selected in your browser/OS settings.</li>
						<li>Voice works best in Chrome or Edge on desktop.</li>
					</ul>

					<h4>Search or "enterprise" answers return nothing</h4>
					<ul>
						<li>Connectors to your organization's content (e.g. SharePoint, OneDrive) require your <strong>administrator to enable them and grant consent</strong>.</li>
						<li>Newly added content may take time to sync before it's searchable.</li>
						<li>You will only see content <strong>you are permitted to access</strong>.</li>
					</ul>

					<h4>I think the service is down</h4>
					<ul>
						<li>If you administer a Mela AI deployment, your admins can verify the health endpoint of your deployment (<code>/health</code> on the app's API URL).</li>
						<li>Still stuck? Email <a href="mailto:support@armely.com">support@armely.com</a> with the details above and we'll investigate.</li>
					</ul>
				</div>

				<div id="administrators" class="anchor-offset">
					<h3>For administrators (Managed Application deployments)</h3>
					<p>If your organization deployed Mela AI from Azure Marketplace into your own Azure subscription:</p>
					<ul>
						<li>Both apps (frontend and backend) run as <strong>Azure Container Apps</strong> in the deployment's managed resource group.</li>
						<li>Confirm the backend <strong><code>/health</code></strong> endpoint returns <code>healthy</code>.</li>
						<li>Verify required configuration is present: the Entra <strong>tenant ID</strong> and <strong>application (client) ID</strong> for sign-in, and a valid <strong>Azure OpenAI</strong> endpoint and key.</li>
						<li>Review container logs in the associated <strong>Log Analytics / Application Insights</strong> resource for startup or runtime errors.</li>
						<li>For deployment or upgrade help, contact <a href="mailto:support@armely.com">support@armely.com</a>.</li>
					</ul>
				</div>

				<div id="security" class="anchor-offset">
					<h3>Security issues</h3>
					<p>If you believe you've found a security vulnerability, please <strong>do not</strong> post it publicly. Email <a href="mailto:support@armely.com">support@armely.com</a> with the subject line <strong>"SECURITY"</strong> and we will respond promptly.</p>
				</div>

				<div id="feedback" class="anchor-offset">
					<h3>Feedback &amp; feature requests</h3>
					<p>We love hearing how to make Mela AI better. Send ideas and feedback to <a href="mailto:info@armely.com">info@armely.com</a>.</p>
				</div>

				<div id="related" class="anchor-offset">
					<h3>Related links</h3>
					<ul>
						<li><strong>Privacy Policy:</strong> <a href="{{ url('/privacy-policy') }}">{{ url('/privacy-policy') }}</a></li>
						<li><strong>Mela AI:</strong> <a href="{{ url('/mela-ai') }}">{{ url('/mela-ai') }}</a></li>
						<li><strong>Contact:</strong> <a href="{{ route('contact') }}">Get in touch</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@push('scripts')
<script>
(function() {
	const toc = document.querySelector('.toc-card');
	if (!toc) return;

	const links = Array.from(toc.querySelectorAll('.toc-list a'));
	const sections = links
		.map(a => ({ id: a.getAttribute('href').replace('#',''), el: null, link: a }))
		.map(item => { item.el = document.getElementById(item.id); return item; })
		.filter(item => !!item.el);

	const setActive = (id) => {
		links.forEach(a => { a.classList.remove('active'); a.removeAttribute('aria-current'); });
		const found = links.find(a => a.getAttribute('href') === `#${id}`);
		if (found) { found.classList.add('active'); found.setAttribute('aria-current', 'true'); }
	};

	links.forEach(link => {
		link.addEventListener('click', (e) => {
			const hash = link.getAttribute('href');
			const target = document.querySelector(hash);
			if (!target) return;
			e.preventDefault();
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			if (history.replaceState) history.replaceState(null, '', hash);
			setActive(target.id);
		});
	});

	const pickCurrent = () => {
		let currentId = null;
		let bestTop = Infinity;
		sections.forEach(({ el }) => {
			const rect = el.getBoundingClientRect();
			if (rect.top >= 90 && rect.top < bestTop) {
				bestTop = rect.top;
				currentId = el.id;
			}
		});
		if (!currentId) {
			let bestAbove = -Infinity;
			sections.forEach(({ el }) => {
				const rect = el.getBoundingClientRect();
				if (rect.top < 90 && rect.top > bestAbove) {
					bestAbove = rect.top;
					currentId = el.id;
				}
			});
		}
		if (currentId) setActive(currentId);
	};

	const onScroll = () => { window.requestAnimationFrame(pickCurrent); };
	document.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll);
	window.addEventListener('load', onScroll);

	if (location.hash) {
		setActive(location.hash.replace('#',''));
	} else {
		pickCurrent();
	}
})();
</script>
@endpush
