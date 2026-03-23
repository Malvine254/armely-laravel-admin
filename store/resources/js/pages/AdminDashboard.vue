<template>
  <AdminLayout>
    <template #title>Dashboard</template>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Quotes -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Quotes</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.total_quotes }}</p>
            <p class="text-green-600 text-xs mt-2">
              <i class="fas fa-arrow-up"></i> {{ pending_percentage }}% pending
            </p>
          </div>
          <div class="bg-blue-100 p-3 rounded-lg">
            <i class="fas fa-file-invoice text-blue-600 text-2xl"></i>
          </div>
        </div>
      </div>

      <!-- Pending Quotes -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm font-medium">Pending Quotes</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.pending_quotes }}</p>
            <p class="text-red-600 text-xs mt-2">
              <i class="fas fa-exclamation-circle"></i> Awaiting review
            </p>
          </div>
          <div class="bg-yellow-100 p-3 rounded-lg">
            <i class="fas fa-hourglass-half text-yellow-600 text-2xl"></i>
          </div>
        </div>
      </div>

      <!-- Total Orders -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-600">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm font-medium">Total Orders</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.total_orders }}</p>
            <p class="text-green-600 text-xs mt-2">
              <i class="fas fa-check-circle"></i> {{ stats.completed_orders }} completed
            </p>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <i class="fas fa-shopping-cart text-green-600 text-2xl"></i>
          </div>
        </div>
      </div>

      <!-- Monthly Revenue -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-600">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm font-medium">Monthly Revenue</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">${{ formatCurrency(stats.monthly_revenue) }}</p>
            <p class="text-blue-600 text-xs mt-2">
              <i class="fas fa-arrow-up"></i> This month
            </p>
          </div>
          <div class="bg-purple-100 p-3 rounded-lg">
            <i class="fas fa-dollar-sign text-purple-600 text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Second Row of KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Processing Orders -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm font-medium">Processing Orders</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.processing_orders }}</p>
            <p class="text-orange-600 text-xs mt-2">
              <i class="fas fa-clock"></i> In progress
            </p>
          </div>
          <div class="bg-orange-100 p-3 rounded-lg">
            <i class="fas fa-spinner text-orange-600 text-2xl"></i>
          </div>
        </div>
      </div>

      <!-- Active Customers -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-600">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm font-medium">Active Customers</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.active_customers }}</p>
            <p class="text-indigo-600 text-xs mt-2">
              <i class="fas fa-users"></i> Approved accounts
            </p>
          </div>
          <div class="bg-indigo-100 p-3 rounded-lg">
            <i class="fas fa-user-check text-indigo-600 text-2xl"></i>
          </div>
        </div>
      </div>

      <!-- Pending Invoices -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-600">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm font-medium">Pending Invoices</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.pending_invoices }}</p>
            <p class="text-red-600 text-xs mt-2">
              <i class="fas fa-alert-circle"></i> Awaiting payment
            </p>
          </div>
          <div class="bg-red-100 p-3 rounded-lg">
            <i class="fas fa-file-invoice-dollar text-red-600 text-2xl"></i>
          </div>
        </div>
      </div>

      <!-- Overdue Invoices -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-pink-600">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm font-medium">Overdue Invoices</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ stats.overdue_invoices }}</p>
            <p class="text-pink-600 text-xs mt-2">
              <i class="fas fa-exclamation-triangle"></i> Past due
            </p>
          </div>
          <div class="bg-pink-100 p-3 rounded-lg">
            <i class="fas fa-calendar-times text-pink-600 text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Recent Pending Quotes -->
      <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-800">Recent Pending Quotes</h3>
          <router-link to="/admin/quotes/pending" class="text-blue-600 text-sm font-medium hover:underline">
            View All
          </router-link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full min-w-[720px] text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Quote ID</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Customer</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Amount</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Submitted</th>
                <th class="px-4 py-2 text-center font-semibold text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="pendingQuotes.length === 0" class="border-b border-gray-200 hover:bg-gray-50">
                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                  <i class="fas fa-inbox text-2xl mb-2"></i>
                  <p>No pending quotes</p>
                </td>
              </tr>
              <tr v-for="quote in pendingQuotes" :key="quote.id" class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-blue-600">{{ quote.quote_id }}</td>
                <td class="px-4 py-3">{{ getCompanyName(quote) }}</td>
                <td class="px-4 py-3">${{ formatCurrency(quote.total_amount) }}</td>
                <td class="px-4 py-3 text-gray-600">{{ formatDate(quote.created_at) }}</td>
                <td class="px-4 py-3 text-center">
                  <router-link :to="`/admin/quotes/${quote.id}`" class="text-blue-600 hover:text-blue-900 font-medium">
                    Review
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="bg-gradient-to-br from-[#2f5597] to-[#244477] rounded-lg shadow p-6 text-white">
        <h3 class="text-lg font-bold mb-6">System Status</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <span class="flex items-center">
              <i class="fas fa-database mr-2"></i> Database
            </span>
            <span class="bg-green-400 text-green-900 px-3 py-1 rounded-full text-xs font-semibold">Healthy</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="flex items-center">
              <i class="fas fa-server mr-2"></i> API Server
            </span>
            <span class="bg-green-400 text-green-900 px-3 py-1 rounded-full text-xs font-semibold">Running</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="flex items-center">
              <i class="fas fa-envelope mr-2"></i> Email Queue
            </span>
            <span class="bg-green-400 text-green-900 px-3 py-1 rounded-full text-xs font-semibold">Active</span>
          </div>
          <div class="border-t border-[#2f5597]/70 pt-4 mt-4">
            <p class="text-xs text-[#d7e3f4] mb-3">Last Updated: {{ new Date().toLocaleTimeString() }}</p>
            <button @click="refreshStats" class="w-full bg-white text-[#2f5597] py-2 rounded font-semibold hover:bg-[#edf3fb] transition">
              Refresh Stats
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Recent Orders</h3>
        <router-link to="/admin/orders" class="text-blue-600 text-sm font-medium hover:underline">
          View All
        </router-link>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-2 text-left font-semibold text-gray-700">Order ID</th>
              <th class="px-4 py-2 text-left font-semibold text-gray-700">Customer</th>
              <th class="px-4 py-2 text-left font-semibold text-gray-700">Amount</th>
              <th class="px-4 py-2 text-left font-semibold text-gray-700">Status</th>
              <th class="px-4 py-2 text-left font-semibold text-gray-700">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="recentOrders.length === 0" class="border-b border-gray-200 hover:bg-gray-50">
              <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                <p>No recent orders</p>
              </td>
            </tr>
            <tr v-for="order in recentOrders" :key="order.id" class="border-b border-gray-200 hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-blue-600">{{ order.order_number }}</td>
              <td class="px-4 py-3">{{ getCompanyName(order) }}</td>
              <td class="px-4 py-3">${{ formatCurrency(order.total_amount) }}</td>
              <td class="px-4 py-3">
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusBadgeClass(order.status)]">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-600">{{ formatDate(order.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
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

const pending_percentage = computed(() => {
  if (stats.value.total_quotes === 0) return 0
  return Math.round((stats.value.pending_quotes / stats.value.total_quotes) * 100)
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
    'pending': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'confirmed': 'bg-indigo-100 text-indigo-800',
    'shipped': 'bg-purple-100 text-purple-800',
    'delivered': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const fetchDashboardData = async () => {
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
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  }
}

const refreshStats = () => {
  fetchDashboardData()
}

onMounted(() => {
  fetchDashboardData()
  // Refresh every 60 seconds
  setInterval(fetchDashboardData, 60000)
})
</script>
