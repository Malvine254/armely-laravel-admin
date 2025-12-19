@extends('layouts.public')

@php($title = 'Services')

@section('content')
<div class="breadcrumbs overlay">
    <div class="container"><div class="bread-inner"><div class="row"><div class="col-12">
        <h2>Services</h2>
        <ul class="bread-list"><li><a href="/">Home</a></li><li><i class="icofont-simple-right"></i></li><li class="active">Services</li></ul>
    </div></div></div></div>
</div>

<section class="service-navigation">
    <div class="container">
        <div class="service-search-box"><input type="text" id="serviceSearch" placeholder="Search services..."><i class="fa fa-search"></i></div>
        <div class="category-filters">
            <button class="filter-btn active" data-filter="all">All <span class="service-count" id="count-all">0</span></button>
            <button class="filter-btn" data-filter="data">Data <span class="service-count" id="count-data">0</span></button>
            <button class="filter-btn" data-filter="digital">Digital <span class="service-count" id="count-digital">0</span></button>
            <button class="filter-btn" data-filter="ai">AI &amp; ML <span class="service-count" id="count-ai">0</span></button>
            <button class="filter-btn" data-filter="managed">Managed <span class="service-count" id="count-managed">0</span></button>
            <button class="filter-btn" data-filter="advisory">Advisory <span class="service-count" id="count-advisory">0</span></button>
        </div>
    </div>
</section>

<section class="pricing-table mt-5" id="services-list">
    <div class="container">
        <div class="row"><div class="col-lg-12"><div class="section-title"><h2>Empowering Your Tech Journey</h2><center><hr class="default-background hr"></center><p>Our experts provide tailored guidance in areas such as business planning, product development, marketing, financial management, and risk management, ensuring your company's competitiveness and sustainable growth.</p></div></div></div>
        <div id="noResults" class="no-results" style="display: none;"><i class="fa fa-search"></i><h3>No Services Found</h3><p>Try adjusting your search or filter to find what you're looking for.</p></div>
        <div class="row" id="servicesContainer">
            @foreach($services as $service)
                <div class="col-lg-4 col-md-12 col-12 single-table" data-title="{{ strtolower($service->title) }}">
                    <div class="single-table card-shadow default-background" style="max-block-size: 350px; min-block-size: 340px;">
                        <a class="text-light" href="{{ route('services.show', $service->url_name) }}">
                            <div class="table-head">
                                <div class="icon text-light"><i class="icofont text-light {{ $service->image }}"></i></div>
                                <h4 class="title text-light">{{ $service->title }}</h4>
                                <div class="price text-light"><p class="text-light">{{ Str::limit(strip_tags($service->body), 150) }}</p></div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    const searchInput = document.getElementById('serviceSearch');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const servicesContainer = document.getElementById('servicesContainer');
    const noResults = document.getElementById('noResults');

    const serviceMap = {
        'ai': ['ai consulting','ai advisory','generative','poc','copilot'],
        'data': ['fabric','data science','analytics','data strategy','databricks','snowflake','sql','warehouse'],
        'digital': ['api','powerapps','power automate','automate','virtual agent','power pages','dynamics','robotic','rpa','sharepoint'],
        'managed': ['sql server support','sql support','support','managed'],
        'advisory': ['advisory','freemium','starter']
    };

    let currentFilter = 'all';
    let searchTerm = '';

    function updateCounts() {
        const cards = servicesContainer.querySelectorAll('.single-table');
        const totals = {all: cards.length, data:0,digital:0,ai:0,managed:0,advisory:0};
        cards.forEach(card => {
            const title = card.dataset.title || '';
            Object.keys(serviceMap).forEach(cat => {
                if (serviceMap[cat].some(key => title.includes(key))) totals[cat]++;
            });
        });
        Object.entries(totals).forEach(([k,v]) => {
            const el = document.getElementById('count-' + k);
            if (el) el.textContent = v;
        });
    }

    function filterServices() {
        const cards = servicesContainer.querySelectorAll('.single-table');
        let visible = 0;
        cards.forEach(card => {
            const title = (card.dataset.title || '').toLowerCase();
            const matchesSearch = title.includes(searchTerm);
            const matchesFilter = currentFilter === 'all' || serviceMap[currentFilter].some(key => title.includes(key));
            const show = matchesSearch && matchesFilter;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    searchInput?.addEventListener('input', e => { searchTerm = e.target.value.toLowerCase(); filterServices(); });
    filterBtns.forEach(btn => btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = btn.dataset.filter;
        filterServices();
    }));

    updateCounts();
    filterServices();
})();
</script>
@endpush
@endsection
