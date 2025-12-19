{{-- Enhanced Search Modal --}}
<div class="modal-search-overlay" id="searchModal">
    <div class="search-modal-content">
        {{-- Header --}}
        <div class="search-modal-header">
            <h3><i class="fa fa-search"></i> Search Armely</h3>
            <button class="search-close-btn" aria-label="Close search">&times;</button>
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
                <i class="fa fa-search"></i>
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
