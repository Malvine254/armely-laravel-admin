<template>
  <AdminLayout>
    <template #title>Revenue Reports</template>

    <!-- Period Selector -->
    <div class="rounded-xl border-0 shadow-lg bg-white p-6 mb-6">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Report Period</h3>
        <div class="flex space-x-2">
          <button
            v-for="periodOption in periods"
            :key="periodOption.value"
            @click="period = periodOption.value; fetchRevenueReport()"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition',
              period === periodOption.value
                ? 'bg-gradient-to-r from-[#2F5597] to-[#1e3a6b] text-white'
                : 'bg-gray-50 text-gray-500 hover:bg-gray-100'
            ]"
          >
            {{ periodOption.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Revenue Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-gray-500">Total Revenue</p>
          <div class="w-10 h-10 rounded-lg flex items-center justify-center">
            <i class="fas fa-dollar-sign text-[#2F5597]"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">${{ formatCurrency(revenueData.total_revenue) }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ getPeriodLabel() }}</p>
      </div>

      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-gray-500">Total Orders</p>
          <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-shopping-cart text-blue-700"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ revenueData.total_orders || 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Delivered orders</p>
      </div>

      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-gray-500">Avg Order Value</p>
          <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-chart-line text-emerald-600"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">${{ formatCurrency(revenueData.average_order_value) }}</p>
        <p class="text-xs text-gray-500 mt-1">Per order</p>
      </div>

      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-gray-500">Total Tax</p>
          <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-percentage text-amber-600"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">${{ formatCurrency(revenueData.total_tax) }}</p>
        <p class="text-xs text-gray-500 mt-1">Collected</p>
      </div>

      <div class="rounded-xl border-0 shadow-lg bg-white p-6">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-gray-500">Total Shipping</p>
          <div class="w-10 h-10 bg-violet-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-truck text-violet-700"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">${{ formatCurrency(revenueData.total_shipping) }}</p>
        <p class="text-xs text-gray-500 mt-1">Revenue</p>
      </div>
    </div>

    <!-- Top Customers -->
    <div class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Top Customers</h3>
          <p class="text-sm text-gray-500">Customers with highest total purchase value</p>
        </div>
        <button
          @click="fetchTopCustomers"
          class="px-4 py-2 text-sm bg-[#2F5597] hover:bg-[#1e3a6b] text-white rounded-lg transition"
        >
          <i class="fas fa-sync-alt mr-2"></i>Refresh
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Rank</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Customer</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Email</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">Orders</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Spent</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="topCustomers.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-users text-4xl mb-3 block text-gray-400"></i>
                <p>No customer data available</p>
              </td>
            </tr>
            <tr v-for="(customer, index) in topCustomers" :key="customer.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <span :class="[
                    'w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm',
                    index === 0 ? 'bg-amber-500/20 text-amber-600' :
                    index === 1 ? 'bg-slate-500/20 text-gray-500' :
                    index === 2 ? 'bg-orange-500/20 text-orange-300' :
                    'bg-gray-50 text-gray-500'
                  ]">
                    {{ index + 1 }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="font-semibold text-gray-900">{{ customer.name }}</p>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ customer.email }}
              </td>
              <td class="px-6 py-4 text-center">
                <span class="px-3 py-1 bg-[#2F5597]/20 text-[#2F5597] rounded-full text-sm font-semibold">
                  {{ customer.order_count || 0 }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <p class="font-bold text-lg text-[#2F5597]">${{ formatCurrency(customer.total_spent) }}</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <p class="text-sm text-gray-500">Showing top {{ topCustomers.length }} customers by total purchase value</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const period = ref('month')
const revenueData = ref({
  total_revenue: 0,
  total_orders: 0,
  average_order_value: 0,
  total_tax: 0,
  total_shipping: 0
})
const topCustomers = ref([])

const periods = [
  { label: 'Today', value: 'day' },
  { label: 'This Week', value: 'week' },
  { label: 'This Month', value: 'month' },
  { label: 'This Year', value: 'year' }
]

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const getPeriodLabel = () => {
  const labels = {
    day: 'Today',
    week: 'This Week',
    month: 'This Month',
    year: 'This Year'
  }
  return labels[period.value] || 'This Month'
}

const fetchRevenueReport = async () => {
  try {
    const response = await api.get('/admin/reports/revenue', {
      params: { period: period.value }
    })

    if (response.data.success) {
      revenueData.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch revenue report:', error)
  }
}

const fetchTopCustomers = async () => {
  try {
    const response = await api.get('/admin/reports/top-customers', {
      params: { limit: 10 }
    })

    if (response.data.success) {
      topCustomers.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch top customers:', error)
  }
}

onMounted(() => {
  fetchRevenueReport()
  fetchTopCustomers()
})
</script>
