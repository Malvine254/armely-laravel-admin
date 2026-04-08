<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">My Favorites</h1>
        <p class="text-gray-600 text-lg">Saved products in the same catalog format</p>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-4">
          <div class="flex-1">
            <div class="relative">
              <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input v-model="searchQuery" type="text" placeholder="Search favorites..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition">
            </div>
          </div>
          <button @click="goBack" class="px-6 py-3 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
            Back to Products
          </button>
        </div>
      </div>

      <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <p class="text-gray-600 font-medium">Showing <span class="font-bold" style="color: #2F5597;">{{ filteredFavorites.length }}</span> favorite products</p>
      </div>

      <div v-if="filteredFavorites.length === 0" class="text-center py-9 bg-white rounded-xl border border-gray-200">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No favorites found</h3>
        <p class="text-gray-600 mb-6">Try a different search or add products to your favorites.</p>
        <button @click="goBack" class="px-6 py-2 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Browse Products
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        <div v-for="product in filteredFavorites" :key="product.productId" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-lg transition" style="border: 1px solid rgb(229, 231, 235);" @mouseenter="$event.currentTarget.style.borderColor='#cce4f5'" @mouseleave="$event.currentTarget.style.borderColor='rgb(229, 231, 235)'">
          <div class="bg-gradient-to-br from-gray-200 to-gray-300 h-40 flex items-center justify-center transition relative overflow-hidden" style="background: linear-gradient(135deg, rgb(229, 231, 235), rgb(209, 213, 219));">
            <button
              @click="removeFromFavorites(product.productId)"
              class="absolute top-2 right-2 z-20 w-7 h-7 rounded-full bg-red-600 text-white hover:bg-red-700 transition shadow"
              title="Remove from Favorites"
              aria-label="Remove from Favorites"
            >
              &times;
            </button>

            <img
              v-if="getPrimaryImage(product)"
              :src="getPrimaryImage(product)"
              :alt="product.productName"
              class="w-full h-full object-cover"
              loading="lazy"
              @error="event => event.target.style.display = 'none'"
            />

            <template v-else>
              <div class="absolute inset-0 opacity-10">
                <div class="absolute top-2 right-2 w-12 h-12 bg-blue-400 rounded-full"></div>
                <div class="absolute bottom-4 left-2 w-8 h-8 bg-blue-300 rounded-full"></div>
              </div>

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

          <div class="p-4">
            <div class="flex items-start justify-between mb-2">
              <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ product.productName }}</h3>
              <span v-if="product.discontinueProduct" class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">EOL</span>
              <span v-else class="ml-2 px-2 py-1 text-xs font-semibold rounded" style="background-color: #cce4f4; color: #2F5597;">Active</span>
            </div>

            <div class="flex items-center justify-between gap-3 text-xs text-gray-600 mb-3">
              <p class="truncate">SKU: {{ product.mfgPartNo || 'N/A' }}</p>
              <p class="truncate text-right">Vendor: {{ product.vendorId || 'N/A' }}</p>
            </div>

            <div v-if="product.productPrice && product.productPrice.length > 0" class="mb-4">
              <p class="text-2xl font-bold" style="color: #2F5597;">{{ formatPrice(product.productPrice[0].rsPrice) }}</p>
              <p class="text-xs text-gray-600">Min Qty: {{ product.productPrice[0].minQty }}</p>
            </div>

            <div class="mb-4 flex flex-wrap gap-1">
              <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ product.billingModel || 'N/A' }}</span>
              <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ product.billingFrequency || 'N/A' }}</span>
            </div>

            <div class="flex gap-2 w-full">
              <button @click="viewDetails(product)" class="flex-1 px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">View Details</button>
              <button @click="addToQuote(product)" class="px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'" title="Add to Quote">+</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useCartStore } from '../../stores/cartStore'
import { useToastStore } from '../../stores/toastStore'
import Navbar from '../../components/Navbar.vue'
import { usePricingSettings } from '../../composables/usePricingSettings'

const router = useRouter()
const favoritesStore = useFavoritesStore()
const cartStore = useCartStore()
const toastStore = useToastStore()
const searchQuery = ref('')
const { loadPricingSettings, getCatalogPriceWithRules, convertFromUsd, formatWithCurrency } = usePricingSettings()

const filteredFavorites = computed(() => {
  const term = String(searchQuery.value || '').toLowerCase().trim()
  if (!term) {
    return favoritesStore.items
  }

  return favoritesStore.items.filter((product) => {
    const blob = [
      product?.productName,
      product?.vendorId,
      product?.mfgPartNo,
      product?.billingModel,
      product?.billingFrequency,
    ].join(' ').toLowerCase()

    return blob.includes(term)
  })
})

const goBack = () => {
  router.push({ name: 'products' })
}

const formatPrice = (price) => {
  const adjustedUsd = getCatalogPriceWithRules(Number(price || 0))
  return formatWithCurrency(convertFromUsd(adjustedUsd))
}

loadPricingSettings()

const getPrimaryImage = (product) => {
  const images = Array.isArray(product?.productImages) ? product.productImages : []
  const first = images[0]

  if (typeof first === 'string') {
    const normalized = first.trim()
    if (normalized.length > 0) return normalized
  }

  if (first && typeof first === 'object') {
    const mapped = String(first.imageUrl || first.imageURL || first.image_url || first.url || first.thumbnailUrl || '').trim()
    if (mapped.length > 0) return mapped
  }

  return String(product?.favoriteImageUrl || '').trim()
}

const getProductIcon = (productName) => {
  const name = String(productName || '').toLowerCase()
  if (name.includes('server') || name.includes('instance')) return 'server'
  if (name.includes('azure') || name.includes('cloud') || name.includes('subscription')) return 'cloud'
  if (name.includes('database') || name.includes('sql')) return 'database'
  return 'default'
}

const removeFromFavorites = (productId) => {
  const removed = favoritesStore.removeItem(productId)
  if (!removed) {
    toastStore.addToast('Account suspended: favorites are read-only', 'error')
  }
}

const viewDetails = (product) => {
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
</script>
