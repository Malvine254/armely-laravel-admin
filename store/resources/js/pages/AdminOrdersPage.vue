<template>
  <AdminLayout>
    <template #title>All Orders</template>

    <div class="admin-fit-page">

    <!-- Company Filter Banner -->
    <div v-if="companyId" class="rounded-lg bg-[#2F5597]/5 border border-[#2F5597]/20 p-4 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <i class="fas fa-filter text-[#2F5597] text-xl mr-3"></i>
          <div>
            <p class="text-sm text-gray-500">Showing orders for</p>
            <p class="font-semibold text-gray-900">{{ companyName || 'Selected Company' }}</p>
          </div>
        </div>
        <button
          @click="clearCompanyFilter"
          class="px-4 py-2 border border-[#2F5597]/30 text-[#2F5597] rounded-lg hover:bg-[#2F5597]/10 transition font-medium text-sm"
        >
          <i class="fas fa-times mr-2"></i>Clear Filter
        </button>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="rounded-xl border-0 shadow-lg bg-white p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search Order</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Order ID or customer name..."
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 placeholder-slate-500 transition"
           
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
          <select v-model="statusFilter" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 transition">
            <option value="" class="bg-white">All Status</option>
            <option value="pending" class="bg-white">Pending</option>
            <option value="processing" class="bg-white">Processing</option>
            <option value="confirmed" class="bg-white">Confirmed</option>
            <option value="shipped" class="bg-white">Shipped</option>
            <option value="delivered" class="bg-white">Delivered</option>
            <option value="cancelled" class="bg-white">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
          <select v-model="dateRange" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 transition">
            <option value="all" class="bg-white">All Time</option>
            <option value="today" class="bg-white">Today</option>
            <option value="week" class="bg-white">This Week</option>
            <option value="month" class="bg-white">This Month</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium py-2.5 px-4 rounded-lg transition shadow-lg"
          >
            <i class="fas fa-search mr-2"></i>Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Orders List -->
    <div class="admin-table-card rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <!-- Bulk Actions Bar -->
      <div v-if="selectedOrders.length > 0" class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <span class="text-sm font-medium text-[#2F5597]"><i class="fas fa-check-square mr-2"></i>{{ selectedOrders.length }} order(s) selected</span>
        <button
          @click="confirmBulkDelete"
          class="px-4 py-2 text-xs font-semibold rounded-lg border border-rose-500/50 text-rose-600 hover:bg-rose-500/20 transition"
        >
          <i class="fas fa-trash mr-1"></i>Delete Selected
        </button>
      </div>

      <div class="overflow-x-auto admin-table-scroll">
        <table class="w-full">
          <thead class="border-b border-gray-200">
            <tr>
              <th class="px-4 py-4 text-left">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded cursor-pointer" style="accent-color: #2F5597;"
                />
              </th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Order ID</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Status</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Customer</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Company</th>
              <th class="px-6 py-4 text-right font-semibold text-gray-700 uppercase text-xs tracking-wide">Amount</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Date</th>
              <th class="px-6 py-4 text-center font-semibold text-gray-700 uppercase text-xs tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="orders.length === 0" class="border-b border-gray-200">
              <td colspan="8" class="px-6 py-16 text-center">
                <i class="fas fa-inbox text-5xl mb-4 block opacity-20 text-gray-500"></i>
                <p class="text-gray-500 text-lg font-medium">No orders found</p>
              </td>
            </tr>
            <tr v-for="order in orders" :key="order.id" class="border-b border-gray-200 hover:bg-gray-50 transition">
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :value="order.id"
                  v-model="selectedOrders"
                  class="w-4 h-4 rounded cursor-pointer" style="accent-color: #2F5597;"
                />
              </td>
              <td class="px-6 py-4">
                <span class="font-medium text-[#2F5597] font-mono">{{ order.order_number }}</span>
              </td>
              <td class="px-6 py-4">
                <ul class="space-y-1 text-xs">
                  <li class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500 shrink-0"></span>
                    <span class="text-gray-500 shrink-0">Local:</span>
                    <span :class="['px-2 py-0.5 rounded-full font-semibold', statusClass(order.status)]">{{ order.status }}</span>
                  </li>
                  <li class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#2F5597] shrink-0"></span>
                    <span class="text-gray-500 shrink-0">TD:</span>
                    <span v-if="getTdStatusLabel(order)" class="px-2 py-0.5 rounded-full font-semibold bg-[#2F5597]/20 text-[#2F5597]">{{ getTdStatusLabel(order) }}</span>
                    <span v-else-if="tdStatusLoading[order.order_number]" class="text-gray-500 italic"><i class="fas fa-spinner fa-spin mr-0.5"></i>checking…</span>
                    <span v-else class="text-gray-400">—</span>
                  </li>
                </ul>
              </td>
              <td class="px-6 py-4">
                <p class="font-medium text-gray-900">{{ getUserName(order) }}</p>
                <p class="text-xs text-gray-500">{{ getUserEmail(order) }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="text-gray-900">{{ getCompanyName(order) }}</p>
              </td>
              <td class="px-6 py-4 text-right">
                <p class="font-bold text-gray-900">${{ formatCurrency(order.total_amount) }}</p>
                <p class="text-xs text-gray-500">Tax: ${{ formatCurrency(order.tax_amount) }}</p>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center gap-2">
                  <button
                    @click="viewOrderDetails(order)"
                    class="px-3 py-1.5 bg-[#2F5597]/10 hover:bg-[#2F5597]/20 text-[#2F5597] font-medium rounded-lg transition border border-[#2F5597]/30 text-xs"
                  >
                    <i class="fas fa-eye mr-1"></i>View
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="admin-table-pagination px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="text-sm text-gray-500">
          <p>Showing {{ orders.length }} of {{ totalOrders }} orders</p>
          <p class="mt-1 text-xs text-gray-500">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchOrders()"
            class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#2F5597] transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
          >
            Previous
          </button>
          <button
            v-for="page in pageNumbers"
            :key="page"
            @click="currentPage = page; fetchOrders()"
            :class="[
              'px-3 py-2 rounded-lg border text-sm font-semibold transition',
              page === currentPage
                ? 'bg-gradient-to-r from-[#2F5597] to-[#1e3a6b] text-white border-[#2F5597]'
                : 'border-gray-200 text-gray-500 hover:border-[#2F5597]/50 hover:text-[#2F5597]'
            ]"
          >
            {{ page }}
          </button>
          <button
            :disabled="currentPage >= lastPage"
            @click="currentPage++; fetchOrders()"
            class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#2F5597] transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    </div>

    <!-- Order Details Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click="selectedOrder = null">
      <div class="rounded-2xl bg-white shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto border-0" @click.stop>
        <div class="sticky top-0 text-white p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-xs uppercase tracking-wide text-white/70 font-semibold">Order Details</p>
              <h3 class="text-xl font-bold text-white mt-1">{{ selectedOrder.order_number }}</h3>
            </div>
            <button @click="selectedOrder = null" class="text-white/60 hover:text-white transition text-2xl">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-6">
          <!-- Status and Timeline -->
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3">Order Status</p>
            <div class="flex items-center space-x-4">
              <span :class="['px-4 py-2 rounded-full text-sm font-semibold', statusClass(selectedOrder.status)]">
                {{ selectedOrder.status.toUpperCase() }}
              </span>
              <p class="text-sm text-gray-500">
                Last updated: {{ formatDate(selectedOrder.updated_at) }}
              </p>
            </div>
          </div>

          <hr class="border-gray-200" />

          <!-- Order Information -->
          <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <p class="text-sm text-gray-500 font-medium">Customer</p>
              <p class="text-lg font-semibold text-gray-900">{{ getUserName(selectedOrder) }}</p>
              <p class="text-sm text-gray-500">{{ getUserEmail(selectedOrder) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Company</p>
              <p class="text-lg font-semibold text-gray-900">{{ getCompanyName(selectedOrder) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Order Date</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedOrder.created_at) }}</p>
            </div>
          </div>

          <hr class="border-gray-200" />

          <!-- Order Items -->
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3">Order Items</p>
            <div class="rounded-lg overflow-hidden border border-gray-200">
              <table class="w-full text-sm">
                <thead>
                  <tr>
                    <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase text-xs tracking-wide">Description</th>
                    <th class="px-4 py-2 text-center font-semibold text-gray-500 uppercase text-xs tracking-wide">Qty</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-500 uppercase text-xs tracking-wide">Unit Price</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-500 uppercase text-xs tracking-wide">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in selectedOrder.items" :key="index" class="border-t border-gray-200 hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-700">{{ item.name || 'Item ' + (index + 1) }}</td>
                    <td class="px-4 py-2 text-center text-gray-500">{{ item.quantity }}</td>
                    <td class="px-4 py-2 text-right text-gray-500">${{ formatCurrency(item.price) }}</td>
                    <td class="px-4 py-2 text-right font-semibold text-[#2F5597]">${{ formatCurrency(item.price * item.quantity) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <hr class="border-gray-200" />

          <!-- Order Summary -->
          <div class="rounded-lg p-4 border border-gray-200">
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-500">Subtotal:</span>
                <span class="font-medium text-gray-700">${{ formatCurrency(calculateSubtotal(selectedOrder)) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Tax:</span>
                <span class="font-medium text-gray-700">${{ formatCurrency(selectedOrder.tax_amount) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">Shipping:</span>
                <span class="font-medium text-gray-700">${{ formatCurrency(selectedOrder.shipping_amount || 0) }}</span>
              </div>
              <div class="border-t border-gray-200 pt-2 flex justify-between">
                <span class="font-semibold text-gray-700">Total:</span>
                <span class="font-bold text-lg text-[#2F5597]">${{ formatCurrency(selectedOrder.total_amount) }}</span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3 justify-end border-t border-gray-200 pt-4">
            <button
              @click="selectedOrder = null"
              class="px-6 py-2 border border-[#2F5597]/50 rounded-lg text-[#2F5597] font-medium hover:bg-[#2F5597]/20 transition text-sm"
            >
              Close
            </button>
            <button
              v-if="selectedOrder.status !== 'cancelled' && canCancelOrder(selectedOrder)"
              @click="cancelOrder"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-rose-600/30 hover:bg-rose-600/40 text-rose-600 font-medium rounded-lg transition border border-rose-500/50 text-sm disabled:opacity-50"
            >
              <i class="fas fa-ban mr-2"></i>Cancel Order
            </button>
            <button
              @click="downloadInvoice"
              class="px-6 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition text-sm"
            >
              <i class="fas fa-file-pdf mr-2"></i>Download Invoice
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click="showDeleteConfirm = false">
      <div class="rounded-2xl bg-white shadow-2xl max-w-md w-full border-0" @click.stop>
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4" style="background: rgba(239, 68, 68, 0.1);">
              <i class="fas fa-trash text-rose-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Delete {{ selectedOrders.length }} Order(s)?</h3>
          </div>
          <p class="text-gray-500 mb-6">Are you sure you want to permanently delete {{ selectedOrders.length }} order(s)? This action cannot be undone.</p>
          <div class="flex justify-end gap-3">
            <button
              @click="showDeleteConfirm = false"
              class="px-4 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 transition text-sm font-medium"
            >
              Cancel
            </button>
            <button
              @click="executeBulkDelete"
              :disabled="isSubmitting"
              class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg transition disabled:opacity-50 text-sm"
            >
              <i class="fas fa-trash mr-2"></i>Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()

const orders = ref([])
const selectedOrder = ref(null)
const selectedOrders = ref([])
const searchQuery = ref('')
const statusFilter = ref('')
const dateRange = ref('all')
const currentPage = ref(1)
const totalOrders = ref(0)
const lastPage = ref(1)
const isSubmitting = ref(false)
const showDeleteConfirm = ref(false)
const companyId = ref(route.query.company_id || null)
const companyName = ref(route.query.company_name || null)
const tdStatusByOrder = ref({})
const tdStatusLoading = ref({})
const tdApiStatusByOrder = ref({})
const tdStatusSourceByOrder = ref({})
const tdStatusDetailsByOrder = ref({})

const allSelected = computed(() => {
  return orders.value.length > 0 && orders.value.every(order => selectedOrders.value.includes(order.id))
})

const pageNumbers = computed(() => {
  const total = lastPage.value
  const current = currentPage.value
  const windowSize = 5
  const half = Math.floor(windowSize / 2)
  let start = Math.max(1, current - half)
  let end = Math.min(total, start + windowSize - 1)

  if (end - start + 1 < windowSize) {
    start = Math.max(1, end - windowSize + 1)
  }

  const pages = []
  for (let page = start; page <= end; page += 1) {
    pages.push(page)
  }
  return pages
})

const calculateSubtotal = (order) => {
  if (!order) return 0
  
  // If items exist and is an array, calculate from items
  if (order.items && Array.isArray(order.items) && order.items.length > 0) {
    return order.items.reduce((sum, item) => {
      return sum + (parseFloat(item.price || 0) * parseInt(item.quantity || 0))
    }, 0)
  }
  
  // Fallback: subtract tax and shipping from total
  return (order.total_amount || 0) - (order.tax_amount || 0) - (order.shipping_amount || 0)
}

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const statusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'confirmed': 'bg-indigo-100 text-indigo-800',
    'shipped': 'bg-purple-100 text-purple-800',
    'delivered': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getUserName = (row) => {
  return row?.user?.name || 'Unknown user'
}

const getUserEmail = (row) => {
  return row?.user?.email || 'No email'
}

const getCompanyName = (row) => {
  return row?.user?.company?.name || row?.company?.name || 'Unknown company'
}

const parseTrackingInfo = (order) => {
  const trackingInfo = order?.tracking_info
  if (!trackingInfo) return {}

  if (typeof trackingInfo === 'string') {
    try {
      return JSON.parse(trackingInfo)
    } catch {
      return { tracking_number: trackingInfo }
    }
  }

  if (typeof trackingInfo === 'object') {
    return trackingInfo
  }

  return {}
}

const getTrackingNumber = (order) => {
  const normalized = tdStatusDetailsByOrder.value[order?.order_number] || null
  if (normalized?.tracking_number) {
    return String(normalized.tracking_number)
  }

  const apiStatus = tdApiStatusByOrder.value[order?.order_number] || null
  const apiTracking = apiStatus?.tracking_number
    || apiStatus?.trackingNumber
    || apiStatus?.carrierTrackingNumber
    || apiStatus?.shipmentTrackingNumber
    || apiStatus?.shipment?.trackingNumber
    || apiStatus?.shipments?.[0]?.trackingNumber

  if (apiTracking) {
    return String(apiTracking)
  }

  const localTracking = parseTrackingInfo(order)?.tracking_number || parseTrackingInfo(order)?.trackingNumber
  if (localTracking) {
    return String(localTracking)
  }

  return 'Unavailable'
}

const getShippingStatusLabel = (order) => {
  const normalized = tdStatusDetailsByOrder.value[order?.order_number] || null
  if (normalized?.shipping_status) {
    return formatStatusLabel(normalized.shipping_status)
  }

  const apiStatus = tdApiStatusByOrder.value[order?.order_number] || null
  const apiShippingStatus = apiStatus?.shippingStatus
    || apiStatus?.shipping_status
    || apiStatus?.shipmentStatus
    || apiStatus?.deliveryStatus
    || apiStatus?.status
    || apiStatus?.shipments?.[0]?.status

  if (apiShippingStatus) {
    return formatStatusLabel(String(apiShippingStatus))
  }

  return formatStatusLabel(order?.status || 'Unavailable')
}

const getFreightAmount = (order) => {
  const normalized = tdStatusDetailsByOrder.value[order?.order_number] || null
  const raw = normalized?.freight_amount ?? order?.shipping_amount
  if (raw === null || raw === undefined || raw === '') {
    return '$0.00'
  }

  const value = Number(raw)
  if (!Number.isFinite(value)) {
    return '$0.00'
  }

  return `$${formatCurrency(value)}`
}

const shippingStatusClass = (order) => {
  const status = String(getShippingStatusLabel(order) || '').toLowerCase()

  if (status.includes('deliver')) return 'bg-green-100 text-green-700'
  if (status.includes('ship') || status.includes('transit')) return 'bg-blue-100 text-blue-700'
  if (status.includes('cancel') || status.includes('failed')) return 'bg-red-100 text-red-700'
  return 'bg-yellow-100 text-yellow-700'
}

const getTdStatusLabel = (order) => {
  const orderNumber = order?.order_number
  if (!orderNumber) return ''

  if (tdStatusByOrder.value[orderNumber]) {
    return tdStatusByOrder.value[orderNumber]
  }

  if (isLocalOrder(orderNumber)) {
    return formatStatusLabel(order?.status || 'pending')
  }

  return ''
}

const getTdStatusSource = (order) => {
  const orderNumber = order?.order_number
  if (!orderNumber) return ''

  if (tdStatusSourceByOrder.value[orderNumber]) {
    return tdStatusSourceByOrder.value[orderNumber]
  }

  if (isLocalOrder(orderNumber)) {
    return 'local-order'
  }

  return ''
}

const formatStatusLabel = (value) => {
  return String(value || 'Unknown')
    .replace(/[_-]+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
    .replace(/\b\w/g, (c) => c.toUpperCase())
}

const canCancelOrder = (order) => {
  const sevenDaysAgo = new Date()
  sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7)
  const orderDate = new Date(order.created_at)
  return orderDate > sevenDaysAgo && ['pending', 'processing', 'confirmed'].includes(order.status)
}

const fetchOrders = async () => {
  try {
    const params = {
      page: currentPage.value,
      pageSize: 10
    }

    if (companyId.value) params.company_id = companyId.value
    if (statusFilter.value) params.status = statusFilter.value
    if (searchQuery.value) params.search = searchQuery.value
    if (dateRange.value) params.dateRange = dateRange.value

    const response = await api.get('/admin/orders', { params })

    if (response.data.success) {
      orders.value = response.data.data
      totalOrders.value = response.data.pagination.total
      lastPage.value = response.data.pagination.last_page

      const retainedStatuses = {}
      const retainedLoading = {}
      const retainedApiStatuses = {}
      const retainedStatusSources = {}
      const retainedStatusDetails = {}
      for (const order of orders.value) {
        if (tdStatusByOrder.value[order.order_number]) {
          retainedStatuses[order.order_number] = tdStatusByOrder.value[order.order_number]
        }
        if (tdStatusLoading.value[order.order_number]) {
          retainedLoading[order.order_number] = tdStatusLoading.value[order.order_number]
        }
        if (tdApiStatusByOrder.value[order.order_number]) {
          retainedApiStatuses[order.order_number] = tdApiStatusByOrder.value[order.order_number]
        }
        if (tdStatusSourceByOrder.value[order.order_number]) {
          retainedStatusSources[order.order_number] = tdStatusSourceByOrder.value[order.order_number]
        }
        if (tdStatusDetailsByOrder.value[order.order_number]) {
          retainedStatusDetails[order.order_number] = tdStatusDetailsByOrder.value[order.order_number]
        }
      }
      tdStatusByOrder.value = retainedStatuses
      tdStatusLoading.value = retainedLoading
      tdApiStatusByOrder.value = retainedApiStatuses
      tdStatusSourceByOrder.value = retainedStatusSources
      tdStatusDetailsByOrder.value = retainedStatusDetails

      await fetchVisibleOrderStatusesFromApi()
    }
  } catch (error) {
    console.error('Failed to fetch orders:', error)
  }
}

const fetchVisibleOrderStatusesFromApi = async () => {
  const candidates = orders.value.filter((order) => !isLocalOrder(order.order_number))
  await Promise.all(candidates.map((order) => trackOrderStatus(order, true)))
}

const trackOrderStatus = async (order, silent = false) => {
  if (!order?.order_number) return

  tdStatusLoading.value = {
    ...tdStatusLoading.value,
    [order.order_number]: true
  }

  try {
    const statusResponse = await api.get(`/admin/orders/${order.order_number}/status`)
    if (statusResponse.data.success) {
      const payload = statusResponse.data.data || {}
      const rawApiStatus = payload?.td_synnex_status
      const normalizedStatus = formatStatusLabel(payload?.normalized_status || getStatusDisplay(rawApiStatus))
      const sourceLabel = payload?.status_source || 'api'

      tdStatusDetailsByOrder.value = {
        ...tdStatusDetailsByOrder.value,
        [order.order_number]: {
          normalized_status: payload?.normalized_status || null,
          raw_status: payload?.raw_status || null,
          shipping_status: payload?.shipping_status || null,
          tracking_number: payload?.tracking_number || null,
          freight_amount: payload?.freight_amount || null,
          estimated_delivery_date: payload?.estimated_delivery_date || null,
        }
      }

      tdApiStatusByOrder.value = {
        ...tdApiStatusByOrder.value,
        [order.order_number]: rawApiStatus || {}
      }

      tdStatusSourceByOrder.value = {
        ...tdStatusSourceByOrder.value,
        [order.order_number]: sourceLabel
      }

      tdStatusByOrder.value = {
        ...tdStatusByOrder.value,
        [order.order_number]: normalizedStatus
      }
    }
  } catch (err) {
    if (!silent) {
      console.warn(`Failed to fetch status for order ${order.order_number}:`, err.message)
    }
    tdStatusByOrder.value = {
      ...tdStatusByOrder.value,
      [order.order_number]: 'Status Unavailable'
    }
  } finally {
    tdStatusLoading.value = {
      ...tdStatusLoading.value,
      [order.order_number]: false
    }
  }
}

const isLocalOrder = (orderNumber) => {
  return orderNumber && orderNumber.startsWith('ORD-')
}

const getStatusDisplay = (statusData) => {
  if (!statusData) return 'Unavailable'
  
  // Handle error responses
  if (statusData.error || statusData.success === false) {
    return 'Error'
  }
  
  // Handle different response formats from TD SYNNEX
  if (typeof statusData === 'string') {
    return formatStatusLabel(statusData)
  }
  
  if (typeof statusData === 'object') {
    // Check for error in response
    if (statusData.BizError || statusData.bizError) {
      return 'API Error'
    }
    
    // Try common field names for status
    const status = statusData.status || 
                   statusData.poStatus || 
                   statusData.orderStatus || 
                   statusData.Status || 
                   statusData.POStatus ||
                   statusData.OrderStatus ||
                   statusData.shippingStatus ||
                   statusData.deliveryStatus ||
                   statusData?.shipment?.status ||
                   statusData?.shipments?.[0]?.status
    
    if (status) return formatStatusLabel(status)
    
    // If response is empty or unknown format
    if (Object.keys(statusData).length === 0 || Array.isArray(statusData) && statusData.length === 0) {
      return 'Unavailable'
    }
  }
  
  return 'Unavailable'
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order

  if (!tdStatusByOrder.value[order.order_number]) {
    trackOrderStatus(order)
  }
}

const cancelOrder = async () => {
  if (!selectedOrder.value) return

  const reason = prompt('Please provide a reason for cancellation:')
  if (!reason) return

  isSubmitting.value = true
  try {
    const response = await api.post(`/admin/orders/${selectedOrder.value.order_number}/cancel`, {
      reason: reason
    })

    if (response.data.success) {
      alert('Order cancelled successfully!')
      selectedOrder.value = null
      fetchOrders()
    }
  } catch (error) {
    console.error('Failed to cancel order:', error)
    alert('Failed to cancel order: ' + (error.response?.data?.message || error.message))
  } finally {
    isSubmitting.value = false
  }
}

const downloadInvoice = async () => {
  if (!selectedOrder.value) return

  try {
    const invoiceNumber = selectedOrder.value.linked_invoice_number || selectedOrder.value.order_number
    const response = await api.get(`/invoices/${invoiceNumber}/pdf`, {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `invoice-${invoiceNumber}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.parentNode.removeChild(link)
  } catch (error) {
    console.error('Failed to download invoice:', error)
    alert('Failed to download invoice')
  }
}

const applyFilters = () => {
  currentPage.value = 1
  fetchOrders()
}

const clearCompanyFilter = () => {
  companyId.value = null
  companyName.value = null
  router.push({ name: 'admin-orders' })
  fetchOrders()
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedOrders.value = []
  } else {
    selectedOrders.value = orders.value.map(order => order.id)
  }
}

const confirmBulkDelete = () => {
  showDeleteConfirm.value = true
}

const executeBulkDelete = async () => {
  if (selectedOrders.value.length === 0) return

  isSubmitting.value = true
  try {
    const response = await api.post('/admin/orders/bulk-delete', {
      order_ids: selectedOrders.value
    })

    if (response.data.success) {
      alert(response.data.message)
      selectedOrders.value = []
      showDeleteConfirm.value = false
      fetchOrders()
    }
  } catch (error) {
    console.error('Failed to delete orders:', error)
    alert(error.response?.data?.message || 'Failed to delete orders')
  } finally {
    isSubmitting.value = false
  }
}

watch(() => route.query.company_id, (newCompanyId) => {
  companyId.value = newCompanyId || null
  companyName.value = route.query.company_name || null
  currentPage.value = 1
  fetchOrders()
})

onMounted(() => {
  fetchOrders()
})
</script>

<style scoped>
.admin-fit-page {
  block-size: calc(100vh - 170px);
  min-block-size: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.admin-fit-page > :not(.admin-table-card) {
  flex-shrink: 0;
}

.admin-table-card {
  min-block-size: 0;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.admin-table-scroll {
  min-block-size: 0;
  flex: 1;
  overflow: auto;
}

.admin-table-pagination {
  position: sticky;
  inset-block-end: 0;
  z-index: 5;
  background: #ffffff;
  flex-shrink: 0;
}

@media (max-width: 1023px) {
  .admin-fit-page {
    block-size: auto;
    min-block-size: calc(100vh - 170px);
  }

  .admin-table-scroll {
    max-block-size: 60vh;
  }
}
</style>
