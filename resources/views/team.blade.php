@extends('layouts.public')

@section('title', 'Our Team - Armely')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Team</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Team</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Start service -->
<section class="services mt-5">
<div class="py-5 team4">
  <div class="container">
  	<div class="section-title">
		  <h2>Experienced & Professional Team</h2>
		  <center><hr class="default-background hr"></center>
		</div>
    @if(!empty($dbErrorMessage))
      <div class="row mb-3">
        <div class="col-12">
          <div class="alert alert-warning text-center" role="alert">
            <i class="icofont-warning-alt"></i> {{ $dbErrorMessage }}
          </div>
        </div>
      </div>
    @endif
    <div class="row justify-content-center mb-4">
      <div class="col-md-7 text-center">
        <h6 class="subtitle">You can rely on our amazing features list and also our customer services will be great experience for you without doubt and in no-time</h6>
      </div>
    </div>
    <style>
      .team-org-chart-wrapper {
        overflow: hidden;
        clear: both;
        padding-bottom: 150px;
        margin-bottom: 100px;
      }
      .org-chart {
        text-align: center;
        padding: 40px 20px;
        padding-bottom: 150px;
        margin-bottom: 100px;
      }
      .org-chart ul {
        padding-top: 20px;
        position: relative;
        transition: all 0.5s;
        list-style-type: none;
        margin: 0;
        padding-left: 0;
      }
      .org-chart li {
        float: left;
        text-align: center;
        list-style-type: none;
        position: relative;
        padding: 20px 5px 0 5px;
        transition: all 0.5s;
        margin: 0;
      }
      .org-chart li::before, .org-chart li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid;
        border-color: inherit;
        width: 50%;
        height: 20px;
      }
      .org-chart li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid;
        border-color: inherit;
      }
      .org-chart li:only-child::after, .org-chart li:only-child::before {
        display: none;
      }
      .org-chart li:only-child {
        padding-top: 0;
      }
      .org-chart li:first-child::before, .org-chart li:last-child::after {
        border: 0 none;
      }
      .org-chart li:last-child::before {
        border-right: 2px solid;
        border-color: inherit;
        border-radius: 0 5px 0 0;
      }
      .org-chart li:first-child::after {
        border-radius: 5px 0 0 0;
      }
      .org-chart ul ul::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        border-left: 2px solid;
        border-color: inherit;
        width: 0;
        height: 20px;
      }
      .org-chart .org-member {
        border: 2px solid;
        padding: 10px;
        display: inline-block;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        transition: all 0.3s;
        min-width: 200px;
        max-width: 250px;
        position: relative;
      }
      .org-chart .org-member:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
      }
      .org-chart .org-member .bio-tooltip {
        display: none;
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 12px 15px;
        border-radius: 6px;
        font-size: 12px;
        line-height: 1.4;
        width: 200px;
        text-align: center;
        margin-bottom: 10px;
        z-index: 1000;
        white-space: normal;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      }
      .org-chart .org-member .bio-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #333;
      }
      .org-chart .org-member:hover .bio-tooltip {
        display: block;
      }
      .org-chart .org-member img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 10px;
      }
      .org-chart .org-member .avatar-initials {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: bold;
        color: white;
        margin-bottom: 10px;
        text-transform: uppercase;
      }
      .org-chart .org-member h5 {
        font-size: 14px;
        font-weight: bold;
        margin: 5px 0;
      }
      .org-chart .org-member .position {
        font-size: 12px;
        color: #666;
        margin-bottom: 8px;
      }
      .org-chart .org-member .social-links a {
        font-size: 16px;
        margin: 0 5px;
      }
      @media (max-width: 768px) {
        .org-chart li {
          float: none;
          padding: 10px 0;
        }
        .org-chart li::before, .org-chart li::after,
        .org-chart ul ul::before {
          display: none;
        }
        .team-org-chart-wrapper {
          padding-bottom: 100px;
          margin-bottom: 50px;
        }
      }
    </style>

    <div class="team-org-chart-wrapper">
    <div class="org-chart">
      @php
        $displayedLevels = array_filter($hierarchy, function($members) {
          return count($members) > 0;
        });
      @endphp
      
      @foreach($displayedLevels as $level => $members)
        <div class="mb-5 default-color">
          <div class="default-background text-white p-3 rounded d-inline-block font-weight-bold" style="padding: 15px 30px !important; font-size: 18px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); margin-bottom: 40px;">
            <i class="fa fa-sitemap mr-2"></i>{{ $level }}
          </div>
          
          <ul class="p-0">
            @foreach($members as $member)
              <li>
                <div class="org-member default-color">
                  @php
                    $imagePath = public_path('images/team/' . $member->image);
                    $imageExists = !empty($member->image) && file_exists($imagePath);
                    
                    // Generate initials
                    $nameParts = explode(' ', trim($member->name));
                    $initials = '';
                    if (count($nameParts) >= 2) {
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1));
                    } else {
                        $initials = strtoupper(substr($member->name, 0, 2));
                    }
                  @endphp
                  
                  @if($imageExists)
                    <a target="_blank" href="{{ asset('images/team/' . $member->image) }}">
                      <img src="{{ asset('images/team/' . $member->image) }}" alt="{{ $member->name }}" />
                    </a>
                  @else
                    <div class="avatar-initials default-background">
                      {{ $initials }}
                    </div>
                  @endif
                  
                  <h5 class="default-color">{{ ucwords(strtolower($member->name)) }}</h5>
                  <div class="position">{{ $member->position }}</div>
                  
                  <!-- Bio Tooltip -->
                  <div class="bio-tooltip">
                    {{ $member->bio }}
                  </div>
                  
                  <div class="social-links">
                    @if($member->facebook)
                      <a href="{{ $member->facebook }}" target="_blank" title="Facebook" class="default-color"><i class="icon-social-facebook"></i></a>
                    @endif
                    @if($member->x)
                      <a href="{{ $member->x }}" target="_blank" title="X" class="default-color"><i class="fab fa-x-twitter"></i></a>
                    @endif
                    @if($member->instagram)
                      <a href="{{ $member->instagram }}" target="_blank" title="Instagram" class="default-color"><i class="icon-social-instagram"></i></a>
                    @endif
                    @if($member->linkedin)
                      <a href="{{ $member->linkedin }}" target="_blank" title="LinkedIn" class="default-color"><i class="icon-social-linkedin"></i></a>
                    @endif
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        </div>
      @endforeach

      @if(array_sum(array_map('count', $hierarchy)) == 0)
        <div class="col-12">
          <div class="alert alert-info text-center">
            <i class="icofont-info-circle"></i> Team members will be displayed soon.
          </div>
        </div>
      @endif
      
      <div style="clear: both; height: 1px;"></div>
    </div>
    </div>
  </div>
</div>
</section>
<!--/ End service -->
@endsection
