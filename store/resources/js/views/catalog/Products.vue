<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <PopularCategories v-if="route.name === 'home'" />

    <!-- Main Content -->
    <div class="mx-auto max-w-[1600px] px-3 py-4 sm:px-4 sm:py-5 lg:px-5">
      <CatalogHero v-if="route.name === 'home'" :products="products" @browse="scrollToCatalog" />

      <!-- Content Layout: Sidebar + Products Grid -->
      <div id="catalog-results" class="flex items-stretch gap-8 lg:gap-6">
        <!-- Filters Sidebar -->
        <aside
          class="relative hidden flex-shrink-0 lg:block lg:w-80"
          :class="totalPages > 1
            ? 'lg:mb-[91px] lg:min-h-[1648px] lg:self-stretch'
            : 'lg:min-h-[1648px] lg:self-stretch'"
        >
          <FilterSidebar 
            :vendors="availableVendors" 
            :categories="availableCategories"
            :vendors-loading="vendorsLoading"
            :categories-loading="categoriesLoading"
            :active-filters="currentFilters"
            :lifecycle-options="lifecycleOptions"
            :media-options="reviewRatingOptions"
            :compact="false"
            @filter-change="handleFilterChange"
            @clear-all="resetFilters"
            class="lg:absolute lg:inset-0 lg:h-full lg:min-h-0"
          />
        </aside>

        <!-- Products Section -->
        <div class="min-w-0 flex-1">
          <!-- Loading State - skeleton cards matching the real product grid -->
          <div v-if="loading && !pageLoading">
            <div class="mb-6 flex items-center justify-between rounded-xl border border-blue-100 bg-white px-5 py-4 shadow-sm">
              <div class="flex items-center gap-3">
                <span class="h-6 w-6 animate-spin rounded-full border-[3px] border-blue-100 border-t-[#2F5597]" aria-hidden="true"></span>
                <div>
                  <p class="text-sm font-extrabold text-[#102a52]">{{ searchQuery ? 'Searching products…' : 'Loading products…' }}</p>
                  <p v-if="searchQuery" class="mt-0.5 max-w-[70vw] truncate text-xs text-slate-500">Finding the best matches for “{{ searchQuery }}”</p>
                </div>
              </div>
              <span class="hidden text-xs font-semibold text-[#2F5597] sm:block">Please wait</span>
            </div>
            <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
              <div v-for="i in 12" :key="'skel-' + i" class="flex min-h-[248px] flex-col overflow-hidden rounded-xl border border-gray-200 bg-white animate-pulse sm:flex-row">
                <div class="h-52 bg-gray-100 sm:h-auto sm:min-h-[248px] sm:w-[39%] sm:border-r"></div>
                <div class="flex-1 space-y-3 p-4">
                  <div class="flex justify-between items-start">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-5 w-10 bg-gray-100 rounded ml-2 flex-shrink-0"></div>
                  </div>
                  <div class="flex justify-between">
                    <div class="h-3 bg-gray-100 rounded w-1/3"></div>
                    <div class="h-3 bg-gray-100 rounded w-1/3"></div>
                  </div>
                  <div class="flex gap-1">
                    <div v-for="s in 5" :key="s" class="w-4 h-4 bg-gray-200 rounded"></div>
                    <div class="h-3 w-16 bg-gray-100 rounded ml-1"></div>
                  </div>
                  <div class="h-8 w-28 bg-gray-200 rounded"></div>
                  <div class="flex gap-2">
                    <div class="h-5 w-16 bg-gray-100 rounded"></div>
                    <div class="h-5 w-20 bg-gray-100 rounded"></div>
                  </div>
                  <div class="flex justify-between">
                    <div class="h-3 w-24 bg-gray-100 rounded"></div>
                    <div class="h-3 w-20 bg-gray-100 rounded"></div>
                  </div>
                  <div class="flex gap-2 pt-1">
                    <div class="flex-1 h-9 bg-gray-200 rounded-lg"></div>
                    <div class="w-9 h-9 bg-gray-100 rounded-lg"></div>
                    <div class="w-9 h-9 bg-gray-100 rounded-lg"></div>
                    <div class="w-9 h-9 bg-gray-100 rounded-lg"></div>
                  </div>
                </div>
              </div>
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
          <div v-else-if="!loading || pageLoading">
            <!-- Page loading overlay -->
            <div v-if="pageLoading" class="fixed top-0 left-0 right-0 z-50">
              <div class="h-1 bg-gray-200 w-full">
                <div class="h-1 rounded-r animate-pulse" style="background-color: #2F5597; width: 100%;"></div>
              </div>
            </div>
            <div class="z-30 -mx-1 mb-5 flex flex-none flex-col items-start justify-between gap-3 border-b border-slate-200/80 bg-gray-50/95 px-1 py-3 backdrop-blur-sm sm:flex-row sm:items-start">
              <div>
                <div class="flex items-baseline gap-3">
                  <h2 class="text-xl font-extrabold text-[#102a52]">Products</h2>
                  <span class="text-sm font-bold text-[#2F5597]">{{ totalProducts.toLocaleString() }} results</span>
                </div>
                <p class="mt-1 text-xs text-slate-500">High quality technology and business supplies.</p>
                <div v-if="hasActiveCatalogState" class="mt-3 flex flex-wrap items-center gap-2">
                  <span class="text-sm font-semibold text-gray-900">Applied Filters</span>
                  <button type="button" @click="resetFilters" class="inline-flex items-center gap-1 text-sm font-medium text-[#2F5597] transition hover:opacity-70">
                    Clear All
                    <span aria-hidden="true" class="text-lg leading-none">×</span>
                  </button>
                </div>
              </div>
              <div class="flex flex-wrap items-center gap-3">
                <label class="text-sm text-gray-600 font-medium whitespace-nowrap">Sort by:</label>
                <select
                  v-model="sortBy"
                  @change="currentPage = 1"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:border-transparent bg-white text-gray-700"
                  style="--tw-ring-color: #2F5597;"
                >
                  <option value="relevance">Relevance</option>
                  <option value="price_asc">Price: Low to High</option>
                  <option value="price_desc">Price: High to Low</option>
                  <option value="name_asc">Name: A – Z</option>
                  <option value="name_desc">Name: Z – A</option>
                </select>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="totalProducts === 0" class="bg-white rounded-xl border border-gray-200 px-5 py-9 sm:px-8">
              <div class="text-center">
              <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
              </svg>
              <h3 class="text-xl font-bold text-gray-900 mb-2">No Products Found</h3>
              <p class="text-gray-600 mb-6">Try adjusting your search or ask our procurement team to source it.</p>
              <p v-if="supplierLookupQueued" class="mx-auto mb-5 max-w-xl rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                We didn’t find a local match, so we’re checking the supplier catalog. Please retry in a moment; any verified result will be added automatically.
              </p>
              <button @click="resetFilters" class="px-6 py-2 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
                Clear Filters
              </button>
              <button v-if="searchQuery" @click="openSourcingRequest" class="ml-2 rounded-lg border border-[#2F5597] px-6 py-2 font-semibold text-[#2F5597] transition hover:bg-blue-50">
                Request sourcing
              </button>
              <button v-if="supplierLookupQueued" @click="performSearch(true)" class="ml-2 rounded-lg border border-amber-500 px-6 py-2 font-semibold text-amber-700 transition hover:bg-amber-50">
                Retry search
              </button>
              </div>

              <form v-if="showSourcingForm" @submit.prevent="submitSourcingRequest" class="mx-auto mt-8 max-w-2xl rounded-xl border border-blue-100 bg-blue-50/50 p-5 text-left">
                <h4 class="font-bold text-[#102a52]">Can’t find this product?</h4>
                <p class="mt-1 text-sm text-slate-600">We’ll verify supplier availability, pricing, and warranty before offering it for purchase.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                  <label class="text-sm font-semibold text-slate-700 sm:col-span-2">Product searched
                    <input v-model.trim="sourcingForm.search_query" required maxlength="500" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal outline-none focus:border-[#2F5597]" />
                  </label>
                  <label class="text-sm font-semibold text-slate-700">Manufacturer
                    <input v-model.trim="sourcingForm.manufacturer" maxlength="100" placeholder="e.g. Dell" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal outline-none focus:border-[#2F5597]" />
                  </label>
                  <label class="text-sm font-semibold text-slate-700">Model or part number
                    <input v-model.trim="sourcingForm.model_or_part_number" maxlength="150" placeholder="e.g. RB14250" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal outline-none focus:border-[#2F5597]" />
                  </label>
                  <label class="text-sm font-semibold text-slate-700">Quantity
                    <input v-model.number="sourcingForm.quantity" type="number" min="1" max="100000" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal outline-none focus:border-[#2F5597]" />
                  </label>
                  <label class="text-sm font-semibold text-slate-700 sm:col-span-2">Additional details
                    <textarea v-model.trim="sourcingForm.notes" maxlength="2000" rows="3" placeholder="Required configuration, delivery date, or alternatives you would accept" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal outline-none focus:border-[#2F5597]"></textarea>
                  </label>
                </div>
                <p v-if="sourcingError" class="mt-3 text-sm font-semibold text-red-700">{{ sourcingError }}</p>
                <div class="mt-4 flex justify-end gap-2">
                  <button type="button" @click="showSourcingForm = false" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancel</button>
                  <button type="submit" :disabled="sourcingSubmitting" class="rounded-lg bg-[#2F5597] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">
                    {{ sourcingSubmitting ? 'Sending…' : 'Send sourcing request' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Horizontal product cards -->
            <div v-else class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
              <ProductCard
                v-for="(product, productIndex) in paginatedProducts"
                :key="product.productId"
                :product="product"
                :image="imgFallbackMap[product.productId] || getPrimaryImageUrl(product)"
                :favorite="isFavorite(product.productId)"
                :eager="productIndex < 3"
                :review-stats="getReviewStatsForProduct(product.productId)"
                @view="viewProductDetails"
                @quote="addToQuote"
                @favorite="toggleFavorite"
                @share="openShareModal"
              />
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="mt-8 flex flex-col items-center justify-between gap-3 border-t border-slate-200 pt-4">
              <p class="whitespace-nowrap text-sm font-medium text-slate-600">{{ visibleProductsRangeLabel }}</p>
              <div ref="paginationRow" class="flex w-full flex-nowrap items-center justify-center gap-1.5 overflow-hidden sm:gap-2">
              <!-- Previous Button -->
              <button
                @click="previousPage"
                :disabled="currentPage === 1"
                title="Previous page"
                aria-label="Previous page"
                class="inline-flex h-10 flex-shrink-0 items-center justify-center gap-1 rounded-lg border border-gray-300 px-3 text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-white sm:px-4"
              >
                <span aria-hidden="true">←</span><span class="hidden sm:inline">Previous</span>
              </button>

              <!-- Page Numbers -->
              <div class="flex min-w-0 flex-nowrap justify-center gap-1">
                <button
                  v-for="page in pageNumbers"
                  :key="page"
                  @click="goToPage(page)"
                  :class="['h-10 w-10 flex-shrink-0 rounded-lg transition', page === currentPage ? 'text-white font-semibold' : 'border border-gray-300 text-gray-700 hover:bg-gray-50']"
                  :style="page === currentPage ? { backgroundColor: '#2F5597' } : {}"
                >
                  {{ page }}
                </button>
              </div>

              <!-- Next Button -->
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                title="Next page"
                aria-label="Next page"
                class="inline-flex h-10 flex-shrink-0 items-center justify-center gap-1 rounded-lg border border-gray-300 px-3 text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-white sm:px-4"
              >
                <span class="hidden sm:inline">Next</span><span aria-hidden="true">→</span>
              </button>
              </div>
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
import { ref, computed, watch, onMounted, onUnmounted, reactive, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useCartStore } from '../../stores/cartStore'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useAuthStore } from '../../stores/authStore'
import { usePricingSettings } from '../../composables/usePricingSettings'
import { trackSearchTerm, hasTrackingConsent, getSearchProfileTerms, getSearchSuggestions, loadSearchProfile } from '../../services/searchInsights'
import api from '../../services/api'
import { buildStoreUrl, resolveProductImageUrl } from '../../services/runtimeConfig'
import { buildProductsLocation, parseProductsRouteFilters } from '../../services/productRoute'
import { isSupplierOrderable } from '../../services/productAvailability'
import Navbar from '../../components/Navbar.vue'
import ProductCard from '../../components/ProductCard.vue'
import FilterSidebar from '../../components/FilterSidebar.vue'
import CatalogHero from '../../components/CatalogHero.vue'
import PopularCategories from '../../components/PopularCategories.vue'

const router = useRouter()
const route = useRoute()
const scrollToCatalog = () => {
  const catalog = document.getElementById('catalog-results')
  if (!catalog) return
  const navigationOffset = window.innerWidth >= 1280 ? 208 : window.innerWidth >= 1024 ? 144 : 88
  const top = window.scrollY + catalog.getBoundingClientRect().top - navigationOffset - 8
  window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' })
}
const toastStore = useToastStore()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const { loadPricingSettings, convertFromUsd, formatWithCurrency } = usePricingSettings()
const pricingReady = ref(false)
const imgErrorMap = reactive({})
const imgFallbackMap = reactive({})

const getImgFallbackUrl = (product) => {
  const primary = getPrimaryImageUrl(product)
  if (!primary) return ''
  // Primary is /images/products/... → fallback is /store/images/products/...
  if (primary.startsWith('/images/products/')) {
    const alt = buildStoreUrl(primary)
    return alt !== primary ? alt : ''
  }
  // Production assets live under /store/images/products. Do not retry the
  // known-invalid root /images path when a production transfer fails.
  if (primary.includes('/store/images/products/')) return ''
  return ''
}

const onImgError = (productId, product) => {
  if (!imgFallbackMap[productId]) {
    const fallback = getImgFallbackUrl(product)
    if (fallback) {
      imgFallbackMap[productId] = fallback
      return
    }
  }
  imgErrorMap[productId] = true
}
const resetImgErrorMap = () => {
  Object.keys(imgErrorMap).forEach((key) => { delete imgErrorMap[key] })
  Object.keys(imgFallbackMap).forEach((key) => { delete imgFallbackMap[key] })
}
const ITEMS_PER_PAGE = 12
const paginationRow = ref(null)
const paginationWidth = ref(0)
const SHOW_IMAGE_FILTERS = true
const API_PAGE_SIZE = 100
const SEARCH_TRACK_DEBOUNCE_MS = 15000
const PROFILE_TERM_LIMIT = 25
const LOCAL_SEARCH_HISTORY_KEY = 'armely_products_search_history'
const LOCAL_SEARCH_HISTORY_LIMIT = 12
const TOP_VENDOR_DISPLAY_LIMIT = 40
const DEFAULT_VENDOR_SCOPE_LIMIT = 12
const DEFAULT_BROWSE_MIN_PRICE = 100
const DEFAULT_BROWSE_MAX_PRICE = 0
const CURATED_CACHE_VERSION = 11
// Bump whenever persisted catalog classification changes. This becomes part of
// the URL so browsers cannot reuse an older publicly cached catalog response.
const CATALOG_REQUEST_REVISION = 'taxonomy-relevance-20260821-2'
const ENABLE_SERVER_PREFETCH = true
const ENABLE_VENDOR_COUNTS_API = true
const PRODUCTS_RESULTS_SOFT_TTL_MS = 5 * 60 * 1000
const PRODUCTS_RESULTS_HARD_TTL_MS = 24 * 60 * 60 * 1000
const PRODUCTS_RESULTS_CACHE_PREFIX = 'products_results_cache_v17'
const SIDEBAR_FACETS_CACHE_TTL_MS = 10 * 60 * 1000
const SIDEBAR_VENDORS_STORAGE_KEY = 'products_sidebar_vendors_v1'
const SIDEBAR_CATEGORIES_STORAGE_KEY = 'products_sidebar_categories_v1'

// Product responses are too large for localStorage. Clear legacy entries and
// keep response caching in the bounded in-memory request cache instead.
if (typeof window !== 'undefined') {
  try {
    Object.keys(window.localStorage)
      .filter((key) => key.startsWith(`${PRODUCTS_RESULTS_CACHE_PREFIX}:`))
      .forEach((key) => window.localStorage.removeItem(key))
  } catch {
    // Storage can be unavailable or already over quota.
  }
}
const CURATED_VENDOR_ALLOWLIST = [
  'CISCO', 'DELL', 'HP', 'LENOVO', 'MICROSOFT', 'SAMSUNG', 'EPSON', 'CANON',
  'PANASONIC', 'ACER', 'ASUS', 'XEROX', 'GETAC', 'GOOGLE', 'RICOH', 'SONY',
  'LEXMARK', 'SANDISK', 'CRADLEPOINT', 'AVAYA', 'BRADY', 'DT RESEARCH',
  'HUBBELL', 'HAIVISION',
]

const CURATED_VENDOR_ALIAS_MAP = {
  CISCO: ['CISCO SYSTEMS', 'CISCO SYSTEMS CAPITAL REMARKET'],
  DELL: ['DELL MARKETING L.P.', 'DELL MARKETING LP', 'DELL WORLD TRADE L.P.'],
  HP: [
    'HP INC', 'HP INC.',
    'HEWLETT PACKARD', 'HEWLETT PACKARD ENTERPRISE',
    'HEWLETT PACKARD ENTERPRISE COM', 'HEWLETT PACKARD ENTERPRISE COMPANY',
  ],
  LENOVO: ['LENOVO DATA CENTER', 'LENOVO GROUP', 'LENOVO PC HK LIMITED'],
  MICROSOFT: [
    'MICROSOFT CORPORATION', 'MICROSOFT CORP', 'MICROSOFT RETAIL',
    'MSFT RETAL NEW NAE', 'MSFT SURFACE RECERTIFIED',
  ],
  SAMSUNG: [
    'SAMSUNG ELECTRONICS AMERICA', 'SAMSUNG ELECTRONICS AMERICA, I',
    'SAMSUNG ELECTRONICS AMERICA IN', 'SAMSUNG ELECTRONICS AMERICA (W',
    'SAMSUNG ELECTRONICS CO.', 'SAMSUNG VXT',
  ],
  EPSON: ['EPSON PRINT', 'EPSON PROJECTION', 'EPSON PROJECTOR', 'EPSON SCANNER'],
  CANON: ['CANON USA', 'CANON USA INC', 'CANON USA INC.', 'CANON USA, INC'],
  PANASONIC: ['PANASONIC SOLUTIONS COMPANY', 'PANASONIC SYSTEM SOLUTIONS'],
  ACER: ['ACER AMERICA', 'ACER AMERICA CORPORATION'],
  ASUS: ['ASUS - RETAIL', 'ASUS SBG COMMERCIAL'],
  XEROX: ['XEROX CORPORATION'],
  GETAC: ['GETAC INC.', 'GETAC VIDEO SOLUTIONS INC.'],
  GOOGLE: ['GOOGLE CLOUD', 'GOOGLE INC', 'GOOGLE LLC', 'GOOGLE REMAN'],
  RICOH: ['RICOH PFU', 'RICOH USA'],
  SONY: ['SONY CONSUMER ELECTRONICS INC', 'SONY PRO ELECTRONICS INC'],
  LEXMARK: ['LEXMARK WARRANTIES'],
  SANDISK: ['SANDISK PROFESSIONAL', 'SANDISK TECHNOLOGIES, INC.'],
  CRADLEPOINT: ['CRADLEPOINT INC', 'CRADLEPOINT MSP'],
  AVAYA: ['AVAYA BLUE'],
  BRADY: ['BRADY PEOPLE ID - CIPI', 'BRADY WORLDWIDE, INC.'],
  'DT RESEARCH': ['DT RESEARCH GOVERNMENT'],
  HUBBELL: ['HUBBELL PREMISE WIRING'],
  HAIVISION: ['HAIVISION MCS, LLC', 'HAIVISION NETWORK VIDEO INC.'],
}

// Only vendors matching a term below will appear in the sidebar.
// To add a new brand, append its uppercase display name here.
const PREFERRED_VENDOR_TERMS = Object.keys(CURATED_VENDOR_ALIAS_MAP)

// Vendors used for implicit browse scope when user has not selected a vendor
// and the live vendor list has not loaded yet.
const FALLBACK_VENDOR_SCOPE = [
  'CISCO',
  'DELL',
  'HP',
  'LENOVO',
]

const products = ref([])
const serverTotal = ref(0)
const serverPaged = ref(false)
const serverHasMore = ref(false)
const serverTotalIsEstimate = ref(false)
const loading = ref(false)
const pageLoading = ref(false)
const error = ref('')
const searchQuery = ref('')
const supplierLookupQueued = ref(false)
const showSourcingForm = ref(false)
const sourcingSubmitting = ref(false)
const sourcingError = ref('')
const sourcingForm = reactive({
  search_query: '', manufacturer: '', model_or_part_number: '', quantity: 1, notes: '',
})
const currentPage = ref(1)
// Prevents the route watcher from double-fetching when we call router.replace ourselves
let ownRouterReplace = false
const lastTrackedTerm = ref('')
const lastTrackedAt = ref(0)
const reviewStatsByProduct = ref({})
const localSearchHistory = ref([])
const showSearchSuggestions = ref(false)
const activeSuggestionIndex = ref(-1)
const searchSuggestionItems = ref([])
const sortBy = ref('relevance')
const showShareModal = ref(false)
const sharingProduct = ref(null)
const shareRecipientEmail = ref('')
const shareNote = ref('')
const shareGeneratedLink = ref('')
const shareSubmitting = ref(false)

const inferSourcingIdentifiers = (query) => {
  const value = String(query || '').trim()
  const modelMatch = value.match(/\(([A-Z0-9][A-Z0-9-]{3,})\)|\b([A-Z]{1,5}\d[A-Z0-9-]{3,})\b/i)
  const manufacturer = CURATED_VENDOR_ALLOWLIST.find((vendor) =>
    value.toUpperCase().includes(vendor.toUpperCase())
  ) || ''
  return { manufacturer, model: modelMatch?.[1] || modelMatch?.[2] || '' }
}

const openSourcingRequest = () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  const inferred = inferSourcingIdentifiers(searchQuery.value)
  sourcingForm.search_query = searchQuery.value
  sourcingForm.manufacturer = inferred.manufacturer
  sourcingForm.model_or_part_number = inferred.model
  sourcingError.value = ''
  showSourcingForm.value = true
}

const submitSourcingRequest = async () => {
  sourcingSubmitting.value = true
  sourcingError.value = ''
  try {
    const response = await api.post('/product-sourcing-requests', sourcingForm)
    toastStore.addToast(response.data?.message || 'Sourcing request sent.', 'success', 5000)
    showSourcingForm.value = false
    sourcingForm.quantity = 1
    sourcingForm.notes = ''
  } catch (requestError) {
    sourcingError.value = requestError.response?.data?.message || 'We could not send your request. Please try again.'
  } finally {
    sourcingSubmitting.value = false
  }
}

const availableVendors = ref([])
const allVendors = ref([])
const availableCategories = ref([])
const vendorsLoading = ref(true)
const categoriesLoading = ref(true)
let activeVendorFacetRequestId = 0
let activeCategoryFacetRequestId = 0

const loadSidebarFacetCache = (key) => {
  if (typeof window === 'undefined') return []

  try {
    const raw = window.localStorage.getItem(key)
    if (!raw) return []

    const parsed = JSON.parse(raw)
    const timestamp = Number(parsed?.timestamp || 0)
    if (!timestamp || Date.now() - timestamp > SIDEBAR_FACETS_CACHE_TTL_MS) {
      window.localStorage.removeItem(key)
      return []
    }

    return Array.isArray(parsed?.data) ? parsed.data : []
  } catch {
    return []
  }
}

const saveSidebarFacetCache = (key, data) => {
  if (typeof window === 'undefined') return

  try {
    window.localStorage.setItem(
      key,
      JSON.stringify({
        timestamp: Date.now(),
        data,
      })
    )
  } catch {
    // Ignore storage write errors
  }
}

const hashString = (value = '') => {
  let hash = 5381
  const input = String(value || '')
  for (let i = 0; i < input.length; i += 1) {
    hash = ((hash << 5) + hash) + input.charCodeAt(i)
    hash |= 0
  }
  return Math.abs(hash).toString(36)
}

const getProductsResultsStorageKey = (cacheKey) => {
  return `${PRODUCTS_RESULTS_CACHE_PREFIX}:${hashString(cacheKey)}`
}

const loadProductResultsCache = (cacheKey) => {
  if (typeof window === 'undefined') return null

  try {
    const storageKey = getProductsResultsStorageKey(cacheKey)
    const raw = window.localStorage.getItem(storageKey)
    if (!raw) return null

    const parsed = JSON.parse(raw)
    if (!parsed || parsed.cacheKey !== cacheKey) return null

    const timestamp = Number(parsed.timestamp || 0)
    if (!timestamp) {
      window.localStorage.removeItem(storageKey)
      return null
    }

    const ageMs = Date.now() - timestamp
    if (ageMs > PRODUCTS_RESULTS_HARD_TTL_MS) {
      window.localStorage.removeItem(storageKey)
      return null
    }

    return {
      stale: ageMs > PRODUCTS_RESULTS_SOFT_TTL_MS,
      payload: parsed,
    }
  } catch {
    return null
  }
}

const saveProductResultsCache = (cacheKey, payload) => {
  return undefined
}

const normalizeVendorsForSidebar = (items = []) => {
  return (Array.isArray(items) ? items : [])
    .map((vendor) => ({
      name: String(vendor?.name || '').trim(),
      value: String(vendor?.value || vendor?.name || '').trim(),
      count: Number(vendor?.count || 0),
    }))
    .filter((vendor) => vendor.name !== '')
}

const normalizeCategoriesForSidebar = (items = []) => {
  return (Array.isArray(items) ? items : [])
    .map((cat) => ({
      name: String(cat?.name || '').trim(),
      value: String(cat?.value || cat?.name || '').trim(),
      count: Number(cat?.count || 0),
    }))
    .filter((cat) => cat.name !== '' && cat.count > 0)
}

// Restore sidebar caches synchronously at setup so filters show before first API response
;(() => {
  const sv = normalizeVendorsForSidebar(loadSidebarFacetCache(SIDEBAR_VENDORS_STORAGE_KEY))
  if (sv.length > 0) { allVendors.value = sv; availableVendors.value = sv }
  const sc = normalizeCategoriesForSidebar(loadSidebarFacetCache(SIDEBAR_CATEGORIES_STORAGE_KEY))
  if (sc.length > 0) { availableCategories.value = sc }
})()

const currentFilters = ref({
  priceMin: 100,
  priceMax: DEFAULT_BROWSE_MAX_PRICE,
  partNumber: '',
  productType: '',
  vendors: [],
  categories: [],
  lifecycleStatuses: [],
  mediaStatuses: []
})

const hasActiveCatalogState = computed(() => (
  String(searchQuery.value || '').trim() !== ''
  || currentFilters.value.priceMin !== DEFAULT_BROWSE_MIN_PRICE
  || currentFilters.value.priceMax !== DEFAULT_BROWSE_MAX_PRICE
  || String(currentFilters.value.partNumber || '').trim() !== ''
  || String(currentFilters.value.productType || '').trim() !== ''
  || currentFilters.value.vendors.length > 0
  || currentFilters.value.categories.length > 0
  || currentFilters.value.lifecycleStatuses.length > 0
  || currentFilters.value.mediaStatuses.length > 0
  || sortBy.value !== 'relevance'
))

const buildProductsRouteQuery = () => {
  const query = {}

  if (searchQuery.value) query.q = searchQuery.value
  if (currentFilters.value.vendors.length > 0) query.vendors = currentFilters.value.vendors.join(',')
  if (currentFilters.value.categories.length > 0) query.category = currentFilters.value.categories[0]
  if (currentFilters.value.priceMin > 0 && currentFilters.value.priceMin !== 100) {
    query.minPrice = String(currentFilters.value.priceMin)
  }
  if (currentFilters.value.priceMax > 0) query.maxPrice = String(currentFilters.value.priceMax)
  if (currentFilters.value.partNumber) query.partNumber = currentFilters.value.partNumber
  if (currentFilters.value.productType) query.productType = currentFilters.value.productType
  if (currentFilters.value.lifecycleStatuses.length > 0) query.lifecycle = currentFilters.value.lifecycleStatuses.join(',')
  if (currentFilters.value.mediaStatuses.length > 0) query.media = currentFilters.value.mediaStatuses.join(',')
  if (currentPage.value > 1) query.page = String(currentPage.value)

  return query
}

const requiresClientForFilters = (filters) => {
  return (
    String(filters?.partNumber || '').trim().length > 0
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
  let hasImages = 0
  let noImages = 0

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
    if (getPrimaryImageUrl(product)) {
      hasImages += 1
    } else {
      noImages += 1
    }
  })

  const options = [
    { name: '5 Stars', count: fiveStar },
    { name: '4 Stars & Up', count: fourPlus },
    { name: '3 Stars & Up', count: threePlus },
    { name: 'Has Reviews', count: hasReviews },
  ]

  if (SHOW_IMAGE_FILTERS) {
    options.push({ name: 'Has Images', count: hasImages })
    options.push({ name: 'No Images', count: noImages })
  }

  return options
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

  loadLocalSearchHistory()
  const withoutTerm = localSearchHistory.value.filter(
    (item) => normalizeSearchHistoryTerm(item).toLowerCase() !== normalized.toLowerCase()
  )

  localSearchHistory.value = [normalized, ...withoutTerm].slice(0, LOCAL_SEARCH_HISTORY_LIMIT)
  try {
    localStorage.setItem(LOCAL_SEARCH_HISTORY_KEY, JSON.stringify(localSearchHistory.value))
  } catch {
    try {
      localStorage.removeItem(LOCAL_SEARCH_HISTORY_KEY)
    } catch {
      // Storage may be disabled; search must continue without persistence.
    }
  }
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

  // Sort by count descending, then alphabetically
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

  // Show only the most popular vendors with available products.
  source.filter((vendor) => Number(vendor.count || 0) > 0)
    .slice(0, TOP_VENDOR_DISPLAY_LIMIT)
    .forEach((vendor) => {
      tryPush(vendor)
    })

  // Always keep any already-selected vendor visible even if count is 0.
  source.forEach((vendor) => {
    const vendorKey = normalizeVendorKey(vendor.name || vendor.value)
    if (selectedSet.has(vendorKey)) {
      tryPush(vendor)
    }
  })

  return chosen
}

const getCategorySegmentKey = (value) => {
  const raw = String(value || '').trim()
  if (!raw) return ''

  const digits = raw.replace(/\D+/g, '')
  if (!digits) return ''

  if (digits.length === 1) return `0${digits}`
  return digits.slice(0, 2)
}

const getSignificantSegmentKey = (value) => {
  const raw = String(value || '').trim()
  if (!raw) return ''

  const digits = raw.replace(/\D+/g, '').replace(/^0+/, '')
  if (!digits) return ''

  if (digits.length === 1) return `0${digits}`
  return digits.slice(0, 2)
}

const isNumericCategoryLabel = (value) => {
  const normalized = String(value || '').trim()
  if (!normalized) return false
  return /^category\s*\d+$/i.test(normalized) || /^\d+$/.test(normalized)
}

const getProductCategoryLabel = (product) => {
  // Match product category code against API-provided category segment keys.
  const categoryCode = String(product?.categoryCode || product?.specifications?.categoryCode || '').trim()
  if (categoryCode && availableCategories.value.length > 0) {
    const segmentCandidates = [
      getCategorySegmentKey(categoryCode),
      getSignificantSegmentKey(categoryCode),
    ].filter(Boolean)

    const match = availableCategories.value.find((category) => {
      const categoryKey = getCategorySegmentKey(category?.value)
      return segmentCandidates.includes(categoryKey)
    })

    if (match?.name) return match.name
  }

  const rawCategory = [
    product?.flatCategoryName,
    product?.categoryName,
    product?.category,
    product?.specifications?.categoryName,
  ]
    .map((value) => String(value || '').trim())
    .find((value) => value && !isNumericCategoryLabel(value))

  if (rawCategory) {
    return rawCategory
  }

  return categoryCode ? 'Other' : 'Uncategorized'
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

  // Always strip zero-price products client-side regardless of what the API returned.
  let filtered = products.value.filter((p) => {
    const price = Number(p?.productPrice?.[0]?.rsPrice ?? p?.price ?? 0)
    return price > 0
  })

  // Filter by part number
  if (filters.partNumber && String(filters.partNumber).trim().length > 0) {
    const partQuery = String(filters.partNumber).toLowerCase().trim()
    filtered = filtered.filter((product) => String(product.mfgPartNo || '').toLowerCase().includes(partQuery))
  }
  
  // Category filtering is handled server-side via the `category` API param.
  
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

  if ((filters.mediaStatuses || []).length > 0) {
    filtered = filtered.filter((product) => {
      const stats = getProductReviewStats(product.productId)
      return filters.mediaStatuses.some((status) => {
        if (status === 'No Images') return !getPrimaryImageUrl(product)
        if (status === 'Has Images') return !!getPrimaryImageUrl(product)
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

const totalProductsLabel = computed(() => {
  if (serverPaged.value && serverTotalIsEstimate.value) {
    return `${totalProducts.value}+`
  }

  return String(totalProducts.value)
})

const totalPages = computed(() => Math.max(1, Math.ceil(totalProducts.value / ITEMS_PER_PAGE)))

const visibleProductsCount = computed(() => paginatedProducts.value.length)

const visibleProductsRangeLabel = computed(() => {
  if (totalProducts.value === 0 || visibleProductsCount.value === 0) {
    return `0 of ${totalProductsLabel.value} products`
  }

  const rangeStart = ((currentPage.value - 1) * ITEMS_PER_PAGE) + 1
  const rangeEnd = rangeStart + visibleProductsCount.value - 1

  if (rangeStart === rangeEnd) {
    return `${rangeStart} of ${totalProductsLabel.value} products`
  }

  return `${rangeStart}-${rangeEnd} of ${totalProductsLabel.value} products`
})

const totalPagesLabel = computed(() => {
  if (serverPaged.value && serverTotalIsEstimate.value && serverHasMore.value) {
    return `${totalPages.value}+`
  }

  return String(totalPages.value)
})

const getProductUnitPrice = (product) => {
  const price = product?.productPrice?.[0]?.rsPrice
  return price != null ? Number(price) : null
}

const paginatedProducts = computed(() => {
  const source = filteredProducts.value
  const indexed = source.map((item, index) => ({ item, index }))

  const mode = sortBy.value
  indexed.sort((left, right) => {
    if (mode === 'price_asc' || mode === 'price_desc') {
      const lp = getProductUnitPrice(left.item)
      const rp = getProductUnitPrice(right.item)
      if (lp === null && rp === null) return left.index - right.index
      if (lp === null) return 1
      if (rp === null) return -1
      const diff = lp - rp
      if (diff !== 0) return mode === 'price_asc' ? diff : -diff
      return left.index - right.index
    }
    if (mode === 'name_asc' || mode === 'name_desc') {
      const ln = String(left.item.productName || '').toLowerCase()
      const rn = String(right.item.productName || '').toLowerCase()
      const cmp = ln.localeCompare(rn)
      if (cmp !== 0) return mode === 'name_asc' ? cmp : -cmp
      return left.index - right.index
    }
    // relevance: stock rank first, then personalization order
    const rankDiff = getStockRank(left.item) - getStockRank(right.item)
    if (rankDiff !== 0) return rankDiff
    return left.index - right.index
  })
  const sorted = indexed.map(({ item }) => item)

  if (serverPaged.value) {
    return sorted
  }

  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return sorted.slice(start, end)
})

const pageNumbers = computed(() => {
  const pages = []
  const rowWidth = paginationWidth.value || 360
  const navigationWidth = rowWidth >= 640 ? 230 : 100
  const maxPagesToShow = Math.max(3, Math.min(10, Math.floor((rowWidth - navigationWidth) / 44)))
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
let activeSearchRequestId = 0
const pendingReviewStats = new Set()
const PRODUCTS_API_TIMEOUT_MS = 45000
const PRODUCTS_API_RETRY_COUNT = 1
const FACET_REFRESH_MIN_INTERVAL_MS = 30000
const FACET_REFRESH_DEFER_MS = 120
let lastFacetRefreshAt = 0
let facetRefreshTimer = null

const isRetryableProductsError = (err) => {
  if (!err) return false

  const code = String(err.code || '').toUpperCase()
  const message = String(err.message || '').toLowerCase()

  return code === 'ECONNABORTED'
    || code === 'ERR_NETWORK'
    || message.includes('timeout')
    || message.includes('network error')
}

const getProductsApiErrorMessage = (err) => {
  if (isRetryableProductsError(err)) {
    return 'The products service is taking too long to respond. Please retry in a few seconds.'
  }

  return err?.response?.data?.message || err?.message || 'Failed to fetch products'
}

const getProductsWithRetry = async (path, options = {}, retries = PRODUCTS_API_RETRY_COUNT) => {
  let attempt = 0
  let lastError = null

  while (attempt <= retries) {
    try {
      return await api.get(path, {
        ...options,
        timeout: PRODUCTS_API_TIMEOUT_MS,
      })
    } catch (err) {
      lastError = err
      if (!isRetryableProductsError(err) || attempt >= retries) {
        throw err
      }
      attempt += 1
    }
  }

  throw lastError || new Error('Failed to fetch products')
}

const shouldRefreshFacetsNow = () => {
  const hasSearch = normalizeSearchText(searchQuery.value) !== ''
  const hasFacetSelection = (currentFilters.value.vendors?.length || 0) > 0
    || (currentFilters.value.categories?.length || 0) > 0

  // For plain text searches, prioritize product cards and skip expensive facet
  // refreshes that can queue behind the same PHP worker.
  if (hasSearch && !hasFacetSelection) {
    return false
  }

  return true
}

const queueFacetRefresh = ({ force = false } = {}) => {
  if (facetRefreshTimer) {
    clearTimeout(facetRefreshTimer)
    facetRefreshTimer = null
  }

  facetRefreshTimer = setTimeout(async () => {
    facetRefreshTimer = null

    if (!force && !shouldRefreshFacetsNow()) {
      return
    }

    const now = Date.now()
    if (!force && (now - lastFacetRefreshAt) < FACET_REFRESH_MIN_INTERVAL_MS) {
      return
    }

    lastFacetRefreshAt = now
    await Promise.all([fetchVendors(), fetchCategories()])
  }, FACET_REFRESH_DEFER_MS)
}

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

  idsToFetch.forEach((id) => pendingReviewStats.add(id))

  try {
    const response = await api.get('/products/reviews/stats', {
      params: { ids: idsToFetch.join(',') },
    })
    const statsByProduct = response.data?.data || {}
    const updates = {}

    idsToFetch.forEach((id) => {
      const stats = statsByProduct[id] || {}
      updates[id] = {
        total: Number(stats.total || 0),
        average: Number(stats.average || 0),
      }
    })

    reviewStatsByProduct.value = { ...reviewStatsByProduct.value, ...updates }
  } catch (statsError) {
    const updates = {}
    idsToFetch.forEach((id) => {
      updates[id] = { total: 0, average: 0 }
    })
    reviewStatsByProduct.value = { ...reviewStatsByProduct.value, ...updates }
    console.warn('Bulk review stats request failed; continuing without ratings.', statsError)
  } finally {
    idsToFetch.forEach((id) => pendingReviewStats.delete(id))
  }
}

const fetchAllProductPages = async (params) => {
  // Fetch up to the storefront cap for client-side filtering (partNumber, lifecycle, media)
  const response = await getProductsWithRetry('/products', {
    params: {
      ...params,
      page: 1,
      per_page: 3000,
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

const getCatalogPricingScope = () => {
  if (!authStore.isAuthenticated || !authStore.user?.id) {
    return 'guest'
  }

  const discount = Number(authStore.user?.special_pricing_percent || 0)
  return `user:${authStore.user.id}:discount:${discount.toFixed(2)}`
}

const getCacheKey = (filters, page = 1, useServerPaged = false) => {
  return JSON.stringify({
    curatedVersion: CURATED_CACHE_VERSION,
    pricingScope: getCatalogPricingScope(),
    productType: filters.productType,
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

const applyProductResultPayload = (result = {}) => {
  resetImgErrorMap()
  products.value = Array.isArray(result.data) ? result.data : []
  serverTotal.value = Number(result.total || products.value.length || 0)
  serverPaged.value = Boolean(result.serverPaged)
  serverHasMore.value = Boolean(result.hasMore)
  serverTotalIsEstimate.value = Boolean(result.totalIsEstimate)
  supplierLookupQueued.value = Boolean(result.supplierLookupQueued)
  if (!Boolean(result.serverPaged)) {
    updateVendorCounts(products.value)
  }
}

const isDefaultCuratedBrowse = (filters = currentFilters.value) => {
  const hasClientOnlyFilters = requiresClientForFilters(filters)
  return !searchQuery.value && (filters?.vendors || []).length === 0 && !hasClientOnlyFilters
}



const performSearch = async (resetPage = true) => {
  const requestId = ++activeSearchRequestId
  error.value = ''
  dismissSearchSuggestions()
  if (resetPage) {
    currentPage.value = 1
    loading.value = true
  } else {
    if (products.value.length === 0) {
      loading.value = true
    } else {
      pageLoading.value = true
    }
  }

  const useServerPaged = !requiresClientSideFiltering.value
  serverPaged.value = useServerPaged

  const normalizedQuery = normalizeSearchText(searchQuery.value)
  if (resetPage && normalizedQuery) {
    products.value = []
    serverTotal.value = 0
    supplierLookupQueued.value = false
  }
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

  // First use fast in-memory cache (soft TTL).
  if (requestCache.has(cacheKey)) {
    const cached = requestCache.get(cacheKey)
    if (Date.now() - cached.timestamp < PRODUCTS_RESULTS_SOFT_TTL_MS) {
      applyProductResultPayload(cached)
      loading.value = false
      pageLoading.value = false
      return
    }
  }

  // Then use persistent local cache (survives reloads) for instant paint.
  const persistedCache = loadProductResultsCache(cacheKey)
  if (persistedCache?.payload) {
    applyProductResultPayload(persistedCache.payload)
    loading.value = false
    pageLoading.value = false

    // Search URLs must always revalidate after reload. Cached search payloads are
    // useful for instant paint, but treating them as final made navbar searches
    // appear to do nothing when an older empty/stale result was stored locally.
    if (!persistedCache.stale && !normalizedQuery) {
      return
    }
  }

  // Check if request is already in progress (prevent duplicate requests)
  if (pendingRequests.has(cacheKey)) {
    try {
      const result = await pendingRequests.get(cacheKey)
      if (requestId !== activeSearchRequestId) return
      products.value = result.data
      serverTotal.value = Number(result.total || result.data?.length || 0)
      serverPaged.value = Boolean(result.serverPaged)
      serverHasMore.value = Boolean(result.hasMore)
      serverTotalIsEstimate.value = Boolean(result.totalIsEstimate)
      if (!Boolean(result.serverPaged)) {
        updateVendorCounts(result.data)
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
        catalog_revision: CATALOG_REQUEST_REVISION,
        curated_it_mix: true,
        hide_zero_price: true,
        catalog_clean: true,
      }

      if (currentFilters.value.productType) {
        params.product_type = currentFilters.value.productType
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

      if (currentFilters.value.categories.length > 0) {
        const selectedCategoryName = currentFilters.value.categories[0]
        const categoryEntry = availableCategories.value.find(c => c.name === selectedCategoryName)
        params.category = categoryEntry?.value || selectedCategoryName
      }

      if (currentFilters.value.priceMin > 0) {
        params.min_price = currentFilters.value.priceMin
      }
      if (currentFilters.value.priceMax > 0) {
        params.max_price = currentFilters.value.priceMax
      }

      let loadedProducts = []
      let loadedTotal = 0
      let loadedHasMore = false
      let loadedTotalIsEstimate = false
      let loadedSupplierLookupQueued = false

      if (useServerPaged) {
        const response = await getProductsWithRetry('/products', {
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
        loadedHasMore = Boolean(payload.has_more)
        loadedTotalIsEstimate = Boolean(payload.total_is_estimate)
        loadedSupplierLookupQueued = Boolean(payload.supplier_lookup_queued)
      } else {
        loadedProducts = await fetchAllProductPages(params)
        loadedTotal = loadedProducts.length
      }

      if (requestId !== activeSearchRequestId) {
        return { data: [], total: 0, serverPaged: useServerPaged, stale: true }
      }

      resetImgErrorMap()
      products.value = loadedProducts

      serverTotal.value = loadedTotal
      serverHasMore.value = loadedHasMore
      serverTotalIsEstimate.value = loadedTotalIsEstimate
      supplierLookupQueued.value = loadedSupplierLookupQueued

      if (Array.isArray(products.value)) {
        // Vendor counts should only be recomputed from full client-side datasets.
        if (!useServerPaged) {
          updateVendorCounts(loadedProducts)
        }
        
        // Cache results in-memory and persist for fast reloads.
        const cachePayload = {
          data: products.value,
          total: loadedTotal,
          serverPaged: useServerPaged,
          hasMore: loadedHasMore,
          totalIsEstimate: loadedTotalIsEstimate,
          supplierLookupQueued: loadedSupplierLookupQueued,
          timestamp: Date.now()
        }
        if (!loadedSupplierLookupQueued) {
          requestCache.set(cacheKey, cachePayload)
          saveProductResultsCache(cacheKey, cachePayload)
        }

        if (useServerPaged && ENABLE_SERVER_PREFETCH) {
          prefetchPage(currentPage.value + 1)
          if (currentPage.value > 1) prefetchPage(currentPage.value - 1)
        }

        // Refresh heavy facet queries lazily after products are rendered.
        if (requestId === activeSearchRequestId) {
          queueFacetRefresh()
        }
        
        return {
          data: products.value,
          total: loadedTotal,
          serverPaged: useServerPaged,
          hasMore: loadedHasMore,
          totalIsEstimate: loadedTotalIsEstimate,
          supplierLookupQueued: loadedSupplierLookupQueued,
        }
      } else {
        error.value = 'Failed to fetch products'
        return { data: [], total: 0, serverPaged: useServerPaged }
      }
    } catch (err) {
      error.value = getProductsApiErrorMessage(err)
      console.error('❌ Product fetch error:', err)
      return { data: [], total: 0, serverPaged: useServerPaged }
    }
  })()

  pendingRequests.set(cacheKey, requestPromise)

  try {
    await requestPromise
  } finally {
    if (requestId === activeSearchRequestId) {
      loading.value = false
      pageLoading.value = false
    }
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
  const requestId = ++activeVendorFacetRequestId
  vendorsLoading.value = true
  // Build vendor list from API response — show ALL vendors with their counts
  const buildFromApi = (apiVendors = []) => {
    const aggregated = new Map()
    apiVendors.forEach((vendor) => {
      const name = String(vendor.vendorName || vendor.vendorId || '').trim()
      if (!name) return
      const key = toCanonicalVendorKey(name)
      if (!key) return
      const existing = aggregated.get(key)
      if (existing) {
        existing.count += Number(vendor.count || 0)
      } else {
        aggregated.set(key, {
          name,
          value: name,
          count: Number(vendor.count || 0),
        })
      }
    })
    return Array.from(aggregated.values()).sort((a, b) => {
      if (b.count !== a.count) return b.count - a.count
      return a.name.localeCompare(b.name)
    })
  }

  if (!ENABLE_VENDOR_COUNTS_API) {
    vendorsLoading.value = false
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

    if (currentFilters.value.productType) {
      vendorParams.product_type = currentFilters.value.productType
    }

    if (isDefaultBrowse && Number(currentFilters.value.priceMin || 0) <= 0) {
      vendorParams.min_price = DEFAULT_BROWSE_MIN_PRICE
    } else if (Number(currentFilters.value.priceMin || 0) > 0) {
      vendorParams.min_price = currentFilters.value.priceMin
    }

    if (Number(currentFilters.value.priceMax || 0) > 0) {
      vendorParams.max_price = currentFilters.value.priceMax
    }

    if (searchQuery.value) {
      vendorParams.search = searchQuery.value
    }

    if (currentFilters.value.categories.length > 0) {
      const selectedCategoryName = currentFilters.value.categories[0]
      const categoryEntry = availableCategories.value.find(c => c.name === selectedCategoryName)
      vendorParams.category = categoryEntry?.value || selectedCategoryName
    }

    const response = await api.get('/vendors', { params: vendorParams })

    const rawVendorData = response.data?.data || []
    const apiVendors = Array.isArray(rawVendorData) ? rawVendorData : (rawVendorData.records || [])
    const mappedVendors = normalizeVendorsForSidebar(buildFromApi(apiVendors))

    if (requestId === activeVendorFacetRequestId) {
      allVendors.value = mappedVendors
      availableVendors.value = mappedVendors
      saveSidebarFacetCache(SIDEBAR_VENDORS_STORAGE_KEY, mappedVendors)
    }
  } catch (err) {
    console.error('Error fetching vendors:', err)
  } finally {
    if (requestId === activeVendorFacetRequestId) vendorsLoading.value = false
  }
}

const fetchCategories = async () => {
  const requestId = ++activeCategoryFacetRequestId
  categoriesLoading.value = true
  try {
    const isDefaultBrowse =
      !searchQuery.value
      && currentFilters.value.vendors.length === 0
      && !requiresClientForFilters(currentFilters.value)

    const params = {
      catalog_revision: CATALOG_REQUEST_REVISION,
      hide_zero_price: true,
      catalog_clean: true,
    }

    if (currentFilters.value.productType) {
      params.product_type = currentFilters.value.productType
    }

    if (isDefaultBrowse && Number(currentFilters.value.priceMin || 0) <= 0) {
      params.min_price = DEFAULT_BROWSE_MIN_PRICE
    } else if (Number(currentFilters.value.priceMin || 0) > 0) {
      params.min_price = currentFilters.value.priceMin
    }

    if (Number(currentFilters.value.priceMax || 0) > 0) {
      params.max_price = currentFilters.value.priceMax
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

    const response = await api.get('/categories', { params })
    const rawData = response.data?.data || []

    if (Array.isArray(rawData) && requestId === activeCategoryFacetRequestId) {
      const normalized = normalizeCategoriesForSidebar(rawData)
      availableCategories.value = normalized
      if (normalized.length > 0) {
      saveSidebarFacetCache(SIDEBAR_CATEGORIES_STORAGE_KEY, normalized)
      }
    }
  } catch (err) {
    console.error('Error fetching categories:', err)
  } finally {
    if (requestId === activeCategoryFacetRequestId) categoriesLoading.value = false
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

const handleFilterChange = (filters) => {
  const normalizeFilters = (value = {}) => ({
    priceMin: Number(value.priceMin ?? 0),
    priceMax: Number(value.priceMax ?? DEFAULT_BROWSE_MAX_PRICE),
    partNumber: String(value.partNumber ?? '').trim(),
    productType: String(value.productType ?? '').toLowerCase() === 'software' ? 'software' : (String(value.productType ?? '').toLowerCase() === 'hardware' ? 'hardware' : ''),
    vendors: Array.isArray(value.vendors) ? [...value.vendors].map((v) => String(v).trim()) : [],
    categories: Array.isArray(value.categories) ? [...value.categories].map((v) => String(v).trim()) : [],
    lifecycleStatuses: Array.isArray(value.lifecycleStatuses) ? [...value.lifecycleStatuses].map((v) => String(v).trim()) : [],
    mediaStatuses: Array.isArray(value.mediaStatuses)
      ? [...value.mediaStatuses]
          .map((v) => String(v).trim())
          .filter((status) => SHOW_IMAGE_FILTERS || !['Has Images', 'No Images'].includes(status))
      : [],
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
  sortBy.value = 'relevance'
  searchSuggestionItems.value = []
  showSearchSuggestions.value = false
  activeSuggestionIndex.value = -1
  currentPage.value = 1
  currentFilters.value = {
    priceMin: DEFAULT_BROWSE_MIN_PRICE,
    priceMax: DEFAULT_BROWSE_MAX_PRICE,
    partNumber: '',
    productType: '',
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
    catalog_revision: CATALOG_REQUEST_REVISION,
    curated_it_mix: true,
  }

  if (currentFilters.value.productType) {
    params.product_type = currentFilters.value.productType
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
  if (currentFilters.value.categories.length > 0) {
    const selectedCategoryName = currentFilters.value.categories[0]
    const categoryEntry = availableCategories.value.find(c => c.name === selectedCategoryName)
    params.category = categoryEntry?.value || selectedCategoryName
  }
  if (isDefaultBrowse && Number(currentFilters.value.priceMin || 0) <= 0) {
    params.min_price = DEFAULT_BROWSE_MIN_PRICE
  }
  if (currentFilters.value.priceMin > 0) params.min_price = currentFilters.value.priceMin
  if (currentFilters.value.priceMax > 0) params.max_price = currentFilters.value.priceMax

  const cacheKey = getCacheKey(currentFilters.value, page, true)

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

      const cachePayload = {
        data: records,
        total: prefetchedTotal,
        serverPaged: true,
        timestamp: Date.now()
      }
      requestCache.set(cacheKey, cachePayload)
      saveProductResultsCache(cacheKey, cachePayload)
    }
  }).catch(() => {
    // Silently ignore prefetch errors
  })
}

const viewProductDetails = (product) => {
  router.push({
    name: 'product-detail',
    params: { id: product.productId },
    query: {
      returnTo: route.fullPath,
    },
  })
}

const addToQuote = (product) => {
  if (isOutOfStock(product)) {
    toastStore.addToast(`"${product.productName}" is out of stock and cannot be added to quote`, 'error')
    return
  }

  const added = cartStore.addItem(product, 1)
  if (!added) {
    toastStore.addToast('This product cannot be added to quote right now', 'error')
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

const fileNameFromImageValue = (value) => {
  const rawUrl = String(value || '').trim()
  if (!rawUrl) return ''

  const path = rawUrl.split('?')[0].split('#')[0]
  const fileName = path.split('/').filter(Boolean).pop() || ''

  return /\.(?:jpg|jpeg|png|gif|webp|avif)$/i.test(fileName) ? fileName : ''
}

const getExpectedImageFileName = (product) => {
  const imageSources = [
    ...(Array.isArray(product?.productImages) ? product.productImages : []),
    ...(Array.isArray(product?.images) ? product.images : []),
    product?.image_url,
    product?.thumbnailUrl,
    product?.thumbnail,
  ]

  for (const image of imageSources) {
    const fileName = typeof image === 'string'
      ? fileNameFromImageValue(image)
      : fileNameFromImageValue(image?.imagePath || image?.imageUrl || image?.imageURL || image?.image_url || image?.url || image?.thumbnailUrl)

    if (fileName) return fileName
  }

  const identifier = String(product?.productId || product?.id || (getProductSku(product) !== 'N/A' ? getProductSku(product) : 'product'))
    .trim()
    .replace(/[^a-z0-9._-]+/gi, '-')
    .replace(/^-+|-+$/g, '')

  return `${identifier || 'product'}.jpg`
}

const getAvailableQuantity = (product) => {
  const qty = Number(
    product?.availableQuantity ??
    product?.totalQuantity ??
    product?.qty ??
    NaN
  )

  return Number.isFinite(qty) ? Math.max(0, qty) : null
}

const isOutOfStock = (product) => getAvailableQuantity(product) === 0 && !isSupplierOrderable(product)

const getStockRank = (product) => {
  const qty = getAvailableQuantity(product)
  if (qty === null) return 1
  return qty <= 0 ? 2 : 0
}

const getAvailabilityByWarehouse = (product) => {
  return Array.isArray(product?.AvailabilityByWarehouse) ? product.AvailabilityByWarehouse : []
}

const getStockLabel = (product) => {
  const qty = getAvailableQuantity(product)
  if (qty !== null) {
    if (qty > 0) return `Stock: ${qty}`
    return isSupplierOrderable(product) ? 'Supplier orderable' : 'Out of stock'
  }

  return 'Stock: Check availability'
}

const getStockTone = (product) => {
  const qty = getAvailableQuantity(product)
  if (qty === null) return 'text-amber-600'
  return qty > 0 || isSupplierOrderable(product) ? 'text-emerald-600' : 'text-red-600'
}

const getWarehouseSummary = (product) => {
  const warehouses = getAvailabilityByWarehouse(product)
  if (warehouses.length > 0) {
    return `${warehouses.length} warehouse${warehouses.length === 1 ? '' : 's'}`
  }

  const qty = getAvailableQuantity(product)
  if (qty !== null) {
    return qty > 0 ? 'Available now' : isSupplierOrderable(product) ? 'Special order' : 'Request quote'
  }

  return 'No live count'
}

const getProductMetaPrimary = (product) => {
  const billingModel = String(product?.billingModel || '').trim()
  if (billingModel) return billingModel

  const qty = getAvailableQuantity(product)
  if (qty !== null) {
    return qty > 0 ? 'In Stock' : isSupplierOrderable(product) ? 'Supplier Orderable' : 'Out of Stock'
  }

  return product?.discontinueProduct ? 'Legacy Product' : 'Catalog Product'
}

const getProductMetaSecondary = (product) => {
  const billingFrequency = String(product?.billingFrequency || '').trim()
  if (billingFrequency) return billingFrequency

  const qty = getAvailableQuantity(product)
  if (qty !== null) {
    return qty > 0 ? `${qty} available` : isSupplierOrderable(product) ? 'Special order' : 'Request quote'
  }

  return 'Request quote'
}

const getPrimaryImageUrl = (product) => {
  const candidates = []

  const appendUrl = (value) => {
    const rawUrl = String(value || '').trim()
    if (!rawUrl) return
    candidates.push(resolveProductImageUrl(rawUrl))
  }

  const appendImages = (images) => {
    if (!Array.isArray(images)) return

    images.forEach((image) => {
      if (typeof image === 'string') {
        appendUrl(image)
        return
      }

      if (image && typeof image === 'object') {
        appendUrl(image.imageUrl || image.imageURL || image.image_url || image.url || image.thumbnailUrl)
      }
    })
  }

  appendImages(product?.productImages)
  appendImages(product?.images)
  appendUrl(product?.image_url)
  appendUrl(product?.thumbnailUrl)
  appendUrl(product?.thumbnail)

  if (candidates.length === 0) return ''

  const localCandidate = candidates.find((url) => url.startsWith('/images/') || url.includes('/images/products/'))
  return localCandidate || candidates[0]
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
        imageUrl: getPrimaryImageUrl(product),
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
  const converted = convertFromUsd(Math.max(0, Number(baseUsdPrice || 0)))
  return formatWithCurrency(converted)
}

const formatReferencePrice = (baseUsdPrice) => {
  const converted = convertFromUsd(Math.max(0, Number(baseUsdPrice || 0)))
  return formatWithCurrency(converted)
}

const getProductMsrp = (product) => {
  return Number(product?.msrp || product?.regularPrice || product?.productPrice?.[0]?.msrp || 0)
}

const getCustomerPrice = (product) => {
  return Number(product?.productPrice?.[0]?.rsPrice || 0)
}

const hasMsrpDiscount = (product) => {
  const msrp = getProductMsrp(product)
  const customerPrice = getCustomerPrice(product)
  return msrp > 0 && customerPrice > 0 && msrp > customerPrice + 0.005
}

const getMsrpSavingsPercent = (product) => {
  if (!hasMsrpDiscount(product)) return 0
  return Math.max(1, Math.round(((getProductMsrp(product) - getCustomerPrice(product)) / getProductMsrp(product)) * 100))
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

watch(paginationRow, (nextRow, previousRow) => {
  if (previousRow) {
    paginationResizeObserver?.unobserve(previousRow)
  }
  if (nextRow) {
    paginationWidth.value = nextRow.getBoundingClientRect().width
    paginationResizeObserver?.observe(nextRow)
  }
})

watch(
  () => route.fullPath,
  () => {
    const routeFilters = parseProductsRouteFilters(route)
    const newQuery = routeFilters.q
    const queryChanged = String(newQuery || '').trim() !== String(searchQuery.value || '').trim()
    const newVendor = routeFilters.vendor
    const newVendors = routeFilters.vendors
    const newCategory = routeFilters.category
    const minPrice = routeFilters.minPrice
    const maxPrice = routeFilters.maxPrice
    const partNumber = routeFilters.partNumber
    const productType = routeFilters.productType
    const lifecycle = routeFilters.lifecycle
    const media = routeFilters.media
    const page = routeFilters.page
    const hasExplicitPage = page !== undefined && String(page).trim() !== ''
    searchQuery.value = newQuery ? String(newQuery) : ''

    const hasVendorQuery = newVendor !== undefined || newVendors !== undefined
    const hasCategoryQuery = newCategory !== undefined
    const hasMinPriceQuery = minPrice !== undefined
    const hasMaxPriceQuery = maxPrice !== undefined
    const hasPartNumberQuery = partNumber !== undefined
    const hasProductTypeQuery = productType !== undefined
    const hasLifecycleQuery = lifecycle !== undefined
    const hasMediaQuery = media !== undefined
    const isStandaloneSearch = Boolean(newQuery)
      && !hasVendorQuery
      && !hasCategoryQuery
      && !hasMinPriceQuery
      && !hasMaxPriceQuery
      && !hasPartNumberQuery
      && !hasProductTypeQuery
      && !hasLifecycleQuery
      && !hasMediaQuery

    let nextVendors = currentFilters.value.vendors
    if (isStandaloneSearch) {
      nextVendors = []
    } else if (hasVendorQuery) {
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
    if (isStandaloneSearch) {
      nextCategories = []
    } else if (hasCategoryQuery) {
      nextCategories = newCategory ? [String(newCategory)] : []
    }

    let nextLifecycleStatuses = currentFilters.value.lifecycleStatuses
    if (isStandaloneSearch) {
      nextLifecycleStatuses = []
    } else if (hasLifecycleQuery) {
      nextLifecycleStatuses = lifecycle
        ? String(lifecycle).split(',').map((value) => value.trim()).filter(Boolean)
        : []
    }

    let nextMediaStatuses = currentFilters.value.mediaStatuses
    if (isStandaloneSearch) {
      nextMediaStatuses = []
    } else if (hasMediaQuery) {
      nextMediaStatuses = media
        ? String(media).split(',').map((value) => value.trim()).filter(Boolean)
        : []
    }

    if (!SHOW_IMAGE_FILTERS) {
      nextMediaStatuses = nextMediaStatuses.filter((status) => !['Has Images', 'No Images'].includes(status))
    }

    currentFilters.value = {
      ...currentFilters.value,
      priceMin: isStandaloneSearch ? 0 : (hasMinPriceQuery ? Number(minPrice || 0) : 100),
      priceMax: isStandaloneSearch ? 0 : (hasMaxPriceQuery ? Number(maxPrice || 0) : DEFAULT_BROWSE_MAX_PRICE),
      partNumber: isStandaloneSearch ? '' : (hasPartNumberQuery ? String(partNumber || '').trim() : ''),
      productType: isStandaloneSearch ? '' : (hasProductTypeQuery ? String(productType || '').trim() : ''),
      vendors: nextVendors,
      categories: nextCategories,
      lifecycleStatuses: nextLifecycleStatuses,
      mediaStatuses: nextMediaStatuses,
    }

    currentPage.value = Math.max(1, Number(page || 1) || 1)

    if (ownRouterReplace) {
      ownRouterReplace = false
      return
    }

    // A page encoded in the URL is authoritative during reload/deep-linking.
    // Only reset for a genuinely new search that did not request a page.
    performSearch(queryChanged && !hasExplicitPage)
    if (newQuery) {
      nextTick(() => scrollToCatalog())
    }
  },
  { immediate: true }
)

watch(
  () => [
    searchQuery.value,
    currentFilters.value.priceMin,
    currentFilters.value.priceMax,
    currentFilters.value.partNumber,
    currentFilters.value.productType,
    currentFilters.value.vendors.join(','),
    currentFilters.value.categories.join(','),
    currentFilters.value.lifecycleStatuses.join(','),
    currentFilters.value.mediaStatuses.join(','),
    currentPage.value,
  ],
  () => {
    const nextQuery = buildProductsRouteQuery()
    const nextLocation = buildProductsLocation(nextQuery)
    const targetPath = router.resolve(nextLocation).fullPath

    if (route.fullPath === targetPath) {
      return
    }

    ownRouterReplace = true
    router.replace(nextLocation)
  }
)

watch(
  () => [authStore.isAuthenticated, authStore.user?.id, authStore.user?.special_pricing_percent],
  () => {
    requestCache.clear()
    pendingRequests.clear()
    performSearch(false)
  }
)

onMounted(async () => {
  paginationResizeObserver = new ResizeObserver((entries) => {
    paginationWidth.value = entries[0]?.contentRect?.width || 0
  })
  if (paginationRow.value) {
    paginationResizeObserver.observe(paginationRow.value)
  }

  if (route.query.next === 'login') {
    const query = {}
    if (route.query.email) query.email = String(route.query.email)
    if (route.query.activation) query.activation = String(route.query.activation)
    if (route.query.message) query.message = String(route.query.message)

    router.replace({ name: 'login', query })
    return
  }

  loadLocalSearchHistory()

  // Product searches refresh facets so the sidebar always matches the cards.
  await Promise.all([
    loadSearchProfile(),
    loadPricingSettings(true).then(() => { pricingReady.value = true }),
  ])

  // Initial facet load is non-blocking so product cards render first.
  queueFacetRefresh({ force: true })
})

let paginationResizeObserver = null
onUnmounted(() => paginationResizeObserver?.disconnect())
</script>
