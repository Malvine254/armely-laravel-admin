@extends('layouts.public')

@section('title', isset($main) ? $main->title . ' - Armely Blog' : 'Our Insights & Stories - Armely Blog')

@push('head')
	<!-- Blog Page Styles -->
	<link rel="stylesheet" href="{{ asset('css/blog-modern.css') }}">
	
@if(isset($main))
	<!-- Open Graph Meta Tags for Social Media Sharing -->
	<meta property="og:type" content="article">
	<meta property="og:title" content="{{ $main->title }}">
	<meta property="og:description" content="{{ Str::limit(strip_tags($main->body), 160) }}">
	<meta property="og:url" content="{{ request()->url() }}">
	@if($main->image_path)
		<meta property="og:image" content="{{ url($main->image_path) }}">
		<meta property="og:image:secure_url" content="{{ url($main->image_path) }}">
		<meta property="og:image:width" content="1200">
		<meta property="og:image:height" content="630">
	@endif
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="{{ $main->title }}">
	<meta name="twitter:description" content="{{ Str::limit(strip_tags($main->body), 160) }}">
	@if($main->image_path)
		<meta name="twitter:image" content="{{ url($main->image_path) }}">
	@endif
@endif
@endpush

@section('content')

<!-- Breadcrumbs -->
<div class="breadcrumbs overlay blog-breadcrumbs">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Our Insights & Stories</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Blog</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<!-- Blog Listing Section -->
<section class="blog-listing-section">
	<div class="container">
		@if(!empty($dbErrorMessage))
			<div class="row mb-3">
				<div class="col-12">
					<div class="alert alert-warning text-center" role="alert">
						<i class="icofont-warning-alt"></i> {{ $dbErrorMessage }}
					</div>
				</div>
			</div>
		@endif
		<div class="row">
			<div class="col-lg-8 col-12">
				<div id="blog-main-content">
					@if($main)
						<div class="modern-blog-card">
							<!-- Blog Image -->
							<div class="blog-image-wrapper {{ !$main->image_path ? 'no-image' : '' }}">
								@if($main->image_path)
									<img src="{{ asset($main->image_path) }}" alt="{{ $main->title }}">
								@else
									<div class="default-blog-gradient">
										<div class="gradient-icon">
											<i class="fa fa-newspaper"></i>
										</div>
									</div>
								@endif
							</div>
							
							<!-- Blog Content -->
							<div class="blog-content-wrapper">
								<!-- Blog Title -->
								<h1 class="blog-title"><a href="#">{{ $main->title }}</a></h1>
								
								<!-- Blog Meta -->
								<div class="blog-meta">
									<div class="blog-meta-item">
										@if(isset($main->author_image) && $main->author_image)
											<img src="{{ asset('images/team/' . $main->author_image) }}" alt="Author" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
										@else
											<img src="{{ asset('images/blog/profile.svg') }}" alt="Author">
										@endif
										<span class="blog-author">{{ $main->author ?? 'Armely Team' }}</span>
									</div>
									<div class="blog-meta-item">
										<i class="fa fa-calendar"></i>
										<span>{{ $main->date }}</span>
									</div>
									<div class="blog-meta-item">
										<i class="fa fa-eye"></i>
										<span id="views-count">{{ $main->clicks ?? 0 }}</span> Views
									</div>
									<div class="blog-meta-item">
										<button id="toggleSpeech" class="read-aloud-btn">
											<i class="fa fa-volume-high text-light" id="volume-icons"></i>
											<span>Read Aloud</span>
										</button>
									</div>
								</div>
								
								<!-- Blog Text -->
								<div class="blog-text-content" id="blog-content">
									{!! $main->body !!}
								</div>

								<!-- Blog Footer -->
								<div class="blog-footer-actions">
									<!-- Social Share -->
									<ul class="blog-social-share">
										<li class="facebook"><a class="shareBtn text-light" data-social="facebook"><i class="fa-brands fa-facebook"></i><span>Share</span></a></li>
										<li class="twitter"><a class="shareBtn text-light" data-social="twitter"><i class="fa-brands fa-x-twitter"></i><span>Tweet</span></a></li>
										<li class="instagram"><a class="shareBtn text-light" data-social="instagram"><i class="fa-brands fa-instagram"></i><span>Post</span></a></li>
										<li class="linkedin"><a class="shareBtn text-light" data-social="linkedin"><i class="fa-brands fa-linkedin"></i><span>Share</span></a></li>
									</ul>
									
									<!-- Scroll More Button -->
									<button id="show-more" class="scroll-more-btn show-more">
										Scroll to Read More <i class="icofont-long-arrow-down"></i>
									</button>
								</div>
							</div>
						</div>
					@else
						<div class="modern-blog-card">
							<div class="blog-content-wrapper text-center">
								<p class="text-muted">Blog post not found.</p>
							</div>
						</div>
					@endif
				</div>
			</div>
			
			<div class="col-lg-4 col-12">
				<div class="blog-sidebar">
					<!-- Search Widget -->
					<div class="sidebar-widget">
						<h3 class="sidebar-widget-title">Search Articles</h3>
						<div class="blog-search-form">
							<input id="searchBar" type="text" class="blog-search-input" placeholder="Search for articles...">
							<button class="blog-search-btn default-background"><i class="fa fa-search"></i></button>
						</div>
					</div>
					
					<!-- Recent Posts Widget -->
					<div class="sidebar-widget">
						<h3 class="sidebar-widget-title">Recent Posts</h3>
						<p style="display: none;" class="no-results-alert" id="noResults">No results found!</p>
						<div class="recent-posts-list">
							@forelse($recent as $blog)
								<a href="{{ route('blog.index', ['blogId' => $blog->blog_id]) }}" class="sidebar-blog-card data-item">
									<div class="sidebar-blog-image {{ !$blog->image_path ? 'no-image' : '' }}">
										@if($blog->image_path)
											<img src="{{ asset($blog->image_path) }}" alt="{{ $blog->title }}">
										@else
											<div class="default-blog-gradient">
												<i class="fa fa-newspaper"></i>
											</div>
										@endif
									</div>
									<div class="sidebar-blog-content">
										<h5 class="sidebar-blog-title">{{ $blog->title }}</h5>
										<div class="sidebar-blog-meta">
											<span><i class="fa fa-calendar"></i>{{ $blog->date }}</span>
											<span><i class="fa fa-eye"></i>{{ $blog->clicks ?? 0 }} Views</span>
										</div>
									</div>
								</a>
							@empty
								<p class="text-center text-muted">No blog posts found.</p>
							@endforelse
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--/ End Blog Listing Section -->

@push('scripts')
<script>
// Helper function to set and get cookies
function setCookie(name, value, days = 30) {
	const date = new Date();
	date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	const expires = "expires=" + date.toUTCString();
	document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
	const nameEQ = name + "=";
	const cookies = document.cookie.split(';');
	for (let i = 0; i < cookies.length; i++) {
		const cookie = cookies[i].trim();
		if (cookie.indexOf(nameEQ) === 0) {
			return true;
		}
	}
	return false;
}

document.addEventListener('DOMContentLoaded', function() {
	const searchBar = document.getElementById('searchBar');
	const noResults = document.getElementById('noResults');
	const blogCards = document.querySelectorAll('.sidebar-blog-card');
	
	// Search functionality
	if (searchBar) {
		searchBar.addEventListener('keyup', function() {
			const searchTerm = this.value.toLowerCase().trim();
			let visibleCount = 0;
			
			blogCards.forEach(function(card) {
				const title = card.querySelector('.sidebar-blog-title').textContent.toLowerCase();
				if (title.includes(searchTerm)) {
					card.style.display = 'flex';
					visibleCount++;
				} else {
					card.style.display = 'none';
				}
			});
			
			if (visibleCount === 0) {
				noResults.style.display = 'block';
			} else {
				noResults.style.display = 'none';
			}
		});
	}
	
	// Seamless blog loading without page reload
	blogCards.forEach(function(card) {
		card.addEventListener('click', function(e) {
			e.preventDefault();
			
			const blogId = this.getAttribute('href').split('/').pop();
			const mainContent = document.getElementById('blog-main-content');
			
			// Add loading state
			mainContent.style.opacity = '0.5';
			mainContent.style.pointerEvents = 'none';
			
			// Fetch new blog content
			fetch(`{{ url('/blog') }}/${blogId}`, {
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(response => response.text())
			.then(html => {
				// Parse the HTML response
				const parser = new DOMParser();
				const doc = parser.parseFromString(html, 'text/html');
				const newContent = doc.getElementById('blog-main-content');
				
				if (newContent) {
					// Update content with smooth transition
					mainContent.style.opacity = '0';
					
					setTimeout(() => {
						mainContent.innerHTML = newContent.innerHTML;
						mainContent.style.opacity = '1';
						mainContent.style.pointerEvents = 'auto';
						
						// Update URL without reload
						window.history.pushState({blogId: blogId}, '', `{{ url('/blog') }}/${blogId}`);
						
						// Scroll to top smoothly
						window.scrollTo({top: 0, behavior: 'smooth'});
						
						// Update sidebar recent posts to show current views
						updateRecentPostsViews();
						
						// Reinitialize event listeners for new content
						reinitializeBlogFeatures();
					}, 300);
				}
			})
			.catch(error => {
				console.error('Error loading blog:', error);
				mainContent.style.opacity = '1';
				mainContent.style.pointerEvents = 'auto';
			});
		});
	});
	
	// Handle browser back/forward buttons
	window.addEventListener('popstate', function(e) {
		if (e.state && e.state.blogId) {
			location.reload();
		}
	});
	
	// Function to reinitialize features after content update
	function reinitializeBlogFeatures() {
		// Diagnostic helper to log container/button metrics for debugging autoscroll/show-more
		function _logBlogDiagnostics(context, contentEl, span) {
			try {
				const out = { context };
				if (contentEl) {
					const cs = window.getComputedStyle(contentEl);
					out.container = {
						scrollHeight: contentEl.scrollHeight,
						clientHeight: contentEl.clientHeight,
						overflowY: cs.overflowY,
						display: cs.display,
						transform: cs.transform === 'none' ? null : cs.transform,
						hasScrollable: contentEl.scrollHeight > contentEl.clientHeight
					};
				}
				if (span) {
					const sr = span.getBoundingClientRect();
					out.span = { text: span.textContent.slice(0,40), top: sr.top, bottom: sr.bottom, height: sr.height };
				}
				console.debug('BLOG-DIAG', out);
			} catch (e) { console.debug('BLOG-DIAG-ERR', e); }
		}
		// -------------------------
		// Read Aloud (Speech) — robust per-word highlighting
		// -------------------------
		const toggleSpeech = document.getElementById('toggleSpeech');
		const volumeIcon = document.getElementById('volume-icons');

		const synth = window.speechSynthesis;
		let speaking = false;
		let stopRequested = false;

		// Wrap words in the content area with spans so we can highlight per-word reliably
		function wrapWords(container) {
			if (!container) return [];
			if (container.dataset.wordsWrapped === '1') {
				return Array.from(container.querySelectorAll('span._s_word'));
			}

			let walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null, false);
			let node;
			let index = 0;
			const wordSpans = [];

			// Match Unicode words (letters/numbers) and include internal apostrophes or hyphens
			const wordRegex = /[\p{L}\p{N}]+(?:[\u0027\u2019\u2010-\u2015][\p{L}\p{N}]+)*/gu;

			while (node = walker.nextNode()) {
				// Normalize text node: replace non-breaking spaces and remove zero-width chars
				if (node.nodeValue) {
					node.nodeValue = node.nodeValue.replace(/\u00A0/g, ' ').replace(/[\u200B-\u200D\uFEFF]/g, '');
				}
				const text = node.nodeValue;
				if (!text || !text.trim()) continue;

				// skip text nodes that are inside excluded or invisible elements
				const parentEl = node.parentElement;
				if (parentEl) {
					// skip code blocks, preformatted text, scripts, styles, noscript
					if (parentEl.closest && parentEl.closest('script, style, code, pre, noscript')) continue;
					// skip elements explicitly hidden
					if (parentEl.closest && parentEl.closest('[aria-hidden="true"]')) continue;
					// skip if not rendered (no client rects)
					try {
						if (parentEl.getClientRects && parentEl.getClientRects().length === 0) continue;
					} catch (e) {
						// ignore errors from getClientRects in some environments
					}
				}

				const frag = document.createDocumentFragment();
				let lastIndex = 0;
				let match;

				wordRegex.lastIndex = 0;
				while ((match = wordRegex.exec(text)) !== null) {
					const word = match[0];
					const start = match.index;
					// append text before the match
					if (start > lastIndex) {
						frag.appendChild(document.createTextNode(text.slice(lastIndex, start)));
					}
					// create span for matched word
					const span = document.createElement('span');
					span.className = '_s_word';
					span.dataset.wordIndex = index++;
					span.textContent = word;
					frag.appendChild(span);
					wordSpans.push(span);
					lastIndex = wordRegex.lastIndex;
				}

				// append remaining text
				if (lastIndex < text.length) {
					frag.appendChild(document.createTextNode(text.slice(lastIndex)));
				}

				// replace original text node
				node.parentNode.replaceChild(frag, node);
			}

			container.dataset.wordsWrapped = '1';
			return wordSpans;
		}

		function clearHighlights(container) {
			container.querySelectorAll('span._s_word._speaking').forEach(s => s.classList.remove('_speaking'));
		}

		function stopSpeaking() {
			stopRequested = true;
			speaking = false;
			if (synth && synth.speaking) synth.cancel();
			const content = document.querySelector('.blog-text-content');
			if (content) clearHighlights(content);
			if (volumeIcon) volumeIcon.className = 'fa fa-volume-high text-light';
		}

		// Speak words sequentially (per-word utterances) — fallback will work across browsers
		async function speakPerWord(wordSpans, startIndex = 0) {
			if (!wordSpans || !wordSpans.length) return;
			stopRequested = false;
			speaking = true;
			if (volumeIcon) volumeIcon.className = 'fa fa-pause text-light';
			for (let i = startIndex; i < wordSpans.length; i++) {
				if (stopRequested) break;
				const span = wordSpans[i];
				const text = span.textContent.trim();
				if (!text) continue;

				// highlight current word
				const contentContainer = span.closest('.blog-text-content');
				clearHighlights(contentContainer || document);
				span.classList.add('_speaking');

				// Auto-scroll highlighted word into view within its scroll container or window
				_logBlogDiagnostics('before-scroll', contentContainer, span);
				try {
					if (contentContainer && contentContainer.scrollHeight > contentContainer.clientHeight) {
						const spanRect = span.getBoundingClientRect();
						const containerRect = contentContainer.getBoundingClientRect();
						console.debug('BLOG-DIAG scrollRects', { spanRect, containerRect, scrollTop: contentContainer.scrollTop });
						const padding = 18;
						const topDelta = spanRect.top - containerRect.top;
						const bottomDelta = spanRect.bottom - containerRect.bottom;

						// compute desired newTop (relative to contentContainer.scrollTop)
						let newTop = contentContainer.scrollTop;
						if (bottomDelta > -padding) {
							const delta = spanRect.bottom - containerRect.bottom + padding;
							newTop = Math.max(0, contentContainer.scrollTop + delta);
						} else if (topDelta < padding) {
							const delta = topDelta - padding;
							newTop = Math.max(0, contentContainer.scrollTop + delta);
						}

						// apply scroll and verify it changed — fallback to scrollIntoView if not
						const prevTop = contentContainer.scrollTop;
						try {
							// prefer smooth scroll when available
							if (typeof contentContainer.scrollTo === 'function') {
								contentContainer.scrollTo({ top: newTop, behavior: 'smooth' });
							} else {
								contentContainer.scrollTop = newTop;
							}
						} catch (e) {
							contentContainer.scrollTop = newTop;
						}

						// If the scroll didn't change (some browsers disallow element scroll), fallback to scrollIntoView
						setTimeout(() => {
							try {
								_logBlogDiagnostics('post-scroll-check', contentContainer, span);
								if (Math.abs((contentContainer.scrollTop || 0) - prevTop) < 1) {
									// minimal fallback: scroll the specific span into view inside its scrollable ancestor
									if (typeof span.scrollIntoView === 'function') {
										span.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
									}
								}
							} catch (e) { /* ignore */ }
						}, 50);
					} else {
						// If the article container isn't scrollable, only then scroll the window
						_logBlogDiagnostics('window-scroll-fallback', contentContainer, span);
						const header = document.querySelector('.header');
						const headerOffset = header ? header.offsetHeight : 80;
						const spanRect = span.getBoundingClientRect();
						const absoluteTop = window.pageYOffset + spanRect.top - headerOffset - 20;
						// only scroll window when the span is outside viewport
						if (spanRect.top < 0 || spanRect.bottom > (window.innerHeight || document.documentElement.clientHeight)) {
							window.scrollTo({ top: absoluteTop, behavior: 'smooth' });
						}
					}
				} catch (e) {
					// ignore scrolling errors
				}
				// create utterance for the single word
				const utter = new SpeechSynthesisUtterance(text);
				utter.lang = navigator.language || 'en-US';
				utter.rate = 1.0;

				await new Promise((resolve) => {
					utter.onend = function() { resolve(); };
					utter.onerror = function() { resolve(); };
					synth.speak(utter);
				});
				// small pause between words to allow highlighting to be noticeable
				await new Promise(r => setTimeout(r, 25));
			}

			stopRequested = false;
			speaking = false;
			if (volumeIcon) volumeIcon.className = 'fa fa-volume-high text-light';
			const content = document.querySelector('.blog-text-content');
			if (content) clearHighlights(content);
		}

		if (toggleSpeech) {
			const newToggle = toggleSpeech.cloneNode(true);
			toggleSpeech.parentNode.replaceChild(newToggle, toggleSpeech);

			newToggle.addEventListener('click', async function() {
				const contentEl = document.querySelector('.blog-text-content');
				if (!contentEl) return;

				// If already speaking, stop
				if (speaking) { stopSpeaking(); return; }

				// Ensure reading starts from the very top (first paragraph visible)
				// Reset scroll position of the article container only
				try {
					contentEl.scrollTop = 0;
				} catch (e) {}

				// Wrap words and get spans
				const wordSpans = wrapWords(contentEl);
				if (!wordSpans.length) return;

				// Allow DOM to settle after wrapping before speaking
				await new Promise(r => requestAnimationFrame(() => r()));

				// Ensure first paragraph/first word is visible before speaking
				if (wordSpans[0]) {
					const first = wordSpans[0];
					// minimal scroll to ensure it's visible with small padding
					const padding = 12;
					try {
						if (first.offsetTop < contentEl.scrollTop + padding) {
							contentEl.scrollTop = Math.max(0, first.offsetTop - padding);
						}
					} catch (e) {
						// ignore
					}
				}

				// Find first visible word span to start from (skip initial punctuation/hidden spans)
				const firstVisibleIndex = (function() {
					for (let i = 0; i < wordSpans.length; i++) {
						const s = wordSpans[i];
						try {
							const rects = s.getClientRects();
							if (rects && rects.length > 0) {
								// ensure it's inside the article container's visible area
								const sr = rects[0];
								const containerRect = contentEl.getBoundingClientRect();
								if (sr.bottom > containerRect.top + 4 && sr.top < containerRect.bottom - 4) {
									return i;
								}
							}
						} catch (e) {}
					}
					return 0;
				})();

				// Speak per-word starting at the first visible span
				await speakPerWord(wordSpans, firstVisibleIndex);
			});
		}

		// Stop any speaking when navigating away or loading new content
		document.addEventListener('visibilitychange', function() {
			if (document.hidden) stopSpeaking();
		});

		// -------------------------
		// Sharing & Clipboard
		// -------------------------
		// Utility: copy to clipboard
		function copyToClipboard(text) {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				return navigator.clipboard.writeText(text).catch(() => {
					const ta = document.createElement('textarea');
					ta.value = text;
					document.body.appendChild(ta);
					ta.select();
					document.execCommand('copy');
					document.body.removeChild(ta);
				});
			} else {
				const ta = document.createElement('textarea');
				ta.value = text;
				document.body.appendChild(ta);
				ta.select();
				document.execCommand('copy');
				document.body.removeChild(ta);
				return Promise.resolve();
			}
		}

		// Reinitialize social share buttons with Instagram fallback and Web Share support
		const shareBtns = document.querySelectorAll('.shareBtn');
		shareBtns.forEach(function(btn) {
			// avoid duplicate listeners by cloning
			const cloned = btn.cloneNode(true);
			btn.parentNode.replaceChild(cloned, btn);
			cloned.addEventListener('click', function() {
				const social = this.getAttribute('data-social');
				const url = window.location.href;
				const title = document.querySelector('.blog-title a') ? document.querySelector('.blog-title a').textContent : document.title;

				switch(social) {
					case 'facebook': {
						const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
						window.open(shareUrl, '_blank', 'width=600,height=400');
						break;
					}
					case 'twitter': {
						const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
						window.open(shareUrl, '_blank', 'width=600,height=400');
						break;
					}
					case 'linkedin': {
						const shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
						window.open(shareUrl, '_blank', 'width=600,height=400');
						break;
					}
					case 'instagram': {
						if (navigator.share) {
							navigator.share({ title: title, text: title, url: url }).catch(() => {
								copyToClipboard(url).then(()=> alert('Link copied to clipboard. Open Instagram and paste the link.'));
							});
						} else {
							copyToClipboard(url).then(()=> alert('Link copied to clipboard. Open Instagram and paste the link.'));
						}
						break;
					}
				}
			});
		});

		// Reinitialize scroll more button (account for fixed header offset)
		const showMore = document.getElementById('show-more');
		if (showMore) {
			const newShow = showMore.cloneNode(true);
			showMore.parentNode.replaceChild(newShow, showMore);
			newShow.addEventListener('click', function() {
				const content = document.querySelector('.blog-text-content');
				if (content) {
					const headerOffset = document.querySelector('.header') ? document.querySelector('.header').offsetHeight : 80;
					const elementPosition = content.getBoundingClientRect().top + window.pageYOffset;
					const offsetPosition = elementPosition - headerOffset - 20;
					window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
				}
			});
		}
	}
	
	// Function to update recent posts views
	function updateRecentPostsViews() {
		// Get the current blog ID from URL
		const currentUrl = window.location.pathname;
		const blogId = currentUrl.split('/').pop();
		
		// Fetch fresh data to get updated views count
		fetch(`{{ url('/blog') }}/${blogId}`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.text())
		.then(html => {
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const sidebar = doc.querySelector('.recent-posts-list');
			
			if (sidebar) {
				// Update the entire sidebar with fresh content
				const currentSidebar = document.querySelector('.recent-posts-list');
				if (currentSidebar) {
					currentSidebar.innerHTML = sidebar.innerHTML;
					// Re-attach click listeners to new sidebar items
					reattachSidebarListeners();
				}
			}
		})
		.catch(error => console.error('Error updating recent posts:', error));
	}
	
	// Function to reattach event listeners to sidebar blog cards
	function reattachSidebarListeners() {
		const blogCards = document.querySelectorAll('.sidebar-blog-card');
		blogCards.forEach(function(card) {
			// Remove old listeners by cloning
			const newCard = card.cloneNode(true);
			card.parentNode.replaceChild(newCard, card);
			
			// Add click listener
			newCard.addEventListener('click', function(e) {
				e.preventDefault();
				const blogId = this.getAttribute('href').split('/').pop();
				loadBlogContent(blogId);
			});
		});
	}

	// Update head meta tags and title from a fetched document so client-side previews/tools see the latest values
	function updateHeadMetaFromDoc(doc) {
		try {
			const keys = ['og:title','og:description','og:image','og:url','twitter:card','twitter:title','twitter:description','twitter:image'];
			if (doc.title) document.title = doc.title;
			keys.forEach(k => {
				const selector = `meta[property="${k}"], meta[name="${k}"]`;
				const newMeta = doc.head.querySelector(selector);
				if (newMeta) {
					// remove existing meta(s)
					document.head.querySelectorAll(selector).forEach(n => n.parentNode.removeChild(n));
					// append clone
					document.head.appendChild(newMeta.cloneNode(true));
				}
			});
		} catch (e) {
			console.warn('Failed to update meta tags:', e);
		}
	}
	
	// Extract blog loading logic into separate function
	function loadBlogContent(blogId) {
		const mainContent = document.getElementById('blog-main-content');
		mainContent.style.opacity = '0.5';
		mainContent.style.pointerEvents = 'none';
		
		// Check if user has already viewed this blog
		const cookieName = 'blog_viewed_' + blogId;
		const hasViewed = getCookie(cookieName);
		
		fetch(`{{ url('/blog') }}/${blogId}`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.text())
		.then(html => {
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const newContent = doc.getElementById('blog-main-content');
			
			if (newContent) {
				// Update head meta/title so client-side state reflects the loaded article
				updateHeadMetaFromDoc(doc);
				mainContent.style.opacity = '0';
				
				setTimeout(() => {
					mainContent.innerHTML = newContent.innerHTML;
					mainContent.style.opacity = '1';
					mainContent.style.pointerEvents = 'auto';
					
					window.history.pushState({blogId: blogId}, '', `{{ url('/blog') }}/${blogId}`);
					window.scrollTo({top: 0, behavior: 'smooth'});
					
					// Set cookie to mark this blog as viewed (if not already viewed)
					if (!hasViewed) {
						setCookie(cookieName, 'true', 30);
					}
					
					updateRecentPostsViews();
					reinitializeBlogFeatures();
				}, 300);
			}
		})
		.catch(error => {
			console.error('Error loading blog:', error);
			mainContent.style.opacity = '1';
			mainContent.style.pointerEvents = 'auto';
		});
	}
	
	// Initialize features on page load
	reinitializeBlogFeatures();
});
</script>
@endpush

@endsection
