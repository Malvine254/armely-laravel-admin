<template>
  <AdminLayout>
    <template #title>All Products</template>
    <div class="space-y-4">
      <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <div v-for="card in statCards" :key="card.label" class="rounded-xl bg-white p-4 shadow">
          <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ card.label }}</p>
          <p class="mt-1 text-2xl font-extrabold text-[#102a52]">{{ formatNumber(card.value) }}</p>
        </div>
      </div>

      <div class="rounded-xl bg-white p-4 shadow">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <h2 class="font-bold text-[#102a52]">AI product content</h2>
            <p class="mt-1 max-w-3xl text-sm text-slate-600">Search Bing for product website evidence and AI-verified matching images across the entire catalog. Descriptions use website evidence first, then conservative title-based AI content with general category knowledge. Title-based content is labeled for review, not website-verified. Existing content is kept except older generic AI text marked for repair.</p>
            <p class="mt-1 text-xs text-slate-500">Runs in the background using the products-sync queue and configured AI/search providers. API usage charges may apply. Unresolved products are retried after 24 hours.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <button :disabled="enriching" @click="enrichContent(null, 'description')" class="rounded-lg bg-[#2F5597] px-5 py-2.5 font-semibold text-white disabled:opacity-50">Fill Missing Descriptions</button>
            <button :disabled="enriching" @click="enrichContent(null, 'images')" class="rounded-lg bg-[#2F5597] px-5 py-2.5 font-semibold text-white disabled:opacity-50">Fill Missing Images</button>
          </div>
        </div>
        <p v-if="enrichmentMessage" role="status" class="mt-3 text-sm text-slate-700">{{ enrichmentMessage }}</p>
      </div>

      <div class="rounded-xl bg-white p-4 shadow">
        <div class="flex flex-col gap-3 sm:flex-row">
          <input v-model.trim="search" @keyup.enter="load(1)" placeholder="Search name, manufacturer, SKU or MPN" class="min-w-0 flex-1 rounded-lg border border-slate-300 px-4 py-2.5" />
          <select v-model="imageFilter" @change="load(1)" class="rounded-lg border border-slate-300 px-4 py-2.5">
            <option value="all">All image statuses</option>
            <option value="with_image">With image</option>
            <option value="no_image">No image</option>
          </select>
          <label class="flex items-center gap-2 text-sm text-slate-600"><input v-model="missingDescription" type="checkbox" @change="load(1)" /> Missing description</label>
          <button @click="load(1)" class="rounded-lg bg-[#2F5597] px-5 py-2.5 font-semibold text-white hover:bg-[#244579]">Search</button>
        </div>
      </div>

      <div class="overflow-hidden rounded-xl bg-white shadow">
        <div v-if="loading" class="p-10 text-center text-slate-500">Loading products…</div>
        <div v-else-if="products.length === 0" class="p-10 text-center text-slate-500">No products match this search.</div>
        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[1600px] text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4">Product</th><th class="px-4">Vendor</th><th class="px-4">Price</th><th class="px-4">Inventory</th><th class="px-4">Image</th><th class="px-4">Description / AI status</th><th class="px-4">Updated</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="product in products" :key="product.id">
                <td class="px-4"><p class="max-w-lg font-bold text-[#102a52]">{{ product.name }}</p><p class="text-xs text-slate-500">{{ product.manufacturer || 'Unknown' }} · MPN {{ product.mpn || '—' }} · SKU {{ product.sku || '—' }}</p></td>
                <td class="px-4 text-slate-700">{{ product.vendor || '—' }}</td>
                <td class="px-4 font-semibold">${{ Number(product.price || 0).toFixed(2) }}</td>
                <td class="px-4"><p>{{ formatNumber(product.quantity) }}</p><p :class="product.available ? 'text-emerald-600' : 'text-rose-600'" class="text-xs font-semibold">{{ product.available ? 'Available' : 'Unavailable' }}</p></td>
                <td class="px-4"><span :class="product.has_image ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800'" class="rounded-full px-2 py-1 text-xs font-bold">{{ product.has_image ? 'Ready' : 'Missing' }}</span></td>
                <td class="max-w-md px-4 py-3 text-sm">
                  <p v-if="product.description_repair_status === 'needs_review'" class="font-semibold text-amber-800">Needs manual review</p>
                  <p v-else :class="product.has_description && product.description_source !== 'ai-generated-from-title' ? 'text-emerald-700' : 'text-amber-800'" class="font-semibold">{{ product.description_source === 'ai-generated-from-title' ? 'AI title-based - review' : product.has_description ? 'Ready' : product.description_source === 'ai-generated-from-catalog-facts' ? 'Needs research' : 'Missing' }}</p>
                  <p v-if="product.description" class="line-clamp-2 text-slate-600">{{ product.description }}</p>
                  <p v-if="product.description_source" class="text-xs text-slate-500">{{ product.description_source }}</p>
                  <p v-if="product.description_repair_status" class="mt-1 text-xs" :class="product.description_repair_status === 'needs_review' ? 'font-semibold text-amber-800' : 'text-slate-500'">AI description review: {{ product.description_repair_status === 'needs_review' ? 'Needs manual review - previous text kept' : product.description_repair_status }}</p>
                  <p v-if="product.content_status" class="mt-1 text-xs text-slate-500">Last result: {{ !product.has_description && product.content_status === 'complete' ? 'needs_review' : product.content_status }}</p>
                  <p v-for="warning in product.content_warnings" :key="warning" class="mt-1 text-xs text-amber-800">{{ warning }}</p>
                  <button v-if="!product.has_description" :disabled="enriching" @click="enrichContent(product.id, 'description')" class="mt-2 mr-3 font-bold text-[#2F5597] disabled:opacity-40">Fill / retry description</button>
                  <button v-if="!product.has_image" :disabled="enriching" @click="enrichContent(product.id, 'images')" class="mt-2 font-bold text-[#2F5597] disabled:opacity-40">Find / retry image</button>
                </td>
                <td class="px-4 text-sm text-slate-500">{{ formatDate(product.updated_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="flex items-center justify-between border-t p-4 text-sm">
          <button :disabled="meta.current_page <= 1" @click="load(meta.current_page - 1)" class="font-bold disabled:opacity-40">Previous</button>
          <span>Page {{ meta.current_page }} of {{ meta.last_page }} · {{ formatNumber(meta.total) }} products</span>
          <button :disabled="meta.current_page >= meta.last_page" @click="load(meta.current_page + 1)" class="font-bold disabled:opacity-40">Next</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import AdminLayout from '../components/AdminLayout.vue'
import api from '../services/api'
import { useToastStore } from '../stores/toastStore'

const toast = useToastStore()
const loading = ref(false)
const enriching = ref(false)
const enrichmentMessage = ref('')
const search = ref(String(useRoute().query.search || ''))
const imageFilter = ref('all')
const missingDescription = ref(false)
const products = ref([])
const stats = reactive({ total: 0, available: 0, with_images: 0, without_images: 0, without_descriptions: 0 })
const meta = reactive({ current_page: 1, last_page: 1, total: 0 })
const statCards = computed(() => [
  { label: 'All products', value: stats.total },
  { label: 'Available', value: stats.available },
  { label: 'With images', value: stats.with_images },
  { label: 'No image', value: stats.without_images },
  { label: 'No description', value: stats.without_descriptions },
])
const formatNumber = value => Number(value || 0).toLocaleString('en-US')
const formatDate = value => value ? new Date(value).toLocaleString() : '—'

const load = async (page = 1) => {
  loading.value = true
  try {
    const response = await api.get('/admin/products', { params: { search: search.value, image_filter: imageFilter.value, missing_description: missingDescription.value ? 1 : 0, page } })
    products.value = response.data.data.data || []
    Object.assign(meta, response.data.data)
    Object.assign(stats, response.data.stats || {})
  } catch (error) {
    toast.addToast(error.response?.data?.message || 'Products could not be loaded.', 'error')
  } finally {
    loading.value = false
  }
}

const enrichContent = async (productId = null, mode = 'both') => {
  enriching.value = true
  enrichmentMessage.value = ''
  try {
    const path = productId ? `/admin/products/${productId}/enrich-content` : '/admin/products/enrich-content'
    const response = await api.post(path, { mode })
    enrichmentMessage.value = response.data.message
    toast.addToast(response.data.message, 'success')
  } catch (error) {
    toast.addToast(error.response?.data?.message || 'Product enrichment could not be queued.', 'error')
  } finally {
    enriching.value = false
  }
}

onMounted(() => load())
</script>
