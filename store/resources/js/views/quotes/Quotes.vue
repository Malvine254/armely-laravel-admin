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
      <div v-if="quotes.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f7fbff 62%, #eef4ff 100%); border-color: #d9e6f7; box-shadow: 0 12px 26px rgba(47,85,151,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(47,85,151,0.2) 0%, rgba(47,85,151,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Total Quotes</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ quotes.length }}</p>
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
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
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
        <div class="px-4 sm:px-5 py-3 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="border-color: #d9e6f7; background: #f9fcff;">
          <p class="text-sm font-medium text-gray-700">
            {{ selectedQuoteIds.length }} selected
          </p>
          <div class="flex flex-wrap items-center gap-2">
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
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Quote ID</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Created</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Expires</th>
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
                </td>
                <td class="px-5 py-4 align-top">
                  <span :class="getStatusBadge(quote.status)" class="inline-flex px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                    {{ formatStatus(quote.status) }}
                  </span>
                </td>
                <td class="px-5 py-4 text-sm text-gray-700 align-top">{{ formatDate(quote.created_at) }}</td>
                <td class="px-5 py-4 text-sm text-gray-700 align-top">{{ quote.expires_at ? formatDate(quote.expires_at) : 'N/A' }}</td>
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
                      v-if="canPayQuote(quote)"
                      @click="payQuoteInvoice(quote)"
                      class="px-3 py-1.5 text-xs rounded-md text-white font-semibold transition duration-200"
                      style="background-color: #2F5597;"
                      @mouseenter="$event.target.style.backgroundColor='#1f4788'"
                      @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                    >
                      Pay Now
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

    <!-- Quote Detail Modal -->
    <div v-if="selectedQuote" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" @click="selectedQuote = null">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
        <!-- Modal Header -->
        <div class="sticky top-0 text-white px-6 py-6 flex items-center justify-between rounded-t-2xl" style="background: linear-gradient(90deg, #2F5597, #1f4788);">
          <div>
            <p class="text-sm font-semibold text-gray-200 uppercase tracking-wide">Quote</p>
            <h2 class="text-2xl font-bold text-white">{{ selectedQuote.quote_id }}</h2>
          </div>
          <button @click="selectedQuote = null" class="text-gray-300 hover:text-white transition duration-200">
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
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedQuote.expires_at) }}</p>
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
            <button @click="selectedQuote = null" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition duration-200">
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
              v-if="canPayQuote(selectedQuote)"
              @click="payQuoteInvoice(selectedQuote)"
              class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200"
              style="background-color: #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#1f4788'"
              @mouseleave="$event.target.style.backgroundColor='#2F5597'"
            >
              Pay Invoice
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
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'
import Navbar from '../../components/Navbar.vue'
import axios from 'axios'

export default {
  components: { Navbar },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const toastStore = useToastStore()
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

    const filteredQuotes = computed(() => {
      const filtered = quotes.value.filter(q => {
        const statusMatch = !selectedStatus.value || q.status === selectedStatus.value
        const term = (searchQuery.value || '').toLowerCase()
        const linkedOrder = getLinkedOrder(q)
        const linkedInvoice = getLinkedInvoice(q)
        const searchMatch = !term
          || String(q.quote_id || '').toLowerCase().includes(term)
          || String(linkedOrder?.order_number || '').toLowerCase().includes(term)
          || String(linkedInvoice?.invoice_number || '').toLowerCase().includes(term)
        return statusMatch && searchMatch
      })

      const sorted = [...filtered]
      switch (sortBy.value) {
        case 'created_asc':
          sorted.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0))
          break
        case 'amount_desc':
          sorted.sort((a, b) => Number(b.total_amount || 0) - Number(a.total_amount || 0))
          break
        case 'amount_asc':
          sorted.sort((a, b) => Number(a.total_amount || 0) - Number(b.total_amount || 0))
          break
        case 'status_asc':
          sorted.sort((a, b) => String(a.status || '').localeCompare(String(b.status || '')))
          break
        case 'created_desc':
        default:
          sorted.sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
          break
      }

      return sorted
    })

    const totalPages = computed(() => {
      const pages = Math.ceil(filteredQuotes.value.length / pageSize.value)
      return Math.max(1, pages)
    })

    const paginatedQuotes = computed(() => {
      const safePage = Math.min(currentPage.value, totalPages.value)
      const start = (safePage - 1) * pageSize.value
      const end = start + pageSize.value
      return filteredQuotes.value.slice(start, end)
    })

    const paginationStart = computed(() => {
      if (filteredQuotes.value.length === 0) return 0
      return (Math.min(currentPage.value, totalPages.value) - 1) * pageSize.value + 1
    })

    const paginationEnd = computed(() => {
      if (filteredQuotes.value.length === 0) return 0
      const end = Math.min(currentPage.value, totalPages.value) * pageSize.value
      return Math.min(end, filteredQuotes.value.length)
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
      return filteredQuotes.value.filter((quote) => ids.has(quote.quote_id))
    })

    const selectedCancellableQuotes = computed(() => {
      return selectedQuotes.value.filter((quote) => canCancelQuote(quote))
    })

    const allOnPageSelected = computed(() => {
      if (!paginatedQuotes.value.length) return false
      return paginatedQuotes.value.every((quote) => selectedQuoteIds.value.includes(quote.quote_id))
    })

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
      return ['draft', 'pending_review', 'approved'].includes(quote.status)
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

    const canPayQuote = (quote) => {
      const invoice = getLinkedInvoice(quote)
      if (!invoice) return false
      return invoice.status !== 'paid' && Number(invoice.total_amount || 0) > Number(invoice.paid_amount || 0)
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

    const formatCurrency = (amount) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount || 0)

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
        toastStore.addToast('No linked order found for this quote yet.', 'warning')
        return
      }

      searchQuery.value = order.order_number
      selectedStatus.value = ''
      selectedQuote.value = null
      toastStore.addToast(`Showing quote linked to order ${order.order_number}`, 'info')
    }

    const toPaymentUrl = (query) => {
      const params = new URLSearchParams()
      Object.entries(query || {}).forEach(([key, value]) => {
        if (value !== undefined && value !== null && String(value).length > 0) {
          params.set(key, String(value))
        }
      })
      return `/payment?${params.toString()}`
    }

    const navigateToPayment = async (query) => {
      try {
        await router.push({ name: 'payment', query })
        if (router.currentRoute.value?.name !== 'payment') {
          window.location.assign(toPaymentUrl(query))
        }
      } catch (err) {
        console.error('Router payment navigation failed, using hard redirect:', err)
        window.location.assign(toPaymentUrl(query))
      }
    }

    const payQuoteInvoice = async (quote) => {
      const invoice = getLinkedInvoice(quote)
      if (!invoice) {
        toastStore.addToast('No invoice available yet for this quote.', 'warning')
        return
      }

      await navigateToPayment({
        mode: 'quote',
        invoiceNumber: invoice.invoice_number,
        quoteId: quote.quote_id,
        amount: Number(invoice.total_amount || 0),
        from: '/quotes',
      })
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
        toastStore.addToast('No quotes selected', 'warning')
        return
      }

      for (const quote of selectedQuotes.value) {
        // Sequential downloads avoid browser throttling all requests at once.
        await downloadQuotePdf(quote)
      }

      toastStore.addToast(`Downloaded ${selectedQuotes.value.length} quote PDF${selectedQuotes.value.length > 1 ? 's' : ''}`, 'success')
    }

    const cancelSelectedQuotes = async () => {
      if (!selectedCancellableQuotes.value.length) {
        toastStore.addToast('No cancellable quotes selected', 'warning')
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
        toastStore.addToast(`Cancelled ${successCount} quote${successCount > 1 ? 's' : ''}`, 'success')
        await fetchQuotes()
      } else {
        toastStore.addToast('Failed to cancel selected quotes', 'error')
      }

      clearSelection()
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
          toastStore.addToast('Quote cancelled successfully', 'success')
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

    const getQuotesCountByStatus = (status) => {
      return quotes.value.filter(quote => quote.status === status).length
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

    onMounted(() => fetchQuotes())

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
      filteredQuotes,
      paginatedQuotes,
      totalPages,
      paginationStart,
      paginationEnd,
      visiblePageNumbers,
      selectedCancellableQuotes,
      allOnPageSelected,
      fetchQuotes, 
      formatDate, 
      formatStatus, 
      getStatusBadge, 
      formatCurrency, 
      viewQuote, 
      downloadQuotePdf,
      viewLinkedOrder,
      getLinkedOrder,
      getLinkedInvoice,
      canPayQuote,
      payQuoteInvoice,
      canCancelQuote, 
      cancelQuote, 
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