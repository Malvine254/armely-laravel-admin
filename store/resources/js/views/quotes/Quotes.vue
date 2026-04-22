<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <Navbar />

    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Your Quotes</h1>
        <p class="text-gray-600 text-lg">Track and review your quote history</p>
      </div>

      <!-- Quick Stats -->
      <div v-if="visibleQuotes.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f7fbff 62%, #eef4ff 100%); border-color: #d9e6f7; box-shadow: 0 12px 26px rgba(47,85,151,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(47,85,151,0.2) 0%, rgba(47,85,151,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Total Quotes</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ visibleQuotes.length }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #2F5597, #7fa2d8);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f7fbff 62%, #eef4ff 100%); border-color: #d9e6f7; box-shadow: 0 12px 26px rgba(47,85,151,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(47,85,151,0.2) 0%, rgba(47,85,151,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Pending</p>
          <p class="text-3xl font-bold text-blue-600 mt-2">{{ getQuotesCountByStatus('pending_review') }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #2F5597, #7fa2d8);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f6fff9 62%, #edfff3 100%); border-color: #cce9d6; box-shadow: 0 12px 24px rgba(22,163,74,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(34,197,94,0.22) 0%, rgba(34,197,94,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Approved</p>
          <p class="text-3xl font-bold text-green-600 mt-2">{{ getQuotesCountByStatus('approved') }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #16a34a, #86efac);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f6f5ff 62%, #eeecff 100%); border-color: #dcd9ff; box-shadow: 0 12px 24px rgba(99,102,241,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, rgba(99,102,241,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Converted</p>
          <p class="text-3xl font-bold mt-2" style="color: #6366f1;">{{ getQuotesCountByStatus('converted') }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #6366f1, #a5b4fc);"></div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="sticky top-20 z-30 -mx-3 mb-8 px-3 sm:-mx-4 sm:px-4 lg:-mx-5 lg:px-5">
        <div class="rounded-2xl border border-gray-100 bg-white/95 p-6 shadow-lg shadow-slate-200/60 backdrop-blur-sm">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
          <div class="relative md:col-span-3">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Filter by Status</label>
            <select v-model="selectedStatus" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
              <option value="">All Statuses</option>
              <option value="draft">Draft</option>
              <option value="pending_review">Pending Review</option>
              <option value="approved">Approved</option>
              <option value="converted">Converted</option>
              <option value="cancelled">Cancelled</option>
              <option value="rejected">Rejected</option>
              <option value="expired">Expired</option>
            </select>
          </div>

          <div class="relative md:col-span-4">
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Search Quotes</label>
            <input v-model="searchQuery" type="text" placeholder="Enter quote ID..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="focus:ring-color: #2F5597; border-color: #e5e7eb;">
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
            <button @click="fetchQuotes" class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200 hover:shadow-lg" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
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
          <p class="text-gray-600 font-medium">Loading your quotes...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 mb-8">
        <div class="flex gap-4">
          <svg class="h-6 w-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h3 class="font-semibold text-red-900 text-lg">Unable to Load Quotes</h3>
            <p class="text-red-700 mt-1">{{ error }}</p>
            <button @click="fetchQuotes" class="mt-3 text-red-700 hover:text-red-900 font-semibold underline">Try Again</button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredQuotes.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-1">No Quotes Yet</h3>
        <p class="text-gray-600 mb-6">Add products to your cart to create a quote</p>
        <router-link to="/products" class="inline-block px-6 py-3 text-white rounded-lg font-semibold transition duration-200 hover:shadow-lg" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
          Browse Products
        </router-link>
      </div>

      <!-- Quotes Table -->
      <div v-else class="bg-white rounded-xl shadow-sm border overflow-hidden" style="border-color: #d9e6f7;">
        <div ref="bulkActionsBarRef" class="px-4 sm:px-5 py-3 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="border-color: #d9e6f7; background: #f9fcff;">
          <p class="text-sm font-medium text-gray-700">
            {{ selectedQuoteIds.length }} selected
          </p>
          <div class="flex flex-wrap items-center gap-2">
            <button
              @click="reviseSelectedQuotes"
              :disabled="selectedQuoteIds.length !== 1 || hasCancelledSelectedQuote || !!processingQuoteId"
              class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="color: #2F5597; border: 1px solid #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              {{ hasCancelledSelectedQuote ? 'Cancelled Not Revisable' : (selectedQuoteIds.length > 1 ? 'Select 1 Quote' : 'Revise Selected') }}
            </button>
            <button
              @click="downloadSelectedPdfs"
              :disabled="selectedQuoteIds.length === 0"
              class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="color: #2F5597; border: 1px solid #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              Download Selected PDFs
            </button>
            <button
              @click="cancelSelectedQuotes"
              :disabled="selectedCancellableQuotes.length === 0"
              class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="color: #e74c3c; border: 1px solid #e74c3c;"
              @mouseenter="$event.target.style.backgroundColor='#fadbd8'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              Cancel Selected
            </button>
            <button
              @click="clearSelection"
              :disabled="selectedQuoteIds.length === 0"
              class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="color: #6b7280; border: 1px solid #d1d5db;"
              @mouseenter="$event.target.style.backgroundColor='#f3f4f6'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              Clear Selection
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b" style="background: linear-gradient(90deg, #f7fbff, #edf4fc); border-color: #d9e6f7;">
                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                  <input
                    type="checkbox"
                    :checked="allOnPageSelected"
                    @change="toggleSelectAllOnPage($event.target.checked)"
                    class="h-4 w-4 rounded border-gray-300"
                  />
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Quote Hierarchy</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Created / Expires</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Amount</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Progress</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="quote in paginatedQuotes"
                :key="quote.id"
                class="border-b last:border-b-0 hover:bg-[#f8fbff] transition duration-200"
                style="border-color: #ecf3fb;"
              >
                <td class="px-4 py-4 align-top">
                  <input
                    type="checkbox"
                    :checked="selectedQuoteIds.includes(quote.quote_id)"
                    @change="toggleQuoteSelection(quote.quote_id, $event.target.checked)"
                    class="h-4 w-4 rounded border-gray-300"
                  />
                </td>
                <td class="px-5 py-4 align-top">
                  <button
                    @click="viewQuote(quote)"
                    class="text-left font-semibold font-mono transition duration-200 hover:opacity-80"
                    style="color: #2F5597;"
                  >
                    {{ quote.quote_id }}
                  </button>
                  <div class="mt-2">
                    <p class="text-xs text-gray-500">
                      {{ getQuoteItemCount(quote) }} item{{ getQuoteItemCount(quote) === 1 ? '' : 's' }}
                    </p>
                    <div v-if="getQuoteItemNames(quote).length" class="mt-2 space-y-1">
                      <p
                        v-for="(itemName, index) in getVisibleQuoteItemNames(quote)"
                        :key="`${quote.quote_id}-item-${index}`"
                        class="text-xs leading-5 text-gray-600"
                      >
                        {{ itemName }}
                      </p>
                      <button
                        v-if="hasHiddenQuoteItems(quote)"
                        type="button"
                        @click="toggleQuoteItemsExpanded(quote)"
                        class="inline-flex items-center text-xs font-semibold transition duration-200 hover:opacity-80"
                        style="color: #2F5597;"
                      >
                        {{ isQuoteItemsExpanded(quote) ? 'Show less' : `Show all ${getQuoteItemCount(quote)} items` }}
                      </button>
                    </div>
                  </div>
                  <div v-if="isCancelledFamilyRow(quote)" class="mt-1">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap bg-red-100 text-red-700">
                      Cancelled Group
                    </span>
                  </div>
                  <div v-if="getQuoteChildren(quote).length" class="mt-2 space-y-1">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Revisions</p>
                    <div
                      v-for="child in getQuoteChildren(quote)"
                      :key="`child-${child.quote_id}`"
                      class="flex items-center gap-2 text-xs"
                    >
                      <span class="text-gray-400">↳</span>
                      <button
                        @click="viewQuote(child)"
                        class="font-mono text-left text-gray-600 hover:text-[#2F5597] transition duration-200"
                      >
                        {{ child.quote_id }}
                      </button>
                      <span :class="getStatusBadge(child.status)" class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap">
                        {{ formatStatus(child.status) }}
                      </span>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 align-top">
                  <span :class="getStatusBadge(quote.status)" class="inline-flex px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                    {{ formatStatus(quote.status) }}
                  </span>
                </td>
                <td class="px-5 py-4 text-sm text-gray-700 align-top">
                  <p class="font-medium text-gray-900">{{ formatDate(quote.created_at) }}</p>
                  <p class="text-xs text-gray-500 mt-1">Expires: {{ getQuoteExpiryDisplay(quote) }}</p>
                </td>
                <td class="px-5 py-4 align-top">
                  <p class="text-sm font-bold text-gray-900">{{ formatCurrency(quote.total_amount) }}</p>
                  <p v-if="getLinkedOrder(quote)" class="text-xs text-gray-500 mt-1">Order: {{ getLinkedOrder(quote).order_number }}</p>
                </td>
                <td class="px-5 py-4 align-top">
                  <div v-if="['draft', 'pending_review', 'approved'].includes(quote.status)" class="w-36">
                    <div class="w-full rounded-full h-2 mb-1" style="background-color: #d9e6f7;">
                      <div class="h-2 rounded-full" style="background-color: #2F5597;" :style="{ inlineSize: getQuoteProgress(quote.status) + '%' }"></div>
                    </div>
                    <p class="text-xs text-gray-600">{{ getQuoteProgress(quote.status) }}%</p>
                  </div>
                  <p v-else class="text-xs text-gray-500">-</p>
                </td>
                <td class="px-5 py-4 align-top">
                  <div class="flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button
                      @click="downloadQuotePdf(quote)"
                      class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200"
                      style="color: #2F5597; border: 1px solid #2F5597;"
                      @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
                      @mouseleave="$event.target.style.backgroundColor='transparent'"
                    >
                      PDF
                    </button>
                    <button
                      v-if="canViewLinkedInvoice(quote)"
                      @click="viewLinkedInvoice(quote)"
                      class="px-3 py-1.5 text-xs rounded-md text-white font-semibold transition duration-200"
                      style="background-color: #2F5597;"
                      @mouseenter="$event.target.style.backgroundColor='#1f4788'"
                      @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                    >
                      View Invoice
                    </button>
                    <button
                      v-if="canCancelQuote(quote)"
                      @click="cancelQuote(quote)"
                      :disabled="processingQuoteId === quote.quote_id"
                      class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                      style="color: #e74c3c; border: 1px solid #e74c3c;"
                      @mouseenter="$event.target.style.backgroundColor='#fadbd8'"
                      @mouseleave="$event.target.style.backgroundColor='transparent'"
                    >
                      {{ processingQuoteId === quote.quote_id ? 'Cancelling...' : 'Cancel' }}
                    </button>
                    <button
                      v-if="canReviseQuote(quote)"
                      @click="reviseQuote(quote)"
                      :disabled="processingQuoteId === quote.quote_id"
                      class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                      style="color: #2F5597; border: 1px solid #2F5597;"
                      @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
                      @mouseleave="$event.target.style.backgroundColor='transparent'"
                    >
                      {{ processingQuoteId === quote.quote_id ? 'Preparing...' : (isQuoteLockedForPayment(quote) ? 'Re-open Quote' : 'Revise') }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="border-t px-4 sm:px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4" style="border-color: #d9e6f7; background: #f9fcff;">
          <div class="flex items-center gap-3 text-sm text-gray-700">
            <span class="font-medium">Showing {{ paginationStart }}-{{ paginationEnd }} of {{ filteredQuotes.length }}</span>
            <div class="flex items-center gap-2">
              <label class="text-xs font-semibold uppercase tracking-wide text-gray-600">Rows</label>
              <select
                v-model.number="pageSize"
                class="px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white text-gray-900 focus:outline-none"
                style="border-color: #d9e6f7;"
              >
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
              </select>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="goToPreviousPage"
              :disabled="currentPage === 1"
              class="px-3 py-1.5 text-xs sm:text-sm rounded-md border font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="border-color: #d9e6f7; color: #2F5597;"
            >
              Previous
            </button>

            <button
              v-for="page in visiblePageNumbers"
              :key="`page-${page}`"
              @click="goToPage(page)"
              class="px-3 py-1.5 text-xs sm:text-sm rounded-md border font-semibold transition duration-200"
              :class="page === currentPage ? 'text-white' : 'text-[#2F5597] bg-white hover:bg-[#edf3fb]'"
              :style="page === currentPage ? 'background-color: #2F5597; border-color: #2F5597;' : 'border-color: #d9e6f7;'"
            >
              {{ page }}
            </button>

            <button
              @click="goToNextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-1.5 text-xs sm:text-sm rounded-md border font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="border-color: #d9e6f7; color: #2F5597;"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showFloatingBulkActions"
      class="fixed inset-x-4 bottom-4 z-[9998] flex justify-center"
    >
      <div class="w-full max-w-5xl rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-300/40">
        <div class="flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm font-semibold text-gray-800">
            {{ selectedQuoteIds.length }} selected
          </p>
          <div class="flex flex-wrap items-center gap-2">
            <button
              @click="reviseSelectedQuotes"
              :disabled="selectedQuoteIds.length !== 1 || hasCancelledSelectedQuote || !!processingQuoteId"
              class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="color: #2F5597; border: 1px solid #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              {{ hasCancelledSelectedQuote ? 'Cancelled Not Revisable' : (selectedQuoteIds.length > 1 ? 'Select 1 Quote' : 'Revise Selected') }}
            </button>
            <button
              @click="downloadSelectedPdfs"
              :disabled="selectedQuoteIds.length === 0"
              class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="color: #2F5597; border: 1px solid #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              Download Selected PDFs
            </button>
            <button
              @click="cancelSelectedQuotes"
              :disabled="selectedCancellableQuotes.length === 0"
              class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="color: #e74c3c; border: 1px solid #e74c3c;"
              @mouseenter="$event.target.style.backgroundColor='#fadbd8'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              Cancel Selected
            </button>
            <button
              @click="clearSelection"
              :disabled="selectedQuoteIds.length === 0"
              class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed"
              style="color: #6b7280; border: 1px solid #d1d5db;"
              @mouseenter="$event.target.style.backgroundColor='#f3f4f6'"
              @mouseleave="$event.target.style.backgroundColor='transparent'"
            >
              Clear Selection
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quote Detail Modal -->
    <div v-if="selectedQuote" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" @click="closeSelectedQuoteModal">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
        <!-- Modal Header -->
        <div class="sticky top-0 text-white px-6 py-6 flex items-center justify-between rounded-t-2xl" style="background: linear-gradient(90deg, #2F5597, #1f4788);">
          <div>
            <p class="text-sm font-semibold text-gray-200 uppercase tracking-wide">Quote</p>
            <h2 class="text-2xl font-bold text-white">{{ selectedQuote.quote_id }}</h2>
          </div>
          <button @click="closeSelectedQuoteModal" class="text-gray-300 hover:text-white transition duration-200">
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
              <h3 class="text-lg font-bold text-gray-900">Quote Status</h3>
              <span :class="getStatusBadge(selectedQuote.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
                {{ formatStatus(selectedQuote.status) }}
              </span>
            </div>
            <div v-if="['draft', 'pending_review', 'approved'].includes(selectedQuote.status)">
              <div class="w-full rounded-full h-2 mb-2" style="background-color: #d9e6f7;">
                <div class="h-2 rounded-full" style="background-color: #2F5597;" :style="{ inlineSize: getQuoteProgress(selectedQuote.status) + '%' }"></div>
              </div>
              <p class="text-xs text-gray-600">{{ getQuoteProgress(selectedQuote.status) }}% Complete</p>
            </div>
          </div>

          <!-- Quote Information Grid -->
          <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Created Date</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedQuote.created_at) }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Total Amount</p>
              <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(selectedQuote.total_amount) }}</p>
            </div>
            <div v-if="selectedQuote.expires_at">
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Expires On</p>
              <p class="text-lg font-semibold text-gray-900">{{ getQuoteExpiryDisplay(selectedQuote) }}</p>
            </div>
            <div v-if="getLinkedOrder(selectedQuote)">
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Linked Order</p>
              <p class="text-lg font-semibold text-gray-900 font-mono">{{ getLinkedOrder(selectedQuote).order_number }}</p>
            </div>
            <div v-if="getLinkedInvoice(selectedQuote)">
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Linked Invoice</p>
              <p class="text-lg font-semibold text-gray-900 font-mono">{{ getLinkedInvoice(selectedQuote).invoice_number }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button @click="closeSelectedQuoteModal" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition duration-200">
              Close
            </button>
            <button
              @click="downloadQuotePdf(selectedQuote)"
              class="flex-1 px-4 py-3 border border-[#2F5597] text-[#2F5597] rounded-lg font-semibold hover:bg-[#edf3fb] transition duration-200"
            >
              Download PDF
            </button>
            <button
              v-if="getLinkedOrder(selectedQuote)"
              @click="viewLinkedOrder(selectedQuote)"
              class="flex-1 px-4 py-3 border border-[#7c3aed] text-[#7c3aed] rounded-lg font-semibold hover:bg-[#f3e8ff] transition duration-200"
            >
              View Order
            </button>
            <button
              v-if="canViewLinkedInvoice(selectedQuote)"
              @click="viewLinkedInvoice(selectedQuote)"
              class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200"
              style="background-color: #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#1f4788'"
              @mouseleave="$event.target.style.backgroundColor='#2F5597'"
            >
              View Invoice
            </button>
            <button
              v-if="canCancelQuote(selectedQuote)"
              @click="cancelQuote(selectedQuote)"
              :disabled="processingQuoteId === selectedQuote.quote_id"
              class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              style="background-color: #e74c3c;"
              @mouseenter="$event.target.style.backgroundColor='#c0392b'"
              @mouseleave="$event.target.style.backgroundColor='#e74c3c'"
            >
              {{ processingQuoteId === selectedQuote.quote_id ? 'Cancelling...' : 'Cancel Quote' }}
            </button>
            <button
              v-if="canReviseQuote(selectedQuote)"
              @click="reviseQuote(selectedQuote)"
              :disabled="processingQuoteId === selectedQuote.quote_id"
              class="flex-1 px-4 py-3 border border-[#2F5597] text-[#2F5597] rounded-lg font-semibold hover:bg-[#edf3fb] transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processingQuoteId === selectedQuote.quote_id ? 'Preparing...' : (isQuoteLockedForPayment(selectedQuote) ? 'Re-open Quote' : 'Revise Quote') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'
import { useCartStore } from '../../stores/cartStore'
import Navbar from '../../components/Navbar.vue'
import axios from 'axios'
import { usePricingSettings } from '../../composables/usePricingSettings'

export default {
  components: { Navbar },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const toastStore = useToastStore()
    const cartStore = useCartStore()
    const { loadPricingSettings, formatUsdUsingCurrentCurrency } = usePricingSettings()
    const quotes = ref([])
    const loading = ref(false)
    const error = ref(null)
    const selectedStatus = ref('')
    const searchQuery = ref('')
    const sortBy = ref('created_desc')
    const currentPage = ref(1)
    const pageSize = ref(10)
    const selectedQuoteIds = ref([])
    const selectedQuote = ref(null)
    const processingQuoteId = ref(null)
    const ordersByQuoteId = ref({})
    const invoicesByOrderNumber = ref({})
    const productNameMap = ref({})
    const expandedQuoteItems = ref({})
    const bulkActionsBarRef = ref(null)
    const isBulkActionsBarHidden = ref(false)
    const productLookupPromises = new Map()
    const nowTick = ref(Date.now())
    let countdownTimer = null

    const visibleQuotes = computed(() => {
      const revisionSource = String(cartStore.revisionSourceQuoteId || '').trim()
      if (!revisionSource) return quotes.value

      return quotes.value.filter((quote) => String(quote?.quote_id || '').trim() !== revisionSource)
    })

    const normalizeQuoteId = (value) => String(value || '').trim().toUpperCase()

    const getRevisedFromQuoteId = (quote, knownQuoteIds = null) => {
      const currentId = normalizeQuoteId(quote?.quote_id)
      const direct = normalizeQuoteId(quote?.revised_from_quote_id)
      if (direct && direct !== currentId) return direct

      const raw = normalizeQuoteId(quote?.raw_data?.revised_from_quote_id)
      if (raw && raw !== currentId) return raw

      // Fallback for legacy rows where only description text carries linkage.
      const description = String(quote?.description || '')
      const match = description.match(/revision\s+of\s+quote\s+([a-z0-9\-]+)/i)
      const inferred = normalizeQuoteId(match ? match[1] : '')
      if (!inferred || inferred === currentId) return ''
      if (knownQuoteIds && !knownQuoteIds.has(inferred)) return ''

      return inferred
    }

    const quoteFamilies = computed(() => {
      const byId = new Map()
      const grouped = new Map()

      visibleQuotes.value.forEach((quote) => {
        const normalizedId = normalizeQuoteId(quote?.quote_id)
        if (normalizedId) {
          byId.set(normalizedId, quote)
        }
      })

      const resolveRootId = (quote) => {
        let cursor = quote
        const seen = new Set()

        while (cursor) {
          const currentId = normalizeQuoteId(cursor?.quote_id)
          if (!currentId || seen.has(currentId)) {
            break
          }

          seen.add(currentId)
          const parentId = getRevisedFromQuoteId(cursor, byId)
          if (!parentId || !byId.has(parentId)) {
            return currentId
          }

          cursor = byId.get(parentId)
        }

        return normalizeQuoteId(quote?.quote_id)
      }

      visibleQuotes.value.forEach((quote) => {
        const rootId = resolveRootId(quote)
        const list = grouped.get(rootId) || []
        list.push(quote)
        grouped.set(rootId, list)
      })

      return Array.from(grouped.entries()).map(([rootId, chain]) => {
        const sortedChain = [...chain].sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0))
        const primary = sortedChain[sortedChain.length - 1]

        return {
          rootId,
          chain: sortedChain,
          primary,
        }
      })
    })

    const quoteFamilyRows = computed(() => {
      const rows = []

      quoteFamilies.value.forEach((family) => {
        const activeChain = family.chain.filter((quote) => String(quote?.status || '').toLowerCase() !== 'cancelled')
        const cancelledChain = family.chain.filter((quote) => String(quote?.status || '').toLowerCase() === 'cancelled')

        if (activeChain.length) {
          const primary = activeChain[activeChain.length - 1]
          rows.push({
            rowId: `${family.rootId}-active`,
            rootId: family.rootId,
            category: 'active',
            chain: activeChain,
            primary,
            children: activeChain.filter((quote) => quote.quote_id !== primary.quote_id),
          })
        }

        if (cancelledChain.length) {
          const primary = cancelledChain[cancelledChain.length - 1]
          rows.push({
            rowId: `${family.rootId}-cancelled`,
            rootId: family.rootId,
            category: 'cancelled',
            chain: cancelledChain,
            primary,
            children: cancelledChain.filter((quote) => quote.quote_id !== primary.quote_id),
          })
        }
      })

      return rows
    })

    const filteredQuoteFamilies = computed(() => {
      const term = (searchQuery.value || '').toLowerCase()

      const filtered = quoteFamilyRows.value.filter((family) => {
        const statusMatch = !selectedStatus.value || family.chain.some((quote) => quote.status === selectedStatus.value)

        if (!statusMatch) return false
        if (!term) return true

        return family.chain.some((quote) => {
          const linkedOrder = getLinkedOrder(quote)
          const linkedInvoice = getLinkedInvoice(quote)
          return String(quote.quote_id || '').toLowerCase().includes(term)
            || String(linkedOrder?.order_number || '').toLowerCase().includes(term)
            || String(linkedInvoice?.invoice_number || '').toLowerCase().includes(term)
        })
      })

      const sorted = [...filtered]
      switch (sortBy.value) {
        case 'created_asc':
          sorted.sort((a, b) => new Date(a.primary?.created_at || 0) - new Date(b.primary?.created_at || 0))
          break
        case 'amount_desc':
          sorted.sort((a, b) => Number(b.primary?.total_amount || 0) - Number(a.primary?.total_amount || 0))
          break
        case 'amount_asc':
          sorted.sort((a, b) => Number(a.primary?.total_amount || 0) - Number(b.primary?.total_amount || 0))
          break
        case 'status_asc':
          sorted.sort((a, b) => String(a.primary?.status || '').localeCompare(String(b.primary?.status || '')))
          break
        case 'created_desc':
        default:
          sorted.sort((a, b) => new Date(b.primary?.created_at || 0) - new Date(a.primary?.created_at || 0))
          break
      }

      return sorted
    })

    const quoteFamilyByPrimaryId = computed(() => {
      return filteredQuoteFamilies.value.reduce((acc, family) => {
        const primaryId = normalizeQuoteId(family?.primary?.quote_id)
        if (primaryId) {
          acc[primaryId] = family
        }
        return acc
      }, {})
    })

    const filteredQuotes = computed(() => {
      return filteredQuoteFamilies.value.map((family) => family.primary)
    })

    const totalPages = computed(() => {
      const pages = Math.ceil(filteredQuoteFamilies.value.length / pageSize.value)
      return Math.max(1, pages)
    })

    const paginatedQuoteFamilies = computed(() => {
      const safePage = Math.min(currentPage.value, totalPages.value)
      const start = (safePage - 1) * pageSize.value
      const end = start + pageSize.value
      return filteredQuoteFamilies.value.slice(start, end)
    })

    const paginatedQuotes = computed(() => {
      return paginatedQuoteFamilies.value.map((family) => family.primary)
    })

    const paginationStart = computed(() => {
      if (filteredQuoteFamilies.value.length === 0) return 0
      return (Math.min(currentPage.value, totalPages.value) - 1) * pageSize.value + 1
    })

    const paginationEnd = computed(() => {
      if (filteredQuoteFamilies.value.length === 0) return 0
      const end = Math.min(currentPage.value, totalPages.value) * pageSize.value
      return Math.min(end, filteredQuoteFamilies.value.length)
    })

    const visiblePageNumbers = computed(() => {
      const safePage = Math.min(currentPage.value, totalPages.value)
      const maxVisible = 5
      let start = Math.max(1, safePage - 2)
      let end = Math.min(totalPages.value, start + maxVisible - 1)

      if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1)
      }

      const pages = []
      for (let page = start; page <= end; page += 1) {
        pages.push(page)
      }

      return pages
    })

    const selectedQuotes = computed(() => {
      if (!selectedQuoteIds.value.length) return []
      const ids = new Set(selectedQuoteIds.value)
      return filteredQuoteFamilies.value
        .map((family) => family.primary)
        .filter((quote) => ids.has(quote.quote_id))
    })

    const selectedCancellableQuotes = computed(() => {
      return selectedQuotes.value.filter((quote) => canCancelQuote(quote))
    })

    const hasCancelledSelectedQuote = computed(() => {
      return selectedQuotes.value.some((quote) => String(quote?.status || '').toLowerCase() === 'cancelled')
    })

    const showFloatingBulkActions = computed(() => {
      return selectedQuoteIds.value.length > 0 && isBulkActionsBarHidden.value
    })

    const allOnPageSelected = computed(() => {
      if (!paginatedQuotes.value.length) return false
      return paginatedQuotes.value.every((quote) => selectedQuoteIds.value.includes(quote.quote_id))
    })

    const updateBulkActionVisibility = () => {
      if (!bulkActionsBarRef.value) {
        isBulkActionsBarHidden.value = false
        return
      }

      const rect = bulkActionsBarRef.value.getBoundingClientRect()
      isBulkActionsBarHidden.value = rect.bottom < 0 || rect.top < 0
    }

    const getQuoteChildren = (quote) => {
      const quoteId = normalizeQuoteId(quote?.quote_id)
      if (!quoteId) return []

      return quoteFamilyByPrimaryId.value[quoteId]?.children || []
    }

    const isCancelledFamilyRow = (quote) => {
      const quoteId = normalizeQuoteId(quote?.quote_id)
      if (!quoteId) return false
      const family = quoteFamilyByPrimaryId.value[quoteId]
      return String(family?.category || '').toLowerCase() === 'cancelled'
    }

    const indexBy = (list, key) => {
      return (list || []).reduce((acc, item) => {
        if (item && item[key]) {
          acc[item[key]] = item
        }
        return acc
      }, {})
    }

    const fetchAllPages = async (endpoint, pageSize = 100) => {
      let page = 1
      let lastPage = 1
      const records = []

      // Pull every page so historical records are not truncated on the UI.
      while (page <= lastPage) {
        const response = await axios.get(`${endpoint}?page=${page}&pageSize=${pageSize}`)
        if (!response.data?.success) {
          break
        }

        const pageItems = response.data.data || []
        records.push(...pageItems)

        const pagination = response.data.pagination || {}
        lastPage = Number(pagination.last_page || 1)
        page += 1
      }

      return records
    }

    const extractProductName = (data) => {
      if (!data || typeof data !== 'object') return null

      return data.productName
        || data.partDescription
        || data.description
        || data.name
        || data.product_name
        || data.shortDescription
        || null
    }

    const fetchProductNameById = async (productId) => {
      const id = String(productId || '').trim()
      if (!id) return null

      if (productNameMap.value[id]) {
        return productNameMap.value[id]
      }

      if (productLookupPromises.has(id)) {
        return productLookupPromises.get(id)
      }

      const request = (async () => {
        let resolved = null

        try {
          const byId = await axios.get(`/api/v1/products/${encodeURIComponent(id)}`)
          resolved = extractProductName(byId?.data?.data)
        } catch (_) {
          // Continue to SKU/search fallbacks.
        }

        if (!resolved) {
          try {
            const bySku = await axios.get(`/api/v1/products/sku/${encodeURIComponent(id)}`)
            resolved = extractProductName(bySku?.data?.data)
          } catch (_) {
            // Continue to search fallback.
          }
        }

        if (!resolved) {
          try {
            const searched = await axios.get('/api/v1/products', {
              params: {
                search: id,
                page: 1,
                per_page: 20,
                hide_zero_price: false,
              },
            })

            const records = searched?.data?.data?.records
            if (Array.isArray(records) && records.length > 0) {
              const exact = records.find((row) => {
                const productId = String(row?.productId || '').trim()
                const sku = String(row?.mfgPartNo || row?.sku || '').trim()
                return productId === id || sku === id
              })

              resolved = extractProductName(exact || records[0])
            }
          } catch (_) {
            // No-op if search fails.
          }
        }

        if (resolved) {
          productNameMap.value = {
            ...productNameMap.value,
            [id]: resolved,
          }
        }

        return resolved
      })()
        .catch(() => null)
        .finally(() => {
          productLookupPromises.delete(id)
        })

      productLookupPromises.set(id, request)
      return request
    }

    const resolveQuoteItemName = (item, index = 0) => {
      const direct = String(item?.productName || item?.product_name || item?.partDescription || item?.name || item?.description || '').trim()
      if (direct) return direct

      const productId = String(item?.product_id || item?.productId || item?.id || '').trim()
      const sku = String(item?.sku || item?.partNumber || item?.mfg_part_number || item?.mfgPartNo || '').trim()

      if (productId && productNameMap.value[productId]) {
        return productNameMap.value[productId]
      }

      if (sku && productNameMap.value[sku]) {
        return productNameMap.value[sku]
      }

      if (productId) return `Unknown Product (${productId})`
      if (sku) return `Unknown Product (${sku})`
      return `Unknown Product ${index + 1}`
    }

    const hydrateQuoteItemNames = async (quoteRows) => {
      const rows = Array.isArray(quoteRows) ? quoteRows : []
      const ids = Array.from(new Set(rows
        .flatMap((quote) => {
          const items = Array.isArray(quote?.items) ? quote.items : []
          return items
            .filter((item) => {
              const direct = String(item?.productName || item?.product_name || item?.partDescription || item?.name || item?.description || '').trim()
              return direct.length === 0
            })
            .flatMap((item) => [
              String(item?.product_id || item?.productId || item?.id || '').trim(),
              String(item?.sku || item?.partNumber || item?.mfg_part_number || item?.mfgPartNo || '').trim(),
            ])
        })
        .filter(Boolean)
      ))

      const missingIds = ids.filter((id) => !productNameMap.value[id])
      if (!missingIds.length) return

      await Promise.all(missingIds.map((id) => fetchProductNameById(id)))
    }

    const fetchQuotes = async () => {
      loading.value = true
      error.value = null
      
      try {
        if (!authStore.isAuthenticated) {
          error.value = 'Authentication required. Please log in to view your quotes.'
          setTimeout(() => router.push({ name: 'login' }), 2000)
          return
        }

        const [allQuotes, allOrders, allInvoices] = await Promise.all([
          fetchAllPages('/api/v1/quotes', 100),
          fetchAllPages('/api/v1/orders', 100),
          fetchAllPages('/api/v1/invoices', 200)
        ])

        if (Array.isArray(allQuotes)) {
          quotes.value = allQuotes
          await hydrateQuoteItemNames(allQuotes)

          const orders = Array.isArray(allOrders) ? allOrders : []
          const invoices = Array.isArray(allInvoices) ? allInvoices : []
          ordersByQuoteId.value = indexBy(orders, 'quote_id')
          invoicesByOrderNumber.value = indexBy(invoices, 'order_number')
        } else {
          error.value = 'Failed to load quotes'
        }
      } catch (err) {
        if (err.response?.status === 401) {
          error.value = 'Your session has expired. Please log in again.'
          authStore.logout()
          setTimeout(() => router.push({ name: 'login' }), 2000)
        } else {
          error.value = err.response?.data?.message || err.message || 'Failed to load quotes'
        }
      } finally {
        loading.value = false
      }
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      }).format(date)
    }

    const formatStatus = (status) => {
      if (!status) return 'Unknown'
      return status
        .split('_')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ')
    }

    const canCancelQuote = (quote) => {
      if (!quote) return false
      if (isQuoteLockedForPayment(quote)) return false
      return ['draft', 'pending_review', 'approved'].includes(quote.status)
    }

    const canReviseQuote = (quote) => {
      if (!quote) return false
      const status = String(quote.status || '').toLowerCase()
      if (status === 'cancelled') return false
      if (status === 'converted') return false
      if (status === 'approved') return false
      return true
    }

    const getLinkedOrder = (quote) => {
      if (!quote?.quote_id) return null
      return ordersByQuoteId.value[quote.quote_id] || null
    }

    const getLinkedInvoice = (quote) => {
      const linkedOrder = getLinkedOrder(quote)
      if (!linkedOrder?.order_number) return null
      return invoicesByOrderNumber.value[linkedOrder.order_number] || null
    }

    const getInvoiceOutstanding = (invoice) => {
      const total = Number(invoice?.total_amount || 0)
      const paid = Number(invoice?.paid_amount || 0)
      return Math.max(0, total - paid)
    }

    const isQuoteApprovedUnpaid = (quote) => {
      const status = String(quote?.status || '').toLowerCase()
      if (status !== 'approved' && status !== 'expired') return false

      const invoice = getLinkedInvoice(quote)
      if (!invoice) return false

      return String(invoice.status || '').toLowerCase() !== 'paid' && getInvoiceOutstanding(invoice) > 0
    }

    const isQuotePaymentWindowExpired = (quote) => {
      if (!isQuoteApprovedUnpaid(quote)) return false
      if (!quote?.expires_at) return false

      const expiresAtMs = new Date(quote.expires_at).getTime()
      if (!Number.isFinite(expiresAtMs)) return false

      return expiresAtMs <= nowTick.value
    }

    const isQuoteLockedForPayment = (quote) => {
      return isQuoteApprovedUnpaid(quote) && isQuotePaymentWindowExpired(quote)
    }

    const canViewLinkedInvoice = (quote) => {
      return !!getLinkedInvoice(quote) && !isQuoteLockedForPayment(quote)
    }

    const formatCountdown = (msRemaining) => {
      const totalSeconds = Math.max(0, Math.floor(msRemaining / 1000))
      const days = Math.floor(totalSeconds / 86400)
      const hours = Math.floor((totalSeconds % 86400) / 3600)
      const minutes = Math.floor((totalSeconds % 3600) / 60)
      const seconds = totalSeconds % 60

      if (days > 0) return `${days}d ${hours}h ${minutes}m`
      if (hours > 0) return `${hours}h ${minutes}m ${seconds}s`
      return `${minutes}m ${seconds}s`
    }

    const getQuoteExpiryDisplay = (quote) => {
      if (!quote?.expires_at) return 'N/A'

      if (!isQuoteApprovedUnpaid(quote)) {
        return formatDate(quote.expires_at)
      }

      const expiresAtMs = new Date(quote.expires_at).getTime()
      if (!Number.isFinite(expiresAtMs)) {
        return formatDate(quote.expires_at)
      }

      const remaining = expiresAtMs - nowTick.value
      if (remaining <= 0) {
        return 'Expired - Re-open required'
      }

      return `Expires in ${formatCountdown(remaining)}`
    }

    const getStatusBadge = (status) => {
      const badges = {
        draft: 'bg-gray-100 text-gray-800',
        pending_review: 'bg-[#dbe8fa] text-[#2F5597]',
        approved: 'bg-green-100 text-green-800',
        converted: 'bg-indigo-100 text-indigo-800',
        cancelled: 'bg-red-100 text-red-800',
        rejected: 'bg-red-100 text-red-800',
        expired: 'bg-yellow-100 text-yellow-800',
      }
      return badges[status] || 'bg-gray-100 text-gray-800'
    }

    const formatCurrency = (amount) => formatUsdUsingCurrentCurrency(Number(amount || 0))

    const viewQuote = (quote) => selectedQuote.value = quote

    const downloadQuotePdf = async (quote) => {
      try {
        const response = await axios.get(`/api/v1/quotes/${quote.quote_id}/pdf`, {
          responseType: 'blob'
        })

        const blobUrl = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
        const link = document.createElement('a')
        link.href = blobUrl
        link.setAttribute('download', `quote-${quote.quote_id}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(blobUrl)
      } catch (error) {
        console.error('Error downloading quote PDF:', error)
        toastStore.addToast('Failed to download quote PDF', 'error')
      }
    }

    const viewLinkedOrder = (quote) => {
      const order = getLinkedOrder(quote)
      if (!order) {
        toastStore.addToast('No linked order found for this quote yet.', 'warning', 3000, { category: 'orders' })
        return
      }

      searchQuery.value = order.order_number
      selectedStatus.value = ''
      selectedQuote.value = null
      toastStore.addToast(`Showing quote linked to order ${order.order_number}`, 'info', 3000, { category: 'orders' })
    }

    const viewLinkedInvoice = async (quote) => {
      if (isQuoteLockedForPayment(quote)) {
        toastStore.addToast('Quote has expired for payment. Re-open quote to submit for approval again.', 'warning', 3000, { category: 'quotes' })
        return
      }

      const invoice = getLinkedInvoice(quote)
      if (!invoice) {
        toastStore.addToast('No invoice available yet for this quote.', 'warning', 3000, { category: 'invoices' })
        return
      }

      selectedQuote.value = null
      await router.push({
        name: 'invoices',
        query: {
          selectInvoices: invoice.invoice_number,
          focusInvoice: invoice.invoice_number,
          from: 'quotes',
        },
      })
      toastStore.addToast(`Invoice ${invoice.invoice_number} is ready for payment`, 'info', 3000, { category: 'invoices' })
    }

    const toggleQuoteSelection = (quoteId, checked) => {
      if (!quoteId) return

      if (checked) {
        if (!selectedQuoteIds.value.includes(quoteId)) {
          selectedQuoteIds.value = [...selectedQuoteIds.value, quoteId]
        }
      } else {
        selectedQuoteIds.value = selectedQuoteIds.value.filter((id) => id !== quoteId)
      }
    }

    const toggleSelectAllOnPage = (checked) => {
      const pageIds = paginatedQuotes.value.map((quote) => quote.quote_id).filter(Boolean)

      if (checked) {
        selectedQuoteIds.value = Array.from(new Set([...selectedQuoteIds.value, ...pageIds]))
      } else {
        selectedQuoteIds.value = selectedQuoteIds.value.filter((id) => !pageIds.includes(id))
      }
    }

    const clearSelection = () => {
      selectedQuoteIds.value = []
    }

    const downloadSelectedPdfs = async () => {
      if (!selectedQuotes.value.length) {
        toastStore.addToast('No quotes selected', 'warning', 3000, { category: 'quotes' })
        return
      }

      for (const quote of selectedQuotes.value) {
        // Sequential downloads avoid browser throttling all requests at once.
        await downloadQuotePdf(quote)
      }

      toastStore.addToast(`Downloaded ${selectedQuotes.value.length} quote PDF${selectedQuotes.value.length > 1 ? 's' : ''}`, 'success', 3000, { category: 'quotes' })
    }

    const cancelSelectedQuotes = async () => {
      if (!selectedCancellableQuotes.value.length) {
        toastStore.addToast('No cancellable quotes selected', 'warning', 3000, { category: 'quotes' })
        return
      }

      if (authStore.isRestricted) {
        toastStore.addToast('Account suspended: cancelling quotes is disabled', 'error')
        return
      }

      const confirmed = window.confirm(`Cancel ${selectedCancellableQuotes.value.length} selected quote${selectedCancellableQuotes.value.length > 1 ? 's' : ''}?`)
      if (!confirmed) {
        return
      }

      let successCount = 0

      for (const quote of selectedCancellableQuotes.value) {
        processingQuoteId.value = quote.quote_id

        try {
          const response = await axios.post(`/api/v1/quotes/${quote.quote_id}/cancel`, {
            reason: 'Cancelled by customer (bulk action)',
          })

          if (response.data?.success) {
            successCount += 1
          }
        } catch (bulkError) {
          console.error(`Bulk cancel failed for quote ${quote.quote_id}:`, bulkError)
        } finally {
          processingQuoteId.value = null
        }
      }

      if (successCount > 0) {
        toastStore.addToast(`Cancelled ${successCount} quote${successCount > 1 ? 's' : ''}`, 'success', 3000, { category: 'quotes' })
        await fetchQuotes()
      } else {
        toastStore.addToast('Failed to cancel selected quotes', 'error')
      }

      clearSelection()
    }

    const reviseSelectedQuotes = async () => {
      if (!selectedQuotes.value.length) {
        toastStore.addToast('No quote selected for revision', 'warning', 3000, { category: 'quotes' })
        return
      }

      if (selectedQuotes.value.length > 1) {
        toastStore.addToast('Select only one quote to revise', 'warning', 3000, { category: 'quotes' })
        return
      }

      const quote = selectedQuotes.value[0]
      if (!canReviseQuote(quote)) {
        toastStore.addToast('Cancelled quotes cannot be revised', 'warning', 3000, { category: 'quotes' })
        return
      }

      await reviseQuote(quote)
    }

    const closeSelectedQuoteModal = () => {
      selectedQuote.value = null
    }
    
    const cancelQuote = async (quote) => {
      if (!canCancelQuote(quote)) return

      if (authStore.isRestricted) {
        toastStore.addToast('Account suspended: cancelling quotes is disabled', 'error')
        return
      }

      const reason = window.prompt('Optional cancellation reason (max 500 chars):', 'Cancelled by customer')
      if (reason === null) {
        return
      }

      processingQuoteId.value = quote.quote_id

      try {
        const response = await axios.post(`/api/v1/quotes/${quote.quote_id}/cancel`, {
          reason: reason || 'Cancelled by customer',
        })

        if (response.data?.success) {
          toastStore.addToast('Quote cancelled successfully', 'success', 3000, { category: 'quotes' })
          if (selectedQuote.value?.quote_id === quote.quote_id) {
            selectedQuote.value = response.data.data
          }
          await fetchQuotes()
        } else {
          toastStore.addToast(response.data?.message || 'Failed to cancel quote', 'error')
        }
      } catch (error) {
        console.error('Error cancelling quote:', error)
        toastStore.addToast(error.response?.data?.message || 'Failed to cancel quote', 'error')
      } finally {
        processingQuoteId.value = null
      }
    }

    const toNumericProductId = (value) => {
      const parsed = Number(value)
      return Number.isInteger(parsed) && parsed > 0 ? parsed : null
    }

    const extractQuoteItems = (quote) => {
      const items = Array.isArray(quote?.items) ? quote.items : []
      return items
        .map((item) => {
          const productId = toNumericProductId(item?.product_id ?? item?.productId ?? item?.id)
          const quantity = Math.max(1, Number(item?.quantity || 1))
          if (!productId) return null
          return {
            productId,
            quantity,
            fallbackName: item?.productName || item?.product_name || `Product ${productId}`,
            fallbackVendor: item?.vendorId || item?.vendor_id || 'N/A',
            fallbackMfgPartNo: item?.mfgPartNo || item?.mfg_part_no || 'N/A',
            fallbackPrice: Number(item?.unitPrice ?? item?.unit_price ?? 0) || 0,
          }
        })
        .filter(Boolean)
    }

    const getQuoteItemCount = (quote) => {
      return Array.isArray(quote?.items) ? quote.items.length : 0
    }

    const getQuoteItemNames = (quote) => {
      const items = Array.isArray(quote?.items) ? quote.items : []
      if (!items.length) return []

      return items
        .map((item, idx) => String(resolveQuoteItemName(item, idx)).trim())
        .filter(Boolean)
    }

    const isQuoteItemsExpanded = (quote) => {
      const quoteId = normalizeQuoteId(quote?.quote_id)
      if (!quoteId) return false
      return !!expandedQuoteItems.value[quoteId]
    }

    const getVisibleQuoteItemNames = (quote) => {
      const names = getQuoteItemNames(quote)
      if (isQuoteItemsExpanded(quote)) return names
      return names.slice(0, 2)
    }

    const hasHiddenQuoteItems = (quote) => {
      return getQuoteItemNames(quote).length > 2
    }

    const toggleQuoteItemsExpanded = (quote) => {
      const quoteId = normalizeQuoteId(quote?.quote_id)
      if (!quoteId || !hasHiddenQuoteItems(quote)) return

      expandedQuoteItems.value = {
        ...expandedQuoteItems.value,
        [quoteId]: !expandedQuoteItems.value[quoteId],
      }
    }

    const reviseQuote = async (quote) => {
      if (!quote?.quote_id) {
        toastStore.addToast('Unable to revise this quote', 'error')
        return
      }

      if (!canReviseQuote(quote)) {
        if (String(quote.status || '').toLowerCase() === 'cancelled') {
          toastStore.addToast('Cancelled quotes cannot be revised. Create a new quote from products instead.', 'warning', 3000, { category: 'quotes' })
        } else {
          toastStore.addToast('This quote cannot be revised.', 'warning', 3000, { category: 'quotes' })
        }
        return
      }

      if (authStore.isRestricted) {
        toastStore.addToast('Account suspended: revising quotes is disabled', 'error')
        return
      }

      const quoteItems = extractQuoteItems(quote)
      if (!quoteItems.length) {
        toastStore.addToast('This quote has no revisable items', 'warning', 3000, { category: 'quotes' })
        return
      }

      processingQuoteId.value = quote.quote_id

      try {
        const revisedItems = await Promise.all(quoteItems.map(async (line) => {
          try {
            const response = await axios.get(`/api/v1/products/${line.productId}`)
            const product = response.data?.data || response.data || {}
            return {
              ...product,
              productId: product.productId ?? line.productId,
              quantity: line.quantity,
            }
          } catch (fetchError) {
            return {
              productId: line.productId,
              productName: line.fallbackName,
              vendorId: line.fallbackVendor,
              mfgPartNo: line.fallbackMfgPartNo,
              productPrice: [{ rsPrice: line.fallbackPrice || 0, minQty: 1 }],
              quantity: line.quantity,
            }
          }
        }))

        cartStore.replaceCartItems(revisedItems)
        cartStore.setRevisionSource(quote.quote_id)
        selectedQuote.value = null
        toastStore.addToast(`Quote ${quote.quote_id} loaded for revision`, 'success', 3000, { category: 'quotes' })
        router.push({ name: 'cart' })
      } catch (reviseError) {
        console.error('Error preparing quote revision:', reviseError)
        toastStore.addToast('Failed to prepare quote revision', 'error')
      } finally {
        processingQuoteId.value = null
      }
    }

    const getQuotesCountByStatus = (status) => {
      return visibleQuotes.value.filter(quote => quote.status === status).length
    }

    const getQuoteProgress = (status) => {
      const progressMap = {
        draft: 33,
        pending_review: 66,
        approved: 100,
        converted: 100,
        cancelled: 0,
        rejected: 0,
        expired: 0,
      }
      return progressMap[status] || 0
    }

    const resetFilters = () => { 
      selectedStatus.value = ''
      searchQuery.value = ''
      sortBy.value = 'created_desc'
      currentPage.value = 1
      pageSize.value = 10
      expandedQuoteItems.value = {}
      fetchQuotes()
    }

    const goToPage = (page) => {
      currentPage.value = Math.min(Math.max(1, page), totalPages.value)
    }

    const goToPreviousPage = () => {
      if (currentPage.value > 1) {
        currentPage.value -= 1
      }
    }

    const goToNextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value += 1
      }
    }

    watch([selectedStatus, searchQuery, sortBy, pageSize], () => {
      currentPage.value = 1
      clearSelection()
    })

    watch(totalPages, (pages) => {
      if (currentPage.value > pages) {
        currentPage.value = pages
      }
    })

    watch([selectedQuoteIds, currentPage, pageSize, filteredQuotes], async () => {
      await nextTick()
      updateBulkActionVisibility()
    }, { deep: true })

    onMounted(() => {
      countdownTimer = window.setInterval(() => {
        nowTick.value = Date.now()
      }, 1000)

      window.addEventListener('scroll', updateBulkActionVisibility, { passive: true })
      window.addEventListener('resize', updateBulkActionVisibility)

      loadPricingSettings()
      fetchQuotes()
      nextTick(() => {
        updateBulkActionVisibility()
      })
    })

    onUnmounted(() => {
      if (countdownTimer) {
        window.clearInterval(countdownTimer)
        countdownTimer = null
      }

      window.removeEventListener('scroll', updateBulkActionVisibility)
      window.removeEventListener('resize', updateBulkActionVisibility)
    })

    return { 
      quotes, 
      loading, 
      error, 
      selectedStatus, 
      searchQuery, 
      sortBy,
      currentPage,
      pageSize,
      selectedQuoteIds,
      selectedQuote, 
      processingQuoteId, 
      ordersByQuoteId,
      invoicesByOrderNumber,
      visibleQuotes,
      filteredQuotes,
      paginatedQuotes,
      totalPages,
      paginationStart,
      paginationEnd,
      visiblePageNumbers,
      selectedCancellableQuotes,
      hasCancelledSelectedQuote,
      showFloatingBulkActions,
      bulkActionsBarRef,
      allOnPageSelected,
      fetchQuotes, 
      formatDate, 
      formatStatus, 
      getStatusBadge, 
      formatCurrency, 
      viewQuote, 
      downloadQuotePdf,
      viewLinkedOrder,
      viewLinkedInvoice,
      getLinkedOrder,
      getLinkedInvoice,
      canViewLinkedInvoice,
      isQuoteLockedForPayment,
      getQuoteExpiryDisplay,
      getQuoteChildren,
      isCancelledFamilyRow,
      canCancelQuote, 
      canReviseQuote,
      getQuoteItemCount,
      getQuoteItemNames,
      getVisibleQuoteItemNames,
      hasHiddenQuoteItems,
      isQuoteItemsExpanded,
      toggleQuoteItemsExpanded,
      cancelQuote, 
      reviseQuote,
      reviseSelectedQuotes,
      closeSelectedQuoteModal,
      toggleQuoteSelection,
      toggleSelectAllOnPage,
      clearSelection,
      downloadSelectedPdfs,
      cancelSelectedQuotes,
      resetFilters,
      goToPage,
      goToPreviousPage,
      goToNextPage,
      getQuotesCountByStatus,
      getQuoteProgress,
    }
  }
}
</script>
