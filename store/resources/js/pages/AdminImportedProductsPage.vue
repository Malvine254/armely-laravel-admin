<template>
  <AdminLayout>
    <template #title>Imported Products</template>
    <div class="space-y-4">
      <section class="rounded-xl bg-white p-4 shadow">
        <div class="flex flex-col gap-1">
          <h2 class="text-lg font-extrabold text-[#102a52]">Find products at TD SYNNEX</h2>
          <p class="text-sm text-slate-500">Search by product name, SKU, manufacturer part number, or supplier product ID.</p>
        </div>
        <form class="mt-4 flex flex-col gap-2 sm:flex-row" @submit.prevent="searchSupplier">
          <input v-model.trim="supplierQuery" class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2" placeholder="Example: Lenovo IdeaPad 5 or 21DJ0004US" />
          <button :disabled="supplierLoading || supplierQuery.length < 2" class="rounded-lg bg-[#2F5597] px-5 py-2 font-bold text-white disabled:cursor-not-allowed disabled:opacity-50">
            {{ supplierLoading ? 'Searching…' : 'Search supplier' }}
          </button>
        </form>
        <p v-if="supplierError" class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">{{ supplierError }}</p>
        <div v-if="supplierSearched && !supplierLoading && supplierResults.length === 0 && !supplierError" class="mt-4 rounded-lg bg-slate-50 p-5 text-center text-sm text-slate-500">No matching supplier products were found.</div>
        <div v-if="supplierResults.length" class="mt-4 overflow-x-auto rounded-lg border border-slate-200">
          <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Supplier product</th><th class="px-4 py-3">Identifiers</th><th class="px-4 py-3">Availability</th><th class="px-4 py-3 text-right">Action</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="result in supplierResults" :key="result.identifier">
                <td class="px-4 py-3"><p class="max-w-xl font-bold text-[#102a52]">{{ result.name }}</p><p class="text-xs text-slate-500">{{ result.manufacturer || 'Unknown manufacturer' }}</p></td>
                <td class="px-4 py-3 text-xs text-slate-600"><p>SKU {{ result.identifier }}</p><p>MPN {{ result.mpn || '—' }}</p></td>
                <td class="px-4 py-3"><p class="font-semibold">${{ Number(result.price).toFixed(2) }}</p><p :class="result.quantity > 0 && !result.discontinued ? 'text-emerald-700' : 'text-red-700'" class="text-xs font-bold">{{ result.discontinued ? 'Discontinued' : `${result.quantity} available` }}</p></td>
                <td class="px-4 py-3 text-right">
                  <span v-if="result.storefront_pinned" class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">Pinned</span>
                  <button v-else :disabled="importingIdentifier === result.identifier || result.discontinued || result.quantity < 1 || result.price <= 0" @click="importProduct(result)" class="rounded-lg bg-[#102a52] px-3 py-2 text-xs font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">
                    {{ importingIdentifier === result.identifier ? 'Importing…' : result.already_imported ? 'Pin to storefront' : 'Import to storefront' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <div class="grid gap-3 sm:grid-cols-3">
        <div v-for="card in statCards" :key="card.label" class="rounded-xl bg-white p-4 shadow">
          <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ card.label }}</p>
          <p class="mt-1 text-2xl font-extrabold text-[#102a52]">{{ card.value }}</p>
        </div>
      </div>

      <div class="rounded-xl bg-white p-4 shadow">
        <div class="grid gap-3 md:grid-cols-4">
          <input v-model.trim="filters.search" @keyup.enter="load(1)" placeholder="Search name, SKU, MPN or original query" class="rounded-lg border border-slate-300 px-3 py-2 md:col-span-2" />
          <select v-model="filters.status" @change="load(1)" class="rounded-lg border border-slate-300 px-3 py-2">
            <option value="">All review statuses</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option>
          </select>
          <label class="flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold"><input v-model="filters.missing_image" @change="load(1)" type="checkbox" /> Missing image only</label>
        </div>
      </div>

      <div class="overflow-hidden rounded-xl bg-white shadow">
        <div v-if="loading" class="p-10 text-center text-slate-500">Loading imported products…</div>
        <div v-else-if="products.length === 0" class="p-10 text-center text-slate-500">No imported products match these filters.</div>
        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[1500px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Imported from</th><th class="px-4 py-3">Inventory</th><th class="px-4 py-3">Image</th><th class="px-4 py-3">Storefront</th><th class="px-4 py-3">Review</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="product in products" :key="product.id">
                <td class="px-4 py-3"><p class="max-w-md font-bold text-[#102a52]">{{ product.name }}</p><p class="text-xs text-slate-500">{{ product.manufacturer || 'Unknown' }} · MPN {{ product.mpn || '—' }} · SKU {{ product.sku }}</p></td>
                <td class="px-4 py-3"><p class="max-w-xs text-slate-700">{{ product.query }}</p><p class="text-xs text-slate-400">{{ formatDate(product.imported_at) }}</p></td>
                <td class="px-4 py-3"><p>${{ Number(product.price).toFixed(2) }}</p><p class="text-xs text-slate-500">{{ product.quantity }} available</p></td>
                <td class="px-4 py-3"><span :class="product.has_image ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800'" class="rounded-full px-2 py-1 text-xs font-bold">{{ product.has_image ? 'Ready' : 'Missing' }}</span><button v-if="!product.has_image" @click="queueImage(product)" class="ml-2 text-xs font-bold text-[#2F5597]">Retry</button></td>
                <td class="px-4 py-3"><button :disabled="pinningId === product.id" @click="togglePin(product)" :class="product.storefront_pinned ? 'border-emerald-300 bg-emerald-50 text-emerald-700' : 'border-slate-300 text-slate-700'" class="rounded-lg border px-3 py-1.5 text-xs font-bold disabled:opacity-50">{{ product.storefront_pinned ? 'Pinned' : 'Pin product' }}</button></td>
                <td class="px-4 py-3"><select :value="product.status" @change="updateStatus(product, $event.target.value)" class="rounded-lg border border-slate-300 px-2 py-1"><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="meta.last_page > 1" class="flex justify-between border-t p-4 text-sm"><button :disabled="meta.current_page <= 1" @click="load(meta.current_page - 1)" class="font-bold disabled:opacity-40">Previous</button><span>Page {{ meta.current_page }} of {{ meta.last_page }}</span><button :disabled="meta.current_page >= meta.last_page" @click="load(meta.current_page + 1)" class="font-bold disabled:opacity-40">Next</button></div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import AdminLayout from '../components/AdminLayout.vue'
import api from '../services/api'
import { useToastStore } from '../stores/toastStore'

const toast = useToastStore()
const loading = ref(false)
const products = ref([])
const supplierQuery = ref('')
const supplierResults = ref([])
const supplierLoading = ref(false)
const supplierSearched = ref(false)
const supplierError = ref('')
const importingIdentifier = ref('')
const pinningId = ref(null)
const stats = reactive({ total: 0, pending: 0, missing_images: 0 })
const meta = reactive({ current_page: 1, last_page: 1 })
const filters = reactive({ search: '', status: '', missing_image: false })
const statCards = computed(() => [{ label: 'Imported', value: stats.total }, { label: 'Pending review', value: stats.pending }, { label: 'Missing images', value: stats.missing_images }])
const formatDate = (value) => value ? new Date(value).toLocaleString() : '—'

const load = async (page = 1) => {
  loading.value = true
  try {
    const response = await api.get('/admin/imported-products', { params: { ...filters, page } })
    products.value = response.data.data.data || []
    Object.assign(meta, response.data.data)
    Object.assign(stats, response.data.stats || {})
  } finally { loading.value = false }
}
const searchSupplier = async () => {
  if (supplierQuery.value.length < 2) return
  supplierLoading.value = true
  supplierSearched.value = true
  supplierError.value = ''
  try {
    const response = await api.get('/admin/imported-products/supplier-search', { params: { q: supplierQuery.value, limit: 25 } })
    supplierResults.value = response.data.data || []
  } catch (error) {
    supplierResults.value = []
    supplierError.value = error.response?.data?.message || 'Supplier search could not be completed.'
  } finally { supplierLoading.value = false }
}
const importProduct = async (result) => {
  importingIdentifier.value = result.identifier
  try {
    await api.post('/admin/imported-products/import', { identifier: result.identifier, search_query: supplierQuery.value })
    result.already_imported = true
    result.storefront_pinned = true
    toast.addToast('Product imported and pinned to the storefront.', 'success')
    await load(1)
  } catch (error) {
    toast.addToast(error.response?.data?.message || 'Product could not be imported.', 'error')
  } finally { importingIdentifier.value = '' }
}
const togglePin = async (product) => {
  pinningId.value = product.id
  try {
    await api.put(`/admin/imported-products/${product.id}/storefront-pin`, { pinned: !product.storefront_pinned })
    product.storefront_pinned = !product.storefront_pinned
    const result = supplierResults.value.find((item) => item.identifier === String(product.sku))
    if (result) result.storefront_pinned = product.storefront_pinned
    toast.addToast(product.storefront_pinned ? 'Product pinned to storefront.' : 'Storefront pin removed.', 'success')
  } finally { pinningId.value = null }
}
const updateStatus = async (product, status) => {
  await api.put(`/admin/imported-products/${product.id}`, { status })
  product.status = status
  toast.addToast('Review status updated.', 'success')
  await load(meta.current_page)
}
const queueImage = async (product) => {
  await api.post(`/admin/imported-products/${product.id}/enrich-image`)
  toast.addToast('Image lookup queued.', 'success')
}
onMounted(() => load())
</script>
