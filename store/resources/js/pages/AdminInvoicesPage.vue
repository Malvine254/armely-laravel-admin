<template>
  <AdminLayout>
    <template #title>Invoice Management</template>

    <div class="admin-fit-page">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500 mb-1">Total Invoices</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-lg flex items-center justify-center">
            <i class="fas fa-receipt text-[#2F5597] text-xl"></i>
          </div>
        </div>
      </div>
      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500 mb-1">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ stats.pending || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-hourglass-half text-amber-600 text-xl"></i>
          </div>
        </div>
      </div>
      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500 mb-1">Paid</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.paid || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
          </div>
        </div>
      </div>
      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500 mb-1">Overdue</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.overdue || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-rose-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-exclamation-circle text-rose-600 text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="rounded-xl border-0 shadow-lg bg-white mb-6 overflow-hidden">
      <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex -mb-px">
          <button
            v-for="tab in statusTabs"
            :key="tab.value"
            @click="statusFilter = tab.value; applyFilters()"
            :class="[
              'px-6 py-4 text-sm font-semibold border-b-2 transition',
              statusFilter === tab.value
                ? 'border-[#2F5597] text-[#2F5597]'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
            <span v-if="tab.count !== undefined" :class="[
              'ml-2 px-2 py-1 text-xs rounded-full',
              statusFilter === tab.value
                ? 'bg-[#2F5597]/10 text-[#2F5597]'
                : 'bg-gray-100 text-gray-500'
            ]">
              {{ tab.count }}
            </span>
          </button>
        </nav>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="rounded-xl border-0 shadow-lg bg-white p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search Invoice</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Enter invoice number or customer name..."
            @keyup.enter="applyFilters"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
          <select v-model="sortBy" @change="applyFilters" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2F5597]">
            <option value="newest" class="bg-white">Newest First</option>
            <option value="oldest" class="bg-white">Oldest First</option>
            <option value="highest" class="bg-white">Highest Amount</option>
            <option value="lowest" class="bg-white">Lowest Amount</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium py-2.5 px-4 rounded-lg transition"
          >
            <i class="fas fa-search mr-2"></i>Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Action Toolbar -->
    <transition name="slide-down">
      <div v-if="selectedIds.size > 0" class="rounded-lg border border-[#2F5597]/20 text-gray-900 shadow px-5 py-3 mb-4 flex flex-wrap items-center gap-3">
        <span class="font-semibold text-sm mr-2">{{ selectedIds.size }} selected</span>

        <button
          @click="bulkMarkPaid"
          :disabled="bulkLoading"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-green-500 hover:bg-green-400 text-white transition disabled:opacity-50"
        >
          <i class="fas fa-check"></i> Mark Paid
        </button>

        <button
          @click="bulkCancel"
          :disabled="bulkLoading"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-yellow-500 hover:bg-yellow-400 text-gray-900 transition disabled:opacity-50"
        >
          <i class="fas fa-ban"></i> Cancel
        </button>

        <button
          @click="bulkDelete"
          :disabled="bulkLoading"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-red-500 hover:bg-red-400 text-gray-900 transition disabled:opacity-50"
        >
          <i :class="bulkLoading ? 'fas fa-spinner fa-spin' : 'fas fa-trash'"></i> Delete
        </button>

        <button
          @click="clearSelection"
          class="ml-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-gray-200 hover:bg-gray-200 transition"
        >
          <i class="fas fa-times"></i> Clear
        </button>
      </div>
    </transition>

    <!-- Loading -->
    <div v-if="isLoading" class="flex justify-center py-12">
      <svg class="animate-spin h-8 w-8 text-[#2F5597]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <!-- Invoices List -->
    <div v-else class="admin-table-card rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="overflow-x-auto admin-table-scroll">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-4 w-10">
                <input
                  type="checkbox"
                  :checked="allPageSelected"
                  :indeterminate="somePageSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded border-gray-300 cursor-pointer" style="accent-color: #2F5597;"
                />
              </th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Invoice #</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Customer</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Company</th>
              <th class="px-6 py-4 text-right font-semibold text-gray-700 uppercase text-xs tracking-wide">Amount</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Status</th>
              <th class="px-6 py-4 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Due Date</th>
              <th class="px-6 py-4 text-center font-semibold text-gray-700 uppercase text-xs tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="invoices.length === 0" class="border-b border-gray-200">
              <td :colspan="8" class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3 block opacity-30 text-gray-400"></i>
                <p>No invoices found</p>
              </td>
            </tr>
            <tr v-for="invoice in invoices" :key="invoice.id" class="border-b border-gray-200 hover:bg-gray-50 transition">
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :checked="selectedIds.has(invoice.id)"
                  @change="toggleSelect(invoice.id)"
                  class="w-4 h-4 rounded border-gray-300 cursor-pointer" style="accent-color: #2F5597;"
                />
              </td>
              <td class="px-6 py-4">
                <span class="font-medium text-[#2F5597] font-mono">{{ invoice.invoice_number }}</span>
              </td>
              <td class="px-6 py-4">
                <p class="font-medium text-gray-900">{{ invoice.user?.name }}</p>
                <p class="text-xs text-gray-500">{{ invoice.user?.email }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="text-gray-700">{{ invoice.user?.company?.name }}</p>
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
                <span :class="isOverdue(invoice.due_at) ? 'text-rose-600 font-semibold' : 'text-gray-500'">
                  {{ formatDate(invoice.due_at) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center items-center gap-2">
                  <!-- View -->
                  <button
                    @click="viewInvoice(invoice)"
                    title="View Invoice"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-[#2F5597]/10 text-[#2F5597] border border-[#2F5597]/30 hover:bg-[#2F5597]/20 transition-colors duration-150"
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
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-blue-500/10 text-blue-700 border border-blue-500/30 hover:bg-blue-500/20 transition-colors duration-150 disabled:opacity-40 disabled:cursor-not-allowed"
                  >
                    <i :class="sendingReminderInvoiceId === invoice.id ? 'fas fa-spinner fa-spin' : 'fas fa-envelope'"></i>
                    <span>{{ sendingReminderInvoiceId === invoice.id ? 'Sending…' : 'Reminder' }}</span>
                  </button>

                  <!-- Mark Paid -->
                  <button
                    v-if="invoice.status === 'pending'"
                    @click="markAsPaid(invoice)"
                    title="Mark as Paid"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/30 hover:bg-emerald-500/20 transition-colors duration-150"
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
      <div class="admin-table-pagination px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gray-50">
        <div class="text-sm text-gray-500">
          <p>Showing {{ invoices.length }} of {{ totalInvoices }} invoices</p>
          <p class="mt-1">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchInvoices()"
            class="px-3 py-2 border border-gray-200 text-gray-500 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
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
                ? 'bg-gradient-to-r from-[#2F5597] to-[#1e3a6b] text-white border-[#2F5597]'
                : 'border-gray-200 text-gray-500 hover:bg-gray-100'
            ]"
          >
            {{ page }}
          </button>
          <button
            :disabled="currentPage >= lastPage"
            @click="currentPage++; fetchInvoices()"
            class="px-3 py-2 border border-gray-200 text-gray-500 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    </div>

    <!-- Invoice View Modal -->
    <div v-if="selectedInvoice" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click="selectedInvoice = null">
      <div class="bg-white rounded-2xl border-0 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop style="background:#fff;">
        <div class="sticky top-0 text-white p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold">Invoice: {{ selectedInvoice.invoice_number }}</h3>
            <button @click="selectedInvoice = null" class="text-white/60 hover:text-white">
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>

        <div class="bg-white p-6 space-y-6">
          <!-- Invoice Details -->
          <div class="grid grid-cols-2 gap-6">
            <div>
              <p class="text-sm text-gray-500 font-medium">Customer</p>
              <p class="text-lg font-semibold text-gray-900">{{ selectedInvoice.user?.name }}</p>
              <p class="text-sm text-gray-500">{{ selectedInvoice.user?.email }}</p>
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
              <p class="text-lg font-semibold" :class="isOverdue(selectedInvoice.due_at) ? 'text-rose-600' : 'text-gray-900'">
                {{ formatDate(selectedInvoice.due_at) }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Amount</p>
              <p class="text-2xl font-bold text-[#2F5597]">${{ formatCurrency(selectedInvoice.total_amount) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500 font-medium">Status</p>
              <span :class="statusBadgeClass(selectedInvoice.status)">
                {{ formatStatus(selectedInvoice.status) }}
              </span>
            </div>
          </div>

          <hr class="my-4 border-gray-200" />

          <!-- Invoice Items -->
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-3">Items</p>
            <div class="space-y-2 bg-gray-50 border border-gray-200 p-4 rounded-lg max-h-48 overflow-y-auto">
              <div v-for="(item, index) in selectedInvoice.items" :key="index" class="text-sm">
                <p class="font-medium text-gray-900">{{ getInvoiceItemName(item, index) }}</p>
                <p class="text-xs text-gray-500">Qty: {{ getInvoiceItemQuantity(item) }} × ${{ formatCurrency(getInvoiceItemUnitPrice(item)) }}</p>
              </div>
            </div>
          </div>

          <!-- Payment Section -->
          <div v-if="selectedInvoice.status === 'pending'" class="bg-[#2F5597]/10 border border-[#2F5597]/20 rounded-lg p-4">
            <h4 class="font-semibold text-gray-900 mb-3">Mark as Paid</h4>
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
                <input
                  v-model="paymentDate"
                  type="date"
                  class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea
                  v-model="paymentNotes"
                  placeholder="Add payment notes..."
                  class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] h-20"
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
          <div class="flex space-x-3 justify-end border-t border-gray-200 pt-4">
            <button
              @click="selectedInvoice = null"
              class="px-6 py-2 border border-[#2F5597]/50 rounded-lg text-[#2F5597] font-medium hover:bg-[#2F5597]/20 transition"
            >
              Close
            </button>
            <button
              @click="downloadPdf"
              class="px-6 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition"
            >
              <i class="fas fa-download mr-2"></i>Download PDF
            </button>
            <button
              v-if="selectedInvoice.status !== 'paid' && selectedInvoice.status !== 'cancelled'"
              @click="sendReminder(selectedInvoice)"
              :disabled="sendingReminderInvoiceId === selectedInvoice.id"
              class="px-6 py-2 border border-[#2F5597]/50 rounded-lg text-[#2F5597] font-medium hover:bg-[#2F5597]/20 disabled:opacity-50 disabled:cursor-not-allowed transition"
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
const selectedIds = ref(new Set())
const bulkLoading = ref(false)

const searchQuery = ref('')
const sortBy = ref('newest')
const statusFilter = ref('')
const currentPage = ref(1)
const totalInvoices = ref(0)
const lastPage = ref(1)
const isSubmitting = ref(false)
const isLoading = ref(false)
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

const allPageSelected = computed(() =>
  invoices.value.length > 0 && invoices.value.every(inv => selectedIds.value.has(inv.id))
)

const somePageSelected = computed(() =>
  invoices.value.some(inv => selectedIds.value.has(inv.id)) && !allPageSelected.value
)

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

const getInvoiceItemName = (item, index) => {
  return item?.name || item?.product_name || item?.description || item?.title || item?.mfg_part_number || `Item ${index + 1}`
}

const getInvoiceItemQuantity = (item) => {
  return Number(item?.quantity || item?.qty || 1)
}

const getInvoiceItemUnitPrice = (item) => {
  const unitPrice = Number(item?.unit_price ?? item?.price ?? 0)
  if (unitPrice > 0) return unitPrice

  const quantity = getInvoiceItemQuantity(item)
  const lineTotal = Number(item?.line_total ?? item?.total ?? 0)
  return quantity > 0 ? lineTotal / quantity : 0
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

const toggleSelect = (id) => {
  const next = new Set(selectedIds.value)
  if (next.has(id)) next.delete(id)
  else next.add(id)
  selectedIds.value = next
}

const toggleSelectAll = () => {
  if (allPageSelected.value) {
    const next = new Set(selectedIds.value)
    invoices.value.forEach(inv => next.delete(inv.id))
    selectedIds.value = next
  } else {
    const next = new Set(selectedIds.value)
    invoices.value.forEach(inv => next.add(inv.id))
    selectedIds.value = next
  }
}

const clearSelection = () => {
  selectedIds.value = new Set()
}

const bulkDelete = async () => {
  if (selectedIds.value.size === 0) return
  if (!confirm(`Delete ${selectedIds.value.size} invoice(s)? This cannot be undone.`)) return

  bulkLoading.value = true
  try {
    const response = await api.post('/admin/invoices/bulk-delete', { invoice_ids: [...selectedIds.value] })
    if (response.data.success) {
      alert(response.data.message)
      clearSelection()
      fetchInvoices()
      fetchStats()
    } else {
      alert(response.data.message || 'Delete failed')
    }
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to delete invoices')
  } finally {
    bulkLoading.value = false
  }
}

const bulkMarkPaid = async () => {
  if (selectedIds.value.size === 0) return
  if (!confirm(`WARNING: You are about to manually mark ${selectedIds.value.size} invoice(s) as paid.\n\nThis action affects payment status and may trigger downstream order processing.\n\nDo you want to continue?`)) return

  bulkLoading.value = true
  try {
    const response = await api.post('/admin/invoices/bulk-mark-paid', {
      invoice_ids: [...selectedIds.value],
      payment_date: new Date().toISOString().split('T')[0],
    })
    if (response.data.success) {
      alert(response.data.message)
      clearSelection()
      fetchInvoices()
      fetchStats()
    } else {
      alert(response.data.message || 'Failed to mark as paid')
    }
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to mark invoices as paid')
  } finally {
    bulkLoading.value = false
  }
}

const bulkCancel = async () => {
  if (selectedIds.value.size === 0) return
  if (!confirm(`Cancel ${selectedIds.value.size} invoice(s)?`)) return

  bulkLoading.value = true
  try {
    const response = await api.post('/admin/invoices/bulk-cancel', { invoice_ids: [...selectedIds.value] })
    if (response.data.success) {
      alert(response.data.message)
      clearSelection()
      fetchInvoices()
      fetchStats()
    } else {
      alert(response.data.message || 'Failed to cancel invoices')
    }
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to cancel invoices')
  } finally {
    bulkLoading.value = false
  }
}

const fetchInvoices = async () => {
  isLoading.value = true
  try {
    const endpoint = statusFilter.value ? `/admin/invoices/${statusFilter.value}` : '/admin/invoices'
    const response = await api.get(endpoint, {
      params: {
        page: currentPage.value,
        per_page: 10,
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
  } finally {
    isLoading.value = false
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

  if (!confirm(`WARNING: You are about to manually confirm payment for invoice ${selectedInvoice.value.invoice_number}.\n\nDo you want to proceed?`)) {
    return
  }

  isSubmitting.value = true
  try {
    const response = await api.post(`/admin/invoices/${selectedInvoice.value.id}/mark-paid`, {
      payment_date: paymentDate.value,
      payment_notes: paymentNotes.value
    })

    if (response.data.success) {
      alert(response.data.message || 'Payment recorded successfully!')
      selectedInvoice.value = null
      await fetchInvoices()
      await fetchStats()
    }
  } catch (error) {
    console.error('Failed to record payment:', error)
    alert('Failed to record payment: ' + (error.response?.data?.message || error.message))
  } finally {
    isSubmitting.value = false
  }
}

const markAsPaid = async (invoice) => {
  if (!confirm(`WARNING: You are about to start manual payment confirmation for invoice ${invoice.invoice_number}.\n\nContinue?`)) {
    return
  }

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
  background: #f9fafb;
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
