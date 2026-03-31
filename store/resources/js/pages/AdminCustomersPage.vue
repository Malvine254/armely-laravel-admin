<template>
  <AdminLayout>
    <template #title>Customers</template>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search Customer</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Company name or domain..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
          <select v-model="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
          <select v-model="sortBy" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2f5597]">
            <option value="newest">Newest</option>
            <option value="oldest">Oldest</option>
            <option value="name">Company Name</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-[#2f5597] hover:bg-[#274a82] text-white font-medium py-2 px-4 rounded-lg transition"
          >
            <i class="fas fa-search mr-2"></i>Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Customer Accounts</h3>
          <p class="text-sm text-gray-600">Manage company access and status</p>
        </div>
        <div class="flex items-center gap-3">
          <!-- Bulk Actions -->
          <div v-if="selectedCompanies.length > 0" class="flex gap-2">
            <span class="text-sm text-gray-600 self-center">{{ selectedCompanies.length }} selected</span>
            <button
              @click="confirmBulkAction('approve')"
              class="px-3 py-2 text-xs font-semibold rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-50 transition"
            >
              <i class="fas fa-check mr-1"></i>Approve
            </button>
            <button
              @click="confirmBulkAction('suspend')"
              class="px-3 py-2 text-xs font-semibold rounded-lg border border-yellow-600 text-yellow-600 hover:bg-yellow-50 transition"
            >
              <i class="fas fa-pause mr-1"></i>Suspend
            </button>
            <button
              @click="confirmBulkAction('activate')"
              class="px-3 py-2 text-xs font-semibold rounded-lg border border-green-600 text-green-600 hover:bg-green-50 transition"
            >
              <i class="fas fa-play mr-1"></i>Activate
            </button>
            <button
              @click="confirmBulkAction('delete')"
              class="px-3 py-2 text-xs font-semibold rounded-lg border border-red-600 text-red-600 hover:bg-red-50 transition"
            >
              <i class="fas fa-trash mr-1"></i>Delete
            </button>
          </div>
          <p class="text-sm text-gray-600">Showing {{ customers.length }} of {{ totalCustomers }} customers</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-3 text-left">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 text-[#2f5597] border-gray-300 rounded focus:ring-[#2f5597]"
                />
              </th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700">Company</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700">Domain</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700">Primary Contact</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700">Users</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
              <th class="px-6 py-3 text-left font-semibold text-gray-700">Joined</th>
              <th class="px-6 py-3 text-right font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-if="customers.length === 0">
              <td colspan="8" class="px-6 py-9 text-center text-gray-500">
                <i class="fas fa-users text-3xl mb-3 block text-gray-300"></i>
                No customers found
              </td>
            </tr>
            <tr v-for="company in customers" :key="company.id" class="hover:bg-gray-50 transition">
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :value="company.id"
                  v-model="selectedCompanies"
                  class="w-4 h-4 text-[#2f5597] border-gray-300 rounded focus:ring-[#2f5597]"
                />
              </td>
              <td class="px-6 py-4">
                <div class="font-semibold text-gray-900">{{ company.name }}</div>
                <div class="text-xs text-gray-500">ID: {{ company.id }}</div>
              </td>
              <td class="px-6 py-4 text-gray-600">{{ company.domain }}</td>
              <td class="px-6 py-4">
                <div v-if="company.users && company.users.length > 0">
                  <div class="font-medium text-gray-900">{{ company.users[0].name }}</div>
                  <div class="text-xs text-gray-500">{{ company.users[0].email }}</div>
                </div>
                <div v-else class="text-xs text-gray-500">No users</div>
              </td>
              <td class="px-6 py-4 text-gray-600">{{ company.users ? company.users.length : 0 }}</td>
              <td class="px-6 py-4">
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusBadgeClass(getCompanyEffectiveStatus(company))]">
                  {{ formatStatusLabel(getCompanyEffectiveStatus(company)) }}
                </span>
              </td>
              <td class="px-6 py-4 text-gray-600">{{ formatDate(company.created_at) }}</td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <button
                    @click="viewCustomer(company)"
                    class="px-3 py-2 text-xs font-semibold rounded-lg border border-[#2f5597] text-[#2f5597] hover:bg-[#edf3fb] transition"
                  >
                    View
                  </button>
                  <button
                    v-if="company.status === 'pending'"
                    @click="approveCustomer(company)"
                    :disabled="isSubmitting"
                    class="px-3 py-2 text-xs font-semibold rounded-lg bg-[#2f5597] hover:bg-[#274a82] text-white transition disabled:opacity-50"
                  >
                    Approve
                  </button>
                  <button
                    v-if="canSuspendCompany(company)"
                    @click="manageCustomer(company, 'suspend')"
                    :disabled="isSubmitting"
                    class="px-3 py-2 text-xs font-semibold rounded-lg border border-yellow-600 text-yellow-700 hover:bg-yellow-50 transition disabled:opacity-50"
                  >
                    Suspend
                  </button>
                  <button
                    v-if="canActivateCompany(company)"
                    @click="manageCustomer(company, 'activate')"
                    :disabled="isSubmitting"
                    class="px-3 py-2 text-xs font-semibold rounded-lg border border-green-600 text-green-700 hover:bg-green-50 transition disabled:opacity-50"
                  >
                    Activate
                  </button>
                  <button
                    @click="manageCustomer(company, 'delete')"
                    :disabled="isSubmitting"
                    class="px-3 py-2 text-xs font-semibold rounded-lg border border-red-600 text-red-700 hover:bg-red-50 transition disabled:opacity-50"
                  >
                    Delete
                  </button>
                  <button
                    @click="viewCustomerOrders(company)"
                    class="px-3 py-2 text-xs font-semibold rounded-lg bg-[#2f5597] hover:bg-[#274a82] text-white transition"
                  >
                    Orders
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="text-sm text-gray-600">
          <p>Showing {{ customers.length }} of {{ totalCustomers }} customers</p>
          <p class="mt-1">Page {{ currentPage }} of {{ lastPage }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            :disabled="currentPage === 1"
            @click="currentPage--; fetchCustomers()"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
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
                ? 'bg-[#2f5597] text-white border-[#2f5597]'
                : 'border-gray-300 text-gray-700 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          <button
            :disabled="currentPage >= lastPage"
            @click="currentPage++; fetchCustomers()"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Customer Details Modal -->
    <div v-if="selectedCustomer" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 text-white p-6 border-b" style="background: linear-gradient(90deg, #2f5597, #1f4788);">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold">{{ selectedCustomer.name }}</h3>
            <button @click="selectedCustomer = null" class="text-white hover:text-gray-200">
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <!-- Company Info -->
          <div class="bg-[#edf3fb] p-4 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-3">Company Information</h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600">Company Name</p>
                <p class="font-semibold text-gray-900">{{ selectedCustomer.name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Domain</p>
                <p class="font-semibold text-gray-900">{{ selectedCustomer.domain }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Status</p>
                <span :class="['px-3 py-1 rounded-full text-xs font-semibold', statusBadgeClass(getCompanyEffectiveStatus(selectedCustomer))]">
                  {{ formatStatusLabel(getCompanyEffectiveStatus(selectedCustomer)) }}
                </span>
              </div>
              <div>
                <p class="text-sm text-gray-600">Joined</p>
                <p class="font-semibold text-gray-900">{{ formatDate(selectedCustomer.created_at) }}</p>
              </div>
            </div>
          </div>

          <!-- Team Members -->
          <div>
            <h4 class="font-semibold text-gray-900 mb-3">Team Members</h4>
            <div class="space-y-2">
              <div v-for="user in selectedCustomer.users" :key="user.id" class="bg-gray-50 p-4 rounded-lg flex justify-between items-center">
                <div>
                  <p class="font-medium text-gray-900">{{ user.name }}</p>
                  <p class="text-xs text-gray-600">{{ user.email }}</p>
                </div>
                <span class="text-xs font-semibold text-gray-600">{{ user.role }}</span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex space-x-3 justify-end border-t pt-4">
            <button
              @click="selectedCustomer = null"
              class="px-6 py-2 border border-[#2f5597] rounded-lg text-[#2f5597] font-medium hover:bg-[#edf3fb] transition"
            >
              Close
            </button>
            <button
              v-if="selectedCustomer.status === 'pending'"
              @click="approveCustomer(selectedCustomer)"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-[#2f5597] hover:bg-[#274a82] text-white font-medium rounded-lg transition disabled:opacity-50"
            >
              <i class="fas fa-check mr-2"></i>Approve
            </button>
            <button
              v-if="canSuspendCompany(selectedCustomer)"
              @click="manageCustomer(selectedCustomer, 'suspend')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition disabled:opacity-50"
            >
              <i class="fas fa-pause mr-2"></i>Suspend
            </button>
            <button
              v-if="canActivateCompany(selectedCustomer)"
              @click="manageCustomer(selectedCustomer, 'activate')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition disabled:opacity-50"
            >
              <i class="fas fa-play mr-2"></i>Activate
            </button>
            <button
              @click="manageCustomer(selectedCustomer, 'delete')"
              :disabled="isSubmitting"
              class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition disabled:opacity-50"
            >
              <i class="fas fa-trash mr-2"></i>Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal for Bulk Actions -->
    <div v-if="showConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
        <div class="p-6">
          <div class="flex items-center mb-4">
            <div :class="[
              'w-12 h-12 rounded-full flex items-center justify-center mr-4',
              bulkAction === 'delete' ? 'bg-red-100' : 'bg-yellow-100'
            ]">
              <i :class="[
                'fas text-2xl',
                bulkAction === 'delete' ? 'fa-trash text-red-600' : 'fa-exclamation-triangle text-yellow-600'
              ]"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">{{ confirmModalTitle }}</h3>
          </div>
          <p class="text-gray-600 mb-6">{{ confirmModalMessage }}</p>
          <div class="flex justify-end gap-3">
            <button
              @click="showConfirmModal = false"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
            >
              Cancel
            </button>
            <button
              @click="executeBulkAction"
              :disabled="isSubmitting"
              :class="[
                'px-4 py-2 rounded-lg text-white font-medium transition disabled:opacity-50',
                bulkAction === 'delete' ? 'bg-red-600 hover:bg-red-700' : 'bg-yellow-600 hover:bg-yellow-700'
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
      pageSize: 6
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
