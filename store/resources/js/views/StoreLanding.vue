<template>
  <div class="min-h-screen bg-[#f7f9fc]">
    <Navbar />
    <PopularCategories />

    <main>
      <section class="border-y border-slate-200 bg-white">
        <div class="mx-auto max-w-[1320px] px-4 py-12 sm:px-6 lg:py-16">
          <div class="mb-9 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
              <p class="mb-2 text-xs font-extrabold uppercase tracking-[0.2em] text-[#2F5597]">Armely business procurement</p>
              <h1 class="max-w-3xl text-2xl font-extrabold tracking-tight text-[#102a52] sm:text-3xl">Everything your business needs.</h1>
              <p class="mt-3 max-w-2xl text-base text-slate-600">Source trusted technology, manage business purchases, and request competitive quotes from one streamlined storefront.</p>
            </div>
            <router-link :to="{ name: 'products' }" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#0b3b82] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#164f9e]">
              Browse all products <span aria-hidden="true">→</span>
            </router-link>
          </div>

          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article v-for="service in services" :key="service.title" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-[0_6px_24px_rgba(15,42,82,0.06)] transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-[0_14px_34px_rgba(15,42,82,0.11)]">
              <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#2F5597]" v-html="service.icon"></div>
              <h2 class="text-base font-extrabold text-[#102a52]">{{ service.title }}</h2>
              <p class="mt-1 text-sm leading-6 text-slate-600">{{ service.description }}</p>
            </article>
          </div>
        </div>
      </section>

      <section class="mx-auto max-w-[1320px] px-4 py-14 sm:px-6">
        <div class="mb-7 flex items-end justify-between gap-4">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#2F5597]">Selected for business</p>
            <h2 class="mt-1 text-2xl font-extrabold text-[#102a52] sm:text-3xl">Recommended Products</h2>
          </div>
          <router-link :to="{ name: 'products' }" class="text-sm font-bold text-[#2F5597] hover:underline">View all products</router-link>
        </div>

        <div v-if="loading" class="grid grid-cols-1 gap-4 xl:grid-cols-2">
          <div v-for="i in 8" :key="i" class="min-h-[248px] animate-pulse rounded-xl border border-slate-200 bg-white"></div>
        </div>
        <div v-else class="grid grid-cols-1 gap-4 xl:grid-cols-2">
          <ProductCard v-for="product in recommended" :key="product.productId" :product="product" :image="productImage(product)" @view="viewProduct" @quote="addToQuote" @favorite="toggleFavorite" @share="shareProduct" />
        </div>
      </section>

      <section class="bg-[#102a52] py-14 text-white">
        <div class="mx-auto max-w-[1320px] px-4 sm:px-6">
          <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-200">Built around your workflow</p>
            <h2 class="mt-1 text-2xl font-extrabold sm:text-3xl">Shop by Business Need</h2>
          </div>
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <button v-for="need in businessNeeds" :key="need.title" type="button" class="group rounded-xl border border-white/15 bg-white/8 p-5 text-left transition hover:-translate-y-1 hover:border-white/30 hover:bg-white/12" @click="openNeed(need)">
              <span class="mb-7 flex h-10 w-10 items-center justify-center rounded-lg bg-white/12 text-xl">{{ need.symbol }}</span>
              <span class="block text-lg font-extrabold">{{ need.title }}</span>
              <span class="mt-1 block text-sm leading-6 text-blue-100">{{ need.description }}</span>
              <span class="mt-4 inline-block text-sm font-bold text-white">Explore solutions →</span>
            </button>
          </div>
        </div>
      </section>

      <section v-if="recentProducts.length" class="mx-auto max-w-[1320px] px-4 py-14 sm:px-6">
        <h2 class="text-2xl font-extrabold text-[#102a52] sm:text-3xl">Recently Viewed</h2>
        <p class="mt-1 text-sm text-slate-600">Continue where you left off.</p>
        <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
          <ProductCard v-for="product in recentProducts" :key="product.productId" :product="product" :image="productImage(product)" @view="viewProduct" @quote="addToQuote" @favorite="toggleFavorite" @share="shareProduct" />
        </div>
      </section>

      <section class="mx-auto max-w-[1320px] px-4 pb-16 pt-5 sm:px-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0b3b82] to-[#2F5597] px-6 py-10 text-white shadow-[0_18px_50px_rgba(11,59,130,0.22)] sm:px-10 lg:flex lg:items-center lg:justify-between">
          <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full border-[34px] border-white/10"></div>
          <div class="relative max-w-2xl"><p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Procurement support</p><h2 class="mt-2 text-2xl font-extrabold sm:text-3xl">Need volume pricing or a custom solution?</h2><p class="mt-2 text-sm leading-6 text-blue-50">Build your request and let our team help source the right products for your organization.</p></div>
          <button type="button" class="relative mt-6 inline-flex rounded-lg bg-white px-5 py-3 text-sm font-extrabold text-[#0b3b82] transition hover:bg-blue-50 lg:mt-0" @click="openProcurementRequest">Start a Custom Request</button>
        </div>
      </section>
    </main>

    <div v-if="showProcurementForm" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8">
      <button type="button" class="absolute inset-0 bg-slate-950/55" aria-label="Close custom request form" @click="closeProcurementRequest"></button>
      <form class="relative max-h-full w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl" @submit.prevent="submitProcurementRequest">
        <div class="border-b border-slate-200 px-6 py-5">
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#2F5597]">Procurement support</p>
          <h2 class="mt-1 text-2xl font-extrabold text-[#102a52]">Tell us what your organization needs</h2>
          <p class="mt-2 text-sm text-slate-600">Use this form for bulk pricing, hard-to-find products, complete office setups, or custom technology requirements.</p>
        </div>
        <div class="grid gap-4 px-6 py-5 sm:grid-cols-2">
          <label class="text-sm font-semibold text-slate-700 sm:col-span-2">Product or solution needed
            <textarea v-model.trim="procurementForm.search_query" required maxlength="500" rows="3" placeholder="Example: 25 business laptops with docks and three-year warranties" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal outline-none focus:border-[#2F5597]"></textarea>
          </label>
          <label class="text-sm font-semibold text-slate-700">Preferred manufacturer
            <input v-model.trim="procurementForm.manufacturer" maxlength="100" placeholder="Optional" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal outline-none focus:border-[#2F5597]">
          </label>
          <label class="text-sm font-semibold text-slate-700">Model or part number
            <input v-model.trim="procurementForm.model_or_part_number" maxlength="150" placeholder="Optional" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal outline-none focus:border-[#2F5597]">
          </label>
          <label class="text-sm font-semibold text-slate-700">Estimated quantity
            <input v-model.number="procurementForm.quantity" type="number" min="1" max="100000" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal outline-none focus:border-[#2F5597]">
          </label>
          <label class="text-sm font-semibold text-slate-700">Target delivery date
            <input v-model="targetDeliveryDate" type="date" :min="minimumDeliveryDate" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal outline-none focus:border-[#2F5597]">
          </label>
          <label class="text-sm font-semibold text-slate-700 sm:col-span-2">Configuration, budget, or other requirements
            <textarea v-model.trim="procurementNotes" maxlength="1800" rows="4" placeholder="Include required specifications, acceptable alternatives, deployment locations, or budget guidance." class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal outline-none focus:border-[#2F5597]"></textarea>
          </label>
          <p v-if="procurementError" class="text-sm font-semibold text-red-700 sm:col-span-2">{{ procurementError }}</p>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end">
          <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700" @click="closeProcurementRequest">Cancel</button>
          <button type="submit" :disabled="procurementSubmitting" class="rounded-lg bg-[#2F5597] px-5 py-2 text-sm font-semibold text-white disabled:cursor-wait disabled:opacity-60">{{ procurementSubmitting ? 'Sending request…' : 'Send to Procurement Team' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '../stores/cartStore'
import { useToastStore } from '../stores/toastStore'
import { useAuthStore } from '../stores/authStore'
import Navbar from '../components/Navbar.vue'
import PopularCategories from '../components/PopularCategories.vue'
import ProductCard from '../components/ProductCard.vue'
import api from '../services/api'
import { resolveProductImageUrl } from '../services/runtimeConfig'
import { buildProductsLocation } from '../services/productRoute'
import { getRecentlyViewedIds } from '../services/recentlyViewed'
import { usePricingSettings } from '../composables/usePricingSettings'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()
const toastStore = useToastStore()
const authStore = useAuthStore()
const { loadPricingSettings } = usePricingSettings()
const recommended = ref([])
const recentProducts = ref([])
const loading = ref(true)
const showProcurementForm = ref(false)
const procurementSubmitting = ref(false)
const procurementError = ref('')
const targetDeliveryDate = ref('')
const procurementNotes = ref('')
const procurementForm = reactive({ search_query: '', manufacturer: '', model_or_part_number: '', quantity: 1 })
const minimumDeliveryDate = computed(() => new Date().toISOString().slice(0, 10))

const icon = path => `<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="${path}"/></svg>`
const services = [
  { title: 'Business Pricing', description: 'Competitive pricing structured for organizations and repeat purchasing.', icon: icon('M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8v2m0 16v2M5 5l1.5 1.5M17.5 17.5 19 19M2 12h2m16 0h2') },
  { title: 'Bulk Orders', description: 'Simplified sourcing for larger quantities, teams, and multi-site rollouts.', icon: icon('M4 7h16M4 12h16M4 17h10') },
  { title: 'Fast Delivery', description: 'Availability-led fulfillment from a broad distribution network.', icon: icon('M3 7h11v10H3V7Zm11 4h4l3 3v3h-7v-6ZM7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm11 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z') },
  { title: 'Dedicated Support', description: 'Procurement guidance from quote request through order completion.', icon: icon('M18 10a6 6 0 0 0-12 0v4a2 2 0 0 0 2 2h1v-5H6m12 0h-3v5h1a2 2 0 0 0 2-2v-4Zm0 6c0 3-2 4-5 4') },
]
const businessNeeds = [
  { title: 'Office Setup', description: 'Desktops, displays, peripherals, and productivity essentials.', symbol: '▦', query: 'office desktop monitor' },
  { title: 'Remote Work', description: 'Laptops, docks, headsets, and secure mobile productivity.', symbol: '⌂', query: 'laptop dock headset' },
  { title: 'Networking', description: 'Switching, routing, wireless, and infrastructure solutions.', symbol: '⌘', category: 'Networking' },
  { title: 'Printing', description: 'Printers, scanners, supplies, and document workflow hardware.', symbol: '▤', category: 'Printers & Scanners' },
]

const recordsFrom = response => {
  const data = response?.data?.data || {}
  return Array.isArray(data.records) ? data.records : (Array.isArray(data) ? data : [])
}
const productLink = product => ({ name: 'product-detail', params: { id: product.productId } })
const productVendor = product => String(product.vendorName || product.manufacturer || product.vendorId || 'Armely').replace('TD SYNNEX', product.manufacturer || 'Featured')
const productSku = product => String(product.mfgPartNo || product.mfg_part_no || product.tdsynnexSkuNo || product.skuNo || 'N/A')
const productPrice = product => `$${Number(product?.productPrice?.[0]?.rsPrice || product?.price || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
const stockQuantity = product => {
  const value = product?.availableQuantity ?? product?.totalQuantity ?? product?.quantity ?? product?.qty
  const quantity = Number(value)
  return Number.isFinite(quantity) ? Math.max(0, quantity) : null
}
const isOutOfStock = product => product?.isAvailable === false || stockQuantity(product) === 0
const stockLabel = product => {
  const quantity = stockQuantity(product)
  if (isOutOfStock(product)) return 'Out of stock'
  return quantity === null ? 'Stock available' : `${quantity.toLocaleString()} in stock`
}
const addToQuote = product => {
  if (isOutOfStock(product)) return
  const added = cartStore.addItem(product, 1)
  toastStore.addToast(added ? `Added "${product.productName}" to quote` : 'Unable to add this product', added ? 'success' : 'error')
}
const viewProduct = product => router.push(productLink(product))
const toggleFavorite = product => {
  toastStore.addToast(`Open ${product.productName} to manage favorites`, 'info')
  viewProduct(product)
}
const shareProduct = async product => {
  const url = new URL(router.resolve(productLink(product)).href, window.location.origin).toString()
  if (navigator.share) await navigator.share({ title: product.productName, url })
  else {
    await navigator.clipboard.writeText(url)
    toastStore.addToast('Product link copied', 'success')
  }
}
const productImage = product => {
  const images = [...(product?.productImages || []), ...(product?.images || [])]
  const first = images[0]
  return resolveProductImageUrl(typeof first === 'string' ? first : first?.imagePath || first?.imageUrl || product?.image_url || product?.thumbnailUrl || '')
}
const productMsrp = product => Number(product?.msrp || product?.regularPrice || product?.productPrice?.[0]?.msrp || 0)
const hasGenuineMsrpSaving = product => {
  const customerPrice = Number(product?.productPrice?.[0]?.rsPrice || 0)
  return customerPrice > 0 && productMsrp(product) > customerPrice + 0.005
}
const openNeed = need => router.push(buildProductsLocation(need.category ? { category: need.category } : { q: need.query }))
const openProcurementRequest = () => {
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in so we can contact you about your request.', 'info')
    // Redirect values are router-internal paths. APP_BASE_PATH already adds
    // /store in production, so using /store here would become /store/store.
    router.push({ name: 'login', query: { redirect: '/?procurement=1' } })
    return
  }
  procurementError.value = ''
  showProcurementForm.value = true
}
const closeProcurementRequest = () => {
  if (procurementSubmitting.value) return
  showProcurementForm.value = false
  procurementError.value = ''
}
const submitProcurementRequest = async () => {
  procurementSubmitting.value = true
  procurementError.value = ''
  const details = [
    targetDeliveryDate.value ? `Target delivery date: ${targetDeliveryDate.value}` : '',
    procurementNotes.value,
  ].filter(Boolean).join('\n\n')
  try {
    const response = await api.post('/product-sourcing-requests', { ...procurementForm, notes: details })
    toastStore.addToast(response.data?.message || 'Your procurement request was sent. Our team will follow up with you.', 'success', 6000)
    showProcurementForm.value = false
    Object.assign(procurementForm, { search_query: '', manufacturer: '', model_or_part_number: '', quantity: 1 })
    targetDeliveryDate.value = ''
    procurementNotes.value = ''
  } catch (error) {
    procurementError.value = error.response?.data?.message || 'We could not send your request. Please try again.'
  } finally {
    procurementSubmitting.value = false
  }
}

onMounted(async () => {
  try {
    await loadPricingSettings()
    const responses = await Promise.all([1, 2, 3, 4].map(page => api.get('/products', {
      params: { hide_zero_price: true, catalog_clean: true, page, per_page: 100 },
    })))
    const rows = responses.flatMap(recordsFrom)
    const withImages = rows.filter(product => productImage(product))
    const discounted = withImages.filter(hasGenuineMsrpSaving)
    recommended.value = [...discounted, ...withImages.filter(product => !hasGenuineMsrpSaving(product))]
      .filter((product, index, products) => products.findIndex(item => String(item.productId) === String(product.productId)) === index)
      .slice(0, 8)
    const recentIds = getRecentlyViewedIds()
    recentProducts.value = recentIds.map(id => rows.find(product => String(product.productId) === id)).filter(Boolean).slice(0, 6)
    if (authStore.isAuthenticated && route.query.procurement === '1') {
      showProcurementForm.value = true
      router.replace({ name: 'home' })
    }
  } finally {
    loading.value = false
  }
})
</script>
