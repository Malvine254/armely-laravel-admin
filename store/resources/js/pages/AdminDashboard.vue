<template>
  <AdminLayout>
    <template #title>Dashboard</template>

    <section class="dashboard-surface mb-8 overflow-hidden rounded-3xl p-6 md:p-8">
      <div class="absolute inset-0 pointer-events-none dashboard-orb"></div>
      <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.2em] text-cyan-100/80">Operations Center</p>
          <h2 class="text-3xl md:text-4xl font-semibold text-white mt-1">Store Performance Radar</h2>
          <p class="text-slate-200/90 mt-2 max-w-2xl text-sm md:text-base">
            Live command view of quotes, orders, and invoice pressure for the current business cycle.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <span class="rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs text-white/90">
            Last sync: {{ lastSyncLabel }}
          </span>
          <button
            @click="refreshStats"
            :disabled="refreshing"
            class="rounded-full bg-cyan-300 px-5 py-2 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ refreshing ? 'Refreshing...' : 'Refresh' }}
          </button>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
      <article
        v-for="card in kpiCards"
        :key="card.label"
        class="glass-card rounded-2xl p-5"
      >
        <div class="flex items-center justify-between">
          <p class="text-sm text-slate-300">{{ card.label }}</p>
          <span :class="['rounded-full px-3 py-1 text-[11px] font-semibold', card.pillClass]">{{ card.pill }}</span>
        </div>
        <p class="mt-4 text-3xl font-semibold text-white">{{ card.value }}</p>
        <p class="mt-2 text-xs text-slate-300">{{ card.hint }}</p>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
      <article class="glass-card rounded-2xl p-6 xl:col-span-1">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-white text-lg font-semibold">Invoice Pressure</h3>
          <span class="text-xs text-slate-300">Current snapshot</span>
        </div>

        <div class="flex items-center justify-center py-2">
          <div class="relative size-48">
            <div class="size-48 rounded-full chart-ring" :style="invoiceDonutStyle"></div>
            <div class="absolute inset-4 rounded-full bg-[#081a2f] flex flex-col items-center justify-center">
              <span class="text-xs text-slate-300">Revenue</span>
              <span class="text-xl font-semibold text-cyan-200">${{ formatCurrency(stats.monthly_revenue) }}</span>
            </div>
          </div>
        </div>

        <div class="space-y-3 mt-6">
          <div v-for="bucket in invoiceBuckets" :key="bucket.label" class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2 text-slate-200">
              <span class="size-2.5 rounded-full" :style="{ backgroundColor: bucket.color }"></span>
              {{ bucket.label }}
            </div>
            <span class="font-semibold text-white">{{ bucket.count }}</span>
          </div>
        </div>
      </article>

      <article class="glass-card rounded-2xl p-6 xl:col-span-1">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-white text-lg font-semibold">Quote Funnel</h3>
          <router-link :to="{ name: 'admin-quotes' }" class="text-cyan-300 text-xs hover:text-cyan-200">Manage quotes</router-link>
        </div>

        <div class="space-y-4">
          <div v-for="step in quoteFunnel" :key="step.label">
            <div class="flex items-center justify-between text-sm mb-1">
              <span class="text-slate-200">{{ step.label }}</span>
              <span class="text-white font-semibold">{{ step.count }}</span>
            </div>
            <div class="h-2 rounded-full bg-slate-800/90 overflow-hidden">
              <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-blue-500" :style="{ width: step.width + '%' }"></div>
            </div>
          </div>
        </div>
      </article>

      <article class="glass-card rounded-2xl p-6 xl:col-span-1">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-white text-lg font-semibold">Order Flow</h3>
          <router-link :to="{ name: 'admin-orders' }" class="text-cyan-300 text-xs hover:text-cyan-200">Open orders</router-link>
        </div>

        <div class="space-y-4">
          <div v-for="row in orderPipeline" :key="row.label">
            <div class="flex items-center justify-between text-sm mb-1">
              <span class="text-slate-200">{{ row.label }}</span>
              <span class="text-white font-semibold">{{ row.count }}</span>
            </div>
            <div class="h-2 rounded-full bg-slate-800/90 overflow-hidden">
              <div class="h-full rounded-full" :style="{ width: row.width + '%', background: row.gradient }"></div>
            </div>
          </div>
        </div>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-5 gap-6">
      <article class="glass-card rounded-2xl p-6 xl:col-span-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-white">Recent Pending Quotes</h3>
          <router-link :to="{ name: 'admin-quotes' }" class="text-cyan-300 text-sm hover:text-cyan-200">View all</router-link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full min-w-[680px] text-sm">
            <thead>
              <tr class="text-slate-300 border-b border-white/10">
                <th class="px-3 py-3 text-left font-medium">Quote</th>
                <th class="px-3 py-3 text-left font-medium">Customer</th>
                <th class="px-3 py-3 text-left font-medium">Amount</th>
                <th class="px-3 py-3 text-left font-medium">Submitted</th>
                <th class="px-3 py-3 text-left font-medium">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="pendingQuotes.length === 0" class="border-b border-white/10">
                <td colspan="5" class="px-3 py-8 text-center text-slate-300">No pending quotes right now.</td>
              </tr>
              <tr v-for="quote in pendingQuotes" :key="quote.id" class="border-b border-white/10 hover:bg-white/5 transition-colors">
                <td class="px-3 py-3 text-cyan-300 font-medium">{{ quote.quote_id }}</td>
                <td class="px-3 py-3 text-slate-100">{{ getCompanyName(quote) }}</td>
                <td class="px-3 py-3 text-slate-100">${{ formatCurrency(quote.total_amount) }}</td>
                <td class="px-3 py-3 text-slate-300">{{ formatDate(quote.created_at) }}</td>
                <td class="px-3 py-3">
                  <router-link :to="`/admin/quotes/${quote.id}`" class="text-cyan-300 hover:text-cyan-200 font-medium">Review</router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>

      <article class="glass-card rounded-2xl p-6 xl:col-span-2">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
          <router-link :to="{ name: 'admin-orders' }" class="text-cyan-300 text-sm hover:text-cyan-200">View all</router-link>
        </div>

        <div class="space-y-3">
          <div v-if="recentOrders.length === 0" class="rounded-xl border border-white/10 px-4 py-6 text-slate-300 text-sm text-center">
            No recent orders.
          </div>
          <div v-for="order in recentOrders" :key="order.id" class="rounded-xl border border-white/10 p-4 hover:bg-white/5 transition-colors">
            <div class="flex items-center justify-between gap-2">
              <p class="text-cyan-300 font-medium">{{ order.order_number }}</p>
              <span :class="['px-2.5 py-1 rounded-full text-[11px] font-semibold capitalize', statusBadgeClass(order.status)]">{{ order.status }}</span>
            </div>
            <p class="text-sm text-slate-200 mt-1">{{ getCompanyName(order) }}</p>
            <div class="mt-2 flex items-center justify-between text-xs text-slate-300">
              <span>${{ formatCurrency(order.total_amount) }}</span>
              <span>{{ formatDate(order.created_at) }}</span>
            </div>
          </div>
        </div>
      </article>
    </section>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const stats = ref({
  total_quotes: 0,
  pending_quotes: 0,
  total_orders: 0,
  processing_orders: 0,
  completed_orders: 0,
  monthly_revenue: 0,
  total_customers: 0,
  active_customers: 0,
  pending_invoices: 0,
  overdue_invoices: 0
})

const pendingQuotes = ref([])
const recentOrders = ref([])
const lastUpdated = ref(null)
const refreshing = ref(false)
let refreshTimer = null

const pendingPercentage = computed(() => {
  if (stats.value.total_quotes === 0) return 0
  return Math.round((stats.value.pending_quotes / stats.value.total_quotes) * 100)
})

const approvalRate = computed(() => {
  if (stats.value.total_quotes === 0) return 0
  const approved = Math.max(stats.value.total_quotes - stats.value.pending_quotes, 0)
  return Math.round((approved / stats.value.total_quotes) * 100)
})

const activeCustomerRatio = computed(() => {
  if (stats.value.total_customers === 0) return 0
  return Math.round((stats.value.active_customers / stats.value.total_customers) * 100)
})

const orderPending = computed(() => {
  const pending = stats.value.total_orders - stats.value.processing_orders - stats.value.completed_orders
  return Math.max(pending, 0)
})

const kpiCards = computed(() => [
  {
    label: 'Total Quotes',
    value: stats.value.total_quotes,
    pill: `${pendingPercentage.value}% pending`,
    pillClass: 'bg-amber-400/20 text-amber-200',
    hint: `${approvalRate.value}% approved or resolved`
  },
  {
    label: 'Monthly Revenue',
    value: `$${formatCurrency(stats.value.monthly_revenue)}`,
    pill: 'Revenue pulse',
    pillClass: 'bg-cyan-400/20 text-cyan-200',
    hint: 'Updates with dashboard refresh'
  },
  {
    label: 'Total Orders',
    value: stats.value.total_orders,
    pill: `${stats.value.completed_orders} completed`,
    pillClass: 'bg-emerald-400/20 text-emerald-200',
    hint: `${stats.value.processing_orders} currently processing`
  },
  {
    label: 'Active Customers',
    value: stats.value.active_customers,
    pill: `${activeCustomerRatio.value}% active`,
    pillClass: 'bg-fuchsia-400/20 text-fuchsia-200',
    hint: `${stats.value.total_customers || 0} total customer accounts`
  }
])

const quoteFunnel = computed(() => {
  const approved = Math.max(stats.value.total_quotes - stats.value.pending_quotes, 0)
  const total = Math.max(stats.value.total_quotes, 1)

  return [
    { label: 'Submitted', count: stats.value.total_quotes, width: 100 },
    { label: 'Pending Review', count: stats.value.pending_quotes, width: Math.round((stats.value.pending_quotes / total) * 100) },
    { label: 'Approved', count: approved, width: Math.round((approved / total) * 100) }
  ]
})

const orderPipeline = computed(() => {
  const total = Math.max(stats.value.total_orders, 1)
  return [
    {
      label: 'Pending',
      count: orderPending.value,
      width: Math.round((orderPending.value / total) * 100),
      gradient: 'linear-gradient(90deg, #f59e0b, #fbbf24)'
    },
    {
      label: 'Processing',
      count: stats.value.processing_orders,
      width: Math.round((stats.value.processing_orders / total) * 100),
      gradient: 'linear-gradient(90deg, #0ea5e9, #38bdf8)'
    },
    {
      label: 'Completed',
      count: stats.value.completed_orders,
      width: Math.round((stats.value.completed_orders / total) * 100),
      gradient: 'linear-gradient(90deg, #10b981, #34d399)'
    }
  ]
})

const invoiceBuckets = computed(() => {
  const settled = Math.max(stats.value.total_orders - stats.value.pending_invoices - stats.value.overdue_invoices, 0)
  return [
    { label: 'Settled', count: settled, color: '#22d3ee' },
    { label: 'Pending', count: stats.value.pending_invoices, color: '#facc15' },
    { label: 'Overdue', count: stats.value.overdue_invoices, color: '#fb7185' }
  ]
})

const invoiceDonutStyle = computed(() => {
  const total = Math.max(invoiceBuckets.value.reduce((sum, bucket) => sum + bucket.count, 0), 1)
  const settled = Math.round((invoiceBuckets.value[0].count / total) * 100)
  const pending = Math.round((invoiceBuckets.value[1].count / total) * 100)
  const overdue = Math.max(100 - settled - pending, 0)

  return {
    background: `conic-gradient(#22d3ee 0% ${settled}%, #facc15 ${settled}% ${settled + pending}%, #fb7185 ${settled + pending}% ${settled + pending + overdue}%, #1e293b ${settled + pending + overdue}% 100%)`
  }
})

const lastSyncLabel = computed(() => {
  if (!lastUpdated.value) return 'Waiting for first sync'
  return new Date(lastUpdated.value).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
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

const getCompanyName = (row) => {
  return row?.user?.company?.name || row?.company?.name || 'Unknown company'
}

const statusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-amber-400/20 text-amber-200',
    processing: 'bg-cyan-400/20 text-cyan-200',
    confirmed: 'bg-indigo-400/20 text-indigo-200',
    shipped: 'bg-blue-400/20 text-blue-200',
    delivered: 'bg-emerald-400/20 text-emerald-200',
    cancelled: 'bg-rose-400/20 text-rose-200'
  }
  return classes[status] || 'bg-slate-400/20 text-slate-200'
}

const fetchDashboardData = async () => {
  refreshing.value = true
  try {
    const [statsRes, quotesRes, ordersRes] = await Promise.all([
      api.get('/admin/dashboard/stats'),
      api.get('/admin/quotes/pending?pageSize=5'),
      api.get('/admin/orders?pageSize=5')
    ])

    if (statsRes.data.success) {
      stats.value = statsRes.data.data
    }
    if (quotesRes.data.success) {
      pendingQuotes.value = quotesRes.data.data
    }
    if (ordersRes.data.success) {
      recentOrders.value = ordersRes.data.data
    }
    lastUpdated.value = Date.now()
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  } finally {
    refreshing.value = false
  }
}

const refreshStats = () => {
  fetchDashboardData()
}

onMounted(() => {
  fetchDashboardData()
  refreshTimer = setInterval(fetchDashboardData, 60000)
})

onUnmounted(() => {
  if (refreshTimer) {
    clearInterval(refreshTimer)
  }
})
</script>

<style scoped>
.dashboard-surface {
  position: relative;
  background: radial-gradient(circle at 88% 18%, rgba(56, 189, 248, 0.25), transparent 34%),
    linear-gradient(135deg, #0f172a 0%, #0a2948 45%, #1d4c6e 100%);
  border: 1px solid rgba(148, 163, 184, 0.18);
}

.dashboard-orb::before,
.dashboard-orb::after {
  content: '';
  position: absolute;
  border-radius: 9999px;
  filter: blur(50px);
}

.dashboard-orb::before {
  width: 240px;
  height: 240px;
  top: -90px;
  right: -70px;
  background: rgba(34, 211, 238, 0.25);
}

.dashboard-orb::after {
  width: 180px;
  height: 180px;
  bottom: -80px;
  left: -40px;
  background: rgba(37, 99, 235, 0.2);
}

.glass-card {
  background: linear-gradient(180deg, rgba(9, 20, 38, 0.88), rgba(8, 16, 29, 0.88));
  border: 1px solid rgba(148, 163, 184, 0.18);
  box-shadow: 0 16px 45px rgba(2, 6, 23, 0.35);
}

.chart-ring {
  box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.2), 0 0 30px rgba(56, 189, 248, 0.1);
}
</style>
