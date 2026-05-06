<template>
  <AdminLayout>
    <template #title>Admin Users</template>

    <div class="admin-fit-page">

      <!-- Filters -->
      <div class="rounded-xl shadow-lg bg-white p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Name, email, or role..."
              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 placeholder-slate-500 transition"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
            <select v-model="roleFilter" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 transition bg-white">
              <option value="">All Roles</option>
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select v-model="statusFilter" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 transition bg-white">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
          <div class="flex items-end">
            <button @click="fetchAdminUsers" class="px-5 py-2.5 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition">
              Search
            </button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="rounded-xl shadow-lg bg-white overflow-hidden flex-1 flex flex-col min-h-0">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 flex-shrink-0">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Admin Users</h3>
            <p class="text-sm text-gray-500">Manage administrators and portal access</p>
          </div>
          <button
            v-if="isSuperAdmin"
            @click="showAddAdminModal = true"
            class="px-4 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition text-sm"
          >
            + Add Admin
          </button>
        </div>

        <div class="overflow-x-auto overflow-y-auto flex-1">
          <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 sticky top-0">
              <tr>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase text-xs tracking-wide">Admin</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase text-xs tracking-wide">Role</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase text-xs tracking-wide">Permissions</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase text-xs tracking-wide">Status</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase text-xs tracking-wide">Login</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase text-xs tracking-wide">Created</th>
                <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase text-xs tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="filteredAdmins.length === 0">
                <td colspan="7" class="px-6 py-16 text-center text-gray-400">No admin users found</td>
              </tr>
              <tr v-for="admin in filteredAdmins" :key="admin.id" class="hover:bg-gray-50 transition">
                <!-- Admin Info -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0" style="background: linear-gradient(135deg, #2F5597, #1e3a6b)">
                      {{ getInitials(admin.name) }}
                    </div>
                    <div>
                      <div class="font-semibold text-gray-900">{{ admin.name }}</div>
                      <div class="text-xs text-gray-500">{{ admin.email }}</div>
                    </div>
                  </div>
                </td>

                <!-- Role -->
                <td class="px-6 py-4">
                  <span :class="[
                    'px-2.5 py-1 rounded-full text-xs font-semibold',
                    admin.role === 'super_admin' ? 'bg-violet-100 text-violet-700' : 'bg-[#2F5597]/10 text-[#2F5597]'
                  ]">
                    {{ admin.role === 'super_admin' ? 'Super Admin' : 'Admin' }}
                  </span>
                </td>

                <!-- Permissions summary -->
                <td class="px-6 py-4">
                  <span v-if="admin.role === 'super_admin'" class="text-xs text-violet-600 font-medium">All permissions</span>
                  <span v-else-if="!admin.permissions || admin.permissions.length === 0" class="text-xs text-gray-400 italic">None assigned</span>
                  <div v-else class="flex flex-wrap gap-1">
                    <span v-for="p in admin.permissions.slice(0, 3)" :key="p" class="px-1.5 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-medium rounded">
                      {{ permissionLabel(p) }}
                    </span>
                    <span v-if="admin.permissions.length > 3" class="px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[10px] rounded">
                      +{{ admin.permissions.length - 3 }}
                    </span>
                  </div>
                </td>

                <!-- Status -->
                <td class="px-6 py-4">
                  <span :class="[
                    'px-2.5 py-1 rounded-full text-xs font-semibold',
                    admin.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-600'
                  ]">
                    {{ admin.status === 'active' ? 'Active' : 'Suspended' }}
                  </span>
                </td>

                <!-- Login status -->
                <td class="px-6 py-4">
                  <span v-if="admin.force_password_change && isTempExpired(admin.temp_password_expires_at)" class="px-2 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-600">
                    Expired
                  </span>
                  <span v-else-if="admin.force_password_change" class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-600">
                    Awaiting · {{ tempExpiresIn(admin.temp_password_expires_at) }}
                  </span>
                  <span v-else class="px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-600">Active</span>
                </td>

                <!-- Created -->
                <td class="px-6 py-4 text-gray-500 text-sm">{{ new Date(admin.created_at).toLocaleDateString() }}</td>

                <!-- Actions -->
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end items-center gap-2 flex-wrap">
                    <!-- Resend invite -->
                    <button
                      v-if="admin.force_password_change"
                      @click="resendCredentials(admin.id)"
                      class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#2F5597]/30 text-[#2F5597] bg-[#2F5597]/5 hover:bg-[#2F5597]/15 transition"
                    >
                      Resend Invite
                    </button>

                    <!-- Elevate to super admin -->
                    <button
                      v-if="isSuperAdmin && admin.role === 'admin' && admin.id !== currentUserId"
                      @click="elevateToSuperAdmin(admin)"
                      class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-violet-300 text-violet-700 bg-violet-50 hover:bg-violet-100 transition"
                    >
                      Elevate
                    </button>

                    <!-- Manage permissions -->
                    <button
                      v-if="isSuperAdmin && admin.role === 'admin' && admin.id !== currentUserId"
                      @click="openPermissionsModal(admin)"
                      class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 transition"
                    >
                      Permissions
                    </button>

                    <!-- Suspend / Activate -->
                    <button
                      v-if="admin.status === 'active' && admin.id !== currentUserId"
                      @click="suspendAdmin(admin.id)"
                      class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 transition"
                    >
                      Suspend
                    </button>
                    <button
                      v-else-if="admin.status !== 'active' && admin.id !== currentUserId"
                      @click="activateAdmin(admin.id)"
                      class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition"
                    >
                      Activate
                    </button>

                    <!-- Delete -->
                    <button
                      v-if="admin.id !== currentUserId"
                      @click="deleteAdmin(admin.id)"
                      class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-rose-300 text-rose-600 bg-rose-50 hover:bg-rose-100 transition"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add Admin Modal -->
    <div v-if="showAddAdminModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeAddAdminModal">
      <div class="rounded-xl bg-white shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #2F5597, #1e3a6b)">
          <h3 class="text-lg font-semibold text-white">Add New Admin</h3>
          <p class="text-sm text-white/60 mt-0.5">An invite email will be sent with a temporary password</p>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
            <input v-model="newAdmin.name" type="text" placeholder="Jane Smith" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 placeholder-slate-400 bg-gray-50" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
            <input v-model="newAdmin.email" type="email" placeholder="jane@armely.com" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 placeholder-slate-400 bg-gray-50" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
            <select v-model="newAdmin.role" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-gray-900 bg-white">
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
          <button @click="closeAddAdminModal" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm">Cancel</button>
          <button @click="createAdmin" class="px-4 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition text-sm">Create Admin</button>
        </div>
      </div>
    </div>

    <!-- Permissions Modal -->
    <div v-if="permissionsModal.open" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closePermissionsModal">
      <div class="rounded-xl bg-white shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100" style="background: linear-gradient(135deg, #2F5597, #1e3a6b)">
          <h3 class="text-lg font-semibold text-white">Manage Permissions</h3>
          <p class="text-sm text-white/60 mt-0.5">{{ permissionsModal.admin?.name }} &mdash; {{ permissionsModal.admin?.email }}</p>
        </div>

        <div class="p-6">
          <p class="text-sm text-gray-500 mb-4">Select which sections this admin is allowed to access and manage. Super admins always have full access.</p>

          <div class="space-y-3">
            <label
              v-for="perm in ALL_PERMISSIONS"
              :key="perm.key"
              class="flex items-start gap-3 p-3 rounded-lg border border-gray-100 hover:border-[#2F5597]/30 hover:bg-blue-50/40 cursor-pointer transition group"
            >
              <input
                type="checkbox"
                :value="perm.key"
                v-model="permissionsModal.selected"
                class="mt-0.5 accent-[#2F5597] w-4 h-4 flex-shrink-0"
              />
              <div>
                <div class="text-sm font-semibold text-gray-800 group-hover:text-[#2F5597] transition">{{ perm.label }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ perm.description }}</div>
              </div>
            </label>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-3">
          <button @click="permissionsModal.selected = []" class="text-xs text-gray-400 hover:text-gray-600 transition">Clear all</button>
          <div class="flex gap-3">
            <button @click="closePermissionsModal" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm">Cancel</button>
            <button @click="savePermissions" class="px-4 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition text-sm">Save Permissions</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const ALL_PERMISSIONS = [
  { key: 'manage_quotes',    label: 'Quotes',    description: 'View, approve, and reject customer quotes' },
  { key: 'manage_orders',    label: 'Orders',    description: 'View and cancel orders; access order tracking' },
  { key: 'manage_customers', label: 'Customers', description: 'View, approve, suspend, and invite customers' },
  { key: 'manage_invoices',  label: 'Invoices',  description: 'View invoices and record payments' },
  { key: 'manage_reports',   label: 'Reports',   description: 'Access revenue reports and analytics' },
  { key: 'manage_settings',  label: 'Settings',  description: 'Change system and API configuration' },
  { key: 'manage_admins',    label: 'Admins',    description: 'Add, suspend, and manage other admin accounts' },
]

const permissionLabel = (key) => ALL_PERMISSIONS.find(p => p.key === key)?.label ?? key

const searchQuery = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const adminUsers = ref([])
const currentUserId = ref(null)
const isSuperAdmin = ref(false)

const showAddAdminModal = ref(false)
const newAdmin = ref({ name: '', email: '', role: 'admin' })

const permissionsModal = ref({ open: false, admin: null, selected: [] })

const filteredAdmins = computed(() => {
  let list = Array.isArray(adminUsers.value) ? adminUsers.value : []
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(u => u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q) || u.role?.toLowerCase().includes(q))
  }
  if (roleFilter.value) list = list.filter(u => u.role === roleFilter.value)
  if (statusFilter.value) list = list.filter(u => u.status === statusFilter.value)
  return list
})

const getInitials = (name) => name ? name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) : 'A'

const isTempExpired = (expiresAt) => expiresAt && new Date(expiresAt) < new Date()

const tempExpiresIn = (expiresAt) => {
  if (!expiresAt) return ''
  const ms = new Date(expiresAt) - Date.now()
  if (ms <= 0) return 'expired'
  const hrs = Math.floor(ms / 3600000)
  const mins = Math.floor((ms % 3600000) / 60000)
  return hrs > 0 ? `${hrs}h ${mins}m` : `${mins}m`
}

const fetchAdminUsers = async () => {
  try {
    const res = await api.get('/admin/users')
    if (res.data.success) adminUsers.value = res.data.data
  } catch (e) {
    console.error(e)
  }
}

const fetchCurrentUser = async () => {
  try {
    const res = await api.get('/auth/me')
    if (res.data.success) {
      currentUserId.value = res.data.data.user.id
      isSuperAdmin.value = res.data.data.user.role === 'super_admin'
    }
  } catch (e) {
    console.error(e)
  }
}

const closeAddAdminModal = () => {
  showAddAdminModal.value = false
  newAdmin.value = { name: '', email: '', role: 'admin' }
}

const createAdmin = async () => {
  if (!newAdmin.value.name || !newAdmin.value.email) { alert('Please fill in all required fields'); return }
  try {
    const res = await api.post('/admin/users', newAdmin.value)
    if (res.data.success) { alert('Admin created and invite sent!'); closeAddAdminModal(); fetchAdminUsers() }
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to create admin')
  }
}

const suspendAdmin = async (id) => {
  if (!confirm('Suspend this admin? They will be signed out immediately.')) return
  try {
    const res = await api.post(`/admin/users/${id}/suspend`)
    if (res.data.success) { fetchAdminUsers() }
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to suspend admin')
  }
}

const activateAdmin = async (id) => {
  try {
    const res = await api.post(`/admin/users/${id}/activate`)
    if (res.data.success) { fetchAdminUsers() }
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to activate admin')
  }
}

const deleteAdmin = async (id) => {
  if (!confirm('Delete this admin permanently? This cannot be undone.')) return
  try {
    const res = await api.delete(`/admin/users/${id}`)
    if (res.data.success) { fetchAdminUsers() }
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to delete admin')
  }
}

const resendCredentials = async (id) => {
  if (!confirm('Generate a new temporary password and email it to the admin?')) return
  try {
    const res = await api.post(`/admin/users/${id}/resend-credentials`)
    if (res.data.success) { alert(res.data.message); fetchAdminUsers() }
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to resend credentials')
  }
}

const elevateToSuperAdmin = async (admin) => {
  if (!confirm(`Elevate ${admin.name} to Super Admin? They will gain full unrestricted access.`)) return
  try {
    const res = await api.post(`/admin/users/${admin.id}/activate`, { role: 'super_admin' })
    // role update via a dedicated call — re-use createAdmin endpoint payload or add dedicated endpoint
    // For now call update via permissions endpoint stub; a proper role-change endpoint should exist
    alert('Role elevation requires a dedicated backend endpoint. Contact your developer to wire this up.')
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to elevate admin')
  }
}

const openPermissionsModal = (admin) => {
  permissionsModal.value = {
    open: true,
    admin,
    selected: Array.isArray(admin.permissions) ? [...admin.permissions] : [],
  }
}

const closePermissionsModal = () => {
  permissionsModal.value = { open: false, admin: null, selected: [] }
}

const savePermissions = async () => {
  try {
    const res = await api.put(`/admin/users/${permissionsModal.value.admin.id}/permissions`, {
      permissions: permissionsModal.value.selected,
    })
    if (res.data.success) {
      // Update local data immediately
      const idx = adminUsers.value.findIndex(u => u.id === permissionsModal.value.admin.id)
      if (idx !== -1) adminUsers.value[idx].permissions = res.data.data.permissions
      closePermissionsModal()
    }
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to save permissions')
  }
}

onMounted(() => {
  fetchCurrentUser()
  fetchAdminUsers()
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
</style>
