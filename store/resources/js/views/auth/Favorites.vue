<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <!-- Page Title -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">My Favorites</h1>
        <p class="text-gray-600 text-lg">Your saved products</p>
      </div>

      <!-- Back Button -->
      <button @click="goBack" class="mb-6 flex items-center gap-2 text-sm transition" style="color: #2F5597;" @mouseenter="$event.target.style.opacity='0.7'" @mouseleave="$event.target.style.opacity='1'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Products
      </button>

      <!-- Empty State -->
      <div v-if="favoritesStore.isEmpty" class="bg-white rounded-lg shadow-lg p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">No favorites yet</h2>
        <p class="text-gray-600 mb-6">Start adding products to your favorites</p>
        <button @click="goBack" class="px-6 py-3 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Browse Products
        </button>
      </div>

      <!-- Favorites Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="product in favoritesStore.items" :key="product.productId" class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
          <!-- Product Image Section -->
          <div class="bg-gradient-to-br from-gray-200 to-gray-300 h-40 flex items-center justify-center relative">
            <svg class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            <img
              v-if="getFavoriteImageUrl(product)"
              :src="getFavoriteImageUrl(product)"
              :alt="product.productName"
              class="absolute inset-0 w-full h-full object-cover"
              loading="lazy"
              @error="event => event.target.style.display = 'none'"
            />
            <!-- Remove from Favorites Button -->
            <button @click="removeFromFavorites(product.productId)" class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition shadow-md">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
              </svg>
            </button>
          </div>

          <!-- Product Details -->
          <div class="p-4">
            <!-- Status Badge -->
            <div class="mb-2">
              <span v-if="product.discontinueProduct" class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">End of Life</span>
              <span v-else class="px-2 py-1 text-xs font-semibold rounded" style="background-color: #cce4f4; color: #2F5597;">Active</span>
            </div>

            <!-- Product Name -->
            <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-2">{{ product.productName }}</h3>

            <!-- Product Meta -->
            <div class="text-xs text-gray-600 space-y-1 mb-3">
              <p><span class="font-semibold">Vendor:</span> {{ product.vendorId }}</p>
              <p><span class="font-semibold">Billing:</span> {{ product.billingModel || 'N/A' }}</p>
            </div>

            <!-- Price -->
            <div class="mb-4 pb-4 border-b border-gray-200">
              <p class="text-xs text-gray-600 font-semibold mb-1">STARTING PRICE</p>
              <p class="text-xl font-bold" style="color: #2F5597;">${{ formatPrice(product.productPrice?.[0]?.rsPrice || 0) }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 w-full">
              <button @click="viewDetails(product)" class="flex-1 px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
                View Details
              </button>
              <button @click="addToQuote(product)" class="px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'" title="Add to Quote">
                +
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useCartStore } from '../../stores/cartStore'
import { useToastStore } from '../../stores/toastStore'
import Navbar from '../../components/Navbar.vue'

const router = useRouter()
const favoritesStore = useFavoritesStore()
const cartStore = useCartStore()
const toastStore = useToastStore()

const goBack = () => {
  router.push({ name: 'products' })
}

const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
}

const getFavoriteImageUrl = (product) => {
  const direct = [
    product?.favoriteImageUrl,
    product?.imageUrl,
    product?.thumbnailUrl,
    product?.image,
    product?.thumbnail,
  ]

  for (const value of direct) {
    const url = String(value || '').trim()
    if (url.length > 0) return url
  }

  const images = Array.isArray(product?.productImages) ? product.productImages : []
  for (const image of images) {
    if (typeof image === 'string') {
      const url = image.trim()
      if (url.length > 0) return url
      continue
    }

    const url = String(image?.imageUrl || image?.url || image?.thumbnailUrl || '').trim()
    if (url.length > 0) return url
  }

  return ''
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
