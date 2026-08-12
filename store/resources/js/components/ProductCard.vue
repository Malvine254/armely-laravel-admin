<template>
  <div class="product-card-shell group flex h-full min-h-[272px] flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-[0_2px_10px_rgba(15,42,82,0.05)] transition hover:border-blue-200 hover:shadow-[0_8px_24px_rgba(15,42,82,0.10)] sm:h-[272px] sm:min-h-0 sm:flex-row">
    <div class="relative flex h-56 flex-shrink-0 items-center justify-center overflow-hidden border-b border-slate-100 bg-white transition sm:h-full sm:w-[39%] sm:border-b-0 sm:border-r">
      <img v-if="image && !imageFailed" :src="image" :alt="product.productName" class="h-full w-full object-contain p-3" :loading="eager ? 'eager' : 'lazy'" :fetchpriority="eager ? 'high' : 'auto'" decoding="async" sizes="(min-width: 1024px) 320px, (min-width: 768px) 50vw, 100vw" width="320" height="160" @error="imageFailed = true">
      <div v-else class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 opacity-80"><div class="absolute right-2 top-2 h-12 w-12 rounded-full bg-blue-400 opacity-10"></div><div class="absolute bottom-4 left-2 h-8 w-8 rounded-full bg-blue-300 opacity-10"></div></div>
      <div v-if="!image || imageFailed" class="relative z-10 text-center">
        <svg v-if="productIcon === 'server'" class="mx-auto mb-2 h-16 w-16 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-3 8h-2V5h2v6z"/></svg>
        <svg v-else-if="productIcon === 'cloud'" class="mx-auto mb-2 h-16 w-16 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4c-1.48 0-2.85.43-4.01 1.17l1.46 1.46C10.21 5.23 11.08 5 12 5c3.04 0 5.5 2.46 5.5 5.5v.5H19c2.05 0 3.71 1.66 3.71 3.71 0 1.71-1.04 2.86-2.36 3.41z"/></svg>
        <svg v-else-if="productIcon === 'database'" class="mx-auto mb-2 h-16 w-16 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 2.16-9 4.5S7.03 12 12 12s9-2.16 9-4.5S16.97 3 12 3zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3zm0 7c-4.97 0-9 2.16-9 4.5S7.03 24 12 24s9-2.16 9-4.5-4.03-4.5-9-4.5zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3z"/></svg>
        <svg v-else class="mx-auto mb-2 h-16 w-16 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <p class="text-xs text-gray-500">Image not available</p>
        <p class="mt-1 break-all px-2 font-mono text-[11px] text-gray-600">Save as: {{ expectedImageFileName }}</p>
      </div>
    </div>

    <div class="flex min-h-0 min-w-0 flex-1 flex-col p-4">
      <div class="mb-2 flex items-start justify-between gap-3">
        <h3 class="line-clamp-2 min-h-[2.5rem] min-w-0 flex-1 text-sm font-bold leading-5 text-[#102a52]" :title="hoverDetails">{{ product.productName }}</h3>
        <span class="flex-shrink-0 rounded px-2 py-1 text-[10px] font-semibold" :class="product.discontinueProduct ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-[#2F5597]'">{{ product.discontinueProduct ? 'EOL' : 'Active' }}</span>
      </div>
      <div class="mb-2 flex items-center justify-between gap-3 text-[11px] text-slate-500">
        <p class="truncate" :title="`SKU: ${sku}`">SKU: {{ sku }}</p>
        <p class="max-w-[54%] truncate text-right" :title="`Vendor: ${vendor}`">Vendor: {{ vendor }}</p>
      </div>
      <div class="mb-2 flex items-center gap-1">
        <svg v-for="star in 5" :key="star" class="h-3.5 w-3.5" :class="star <= Math.round(reviewStats.average) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        <span class="ml-1 text-[11px] text-gray-500">{{ reviewStats.total ? `${reviewStats.average.toFixed(1)} (${reviewStats.total})` : 'No reviews' }}</span>
      </div>
      <div v-if="basePrice > 0" class="mb-2 min-h-[3.25rem]">
        <div v-if="pricingReady" class="flex flex-wrap items-baseline justify-between gap-2">
          <div class="flex items-baseline gap-2">
            <p class="text-xl font-extrabold text-[#2F5597]">{{ formattedPrice }}</p>
            <span v-if="hasDiscount" class="text-sm text-gray-400 line-through">MSRP: {{ formattedMsrp }}</span>
            <span v-if="hasDiscount" class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Save {{ savingsPercent }}%</span>
          </div>
          <p class="text-[11px] text-slate-500">Min Qty: {{ product.productPrice?.[0]?.minQty || 1 }}</p>
        </div>
        <div v-else class="h-8 w-28 animate-pulse rounded bg-gray-200"></div>
      </div>
      <div class="mb-3 flex items-center justify-between gap-3 text-[11px]">
        <span class="rounded bg-emerald-50 px-2 py-1 font-semibold" :class="outOfStock ? 'text-red-700' : 'text-emerald-700'">{{ stockLabel }}</span>
        <span class="max-w-[48%] truncate text-right text-gray-500" :title="warehouseSummary">{{ warehouseSummary }}</span>
      </div>
      <div class="mt-auto flex w-full gap-2">
        <button type="button" class="inline-flex flex-1 items-center justify-center gap-1 rounded-lg bg-[#2F5597] px-3 py-2 text-sm font-semibold text-white transition hover:bg-[#1f4788]" @click="$emit('view', product)"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z"/></svg><span>View</span></button>
        <button type="button" :disabled="outOfStock" class="rounded-lg bg-[#2F5597] px-3 py-2 text-white transition hover:bg-[#1f4788] disabled:cursor-not-allowed disabled:opacity-60" :title="outOfStock ? 'Out of stock' : 'Add to Quote'" @click="$emit('quote', product)"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5.4 5M7 13l-1.2 6.4A1 1 0 006.8 21h10.4a1 1 0 001-.8L20 13M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg></button>
        <button type="button" class="rounded-lg border px-3 py-2 transition" :class="favorite ? 'border-[#2F5597] bg-[#cce4f4] text-[#2F5597]' : 'border-gray-300 text-gray-600'" :title="favorite ? 'Remove from Favorites' : 'Add to Favorites'" @click="$emit('favorite', product)"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></button>
        <button type="button" class="rounded-lg border border-gray-300 px-3 py-2 text-gray-600 transition hover:bg-gray-50" title="Share Product" @click="$emit('share', product)"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C9.886 12.511 11.326 12 12.889 12c2.87 0 5.322 1.723 6.296 4.182m-16.338 0A6.986 6.986 0 019.111 12c1.563 0 3.003.511 4.205 1.342M15 6a3 3 0 11-6 0 3 3 0 016 0zm6 14a2 2 0 11-4 0 2 2 0 014 0zM7 20a2 2 0 11-4 0 2 2 0 014 0z"/></svg></button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { usePricingSettings } from '../composables/usePricingSettings'

const props = defineProps({
  product: { type: Object, required: true },
  image: { type: String, default: '' },
  favorite: { type: Boolean, default: false },
  eager: { type: Boolean, default: false },
  reviewStats: { type: Object, default: () => ({ total: 0, average: 0 }) },
})
defineEmits(['view', 'quote', 'favorite', 'share'])

const imageFailed = ref(false)
const pricingReady = ref(false)
const { loadPricingSettings, convertFromUsd, formatWithCurrency } = usePricingSettings()
onMounted(async () => { await loadPricingSettings(); pricingReady.value = true })

const basePrice = computed(() => Number(props.product?.productPrice?.[0]?.rsPrice || 0))
// The products API returns rsPrice as the customer-facing sell price.
// Applying the global profit rule again here would double-mark it up and can
// push the displayed price above MSRP, suppressing valid savings.
const customerUsd = computed(() => basePrice.value)
const msrp = computed(() => Number(props.product?.msrp || props.product?.regularPrice || props.product?.productPrice?.[0]?.msrp || 0))
const hasDiscount = computed(() => msrp.value > customerUsd.value + 0.005 && customerUsd.value > 0)
const savingsPercent = computed(() => hasDiscount.value ? Math.max(1, Math.round(((msrp.value - customerUsd.value) / msrp.value) * 100)) : 0)
const formattedPrice = computed(() => formatWithCurrency(convertFromUsd(customerUsd.value)))
const formattedMsrp = computed(() => formatWithCurrency(convertFromUsd(msrp.value)))
const sku = computed(() => String(props.product.mfgPartNo || props.product.mfg_part_no || props.product.tdsynnexSkuNo || props.product.skuNo || 'N/A'))
const vendor = computed(() => String(props.product.vendorName || props.product.manufacturer || props.product.vendorId || 'N/A'))
const hoverDetails = computed(() => [props.product.productName, `SKU: ${sku.value}`, `Vendor: ${vendor.value}`, basePrice.value > 0 ? `Price: ${formattedPrice.value}` : ''].filter(Boolean).join('\n'))
const expectedImageFileName = computed(() => {
  const source = String(props.product.productId || props.product.id || sku.value || 'product')
  return `${source.trim().replace(/[^a-z0-9._-]+/gi, '-').replace(/^-+|-+$/g, '') || 'product'}.jpg`
})
const quantity = computed(() => { const value = Number(props.product.availableQuantity ?? props.product.totalQuantity ?? props.product.quantity ?? props.product.qty); return Number.isFinite(value) ? Math.max(0, value) : null })
const outOfStock = computed(() => props.product.isAvailable === false || quantity.value === 0)
const stockLabel = computed(() => outOfStock.value ? 'Out of stock' : quantity.value === null ? 'Stock: Check availability' : `Stock: ${quantity.value}`)
const warehouseSummary = computed(() => Array.isArray(props.product.AvailabilityByWarehouse) && props.product.AvailabilityByWarehouse.length ? `${props.product.AvailabilityByWarehouse.length} warehouse${props.product.AvailabilityByWarehouse.length === 1 ? '' : 's'}` : quantity.value > 0 ? 'Available now' : 'Request quote')
const productIcon = computed(() => {
  const name = String(props.product.productName || '').toLowerCase()
  if (name.includes('server') || name.includes('instance')) return 'server'
  if (name.includes('azure') || name.includes('cloud') || name.includes('subscription')) return 'cloud'
  if (name.includes('database') || name.includes('sql')) return 'database'
  return 'default'
})
</script>
