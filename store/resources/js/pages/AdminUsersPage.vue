<template>
  <AdminLayout>
    <template #title>Admin Users</template>

    <div class="admin-fit-page">

      <!-- Filters -->
      <div class="rounded-xl shadow-lg bg-white p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Search</label>
            <input v-model="searchQuery" type="text" placeholder="Name or email…"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-sm text-gray-900 placeholder-slate-400 transition" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Role</label>
            <select v-model="roleFilter" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-sm text-gray-900 bg-white transition">
              <option value="">All Roles</option>
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Status</label>
            <select v-model="statusFilter" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] text-sm text-gray-900 bg-white transition">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
          <div class="flex items-end gap-2">
            <button @click="fetchAdminUsers" class="flex-1 px-4 py-2.5 bg-[#2F5597] hover:bg-[#1e3a6b] text-white text-sm font-medium rounded-lg transition">
              Search
            </button>
            <button v-if="isSuperAdmin" @click="openAddModal" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
              + Add Admin
            </button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="rounded-xl shadow-lg bg-white overflow-hidden flex-1 flex flex-col min-h-0">

        <!-- Table header with bulk actions -->
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 flex-shrink-0">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Admin Users
              <span class="ml-2 text-sm font-normal text-gray-400">({{ filteredAdmins.length }})</span>
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Manage administrators, roles, and permissions</p>
          </div>

          <!-- Bulk actions bar -->
          <transition enter-active-class="transition duration-150" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-100" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="selectedIds.length > 0" class="flex items-center gap-2 flex-wrap">
              <span class="text-xs font-semibold text-[#2F5597] bg-[#2F5597]/10 px-3 py-1.5 rounded-full">
                {{ selectedIds.length }} selected
              </span>
              <button @click="bulkActivate" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                Activate
              </button>
              <button @click="bulkSuspend" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
                Suspend
              </button>
              <button @click="bulkDelete" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-rose-300 text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
                Delete
              </button>
              <button @click="clearSelection" class="px-3 py-1.5 text-xs text-gray-400 hover:text-gray-600 transition">
                Clear
              </button>
            </div>
          </transition>
        </div>

        <div class="admin-users-table-scroll overflow-x-auto overflow-y-auto flex-1">
          <table class="w-full min-w-[1100px] text-sm">
            <thead class="border-b border-gray-100 bg-gray-50 sticky top-0 z-10">
              <tr>
                <!-- Select all -->
                <th class="px-4 py-3 w-10">
                  <input type="checkbox" :checked="allSelected" :indeterminate="someSelected" @change="toggleSelectAll"
                    class="w-4 h-4 accent-[#2F5597] rounded cursor-pointer" />
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Admin</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="filteredAdmins.length === 0">
                <td colspan="5" class="px-6 py-16 text-center text-gray-400 text-sm">No admin users found</td>
              </tr>
              <tr v-for="admin in filteredAdmins" :key="admin.id"
                :class="['transition group', selectedIds.includes(admin.id) ? 'bg-[#2F5597]/5' : 'hover:bg-gray-50']">

                <!-- Checkbox -->
                <td class="px-4 py-4">
                  <input type="checkbox" :value="admin.id" v-model="selectedIds"
                    :disabled="admin.id === currentUserId"
                    class="w-4 h-4 accent-[#2F5597] rounded cursor-pointer disabled:opacity-30" />
                </td>

                <!-- Admin info -->
                <td class="px-4 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                      :style="admin.id === currentUserId ? 'background: linear-gradient(135deg, #059669, #047857)' : 'background: linear-gradient(135deg, #2F5597, #1e3a6b)'">
                      {{ getInitials(admin.name) }}
                    </div>
                    <div>
                      <div class="font-semibold text-gray-900 text-sm flex items-center gap-1.5">
                        {{ admin.name }}
                        <span v-if="admin.id === currentUserId" class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full font-semibold">You</span>
                      </div>
                      <div class="text-xs text-gray-400">{{ admin.email }}</div>
                    </div>
                  </div>
                </td>

                <!-- Role -->
                <td class="px-4 py-4">
                  <span :class="[
                    'px-2.5 py-1 rounded-full text-xs font-semibold',
                    admin.role === 'super_admin' ? 'bg-violet-100 text-violet-700' : 'bg-[#2F5597]/10 text-[#2F5597]'
                  ]">
                    {{ admin.role === 'super_admin' ? 'Super Admin' : 'Admin' }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-4 py-4">
                  <span :class="[
                    'px-2.5 py-1 rounded-full text-xs font-semibold',
                    admin.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-600'
                  ]">
                    {{ admin.status === 'active' ? 'Active' : 'Suspended' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-4 py-4 text-right">
                  <div class="flex justify-end items-center gap-1.5">
                    <button v-if="admin.force_password_change" @click="resendCredentials(admin.id)"
                      class="px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                      Resend
                    </button>
                    <button v-if="isSuperAdmin && admin.id !== currentUserId" @click="openEditModal(admin)"
                      class="px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-[#2F5597]/30 text-[#2F5597] bg-[#2F5597]/5 hover:bg-[#2F5597]/15 transition">
                      Edit
                    </button>
                    <button v-if="admin.status === 'active' && admin.id !== currentUserId" @click="suspendAdmin(admin.id)"
                      class="px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
                      Suspend
                    </button>
                    <button v-else-if="admin.status !== 'active' && admin.id !== currentUserId" @click="activateAdmin(admin.id)"
                      class="px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                      Activate
                    </button>
                    <button v-if="admin.id !== currentUserId" @click="deleteAdmin(admin.id)"
                      class="px-2.5 py-1.5 text-xs font-semibold rounded-lg border border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
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

    <!-- ── Add Admin Modal ─────────────────────────────── -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeAddModal">
      <div class="rounded-xl bg-white shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-5 border-b" style="background: linear-gradient(135deg,#2F5597,#1e3a6b)">
          <h3 class="text-lg font-semibold text-white">Add New Admin</h3>
          <p class="text-xs text-white/60 mt-0.5">An invite email with a temporary password will be sent</p>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Full Name</label>
            <input v-model="addForm.name" type="text" placeholder="Jane Smith"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2F5597] bg-gray-50 text-gray-900 placeholder-slate-400" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Email Address</label>
            <input v-model="addForm.email" type="email" placeholder="jane@armely.com"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2F5597] bg-gray-50 text-gray-900 placeholder-slate-400" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Role</label>
            <select v-model="addForm.role"
              class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#2F5597] bg-white text-gray-900">
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
          <button @click="closeAddModal" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">Cancel</button>
          <button @click="createAdmin" class="px-4 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-semibold rounded-lg text-sm transition">Create Admin</button>
        </div>
      </div>
    </div>

    <!-- ── Edit Admin Modal (role + permissions) ──────── -->
    <div v-if="editModal.open" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeEditModal">
      <div class="rounded-xl bg-white shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="px-6 py-5 border-b flex-shrink-0" style="background: linear-gradient(135deg,#2F5597,#1e3a6b)">
          <h3 class="text-lg font-semibold text-white">Edit Admin</h3>
          <p class="text-xs text-white/60 mt-0.5">{{ editModal.admin?.name }} &mdash; {{ editModal.admin?.email }}</p>
        </div>

        <div class="overflow-y-auto flex-1 p-6 space-y-6">
          <!-- Role -->
          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Role</label>
            <div class="grid grid-cols-2 gap-3">
              <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition"
                :class="editModal.role === 'admin' ? 'border-[#2F5597] bg-[#2F5597]/5' : 'border-gray-200 hover:border-gray-300'">
                <input type="radio" value="admin" v-model="editModal.role" class="mt-0.5 accent-[#2F5597]" />
                <div>
                  <div class="text-sm font-semibold text-gray-800">Admin</div>
                  <div class="text-xs text-gray-500 mt-0.5">Access limited to assigned permissions</div>
                </div>
              </label>
              <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition"
                :class="editModal.role === 'super_admin' ? 'border-violet-500 bg-violet-50' : 'border-gray-200 hover:border-gray-300'">
                <input type="radio" value="super_admin" v-model="editModal.role" class="mt-0.5 accent-violet-600" />
                <div>
                  <div class="text-sm font-semibold text-gray-800">Super Admin</div>
                  <div class="text-xs text-gray-500 mt-0.5">Full unrestricted access to all sections</div>
                </div>
              </label>
            </div>
          </div>

          <!-- Permissions (only relevant for Admin role) -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Permissions</label>
              <div class="flex gap-2 text-xs">
                <button @click="editModal.permissions = ALL_PERMISSIONS.map(p => p.key)" class="text-[#2F5597] hover:underline font-medium">Select all</button>
                <span class="text-gray-300">|</span>
                <button @click="editModal.permissions = []" class="text-gray-400 hover:underline">Clear</button>
              </div>
            </div>

            <div v-if="editModal.role === 'super_admin'" class="rounded-xl border border-violet-100 bg-violet-50 p-4 text-center">
              <p class="text-sm text-violet-600 font-medium">Super admins have full access — permissions do not apply</p>
            </div>

            <div v-else class="space-y-2">
              <label v-for="perm in ALL_PERMISSIONS" :key="perm.key"
                class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition group"
                :class="editModal.permissions.includes(perm.key)
                  ? 'border-[#2F5597]/40 bg-[#2F5597]/5'
                  : 'border-gray-100 hover:border-gray-200 hover:bg-gray-50'">
                <input type="checkbox" :value="perm.key" v-model="editModal.permissions"
                  class="mt-0.5 w-4 h-4 accent-[#2F5597] flex-shrink-0" />
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-800">{{ perm.label }}</span>
                    <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-mono">{{ perm.key }}</span>
                  </div>
                  <div class="text-xs text-gray-500 mt-0.5">{{ perm.description }}</div>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center flex-shrink-0">
          <span class="text-xs text-gray-400">
            {{ editModal.role === 'super_admin' ? 'All permissions granted' : `${editModal.permissions.length} of ${ALL_PERMISSIONS.length} permissions` }}
          </span>
          <div class="flex gap-3">
            <button @click="closeEditModal" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-sm transition">Cancel</button>
            <button @click="saveEdit" class="px-4 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-semibold rounded-lg text-sm transition">Save Changes</button>
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
  { key: 'manage_orders',    label: 'Orders',    description: 'View orders, cancel orders, and access order tracking' },
  { key: 'manage_customers', label: 'Customers', description: 'View, approve, suspend, and invite customers' },
  { key: 'manage_invoices',  label: 'Invoices',  description: 'View invoices and record payments' },
  { key: 'manage_reports',   label: 'Reports',   description: 'Access revenue reports and analytics' },
  { key: 'manage_settings',  label: 'Settings',  description: 'Change system configuration and API settings' },
  { key: 'manage_admins',    label: 'Admins',    description: 'Add, suspend, delete, and edit other admin accounts' },
]

const permissionLabel = (key) => ALL_PERMISSIONS.find(p => p.key === key)?.label ?? key

// ── State ────────────────────────────────────────────
const searchQuery  = ref('')
const roleFilter   = ref('')
const statusFilter = ref('')
const adminUsers   = ref([])
const selectedIds  = ref([])
const currentUserId = ref(null)
const isSuperAdmin  = ref(false)

const showAddModal = ref(false)
const addForm = ref({ name: '', email: '', role: 'admin' })

const editModal = ref({ open: false, admin: null, role: 'admin', permissions: [] })

// ── Computed ─────────────────────────────────────────
const filteredAdmins = computed(() => {
  let list = Array.isArray(adminUsers.value) ? adminUsers.value : []
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(u => u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q))
  }
  if (roleFilter.value)   list = list.filter(u => u.role === roleFilter.value)
  if (statusFilter.value) list = list.filter(u => u.status === statusFilter.value)
  return list
})

const selectableIds = computed(() =>
  filteredAdmins.value.filter(a => a.id !== currentUserId.value).map(a => a.id)
)

const allSelected  = computed(() => selectableIds.value.length > 0 && selectableIds.value.every(id => selectedIds.value.includes(id)))
const someSelected = computed(() => !allSelected.value && selectedIds.value.length > 0)

// ── Helpers ──────────────────────────────────────────
const getInitials   = (name) => name ? name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) : 'A'
const isTempExpired = (dt)   => dt && new Date(dt) < new Date()

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedIds.value = []
  } else {
    selectedIds.value = [...selectableIds.value]
  }
}

const clearSelection = () => { selectedIds.value = [] }

// ── API calls ─────────────────────────────────────────
const fetchAdminUsers = async () => {
  try {
    const res = await api.get('/admin/users')
    if (res.data.success) {
      adminUsers.value = res.data.data
      selectedIds.value = selectedIds.value.filter(id => adminUsers.value.some(a => a.id === id))
    }
  } catch (e) { console.error(e) }
}

const fetchCurrentUser = async () => {
  try {
    const res = await api.get('/auth/me')
    if (res.data.success) {
      currentUserId.value = res.data.data.user.id
      isSuperAdmin.value  = res.data.data.user.role === 'super_admin'
    }
  } catch (e) { console.error(e) }
}

// ── Add admin ─────────────────────────────────────────
const openAddModal  = () => { showAddModal.value = true }
const closeAddModal = () => { showAddModal.value = false; addForm.value = { name: '', email: '', role: 'admin' } }

const createAdmin = async () => {
  if (!addForm.value.name || !addForm.value.email) { alert('Please fill in all required fields'); return }
  try {
    const res = await api.post('/admin/users', addForm.value)
    if (res.data.success) { alert('Admin created — invite email sent!'); closeAddModal(); fetchAdminUsers() }
  } catch (e) { alert(e.response?.data?.message || 'Failed to create admin') }
}

// ── Edit admin (role + permissions) ──────────────────
const openEditModal = (admin) => {
  editModal.value = {
    open: true,
    admin,
    role: admin.role,
    permissions: Array.isArray(admin.permissions) ? [...admin.permissions] : [],
  }
}

const closeEditModal = () => { editModal.value = { open: false, admin: null, role: 'admin', permissions: [] } }

const saveEdit = async () => {
  const { admin, role, permissions } = editModal.value
  try {
    // Update role if changed
    if (role !== admin.role) {
      const res = await api.put(`/admin/users/${admin.id}/role`, { role })
      if (!res.data.success) { alert(res.data.message || 'Failed to update role'); return }
    }
    // Update permissions
    const res = await api.put(`/admin/users/${admin.id}/permissions`, {
      permissions: role === 'super_admin' ? [] : permissions,
    })
    if (res.data.success) {
      const idx = adminUsers.value.findIndex(u => u.id === admin.id)
      if (idx !== -1) {
        adminUsers.value[idx].role        = role
        adminUsers.value[idx].permissions = res.data.data.permissions
      }
      closeEditModal()
    }
  } catch (e) { alert(e.response?.data?.message || 'Failed to save changes') }
}

// ── Individual actions ────────────────────────────────
const suspendAdmin = async (id) => {
  if (!confirm('Suspend this admin? They will be signed out immediately.')) return
  try { const r = await api.post(`/admin/users/${id}/suspend`); if (r.data.success) fetchAdminUsers() }
  catch (e) { alert(e.response?.data?.message || 'Failed') }
}

const activateAdmin = async (id) => {
  try { const r = await api.post(`/admin/users/${id}/activate`); if (r.data.success) fetchAdminUsers() }
  catch (e) { alert(e.response?.data?.message || 'Failed') }
}

const deleteAdmin = async (id) => {
  if (!confirm('Delete this admin permanently? This cannot be undone.')) return
  try { const r = await api.delete(`/admin/users/${id}`); if (r.data.success) fetchAdminUsers() }
  catch (e) { alert(e.response?.data?.message || 'Failed') }
}

const resendCredentials = async (id) => {
  if (!confirm('Generate a new temporary password and email it to the admin?')) return
  try {
    const r = await api.post(`/admin/users/${id}/resend-credentials`)
    if (r.data.success) { alert(r.data.message); fetchAdminUsers() }
  } catch (e) { alert(e.response?.data?.message || 'Failed') }
}

// ── Bulk actions ──────────────────────────────────────
const bulkSuspend = async () => {
  if (!selectedIds.value.length) return
  if (!confirm(`Suspend ${selectedIds.value.length} admin(s)?`)) return
  try {
    const res = await api.post('/admin/users/bulk-suspend', { ids: selectedIds.value })
    if (res.data.success) { clearSelection(); fetchAdminUsers(); alert(res.data.message) }
  } catch (e) { alert(e.response?.data?.message || 'Failed') }
}

const bulkActivate = async () => {
  if (!selectedIds.value.length) return
  if (!confirm(`Activate ${selectedIds.value.length} admin(s)?`)) return
  try {
    const res = await api.post('/admin/users/bulk-activate', { ids: selectedIds.value })
    if (res.data.success) { clearSelection(); fetchAdminUsers(); alert(res.data.message) }
  } catch (e) { alert(e.response?.data?.message || 'Failed') }
}

const bulkDelete = async () => {
  if (!selectedIds.value.length) return
  if (!confirm(`Permanently delete ${selectedIds.value.length} admin(s)? This cannot be undone.`)) return
  try {
    const res = await api.post('/admin/users/bulk-delete', { ids: selectedIds.value })
    if (res.data.success) { clearSelection(); fetchAdminUsers(); alert(res.data.message) }
  } catch (e) { alert(e.response?.data?.message || 'Failed') }
}

onMounted(() => { fetchCurrentUser(); fetchAdminUsers() })
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
