<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <!-- Page Title -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">My Quote</h1>
        <p class="text-gray-600 text-lg">Manage items in your quote</p>
      </div>

      <!-- Back Button -->
      <button @click="goBack" class="mb-6 flex items-center gap-2 text-sm transition" style="color: #2F5597;" @mouseenter="$event.target.style.opacity='0.7'" @mouseleave="$event.target.style.opacity='1'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Products
      </button>

      <div v-if="cartStore.revisionSourceQuoteId" class="mb-6 rounded-xl border px-4 py-3 flex items-center justify-between" style="border-color: #cfe0f5; background-color: #f6faff;">
        <p class="text-sm font-semibold" style="color: #2F5597;">
          Revising quote {{ cartStore.revisionSourceQuoteId }}
        </p>
        <button
          @click="clearRevisionMode"
          class="text-xs font-semibold px-3 py-1.5 rounded border border-gray-300 text-gray-700 hover:bg-white transition"
        >
          Clear Revision
        </button>
      </div>

      <!-- Empty State -->
      <div v-if="cartStore.isEmpty" class="bg-white rounded-lg shadow-lg p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Your quote is empty</h2>
        <p class="text-gray-600 mb-6">Start adding products to build your quote</p>
        <button @click="goBack" class="px-6 py-3 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Continue Shopping
        </button>
      </div>

      <!-- Cart Items -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Items List -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div
              class="divide-y divide-gray-200"
              :class="cartStore.items.length > 3 ? 'max-h-[34rem] overflow-y-auto' : ''"
            >
              <div v-for="item in cartStore.items" :key="item.productId" class="p-6 flex gap-4 hover:bg-gray-50 transition">
                <!-- Product Image / Icon -->
                <div class="w-24 h-24 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                  <img
                    v-if="hasProductImage(item)"
                    :src="getProductImageUrl(item)"
                    :alt="item.productName"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    @error="handleImageError(item.productId)"
                  />

                  <div v-else class="text-gray-500">
                    <svg v-if="getProductIcon(item.productName) === 'server'" class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-3 8h-2V5h2v6z"/>
                    </svg>
                    <svg v-else-if="getProductIcon(item.productName) === 'cloud'" class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4c-1.48 0-2.85.43-4.01 1.17l1.46 1.46C10.21 5.23 11.08 5 12 5c3.04 0 5.5 2.46 5.5 5.5v.5H19c2.05 0 3.71 1.66 3.71 3.71 0 1.71-1.04 2.86-2.36 3.41z"/>
                    </svg>
                    <svg v-else-if="getProductIcon(item.productName) === 'database'" class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 3c-4.97 0-9 2.16-9 4.5S7.03 12 12 12s9-2.16 9-4.5S16.97 3 12 3zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3z"/>
                    </svg>
                    <svg v-else class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-1 14H6v-2h12v2zm0-4H6V7h12v6z"/>
                    </svg>
                  </div>
                </div>

                <!-- Product Info -->
                <div class="flex-1">
                  <div class="flex items-start justify-between mb-2">
                    <div>
                      <h3 class="text-lg font-bold text-gray-900">{{ item.productName }}</h3>
                      <div class="flex items-center justify-between gap-3 text-sm text-gray-600 whitespace-nowrap">
                        <p class="truncate">SKU: {{ item.mfgPartNo || 'N/A' }}</p>
                        <p class="truncate text-right">Vendor: {{ item.vendorId || 'N/A' }}</p>
                      </div>
                    </div>
                    <button @click="removeFromCart(item.productId)" class="text-red-500 hover:text-red-700 font-semibold text-sm">
                      Remove
                    </button>
                  </div>

                  <!-- Pricing and Quantity -->
                  <div class="flex items-center gap-4 mt-4">
                    <div>
                      <p class="text-xs text-gray-600 font-semibold">Unit Price</p>
                      <p class="text-lg font-bold" style="color: #2F5597;">${{ formatPrice(item.productPrice?.[0]?.rsPrice || 0) }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 font-semibold">Quantity</p>
                      <div class="flex items-center gap-2 mt-1">
                        <button @click="updateQuantity(item.productId, item.quantity - 1)" class="px-2 py-1 border border-gray-300 rounded text-sm font-semibold hover:bg-gray-100 transition">−</button>
                        <input v-model.number="item.quantity" @change="updateQuantity(item.productId, $event.target.value)" type="number" min="1" class="w-12 text-center border border-gray-300 rounded px-2 py-1">
                        <button @click="updateQuantity(item.productId, item.quantity + 1)" class="px-2 py-1 border border-gray-300 rounded text-sm font-semibold hover:bg-gray-100 transition">+</button>
                      </div>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600 font-semibold">Line Total</p>
                      <p class="text-lg font-bold" style="color: #2F5597;">${{ formatPrice((item.productPrice?.[0]?.rsPrice || 0) * item.quantity) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Card -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Quote Summary</h2>
            
            <div class="space-y-3 mb-6 pb-6 border-b border-gray-200">
              <div class="flex justify-between">
                <span class="text-gray-600">Items:</span>
                <span class="font-semibold text-gray-900">{{ cartStore.cartCount }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-semibold text-gray-900">${{ formatPrice(cartStore.cartTotal) }}</span>
              </div>
            </div>

            <div class="mb-6">
              <div class="text-lg font-bold text-gray-900 flex justify-between mb-4">
                <span>Total:</span>
                <span style="color: #2F5597;">${{ formatPrice(cartStore.cartTotal) }}</span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
              <button @click="downloadQuote" class="w-full px-4 py-3 text-white font-semibold rounded-lg transition inline-flex items-center justify-center gap-2" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"></path>
                </svg>
                <span>Download Quote</span>
              </button>
              <button @click="requestQuote" :disabled="isSubmittingQuote" class="w-full px-4 py-3 border-2 font-semibold rounded-lg transition inline-flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed" style="border-color: #2F5597; color: #2F5597;" @mouseenter="!isSubmittingQuote && ($event.target.style.backgroundColor='#cce4f4')" @mouseleave="$event.target.style.backgroundColor='transparent'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span>{{ isSubmittingQuote ? 'Submitting Quote...' : 'Request Quote' }}</span>
              </button>
              <button @click="clearAllItems" class="w-full px-4 py-3 border border-red-300 text-red-600 font-semibold rounded-lg transition hover:bg-red-50 inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2M7 7l1 12a2 2 0 002 2h4a2 2 0 002-2l1-12"></path>
                </svg>
                <span>Clear Quote</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useCartStore } from '../../stores/cartStore'
import { useQuotesStore } from '../../stores/quotesStore'
import { useToastStore } from '../../stores/toastStore'
import { useAuthStore } from '../../stores/authStore'
import Navbar from '../../components/Navbar.vue'

const router = useRouter()
const cartStore = useCartStore()
const quotesStore = useQuotesStore()
const toastStore = useToastStore()
const authStore = useAuthStore()
const failedImageIds = ref([])
const isSubmittingQuote = ref(false)

const goBack = () => {
  router.push({ name: 'products' })
}

const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
}

const getProductImageUrl = (item) => {
  const firstImage = item?.productImages?.[0]
  if (!firstImage) return null
  if (typeof firstImage === 'string') return firstImage
  return firstImage.imageUrl || null
}

const hasProductImage = (item) => {
  const imageUrl = getProductImageUrl(item)
  return Boolean(imageUrl) && !failedImageIds.value.includes(item.productId)
}

const handleImageError = (productId) => {
  if (!failedImageIds.value.includes(productId)) {
    failedImageIds.value.push(productId)
  }
}

const getProductIcon = (productName) => {
  const name = (productName || '').toLowerCase()
  if (name.includes('server') || name.includes('instance')) return 'server'
  if (name.includes('azure') || name.includes('cloud') || name.includes('subscription')) return 'cloud'
  if (name.includes('database') || name.includes('sql')) return 'database'
  return 'default'
}

const removeFromCart = (productId) => {
  cartStore.removeItem(productId)
}

const updateQuantity = (productId, quantity) => {
  const numQty = parseInt(quantity) || 0
  const updated = cartStore.updateQuantity(productId, numQty)
  if (!updated) {
    toastStore.addToast('Account suspended: quote modifications are disabled', 'error')
  }
}

const clearAllItems = () => {
  if (confirm('Are you sure you want to clear your entire quote?')) {
    cartStore.clearCart()
  }
}

const escapeCsvValue = (value) => {
  const stringValue = String(value ?? '')
  if (stringValue.includes(',') || stringValue.includes('"') || stringValue.includes('\n')) {
    return `"${stringValue.replace(/"/g, '""')}"`
  }
  return stringValue
}

const downloadQuote = () => {
  if (cartStore.isEmpty) {
    toastStore.addToast('Your quote is empty', 'warning')
    return
  }

  try {
    const dateLabel = new Date().toISOString().slice(0, 10)
    const rows = [
      ['Quote Date', dateLabel],
      [],
      ['Product Name', 'SKU', 'Vendor', 'Quantity', 'Unit Price', 'Line Total']
    ]

    cartStore.items.forEach((item) => {
      const quantity = Number(item.quantity || 0)
      const unitPrice = Number(item.productPrice?.[0]?.rsPrice || 0)
      const lineTotal = quantity * unitPrice

      rows.push([
        item.productName || 'N/A',
        item.mfgPartNo || 'N/A',
        item.vendorId || 'N/A',
        quantity,
        formatPrice(unitPrice),
        formatPrice(lineTotal)
      ])
    })

    rows.push([])
    rows.push(['Total Items', cartStore.cartCount])
    rows.push(['Quote Total', formatPrice(cartStore.cartTotal)])

    const csvContent = rows
      .map((row) => row.map(escapeCsvValue).join(','))
      .join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `quote-${dateLabel}.csv`
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)

    toastStore.addToast('Quote downloaded successfully', 'success')
  } catch (error) {
    console.error('Error downloading quote:', error)
    toastStore.addToast('Failed to download quote', 'error')
  }
}

const requestQuote = async () => {
  if (isSubmittingQuote.value) return

  if (authStore.isRestricted) {
    toastStore.addToast('Account suspended: requesting quotes is disabled', 'error')
    return
  }

  if (cartStore.isEmpty) {
    toastStore.addToast('Your quote is empty', 'warning')
    return
  }

  // Check if user is authenticated
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to request a quote', 'info')
    // Redirect to login with return URL
    router.push({ name: 'login', query: { redirect: '/cart' } })
    return
  }

  let slowRequestTimer = null
  try {
    isSubmittingQuote.value = true
    slowRequestTimer = setTimeout(() => {
      toastStore.addToast('Still submitting your quote. Please wait...', 'info')
    }, 4000)

    const quoteItems = cartStore.items
      .map(item => ({
        product_id: Number(item.productId ?? item.id),
        quantity: Number(item.quantity || 1)
      }))
      .filter(item => Number.isInteger(item.product_id) && item.product_id > 0 && item.quantity > 0)

    if (quoteItems.length !== cartStore.items.length) {
      toastStore.addToast('Some cart items are missing valid product IDs. Please re-add those items and try again.', 'error')
      return
    }

    const response = await axios.post('/api/v1/quotes', {
      items: quoteItems,
      description: cartStore.revisionSourceQuoteId
        ? `Revision of quote ${cartStore.revisionSourceQuoteId}`
        : null,
      revised_from_quote_id: cartStore.revisionSourceQuoteId || null,
    }, {
      timeout: 45000,
    })

    if (slowRequestTimer) {
      clearTimeout(slowRequestTimer)
      slowRequestTimer = null
    }

    if (response.data?.success) {
      toastStore.addToast(`Quote #${response.data.data.quote_id} created successfully`, 'success')
      cartStore.clearCart()

      // Navigate immediately after success for faster UX
      router.push({ name: 'quotes' })
    } else {
      toastStore.addToast(response.data?.message || 'Failed to create quote', 'error')
    }
  } catch (error) {
    if (slowRequestTimer) {
      clearTimeout(slowRequestTimer)
      slowRequestTimer = null
    }
    console.error('Error creating quote:', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to create quote', 'error')
  } finally {
    isSubmittingQuote.value = false
  }
}

const clearRevisionMode = () => {
  cartStore.clearRevisionSource()
  toastStore.addToast('Revision mode cleared', 'info')
}
</script>
