<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-5xl font-bold text-gray-900 mb-2">Your Orders</h1>
        <p class="text-gray-600 text-lg">Track and manage all your purchases</p>
      </div>

      <!-- Quick Stats -->
      <div v-if="orders.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f7fbff 62%, #eef4ff 100%); border-color: #d9e6f7; box-shadow: 0 12px 26px rgba(47,85,151,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(47,85,151,0.2) 0%, rgba(47,85,151,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Total Orders</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ pagination.total }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #2F5597, #7fa2d8);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #fffaf2 62%, #fff3df 100%); border-color: #f6dec0; box-shadow: 0 12px 24px rgba(245,158,11,0.12);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(245,158,11,0.2) 0%, rgba(245,158,11,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Processing</p>
          <p class="text-3xl font-bold mt-2" style="color: #d97706;">{{ getOrdersCountByStatus('processing') }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #f59e0b, #fcd34d);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f6fff9 62%, #edfff3 100%); border-color: #cce9d6; box-shadow: 0 12px 24px rgba(22,163,74,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(34,197,94,0.22) 0%, rgba(34,197,94,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Delivered</p>
          <p class="text-3xl font-bold text-green-600 mt-2">{{ getOrdersCountByStatus('delivered') }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #16a34a, #86efac);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #fff6f6 62%, #ffeded 100%); border-color: #f4cccc; box-shadow: 0 12px 24px rgba(220,38,38,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(220,38,38,0.2) 0%, rgba(220,38,38,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Cancelled</p>
          <p class="text-3xl font-bold text-red-600 mt-2">{{ getOrdersCountByStatus('cancelled') }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #dc2626, #fca5a5);"></div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
          <div class="relative md:col-span-3">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Filter by Status</label>
            <select v-model="selectedStatus" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="confirmed">Confirmed</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="relative md:col-span-4">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Search Orders</label>
            <input v-model="searchQuery" type="text" placeholder="Enter order number..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
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
            <button @click="fetchOrders" class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200 hover:shadow-lg" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
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
          <div class="w-16 h-16 border-4 rounded-full animate-spin mb-4" style="border-color: #e5ebf2; border-block-start-color: #2F5597;"></div>
          <p class="text-gray-600 font-medium">Loading your orders...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 mb-8">
        <div class="flex gap-4">
          <svg class="h-6 w-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h3 class="font-semibold text-red-900 text-lg">Unable to Load Orders</h3>
            <p class="text-red-700 mt-1">{{ error }}</p>
            <button @click="fetchOrders" class="mt-3 text-red-700 hover:text-red-900 font-semibold underline">Try Again</button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredOrders.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-1">No Orders Yet</h3>
        <p class="text-gray-600 mb-6">Start shopping and your orders will appear here</p>
        <router-link to="/quotes" class="inline-block px-6 py-3 text-white rounded-lg font-semibold transition duration-200 hover:shadow-lg" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Create a Quote
        </router-link>
      </div>

      <!-- Orders Table -->
      <div v-else class="bg-white rounded-xl shadow-sm border overflow-hidden" style="border-color: #d9e6f7;">
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b" style="background: linear-gradient(90deg, #f7fbff, #edf4fc); border-color: #d9e6f7;">
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Order Number</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Created</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Progress</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="order in filteredOrders"
                :key="order.id"
                class="border-b last:border-b-0 hover:bg-[#f8fbff] transition duration-200"
                style="border-color: #ecf3fb;"
              >
                <td class="px-5 py-4 align-top">
                  <button
                    @click="viewOrder(order)"
                    class="text-left font-semibold font-mono transition duration-200 hover:opacity-80"
                    style="color: #2F5597;"
                  >
                    {{ order.order_number }}
                  </button>
                  <p v-if="order.tracking_number" class="text-xs text-gray-500 mt-1">Tracking: {{ order.tracking_number }}</p>
                </td>
                <td class="px-5 py-4 align-top">
                  <span :class="getStatusBadge(order.status)" class="inline-flex px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                    {{ formatStatus(order.status) }}
                  </span>
                </td>
                <td class="px-5 py-4 text-sm text-gray-700 align-top">{{ formatDate(order.created_at) }}</td>
                <td class="px-5 py-4 align-top">
                  <p class="text-sm font-bold text-gray-900">{{ formatCurrency(order.total_amount) }}</p>
                  <p v-if="order.estimated_delivery" class="text-xs text-gray-500 mt-1">ETA: {{ formatDate(order.estimated_delivery) }}</p>
                </td>
                <td class="px-5 py-4 align-top">
                  <div class="w-36">
                    <div class="w-full rounded-full h-2 mb-1" style="background-color: #d9e6f7;">
                      <div class="h-2 rounded-full" style="background-color: #2F5597;" :style="{ inlineSize: getStatusProgress(order.status) + '%' }"></div>
                    </div>
                    <p class="text-xs text-gray-600">{{ getStatusProgress(order.status) }}%</p>
                  </div>
                </td>
                <td class="px-5 py-4 align-top">
                  <div class="flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button
                      @click="viewOrder(order)"
                      class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200"
                      style="color: #2F5597; border: 1px solid #2F5597;"
                      @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
                      @mouseleave="$event.target.style.backgroundColor='transparent'"
                    >
                      View
                    </button>
                    <button
                      v-if="canCancelOrder(order)"
                      @click="cancelOrder(order)"
                      :disabled="processingOrderNumber === order.order_number"
                      class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                      style="color: #e74c3c; border: 1px solid #e74c3c;"
                      @mouseenter="$event.target.style.backgroundColor='#fadbd8'"
                      @mouseleave="$event.target.style.backgroundColor='transparent'"
                    >
                      {{ processingOrderNumber === order.order_number ? 'Cancelling...' : 'Cancel' }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > pagination.per_page" class="border-t px-4 sm:px-5 py-4 flex items-center justify-between" style="border-color: #d9e6f7; background: #f9fcff;">
          <div class="text-sm text-gray-700 font-medium">
            Showing {{ pagination.from }}-{{ pagination.to }} of {{ pagination.total }}
          </div>
          <div class="flex items-center gap-2">
            <button 
              @click="previousPage" 
              :disabled="pagination.current_page === 1"
              class="px-3 py-1.5 text-xs sm:text-sm rounded-md border font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="border-color: #d9e6f7; color: #2F5597;"
            >
              Previous
            </button>
            <span class="px-3 py-1.5 text-xs sm:text-sm rounded-md border font-semibold text-white" style="background-color: #2F5597; border-color: #2F5597;">
              {{ pagination.current_page }}
            </span>
            <button 
              @click="nextPage" 
              :disabled="pagination.current_page === pagination.last_page"
              class="px-3 py-1.5 text-xs sm:text-sm rounded-md border font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="border-color: #d9e6f7; color: #2F5597;"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Detail Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" @click="selectedOrder = null">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-gray-900 to-gray-800 text-white px-6 py-6 flex items-center justify-between rounded-t-2xl">
          <div>
            <p class="text-sm font-semibold text-gray-200 uppercase tracking-wide">Order</p>
            <h2 class="text-2xl font-bold text-white">{{ selectedOrder.order_number }}</h2>
          </div>
          <button @click="selectedOrder = null" class="text-gray-300 hover:text-white transition duration-200">
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
              <h3 class="text-lg font-bold text-gray-900">Order Status</h3>
              <span :class="getStatusBadge(selectedOrder.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
                {{ formatStatus(selectedOrder.status) }}
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
              <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" :style="{ inlineSize: getStatusProgress(selectedOrder.status) + '%' }"></div>
            </div>
            <p class="text-xs text-gray-600">{{ getStatusProgress(selectedOrder.status) }}% Complete</p>
          </div>

          <!-- Order Information Grid -->
          <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Order Date</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedOrder.created_at) }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Total Amount</p>
              <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(selectedOrder.total_amount) }}</p>
            </div>
            <div v-if="selectedOrder.tracking_number">
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Tracking Number</p>
              <p class="font-mono text-gray-900 font-semibold">{{ selectedOrder.tracking_number }}</p>
            </div>
            <div v-if="selectedOrder.estimated_delivery">
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Est. Delivery</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedOrder.estimated_delivery) }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button @click="selectedOrder = null" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition duration-200">
              Close
            </button>
            <button
              v-if="canCancelOrder(selectedOrder)"
              @click="cancelOrder(selectedOrder)"
              :disabled="processingOrderNumber === selectedOrder.order_number"
              class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              style="background-color: #e74c3c;"
              @mouseenter="$event.target.style.backgroundColor='#c0392b'"
              @mouseleave="$event.target.style.backgroundColor='#e74c3c'"
            >
              {{ processingOrderNumber === selectedOrder.order_number ? 'Cancelling...' : 'Cancel Order' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/authStore';
import { useToastStore } from '../../stores/toastStore';
import axios from 'axios';
import Navbar from '../../components/Navbar.vue';

export default {
  components: {
    Navbar,
  },
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    const toastStore = useToastStore();
    const orders = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const selectedStatus = ref('');
    const searchQuery = ref('');
    const selectedOrder = ref(null);
    const processingOrderNumber = ref(null);
    const pagination = ref({
      current_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0,
      last_page: 1,
    });

    const filteredOrders = computed(() => {
      return orders.value.filter(order => {
        const statusMatch = !selectedStatus.value || order.status === selectedStatus.value;
        const searchMatch = !searchQuery.value || order.order_number.toLowerCase().includes(searchQuery.value.toLowerCase());
        return statusMatch && searchMatch;
      });
    });

    const fetchOrders = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        if (!authStore.isAuthenticated) {
          error.value = 'Authentication required. Please log in to view your orders.';
          setTimeout(() => router.push({ name: 'login' }), 2000);
          return;
        }

        const params = new URLSearchParams({
          page: pagination.value.current_page,
          per_page: pagination.value.per_page,
        });

        const response = await axios.get(`/api/v1/orders?${params}`);
        
        if (response.data?.success) {
          orders.value = response.data.data;
          if (response.data.pagination) {
            pagination.value = response.data.pagination;
          }
        } else {
          error.value = response.data?.message || 'Failed to load orders';
        }
      } catch (err) {
        if (err.response?.status === 401) {
          error.value = 'Your session has expired. Please log in again.';
          authStore.logout();
          setTimeout(() => router.push({ name: 'login' }), 2000);
        } else {
          error.value = err.response?.data?.message || err.message || 'Failed to load orders';
        }
      } finally {
        loading.value = false;
      }
    };

    const viewOrder = (order) => {
      selectedOrder.value = order;
    };

    const canCancelOrder = (order) => {
      if (!order) return false;
      return ['pending', 'processing', 'confirmed'].includes(order.status);
    };

    const cancelOrder = async (order) => {
      if (!canCancelOrder(order)) {
        return;
      }

      if (authStore.isRestricted) {
        toastStore.addToast('Account suspended: cancelling orders is disabled', 'error');
        return;
      }

      const reason = window.prompt('Optional cancellation reason (max 500 chars):', 'Cancelled by customer');
      if (reason === null) {
        return;
      }

      processingOrderNumber.value = order.order_number;

      try {
        const response = await axios.post(`/api/v1/orders/${order.order_number}/cancel`, {
          reason: reason || 'Cancelled by customer',
        });

        if (response.data?.success) {
          toastStore.addToast('Order cancelled successfully', 'success');
          if (selectedOrder.value?.order_number === order.order_number) {
            selectedOrder.value = response.data.data;
          }
          await fetchOrders();
        } else {
          toastStore.addToast(response.data?.message || 'Failed to cancel order', 'error');
        }
      } catch (err) {
        console.error('Error cancelling order:', err);
        toastStore.addToast(err.response?.data?.message || 'Failed to cancel order', 'error');
      } finally {
        processingOrderNumber.value = null;
      }
    };

    const nextPage = () => {
      if (pagination.value.current_page < pagination.value.last_page) {
        pagination.value.current_page++;
        fetchOrders();
      }
    };

    const previousPage = () => {
      if (pagination.value.current_page > 1) {
        pagination.value.current_page--;
        fetchOrders();
      }
    };

    const resetFilters = () => {
      selectedStatus.value = '';
      searchQuery.value = '';
      pagination.value.current_page = 1;
      fetchOrders();
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      }).format(date);
    };

    const formatStatus = (status) => {
      if (!status) return 'Unknown';
      return status
        .split('_')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
    };

    const getStatusBadge = (status) => {
      const badges = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-orange-100 text-orange-800',
        confirmed: 'bg-blue-100 text-blue-800',
        shipped: 'bg-indigo-100 text-indigo-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
      };
      return badges[status] || 'bg-gray-100 text-gray-800';
    };

    const formatCurrency = (amount) => {
      if (!amount) return '$0.00';
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
      }).format(amount);
    };

    const getOrdersCountByStatus = (status) => {
      return orders.value.filter(order => order.status === status).length;
    };

    const getStatusProgress = (status) => {
      const statusProgress = {
        pending: 20,
        processing: 40,
        confirmed: 60,
        shipped: 80,
        delivered: 100,
        cancelled: 0,
      };
      return statusProgress[status] || 0;
    };

    onMounted(() => {
      fetchOrders();
    });

    return {
      orders,
      loading,
      error,
      selectedStatus,
      searchQuery,
      selectedOrder,
      processingOrderNumber,
      pagination,
      filteredOrders,
      fetchOrders,
      viewOrder,
      canCancelOrder,
      cancelOrder,
      nextPage,
      previousPage,
      resetFilters,
      formatDate,
      formatStatus,
      getStatusBadge,
      formatCurrency,
      getOrdersCountByStatus,
      getStatusProgress,
    };
  },
};
</script>

<style scoped>
/* Smooth transitions */
* {
  transition: background-color 0.2s, color 0.2s, border-color 0.2s;
}

/* Ensure modal appears above all */
.z-50 {
  z-index: 50;
}
</style>
