<template>
  <AdminLayout>
    <template #title>Revenue Reports</template>

    <!-- Period Selector -->
    <div class="rounded-xl border border-white/10 shadow p-6 mb-6 backdrop-blur" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.72), rgba(10, 41, 72, 0.72));">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-white">Report Period</h3>
        <div class="flex space-x-2">
          <button
            v-for="periodOption in periods"
            :key="periodOption.value"
            @click="period = periodOption.value; fetchRevenueReport()"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition',
              period === periodOption.value
                ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white'
                : 'bg-white/5 text-slate-300 hover:bg-white/10'
            ]"
          >
            {{ periodOption.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Revenue Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
      <div class="rounded-xl border border-white/10 p-6 backdrop-blur" style="background: linear-gradient(180deg, rgba(34, 211, 238, 0.12), rgba(3, 102, 214, 0.08));">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-slate-300">Total Revenue</p>
          <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.25), rgba(59, 130, 246, 0.25));">
            <i class="fas fa-dollar-sign text-cyan-300"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-white">${{ formatCurrency(revenueData.total_revenue) }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ getPeriodLabel() }}</p>
      </div>

      <div class="rounded-xl border border-white/10 p-6 backdrop-blur" style="background: linear-gradient(180deg, rgba(59, 130, 246, 0.12), rgba(37, 99, 235, 0.08));">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-slate-300">Total Orders</p>
          <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-shopping-cart text-blue-300"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ revenueData.total_orders || 0 }}</p>
        <p class="text-xs text-slate-400 mt-1">Delivered orders</p>
      </div>

      <div class="rounded-xl border border-white/10 p-6 backdrop-blur" style="background: linear-gradient(180deg, rgba(34, 197, 94, 0.12), rgba(22, 163, 74, 0.08));">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-slate-300">Avg Order Value</p>
          <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-chart-line text-emerald-300"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-white">${{ formatCurrency(revenueData.average_order_value) }}</p>
        <p class="text-xs text-slate-400 mt-1">Per order</p>
      </div>

      <div class="rounded-xl border border-white/10 p-6 backdrop-blur" style="background: linear-gradient(180deg, rgba(251, 191, 36, 0.12), rgba(217, 119, 6, 0.08));">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-slate-300">Total Tax</p>
          <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-percentage text-amber-300"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-white">${{ formatCurrency(revenueData.total_tax) }}</p>
        <p class="text-xs text-slate-400 mt-1">Collected</p>
      </div>

      <div class="rounded-xl border border-white/10 p-6 backdrop-blur" style="background: linear-gradient(180deg, rgba(168, 85, 247, 0.12), rgba(124, 58, 237, 0.08));">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm text-slate-300">Total Shipping</p>
          <div class="w-10 h-10 bg-violet-500/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-truck text-violet-300"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-white">${{ formatCurrency(revenueData.total_shipping) }}</p>
        <p class="text-xs text-slate-400 mt-1">Revenue</p>
      </div>
    </div>

    <!-- Top Customers -->
    <div class="rounded-xl border border-white/10 shadow overflow-hidden backdrop-blur" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.72), rgba(10, 41, 72, 0.72));">
      <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between bg-slate-900/50">
        <div>
          <h3 class="text-lg font-semibold text-white">Top Customers</h3>
          <p class="text-sm text-slate-400">Customers with highest total purchase value</p>
        </div>
        <button
          @click="fetchTopCustomers"
          class="px-4 py-2 text-sm bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white rounded-lg transition"
        >
          <i class="fas fa-sync-alt mr-2"></i>Refresh
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-900/70 border-b border-white/10">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-200 uppercase tracking-wide">Rank</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-200 uppercase tracking-wide">Customer</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-200 uppercase tracking-wide">Email</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-slate-200 uppercase tracking-wide">Orders</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-slate-200 uppercase tracking-wide">Total Spent</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            <tr v-if="topCustomers.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                <i class="fas fa-users text-4xl mb-3 block text-slate-600"></i>
                <p>No customer data available</p>
              </td>
            </tr>
            <tr v-for="(customer, index) in topCustomers" :key="customer.id" class="hover:bg-white/5 transition">
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <span :class="[
                    'w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm',
                    index === 0 ? 'bg-amber-500/20 text-amber-300' :
                    index === 1 ? 'bg-slate-500/20 text-slate-300' :
                    index === 2 ? 'bg-orange-500/20 text-orange-300' :
                    'bg-white/5 text-slate-300'
                  ]">
                    {{ index + 1 }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="font-semibold text-white">{{ customer.name }}</p>
              </td>
              <td class="px-6 py-4 text-sm text-slate-400">
                {{ customer.email }}
              </td>
              <td class="px-6 py-4 text-center">
                <span class="px-3 py-1 bg-cyan-500/20 text-cyan-300 rounded-full text-sm font-semibold">
                  {{ customer.order_count || 0 }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <p class="font-bold text-lg text-cyan-300">${{ formatCurrency(customer.total_spent) }}</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="px-6 py-4 border-t border-white/10 bg-slate-900/50">
        <p class="text-sm text-slate-400">Showing top {{ topCustomers.length }} customers by total purchase value</p>
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
