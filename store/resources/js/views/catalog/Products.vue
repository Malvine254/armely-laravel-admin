<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
              <input v-model="searchQuery" @keyup.enter="performSearch" type="text" placeholder="Search products..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition">
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
            @filter-change="handleFilterChange"
          />
        </div>

        <!-- Products Section -->
        <div class="flex-1 min-w-0">
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-12">
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
          <div v-else-if="!loading" class="">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
              <p class="text-gray-600 font-medium">Showing <span class="font-bold" style="color: #2F5597;">{{ paginatedProducts.length }}</span> of <span class="font-bold" style="color: #2F5597;">{{ totalProducts }}</span> products</p>
              <span class="text-gray-600 text-sm">Page <span class="font-bold" style="color: #2F5597;">{{ currentPage }}</span> of <span class="font-bold" style="color: #2F5597;">{{ totalPages }}</span></span>
            </div>

            <!-- Empty State -->
            <div v-if="totalProducts === 0" class="text-center py-12 bg-white rounded-xl border border-gray-200">
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
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
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
                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ product.productName }}</h3>
                    <span v-if="product.discontinueProduct" class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">EOL</span>
                    <span v-else class="ml-2 px-2 py-1 text-xs font-semibold rounded" style="background-color: #cce4f4; color: #2F5597;">Active</span>
                  </div>
                  <p class="text-xs text-gray-600 mb-2">SKU: {{ product.mfgPartNo || 'N/A' }}</p>
                  <p class="text-xs text-gray-600 mb-3">Vendor: {{ product.vendorId }}</p>
                  
                  <!-- Pricing -->
                  <div v-if="product.productPrice && product.productPrice.length > 0" class="mb-4">
                    <p class="text-2xl font-bold" style="color: #2F5597;">${{ formatPrice(product.productPrice[0].rsPrice) }}</p>
                    <p class="text-xs text-gray-600">Min Qty: {{ product.productPrice[0].minQty }}</p>
                  </div>

                  <!-- Features -->
                  <div class="mb-4 flex flex-wrap gap-1">
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ product.billingModel || 'N/A' }}</span>
                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ product.billingFrequency || 'N/A' }}</span>
                  </div>

                  <!-- Actions -->
                  <div class="flex gap-2 w-full">
                    <button @click="viewProductDetails(product)" class="flex-1 px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">View Details</button>
                    <button @click="addToQuote(product)" class="px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'" title="Add to Quote">+</button>
                    <button @click="toggleFavorite(product)" class="px-3 py-2 rounded-lg transition border" :style="isFavorite(product.productId) ? { backgroundColor: '#cce4f4', borderColor: '#2F5597', color: '#2F5597' } : { borderColor: '#d1d5db', color: '#4b5563' }" :title="isFavorite(product.productId) ? 'Remove from Favorites' : 'Add to Favorites'">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useCartStore } from '../../stores/cartStore'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useAuthStore } from '../../stores/authStore'
import { trackSearchTerm, hasTrackingConsent, getSearchProfileTerms } from '../../services/searchInsights'
import Navbar from '../../components/Navbar.vue'
import FilterSidebar from '../../components/FilterSidebar.vue'

const router = useRouter()
const route = useRoute()
const toastStore = useToastStore()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const ITEMS_PER_PAGE = 9
const API_PAGE_SIZE = 100
const SEARCH_TRACK_DEBOUNCE_MS = 15000
const PROFILE_TERM_LIMIT = 25

const products = ref([])
const loading = ref(false)
const error = ref('')
const searchQuery = ref('')
const currentPage = ref(1)
const lastTrackedTerm = ref('')
const lastTrackedAt = ref(0)

const availableVendors = ref([])
const availableCategories = ref([])

const currentFilters = ref({
  priceMin: 0,
  priceMax: 10000,
  vendors: [],
  categories: [],
  billingModels: []
})

const normalizeSearchText = (value) => String(value || '').toLowerCase().trim().replace(/\s+/g, ' ')

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
      
      // Check in productCategories
      if (product.productCategories && Array.isArray(product.productCategories)) {
        return product.productCategories.some(cat => {
          const categoryName = typeof cat === 'object' ? cat.categoryName : cat
          return categoryName === selectedCategoryValue
        })
      }

      // PriceAvailability products use categoryCode
      if (product.categoryCode) {
        return String(product.categoryCode).trim() === String(selectedCategoryValue).trim()
      }
      
      return false
    })
  }
  
  // Filter by billing models (if not already filtered by category)
  if (filters.billingModels && filters.billingModels.length > 0 && !filters.categories?.length) {
    filtered = filtered.filter(product => {
      return filters.billingModels.some(model => 
        product.billingModel && product.billingModel.includes(model)
      )
    })
  }
  
  return rankProductsByPersonalization(filtered)
})

const totalProducts = computed(() => filteredProducts.value.length)

const totalPages = computed(() => Math.ceil(totalProducts.value / ITEMS_PER_PAGE))

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return filteredProducts.value.slice(start, end)
})

const pageNumbers = computed(() => {
  const pages = []
  const maxPagesToShow = 5
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

const resolveVendorApiValues = (selectedVendorNames = []) => {
  return selectedVendorNames
    .map((selectedName) => {
      const vendor = availableVendors.value.find(v => v.name === selectedName)
      return vendor?.value || selectedName
    })
    .filter(Boolean)
}

const getCacheKey = (filters) => {
  return JSON.stringify({
    vendors: filters.vendors,
    search: searchQuery.value,
    minPrice: filters.priceMin,
    maxPrice: filters.priceMax,
    billingModels: filters.billingModels
  })
}

const fetchAllProductPages = async (baseParams) => {
  const firstResponse = await axios.get('/api/v1/products', {
    params: {
      ...baseParams,
      page: 1,
      per_page: API_PAGE_SIZE,
      hide_zero_price: false
    }
  })

  if (!firstResponse.data?.success) {
    return []
  }

  const firstData = firstResponse.data.data || {}
  const firstRecords = Array.isArray(firstData.records)
    ? firstData.records
    : (Array.isArray(firstData) ? firstData : [])

  const total = Number(firstData.total || firstRecords.length || 0)
  const totalPages = Math.max(1, Math.ceil(total / API_PAGE_SIZE))
  if (totalPages <= 1) {
    return firstRecords
  }

  const pagePromises = []
  for (let page = 2; page <= totalPages; page++) {
    pagePromises.push(
      axios.get('/api/v1/products', {
        params: {
          ...baseParams,
          page,
          per_page: API_PAGE_SIZE,
          hide_zero_price: false
        }
      }).then((res) => {
        if (!res.data?.success) {
          return []
        }

        const pageData = res.data.data || {}
        return Array.isArray(pageData.records) ? pageData.records : []
      }).catch(() => [])
    )
  }

  const remaining = await Promise.all(pagePromises)
  return [...firstRecords, ...remaining.flat()]
}

const performSearch = async () => {
  loading.value = true
  error.value = ''
  currentPage.value = 1
  products.value = [] // Clear products while loading

  const normalizedQuery = normalizeSearchText(searchQuery.value)
  if (normalizedQuery) {
    const now = Date.now()
    const canTrack = normalizedQuery !== lastTrackedTerm.value || (now - lastTrackedAt.value) > SEARCH_TRACK_DEBOUNCE_MS
    if (canTrack) {
      trackSearchTerm(searchQuery.value)
      lastTrackedTerm.value = normalizedQuery
      lastTrackedAt.value = now
    }
  }

  const cacheKey = getCacheKey(currentFilters.value)

  // Check if results are already cached (cache for 5 minutes)
  if (requestCache.has(cacheKey)) {
    const cached = requestCache.get(cacheKey)
    if (Date.now() - cached.timestamp < 5 * 60 * 1000) {
      console.log('📦 Loading from local cache')
      products.value = cached.data
      extractCategories()
      loading.value = false
      return
    }
  }

  // Check if request is already in progress (prevent duplicate requests)
  if (pendingRequests.has(cacheKey)) {
    console.log('⏳ Waiting for in-progress request...')
    try {
      const result = await pendingRequests.get(cacheKey)
      products.value = result
      extractCategories()
    } finally {
      loading.value = false
    }
    return
  }

  // Create promise for this request
  const requestPromise = (async () => {
    try {
      const params = {
        hide_zero_price: false // include zero-priced products
      }

      if (searchQuery.value) {
        params.search = searchQuery.value
      }

      if (currentFilters.value.vendors.length > 0) {
        const selectedVendorValues = resolveVendorApiValues(currentFilters.value.vendors)
        if (selectedVendorValues.length > 0) {
          params.vendors = selectedVendorValues.join(',')
        }
      } else {
        params.vendor = 'Microsoft'
      }

      if (currentFilters.value.priceMin > 0) {
        params.min_price = currentFilters.value.priceMin
      }
      if (currentFilters.value.priceMax < 10000) {
        params.max_price = currentFilters.value.priceMax
      }

      if (currentFilters.value.billingModels.length > 0) {
        params.billing_models = currentFilters.value.billingModels.join(',')
      }

      console.log('🔍 Fetching products with filters:', {
        vendors: currentFilters.value.vendors,
        cached: params.cached ?? false,
        hide_zero_price: params.hide_zero_price
      })

      products.value = await fetchAllProductPages(params)
      if (Array.isArray(products.value)) {
        
        // Log cache status
        console.log('✅ Products loaded from API:', products.value.length)
        
        // Log first product structure for debugging
        if (products.value.length > 0) {
          console.log('📦 First product structure:', {
            ...products.value[0],
            productImages: products.value[0].productImages ? `${products.value[0].productImages.length} images` : 'No images'
          })
        }
        
        // Cache results locally
        requestCache.set(cacheKey, {
          data: products.value,
          timestamp: Date.now()
        })
        
        // Extract categories from loaded products
        extractCategories()
        
        return products.value
      } else {
        error.value = 'Failed to fetch products'
        return []
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'Failed to fetch products'
      console.error('❌ Product fetch error:', err)
      return []
    }
  })()

  pendingRequests.set(cacheKey, requestPromise)

  try {
    await requestPromise
  } finally {
    loading.value = false
    pendingRequests.delete(cacheKey)
  }
}

const clearSearch = async () => {
  if (!searchQuery.value) {
    return
  }

  searchQuery.value = ''
  await performSearch()
}

const fetchVendors = async () => {
  try {
    const response = await axios.get('/api/v1/vendors')
    
    if (response.data.success) {
      const rawVendorData = response.data.data || []
      const vendors = Array.isArray(rawVendorData)
        ? rawVendorData
        : (rawVendorData.records || [])

      // Transform API response to match frontend format and drop invalid/empty vendor rows
      availableVendors.value = vendors
        .map(vendor => {
          const name = String(vendor.vendorName || vendor.vendorId || '').trim()
          const value = String(vendor.vendorId || vendor.vendorName || '').trim()

          if (!name || !value) {
            return null
          }

          return {
            name,
            value,
            count: 0 // Will be updated below
          }
        })
        .filter(Boolean)
      
      // Fetch product counts for all vendors
      await fetchVendorCounts()
    }
  } catch (err) {
    console.error('Error fetching vendors:', err)
    // Fallback to some default vendors if API fails
    availableVendors.value = [
      { name: 'Microsoft', value: 'Microsoft', count: 0 },
      { name: 'Google', value: 'Google', count: 0 }
    ]
  }
}

const fetchVendorCounts = async () => {
  // Fetch product counts for each vendor (just first page to get total)
  const countPromises = availableVendors.value.map(async (vendor) => {
    try {
      const response = await axios.get('/api/v1/products', {
        params: {
          vendor: vendor.value || vendor.name,
          page: 1,
          per_page: 1 // Just need the total count, not all products
              , hide_zero_price: false
        }
      })
      
      if (response.data.success) {
        const data = response.data.data
        // Use apiTotal if available, otherwise fall back to total
        return {
          name: vendor.name,
          count: data.apiTotal || data.total || 0
        }
      }
    } catch (err) {
      console.error(`Error fetching count for ${vendor.name}:`, err)
      return { name: vendor.name, count: 0 }
    }
    return { name: vendor.name, count: 0 }
  })
  
  const counts = await Promise.all(countPromises)
  
  // Update vendor counts
  counts.forEach(({ name, count }) => {
    const vendor = availableVendors.value.find(v => v.name === name)
    if (vendor) {
      vendor.count = Number(count || 0)
    }
  })

  // Only show vendors that actually have products, ordered ascending by count.
  availableVendors.value = availableVendors.value
    .filter(vendor => {
      const hasName = String(vendor.name || '').trim().length > 0
      return hasName && Number(vendor.count || 0) > 0
    })
    .sort((a, b) => {
      const countDiff = Number(a.count || 0) - Number(b.count || 0)
      if (countDiff !== 0) return countDiff
      return String(a.name || '').localeCompare(String(b.name || ''))
    })
}

const updateVendorCounts = () => {
  // Count products per vendor
  const vendorCountMap = new Map()
  
  products.value.forEach(product => {
    if (product.vendorId) {
      const count = vendorCountMap.get(product.vendorId) || 0
      vendorCountMap.set(product.vendorId, count + 1)
    }
  })
  
  // Update vendor counts
  availableVendors.value = availableVendors.value.map(vendor => ({
    ...vendor,
    count: vendorCountMap.get(vendor.name) || 0
  }))
}

const extractCategories = () => {
  // Extract unique categories from products
  const categoryMap = new Map()
  const codeBuckets = new Map()
  
  products.value.forEach(product => {
    // Check for productCategories array
    if (product.productCategories && Array.isArray(product.productCategories)) {
      product.productCategories.forEach(category => {
        const categoryName = typeof category === 'object' ? category.categoryName : category
        const normalizedCategoryName = String(categoryName || '').trim()
        if (normalizedCategoryName) {
          const count = categoryMap.get(normalizedCategoryName) || 0
          categoryMap.set(normalizedCategoryName, count + 1)
        }
      })
    }
    
    // Also extract from billing model as a fallback category
    if (product.billingModel) {
      const billingCategory = `Billing: ${product.billingModel}`
      const normalizedBillingCategory = String(billingCategory).trim()
      if (normalizedBillingCategory) {
        const count = categoryMap.get(normalizedBillingCategory) || 0
        categoryMap.set(normalizedBillingCategory, count + 1)
      }
    }
    
    // Extract from billing frequency
    if (product.billingFrequency) {
      const freqCategory = `Frequency: ${product.billingFrequency}`
      const normalizedFreqCategory = String(freqCategory).trim()
      if (normalizedFreqCategory) {
        const count = categoryMap.get(normalizedFreqCategory) || 0
        categoryMap.set(normalizedFreqCategory, count + 1)
      }
    }

    // Extract categoryCode for PriceAvailability products
    if (product.categoryCode) {
      const categoryCode = String(product.categoryCode || '').trim()
      if (categoryCode) {
        const bucket = codeBuckets.get(categoryCode) || { count: 0, terms: new Map() }
        bucket.count += 1

        const tokens = String(product.productName || '')
          .toLowerCase()
          .replace(/[^a-z0-9\s]/g, ' ')
          .split(/\s+/)
          .filter(Boolean)

        tokens.forEach(token => {
          if (token.length < 4) return
          if (['with', 'from', 'that', 'this', 'kit', 'model', 'module', 'system', 'pack', 'each'].includes(token)) return
          bucket.terms.set(token, (bucket.terms.get(token) || 0) + 1)
        })

        codeBuckets.set(categoryCode, bucket)
      }
    }
  })

  // Add readable labels for numeric category codes while preserving code as filter value
  codeBuckets.forEach((bucket, code) => {
    let bestTerm = 'Category'
    let bestCount = 0
    bucket.terms.forEach((count, term) => {
      if (count > bestCount) {
        bestCount = count
        bestTerm = term
      }
    })

    const label = `${bestTerm.charAt(0).toUpperCase()}${bestTerm.slice(1)} (${code})`
    categoryMap.set(label, { count: bucket.count, value: code })
  })
  
  // Convert map to array format and sort by count
  availableCategories.value = Array.from(categoryMap.entries())
    .map(([name, data]) => ({
      name: String(name || '').trim(),
      count: Number((typeof data === 'object' ? data.count : data) || 0),
      value: typeof data === 'object' ? (data.value || name) : name
    }))
    .filter(cat => cat.name.length > 0 && cat.count > 0)
    .sort((a, b) => b.count - a.count)
  
  // DO NOT update vendor counts here - they should remain static from initial API fetch
  // Vendor counts are set by fetchVendorCounts() and should not change based on filters
  
  // Log for verification
  console.log('Categories extracted:', availableCategories.value.length)
  console.log('Total products:', products.value.length)
}

const handleFilterChange = (filters) => {
  const previousVendors = [...currentFilters.value.vendors]
  const previousPriceMin = currentFilters.value.priceMin
  const previousPriceMax = currentFilters.value.priceMax
  const previousCategories = [...currentFilters.value.categories]
  const previousBillingModels = [...currentFilters.value.billingModels]
  
  currentPage.value = 1 // Reset to first page when filters change
  
  // Check what changed
  const vendorsChanged = 
    previousVendors.length !== filters.vendors.length ||
    previousVendors.some((v, i) => v !== filters.vendors[i])
  
  const priceChanged = 
    previousPriceMin !== filters.priceMin ||
    previousPriceMax !== filters.priceMax
  
  const categoriesChanged =
    previousCategories.length !== filters.categories.length ||
    previousCategories.some((c, i) => c !== filters.categories[i])
  
  const billingModelsChanged =
    previousBillingModels.length !== filters.billingModels.length ||
    previousBillingModels.some((b, i) => b !== filters.billingModels[i])
  
  // Always update the filters - this ensures the computed property recalculates
  currentFilters.value = { ...filters }
  
  console.log('📋 Filter changed:', {
    vendorsChanged: vendorsChanged ? `${previousVendors.join(',')} → ${filters.vendors.join(',')}` : 'No',
    categoriesChanged: categoriesChanged ? 'Yes' : 'No',
    vendors: filters.vendors
  })
  
  // Re-fetch if vendor or price changed (API filters)
  if (vendorsChanged || priceChanged) {
    performSearch()
  }
  // If only categories or billing models changed, compute will handle it automatically
  // since currentFilters.value is now updated and will trigger the computed property
}

const resetFilters = () => {
  searchQuery.value = ''
  currentPage.value = 1
  currentFilters.value = {
    priceMin: 0,
    priceMax: 10000,
    vendors: [],
    categories: [],
    billingModels: []
  }
  performSearch()
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const goToPage = (page) => {
  currentPage.value = page
  window.scrollTo({ top: 0, behavior: 'smooth' })
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

const getProductIcon = (productName) => {
  const name = productName.toLowerCase()
  if (name.includes('server') || name.includes('instance')) return 'server'
  if (name.includes('azure') || name.includes('cloud') || name.includes('subscription')) return 'cloud'
  if (name.includes('database') || name.includes('sql')) return 'database'
  return 'default'
}

watch(
  () => route.query.q,
  (newQuery) => {
    searchQuery.value = newQuery ? String(newQuery) : ''
    performSearch()
  },
  { immediate: true }
)

onMounted(() => {
  fetchVendors()
})
</script>
