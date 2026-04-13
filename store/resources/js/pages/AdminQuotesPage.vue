<template>
  <AdminLayout>
    <template #title>Quote Management</template>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="rounded-xl border border-white/10 p-6 backdrop-blur transition hover:border-cyan-500/40" style="background: linear-gradient(180deg, rgba(34, 211, 238, 0.12), rgba(3, 102, 214, 0.08));">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-300 uppercase tracking-wide mb-1 font-semibold">Total Quotes</p>
            <p class="text-3xl font-bold text-white">{{ stats.total || 0 }}</p>
            <p class="text-xs text-slate-400 mt-2">All submitted quotes</p>
          </div>
          <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.25), rgba(59, 130, 246, 0.25));">
            <i class="fas fa-file-invoice text-cyan-300 text-2xl"></i>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-white/10 p-6 backdrop-blur transition hover:border-amber-500/40" style="background: linear-gradient(180deg, rgba(251, 191, 36, 0.12), rgba(217, 119, 6, 0.08));">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-300 uppercase tracking-wide mb-1 font-semibold">Awaiting Action</p>
            <p class="text-3xl font-bold text-amber-300">{{ stats.pending || 0 }}</p>
            <p class="text-xs text-slate-400 mt-2">Need approval</p>
          </div>
          <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(251, 191, 36, 0.25), rgba(217, 119, 6, 0.25));">
            <i class="fas fa-clock text-amber-300 text-2xl"></i>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-white/10 p-6 backdrop-blur transition hover:border-emerald-500/40" style="background: linear-gradient(180deg, rgba(34, 197, 94, 0.12), rgba(22, 163, 74, 0.08));">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-300 uppercase tracking-wide mb-1 font-semibold">Approved</p>
            <p class="text-3xl font-bold text-emerald-300">{{ stats.approved || 0 }}</p>
            <p class="text-xs text-slate-400 mt-2">Converted to orders</p>
          </div>
          <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.25), rgba(22, 163, 74, 0.25));">
            <i class="fas fa-check-circle text-emerald-300 text-2xl"></i>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-white/10 p-6 backdrop-blur transition hover:border-rose-500/40" style="background: linear-gradient(180deg, rgba(239, 68, 68, 0.12), rgba(185, 28, 28, 0.08));">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-slate-300 uppercase tracking-wide mb-1 font-semibold">Rejected</p>
            <p class="text-3xl font-bold text-rose-300">{{ stats.rejected || 0 }}</p>
            <p class="text-xs text-slate-400 mt-2">Declined requests</p>
          </div>
          <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(185, 28, 28, 0.25));">
            <i class="fas fa-times-circle text-rose-300 text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Info Banner -->
    <div class="rounded-lg border border-cyan-500/20 p-4 mb-6 backdrop-blur" style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.1), rgba(59, 130, 246, 0.08));">
      <div class="flex items-start gap-3">
        <i class="fas fa-lightbulb text-cyan-300 mt-0.5 text-lg flex-shrink-0"></i>
        <p class="text-sm text-slate-200 font-medium">
          <span class="text-cyan-300 font-semibold">Review & Approve</span> pending customer quotes. Click <span class="font-semibold">Review</span> to see full details and approve or reject each quote.
        </p>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="rounded-xl border border-white/10 p-6 mb-6 backdrop-blur" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.7), rgba(10, 41, 72, 0.7));">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-200 mb-2">Search Quote</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Quote ID, customer name..."
            @keyup.enter="applyFilters"
            class="w-full px-4 py-2.5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 text-white placeholder-slate-500 transition"
            style="background: rgba(148, 163, 184, 0.12);"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-200 mb-2">Sort By</label>
          <select v-model="sortBy" @change="applyFilters" class="w-full px-4 py-2.5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 text-white transition" style="background: rgba(148, 163, 184, 0.12);">
            <option value="newest" class="bg-slate-900">Newest First</option>
            <option value="oldest" class="bg-slate-900">Oldest First</option>
            <option value="highest" class="bg-slate-900">Highest Amount</option>
            <option value="lowest" class="bg-slate-900">Lowest Amount</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium py-2.5 px-4 rounded-lg transition shadow-lg"
          >
            <i class="fas fa-search mr-2"></i>Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Quotes List -->
    <div class="rounded-xl border border-white/10 overflow-hidden backdrop-blur" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.6), rgba(10, 41, 72, 0.6));">
      <!-- Bulk Actions Bar -->
      <div v-if="selectedQuotes.length > 0" class="px-6 py-4 border-b border-white/10 flex items-center justify-between" style="background: rgba(34, 211, 238, 0.1);">
        <span class="text-sm font-medium text-cyan-300"><i class="fas fa-check-square mr-2"></i>{{ selectedQuotes.length }} quote(s) selected</span>
        <button
          @click="confirmBulkDelete"
          class="px-4 py-2 text-xs font-semibold rounded-lg border border-rose-500/50 text-rose-300 hover:bg-rose-500/20 transition"
        >
          <i class="fas fa-trash mr-1"></i>Delete Selected
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead style="background: rgba(15, 23, 42, 0.8);" class="border-b border-white/10">
            <tr>
              <th class="px-4 py-4 text-left">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded cursor-pointer" style="accent-color: #22d3ee;"
                />
              </th>
              <th class="px-6 py-4 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Quote</th>
              <th class="px-6 py-4 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Status</th>
              <th class="px-6 py-4 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Customer</th>
              <th class="px-6 py-4 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Company</th>
              <th class="px-6 py-4 text-right font-semibold text-slate-200 uppercase text-xs tracking-wide">Amount</th>
              <th class="px-6 py-4 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Submitted</th>
              <th class="px-6 py-4 text-center font-semibold text-slate-200 uppercase text-xs tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="quotes.length === 0" class="border-b border-white/10 hover:bg-white/5 transition">
              <td colspan="8" class="px-6 py-16 text-center">
                <i class="fas fa-inbox text-5xl mb-4 block opacity-20 text-slate-400"></i>
                <p class="text-slate-400 text-lg font-medium">No pending quotes found</p>
              </td>
            </tr>
            <tr v-for="quote in quotes" :key="quote.id" class="border-b border-white/10 hover:bg-white/5 transition cursor-pointer">
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :value="quote.id"
                  v-model="selectedQuotes"
                  class="w-4 h-4 rounded cursor-pointer" style="accent-color: #22d3ee;"
                />
              </td>
              <td class="px-6 py-4">
                <p class="text-sm font-semibold text-slate-100 max-w-[320px] truncate" :title="getQuoteDisplayName(quote)">
                  {{ getQuoteDisplayName(quote) }}
                </p>
                <p class="text-xs text-cyan-400 mt-0.5 font-mono">{{ quote.quote_id }}</p>
              </td>
              <td class="px-6 py-4">
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold',
                    quote.status === 'approved'
                      ? 'bg-emerald-500/20 text-emerald-300'
                      : quote.status === 'rejected'
                        ? 'bg-rose-500/20 text-rose-300'
                        : 'bg-amber-500/20 text-amber-300'
                  ]"
                >
                  {{ formatStatus(quote.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <p class="font-medium text-slate-100">{{ getUserName(quote) }}</p>
                <p class="text-xs text-slate-400">{{ getUserEmail(quote) }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="text-slate-100">{{ getCompanyName(quote) }}</p>
              </td>
              <td class="px-6 py-4 text-right">
                <p class="font-bold text-white">${{ formatCurrency(quote.total_amount) }}</p>
                <p class="text-xs text-slate-400">Tax: ${{ formatCurrency(quote.tax_amount) }}</p>
              </td>
              <td class="px-6 py-4 text-sm text-slate-300">
                {{ formatDate(quote.submitted_at) }}
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center">
                  <button
                    @click="selectQuote(quote)"
                    class="px-3 py-1.5 bg-gradient-to-r from-cyan-500/30 to-blue-600/30 hover:from-cyan-400/40 hover:to-blue-500/40 text-cyan-300 font-medium rounded-lg transition border border-cyan-500/30 text-sm"
                  >
                    <i class="fas fa-eye mr-1"></i>Review
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-6 py-4 border-t border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="background: rgba(15, 23, 42, 0.5);">
        <div class="text-sm text-slate-300">
          <p>Showing {{ quotes.length }} of {{ totalQuotes }} quotes</p>
          <p class="mt-1 text-xs text-slate-400">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchQuotes()"
            class="px-3 py-2 border border-white/10 rounded-lg text-slate-300 hover:bg-white/10 hover:text-cyan-300 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
          >
            Previous
          </button>
          <button
            v-for="page in pageNumbers"
            :key="page"
            @click="currentPage = page; fetchQuotes()"
            :class="[
              'px-3 py-2 rounded-lg border text-sm font-semibold transition',
              page === currentPage
                ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white border-cyan-500'
                : 'border-white/10 text-slate-300 hover:border-cyan-500/50 hover:text-cyan-300'
            ]"
          >
            {{ page }}
          </button>
          <button
            :disabled="currentPage >= lastPage"
            @click="currentPage++; fetchQuotes()"
            class="px-3 py-2 border border-white/10 rounded-lg text-slate-300 hover:bg-white/10 hover:text-cyan-300 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Quote Review Modal -->
    <div v-if="selectedQuote" class="fixed inset-0 z-[9999] bg-black/50 backdrop-blur-[2px] flex items-center justify-center p-4" @click="selectedQuote = null">
      <div class="rounded-2xl shadow-2xl max-w-6xl w-full max-h-[94vh] overflow-y-auto border border-white/10" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(10, 41, 72, 0.95));" @click.stop>
        <div class="sticky top-0 text-white p-6 border-b border-white/10" style="background: linear-gradient(90deg, rgba(34, 211, 238, 0.15), rgba(59, 130, 246, 0.1));">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-xs uppercase tracking-wide text-cyan-300 font-semibold">Quote Approval</p>
              <h3 class="text-2xl font-bold text-white mt-1">{{ selectedQuote.quote_id }}</h3>
            </div>
            <button @click="selectedQuote = null" class="text-slate-400 hover:text-cyan-300 transition text-2xl">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
          <div v-if="isLoadingQuoteDetails" class="bg-cyan-500/20 border border-cyan-400/30 rounded-lg px-4 py-3 text-sm text-cyan-300">
            <i class="fas fa-spinner fa-spin mr-2"></i>Loading full quote details...
          </div>

          <!-- Quote Details -->
          <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 rounded-xl border border-white/10 p-4" style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.1), rgba(59, 130, 246, 0.08));">
              <p class="text-xs uppercase tracking-wide text-cyan-300 font-semibold">Quote Summary</p>
              <p class="text-xl font-bold text-white mt-1">{{ getQuoteDisplayName(selectedQuote) }}</p>
              <p class="text-sm text-slate-400 mt-1">ID: {{ selectedQuote.quote_id }}</p>
              <div class="mt-3 flex flex-wrap gap-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="selectedQuote.status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : selectedQuote.status === 'rejected' ? 'bg-rose-500/20 text-rose-300' : 'bg-amber-500/20 text-amber-300'">{{ formatStatus(selectedQuote.status) }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-300">Submitted: {{ formatDate(selectedQuote.submitted_at || selectedQuote.created_at) }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-300">Expires: {{ formatDate(selectedQuote.expires_at) }}</span>
              </div>
            </div>

            <div class="rounded-xl border border-white/10 p-4" style="background: rgba(148, 163, 184, 0.08);">
              <p class="text-xs uppercase tracking-wide text-slate-300 font-semibold mb-1">Financial</p>
              <p class="text-3xl font-extrabold text-cyan-300 leading-tight">${{ formatCurrency(selectedQuote.total_amount) }}</p>
              <p class="text-sm text-slate-400 mt-1">Tax: ${{ formatCurrency(selectedQuote.tax_amount) }}</p>
              <p class="text-sm text-slate-400">Discount: ${{ formatCurrency(selectedQuote.discount_amount) }}</p>
              <p class="text-sm text-slate-400">Items: {{ normalizedQuoteItems.length }}</p>
            </div>

            <div class="rounded-xl border border-white/10 p-4" style="background: rgba(148, 163, 184, 0.08);">
              <p class="text-xs uppercase tracking-wide text-slate-300 font-semibold mb-1">Customer</p>
              <p class="text-base font-semibold text-white">{{ getUserName(selectedQuote) }}</p>
              <p class="text-sm text-slate-400">{{ getUserEmail(selectedQuote) }}</p>
              <p class="text-sm text-slate-400 mt-2">Company: {{ getCompanyName(selectedQuote) }}</p>
            </div>

            <div v-if="selectedQuote.order" class="rounded-xl border border-white/10 p-4" style="background: rgba(34, 197, 94, 0.08);">
              <p class="text-xs uppercase tracking-wide text-slate-300 font-semibold mb-1">Linked Order</p>
              <p class="text-base font-semibold text-emerald-300">{{ selectedQuote.order.order_number || 'N/A' }}</p>
              <p class="text-sm text-slate-400">Status: {{ selectedQuote.order.status || 'N/A' }}</p>
            </div>
          </div>

          <hr class="my-2" />

          <!-- Quote Items -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <p class="text-sm font-semibold text-slate-200">Items</p>
              <span class="text-xs font-semibold px-2 py-1 rounded-full bg-white/10 text-slate-400">{{ normalizedQuoteItems.length }} item(s)</span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 p-4 rounded-lg max-h-[26rem] overflow-y-auto border border-white/10" style="background: rgba(15, 23, 42, 0.5);">
              <div v-for="(item, index) in normalizedQuoteItems" :key="index" class="text-sm rounded-lg p-3 border border-white/10" style="background: rgba(148, 163, 184, 0.08);">
                <p class="font-semibold text-slate-100 leading-snug">{{ item.name }}</p>
                <p class="text-xs text-slate-400 mt-1" v-if="item.sku">SKU: {{ item.sku }}</p>
                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-400">
                  <span>Qty: <span class="text-slate-200">{{ item.quantity }}</span></span>
                  <span>Unit: <span class="text-slate-200">${{ formatCurrency(item.price) }}</span></span>
                  <span class="font-semibold text-cyan-300">Line: ${{ formatCurrency(item.lineTotal) }}</span>
                </div>
              </div>
              <p v-if="normalizedQuoteItems.length === 0" class="text-sm text-slate-400">No item details available.</p>
            </div>
          </div>

          <!-- Full Row Details -->
          <div class="border rounded-lg">
            <button
              @click="showFullRowDetails = !showFullRowDetails"
              class="w-full px-4 py-3 flex items-center justify-between text-left font-semibold text-gray-800 hover:bg-gray-50 transition"
            >
              <span>Full Row Details</span>
              <i :class="showFullRowDetails ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
            </button>
            <div v-if="showFullRowDetails" class="px-4 pb-4 pt-3 border-t bg-gray-50">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-white rounded-lg border border-gray-200 p-3 space-y-1">
                  <p class="font-semibold text-gray-800">Quote Metadata</p>
                  <p><span class="font-medium text-gray-700">Internal ID:</span> {{ selectedQuote.id }}</p>
                  <p><span class="font-medium text-gray-700">Quote ID:</span> {{ selectedQuote.quote_id }}</p>
                  <p><span class="font-medium text-gray-700">Status:</span> {{ formatStatus(selectedQuote.status) }}</p>
                  <p><span class="font-medium text-gray-700">Created:</span> {{ formatDate(selectedQuote.created_at) }}</p>
                  <p><span class="font-medium text-gray-700">Submitted:</span> {{ formatDate(selectedQuote.submitted_at || selectedQuote.created_at) }}</p>
                  <p><span class="font-medium text-gray-700">Expires:</span> {{ formatDate(selectedQuote.expires_at) }}</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-3 space-y-1">
                  <p class="font-semibold text-gray-800">Financial</p>
                  <p><span class="font-medium text-gray-700">Subtotal:</span> ${{ formatCurrency(selectedQuote.total_amount) }}</p>
                  <p><span class="font-medium text-gray-700">Tax:</span> ${{ formatCurrency(selectedQuote.tax_amount) }}</p>
                  <p><span class="font-medium text-gray-700">Discount:</span> ${{ formatCurrency(selectedQuote.discount_amount) }}</p>
                  <p><span class="font-medium text-gray-700">Items Count:</span> {{ selectedQuote.items?.length || 0 }}</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-3 space-y-1">
                  <p class="font-semibold text-gray-800">Approval Trail</p>
                  <p><span class="font-medium text-gray-700">Approved At:</span> {{ formatDate(selectedQuote.approved_at) }}</p>
                  <p><span class="font-medium text-gray-700">Rejected At:</span> {{ formatDate(selectedQuote.rejected_at) }}</p>
                  <p><span class="font-medium text-gray-700">Rejected Reason:</span> {{ selectedQuote.rejection_reason || 'N/A' }}</p>
                  <p><span class="font-medium text-gray-700">Admin Notes:</span> {{ selectedQuote.admin_notes || adminNotes || 'N/A' }}</p>
                </div>
              </div>

              <div class="mt-4 bg-white rounded-lg border border-gray-200 p-3 text-sm" v-if="selectedQuote.order?.invoice">
                <p class="font-semibold text-gray-800 mb-1">Invoice</p>
                <p><span class="font-medium text-gray-700">Invoice #:</span> {{ selectedQuote.order.invoice.invoice_number || 'N/A' }}</p>
                <p><span class="font-medium text-gray-700">Status:</span> {{ selectedQuote.order.invoice.status || 'N/A' }}</p>
                <p><span class="font-medium text-gray-700">Due Date:</span> {{ formatDate(selectedQuote.order.invoice.due_date) }}</p>
                <p><span class="font-medium text-gray-700">Amount:</span> ${{ formatCurrency(selectedQuote.order.invoice.total_amount) }}</p>
              </div>

              <div class="mt-4 bg-white rounded-lg border border-gray-200 p-3 text-sm">
                <p class="font-semibold text-gray-800 mb-1">Description</p>
                <p class="text-gray-700 whitespace-pre-wrap">{{ selectedQuote.description || 'N/A' }}</p>
              </div>
            </div>
          </div>

          <!-- Admin Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
            <textarea
              v-model="adminNotes"
              placeholder="Add internal notes..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597] h-24"
            ></textarea>
          </div>

          <!-- Rejection Reason (if rejecting) -->
          <div v-if="showRejectionReason">
            <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
            <textarea
              v-model="rejectionReason"
              placeholder="Provide a reason for rejection..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597] h-20"
              required
            ></textarea>
          </div>

          <!-- Already Approved Message -->
          <div v-if="selectedQuote.order || selectedQuote.status === 'approved'" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
              <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
              <div>
                <p class="font-semibold text-green-900">Quote Already Approved</p>
                <p class="text-sm text-green-700">This quote has been approved and converted to order: {{ selectedQuote.order?.order_number }}</p>
              </div>
            </div>
          </div>

          <!-- Already Rejected Message -->
          <div v-else-if="selectedQuote.status === 'rejected'" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
              <i class="fas fa-times-circle text-red-600 text-xl mr-3"></i>
              <div>
                <p class="font-semibold text-red-900">Quote Rejected</p>
                <p class="text-sm text-red-700" v-if="selectedQuote.rejection_reason">Reason: {{ selectedQuote.rejection_reason }}</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons (only for pending quotes) -->
          <div v-else class="flex space-x-3 justify-end border-t pt-4">
            <button
              @click="selectedQuote = null"
              class="px-6 py-2 border border-cyan-500/50 rounded-lg text-cyan-300 font-medium hover:bg-cyan-500/20 transition text-sm"
            >
              Cancel
            </button>
            <button
              v-if="!showRejectionReason"
              @click="showRejectionReason = true"
              class="px-6 py-2 bg-rose-600/30 hover:bg-rose-600/40 text-rose-300 font-medium rounded-lg transition border border-rose-500/50 text-sm"
            >
              <i class="fas fa-times mr-2"></i>Reject
            </button>
            <button
              v-if="showRejectionReason"
              @click="rejectQuote"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg transition disabled:opacity-50 text-sm"
            >
              <i class="fas fa-check mr-2"></i>Confirm Rejection
            </button>
            <button
              v-if="!showRejectionReason"
              @click="approveQuote"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium rounded-lg transition disabled:opacity-50 text-sm"
            >
              <i class="fas fa-check mr-2"></i>Approve
            </button>
          </div>
          
          <!-- Close Button (for already processed quotes) -->
          <div v-if="selectedQuote.order || selectedQuote.status === 'approved' || selectedQuote.status === 'rejected'" class="flex justify-end mt-4">
            <button
              @click="selectedQuote = null"
              class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium rounded-lg transition text-sm"
            >
              <i class="fas fa-times mr-2"></i>Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 z-[9999] bg-black/50 backdrop-blur-[2px] flex items-center justify-center p-4" @click="showDeleteConfirm = false">
      <div class="rounded-2xl shadow-2xl max-w-md w-full border border-white/10" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(10, 41, 72, 0.95));" @click.stop>
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4" style="background: rgba(239, 68, 68, 0.2);">
              <i class="fas fa-trash text-rose-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-white">Delete {{ selectedQuotes.length }} Quote(s)?</h3>
          </div>
          <p class="text-slate-300 mb-6">Are you sure you want to permanently delete {{ selectedQuotes.length }} quote(s)? This action cannot be undone.</p>
          <div class="flex justify-end gap-3">
            <button
              @click="showDeleteConfirm = false"
              class="px-4 py-2 border border-white/10 rounded-lg text-slate-300 hover:bg-white/10 transition text-sm font-medium"
            >
              Cancel
            </button>
            <button
              @click="executeBulkDelete"
              :disabled="isSubmitting"
              class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg transition disabled:opacity-50 text-sm"
            >
              <i class="fas fa-trash mr-2"></i>Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const quotes = ref([])
const selectedQuote = ref(null)
const selectedQuotes = ref([])
const searchQuery = ref('')
const sortBy = ref('newest')
const currentPage = ref(1)
const totalQuotes = ref(0)
const lastPage = ref(1)
const adminNotes = ref('')
const rejectionReason = ref('')
const showRejectionReason = ref(false)
const showDeleteConfirm = ref(false)
const showFullRowDetails = ref(false)
const isLoadingQuoteDetails = ref(false)
const isSubmitting = ref(false)
const stats = ref({
  total: 0,
  pending: 0,
  approved: 0,
  rejected: 0
})

const allSelected = computed(() => {
  return quotes.value.length > 0 && quotes.value.every(quote => selectedQuotes.value.includes(quote.id))
})

const pageNumbers = computed(() => {
  const total = lastPage.value
  const current = currentPage.value
  const windowSize = 5
  const half = Math.floor(windowSize / 2)
  let start = Math.max(1, current - half)
  let end = Math.min(total, start + windowSize - 1)

  if (end - start + 1 < windowSize) {
    start = Math.max(1, end - windowSize + 1)
  }

  const pages = []
  for (let page = start; page <= end; page += 1) {
    pages.push(page)
  }
  return pages
})

const normalizedQuoteItems = computed(() => {
  const rawItems = Array.isArray(selectedQuote.value?.items) ? selectedQuote.value.items : []
  const lineItems = Array.isArray(selectedQuote.value?.lineItems) ? selectedQuote.value.lineItems : []
  const sourceItems = rawItems.length > 0 ? rawItems : lineItems

  if (sourceItems.length === 0) return []

  return sourceItems.map((item, index) => {
    const lineItem = lineItems[index] || {}
    const quantity = Number(
      item?.quantity
      || item?.qty
      || lineItem?.quantity
      || lineItem?.qty
      || 0
    )

    const price = Number(
      item?.price
      || item?.unit_price
      || item?.unitPrice
      || lineItem?.unit_price
      || lineItem?.price
      || 0
    )

    const name = String(
      item?.product_name
      || item?.productName
      || item?.partDescription
      || item?.item_name
      || item?.name
      || item?.description
      || lineItem?.product_name
      || lineItem?.item_name
      || lineItem?.name
      || lineItem?.description
      || item?.sku
      || item?.product_id
      || lineItem?.sku
      || lineItem?.product_id
      || 'Unknown Product'
    ).trim()

    const sku = String(
      item?.sku
      || item?.sku_no
      || item?.partNumber
      || lineItem?.sku
      || lineItem?.sku_no
      || lineItem?.part_number
      || ''
    ).trim()

    return {
      name,
      sku,
      quantity,
      price,
      lineTotal: quantity * price
    }
  })
})

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const formatDate = (date) => {
  if (!date) {
    return 'N/A'
  }

  const parsed = new Date(date)
  if (Number.isNaN(parsed.getTime())) {
    return 'N/A'
  }

  return parsed.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const getUserName = (row) => {
  return row?.user?.name || 'Unknown user'
}

const getUserEmail = (row) => {
  return row?.user?.email || 'No email'
}

const getCompanyName = (row) => {
  return row?.user?.company?.name || row?.company?.name || 'Unknown company'
}

const getQuoteDisplayName = (quote) => {
  const fromDescription = String(quote?.description || '').trim()
  if (fromDescription) return fromDescription

  const items = Array.isArray(quote?.items) ? quote.items : []
  if (items.length > 0) {
    const first = items[0] || {}
    const firstName = String(
      first.product_name
      || first.productName
      || first.partDescription
      || first.name
      || first.description
      || ''
    ).trim()

    if (firstName) {
      const extra = Math.max(items.length - 1, 0)
      return extra > 0 ? `${firstName} +${extra} more` : firstName
    }
  }

  return `Quote ${quote?.quote_id || quote?.id || ''}`.trim()
}

const formatStatus = (status) => {
  if (!status) return 'N/A'
  return String(status).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

const fetchQuotes = async () => {
  try {
    const response = await api.get('/admin/quotes/pending', {
      params: {
        page: currentPage.value,
        pageSize: 10,
        search: searchQuery.value || undefined,
        sortBy: sortBy.value || undefined
      }
    })
    
    if (response.data.success) {
      quotes.value = response.data.data
      totalQuotes.value = response.data.pagination.total
      lastPage.value = response.data.pagination.last_page
    }
  } catch (error) {
    console.error('[fetchQuotes] Failed to fetch pending quotes:', error)
    alert('Failed to fetch quotes: ' + error.message)
  }
}

const fetchStats = async () => {
  try {
    const response = await api.get('/admin/quotes/stats')
    if (response.data.success) {
      stats.value = response.data.stats
    }
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const selectQuote = async (quote) => {
  selectedQuote.value = quote
  adminNotes.value = quote.admin_notes || ''
  rejectionReason.value = ''
  showRejectionReason.value = false
  showFullRowDetails.value = false

  isLoadingQuoteDetails.value = true
  try {
    const response = await api.get(`/admin/quotes/${quote.id}`)
    if (response.data?.success && response.data?.data) {
      selectedQuote.value = response.data.data
      adminNotes.value = response.data.data.admin_notes || ''
    }
  } catch (error) {
    console.error('Failed to fetch full quote details:', error)
  } finally {
    isLoadingQuoteDetails.value = false
  }
}

const approveQuote = async () => {
  if (!selectedQuote.value) return

  isSubmitting.value = true
  try {
    console.log('[approveQuote] Starting approval for quote:', selectedQuote.value.id)
    
    const response = await api.post(`/admin/quotes/${selectedQuote.value.id}/approve`, {
      admin_notes: adminNotes.value
    })

    console.log('[approveQuote] Approval response:', response.data)
    
    if (response.data.success) {
      alert(response.data.message || 'Quote action completed successfully!')

      if (!response.data?.data?.payment_required) {
        selectedQuote.value = null
      }
      
      console.log('[approveQuote] Calling fetchQuotes after approval...')
      await fetchQuotes()
      console.log('[approveQuote] fetchQuotes completed')
      
      await fetchStats()
      console.log('[approveQuote] fetchStats completed')
    } else {
      alert(response.data.message || 'Failed to process quote approval')
    }
  } catch (error) {
    console.error('Failed to approve quote:', error)
    const responseMessage = error.response?.data?.message || error.message
    const responseStatus = error.response?.data?.data?.status
    const statusNote = responseStatus ? ` (Current status: ${formatStatus(responseStatus)})` : ''
    alert('Failed to approve quote: ' + responseMessage + statusNote)
  } finally {
    isSubmitting.value = false
  }
}

const rejectQuote = async () => {
  if (!selectedQuote.value || !rejectionReason.value) {
    alert('Please provide a rejection reason')
    return
  }

  isSubmitting.value = true
  try {
    const response = await api.post(`/admin/quotes/${selectedQuote.value.id}/reject`, {
      reason: rejectionReason.value,
      admin_notes: adminNotes.value
    })

    if (response.data.success) {
      alert('Quote rejected successfully!')
      selectedQuote.value = null
      fetchQuotes()
      fetchStats()
    }
  } catch (error) {
    console.error('Failed to reject quote:', error)
    alert('Failed to reject quote: ' + (error.response?.data?.message || error.message))
  } finally {
    isSubmitting.value = false
  }
}

const applyFilters = () => {
  currentPage.value = 1
  fetchQuotes()
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedQuotes.value = []
  } else {
    selectedQuotes.value = quotes.value.map(quote => quote.id)
  }
}

const confirmBulkDelete = () => {
  showDeleteConfirm.value = true
}

const executeBulkDelete = async () => {
  if (selectedQuotes.value.length === 0) return

  isSubmitting.value = true
  try {
    const response = await api.post('/admin/quotes/bulk-delete', {
      quote_ids: selectedQuotes.value
    })

    if (response.data.success) {
      alert(response.data.message)
      selectedQuotes.value = []
      showDeleteConfirm.value = false
      fetchQuotes()
      fetchStats()
    }
  } catch (error) {
    console.error('Failed to delete quotes:', error)
    alert(error.response?.data?.message || 'Failed to delete quotes')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchStats()
  fetchQuotes()
})
</script>
