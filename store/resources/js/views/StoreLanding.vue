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
          <article v-for="product in recommended" :key="product.productId" class="group flex min-h-[248px] flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-[0_2px_10px_rgba(15,42,82,0.05)] transition hover:border-blue-200 hover:shadow-[0_8px_24px_rgba(15,42,82,0.10)] sm:flex-row">
            <router-link :to="productLink(product)" class="flex h-52 flex-shrink-0 items-center justify-center border-b border-slate-100 bg-white p-3 sm:h-auto sm:min-h-[248px] sm:w-[39%] sm:border-b-0 sm:border-r">
              <img v-if="productImage(product)" :src="productImage(product)" :alt="product.productName" class="h-full w-full object-contain transition duration-300 group-hover:scale-[1.02]" loading="lazy">
              <div v-else class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 text-2xl font-extrabold text-[#2F5597]">A</div>
            </router-link>
            <div class="flex min-w-0 flex-1 flex-col p-4">
              <div class="mb-2 flex items-start justify-between gap-3">
                <router-link :to="productLink(product)" class="line-clamp-2 min-h-10 min-w-0 flex-1 text-sm font-bold leading-5 text-[#102a52] hover:text-blue-700">{{ product.productName }}</router-link>
                <span class="flex-shrink-0 rounded px-2 py-1 text-[10px] font-semibold" :class="product.discontinueProduct ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-[#2F5597]'">{{ product.discontinueProduct ? 'EOL' : 'Active' }}</span>
              </div>
              <div class="mb-3 flex items-center justify-between gap-3 text-[11px] text-slate-500">
                <p class="truncate" :title="`SKU: ${productSku(product)}`">SKU: {{ productSku(product) }}</p>
                <p class="truncate text-right" :title="`Vendor: ${productVendor(product)}`">Vendor: {{ productVendor(product) }}</p>
              </div>
              <div class="mb-3 flex items-baseline justify-between gap-2">
                <p class="text-xl font-extrabold text-[#2F5597]">{{ productPrice(product) }}</p>
                <span v-if="product.isOnSale" class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Offer</span>
              </div>
              <div class="mb-3 flex items-center justify-between gap-3 text-[11px]">
                <span class="rounded bg-emerald-50 px-2 py-1 font-semibold" :class="isOutOfStock(product) ? 'text-red-700' : 'text-emerald-700'">{{ isOutOfStock(product) ? 'Out of stock' : 'In stock' }}</span>
                <span class="text-slate-500">Business-ready pricing</span>
              </div>
              <div class="mt-auto flex w-full gap-2">
                <router-link :to="productLink(product)" class="inline-flex flex-1 items-center justify-center rounded-lg bg-[#2F5597] px-3 py-2 text-sm font-semibold text-white transition hover:bg-[#1f4788]">View</router-link>
                <button type="button" :disabled="isOutOfStock(product)" class="inline-flex items-center justify-center rounded-lg bg-[#2F5597] px-3 py-2 text-white transition hover:bg-[#1f4788] disabled:cursor-not-allowed disabled:opacity-50" title="Add to Quote" @click="addToQuote(product)">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5.4 5M7 13l-1 5h12m-9 3h.01m8-.01h.01"/></svg>
                </button>
              </div>
            </div>
          </article>
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
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <router-link v-for="product in recentProducts" :key="product.productId" :to="productLink(product)" class="flex min-w-0 items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-blue-200 hover:shadow-md">
            <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-lg bg-white p-2"><img v-if="productImage(product)" :src="productImage(product)" :alt="product.productName" class="h-full w-full object-contain"></div>
            <div class="min-w-0"><p class="truncate text-sm font-extrabold text-[#102a52]">{{ product.productName }}</p><p class="mt-2 font-bold text-[#2F5597]">{{ productPrice(product) }}</p></div>
          </router-link>
        </div>
      </section>

      <section class="mx-auto max-w-[1320px] px-4 pb-16 pt-5 sm:px-6">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0b3b82] to-[#2F5597] px-6 py-10 text-white shadow-[0_18px_50px_rgba(11,59,130,0.22)] sm:px-10 lg:flex lg:items-center lg:justify-between">
          <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full border-[34px] border-white/10"></div>
          <div class="relative max-w-2xl"><p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Procurement support</p><h2 class="mt-2 text-2xl font-extrabold sm:text-3xl">Need volume pricing or a custom solution?</h2><p class="mt-2 text-sm leading-6 text-blue-50">Build your request and let our team help source the right products for your organization.</p></div>
          <router-link :to="{ name: 'cart' }" class="relative mt-6 inline-flex rounded-lg bg-white px-5 py-3 text-sm font-extrabold text-[#0b3b82] transition hover:bg-blue-50 lg:mt-0">Request a Custom Quote</router-link>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cartStore'
import { useToastStore } from '../stores/toastStore'
import Navbar from '../components/Navbar.vue'
import PopularCategories from '../components/PopularCategories.vue'
import api from '../services/api'
import { resolveProductImageUrl } from '../services/runtimeConfig'
import { buildProductsLocation } from '../services/productRoute'
import { getRecentlyViewedIds } from '../services/recentlyViewed'

const router = useRouter()
const cartStore = useCartStore()
const toastStore = useToastStore()
const recommended = ref([])
const recentProducts = ref([])
const loading = ref(true)

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
const isOutOfStock = product => product?.isAvailable === false || Number(product?.quantity ?? product?.totalQuantity ?? 1) <= 0
const addToQuote = product => {
  if (isOutOfStock(product)) return
  const added = cartStore.addItem(product, 1)
  toastStore.addToast(added ? `Added "${product.productName}" to quote` : 'Unable to add this product', added ? 'success' : 'error')
}
const productImage = product => {
  const images = [...(product?.productImages || []), ...(product?.images || [])]
  const first = images[0]
  return resolveProductImageUrl(typeof first === 'string' ? first : first?.imagePath || first?.imageUrl || product?.image_url || product?.thumbnailUrl || '')
}
const openNeed = need => router.push(buildProductsLocation(need.category ? { category: need.category } : { q: need.query }))

onMounted(async () => {
  try {
    const response = await api.get('/products', { params: { curated_it_mix: true, hide_zero_price: true, catalog_clean: true, page: 1, per_page: 16 } })
    const rows = recordsFrom(response)
    recommended.value = rows.filter(product => productImage(product)).slice(0, 8)
    const recentIds = getRecentlyViewedIds()
    recentProducts.value = recentIds.map(id => rows.find(product => String(product.productId) === id)).filter(Boolean).slice(0, 6)
  } finally {
    loading.value = false
  }
})
</script>
