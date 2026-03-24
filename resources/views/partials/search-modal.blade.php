{{-- Enhanced Search Modal --}}
<div class="modal-search-overlay" id="searchModal">
    <div class="search-modal-content">

        {{-- Header --}}
        <div class="search-modal-header">
            <div class="search-modal-header-left">
                <div class="search-modal-icon-wrap">
                    <i class="fa fa-search"></i>
                </div>
                <div class="search-modal-header-text">
                    <h3>Search Armely</h3>
                    <p>Find services, articles, events &amp; more</p>
                </div>
            </div>
            <button class="search-close-btn" aria-label="Close search">
                <i class="fa fa-times"></i>
            </button>
        </div>

        {{-- Search Input --}}
        <div class="search-input-container">
            <div class="search-input-wrapper">
                <i class="fa fa-search search-icon"></i>
                <input
                    type="text"
                    id="globalSearchInput"
                    placeholder="Search for services, articles, pages..."
                    autocomplete="off"
                    aria-label="Search input"
                >
                <div class="search-loading-spinner" id="searchLoadingSpinner"></div>
            </div>
        </div>

        {{-- Results Container --}}
        <div class="search-results-container" id="searchResultsContainer">
            <div class="search-empty-state">
                <div class="search-empty-icon">
                    <i class="fa fa-search"></i>
                </div>
                <h4>Start Searching</h4>
                <p>Enter keywords to find content across all pages</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="search-stats" id="searchStats" style="display: none;"></div>
    </div>
</div>

{{-- Update search trigger in header topbar --}}
<style>
    .search-trigger {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .search-trigger:hover {
        opacity: 0.8;
    }
</style>
