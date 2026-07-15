<template>
  <section class="bg-white py-12 sm:py-14" aria-labelledby="popular-products-heading">
    <div class="mx-auto max-w-[1320px] px-4 sm:px-6">
      <h2 id="popular-products-heading" class="mb-10 text-center text-2xl font-bold tracking-tight text-[#102a52] sm:text-3xl">
        Explore Popular Products
      </h2>

      <div v-if="loading" class="popular-category-list">
        <div v-for="index in 6" :key="index" class="popular-category-card h-60 animate-pulse rounded-lg bg-slate-50 shadow-[0_12px_35px_rgba(15,23,42,0.08)]"></div>
      </div>

      <div v-else-if="categories.length" class="popular-category-list">
        <button
          v-for="category in categories"
          :key="category.slug"
          type="button"
          class="popular-category-card group flex h-60 flex-col overflow-hidden rounded-lg bg-white text-left shadow-[0_12px_35px_rgba(15,23,42,0.09)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_18px_40px_rgba(15,23,42,0.14)] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2F5597] focus-visible:ring-offset-2"
          :aria-label="`Browse popular ${category.label} products`"
          @click="browseCategory(category)"
        >
          <span class="flex h-[190px] w-full items-center justify-center bg-white px-4 pb-2 pt-5">
            <img
              :src="resolveProductImageUrl(category.imageUrl)"
              :alt="`${category.label} category`"
              class="h-full w-full object-contain transition duration-300 group-hover:scale-[1.03]"
              loading="lazy"
            />
          </span>
          <span class="mt-auto w-full px-3 pb-5 pt-3 text-center text-base font-bold text-black">
            {{ category.label }}
          </span>
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import { buildProductsLocation } from '../services/productRoute'
import { resolveProductImageUrl } from '../services/runtimeConfig'

const router = useRouter()
const categories = ref([])
const loading = ref(true)

const browseCategory = category => {
  const searchTerms = {
    'laptops-notebooks': 'laptop',
    'printers-scanners': 'printer',
    'monitors-displays': 'monitor',
    networking: 'networking',
    'desktops-workstations': 'desktop',
  }

  router.push(buildProductsLocation({
    q: searchTerms[category.slug] || category.label,
    productType: category.productType || 'hardware',
    category: category.segment || undefined,
    minPrice: 0,
    maxPrice: 0,
    page: 1,
  }))
}

onMounted(async () => {
  try {
    const response = await api.get('/products/popular-categories')
    categories.value = Array.isArray(response.data?.data) ? response.data.data.slice(0, 6) : []
  } catch {
    categories.value = []
  } finally {
    loading.value = false
  }
})
</script>
