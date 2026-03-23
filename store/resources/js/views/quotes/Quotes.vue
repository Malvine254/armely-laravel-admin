<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <Navbar />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Page Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Your Quotes</h1>
        <p class="text-gray-600 text-lg">Track and review your quote history</p>
      </div>

      <!-- Quick Stats -->
      <div v-if="quotes.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg p-6 border transition duration-200" style="border-color: #d9e6f7; box-shadow: 0 6px 18px rgba(47,85,151,0.08);">
          <p class="text-gray-600 text-sm font-medium">Total Quotes</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ quotes.length }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border transition duration-200" style="border-color: #d9e6f7; box-shadow: 0 6px 18px rgba(47,85,151,0.08);">
          <p class="text-gray-600 text-sm font-medium">Pending</p>
          <p class="text-3xl font-bold text-blue-600">{{ getQuotesCountByStatus('pending_review') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border transition duration-200" style="border-color: #d9e6f7; box-shadow: 0 6px 18px rgba(47,85,151,0.08);">
          <p class="text-gray-600 text-sm font-medium">Approved</p>
          <p class="text-3xl font-bold text-green-600">{{ getQuotesCountByStatus('approved') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border transition duration-200" style="border-color: #d9e6f7; box-shadow: 0 6px 18px rgba(47,85,151,0.08);">
          <p class="text-gray-600 text-sm font-medium">Converted</p>
          <p class="text-3xl font-bold" style="color: #6366f1;">{{ getQuotesCountByStatus('converted') }}</p>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
          <div class="relative md:col-span-3">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Filter by Status</label>
            <select v-model="selectedStatus" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
              <option value="">All Statuses</option>
              <option value="draft">Draft</option>
              <option value="pending_review">Pending Review</option>
              <option value="approved">Approved</option>
              <option value="converted">Converted</option>
              <option value="cancelled">Cancelled</option>
              <option value="rejected">Rejected</option>
              <option value="expired">Expired</option>
            </select>
          </div>

          <div class="relative md:col-span-4">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Search Quotes</label>
            <input v-model="searchQuery" type="text" placeholder="Enter quote ID..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Sort By</label>
            <select v-model="sortBy" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
              <option value="created_desc">Newest First</option>
              <option value="created_asc">Oldest First</option>
              <option value="amount_desc">Highest Amount</option>
              <option value="amount_asc">Lowest Amount</option>
              <option value="status_asc">Status (A-Z)</option>
            </select>
          </div>

          <div class="md:col-span-2 flex gap-2">
            <button @click="fetchQuotes" class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200 hover:shadow-lg" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
              Search
            </button>
            <button @click="resetFilters" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition duration-200">
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="inline-block">
          <div class="w-16 h-16 border-4 rounded-full animate-spin mb-4" style="border-color: #e5ebf2; border-top-color: #2F5597;"></div>
          <p class="text-gray-600 font-medium">Loading your quotes...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 mb-8">
        <div class="flex gap-4">
          <svg class="h-6 w-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h3 class="font-semibold text-red-900 text-lg">Unable to Load Quotes</h3>
            <p class="text-red-700 mt-1">{{ error }}</p>
            <button @click="fetchQuotes" class="mt-3 text-red-700 hover:text-red-900 font-semibold underline">Try Again</button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredQuotes.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-1">No Quotes Yet</h3>
        <p class="text-gray-600 mb-6">Add products to your cart to create a quote</p>
        <router-link to="/products" class="inline-block px-6 py-3 text-white rounded-lg font-semibold transition duration-200 hover:shadow-lg" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Browse Products
        </router-link>
      </div>

      <!-- Quotes Grid (Card Layout) -->
      <div v-else class="space-y-4">
        <div v-for="quote in filteredQuotes" :key="quote.id" class="bg-white rounded-xl shadow-sm border transition duration-300 overflow-hidden cursor-pointer" style="border-color: #d9e6f7;" @mouseenter="$event.currentTarget.style.boxShadow='0 10px 24px rgba(47,85,151,0.12)'" @mouseleave="$event.currentTarget.style.boxShadow=''" @click="viewQuote(quote)">
          <!-- Card Header with Status -->
          <div class="px-6 py-4 border-b flex items-center justify-between" style="background: linear-gradient(90deg, #f7fbff, #edf4fc); border-color: #d9e6f7;">
            <div class="flex items-center gap-4 flex-1">
              <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Quote ID</p>
                <p class="text-xl font-bold text-gray-900 font-mono">{{ quote.quote_id }}</p>
              </div>
            </div>
            <span :class="getStatusBadge(quote.status)" class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap">
              {{ formatStatus(quote.status) }}
            </span>
          </div>

          <!-- Card Body -->
          <div class="px-6 py-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 mb-5">
              <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Created Date</p>
                <p class="text-gray-900 font-semibold mt-1">{{ formatDate(quote.created_at) }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Amount</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(quote.total_amount) }}</p>
              </div>
              <div v-if="quote.expires_at">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Expires On</p>
                <p class="text-gray-900 font-semibold mt-1">{{ formatDate(quote.expires_at) }}</p>
              </div>
            </div>

            <!-- Progress Bar (for pending/approved quotes) -->
            <div v-if="['draft', 'pending_review', 'approved'].includes(quote.status)" class="mb-5">
              <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-700">Quote Status Progress</p>
                <p class="text-xs text-gray-600">{{ getQuoteProgress(quote.status) }}%</p>
              </div>
              <div class="w-full rounded-full h-2" style="background-color: #d9e6f7;">
                <div class="h-2 rounded-full transition duration-500" style="background-color: #2F5597;" :style="{ width: getQuoteProgress(quote.status) + '%' }"></div>
              </div>
            </div>
          </div>

          <!-- Card Footer with Actions -->
          <div class="border-t px-6 py-4 flex items-center justify-between" style="background-color: #f8fbff; border-color: #d9e6f7;">
            <button @click.stop="viewQuote(quote)" class="flex items-center gap-2 font-semibold transition duration-200 hover:opacity-75" style="color: #2F5597;">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              View Details
            </button>
            <div class="flex gap-2">
              <button
                @click.stop="downloadQuotePdf(quote)"
                class="flex items-center gap-2 font-semibold px-4 py-2 rounded-lg transition duration-200"
                style="color: #2F5597; border: 1px solid #2F5597;"
                @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
                @mouseleave="$event.target.style.backgroundColor='transparent'"
              >
                PDF
              </button>
              <button
                v-if="getLinkedOrder(quote)"
                @click.stop="viewLinkedOrder(quote)"
                class="flex items-center gap-2 font-semibold px-4 py-2 rounded-lg transition duration-200"
                style="color: #7c3aed; border: 1px solid #7c3aed;"
                @mouseenter="$event.target.style.backgroundColor='#f3e8ff'"
                @mouseleave="$event.target.style.backgroundColor='transparent'"
              >
                View Order
              </button>
              <button
                v-if="canPayQuote(quote)"
                @click.stop="payQuoteInvoice(quote)"
                class="flex items-center gap-2 font-semibold px-4 py-2 rounded-lg text-white transition duration-200"
                style="background-color: #2F5597;"
                @mouseenter="$event.target.style.backgroundColor='#1f4788'"
                @mouseleave="$event.target.style.backgroundColor='#2F5597'"
              >
                Pay Now
              </button>
              <button
                v-if="canCancelQuote(quote)"
                @click.stop="cancelQuote(quote)"
                :disabled="processingQuoteId === quote.quote_id"
                class="flex items-center gap-2 font-semibold px-4 py-2 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                style="color: #e74c3c; border: 1px solid #e74c3c;"
                @mouseenter="$event.target.style.backgroundColor='#fadbd8'"
                @mouseleave="$event.target.style.backgroundColor='transparent'"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ processingQuoteId === quote.quote_id ? 'Cancelling...' : 'Cancel' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quote Detail Modal -->
    <div v-if="selectedQuote" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" @click="selectedQuote = null">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
        <!-- Modal Header -->
        <div class="sticky top-0 text-white px-6 py-6 flex items-center justify-between rounded-t-2xl" style="background: linear-gradient(90deg, #2F5597, #1f4788);">
          <div>
            <p class="text-sm font-semibold text-gray-200 uppercase tracking-wide">Quote</p>
            <h2 class="text-2xl font-bold text-white">{{ selectedQuote.quote_id }}</h2>
          </div>
          <button @click="selectedQuote = null" class="text-gray-300 hover:text-white transition duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-8">
          <!-- Status Section -->
          <div class="mb-8 pb-8 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-bold text-gray-900">Quote Status</h3>
              <span :class="getStatusBadge(selectedQuote.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
                {{ formatStatus(selectedQuote.status) }}
              </span>
            </div>
            <div v-if="['draft', 'pending_review', 'approved'].includes(selectedQuote.status)">
              <div class="w-full rounded-full h-2 mb-2" style="background-color: #d9e6f7;">
                <div class="h-2 rounded-full" style="background-color: #2F5597;" :style="{ width: getQuoteProgress(selectedQuote.status) + '%' }"></div>
              </div>
              <p class="text-xs text-gray-600">{{ getQuoteProgress(selectedQuote.status) }}% Complete</p>
            </div>
          </div>

          <!-- Quote Information Grid -->
          <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Created Date</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedQuote.created_at) }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Total Amount</p>
              <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(selectedQuote.total_amount) }}</p>
            </div>
            <div v-if="selectedQuote.expires_at">
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Expires On</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedQuote.expires_at) }}</p>
            </div>
            <div v-if="getLinkedOrder(selectedQuote)">
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Linked Order</p>
              <p class="text-lg font-semibold text-gray-900 font-mono">{{ getLinkedOrder(selectedQuote).order_number }}</p>
            </div>
            <div v-if="getLinkedInvoice(selectedQuote)">
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Linked Invoice</p>
              <p class="text-lg font-semibold text-gray-900 font-mono">{{ getLinkedInvoice(selectedQuote).invoice_number }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button @click="selectedQuote = null" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition duration-200">
              Close
            </button>
            <button
              @click="downloadQuotePdf(selectedQuote)"
              class="flex-1 px-4 py-3 border border-[#2F5597] text-[#2F5597] rounded-lg font-semibold hover:bg-[#edf3fb] transition duration-200"
            >
              Download PDF
            </button>
            <button
              v-if="getLinkedOrder(selectedQuote)"
              @click="viewLinkedOrder(selectedQuote)"
              class="flex-1 px-4 py-3 border border-[#7c3aed] text-[#7c3aed] rounded-lg font-semibold hover:bg-[#f3e8ff] transition duration-200"
            >
              View Order
            </button>
            <button
              v-if="canPayQuote(selectedQuote)"
              @click="payQuoteInvoice(selectedQuote)"
              class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200"
              style="background-color: #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#1f4788'"
              @mouseleave="$event.target.style.backgroundColor='#2F5597'"
            >
              Pay Invoice
            </button>
            <button
              v-if="canCancelQuote(selectedQuote)"
              @click="cancelQuote(selectedQuote)"
              :disabled="processingQuoteId === selectedQuote.quote_id"
              class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              style="background-color: #e74c3c;"
              @mouseenter="$event.target.style.backgroundColor='#c0392b'"
              @mouseleave="$event.target.style.backgroundColor='#e74c3c'"
            >
              {{ processingQuoteId === selectedQuote.quote_id ? 'Cancelling...' : 'Cancel Quote' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'
import Navbar from '../../components/Navbar.vue'
import axios from 'axios'

export default {
  components: { Navbar },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const toastStore = useToastStore()
    const quotes = ref([])
    const loading = ref(false)
    const error = ref(null)
    const selectedStatus = ref('')
    const searchQuery = ref('')
    const sortBy = ref('created_desc')
    const selectedQuote = ref(null)
    const processingQuoteId = ref(null)
    const ordersByQuoteId = ref({})
    const invoicesByOrderNumber = ref({})

    const filteredQuotes = computed(() => {
      const filtered = quotes.value.filter(q => {
        const statusMatch = !selectedStatus.value || q.status === selectedStatus.value
        const term = (searchQuery.value || '').toLowerCase()
        const linkedOrder = getLinkedOrder(q)
        const linkedInvoice = getLinkedInvoice(q)
        const searchMatch = !term
          || String(q.quote_id || '').toLowerCase().includes(term)
          || String(linkedOrder?.order_number || '').toLowerCase().includes(term)
          || String(linkedInvoice?.invoice_number || '').toLowerCase().includes(term)
        return statusMatch && searchMatch
      })

      const sorted = [...filtered]
      switch (sortBy.value) {
        case 'created_asc':
          sorted.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0))
          break
        case 'amount_desc':
          sorted.sort((a, b) => Number(b.total_amount || 0) - Number(a.total_amount || 0))
          break
        case 'amount_asc':
          sorted.sort((a, b) => Number(a.total_amount || 0) - Number(b.total_amount || 0))
          break
        case 'status_asc':
          sorted.sort((a, b) => String(a.status || '').localeCompare(String(b.status || '')))
          break
        case 'created_desc':
        default:
          sorted.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
          break
      }

      return sorted
    })

    const indexBy = (list, key) => {
      return (list || []).reduce((acc, item) => {
        if (item && item[key]) {
          acc[item[key]] = item
        }
        return acc
      }, {})
    }

    const fetchAllPages = async (endpoint, pageSize = 100) => {
      let page = 1
      let lastPage = 1
      const records = []

      // Pull every page so historical records are not truncated on the UI.
      while (page <= lastPage) {
        const response = await axios.get(`${endpoint}?page=${page}&pageSize=${pageSize}`)
        if (!response.data?.success) {
          break
        }

        const pageItems = response.data.data || []
        records.push(...pageItems)

        const pagination = response.data.pagination || {}
        lastPage = Number(pagination.last_page || 1)
        page += 1
      }

      return records
    }

    const fetchQuotes = async () => {
      loading.value = true
      error.value = null
      
      try {
        if (!authStore.isAuthenticated) {
          error.value = 'Authentication required. Please log in to view your quotes.'
          setTimeout(() => router.push({ name: 'login' }), 2000)
          return
        }

        const [allQuotes, allOrders, allInvoices] = await Promise.all([
          fetchAllPages('/api/v1/quotes', 100),
          fetchAllPages('/api/v1/orders', 100),
          fetchAllPages('/api/v1/invoices', 200)
        ])

        if (Array.isArray(allQuotes)) {
          quotes.value = allQuotes

          const orders = Array.isArray(allOrders) ? allOrders : []
          const invoices = Array.isArray(allInvoices) ? allInvoices : []
          ordersByQuoteId.value = indexBy(orders, 'quote_id')
          invoicesByOrderNumber.value = indexBy(invoices, 'order_number')
        } else {
          error.value = 'Failed to load quotes'
        }
      } catch (err) {
        if (err.response?.status === 401) {
          error.value = 'Your session has expired. Please log in again.'
          authStore.logout()
          setTimeout(() => router.push({ name: 'login' }), 2000)
        } else {
          error.value = err.response?.data?.message || err.message || 'Failed to load quotes'
        }
      } finally {
        loading.value = false
      }
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      }).format(date)
    }

    const formatStatus = (status) => {
      if (!status) return 'Unknown'
      return status
        .split('_')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ')
    }

    const canCancelQuote = (quote) => {
      if (!quote) return false
      return ['draft', 'pending_review', 'approved'].includes(quote.status)
    }

    const getLinkedOrder = (quote) => {
      if (!quote?.quote_id) return null
      return ordersByQuoteId.value[quote.quote_id] || null
    }

    const getLinkedInvoice = (quote) => {
      const linkedOrder = getLinkedOrder(quote)
      if (!linkedOrder?.order_number) return null
      return invoicesByOrderNumber.value[linkedOrder.order_number] || null
    }

    const canPayQuote = (quote) => {
      const invoice = getLinkedInvoice(quote)
      if (!invoice) return false
      return invoice.status !== 'paid' && Number(invoice.total_amount || 0) > Number(invoice.paid_amount || 0)
    }

    const getStatusBadge = (status) => {
      const badges = {
        draft: 'bg-gray-100 text-gray-800',
        pending_review: 'bg-[#dbe8fa] text-[#2F5597]',
        approved: 'bg-green-100 text-green-800',
        converted: 'bg-indigo-100 text-indigo-800',
        cancelled: 'bg-red-100 text-red-800',
        rejected: 'bg-red-100 text-red-800',
        expired: 'bg-yellow-100 text-yellow-800',
      }
      return badges[status] || 'bg-gray-100 text-gray-800'
    }

    const formatCurrency = (amount) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount || 0)

    const viewQuote = (quote) => selectedQuote.value = quote

    const downloadQuotePdf = async (quote) => {
      try {
        const response = await axios.get(`/api/v1/quotes/${quote.quote_id}/pdf`, {
          responseType: 'blob'
        })

        const blobUrl = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
        const link = document.createElement('a')
        link.href = blobUrl
        link.setAttribute('download', `quote-${quote.quote_id}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(blobUrl)
      } catch (error) {
        console.error('Error downloading quote PDF:', error)
        toastStore.addToast('Failed to download quote PDF', 'error')
      }
    }

    const viewLinkedOrder = (quote) => {
      const order = getLinkedOrder(quote)
      if (!order) {
        toastStore.addToast('No linked order found for this quote yet.', 'warning')
        return
      }

      searchQuery.value = order.order_number
      selectedStatus.value = ''
      selectedQuote.value = null
      toastStore.addToast(`Showing quote linked to order ${order.order_number}`, 'info')
    }

    const toPaymentUrl = (query) => {
      const params = new URLSearchParams()
      Object.entries(query || {}).forEach(([key, value]) => {
        if (value !== undefined && value !== null && String(value).length > 0) {
          params.set(key, String(value))
        }
      })
      return `/payment?${params.toString()}`
    }

    const navigateToPayment = async (query) => {
      try {
        await router.push({ name: 'payment', query })
        if (router.currentRoute.value?.name !== 'payment') {
          window.location.assign(toPaymentUrl(query))
        }
      } catch (err) {
        console.error('Router payment navigation failed, using hard redirect:', err)
        window.location.assign(toPaymentUrl(query))
      }
    }

    const payQuoteInvoice = async (quote) => {
      const invoice = getLinkedInvoice(quote)
      if (!invoice) {
        toastStore.addToast('No invoice available yet for this quote.', 'warning')
        return
      }

      await navigateToPayment({
        mode: 'quote',
        invoiceNumber: invoice.invoice_number,
        quoteId: quote.quote_id,
        amount: Number(invoice.total_amount || 0),
        from: '/quotes',
      })
    }
    
    const cancelQuote = async (quote) => {
      if (!canCancelQuote(quote)) return

      if (authStore.isRestricted) {
        toastStore.addToast('Account suspended: cancelling quotes is disabled', 'error')
        return
      }

      const reason = window.prompt('Optional cancellation reason (max 500 chars):', 'Cancelled by customer')
      if (reason === null) {
        return
      }

      processingQuoteId.value = quote.quote_id

      try {
        const response = await axios.post(`/api/v1/quotes/${quote.quote_id}/cancel`, {
          reason: reason || 'Cancelled by customer',
        })

        if (response.data?.success) {
          toastStore.addToast('Quote cancelled successfully', 'success')
          if (selectedQuote.value?.quote_id === quote.quote_id) {
            selectedQuote.value = response.data.data
          }
          await fetchQuotes()
        } else {
          toastStore.addToast(response.data?.message || 'Failed to cancel quote', 'error')
        }
      } catch (error) {
        console.error('Error cancelling quote:', error)
        toastStore.addToast(error.response?.data?.message || 'Failed to cancel quote', 'error')
      } finally {
        processingQuoteId.value = null
      }
    }

    const getQuotesCountByStatus = (status) => {
      return quotes.value.filter(quote => quote.status === status).length
    }

    const getQuoteProgress = (status) => {
      const progressMap = {
        draft: 33,
        pending_review: 66,
        approved: 100,
        converted: 100,
        cancelled: 0,
        rejected: 0,
        expired: 0,
      }
      return progressMap[status] || 0
    }

    const resetFilters = () => { 
      selectedStatus.value = ''
      searchQuery.value = ''
      sortBy.value = 'created_desc'
      fetchQuotes()
    }

    onMounted(() => fetchQuotes())

    return { 
      quotes, 
      loading, 
      error, 
      selectedStatus, 
      searchQuery, 
      sortBy,
      selectedQuote, 
      processingQuoteId, 
      ordersByQuoteId,
      invoicesByOrderNumber,
      filteredQuotes, 
      fetchQuotes, 
      formatDate, 
      formatStatus, 
      getStatusBadge, 
      formatCurrency, 
      viewQuote, 
      downloadQuotePdf,
      viewLinkedOrder,
      getLinkedOrder,
      getLinkedInvoice,
      canPayQuote,
      payQuoteInvoice,
      canCancelQuote, 
      cancelQuote, 
      resetFilters,
      getQuotesCountByStatus,
      getQuoteProgress,
    }
  }
}
</script>