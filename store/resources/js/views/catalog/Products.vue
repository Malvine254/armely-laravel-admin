<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <!-- Page Title -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">B2B Procurements</h1>
        <p class="text-gray-600 text-lg">Browse our complete catalog of enterprise solutions</p>
      </div>

      <!-- Search Bar -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-4">
          <div class="flex-1">
            <div class="relative">
              <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="searchQuery"
                @input="handleSearchInput"
                @focus="handleSearchFocus"
                @blur="handleSearchBlur"
                @keydown.down.prevent="highlightNextSuggestion"
                @keydown.up.prevent="highlightPreviousSuggestion"
                @keydown.esc="dismissSearchSuggestions"
                @keydown.enter.prevent="handleSearchEnter"
                type="text"
                placeholder="Search products..."
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition"
              >

              <div
                v-if="showSearchSuggestions && searchSuggestionItems.length > 0"
                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
              >
                <button
                  v-for="(item, index) in searchSuggestionItems"
                  :key="`${item.term}-${item.source}-${index}`"
                  type="button"
                  class="w-full text-left px-3 py-2.5 border-b border-gray-100 last:border-b-0 transition flex items-center justify-between"
                  :class="index === activeSuggestionIndex ? 'bg-blue-50' : 'hover:bg-gray-50'"
                  @mousedown.prevent="applySearchSuggestion(item.term)"
                >
                  <span class="text-sm text-gray-800 truncate pr-3">{{ item.term }}</span>
                  <span class="text-[10px] uppercase tracking-wide text-gray-500">{{ item.source }}</span>
                </button>
              </div>
            </div>
          </div>
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-200 transition"
          >
            Clear Search
          </button>
          <button @click="performSearch" class="px-6 py-3 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
            Search
          </button>
        </div>
      </div>

      <!-- Content Layout: Sidebar + Products Grid -->
      <div class="flex gap-8 lg:gap-6">
        <!-- Filters Sidebar -->
        <div class="hidden lg:block lg:w-80 flex-shrink-0">
          <FilterSidebar 
            :vendors="availableVendors" 
            :categories="availableCategories"
            :active-filters="currentFilters"
            :lifecycle-options="lifecycleOptions"
            :media-options="reviewRatingOptions"
            @filter-change="handleFilterChange"
          />
        </div>

        <!-- Products Section -->
        <div class="flex-1 min-w-0">
          <!-- Loading State (full load only, not page changes) -->
          <div v-if="loading && !pageLoading" class="text-center py-9">
            <div class="inline-block">
              <div class="w-12 h-12 border-4 border-gray-200 rounded-full animate-spin" style="border-top-color: #2F5597;"></div>
              <p class="mt-4 text-gray-600 font-semibold">Loading products...</p>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
            <div class="flex gap-4">
              <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div>
                <h3 class="text-lg font-bold text-red-900 mb-1">Error Loading Products</h3>
                <p class="text-red-700">{{ error }}</p>
                <button @click="performSearch" class="mt-3 px-4 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700 transition">
                  Retry
                </button>
              </div>
            </div>
          </div>

          <!-- Results Summary -->
          <div v-else-if="!loading || pageLoading" class="">
            <!-- Page loading overlay -->
            <div v-if="pageLoading" class="fixed top-0 left-0 right-0 z-50">
              <div class="h-1 bg-gray-200 w-full">
                <div class="h-1 rounded-r animate-pulse" style="background-color: #2F5597; width: 100%;"></div>
              </div>
            </div>
            <div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
              <p class="text-gray-600 font-medium">Showing <span class="font-bold" style="color: #2F5597;">{{ Math.min(ITEMS_PER_PAGE, totalProducts) }}</span> of <span class="font-bold" style="color: #2F5597;">{{ totalProducts }}</span> products</p>
              <span class="text-gray-600 text-sm">Page <span class="font-bold" style="color: #2F5597;">{{ currentPage }}</span> of <span class="font-bold" style="color: #2F5597;">{{ totalPages }}</span></span>
            </div>

            <!-- Empty State -->
            <div v-if="totalProducts === 0" class="text-center py-9 bg-white rounded-xl border border-gray-200">
              <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
              </svg>
              <h3 class="text-xl font-bold text-gray-900 mb-2">No Products Found</h3>
              <p class="text-gray-600 mb-6">Try adjusting your search or filters</p>
              <button @click="resetFilters" class="px-6 py-2 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
                Clear Filters
              </button>
            </div>

            <!-- Products Grid (3 columns on desktop, 2 on tablet, 1 on mobile) -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 transition-opacity duration-200" :class="{ 'opacity-50 pointer-events-none': pageLoading }">
              <div v-for="product in paginatedProducts" :key="product.productId" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-lg transition" style="border: 1px solid rgb(229, 231, 235);" @mouseenter="$event.currentTarget.style.borderColor='#cce4f5'" @mouseleave="$event.currentTarget.style.borderColor='rgb(229, 231, 235)'">
                <!-- Product Image -->
                <div class="bg-gradient-to-br from-gray-200 to-gray-300 h-40 flex items-center justify-center transition relative overflow-hidden" style="background: linear-gradient(135deg, rgb(229, 231, 235), rgb(209, 213, 219));">
                  <!-- Actual Product Image if available -->
                  <img 
                    v-if="product.productImages && product.productImages[0]"
                    :src="product.productImages[0].imageUrl || product.productImages[0]"
                    :alt="product.productName"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    @error="event => event.target.style.display = 'none'"
                  />
                  
                  <!-- Fallback: Animated background + Icon -->
                  <template v-else>
                    <!-- Animated background -->
                    <div class="absolute inset-0 opacity-10">
                      <div class="absolute top-2 right-2 w-12 h-12 bg-blue-400 rounded-full"></div>
                      <div class="absolute bottom-4 left-2 w-8 h-8 bg-blue-300 rounded-full"></div>
                    </div>
                    
                    <!-- Product Icon based on type -->
                    <div class="relative z-10 text-center">
                      <svg v-if="getProductIcon(product.productName) === 'server'" class="w-16 h-16 mx-auto mb-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-3 8h-2V5h2v6z"/>
                      </svg>
                      <svg v-else-if="getProductIcon(product.productName) === 'cloud'" class="w-16 h-16 mx-auto mb-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4c-1.48 0-2.85.43-4.01 1.17l1.46 1.46C10.21 5.23 11.08 5 12 5c3.04 0 5.5 2.46 5.5 5.5v.5H19c2.05 0 3.71 1.66 3.71 3.71 0 1.71-1.04 2.86-2.36 3.41z"/>
                      </svg>
                      <svg v-else-if="getProductIcon(product.productName) === 'database'" class="w-16 h-16 mx-auto mb-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3c-4.97 0-9 2.16-9 4.5S7.03 12 12 12s9-2.16 9-4.5S16.97 3 12 3zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3zm0 7c-4.97 0-9 2.16-9 4.5S7.03 24 12 24s9-2.16 9-4.5-4.03-4.5-9-4.5zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3z"/>
                      </svg>
                      <svg v-else class="w-16 h-16 mx-auto mb-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                      </svg>
                    </div>
                  </template>
                </div>

                <!-- Product Info -->
                <div class="p-4">
                  <div class="flex items-start justify-between mb-2">
                    <h3
                      class="text-sm font-semibold text-gray-900 line-clamp-2"
                      :title="buildProductHoverDetails(product)"
                    >{{ product.productName }}</h3>
                    <span v-if="product.discontinueProduct" class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">EOL</span>
                    <span v-else class="ml-2 px-2 py-1 text-xs font-semibold rounded" style="background-color: #cce4f4; color: #2F5597;">Active</span>
                  </div>
                  <div class="flex items-center justify-between gap-3 text-xs text-gray-600 mb-3">
                    <p class="truncate" :title="`SKU: ${getProductSku(product)}`">SKU: {{ getProductSku(product) }}</p>
                    <p class="truncate text-right" :title="`Vendor: ${getProductVendor(product)}`">Vendor: {{ getProductVendor(product) }}</p>
                  </div>

                  <!-- Reviews -->
                  <div class="flex items-center gap-1 mb-3">
                    <svg
                      v-for="star in 5"
                      :key="`rating-${product.productId}-${star}`"
                      class="w-3.5 h-3.5"
                      :class="star <= Math.round(getReviewStatsForProduct(product.productId).average) ? 'text-yellow-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-xs text-gray-500 ml-1">
                      {{ getReviewStatsForProduct(product.productId).total > 0
                        ? `${getReviewStatsForProduct(product.productId).average.toFixed(1)} (${getReviewStatsForProduct(product.productId).total})`
                        : 'No reviews' }}
                    </span>
                  </div>
                  
                  <!-- Pricing -->
                  <div v-if="product.productPrice && product.productPrice.length > 0" class="mb-4">
                    <p class="text-2xl font-bold" style="color: #2F5597;">{{ formatCatalogPrice(product.productPrice[0].rsPrice) }}</p>
                    <p class="text-xs text-gray-600">Min Qty: {{ product.productPrice[0].minQty }}</p>
                  </div>

                  <!-- Features -->
                  <div class="mb-4 flex flex-wrap gap-1">
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ product.billingModel || 'N/A' }}</span>
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ product.billingFrequency || 'N/A' }}</span>
                  </div>

                  <!-- Actions -->
                  <div class="flex gap-2 w-full">
                    <button @click="viewProductDetails(product)" class="flex-1 px-3 py-2 text-white text-sm font-semibold rounded-lg transition inline-flex items-center justify-center gap-1" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                      </svg>
                      <span>View</span>
                    </button>
                    <button @click="addToQuote(product)" class="px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'" title="Add to Quote" aria-label="Add to Quote">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5.4 5M7 13l-1.2 6.4A1 1 0 006.8 21h10.4a1 1 0 001-.8L20 13M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                      </svg>
                    </button>
                    <button @click="toggleFavorite(product)" class="px-3 py-2 rounded-lg transition border" :style="isFavorite(product.productId) ? { backgroundColor: '#cce4f4', borderColor: '#2F5597', color: '#2F5597' } : { borderColor: '#d1d5db', color: '#4b5563' }" :title="isFavorite(product.productId) ? 'Remove from Favorites' : 'Add to Favorites'">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                      </svg>
                    </button>
                    <button @click="openShareModal(product)" class="px-3 py-2 rounded-lg transition border border-gray-300 text-gray-600 hover:bg-gray-50" title="Share Product">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C9.886 12.511 11.326 12 12.889 12c2.87 0 5.322 1.723 6.296 4.182m-16.338 0A6.986 6.986 0 019.111 12c1.563 0 3.003.511 4.205 1.342M15 6a3 3 0 11-6 0 3 3 0 016 0zm6 14a2 2 0 11-4 0 2 2 0 014 0zM7 20a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="flex flex-col sm:flex-row items-center justify-center gap-2 mt-8 p-6 bg-white rounded-xl border border-gray-200">
              <!-- Previous Button -->
              <button
                @click="previousPage"
                :disabled="currentPage === 1"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white transition"
              >
                ← Previous
              </button>

              <!-- Page Numbers -->
              <div class="flex gap-1 flex-wrap justify-center">
                <button
                  v-for="page in pageNumbers"
                  :key="page"
                  @click="goToPage(page)"
                  :class="['px-3 py-2 rounded-lg transition', page === currentPage ? 'text-white font-semibold' : 'border border-gray-300 text-gray-700 hover:bg-gray-50']"
                  :style="page === currentPage ? { backgroundColor: '#2F5597' } : {}"
                >
                  {{ page }}
                </button>
              </div>

              <!-- Next Button -->
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white transition"
              >
                Next →
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showShareModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-900/45" @click="closeShareModal"></div>
        <div class="relative w-full max-w-lg rounded-2xl border bg-white shadow-2xl" style="border-color:#cfe0f5;">
          <div class="px-5 py-4 border-b" style="border-color:#e2e8f0;">
            <h3 class="text-lg font-bold" style="color:#2F5597;">Share Product</h3>
            <p class="text-sm text-slate-600 mt-1">{{ sharingProduct?.productName || 'Selected product' }}</p>
          </div>

          <div class="p-5 space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Recipient Email (optional)</label>
              <input
                v-model="shareRecipientEmail"
                type="email"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                style="border-color:#cbd5e1;"
                placeholder="user@company.com"
              >
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Note (optional)</label>
              <textarea
                v-model="shareNote"
                rows="3"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                style="border-color:#cbd5e1;"
                placeholder="Add a message for the recipient"
              ></textarea>
            </div>

            <div v-if="shareGeneratedLink" class="rounded-lg border p-3" style="border-color:#bfdbfe;background:#eff6ff;">
              <p class="text-xs font-semibold text-slate-700 mb-1">Share Link</p>
              <p class="text-xs break-all text-slate-700">{{ shareGeneratedLink }}</p>
              <div class="mt-3 flex flex-wrap gap-2">
                <button @click="copyShareGeneratedLink" type="button" class="px-3 py-2 text-xs font-semibold rounded-lg text-white" style="background-color:#2F5597;">Copy Link</button>
                <button @click="sendShareLinkByEmail" type="button" class="px-3 py-2 text-xs font-semibold rounded-lg border" style="border-color:#2F5597;color:#2F5597;">Send to Email</button>
              </div>
            </div>
          </div>

          <div class="px-5 py-4 border-t flex justify-end gap-2" style="border-color:#e2e8f0;">
            <button @click="closeShareModal" type="button" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">Close</button>
            <button @click="submitProductShare" type="button" :disabled="shareSubmitting" class="px-4 py-2 text-sm font-semibold rounded-lg text-white disabled:opacity-60" style="background-color:#2F5597;">
              {{ shareSubmitting ? 'Generating...' : (shareGeneratedLink ? 'Regenerate Link' : 'Generate Link') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useCartStore } from '../../stores/cartStore'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useAuthStore } from '../../stores/authStore'
import { usePricingSettings } from '../../composables/usePricingSettings'
import { trackSearchTerm, hasTrackingConsent, getSearchProfileTerms, getSearchSuggestions } from '../../services/searchInsights'
import api from '../../services/api'
import Navbar from '../../components/Navbar.vue'
import FilterSidebar from '../../components/FilterSidebar.vue'

const router = useRouter()
const route = useRoute()
const toastStore = useToastStore()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const { loadPricingSettings, getCatalogPriceWithRules, convertFromUsd, formatWithCurrency } = usePricingSettings()
const ITEMS_PER_PAGE = 9
const API_PAGE_SIZE = 100
const SEARCH_TRACK_DEBOUNCE_MS = 15000
const PROFILE_TERM_LIMIT = 25
const LOCAL_SEARCH_HISTORY_KEY = 'armely_products_search_history'
const LOCAL_SEARCH_HISTORY_LIMIT = 12
const TOP_VENDOR_DISPLAY_LIMIT = 40
const DEFAULT_VENDOR_SCOPE_LIMIT = 12
const DEFAULT_BROWSE_MIN_PRICE = 200
const CURATED_CACHE_VERSION = 2
const ENABLE_SERVER_PREFETCH = false
const ENABLE_VENDOR_COUNTS_API = false
const CURATED_VENDOR_ALLOWLIST = [
  'CISCO SYSTEMS',
  'HEWLETT PACKARD ENTERPRISE',
  'NVIDIA CORPORATION',
  'LENOVO DATA CENTER',
  'MICROSOFT CORPORATION',
  'HP INC.',
  'VEEAM SOFTWARE CORPORATION',
  'LENOVO',
  'FORTINET INC.',
  'APC BY SCHNEIDER ELECTRIC',
  'STARTECH.COM',
  'BELKIN INTERNATIONAL INC',
  'SAMSUNG',
  'DELL MARKETING L.P.',
  'ASUS',
  'INTEL',
  'INTELLINET',
  'LOGITECH',
  'JABRA',
  'KINGSTON',
  'KENSINGTON COMPUTER',
  'ASUS SBG COMMERCIAL',
  'INTELLIGENT SECURITY SYSTEMS C',
  'NETGEAR',
  'WESTERN DIGITAL',
  'AMD',
  'ACER AMERICA CORPORATION',
  'BROADCOM',
  'INTELLIGENT COMPUTER SOLUTIONS',
  'CROWDSTRIKE, INC.',
  'ASUSTOR AMERICA INC',
  'Q6 INTELLIGENCE, LLC',
  'SEAGATE TECHNOLOGY LLC',
  'PALO ALTO NETWORKS',
]

const CURATED_VENDOR_ALIAS_MAP = {
  'HEWLETT PACKARD ENTERPRISE': ['HEWLETT PACKARD ENTERPRISE COM'],
  'MICROSOFT CORPORATION': ['MICROSOFT', 'MICROSOFT CORP', 'MICROSOFT RETAIL'],
  SAMSUNG: [
    'SAMSUNG ELECTRONICS AMERICA, I',
    'SAMSUNG ELECTRONICS AMERICA',
    'SAMSUNG ELECTRONICS CO.',
    'SAMSUNG ELECTRONICS AMERICA IN',
    'SAMSUNG ELECTRONICS AMERICA (W',
  ],
  'CISCO SYSTEMS': ['CISCO SYSTEMS CAPITAL REMARKET'],
  'DELL MARKETING L.P.': ['DELL MARKETING LP'],
  'ACER AMERICA CORPORATION': ['ACER', 'ACER AMERICA'],
  'SEAGATE TECHNOLOGY LLC': ['STRATEGIC SOURCING -SEAGATE'],
}

const CATEGORY_GROUP_DEFINITIONS = [
  {
    name: 'Laptops & PCs',
    value: 'Laptops & PCs',
    keywords: ['laptop', 'notebook', 'desktop', 'workstation', 'all-in-one', 'mini pc'],
  },
  {
    name: 'Monitors & Docks',
    value: 'Monitors & Docks',
    keywords: ['monitor', 'display', 'dock', 'docking', 'usb-c hub', 'port replicator'],
  },
  {
    name: 'Printing & Supplies',
    value: 'Printing & Supplies',
    keywords: ['printer', 'toner', 'ink', 'cartridge', 'drum', 'laserjet', 'deskjet'],
  },
  {
    name: 'Networking Gear',
    value: 'Networking Gear',
    keywords: ['router', 'switch', 'access point', 'wifi', 'firewall', 'gateway', 'mesh'],
  },
  {
    name: 'Peripherals',
    value: 'Peripherals',
    keywords: ['keyboard', 'mouse', 'headset', 'webcam', 'speakerphone', 'microphone'],
  },
  {
    name: 'Software & Services',
    value: 'Software & Services',
    keywords: ['license', 'software', 'subscription', 'antivirus', 'security', 'backup', 'microsoft 365', 'office 365'],
  },
]

// Only vendors matching a term below will appear in the sidebar.
// To add a new brand, append its uppercase display name here.
const PREFERRED_VENDOR_TERMS = [
  'MICROSOFT',
  'CISCO',
  'HEWLETT PACKARD',
  'HP INC',
  'LENOVO',
  'DELL',
  'NVIDIA',
  'FORTINET',
  'VEEAM',
  'STARTECH',
  'LOGITECH',
  'APPLE',
  'AMD',
  'INTEL',
  'SAMSUNG',
  'UBIQUITI',
  'NETGEAR',
  'PALO ALTO',
  'JUNIPER',
  'ARUBA',
  'CROWDSTRIKE',
  'VMWARE',
  'BROADCOM',
  'ACER',
  'ASUS',
  'JABRA',
  'PLANTRONICS',
  'POLY ',
  'SEAGATE',
  'WESTERN DIGITAL',
  'KINGSTON',
  'CRUCIAL',
  'CORSAIR',
  'BELKIN',
  'KENSINGTON',
  'APC BY',
  'APC ',
]

// Vendors used for implicit browse scope when user has not selected a vendor
// and the live vendor list has not loaded yet.
const FALLBACK_VENDOR_SCOPE = [
  'CISCO SYSTEMS',
  'HEWLETT PACKARD ENTERPRISE',
  'NVIDIA CORPORATION',
  'LENOVO DATA CENTER',
  'MICROSOFT CORPORATION',
  'HP INC.',
  'VEEAM SOFTWARE CORPORATION',
  'FORTINET INC.',
  'STARTECH.COM',
  'LOGITECH',
  'DELL MARKETING L.P.',
  'LENOVO',
]

const products = ref([])
const serverTotal = ref(0)
const serverPaged = ref(false)
const loading = ref(false)
const pageLoading = ref(false)
const error = ref('')
const searchQuery = ref('')
const currentPage = ref(1)
const lastTrackedTerm = ref('')
const lastTrackedAt = ref(0)
const reviewStatsByProduct = ref({})
const localSearchHistory = ref([])
const showSearchSuggestions = ref(false)
const activeSuggestionIndex = ref(-1)
const searchSuggestionItems = ref([])
const showShareModal = ref(false)
const sharingProduct = ref(null)
const shareRecipientEmail = ref('')
const shareNote = ref('')
const shareGeneratedLink = ref('')
const shareSubmitting = ref(false)

const availableVendors = ref([])
const allVendors = ref([])
const availableCategories = ref([])

const currentFilters = ref({
  priceMin: 0,
  priceMax: 10000,
  partNumber: '',
  vendors: [],
  categories: [],
  lifecycleStatuses: [],
  mediaStatuses: []
})

const requiresClientForFilters = (filters) => {
  return (
    (Array.isArray(filters?.categories) && filters.categories.length > 0)
    || String(filters?.partNumber || '').trim().length > 0
    || (Array.isArray(filters?.lifecycleStatuses) && filters.lifecycleStatuses.length > 0)
    || (Array.isArray(filters?.mediaStatuses) && filters.mediaStatuses.length > 0)
  )
}

const isEolProduct = (product) => {
  const value = product?.discontinueProduct
  if (typeof value === 'boolean') return value
  const normalized = String(value || '').toLowerCase().trim()
  return normalized === 'true' || normalized === '1' || normalized === 'yes' || normalized === 'y'
}

const lifecycleOptions = computed(() => {
  let activeCount = 0
  let eolCount = 0

  products.value.forEach((product) => {
    if (isEolProduct(product)) {
      eolCount += 1
    } else {
      activeCount += 1
    }
  })

  return [
    { name: 'Active', count: activeCount },
    { name: 'End of Life', count: eolCount }
  ]
})

const getProductReviewStats = (productId) => {
  const key = String(productId || '')
  const stats = reviewStatsByProduct.value[key]
  if (!stats) {
    return { total: 0, average: 0 }
  }

  return {
    total: Number(stats.total || 0),
    average: Number(stats.average || 0)
  }
}

const reviewRatingOptions = computed(() => {
  let fiveStar = 0
  let fourPlus = 0
  let threePlus = 0
  let hasReviews = 0

  products.value.forEach((product) => {
    const stats = getProductReviewStats(product.productId)
    if (stats.total > 0) {
      hasReviews += 1
    }
    if (stats.average >= 4.5) {
      fiveStar += 1
    }
    if (stats.average >= 4) {
      fourPlus += 1
    }
    if (stats.average >= 3) {
      threePlus += 1
    }
  })

  return [
    { name: '5 Stars', count: fiveStar },
    { name: '4 Stars & Up', count: fourPlus },
    { name: '3 Stars & Up', count: threePlus },
    { name: 'Has Reviews', count: hasReviews },
  ]
})

const normalizeSearchText = (value) => String(value || '').toLowerCase().trim().replace(/\s+/g, ' ')

const normalizeSearchHistoryTerm = (value) => String(value || '').trim().replace(/\s+/g, ' ')

const loadLocalSearchHistory = () => {
  if (typeof window === 'undefined') return
  try {
    const parsed = JSON.parse(localStorage.getItem(LOCAL_SEARCH_HISTORY_KEY) || '[]')
    if (Array.isArray(parsed)) {
      localSearchHistory.value = parsed
        .map((entry) => normalizeSearchHistoryTerm(entry))
        .filter((entry) => entry.length > 1)
        .slice(0, LOCAL_SEARCH_HISTORY_LIMIT)
    }
  } catch (error) {
    localSearchHistory.value = []
  }
}

const persistLocalSearchHistory = (term) => {
  const normalized = normalizeSearchHistoryTerm(term)
  if (normalized.length < 2 || typeof window === 'undefined') return

  const withoutTerm = localSearchHistory.value.filter(
    (item) => normalizeSearchHistoryTerm(item).toLowerCase() !== normalized.toLowerCase()
  )

  localSearchHistory.value = [normalized, ...withoutTerm].slice(0, LOCAL_SEARCH_HISTORY_LIMIT)
  localStorage.setItem(LOCAL_SEARCH_HISTORY_KEY, JSON.stringify(localSearchHistory.value))
}

const buildSearchSuggestionItems = (input = '') => {
  const normalizedInput = normalizeSearchText(input)
  const fromHistory = localSearchHistory.value
    .filter((term) => {
      if (!normalizedInput) return true
      return normalizeSearchText(term).includes(normalizedInput)
    })
    .map((term) => ({ term, source: 'history' }))

  const insightSuggestions = getSearchSuggestions(input)
  const fromInsights = [
    ...(insightSuggestions.recommended || []).map((entry) => ({ term: entry.term, source: 'recommended' })),
    ...(insightSuggestions.recent || []).map((entry) => ({ term: entry.term, source: 'recent' })),
    ...(insightSuggestions.popular || []).map((entry) => ({ term: entry.term, source: 'popular' })),
  ]

  const merged = [...fromHistory, ...fromInsights]
  const seen = new Set()
  const unique = []

  merged.forEach((item) => {
    const normalized = normalizeSearchText(item.term)
    if (!normalized || seen.has(normalized)) return
    seen.add(normalized)
    unique.push({ term: normalizeSearchHistoryTerm(item.term), source: item.source })
  })

  searchSuggestionItems.value = unique.slice(0, 8)
  activeSuggestionIndex.value = -1
}

const handleSearchInput = () => {
  buildSearchSuggestionItems(searchQuery.value)
  showSearchSuggestions.value = searchSuggestionItems.value.length > 0
}

const handleSearchFocus = () => {
  buildSearchSuggestionItems(searchQuery.value)
  showSearchSuggestions.value = searchSuggestionItems.value.length > 0
}

const handleSearchBlur = () => {
  setTimeout(() => {
    showSearchSuggestions.value = false
    activeSuggestionIndex.value = -1
  }, 120)
}

const dismissSearchSuggestions = () => {
  showSearchSuggestions.value = false
  activeSuggestionIndex.value = -1
}

const applySearchSuggestion = async (term) => {
  searchQuery.value = normalizeSearchHistoryTerm(term)
  dismissSearchSuggestions()
  await performSearch(true)
}

const highlightNextSuggestion = () => {
  if (!showSearchSuggestions.value || searchSuggestionItems.value.length === 0) return
  activeSuggestionIndex.value = (activeSuggestionIndex.value + 1) % searchSuggestionItems.value.length
}

const highlightPreviousSuggestion = () => {
  if (!showSearchSuggestions.value || searchSuggestionItems.value.length === 0) return
  if (activeSuggestionIndex.value <= 0) {
    activeSuggestionIndex.value = searchSuggestionItems.value.length - 1
    return
  }
  activeSuggestionIndex.value -= 1
}

const handleSearchEnter = async () => {
  if (
    showSearchSuggestions.value
    && activeSuggestionIndex.value >= 0
    && activeSuggestionIndex.value < searchSuggestionItems.value.length
  ) {
    const selected = searchSuggestionItems.value[activeSuggestionIndex.value]
    if (selected?.term) {
      await applySearchSuggestion(selected.term)
      return
    }
  }

  dismissSearchSuggestions()
  await performSearch(true)
}

const getProductSearchBlob = (product) => {
  const categories = Array.isArray(product.productCategories)
    ? product.productCategories
      .map((cat) => (typeof cat === 'object' ? cat.categoryName : cat))
      .filter(Boolean)
      .join(' ')
    : ''

  return normalizeSearchText([
    product.productName,
    product.vendorId,
    product.mfgPartNo,
    product.billingModel,
    product.billingFrequency,
    categories,
  ].join(' '))
}

const normalizeVendorKey = (value) => String(value || '')
  .toUpperCase()
  .replace(/[^A-Z0-9\s]/g, ' ')
  .replace(/\s+/g, ' ')
  .trim()

const curatedVendorAliasLookup = (() => {
  const lookup = new Map()
  Object.entries(CURATED_VENDOR_ALIAS_MAP).forEach(([canonical, aliases]) => {
    const canonicalKey = normalizeVendorKey(canonical)
    lookup.set(canonicalKey, canonicalKey)
    ;(aliases || []).forEach((alias) => {
      lookup.set(normalizeVendorKey(alias), canonicalKey)
    })
  })
  return lookup
})()

const toCanonicalVendorKey = (value) => {
  const key = normalizeVendorKey(value)
  return curatedVendorAliasLookup.get(key) || key
}

const getVendorCountForKey = (vendorKey, vendorCountMap) => {
  if (!vendorKey) {
    return 0
  }

  const exact = vendorCountMap.get(vendorKey)
  if (typeof exact === 'number') {
    return exact
  }

  // Fallback for naming variants, e.g. "MICROSOFT" vs "MICROSOFT CORPORATION".
  let count = 0
  for (const [productVendorKey, productVendorCount] of vendorCountMap.entries()) {
    if (productVendorKey.startsWith(`${vendorKey} `) || vendorKey.startsWith(`${productVendorKey} `)) {
      count += productVendorCount
      continue
    }

    if (vendorKey.length >= 8 && productVendorKey.includes(vendorKey)) {
      count += productVendorCount
    }
  }

  return count
}

const selectTopDisplayVendors = (vendors = [], selected = []) => {
  const source = Array.isArray(vendors) ? [...vendors] : []
  const selectedSet = new Set((selected || []).map((name) => normalizeVendorKey(name)))
  const curatedVendorSet = new Set(CURATED_VENDOR_ALLOWLIST.map((name) => normalizeVendorKey(name)))

  source.sort((a, b) => {
    if (b.count !== a.count) return b.count - a.count
    return a.name.localeCompare(b.name)
  })

  const chosen = []
  const chosenKeys = new Set()

  const tryPush = (vendor) => {
    const key = normalizeVendorKey(vendor?.name || vendor?.value)
    if (!key || chosenKeys.has(key)) return
    chosen.push(vendor)
    chosenKeys.add(key)
  }

  // Show only vendors that match a preferred brand term.
  // This prevents niche/high-volume vendors (SOPHOS, EXTREME, ADD-ON, etc.)
  // from flooding the sidebar.
  source.forEach((vendor) => {
    const vendorKey = normalizeVendorKey(vendor.name || vendor.value)
    if (!vendorKey) return
    if (curatedVendorSet.has(vendorKey)) {
      tryPush(vendor)
    }
  })

  // Backward-compatible fallback for legacy vendor naming variations.
  if (chosen.length === 0) {
    source.forEach((vendor) => {
      const vendorKey = normalizeVendorKey(vendor.name || vendor.value)
      if (!vendorKey) return
      if (PREFERRED_VENDOR_TERMS.some((term) => vendorKey.includes(term.toUpperCase()))) {
        tryPush(vendor)
      }
    })
  }

  // Always keep any already-selected vendor visible even if not in preferred list.
  source.forEach((vendor) => {
    const vendorKey = normalizeVendorKey(vendor.name || vendor.value)
    if (selectedSet.has(vendorKey)) {
      tryPush(vendor)
    }
  })

  return chosen
}

const getCuratedCategoryForProduct = (product) => {
  const haystack = normalizeSearchText([
    product?.productName,
    product?.description,
    product?.category,
    product?.categoryName,
    product?.flatCategoryName,
    product?.categoryCode,
    product?.specifications?.categoryName,
    product?.mfgPartNo,
  ].join(' '))

  if (!haystack) return null

  for (const group of CATEGORY_GROUP_DEFINITIONS) {
    if (group.keywords.some((keyword) => haystack.includes(normalizeSearchText(keyword)))) {
      return group
    }
  }

  return null
}

const computePersonalizationWeight = (entry) => {
  const now = new Date()
  const hour = String(now.getHours())
  const day = String(now.getDay())
  const countWeight = Math.log1p(Number(entry.count || 0)) * 2.5

  let recencyWeight = 0
  if (entry.lastSearched) {
    const daysAgo = Math.max(0, (Date.now() - new Date(entry.lastSearched).getTime()) / (1000 * 60 * 60 * 24))
    recencyWeight = Math.max(0, 6 - daysAgo / 5)
  }

  const timeContextWeight = (Number(entry.hourWeights?.[hour] || 0) * 0.6) + (Number(entry.dayWeights?.[day] || 0) * 0.4)
  return countWeight + recencyWeight + timeContextWeight
}

const rankProductsByPersonalization = (items) => {
  if (!Array.isArray(items) || items.length <= 1) return items
  if (!hasTrackingConsent()) return items

  const profileTerms = getSearchProfileTerms(PROFILE_TERM_LIMIT)
  if (!profileTerms.length) return items

  const queryTokens = normalizeSearchText(searchQuery.value)
    .split(' ')
    .filter((token) => token.length > 1)

  const ranked = items.map((product, index) => {
    const productName = normalizeSearchText(product.productName)
    const haystack = getProductSearchBlob(product)
    let score = 0

    profileTerms.forEach((entry) => {
      const term = normalizeSearchText(entry.termKey)
      if (!term || !haystack.includes(term)) return

      const baseWeight = computePersonalizationWeight(entry)
      score += baseWeight

      if (productName.includes(term)) {
        score += baseWeight * 0.9
      }
    })

    queryTokens.forEach((token) => {
      if (!haystack.includes(token)) return
      score += 6
      if (productName.includes(token)) {
        score += 4
      }
    })

    return { product, index, score }
  })

  if (!ranked.some((entry) => entry.score > 0)) {
    return items
  }

  ranked.sort((a, b) => {
    if (b.score !== a.score) return b.score - a.score
    return a.index - b.index
  })

  return ranked.map((entry) => entry.product)
}

const filteredProducts = computed(() => {
  // Access currentFilters.value to establish dependency
  const filters = currentFilters.value
  
  let filtered = products.value

  // Filter by part number
  if (filters.partNumber && String(filters.partNumber).trim().length > 0) {
    const partQuery = String(filters.partNumber).toLowerCase().trim()
    filtered = filtered.filter((product) => String(product.mfgPartNo || '').toLowerCase().includes(partQuery))
  }
  
  // Filter by category
  if (filters.categories && filters.categories.length > 0) {
    filtered = filtered.filter(product => {
      const selectedCategory = filters.categories[0]
      const selectedCategoryValue = availableCategories.value.find(c => c.name === selectedCategory)?.value || selectedCategory
      
      // Check if it's a billing model category
      if (selectedCategory.startsWith('Billing: ')) {
        const billingModel = selectedCategory.replace('Billing: ', '')
        return product.billingModel === billingModel
      }
      
      // Check if it's a billing frequency category
      if (selectedCategory.startsWith('Frequency: ')) {
        const frequency = selectedCategory.replace('Frequency: ', '')
        return product.billingFrequency === frequency
      }

      const groupedCategory = CATEGORY_GROUP_DEFINITIONS.find((group) => group.name === selectedCategory)
      if (groupedCategory) {
        const match = getCuratedCategoryForProduct(product)
        return match?.name === groupedCategory.name
      }
      
      // Check in productCategories
      if (product.productCategories && Array.isArray(product.productCategories)) {
        return product.productCategories.some(cat => {
          const categoryName = typeof cat === 'object' ? cat.categoryName : cat
          return categoryName === selectedCategoryValue
        })
      }

      // Check readable flat category names from PriceAvailability payloads
      const flatCategoryName = String(product.flatCategoryName || product.categoryName || product.category || '').trim()
      if (flatCategoryName) {
        return flatCategoryName === selectedCategoryValue
      }

      const specCategoryName = String(product.specifications?.categoryName || '').trim()
      if (specCategoryName) {
        return specCategoryName === selectedCategoryValue
      }

      // PriceAvailability products use categoryCode
      if (product.categoryCode) {
        return String(product.categoryCode).trim() === String(selectedCategoryValue).trim()
      }
      
      return false
    })
  }
  
  // Filter by lifecycle status
  if (filters.lifecycleStatuses && filters.lifecycleStatuses.length > 0) {
    filtered = filtered.filter(product => {
      const eol = isEolProduct(product)
      return filters.lifecycleStatuses.some((status) => {
        if (status === 'End of Life') return eol
        if (status === 'Active') return !eol
        return false
      })
    })
  }

  // Filter by review rating
  if (filters.mediaStatuses && filters.mediaStatuses.length > 0) {
    filtered = filtered.filter((product) => {
      const stats = getProductReviewStats(product.productId)
      return filters.mediaStatuses.some((status) => {
        if (status === '5 Stars') return stats.average >= 4.5
        if (status === '4 Stars & Up') return stats.average >= 4
        if (status === '3 Stars & Up') return stats.average >= 3
        if (status === 'Has Reviews') return stats.total > 0
        return false
      })
    })
  }
  
  return rankProductsByPersonalization(filtered)
})

const requiresClientSideFiltering = computed(() => {
  return requiresClientForFilters(currentFilters.value)
})

const totalProducts = computed(() => {
  if (serverPaged.value) {
    return Number(serverTotal.value || 0)
  }

  return filteredProducts.value.length
})

const totalPages = computed(() => Math.max(1, Math.ceil(totalProducts.value / ITEMS_PER_PAGE)))

const paginatedProducts = computed(() => {
  if (serverPaged.value) {
    return filteredProducts.value
  }

  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return filteredProducts.value.slice(start, end)
})

const pageNumbers = computed(() => {
  const pages = []
  const maxPagesToShow = 10
  let startPage = Math.max(1, currentPage.value - Math.floor(maxPagesToShow / 2))
  let endPage = Math.min(totalPages.value, startPage + maxPagesToShow - 1)

  if (endPage - startPage + 1 < maxPagesToShow) {
    startPage = Math.max(1, endPage - maxPagesToShow + 1)
  }

  for (let i = startPage; i <= endPage; i++) {
    pages.push(i)
  }
  return pages
})

// Add request deduplication and caching
const requestCache = new Map()
const pendingRequests = new Map()
const pendingReviewStats = new Set()

const getReviewStatsForProduct = (productId) => getProductReviewStats(productId)

const loadReviewStatsForProducts = async (items = []) => {
  const uniqueIds = Array.from(new Set(
    (Array.isArray(items) ? items : [])
      .map((item) => String(item?.productId || '').trim())
      .filter(Boolean)
  ))

  const idsToFetch = uniqueIds.filter((id) => {
    if (reviewStatsByProduct.value[id]) return false
    if (pendingReviewStats.has(id)) return false
    return true
  })

  if (idsToFetch.length === 0) return

  await Promise.all(idsToFetch.map(async (id) => {
    pendingReviewStats.add(id)
    try {
      const response = await api.get(`/products/${encodeURIComponent(id)}/reviews`, {
        params: {
          per_page: 1,
        }
      })

      const stats = response.data?.stats || {}
      reviewStatsByProduct.value = {
        ...reviewStatsByProduct.value,
        [id]: {
          total: Number(stats.total || 0),
          average: Number(stats.average || 0),
        }
      }
    } catch (statsError) {
      reviewStatsByProduct.value = {
        ...reviewStatsByProduct.value,
        [id]: {
          total: 0,
          average: 0,
        }
      }
      console.warn('Failed to load review stats for product:', id, statsError)
    } finally {
      pendingReviewStats.delete(id)
    }
  }))
}

const fetchAllProductPages = async (params) => {
  // Fetch a large batch for client-side filtering (categories, partNumber, lifecycle, media)
  const response = await api.get('/products', {
    params: {
      ...params,
      page: 1,
      per_page: 500,
      hide_zero_price: true,
      catalog_clean: true,
    }
  })

  if (!response.data?.success) {
    return []
  }

  const payload = response.data.data || {}
  return Array.isArray(payload.records)
    ? payload.records
    : (Array.isArray(payload) ? payload : [])
}

const resolveVendorApiValues = (selectedVendorNames = []) => {
  return selectedVendorNames
    .map((selectedName) => {
      const normalizedSelected = normalizeVendorKey(selectedName)
      const vendor = availableVendors.value.find((v) => (
        normalizeVendorKey(v.name) === normalizedSelected
        || normalizeVendorKey(v.value) === normalizedSelected
      ))
      return vendor?.value || selectedName
    })
    .filter(Boolean)
}

const getImplicitVendorScope = () => {
  if (currentFilters.value.vendors.length > 0) {
    return resolveVendorApiValues(currentFilters.value.vendors)
  }

  if (allVendors.value.length > 0) {
    const topVendors = selectTopDisplayVendors(allVendors.value, [])
      .slice(0, DEFAULT_VENDOR_SCOPE_LIMIT)
      .map((vendor) => String(vendor.value || vendor.name || '').trim())
      .filter(Boolean)

    if (topVendors.length > 0) {
      return topVendors
    }
  }

  return [...FALLBACK_VENDOR_SCOPE]
}

const getCacheKey = (filters, page = 1, useServerPaged = false) => {
  return JSON.stringify({
    curatedVersion: CURATED_CACHE_VERSION,
    vendors: filters.vendors,
    search: searchQuery.value,
    minPrice: filters.priceMin,
    maxPrice: filters.priceMax,
    partNumber: filters.partNumber,
    lifecycleStatuses: filters.lifecycleStatuses,
    mediaStatuses: filters.mediaStatuses,
    categories: filters.categories,
    page: useServerPaged ? page : 1,
    mode: useServerPaged ? 'server' : 'client'
  })
}

const isDefaultCuratedBrowse = (filters = currentFilters.value) => {
  const hasClientOnlyFilters = requiresClientForFilters(filters)
  return !searchQuery.value && (filters?.vendors || []).length === 0 && !hasClientOnlyFilters
}



const performSearch = async (resetPage = true) => {
  error.value = ''
  dismissSearchSuggestions()
  if (resetPage) {
    currentPage.value = 1
    loading.value = true
  } else {
    pageLoading.value = true
  }

  const useServerPaged = !requiresClientSideFiltering.value
  serverPaged.value = useServerPaged

  const normalizedQuery = normalizeSearchText(searchQuery.value)
  if (normalizedQuery) {
    persistLocalSearchHistory(searchQuery.value)
    const now = Date.now()
    const canTrack = normalizedQuery !== lastTrackedTerm.value || (now - lastTrackedAt.value) > SEARCH_TRACK_DEBOUNCE_MS
    if (canTrack) {
      trackSearchTerm(searchQuery.value)
      lastTrackedTerm.value = normalizedQuery
      lastTrackedAt.value = now
    }
  }

  const cacheKey = getCacheKey(currentFilters.value, currentPage.value, useServerPaged)

  // Check if results are already cached (cache for 5 minutes)
  if (requestCache.has(cacheKey)) {
    const cached = requestCache.get(cacheKey)
    if (Date.now() - cached.timestamp < 5 * 60 * 1000) {
      products.value = cached.data
      serverTotal.value = Number(cached.total || cached.data?.length || 0)
      serverPaged.value = Boolean(cached.serverPaged)
      if (!Boolean(cached.serverPaged)) {
        updateVendorCounts(cached.data)
        extractCategories(cached.data)
      }
      loading.value = false
      pageLoading.value = false
      return
    }
  }

  // Check if request is already in progress (prevent duplicate requests)
  if (pendingRequests.has(cacheKey)) {
    try {
      const result = await pendingRequests.get(cacheKey)
      products.value = result.data
      serverTotal.value = Number(result.total || result.data?.length || 0)
      serverPaged.value = Boolean(result.serverPaged)
      if (!Boolean(result.serverPaged)) {
        updateVendorCounts(result.data)
        extractCategories(result.data)
      }
    } finally {
      loading.value = false
      pageLoading.value = false
    }
    return
  }

  // Create promise for this request
  const requestPromise = (async () => {
    try {
      const hasClientOnlyFilters = requiresClientForFilters(currentFilters.value)
      const isDefaultBrowse =
        !searchQuery.value
        && currentFilters.value.vendors.length === 0
        && !hasClientOnlyFilters

      const params = {
        curated_it_mix: true,
        hide_zero_price: true,
        catalog_clean: true,
      }

      if (isDefaultBrowse && Number(currentFilters.value.priceMin || 0) <= 0) {
        params.min_price = DEFAULT_BROWSE_MIN_PRICE
      }

      if (searchQuery.value) {
        params.search = searchQuery.value
      }

      if (currentFilters.value.vendors.length > 0) {
        const selectedVendorValues = resolveVendorApiValues(currentFilters.value.vendors)
        if (selectedVendorValues.length > 0) {
          params.vendors = selectedVendorValues.join(',')
        }
      }

      if (currentFilters.value.priceMin > 0) {
        params.min_price = currentFilters.value.priceMin
      }
      if (currentFilters.value.priceMax < 10000) {
        params.max_price = currentFilters.value.priceMax
      }

      let loadedProducts = []
      let loadedTotal = 0

      if (useServerPaged) {
        const response = await api.get('/products', {
          params: {
            ...params,
            page: currentPage.value,
            per_page: ITEMS_PER_PAGE,
            hide_zero_price: true,
            catalog_clean: true,
          }
        })

        if (!response.data?.success) {
          error.value = 'Failed to fetch products'
          return { data: [], total: 0, serverPaged: true }
        }

        const payload = response.data.data || {}
        loadedProducts = Array.isArray(payload.records)
          ? payload.records
          : (Array.isArray(payload) ? payload : [])
        loadedTotal = Number(payload.total || loadedProducts.length || 0)
      } else {
        loadedProducts = await fetchAllProductPages(params)
        loadedTotal = loadedProducts.length
      }

      products.value = loadedProducts

      serverTotal.value = loadedTotal

      if (Array.isArray(products.value)) {
        // Always refresh categories from current results so sidebar categories are never empty.
        try {
          extractCategories(loadedProducts)
        } catch (err) {
          console.error('Category extraction error:', err)
        }

        // Vendor counts should only be recomputed from full client-side datasets.
        if (!useServerPaged) {
          updateVendorCounts(loadedProducts)
        }
        
        // Cache results locally
        requestCache.set(cacheKey, {
          data: products.value,
          total: loadedTotal,
          serverPaged: useServerPaged,
          timestamp: Date.now()
        })

        // Prefetch is disabled to avoid extra concurrent API pressure.
        if (useServerPaged && ENABLE_SERVER_PREFETCH) {
          prefetchPage(currentPage.value + 1)
          if (currentPage.value > 1) prefetchPage(currentPage.value - 1)
        }
        
        return {
          data: products.value,
          total: loadedTotal,
          serverPaged: useServerPaged
        }
      } else {
        error.value = 'Failed to fetch products'
        return { data: [], total: 0, serverPaged: useServerPaged }
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch products'
      console.error('❌ Product fetch error:', err)
      return { data: [], total: 0, serverPaged: useServerPaged }
    }
  })()

  pendingRequests.set(cacheKey, requestPromise)

  try {
    await requestPromise
  } finally {
    loading.value = false
    pageLoading.value = false
    pendingRequests.delete(cacheKey)
  }
}

const clearSearch = async () => {
  if (!searchQuery.value) {
    return
  }

  searchQuery.value = ''
  await performSearch(true)
}

const fetchVendors = async () => {
  // Build count lookup from API, then map CURATED_VENDOR_ALLOWLIST as the authoritative list
  const buildFromAllowlist = (apiVendors = []) => {
    const countLookup = new Map()
    apiVendors.forEach((vendor) => {
      const name = String(vendor.vendorName || vendor.vendorId || '').trim()
      const key = toCanonicalVendorKey(name)
      if (key) countLookup.set(key, (countLookup.get(key) || 0) + Number(vendor.count || 0))
    })

    return CURATED_VENDOR_ALLOWLIST.map((vendorName) => {
      const key = toCanonicalVendorKey(vendorName)
      const apiCount = countLookup.get(key) || 0
      return {
        name: vendorName,
        value: vendorName,
        count: apiCount,
      }
    }).sort((a, b) => {
      if (b.count !== a.count) return b.count - a.count
      return a.name.localeCompare(b.name)
    })
  }

  if (!ENABLE_VENDOR_COUNTS_API) {
    const mapped = buildFromAllowlist([])
    allVendors.value = mapped
    availableVendors.value = mapped
    return
  }

  try {
    const isDefaultBrowse =
      !searchQuery.value
      && currentFilters.value.vendors.length === 0
      && !requiresClientForFilters(currentFilters.value)

    const vendorParams = {
      curated_it_mix: true,
      hide_zero_price: true,
      catalog_clean: true,
    }

    if (isDefaultBrowse && Number(currentFilters.value.priceMin || 0) <= 0) {
      vendorParams.min_price = DEFAULT_BROWSE_MIN_PRICE
    } else if (Number(currentFilters.value.priceMin || 0) > 0) {
      vendorParams.min_price = currentFilters.value.priceMin
    }

    if (Number(currentFilters.value.priceMax || 10000) < 10000) {
      vendorParams.max_price = currentFilters.value.priceMax
    }

    const response = await api.get('/vendors', { params: vendorParams })

    const rawVendorData = response.data?.data || []
    const apiVendors = Array.isArray(rawVendorData) ? rawVendorData : (rawVendorData.records || [])
    const mappedVendors = buildFromAllowlist(apiVendors)

    allVendors.value = mappedVendors
    availableVendors.value = mappedVendors
  } catch (err) {
    console.error('Error fetching vendors:', err)
    // Fallback: still show all 26 curated brands with nominal count
    const fallback = buildFromAllowlist([])
    allVendors.value = fallback
    availableVendors.value = fallback
  }
}

const updateVendorCounts = (sourceProducts = products.value) => {
  // Count products per vendor
  const vendorCountMap = new Map()
  
  sourceProducts.forEach(product => {
    const rawVendor = product.vendorId || product.vendorName
    const key = toCanonicalVendorKey(rawVendor)
    if (key) {
      const count = vendorCountMap.get(key) || 0
      vendorCountMap.set(key, count + 1)
    }
  })

  const sourceVendors = allVendors.value.length > 0 ? allVendors.value : availableVendors.value

  // Keep full vendor list visible and provide counts from the facet dataset.
  availableVendors.value = sourceVendors
    .map(vendor => ({
      ...vendor,
      count: getVendorCountForKey(toCanonicalVendorKey(vendor.value || vendor.name), vendorCountMap)
    }))
    .sort((a, b) => {
      if (b.count !== a.count) return b.count - a.count
      return a.name.localeCompare(b.name)
    })

  availableVendors.value = selectTopDisplayVendors(availableVendors.value, currentFilters.value.vendors)
}

const extractCategories = (sourceProducts = products.value) => {
  const counts = new Map(CATEGORY_GROUP_DEFINITIONS.map((group) => [group.name, 0]))

  sourceProducts.forEach((product) => {
    const match = getCuratedCategoryForProduct(product)
    if (match) {
      counts.set(match.name, Number(counts.get(match.name) || 0) + 1)
    }
  })

  availableCategories.value = CATEGORY_GROUP_DEFINITIONS.map((group) => ({
    name: group.name,
    value: group.value,
    // Keep each curated category visible in sidebar even when current page has sparse matches.
    count: Math.max(1, Number(counts.get(group.name) || 0)),
  }))
  
  // Vendor counts are updated from currently loaded products.
  
  // Log for verification
}

const handleFilterChange = (filters) => {
  const normalizeFilters = (value = {}) => ({
    priceMin: Number(value.priceMin ?? 0),
    priceMax: Number(value.priceMax ?? 10000),
    partNumber: String(value.partNumber ?? '').trim(),
    vendors: Array.isArray(value.vendors) ? [...value.vendors].map((v) => String(v).trim()) : [],
    categories: Array.isArray(value.categories) ? [...value.categories].map((v) => String(v).trim()) : [],
    lifecycleStatuses: Array.isArray(value.lifecycleStatuses) ? [...value.lifecycleStatuses].map((v) => String(v).trim()) : [],
    mediaStatuses: Array.isArray(value.mediaStatuses) ? [...value.mediaStatuses].map((v) => String(v).trim()) : [],
  })

  const previousNormalized = normalizeFilters(currentFilters.value)
  const nextNormalized = normalizeFilters(filters)
  const changed = JSON.stringify(previousNormalized) !== JSON.stringify(nextNormalized)

  currentFilters.value = nextNormalized

  if (!changed) {
    return
  }

  currentPage.value = 1
  performSearch(true)
}

const resetFilters = () => {
  searchQuery.value = ''
  currentPage.value = 1
  currentFilters.value = {
    priceMin: 0,
    priceMax: 10000,
    partNumber: '',
    vendors: [],
    categories: [],
    lifecycleStatuses: [],
    mediaStatuses: []
  }
  performSearch(true)
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    if (serverPaged.value) {
      performSearch(false)
    }
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    if (serverPaged.value) {
      performSearch(false)
    }
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const goToPage = (page) => {
  currentPage.value = page
  if (serverPaged.value) {
    performSearch(false)
  }
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Prefetch adjacent pages silently for instant pagination
const prefetchPage = (page) => {
  if (!ENABLE_SERVER_PREFETCH || page < 1 || !serverPaged.value) return

  const params = {
    curated_it_mix: true,
  }

  const hasClientOnlyFilters = requiresClientForFilters(currentFilters.value)
  const isDefaultBrowse =
    !searchQuery.value
    && currentFilters.value.vendors.length === 0
    && !hasClientOnlyFilters

  if (searchQuery.value) params.search = searchQuery.value
  if (currentFilters.value.vendors.length > 0) {
    const selectedVendorValues = resolveVendorApiValues(currentFilters.value.vendors)
    if (selectedVendorValues.length > 0) params.vendors = selectedVendorValues.join(',')
  }
  if (isDefaultBrowse && Number(currentFilters.value.priceMin || 0) <= 0) {
    params.min_price = DEFAULT_BROWSE_MIN_PRICE
  }
  if (currentFilters.value.priceMin > 0) params.min_price = currentFilters.value.priceMin
  if (currentFilters.value.priceMax < 10000) params.max_price = currentFilters.value.priceMax

  const tempFilters = { ...currentFilters.value }
  const cacheKey = JSON.stringify({
    vendors: tempFilters.vendors,
    search: searchQuery.value,
    minPrice: tempFilters.priceMin,
    maxPrice: tempFilters.priceMax,
    partNumber: tempFilters.partNumber,
    lifecycleStatuses: tempFilters.lifecycleStatuses,
    mediaStatuses: tempFilters.mediaStatuses,
    categories: tempFilters.categories,
    page: page,
    mode: 'server'
  })

  // Skip if already cached
  if (requestCache.has(cacheKey)) return

  // Fire-and-forget prefetch
  api.get('/products', {
    params: { ...params, page, per_page: ITEMS_PER_PAGE, hide_zero_price: true, catalog_clean: true }
  }).then((response) => {
    if (response.data?.success) {
      const payload = response.data.data || {}
      const records = Array.isArray(payload.records) ? payload.records : []
      const prefetchedTotal = Number(payload.total || records.length || 0)

      requestCache.set(cacheKey, {
        data: records,
        total: prefetchedTotal,
        serverPaged: true,
        timestamp: Date.now()
      })
    }
  }).catch(() => {
    // Silently ignore prefetch errors
  })
}

const viewProductDetails = (product) => {
  router.push({
    name: 'product-detail',
    params: { id: product.productId }
  })
}

const addToQuote = (product) => {
  const added = cartStore.addItem(product, 1)
  if (!added) {
    toastStore.addToast('Account suspended: adding items to quotes is disabled', 'error')
    return
  }

  toastStore.addToast(`Added "${product.productName}" to quote`, 'success')
}

const getProductSku = (product) => {
  return String(
    product?.mfgPartNo ||
    product?.mfg_part_no ||
    product?.tdsynnexSkuNo ||
    product?.tdsynnex_sku_no ||
    product?.skuNo ||
    product?.sku_no ||
    'N/A'
  )
}

const getProductVendor = (product) => {
  return String(
    product?.vendorId ||
    product?.vendor_id ||
    product?.vendorName ||
    product?.vendor_name ||
    product?.manufacturerName ||
    product?.manufacturer_name ||
    'N/A'
  )
}

const buildProductHoverDetails = (product) => {
  const lines = [
    product?.productName || 'Product',
    `SKU: ${getProductSku(product)}`,
    `Vendor: ${getProductVendor(product)}`,
  ]

  const price = Number(product?.productPrice?.[0]?.rsPrice || 0)
  if (price > 0) {
    lines.push(`Price: ${formatCatalogPrice(price)}`)
  }

  const billing = [product?.billingModel || '', product?.billingFrequency || ''].filter(Boolean).join(' / ')
  if (billing) {
    lines.push(`Billing: ${billing}`)
  }

  return lines.join('\n')
}

const openShareModal = (product) => {
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to share products', 'info')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  sharingProduct.value = product
  shareRecipientEmail.value = ''
  shareNote.value = ''
  shareGeneratedLink.value = ''
  showShareModal.value = true
}

const closeShareModal = () => {
  showShareModal.value = false
  sharingProduct.value = null
  shareSubmitting.value = false
}

const submitProductShare = async () => {
  const product = sharingProduct.value
  if (!product) return

  const recipientEmail = shareRecipientEmail.value.trim()

  shareSubmitting.value = true
  try {
    const response = await api.post('/shares/product', {
      recipient_email: recipientEmail || null,
      note: shareNote.value.trim(),
      product: {
        productId: product.productId,
        productName: product.productName,
        mfgPartNo: getProductSku(product) === 'N/A' ? '' : getProductSku(product),
        vendorId: getProductVendor(product) === 'N/A' ? '' : getProductVendor(product),
        description: product.description || '',
        imageUrl: product.productImages?.[0]?.imageUrl || product.productImages?.[0] || '',
        price: Number(product.productPrice?.[0]?.rsPrice || 0),
      },
    })

    const shareUrl = String(response.data?.data?.share_url || '').trim()
    shareGeneratedLink.value = shareUrl
    if (shareUrl) {
      toastStore.addToast('Share link generated. Use Copy Link or Send to Email.', 'success')
    } else {
      toastStore.addToast(`Shared "${product.productName}" successfully`, 'success')
    }
  } catch (error) {
    console.error('Failed to share product:', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to share product', 'error')
  } finally {
    shareSubmitting.value = false
  }
}

const copyShareGeneratedLink = async () => {
  const link = shareGeneratedLink.value.trim()
  if (!link) return

  try {
    if (navigator?.clipboard?.writeText) {
      await navigator.clipboard.writeText(link)
      toastStore.addToast('Share link copied to clipboard', 'success')
      return
    }
  } catch (error) {
    console.warn('Clipboard copy failed:', error)
  }

  window.prompt('Copy this share link:', link)
}

const sendShareLinkByEmail = () => {
  const link = shareGeneratedLink.value.trim()
  if (!link) {
    toastStore.addToast('Generate the share link first', 'warning')
    return
  }

  const recipient = encodeURIComponent(shareRecipientEmail.value.trim())
  const productName = encodeURIComponent(sharingProduct.value?.productName || 'Shared product')
  const note = encodeURIComponent(shareNote.value.trim())
  const bodyParts = [
    `I wanted to share this product with you:`,
    decodeURIComponent(productName),
    '',
    link,
  ]

  if (note) {
    bodyParts.splice(3, 0, `Note: ${decodeURIComponent(note)}`, '')
  }

  const subject = `Shared product: ${decodeURIComponent(productName)}`
  const body = bodyParts.join('\n')
  window.location.href = `mailto:${recipient}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`
}

const toggleFavorite = (product) => {
  // Check if user is authenticated
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to add items to favorites', 'info')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  const isNowFavorite = favoritesStore.toggleFavorite(product)
  if (isNowFavorite === null) {
    toastStore.addToast('Account suspended: favorites are read-only', 'error')
    return
  }

  if (isNowFavorite) {
    toastStore.addToast(`Added "${product.productName}" to favorites`, 'success')
  } else {
    toastStore.addToast(`Removed "${product.productName}" from favorites`, 'info')
  }
}

const isFavorite = (productId) => {
  return favoritesStore.isFavorite(productId)
}

const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
}

const formatCatalogPrice = (baseUsdPrice) => {
  const adjustedUsd = getCatalogPriceWithRules(baseUsdPrice)
  const converted = convertFromUsd(adjustedUsd)
  return formatWithCurrency(converted)
}

const getProductIcon = (productName) => {
  const name = productName.toLowerCase()
  if (name.includes('server') || name.includes('instance')) return 'server'
  if (name.includes('azure') || name.includes('cloud') || name.includes('subscription')) return 'cloud'
  if (name.includes('database') || name.includes('sql')) return 'database'
  return 'default'
}

watch(
  paginatedProducts,
  (visibleProducts) => {
    void loadReviewStatsForProducts(visibleProducts)
  },
  { immediate: true }
)

watch(
  () => [route.query.q, route.query.vendor, route.query.vendors, route.query.category],
  ([newQuery, newVendor, newVendors, newCategory]) => {
    searchQuery.value = newQuery ? String(newQuery) : ''

    const hasVendorQuery = newVendor !== undefined || newVendors !== undefined
    const hasCategoryQuery = newCategory !== undefined

    let nextVendors = currentFilters.value.vendors
    if (hasVendorQuery) {
      const vendorsRaw = newVendors ?? newVendor
      if (Array.isArray(vendorsRaw)) {
        nextVendors = vendorsRaw.map((value) => String(value)).filter(Boolean)
      } else if (typeof vendorsRaw === 'string' && vendorsRaw.trim() !== '') {
        nextVendors = vendorsRaw.split(',').map((value) => value.trim()).filter(Boolean)
      } else {
        nextVendors = []
      }
    }

    let nextCategories = currentFilters.value.categories
    if (hasCategoryQuery) {
      nextCategories = newCategory ? [String(newCategory)] : []
    }

    currentFilters.value = {
      ...currentFilters.value,
      vendors: nextVendors,
      categories: nextCategories,
    }

    currentPage.value = 1
    performSearch(true)
  },
  { immediate: true }
)

onMounted(async () => {
  if (route.query.next === 'login') {
    const query = {}
    if (route.query.email) query.email = String(route.query.email)
    if (route.query.activation) query.activation = String(route.query.activation)
    if (route.query.message) query.message = String(route.query.message)

    router.replace({ name: 'login', query })
    return
  }

  await loadPricingSettings(true)
  loadLocalSearchHistory()
  fetchVendors()
})
</script>
