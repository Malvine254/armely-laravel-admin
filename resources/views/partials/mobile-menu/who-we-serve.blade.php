@php
    $mobileIndustries = [
        'healthcare' => 'Healthcare',
        'energy' => 'Energy / Oil & Gas',
        'financial-services' => 'Financial Services',
        'higher-education' => 'Higher Education',
        'manufacturing' => 'Manufacturing',
        'nonprofit-social-services' => 'Nonprofit & Social Services',
        'professional-services' => 'Professional Services',
        'state-local-government' => 'State & Local Government',
        'transportation-logistics' => 'Transportation & Logistics',
        'agriculture-cannabis' => 'Agriculture & Cannabis',
    ];
@endphp

<div class="mobile-mega-panel">
    <div class="mobile-mega-section">
        <h4>Industries</h4>
        @foreach($mobileIndustries as $slug => $label)
            <a href="{{ route('industries.show', $slug) }}">{{ $label }}</a>
        @endforeach
    </div>
</div>
