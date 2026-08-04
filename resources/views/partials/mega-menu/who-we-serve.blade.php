@php
    $industryMenuGroups = [
        'Regulated Industries' => [
            ['healthcare', 'Healthcare', 'Modern data, AI, and workflow solutions for healthcare teams.', 'fa-heart-pulse'],
            ['financial-services', 'Financial Services', 'Secure modernization for banking, insurance, and advisory firms.', 'fa-building-columns'],
            ['higher-education', 'Higher Education', 'Analytics and governance for colleges and universities.', 'fa-graduation-cap'],
        ],
        'Operations' => [
            ['energy', 'Energy / Oil & Gas', 'Digital transformation for oil, gas, utilities, and energy operations.', 'fa-oil-well'],
            ['manufacturing', 'Manufacturing', 'Operational reporting and automation for modern manufacturing teams.', 'fa-industry'],
            ['transportation-logistics', 'Transportation & Logistics', 'Improve planning, tracking, analytics, and delivery performance.', 'fa-truck-fast'],
        ],
        'Public Sector' => [
            ['state-local-government', 'State & Local Government', 'Secure Microsoft platform solutions for public-sector agencies.', 'fa-landmark'],
            ['nonprofit-social-services', 'Nonprofit & Social Services', 'Mission-focused automation and analytics for service organizations.', 'fa-hand-holding-heart'],
        ],
        'Specialized' => [
            ['professional-services', 'Professional Services', 'Client delivery and knowledge-work automation for firms.', 'fa-briefcase'],
            ['agriculture-cannabis', 'Agriculture & Cannabis', 'Data, automation, and reporting for field operations.', 'fa-seedling'],
        ],
    ];
@endphp

<div class="mega-panel-inner">
    <a class="mega-feature-card" href="{{ route('industries.index') }}">
        <img src="{{ asset('images/blog/1.png') }}" alt="Business leaders reviewing client outcomes">
        <div class="mega-feature-card-content">
            <h3>Industries We Serve</h3>
            <p>Explore Armely's dedicated industry solutions for data, AI, automation, and platform modernization.</p>
        </div>
    </a>
    <div class="mega-columns">
        @foreach($industryMenuGroups as $group => $industries)
            <div>
                <h3 class="mega-column-title">{{ $group }}</h3>
                <ul class="mega-link-list">
                    @foreach($industries as [$slug, $label, $description, $icon])
                        <li>
                            <a class="mega-link-with-icon" href="{{ route('industries.show', $slug) }}">
                                <span class="mega-link-icon"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
                                <span class="mega-link-copy">
                                    <span class="mega-link-label">{{ $label }}</span>
                                    <span class="mega-link-description">{{ $description }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>
