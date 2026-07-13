<template>
  <div class="mb-6 space-y-3">
    <section class="relative h-80 w-full overflow-hidden bg-white text-slate-900" @mouseenter="pauseRotation" @mouseleave="resumeRotation">
      <div class="absolute inset-0 bg-white"></div>

      <div class="relative z-10 mx-auto grid h-full max-w-[1800px] grid-cols-1 items-center px-6 py-6 sm:px-10 lg:grid-cols-2 lg:px-16 xl:px-20">
        <div class="max-w-xl">
          <div class="mb-2 flex h-6 items-center gap-2">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600">{{ currentSlide?.isOnSale ? 'Featured offer' : 'Technology built for business' }}</p>
            <span v-if="currentSlide?.isOnSale" class="rounded-full bg-red-600 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wide text-white">
              Save {{ getDiscountPercent(currentSlide) }}%
            </span>
          </div>
          <h1 class="text-3xl font-bold leading-tight text-[#102a52] sm:text-4xl">Power your business<br>with the right technology</h1>
          <p class="mt-3 h-6 line-clamp-1 text-sm font-medium text-slate-600 sm:text-base">{{ currentSlide?.productName || 'Reliable. Scalable. Built for performance.' }}</p>
          <div class="mt-4 flex h-14 items-center gap-3">
            <router-link v-if="currentSlide" :to="{ name: 'product-detail', params: { id: currentSlide.productId } }" class="rounded-lg bg-[#0b3b82] px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
              {{ currentSlide.isOnSale ? 'Shop This Deal' : 'View Product' }}
            </router-link>
            <button v-else type="button" class="rounded-lg bg-[#0b3b82] px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700" @click="$emit('browse')">Shop Best Deals</button>
            <div v-if="currentSlide" class="flex flex-col">
              <div class="flex items-baseline gap-2">
                <span class="text-2xl font-extrabold" :class="currentSlide.isOnSale ? 'text-red-600' : 'text-[#0b3b82]'">{{ formatPrice(currentSlide.productPrice?.[0]?.rsPrice) }}</span>
                <span v-if="currentSlide.isOnSale && currentSlide.regularPrice" class="text-sm font-semibold text-red-500 line-through decoration-2">{{ formatPrice(currentSlide.regularPrice) }}</span>
              </div>
              <span v-if="currentSlide.isOnSale" class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Limited-time offer</span>
            </div>
          </div>
        </div>

        <div class="relative hidden h-64 items-center justify-center lg:flex">
          <transition name="hero-product" mode="out-in">
            <div v-if="currentSlide" :key="currentSlide.productId" class="flex h-full w-full items-center justify-center px-2">
              <div v-if="getProductImage(currentSlide)" class="relative flex h-64 w-full items-center justify-center p-2">
                <span class="absolute right-1 top-1 z-10 rounded-full border px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide" :class="currentSlide.isOnSale ? 'border-red-200 bg-red-50 text-red-700' : 'border-blue-200 bg-blue-50 text-[#0b3b82]'">
                  {{ currentSlide.isOnSale ? `Sale · ${currentSlide.heroCategory}` : currentSlide.heroCategory }}
                </span>
                <img :src="getProductImage(currentSlide)" :alt="currentSlide.productName" class="h-full w-full object-contain" loading="eager" @error="onProductImageError(currentSlide)">
              </div>
              <div v-else class="flex h-40 w-64 items-center justify-center rounded-xl border border-blue-100 bg-blue-50 text-sm font-semibold text-blue-700">Featured product</div>
            </div>
            <img v-else key="hardware" :src="heroFallbackSrc" alt="Business laptop, monitor and desktop" class="h-full max-h-64 w-full object-contain drop-shadow-2xl" @error="onHeroFallbackError">
          </transition>
        </div>
      </div>

      <div v-if="slides.length > 1" class="absolute bottom-3 left-1/2 z-20 flex -translate-x-1/2 gap-2">
        <button v-for="(_, index) in slides" :key="index" type="button" class="h-2.5 w-2.5 rounded-full border border-blue-300 transition" :class="index === activeIndex ? 'bg-[#0b3b82]' : 'bg-blue-100 hover:bg-blue-300'" :aria-label="`Show featured product ${index + 1}`" @click="setActive(index)"></button>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import api from '../services/api'
import { buildStoreUrl, normalizeLocalAssetUrl } from '../services/runtimeConfig'

defineEmits(['browse'])

const props = defineProps({
  products: { type: Array, default: () => [] },
})

const activeIndex = ref(0)
const featuredProducts = ref([])
let rotationTimer = null
const rotationPaused = ref(false)

const slides = computed(() => {
  const rows = featuredProducts.value.length > 0
    ? featuredProducts.value
    : (Array.isArray(props.products) ? props.products : [])
  if (featuredProducts.value.length > 0) {
    return rows.filter(product => getProductImageCandidates(product).length > 0).slice(0, 6)
  }
  const categories = [
    { label: 'Laptops', terms: ['laptop', 'notebook'] },
    { label: 'Printers', terms: ['printer', 'laserjet', 'officejet'] },
    { label: 'Monitors', terms: ['monitor', 'display'] },
    { label: 'Networking', terms: ['switch', 'router', 'network', 'firewall'] },
    { label: 'Desktops', terms: ['desktop', 'workstation', 'optiplex'] },
    { label: 'Software', terms: ['software', 'license', 'subscription'] },
  ]
  const selected = []
  const used = new Set()

  categories.forEach(category => {
    const matches = rows.filter(product => {
      const searchable = `${product?.productName || ''} ${product?.description || ''} ${product?.categoryName || ''} ${product?.categoryCode || ''}`.toLowerCase()
      return category.terms.some(term => searchable.includes(term)) && getProductImageCandidates(product).length > 0
    })
    const product = matches.find(item => item?.isOnSale) || matches[0]
    if (product && !used.has(product.productId)) {
      used.add(product.productId)
      selected.push({ ...product, heroCategory: category.label })
    }
  })

  if (selected.length < 4) {
    rows.filter(product => !used.has(product?.productId) && getProductImageCandidates(product).length > 0)
      .slice(0, 4 - selected.length)
      .forEach(product => selected.push({ ...product, heroCategory: product?.isOnSale ? 'Featured offer' : 'Popular product' }))
  }

  return selected.slice(0, 6)
})

const currentSlide = computed(() => slides.value[activeIndex.value] || null)
const imageCandidateIndexes = reactive({})

const appendImageCandidates = (candidates, value) => {
  const rawUrl = String(value || '').trim()
  if (!rawUrl) return

  if (rawUrl.startsWith('/images/')) {
    candidates.push(buildStoreUrl(rawUrl))
    return
  }

  candidates.push(normalizeLocalAssetUrl(rawUrl))
}

const getProductImageCandidates = (product) => {
  const candidates = []
  const appendImages = images => {
    if (!Array.isArray(images)) return
    images.forEach(image => appendImageCandidates(
      candidates,
      typeof image === 'string'
        ? image
        : image?.imagePath || image?.imageUrl || image?.imageURL || image?.image_url || image?.url || image?.thumbnailUrl
    ))
  }

  appendImages(product?.productImages)
  appendImages(product?.images)
  appendImageCandidates(candidates, product?.image_url)
  appendImageCandidates(candidates, product?.thumbnailUrl)

  return [...new Set(candidates.filter(Boolean))]
}

const getProductImage = (product) => {
  const candidates = getProductImageCandidates(product)
  const key = String(product?.productId || product?.sku || '')
  const index = Number(imageCandidateIndexes[key] || 0)
  return candidates[index] || ''
}

const onProductImageError = product => {
  const key = String(product?.productId || product?.sku || '')
  const nextIndex = Number(imageCandidateIndexes[key] || 0) + 1
  const candidates = getProductImageCandidates(product)
  imageCandidateIndexes[key] = nextIndex < candidates.length ? nextIndex : candidates.length
}

const heroFallbackCandidates = [
  buildStoreUrl('images/hero-hardware.png'),
  '/images/hero-hardware.png',
  '/store/images/hero-hardware.png',
  '/store/public/images/hero-hardware.png',
].filter((value, index, rows) => value && rows.indexOf(value) === index)
const heroFallbackIndex = ref(0)
const heroFallbackSrc = computed(() => heroFallbackCandidates[heroFallbackIndex.value] || '')
const onHeroFallbackError = () => {
  if (heroFallbackIndex.value < heroFallbackCandidates.length - 1) heroFallbackIndex.value += 1
}

const formatPrice = value => `$${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`

const getDiscountPercent = product => {
  const supplied = Number(product?.offer?.discountPercent || 0)
  if (supplied > 0) return Math.round(supplied)

  const regular = Number(product?.regularPrice || product?.productPrice?.[0]?.msrp || 0)
  const sale = Number(product?.salePrice || product?.productPrice?.[0]?.rsPrice || 0)
  if (regular <= 0 || sale <= 0 || sale >= regular) return 0
  return Math.round(((regular - sale) / regular) * 100)
}

const setActive = index => {
  activeIndex.value = index
  if (!rotationPaused.value) startRotation()
}

const stopRotation = () => {
  if (rotationTimer) window.clearInterval(rotationTimer)
  rotationTimer = null
}

const startRotation = () => {
  stopRotation()
  if (rotationPaused.value || slides.value.length < 2) return
  rotationTimer = window.setInterval(() => {
    activeIndex.value = (activeIndex.value + 1) % slides.value.length
  }, 6000)
}

const pauseRotation = () => {
  rotationPaused.value = true
  stopRotation()
}

const resumeRotation = () => {
  rotationPaused.value = false
  startRotation()
}

const loadFeaturedProducts = async () => {
  try {
    const response = await api.get('/products/featured')
    const rows = response?.data?.data
    featuredProducts.value = Array.isArray(rows) ? rows : []
  } catch (error) {
    // The catalog-page products remain a safe visual fallback.
    featuredProducts.value = []
  }
}

watch(slides, () => {
  activeIndex.value = 0
  startRotation()
})
onMounted(() => {
  startRotation()
  loadFeaturedProducts()
})
onBeforeUnmount(stopRotation)

</script>

<style scoped>
.hero-product-enter-active,
.hero-product-leave-active { transition: opacity .3s ease, transform .3s ease; }
.hero-product-enter-from { opacity: 0; transform: translateX(18px); }
.hero-product-leave-to { opacity: 0; transform: translateX(-18px); }
</style>
