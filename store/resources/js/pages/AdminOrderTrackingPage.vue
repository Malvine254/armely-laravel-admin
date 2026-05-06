<template>
  <AdminLayout>
    <template #title>Order Tracking</template>

    <div class="admin-fit-page">

    <!-- Stats Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
      <div v-for="stat in statCards" :key="stat.key"
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 cursor-pointer transition hover:shadow-md"
        :class="statusFilter === stat.key ? 'ring-2 ring-[#2F5597]' : ''"
        @click="setStatusFilter(stat.key)">
        <p class="text-xs text-gray-500 font-medium">{{ stat.label }}</p>
        <p class="text-2xl font-bold mt-1" :class="stat.color">{{ statusCounts[stat.key] }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="rounded-xl border-0 shadow-lg bg-white p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            v-model="searchQuery"
            @keyup.enter="applyFilters"
            type="text"
            placeholder="Order #, PO #, customer, company..."
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#2F5597]/30 focus:border-[#2F5597] text-gray-900"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select
            v-model="statusFilter"
            @change="applyFilters"
            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#2F5597]/30 focus:border-[#2F5597] text-gray-900"
          >
            <option value="">All Orders</option>
            <option value="pending">Pending</option>
            <option value="accepted">Accepted</option>
            <option value="backordered">Backordered</option>
            <option value="shipped">Shipped</option>
            <option value="invoiced">Invoiced</option>
            <option value="failed">Failed</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="px-5 py-2.5 bg-[#2F5597] text-white rounded-lg hover:bg-[#1e3a6b] transition text-sm font-medium"
          >
            <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Search
          </button>
          <button
            @click="refreshAll"
            :disabled="refreshing"
            class="ml-3 px-5 py-2.5 border border-[#2F5597]/30 text-[#2F5597] rounded-lg hover:bg-[#2F5597]/10 transition text-sm font-medium disabled:opacity-50"
          >
            <svg :class="['w-4 h-4 inline mr-1.5', refreshing ? 'animate-spin' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ refreshing ? 'Refreshing...' : 'Refresh Status' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <svg class="animate-spin h-8 w-8 text-[#2F5597]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <!-- Orders Tracking Table -->
    <div v-else-if="orders.length > 0" class="admin-table-card rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="overflow-x-auto admin-table-scroll">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
              <th class="text-left py-3.5 px-4 font-semibold text-white">Product</th>
              <th class="text-left py-3.5 px-4 font-semibold text-white">Order</th>
              <th class="text-left py-3.5 px-4 font-semibold text-white">Customer</th>
              <th class="text-left py-3.5 px-4 font-semibold text-white">Status</th>
              <th class="text-left py-3.5 px-4 font-semibold text-white">Shipping</th>
              <th class="text-left py-3.5 px-4 font-semibold text-white">Amount</th>
              <th class="text-center py-3.5 px-4 font-semibold text-white">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="order in orders"
              :key="order.order_number"
              class="border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer"
              @click="selectOrder(order)"
            >
              <!-- Product Column -->
              <td class="py-3.5 px-4 max-w-[260px]">
                <div v-if="order.items && order.items.length > 0">
                  <div class="flex items-start gap-2.5">
                    <div v-if="order.items[0].image" class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                      <img :src="order.items[0].image" :alt="order.items[0].name" class="w-full h-full object-contain" />
                    </div>
                    <div v-else class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center flex-shrink-0">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate" :title="order.items[0].name">{{ order.items[0].name }}</p>
                      <p v-if="order.items[0].manufacturer" class="text-xs text-gray-500">{{ order.items[0].manufacturer }}</p>
                      <p v-if="order.items[0].sku || order.items[0].mfg_part_no" class="text-xs text-gray-400 font-mono">
                        {{ order.items[0].mfg_part_no || order.items[0].sku }}
                      </p>
                      <p class="text-xs text-gray-500">Qty: {{ order.items[0].quantity }} &times; ${{ formatAmount(order.items[0].price) }}</p>
                      <p v-if="order.items.length > 1" class="text-xs text-[#2F5597] font-medium mt-0.5">
                        +{{ order.items.length - 1 }} more item{{ order.items.length > 2 ? 's' : '' }}
                      </p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-xs text-gray-400 italic">No item details</div>
              </td>

              <!-- Order Column -->
              <td class="py-3.5 px-4">
                <div class="font-semibold text-gray-900 font-mono text-xs">{{ order.order_number }}</div>
                <div v-if="order.po_number && order.po_number !== order.order_number" class="text-xs text-gray-500 mt-0.5">
                  PO: {{ order.po_number }}
                </div>
                <div class="text-xs text-gray-400 mt-0.5">{{ formatDate(order.created_at) }}</div>
              </td>

              <!-- Customer Column -->
              <td class="py-3.5 px-4">
                <div class="font-medium text-gray-900">{{ order.customer_name }}</div>
                <div class="text-xs text-gray-500">{{ order.company_name }}</div>
              </td>

              <!-- Status Column -->
              <td class="py-3.5 px-4">
                <span :class="statusBadgeClass(order.status)">
                  {{ formatStatus(order.status) }}
                </span>
                <div v-if="order.td_status" class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                  TD: {{ formatStatus(order.td_status) }}
                </div>
                <div v-else-if="order.po_number && order.po_number !== order.order_number" class="text-[10px] text-amber-500 mt-1">
                  Checking TD…
                </div>
              </td>

              <!-- Shipping Column (moved from orders page) -->
              <td class="py-3.5 px-4">
                <div v-if="order.tracking_number" class="flex items-center gap-1.5">
                  <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                  </svg>
                  <a
                    :href="getTrackingUrl(order.tracking_number, order.carrier)"
                    target="_blank"
                    @click.stop
                    class="text-[#2F5597] hover:underline font-medium text-xs"
                  >
                    {{ order.tracking_number }}
                  </a>
                </div>
                <div v-if="order.carrier" class="text-xs text-gray-500 mt-0.5">{{ order.carrier }}</div>
                <div v-if="order.packages && order.packages.length > 1" class="text-xs text-gray-400 mt-0.5">
                  {{ order.packages.length }} packages
                </div>
                <div v-if="order.shipping_amount" class="text-xs mt-1">
                  <span class="text-gray-500">Freight:</span>
                  <span class="text-gray-700 font-medium ml-1">${{ formatAmount(order.shipping_amount) }}</span>
                </div>
                <div v-if="order.shipped_at" class="text-xs text-gray-500 mt-0.5">
                  Shipped: {{ formatDate(order.shipped_at) }}
                </div>
                <div v-if="order.delivered_at" class="text-xs text-emerald-600 mt-0.5">
                  Delivered: {{ formatDate(order.delivered_at) }}
                </div>
                <div v-if="!order.tracking_number && !order.shipped_at" class="text-xs text-gray-400 italic">Awaiting shipment</div>
              </td>

              <!-- Amount Column -->
              <td class="py-3.5 px-4">
                <div class="font-semibold text-gray-900">${{ formatAmount(order.total_amount) }}</div>
              </td>

              <!-- Actions Column -->
              <td class="py-3.5 px-4 text-center">
                <button
                  @click.stop="selectOrder(order)"
                  class="px-3 py-1.5 text-xs font-medium text-[#2F5597] bg-[#2F5597]/10 rounded-lg hover:bg-[#2F5597]/20 transition"
                >
                  Details
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="lastPage > 1" class="admin-table-pagination flex items-center justify-between px-6 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500">
          Showing page {{ currentPage }} of {{ lastPage }} ({{ totalOrders }} orders)
        </p>
        <div class="flex gap-2">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage <= 1"
            class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 text-gray-700"
          >
            Previous
          </button>
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage >= lastPage"
            class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 text-gray-700"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="rounded-xl border-0 shadow-lg bg-white p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
      </svg>
      <h3 class="text-lg font-semibold text-gray-700 mb-2">No Trackable Orders</h3>
      <p class="text-gray-500">No orders found. Once quotes are approved and submitted to TD SYNNEX, they will appear here with live status.</p>
    </div>

    </div>

    <!-- Order Detail Slide-Over -->
    <div
      v-if="selectedOrder"
      class="fixed inset-0 z-50 flex justify-end"
      @click.self="selectedOrder = null"
    >
      <div class="fixed inset-0 bg-black/30" @click="selectedOrder = null"></div>
      <div class="relative w-full max-w-lg bg-white shadow-2xl overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 z-10 px-6 py-4" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-bold text-white">{{ selectedOrder.order_number }}</h3>
              <p class="text-sm text-blue-200">Tracking Details</p>
            </div>
            <button @click="selectedOrder = null" class="text-white/80 hover:text-white transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-6">
          <!-- Order Progress Timeline -->
          <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wider">Order Progress</h4>
            <div class="relative">
              <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
              <div v-for="(step, idx) in orderTimeline" :key="idx" class="relative flex items-start mb-4 last:mb-0">
                <div
                  :class="[
                    'w-8 h-8 rounded-full flex items-center justify-center z-10 flex-shrink-0',
                    step.completed ? 'bg-emerald-500 text-white' : step.current ? 'bg-[#2F5597] text-white ring-4 ring-[#2F5597]/20' : 'bg-gray-200 text-gray-400'
                  ]"
                >
                  <svg v-if="step.completed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                  </svg>
                  <span v-else class="text-xs font-bold">{{ idx + 1 }}</span>
                </div>
                <div class="ml-3 flex-1 min-w-0">
                  <p :class="['text-sm font-semibold', step.completed || step.current ? 'text-gray-900' : 'text-gray-400']">{{ step.label }}</p>
                  <p v-if="step.date" class="text-xs text-gray-500">{{ step.date }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Customer Info -->
          <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Customer</h4>
            <div class="bg-gray-50 rounded-lg p-4 space-y-1">
              <p class="text-sm text-gray-900 font-medium">{{ selectedOrder.customer_name }}</p>
              <p class="text-sm text-gray-500">{{ selectedOrder.company_name }}</p>
            </div>
          </div>

          <!-- Products / Order Items -->
          <div v-if="selectedOrder.items && selectedOrder.items.length > 0">
            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">
              Products ({{ selectedOrder.items.length }})
            </h4>
            <div class="space-y-3">
              <div
                v-for="(item, iidx) in selectedOrder.items"
                :key="iidx"
                class="bg-gray-50 rounded-lg p-4 border border-gray-100"
              >
                <div class="flex items-start gap-3">
                  <div v-if="item.image" class="w-12 h-12 rounded-lg bg-white border border-gray-200 overflow-hidden flex-shrink-0">
                    <img :src="item.image" :alt="item.name" class="w-full h-full object-contain" />
                  </div>
                  <div v-else class="w-12 h-12 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">{{ item.name }}</p>
                    <p v-if="item.manufacturer" class="text-xs text-gray-500">{{ item.manufacturer }}</p>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                      <span v-if="item.mfg_part_no">MPN: <span class="text-gray-700 font-mono">{{ item.mfg_part_no }}</span></span>
                      <span v-if="item.sku">SKU: <span class="text-gray-700 font-mono">{{ item.sku }}</span></span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                      <span class="text-xs text-gray-500">Qty: <span class="text-gray-900 font-semibold">{{ item.quantity }}</span></span>
                      <span class="text-sm font-semibold text-[#2F5597]">${{ formatAmount(item.price * item.quantity) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Shipment Packages -->
          <div v-if="selectedOrder.packages && selectedOrder.packages.length > 0">
            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">
              Shipment Packages ({{ selectedOrder.packages.length }})
            </h4>
            <div class="space-y-3">
              <div
                v-for="(pkg, pidx) in selectedOrder.packages"
                :key="pidx"
                class="bg-gray-50 rounded-lg p-4 border border-gray-100"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-semibold text-gray-500 uppercase">Package {{ pidx + 1 }}</span>
                  <span v-if="pkg.carrier" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium">
                    {{ pkg.carrier }}
                  </span>
                </div>
                <div v-if="pkg.tracking_number" class="flex items-center gap-2 mb-1">
                  <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                  </svg>
                  <a
                    :href="getTrackingUrl(pkg.tracking_number, pkg.carrier)"
                    target="_blank"
                    class="text-[#2F5597] hover:underline font-medium text-sm"
                  >
                    {{ pkg.tracking_number }}
                  </a>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mt-2">
                  <div v-if="pkg.sku">SKU: <span class="text-gray-700">{{ pkg.sku }}</span></div>
                  <div v-if="pkg.quantity_shipped">Qty: <span class="text-gray-700">{{ pkg.quantity_shipped }}</span></div>
                  <div v-if="pkg.ship_date">Shipped: <span class="text-gray-700">{{ pkg.ship_date }}</span></div>
                  <div v-if="pkg.weight">Weight: <span class="text-gray-700">{{ pkg.weight }}</span></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Single Tracking (fallback) -->
          <div v-else-if="selectedOrder.tracking_number">
            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Tracking</h4>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
                <a
                  :href="getTrackingUrl(selectedOrder.tracking_number, selectedOrder.carrier)"
                  target="_blank"
                  class="text-[#2F5597] hover:underline font-semibold"
                >
                  {{ selectedOrder.tracking_number }}
                </a>
              </div>
              <p v-if="selectedOrder.carrier" class="text-sm text-gray-500 mt-1">Carrier: {{ selectedOrder.carrier }}</p>
              <p v-if="selectedOrder.estimated_delivery_date" class="text-sm text-gray-500 mt-1">
                ETA: {{ selectedOrder.estimated_delivery_date }}
              </p>
            </div>
          </div>

          <!-- No Tracking -->
          <div v-else>
            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Tracking</h4>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-center">
              <svg class="w-8 h-8 mx-auto text-amber-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-sm text-amber-700 font-medium">Tracking information not yet available</p>
              <p class="text-xs text-amber-600 mt-1">Tracking will appear once the order ships from the warehouse.</p>
            </div>
          </div>

          <!-- Live PO Status (raw from TD SYNNEX) -->
          <div v-if="selectedOrder.live_po_status">
            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">TD SYNNEX PO Status</h4>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
              <pre class="text-xs text-gray-600 whitespace-pre-wrap break-words max-h-48 overflow-y-auto">{{ JSON.stringify(selectedOrder.live_po_status, null, 2) }}</pre>
            </div>
          </div>

          <!-- Order Summary -->
          <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider">Order Summary</h4>
            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Items</span>
                <span class="text-gray-900 font-medium">{{ selectedOrder.items_count }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Total</span>
                <span class="text-gray-900 font-bold">${{ formatAmount(selectedOrder.total_amount) }}</span>
              </div>
              <div v-if="selectedOrder.shipping_amount" class="flex justify-between text-sm">
                <span class="text-gray-500">Shipping</span>
                <span class="text-gray-900">${{ formatAmount(selectedOrder.shipping_amount) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Payment</span>
                <span :class="selectedOrder.payment_status === 'completed' ? 'text-emerald-600 font-semibold' : 'text-amber-600 font-semibold'">
                  {{ formatStatus(selectedOrder.payment_status) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const orders = ref([])
const selectedOrder = ref(null)
const searchQuery = ref('')
const statusFilter = ref('')
const currentPage = ref(1)
const totalOrders = ref(0)
const lastPage = ref(1)
const loading = ref(true)
const refreshing = ref(false)

const statCards = [
  { key: '',            label: 'All',         color: 'text-gray-800' },
  { key: 'pending',     label: 'Pending',     color: 'text-amber-600' },
  { key: 'accepted',    label: 'Accepted',    color: 'text-[#2F5597]' },
  { key: 'backordered', label: 'Backordered', color: 'text-orange-600' },
  { key: 'shipped',     label: 'Shipped',     color: 'text-indigo-600' },
  { key: 'invoiced',    label: 'Invoiced',    color: 'text-emerald-600' },
]

const statusCounts = computed(() => {
  const counts = { '': orders.value.length, pending: 0, accepted: 0, backordered: 0, shipped: 0, invoiced: 0, failed: 0 }
  orders.value.forEach(o => {
    const s = normalizeStatus(o.status)
    if (s in counts) counts[s]++
  })
  return counts
})

const normalizeStatus = (status) => {
  const raw = String(status || '').toLowerCase().trim()
  const map = {
    processing: 'accepted',
    confirmed:  'accepted',
    delivered:  'invoiced',
    complete:   'invoiced',
    completed:  'invoiced',
  }
  return map[raw] || raw
}

const setStatusFilter = (key) => {
  statusFilter.value = key
  applyFilters()
}

const orderTimeline = computed(() => {
  if (!selectedOrder.value) return []
  const status = normalizeStatus(selectedOrder.value.status)
  const steps = [
    { key: 'accepted', label: 'Accepted by Supplier', date: formatDate(selectedOrder.value.created_at) },
    { key: 'backordered', label: 'Backordered', date: null },
    { key: 'shipped', label: 'Shipped', date: selectedOrder.value.shipped_at ? formatDate(selectedOrder.value.shipped_at) : null },
    { key: 'invoiced', label: 'Invoiced / Complete', date: selectedOrder.value.delivered_at ? formatDate(selectedOrder.value.delivered_at) : null },
  ]
  const statusOrder = ['accepted', 'backordered', 'shipped', 'invoiced']
  const currentIdx = statusOrder.indexOf(status)
  return steps.map((step, idx) => ({
    ...step,
    completed: currentIdx >= 0 && idx < currentIdx,
    current: currentIdx >= 0 && idx === currentIdx,
  }))
})

const fetchOrders = async () => {
  loading.value = true
  try {
    const params = {
      page: currentPage.value,
      pageSize: 10,
    }
    if (statusFilter.value) params.status = statusFilter.value
    if (searchQuery.value) params.search = searchQuery.value

    const response = await api.get('/admin/orders/tracking', { params })
    if (response.data.success) {
      orders.value = response.data.data
      totalOrders.value = response.data.pagination.total
      lastPage.value = response.data.pagination.last_page
    }
  } catch (error) {
    console.error('Failed to fetch tracking orders:', error)
  } finally {
    loading.value = false
  }
}

const refreshAll = async () => {
  refreshing.value = true
  await fetchOrders()
  refreshing.value = false
}

const applyFilters = () => {
  currentPage.value = 1
  fetchOrders()
}

const changePage = (page) => {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  fetchOrders()
}

const selectOrder = (order) => {
  selectedOrder.value = order
}

const formatStatus = (status) => {
  if (!status) return 'Unknown'
  return status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const formatAmount = (amount) => {
  if (!amount) return '0.00'
  return Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const statusBadgeClass = (status) => {
  const normalized = normalizeStatus(status)
  const base = 'px-2.5 py-1 rounded-full text-xs font-semibold inline-block'
  switch (normalized) {
    case 'accepted': return `${base} bg-blue-100 text-[#2F5597]`
    case 'backordered': return `${base} bg-amber-100 text-amber-700`
    case 'shipped': return `${base} bg-indigo-100 text-indigo-700`
    case 'invoiced': return `${base} bg-emerald-100 text-emerald-700`
    default: return `${base} bg-gray-100 text-gray-600`
  }
}

const getTrackingUrl = (trackingNumber, carrier) => {
  if (!trackingNumber) return '#'
  const c = (carrier || '').toLowerCase()
  if (c.includes('ups')) return `https://www.ups.com/track?tracknum=${trackingNumber}`
  if (c.includes('fedex')) return `https://www.fedex.com/fedextrack/?trknbr=${trackingNumber}`
  if (c.includes('usps')) return `https://tools.usps.com/go/TrackConfirmAction?tLabels=${trackingNumber}`
  if (c.includes('dhl')) return `https://www.dhl.com/us-en/home/tracking.html?tracking-id=${trackingNumber}`
  // Default: Google search with the tracking number
  return `https://www.google.com/search?q=${encodeURIComponent(trackingNumber + ' tracking')}`
}

onMounted(() => {
  fetchOrders()
})
</script>

<style scoped>
.admin-fit-page {
  block-size: calc(100vh - 170px);
  min-block-size: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.admin-fit-page > :not(.admin-table-card) {
  flex-shrink: 0;
}

.admin-table-card {
  min-block-size: 0;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.admin-table-scroll {
  min-block-size: 0;
  flex: 1;
  overflow: auto;
}

.admin-table-pagination {
  position: sticky;
  inset-block-end: 0;
  z-index: 5;
  background: #ffffff;
  flex-shrink: 0;
}

@media (max-width: 1023px) {
  .admin-fit-page {
    block-size: auto;
    min-block-size: calc(100vh - 170px);
  }

  .admin-table-scroll {
    max-block-size: 60vh;
  }
}
</style>
