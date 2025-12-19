@extends('layouts.public')

@section('title', 'Our Insights & Stories - Armely Blog')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/blog-modern.css') }}">
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
										<img src="{{ asset('images/blog/profile.svg') }}" alt="Author">
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
		// Reinitialize read aloud button
		const toggleSpeech = document.getElementById('toggleSpeech');
		if (toggleSpeech) {
			toggleSpeech.addEventListener('click', function() {
				// Add your read aloud functionality here
				console.log('Read aloud clicked');
			});
		}
		
		// Reinitialize social share buttons
		const shareBtns = document.querySelectorAll('.shareBtn');
		shareBtns.forEach(function(btn) {
			btn.addEventListener('click', function() {
				const social = this.getAttribute('data-social');
				const url = window.location.href;
				const title = document.querySelector('.blog-title a').textContent;
				
				let shareUrl = '';
				switch(social) {
					case 'facebook':
						shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
						break;
					case 'twitter':
						shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
						break;
					case 'linkedin':
						shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
						break;
				}
				
				if (shareUrl) {
					window.open(shareUrl, '_blank', 'width=600,height=400');
				}
			});
		});
		
		// Reinitialize scroll more button
		const showMore = document.getElementById('show-more');
		if (showMore) {
			showMore.addEventListener('click', function() {
				const content = document.querySelector('.blog-text-content');
				if (content) {
					content.scrollIntoView({behavior: 'smooth', block: 'start'});
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
