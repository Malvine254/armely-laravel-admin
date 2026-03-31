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
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
          <p class="text-gray-600 text-sm font-medium">Total Orders</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ pagination.total }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
          <p class="text-gray-600 text-sm font-medium">Processing</p>
          <p class="text-3xl font-bold" style="color: #ff9800;">{{ getOrdersCountByStatus('processing') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
          <p class="text-gray-600 text-sm font-medium">Delivered</p>
          <p class="text-3xl font-bold text-green-600">{{ getOrdersCountByStatus('delivered') }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
          <p class="text-gray-600 text-sm font-medium">Cancelled</p>
          <p class="text-3xl font-bold text-red-600">{{ getOrdersCountByStatus('cancelled') }}</p>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="relative">
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
          <div class="relative">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Search Orders</label>
            <input v-model="searchQuery" type="text" placeholder="Enter order number..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
          </div>
          <div class="flex gap-2 items-end">
            <button @click="fetchOrders" class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200 hover:shadow-lg" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>Search
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

      <!-- Orders Grid (Card Layout) -->
      <div v-else class="space-y-4">
        <div v-for="order in filteredOrders" :key="order.id" class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 overflow-hidden cursor-pointer" @click="viewOrder(order)">
          <!-- Card Header with Status -->
          <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-4 flex-1">
              <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Order Number</p>
                <p class="text-xl font-bold text-gray-900 font-mono">{{ order.order_number }}</p>
              </div>
            </div>
            <span :class="getStatusBadge(order.status)" class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap">
              {{ formatStatus(order.status) }}
            </span>
          </div>

          <!-- Card Body -->
          <div class="px-6 py-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 mb-5">
              <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Order Date</p>
                <p class="text-gray-900 font-semibold mt-1">{{ formatDate(order.created_at) }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Amount</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(order.total_amount) }}</p>
              </div>
              <div v-if="order.tracking_number">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Tracking Number</p>
                <p class="text-gray-900 font-mono font-semibold mt-1 truncate">{{ order.tracking_number }}</p>
              </div>
              <div v-if="order.estimated_delivery">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Est. Delivery</p>
                <p class="text-gray-900 font-semibold mt-1">{{ formatDate(order.estimated_delivery) }}</p>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-5">
              <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-gray-700">Order Status Progress</p>
                <p class="text-xs text-gray-600">{{ getStatusProgress(order.status) }}%</p>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition duration-500" :style="{ inlineSize: getStatusProgress(order.status) + '%' }"></div>
              </div>
            </div>
          </div>

          <!-- Card Footer with Actions -->
          <div class="border-t border-gray-100 bg-gray-50 px-6 py-4 flex items-center justify-between">
            <button @click.stop="viewOrder(order)" class="flex items-center gap-2 font-semibold transition duration-200 hover:opacity-75" style="color: #2F5597;">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              View Details
            </button>
            <button
              v-if="canCancelOrder(order)"
              @click.stop="cancelOrder(order)"
              :disabled="processingOrderNumber === order.order_number"
              class="flex items-center gap-2 font-semibold px-4 py-2 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              style="color: #e74c3c; border: 1px solid #e74c3c;"
              @mouseenter="$event.target.style.backgroundColor='#fadbd8'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              {{ processingOrderNumber === order.order_number ? 'Cancelling...' : 'Cancel' }}
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > pagination.per_page" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-8 flex items-center justify-between">
          <div class="text-sm text-gray-600">
            Showing <span class="font-semibold">{{ pagination.from }}</span> to <span class="font-semibold">{{ pagination.to }}</span> of <span class="font-semibold">{{ pagination.total }}</span> orders
          </div>
          <div class="flex gap-3">
            <button 
              @click="previousPage" 
              :disabled="pagination.current_page === 1"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
            >
              ← Previous
            </button>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">Page <span class="font-semibold">{{ pagination.current_page }}</span> of <span class="font-semibold">{{ pagination.last_page }}</span></span>
            </div>
            <button 
              @click="nextPage" 
              :disabled="pagination.current_page === pagination.last_page"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
            >
              Next →
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
