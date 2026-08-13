<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
        <p class="text-gray-600 text-lg">Manage quotes, orders, and customers</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Quotes Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-600 mb-1">Total Quotes</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.totalQuotes }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z"></path>
              </svg>
            </div>
          </div>
          <p class="text-sm text-orange-600 font-semibold mt-2">{{ stats.pendingQuotes }} pending</p>
        </div>

        <!-- Total Orders Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-600 mb-1">Total Orders</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.totalOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
          </div>
          <p class="text-sm text-purple-600 font-semibold mt-2">{{ stats.processingOrders }} processing</p>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-600 mb-1">Revenue (This Month)</p>
              <p class="text-3xl font-bold text-gray-900">${{ formatCurrency(stats.monthlyRevenue) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <p class="text-sm text-green-600 font-semibold mt-2">↑ 12% from last month</p>
        </div>

        <!-- Active Customers Card -->
        <div class="bg-white rounded-lg shadow-lg p-6">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm text-gray-600 mb-1">Active Customers</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.activeCustomers }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM16 11h5M1 20h5v-2a3 3 0 015.856-1.487M13 10a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="mb-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="rounded-lg border border-blue-100 bg-white p-6 shadow-lg">
          <div class="mb-4 flex items-start justify-between">
            <div>
              <p class="text-sm font-semibold text-[#2F5597]">Lifecycle Campaign Metrics</p>
              <h3 class="text-xl font-bold text-gray-900">Last {{ lifecycleMetrics.windowDays }} Days</h3>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ lifecycleMetrics.totals.sent }} sent</span>
          </div>

          <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-lg bg-gray-50 px-3 py-2">
              <p class="text-xs uppercase tracking-wide text-gray-500">Unsubscribes</p>
              <p class="text-lg font-bold text-gray-900">{{ lifecycleMetrics.totals.unsubscribes }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 px-3 py-2">
              <p class="text-xs uppercase tracking-wide text-gray-500">Idempotency Markers</p>
              <p class="text-lg font-bold text-gray-900">{{ lifecycleMetrics.totals.idempotencyMarkers }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 px-3 py-2">
              <p class="text-xs uppercase tracking-wide text-gray-500">Active Price Alerts</p>
              <p class="text-lg font-bold text-gray-900">{{ lifecycleMetrics.activeSubscriptions.priceAlerts }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 px-3 py-2">
              <p class="text-xs uppercase tracking-wide text-gray-500">Active Reminders</p>
              <p class="text-lg font-bold text-gray-900">{{ lifecycleMetrics.activeSubscriptions.totalReminders }}</p>
            </div>
          </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-lg">
          <div class="mb-4 flex items-start justify-between">
            <div>
              <p class="text-sm font-semibold text-slate-700">Runtime Health</p>
              <h3 class="text-xl font-bold text-gray-900">Automation Watch</h3>
            </div>
            <span :class="runtimeHealth.overallStatus === 'healthy' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'" class="rounded-full px-3 py-1 text-xs font-bold">{{ runtimeHealth.overallStatus }}</span>
          </div>

          <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-lg bg-gray-50 px-3 py-2">
              <p class="text-xs uppercase tracking-wide text-gray-500">Queue Pending</p>
              <p class="text-lg font-bold text-gray-900">{{ runtimeHealth.queuePending }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 px-3 py-2">
              <p class="text-xs uppercase tracking-wide text-gray-500">Queue Failed</p>
              <p class="text-lg font-bold text-gray-900">{{ runtimeHealth.queueFailed }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 px-3 py-2">
              <p class="text-xs uppercase tracking-wide text-gray-500">Price Job Lag</p>
              <p class="text-lg font-bold text-gray-900">{{ runtimeHealth.priceDropLag }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 px-3 py-2">
              <p class="text-xs uppercase tracking-wide text-gray-500">Reminder Job Lag</p>
              <p class="text-lg font-bold text-gray-900">{{ runtimeHealth.reminderLag }}</p>
            </div>
          </div>
        </section>
      </div>

      <!-- Tabs -->
      <div class="mb-6">
        <div class="flex gap-4 border-b border-gray-200">
          <button 
            @click="activeTab = 'pending-quotes'"
            :class="[
              'px-4 py-3 font-semibold border-b-2 transition',
              activeTab === 'pending-quotes' 
                ? 'text-blue-600 border-blue-600' 
                : 'text-gray-600 border-transparent hover:text-gray-900'
            ]"
          >
            Pending Quotes ({{ stats.pendingQuotes }})
          </button>
          <button 
            @click="activeTab = 'recent-orders'"
            :class="[
              'px-4 py-3 font-semibold border-b-2 transition',
              activeTab === 'recent-orders' 
                ? 'text-blue-600 border-blue-600' 
                : 'text-gray-600 border-transparent hover:text-gray-900'
            ]"
          >
            Recent Orders
          </button>
        </div>
      </div>

      <!-- Pending Quotes Tab -->
      <div v-if="activeTab === 'pending-quotes'" class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Pending Quote Reviews</h3>
        </div>
        <div v-if="pendingQuotes.length === 0" class="px-6 py-9 text-center">
          <p class="text-gray-600">No pending quotes to review</p>
        </div>
        <table v-else class="min-w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Quote ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Customer</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Company</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Amount</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Items</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="quote in pendingQuotes" :key="quote.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ quote.quote_id }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ quote.user?.name || 'Unknown' }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ quote.user?.company?.name || 'N/A' }}</td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ formatCurrency(quote.total_amount) }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ quote.items?.length || 0 }} items</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(quote.created_at) }}</td>
              <td class="px-6 py-4 text-sm">
                <div class="flex items-center gap-2">
                  <button
                    @click="approveQuote(quote)"
                    :disabled="processingQuoteId === quote.id"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background-color:#16a34a;"
                  >
                    {{ processingQuoteId === quote.id ? 'Approving...' : 'Approve' }}
                  </button>
                  <button
                    @click="rejectQuote(quote)"
                    :disabled="processingQuoteId === quote.id"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background-color:#dc2626;"
                  >
                    Reject
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Recent Orders Tab -->
      <div v-if="activeTab === 'recent-orders'" class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
        </div>
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
          <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="rounded-lg bg-white border border-gray-200 px-3 py-2">
              <p class="text-[11px] uppercase tracking-wide text-gray-500">Pending</p>
              <p class="text-lg font-bold text-yellow-700">{{ categorizedOrders.pending.length }}</p>
            </div>
            <div class="rounded-lg bg-white border border-gray-200 px-3 py-2">
              <p class="text-[11px] uppercase tracking-wide text-gray-500">Processing</p>
              <p class="text-lg font-bold text-blue-700">{{ categorizedOrders.processing.length }}</p>
            </div>
            <div class="rounded-lg bg-white border border-gray-200 px-3 py-2">
              <p class="text-[11px] uppercase tracking-wide text-gray-500">Shipped</p>
              <p class="text-lg font-bold text-purple-700">{{ categorizedOrders.shipped.length }}</p>
            </div>
            <div class="rounded-lg bg-white border border-gray-200 px-3 py-2">
              <p class="text-[11px] uppercase tracking-wide text-gray-500">Delivered</p>
              <p class="text-lg font-bold text-green-700">{{ categorizedOrders.delivered.length }}</p>
            </div>
            <div class="rounded-lg bg-white border border-gray-200 px-3 py-2">
              <p class="text-[11px] uppercase tracking-wide text-gray-500">Cancelled</p>
              <p class="text-lg font-bold text-red-700">{{ categorizedOrders.cancelled.length }}</p>
            </div>
          </div>
        </div>
        <div v-if="recentOrders.length === 0" class="px-6 py-9 text-center">
          <p class="text-gray-600">No recent orders</p>
        </div>
        <table v-else class="min-w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Order Number</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Customer</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Primary Item</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Amount</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Payment</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ order.order_number }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ order.user?.name || 'Unknown' }}</td>
              <td class="px-6 py-4 text-sm text-gray-800 max-w-[320px] truncate" :title="resolvePrimaryItemName(order)">
                {{ truncateText(resolvePrimaryItemName(order), 58) }}
              </td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ formatCurrency(order.total_amount) }}</td>
              <td class="px-6 py-4 text-sm">
                <span :class="paymentBadge(order)" class="px-3 py-1 rounded-full text-xs font-semibold">
                  {{ normalizePaymentStatus(order) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm">
                <span :class="getStatusBadge(order.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                  {{ formatStatus(order.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(order.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'
import Navbar from '../../components/Navbar.vue'
import axios from 'axios'

const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()
const activeTab = ref('pending-quotes')
const processingQuoteId = ref(null)

const stats = ref({
  totalQuotes: 0,
  pendingQuotes: 0,
  totalOrders: 0,
  processingOrders: 0,
  monthlyRevenue: 0,
  activeCustomers: 0
})

const pendingQuotes = ref([])
const recentOrders = ref([])
const lifecycleMetrics = ref({
  windowDays: 14,
  totals: {
    sent: 0,
    unsubscribes: 0,
    idempotencyMarkers: 0,
  },
  activeSubscriptions: {
    priceAlerts: 0,
    totalReminders: 0,
  },
})
const runtimeHealth = ref({
  overallStatus: 'unknown',
  queuePending: 0,
  queueFailed: 0,
  priceDropLag: 'n/a',
  reminderLag: 'n/a',
})

const categorizedOrders = computed(() => {
  const byStatus = {
    pending: [],
    processing: [],
    shipped: [],
    delivered: [],
    cancelled: [],
  }

  recentOrders.value.forEach((order) => {
    const key = String(order?.status || '').toLowerCase()
    if (Object.prototype.hasOwnProperty.call(byStatus, key)) {
      byStatus[key].push(order)
    }
  })

  return byStatus
})

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const formatStatus = (status) => status.charAt(0).toUpperCase() + status.slice(1)

const truncateText = (value, maxLength = 56) => {
  const text = String(value || 'Item details unavailable').trim()
  if (text.length <= maxLength) return text
  return `${text.slice(0, Math.max(1, maxLength - 1)).trimEnd()}…`
}

const resolvePrimaryItemName = (order) => {
  const direct = String(order?.primary_item_name || '').trim()
  if (direct) return direct

  const items = Array.isArray(order?.items) ? order.items : []
  if (!items.length) return 'Item details unavailable'

  const first = items[0] || {}
  const fallback = String(
    first.product_name
    || first.productName
    || first.partDescription
    || first.description
    || first.name
    || first.sku
    || first.partNumber
    || 'Item details unavailable'
  ).trim()

  return fallback || 'Item details unavailable'
}

const normalizePaymentStatus = (order) => {
  const status = String(order?.payment_status || '').trim().toLowerCase()
  if (status === 'completed') return 'Completed'
  if (status === 'paid') return 'Paid'
  if (!status) return 'Pending'
  return status.charAt(0).toUpperCase() + status.slice(1)
}

const paymentBadge = (order) => {
  const status = String(order?.payment_status || '').trim().toLowerCase()
  if (status === 'completed' || status === 'paid') return 'bg-green-100 text-green-800'
  return 'bg-yellow-100 text-yellow-800'
}

const getStatusBadge = (status) => {
  const badges = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return badges[status] || 'bg-gray-100 text-gray-800'
}

const loadDashboardData = async () => {
  try {
    const [statsRes, quotesRes, ordersRes, lifecycleRes, healthRes] = await Promise.all([
      axios.get('/api/v1/admin/dashboard/stats'),
      axios.get('/api/v1/admin/quotes/pending?pageSize=100'),
      axios.get('/api/v1/admin/orders?pageSize=25'),
      axios.get('/api/v1/admin/settings/lifecycle/metrics?days=14'),
      axios.get('/api/v1/admin/settings/runtime/health'),
    ])

    if (statsRes.data?.success) {
      const data = statsRes.data.data || {}
      stats.value.totalQuotes = Number(data.total_quotes || 0)
      stats.value.pendingQuotes = Number(data.pending_quotes || 0)
      stats.value.totalOrders = Number(data.total_orders || 0)
      stats.value.processingOrders = Number(data.processing_orders || 0)
      stats.value.monthlyRevenue = Number(data.monthly_revenue || 0)
      stats.value.activeCustomers = Number(data.active_customers || 0)
    }

    if (quotesRes.data?.success) {
      pendingQuotes.value = Array.isArray(quotesRes.data.data) ? quotesRes.data.data : []
    }

    if (ordersRes.data?.success) {
      recentOrders.value = Array.isArray(ordersRes.data.data) ? ordersRes.data.data : []
    }

    if (lifecycleRes.data?.success) {
      const data = lifecycleRes.data.data || {}
      const active = data.active_subscriptions || {}
      const totals = data.totals || {}
      lifecycleMetrics.value = {
        windowDays: Number(data.window_days || 14),
        totals: {
          sent: Number(totals.sent || 0),
          unsubscribes: Number(totals.unsubscribes || 0),
          idempotencyMarkers: Number(totals.idempotency_markers || 0),
        },
        activeSubscriptions: {
          priceAlerts: Number(active.price_alerts || 0),
          totalReminders: Number(active.abandoned_cart || 0) + Number(active.viewed_product || 0) + Number(active.favorite_product || 0),
        },
      }
    }

    if (healthRes.data?.success) {
      const data = healthRes.data.data || {}
      const queue = data.queue || {}
      const scheduler = data.scheduler || {}

      runtimeHealth.value = {
        overallStatus: String(data.overall_status || 'unknown'),
        queuePending: Number(queue.pending_jobs ?? 0),
        queueFailed: Number(queue.failed_jobs ?? 0),
        priceDropLag: Number.isFinite(Number(scheduler.price_drop_lag_minutes)) ? `${Number(scheduler.price_drop_lag_minutes)}m` : 'n/a',
        reminderLag: Number.isFinite(Number(scheduler.reminders_lag_minutes)) ? `${Number(scheduler.reminders_lag_minutes)}m` : 'n/a',
      }
    }
  } catch (error) {
    console.error('Error loading dashboard:', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to load admin dashboard data', 'error')
  }
}

const approveQuote = async (quote) => {
  if (!quote?.id) return

  processingQuoteId.value = quote.id
  try {
    const response = await axios.post(`/api/v1/admin/quotes/${quote.id}/approve`, {
      admin_notes: 'Approved via admin dashboard',
    })

    if (response.data?.success) {
      toastStore.addToast(`Quote ${quote.quote_id} approved`, 'success')
      await loadDashboardData()
      router.push({ name: 'admin-orders' })
      return
    }

    toastStore.addToast(response.data?.message || 'Failed to approve quote', 'error')
  } catch (error) {
    toastStore.addToast(error.response?.data?.message || 'Failed to approve quote', 'error')
  } finally {
    processingQuoteId.value = null
  }
}

const rejectQuote = async (quote) => {
  if (!quote?.id) return

  const reason = window.prompt('Enter rejection reason', 'Rejected by admin')
  if (reason === null) return

  processingQuoteId.value = quote.id
  try {
    const response = await axios.post(`/api/v1/admin/quotes/${quote.id}/reject`, {
      reason: reason || 'Rejected by admin',
    })

    if (response.data?.success) {
      toastStore.addToast(`Quote ${quote.quote_id} rejected`, 'success')
      await loadDashboardData()
      return
    }

    toastStore.addToast(response.data?.message || 'Failed to reject quote', 'error')
  } catch (error) {
    toastStore.addToast(error.response?.data?.message || 'Failed to reject quote', 'error')
  } finally {
    processingQuoteId.value = null
  }
}

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login' })
    return
  }
  loadDashboardData()
})
</script>
