<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
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
        <div class="flex flex-col sm:flex-row items-center gap-6">
          <!-- Profile Picture -->
          <div class="relative">
            <img v-if="userProfilePictureUrl" :src="userProfilePictureUrl" :alt="userName" class="w-24 h-24 rounded-full object-cover border-2" style="border-color: #2F5597;">
            <div v-else class="w-24 h-24 rounded-full flex items-center justify-center text-white font-bold text-xl" style="background-color: #2F5597;">{{ initials }}</div>
            <button @click="triggerProfilePictureUpload" class="absolute bottom-0 right-0 bg-white rounded-full p-2 shadow-lg hover:shadow-xl transition border border-gray-300">
              <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M9 4.5a1.5 1.5 0 00-1.342.829L6.882 6.9A1.5 1.5 0 015.54 7.7H4.5A2.5 2.5 0 002 10.2v7.3A2.5 2.5 0 004.5 20h15a2.5 2.5 0 002.5-2.5v-7.3a2.5 2.5 0 00-2.5-2.5h-1.04a1.5 1.5 0 01-1.343-.8l-.775-1.571A1.5 1.5 0 0015 4.5H9zm3 12.25a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"></path>
              </svg>
            </button>
            <input ref="profilePictureInput" type="file" accept="image/*" class="hidden" @change="handleProfilePictureUpload">
          </div>
          <!-- Profile Info -->
          <div class="flex-1 text-center sm:text-left">
            <h2 class="text-2xl font-bold text-gray-900">{{ userName }}</h2>
            <p class="text-gray-600">{{ userEmail }}</p>
            <p class="text-sm text-gray-500 mb-3">Company: {{ companyName }}</p>
            <!-- Incomplete Fields Indicator -->
            <div v-if="incompleteFields.length > 0" class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
              <p class="text-sm font-medium text-amber-900">Complete your profile:</p>
              <ul class="text-sm text-amber-800 mt-1 list-disc list-inside">
                <li v-if="incompleteFields.includes('phone')">Add phone number</li>
                <li v-if="incompleteFields.includes('shipping_address')">Add shipping address</li>
                <li v-if="incompleteFields.includes('profile_picture')">Add profile picture</li>
              </ul>
            </div>
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
            <button @click="handleManageNotifications" class="w-full px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg transition hover:bg-gray-100">
              Manage Notifications
            </button>
            <button @click="showSignOutModal = true" class="w-full px-4 py-3 border border-red-300 text-red-600 font-semibold rounded-lg transition hover:bg-red-50">
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
            <div class="w-8 h-8 border-4 rounded-full" style="border-color: #2F5597; border-block-start-color: transparent; animation: spin 1s linear infinite;"></div>
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
    <div v-if="showEditProfileModal" class="fixed inset-0 bg-gray-400/20 backdrop-blur-sm flex items-center justify-center z-[9999] p-4" @click.self="showEditProfileModal = false">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[92vh] overflow-y-auto">
        <div class="p-8">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Edit Profile</h3>
            <button @click="showEditProfileModal = false" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="submitEditProfile" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input v-model="editForm.name" type="text" required placeholder="Enter your full name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input v-model="editForm.email" type="email" required placeholder="name@company.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input v-model="editForm.phone" type="tel" placeholder="e.g. +254 700 000 000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
            </div>
            <div class="md:col-span-3">
              <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
              <div class="flex items-center gap-4">
                <img v-if="profilePicturePreview" :src="profilePicturePreview" alt="Preview" class="w-16 h-16 rounded-full object-cover border-2 border-gray-300">
                <div v-else-if="userProfilePictureUrl" class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold" style="background-color: #2F5597;">
                  <img :src="userProfilePictureUrl" alt="Profile" class="w-16 h-16 rounded-full object-cover">
                </div>
                <div v-else class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold" style="background-color: #e5e7eb;">
                  <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 4.5a1.5 1.5 0 00-1.342.829L6.882 6.9A1.5 1.5 0 015.54 7.7H4.5A2.5 2.5 0 002 10.2v7.3A2.5 2.5 0 004.5 20h15a2.5 2.5 0 002.5-2.5v-7.3a2.5 2.5 0 00-2.5-2.5h-1.04a1.5 1.5 0 01-1.343-.8l-.775-1.571A1.5 1.5 0 0015 4.5H9zm3 12.25a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"></path>
                  </svg>
                </div>
                <input ref="editProfilePictureInput" type="file" accept="image/*" class="hidden" @change="handleEditProfilePictureChange">
                <button type="button" @click="$refs.editProfilePictureInput.click()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium flex items-center gap-2">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 4.5a1.5 1.5 0 00-1.342.829L6.882 6.9A1.5 1.5 0 015.54 7.7H4.5A2.5 2.5 0 002 10.2v7.3A2.5 2.5 0 004.5 20h15a2.5 2.5 0 002.5-2.5v-7.3a2.5 2.5 0 00-2.5-2.5h-1.04a1.5 1.5 0 01-1.343-.8l-.775-1.571A1.5 1.5 0 0015 4.5H9zm3 12.25a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"></path>
                  </svg>
                  Upload Picture
                </button>
              </div>
            </div>
            <div class="pt-2 border-t border-gray-200">
              <h4 class="text-sm font-semibold text-gray-900 mb-2">Shipping Details</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                  <input v-model="editForm.shipping.label" type="text" placeholder="Default Shipping" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Street 1</label>
                  <input v-model="editForm.shipping.street_1" type="text" placeholder="Building, street, area" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Street 2</label>
                  <input v-model="editForm.shipping.street_2" type="text" placeholder="Apartment, suite, landmark" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                  <input v-model="editForm.shipping.contact_name" type="text" placeholder="Who receives deliveries?" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                  <input v-model="editForm.shipping.contact_phone" type="text" placeholder="Delivery contact number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                  <input v-model="editForm.shipping.city" type="text" placeholder="City" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                  <input v-model="editForm.shipping.state" type="text" placeholder="State or county" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                  <input v-model="editForm.shipping.postal_code" type="text" placeholder="Postal / ZIP code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                  <input v-model="editForm.shipping.country" type="text" maxlength="2" placeholder="US" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
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
    <div v-if="showChangePasswordModal" class="fixed inset-0 bg-gray-400/20 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
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
    <div v-if="showNotificationsModal" class="fixed inset-0 bg-gray-400/20 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
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

    <!-- Sign Out Confirmation Modal -->
    <div v-if="showSignOutModal" class="fixed inset-0 bg-gray-400/20 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6">
        <div class="flex flex-col items-center text-center gap-3">
          <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900">Sign Out</h3>
          <p class="text-sm text-gray-500">Are you sure you want to sign out of your account?</p>
        </div>
        <div class="flex gap-3 mt-6">
          <button @click="handleSignOut" class="flex-1 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
            Yes, Sign Out
          </button>
          <button @click="showSignOutModal = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
            Cancel
          </button>
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
const showSignOutModal = ref(false)

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

// Profile picture handling
const profilePictureInput = ref(null)
const editProfilePictureInput = ref(null)
const profilePicturePreview = ref(null)
const profilePictureFile = ref(null)
const incompleteFields = ref([])
const showCompleteProfileModal = ref(false)

const getNotificationStorageKey = () => {
  const userId = authStore.user?.id
  return userId ? `notification_preferences_${userId}` : 'notification_preferences'
}

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

const userProfilePictureUrl = computed(() => {
  if (profilePicturePreview.value) {
    return profilePicturePreview.value
  }
  if (authStore.user?.profile_picture_url) {
    return authStore.user.profile_picture_url
  }
  return null
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

const triggerProfilePictureUpload = () => {
  profilePictureInput.value?.click()
}

const handleProfilePictureUpload = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  profilePictureFile.value = file
  
  const reader = new FileReader()
  reader.onload = (e) => {
    profilePicturePreview.value = e.target?.result
  }
  reader.readAsDataURL(file)

  // Auto-submit the profile picture upload
  submitProfilePictureUpload()
}

const handleEditProfilePictureChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  profilePictureFile.value = file
  
  const reader = new FileReader()
  reader.onload = (e) => {
    profilePicturePreview.value = e.target?.result
  }
  reader.readAsDataURL(file)
}

const submitProfilePictureUpload = async () => {
  if (!profilePictureFile.value) {
    console.warn('No profile picture file selected')
    return
  }

  try {
    const formData = new FormData()
    formData.append('profile_picture', profilePictureFile.value)

    const token = localStorage.getItem('auth_token')
    console.log('Uploading profile picture...')
    const response = await fetch(`${API_BASE_URL}/auth/update-profile`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: formData
    })

    const data = await response.json()
    console.log('Upload response:', data)

    if (!response.ok) {
      throw new Error(data.message || `Failed to upload profile picture (${response.status})`)
    }

    // Update auth store with fresh user data
    if (data.user) {
      authStore.user = {
        ...authStore.user,
        ...data.user
      }
      localStorage.setItem('armely_user', JSON.stringify(authStore.user))
      console.log('User updated with profile picture:', authStore.user.profile_picture_url)
    }

    toastStore.addToast('Profile picture updated successfully!', 'success')
    profilePictureFile.value = null
    profilePicturePreview.value = null
    if (profilePictureInput.value) {
      profilePictureInput.value.value = ''
    }
  } catch (error) {
    console.error('Error uploading profile picture:', error)
    toastStore.addToast(error.message || 'Failed to upload profile picture', 'error')
  }
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
    
    // Use FormData to support file uploads
    const formData = new FormData()
    formData.append('name', editForm.value.name)
    formData.append('email', editForm.value.email)
    formData.append('phone', editForm.value.phone)
    
    // Add shipping address as JSON string
    const shippingAddress = {
      label: editForm.value.shipping.label,
      contact_name: editForm.value.shipping.contact_name,
      contact_phone: editForm.value.shipping.contact_phone,
      street_1: editForm.value.shipping.street_1,
      street_2: editForm.value.shipping.street_2,
      city: editForm.value.shipping.city,
      state: editForm.value.shipping.state,
      postal_code: editForm.value.shipping.postal_code,
      country: (editForm.value.shipping.country || 'US').toUpperCase()
    }
    
    // Append shipping address
    Object.entries(shippingAddress).forEach(([key, value]) => {
      formData.append(`shipping_address[${key}]`, value || '')
    })
    
    // Add profile picture if selected
    if (profilePictureFile.value) {
      formData.append('profile_picture', profilePictureFile.value)
    }
    
    const response = await fetch(`${API_BASE_URL}/auth/update-profile`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: formData
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Failed to update profile')
    }

    // Update auth store
    if (data.user) {
      authStore.user = data.user
      localStorage.setItem('armely_user', JSON.stringify(data.user))
      // Update incomplete fields
      incompleteFields.value = data.incomplete_fields || []
    }

    toastStore.addToast('Profile updated successfully!', 'success')
    showEditProfileModal.value = false
    profilePictureFile.value = null
    profilePicturePreview.value = null
    if (editProfilePictureInput.value) {
      editProfilePictureInput.value.value = ''
    }
    
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
  const saved = localStorage.getItem(getNotificationStorageKey())
  if (saved) {
    try {
      const parsed = JSON.parse(saved)
      notificationSettings.value = {
        email: parsed.email !== false,
        quotes: parsed.quotes !== false,
        orders: parsed.orders !== false,
        invoices: parsed.invoices !== false,
      }
    } catch (error) {
      console.warn('Invalid notification preferences in localStorage, resetting to defaults.', error)
      notificationSettings.value = {
        email: true,
        quotes: true,
        orders: true,
        invoices: true,
      }
    }
  }

  showNotificationsModal.value = true
}

const saveNotificationSettings = () => {
  localStorage.setItem(getNotificationStorageKey(), JSON.stringify(notificationSettings.value))
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
  
  // Load incomplete fields from auth store
  if (authStore.user?.incomplete_fields) {
    incompleteFields.value = authStore.user.incomplete_fields
  }
  
  fetchActivities()
  authStore.refreshUser()
})
</script>
