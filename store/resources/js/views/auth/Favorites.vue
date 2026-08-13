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

      <div v-else class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
        <ProductCard
          v-for="(product, productIndex) in filteredFavorites"
          :key="product.productId"
          :product="product"
          :image="getPrimaryImage(product)"
          :favorite="isFavorite(product.productId)"
          :eager="productIndex < 2"
          @view="viewDetails"
          @quote="addToQuote"
          @favorite="toggleFavorite"
          @share="openShareModal"
        />
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
import ProductCard from '../../components/ProductCard.vue'
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

const isFavorite = (productId) => favoritesStore.isFavorite(productId)

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

const toggleFavorite = (product) => {
  const isNowFavorite = favoritesStore.toggleFavorite(product)
  if (isNowFavorite === null) {
    toastStore.addToast('Account suspended: favorites are read-only', 'error')
    return
  }

  toastStore.addToast(
    isNowFavorite ? `Added "${product.productName}" to favorites` : `Removed "${product.productName}" from favorites`,
    isNowFavorite ? 'success' : 'info'
  )
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
