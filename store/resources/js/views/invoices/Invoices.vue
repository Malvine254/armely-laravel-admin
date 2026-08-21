<template>
  <div class="min-h-screen" style="background: linear-gradient(180deg, #f5f8fd 0%, #eef4fb 100%);">
    <Navbar />

    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Invoices</h1>
        <p class="text-gray-600 text-lg">Track invoice balances, due dates, delivery status, and payment records</p>
      </div>

      <div v-if="pagination.total > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f7fbff 62%, #eef4ff 100%); border-color: #d9e6f7; box-shadow: 0 12px 26px rgba(47,85,151,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(47,85,151,0.2) 0%, rgba(47,85,151,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Total Invoices</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalInvoiceCount }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #2F5597, #7fa2d8);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f7fbff 62%, #eef4ff 100%); border-color: #d9e6f7; box-shadow: 0 12px 26px rgba(47,85,151,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(47,85,151,0.2) 0%, rgba(47,85,151,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Outstanding</p>
          <p class="text-3xl font-bold mt-2" style="color: #2F5597;">{{ formatCurrency(totalOutstanding) }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #2F5597, #7fa2d8);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #f6fff9 62%, #edfff3 100%); border-color: #cce9d6; box-shadow: 0 12px 24px rgba(22,163,74,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(34,197,94,0.22) 0%, rgba(34,197,94,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Paid</p>
          <p class="text-3xl font-bold text-green-600 mt-2">{{ formatCurrency(totalPaid) }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #16a34a, #86efac);"></div>
        </div>
        <div class="group relative overflow-hidden rounded-2xl border p-5 sm:p-6 transition duration-300 hover:-translate-y-0.5" style="background: linear-gradient(160deg, #ffffff 0%, #fff8f8 62%, #fff0f0 100%); border-color: #f7d4d4; box-shadow: 0 12px 24px rgba(239,68,68,0.1);">
          <div class="pointer-events-none absolute -right-6 -top-6 h-16 w-16 rounded-full" style="background: radial-gradient(circle, rgba(239,68,68,0.2) 0%, rgba(239,68,68,0) 70%);"></div>
          <p class="text-gray-600 text-xs font-semibold uppercase tracking-wide">Overdue</p>
          <p class="text-3xl font-bold text-red-600 mt-2">{{ overdueCount }}</p>
          <div class="mt-4 h-1.5 w-16 rounded-full" style="background: linear-gradient(90deg, #dc2626, #fca5a5);"></div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Filter by Status</label>
            <select v-model="selectedStatus" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="border-color: #e5e7eb;">
              <option value="">All Statuses</option>
              <option value="issued">Issued</option>
              <option value="pending">Pending</option>
              <option value="partial">Partial</option>
              <option value="paid">Paid</option>
              <option value="overdue">Overdue</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Search</label>
            <input v-model="searchQuery" type="text" placeholder="Invoice or order number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="border-color: #e5e7eb;">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide">Sort</label>
            <select v-model="sortBy" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 transition duration-200" style="border-color: #e5e7eb;">
              <option value="due_asc">Due Date (Soonest)</option>
              <option value="due_desc">Due Date (Latest)</option>
              <option value="amount_desc">Amount (High to Low)</option>
              <option value="amount_asc">Amount (Low to High)</option>
              <option value="issued_desc">Issued (Newest)</option>
            </select>
          </div>
          <div class="flex items-end gap-2">
            <button @click="fetchInvoices" class="flex-1 px-4 py-3 text-white rounded-lg font-semibold transition duration-200 hover:shadow-lg" style="background-color: #2F5597;">Refresh</button>
            <button @click="resetFilters" class="flex-1 px-4 py-3 border border-[#2f5597] text-[#2f5597] rounded-lg font-semibold hover:bg-[#edf3fb] transition duration-200">Reset</button>
          </div>
        </div>
      </div>

      <div v-if="selectedInvoices.length > 0" class="bg-white rounded-xl shadow-sm border border-[#d9e6f7] p-4 mb-6 flex items-center justify-between gap-3">
        <div class="text-sm text-gray-700">
          <span class="font-semibold">{{ selectedInvoices.length }}</span> selected | Outstanding:
          <span class="font-semibold" style="color: #2F5597;">{{ formatCurrency(selectedOutstandingTotal) }}</span>
        </div>
        <div class="flex gap-2">
          <button @click="combineSelectedInvoices" class="px-4 py-2 rounded-lg font-semibold border border-[#2F5597] text-[#2F5597] hover:bg-[#edf3fb] transition duration-200">
            Combine Invoices
          </button>
          <button @click="selectAllUnpaid" class="px-4 py-2 rounded-lg font-semibold border border-[#2563eb] text-[#2563eb] hover:bg-[#eff6ff] transition duration-200">
            Select All Unpaid
          </button>
          <button @click="downloadSelectedPdfs" class="px-4 py-2 rounded-lg font-semibold border border-[#2F5597] text-[#2F5597] hover:bg-[#edf3fb] transition duration-200">
            Download PDFs
          </button>
          <button v-if="quickBooksPaymentsEnabled" @click="paySelectedInvoices" :disabled="bulkPaying" class="px-4 py-2 rounded-lg text-white font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition duration-200" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
            {{ bulkPaying ? 'Opening QuickBooks...' : 'Pay Selected in QuickBooks' }}
          </button>
          <button @click="clearSelection" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition duration-200">
            Clear
          </button>
        </div>
      </div>

      <div v-if="combinedBundle" class="bg-white rounded-xl shadow-sm border border-[#2F5597] p-5 mb-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Combined Invoice Preview</p>
            <h3 class="text-lg font-bold text-gray-900 mt-1">{{ combinedBundle.bundle_id }}</h3>
            <p class="text-sm text-gray-700 mt-1">Includes {{ combinedBundle.invoice_numbers.length }} invoices</p>
            <p class="text-sm text-gray-700 mt-1">{{ combinedBundle.invoice_numbers.join(', ') }}</p>
            <p class="text-sm text-gray-700 mt-1">Products included: <span class="font-semibold">{{ combinedBundle.product_count }}</span></p>
          </div>
          <div class="text-right">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">Total Due</p>
            <p class="text-2xl font-bold" style="color: #2F5597;">{{ formatCurrency(combinedBundle.total_outstanding) }}</p>
          </div>
        </div>
        <div class="mt-4 flex gap-2">
          <p class="text-sm text-gray-600 self-center">Combined invoice created. Pay it later from the invoice list.</p>
          <button @click="clearCombinedBundle" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition duration-200">
            Remove Combined
          </button>
        </div>
      </div>

      <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="inline-block">
          <div class="w-16 h-16 border-4 rounded-full animate-spin mb-4" style="border-color: #e5ebf2; border-block-start-color: #2F5597;"></div>
          <p class="text-gray-600 font-medium">Loading your invoices...</p>
        </div>
      </div>

      <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 mb-8">
        <div class="flex gap-4">
          <svg class="h-6 w-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <h3 class="font-semibold text-red-900 text-lg">Unable to Load Invoices</h3>
            <p class="text-red-700 mt-1">{{ error }}</p>
            <button @click="fetchInvoices" class="mt-3 text-red-700 hover:text-red-900 font-semibold underline">Try Again</button>
          </div>
        </div>
      </div>

      <div v-else-if="filteredInvoices.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-2xl font-bold text-gray-900 mb-1">No Invoices Found</h3>
        <p class="text-gray-600">Adjust your filters or wait for new billing activity.</p>
      </div>

      <div v-else class="bg-white rounded-xl shadow-sm border overflow-hidden" style="border-color: #d9e6f7;">
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
                    style="accent-color: #2F5597;"
                  />
                </th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Invoice</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Issued</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Due</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Total</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Outstanding</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Progress</th>
                <th class="px-5 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="invoice in paginatedInvoices"
                :key="invoice.id"
                class="border-b last:border-b-0 hover:bg-[#f8fbff] transition duration-200"
                style="border-color: #ecf3fb;"
              >
                <td class="px-4 py-4 align-top">
                  <input
                    type="checkbox"
                    :checked="isSelected(invoice)"
                    :disabled="!canPayInvoice(invoice)"
                    @change="toggleInvoiceSelection(invoice)"
                    class="h-4 w-4 rounded border-gray-300 disabled:opacity-40"
                    style="accent-color: #2F5597;"
                  />
                </td>
                <td class="px-5 py-4 align-top">
                  <button
                    @click="viewInvoice(invoice)"
                    class="text-left font-semibold font-mono transition duration-200 hover:opacity-80"
                    style="color: #2F5597;"
                  >
                    {{ invoice.invoice_number }}
                  </button>
                  <p v-if="invoice.order_number" class="text-xs text-gray-500 mt-1">Order: {{ invoice.order_number }}</p>
                  <p v-if="getInvoiceItemPreview(invoice)" class="text-xs text-gray-500 mt-1">
                    {{ getInvoiceItemPreview(invoice) }}
                  </p>
                </td>
                <td class="px-5 py-4 align-top">
                  <span :class="getStatusBadge(invoice.status)" class="inline-flex px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap">
                    {{ formatStatus(invoice.status) }}
                  </span>
                </td>
                <td class="px-5 py-4 text-sm text-gray-700 align-top">{{ formatDate(invoice.issued_at) }}</td>
                <td class="px-5 py-4 text-sm text-gray-700 align-top">{{ formatDate(invoice.due_at) }}</td>
                <td class="px-5 py-4 align-top">
                  <p class="text-sm font-bold text-gray-900">{{ formatCurrency(invoice.total_amount) }}</p>
                  <p class="text-xs text-green-700 mt-1">Paid: {{ formatCurrency(invoice.paid_amount) }}</p>
                </td>
                <td class="px-5 py-4 align-top">
                  <p class="text-sm font-semibold" :class="getOutstanding(invoice) > 0 ? 'text-red-600' : 'text-gray-900'">
                    {{ formatCurrency(getOutstanding(invoice)) }}
                  </p>
                </td>
                <td class="px-5 py-4 align-top">
                  <div class="w-36">
                    <div class="w-full rounded-full h-2 mb-1" style="background-color: #d9e6f7;">
                      <div class="h-2 rounded-full" style="background-color: #2F5597;" :style="{ inlineSize: calculatePercent(invoice.paid_amount, invoice.total_amount) + '%' }"></div>
                    </div>
                    <p class="text-xs text-gray-600">{{ calculatePercent(invoice.paid_amount, invoice.total_amount) }}%</p>
                  </div>
                </td>
                <td class="px-5 py-4 align-top">
                  <div class="flex flex-nowrap items-center gap-2 whitespace-nowrap">
                    <button
                      @click="downloadPDF(invoice)"
                      class="px-3 py-1.5 text-xs rounded-md font-semibold transition duration-200"
                      style="color: #2F5597; border: 1px solid #2F5597;"
                      @mouseenter="$event.target.style.backgroundColor='#edf3fb'"
                      @mouseleave="$event.target.style.backgroundColor='transparent'"
                    >
                      PDF
                    </button>
                    <button
                      v-if="quickBooksPaymentsEnabled && canPayInvoice(invoice)"
                      @click="startPayment(invoice)"
                      :disabled="payingInvoiceNumber === invoice.invoice_number"
                      class="px-3 py-1.5 text-xs rounded-md text-white font-semibold transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                      style="background-color: #2F5597;"
                      @mouseenter="$event.target.style.backgroundColor='#1f4788'"
                      @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                    >
                      {{ payingInvoiceNumber === invoice.invoice_number ? 'Starting...' : 'Pay Now' }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="border-t px-4 sm:px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4" style="border-color: #d9e6f7; background: #f9fcff;">
          <div class="flex items-center gap-3 text-sm text-gray-700">
            <span class="font-medium">Showing {{ paginationStart }}-{{ paginationEnd }} of {{ pagination.total }}</span>
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
              :key="`invoice-page-${page}`"
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

    <div v-if="selectedInvoice" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" @click="selectedInvoice = null">
      <div class="bg-white rounded-2xl shadow-2xl max-w-7xl w-full max-h-[92vh] overflow-y-auto" @click.stop style="background: #fff;">
        <div class="sticky top-0 text-white px-6 py-6 flex items-center justify-between rounded-t-2xl" style="background: linear-gradient(90deg, #2F5597, #1f4788);">
          <div>
            <p class="text-sm font-semibold text-gray-200 uppercase tracking-wide">Invoice</p>
            <h2 class="text-2xl font-bold text-white">{{ selectedInvoice.invoice_number }}</h2>
          </div>
          <button @click="selectedInvoice = null" class="text-gray-300 hover:text-white transition duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="px-6 py-8">
          <div class="mb-8 pb-8 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-bold text-gray-900">Payment Status</h3>
              <span :class="getStatusBadge(selectedInvoice.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
                {{ formatStatus(selectedInvoice.status) }}
              </span>
            </div>
            <div class="w-full rounded-full h-2 mb-2" style="background-color: #d9e6f7;">
              <div class="h-2 rounded-full" style="background-color: #2F5597;" :style="{ inlineSize: calculatePercent(selectedInvoice.paid_amount, selectedInvoice.total_amount) + '%' }"></div>
            </div>
            <p class="text-xs text-gray-600">{{ calculatePercent(selectedInvoice.paid_amount, selectedInvoice.total_amount) }}% Paid</p>
          </div>

          <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Issued Date</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedInvoice.issued_at) }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Due Date</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedInvoice.due_at) }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Total</p>
              <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(selectedInvoice.total_amount) }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Outstanding</p>
              <p class="text-2xl font-bold" :class="getOutstanding(selectedInvoice) > 0 ? 'text-red-600' : 'text-gray-900'">{{ formatCurrency(getOutstanding(selectedInvoice)) }}</p>
            </div>
          </div>

          <div v-if="getSourceInvoices(selectedInvoice).length > 0" class="mb-6 p-4 rounded-lg" style="background-color: #f8fbff; border: 1px solid #d9e6f7;">
            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Combined From Invoices</p>
            <p class="text-sm text-gray-800">{{ getSourceInvoices(selectedInvoice).join(', ') }}</p>
          </div>

          <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Products / Line Items</h4>
              <span class="text-xs text-gray-600">{{ getInvoiceItems(selectedInvoice).length }} item(s)</span>
            </div>

            <div v-if="getInvoiceItems(selectedInvoice).length === 0" class="p-4 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-600">
              No product line items were found on this invoice.
            </div>

            <div v-else class="overflow-x-auto border border-gray-200 rounded-lg">
              <table class="w-full text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left font-semibold text-gray-700">Product</th>
                    <th class="px-3 py-2 text-left font-semibold text-gray-700">Part/SKU</th>
                    <th class="px-3 py-2 text-right font-semibold text-gray-700">Qty</th>
                    <th class="px-3 py-2 text-right font-semibold text-gray-700">Unit (Incl. Tax)</th>
                    <th class="px-3 py-2 text-right font-semibold text-gray-700">Line Total (Incl. Tax)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, idx) in getInvoiceItems(selectedInvoice)" :key="`inv-item-${idx}`" class="border-t border-gray-100">
                    <td class="px-3 py-2 text-gray-900">{{ item.name }}</td>
                    <td class="px-3 py-2 text-gray-600 font-mono">{{ item.partNumber || '-' }}</td>
                    <td class="px-3 py-2 text-right text-gray-800">{{ item.quantity }}</td>
                    <td class="px-3 py-2 text-right text-gray-800">{{ formatCurrency(item.unitPriceWithTax) }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-900">{{ formatCurrency(item.extendedPriceWithTax) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="flex flex-wrap justify-end gap-2 pt-4 border-t border-gray-200">
            <button @click="selectedInvoice = null" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition duration-200">
              Close
            </button>
            <button @click="downloadPDF(selectedInvoice)" class="px-4 py-2.5 border border-[#2F5597] text-[#2F5597] rounded-lg font-semibold hover:bg-[#edf3fb] transition duration-200">
              Download PDF
            </button>
            <button
              v-if="quickBooksPaymentsEnabled && canPayInvoice(selectedInvoice)"
              @click="startPayment(selectedInvoice)"
              class="px-4 py-2.5 rounded-lg text-white font-semibold transition duration-200"
              style="background-color: #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#1f4788'"
              @mouseleave="$event.target.style.backgroundColor='#2F5597'"
            >
              Pay in QuickBooks
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'
import axios from 'axios'
import Navbar from '../../components/Navbar.vue'
import { usePricingSettings } from '../../composables/usePricingSettings'
import { API_BASE_URL } from '../../services/runtimeConfig'

export default {
  components: { Navbar },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const authStore = useAuthStore()
    const toastStore = useToastStore()
    const { loadPricingSettings, formatUsdUsingCurrentCurrency } = usePricingSettings()
    const quickBooksPaymentsEnabled = false

    const invoices = ref([])
    const loading = ref(false)
    const error = ref(null)
    const selectedStatus = ref('')
    const searchQuery = ref('')
    const sortBy = ref('due_asc')
    const currentPage = ref(1)
    const pageSize = ref(10)
    const selectedInvoice = ref(null)
    const productNameMap = ref({})
    const selectedInvoices = ref([])
    const combinedBundle = ref(null)
    const payingInvoiceNumber = ref(null)
    const bulkPaying = ref(false)
    const invoiceSummary = ref({
      total_invoices: 0,
      active_invoices: 0,
      overdue_count: 0,
      paid_amount: 0,
      outstanding_amount: 0,
    })
    const pagination = ref({
      current_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0,
      last_page: 1,
    })
    const productLookupPromises = new Map()

    const getOutstanding = (invoice) => {
      const total = Number(invoice?.total_amount || 0)
      const paid = Number(invoice?.paid_amount || 0)
      return Math.max(0, total - paid)
    }

    const canPayInvoice = (invoice) => {
      if (!invoice) return false
      if (invoice.status === 'paid' || invoice.status === 'cancelled' || invoice.status === 'merged') return false
      return getOutstanding(invoice) > 0
    }

    const filteredInvoices = computed(() => {
      const includeMerged = selectedStatus.value === 'merged'
      if (includeMerged) {
        return invoices.value
      }

      return invoices.value.filter((inv) => inv.status !== 'merged')
    })

    const totalPages = computed(() => {
      return Math.max(1, Number(pagination.value.last_page || 1))
    })

    const paginatedInvoices = computed(() => {
      return filteredInvoices.value
    })

    const paginationStart = computed(() => {
      return Number(pagination.value.from || 0)
    })

    const paginationEnd = computed(() => {
      return Number(pagination.value.to || 0)
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

    const allOnPageSelected = computed(() => {
      const payableRows = paginatedInvoices.value.filter((invoice) => canPayInvoice(invoice))
      if (payableRows.length === 0) return false
      return payableRows.every((invoice) => selectedInvoices.value.includes(invoice.invoice_number))
    })

    const activeInvoices = computed(() => invoices.value.filter(inv => inv.status !== 'merged'))
    const totalInvoiceCount = computed(() => Number(invoiceSummary.value.total_invoices || pagination.value.total || invoices.value.length || 0))
    const totalOutstanding = computed(() => {
      const summaryValue = Number(invoiceSummary.value.outstanding_amount)
      if (Number.isFinite(summaryValue) && summaryValue >= 0) {
        return summaryValue
      }
      return activeInvoices.value.reduce((sum, inv) => sum + getOutstanding(inv), 0)
    })
    const totalPaid = computed(() => {
      const summaryValue = Number(invoiceSummary.value.paid_amount)
      if (Number.isFinite(summaryValue) && summaryValue >= 0) {
        return summaryValue
      }
      return activeInvoices.value.reduce((sum, inv) => sum + Number(inv.paid_amount || 0), 0)
    })
    const overdueCount = computed(() => {
      const summaryValue = Number(invoiceSummary.value.overdue_count)
      if (Number.isFinite(summaryValue) && summaryValue >= 0) {
        return summaryValue
      }
      return activeInvoices.value.filter(inv => inv.status === 'overdue').length
    })

    const unpaidInvoices = computed(() => {
      return filteredInvoices.value.filter(inv => canPayInvoice(inv))
    })

    const paidInvoices = computed(() => {
      return filteredInvoices.value.filter(inv => !canPayInvoice(inv))
    })

    const selectedOutstandingTotal = computed(() => {
      return selectedInvoices.value.reduce((sum, invoiceNumber) => {
        const found = invoices.value.find(inv => inv.invoice_number === invoiceNumber)
        return sum + getOutstanding(found)
      }, 0)
    })

    const fetchInvoices = async () => {
      loading.value = true
      error.value = null

      try {
        if (!authStore.isAuthenticated) {
          error.value = 'Authentication required. Please log in to view your invoices.'
          setTimeout(() => router.push({ name: 'login' }), 1200)
          return
        }

        const response = await axios.get(`${API_BASE_URL}/invoices`, {
          params: {
            page: currentPage.value,
            pageSize: pageSize.value,
            include_items: false,
            status: selectedStatus.value || undefined,
            search: searchQuery.value || undefined,
            sort: sortBy.value || undefined,
          },
        })

        if (!response.data?.success) {
          throw new Error(response.data?.message || 'Failed to load invoices')
        }

        invoices.value = Array.isArray(response.data.data) ? response.data.data : []
        const summaryInfo = response.data.summary || {}
        invoiceSummary.value = {
          total_invoices: Number(summaryInfo.total_invoices || 0),
          active_invoices: Number(summaryInfo.active_invoices || 0),
          overdue_count: Number(summaryInfo.overdue_count || 0),
          paid_amount: Number(summaryInfo.paid_amount || 0),
          outstanding_amount: Number(summaryInfo.outstanding_amount || 0),
        }
        const pageInfo = response.data.pagination || {}
        pagination.value = {
          current_page: Number(pageInfo.current_page || currentPage.value || 1),
          per_page: Number(pageInfo.per_page || pageSize.value || 10),
          total: Number(pageInfo.total || 0),
          from: Number(pageInfo.from || ((pageInfo.total || 0) > 0 ? ((Number(pageInfo.current_page || currentPage.value || 1) - 1) * Number(pageInfo.per_page || pageSize.value || 10)) + 1 : 0)),
          to: Number(pageInfo.to || Math.min(Number(pageInfo.total || 0), Number(pageInfo.current_page || currentPage.value || 1) * Number(pageInfo.per_page || pageSize.value || 10))),
          last_page: Number(pageInfo.last_page || 1),
        }
        currentPage.value = pagination.value.current_page

        selectedInvoices.value = selectedInvoices.value.filter((invoiceNumber) => {
          const found = invoices.value.find(inv => inv.invoice_number === invoiceNumber)
          return !!found && canPayInvoice(found)
        })
      } catch (err) {
        if (err.response?.status === 401) {
          error.value = 'Your session has expired. Please log in again.'
          authStore.logout()
          setTimeout(() => router.push({ name: 'login' }), 1200)
        } else {
          error.value = err.response?.data?.message || err.message || 'Failed to load invoices'
        }
      } finally {
        loading.value = false
      }
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

        // 1) Try direct product detail endpoint (product id).
        try {
          const byId = await axios.get(`${API_BASE_URL}/products/${encodeURIComponent(id)}`)
          resolved = extractProductName(byId?.data?.data)
        } catch (_) {
          // Try additional fallbacks below.
        }

        // 2) Try SKU endpoint because invoice part/SKU may not be productId.
        if (!resolved) {
          try {
            const bySku = await axios.get(`${API_BASE_URL}/products/sku/${encodeURIComponent(id)}`)
            resolved = extractProductName(bySku?.data?.data)
          } catch (_) {
            // Continue to search fallback.
          }
        }

        // 3) Try products search endpoint and match best candidate.
        if (!resolved) {
          try {
            const searched = await axios.get(`${API_BASE_URL}/products`, {
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
            // No-op: keep unresolved if all endpoints fail.
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

    const resolveInvoiceItemDisplayName = (item, index = 0) => {
      const productId = String(item?.product_id || item?.productId || item?.id || '').trim()
      const sku = String(item?.sku || item?.partNumber || item?.mfg_part_number || item?.mfgPartNo || '').trim()

      const directName = item?.partDescription
        || item?.productName
        || item?.product_name
        || item?.name
        || item?.description

      if (directName && String(directName).trim().length > 0) {
        return String(directName).trim()
      }

      if (productId && productNameMap.value[productId]) {
        return productNameMap.value[productId]
      }

      if (sku && productNameMap.value[sku]) {
        return productNameMap.value[sku]
      }

      if (sku) {
        return `Unknown Product (${sku})`
      }

      if (productId) {
        return `Unknown Product (${productId})`
      }

      return `Unknown Product ${index + 1}`
    }

    const hydrateInvoiceProductNames = async (invoice) => {
      const rows = Array.isArray(invoice?.items) ? invoice.items : []
      const ids = Array.from(new Set(rows
        .flatMap((item) => [
          String(item?.product_id || item?.productId || item?.id || '').trim(),
          String(item?.sku || item?.partNumber || item?.mfg_part_number || item?.mfgPartNo || '').trim(),
        ])
        .filter(Boolean)
      ))

      const missingIds = ids.filter((id) => !productNameMap.value[id])
      if (missingIds.length === 0) {
        return
      }

      await Promise.all(missingIds.map((id) => fetchProductNameById(id)))
    }

    const viewInvoice = async (invoice) => {
      selectedInvoice.value = invoice

      try {
        const response = await axios.get(`${API_BASE_URL}/invoices/${encodeURIComponent(invoice.invoice_number)}`)
        if (response.data?.success && response.data?.data) {
          selectedInvoice.value = response.data.data
          await hydrateInvoiceProductNames(response.data.data)
        }
      } catch (_) {
        // Keep summary row visible if detail fetch fails.
      }
    }

    const downloadPDF = async (invoice) => {
      try {
        const response = await axios.get(`${API_BASE_URL}/invoices/${invoice.invoice_number}/pdf`, {
          responseType: 'blob',
        })

        const blobUrl = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
        const link = document.createElement('a')
        link.href = blobUrl
        link.setAttribute('download', `invoice-${invoice.invoice_number}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(blobUrl)
      } catch (err) {
        console.error('Failed to download invoice PDF:', err)
        toastStore.addToast(err.response?.data?.message || 'Failed to download invoice PDF', 'error')
      }
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

    const startPayment = async (invoice) => {
      if (!quickBooksPaymentsEnabled) {
        toastStore.addToast('QuickBooks invoice payments are temporarily disabled.', 'info', 3500, { category: 'invoices' })
        return
      }

      if (!canPayInvoice(invoice)) {
        toastStore.addToast('This invoice is already fully paid.', 'warning', 3000, { category: 'invoices' })
        return
      }

      await navigateToPayment({
        mode: 'invoice',
        invoiceNumber: invoice.invoice_number,
        amount: getOutstanding(invoice),
        from: '/invoices',
      })
    }

    const isSelected = (invoice) => {
      return selectedInvoices.value.includes(invoice.invoice_number)
    }

    const toggleInvoiceSelection = (invoice) => {
      if (!canPayInvoice(invoice)) {
        return
      }

      if (isSelected(invoice)) {
        selectedInvoices.value = selectedInvoices.value.filter(id => id !== invoice.invoice_number)
      } else {
        selectedInvoices.value.push(invoice.invoice_number)
      }
    }

    const toggleSelectAllOnPage = (checked) => {
      const pagePayable = paginatedInvoices.value
        .filter((invoice) => canPayInvoice(invoice))
        .map((invoice) => invoice.invoice_number)

      if (checked) {
        selectedInvoices.value = Array.from(new Set([...selectedInvoices.value, ...pagePayable]))
      } else {
        selectedInvoices.value = selectedInvoices.value.filter((id) => !pagePayable.includes(id))
      }
    }

    const clearSelection = () => {
      selectedInvoices.value = []
      combinedBundle.value = null
    }

    const selectAllUnpaid = () => {
      selectedInvoices.value = unpaidInvoices.value.map(inv => inv.invoice_number)
      combinedBundle.value = null
    }

    const combineSelectedInvoices = async () => {
      const payableNumbers = selectedInvoices.value.filter((invoiceNumber) => {
        const found = invoices.value.find(inv => inv.invoice_number === invoiceNumber)
        return !!found && canPayInvoice(found)
      })

      if (payableNumbers.length < 2) {
        toastStore.addToast('Select at least 2 unpaid invoices to combine.', 'warning', 3000, { category: 'invoices' })
        return
      }

      try {
        const response = await axios.post(`${API_BASE_URL}/invoices/combine`, {
          invoice_numbers: payableNumbers,
        })

        if (!response.data?.success) {
          throw new Error(response.data?.message || 'Failed to combine invoices')
        }

        const combined = response.data?.data?.invoice
        combinedBundle.value = {
          bundle_id: combined?.invoice_number || 'COMBINED',
          invoice_numbers: response.data?.data?.source_invoices || payableNumbers,
          total_outstanding: Number(combined?.total_amount || 0),
          product_count: Number(response.data?.data?.product_count || 0),
        }

        toastStore.addToast(response.data?.message || 'Invoices combined successfully', 'success', 3000, { category: 'invoices' })
        await fetchInvoices()
        clearSelection()
      } catch (err) {
        console.error('Failed to combine invoices:', err)
        toastStore.addToast(err.response?.data?.message || err.message || 'Failed to combine invoices', 'error')
      }
    }

    const clearCombinedBundle = () => {
      combinedBundle.value = null
    }

    const downloadSelectedPdfs = async () => {
      const selectedRows = invoices.value.filter((inv) => selectedInvoices.value.includes(inv.invoice_number))
      if (selectedRows.length === 0) {
        toastStore.addToast('Select at least one invoice first.', 'warning', 3000, { category: 'invoices' })
        return
      }

      for (const invoice of selectedRows) {
        await downloadPDF(invoice)
      }
    }

    const paySelectedInvoices = async () => {
      if (!quickBooksPaymentsEnabled) {
        toastStore.addToast('QuickBooks invoice payments are temporarily disabled.', 'info', 3500, { category: 'invoices' })
        return
      }

      const payableNumbers = selectedInvoices.value.filter((invoiceNumber) => {
        const found = invoices.value.find(inv => inv.invoice_number === invoiceNumber)
        return !!found && canPayInvoice(found)
      })

      if (payableNumbers.length === 0) {
        toastStore.addToast('Select at least one payable invoice.', 'warning', 3000, { category: 'invoices' })
        return
      }

      const totalOutstanding = payableNumbers.reduce((sum, invoiceNumber) => {
        const found = invoices.value.find(inv => inv.invoice_number === invoiceNumber)
        return sum + getOutstanding(found)
      }, 0)

      bulkPaying.value = true
      try {
        await navigateToPayment({
          mode: 'bulk',
          invoiceNumbers: payableNumbers.join(','),
          amount: totalOutstanding,
          count: payableNumbers.length,
          from: '/invoices',
        })
      } finally {
        bulkPaying.value = false
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
      return String(status)
        .split('_')
        .map(part => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ')
    }

    const getStatusBadge = (status) => {
      const badges = {
        issued: 'bg-blue-100 text-blue-800',
        pending: 'bg-yellow-100 text-yellow-800',
        partial: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        merged: 'bg-purple-100 text-purple-800',
        overdue: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
      }
      return badges[status] || 'bg-gray-100 text-gray-800'
    }

    const formatCurrency = (amount) => {
      return formatUsdUsingCurrentCurrency(Number(amount || 0))
    }

    const toNumber = (value, fallback = 0) => {
      const n = Number(value)
      return Number.isFinite(n) ? n : fallback
    }

    const getInvoiceItems = (invoice) => {
      const rows = Array.isArray(invoice?.items) ? invoice.items : []

      const mapped = rows.map((item, idx) => {
        const productId = String(item?.product_id || item?.productId || item?.id || '').trim()
        const quantity = Math.max(1, toNumber(item?.quantity, 1))
        const unitPrice = toNumber(item?.unitPrice ?? item?.price ?? item?.unit_price, 0)
        const extendedPrice = toNumber(
          item?.extendedPrice ?? item?.lineTotal ?? item?.line_total,
          unitPrice * quantity
        )

        return {
          productId,
          name: resolveInvoiceItemDisplayName(item, idx),
          partNumber: item?.partNumber || item?.sku || productId || '',
          quantity,
          unitPrice,
          extendedPrice,
          taxAmount: 0,
          unitPriceWithTax: unitPrice,
          extendedPriceWithTax: extendedPrice,
        }
      })

      const hasAnyPricing = mapped.some((item) => item.unitPrice > 0 || item.extendedPrice > 0)
      const invoiceTotal = toNumber(invoice?.total_amount, 0)

      // Some upstream item payloads contain quantities/part numbers but no line pricing.
      // Allocate invoice total across quantities so detail view stays consistent with header totals.
      if (!hasAnyPricing && invoiceTotal > 0 && mapped.length > 0) {
        const totalQty = mapped.reduce((sum, item) => sum + Math.max(1, toNumber(item.quantity, 1)), 0)

        if (totalQty > 0) {
          let running = 0
          mapped.forEach((item, idx) => {
            const qty = Math.max(1, toNumber(item.quantity, 1))
            const isLast = idx === mapped.length - 1
            const lineTotal = isLast
              ? Number((invoiceTotal - running).toFixed(2))
              : Number(((invoiceTotal * qty) / totalQty).toFixed(2))

            item.extendedPrice = lineTotal
            item.unitPrice = Number((lineTotal / qty).toFixed(2))
            running = Number((running + lineTotal).toFixed(2))
          })
        }
      }

      // Merge duplicate items by product id (fallback to part/name key when id is missing).
      const grouped = new Map()

      mapped.forEach((item) => {
        const productId = String(item.productId || '').trim()
        const fallbackKey = `${String(item.partNumber || '').trim().toLowerCase()}|${String(item.name || '').trim().toLowerCase()}`
        const key = productId ? `id:${productId}` : `fallback:${fallbackKey}`

        if (!grouped.has(key)) {
          grouped.set(key, { ...item })
          return
        }

        const existing = grouped.get(key)
        existing.quantity = Number(existing.quantity || 0) + Number(item.quantity || 0)
        existing.extendedPrice = Number((Number(existing.extendedPrice || 0) + Number(item.extendedPrice || 0)).toFixed(2))
        if (!existing.partNumber && item.partNumber) {
          existing.partNumber = item.partNumber
        }
      })

      const groupedItems = Array.from(grouped.values()).map((item) => {
        const qty = Math.max(1, Number(item.quantity || 1))
        const extended = Number(item.extendedPrice || 0)
        return {
          ...item,
          unitPrice: Number((extended / qty).toFixed(2)),
          extendedPrice: Number(extended.toFixed(2)),
          taxAmount: 0,
          unitPriceWithTax: Number((extended / qty).toFixed(2)),
          extendedPriceWithTax: Number(extended.toFixed(2)),
        }
      })

      const invoiceTax = Math.max(0, toNumber(invoice?.tax_amount, 0))
      const preTaxSubtotal = groupedItems.reduce((sum, item) => sum + Math.max(0, toNumber(item.extendedPrice, 0)), 0)

      if (invoiceTax > 0 && groupedItems.length > 0) {
        let runningTax = 0

        groupedItems.forEach((item, idx) => {
          const qty = Math.max(1, toNumber(item.quantity, 1))
          const preTaxLine = Math.max(0, toNumber(item.extendedPrice, 0))
          const isLast = idx === groupedItems.length - 1

          const lineTax = isLast
            ? Number((invoiceTax - runningTax).toFixed(2))
            : Number((preTaxSubtotal > 0
              ? (invoiceTax * preTaxLine) / preTaxSubtotal
              : invoiceTax / groupedItems.length
            ).toFixed(2))

          runningTax = Number((runningTax + lineTax).toFixed(2))
          item.taxAmount = lineTax
          item.extendedPriceWithTax = Number((preTaxLine + lineTax).toFixed(2))
          item.unitPriceWithTax = Number((item.extendedPriceWithTax / qty).toFixed(2))
        })
      }

      return groupedItems
    }

    const getSourceInvoices = (invoice) => {
      const source = invoice?.raw_data?.source_invoice_numbers
      return Array.isArray(source) ? source : []
    }

    const getInvoiceItemPreview = (invoice) => {
      const previewFromApi = String(invoice?.item_preview || '').trim()
      if (previewFromApi) {
        return previewFromApi
      }

      const items = getInvoiceItems(invoice)
      if (!items.length) return ''

      const names = items
        .map((item) => String(item?.name || '').trim())
        .filter((name) => name.length > 0)

      if (!names.length) return ''
      if (names.length <= 2) return names.join(', ')
      return `${names[0]}, ${names[1]} +${names.length - 2} more`
    }

    const calculatePercent = (paid, total) => {
      const totalValue = Number(total || 0)
      if (!totalValue) return 0
      const percent = Math.round((Number(paid || 0) / totalValue) * 100)
      return Math.max(0, Math.min(100, percent))
    }

    const parseRouteInvoiceSelection = () => {
      const raw = route.query?.selectInvoices
      if (!raw) return []

      const list = Array.isArray(raw) ? raw : [raw]
      return Array.from(new Set(
        list
          .flatMap((value) => String(value || '').split(','))
          .map((value) => value.trim())
          .filter(Boolean)
      ))
    }

    const clearSelectionQueryParams = async () => {
      const nextQuery = { ...route.query }
      delete nextQuery.selectInvoices
      delete nextQuery.focusInvoice
      delete nextQuery.from

      await router.replace({ path: route.path, query: nextQuery })
    }

    const applyRouteInvoiceSelection = async () => {
      const requested = parseRouteInvoiceSelection()
      if (!requested.length) return

      const focusInvoice = String(route.query?.focusInvoice || requested[0] || '').trim()

      let available = invoices.value
      if (focusInvoice && !available.some((inv) => inv.invoice_number === focusInvoice)) {
        searchQuery.value = focusInvoice
        selectedStatus.value = ''
        sortBy.value = 'due_asc'
        currentPage.value = 1
        await fetchInvoices()
        available = invoices.value
      }

      const selectable = requested.filter((invoiceNumber) => {
        const found = available.find((inv) => inv.invoice_number === invoiceNumber)
        return !!found && canPayInvoice(found)
      })

      if (selectable.length > 0) {
        selectedInvoices.value = Array.from(new Set([...selectedInvoices.value, ...selectable]))
      }

      const target = available.find((inv) => inv.invoice_number === focusInvoice)
      if (target) {
        selectedInvoice.value = target
      }

      if (selectable.length > 0) {
        toastStore.addToast(`Selected ${selectable.length} invoice${selectable.length > 1 ? 's' : ''} for payment`, 'info', 3000, { category: 'invoices' })
      } else if (!target) {
        toastStore.addToast('Selected invoice was not found. Try refreshing invoices.', 'warning', 3000, { category: 'invoices' })
      }

      await clearSelectionQueryParams()
    }

    const resetFilters = () => {
      selectedStatus.value = ''
      searchQuery.value = ''
      sortBy.value = 'due_asc'
      currentPage.value = 1
      pageSize.value = 10
      fetchInvoices()
    }

    const goToPage = (page) => {
      const nextPage = Math.min(Math.max(1, page), totalPages.value)
      if (nextPage === currentPage.value) return
      currentPage.value = nextPage
      fetchInvoices()
    }

    const goToPreviousPage = () => {
      if (currentPage.value > 1) {
        currentPage.value -= 1
        fetchInvoices()
      }
    }

    const goToNextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value += 1
        fetchInvoices()
      }
    }

    watch(pageSize, () => {
      currentPage.value = 1
      fetchInvoices()
    })

    watch(totalPages, (pages) => {
      if (currentPage.value > pages) {
        currentPage.value = pages
      }
    })

    onMounted(async () => {
      await loadPricingSettings()
      await fetchInvoices()
      await applyRouteInvoiceSelection()

      const paymentStatus = String(route.query?.quickbooks || route.query?.payment || route.query?.stripe || '').trim()
      const paymentInvoice = String(route.query?.invoice || route.query?.invoiceNumber || '').trim()
      const paymentInvoiceList = String(route.query?.invoiceNumbers || '').trim()
      const paymentRef = String(route.query?.paymentId || route.query?.txn || route.query?.transactionId || '').trim()

      const paymentTarget = paymentInvoice
        || (paymentInvoiceList ? `invoices ${paymentInvoiceList}` : '')

      const suffixParts = []
      if (paymentTarget) {
        suffixParts.push(paymentTarget)
      }
      if (paymentRef) {
        suffixParts.push(`ref ${paymentRef}`)
      }
      const suffix = suffixParts.length > 0 ? ` (${suffixParts.join(' | ')})` : ''

      if (paymentStatus === 'success') {
        toastStore.addToast(`Payment completed successfully${suffix}.`, 'success', 3000, { category: 'invoices' })
      }
      if (paymentStatus === 'cancel') {
        toastStore.addToast(`Payment was canceled${suffix}.`, 'warning', 3000, { category: 'invoices' })
      }

      if (paymentStatus) {
        router.replace({ path: route.path, query: {} })
      }
    })

    return {
      invoices,
      loading,
      error,
      selectedStatus,
      searchQuery,
      sortBy,
      currentPage,
      pageSize,
      selectedInvoice,
      selectedInvoices,
      combinedBundle,
      payingInvoiceNumber,
      bulkPaying,
      pagination,
      filteredInvoices,
      paginatedInvoices,
      totalPages,
      paginationStart,
      paginationEnd,
      visiblePageNumbers,
      allOnPageSelected,
      unpaidInvoices,
      paidInvoices,
      totalOutstanding,
      totalPaid,
      overdueCount,
      totalInvoiceCount,
      selectedOutstandingTotal,
      quickBooksPaymentsEnabled,
      fetchInvoices,
      viewInvoice,
      downloadPDF,
      startPayment,
      isSelected,
      toggleInvoiceSelection,
      toggleSelectAllOnPage,
      clearSelection,
      selectAllUnpaid,
      combineSelectedInvoices,
      clearCombinedBundle,
      downloadSelectedPdfs,
      paySelectedInvoices,
      formatDate,
      formatStatus,
      getStatusBadge,
      formatCurrency,
      getInvoiceItems,
      resolveInvoiceItemDisplayName,
      getInvoiceItemPreview,
      getSourceInvoices,
      calculatePercent,
      resetFilters,
      goToPage,
      goToPreviousPage,
      goToNextPage,
      getOutstanding,
      canPayInvoice,
    }
  }
}
</script>
