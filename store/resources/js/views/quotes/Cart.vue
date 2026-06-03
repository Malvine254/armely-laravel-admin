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
                <div class="w-24 h-24 rounded-lg bg-white border border-gray-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                  <img
                    v-if="hasProductImage(item)"
                    :src="getProductImageUrl(item)"
                    :alt="item.productName"
                    class="w-full h-full object-contain p-1"
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
                      <p class="text-lg font-bold" style="color: #2F5597;">{{ formatAdjustedCurrency(getAdjustedUnitUsd(item)) }}</p>
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
                      <p class="text-lg font-bold" style="color: #2F5597;">{{ formatAdjustedCurrency(getAdjustedUnitUsd(item) * item.quantity) }}</p>
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
                <span class="font-semibold text-gray-900">{{ formatAdjustedCurrency(quoteSubtotalUsd) }}</span>
              </div>
            </div>

            <div class="mb-6">
              <div class="text-lg font-bold text-gray-900 flex justify-between mb-4">
                <span>Total:</span>
                <span style="color: #2F5597;">{{ formatAdjustedCurrency(quoteSubtotalUsd) }}</span>
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
              <button @click="requestQuote()" :disabled="isSubmittingQuote" class="w-full px-4 py-3 border-2 font-semibold rounded-lg transition inline-flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed" style="border-color: #2F5597; color: #2F5597;" @mouseenter="!isSubmittingQuote && ($event.target.style.backgroundColor='#cce4f4')" @mouseleave="$event.target.style.backgroundColor='transparent'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span>{{ isSubmittingQuote ? 'Submitting Quote...' : 'Request Quote' }}</span>
              </button>
              <button @click="openCartShareModal" class="w-full px-4 py-3 border font-semibold rounded-lg transition inline-flex items-center justify-center gap-2" style="border-color: #2F5597; color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#eef5fc'" @mouseleave="$event.target.style.backgroundColor='transparent'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C9.886 12.511 11.326 12 12.889 12c2.87 0 5.322 1.723 6.296 4.182m-16.338 0A6.986 6.986 0 019.111 12c1.563 0 3.003.511 4.205 1.342M15 6a3 3 0 11-6 0 3 3 0 016 0zm6 14a2 2 0 11-4 0 2 2 0 014 0zM7 20a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Share Cart</span>
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

      <div v-if="showCartShareModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-900/45" @click="closeCartShareModal"></div>
        <div class="relative w-full max-w-lg rounded-2xl border bg-white shadow-2xl" style="border-color:#cfe0f5;">
          <div class="px-5 py-4 border-b" style="border-color:#e2e8f0;">
            <h3 class="text-lg font-bold" style="color:#2F5597;">Share Cart</h3>
            <p class="text-sm text-slate-600 mt-1">Share {{ cartStore.cartCount }} item(s) with another user.</p>
          </div>

          <div class="p-5 space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Recipient Email (optional)</label>
              <input
                v-model="cartShareRecipientEmail"
                type="email"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                style="border-color:#cbd5e1;"
                placeholder="user@company.com"
              >
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Note (optional)</label>
              <textarea
                v-model="cartShareNote"
                rows="3"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                style="border-color:#cbd5e1;"
                placeholder="Add a message for the recipient"
              ></textarea>
            </div>

            <div v-if="cartShareGeneratedLink" class="rounded-lg border p-3" style="border-color:#bfdbfe;background:#eff6ff;">
              <p class="text-xs font-semibold text-slate-700 mb-1">Share Link</p>
              <p class="text-xs break-all text-slate-700">{{ cartShareGeneratedLink }}</p>
              <div class="mt-3 flex flex-wrap gap-2">
                <button @click="copyCartShareLink" type="button" class="px-3 py-2 text-xs font-semibold rounded-lg text-white" style="background-color:#2F5597;">Copy Link</button>
                <button @click="sendCartShareByEmail" type="button" :disabled="cartShareEmailSending" class="px-3 py-2 text-xs font-semibold rounded-lg border disabled:opacity-60" style="border-color:#2F5597;color:#2F5597;">{{ cartShareEmailSending ? 'Sending...' : 'Send to Email' }}</button>
              </div>
            </div>
          </div>

          <div class="px-5 py-4 border-t flex justify-end gap-2" style="border-color:#e2e8f0;">
            <button @click="closeCartShareModal" type="button" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">Close</button>
            <button @click="submitCartShare" type="button" :disabled="cartShareSubmitting" class="px-4 py-2 text-sm font-semibold rounded-lg text-white disabled:opacity-60" style="background-color:#2F5597;">
              {{ cartShareSubmitting ? 'Generating...' : (cartShareGeneratedLink ? 'Regenerate Link' : 'Generate Link') }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="showShippingConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-900/45" @click="closeShippingConfirmModal"></div>
        <div class="relative w-full max-w-lg rounded-2xl border bg-white shadow-2xl" style="border-color:#cfe0f5;">
          <div class="px-5 py-4 border-b" style="border-color:#e2e8f0;">
            <h3 class="text-lg font-bold" style="color:#2F5597;">Confirm Shipping Address</h3>
            <p class="text-sm text-slate-600 mt-1">Please confirm where this order should be delivered before submitting your quote.</p>
          </div>

          <div class="p-5 space-y-4">
            <div class="rounded-xl border p-4" style="border-color:#d9e6f7;background:#f8fbff;">
              <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Saved Address</p>
              <p class="text-sm font-semibold text-slate-900">{{ savedShippingAddressSummary }}</p>
            </div>
          </div>

          <div class="px-5 py-4 border-t flex flex-col sm:flex-row sm:justify-end gap-2" style="border-color:#e2e8f0;">
            <button @click="changeShippingAddress" type="button" class="px-4 py-2 text-sm font-semibold rounded-lg border" style="border-color:#2F5597;color:#2F5597;">
              Change Address
            </button>
            <button @click="closeShippingConfirmModal" type="button" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">
              Cancel
            </button>
            <button @click="requestQuote(true)" type="button" :disabled="isSubmittingQuote" class="px-4 py-2 text-sm font-semibold rounded-lg text-white disabled:opacity-60" style="background-color:#2F5597;">
              {{ isSubmittingQuote ? 'Submitting...' : 'Use This Address' }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="showProfileIncompleteModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-900/55" @click="closeProfileIncompleteModal"></div>
        <div class="relative w-full max-w-md overflow-hidden rounded-2xl border bg-white shadow-2xl" style="border-color:#cfe0f5;">
          <div class="px-5 py-4 border-b" style="border-color:#e2e8f0;background:#f8fbff;">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="text-xs font-bold uppercase tracking-wide" style="color:#2F5597;">Profile incomplete</p>
                <h3 class="text-xl font-bold text-slate-900 mt-1">Complete your profile</h3>
                <p class="text-sm text-slate-600 mt-1">Finish these items before requesting a quote.</p>
              </div>
              <button @click="closeProfileIncompleteModal" type="button" class="text-2xl leading-none text-slate-400 hover:text-slate-700" aria-label="Dismiss profile completion modal">×</button>
            </div>
          </div>

          <div class="p-5">
            <ul class="space-y-3">
              <li
                v-for="item in missingProfileItems"
                :key="item.key"
                class="flex items-center gap-3 rounded-xl border px-4 py-3"
                style="border-color:#fde68a;background:#fffbeb;"
              >
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                  </svg>
                </span>
                <span class="text-sm font-semibold text-slate-900">{{ item.label }}</span>
              </li>
            </ul>
          </div>

          <div class="px-5 py-4 border-t flex flex-col sm:flex-row sm:justify-end gap-2" style="border-color:#e2e8f0;">
            <button @click="closeProfileIncompleteModal" type="button" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">
              Dismiss
            </button>
            <button @click="goToAccountForProfile" type="button" class="px-4 py-2 text-sm font-semibold rounded-lg text-white" style="background-color:#2F5597;">
              Go to Account
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'
import { useCartStore } from '../../stores/cartStore'
import { useQuotesStore } from '../../stores/quotesStore'
import { useToastStore } from '../../stores/toastStore'
import { useAuthStore } from '../../stores/authStore'
import { usePricingSettings } from '../../composables/usePricingSettings'
import api from '../../services/api'
import Navbar from '../../components/Navbar.vue'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()
const quotesStore = useQuotesStore()
const toastStore = useToastStore()
const authStore = useAuthStore()
const failedImageIds = ref([])
const isSubmittingQuote = ref(false)
const showCartShareModal = ref(false)
const showShippingConfirmModal = ref(false)
const showProfileIncompleteModal = ref(false)
const cartShareRecipientEmail = ref('')
const cartShareNote = ref('')
const cartShareGeneratedLink = ref('')
const cartShareSubmitting = ref(false)
const { loadPricingSettings, getCatalogPriceWithRules, convertFromUsd, formatWithCurrency } = usePricingSettings()

const getAdjustedUnitUsd = (item) => {
  const base = Number(item?.productPrice?.[0]?.rsPrice || 0)
  return getCatalogPriceWithRules(base)
}

const quoteSubtotalUsd = computed(() => {
  return cartStore.items.reduce((sum, item) => {
    return sum + (getAdjustedUnitUsd(item) * Number(item.quantity || 0))
  }, 0)
})

const formatAdjustedCurrency = (amountUsd) => {
  return formatWithCurrency(convertFromUsd(amountUsd))
}

const getSavedShippingAddress = () => {
  return authStore.user?.shipping_address || {}
}

const hasUsableShippingAddress = (shipping = getSavedShippingAddress()) => {
  return [
    shipping.street_1,
    shipping.city,
    shipping.country,
  ].some((value) => String(value || '').trim() !== '')
}

const savedShippingAddressSummary = computed(() => {
  const shipping = getSavedShippingAddress()
  const parts = [
    shipping.label,
    shipping.street_1,
    shipping.street_2,
    [shipping.city, shipping.state].filter(Boolean).join(', '),
    shipping.postal_code,
    shipping.country,
  ].filter(Boolean)

  return parts.length ? parts.join(' | ') : 'No shipping address saved'
})

const goBack = () => {
  router.push({ name: 'products' })
}

const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
}

const mergeSharedItemsIntoCart = (sharedItems = []) => {
  const merged = new Map()

  cartStore.items.forEach((item) => {
    const key = String(item.productId || '')
    if (!key) return
    merged.set(key, { ...item, quantity: Number(item.quantity || 1) })
  })

  sharedItems.forEach((item) => {
    const key = String(item.productId || '')
    if (!key) return

    const existing = merged.get(key)
    if (existing) {
      existing.quantity = Number(existing.quantity || 0) + Number(item.quantity || 0)
      return
    }

    merged.set(key, { ...item, quantity: Math.max(1, Number(item.quantity || 1)) })
  })

  cartStore.replaceCartItems(Array.from(merged.values()))
}

const clearSharedMessageQuery = async () => {
  const nextQuery = { ...route.query }
  delete nextQuery.shared_message
  delete nextQuery.shared_token
  await router.replace({ query: nextQuery })
}

const importSharedCartFromPublicToken = async () => {
  const sharedToken = String(route.query.shared_token || '').trim()
  if (!sharedToken) return false

  try {
    const response = await api.get(`/shares/public/cart/${encodeURIComponent(sharedToken)}`)
    const payload = response.data?.data || {}
    const sharedItems = Array.isArray(payload.items) ? payload.items : []
    if (!sharedItems.length) {
      toastStore.addToast('This shared cart has no items to import', 'warning')
      await clearSharedMessageQuery()
      return true
    }

    if (cartStore.items.length > 0) {
      const shouldMerge = window.confirm(`Import ${sharedItems.length} shared item(s) from ${payload.shared_by_name || 'another user'}? Click OK to merge, or Cancel to replace your current cart.`)
      if (shouldMerge) {
        mergeSharedItemsIntoCart(sharedItems)
        toastStore.addToast(`Merged ${sharedItems.length} shared item(s) into your quote`, 'success')
      } else {
        cartStore.replaceCartItems(sharedItems)
        cartStore.clearRevisionSource()
        toastStore.addToast(`Replaced your quote with ${sharedItems.length} shared item(s)`, 'success')
      }
    } else {
      cartStore.replaceCartItems(sharedItems)
      cartStore.clearRevisionSource()
      toastStore.addToast(`Imported ${sharedItems.length} shared item(s) from ${payload.shared_by_name || 'another user'}`, 'success')
    }

    await clearSharedMessageQuery()
    return true
  } catch (error) {
    console.error('Failed to import public shared cart:', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to import shared cart', 'error')
    return true
  }
}

const importSharedCartFromMessage = async () => {
  const sharedMessageId = String(route.query.shared_message || '').trim()
  if (!sharedMessageId) return

  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to open a shared cart', 'info')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  try {
    const response = await api.get(`/shares/cart/${encodeURIComponent(sharedMessageId)}`)
    const payload = response.data?.data || {}
    const sharedItems = Array.isArray(payload.items) ? payload.items : []
    if (!sharedItems.length) {
      toastStore.addToast('This shared cart has no items to import', 'warning')
      await clearSharedMessageQuery()
      return
    }

    if (cartStore.items.length > 0) {
      const shouldMerge = window.confirm(`Import ${sharedItems.length} shared item(s) from ${payload.shared_by_name || 'another user'}? Click OK to merge, or Cancel to replace your current cart.`)
      if (shouldMerge) {
        mergeSharedItemsIntoCart(sharedItems)
        toastStore.addToast(`Merged ${sharedItems.length} shared item(s) into your quote`, 'success')
      } else {
        cartStore.replaceCartItems(sharedItems)
        cartStore.clearRevisionSource()
        toastStore.addToast(`Replaced your quote with ${sharedItems.length} shared item(s)`, 'success')
      }
    } else {
      cartStore.replaceCartItems(sharedItems)
      cartStore.clearRevisionSource()
      toastStore.addToast(`Imported ${sharedItems.length} shared item(s) from ${payload.shared_by_name || 'another user'}`, 'success')
    }

    await clearSharedMessageQuery()
  } catch (error) {
    console.error('Failed to import shared cart:', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to import shared cart', 'error')
  }
}

onMounted(async () => {
  await loadPricingSettings()
  const importedFromPublic = await importSharedCartFromPublicToken()
  if (importedFromPublic) return
  await importSharedCartFromMessage()
})

const getProductImageUrl = (item) => {
  const candidates = []

  const appendUrl = (value) => {
    const rawUrl = String(value || '').trim()
    if (rawUrl) candidates.push(rawUrl)
  }

  const appendImages = (images) => {
    if (!Array.isArray(images)) return
    images.forEach((image) => {
      if (typeof image === 'string') { appendUrl(image); return }
      if (image && typeof image === 'object') {
        appendUrl(image.imageUrl || image.imageURL || image.image_url || image.url || image.thumbnailUrl)
      }
    })
  }

  appendImages(item?.productImages)
  appendImages(item?.images)
  appendUrl(item?.image_url)
  appendUrl(item?.thumbnailUrl)
  appendUrl(item?.thumbnail)

  return candidates.find(url => url.startsWith('/images/') || url.includes('/images/products/'))
    || candidates[0]
    || null
}

const fallbackIncompleteFields = computed(() => {
  const missing = []
  const user = authStore.user || {}

  if (!String(user.phone || '').trim()) {
    missing.push('phone')
  }

  if (!hasUsableShippingAddress(user.shipping_address || {})) {
    missing.push('shipping_address')
  }

  if (!String(user.profile_picture_url || '').trim()) {
    missing.push('profile_picture')
  }

  return missing
})

const incompleteProfileFields = computed(() => {
  const apiFields = authStore.user?.incomplete_fields
  const fields = Array.isArray(apiFields) ? apiFields : []
  return Array.from(new Set([...fields, ...fallbackIncompleteFields.value]))
})

const missingProfileItems = computed(() => {
  const labels = {
    phone: 'Add phone number',
    shipping_address: 'Add shipping address',
    profile_picture: 'Add profile picture',
  }

  return incompleteProfileFields.value
    .filter((field) => labels[field])
    .map((field) => ({ key: field, label: labels[field] }))
})

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

const openCartShareModal = () => {
  if (cartStore.isEmpty) {
    toastStore.addToast('Your quote is empty', 'warning')
    return
  }

  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to share a cart', 'info')
    router.push({ name: 'login', query: { redirect: '/cart' } })
    return
  }

  cartShareRecipientEmail.value = ''
  cartShareNote.value = ''
  cartShareGeneratedLink.value = ''
  showCartShareModal.value = true
}

const closeCartShareModal = () => {
  showCartShareModal.value = false
  cartShareSubmitting.value = false
}

const submitCartShare = async () => {
  const recipientEmail = cartShareRecipientEmail.value.trim()

  cartShareSubmitting.value = true

  try {
    const payloadItems = cartStore.items.map((item) => ({
      productId: item.productId,
      productName: item.productName,
      quantity: Number(item.quantity || 1),
      mfgPartNo: item.mfgPartNo || '',
      vendorId: item.vendorId || '',
      billingModel: item.billingModel || '',
      billingFrequency: item.billingFrequency || '',
      productImages: Array.isArray(item.productImages) ? item.productImages : [],
      productPrice: Array.isArray(item.productPrice) ? item.productPrice : [],
    }))

    const response = await api.post('/shares/cart', {
      recipient_email: recipientEmail || null,
      note: cartShareNote.value.trim(),
      items: payloadItems,
    })

    const shareUrl = String(response.data?.data?.share_url || '').trim()
    cartShareGeneratedLink.value = shareUrl
    if (shareUrl) {
      toastStore.addToast('Share link generated. Use Copy Link or Send to Email.', 'success')
    } else {
      toastStore.addToast(`Shared ${payloadItems.length} item(s) successfully`, 'success')
    }
  } catch (error) {
    console.error('Failed to share cart:', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to share cart', 'error')
  } finally {
    cartShareSubmitting.value = false
  }
}

const copyCartShareLink = async () => {
  const link = cartShareGeneratedLink.value.trim()
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

const cartShareEmailSending = ref(false)

const sendCartShareByEmail = async () => {
  const link = cartShareGeneratedLink.value.trim()
  if (!link) {
    toastStore.addToast('Generate the share link first', 'warning')
    return
  }

  const recipientEmail = cartShareRecipientEmail.value.trim()
  if (!recipientEmail) {
    toastStore.addToast('Enter a recipient email address first', 'warning')
    return
  }

  cartShareEmailSending.value = true
  try {
    await api.post('/shares/cart/send-email', {
      to_email: recipientEmail,
      share_url: link,
      note: cartShareNote.value.trim(),
      item_count: cartStore.cartCount,
    })
    toastStore.addToast(`Share link sent to ${recipientEmail}`, 'success')
  } catch (err) {
    toastStore.addToast(err.response?.data?.message || 'Failed to send email', 'error')
  } finally {
    cartShareEmailSending.value = false
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
      const unitPrice = Number(getAdjustedUnitUsd(item) || 0)
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
    rows.push(['Quote Total', formatPrice(quoteSubtotalUsd.value)])

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

    toastStore.addToast('Quote downloaded successfully', 'success', 3000, { category: 'quotes' })
  } catch (error) {
    console.error('Error downloading quote:', error)
    toastStore.addToast('Failed to download quote', 'error')
  }
}

const closeShippingConfirmModal = () => {
  showShippingConfirmModal.value = false
}

const changeShippingAddress = () => {
  closeShippingConfirmModal()
  toastStore.addToast('Update your shipping address, then return to your quote.', 'info', 3000, { category: 'quotes' })
  router.push({ name: 'account' })
}

const closeProfileIncompleteModal = () => {
  showProfileIncompleteModal.value = false
}

const goToAccountForProfile = () => {
  closeProfileIncompleteModal()
  router.push({ name: 'account' })
}

const requestQuote = async (shippingConfirmed = false) => {
  if (isSubmittingQuote.value) return

  if (authStore.isRestricted) {
    toastStore.addToast('Account suspended: requesting quotes is disabled', 'error')
    return
  }

  if (cartStore.isEmpty) {
    toastStore.addToast('Your quote is empty', 'warning', 3000, { category: 'quotes' })
    return
  }

  // Check if user is authenticated
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to request a quote', 'info', 3000, { category: 'quotes' })
    // Redirect to login with return URL
    router.push({ name: 'login', query: { redirect: '/cart' } })
    return
  }

  if (missingProfileItems.value.length > 0) {
    showProfileIncompleteModal.value = true
    return
  }

  if (shippingConfirmed !== true) {
    showShippingConfirmModal.value = true
    return
  }

  closeShippingConfirmModal()

  let slowRequestTimer = null
  try {
    isSubmittingQuote.value = true
    slowRequestTimer = setTimeout(() => {
      toastStore.addToast('Still submitting your quote. Please wait...', 'info', 3000, { category: 'quotes' })
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
      toastStore.addToast(`Quote #${response.data.data.quote_id} created successfully`, 'success', 3000, { category: 'quotes' })
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
  toastStore.addToast('Revision mode cleared', 'info', 3000, { category: 'quotes' })
}
</script>
