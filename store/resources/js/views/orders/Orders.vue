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

      <!-- View Tabs -->
      <div class="mb-6">
        <div class="inline-flex rounded-xl border border-gray-200 bg-white p-1 shadow-sm">
          <button
            @click="activeTab = 'orders'"
            :class="activeTab === 'orders' ? 'text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100'"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition duration-200"
            :style="activeTab === 'orders' ? 'background-color: #2F5597;' : ''"
          >
            Orders Table
          </button>
          <button
            @click="activeTab = 'tracking'"
            :class="activeTab === 'tracking' ? 'text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100'"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition duration-200 flex items-center gap-2"
            :style="activeTab === 'tracking' ? 'background-color: #2F5597;' : ''"
          >
            Order Tracking
            <span class="inline-flex items-center justify-center min-w-[1.2rem] h-5 px-1.5 rounded-full text-xs font-bold"
              :class="activeTab === 'tracking' ? 'bg-white text-[#2F5597]' : 'bg-[#2F5597] text-white'"
            >
              {{ activeShipmentsCount }}
            </span>
          </button>
        </div>
      </div>

      <!-- Order Tracking -->
      <div v-if="activeTab === 'tracking'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
          <div>
            <h3 class="text-xl font-bold text-gray-900">Order Tracking</h3>
            <p class="text-sm text-gray-600">Real-time tracking snapshots for your latest orders</p>
          </div>
          <span class="text-xs text-gray-500">Auto-updates in background</span>
        </div>

        <div v-if="liveShippingLoading" class="py-8 text-center">
          <div class="inline-block w-8 h-8 border-4 rounded-full animate-spin" style="border-color: #d9e6f7; border-block-start-color: #2F5597;"></div>
          <p class="text-sm text-gray-600 mt-2">Syncing latest shipment updates...</p>
        </div>

        <div v-else-if="liveShippingError" class="rounded-lg border border-amber-200 bg-amber-50 p-4">
          <p class="text-sm font-semibold text-amber-900">Could not load live tracker</p>
          <p class="text-sm text-amber-800 mt-1">{{ liveShippingError }}</p>
        </div>

        <div v-else-if="liveShipments.length === 0" class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
          <p class="text-sm text-gray-600">No active shipments yet. Once an order ships, tracking appears here.</p>
        </div>

        <div v-else class="grid grid-cols-1 gap-4">
          <article
            v-for="shipment in liveShipments"
            :key="shipment.order_number"
            class="group rounded-2xl border p-4 md:p-5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg"
            style="border-color: #d9e6f7; background: linear-gradient(170deg, #ffffff 0%, #f6faff 55%, #eef5ff 100%);"
          >
            <div class="flex items-start justify-between gap-3 mb-4">
              <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Order</p>
                <p class="font-mono font-semibold text-gray-900">{{ shipment.order_number }}</p>
                <p
                  class="text-sm text-gray-800 mt-1 font-semibold max-w-[280px] leading-snug"
                  :title="shipment.primary_item_name || 'Item details unavailable'"
                >
                  {{ truncateProductName(shipment.primary_item_name) }}
                </p>
                <p v-if="shipment.additional_items_count > 0" class="text-xs text-gray-500">+{{ shipment.additional_items_count }} more item(s)</p>
              </div>
              <span :class="liveStatusBadge(shipment.status)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold">
                <span class="w-2 h-2 rounded-full animate-pulse" :style="{ backgroundColor: liveStatusDot(shipment.status) }"></span>
                {{ formatStatus(shipment.status) }}
              </span>
            </div>

            <div class="mb-4 rounded-xl border px-3 py-2.5" style="border-color:#dbe8f8; background-color:#ffffffcc;">
              <div class="flex items-center justify-between mb-1.5">
                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-600">Progress</p>
                <p class="text-xs font-bold" style="color:#2F5597;">{{ shipment.progress }}%</p>
              </div>
              <div class="w-full rounded-full h-2.5 mb-1" style="background-color: #d9e6f7;">
                <div class="h-2.5 rounded-full" style="background: linear-gradient(90deg, #2F5597, #4a79c6);" :style="{ inlineSize: shipment.progress + '%' }"></div>
              </div>
              <p class="text-xs text-gray-600">{{ shipment.progress }}% complete</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 mb-4 text-xs text-gray-700">
              <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="rounded-lg px-3 py-2 border" style="border-color:#e2e8f0; background-color:#fff;">
                  <p class="text-[10px] uppercase tracking-wide text-gray-500">Carrier</p>
                  <p class="mt-0.5 font-semibold text-gray-900 truncate">{{ shipment.carrier ? shipment.carrier.toUpperCase() : 'Awaiting assignment' }}</p>
                </div>
                <div class="rounded-lg px-3 py-2 border" style="border-color:#e2e8f0; background-color:#fff;">
                  <p class="text-[10px] uppercase tracking-wide text-gray-500">Tracking</p>
                  <p class="mt-0.5 font-mono text-gray-900 truncate">{{ shipment.tracking_number || 'Pending assignment' }}</p>
                </div>
                <div class="rounded-lg px-3 py-2 border" style="border-color:#e2e8f0; background-color:#fff;">
                  <p class="text-[10px] uppercase tracking-wide text-gray-500">ETA</p>
                  <p class="mt-0.5 font-semibold text-gray-900 truncate">{{ shipment.estimated_delivery_at ? formatDate(shipment.estimated_delivery_at) : 'Awaiting update' }}</p>
                </div>
              </div>

              <div class="lg:col-span-5 rounded-xl border px-3 py-3" style="border-color:#e2e8f0; background-color:#fff;">
                <p class="text-[11px] font-semibold text-gray-700 mb-2">Timeline</p>
                <div class="flex items-start">
                  <template v-for="(milestone, idx) in shipment.milestones" :key="milestone.label">
                    <div v-if="idx > 0" class="h-px w-5 mt-2.5"
                      :class="milestone.done ? 'bg-green-400' : 'bg-gray-300'"
                    ></div>
                    <div class="min-w-[56px] flex flex-col items-center text-center">
                      <span class="w-5 h-5 rounded-full border-2"
                        :class="milestone.done ? 'bg-green-500 border-green-500' : 'bg-white border-gray-300'"
                      ></span>
                      <span class="mt-1 text-[10px] leading-tight"
                        :class="milestone.done ? 'text-gray-800 font-semibold' : 'text-gray-400'"
                      >{{ milestone.label }}</span>
                    </div>
                  </template>
                </div>
              </div>
            </div>

            <div
              v-if="shipment.tracking_eligible && !shipment.tracking_number"
              class="mb-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2"
            >
              <p class="text-xs font-medium text-blue-800">
                Payment confirmed. Tracking will appear once the carrier dispatch scan is available.
              </p>
            </div>

            <div v-if="shipment.tracking_url" class="mt-3 flex justify-start">
              <button
                @click="openTracking(shipment.tracking_url)"
                class="inline-flex h-9 items-center gap-2 rounded-md border border-[#2F5597]/20 bg-[#2F5597] px-3 text-xs font-semibold text-white shadow-sm shadow-[#2F5597]/10 transition duration-200 hover:bg-[#24467f] focus:outline-none focus:ring-2 focus:ring-[#2F5597]/30"
                aria-label="Open carrier tracking"
              >
                <span class="inline-flex h-4 w-4 items-center justify-center" aria-hidden="true">
                  <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M11.5 3a.75.75 0 0 0 0 1.5h2.94l-7.47 7.47a.75.75 0 1 0 1.06 1.06l7.47-7.47V8.5a.75.75 0 0 0 1.5 0V3.75A.75.75 0 0 0 16.25 3H11.5Z" />
                    <path d="M4.75 5A1.75 1.75 0 0 0 3 6.75v8.5C3 16.216 3.784 17 4.75 17h8.5A1.75 1.75 0 0 0 15 15.25v-3a.75.75 0 0 0-1.5 0v3a.25.25 0 0 1-.25.25h-8.5a.25.25 0 0 1-.25-.25v-8.5a.25.25 0 0 1 .25-.25h3a.75.75 0 0 0 0-1.5h-3Z" />
                  </svg>
                </span>
                Track shipment
              </button>
            </div>

            <p v-if="shipment.last_updated_at" class="text-[11px] text-gray-500 mt-2">Updated {{ formatDate(shipment.last_updated_at) }}</p>
          </article>
        </div>
      </div>

      <template v-if="activeTab === 'orders'">
      <!-- Filter Bar -->
      <div class="sticky top-20 z-30 -mx-3 mb-8 px-3 sm:-mx-4 sm:px-4 lg:-mx-5 lg:px-5">
        <div class="rounded-2xl border border-gray-100 bg-white/95 p-6 shadow-lg shadow-slate-200/60 backdrop-blur-sm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
          <div class="relative md:col-span-3">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Filter by Status</label>
            <select v-model="selectedStatus" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="accepted">Accepted</option>
              <option value="backordered">Backordered</option>
              <option value="shipped">Shipped</option>
              <option value="invoiced">Invoiced / Complete</option>
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
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Created</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Journey</th>
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
                  <div class="mt-2">
                    <p class="text-xs text-gray-500">
                      {{ getOrderItemCount(order) }} item{{ getOrderItemCount(order) === 1 ? '' : 's' }}
                    </p>
                    <div v-if="getOrderItemNames(order).length" class="mt-2 space-y-1">
                      <p
                        v-for="(itemName, index) in getVisibleOrderItemNames(order)"
                        :key="`${order.order_number}-item-${index}`"
                        class="text-xs leading-5 text-gray-600"
                      >
                        {{ itemName }}
                      </p>
                      <button
                        v-if="hasHiddenOrderItems(order)"
                        type="button"
                        @click="toggleOrderItemsExpanded(order)"
                        class="inline-flex items-center text-xs font-semibold transition duration-200 hover:opacity-80"
                        style="color: #2F5597;"
                      >
                        {{ isOrderItemsExpanded(order) ? 'Show less' : `Show all ${getOrderItemCount(order)} items` }}
                      </button>
                    </div>
                  </div>
                  <p v-if="order.tracking_number" class="text-xs text-gray-500 mt-1">Tracking: {{ order.tracking_number }}</p>
                </td>
                <td class="px-5 py-4 text-sm text-gray-700 align-top">{{ formatDate(order.created_at) }}</td>
                <td class="px-5 py-4 align-top">
                  <p class="text-sm font-bold text-gray-900">{{ formatCurrency(order.total_amount) }}</p>
                  <p v-if="order.estimated_delivery" class="text-xs text-gray-500 mt-1">ETA: {{ formatDate(order.estimated_delivery) }}</p>
                </td>
                <td class="px-5 py-4 align-top">
                  <!-- Horizontal journey stepper with labels -->
                  <div class="flex items-start gap-0">
                    <template v-for="(step, idx) in statusSteps" :key="step.key">
                      <!-- connector line -->
                      <div v-if="idx > 0" class="h-px w-5 flex-shrink-0 mt-3.5 transition-colors duration-300"
                        :class="['completed','current'].includes(getStepState(order.status, step.key)) ? 'bg-[#2F5597]' : 'bg-gray-200'"
                      ></div>
                      <!-- step node + label -->
                      <div class="flex flex-col items-center" style="min-width:52px;">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center transition-all duration-300 shadow-sm"
                          :class="{
                            'bg-[#2F5597] text-white':                               getStepState(order.status, step.key) === 'completed',
                            'bg-[#2F5597] text-white ring-2 ring-blue-200 ring-offset-1': getStepState(order.status, step.key) === 'current',
                            'bg-gray-100 text-gray-400':                             getStepState(order.status, step.key) === 'upcoming',
                            'bg-red-100 text-red-500':                               getStepState(order.status, step.key) === 'cancelled',
                            'bg-gray-50  text-gray-300':                             getStepState(order.status, step.key) === 'disabled',
                          }"
                        >
                          <svg v-if="step.key === 'pending'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                          <svg v-else-if="step.key === 'accepted'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                          <svg v-else-if="step.key === 'backordered'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                          <svg v-else-if="step.key === 'shipped'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                          <svg v-else-if="step.key === 'invoiced'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span class="mt-1 text-center leading-tight"
                          :class="{
                            'text-[#2F5597] font-semibold': getStepState(order.status, step.key) === 'current',
                            'text-gray-700 font-medium':    getStepState(order.status, step.key) === 'completed',
                            'text-gray-400':                ['upcoming','disabled'].includes(getStepState(order.status, step.key)),
                            'text-red-500 font-semibold':   getStepState(order.status, step.key) === 'cancelled',
                          }"
                          style="font-size:9px; max-width:52px; word-break:break-word;"
                        >{{ step.label }}</span>
                      </div>
                    </template>
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
                      v-if="hasPayableInvoice(order)"
                      @click="payViaInvoice(order)"
                      class="px-3 py-1.5 text-xs rounded-md text-white font-semibold transition duration-200"
                      style="background-color: #2F5597;"
                      @mouseenter="$event.target.style.backgroundColor='#1f4788'"
                      @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                    >
                      Pay via Invoice
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
      </template>
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
          <!-- Status Section - vertical timeline tree -->
          <div class="mb-8 pb-8 border-b border-gray-200">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold text-gray-900">Order Journey</h3>
              <span :class="getStatusBadge(selectedOrder.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
                {{ formatStatus(selectedOrder.status) }}
              </span>
            </div>
            <!-- vertical timeline -->
            <ol class="relative ml-1">
              <li v-for="(step, idx) in statusSteps" :key="step.key" class="relative flex gap-4 pb-7 last:pb-0">
                <!-- vertical connector -->
                <div v-if="idx < statusSteps.length - 1"
                  class="absolute left-[15px] top-8 bottom-0 w-0.5 transition-colors duration-300"
                  :class="['completed','current'].includes(getStepState(selectedOrder.status, statusSteps[idx + 1].key)) ? 'bg-[#2F5597]' : 'bg-gray-200'"
                ></div>
                <!-- icon node -->
                <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center shadow-sm transition-all duration-300"
                  :class="{
                    'bg-[#2F5597] text-white':                                 getStepState(selectedOrder.status, step.key) === 'completed',
                    'bg-[#2F5597] text-white ring-4 ring-blue-100':            getStepState(selectedOrder.status, step.key) === 'current',
                    'bg-gray-100  text-gray-400':                              getStepState(selectedOrder.status, step.key) === 'upcoming',
                    'bg-red-100   text-red-500':                               getStepState(selectedOrder.status, step.key) === 'cancelled',
                    'bg-gray-50   text-gray-300':                              getStepState(selectedOrder.status, step.key) === 'disabled',
                  }"
                >
                  <!-- check for completed -->
                  <svg v-if="getStepState(selectedOrder.status, step.key) === 'completed'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                  <!-- x for cancelled -->
                  <svg v-else-if="getStepState(selectedOrder.status, step.key) === 'cancelled'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                  <!-- individual icons for current/upcoming/disabled -->
                  <template v-else>
                    <svg v-if="step.key === 'pending'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <svg v-else-if="step.key === 'accepted'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg v-else-if="step.key === 'backordered'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg v-else-if="step.key === 'shipped'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <svg v-else-if="step.key === 'invoiced'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                  </template>
                </div>
                <!-- text -->
                <div class="flex-1 min-w-0 pt-0.5">
                  <p class="text-sm font-semibold leading-tight"
                    :class="{
                      'text-[#2F5597]': getStepState(selectedOrder.status, step.key) === 'current',
                      'text-gray-900':  getStepState(selectedOrder.status, step.key) === 'completed',
                      'text-gray-400':  ['upcoming','disabled'].includes(getStepState(selectedOrder.status, step.key)),
                      'text-red-600':   getStepState(selectedOrder.status, step.key) === 'cancelled',
                    }"
                  >{{ step.label }}
                    <span v-if="getStepState(selectedOrder.status, step.key) === 'current'" class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-[#2F5597] uppercase tracking-wide">Current</span>
                  </p>
                  <p class="text-xs mt-0.5"
                    :class="{
                      'text-gray-600': ['completed','current'].includes(getStepState(selectedOrder.status, step.key)),
                      'text-gray-300': ['upcoming','disabled'].includes(getStepState(selectedOrder.status, step.key)),
                      'text-red-400':  getStepState(selectedOrder.status, step.key) === 'cancelled',
                    }"
                  >{{ step.desc }}</p>
                </div>
              </li>
            </ol>
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
              v-if="hasPayableInvoice(selectedOrder)"
              @click="payViaInvoice(selectedOrder)"
              class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200"
              style="background-color: #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#1f4788'"
              @mouseleave="$event.target.style.backgroundColor='#2F5597'"
            >
              Pay via Invoice
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/authStore';
import { useToastStore } from '../../stores/toastStore';
import axios from 'axios';
import Navbar from '../../components/Navbar.vue';
import { usePricingSettings } from '../../composables/usePricingSettings';

export default {
  components: {
    Navbar,
  },
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    const toastStore = useToastStore();
    const { loadPricingSettings, formatUsdUsingCurrentCurrency } = usePricingSettings();
    const orders = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const selectedStatus = ref('');
    const searchQuery = ref('');
    const sortBy = ref('created_desc');
    const activeTab = ref('orders');
    const selectedOrder = ref(null);
    const processingOrderNumber = ref(null);
    const expandedOrderItems = ref({});
    const pagination = ref({
      current_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0,
      last_page: 1,
    });
    const liveShipments = ref([]);
    const liveShippingLoading = ref(false);
    const liveShippingError = ref(null);
    let liveRefreshTimer = null;

    const filteredOrders = computed(() => {
      const filtered = orders.value.filter(order => {
        const statusMatch = !selectedStatus.value || order.status === selectedStatus.value;
        const searchMatch = !searchQuery.value || order.order_number.toLowerCase().includes(searchQuery.value.toLowerCase());
        return statusMatch && searchMatch;
      });

      const sorted = [...filtered];
      switch (sortBy.value) {
        case 'created_asc':
          sorted.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));
          break;
        case 'amount_desc':
          sorted.sort((a, b) => Number(b.total_amount || 0) - Number(a.total_amount || 0));
          break;
        case 'amount_asc':
          sorted.sort((a, b) => Number(a.total_amount || 0) - Number(b.total_amount || 0));
          break;
        case 'status_asc':
          sorted.sort((a, b) => String(a.status || '').localeCompare(String(b.status || '')));
          break;
        case 'created_desc':
        default:
          sorted.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0));
          break;
      }

      return sorted;
    });

    const activeShipmentsCount = computed(() => {
      return liveShipments.value.filter((shipment) => shipment.status !== 'delivered').length;
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
          toastStore.addToast('Order cancelled successfully', 'success', 3000, { category: 'orders' });
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
      sortBy.value = 'created_desc';
      pagination.value.current_page = 1;
      expandedOrderItems.value = {};
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
        pending:     'bg-yellow-100 text-yellow-800',
        accepted:    'bg-blue-100 text-blue-800',
        // legacy local aliases
        processing:  'bg-blue-100 text-blue-800',
        confirmed:   'bg-blue-100 text-blue-800',
        backordered: 'bg-orange-100 text-orange-800',
        shipped:     'bg-indigo-100 text-indigo-800',
        invoiced:    'bg-green-100 text-green-800',
        // legacy
        delivered:   'bg-green-100 text-green-800',
        cancelled:   'bg-red-100 text-red-800',
      };
      return badges[status] || 'bg-gray-100 text-gray-800';
    };

    const formatCurrency = (amount) => {
      return formatUsdUsingCurrentCurrency(Number(amount || 0));
    };

    const truncateProductName = (name, maxLength = 56) => {
      const text = String(name || 'Item details unavailable').trim();
      if (text.length <= maxLength) {
        return text;
      }
      return `${text.slice(0, Math.max(1, maxLength - 1)).trimEnd()}…`;
    };

    const getOrderItemCount = (order) => {
      return Array.isArray(order?.items) ? order.items.length : 0;
    };

    const getOrderItemNames = (order) => {
      const items = Array.isArray(order?.items) ? order.items : [];
      const names = items
        .map((item, index) => String(
          item?.product_name
          || item?.productName
          || item?.name
          || item?.description
          || item?.partDescription
          || item?.mfg_part_number
          || item?.mfgPartNo
          || item?.sku
          || ''
        ).trim())
        .filter(Boolean);

      if (names.length > 0) {
        return names;
      }

      const fallback = String(order?.primary_item_name || '').trim();
      if (fallback && fallback.toLowerCase() !== 'item details unavailable') {
        return [fallback];
      }

      const genericNames = items.map((_, index) => `Order Item ${index + 1}`);
      return genericNames.filter(Boolean);
    };

    const isOrderItemsExpanded = (order) => {
      const orderNumber = String(order?.order_number || '').trim();
      if (!orderNumber) return false;
      return !!expandedOrderItems.value[orderNumber];
    };

    const getVisibleOrderItemNames = (order) => {
      const names = getOrderItemNames(order);
      if (isOrderItemsExpanded(order)) return names;
      return names.slice(0, 2);
    };

    const hasHiddenOrderItems = (order) => {
      return getOrderItemNames(order).length > 2;
    };

    const toggleOrderItemsExpanded = (order) => {
      const orderNumber = String(order?.order_number || '').trim();
      if (!orderNumber || !hasHiddenOrderItems(order)) return;

      expandedOrderItems.value = {
        ...expandedOrderItems.value,
        [orderNumber]: !expandedOrderItems.value[orderNumber],
      };
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

    // TD SYNNEX canonical order status flow (no local "processing" or "confirmed")
    const statusSteps = [
      { key: 'pending',     label: 'Order Placed',        desc: 'Your order has been submitted'              },
      { key: 'accepted',    label: 'Accepted by Supplier', desc: 'TD SYNNEX has accepted your order'          },
      { key: 'backordered', label: 'Backordered',          desc: 'Item(s) awaiting stock availability'        },
      { key: 'shipped',     label: 'Shipped',              desc: 'Your order is on the way'                   },
      { key: 'invoiced',    label: 'Invoiced / Complete',  desc: 'Order fulfilled and invoice issued'         },
    ];
    // Old local statuses mapped to the canonical step keys for display
    const _statusAlias = {
      processing: 'accepted', confirmed: 'accepted', delivered: 'invoiced',
    };
    const _stepOrder = ['pending', 'accepted', 'backordered', 'shipped', 'invoiced'];

    const resolveStatus = (s) => _statusAlias[s] ?? s;

    const getStepState = (orderStatus, stepKey) => {
      const resolved = resolveStatus(orderStatus);
      if (resolved === 'cancelled') {
        return stepKey === 'pending' ? 'cancelled' : 'disabled';
      }
      const currentIdx = _stepOrder.indexOf(resolved);
      const stepIdx    = _stepOrder.indexOf(stepKey);
      if (currentIdx === -1) return 'upcoming';
      if (stepIdx < currentIdx)  return 'completed';
      if (stepIdx === currentIdx) return 'current';
      return 'upcoming';
    };

    const liveStatusBadge = (status) => {
      const badges = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        confirmed: 'bg-blue-100 text-blue-800',
        shipped: 'bg-indigo-100 text-indigo-800',
        in_transit: 'bg-indigo-100 text-indigo-800',
        delivered: 'bg-green-100 text-green-800',
        returned: 'bg-red-100 text-red-800',
      };
      return badges[status] || 'bg-gray-100 text-gray-800';
    };

    const liveStatusDot = (status) => {
      const dots = {
        pending: '#f59e0b',
        processing: '#2563eb',
        confirmed: '#2563eb',
        shipped: '#4f46e5',
        in_transit: '#4f46e5',
        delivered: '#16a34a',
        returned: '#dc2626',
      };
      return dots[status] || '#6b7280';
    };

    const normalizeTrackingUrl = (trackingUrl) => {
      const rawUrl = String(trackingUrl || '').trim();
      if (!rawUrl) return '';

      try {
        const url = new URL(rawUrl);
        if (url.hostname === 'tracking.fedex.com' && url.pathname === '/track') {
          const trackingNumber = url.searchParams.get('tracknumbers') || url.searchParams.get('trknbr');
          if (trackingNumber) {
            return `https://www.fedex.com/fedextrack/?trknbr=${encodeURIComponent(trackingNumber)}`;
          }
        }
      } catch (_) {
        return rawUrl;
      }

      return rawUrl;
    };

    const openTracking = (trackingUrl) => {
      const normalizedTrackingUrl = normalizeTrackingUrl(trackingUrl);
      if (!normalizedTrackingUrl) {
        return;
      }
      window.open(normalizedTrackingUrl, '_blank', 'noopener,noreferrer');
    };

    const hasPayableInvoice = (order) => {
      if (!order) return false;
      const invoiceNumber = String(order.linked_invoice_number || '').trim();
      if (!invoiceNumber) return false;
      const status = String(order.linked_invoice_status || '').toLowerCase();
      return status !== 'paid';
    };

    const payViaInvoice = async (order) => {
      if (!hasPayableInvoice(order)) {
        toastStore.addToast('No payable invoice linked to this order yet.', 'warning', 3000, { category: 'invoices' });
        return;
      }

      selectedOrder.value = null;
      await router.push({
        name: 'invoices',
        query: {
          selectInvoices: order.linked_invoice_number,
          focusInvoice: order.linked_invoice_number,
          from: 'orders',
        },
      });
      toastStore.addToast(`Invoice ${order.linked_invoice_number} is ready for payment`, 'info', 3000, { category: 'invoices' });
    };


    const fetchLiveShipping = async () => {
      if (!authStore.isAuthenticated) {
        return;
      }

      // Only show loading spinner on first load
      if (liveShipments.value.length === 0 && !liveShippingLoading.value) {
        liveShippingLoading.value = true;
      }
      liveShippingError.value = null;

      try {
        const response = await axios.get('/api/v1/orders/shipping/live');
        if (response.data?.success) {
          liveShipments.value = response.data.data || [];
        } else {
          liveShippingError.value = response.data?.message || 'Failed to load live shipping data';
        }
      } catch (err) {
        liveShippingError.value = err.response?.data?.message || err.message || 'Failed to load live shipping data';
      } finally {
        liveShippingLoading.value = false;
      }
    };

    const startLiveShippingRefresh = () => {
      if (liveRefreshTimer) {
        clearInterval(liveRefreshTimer);
      }

      liveRefreshTimer = setInterval(() => {
        fetchLiveShipping();
      }, 60000); // 60 seconds
    };

    const stopLiveShippingRefresh = () => {
      if (liveRefreshTimer) {
        clearInterval(liveRefreshTimer);
        liveRefreshTimer = null;
      }
    };

    onMounted(() => {
      loadPricingSettings();
      fetchOrders();
      fetchLiveShipping();
      startLiveShippingRefresh();
    });

    onUnmounted(() => {
      stopLiveShippingRefresh();
    });

    return {
      orders,
      loading,
      error,
      selectedStatus,
      searchQuery,
      sortBy,
      activeTab,
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
      truncateProductName,
      getOrderItemCount,
      getOrderItemNames,
      getVisibleOrderItemNames,
      hasHiddenOrderItems,
      isOrderItemsExpanded,
      toggleOrderItemsExpanded,
      getOrdersCountByStatus,
      getStatusProgress,
      liveShipments,
      activeShipmentsCount,
      liveShippingLoading,
      liveShippingError,
      fetchLiveShipping,
      liveStatusBadge,
      liveStatusDot,
      openTracking,
      hasPayableInvoice,
      payViaInvoice,
      statusSteps,
      getStepState,
      resolveStatus,
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
