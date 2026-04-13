<template>
  <AdminLayout>
    <template #title>Customers</template>

    <!-- Filters and Search -->
    <div class="rounded-xl border border-white/10 p-6 mb-6 backdrop-blur" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.7), rgba(10, 41, 72, 0.7));">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-200 mb-2">Search Customer</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Company name or domain..."
            class="w-full px-4 py-2.5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 text-white placeholder-slate-500 transition"
            style="background: rgba(148, 163, 184, 0.12);"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-200 mb-2">Filter by Status</label>
          <select v-model="statusFilter" class="w-full px-4 py-2.5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 text-white transition" style="background: rgba(148, 163, 184, 0.12);">
            <option value="" class="bg-slate-900">All Status</option>
            <option value="pending" class="bg-slate-900">Pending</option>
            <option value="approved" class="bg-slate-900">Approved</option>
            <option value="inactive" class="bg-slate-900">Inactive</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-200 mb-2">Sort By</label>
          <select v-model="sortBy" class="w-full px-4 py-2.5 border border-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 text-white transition" style="background: rgba(148, 163, 184, 0.12);">
            <option value="newest" class="bg-slate-900">Newest</option>
            <option value="oldest" class="bg-slate-900">Oldest</option>
            <option value="name" class="bg-slate-900">Company Name</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium py-2.5 px-4 rounded-lg transition shadow-lg"
          >
            <i class="fas fa-search mr-2"></i>Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="rounded-xl border border-white/10 overflow-hidden backdrop-blur" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.6), rgba(10, 41, 72, 0.6));">
      <div class="px-6 py-4 border-b border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="background: rgba(15, 23, 42, 0.5);">
        <div>
          <h3 class="text-lg font-semibold text-white">Customer Accounts</h3>
          <p class="text-sm text-slate-400">Manage company access and status</p>
        </div>
        <div class="flex items-center gap-3">
          <!-- Bulk Actions -->
          <div v-if="selectedCompanies.length > 0" class="flex gap-2 flex-wrap">
            <span class="text-sm text-cyan-300 self-center"><i class="fas fa-check-square mr-1"></i>{{ selectedCompanies.length }} selected</span>
            <button
              @click="confirmBulkAction('approve')"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-cyan-500/50 text-cyan-300 hover:bg-cyan-500/20 transition"
            >
              <i class="fas fa-check mr-1"></i>Approve
            </button>
            <button
              @click="confirmBulkAction('suspend')"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-amber-500/50 text-amber-300 hover:bg-amber-500/20 transition"
            >
              <i class="fas fa-pause mr-1"></i>Suspend
            </button>
            <button
              @click="confirmBulkAction('activate')"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-emerald-500/50 text-emerald-300 hover:bg-emerald-500/20 transition"
            >
              <i class="fas fa-play mr-1"></i>Activate
            </button>
            <button
              @click="confirmBulkAction('delete')"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-rose-500/50 text-rose-300 hover:bg-rose-500/20 transition"
            >
              <i class="fas fa-trash mr-1"></i>Delete
            </button>
          </div>
          <p class="text-sm text-slate-400">Showing {{ customers.length }} of {{ totalCustomers }} customers</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead style="background: rgba(15, 23, 42, 0.8);" class="border-b border-white/10">
            <tr>
              <th class="px-4 py-3 text-left">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded cursor-pointer" style="accent-color: #22d3ee;"
                />
              </th>
              <th class="px-6 py-3 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Company</th>
              <th class="px-6 py-3 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Domain</th>
              <th class="px-6 py-3 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Primary Contact</th>
              <th class="px-6 py-3 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Users</th>
              <th class="px-6 py-3 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Status</th>
              <th class="px-6 py-3 text-left font-semibold text-slate-200 uppercase text-xs tracking-wide">Joined</th>
              <th class="px-6 py-3 text-right font-semibold text-slate-200 uppercase text-xs tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            <tr v-if="customers.length === 0">
              <td colspan="8" class="px-6 py-16 text-center">
                <i class="fas fa-users text-5xl mb-4 block opacity-20 text-slate-400"></i>
                <p class="text-slate-400 text-lg font-medium">No customers found</p>
              </td>
            </tr>
            <tr v-for="company in customers" :key="company.id" class="hover:bg-white/5 transition">
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :value="company.id"
                  v-model="selectedCompanies"
                  class="w-4 h-4 rounded cursor-pointer" style="accent-color: #22d3ee;"
                />
              </td>
              <td class="px-6 py-4">
                <div class="font-semibold text-slate-100">{{ company.name }}</div>
                <div class="text-xs text-slate-400 font-mono">ID: {{ company.id }}</div>
              </td>
              <td class="px-6 py-4 text-slate-300">{{ company.domain }}</td>
              <td class="px-6 py-4">
                <div v-if="company.users && company.users.length > 0">
                  <div class="font-medium text-slate-100">{{ company.users[0].name }}</div>
                  <div class="text-xs text-slate-400">{{ company.users[0].email }}</div>
                </div>
                <div v-else class="text-xs text-slate-500">No users</div>
              </td>
              <td class="px-6 py-4 text-slate-300">{{ company.users ? company.users.length : 0 }}</td>
              <td class="px-6 py-4">
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusBadgeClass(getCompanyEffectiveStatus(company))]">
                  {{ formatStatusLabel(getCompanyEffectiveStatus(company)) }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-300">{{ formatDate(company.created_at) }}</td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <button
                    @click="viewCustomer(company)"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gradient-to-r from-cyan-500/30 to-blue-600/30 hover:from-cyan-400/40 hover:to-blue-500/40 text-cyan-300 transition border border-cyan-500/30"
                  >
                    View
                  </button>
                  <button
                    v-if="company.status === 'pending'"
                    @click="approveCustomer(company)"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white transition disabled:opacity-50"
                  >
                    Approve
                  </button>
                  <button
                    v-if="canSuspendCompany(company)"
                    @click="manageCustomer(company, 'suspend')"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-amber-500/50 text-amber-300 hover:bg-amber-500/20 transition disabled:opacity-50"
                  >
                    Suspend
                  </button>
                  <button
                    v-if="canActivateCompany(company)"
                    @click="manageCustomer(company, 'activate')"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-emerald-500/50 text-emerald-300 hover:bg-emerald-500/20 transition disabled:opacity-50"
                  >
                    Activate
                  </button>
                  <button
                    @click="manageCustomer(company, 'delete')"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-rose-500/50 text-rose-300 hover:bg-rose-500/20 transition disabled:opacity-50"
                  >
                    Delete
                  </button>
                  <button
                    @click="viewCustomerOrders(company)"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white transition"
                  >
                    Orders
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="px-6 py-4 border-t border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" style="background: rgba(15, 23, 42, 0.5);">
        <div class="text-sm text-slate-300">
          <p>Showing {{ customers.length }} of {{ totalCustomers }} customers</p>
          <p class="mt-1 text-xs text-slate-400">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchCustomers()"
            class="px-3 py-2 border border-white/10 rounded-lg text-slate-300 hover:bg-white/10 hover:text-cyan-300 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
          >
            Previous
          </button>
          <button
            v-for="page in pageNumbers"
            :key="page"
            @click="currentPage = page; fetchCustomers()"
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
            @click="currentPage++; fetchCustomers()"
            class="px-3 py-2 border border-white/10 rounded-lg text-slate-300 hover:bg-white/10 hover:text-cyan-300 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Customer Details Modal -->
    <div v-if="selectedCustomer" class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 p-4" @click="selectedCustomer = null">
      <div class="rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-white/10" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(10, 41, 72, 0.95));" @click.stop>
        <div class="sticky top-0 text-white p-6 border-b border-white/10" style="background: linear-gradient(90deg, rgba(34, 211, 238, 0.15), rgba(59, 130, 246, 0.1));">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-xs uppercase tracking-wide text-cyan-300 font-semibold">Customer Details</p>
              <h3 class="text-xl font-bold text-white mt-1">{{ selectedCustomer.name }}</h3>
            </div>
            <button @click="selectedCustomer = null" class="text-slate-400 hover:text-cyan-300 transition text-2xl">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <!-- Company Info -->
          <div class="rounded-xl border border-white/10 p-4" style="background: linear-gradient(135deg, rgba(34, 211, 238, 0.1), rgba(59, 130, 246, 0.08));">
            <h4 class="font-semibold text-cyan-300 mb-3 text-sm uppercase tracking-wide">Company Information</h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-slate-400">Company Name</p>
                <p class="font-semibold text-white">{{ selectedCustomer.name }}</p>
              </div>
              <div>
                <p class="text-sm text-slate-400">Domain</p>
                <p class="font-semibold text-white">{{ selectedCustomer.domain }}</p>
              </div>
              <div>
                <p class="text-sm text-slate-400">Status</p>
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusBadgeClass(getCompanyEffectiveStatus(selectedCustomer))]">
                  {{ formatStatusLabel(getCompanyEffectiveStatus(selectedCustomer)) }}
                </span>
              </div>
              <div>
                <p class="text-sm text-slate-400">Joined</p>
                <p class="font-semibold text-white">{{ formatDate(selectedCustomer.created_at) }}</p>
              </div>
            </div>
          </div>

          <!-- Team Members -->
          <div>
            <h4 class="font-semibold text-slate-200 mb-3 text-sm uppercase tracking-wide">Team Members</h4>
            <div class="space-y-2">
              <div v-for="user in selectedCustomer.users" :key="user.id" class="rounded-lg p-4 border border-white/10 flex justify-between items-center" style="background: rgba(148, 163, 184, 0.08);">
                <div>
                  <p class="font-medium text-slate-100">{{ user.name }}</p>
                  <p class="text-xs text-slate-400">{{ user.email }}</p>
                </div>
                <span class="text-xs font-semibold text-slate-400 border border-white/10 px-2 py-1 rounded-full">{{ user.role }}</span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3 justify-end border-t border-white/10 pt-4">
            <button
              @click="selectedCustomer = null"
              class="px-6 py-2 border border-cyan-500/50 rounded-lg text-cyan-300 font-medium hover:bg-cyan-500/20 transition text-sm"
            >
              Close
            </button>
            <button
              v-if="selectedCustomer.status === 'pending'"
              @click="approveCustomer(selectedCustomer)"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-medium rounded-lg transition disabled:opacity-50 text-sm"
            >
              <i class="fas fa-check mr-2"></i>Approve
            </button>
            <button
              v-if="canSuspendCompany(selectedCustomer)"
              @click="manageCustomer(selectedCustomer, 'suspend')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-amber-600/30 hover:bg-amber-600/40 text-amber-300 font-medium rounded-lg transition border border-amber-500/50 text-sm disabled:opacity-50"
            >
              <i class="fas fa-pause mr-2"></i>Suspend
            </button>
            <button
              v-if="canActivateCompany(selectedCustomer)"
              @click="manageCustomer(selectedCustomer, 'activate')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-emerald-600/30 hover:bg-emerald-600/40 text-emerald-300 font-medium rounded-lg transition border border-emerald-500/50 text-sm disabled:opacity-50"
            >
              <i class="fas fa-play mr-2"></i>Activate
            </button>
            <button
              @click="manageCustomer(selectedCustomer, 'delete')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-rose-600/30 hover:bg-rose-600/40 text-rose-300 font-medium rounded-lg transition border border-rose-500/50 text-sm disabled:opacity-50"
            >
              <i class="fas fa-trash mr-2"></i>Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal for Bulk Actions -->
    <div v-if="showConfirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 p-4" @click="showConfirmModal = false">
      <div class="rounded-2xl shadow-2xl max-w-md w-full border border-white/10" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(10, 41, 72, 0.95));" @click.stop>
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div :class="[
              'w-12 h-12 rounded-full flex items-center justify-center mr-4',
              bulkAction === 'delete' ? 'bg-rose-500/20' : 'bg-amber-500/20'
            ]">
              <i :class="[
                'fas text-2xl',
                bulkAction === 'delete' ? 'fa-trash text-rose-400' : 'fa-exclamation-triangle text-amber-400'
              ]"></i>
            </div>
            <h3 class="text-lg font-bold text-white">{{ confirmModalTitle }}</h3>
          </div>
          <p class="text-slate-300 mb-6">{{ confirmModalMessage }}</p>
          <div class="flex justify-end gap-3">
            <button
              @click="showConfirmModal = false"
              class="px-4 py-2 border border-white/10 rounded-lg text-slate-300 hover:bg-white/10 transition text-sm font-medium"
            >
              Cancel
            </button>
            <button
              @click="executeBulkAction"
              :disabled="isSubmitting"
              :class="[
                'px-4 py-2 rounded-lg text-white font-medium transition disabled:opacity-50 text-sm',
                bulkAction === 'delete' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-amber-600 hover:bg-amber-700'
              ]"
            >
              <i :class="['fas mr-2', bulkAction === 'delete' ? 'fa-trash' : 'fa-check']"></i>
              {{ bulkAction === 'delete' ? 'Delete' : 'Confirm' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const router = useRouter()

const customers = ref([])
const selectedCustomer = ref(null)
const selectedCompanies = ref([])
const searchQuery = ref('')
const statusFilter = ref('')
const sortBy = ref('newest')
const currentPage = ref(1)
const totalCustomers = ref(0)
const lastPage = ref(1)
const isSubmitting = ref(false)
const showConfirmModal = ref(false)
const bulkAction = ref('')

const allSelected = computed(() => {
  if (customers.value.length === 0) return false
  return customers.value.every(company => selectedCompanies.value.includes(company.id))
})

const confirmModalTitle = computed(() => {
  const count = selectedCompanies.value.length
  if (bulkAction.value === 'delete') {
    return `Delete ${count} Compan${count > 1 ? 'ies' : 'y'}?`
  } else if (bulkAction.value === 'suspend') {
    return `Suspend ${count} Compan${count > 1 ? 'ies' : 'y'}?`
  } else if (bulkAction.value === 'activate') {
    return `Activate ${count} Compan${count > 1 ? 'ies' : 'y'}?`
  } else if (bulkAction.value === 'approve') {
    return `Approve ${count} Compan${count > 1 ? 'ies' : 'y'}?`
  }
  return 'Confirm Action'
})

const confirmModalMessage = computed(() => {
  const count = selectedCompanies.value.length
  if (bulkAction.value === 'delete') {
    return `Are you sure you want to permanently delete ${count} compan${count > 1 ? 'ies' : 'y'} and all associated users? This action cannot be undone.`
  } else if (bulkAction.value === 'suspend') {
    return `Are you sure you want to suspend ${count} compan${count > 1 ? 'ies' : 'y'}? All users will not be able to access the system.`
  } else if (bulkAction.value === 'activate') {
    return `Are you sure you want to activate ${count} compan${count > 1 ? 'ies' : 'y'}? All users will regain access to the system.`
  } else if (bulkAction.value === 'approve') {
    return `Are you sure you want to approve ${count} compan${count > 1 ? 'ies' : 'y'}? They will be granted access to the platform.`
  }
  return ''
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const statusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800',
    suspended: 'bg-red-100 text-red-800',
    mixed: 'bg-purple-100 text-purple-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getCompanyEffectiveStatus = (company) => {
  const users = Array.isArray(company?.users) ? company.users : []

  if (users.length > 0) {
    const hasActive = users.some((user) => user?.status === 'active')
    const hasSuspended = users.some((user) => user?.status === 'suspended')

    if (hasSuspended && !hasActive) return 'suspended'
    if (hasActive && !hasSuspended) return 'active'
    if (hasActive && hasSuspended) return 'mixed'
  }

  if (company?.status === 'approved') return 'active'
  if (company?.status === 'inactive') return 'suspended'

  return company?.status || 'unknown'
}

const formatStatusLabel = (status) => {
  const labels = {
    pending: 'Pending',
    approved: 'Approved',
    active: 'Active',
    inactive: 'Inactive',
    suspended: 'Suspended',
    mixed: 'Mixed',
    unknown: 'Unknown',
  }

  return labels[status] || status
}

const canSuspendCompany = (company) => {
  if (!company || company.status === 'pending') return false
  const effectiveStatus = getCompanyEffectiveStatus(company)
  return effectiveStatus === 'active' || effectiveStatus === 'mixed'
}

const canActivateCompany = (company) => {
  if (!company) return false
  const effectiveStatus = getCompanyEffectiveStatus(company)
  return effectiveStatus === 'suspended' || effectiveStatus === 'mixed'
}

const fetchCustomers = async () => {
  try {
    const params = {
      page: currentPage.value,
      pageSize: 100
    }

    if (statusFilter.value) params.status = statusFilter.value
    if (searchQuery.value) params.search = searchQuery.value
    if (sortBy.value) params.sortBy = sortBy.value

    const response = await api.get('/admin/customers', { params })

    if (response.data.success) {
      customers.value = response.data.data
      totalCustomers.value = response.data.pagination.total
      lastPage.value = response.data.pagination.last_page
    }
  } catch (error) {
    console.error('Failed to fetch customers:', error)
  }
}

const viewCustomer = (company) => {
  selectedCustomer.value = company
}

const approveCustomer = async (company) => {
  isSubmitting.value = true
  try {
    const response = await api.post(`/admin/customers/${company.id}/approve`)
    
    if (response.data.success) {
      alert('Customer approved successfully!')
      selectedCustomer.value = null
      fetchCustomers()
    }
  } catch (error) {
    console.error('Failed to approve customer:', error)
    alert(error.response?.data?.message || 'Failed to approve customer')
  } finally {
    isSubmitting.value = false
  }
}

const manageCustomer = async (company, action) => {
  const actionText = action === 'delete'
    ? 'delete this customer and all associated users'
    : `${action} this customer account`

  if (!confirm(`Are you sure you want to ${actionText}?`)) {
    return
  }

  isSubmitting.value = true
  try {
    let response

    if (action === 'delete') {
      response = await api.post('/admin/customers/bulk-delete', {
        company_ids: [company.id]
      })
    } else if (action === 'suspend' || action === 'activate') {
      response = await api.post('/admin/customers/bulk-suspend', {
        company_ids: [company.id],
        action
      })
    }

    if (response?.data?.success) {
      alert(response.data.message || `Customer ${action}d successfully`)
      if (selectedCustomer.value?.id === company.id) {
        selectedCustomer.value = null
      }
      fetchCustomers()
    }
  } catch (error) {
    console.error(`Failed to ${action} customer:`, error)
    alert(error.response?.data?.message || `Failed to ${action} customer`)
  } finally {
    isSubmitting.value = false
  }
}

const viewCustomerOrders = (company) => {
  // Navigate to orders filtered by this customer
  router.push({
    name: 'admin-orders',
    query: {
      company_id: company.id,
      company_name: company.name
    }
  })
}

const applyFilters = () => {
  currentPage.value = 1
  fetchCustomers()
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedCompanies.value = []
  } else {
    selectedCompanies.value = customers.value.map(company => company.id)
  }
}

const confirmBulkAction = (action) => {
  bulkAction.value = action
  showConfirmModal.value = true
}

const executeBulkAction = async () => {
  if (selectedCompanies.value.length === 0) return

  isSubmitting.value = true
  try {
    let response
    if (bulkAction.value === 'approve') {
      response = await api.post('/admin/customers/bulk-approve', {
        company_ids: selectedCompanies.value
      })
    } else if (bulkAction.value === 'delete') {
      response = await api.post('/admin/customers/bulk-delete', {
        company_ids: selectedCompanies.value
      })
    } else if (bulkAction.value === 'suspend' || bulkAction.value === 'activate') {
      response = await api.post('/admin/customers/bulk-suspend', {
        company_ids: selectedCompanies.value,
        action: bulkAction.value
      })
    }

    if (response && response.data.success) {
      alert(response.data.message)
      selectedCompanies.value = []
      showConfirmModal.value = false
      fetchCustomers()
    }
  } catch (error) {
    console.error('Failed to execute bulk action:', error)
    alert(error.response?.data?.message || 'Failed to complete action')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  fetchCustomers()
})
</script>
