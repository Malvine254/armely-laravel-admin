@extends('layouts.public')

@section('title', 'Privacy Policy - Armely')

@push('styles')
<style>
/***** Privacy Policy Styles *****/
:root { --pp-accent: #2f5597; }
.policy-hero { background: linear-gradient(180deg, rgba(47,85,151,.08), transparent); padding: 40px 0 10px; }
.policy-wrapper { display: grid; grid-template-columns: 300px 1fr; gap: 24px; }
@media (max-width: 992px) { .policy-wrapper { grid-template-columns: 1fr; } }

.toc-card { position: sticky; top: 90px; align-self: start; border-radius: 12px; border: 1px solid #e9ecef; background: #fff; box-shadow: 0 6px 18px rgba(0,0,0,.06); }
.toc-card .toc-header { padding: 16px 18px; border-bottom: 1px solid #f0f2f5; display: flex; align-items: center; gap: 10px; }
.toc-card .toc-header i { color: var(--pp-accent); }
.toc-card .toc-list { list-style: none; margin: 0; padding: 8px 0 14px; }
.toc-card .toc-list li { margin: 0; }
.toc-card .toc-list a { display: block; padding: 10px 18px; color: #333; text-decoration: none; border-left: 3px solid transparent; transition: all .2s ease; }
.toc-card .toc-list a:hover { background: #f7f9fc; border-left-color: var(--pp-accent); color: var(--pp-accent); }
.toc-card .toc-list a.active { background: #eef3fb; border-left-color: var(--pp-accent); color: var(--pp-accent); font-weight: 600; }

.policy-card { border-radius: 12px; border: 1px solid #e9ecef; background: #fff; box-shadow: 0 6px 18px rgba(0,0,0,.06); padding: 28px; }
.policy-card h3 { color: var(--pp-accent); margin-top: 28px; margin-bottom: 10px; font-weight: 700; }
.policy-card p { color: #4a5568; line-height: 1.8; }
.policy-card ol { padding-left: 20px; }
.anchor-offset { scroll-margin-top: 100px; }
.badge-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--pp-accent); display: inline-block; }

.section-heading-modern { font-weight: 800; letter-spacing: .3px; }
.title-divider { width: 80px; height: 4px; background: var(--pp-accent); border-radius: 2px; margin: 12px 0 0; }

.action-links { display:flex; gap: 12px; margin-top: 18px; }
</style>
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay policy-hero">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Privacy Policy</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Privacy Policy</li>
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
					<strong>Table of Contents</strong>
				</div>
				<ol class="toc-list">
					<li><a href="#overview">Overview</a></li>
					<li><a href="#information-collection">Information Collection and Use</a></li>
					<li><a href="#log-data">Log Data</a></li>
					<li><a href="#cookies">Cookies</a></li>
					<li><a href="#service-providers">Service Providers</a></li>
					<li><a href="#security">Security</a></li>
					<li><a href="#links">Links to Other Sites</a></li>
					<li><a href="#children">Children’s Privacy</a></li>
					<li><a href="#changes">Changes to This Policy</a></li>
					<li><a href="#contact">Contact Us</a></li>
					<li><a href="#about">Who We Are</a></li>
				</ol>
			</aside>

			<!-- Policy Content -->
			<div class="policy-card">
				<div id="overview" class="anchor-offset">
					<h3 class="section-heading-modern">Our Privacy Policy</h3>
					<p>Armely, LLC operates the <a href="https://armely.com/" target="_blank" rel="noopener">www.armely.com</a> website, which provides the SERVICE.</p>
					<p>This page informs website visitors of our policies regarding the collection, use, and disclosure of Personal Information for anyone using our Service, the Armely website.</p>
					<p>By using our Service, you agree to the collection and use of information in accordance with this policy. The Personal Information we collect is used to provide and improve the Service. We will not use or share your information with anyone except as described in this Privacy Policy.</p>
				</div>

				<div id="information-collection" class="anchor-offset">
					<h3>Information Collection and Use</h3>
					<p>For a better experience, we may require you to provide certain personally identifiable information, including but not limited to your name, phone number, and postal address. The information we collect is used to contact or identify you.</p>
				</div>

				<div id="log-data" class="anchor-offset">
					<h3>Log Data</h3>
					<p>Whenever you visit our Service, we collect information that your browser sends to us called Log Data. This Log Data may include your computer’s Internet Protocol (IP) address, browser version, pages of our Service visited, the time and date of your visit, time spent on pages, and other statistics.</p>
				</div>

				<div id="cookies" class="anchor-offset">
					<h3>Cookies</h3>
					<p>Cookies are files with a small amount of data, commonly used as anonymous unique identifiers. These are sent to your browser from websites and stored on your device. Our website uses cookies to collect information and to improve our Service. You can choose to accept or refuse cookies. If you refuse cookies, some parts of the Service may not function properly. For more about cookies, see <a href="https://web.archive.org/web/20210624212307/https://www.privacypolicyonline.com/what-are-cookies/" target="_blank" rel="noopener">What Are Cookies</a>.</p>
				</div>

				<div id="service-providers" class="anchor-offset">
					<h3>Service Providers</h3>
					<p>We may employ third‑party companies and individuals for the following reasons:</p>
					<ol class="ml-3">
						<li>To facilitate our Service</li>
						<li>To provide the Service on our behalf</li>
						<li>To perform Service‑related services</li>
						<li>To assist us in analyzing how our Service is used</li>
					</ol>
					<p>These third parties have access to your Personal Information only to perform tasks on our behalf and are obligated not to disclose or use it for any other purpose.</p>
				</div>

				<div id="security" class="anchor-offset">
					<h3>Security</h3>
					<p>We value your trust in providing us your Personal Information and strive to use commercially acceptable means to protect it. But remember, no method of transmission over the Internet or method of electronic storage is 100% secure and reliable; we cannot guarantee absolute security.</p>
				</div>

				<div id="links" class="anchor-offset">
					<h3>Links to Other Sites</h3>
					<p>Our Service may contain links to other sites. If you click a third‑party link, you will be directed to that site. These external sites are not operated by us. We strongly advise you to review the Privacy Policy of those websites. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third‑party sites or services.</p>
				</div>

				<div id="children" class="anchor-offset">
					<h3>Children’s Privacy</h3>
					<p>Our Services do not address anyone under the age of 13. We do not knowingly collect personally identifiable information from children under 13. If we discover that a child under 13 has provided personal information, we will promptly delete it. If you are a parent or guardian and are aware that your child has provided us personal information, please contact us so that we can take necessary action.</p>
				</div>

				<div id="changes" class="anchor-offset">
					<h3>Changes to This Privacy Policy</h3>
					<p>We may update our Privacy Policy from time to time. We advise you to review this page periodically for changes. We will notify you by posting the new Privacy Policy on this page. Changes take effect immediately after they are posted.</p>
				</div>

				<div id="contact" class="anchor-offset">
					<h3>Contact Us</h3>
					<p>If you have any questions or suggestions about our Privacy Policy, do not hesitate to <a href="{{ route('contact') }}">contact us</a>.</p>
				</div>

				<div id="about" class="anchor-offset">
					<h3>Who We Are</h3>
					<p>Armely is a data management and portal consulting firm in North Texas with a global reach. We help clients transform and achieve competitive advantage through data, analytics, visualization, and predictions.</p>
				</div>

				<div class="action-links">
					<a href="#overview" class="btn default-button text-light"><i class="icofont-simple-up"></i> Back to top</a>
					<a href="https://armely.com/privacy-policy" target="_blank" rel="noopener" class="btn btn-outline-primary">Canonical Policy</a>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
	const tocLinks = Array.from(document.querySelectorAll('.toc-card .toc-list a'));
	if (!tocLinks.length) return;

	const sections = tocLinks
		.map(a => ({ link: a, id: a.getAttribute('href') }))
		.map(x => ({ link: x.link, el: document.querySelector(x.id) }))
		.filter(x => !!x.el);

	const setActive = (link) => {
		tocLinks.forEach(l => l.classList.remove('active'));
		if (link) link.classList.add('active');
	};

	// Highlight on click immediately
	tocLinks.forEach(l => l.addEventListener('click', () => setActive(l)));

	// Use IntersectionObserver to highlight section in view
	const observer = new IntersectionObserver((entries) => {
		const visible = entries
			.filter(e => e.isIntersecting)
			.sort((a, b) => b.intersectionRatio - a.intersectionRatio);

		if (visible.length) {
			const top = visible[0];
			const active = sections.find(s => s.el === top.target);
			if (active) setActive(active.link);
		} else {
			// Fallback: pick the closest section to the top accounting for header offset
			const nearest = sections
				.map((s, i) => ({ i, d: Math.abs(s.el.getBoundingClientRect().top - 110) }))
				.sort((a, b) => a.d - b.d)[0];
			if (nearest) setActive(tocLinks[nearest.i]);
		}
	}, {
		// Adjust for sticky header; favor the section near top
		root: null,
		rootMargin: '-110px 0px -60% 0px',
		threshold: [0.1, 0.25, 0.5, 0.75]
	});

	sections.forEach(s => observer.observe(s.el));

	// Initialize state
	setActive(tocLinks[0]);
});
</script>
@endpush

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

	// Highlight on click (with smooth scroll)
	links.forEach(link => {
		link.addEventListener('click', (e) => {
			const hash = link.getAttribute('href');
			const target = document.querySelector(hash);
			if (!target) return;
			e.preventDefault();
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			// Update URL hash without pushing history
			if (history.replaceState) history.replaceState(null, '', hash);
			setActive(target.id);
		});
	});

	// Highlight on scroll via IntersectionObserver
	const pickCurrent = () => {
		// Prefer the nearest section below top; else the last one above top
		let currentId = null;
		let bestTop = Infinity;
		sections.forEach(({ el }) => {
			const rect = el.getBoundingClientRect();
			if (rect.top >= 90 && rect.top < bestTop) { // header/hero offset
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

	// If page loads with a hash, set active accordingly
	if (location.hash) {
		const id = location.hash.replace('#','');
		setActive(id);
	} else {
		pickCurrent();
	}
})();
</script>
@endpush
