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
              <h3 class="text-sm font-semibold text-gray-900 line-clamp-2" :title="buildProductHoverDetails(product)">{{ product.productName }}</h3>
              <span v-if="product.discontinueProduct" class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">EOL</span>
              <span v-else class="ml-2 px-2 py-1 text-xs font-semibold rounded" style="background-color: #cce4f4; color: #2F5597;">Active</span>
            </div>

            <div class="flex items-center justify-between gap-3 text-xs text-gray-600 mb-3">
              <p class="truncate" :title="`SKU: ${getProductSku(product)}`">SKU: {{ getProductSku(product) }}</p>
              <p class="truncate text-right" :title="`Vendor: ${getProductVendor(product)}`">Vendor: {{ getProductVendor(product) }}</p>
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
              <button @click="viewDetails(product)" class="flex-1 px-3 py-2 text-white text-sm font-semibold rounded-lg transition inline-flex items-center justify-center gap-1" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
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
              <button @click="openShareModal(product)" class="px-3 py-2 rounded-lg transition border border-gray-300 text-gray-600 hover:bg-gray-50" title="Share Product">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C9.886 12.511 11.326 12 12.889 12c2.87 0 5.322 1.723 6.296 4.182m-16.338 0A6.986 6.986 0 019.111 12c1.563 0 3.003.511 4.205 1.342M15 6a3 3 0 11-6 0 3 3 0 016 0zm6 14a2 2 0 11-4 0 2 2 0 014 0zM7 20a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
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
import { computed, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useCartStore } from '../../stores/cartStore'
import { useToastStore } from '../../stores/toastStore'
import { useAuthStore } from '../../stores/authStore'
import Navbar from '../../components/Navbar.vue'
import { usePricingSettings } from '../../composables/usePricingSettings'
import api from '../../services/api'

const router = useRouter()
const route = useRoute()
const favoritesStore = useFavoritesStore()
const cartStore = useCartStore()
const toastStore = useToastStore()
const authStore = useAuthStore()
const searchQuery = ref('')
const showShareModal = ref(false)
const sharingProduct = ref(null)
const shareRecipientEmail = ref('')
const shareNote = ref('')
const shareGeneratedLink = ref('')
const shareSubmitting = ref(false)
const { loadPricingSettings, getCatalogPriceWithRules, convertFromUsd, formatWithCurrency } = usePricingSettings()

const filteredFavorites = computed(() => {
  const term = String(searchQuery.value || '').toLowerCase().trim()
  if (!term) {
    return favoritesStore.items
  }

  return favoritesStore.items.filter((product) => {
    const blob = [
      product?.productName,
      getProductVendor(product),
      getProductSku(product),
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
    lines.push(`Price: ${formatPrice(price)}`)
  }

  return lines.join('\n')
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
        imageUrl: getPrimaryImage(product) || '',
        price: Number(product.productPrice?.[0]?.rsPrice || 0),
      },
    })

    const shareUrl = String(response.data?.data?.share_url || '').trim()
    shareGeneratedLink.value = shareUrl
    toastStore.addToast('Share link generated. Use Copy Link or Send to Email.', 'success')
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
  const productName = sharingProduct.value?.productName || 'Shared product'
  const note = shareNote.value.trim()
  const bodyParts = [
    'I wanted to share this product with you:',
    productName,
    '',
  ]

  if (note) {
    bodyParts.push(`Note: ${note}`, '')
  }

  bodyParts.push(link)
  const subject = `Shared product: ${productName}`
  const body = bodyParts.join('\n')
  window.location.href = `mailto:${recipient}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`
}
</script>
