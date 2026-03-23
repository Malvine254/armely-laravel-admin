<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            <div class="flex-1 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg p-8 flex items-center justify-center mb-4" style="background: linear-gradient(135deg, rgb(229, 231, 235), rgb(209, 213, 219));">
              <div class="text-center">
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

        <!-- Additional Details Section -->
        <div class="bg-gray-50 px-6 lg:px-10 py-8 border-t border-gray-200">
          <h2 class="text-lg font-bold text-gray-900 mb-6">Complete Product Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Details Grid -->
            <div v-for="(value, key) in productDetails" :key="key" class="bg-white p-4 rounded-lg border border-gray-200">
              <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">{{ formatKey(key) }}</p>
              <p class="text-sm text-gray-900 break-words">
                {{ value === null || value === undefined || value === '' ? 'N/A' : String(value).substring(0, 100) }}
                <span v-if="String(value).length > 100">...</span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-else class="bg-white rounded-lg shadow-lg p-12 text-center">
        <div class="w-12 h-12 border-4 border-gray-200 rounded-full animate-spin mx-auto mb-4" style="border-top-color: #2F5597;"></div>
        <p class="text-gray-600 font-semibold">Loading product details...</p>
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
            <div class="space-y-1 mb-3">
              <p class="text-xs text-gray-600">
                <span class="font-semibold">Vendor:</span> {{ relatedProduct.vendorId || 'N/A' }}
              </p>
              <p class="text-xs text-gray-600">
                <span class="font-semibold">SKU:</span> {{ relatedProduct.mfgPartNo || 'N/A' }}
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
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useCartStore } from '../../stores/cartStore'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useAuthStore } from '../../stores/authStore'
import Navbar from '../../components/Navbar.vue'

const router = useRouter()
const route = useRoute()
const product = ref(null)
const relatedProducts = ref([])
const toastStore = useToastStore()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()

onMounted(async () => {
  // Fetch product from API using product ID from route params
  const productId = route.params.id
  if (productId) {
    try {
      const response = await fetch(`/api/v1/products/${productId}`)
      if (response.ok) {
        const json = await response.json()
        // Extract product data from API response wrapper
        product.value = json.data || json
        
        // Fetch related products after main product is loaded
        fetchRelatedProducts(productId)
      } else {
        console.error('Failed to fetch product')
      }
    } catch (error) {
      console.error('Error fetching product:', error)
    }
  }
})

const fetchRelatedProducts = async (productId) => {
  try {
    const response = await fetch(`/api/v1/products/${productId}/related`)
    if (response.ok) {
      const json = await response.json()
      // Extract related products data from API response
      const data = json.data || json
      relatedProducts.value = data.records || data || []
      console.log('Related products fetched:', relatedProducts.value.length)
    } else {
      console.warn('Failed to fetch related products')
      relatedProducts.value = []
    }
  } catch (error) {
    console.error('Error fetching related products:', error)
    relatedProducts.value = []
  }
}

const navigateToProduct = (productId) => {
  router.push({ name: 'product-detail', params: { id: productId } })
}

const goBack = () => {
  router.push({ name: 'products' })
}

const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
}

const formatKey = (key) => {
  // Convert camelCase to Title Case
  return key
    .replace(/([A-Z])/g, ' $1')
    .replace(/^./, str => str.toUpperCase())
    .trim()
}

const getProductIcon = (productName) => {
  const name = productName.toLowerCase()
  if (name.includes('server') || name.includes('instance')) return 'server'
  if (name.includes('azure') || name.includes('cloud') || name.includes('subscription')) return 'cloud'
  if (name.includes('database') || name.includes('sql')) return 'database'
  return 'default'
}

const productDetails = computed(() => {
  if (!product.value) return {}
  
  const details = {}
  const excludeKeys = ['productName', 'productId', 'vendorId', 'productPrice', 'productCategories', 'billingModel', 'billingFrequency', 'billingTerm', 'isTier', 'discontinueProduct', 'mfgPartNo']
  
  Object.keys(product.value).forEach(key => {
    if (!excludeKeys.includes(key) && product.value[key]) {
      const value = product.value[key]
      const valueType = typeof value
      
      // Only include strict scalar values
      if (valueType === 'string' && !value.startsWith('{') && !value.startsWith('[')) {
        details[key] = value
      } else if (valueType === 'number' || valueType === 'boolean') {
        details[key] = value
      } else if (Array.isArray(value) && value.length > 0 && typeof value[0] === 'string') {
        // Only include if all items are strings
        if (value.every(item => typeof item === 'string')) {
          details[key] = value.join(', ')
        }
      }
      // Skip all object types and mixed arrays
    }
  })
  
  return details
})

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

