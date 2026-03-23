<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
        <div v-if="pendingQuotes.length === 0" class="px-6 py-12 text-center">
          <p class="text-gray-600">No pending quotes to review</p>
        </div>
        <table v-else class="min-w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Quote ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Customer</th>
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
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ formatCurrency(quote.total_amount) }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ quote.items?.length || 0 }} items</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(quote.created_at) }}</td>
              <td class="px-6 py-4 text-sm">
                <button class="text-blue-600 hover:text-blue-900 font-semibold">Review</button>
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
        <div v-if="recentOrders.length === 0" class="px-6 py-12 text-center">
          <p class="text-gray-600">No recent orders</p>
        </div>
        <table v-else class="min-w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Order Number</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Customer</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Amount</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ order.order_number }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ order.user?.name || 'Unknown' }}</td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ formatCurrency(order.total_amount) }}</td>
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import Navbar from '../../components/Navbar.vue'
import axios from 'axios'

const router = useRouter()
const authStore = useAuthStore()
const activeTab = ref('pending-quotes')

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

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const formatStatus = (status) => status.charAt(0).toUpperCase() + status.slice(1)

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
    // Load pending quotes
    const quotesRes = await axios.get('/api/v1/quotes?status=draft&pageSize=100')
    if (quotesRes.data?.success) {
      pendingQuotes.value = quotesRes.data.data
      stats.value.pendingQuotes = quotesRes.data.pagination?.total || 0
    }

    // Load recent orders
    const ordersRes = await axios.get('/api/v1/orders?pageSize=10')
    if (ordersRes.data?.success) {
      recentOrders.value = ordersRes.data.data
      stats.value.totalOrders = ordersRes.data.pagination?.total || 0
      stats.value.processingOrders = recentOrders.value.filter(o => o.status === 'processing').length
      
      // Calculate revenue
      stats.value.monthlyRevenue = recentOrders.value.reduce((sum, order) => {
        const orderDate = new Date(order.created_at)
        const now = new Date()
        if (orderDate.getMonth() === now.getMonth() && orderDate.getFullYear() === now.getFullYear()) {
          return sum + (parseFloat(order.total_amount) || 0)
        }
        return sum
      }, 0)
    }

    // Load stats
    stats.value.totalQuotes = stats.value.pendingQuotes
    stats.value.activeCustomers = 12 // Placeholder
  } catch (error) {
    console.error('Error loading dashboard:', error)
  }
}

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login' })
  }
  loadDashboardData()
})
</script>
