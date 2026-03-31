<template>
  <AdminLayout>
    <template #title>Quote Management</template>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Total Quotes</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-[#edf3fb] rounded-lg flex items-center justify-center">
            <i class="fas fa-file-invoice text-[#2f5597] text-xl"></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ stats.pending || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-clock text-yellow-600 text-xl"></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.approved || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Rejected</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.rejected || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-times-circle text-red-600 text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
          <button
            v-for="tab in statusTabs"
            :key="tab.value"
            @click="statusFilter = tab.value; applyFilters()"
            :class="[
              'px-6 py-4 text-sm font-semibold border-b-2 transition',
              statusFilter === tab.value
                ? 'border-[#2f5597] text-[#2f5597]'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
            <span v-if="tab.count !== undefined" :class="[
              'ml-2 px-2 py-1 text-xs rounded-full',
              statusFilter === tab.value
                ? 'bg-[#2f5597] text-white'
                : 'bg-gray-200 text-gray-600'
            ]">
              {{ tab.count }}
            </span>
          </button>
        </nav>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search Quote</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Enter quote ID or customer name..."
            @keyup.enter="applyFilters"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
          <select v-model="sortBy" @change="applyFilters" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]">
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="highest">Highest Amount</option>
            <option value="lowest">Lowest Amount</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-[#2f5597] hover:bg-[#274a82] text-white font-medium py-2 px-4 rounded-lg transition"
          >
            <i class="fas fa-search mr-2"></i>Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Quotes List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Bulk Actions Bar -->
      <div v-if="selectedQuotes.length > 0" class="px-6 py-3 bg-[#edf3fb] border-b border-gray-200 flex items-center justify-between">
        <span class="text-sm font-medium text-gray-700">{{ selectedQuotes.length }} quote(s) selected</span>
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
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Quote ID</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Order ID</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Order Status</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Customer</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Company</th>
              <th class="px-6 py-4 text-right font-semibold text-gray-700">Amount</th>
              <th v-if="!statusFilter" class="px-6 py-4 text-left font-semibold text-gray-700">Status</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Submitted</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Expires</th>
              <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="quotes.length === 0" class="border-b border-gray-200 hover:bg-gray-50">
              <td :colspan="statusFilter ? 9 : 10" class="px-6 py-9 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                <p>No quotes found</p>
              </td>
            </tr>
            <tr v-for="quote in quotes" :key="quote.id" class="border-b border-gray-200 hover:bg-gray-50 transition">
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :value="quote.id"
                  v-model="selectedQuotes"
                  class="w-4 h-4 text-[#2f5597] border-gray-300 rounded focus:ring-[#2f5597]"
                />
              </td>
              <td class="px-6 py-4">
                <span class="font-medium text-[#2f5597]">{{ quote.quote_id }}</span>
              </td>
              <td class="px-6 py-4">
                <span class="text-gray-900">{{ quote.order?.order_number || '-' }}</span>
              </td>
              <td class="px-6 py-4">
                <span v-if="quote.order?.order_number">
                  <span v-if="isLocalOrder(quote.order.order_number)" class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    <i class="fas fa-database mr-1"></i>Local Order
                  </span>
                  <span v-else-if="quote.order.td_synnex_status" class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                    <i class="fas fa-check-circle mr-1"></i>{{ getStatusDisplay(quote.order.td_synnex_status) }}
                  </span>
                  <span v-else class="text-gray-500 text-sm">
                    <i class="fas fa-spinner fa-spin mr-1"></i>Fetching...
                  </span>
                </span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="px-6 py-4">
                <p class="font-medium text-gray-900">{{ getUserName(quote) }}</p>
                <p class="text-xs text-gray-500">{{ getUserEmail(quote) }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="text-gray-900">{{ getCompanyName(quote) }}</p>
              </td>
              <td class="px-6 py-4 text-right">
                <p class="font-bold text-gray-900">${{ formatCurrency(quote.total_amount) }}</p>
                <p class="text-xs text-gray-500">Tax: ${{ formatCurrency(quote.tax_amount) }}</p>
              </td>
              <td v-if="!statusFilter" class="px-6 py-4">
                <span :class="statusBadgeClass(quote.status)">
                  {{ formatStatus(quote.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ formatDate(quote.submitted_at) }}
              </td>
              <td class="px-6 py-4 text-sm">
                <span :class="isExpiringSoon(quote.expires_at) ? 'text-red-600 font-semibold' : 'text-gray-600'">
                  {{ formatDate(quote.expires_at) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center space-x-3">
                  <button
                    @click="selectQuote(quote)"
                    class="text-[#2f5597] hover:text-[#274a82] font-medium"
                  >
                    <i class="fas fa-eye mr-1"></i>Review
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
          <p>Showing {{ quotes.length }} of {{ totalQuotes }} quotes</p>
          <p class="mt-1">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchQuotes()"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button
            v-for="page in pageNumbers"
            :key="page"
            @click="currentPage = page; fetchQuotes()"
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
            @click="currentPage++; fetchQuotes()"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Quote Review Modal -->
    <div v-if="selectedQuote" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 text-white p-6 border-b" style="background: linear-gradient(90deg, #2f5597, #1f4788);">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold">Quote Review: {{ selectedQuote.quote_id }}</h3>
            <button @click="selectedQuote = null" class="text-white hover:text-gray-200">
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <!-- Quote Details -->
          <div class="grid grid-cols-2 gap-6">
            <div>
              <p class="text-sm text-gray-500 font-medium">Customer</p>
              <p class="text-lg font-semibold text-gray-900">{{ getUserName(selectedQuote) }}</p>
              <p class="text-sm text-gray-600">{{ getUserEmail(selectedQuote) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Company</p>
              <p class="text-lg font-semibold text-gray-900">{{ getCompanyName(selectedQuote) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Amount</p>
              <p class="text-2xl font-bold text-gray-900">${{ formatCurrency(selectedQuote.total_amount) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Tax</p>
              <p class="text-lg font-semibold text-gray-900">${{ formatCurrency(selectedQuote.tax_amount) }}</p>
            </div>
            <div v-if="selectedQuote.order">
              <p class="text-sm text-gray-500 font-medium">Order ID</p>
              <p class="text-lg font-semibold text-gray-900">{{ selectedQuote.order.order_number }}</p>
              <p class="text-xs text-gray-500">Status: {{ selectedQuote.order.status }}</p>
            </div>
          </div>

          <hr class="my-4" />

          <!-- Quote Items -->
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3">Items</p>
            <div class="space-y-2 bg-gray-50 p-4 rounded-lg max-h-48 overflow-y-auto">
              <div v-for="(item, index) in selectedQuote.items" :key="index" class="text-sm">
                <p class="font-medium text-gray-900">{{ item.name || 'Item ' + (index + 1) }}</p>
                <p class="text-xs text-gray-600">Qty: {{ item.quantity }} × ${{ formatCurrency(item.price) }}</p>
              </div>
            </div>
          </div>

          <!-- Admin Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
            <textarea
              v-model="adminNotes"
              placeholder="Add internal notes..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597] h-24"
            ></textarea>
          </div>

          <!-- Rejection Reason (if rejecting) -->
          <div v-if="showRejectionReason">
            <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
            <textarea
              v-model="rejectionReason"
              placeholder="Provide a reason for rejection..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597] h-20"
              required
            ></textarea>
          </div>

          <!-- Already Approved Message -->
          <div v-if="selectedQuote.order || selectedQuote.status === 'approved'" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
              <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
              <div>
                <p class="font-semibold text-green-900">Quote Already Approved</p>
                <p class="text-sm text-green-700">This quote has been approved and converted to order: {{ selectedQuote.order?.order_number }}</p>
              </div>
            </div>
          </div>

          <!-- Already Rejected Message -->
          <div v-else-if="selectedQuote.status === 'rejected'" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
              <i class="fas fa-times-circle text-red-600 text-xl mr-3"></i>
              <div>
                <p class="font-semibold text-red-900">Quote Rejected</p>
                <p class="text-sm text-red-700" v-if="selectedQuote.rejection_reason">Reason: {{ selectedQuote.rejection_reason }}</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons (only for pending quotes) -->
          <div v-else class="flex space-x-3 justify-end border-t pt-4">
            <button
              @click="selectedQuote = null"
              class="px-6 py-2 border border-[#2f5597] rounded-lg text-[#2f5597] font-medium hover:bg-[#edf3fb] transition"
            >
              Cancel
            </button>
            <button
              v-if="!showRejectionReason"
              @click="showRejectionReason = true"
              class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition"
            >
              <i class="fas fa-times mr-2"></i>Reject
            </button>
            <button
              v-if="showRejectionReason"
              @click="rejectQuote"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition disabled:opacity-50"
            >
              <i class="fas fa-check mr-2"></i>Confirm Rejection
            </button>
            <button
              v-if="!showRejectionReason"
              @click="approveQuote"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-[#2f5597] hover:bg-[#274a82] text-white font-medium rounded-lg transition disabled:opacity-50"
            >
              <i class="fas fa-check mr-2"></i>Approve
            </button>
          </div>
          
          <!-- Close Button (for already processed quotes) -->
          <div v-if="selectedQuote.order || selectedQuote.status === 'approved' || selectedQuote.status === 'rejected'" class="flex justify-end mt-4">
            <button
              @click="selectedQuote = null"
              class="px-6 py-2 bg-[#2f5597] hover:bg-[#274a82] text-white font-medium rounded-lg transition"
            >
              <i class="fas fa-times mr-2"></i>Close
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
            <h3 class="text-lg font-bold text-gray-900">Delete {{ selectedQuotes.length }} Quote(s)?</h3>
          </div>
          <p class="text-gray-600 mb-6">Are you sure you want to permanently delete {{ selectedQuotes.length }} quote(s)? This action cannot be undone.</p>
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
import { ref, computed, onMounted } from 'vue'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const quotes = ref([])
const selectedQuote = ref(null)
const selectedQuotes = ref([])
const searchQuery = ref('')
const sortBy = ref('newest')
const statusFilter = ref('')
const currentPage = ref(1)
const totalQuotes = ref(0)
const lastPage = ref(1)
const adminNotes = ref('')
const rejectionReason = ref('')
const showRejectionReason = ref(false)
const showDeleteConfirm = ref(false)
const isSubmitting = ref(false)
const stats = ref({
  total: 0,
  pending: 0,
  approved: 0,
  rejected: 0
})

const allSelected = computed(() => {
  return quotes.value.length > 0 && quotes.value.every(quote => selectedQuotes.value.includes(quote.id))
})

const statusTabs = computed(() => [
  { label: 'All Quotes', value: '', count: stats.value.total },
  { label: 'Pending', value: 'pending', count: stats.value.pending },
  { label: 'Approved', value: 'approved', count: stats.value.approved },
  { label: 'Rejected', value: 'rejected', count: stats.value.rejected }
])

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

const formatStatus = (status) => {
  const statusMap = {
    'pending_review': 'Pending',
    'approved': 'Approved',
    'rejected': 'Rejected',
    'draft': 'Draft'
  }
  return statusMap[status] || status
}

const statusBadgeClass = (status) => {
  const classes = {
    'pending_review': 'px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800',
    'approved': 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800',
    'rejected': 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800',
    'draft': 'px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800'
  }
  return classes[status] || 'px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800'
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

const isLocalOrder = (orderNumber) => {
  return orderNumber && orderNumber.startsWith('ORD-')
}

const isExpiringSoon = (expiryDate) => {
  const daysUntilExpiry = Math.ceil((new Date(expiryDate) - new Date()) / (1000 * 60 * 60 * 24))
  return daysUntilExpiry <= 3
}

const fetchQuotes = async () => {
  try {
    const endpoint = statusFilter.value ? `/admin/quotes/${statusFilter.value}` : '/admin/quotes'
    console.log('[fetchQuotes] Fetching from endpoint:', endpoint)
    console.log('[fetchQuotes] Current statusFilter:', statusFilter.value)
    
    const response = await api.get(endpoint, {
      params: {
        page: currentPage.value,
        pageSize: 10,
        search: searchQuery.value || undefined,
        sortBy: sortBy.value || undefined
      }
    })

    console.log('[fetchQuotes] Response received:', response.data)
    
    if (response.data.success) {
      const newQuotes = response.data.data
      console.log('[fetchQuotes] Setting quotes array. Before:', quotes.value.length, 'After:', newQuotes.length)
      quotes.value = newQuotes
      totalQuotes.value = response.data.pagination.total
      lastPage.value = response.data.pagination.last_page
      
      // Fetch order status for each quote that has an order
      await fetchAllOrderStatuses(newQuotes)
    } else {
      console.error('[fetchQuotes] Response not successful:', response.data)
    }
  } catch (error) {
    console.error('[fetchQuotes] Failed to fetch quotes:', error)
    alert('Failed to fetch quotes: ' + error.message)
  }
}

const fetchAllOrderStatuses = async (quotesData) => {
  try {
    console.log('[fetchAllOrderStatuses] Checking statuses for', quotesData.length, 'quotes')
    
    for (const quote of quotesData) {
      if (quote.order && quote.order.order_number) {
        // Skip fetching status for local orders (they don't exist in TD SYNNEX)
        if (isLocalOrder(quote.order.order_number)) {
          console.log(`[fetchAllOrderStatuses] Skipping local order ${quote.order.order_number}`)
          continue
        }
        
        try {
          const statusResponse = await api.get(`/admin/orders/${quote.order.order_number}/status`)
          if (statusResponse.data.success) {
            // Update the order with TD SYNNEX status
            quote.order.td_synnex_status = statusResponse.data.data.td_synnex_status
            console.log(`[fetchAllOrderStatuses] Status for order ${quote.order.order_number}:`, quote.order.td_synnex_status)
          }
        } catch (err) {
          console.warn(`[fetchAllOrderStatuses] Failed to fetch status for order ${quote.order.order_number}:`, err.message)
          // Continue with next order even if one fails
        }
      }
    }
  } catch (error) {
    console.error('[fetchAllOrderStatuses] Error fetching order statuses:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await api.get('/admin/quotes/stats')
    if (response.data.success) {
      stats.value = response.data.stats
    }
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const selectQuote = (quote) => {
  selectedQuote.value = quote
  adminNotes.value = quote.admin_notes || ''
  rejectionReason.value = ''
  showRejectionReason.value = false
}

const approveQuote = async () => {
  if (!selectedQuote.value) return

  isSubmitting.value = true
  try {
    console.log('[approveQuote] Starting approval for quote:', selectedQuote.value.id)
    
    const response = await api.post(`/admin/quotes/${selectedQuote.value.id}/approve`, {
      admin_notes: adminNotes.value
    })

    console.log('[approveQuote] Approval response:', response.data)
    
    if (response.data.success) {
      alert('Quote approved successfully!')
      selectedQuote.value = null
      
      console.log('[approveQuote] Calling fetchQuotes after approval...')
      await fetchQuotes()
      console.log('[approveQuote] fetchQuotes completed')
      
      await fetchStats()
      console.log('[approveQuote] fetchStats completed')
    }
  } catch (error) {
    console.error('Failed to approve quote:', error)
    alert('Failed to approve quote: ' + (error.response?.data?.message || error.message))
  } finally {
    isSubmitting.value = false
  }
}

const rejectQuote = async () => {
  if (!selectedQuote.value || !rejectionReason.value) {
    alert('Please provide a rejection reason')
    return
  }

  isSubmitting.value = true
  try {
    const response = await api.post(`/admin/quotes/${selectedQuote.value.id}/reject`, {
      reason: rejectionReason.value,
      admin_notes: adminNotes.value
    })

    if (response.data.success) {
      alert('Quote rejected successfully!')
      selectedQuote.value = null
      fetchQuotes()
      fetchStats()
    }
  } catch (error) {
    console.error('Failed to reject quote:', error)
    alert('Failed to reject quote: ' + (error.response?.data?.message || error.message))
  } finally {
    isSubmitting.value = false
  }
}

const applyFilters = () => {
  currentPage.value = 1
  fetchQuotes()
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedQuotes.value = []
  } else {
    selectedQuotes.value = quotes.value.map(quote => quote.id)
  }
}

const confirmBulkDelete = () => {
  showDeleteConfirm.value = true
}

const executeBulkDelete = async () => {
  if (selectedQuotes.value.length === 0) return

  isSubmitting.value = true
  try {
    const response = await api.post('/admin/quotes/bulk-delete', {
      quote_ids: selectedQuotes.value
    })

    if (response.data.success) {
      alert(response.data.message)
      selectedQuotes.value = []
      showDeleteConfirm.value = false
      fetchQuotes()
      fetchStats()
    }
  } catch (error) {
    console.error('Failed to delete quotes:', error)
    alert(error.response?.data?.message || 'Failed to delete quotes')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchStats()
  fetchQuotes()
})
</script>
