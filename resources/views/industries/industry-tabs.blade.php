@php
    $industryTabIcons = [
        'healthcare' => 'icofont-doctor',
        'energy' => 'icofont-fire-burn',
        'financial-services' => 'icofont-bank-alt',
        'higher-education' => 'icofont-graduate',
        'manufacturing' => 'icofont-industry',
        'nonprofit-social-services' => 'icofont-users-alt-1',
        'professional-services' => 'icofont-briefcase',
        'state-local-government' => 'icofont-building-alt',
        'transportation-logistics' => 'icofont-delivery-time',
        'agriculture-cannabis' => 'icofont-leaf',
    ];

    $primaryIndustries = array_slice($industryPages, 0, 5, true);
    $secondaryIndustries = array_slice($industryPages, 5, null, true);
@endphp

<nav class="industry-pages-tabs" aria-label="Industry pages">
    <div class="industry-pages-tabs-inner">
        <ul class="nav nav-tabs modern-tabs-industries industry-pages-tablist">
            @foreach($primaryIndustries as $slug => $page)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('industries.show', $slug) }}">
                        <i class="{{ $industryTabIcons[$slug] ?? 'icofont-building-alt' }}" aria-hidden="true"></i>
                        <strong>{{ $page['label'] }}</strong>
                    </a>
                </li>
            @endforeach
            @if(count($secondaryIndustries))
                <li class="nav-item dropdown industry-more-dropdown">
                    <a class="nav-link dropdown-toggle industry-more-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="icofont-ui-menu" aria-hidden="true"></i>
                        <strong>More Industries</strong>
                    </a>
                    <div class="dropdown-menu industry-more-menu">
                        @foreach($secondaryIndustries as $slug => $page)
                            <a class="dropdown-item industry-tab-link" href="{{ route('industries.show', $slug) }}">
                                <i class="{{ $industryTabIcons[$slug] ?? 'icofont-building-alt' }}" aria-hidden="true"></i>
                                <span>{{ $page['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </li>
            @endif
        </ul>
    </div>
</nav>
