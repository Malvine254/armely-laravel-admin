<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <!-- Back Button -->
      <button @click="goBack" class="mb-6 flex items-center gap-2 text-sm transition" style="color: #2F5597;" @mouseenter="$event.target.style.opacity='0.7'" @mouseleave="$event.target.style.opacity='1'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Products
      </button>

      <!-- Product Detail Container -->
      <div v-if="product" class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6 lg:p-10">
          <!-- Product Image Section -->
          <div class="flex flex-col">
            <div class="flex-1 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg flex items-center justify-center mb-4 overflow-hidden" style="background: linear-gradient(135deg, rgb(229, 231, 235), rgb(209, 213, 219)); min-block-size: 22rem;">
              <img
                v-if="selectedImage"
                :src="selectedImage"
                :alt="product.productName"
                class="w-full h-full object-cover object-center"
                loading="eager"
                @error="selectedImage = ''"
              />
              <div v-else class="text-center">
                <svg v-if="getProductIcon(product.productName) === 'server'" class="w-24 h-24 mx-auto text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-3 8h-2V5h2v6z"/>
                </svg>
                <svg v-else-if="getProductIcon(product.productName) === 'cloud'" class="w-24 h-24 mx-auto text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4c-1.48 0-2.85.43-4.01 1.17l1.46 1.46C10.21 5.23 11.08 5 12 5c3.04 0 5.5 2.46 5.5 5.5v.5H19c2.05 0 3.71 1.66 3.71 3.71 0 1.71-1.04 2.86-2.36 3.41z"/>
                </svg>
                <svg v-else-if="getProductIcon(product.productName) === 'database'" class="w-24 h-24 mx-auto text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 3c-4.97 0-9 2.16-9 4.5S7.03 12 12 12s9-2.16 9-4.5S16.97 3 12 3zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3zm0 7c-4.97 0-9 2.16-9 4.5S7.03 24 12 24s9-2.16 9-4.5-4.03-4.5-9-4.5zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3z"/>
                </svg>
                <svg v-else class="w-24 h-24 mx-auto text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
              </div>
            </div>

            <div v-if="normalizedImages.length > 1" class="grid grid-cols-5 gap-2">
              <button
                v-for="(image, idx) in normalizedImages"
                :key="`thumb-${idx}`"
                class="h-16 rounded border overflow-hidden"
                :class="selectedImage === image ? 'border-blue-600 ring-2 ring-blue-200' : 'border-gray-300'"
                @click="selectedImage = image"
                type="button"
              >
                <img :src="image" :alt="`Image ${idx + 1}`" class="w-full h-full object-cover" loading="lazy" />
              </button>
            </div>
          </div>

          <!-- Product Details Section -->
          <div class="flex flex-col">
            <!-- Product Status -->
            <div class="flex items-center gap-2 mb-4">
              <span v-if="product.discontinueProduct" class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">End of Life</span>
              <span v-else class="px-3 py-1 text-xs font-semibold rounded" style="background-color: #cce4f4; color: #2F5597;">Active</span>
            </div>

            <!-- Product Name -->
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ product.productName }}</h1>

            <!-- Basic Info -->
            <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-200">
              <div>
                <p class="text-xs text-gray-600 font-semibold">SKU</p>
                <p class="text-sm text-gray-900">{{ product.mfgPartNo || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-600 font-semibold">VENDOR</p>
                <p class="text-sm text-gray-900">{{ product.vendorId }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-600 font-semibold">PRODUCT ID</p>
                <p class="text-sm text-gray-900">{{ product.productId }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-600 font-semibold">BILLING MODEL</p>
                <p class="text-sm text-gray-900">{{ product.billingModel || 'N/A' }}</p>
              </div>
            </div>

            <!-- Pricing Section -->
            <div class="mb-6 pb-6 border-b border-gray-200">
              <p class="text-xs text-gray-600 font-semibold mb-2">PRICING</p>
              <div v-if="product.productPrice && product.productPrice.length > 0">
                <div class="space-y-3">
                  <div v-for="(price, idx) in product.productPrice.slice(0, 3)" :key="idx" class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-sm text-gray-700">
                      Qty: <span class="font-semibold">{{ price.minQty }}</span>
                      <span v-if="price.maxQty"> - {{ price.maxQty }}</span>
                    </span>
                    <span class="text-lg font-bold" style="color: #2F5597;">${{ formatPrice(price.rsPrice) }}</span>
                  </div>
                </div>
                <p v-if="product.productPrice.length > 3" class="text-xs text-gray-600 mt-2 italic">
                  +{{ product.productPrice.length - 3 }} more pricing tiers available
                </p>
              </div>
              <p v-else class="text-sm text-gray-600">Pricing not available</p>
            </div>

            <!-- Features -->
            <div class="mb-6 pb-6 border-b border-gray-200">
              <p class="text-xs text-gray-600 font-semibold mb-3">FEATURES & ATTRIBUTES</p>
              <div class="space-y-2">
                <div v-if="product.billingFrequency" class="flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #2F5597;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-sm text-gray-700">Billing Frequency: <span class="font-semibold">{{ product.billingFrequency }}</span></span>
                </div>
                <div v-if="product.billingTerm" class="flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #2F5597;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-sm text-gray-700">Billing Term: <span class="font-semibold">{{ product.billingTerm }}</span></span>
                </div>
                <div v-if="product.isTier" class="flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #2F5597;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-sm text-gray-700">Tiered Pricing Available</span>
                </div>
                <div v-if="product.productCategories && product.productCategories.length > 0" class="flex flex-wrap gap-2">
                  <span v-for="(cat, idx) in product.productCategories.slice(0, 5)" :key="idx" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">{{ typeof cat === 'object' ? cat.categoryName : cat }}</span>
                  <span v-if="product.productCategories.length > 5" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">+{{ product.productCategories.length - 5 }}</span>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-auto">
              <button @click="addToQuote" class="w-full px-4 py-3 text-white font-semibold rounded-lg transition text-sm" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
                Add to Quote
              </button>
              <button @click="addToFavorite" class="w-full px-4 py-3 border-2 font-semibold rounded-lg transition text-sm" :style="isFavorite ? { borderColor: '#2F5597', color: '#2F5597', backgroundColor: '#cce4f4' } : { borderColor: '#2F5597', color: '#2F5597' }" @mouseenter="$event.target.style.backgroundColor='#cce4f4'" @mouseleave="$event.target.style.backgroundColor=isFavorite ? '#cce4f4' : 'transparent'">
                ♥ {{ isFavorite ? 'Remove Favorite' : 'Add to Favorites' }}
              </button>
            </div>
          </div>
        </div>

      </div>

      <!-- Loading State -->
      <div v-else-if="isLoading" class="bg-white rounded-lg shadow-lg p-12 text-center">
        <div class="w-12 h-12 border-4 border-gray-200 rounded-full animate-spin mx-auto mb-4" style="border-block-start-color: #2F5597;"></div>
        <p class="text-gray-600 font-semibold">Loading product details...</p>
      </div>

      <!-- Error State -->
      <div v-else class="bg-white rounded-lg shadow-lg p-12 text-center">
        <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-red-50 flex items-center justify-center text-red-600">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"></path>
          </svg>
        </div>
        <p class="text-gray-900 font-semibold mb-2">Unable to load product details</p>
        <p class="text-gray-600 mb-6">{{ loadError || 'The product could not be loaded at this time.' }}</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <button @click="retryLoadProduct" class="px-5 py-2.5 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
            Try Again
          </button>
          <button @click="goBack" class="px-5 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
            Back to Products
          </button>
        </div>
      </div>

      <!-- Related Products Section -->
      <div v-if="product && relatedProducts.length > 0" class="mt-8 bg-white rounded-lg shadow-lg p-6 lg:p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="relatedProduct in relatedProducts" :key="relatedProduct.productId" 
               @click="navigateToProduct(relatedProduct.productId)"
               class="bg-white border-2 border-gray-200 rounded-lg p-5 hover:border-blue-500 transition cursor-pointer">
            <!-- Product Icon -->
            <div class="bg-gray-100 rounded-lg p-4 mb-4 flex items-center justify-center h-24">
              <svg v-if="getProductIcon(relatedProduct.productName) === 'server'" class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-3 8h-2V5h2v6z"/>
              </svg>
              <svg v-else-if="getProductIcon(relatedProduct.productName) === 'cloud'" class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4c-1.48 0-2.85.43-4.01 1.17l1.46 1.46C10.21 5.23 11.08 5 12 5c3.04 0 5.5 2.46 5.5 5.5v.5H19c2.05 0 3.71 1.66 3.71 3.71 0 1.71-1.04 2.86-2.36 3.41z"/>
              </svg>
              <svg v-else-if="getProductIcon(relatedProduct.productName) === 'database'" class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 3c-4.97 0-9 2.16-9 4.5S7.03 12 12 12s9-2.16 9-4.5S16.97 3 12 3zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3zm0 7c-4.97 0-9 2.16-9 4.5S7.03 24 12 24s9-2.16 9-4.5-4.03-4.5-9-4.5zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3z"/>
              </svg>
              <svg v-else class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
            </div>
            
            <!-- Product Name -->
            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem]">
              {{ relatedProduct.productName }}
            </h3>
            
            <!-- Product Details -->
            <div class="mb-3 flex items-center justify-between gap-3 text-xs text-gray-600 whitespace-nowrap">
              <p class="truncate">
                <span class="font-semibold">SKU:</span> {{ relatedProduct.mfgPartNo || 'N/A' }}
              </p>
              <p class="truncate text-right">
                <span class="font-semibold">Vendor:</span> {{ relatedProduct.vendorId || 'N/A' }}
              </p>
            </div>
            
            <!-- Price -->
            <div v-if="relatedProduct.productPrice && relatedProduct.productPrice.length > 0" class="pt-3 border-t border-gray-200">
              <p class="text-xs text-gray-600 mb-1">Starting from</p>
              <p class="text-lg font-bold" style="color: #2F5597;">
                ${{ formatPrice(relatedProduct.productPrice[0].rsPrice) }}
              </p>
            </div>
            <div v-else class="pt-3 border-t border-gray-200">
              <p class="text-sm text-gray-500">Price not available</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useCartStore } from '../../stores/cartStore'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useAuthStore } from '../../stores/authStore'
import Navbar from '../../components/Navbar.vue'
import { API_BASE_URL } from '../../services/runtimeConfig'

const router = useRouter()
const route = useRoute()
const product = ref(null)
const relatedProducts = ref([])
const toastStore = useToastStore()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const selectedImage = ref('')
const isLoading = ref(false)
const loadError = ref('')

const productDetailCache = new Map()
const relatedProductsCache = new Map()

const loadRelatedProducts = async (productId, cacheKey, cachedRelated) => {
  if (cachedRelated) {
    relatedProducts.value = cachedRelated
    return
  }

  try {
    const response = await fetch(`${API_BASE_URL}/products/${productId}/related`)
    if (!response.ok) {
      relatedProducts.value = []
      relatedProductsCache.set(cacheKey, [])
      return
    }

    const json = await response.json()
    const data = json.data || json
    const loadedRelated = data.records || data || []
    relatedProducts.value = loadedRelated
    relatedProductsCache.set(cacheKey, loadedRelated)
  } catch (relatedError) {
    console.warn('Related products fetch failed:', relatedError)
    relatedProducts.value = []
  }
}

const loadProductDetail = async (productId) => {
  if (!productId) return

  const cacheKey = String(productId)
  const cachedProduct = productDetailCache.get(cacheKey)
  const cachedRelated = relatedProductsCache.get(cacheKey)
  isLoading.value = true
  loadError.value = ''

  if (cachedProduct) {
    product.value = cachedProduct
  } else {
    product.value = null
    selectedImage.value = ''
  }

  if (cachedRelated) {
    relatedProducts.value = cachedRelated
  } else {
    relatedProducts.value = []
  }

  try {
    if (!cachedProduct) {
      const response = await fetch(`${API_BASE_URL}/products/${productId}`)
      if (!response.ok) {
        throw new Error(response.status === 404 ? 'Product not found.' : `Failed to fetch product (${response.status}).`)
      }

      const json = await response.json()
      const loadedProduct = json.data || json
      if (!loadedProduct || (typeof loadedProduct === 'object' && Object.keys(loadedProduct).length === 0)) {
        throw new Error('Product not found.')
      }

      product.value = loadedProduct
      productDetailCache.set(cacheKey, loadedProduct)
    }

  } catch (error) {
    console.error('Error loading product detail:', error)
    product.value = null
    loadError.value = error instanceof Error ? error.message : 'Unable to load this product.'
  } finally {
    isLoading.value = false
  }

  if (product.value) {
    void loadRelatedProducts(productId, cacheKey, cachedRelated)
  }
}

const retryLoadProduct = () => {
  const productId = route.params.id
  if (!productId) return
  loadProductDetail(productId)
}

watch(
  () => route.params.id,
  (productId) => {
    if (!productId) return
    loadProductDetail(productId)
  },
  { immediate: true }
)

const navigateToProduct = (productId) => {
  router.push({ name: 'product-detail', params: { id: productId } })
}

const goBack = () => {
  router.push({ name: 'products' })
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

const normalizeImages = (source) => {
  if (!Array.isArray(source)) return []

  const seen = new Set()
  const urls = []

  source.forEach((entry) => {
    let url = ''
    if (typeof entry === 'string') {
      url = entry.trim()
    } else if (entry && typeof entry === 'object') {
      url = String(entry.imageUrl || entry.url || '').trim()
    }

    if (!url || seen.has(url)) return
    seen.add(url)
    urls.push(url)
  })

  return urls
}

const normalizedImages = computed(() => {
  if (!product.value) return []

  const urls = normalizeImages(product.value.productImages)
  if (urls.length > 0) return urls

  const imageFallback = normalizeImages(product.value.images)
  if (imageFallback.length > 0) return imageFallback

  if (product.value.image_url) {
    return [String(product.value.image_url)]
  }

  return []
})

watch(normalizedImages, (images) => {
  selectedImage.value = images[0] || ''
}, { immediate: true })

const isFavorite = computed(() => {
  if (!product.value) return false
  return favoritesStore.isFavorite(product.value.productId)
})

const addToQuote = () => {
  if (!product.value) return
  const added = cartStore.addItem(product.value, 1)
  if (!added) {
    toastStore.addToast('Account suspended: adding items to quotes is disabled', 'error')
    return
  }

  toastStore.addToast(`Added "${product.value.productName}" to quote`, 'success')
}

const addToFavorite = () => {
  if (!product.value) return
  
  // Check if user is authenticated
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to add items to favorites', 'info')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  const isNowFavorite = favoritesStore.toggleFavorite(product.value)
  if (isNowFavorite === null) {
    toastStore.addToast('Account suspended: favorites are read-only', 'error')
    return
  }

  if (isNowFavorite) {
    toastStore.addToast(`Added "${product.value.productName}" to favorites`, 'success')
  } else {
    toastStore.addToast(`Removed "${product.value.productName}" from favorites`, 'info')
  }
}
</script>

