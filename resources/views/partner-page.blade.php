@extends('layouts.public')

@section('title', $pageTitle ?? 'Partner | Armely')
@section('meta_description', $metaDescription ?? 'Explore Armely partner resources and offerings across leading technology ecosystems.')
@section('meta_keywords', $metaKeywords ?? 'Armely partners, technology partners, enterprise technology services')
@section('canonical_url', $canonicalUrl ?? request()->url())

@push('head')
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $pageTitle ?? 'Partner | Armely' }}">
<meta property="og:description" content="{{ $metaDescription ?? 'Explore Armely partner resources and offerings across leading technology ecosystems.' }}">
<meta property="og:url" content="{{ $canonicalUrl ?? request()->url() }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $pageTitle ?? 'Partner | Armely' }}">
<meta name="twitter:description" content="{{ $metaDescription ?? 'Explore Armely partner resources and offerings across leading technology ecosystems.' }}">
@endpush

@push('styles')
<style>
.partner-page-wrapper .partner-tabbed-container {
    max-width: none !important;
    width: 100% !important;
    padding-left: 16px !important;
    padding-right: 16px !important;
}
.partner-page-wrapper .partner-detail-layout {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    gap: 28px;
    align-items: flex-start;
}
.partner-page-wrapper .partner-detail-tabs {
    display: grid;
    gap: 10px;
    margin: 0;
    padding: 16px;
    background: linear-gradient(155deg, rgba(255, 255, 255, 0.99), rgba(244, 249, 255, 0.99));
    border: 1px solid #d4e1f5;
    border-radius: 18px;
    box-shadow: 0 15px 34px rgba(22, 48, 91, 0.09);
    position: sticky;
    top: 90px;
}
.partner-page-wrapper .partner-detail-tabs::before {
    content: "Explore Sections";
    color: #163365;
    font-size: 1.02rem;
    font-weight: 800;
    padding-bottom: 12px;
    margin-bottom: 2px;
    border-bottom: 1px solid #e2ebfa;
}
.partner-page-wrapper .partner-mobile-menu-toggle {
    display: none;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    border: 1px solid #d4e1f5;
    border-radius: 12px;
    background: #fff;
    color: #163365;
    font-size: .95rem;
    font-weight: 800;
    padding: 12px 14px;
    box-shadow: 0 10px 22px rgba(24, 54, 107, .08);
}
.partner-page-wrapper .partner-mobile-menu-toggle i {
    color: #2f5597;
}
.partner-page-wrapper .partner-detail-tabs .nav-item {
    margin: 0;
}
.partner-page-wrapper .partner-detail-tabs .nav-link {
    border: 1px solid #c4d6f3;
    border-radius: 999px;
    background: #fff;
    color: #26457d;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 13px;
    font-size: .85rem;
    font-weight: 800;
    line-height: 1.25;
    cursor: pointer;
    width: 100%;
    white-space: normal;
    overflow-wrap: anywhere;
    transition: all .2s ease;
}
.partner-page-wrapper .partner-detail-tabs .nav-link::before {
    content: "";
    flex: 0 0 7px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #87a7de;
    box-shadow: 0 0 0 3px rgba(135, 167, 222, .2);
    margin-top: 4px;
}
.partner-page-wrapper .partner-detail-tabs .nav-link:hover {
    color: #18366b;
    border-color: #9eb8e7;
    background: #edf3ff;
}
.partner-page-wrapper .partner-detail-tabs .nav-link.active {
    background: #2f5597;
    border-color: #2f5597;
    color: #fff;
    box-shadow: 0 8px 18px rgba(47, 85, 151, .3);
}
.partner-page-wrapper .partner-detail-tabs .nav-link.active::before {
    background: #cfe0ff;
    box-shadow: 0 0 0 3px rgba(207, 224, 255, .25);
}
.partner-page-wrapper .partner-detail-panel {
    display: none;
}
.partner-page-wrapper .partner-detail-panel.active {
    display: block;
}
.partner-page-wrapper .partner-detail-panel > .section-title:first-child {
    margin-top: 0 !important;
    color: #172033 !important;
    font-size: 1.7rem !important;
    font-weight: 800 !important;
    line-height: 1.25 !important;
    margin-bottom: 8px !important;
    display: block !important;
    text-align: left !important;
}
.partner-page-wrapper .partner-detail-panel > .section-title:first-child::after {
    content: "";
    display: block;
    width: 56px;
    height: 2px;
    background: #2f5597;
    margin-top: 12px;
}
.partner-page-wrapper .partner-section-heading {
    background: #fff;
    border: 1px solid #dce7fb;
    border-radius: 14px;
    box-shadow: 0 10px 22px rgba(24, 54, 107, .08);
    padding: 22px 24px;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
}
.partner-page-wrapper .partner-section-heading::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: #2f5597;
}
.partner-page-wrapper .partner-section-heading .section-title {
    margin-bottom: 8px !important;
}
.partner-page-wrapper .partner-section-heading .partner-tab-intro,
.partner-page-wrapper .partner-section-heading .partner-lead {
    margin-bottom: 0 !important;
}
.partner-page-wrapper .partner-tab-intro {
    color: #5f6f86;
    font-size: .98rem;
    line-height: 1.65;
    max-width: 860px;
    margin: 0;
}
.partner-page-wrapper .partner-detail-panel > .modern-grid:first-of-type {
    margin-top: 0 !important;
}
.partner-page-wrapper .modern-grid,
.partner-page-wrapper .dynamics-grid,
.partner-page-wrapper .v-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 20px !important;
    align-items: stretch !important;
}
.partner-page-wrapper .modern-card,
.partner-page-wrapper .dynamics-card,
.partner-page-wrapper .v-s-card {
    background: #fff !important;
    border: 1px solid #dce7fb !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 22px rgba(24, 54, 107, .08) !important;
    padding: 22px !important;
    min-height: 210px;
    height: 100%;
    display: flex !important;
    flex-direction: column;
    gap: 10px;
    overflow: hidden;
    position: relative;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease !important;
}
.partner-page-wrapper .modern-card::before,
.partner-page-wrapper .dynamics-card::before,
.partner-page-wrapper .v-s-card::before {
    height: 3px !important;
    background: #2f5597 !important;
}
.partner-page-wrapper .modern-card:hover,
.partner-page-wrapper .dynamics-card:hover,
.partner-page-wrapper .v-s-card:hover {
    border-color: #bdd0ef !important;
    box-shadow: 0 18px 34px rgba(24, 54, 107, .14) !important;
    transform: translateY(-4px) !important;
}
.partner-page-wrapper .modern-card .card-icon,
.partner-page-wrapper .dynamics-card .dynamics-icon,
.partner-page-wrapper .v-s-card .v-icon {
    width: 46px;
    height: 46px;
    border: 1px solid #dce7fb;
    border-radius: 10px;
    background: #f4f8ff;
    color: #2f5597;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem !important;
    margin-bottom: 4px !important;
    filter: none !important;
}
.partner-page-wrapper .modern-card .card-title,
.partner-page-wrapper .dynamics-card .dynamics-card-title,
.partner-page-wrapper .v-s-card .v-title {
    color: #172033 !important;
    font-size: 1.02rem !important;
    font-weight: 800 !important;
    line-height: 1.34 !important;
    min-height: 2.7em;
    margin: 0 !important;
}
.partner-page-wrapper .modern-card .card-desc,
.partner-page-wrapper .dynamics-card .dynamics-card-desc,
.partner-page-wrapper .v-s-card .v-desc {
    color: #5f6f86 !important;
    font-size: .92rem !important;
    line-height: 1.58 !important;
    margin: 0 !important;
}
.partner-page-wrapper .modern-card:not(.is-expanded) .card-desc:first-of-type,
.partner-page-wrapper .dynamics-card:not(.is-expanded) .dynamics-card-desc:first-of-type,
.partner-page-wrapper .v-s-card:not(.is-expanded) .v-desc:first-of-type {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.partner-page-wrapper .modern-card:not(.is-expanded) .card-desc:nth-of-type(n+2) {
    display: none !important;
}
.partner-page-wrapper .modern-card.is-expanded .card-desc,
.partner-page-wrapper .modern-card.is-expanded .card-features,
.partner-page-wrapper .modern-card.is-expanded .card-features li,
.partner-page-wrapper .dynamics-card.is-expanded .dynamics-card-desc,
.partner-page-wrapper .dynamics-card.is-expanded .dynamics-features,
.partner-page-wrapper .dynamics-card.is-expanded .dynamics-features li,
.partner-page-wrapper .v-s-card.is-expanded .v-desc,
.partner-page-wrapper .v-s-card.is-expanded .v-feats,
.partner-page-wrapper .v-s-card.is-expanded .v-feats li {
    display: block !important;
    max-height: none !important;
    overflow: visible !important;
}
.partner-page-wrapper .modern-card .card-features,
.partner-page-wrapper .dynamics-card .dynamics-features,
.partner-page-wrapper .v-s-card .v-feats {
    margin: 2px 0 0 !important;
}
.partner-page-wrapper .modern-card:not(.is-expanded) .card-features li:nth-child(n+4),
.partner-page-wrapper .dynamics-card:not(.is-expanded) .dynamics-features li:nth-child(n+4),
.partner-page-wrapper .v-s-card:not(.is-expanded) .v-feats li:nth-child(n+4) {
    display: none !important;
}
.partner-page-wrapper .modern-card .card-features li,
.partner-page-wrapper .dynamics-card .dynamics-features li,
.partner-page-wrapper .v-s-card .v-feats li {
    color: #5f6f86 !important;
    font-size: .88rem !important;
    line-height: 1.45 !important;
    margin-bottom: 7px !important;
}
.partner-page-wrapper .partner-card-toggle {
    align-self: flex-start;
    margin-top: auto;
    border: 0;
    background: transparent;
    color: #2f5597;
    font-size: .9rem;
    font-weight: 800;
    padding: 6px 0 0;
    cursor: pointer;
}
.partner-page-wrapper .partner-card-toggle:hover {
    color: #1e3a6d;
    text-decoration: underline;
}

.partner-page-wrapper .partner-detail-panel h2,
.partner-page-wrapper .partner-detail-panel h3,
.partner-page-wrapper .partner-detail-panel h4,
.partner-page-wrapper .partner-detail-panel .section-title,
.partner-page-wrapper .partner-detail-panel .ms-list-title,
.partner-page-wrapper .partner-detail-panel .v-list-title {
    line-height: 1.3 !important;
    margin-top: 18px !important;
    margin-bottom: 10px !important;
}

.partner-page-wrapper .partner-detail-panel p,
.partner-page-wrapper .partner-detail-panel .partner-lead,
.partner-page-wrapper .partner-detail-panel .card-desc,
.partner-page-wrapper .partner-detail-panel .dynamics-card-desc,
.partner-page-wrapper .partner-detail-panel .v-desc {
    line-height: 1.7 !important;
    margin-bottom: 12px !important;
}

.partner-page-wrapper .partner-detail-panel ul,
.partner-page-wrapper .partner-detail-panel ol {
    margin-bottom: 14px !important;
}

.partner-page-wrapper .partner-detail-panel li,
.partner-page-wrapper .partner-detail-panel .card-features li,
.partner-page-wrapper .partner-detail-panel .dynamics-features li,
.partner-page-wrapper .partner-detail-panel .v-feats li {
    line-height: 1.5 !important;
}
@media (max-width: 1199px) {
    .partner-page-wrapper .modern-grid,
    .partner-page-wrapper .dynamics-grid,
    .partner-page-wrapper .v-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}
@media (max-width: 767px) {
    .partner-page-wrapper .partner-tabbed-container {
        padding-left: 12px !important;
        padding-right: 12px !important;
    }
    .partner-page-wrapper .partner-detail-layout {
        grid-template-columns: minmax(0, 1fr);
    }
    .partner-page-wrapper .partner-mobile-menu-toggle {
        display: flex;
        margin-bottom: 10px;
    }
    .partner-page-wrapper .partner-detail-tabs {
        position: static;
        border-radius: 14px;
        display: none;
        margin-bottom: 18px;
        padding: 12px;
    }
    .partner-page-wrapper .partner-detail-tabs.is-open {
        display: grid;
    }
    .partner-page-wrapper .partner-detail-tabs .nav-link {
        border-radius: 10px;
        width: 100%;
    }
    .partner-page-wrapper .modern-grid,
    .partner-page-wrapper .dynamics-grid,
    .partner-page-wrapper .v-grid {
        grid-template-columns: minmax(0, 1fr) !important;
    }
}
</style>
@endpush

@section('content')
    <div class="partner-page-wrapper">
        {!! $content !!}
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var wrapper = document.querySelector('.partner-page-wrapper');
    var container = wrapper ? wrapper.querySelector(':scope > .container') : null;
    var iconMap = {
        '🛫': 'fa fa-plane',
        '📊': 'fa fa-bar-chart',
        '🛠️': 'fa fa-wrench',
        '🖧': 'fa fa-sitemap',
        '🔐': 'fa fa-lock',
        '⚙️': 'fa fa-cogs',
        '🖥️': 'fa fa-desktop',
        '💾': 'fa fa-hdd-o',
        '🗄️': 'fa fa-database',
        '🌐': 'fa fa-globe',
        '📈': 'fa fa-line-chart',
        '🤖': 'fa fa-microchip',
        '⚕️': 'fa fa-heartbeat',
        '🏦': 'fa fa-bank',
        '🏭': 'fa fa-industry',
        '🛍️': 'fa fa-shopping-bag',
        '🏛️': 'fa fa-university',
        '🎬': 'fa fa-film',
        '🧭': 'fa fa-compass',
        '🚚': 'fa fa-truck',
        '🏗️': 'fa fa-building',
        '🛡️': 'fa fa-shield',
        '🎓': 'fa fa-graduation-cap',
        '🔁': 'fa fa-refresh',
        '⚡': 'fa fa-bolt',
        '💰': 'fa fa-money',
        '🌱': 'fa fa-leaf',
        '🌍': 'fa fa-globe',
        '🧩': 'fa fa-puzzle-piece',
        '🚀': 'fa fa-rocket',
        '💵': 'fa fa-usd',
        '🏅': 'fa fa-trophy',
        '🤝': 'fa fa-handshake-o',
        '🧰': 'fa fa-briefcase',
        '🔎': 'fa fa-search',
        '🗺️': 'fa fa-map-o',
        '🔧': 'fa fa-wrench',
        '🎯': 'fa fa-bullseye',
        '✅': 'fa fa-check-circle',
        '✔️': 'fa fa-check-circle',
        '⭐': 'fa fa-star',
        '☁️': 'fa fa-cloud',
        '📦': 'fa fa-cube',
        '👨‍🎓': 'fa fa-user-graduate'
    };
    var tabIntros = {
        'Overview': 'A quick summary of the partnership, delivery approach, and how Armely supports customer outcomes.',
        'Service Capabilities': 'Core capabilities organized around migration, modernization, data, security, operations, and automation.',
        'Service Areas': 'Common platform areas where Armely helps teams design, deploy, govern, and optimize cloud solutions.',
        'Industry Solutions': 'Practical solution patterns for regulated and data-intensive teams across key industries.',
        'Our Service Offerings': 'Engagement models that support strategy, migration, implementation, operations, and enablement.',
        'Well-Architected Framework': 'Architecture review areas that help teams improve reliability, security, performance, cost, operations, and sustainability.',
        'Why AWS?': 'Platform advantages and operating benefits that help teams move faster with resilient cloud foundations.',
        'Our Differentiators': 'What Armely brings to partner-led delivery: practical experience, governance, and outcome-focused execution.',
        'Getting Started': 'A simple path for moving from discovery to implementation with the right level of planning and support.',
        'Ready to Start?': 'Next steps for discussing goals, priorities, and the right starting point for your cloud journey.'
    };

    if (container && !container.classList.contains('partner-tabbed-container')) {
        var children = Array.prototype.slice.call(container.children);
        var groups = [];
        var current = { title: 'Overview', nodes: [] };

        function getSectionTitle(node) {
            var heading = null;

            if (node.matches('h2.section-title, h3.section-title, h4.v-list-title')) {
                heading = node;
            } else {
                heading = node.querySelector(':scope > .dynamics-header > .section-title, :scope > h2.section-title, :scope > h3.section-title, :scope > h4.v-list-title');
            }

            return heading ? heading.textContent.trim() : '';
        }

        children.forEach(function (child) {
            if (child.tagName === 'HR') {
                return;
            }

            var sectionTitle = getSectionTitle(child);
            var isSectionStart = sectionTitle !== '';

            if (isSectionStart && current.nodes.length) {
                groups.push(current);
                current = { title: sectionTitle || 'Section', nodes: [child] };
                return;
            }

            if (isSectionStart) {
                current.title = sectionTitle || current.title;
            }

            current.nodes.push(child);
        });

        if (current.nodes.length) {
            groups.push(current);
        }

        if (groups.length > 1) {
            container.classList.add('partner-tabbed-container');
            container.innerHTML = '';

            var layout = document.createElement('div');
            layout.className = 'partner-detail-layout';

            var mobileToggle = document.createElement('button');
            mobileToggle.type = 'button';
            mobileToggle.className = 'partner-mobile-menu-toggle';
            mobileToggle.innerHTML = '<span>Explore Sections</span><i class="fa fa-bars" aria-hidden="true"></i>';

            var tabs = document.createElement('ul');
            tabs.className = 'nav nav-tabs partner-detail-tabs';
            tabs.setAttribute('role', 'tablist');

            var panels = document.createElement('div');
            panels.className = 'tab-content partner-detail-panels';

            groups.forEach(function (group, index) {
                var tabId = 'partner-detail-tab-' + index;
                var panelId = 'partner-detail-panel-' + index;
                var navItem = document.createElement('li');
                navItem.className = 'nav-item';
                navItem.setAttribute('role', 'presentation');

                var tab = document.createElement('a');
                tab.href = '#' + panelId;
                tab.id = tabId;
                tab.className = 'nav-link' + (index === 0 ? ' active' : '');
                tab.textContent = group.title;
                tab.setAttribute('role', 'tab');
                tab.setAttribute('aria-controls', panelId);
                tab.setAttribute('aria-selected', index === 0 ? 'true' : 'false');

                var panel = document.createElement('section');
                panel.id = panelId;
                panel.className = 'tab-pane partner-detail-panel' + (index === 0 ? ' active show' : '');
                panel.setAttribute('role', 'tabpanel');
                panel.setAttribute('aria-labelledby', tabId);

                group.nodes.forEach(function (node) {
                    panel.appendChild(node);
                });

                var heading = panel.querySelector(':scope > h3.section-title');
                var existingIntro = heading ? heading.nextElementSibling : null;
                var hasIntro = existingIntro && existingIntro.classList && existingIntro.classList.contains('partner-lead');
                var introText = tabIntros[group.title] || 'Explore the key capabilities, services, and delivery considerations for this partner area.';

                if (heading && !hasIntro) {
                    var intro = document.createElement('p');
                    intro.className = 'partner-tab-intro';
                    intro.textContent = introText;
                    heading.insertAdjacentElement('afterend', intro);
                } else if (hasIntro) {
                    existingIntro.classList.add('partner-tab-intro');
                }

                heading = panel.querySelector(':scope > h3.section-title');
                var introNode = heading ? heading.nextElementSibling : null;
                var canWrapHeading = heading && introNode && (
                    introNode.classList.contains('partner-tab-intro') ||
                    introNode.classList.contains('partner-lead')
                );

                if (canWrapHeading && !heading.closest('.partner-section-heading')) {
                    var headingWrap = document.createElement('div');
                    headingWrap.className = 'partner-section-heading';
                    panel.insertBefore(headingWrap, heading);
                    headingWrap.appendChild(heading);
                    headingWrap.appendChild(introNode);
                }

                tab.addEventListener('click', function (event) {
                    event.preventDefault();
                    tabs.querySelectorAll('.nav-link').forEach(function (button) {
                        var isActive = button === tab;
                        button.classList.toggle('active', isActive);
                        button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });
                    panels.querySelectorAll('.partner-detail-panel').forEach(function (item) {
                        var isActive = item === panel;
                        item.classList.toggle('active', isActive);
                        item.classList.toggle('show', isActive);
                    });
                    tabs.classList.remove('is-open');
                    mobileToggle.querySelector('span').textContent = group.title;
                });

                navItem.appendChild(tab);
                tabs.appendChild(navItem);
                panels.appendChild(panel);
            });

            mobileToggle.addEventListener('click', function () {
                tabs.classList.toggle('is-open');
            });

            layout.appendChild(mobileToggle);
            layout.appendChild(tabs);
            layout.appendChild(panels);
            container.appendChild(layout);
        }
    }

    document.querySelectorAll('.partner-page-wrapper .card-icon, .partner-page-wrapper .dynamics-icon, .partner-page-wrapper .v-icon, .partner-page-wrapper .stat-number').forEach(function (iconEl) {
        var token = (iconEl.textContent || '').trim();
        if (iconMap[token]) {
            iconEl.innerHTML = '<i class="' + iconMap[token] + '" aria-hidden="true"></i>';
        }
    });

    document.querySelectorAll('.partner-page-wrapper .modern-card, .partner-page-wrapper .dynamics-card, .partner-page-wrapper .v-s-card').forEach(function (card) {
        var extraDescriptions = card.querySelectorAll('.card-desc:nth-of-type(n+2), .dynamics-card-desc:nth-of-type(n+2), .v-desc:nth-of-type(n+2)');
        var features = card.querySelector('.card-features, .dynamics-features, .v-feats');
        var hiddenFeatureItems = features ? features.querySelectorAll('li:nth-child(n+4)') : [];

        if (!extraDescriptions.length && !hiddenFeatureItems.length) {
            return;
        }

        if (card.querySelector('.partner-card-toggle')) {
            return;
        }

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'partner-card-toggle';
        button.textContent = 'Read more';
        button.addEventListener('click', function () {
            var expanded = card.classList.toggle('is-expanded');
            button.textContent = expanded ? 'Show less' : 'Read more';
        });

        card.appendChild(button);
    });
});
</script>
@endpush
