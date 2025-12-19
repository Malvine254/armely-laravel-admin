/**
 * Enhanced Search & Bot Interface JavaScript
 * Armely Laravel Admin
 */

(function ($) {
    'use strict';

    // ========================================
    // SEARCH FUNCTIONALITY
    // ========================================

    const Search = {
        init: function () {
            this.cacheDom();
            this.bindEvents();
            this.setupKeyboardNavigation();
        },

        cacheDom: function () {
            this.$searchModal = $('#searchModal');
            this.$searchInput = $('#globalSearchInput');
            this.$searchResults = $('#searchResultsContainer');
            this.$searchLoading = $('#searchLoadingSpinner');
            this.$searchStats = $('#searchStats');
            this.$searchTrigger = $('.search-trigger');
            this.$searchClose = $('.search-close-btn');
        },

        bindEvents: function () {
            const self = this;

            // Open search modal
            self.$searchTrigger.on('click', function (e) {
                e.preventDefault();
                self.openModal();
            });

            // Close search modal
            self.$searchClose.on('click', function () {
                self.closeModal();
            });

            // Close on overlay click
            self.$searchModal.on('click', function (e) {
                if ($(e.target).is(self.$searchModal)) {
                    self.closeModal();
                }
            });

            // Close on ESC key
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape' && self.$searchModal.hasClass('active')) {
                    self.closeModal();
                }
            });

            // Search input with debounce
            let searchTimeout;
            self.$searchInput.on('input', function () {
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();

                if (query.length >= 2) {
                    searchTimeout = setTimeout(function () {
                        self.performSearch(query);
                    }, 500);
                } else if (query.length === 0) {
                    self.showEmptyState();
                } else {
                    self.$searchResults.html('<div class="search-empty-state"><p>Type at least 2 characters to search...</p></div>');
                }
            });

            // Handle result clicks
            self.$searchResults.on('click', '.search-result-item', function () {
                const url = $(this).data('url');
                if (url) {
                    window.location.href = url;
                }
            });
        },

        setupKeyboardNavigation: function () {
            const self = this;
            let currentIndex = -1;

            self.$searchInput.on('keydown', function (e) {
                const $results = $('.search-result-item');

                if ($results.length === 0) return;

                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        currentIndex = Math.min(currentIndex + 1, $results.length - 1);
                        self.highlightResult($results, currentIndex);
                        break;

                    case 'ArrowUp':
                        e.preventDefault();
                        currentIndex = Math.max(currentIndex - 1, 0);
                        self.highlightResult($results, currentIndex);
                        break;

                    case 'Enter':
                        e.preventDefault();
                        if (currentIndex >= 0 && $results.eq(currentIndex).length) {
                            const url = $results.eq(currentIndex).data('url');
                            if (url) window.location.href = url;
                        }
                        break;
                }
            });
        },

        highlightResult: function ($results, index) {
            $results.removeClass('keyboard-focused');
            $results.eq(index).addClass('keyboard-focused');
            $results.eq(index)[0].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        },

        openModal: function () {
            this.$searchModal.addClass('active');
            this.$searchInput.focus();
            $('body').css('overflow', 'hidden');
        },

        closeModal: function () {
            this.$searchModal.removeClass('active');
            this.$searchInput.val('');
            this.showEmptyState();
            $('body').css('overflow', '');
        },

        performSearch: function (query) {
            const self = this;

            // Show loading state
            self.$searchLoading.addClass('active');
            self.$searchResults.html('');
            self.$searchStats.hide();

            // Make AJAX request
            $.ajax({
                url: '/api/search',
                method: 'GET',
                data: { query: query },
                dataType: 'json',
                success: function (response) {
                    self.$searchLoading.removeClass('active');

                    if (response.success && response.results.length > 0) {
                        self.displayResults(response.results, response.query);
                        self.updateStats(response.total_results, response.query);
                    } else {
                        self.showNoResults(query);
                    }
                },
                error: function (xhr, status, error) {
                    self.$searchLoading.removeClass('active');
                    self.$searchResults.html(
                        '<div class="search-no-results">' +
                        '<i class="fa fa-exclamation-triangle"></i>' +
                        '<h4>Search Error</h4>' +
                        '<p>Unable to perform search. Please try again.</p>' +
                        '</div>'
                    );
                    console.error('Search error:', error);
                }
            });
        },

        displayResults: function (results, query) {
            const self = this;
            let html = '';

            results.forEach(function (result, index) {
                html += self.createResultItem(result, index);
            });

            self.$searchResults.html(html);

            // Animate results
            $('.search-result-item').each(function (index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
            });
        },

        createResultItem: function (result, index) {
            return `
                <div class="search-result-item" data-url="${result.page_url}" data-index="${index}" tabindex="0">
                    <div class="search-result-header">
                        <h4 class="search-result-title">${result.page_name}</h4>
                        <span class="search-result-page-badge">${result.page_name}</span>
                    </div>
                    <div class="search-result-snippet">${result.snippet}</div>
                    <div class="search-result-meta">
                        <span class="search-result-location">
                            <i class="fa fa-map-marker-alt"></i>
                            Position: ${result.position} through page
                        </span>
                        <a href="${result.page_url}" class="search-result-link" onclick="event.stopPropagation()">
                            View Page <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            `;
        },

        showEmptyState: function () {
            this.$searchResults.html(
                '<div class="search-empty-state">' +
                '<i class="fa fa-search"></i>' +
                '<h4>Start Searching</h4>' +
                '<p>Enter keywords to find content across all pages</p>' +
                '</div>'
            );
            this.$searchStats.hide();
        },

        showNoResults: function (query) {
            this.$searchResults.html(
                '<div class="search-no-results">' +
                '<i class="fa fa-search-minus"></i>' +
                '<h4>No Results Found</h4>' +
                '<p>No matches found for "<strong>' + this.escapeHtml(query) + '</strong>"</p>' +
                '<p>Try different keywords or check your spelling.</p>' +
                '</div>'
            );
            this.$searchStats.hide();
        },

        updateStats: function (total, query) {
            this.$searchStats.html(
                'Found <strong>' + total + '</strong> result' + (total !== 1 ? 's' : '') +
                ' for "<strong>' + this.escapeHtml(query) + '</strong>"'
            ).show();
        },

        escapeHtml: function (text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    };

    // ========================================
    // BOT INTERFACE FUNCTIONALITY
    // ========================================

    const ChatBot = {
        init: function () {
            this.cacheDom();
            this.bindEvents();
            this.showInitialPopup();
        },

        cacheDom: function () {
            this.$popup = $('#helpPopup');
            this.$bubble = $('#chatBubble');
            this.$modal = $('#myModal');
            this.$chatNowBtn = $('#chatNowBtn');
            this.$noThanksBtn = $('#noThanksBtn');
            this.$closeModal = $('.modal-chat .close');
        },

        bindEvents: function () {
            const self = this;

            // Chat Now button
            self.$chatNowBtn.on('click', function () {
                self.$popup.hide();
                self.openChat();
            });

            // No Thanks button
            self.$noThanksBtn.on('click', function () {
                self.$popup.hide();
                self.$bubble.css('display', 'flex');
                // Store preference in localStorage
                localStorage.setItem('armely_chat_dismissed', Date.now());
            });

            // Bubble click
            self.$bubble.on('click', function () {
                self.openChat();
            });

            // Close modal
            self.$closeModal.on('click', function () {
                self.closeChat();
            });

            // Close on overlay click
            self.$modal.on('click', function (e) {
                if ($(e.target).is(self.$modal)) {
                    self.closeChat();
                }
            });

            // Close on ESC key
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape' && self.$modal.is(':visible')) {
                    self.closeChat();
                }
            });
        },

        showInitialPopup: function () {
            const self = this;

            // Check if user has dismissed popup in the last 24 hours
            const dismissed = localStorage.getItem('armely_chat_dismissed');
            const now = Date.now();
            const oneDayMs = 24 * 60 * 60 * 1000;

            if (!dismissed || (now - parseInt(dismissed)) > oneDayMs) {
                // Show popup after 5 seconds
                setTimeout(function () {
                    self.$popup.fadeIn(400);
                }, 5000);
            } else {
                // Show bubble instead
                self.$bubble.css('display', 'flex');
            }
        },

        openChat: function () {
            this.$modal.fadeIn(300);
            this.$bubble.hide();
            $('body').css('overflow', 'hidden');
        },

        closeChat: function () {
            this.$modal.fadeOut(300);
            this.$bubble.css('display', 'flex');
            $('body').css('overflow', '');
        }
    };

    // ========================================
    // ANNOUNCEMENT BANNER
    // ========================================

    const Banner = {
        init: function () {
            const self = this;
            const $banner = $('#announcementBanner');

            // Check if banner was closed
            if (localStorage.getItem('armely_banner_closed')) {
                $banner.hide();
            }

            // Close banner
            window.closeBanner = function () {
                $banner.slideUp(300);
                localStorage.setItem('armely_banner_closed', 'true');
            };
        }
    };

    // ========================================
    // COOKIES CONSENT
    // ========================================

    const Cookies = {
        init: function () {
            this.cacheDom();
            this.bindEvents();
            this.checkCookieConsent();
        },

        cacheDom: function () {
            this.$snackbar = $('#snackbar');
            this.$acceptAllBtn = $('#acceptAll');
            this.$savePreferencesBtn = $('#saveAllPreferences');
            this.$closeBtn = $('.snackbar .btn-close');
        },

        bindEvents: function () {
            const self = this;

            // Accept all cookies
            self.$acceptAllBtn.on('click', function () {
                self.acceptAllCookies();
            });

            // Save preferences
            self.$savePreferencesBtn.on('click', function () {
                self.savePreferences();
            });

            // Close snackbar
            self.$closeBtn.on('click', function () {
                self.$snackbar.removeClass('show');
            });
        },

        checkCookieConsent: function () {
            if (!localStorage.getItem('armely_cookies_accepted')) {
                setTimeout(() => {
                    this.$snackbar.addClass('show');
                }, 2000);
            }
        },

        acceptAllCookies: function () {
            localStorage.setItem('armely_cookies_accepted', 'all');
            this.$snackbar.removeClass('show');
        },

        savePreferences: function () {
            const preferences = {
                essential: true,
                performance: $('#cookieModal input').eq(1).prop('checked'),
                functionality: $('#cookieModal input').eq(2).prop('checked'),
                targeting: $('#cookieModal input').eq(3).prop('checked'),
                analytics: $('#cookieModal input').eq(4).prop('checked')
            };

            localStorage.setItem('armely_cookies_accepted', JSON.stringify(preferences));
            this.$snackbar.removeClass('show');
        }
    };

    // ========================================
    // LAZY LOADING
    // ========================================

    const LazyLoad = {
        init: function () {
            this.lazyLoadImages();
            this.lazyLoadVideos();
        },

        lazyLoadImages: function () {
            if (!('IntersectionObserver' in window)) {
                console.warn('IntersectionObserver not supported');
                return;
            }

            const lazyImages = document.querySelectorAll('.lazy-img');
            const lazyBGs = document.querySelectorAll('.lazy-bg');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;

                        if (target.tagName === 'IMG') {
                            const dataSrc = target.getAttribute('data-src');
                            if (dataSrc) {
                                target.src = dataSrc;
                                target.removeAttribute('data-src');
                                target.classList.add('loaded');

                                target.onerror = function () {
                                    console.error('Image failed to load:', dataSrc);
                                    target.classList.add('error');
                                };
                            }
                        } else {
                            const dataBg = target.getAttribute('data-bg');
                            if (dataBg) {
                                target.style.backgroundImage = `url('${dataBg}')`;
                                target.removeAttribute('data-bg');
                                target.classList.add('loaded');
                            }
                        }

                        observer.unobserve(target);
                    }
                });
            });

            lazyImages.forEach(img => observer.observe(img));
            lazyBGs.forEach(bg => observer.observe(bg));
        },

        lazyLoadVideos: function () {
            const lazyVideos = document.querySelectorAll('.lazy-video');

            lazyVideos.forEach(video => {
                video.addEventListener('click', function () {
                    const iframe = document.createElement('iframe');
                    iframe.setAttribute('width', '560');
                    iframe.setAttribute('height', '315');
                    iframe.setAttribute('src', video.getAttribute('data-src'));
                    iframe.setAttribute('frameborder', '0');
                    iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                    iframe.setAttribute('allowfullscreen', 'true');

                    video.innerHTML = '';
                    video.appendChild(iframe);
                });
            });
        }
    };

    // ========================================
    // BLOG SEARCH (FOR BLOG PAGE)
    // ========================================

    const BlogSearch = {
        init: function () {
            const $searchBar = $('#searchBar');
            const $noResults = $('#noResults');

            if ($searchBar.length) {
                $searchBar.on('input', function () {
                    const filter = $(this).val().toLowerCase();
                    let matchedItems = 0;
                    $noResults.hide();

                    $('.data-item').each(function () {
                        const text = $(this).text().toLowerCase();
                        if (text.includes(filter)) {
                            $(this).show();
                            matchedItems++;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (matchedItems === 0) {
                        $noResults.show();
                    }
                });
            }
        }
    };

    // ========================================
    // INITIALIZATION
    // ========================================

    $(document).ready(function () {
        Search.init();
        ChatBot.init();
        Banner.init();
        Cookies.init();
        LazyLoad.init();
        BlogSearch.init();

        console.log('%c🚀 Armely Enhanced Search & Bot Initialized', 'color: #2f5597; font-size: 14px; font-weight: bold;');
    });

})(jQuery);
