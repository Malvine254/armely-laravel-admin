<template>
  <AdminLayout>
    <template #title>Dashboard</template>

    <div class="mb-5 flex flex-wrap items-center justify-end gap-3">
      <span class="text-xs text-slate-500">Updated {{ lastSyncLabel }}</span>
      <button
        @click="refreshStats"
        :disabled="refreshing"
        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-[#2F5597] shadow-sm transition hover:border-blue-200 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-60"
      >
        <svg class="h-4 w-4" :class="refreshing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9M20 20v-5h-.581m-15.357-2A8.001 8.001 0 0019.418 15" />
        </svg>
        {{ refreshing ? 'Refreshing' : 'Refresh data' }}
      </button>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
      <article
        v-for="card in kpiCards"
        :key="card.label"
        class="glass-card rounded-2xl p-5"
      >
        <div class="flex items-center justify-between">
          <p class="text-xs text-gray-500">{{ card.label }}</p>
          <span :class="['rounded-full px-3 py-1 text-[11px] font-semibold', card.pillClass]">{{ card.pill }}</span>
        </div>
        <p class="mt-4 text-2xl font-semibold text-gray-900">{{ card.value }}</p>
        <p class="mt-2 text-xs text-gray-500">{{ card.hint }}</p>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
      <article class="glass-card rounded-2xl p-6 xl:col-span-2">
        <div class="flex items-center justify-between mb-5">
          <div>
            <h3 class="text-gray-900 text-base font-semibold">Recent Order Value</h3>
            <p class="mt-1 text-xs text-gray-500">Daily order value from the latest activity</p>
          </div>
          <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-[#2F5597]">USD</span>
        </div>
        <div class="h-72">
          <Line :data="orderTrendData" :options="lineChartOptions" />
        </div>
      </article>

      <article class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
          <div>
            <h3 class="text-gray-900 text-base font-semibold">Order Status</h3>
            <p class="mt-1 text-xs text-gray-500">Current fulfillment mix</p>
          </div>
          <router-link :to="{ name: 'admin-orders' }" class="text-[#2F5597] text-xs hover:underline">View orders</router-link>
        </div>
        <div class="mx-auto h-72 max-w-xs">
          <Doughnut :data="orderStatusData" :options="doughnutOptions" />
        </div>
      </article>

      <article class="glass-card rounded-2xl p-6 xl:col-span-3">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-gray-900 text-base font-semibold">Quote Funnel</h3>
          <router-link :to="{ name: 'admin-quotes' }" class="text-[#2F5597] text-xs hover:text-[#2F5597]">Manage quotes</router-link>
        </div>
        <div class="h-64">
          <Bar :data="quotePipelineData" :options="barChartOptions" />
        </div>
      </article>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-5 gap-6">
      <article class="glass-card rounded-2xl p-6 xl:col-span-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-base font-semibold text-gray-900">Recent Pending Quotes</h3>
          <router-link :to="{ name: 'admin-quotes' }" class="text-[#2F5597] text-sm hover:text-[#2F5597]">View all</router-link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full min-w-[900px] text-sm">
            <thead>
              <tr class="text-gray-500 border-b border-gray-200">
                <th class="px-3 py-3 text-left font-medium">Quote</th>
                <th class="px-3 py-3 text-left font-medium">Customer</th>
                <th class="px-3 py-3 text-left font-medium">Amount</th>
                <th class="px-3 py-3 text-left font-medium">Submitted</th>
                <th class="px-3 py-3 text-left font-medium">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="pendingQuotes.length === 0" class="border-b border-gray-200">
                <td colspan="5" class="px-3 py-8 text-center text-gray-500">No pending quotes right now.</td>
              </tr>
              <tr v-for="quote in pendingQuotes" :key="quote.id" class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                <td class="px-3 py-3 text-[#2F5597] font-medium">{{ quote.quote_id }}</td>
                <td class="px-3 py-3 text-gray-900">{{ getCompanyName(quote) }}</td>
                <td class="px-3 py-3 text-gray-900">${{ formatCurrency(quote.total_amount) }}</td>
                <td class="px-3 py-3 text-gray-500">{{ formatDate(quote.created_at) }}</td>
                <td class="px-3 py-3">
                  <router-link :to="`/admin/quotes/${quote.id}`" class="text-[#2F5597] hover:text-[#2F5597] font-medium">Review</router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>

      <article class="glass-card rounded-2xl p-6 xl:col-span-2">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-base font-semibold text-gray-900">Recent Orders</h3>
          <router-link :to="{ name: 'admin-orders' }" class="text-[#2F5597] text-sm hover:text-[#2F5597]">View all</router-link>
        </div>

        <div class="space-y-3">
          <div v-if="recentOrders.length === 0" class="rounded-xl border border-gray-200 px-4 py-6 text-gray-500 text-sm text-center">
            No recent orders.
          </div>
          <div v-for="order in recentOrders.slice(0, 5)" :key="order.id" class="rounded-xl border border-gray-200 p-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between gap-2">
              <p class="text-[#2F5597] font-medium">{{ order.order_number }}</p>
              <span :class="['px-2.5 py-1 rounded-full text-[11px] font-semibold capitalize', statusBadgeClass(order.status)]">{{ order.status }}</span>
            </div>
            <p class="text-sm text-gray-700 mt-1">{{ getCompanyName(order) }}</p>
            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
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
import { Bar, Doughnut, Line } from 'vue-chartjs'
import {
  ArcElement,
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Filler,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  Title,
  Tooltip,
} from 'chart.js'

ChartJS.register(
  ArcElement,
  BarElement,
  CategoryScale,
  Filler,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  Title,
  Tooltip,
)

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

const orderTrendData = computed(() => {
  const grouped = new Map()
  ;[...recentOrders.value].reverse().forEach((order) => {
    const date = new Date(order.created_at)
    if (Number.isNaN(date.getTime())) return
    const key = date.toISOString().slice(0, 10)
    grouped.set(key, (grouped.get(key) || 0) + Number(order.total_amount || 0))
  })

  const entries = [...grouped.entries()].slice(-14)
  return {
    labels: entries.map(([date]) => new Date(`${date}T12:00:00`).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
    datasets: [{
      label: 'Order value',
      data: entries.map(([, value]) => value),
      borderColor: '#2F5597',
      backgroundColor: 'rgba(47, 85, 151, 0.12)',
      pointBackgroundColor: '#ffffff',
      pointBorderColor: '#2F5597',
      pointBorderWidth: 2,
      pointRadius: 3,
      pointHoverRadius: 5,
      borderWidth: 3,
      tension: 0.38,
      fill: true,
    }],
  }
})

const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: { intersect: false, mode: 'index' },
  plugins: {
    legend: { display: false },
    tooltip: { callbacks: { label: (context) => ` $${formatCurrency(context.parsed.y)}` } },
  },
  scales: {
    x: { grid: { display: false }, border: { display: false }, ticks: { color: '#64748b' } },
    y: {
      beginAtZero: true,
      border: { display: false },
      grid: { color: 'rgba(148, 163, 184, 0.16)' },
      ticks: { color: '#64748b', callback: (value) => `$${Number(value).toLocaleString('en-US')}` },
    },
  },
}

const orderStatusData = computed(() => {
  const counts = stats.value.order_status_counts || {}
  return {
    labels: ['Pending', 'In progress', 'Invoiced', 'Delivered'],
    datasets: [{
      data: [
        Number(counts.pending ?? orderPending.value),
        Number(counts.in_progress ?? stats.value.processing_orders),
        Number(counts.invoiced || 0),
        Number(counts.delivered ?? stats.value.completed_orders),
      ],
      backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#2F5597'],
      borderColor: '#ffffff',
      borderWidth: 4,
      hoverOffset: 6,
    }],
  }
})

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '68%',
  plugins: {
    legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, padding: 16, color: '#475569' } },
  },
}

const quotePipelineData = computed(() => {
  const resolved = Math.max(stats.value.total_quotes - stats.value.pending_quotes, 0)
  return {
    labels: ['Submitted', 'Pending review', 'Resolved'],
    datasets: [{
      label: 'Quotes',
      data: [stats.value.total_quotes, stats.value.pending_quotes, resolved],
      backgroundColor: ['#2F5597', '#f59e0b', '#10b981'],
      borderRadius: 8,
      borderSkipped: false,
      maxBarThickness: 54,
    }],
  }
})

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: { grid: { display: false }, border: { display: false }, ticks: { color: '#475569' } },
    y: { beginAtZero: true, border: { display: false }, grid: { color: 'rgba(148, 163, 184, 0.16)' }, ticks: { precision: 0, color: '#64748b' } },
  },
}

const kpiCards = computed(() => [
  {
    label: 'Total Quotes',
    value: stats.value.total_quotes,
    pill: `${pendingPercentage.value}% pending`,
    pillClass: 'bg-amber-100 text-amber-800',
    hint: `${approvalRate.value}% approved or resolved`
  },
  {
    label: 'Monthly Revenue',
    value: `$${formatCurrency(stats.value.monthly_revenue)}`,
    pill: 'Revenue pulse',
    pillClass: 'bg-[#2F5597]/10 text-[#2F5597]',
    hint: 'Updates with dashboard refresh'
  },
  {
    label: 'Total Orders',
    value: stats.value.total_orders,
    pill: `${stats.value.completed_orders} completed`,
    pillClass: 'bg-emerald-100 text-emerald-800',
    hint: `${stats.value.processing_orders} currently processing`
  },
  {
    label: 'Active Customers',
    value: stats.value.active_customers,
    pill: `${activeCustomerRatio.value}% active`,
    pillClass: 'bg-fuchsia-100 text-fuchsia-800',
    hint: `${stats.value.total_customers || 0} total customer accounts`
  }
])

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
    pending: 'bg-amber-100 text-amber-800',
    processing: 'bg-[#2F5597]/10 text-[#2F5597]',
    confirmed: 'bg-indigo-100 text-indigo-800',
    shipped: 'bg-blue-100 text-blue-800',
    delivered: 'bg-emerald-100 text-emerald-800',
    cancelled: 'bg-rose-100 text-rose-700'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const fetchDashboardData = async () => {
  refreshing.value = true
  try {
    const [statsRes, quotesRes, ordersRes] = await Promise.all([
      api.get('/admin/dashboard/stats'),
      api.get('/admin/quotes/pending?pageSize=5'),
      api.get('/admin/orders?pageSize=30')
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
.glass-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
  transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.glass-card:hover {
  box-shadow: 0 8px 24px rgba(47, 85, 151, 0.1), 0 2px 6px rgba(0, 0, 0, 0.06);
  transform: translateY(-1px);
}

</style>
