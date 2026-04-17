<template>
  <AdminLayout>
    <template #title>Customers</template>

    <div class="admin-fit-page">

    <div class="rounded-xl border-0 shadow-lg bg-white p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search User</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="User, email, company, or domain..."
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 placeholder-slate-500 transition"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
          <select v-model="statusFilter" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 transition">
            <option value="" class="bg-white">All Status</option>
            <option value="pending" class="bg-white">Pending</option>
            <option value="active" class="bg-white">Active</option>
            <option value="suspended" class="bg-white">Suspended</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
          <select v-model="sortBy" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 transition">
            <option value="newest" class="bg-white">Newest</option>
            <option value="oldest" class="bg-white">Oldest</option>
            <option value="name" class="bg-white">User Name</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium py-2.5 px-4 rounded-lg transition shadow-lg"
          >
            <i class="fas fa-search mr-2"></i>Filter
          </button>
        </div>
      </div>
    </div>

    <div class="admin-table-card rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Customer Users</h3>
          <p class="text-sm text-gray-500">Manage each customer user individually, including access and special pricing</p>
        </div>
        <div class="flex items-center gap-3">
          <div v-if="selectedUsers.length > 0" class="flex gap-2 flex-wrap">
            <span class="text-sm text-[#2F5597] self-center"><i class="fas fa-check-square mr-1"></i>{{ selectedUsers.length }} selected</span>
            <button
              @click="confirmBulkAction('approve')"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#2F5597]/50 text-[#2F5597] hover:bg-[#2F5597]/20 transition"
            >
              <i class="fas fa-check mr-1"></i>Approve
            </button>
            <button
              @click="confirmBulkAction('suspend')"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-amber-500/50 text-amber-600 hover:bg-amber-500/20 transition"
            >
              <i class="fas fa-pause mr-1"></i>Suspend
            </button>
            <button
              @click="confirmBulkAction('activate')"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-emerald-500/50 text-emerald-600 hover:bg-emerald-500/20 transition"
            >
              <i class="fas fa-play mr-1"></i>Activate
            </button>
            <button
              @click="confirmBulkAction('delete')"
              class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-rose-500/50 text-rose-600 hover:bg-rose-500/20 transition"
            >
              <i class="fas fa-trash mr-1"></i>Delete
            </button>
          </div>
          <p class="text-sm text-gray-500">Showing {{ customerUsers.length }} of {{ totalCustomers }} users</p>
        </div>
      </div>

      <div class="overflow-x-auto admin-table-scroll">
        <table class="w-full text-sm">
          <thead class="border-b border-gray-200">
            <tr>
              <th class="px-4 py-3 text-left">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded cursor-pointer"
                  style="accent-color: #2F5597;"
                />
              </th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">User</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Email</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Company</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Status</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Special Pricing</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Joined</th>
              <th class="px-6 py-3 text-right font-semibold text-gray-700 uppercase text-xs tracking-wide">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="customerUsers.length === 0">
              <td colspan="8" class="px-6 py-16 text-center">
                <i class="fas fa-users text-5xl mb-4 block opacity-20 text-gray-500"></i>
                <p class="text-gray-500 text-lg font-medium">No customer users found</p>
              </td>
            </tr>
            <tr v-for="user in customerUsers" :key="user.id" class="hover:bg-gray-50 transition align-top">
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :value="user.id"
                  v-model="selectedUsers"
                  class="w-4 h-4 rounded cursor-pointer"
                  style="accent-color: #2F5597;"
                />
              </td>
              <td class="px-6 py-4">
                <div class="font-semibold text-gray-900">{{ user.name }}</div>
                <div class="text-xs text-gray-500 font-mono">ID: {{ user.id }}</div>
              </td>
              <td class="px-6 py-4 text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4">
                <div class="font-medium text-gray-900">{{ user.company?.name || 'No company' }}</div>
                <div class="text-xs text-gray-500">{{ user.company?.domain || 'No domain' }}</div>
              </td>
              <td class="px-6 py-4">
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusBadgeClass(user.status)]">
                  {{ formatStatusLabel(user.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2 min-w-[170px]">
                  <input
                    v-model.number="specialPricingDrafts[user.id]"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    class="w-24 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                  />
                  <span class="text-xs text-gray-500">%</span>
                  <button
                    @click="saveUserSpecialPricing(user)"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#2F5597] hover:bg-[#1e3a6b] text-white transition disabled:opacity-50"
                  >
                    Save
                  </button>
                </div>
              </td>
              <td class="px-6 py-4 text-gray-500">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2 flex-wrap">
                  <button
                    @click="viewUser(user)"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#2F5597]/10 hover:bg-[#2F5597]/20 text-[#2F5597] transition border border-[#2F5597]/30"
                  >
                    View
                  </button>
                  <button
                    v-if="canApproveUser(user)"
                    @click="approveUser(user)"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#2F5597] hover:bg-[#1e3a6b] text-white transition disabled:opacity-50"
                  >
                    Approve
                  </button>
                  <button
                    v-if="canSuspendUser(user)"
                    @click="manageUser(user, 'suspend')"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-amber-500/50 text-amber-600 hover:bg-amber-500/20 transition disabled:opacity-50"
                  >
                    Suspend
                  </button>
                  <button
                    v-if="canActivateUser(user)"
                    @click="manageUser(user, 'activate')"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-emerald-500/50 text-emerald-600 hover:bg-emerald-500/20 transition disabled:opacity-50"
                  >
                    Activate
                  </button>
                  <button
                    @click="manageUser(user, 'delete')"
                    :disabled="isSubmitting"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-rose-500/50 text-rose-600 hover:bg-rose-500/20 transition disabled:opacity-50"
                  >
                    Delete
                  </button>
                  <button
                    @click="viewCustomerOrders(user)"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#2F5597] hover:bg-[#1e3a6b] text-white transition"
                  >
                    Orders
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="admin-table-pagination px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="text-sm text-gray-500">
          <p>Showing {{ customerUsers.length }} of {{ totalCustomers }} users</p>
          <p class="mt-1 text-xs text-gray-500">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchCustomers()"
            class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#2F5597] transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
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
                ? 'bg-gradient-to-r from-[#2F5597] to-[#1e3a6b] text-white border-[#2F5597]'
                : 'border-gray-200 text-gray-500 hover:border-[#2F5597]/50 hover:text-[#2F5597]'
            ]"
          >
            {{ page }}
          </button>
          <button
            :disabled="currentPage >= lastPage"
            @click="currentPage++; fetchCustomers()"
            class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#2F5597] transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    </div>

    <div v-if="selectedUser" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click="selectedUser = null">
      <div class="rounded-2xl bg-white shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border-0" @click.stop>
        <div class="sticky top-0 text-white p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-xs uppercase tracking-wide text-white/70 font-semibold">Customer User</p>
              <h3 class="text-xl font-bold text-white mt-1">{{ selectedUser.name }}</h3>
            </div>
            <button @click="selectedUser = null" class="text-white/60 hover:text-white transition text-2xl">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <div class="rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-[#2F5597] mb-3 text-sm uppercase tracking-wide">User Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-500">Name</p>
                <p class="font-semibold text-gray-900">{{ selectedUser.name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-semibold text-gray-900">{{ selectedUser.email }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Company</p>
                <p class="font-semibold text-gray-900">{{ selectedUser.company?.name || 'No company' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Domain</p>
                <p class="font-semibold text-gray-900">{{ selectedUser.company?.domain || 'No domain' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Status</p>
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusBadgeClass(selectedUser.status)]">
                  {{ formatStatusLabel(selectedUser.status) }}
                </span>
              </div>
              <div>
                <p class="text-sm text-gray-500">Joined</p>
                <p class="font-semibold text-gray-900">{{ formatDate(selectedUser.created_at) }}</p>
              </div>
            </div>
          </div>

          <div class="rounded-xl border border-gray-200 p-4">
            <h4 class="font-semibold text-[#2F5597] mb-3 text-sm uppercase tracking-wide">Special Pricing</h4>
            <div class="flex flex-wrap items-center gap-2">
              <input
                v-model.number="specialPricingDrafts[selectedUser.id]"
                type="number"
                min="0"
                max="100"
                step="0.01"
                class="w-28 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
              <span class="text-sm text-gray-500">% off</span>
              <button
                @click="saveUserSpecialPricing(selectedUser)"
                :disabled="isSubmitting"
                class="px-4 py-2 text-sm font-semibold rounded-lg bg-[#2F5597] hover:bg-[#1e3a6b] text-white transition disabled:opacity-50"
              >
                Save Pricing
              </button>
            </div>
          </div>

          <div class="flex space-x-3 justify-end border-t border-gray-200 pt-4">
            <button
              @click="selectedUser = null"
              class="px-6 py-2 border border-[#2F5597]/50 rounded-lg text-[#2F5597] font-medium hover:bg-[#2F5597]/20 transition text-sm"
            >
              Close
            </button>
            <button
              v-if="canApproveUser(selectedUser)"
              @click="approveUser(selectedUser)"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition disabled:opacity-50 text-sm"
            >
              <i class="fas fa-check mr-2"></i>Approve
            </button>
            <button
              v-if="canSuspendUser(selectedUser)"
              @click="manageUser(selectedUser, 'suspend')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-amber-600/30 hover:bg-amber-600/40 text-amber-600 font-medium rounded-lg transition border border-amber-500/50 text-sm disabled:opacity-50"
            >
              <i class="fas fa-pause mr-2"></i>Suspend
            </button>
            <button
              v-if="canActivateUser(selectedUser)"
              @click="manageUser(selectedUser, 'activate')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-emerald-600/30 hover:bg-emerald-600/40 text-emerald-600 font-medium rounded-lg transition border border-emerald-500/50 text-sm disabled:opacity-50"
            >
              <i class="fas fa-play mr-2"></i>Activate
            </button>
            <button
              @click="manageUser(selectedUser, 'delete')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-rose-600/30 hover:bg-rose-600/40 text-rose-600 font-medium rounded-lg transition border border-rose-500/50 text-sm disabled:opacity-50"
            >
              <i class="fas fa-trash mr-2"></i>Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showConfirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click="showConfirmModal = false">
      <div class="rounded-2xl bg-white shadow-2xl max-w-md w-full border-0" @click.stop>
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
            <h3 class="text-lg font-bold text-gray-900">{{ confirmModalTitle }}</h3>
          </div>
          <p class="text-gray-500 mb-6">{{ confirmModalMessage }}</p>
          <div class="flex justify-end gap-3">
            <button
              @click="showConfirmModal = false"
              class="px-4 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 transition text-sm font-medium"
            >
              Cancel
            </button>
            <button
              @click="executeBulkAction"
              :disabled="isSubmitting"
              :class="[
                'px-4 py-2 rounded-lg text-gray-900 font-medium transition disabled:opacity-50 text-sm',
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
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const router = useRouter()

const customerUsers = ref([])
const selectedUser = ref(null)
const selectedUsers = ref([])
const searchQuery = ref('')
const statusFilter = ref('')
const sortBy = ref('newest')
const currentPage = ref(1)
const totalCustomers = ref(0)
const lastPage = ref(1)
const isSubmitting = ref(false)
const showConfirmModal = ref(false)
const bulkAction = ref('')
const specialPricingDrafts = ref({})

const allSelected = computed(() => {
  if (customerUsers.value.length === 0) return false
  return customerUsers.value.every((user) => selectedUsers.value.includes(user.id))
})

const confirmModalTitle = computed(() => {
  const count = selectedUsers.value.length
  if (bulkAction.value === 'delete') return `Delete ${count} User${count > 1 ? 's' : ''}?`
  if (bulkAction.value === 'suspend') return `Suspend ${count} User${count > 1 ? 's' : ''}?`
  if (bulkAction.value === 'activate') return `Activate ${count} User${count > 1 ? 's' : ''}?`
  if (bulkAction.value === 'approve') return `Approve ${count} User${count > 1 ? 's' : ''}?`
  return 'Confirm Action'
})

const confirmModalMessage = computed(() => {
  const count = selectedUsers.value.length
  if (bulkAction.value === 'delete') return `Are you sure you want to permanently delete ${count} selected user${count > 1 ? 's' : ''}? This action cannot be undone.`
  if (bulkAction.value === 'suspend') return `Are you sure you want to suspend ${count} selected user${count > 1 ? 's' : ''}?`
  if (bulkAction.value === 'activate') return `Are you sure you want to activate ${count} selected user${count > 1 ? 's' : ''}?`
  if (bulkAction.value === 'approve') return `Are you sure you want to approve ${count} selected user${count > 1 ? 's' : ''}?`
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

const formatDate = (date) => new Date(date).toLocaleDateString('en-US', {
  month: 'short',
  day: 'numeric',
  year: 'numeric'
})

const statusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    active: 'bg-green-100 text-green-800',
    suspended: 'bg-red-100 text-red-800'
  }

  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatusLabel = (status) => {
  const labels = {
    pending: 'Pending',
    active: 'Active',
    suspended: 'Suspended'
  }

  return labels[status] || status || 'Unknown'
}

const canApproveUser = (user) => user?.status === 'pending'
const canSuspendUser = (user) => user?.status === 'active'
const canActivateUser = (user) => user?.status === 'suspended'

const clampSpecialPricingPercent = (value) => {
  const numeric = Number(value)
  if (!Number.isFinite(numeric)) return 0
  return Math.min(100, Math.max(0, Math.round(numeric * 100) / 100))
}

const hydrateSpecialPricingDrafts = (users) => {
  const nextDrafts = { ...specialPricingDrafts.value }

  ;(Array.isArray(users) ? users : []).forEach((user) => {
    nextDrafts[user.id] = clampSpecialPricingPercent(user?.special_pricing_percent ?? 0)
  })

  specialPricingDrafts.value = nextDrafts
}

const syncUserRecord = (userId, updates) => {
  customerUsers.value = customerUsers.value.map((user) => (
    user.id === userId ? { ...user, ...updates } : user
  ))

  if (selectedUser.value?.id === userId) {
    selectedUser.value = { ...selectedUser.value, ...updates }
  }
}

const fetchCustomers = async () => {
  try {
    const params = {
      page: currentPage.value,
      pageSize: 10
    }

    if (statusFilter.value) params.status = statusFilter.value
    if (searchQuery.value) params.search = searchQuery.value
    if (sortBy.value) params.sortBy = sortBy.value

    const response = await api.get('/admin/customers', { params })

    if (response.data.success) {
      customerUsers.value = response.data.data
      totalCustomers.value = response.data.pagination.total
      lastPage.value = response.data.pagination.last_page
      hydrateSpecialPricingDrafts(customerUsers.value)
    }
  } catch (error) {
    console.error('Failed to fetch customer users:', error)
  }
}

const viewUser = (user) => {
  selectedUser.value = { ...user }
  hydrateSpecialPricingDrafts([user])
}

const saveUserSpecialPricing = async (user) => {
  const draftValue = clampSpecialPricingPercent(specialPricingDrafts.value[user.id])
  isSubmitting.value = true

  try {
    const response = await api.post(`/admin/customers/users/${user.id}/special-pricing`, {
      special_pricing_percent: draftValue
    })

    if (response?.data?.success) {
      const updatedPercent = clampSpecialPricingPercent(response.data?.data?.special_pricing_percent ?? draftValue)
      specialPricingDrafts.value[user.id] = updatedPercent
      syncUserRecord(user.id, { special_pricing_percent: updatedPercent })
      alert('Special pricing updated successfully')
    }
  } catch (error) {
    console.error('Failed to update special pricing:', error)
    alert(error.response?.data?.message || 'Failed to update special pricing')
  } finally {
    isSubmitting.value = false
  }
}

const approveUser = async (user) => {
  isSubmitting.value = true

  try {
    const response = await api.post(`/admin/customers/users/${user.id}/approve`)

    if (response?.data?.success) {
      syncUserRecord(user.id, {
        status: 'active',
        company: user.company ? { ...user.company, status: response.data?.data?.company_status || 'approved' } : user.company
      })
      alert(response.data.message || 'Customer user approved successfully')
    }
  } catch (error) {
    console.error('Failed to approve customer user:', error)
    alert(error.response?.data?.message || 'Failed to approve customer user')
  } finally {
    isSubmitting.value = false
  }
}

const manageUser = async (user, action) => {
  const actionText = action === 'delete' ? 'delete this user' : `${action} this user`
  if (!confirm(`Are you sure you want to ${actionText}?`)) return

  isSubmitting.value = true
  try {
    let response

    if (action === 'delete') {
      response = await api.post('/admin/customers/bulk-delete', {
        user_ids: [user.id]
      })
    } else {
      response = await api.post('/admin/customers/bulk-suspend', {
        user_ids: [user.id],
        action
      })
    }

    if (response?.data?.success) {
      alert(response.data.message || `User ${action}d successfully`)

      if (action === 'delete') {
        customerUsers.value = customerUsers.value.filter((entry) => entry.id !== user.id)
        selectedUsers.value = selectedUsers.value.filter((id) => id !== user.id)
        if (selectedUser.value?.id === user.id) {
          selectedUser.value = null
        }
      } else {
        syncUserRecord(user.id, { status: action === 'suspend' ? 'suspended' : 'active' })
      }
    }
  } catch (error) {
    console.error(`Failed to ${action} user:`, error)
    alert(error.response?.data?.message || `Failed to ${action} user`)
  } finally {
    isSubmitting.value = false
  }
}

const viewCustomerOrders = (user) => {
  router.push({
    name: 'admin-orders',
    query: {
      company_id: user.company_id,
      company_name: user.company?.name,
      customer_email: user.email
    }
  })
}

const applyFilters = () => {
  currentPage.value = 1
  fetchCustomers()
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedUsers.value = []
  } else {
    selectedUsers.value = customerUsers.value.map((user) => user.id)
  }
}

const confirmBulkAction = (action) => {
  bulkAction.value = action
  showConfirmModal.value = true
}

const executeBulkAction = async () => {
  if (selectedUsers.value.length === 0) return

  isSubmitting.value = true
  try {
    let response

    if (bulkAction.value === 'approve') {
      response = await api.post('/admin/customers/bulk-approve', {
        user_ids: selectedUsers.value
      })
    } else if (bulkAction.value === 'delete') {
      response = await api.post('/admin/customers/bulk-delete', {
        user_ids: selectedUsers.value
      })
    } else {
      response = await api.post('/admin/customers/bulk-suspend', {
        user_ids: selectedUsers.value,
        action: bulkAction.value
      })
    }

    if (response?.data?.success) {
      alert(response.data.message)
      selectedUsers.value = []
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
