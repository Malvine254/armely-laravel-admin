<template>
  <AdminLayout>
    <template #title>Invoice Management</template>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Total Invoices</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-[#edf3fb] rounded-lg flex items-center justify-center">
            <i class="fas fa-receipt text-[#2f5597] text-xl"></i>
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
            <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Paid</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.paid || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 mb-1">Overdue</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.overdue || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
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
          <label class="block text-sm font-medium text-gray-700 mb-2">Search Invoice</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Enter invoice number or customer name..."
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

    <!-- Invoices List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Invoice #</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Customer</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Company</th>
              <th class="px-6 py-4 text-right font-semibold text-gray-700">Amount</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Status</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700">Due Date</th>
              <th class="px-6 py-4 text-center font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="invoices.length === 0" class="border-b border-gray-200 hover:bg-gray-50">
              <td :colspan="7" class="px-6 py-9 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                <p>No invoices found</p>
              </td>
            </tr>
            <tr v-for="invoice in invoices" :key="invoice.id" class="border-b border-gray-200 hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <span class="font-medium text-[#2f5597]">{{ invoice.invoice_number }}</span>
              </td>
              <td class="px-6 py-4">
                <p class="font-medium text-gray-900">{{ invoice.user?.name }}</p>
                <p class="text-xs text-gray-500">{{ invoice.user?.email }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="text-gray-900">{{ invoice.user?.company?.name }}</p>
              </td>
              <td class="px-6 py-4 text-right">
                <p class="font-bold text-gray-900">${{ formatCurrency(invoice.total_amount) }}</p>
              </td>
              <td class="px-6 py-4">
                <span :class="statusBadgeClass(invoice.status)">
                  {{ formatStatus(invoice.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm">
                <span :class="isOverdue(invoice.due_at) ? 'text-red-600 font-semibold' : 'text-gray-600'">
                  {{ formatDate(invoice.due_at) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center items-center gap-2">
                  <!-- View -->
                  <button
                    @click="viewInvoice(invoice)"
                    title="View Invoice"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-[#edf3fb] text-[#2f5597] hover:bg-[#2f5597] hover:text-white transition-colors duration-150"
                  >
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                  </button>

                  <!-- Send Reminder -->
                  <button
                    v-if="invoice.status !== 'paid' && invoice.status !== 'cancelled'"
                    @click="sendReminder(invoice)"
                    :disabled="sendingReminderInvoiceId === invoice.id"
                    title="Send Payment Reminder"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed"
                  >
                    <i :class="sendingReminderInvoiceId === invoice.id ? 'fas fa-spinner fa-spin' : 'fas fa-envelope'"></i>
                    <span>{{ sendingReminderInvoiceId === invoice.id ? 'Sending…' : 'Reminder' }}</span>
                  </button>

                  <!-- Mark Paid -->
                  <button
                    v-if="invoice.status === 'pending'"
                    @click="markAsPaid(invoice)"
                    title="Mark as Paid"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-green-50 text-green-700 hover:bg-green-600 hover:text-white transition-colors duration-150"
                  >
                    <i class="fas fa-check"></i>
                    <span>Mark Paid</span>
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
          <p>Showing {{ invoices.length }} of {{ totalInvoices }} invoices</p>
          <p class="mt-1">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchInvoices()"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button
            v-for="page in pageNumbers"
            :key="page"
            @click="currentPage = page; fetchInvoices()"
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
            @click="currentPage++; fetchInvoices()"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Invoice View Modal -->
    <div v-if="selectedInvoice" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 text-white p-6 border-b" style="background: linear-gradient(90deg, #2f5597, #1f4788);">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold">Invoice: {{ selectedInvoice.invoice_number }}</h3>
            <button @click="selectedInvoice = null" class="text-white hover:text-gray-200">
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-6">
          <!-- Invoice Details -->
          <div class="grid grid-cols-2 gap-6">
            <div>
              <p class="text-sm text-gray-500 font-medium">Customer</p>
              <p class="text-lg font-semibold text-gray-900">{{ selectedInvoice.user?.name }}</p>
              <p class="text-sm text-gray-600">{{ selectedInvoice.user?.email }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Company</p>
              <p class="text-lg font-semibold text-gray-900">{{ selectedInvoice.user?.company?.name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Invoice Date</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedInvoice.invoice_date) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Due Date</p>
              <p class="text-lg font-semibold" :class="isOverdue(selectedInvoice.due_at) ? 'text-red-600' : 'text-gray-900'">
                {{ formatDate(selectedInvoice.due_at) }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Amount</p>
              <p class="text-2xl font-bold text-gray-900">${{ formatCurrency(selectedInvoice.total_amount) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Status</p>
              <span :class="statusBadgeClass(selectedInvoice.status)">
                {{ formatStatus(selectedInvoice.status) }}
              </span>
            </div>
          </div>

          <hr class="my-4" />

          <!-- Invoice Items -->
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3">Items</p>
            <div class="space-y-2 bg-gray-50 p-4 rounded-lg max-h-48 overflow-y-auto">
              <div v-for="(item, index) in selectedInvoice.items" :key="index" class="text-sm">
                <p class="font-medium text-gray-900">{{ item.name || 'Item ' + (index + 1) }}</p>
                <p class="text-xs text-gray-600">Qty: {{ item.quantity }} × ${{ formatCurrency(item.price) }}</p>
              </div>
            </div>
          </div>

          <!-- Payment Section -->
          <div v-if="selectedInvoice.status === 'pending'" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-semibold text-gray-900 mb-3">Mark as Paid</h4>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
                <input
                  v-model="paymentDate"
                  type="date"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea
                  v-model="paymentNotes"
                  placeholder="Add payment notes..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597] h-20"
                ></textarea>
              </div>
              <button
                @click="recordPayment"
                :disabled="isSubmitting"
                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition disabled:opacity-50"
              >
                <i class="fas fa-check mr-2"></i>Record Payment
              </button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3 justify-end border-t pt-4">
            <button
              @click="selectedInvoice = null"
              class="px-6 py-2 border border-[#2f5597] rounded-lg text-[#2f5597] font-medium hover:bg-[#edf3fb] transition"
            >
              Close
            </button>
            <button
              @click="downloadPdf"
              class="px-6 py-2 bg-[#2f5597] hover:bg-[#274a82] text-white font-medium rounded-lg transition"
            >
              <i class="fas fa-download mr-2"></i>Download PDF
            </button>
            <button
              v-if="selectedInvoice.status !== 'paid' && selectedInvoice.status !== 'cancelled'"
              @click="sendReminder(selectedInvoice)"
              :disabled="sendingReminderInvoiceId === selectedInvoice.id"
              class="px-6 py-2 border border-[#2f5597] rounded-lg text-[#2f5597] font-medium hover:bg-[#edf3fb] disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
              <i class="fas fa-envelope mr-2"></i>{{ sendingReminderInvoiceId === selectedInvoice.id ? 'Sending...' : 'Send Reminder' }}
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

const invoices = ref([])
const selectedInvoice = ref(null)
const searchQuery = ref('')
const sortBy = ref('newest')
const statusFilter = ref('')
const currentPage = ref(1)
const totalInvoices = ref(0)
const lastPage = ref(1)
const isSubmitting = ref(false)
const sendingReminderInvoiceId = ref(null)
const paymentDate = ref('')
const paymentNotes = ref('')
const stats = ref({
  total: 0,
  pending: 0,
  paid: 0,
  overdue: 0
})

const statusTabs = computed(() => [
  { label: 'All Invoices', value: '', count: stats.value.total },
  { label: 'Pending', value: 'pending', count: stats.value.pending },
  { label: 'Paid', value: 'paid', count: stats.value.paid },
  { label: 'Overdue', value: 'overdue', count: stats.value.overdue }
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
    'pending': 'Pending',
    'paid': 'Paid',
    'cancelled': 'Cancelled',
    'draft': 'Draft'
  }
  return statusMap[status] || status
}

const statusBadgeClass = (status) => {
  const classes = {
    'pending': 'px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800',
    'paid': 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800',
    'cancelled': 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800',
    'draft': 'px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800'
  }
  return classes[status] || 'px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800'
}

const isOverdue = (dueDate) => {
  return new Date(dueDate) < new Date() && selectedInvoice.value?.status === 'pending'
}

const fetchInvoices = async () => {
  try {
    const endpoint = statusFilter.value ? `/admin/invoices/${statusFilter.value}` : '/admin/invoices'
    const response = await api.get(endpoint, {
      params: {
        page: currentPage.value,
        per_page: 15,
        search: searchQuery.value || undefined,
        sort_by: 'created_at',
        sort_order: sortBy.value === 'newest' ? 'desc' : 'asc'
      }
    })

    if (response.data.success) {
      invoices.value = response.data.data
      totalInvoices.value = response.data.pagination.total
      lastPage.value = response.data.pagination.last_page
    }
  } catch (error) {
    console.error('Failed to fetch invoices:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await api.get('/admin/invoices/stats')
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const viewInvoice = (invoice) => {
  selectedInvoice.value = invoice
  paymentDate.value = ''
  paymentNotes.value = ''
}

const recordPayment = async () => {
  if (!selectedInvoice.value || !paymentDate.value) {
    alert('Please select a payment date')
    return
  }

  isSubmitting.value = true
  try {
    const response = await api.post(`/admin/invoices/${selectedInvoice.value.id}/mark-paid`, {
      payment_date: paymentDate.value,
      payment_notes: paymentNotes.value
    })

    if (response.data.success) {
      alert('Payment recorded successfully!')
      selectedInvoice.value = null
      fetchInvoices()
      fetchStats()
    }
  } catch (error) {
    console.error('Failed to record payment:', error)
    alert('Failed to record payment: ' + (error.response?.data?.message || error.message))
  } finally {
    isSubmitting.value = false
  }
}

const markAsPaid = async (invoice) => {
  selectedInvoice.value = invoice
  paymentDate.value = new Date().toISOString().split('T')[0]
  paymentNotes.value = ''
}

const downloadPdf = async () => {
  try {
    window.open(`/admin/invoices/${selectedInvoice.value.id}/pdf`, '_blank')
  } catch (error) {
    console.error('Failed to download PDF:', error)
  }
}

const sendReminder = async (invoice) => {
  if (!invoice?.id) {
    alert('Invalid invoice selected')
    return
  }

  sendingReminderInvoiceId.value = invoice.id

  try {
    const response = await api.post(`/admin/invoices/${invoice.id}/send-reminder`)
    if (response.data?.success) {
      alert(response.data?.message || 'Payment reminder sent successfully')
    } else {
      alert(response.data?.message || 'Failed to send payment reminder')
    }
  } catch (error) {
    console.error('Failed to send reminder:', error)
    alert(error.response?.data?.message || 'Failed to send payment reminder')
  } finally {
    sendingReminderInvoiceId.value = null
  }
}

const applyFilters = () => {
  currentPage.value = 1
  fetchInvoices()
}

onMounted(() => {
  fetchStats()
  fetchInvoices()
})
</script>
