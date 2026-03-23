<template>
  <div class="min-h-screen" style="background: linear-gradient(180deg, #f5f8fd 0%, #eef4fb 100%);">
    <Navbar />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Invoices</h1>
        <p class="text-gray-600 text-lg">Track balances, download PDFs, and pay one or many invoices</p>
      </div>

      <div v-if="invoices.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg p-6 border transition duration-200" style="border-color: #d9e6f7; box-shadow: 0 6px 18px rgba(47,85,151,0.08);">
          <p class="text-gray-600 text-sm font-medium">Total Invoices</p>
          <p class="text-3xl font-bold text-gray-900">{{ invoices.length }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border transition duration-200" style="border-color: #d9e6f7; box-shadow: 0 6px 18px rgba(47,85,151,0.08);">
          <p class="text-gray-600 text-sm font-medium">Outstanding</p>
          <p class="text-3xl font-bold" style="color: #2F5597;">{{ formatCurrency(totalOutstanding) }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border transition duration-200" style="border-color: #d9e6f7; box-shadow: 0 6px 18px rgba(47,85,151,0.08);">
          <p class="text-gray-600 text-sm font-medium">Paid</p>
          <p class="text-3xl font-bold text-green-600">{{ formatCurrency(totalPaid) }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border transition duration-200" style="border-color: #d9e6f7; box-shadow: 0 6px 18px rgba(47,85,151,0.08);">
          <p class="text-gray-600 text-sm font-medium">Overdue</p>
          <p class="text-3xl font-bold text-red-600">{{ overdueCount }}</p>
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
            Combine Selected
          </button>
          <button @click="selectAllUnpaid" class="px-4 py-2 rounded-lg font-semibold border border-[#2563eb] text-[#2563eb] hover:bg-[#eff6ff] transition duration-200">
            Select All Unpaid
          </button>
          <button @click="downloadSelectedPdfs" class="px-4 py-2 rounded-lg font-semibold border border-[#2F5597] text-[#2F5597] hover:bg-[#edf3fb] transition duration-200">
            Download PDFs
          </button>
          <button @click="paySelectedInvoices" :disabled="bulkPaying" class="px-4 py-2 rounded-lg text-white font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition duration-200" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
            {{ bulkPaying ? 'Starting Checkout...' : 'Pay Selected (Combined)' }}
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
          <div class="w-16 h-16 border-4 rounded-full animate-spin mb-4" style="border-color: #e5ebf2; border-top-color: #2F5597;"></div>
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

      <div v-else class="space-y-8">
        <section v-if="unpaidInvoices.length > 0" class="space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Unpaid Invoices</h2>
            <p class="text-sm text-gray-600">{{ unpaidInvoices.length }} invoice(s) due for payment</p>
          </div>

          <div v-for="invoice in unpaidInvoices" :key="`unpaid-${invoice.id}`" class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(90deg, #f3f8ff 0%, #eef4fb 100%);">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <input
                    type="checkbox"
                    :checked="isSelected(invoice)"
                    :disabled="!canPayInvoice(invoice)"
                    @change="toggleInvoiceSelection(invoice)"
                    class="w-4 h-4 rounded border-gray-300"
                    style="accent-color: #2F5597;"
                  >
                  <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Invoice Number</p>
                    <p class="text-xl font-bold text-gray-900 font-mono">{{ invoice.invoice_number }}</p>
                  </div>
                </div>
                <span :class="getStatusBadge(invoice.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
                  {{ formatStatus(invoice.status) }}
                </span>
              </div>
            </div>

            <div class="px-6 py-5">
              <div class="grid grid-cols-2 sm:grid-cols-5 gap-6 mb-5">
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Issued Date</p>
                  <p class="text-gray-900 font-semibold mt-1">{{ formatDate(invoice.issued_at) }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Due Date</p>
                  <p class="text-gray-900 font-semibold mt-1">{{ formatDate(invoice.due_at) }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total</p>
                  <p class="text-gray-900 font-semibold mt-1">{{ formatCurrency(invoice.total_amount) }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Paid</p>
                  <p class="text-green-700 font-semibold mt-1">{{ formatCurrency(invoice.paid_amount) }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Outstanding</p>
                  <p class="font-semibold mt-1" :class="getOutstanding(invoice) > 0 ? 'text-red-600' : 'text-gray-900'">{{ formatCurrency(getOutstanding(invoice)) }}</p>
                </div>
              </div>

              <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                  <p class="text-xs font-semibold text-gray-700">Payment Progress</p>
                  <p class="text-xs text-gray-600">{{ calculatePercent(invoice.paid_amount, invoice.total_amount) }}%</p>
                </div>
                <div class="w-full rounded-full h-2" style="background-color: #d9e6f7;">
                  <div class="h-2 rounded-full transition duration-500" style="background-color: #2F5597;" :style="{ width: calculatePercent(invoice.paid_amount, invoice.total_amount) + '%' }"></div>
                </div>
              </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50 px-6 py-4 flex items-center justify-between">
              <button @click="viewInvoice(invoice)" class="flex items-center gap-2 font-semibold transition duration-200 hover:opacity-75" style="color: #2F5597;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Details
              </button>
              <div class="flex gap-2">
                <button
                  @click="downloadPDF(invoice)"
                  class="px-4 py-2 rounded-lg font-semibold border border-[#2F5597] text-[#2F5597] hover:bg-[#edf3fb] transition duration-200"
                >
                  PDF
                </button>
                <button
                  v-if="canPayInvoice(invoice)"
                  @click="startPayment(invoice)"
                  :disabled="payingInvoiceNumber === invoice.invoice_number"
                  class="px-4 py-2 rounded-lg text-white font-semibold disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                  style="background-color: #2F5597;"
                  @mouseenter="$event.target.style.backgroundColor='#1f4788'"
                  @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                >
                  {{ payingInvoiceNumber === invoice.invoice_number ? 'Starting...' : 'Pay Now' }}
                </button>
              </div>
            </div>
          </div>
        </section>

        <section v-if="paidInvoices.length > 0" class="space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Paid Invoices</h2>
            <p class="text-sm text-gray-600">{{ paidInvoices.length }} invoice(s) paid</p>
          </div>

          <div v-for="invoice in paidInvoices" :key="`paid-${invoice.id}`" class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(90deg, #f3f8ff 0%, #eef4fb 100%);">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Invoice Number</p>
                  <p class="text-xl font-bold text-gray-900 font-mono">{{ invoice.invoice_number }}</p>
                </div>
                <span :class="getStatusBadge(invoice.status)" class="px-4 py-2 rounded-full text-sm font-semibold">
                  {{ formatStatus(invoice.status) }}
                </span>
              </div>
            </div>

            <div class="px-6 py-5">
              <div class="grid grid-cols-2 sm:grid-cols-5 gap-6 mb-5">
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Issued Date</p>
                  <p class="text-gray-900 font-semibold mt-1">{{ formatDate(invoice.issued_at) }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Due Date</p>
                  <p class="text-gray-900 font-semibold mt-1">{{ formatDate(invoice.due_at) }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total</p>
                  <p class="text-gray-900 font-semibold mt-1">{{ formatCurrency(invoice.total_amount) }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Paid</p>
                  <p class="text-green-700 font-semibold mt-1">{{ formatCurrency(invoice.paid_amount) }}</p>
                </div>
                <div>
                  <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Outstanding</p>
                  <p class="font-semibold mt-1" :class="getOutstanding(invoice) > 0 ? 'text-red-600' : 'text-gray-900'">{{ formatCurrency(getOutstanding(invoice)) }}</p>
                </div>
              </div>

              <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                  <p class="text-xs font-semibold text-gray-700">Payment Progress</p>
                  <p class="text-xs text-gray-600">{{ calculatePercent(invoice.paid_amount, invoice.total_amount) }}%</p>
                </div>
                <div class="w-full rounded-full h-2" style="background-color: #d9e6f7;">
                  <div class="h-2 rounded-full transition duration-500" style="background-color: #2F5597;" :style="{ width: calculatePercent(invoice.paid_amount, invoice.total_amount) + '%' }"></div>
                </div>
              </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50 px-6 py-4 flex items-center justify-between">
              <button @click="viewInvoice(invoice)" class="flex items-center gap-2 font-semibold transition duration-200 hover:opacity-75" style="color: #2F5597;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Details
              </button>
              <div class="flex gap-2">
                <button
                  @click="downloadPDF(invoice)"
                  class="px-4 py-2 rounded-lg font-semibold border border-[#2F5597] text-[#2F5597] hover:bg-[#edf3fb] transition duration-200"
                >
                  PDF
                </button>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <div v-if="selectedInvoice" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]" @click="selectedInvoice = null">
      <div class="bg-white rounded-2xl shadow-2xl max-w-7xl w-full max-h-[92vh] overflow-y-auto" @click.stop>
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
              <div class="h-2 rounded-full" style="background-color: #2F5597;" :style="{ width: calculatePercent(selectedInvoice.paid_amount, selectedInvoice.total_amount) + '%' }"></div>
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
                    <th class="px-3 py-2 text-right font-semibold text-gray-700">Unit</th>
                    <th class="px-3 py-2 text-right font-semibold text-gray-700">Line Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, idx) in getInvoiceItems(selectedInvoice)" :key="`inv-item-${idx}`" class="border-t border-gray-100">
                    <td class="px-3 py-2 text-gray-900">{{ item.name }}</td>
                    <td class="px-3 py-2 text-gray-600 font-mono">{{ item.partNumber || '-' }}</td>
                    <td class="px-3 py-2 text-right text-gray-800">{{ item.quantity }}</td>
                    <td class="px-3 py-2 text-right text-gray-800">{{ formatCurrency(item.unitPrice) }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-900">{{ formatCurrency(item.extendedPrice) }}</td>
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
              v-if="canPayInvoice(selectedInvoice)"
              @click="startPayment(selectedInvoice)"
              class="px-4 py-2.5 rounded-lg text-white font-semibold transition duration-200"
              style="background-color: #2F5597;"
              @mouseenter="$event.target.style.backgroundColor='#1f4788'"
              @mouseleave="$event.target.style.backgroundColor='#2F5597'"
            >
              Pay Invoice
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'
import axios from 'axios'
import Navbar from '../../components/Navbar.vue'

export default {
  components: { Navbar },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const authStore = useAuthStore()
    const toastStore = useToastStore()

    const invoices = ref([])
    const loading = ref(false)
    const error = ref(null)
    const selectedStatus = ref('')
    const searchQuery = ref('')
    const sortBy = ref('due_asc')
    const selectedInvoice = ref(null)
    const productNameMap = ref({})
    const selectedInvoices = ref([])
    const combinedBundle = ref(null)
    const payingInvoiceNumber = ref(null)
    const bulkPaying = ref(false)
    const pagination = ref({
      current_page: 1,
      per_page: 100,
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
      const term = (searchQuery.value || '').toLowerCase()
      const filtered = invoices.value.filter((inv) => {
        const includeMerged = selectedStatus.value === 'merged'
        if (!includeMerged && inv.status === 'merged') {
          return false
        }

        const statusMatch = !selectedStatus.value || inv.status === selectedStatus.value
        const searchMatch = !term
          || String(inv.invoice_number || '').toLowerCase().includes(term)
          || String(inv.order_number || '').toLowerCase().includes(term)

        return statusMatch && searchMatch
      })

      const sorted = [...filtered]
      switch (sortBy.value) {
        case 'due_desc':
          sorted.sort((a, b) => new Date(b.due_at || 0) - new Date(a.due_at || 0))
          break
        case 'amount_desc':
          sorted.sort((a, b) => Number(b.total_amount || 0) - Number(a.total_amount || 0))
          break
        case 'amount_asc':
          sorted.sort((a, b) => Number(a.total_amount || 0) - Number(b.total_amount || 0))
          break
        case 'issued_desc':
          sorted.sort((a, b) => new Date(b.issued_at || b.created_at || 0) - new Date(a.issued_at || a.created_at || 0))
          break
        case 'due_asc':
        default:
          sorted.sort((a, b) => new Date(a.due_at || 0) - new Date(b.due_at || 0))
          break
      }

      return sorted
    })

    const activeInvoices = computed(() => invoices.value.filter(inv => inv.status !== 'merged'))
    const totalOutstanding = computed(() => activeInvoices.value.reduce((sum, inv) => sum + getOutstanding(inv), 0))
    const totalPaid = computed(() => activeInvoices.value.reduce((sum, inv) => sum + Number(inv.paid_amount || 0), 0))
    const overdueCount = computed(() => activeInvoices.value.filter(inv => inv.status === 'overdue').length)

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

    const fetchAllPages = async (endpoint, pageSize = 100) => {
      let page = 1
      let lastPage = 1
      const records = []

      while (page <= lastPage) {
        const response = await axios.get(`${endpoint}?page=${page}&pageSize=${pageSize}`)
        if (!response.data?.success) {
          break
        }

        const pageItems = response.data.data || []
        records.push(...pageItems)

        const pageInfo = response.data.pagination || {}
        lastPage = Number(pageInfo.last_page || 1)
        page += 1
      }

      return records
    }

    const fetchInvoices = async () => {
      loading.value = true
      error.value = null

      try {
        if (!authStore.isAuthenticated) {
          error.value = 'Authentication required. Please log in to view your invoices.'
          setTimeout(() => router.push({ name: 'login' }), 1200)
          return
        }

        const allInvoices = await fetchAllPages('/api/v1/invoices', 150)
        invoices.value = Array.isArray(allInvoices) ? allInvoices : []

        pagination.value.total = invoices.value.length
        pagination.value.from = invoices.value.length > 0 ? 1 : 0
        pagination.value.to = invoices.value.length

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
          const byId = await axios.get(`/api/v1/products/${encodeURIComponent(id)}`)
          resolved = extractProductName(byId?.data?.data)
        } catch (_) {
          // Try additional fallbacks below.
        }

        // 2) Try SKU endpoint because invoice part/SKU may not be productId.
        if (!resolved) {
          try {
            const bySku = await axios.get(`/api/v1/products/sku/${encodeURIComponent(id)}`)
            resolved = extractProductName(bySku?.data?.data)
          } catch (_) {
            // Continue to search fallback.
          }
        }

        // 3) Try products search endpoint and match best candidate.
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

    const hydrateInvoiceProductNames = async (invoice) => {
      const rows = Array.isArray(invoice?.items) ? invoice.items : []
      const ids = Array.from(new Set(rows
        .map((item) => String(item?.product_id || item?.productId || item?.id || '').trim())
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
      await hydrateInvoiceProductNames(invoice)
    }

    const downloadPDF = async (invoice) => {
      try {
        const response = await axios.get(`/api/v1/invoices/${invoice.invoice_number}/pdf`, {
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
      if (!canPayInvoice(invoice)) {
        toastStore.addToast('This invoice is already fully paid.', 'warning')
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
        toastStore.addToast('Select at least 2 unpaid invoices to combine.', 'warning')
        return
      }

      try {
        const response = await axios.post('/api/v1/invoices/combine', {
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

        toastStore.addToast(response.data?.message || 'Invoices combined successfully', 'success')
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
        toastStore.addToast('Select at least one invoice first.', 'warning')
        return
      }

      for (const invoice of selectedRows) {
        await downloadPDF(invoice)
      }
    }

    const paySelectedInvoices = async () => {
      const payableNumbers = selectedInvoices.value.filter((invoiceNumber) => {
        const found = invoices.value.find(inv => inv.invoice_number === invoiceNumber)
        return !!found && canPayInvoice(found)
      })

      if (payableNumbers.length === 0) {
        toastStore.addToast('Select at least one payable invoice.', 'warning')
        return
      }

      const totalOutstanding = payableNumbers.reduce((sum, invoiceNumber) => {
        const found = invoices.value.find(inv => inv.invoice_number === invoiceNumber)
        return sum + getOutstanding(found)
      }, 0)

      await navigateToPayment({
        mode: 'bulk',
        invoiceNumbers: payableNumbers.join(','),
        amount: totalOutstanding,
        count: payableNumbers.length,
        from: '/invoices',
      })
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
      return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(amount || 0))
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
          name: item?.partDescription
            || item?.productName
            || item?.product_name
            || item?.name
            || item?.description
            || productNameMap.value[productId]
            || `Item ${idx + 1}`,
          partNumber: item?.partNumber || item?.sku || productId || '',
          quantity,
          unitPrice,
          extendedPrice,
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

      return Array.from(grouped.values()).map((item) => {
        const qty = Math.max(1, Number(item.quantity || 1))
        const extended = Number(item.extendedPrice || 0)
        return {
          ...item,
          unitPrice: Number((extended / qty).toFixed(2)),
          extendedPrice: Number(extended.toFixed(2)),
        }
      })
    }

    const getSourceInvoices = (invoice) => {
      const source = invoice?.raw_data?.source_invoice_numbers
      return Array.isArray(source) ? source : []
    }

    const calculatePercent = (paid, total) => {
      const totalValue = Number(total || 0)
      if (!totalValue) return 0
      const percent = Math.round((Number(paid || 0) / totalValue) * 100)
      return Math.max(0, Math.min(100, percent))
    }

    const resetFilters = () => {
      selectedStatus.value = ''
      searchQuery.value = ''
      sortBy.value = 'due_asc'
    }

    onMounted(async () => {
      await fetchInvoices()

      if (route.query?.stripe === 'success') {
        toastStore.addToast('Payment completed successfully.', 'success')
      }
      if (route.query?.stripe === 'cancel') {
        toastStore.addToast('Payment was canceled.', 'warning')
      }

      if (route.query?.stripe) {
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
      selectedInvoice,
      selectedInvoices,
      combinedBundle,
      payingInvoiceNumber,
      bulkPaying,
      pagination,
      filteredInvoices,
      unpaidInvoices,
      paidInvoices,
      totalOutstanding,
      totalPaid,
      overdueCount,
      selectedOutstandingTotal,
      fetchInvoices,
      viewInvoice,
      downloadPDF,
      startPayment,
      isSelected,
      toggleInvoiceSelection,
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
      getSourceInvoices,
      calculatePercent,
      resetFilters,
      getOutstanding,
      canPayInvoice,
    }
  }
}
</script>
