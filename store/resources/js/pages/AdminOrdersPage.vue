<template>
  <AdminLayout>
    <template #title>All Orders</template>

    <!-- Company Filter Banner -->
    <div v-if="companyId" class="bg-[#edf3fb] border-l-4 border-[#2f5597] rounded-lg shadow p-4 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <i class="fas fa-filter text-[#2f5597] text-xl mr-3"></i>
          <div>
            <p class="text-sm text-gray-600">Showing orders for</p>
            <p class="font-semibold text-gray-900">{{ companyName || 'Selected Company' }}</p>
          </div>
        </div>
        <button
          @click="clearCompanyFilter"
          class="px-4 py-2 bg-white border border-[#2f5597] text-[#2f5597] rounded-lg hover:bg-[#2f5597] hover:text-white transition font-medium text-sm"
        >
          <i class="fas fa-times mr-2"></i>Clear Filter
        </button>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search Order</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Order ID or customer name..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
          <select v-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="confirmed">Confirmed</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
          <select v-model="dateRange" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]">
            <option value="all">All Time</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-[#2f5597] hover:bg-[#274a82] text-white font-medium py-2 px-4 rounded-lg transition"
          >
            <i class="fas fa-search mr-2"></i>Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Bulk Actions Bar -->
      <div v-if="selectedOrders.length > 0" class="px-6 py-3 bg-[#edf3fb] border-b border-gray-200 flex items-center justify-between">
        <span class="text-sm font-medium text-gray-700">{{ selectedOrders.length }} order(s) selected</span>
        <button
          @click="confirmBulkDelete"
          class="px-4 py-2 text-xs font-semibold rounded-lg border border-red-600 text-red-600 hover:bg-red-50 transition"
        >
          <i class="fas fa-trash mr-1"></i>Delete Selected
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-4 text-left">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 text-[#2f5597] border-gray-300 rounded focus:ring-[#2f5597]"
                />
              </th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Order ID</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">TD SYNNEX Status</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Customer</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Company</th>
              <th class="px-6 py-4 text-right font-semibold text-gray-700">Amount</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Local Status</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Order Date</th>
              <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="orders.length === 0" class="border-b border-gray-200 hover:bg-gray-50">
              <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                <p>No orders found</p>
              </td>
            </tr>
            <tr v-for="order in orders" :key="order.id" class="border-b border-gray-200 hover:bg-gray-50 transition">
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :value="order.id"
                  v-model="selectedOrders"
                  class="w-4 h-4 text-[#2f5597] border-gray-300 rounded focus:ring-[#2f5597]"
                />
              </td>
              <td class="px-6 py-4">
                <span class="font-medium text-[#2f5597]">{{ order.order_number }}</span>
              </td>
              <td class="px-6 py-4">
                <span v-if="isLocalOrder(order.order_number)" class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                  <i class="fas fa-database mr-1"></i>Local Order
                </span>
                <span v-else-if="order.td_synnex_status" class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                  <i class="fas fa-check-circle mr-1"></i>{{ getStatusDisplay(order.td_synnex_status) }}
                </span>
                <span v-else class="text-gray-500 text-sm">
                  <i class="fas fa-spinner fa-spin mr-1"></i>Checking...
                </span>
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
              <td class="px-6 py-4">
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold text-white', statusClass(order.status)]">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center space-x-3">
                  <button
                    @click="viewOrderDetails(order)"
                    class="text-[#2f5597] hover:text-[#274a82] font-medium"
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
      <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="text-sm text-gray-600">
          <p>Showing {{ orders.length }} of {{ totalOrders }} orders</p>
          <p class="mt-1">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchOrders()"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
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
                ? 'bg-[#2f5597] text-white border-[#2f5597]'
                : 'border-gray-300 text-gray-700 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          <button
            :disabled="currentPage >= lastPage"
            @click="currentPage++; fetchOrders()"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 text-white p-6 border-b" style="background: linear-gradient(90deg, #2f5597, #1f4788);">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold">Order Details: {{ selectedOrder.order_number }}</h3>
            <button @click="selectedOrder = null" class="text-white hover:text-gray-200">
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-6">
          <!-- Status and Timeline -->
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3">Order Status</p>
            <div class="flex items-center space-x-4">
              <span :class="['px-4 py-2 rounded-full text-sm font-semibold text-white', statusClass(selectedOrder.status)]">
                {{ selectedOrder.status.toUpperCase() }}
              </span>
              <p class="text-sm text-gray-600">
                Last updated: {{ formatDate(selectedOrder.updated_at) }}
              </p>
            </div>
          </div>

          <hr />

          <!-- Order Information -->
          <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <p class="text-sm text-gray-500 font-medium">Customer</p>
              <p class="text-lg font-semibold text-gray-900">{{ getUserName(selectedOrder) }}</p>
              <p class="text-sm text-gray-600">{{ getUserEmail(selectedOrder) }}</p>
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

          <hr />

          <!-- Order Items -->
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3">Order Items</p>
            <div class="bg-gray-50 rounded-lg overflow-hidden">
              <table class="w-full text-sm">
                <thead class="bg-gray-200">
                  <tr>
                    <th class="px-4 py-2 text-left font-semibold">Description</th>
                    <th class="px-4 py-2 text-center font-semibold">Qty</th>
                    <th class="px-4 py-2 text-right font-semibold">Unit Price</th>
                    <th class="px-4 py-2 text-right font-semibold">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in selectedOrder.items" :key="index" class="border-t border-gray-200">
                    <td class="px-4 py-2">{{ item.name || 'Item ' + (index + 1) }}</td>
                    <td class="px-4 py-2 text-center">{{ item.quantity }}</td>
                    <td class="px-4 py-2 text-right">${{ formatCurrency(item.price) }}</td>
                    <td class="px-4 py-2 text-right font-semibold">${{ formatCurrency(item.price * item.quantity) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <hr />

          <!-- Order Summary -->
          <div class="bg-[#edf3fb] rounded-lg p-4">
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-700">Subtotal:</span>
                <span class="font-medium">${{ formatCurrency(calculateSubtotal(selectedOrder)) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-700">Tax:</span>
                <span class="font-medium">${{ formatCurrency(selectedOrder.tax_amount) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-700">Shipping:</span>
                <span class="font-medium">${{ formatCurrency(selectedOrder.shipping_amount || 0) }}</span>
              </div>
              <div class="border-t pt-2 flex justify-between">
                <span class="font-semibold">Total:</span>
                <span class="font-bold text-lg text-[#2f5597]">${{ formatCurrency(selectedOrder.total_amount) }}</span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3 justify-end border-t pt-4">
            <button
              @click="selectedOrder = null"
              class="px-6 py-2 border border-[#2f5597] rounded-lg text-[#2f5597] font-medium hover:bg-[#edf3fb] transition"
            >
              Close
            </button>
            <button
              v-if="selectedOrder.status !== 'cancelled' && canCancelOrder(selectedOrder)"
              @click="cancelOrder"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition disabled:opacity-50"
            >
              <i class="fas fa-ban mr-2"></i>Cancel Order
            </button>
            <button
              @click="downloadInvoice"
              class="px-6 py-2 bg-[#2f5597] hover:bg-[#274a82] text-white font-medium rounded-lg transition"
            >
              <i class="fas fa-file-pdf mr-2"></i>Download Invoice
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">
              <i class="fas fa-trash text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Delete {{ selectedOrders.length }} Order(s)?</h3>
          </div>
          <p class="text-gray-600 mb-6">Are you sure you want to permanently delete {{ selectedOrders.length }} order(s)? This action cannot be undone.</p>
          <div class="flex justify-end gap-3">
            <button
              @click="showDeleteConfirm = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
            >
              Cancel
            </button>
            <button
              @click="executeBulkDelete"
              :disabled="isSubmitting"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition disabled:opacity-50"
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
    'pending': 'bg-yellow-500',
    'processing': 'bg-blue-500',
    'confirmed': 'bg-indigo-500',
    'shipped': 'bg-purple-500',
    'delivered': 'bg-green-500',
    'cancelled': 'bg-red-500'
  }
  return classes[status] || 'bg-gray-500'
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
      
      // Fetch TD SYNNEX status for each order
      await fetchAllOrderStatuses(orders.value)
    }
  } catch (error) {
    console.error('Failed to fetch orders:', error)
  }
}

const fetchAllOrderStatuses = async (ordersData) => {
  try {
    for (const order of ordersData) {
      // Skip local orders (they don't exist in TD SYNNEX)
      if (isLocalOrder(order.order_number)) {
        continue
      }
      
      try {
        const statusResponse = await api.get(`/admin/orders/${order.order_number}/status`)
        if (statusResponse.data.success) {
          order.td_synnex_status = statusResponse.data.data.td_synnex_status
        }
      } catch (err) {
        console.warn(`Failed to fetch status for order ${order.order_number}:`, err.message)
      }
    }
  } catch (error) {
    console.error('Error fetching order statuses:', error)
  }
}

const isLocalOrder = (orderNumber) => {
  return orderNumber && orderNumber.startsWith('ORD-')
}

const getStatusDisplay = (statusData) => {
  if (!statusData) return 'Unknown'
  
  // Handle error responses
  if (statusData.error || statusData.success === false) {
    return 'Error'
  }
  
  // Handle different response formats from TD SYNNEX
  if (typeof statusData === 'string') {
    return statusData
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
                   statusData.OrderStatus
    
    if (status) return status
    
    // If response is empty or unknown format
    if (Object.keys(statusData).length === 0 || Array.isArray(statusData) && statusData.length === 0) {
      return 'Pending'
    }
  }
  
  return 'Processing'
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order
}

const cancelOrder = async () => {
  if (!selectedOrder.value) return

  const reason = prompt('Please provide a reason for cancellation:')
  if (!reason) return

  isSubmitting.value = true
  try {
    const response = await api.post(`/orders/${selectedOrder.value.order_number}/cancel`, {
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
    const response = await api.get(`/invoices/${selectedOrder.value.order_number}/pdf`, {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `invoice-${selectedOrder.value.order_number}.pdf`)
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
