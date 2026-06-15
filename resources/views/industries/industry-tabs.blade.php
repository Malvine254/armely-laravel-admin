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

<div class="industry-pages-tabs">
	<div class="industry-pages-tabs-inner">
		<ul class="nav nav-tabs modern-tabs-industries industry-pages-tablist" id="industryPagesTab" role="tablist">
			@foreach($primaryIndustries as $slug => $page)
				<li class="nav-item" role="presentation">
					<a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $slug }}-tab" data-toggle="tab" href="#{{ $slug }}" role="tab" aria-controls="{{ $slug }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
						<i class="{{ $industryTabIcons[$slug] ?? 'icofont-building-alt' }}"></i>
						<strong>{{ $page['label'] }}</strong>
					</a>
				</li>
			@endforeach
			@if(count($secondaryIndustries))
				<li class="nav-item dropdown industry-more-dropdown" role="presentation">
					<a class="nav-link dropdown-toggle industry-more-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						<i class="icofont-ui-menu"></i>
						<strong>Read More</strong>
					</a>
					<div class="dropdown-menu industry-more-menu">
						@foreach($secondaryIndustries as $slug => $page)
							<a class="dropdown-item industry-tab-link" href="#{{ $slug }}" data-target-tab="{{ $slug }}">
								<i class="{{ $industryTabIcons[$slug] ?? 'icofont-building-alt' }}"></i>
								<span>{{ $page['label'] }}</span>
							</a>
						@endforeach
					</div>
				</li>
			@endif
		</ul>

		<div class="tab-content modern-tab-content industry-pages-tabcontent" id="industryPagesTabContent">
			@foreach($industryPages as $slug => $page)
				<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $slug }}" role="tabpanel" aria-labelledby="{{ $slug }}-tab">
					@include($page['view'])
				</div>
			@endforeach
		</div>
	</div>
</div>
