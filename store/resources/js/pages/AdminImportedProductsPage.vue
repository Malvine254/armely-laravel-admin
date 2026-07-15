<template>
  <AdminLayout>
    <template #title>Imported Products</template>
    <div class="space-y-4">
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
            <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Imported from</th><th class="px-4 py-3">Inventory</th><th class="px-4 py-3">Image</th><th class="px-4 py-3">Review</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="product in products" :key="product.id">
                <td class="px-4 py-3"><p class="max-w-md font-bold text-[#102a52]">{{ product.name }}</p><p class="text-xs text-slate-500">{{ product.manufacturer || 'Unknown' }} · MPN {{ product.mpn || '—' }} · SKU {{ product.sku }}</p></td>
                <td class="px-4 py-3"><p class="max-w-xs text-slate-700">{{ product.query }}</p><p class="text-xs text-slate-400">{{ formatDate(product.imported_at) }}</p></td>
                <td class="px-4 py-3"><p>${{ Number(product.price).toFixed(2) }}</p><p class="text-xs text-slate-500">{{ product.quantity }} available</p></td>
                <td class="px-4 py-3"><span :class="product.has_image ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800'" class="rounded-full px-2 py-1 text-xs font-bold">{{ product.has_image ? 'Ready' : 'Missing' }}</span><button v-if="!product.has_image" @click="queueImage(product)" class="ml-2 text-xs font-bold text-[#2F5597]">Retry</button></td>
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
