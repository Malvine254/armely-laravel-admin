<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Title -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Account</h1>
        <p class="text-gray-600 text-lg">Manage your profile and preferences</p>
      </div>

      <div
        v-if="isRestricted"
        class="mb-6 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3"
      >
        <p class="text-sm font-semibold text-amber-900">Account Restricted</p>
        <p class="text-sm text-amber-800">
          Your account is suspended or pending approval. You can view your account, but changes and transactions are disabled.
        </p>
      </div>

      <!-- Profile Overview -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl" style="background-color: #2F5597;">{{ initials }}</div>
          <div>
            <h2 class="text-xl font-bold text-gray-900">{{ userName }}</h2>
            <p class="text-gray-600">{{ userEmail }}</p>
            <p class="text-sm text-gray-500">Company: {{ companyName }}</p>
          </div>
        </div>
      </div>

      <!-- Account Sections -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Profile Information</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Name:</span>
              <span class="font-semibold text-gray-900">{{ userName }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Email:</span>
              <span class="font-semibold text-gray-900">{{ userEmail }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Phone:</span>
              <span class="font-semibold text-gray-900">{{ userPhone }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Company:</span>
              <span class="font-semibold text-gray-900">{{ companyName }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Role:</span>
              <span class="font-semibold text-gray-900">{{ userRoleLabel }}</span>
            </div>
            <div class="pt-2 border-t border-gray-100">
              <span class="text-gray-600 block mb-1">Default Shipping:</span>
              <span class="font-semibold text-gray-900 text-sm break-words">{{ shippingSummary }}</span>
            </div>
          </div>
        </div>

        <!-- Account Actions -->
        <div class="bg-white rounded-lg shadow-lg p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Account Actions</h3>
          <div class="space-y-3">
            <button @click="handleEditProfile" :disabled="isRestricted" class="w-full px-4 py-3 text-white font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
              Edit Profile
            </button>
            <button @click="handleChangePassword" :disabled="isRestricted" class="w-full px-4 py-3 border-2 font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed" style="border-color: #2F5597; color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#cce4f4'" @mouseleave="$event.target.style.backgroundColor='transparent'">
              Change Password
            </button>
            <button @click="handleManageNotifications" :disabled="isRestricted" class="w-full px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg transition hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
              Manage Notifications
            </button>
            <button @click="handleSignOut" class="w-full px-4 py-3 border border-red-300 text-red-600 font-semibold rounded-lg transition hover:bg-red-50">
              Sign Out
            </button>
          </div>
        </div>
      </div>

      <!-- Activity Section -->
      <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
        <div v-if="loading" class="text-center py-8">
          <div class="inline-block">
            <div class="w-8 h-8 border-4 rounded-full" style="border-color: #2F5597; border-top-color: transparent; animation: spin 1s linear infinite;"></div>
          </div>
        </div>
        <div v-else-if="activities.length > 0" class="space-y-3">
          <div v-for="activity in activities" :key="activity.id" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background-color: #2F5597;">{{ getActivityIcon(activity.type) }}</div>
            <div>
              <p class="text-sm font-semibold text-gray-900">{{ activity.description }}</p>
              <p class="text-xs text-gray-600">{{ activity.time_ago }}</p>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          <p>No recent activity yet. Start by creating quotes or placing orders!</p>
        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <div v-if="showEditProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Edit Profile</h3>
            <button @click="showEditProfileModal = false" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="submitEditProfile" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input v-model="editForm.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="editForm.email" type="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input v-model="editForm.phone" type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="pt-2 border-t border-gray-200">
              <h4 class="text-sm font-semibold text-gray-900 mb-2">Shipping Details</h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                  <input v-model="editForm.shipping.label" type="text" placeholder="Main Office" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                    <input v-model="editForm.shipping.contact_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                    <input v-model="editForm.shipping.contact_phone" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Street 1</label>
                  <input v-model="editForm.shipping.street_1" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Street 2</label>
                  <input v-model="editForm.shipping.street_2" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input v-model="editForm.shipping.city" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input v-model="editForm.shipping.state" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input v-model="editForm.shipping.postal_code" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input v-model="editForm.shipping.country" type="text" maxlength="2" placeholder="US" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                  </div>
                </div>
              </div>
            </div>
            <div class="flex gap-3 pt-4">
              <button type="submit" :disabled="editFormLoading || isRestricted" class="flex-1 px-4 py-2 text-white font-semibold rounded-lg disabled:opacity-50" style="background-color: #2F5597;">
                {{ editFormLoading ? 'Saving...' : 'Save Changes' }}
              </button>
              <button type="button" @click="showEditProfileModal = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Change Password Modal -->
    <div v-if="showChangePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Change Password</h3>
            <button @click="showChangePasswordModal = false" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="submitChangePassword" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
              <input v-model="passwordForm.current" type="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
              <input v-model="passwordForm.new" type="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
              <input v-model="passwordForm.confirm" type="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-3 pt-4">
              <button type="submit" :disabled="passwordFormLoading || isRestricted" class="flex-1 px-4 py-2 text-white font-semibold rounded-lg disabled:opacity-50" style="background-color: #2F5597;">
                {{ passwordFormLoading ? 'Updating...' : 'Update Password' }}
              </button>
              <button type="button" @click="showChangePasswordModal = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Manage Notifications Modal -->
    <div v-if="showNotificationsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Notification Preferences</h3>
            <button @click="showNotificationsModal = false" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Email Notifications</p>
                <p class="text-sm text-gray-600">Receive email updates about your orders</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="notificationSettings.email" type="checkbox" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
              </label>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Quote Updates</p>
                <p class="text-sm text-gray-600">Get notified when quote status changes</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="notificationSettings.quotes" type="checkbox" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
              </label>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Order Updates</p>
                <p class="text-sm text-gray-600">Track shipment and delivery status</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="notificationSettings.orders" type="checkbox" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
              </label>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900">Invoice Reminders</p>
                <p class="text-sm text-gray-600">Reminders for pending invoices</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="notificationSettings.invoices" type="checkbox" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
              </label>
            </div>
          </div>
          <div class="flex gap-3 pt-6">
            <button @click="saveNotificationSettings" class="flex-1 px-4 py-2 text-white font-semibold rounded-lg" style="background-color: #2F5597;">
              Save Preferences
            </button>
            <button @click="showNotificationsModal = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'
import { API_BASE_URL } from '../../services/runtimeConfig'
import Navbar from '../../components/Navbar.vue'

const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()
const activities = ref([])
const loading = ref(false)

// Modal states
const showEditProfileModal = ref(false)
const showChangePasswordModal = ref(false)
const showNotificationsModal = ref(false)

// Form states
const editFormLoading = ref(false)
const passwordFormLoading = ref(false)

const editForm = ref({
  name: '',
  email: '',
  phone: '',
  shipping: {
    label: '',
    contact_name: '',
    contact_phone: '',
    street_1: '',
    street_2: '',
    city: '',
    state: '',
    postal_code: '',
    country: 'US'
  }
})

const passwordForm = ref({
  current: '',
  new: '',
  confirm: ''
})

const notificationSettings = ref({
  email: true,
  quotes: true,
  orders: true,
  invoices: true
})

const userName = computed(() => authStore.user?.name || '')
const userEmail = computed(() => authStore.user?.email || '')
const userPhone = computed(() => authStore.user?.phone || 'Not set')
const isRestricted = computed(() => authStore.isRestricted)
const userRoleLabel = computed(() => {
  const role = authStore.user?.role
  const roleMap = {
    admin: 'Administrator',
    manager: 'Procurement Manager',
    owner: 'Company Owner',
    buyer: 'Buyer'
  }
  return roleMap[role] || 'User'
})
const companyName = computed(() => {
  const company = authStore.user?.company
  if (typeof company === 'object' && company?.name) {
    return company.name
  }
  return typeof company === 'string' ? company : ''
})

const shippingSummary = computed(() => {
  const shipping = authStore.user?.shipping_address
  if (!shipping) return 'Not set'

  const parts = [
    shipping.street_1,
    shipping.street_2,
    [shipping.city, shipping.state].filter(Boolean).join(', '),
    shipping.postal_code,
    shipping.country,
  ].filter(Boolean)

  return parts.length > 0 ? parts.join(' | ') : 'Not set'
})

const initials = computed(() => {
  if (!authStore.user?.name) return ''
  const parts = authStore.user.name.split(' ').filter(Boolean)
  return parts.slice(0, 2).map(p => p[0].toUpperCase()).join('')
})

const getActivityIcon = (type) => {
  const icons = {
    'quote': 'Q',
    'order': 'O',
    'favorite': 'F',
    'profile': 'U',
    'invoice': 'I'
  }
  return icons[type] || type.charAt(0).toUpperCase()
}

const fetchActivities = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`${API_BASE_URL}/activities?limit=5`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to fetch activities')
    }

    const data = await response.json()
    activities.value = data.data || []
  } catch (error) {
    console.error('Error fetching activities:', error)
    toastStore.addToast('Failed to load activities', 'error')
  } finally {
    loading.value = false
  }
}

const handleEditProfile = () => {
  if (isRestricted.value) {
    toastStore.addToast('Your account is restricted. Profile updates are disabled.', 'warning')
    return
  }

  const shipping = authStore.user?.shipping_address || {}
  editForm.value.name = authStore.user?.name || ''
  editForm.value.email = authStore.user?.email || ''
  editForm.value.phone = authStore.user?.phone || ''
  editForm.value.shipping = {
    label: shipping.label || 'Default Shipping',
    contact_name: shipping.contact_name || authStore.user?.name || '',
    contact_phone: shipping.contact_phone || authStore.user?.phone || '',
    street_1: shipping.street_1 || '',
    street_2: shipping.street_2 || '',
    city: shipping.city || '',
    state: shipping.state || '',
    postal_code: shipping.postal_code || '',
    country: shipping.country || 'US'
  }
  showEditProfileModal.value = true
}

const submitEditProfile = async () => {
  editFormLoading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`${API_BASE_URL}/auth/update-profile`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: editForm.value.name,
        email: editForm.value.email,
        phone: editForm.value.phone,
        shipping_address: {
          ...editForm.value.shipping,
          country: (editForm.value.shipping.country || 'US').toUpperCase()
        }
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Failed to update profile')
    }

    // Update auth store
    if (data.user) {
      authStore.user = data.user
      localStorage.setItem('armely_user', JSON.stringify(data.user))
    }

    toastStore.addToast('Profile updated successfully!', 'success')
    showEditProfileModal.value = false
    
    // Log activity
    await logActivity('profile', 'updated', 'Updated account profile')
    fetchActivities()
  } catch (error) {
    console.error('Error updating profile:', error)
    toastStore.addToast(error.message || 'Failed to update profile', 'error')
  } finally {
    editFormLoading.value = false
  }
}

const handleChangePassword = () => {
  if (isRestricted.value) {
    toastStore.addToast('Your account is restricted. Password changes are disabled.', 'warning')
    return
  }

  passwordForm.value = { current: '', new: '', confirm: '' }
  showChangePasswordModal.value = true
}

const submitChangePassword = async () => {
  if (passwordForm.value.new !== passwordForm.value.confirm) {
    toastStore.addToast('New passwords do not match!', 'error')
    return
  }

  if (passwordForm.value.new.length < 8) {
    toastStore.addToast('Password must be at least 8 characters!', 'error')
    return
  }

  passwordFormLoading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`${API_BASE_URL}/auth/change-password`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        current_password: passwordForm.value.current,
        new_password: passwordForm.value.new,
        new_password_confirmation: passwordForm.value.confirm
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Failed to change password')
    }

    toastStore.addToast('Password changed successfully!', 'success')
    showChangePasswordModal.value = false
    passwordForm.value = { current: '', new: '', confirm: '' }
  } catch (error) {
    console.error('Error changing password:', error)
    toastStore.addToast(error.message || 'Failed to change password', 'error')
  } finally {
    passwordFormLoading.value = false
  }
}

const handleManageNotifications = () => {
  if (isRestricted.value) {
    toastStore.addToast('Your account is restricted. Notification changes are disabled.', 'warning')
    return
  }

  // Load saved preferences from localStorage
  const saved = localStorage.getItem('notification_preferences')
  if (saved) {
    notificationSettings.value = JSON.parse(saved)
  }
  showNotificationsModal.value = true
}

const saveNotificationSettings = () => {
  localStorage.setItem('notification_preferences', JSON.stringify(notificationSettings.value))
  toastStore.addToast('Notification preferences saved!', 'success')
  showNotificationsModal.value = false
}

const logActivity = async (type, action, description) => {
  try {
    const token = localStorage.getItem('auth_token')
    await fetch(`${API_BASE_URL}/activities/log`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ type, action, description })
    })
  } catch (error) {
    console.error('Failed to log activity:', error)
  }
}

const handleSignOut = () => {
  authStore.logout()
  toastStore.addToast('Signed out successfully', 'info')
  router.push({ name: 'login' })
}

onMounted(() => {
  // Verify user is authenticated before loading
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to access your account', 'error')
    router.push({ name: 'login' })
    return
  }
  fetchActivities()
  authStore.refreshUser()
})
</script>
