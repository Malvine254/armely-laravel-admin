@php
use Illuminate\Support\Str;

if (!function_exists('armely_blog_clean_html')) {
	function armely_blog_clean_html(?string $html): string
	{
		if (!is_string($html) || trim($html) === '') {
			return '';
		}

		$html = \App\Support\BlogMedia::normalizeHtml($html);
		$clean = preg_replace('/<(script|style|link|meta)\b[^>]*>.*?<\/\1>/is', '', $html) ?? $html;
		$clean = preg_replace('/<(script|style|link|meta)\b[^>]*\/?>/is', '', $clean) ?? $clean;
		$clean = preg_replace('/\sstyle\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $clean) ?? $clean;
		$clean = preg_replace('/\s(class|id|align|bgcolor|border|cellpadding|cellspacing|color|face|size|width|height)\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $clean) ?? $clean;
		$clean = preg_replace('/<\/?font\b[^>]*>/is', '', $clean) ?? $clean;

		return $clean;
	}
}
@endphp
@extends('layouts.public')

@php
    $hasMain = !empty($main);
    $requestedBlogId = request()->route('blog') ?? request()->query('blogId');
    $hasRequestedBlog = $requestedBlogId !== null && (string) $requestedBlogId !== '';
    $mainTitle = $hasMain ? trim((string) ($main->title ?? '')) : '';
    $mainBodyText = $hasMain ? trim(preg_replace('/\s+/', ' ', strip_tags((string) ($main->body ?? '')))) : '';
    $mainImageUrl = $hasMain ? \App\Support\BlogMedia::publicUrl($main->image_path ?? null) : '';

    $seoTitle = $hasRequestedBlog && $mainTitle !== ''
        ? $mainTitle . ' | Armely Blog'
        : 'Blogs | Armely Insights on Data, AI, and Microsoft Technologies';

    $seoDescription = $hasRequestedBlog && $mainBodyText !== ''
        ? Str::limit($mainBodyText, 158)
        : 'Read Armely blog insights on Microsoft Fabric, Power BI, Copilot, Power Platform, and enterprise transformation strategy.';

    $seoKeywords = $hasRequestedBlog && $mainTitle !== ''
        ? 'Armely blog, ' . Str::lower($mainTitle) . ', Microsoft Fabric, Power BI, Copilot, enterprise AI'
        : 'Armely blog, Microsoft Fabric, Power BI, Copilot, Power Platform, enterprise AI, data modernization';

    $canonicalUrl = ($hasRequestedBlog && $hasMain)
        ? \App\Support\BlogUrl::url($main)
        : route('blog.index');

    $shareImage = ($hasRequestedBlog && !empty($main->image_path))
        ? asset($main->image_path)
        : asset('images/logo/logo1.png');
@endphp

@section('title', $seoTitle)
@section('meta_description', $seoDescription)
@section('meta_keywords', $seoKeywords)
@section('canonical_url', $canonicalUrl)
@section('og_title', $seoTitle)
@section('og_description', $seoDescription)
@section('twitter_title', $seoTitle)
@section('twitter_description', $seoDescription)
@section('meta_image', $shareImage)
@section('robots', 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1')
@section('og_type', $hasRequestedBlog ? 'article' : 'website')

@push('head')
	@if($hasRequestedBlog && !empty($main))
		@php
			$articlePublishedAt = $main->updated_at ?? $main->date ?? $main->blog_date ?? $main->created_at ?? null;
		@endphp
		<meta property="og:image:alt" content="{{ $mainTitle !== '' ? $mainTitle : 'Armely blog article image' }}">
		@if(!empty($articlePublishedAt))
			<meta property="article:published_time" content="{{ \Carbon\Carbon::parse($articlePublishedAt)->toIso8601String() }}">
		@endif
		<meta property="article:author" content="{{ $main->author ?? 'Armely Team' }}">
		<meta property="article:section" content="Insights">
	@endif

	<!-- Blog Page Styles -->
	<link rel="stylesheet" href="{{ asset('css/blog-modern.css') }}?v={{ file_exists(public_path('css/blog-modern.css')) ? filemtime(public_path('css/blog-modern.css')) : '' }}">
@endpush

@section('content')



<!-- Blog Listing Section -->
<section class="blog-listing-section blog-modern-page">
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
		<div class="row blog-scroll-row">
			<div class="col-lg-8 col-12">
				<div id="blog-main-content">
					@if($main)
						<div class="modern-blog-card">
							<!-- Blog Image -->
							<div class="blog-image-wrapper {{ !$mainImageUrl ? 'no-image' : '' }}" style="{{ $mainImageUrl ? '--blog-feature-image: url(' . e($mainImageUrl) . ');' : '' }}">
								@if($mainImageUrl)
									<img src="{{ $mainImageUrl }}" alt="{{ $main->title }}">
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
											<img class="default-color"  src="{{ asset('images/team/' . $main->author_image) }}" alt="Author" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
										@else
											<img class="default-color" src="{{ asset('images/blog/profile.svg') }}" alt="Author">
										@endif
										<span class="blog-author default-color">{{ $main->author ?? 'Armely Team' }}</span>
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
											<span class="sr-only">Toggle article narration</span>
											<span class="read-aloud-icon"><i class="fa fa-volume-high" id="volume-icons"></i></span>
											<span>Read Aloud</span>
										</button>
									</div>
									<div class="blog-meta-item">
										<button id="btn-download-pdf" class="read-aloud-btn" data-blog-id="{{ $main->blog_id ?? '' }}" data-blog-title="{{ $main->title ?? 'Article' }}">
											<i class="fa fa-file-pdf"></i>
											<span>Download PDF</span>
										</button>
									</div>
								</div>
								
								<!-- Blog Text -->
								<div class="blog-text-content" id="blog-content">
									{!! armely_blog_clean_html($main->body ?? '') !!}
								</div>

								@php($blogTopicText = strtolower(($main->title ?? '') . ' ' . strip_tags($main->body ?? '')))
								@if(str_contains($blogTopicText, 'fabric') || str_contains($blogTopicText, 'power bi') || str_contains($blogTopicText, 'copilot') || str_contains($blogTopicText, 'ai') || str_contains($blogTopicText, 'foundry') || str_contains($blogTopicText, 'power platform'))
									<div class="blog-related-resource" style="border:1px solid #dfe8f7; background:#f8fbff; padding:20px; margin:28px 0;">
										<h3 style="font-size:1.15rem; color:#1e3357; font-weight:800; margin-bottom:8px;">Related Armely Results</h3>
										<p style="color:#536176; margin-bottom:14px;">See how these Microsoft platform ideas translate into client outcomes across data, AI, automation, and collaboration work.</p>
										<a class="read-aloud-btn" href="{{ route('case-studies.index') }}">Explore case studies</a>
										@if(str_contains($blogTopicText, 'copilot'))
											<a class="read-aloud-btn" href="{{ route('resources.show', 'microsoft-copilot-commercial-enterprise') }}">Read Copilot guidance</a>
										@endif
										@if(str_contains($blogTopicText, 'fabric') || str_contains($blogTopicText, 'power bi'))
											<a class="read-aloud-btn" href="{{ route('resources.show', 'microsoft-fabric-case-study-agricultural-operations') }}">View Fabric resource</a>
										@endif
									</div>
								@endif

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
			
			<div class="col-lg-4 col-12 blog-sidebar-column" id="blog-sidebar-column">
				<div class="blog-sidebar" id="blog-sidebar">
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
								<a href="{{ \App\Support\BlogUrl::url($blog) }}" class="sidebar-blog-card data-item">
									@php($blogImageUrl = \App\Support\BlogMedia::publicUrl($blog->image_path ?? null))
									<div class="sidebar-blog-image {{ !$blogImageUrl ? 'no-image' : '' }}" style="{{ $blogImageUrl ? '--blog-feature-image: url(' . e($blogImageUrl) . ');' : '' }}">
										@if($blogImageUrl)
											<img src="{{ $blogImageUrl }}" alt="{{ $blog->title }}">
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
			
			const blogUrl = this.href;
			const mainContent = document.getElementById('blog-main-content');
			
			// Add loading state
			mainContent.style.opacity = '0.5';
			mainContent.style.pointerEvents = 'none';
			
			// Fetch new blog content
			fetch(blogUrl, {
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
						window.history.pushState({blogUrl: blogUrl}, '', blogUrl);
						
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
		location.reload();
	});

	function applyForcedBlogBodyStyles() {
		const content = document.getElementById('blog-content') || document.querySelector('.blog-text-content');
		if (!content) return;

		content.classList.add('blog-system-content-forced');

		const resetSelector = 'p,span,div,h1,h2,h3,h4,h5,h6,ul,ol,li,a,strong,em,b,i,font,blockquote,table,thead,tbody,tfoot,tr,th,td,colgroup,col,figure,figcaption,pre,code';
		content.querySelectorAll(resetSelector).forEach(function(el) {
			if (el.hasAttribute('style')) {
				el.removeAttribute('style');
			}
			['align', 'bgcolor', 'border', 'cellpadding', 'cellspacing', 'color', 'face', 'height', 'size', 'valign', 'width'].forEach(function(attr) {
				el.removeAttribute(attr);
			});
		});

		content.querySelectorAll('font').forEach(function(el) {
			el.removeAttribute('color');
			el.removeAttribute('face');
			el.removeAttribute('size');
		});

		content.querySelectorAll('img').forEach(function(img) {
			img.removeAttribute('width');
			img.removeAttribute('height');
			img.setAttribute('loading', 'lazy');
			img.setAttribute('decoding', 'async');
		});

		content.querySelectorAll('table').forEach(function(table) {
			table.classList.add('blog-content-table');
			table.querySelectorAll('colgroup, col').forEach(function(colEl) {
				colEl.remove();
			});
			if (!table.parentElement || !table.parentElement.classList.contains('blog-table-wrap')) {
				const wrap = document.createElement('div');
				wrap.className = 'blog-table-wrap';
				table.parentNode.insertBefore(wrap, table);
				wrap.appendChild(table);
			}
		});

		content.querySelectorAll('iframe, video, embed, object').forEach(function(media) {
			media.classList.add('blog-rich-media');
		});

		// Custom list normalization to ensure ordered/unordered markers are always visible.
		content.querySelectorAll('ul, ol').forEach(function(listEl) {
			listEl.classList.add('blog-list');
			listEl.classList.add(listEl.tagName.toLowerCase() === 'ol' ? 'blog-list-ol' : 'blog-list-ul');

			Array.from(listEl.childNodes).forEach(function(node) {
				if (node.nodeType === Node.TEXT_NODE && node.textContent && node.textContent.trim()) {
					const li = document.createElement('li');
					li.textContent = node.textContent.trim();
					listEl.replaceChild(li, node);
				} else if (node.nodeType === Node.ELEMENT_NODE && node.tagName !== 'LI') {
					const li = document.createElement('li');
					node.parentNode.insertBefore(li, node);
					li.appendChild(node);
				}
			});

			Array.from(listEl.children).forEach(function(li) {
				if (li.tagName === 'LI') {
					li.classList.add('blog-list-item');
					if (li.hasAttribute('style')) {
						li.removeAttribute('style');
					}
				}
			});
		});
	}
	
	// Function to reinitialize features after content update
	function reinitializeBlogFeatures() {
		applyForcedBlogBodyStyles();
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
		const wordSegmenter = (window.Intl && typeof Intl.Segmenter === 'function')
			? new Intl.Segmenter(navigator.language || 'en', { granularity: 'word' })
			: null;

		function getReadAloudTargets() {
			return [
				document.querySelector('.blog-title'),
				document.querySelector('.blog-text-content'),
			].filter(Boolean);
		}

		function getWordSegments(text) {
			if (!text) return [];

			if (wordSegmenter) {
				const segments = [];
				for (const part of wordSegmenter.segment(text)) {
					if (part.isWordLike && part.segment) {
						segments.push({ word: part.segment, start: part.index });
					}
				}
				return segments;
			}

			const segments = [];
			const wordRegex = /[\p{L}\p{N}]+(?:[\u0027\u2019\u2010-\u2015][\p{L}\p{N}]+)*/gu;
			let match;
			wordRegex.lastIndex = 0;
			while ((match = wordRegex.exec(text)) !== null) {
				segments.push({ word: match[0], start: match.index });
			}
			return segments;
		}

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
				const segments = getWordSegments(text);
				for (const segment of segments) {
					const word = segment.word;
					const start = segment.start;
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
					lastIndex = start + word.length;
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

		function clearAllHighlights() {
			getReadAloudTargets().forEach(clearHighlights);
		}

		function updateSpeechToggleState(isSpeaking) {
			const speechToggle = document.getElementById('toggleSpeech');
			if (speechToggle) {
				speechToggle.classList.toggle('is-speaking', isSpeaking);
				speechToggle.setAttribute('aria-pressed', isSpeaking ? 'true' : 'false');
			}
		}

		function stopSpeaking() {
			stopRequested = true;
			speaking = false;
			if (synth && synth.speaking) synth.cancel();
			clearAllHighlights();
			updateSpeechToggleState(false);
			if (volumeIcon) volumeIcon.className = 'fa fa-volume-high';
		}

		function scrollSpanToCenter(span) {
			if (!span || !span.isConnected) return;

			const contentContainer = span.closest('.blog-text-content');
			const header = document.querySelector('.header');
			const headerOffset = header ? header.offsetHeight : 0;

			if (contentContainer && contentContainer.scrollHeight > contentContainer.clientHeight) {
				const spanRect = span.getBoundingClientRect();
				const containerRect = contentContainer.getBoundingClientRect();
				const desiredTop = contentContainer.scrollTop
					+ (spanRect.top - containerRect.top)
					- (containerRect.height / 2)
					+ (spanRect.height / 2);
				const maxTop = Math.max(0, contentContainer.scrollHeight - contentContainer.clientHeight);
				const nextTop = Math.min(Math.max(0, desiredTop), maxTop);

				try {
					if (typeof contentContainer.scrollTo === 'function') {
						contentContainer.scrollTo({ top: nextTop, behavior: 'smooth' });
					} else {
						contentContainer.scrollTop = nextTop;
					}
				} catch (e) {
					contentContainer.scrollTop = nextTop;
				}

				setTimeout(() => {
					try {
						if (typeof span.scrollIntoView === 'function' && Math.abs((contentContainer.scrollTop || 0) - nextTop) > 2) {
							span.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
						}
					} catch (e) { /* ignore */ }
				}, 50);

				return;
			}

			const spanRect = span.getBoundingClientRect();
			const viewportHeight = Math.max(0, (window.innerHeight || document.documentElement.clientHeight) - headerOffset);
			const desiredTop = window.pageYOffset
				+ spanRect.top
				- headerOffset
				- (viewportHeight / 2)
				+ (spanRect.height / 2);
			const maxScroll = Math.max(0, document.documentElement.scrollHeight - (window.innerHeight || document.documentElement.clientHeight));
			const nextTop = Math.min(Math.max(0, desiredTop), maxScroll);

			try {
				window.scrollTo({ top: nextTop, behavior: 'smooth' });
			} catch (e) {
				window.scrollTo(0, nextTop);
			}
		}

		// Speak words sequentially (per-word utterances) — fallback will work across browsers
		async function speakPerWord(wordSpans, startIndex = 0) {
			if (!wordSpans || !wordSpans.length) return;
			stopRequested = false;
			speaking = true;
			updateSpeechToggleState(true);
			if (volumeIcon) volumeIcon.className = 'fa fa-pause';
			for (let i = startIndex; i < wordSpans.length; i++) {
				if (stopRequested) break;
				const span = wordSpans[i];
				const text = span.textContent.trim();
				if (!text) continue;

				// highlight current word
				clearAllHighlights();
				span.classList.add('_speaking');

				scrollSpanToCenter(span);
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
			updateSpeechToggleState(false);
			if (volumeIcon) volumeIcon.className = 'fa fa-volume-high';
			clearAllHighlights();
		}

		if (toggleSpeech) {
			const newToggle = toggleSpeech.cloneNode(true);
			toggleSpeech.parentNode.replaceChild(newToggle, toggleSpeech);

			newToggle.addEventListener('click', async function() {
				const titleEl = document.querySelector('.blog-title');
				const contentEl = document.querySelector('.blog-text-content');
				const readTargets = getReadAloudTargets();
				if (!readTargets.length) return;

				// If already speaking, stop
				if (speaking) { stopSpeaking(); return; }

				// Ensure reading starts from the very top (first paragraph visible)
				// Reset scroll position before narration starts
				try {
					if (contentEl) {
						contentEl.scrollTop = 0;
					}
					window.scrollTo({ top: 0, behavior: 'auto' });
				} catch (e) {}

				// Wrap words and get spans from the title and article body.
				const wordSpans = [];
				if (titleEl) {
					wordSpans.push(...wrapWords(titleEl));
				}
				if (contentEl) {
					wordSpans.push(...wrapWords(contentEl));
				}
				if (!wordSpans.length) return;

				// Allow DOM to settle after wrapping before speaking
				await new Promise(r => requestAnimationFrame(() => r()));

				// Center the first highlighted word before speaking.
				if (wordSpans[0]) {
					scrollSpanToCenter(wordSpans[0]);
				}

				// Speak per-word starting from the title so the narration stays in sync.
				await speakPerWord(wordSpans, 0);
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
		const currentUrl = window.location.href;
		
		// Fetch fresh data to get updated views count
		fetch(currentUrl, {
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
				loadBlogContent(this.href);
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
			const canonicalLink = doc.head.querySelector('link[rel="canonical"]');
			if (canonicalLink) {
				document.head.querySelectorAll('link[rel="canonical"]').forEach(n => n.parentNode.removeChild(n));
				document.head.appendChild(canonicalLink.cloneNode(true));
			}
		} catch (e) {
			console.warn('Failed to update meta tags:', e);
		}
	}
	
	// Extract blog loading logic into separate function
	function loadBlogContent(blogUrl) {
		const mainContent = document.getElementById('blog-main-content');
		mainContent.style.opacity = '0.5';
		mainContent.style.pointerEvents = 'none';
		
		fetch(blogUrl, {
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
					
					window.history.pushState({blogUrl: blogUrl}, '', blogUrl);
					window.scrollTo({top: 0, behavior: 'smooth'});

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

{{-- ── PDF Download Modal ── --}}
@if(isset($main) && $main)
<div id="pdf-download-modal" role="dialog" aria-modal="true" aria-labelledby="pdm-title" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">
    {{-- Backdrop --}}
    <div id="pdm-backdrop" style="position:absolute;inset:0;background:rgba(15,23,42,0.55);backdrop-filter:blur(3px);"></div>

    {{-- Panel --}}
    <div style="position:relative;z-index:1;background:#fff;border-radius:18px;box-shadow:0 24px 64px rgba(15,23,42,0.2);max-width:460px;width:calc(100% - 32px);overflow:hidden;animation:pdm-in 0.22s ease;">
        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:22px 26px 18px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div>
                    <p style="margin:0 0 4px;color:#9cc8ff;font-size:11px;letter-spacing:1.1px;text-transform:uppercase;font-weight:700;">Armely Insights</p>
                    <h2 id="pdm-title" style="margin:0;color:#fff;font-size:18px;font-weight:700;line-height:1.25;">Download Article as PDF</h2>
                </div>
                <button id="pdm-close" aria-label="Close" style="background:rgba(255,255,255,0.15);border:none;color:#fff;border-radius:50%;width:32px;height:32px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">&times;</button>
            </div>
            <p style="margin:10px 0 0;color:#c8dbf7;font-size:13px;line-height:1.45;" id="pdm-article-title">{{ Str::limit($main->title ?? '', 80) }}</p>
        </div>

        {{-- Body --}}
        <div style="padding:22px 26px 26px;">
            <p style="margin:0 0 18px;font-size:13.5px;color:#4b5563;line-height:1.6;">Enter your details below and we'll email you a secure download link for this article in PDF format. The link will be valid for 24 hours.</p>

            <form id="pdm-form" novalidate>
                @csrf
                <div style="margin-bottom:14px;">
                    <label for="pdm-name" style="display:block;font-size:12.5px;font-weight:600;color:#1f2937;margin-bottom:5px;">Full Name <span style="color:#ef4444;">*</span></label>
                    <input id="pdm-name" name="name" type="text" placeholder="Your full name" autocomplete="name"
                        style="width:100%;padding:10px 13px;border:1.5px solid #d1d5db;border-radius:9px;font-size:14px;color:#1f2937;outline:none;transition:border-color 0.18s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#2f5597'" onblur="this.style.borderColor='#d1d5db'">
                    <p class="pdm-field-error" id="pdm-name-error" style="display:none;margin:4px 0 0;font-size:12px;color:#ef4444;"></p>
                </div>
                <div style="margin-bottom:20px;">
                    <label for="pdm-email" style="display:block;font-size:12.5px;font-weight:600;color:#1f2937;margin-bottom:5px;">Email Address <span style="color:#ef4444;">*</span></label>
                    <input id="pdm-email" name="email" type="email" placeholder="you@example.com" autocomplete="email"
                        style="width:100%;padding:10px 13px;border:1.5px solid #d1d5db;border-radius:9px;font-size:14px;color:#1f2937;outline:none;transition:border-color 0.18s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#2f5597'" onblur="this.style.borderColor='#d1d5db'">
                    <p class="pdm-field-error" id="pdm-email-error" style="display:none;margin:4px 0 0;font-size:12px;color:#ef4444;"></p>
                </div>

                {{-- Error / success banners --}}
                <div id="pdm-alert" style="display:none;padding:10px 14px;border-radius:9px;font-size:13px;margin-bottom:16px;line-height:1.5;"></div>

                <button id="pdm-submit" type="submit"
                    style="width:100%;padding:12px 20px;background:linear-gradient(135deg,#153462 0%,#2f5597 100%);color:#fff;border:none;border-radius:10px;font-size:14.5px;font-weight:700;cursor:pointer;transition:opacity 0.18s,transform 0.18s;letter-spacing:0.2px;">
                    <span id="pdm-btn-label"><i class="fa fa-paper-plane" style="margin-right:7px;"></i>Send Download Link</span>
                    <span id="pdm-btn-loading" style="display:none;"><i class="fa fa-circle-notch fa-spin" style="margin-right:7px;"></i>Sending…</span>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes pdm-in {
    from { opacity:0; transform:translateY(18px) scale(0.97); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
#pdf-download-modal.is-open { display:flex !important; }
</style>

<script>
(function () {
    const modal     = document.getElementById('pdf-download-modal');
    const backdrop  = document.getElementById('pdm-backdrop');
    const closeBtn  = document.getElementById('pdm-close');
    const form      = document.getElementById('pdm-form');
    const nameInput = document.getElementById('pdm-name');
    const emailInput= document.getElementById('pdm-email');
    const alert     = document.getElementById('pdm-alert');
    const btnLabel  = document.getElementById('pdm-btn-label');
    const btnLoading= document.getElementById('pdm-btn-loading');
    const submitBtn = document.getElementById('pdm-submit');

    function openModal() {
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        nameInput.focus();
    }
    function closeModal() {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
        form.reset();
        hideAlert();
        clearErrors();
    }
    function showAlert(msg, isSuccess) {
        alert.textContent = msg;
        alert.style.display = 'block';
        if (isSuccess) {
            alert.style.background = '#f0fdf4';
            alert.style.border     = '1px solid #bbf7d0';
            alert.style.color      = '#166534';
        } else {
            alert.style.background = '#fef2f2';
            alert.style.border     = '1px solid #fecaca';
            alert.style.color      = '#991b1b';
        }
    }
    function hideAlert() { alert.style.display = 'none'; }
    function clearErrors() {
        ['pdm-name-error','pdm-email-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }
    function showFieldError(id, msg) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = msg;
        el.style.display = 'block';
    }
    function setLoading(on) {
        btnLabel.style.display  = on ? 'none' : 'inline';
        btnLoading.style.display= on ? 'inline' : 'none';
        submitBtn.disabled = on;
    }

    // Wire the download button — handle both static and AJAX-replaced content
    function wireDownloadBtn() {
        const btn = document.getElementById('btn-download-pdf');
        if (btn && !btn._pdmWired) {
            btn._pdmWired = true;
            btn.addEventListener('click', openModal);
        }
    }
    wireDownloadBtn();
    document.addEventListener('blog:loaded', wireDownloadBtn); // fired by AJAX switcher

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        hideAlert();
        clearErrors();

        const name  = nameInput.value.trim();
        const email = emailInput.value.trim();
        let valid = true;

        if (!name) { showFieldError('pdm-name-error', 'Please enter your name.'); valid = false; }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('pdm-email-error', 'Please enter a valid email address.'); valid = false;
        }
        if (!valid) return;

        const blogId = document.getElementById('btn-download-pdf')?.dataset.blogId;
        if (!blogId) { showAlert('Unable to identify the article. Please refresh and try again.', false); return; }

        setLoading(true);

        try {
            const token = form.querySelector('input[name="_token"]')?.value
                       || document.querySelector('meta[name="csrf-token"]')?.content
                       || '';
            const res = await fetch(`/blog/${blogId}/request-download`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name, email }),
            });

            const json = await res.json();

            if (res.ok && json.success) {
                showAlert('✓ ' + json.message, true);
                form.reset();
                submitBtn.style.display = 'none';
                setTimeout(closeModal, 4000);
            } else {
                const errors = json.errors;
                if (errors) {
                    if (errors.name)  showFieldError('pdm-name-error',  errors.name[0]);
                    if (errors.email) showFieldError('pdm-email-error', errors.email[0]);
                } else {
                    showAlert(json.message || 'Something went wrong. Please try again.', false);
                }
            }
        } catch (err) {
            showAlert('A network error occurred. Please check your connection and try again.', false);
        } finally {
            setLoading(false);
            submitBtn.style.display = '';
        }
    });
})();
</script>
@endif

@endsection
