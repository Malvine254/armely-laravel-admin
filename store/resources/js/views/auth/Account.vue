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
              <span class="text-gray-600 block mb-1">Shipping Address:</span>
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
            <button @click="handleDeleteAccount" class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg transition hover:bg-red-700">
              Delete Account
            </button>
          </div>
        </div>
      </div>

      <!-- Manual Billing -->
      <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Monthly Billing</h3>
            <p class="text-sm text-gray-600">Invoices will be sent to you for review and payment coordination.</p>
          </div>
          <button
            @click="router.push({ name: 'invoices' })"
            :disabled="isRestricted"
            class="px-4 py-2 bg-[#2F5597] text-white rounded-lg font-semibold hover:bg-[#1f4788] transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            View Invoices
          </button>
        </div>

        <div class="rounded-lg border border-[#d9e6f7] bg-[#f7fbff] p-4 text-sm text-gray-700 space-y-2">
          <p>Invoices remain available here for review, tracking, and PDF download.</p>
          <p>For billing questions or payment arrangements, contact Armely support and our team will assist you directly.</p>
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input v-model="editForm.name" type="text" required placeholder="Enter your full name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                <input v-model="editForm.company_name" type="text" required placeholder="Enter your company name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                  <label class="block text-sm font-medium text-gray-700 mb-1">Address Name</label>
                  <input v-model="editForm.shipping.label" type="text" placeholder="Address name (optional)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                  <input v-model="editForm.shipping.country" type="text" maxlength="2" placeholder="Country code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
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

    <!-- Delete Account Modal -->
    <div v-if="showDeleteAccountModal" class="fixed inset-0 bg-gray-400/30 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
          <div class="flex flex-col items-center text-center gap-2 mb-4">
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
              <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Delete Account Permanently</h3>
            <p class="text-sm text-gray-500">This action <strong>cannot be undone</strong>. All your quotes, orders, and data will be permanently removed.</p>
          </div>
          <form @submit.prevent="submitDeleteAccount" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirm your password</label>
              <input v-model="deleteForm.password" type="password" required placeholder="Enter your current password" class="w-full px-3 py-2 border border-red-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400">
            </div>
            <label class="flex items-start gap-3 cursor-pointer">
              <input v-model="deleteForm.confirmed" type="checkbox" class="mt-1 w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-400">
              <span class="text-sm text-gray-700">I understand this will permanently delete my account and all associated data.</span>
            </label>
            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="deleteAccountLoading || !deleteForm.confirmed || !deleteForm.password" class="flex-1 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                {{ deleteAccountLoading ? 'Deleting...' : 'Permanently Delete' }}
              </button>
              <button type="button" @click="showDeleteAccountModal = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                Cancel
              </button>
            </div>
          </form>
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
import { API_BASE_URL, normalizeLocalAssetUrl } from '../../services/runtimeConfig'
import Navbar from '../../components/Navbar.vue'
import {
  DEFAULT_NOTIFICATION_PREFERENCES,
  loadNotificationPreferences,
  saveNotificationPreferences,
} from '../../services/notificationPreferences'

const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()
const activities = ref([])
const loading = ref(false)

const getAuthToken = () => localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token') || authStore.token

// Modal states
const showEditProfileModal = ref(false)
const showChangePasswordModal = ref(false)
const showNotificationsModal = ref(false)
const showSignOutModal = ref(false)
const showDeleteAccountModal = ref(false)
const showAddCardModal = ref(false)

// Form states
const editFormLoading = ref(false)
const passwordFormLoading = ref(false)
const deleteAccountLoading = ref(false)

const deleteForm = ref({
  password: '',
  confirmed: false
})

const editForm = ref({
  name: '',
  email: '',
  phone: '',
  company_name: '',
  shipping: {
    label: '',
    contact_name: '',
    contact_phone: '',
    street_1: '',
    street_2: '',
    city: '',
    state: '',
    postal_code: '',
    country: ''
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
const incompleteFields = computed(() => authStore.user?.incomplete_fields || [])
const showCompleteProfileModal = ref(false)
const paymentMethodsLoading = ref(false)
const consentLoading = ref(false)
const paymentConsent = ref(false)
const savedCards = ref([])
const settingDefaultCardId = ref('')
const removingCardId = ref('')
const savingCard = ref(false)
const cardSetupError = ref('')
const stripeReady = ref(false)
const stripePublishableKey = ref('')
const setupClientSecret = ref('')
const cardElementFocused = ref(false)
const cardElementComplete = ref(false)

let stripe = null
let stripeElements = null
let cardElement = null

const stripeCardElementClass = computed(() => {
  if (cardSetupError.value) {
    return 'border border-red-300 ring-2 ring-red-100'
  }

  if (cardElementFocused.value) {
    return 'border border-blue-400 ring-4 ring-blue-100 shadow-sm'
  }

  if (cardElementComplete.value) {
    return 'border border-emerald-400 ring-2 ring-emerald-100'
  }

  return 'border border-gray-300 hover:border-gray-400'
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

const isPlaceholderShippingAddress = (shipping) => {
  if (!shipping) return false

  const values = [
    String(shipping.street_1 || '').trim().toLowerCase(),
    String(shipping.street_2 || '').trim().toLowerCase(),
    String(shipping.city || '').trim().toLowerCase(),
    String(shipping.state || '').trim().toLowerCase(),
    String(shipping.postal_code || '').trim(),
    String(shipping.country || '').trim().toUpperCase(),
  ]

  return new Set(values.slice(0, 4)).size === 1
    && values[0].length === 3
    && values[4] === '01100'
    && values[5] === 'KE'
}

const shippingSummary = computed(() => {
  const shipping = authStore.user?.shipping_address
  if (!shipping) return 'Not set'
  if (isPlaceholderShippingAddress(shipping)) return 'Not set'

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
    return normalizeLocalAssetUrl(authStore.user.profile_picture_url)
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

    const token = getAuthToken()
    console.log('Uploading profile picture...')
    const response = await fetch(`${API_BASE_URL}/auth/update-profile`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      },
      body: formData
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || `Failed to upload profile picture (${response.status})`)
    }

    // Update auth store with fresh user data
    if (data.user) {
      authStore.setUser({
        ...authStore.user,
        ...data.user
      })
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
    const token = getAuthToken()
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
  const company = authStore.user?.company
  editForm.value.name = authStore.user?.name || ''
  editForm.value.email = authStore.user?.email || ''
  editForm.value.phone = authStore.user?.phone || ''
  editForm.value.company_name = typeof company === 'object' ? (company?.name || '') : ''
  editForm.value.shipping = {
    label: shipping.label || '',
    contact_name: shipping.contact_name || '',
    contact_phone: shipping.contact_phone || '',
    street_1: shipping.street_1 || '',
    street_2: shipping.street_2 || '',
    city: shipping.city || '',
    state: shipping.state || '',
    postal_code: shipping.postal_code || '',
    country: shipping.country || ''
  }
  showEditProfileModal.value = true
}

const submitEditProfile = async () => {
  editFormLoading.value = true
  try {
    const token = getAuthToken()
    
    // Use FormData to support file uploads
    const formData = new FormData()
    formData.append('name', editForm.value.name)
    formData.append('email', editForm.value.email)
    formData.append('phone', editForm.value.phone)
    formData.append('company_name', editForm.value.company_name)
    
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
      country: String(editForm.value.shipping.country || '').trim().toUpperCase()
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
      method: 'POST',
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
      authStore.setUser({ ...data.user, incomplete_fields: data.incomplete_fields || [] })
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
    const token = getAuthToken()
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
  notificationSettings.value = {
    ...DEFAULT_NOTIFICATION_PREFERENCES,
    ...loadNotificationPreferences(authStore.user?.id),
  }

  showNotificationsModal.value = true
}

const saveNotificationSettings = () => {
  notificationSettings.value = saveNotificationPreferences(notificationSettings.value, authStore.user?.id)
  toastStore.addToast('Notification preferences saved!', 'success')
  showNotificationsModal.value = false
}

const fetchSavedPaymentMethods = async () => {
  paymentMethodsLoading.value = true
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/payment-methods`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    })

    const data = await response.json()
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Failed to load payment methods')
    }

    paymentConsent.value = !!data.data?.consent
    stripePublishableKey.value = data.data?.publishable_key || ''
    savedCards.value = Array.isArray(data.data?.cards) ? data.data.cards : []

    if (authStore.user) {
      authStore.user = {
        ...authStore.user,
        payment_methods_consent: paymentConsent.value
      }
    }
  } catch (error) {
    console.error('Failed to fetch payment methods:', error)
    toastStore.addToast(error.message || 'Failed to load payment methods', 'error')
  } finally {
    paymentMethodsLoading.value = false
  }
}

const savePaymentConsent = async () => {
  consentLoading.value = true
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/payment-methods/consent`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({ consent: paymentConsent.value })
    })

    const data = await response.json()
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Failed to update payment consent')
    }

    if (authStore.user) {
      authStore.user = {
        ...authStore.user,
        payment_methods_consent: paymentConsent.value
      }
    }

    toastStore.addToast('Payment consent updated.', 'success')
  } catch (error) {
    paymentConsent.value = !paymentConsent.value
    console.error('Failed to save payment consent:', error)
    toastStore.addToast(error.message || 'Failed to update payment consent', 'error')
  } finally {
    consentLoading.value = false
  }
}

const destroyStripeElements = () => {
  if (cardElement) {
    cardElement.unmount()
    cardElement = null
  }
  stripeElements = null
  setupClientSecret.value = ''
  stripeReady.value = false
  cardElementFocused.value = false
  cardElementComplete.value = false
}

const openAddCardModal = async () => {
  if (!paymentConsent.value) {
    toastStore.addToast('Please provide consent before saving card details.', 'warning')
    return
  }

  showAddCardModal.value = true
  cardSetupError.value = ''
  await initializeStripeCardSetup()
}

const initializeStripeCardSetup = async () => {
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/payment-methods/setup-intent`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({ consent: true })
    })

    const data = await response.json()
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Failed to initialize card setup')
    }

    stripePublishableKey.value = data.data?.publishable_key || stripePublishableKey.value
    setupClientSecret.value = data.data?.client_secret || ''

    if (!stripePublishableKey.value) {
      throw new Error('Stripe publishable key is missing. Set STRIPE_KEY in env.')
    }

    stripe = await loadStripe(stripePublishableKey.value)
    stripeElements = stripe.elements()

    await nextTick()
    destroyStripeElements()
    stripeElements = stripe.elements()
    cardElement = stripeElements.create('card', {
      style: {
        base: {
          fontSize: '15px',
          color: '#0f172a',
          fontFamily: 'Segoe UI, system-ui, -apple-system, sans-serif',
          '::placeholder': {
            color: '#94a3b8'
          }
        },
        invalid: {
          color: '#b91c1c',
          iconColor: '#b91c1c'
        }
      }
    })
    cardElement.on('focus', () => {
      cardElementFocused.value = true
    })
    cardElement.on('blur', () => {
      cardElementFocused.value = false
    })
    cardElement.on('change', (event) => {
      cardElementComplete.value = !!event.complete
      if (event.error?.message) {
        cardSetupError.value = event.error.message
      } else if (cardSetupError.value && stripeReady.value) {
        cardSetupError.value = ''
      }
    })
    cardElement.mount('#stripe-card-element')
    stripeReady.value = true
  } catch (error) {
    cardSetupError.value = error.message || 'Unable to initialize card setup.'
    stripeReady.value = false
  }
}

const closeAddCardModal = () => {
  showAddCardModal.value = false
  cardSetupError.value = ''
  destroyStripeElements()
}

const saveCard = async () => {
  if (!stripe || !cardElement || !setupClientSecret.value) {
    cardSetupError.value = 'Card form is not ready yet.'
    return
  }

  savingCard.value = true
  cardSetupError.value = ''
  try {
    const { setupIntent, error } = await stripe.confirmCardSetup(setupClientSecret.value, {
      payment_method: {
        card: cardElement,
        billing_details: {
          name: userName.value,
          email: userEmail.value,
        },
      },
    })

    if (error) {
      throw new Error(error.message || 'Failed to confirm card setup')
    }

    const paymentMethodId = setupIntent?.payment_method
    if (!paymentMethodId) {
      throw new Error('Stripe did not return a payment method.')
    }

    const token = getAuthToken()
    const attachResponse = await fetch(`${API_BASE_URL}/payment-methods/attach`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({
        payment_method_id: paymentMethodId,
        set_default: savedCards.value.length === 0,
        consent: true
      })
    })

    const attachData = await attachResponse.json()
    if (!attachResponse.ok || !attachData.success) {
      throw new Error(attachData.message || 'Failed to save card')
    }

    savedCards.value = Array.isArray(attachData.data?.cards) ? attachData.data.cards : []
    toastStore.addToast('Card saved securely.', 'success')
    closeAddCardModal()
  } catch (error) {
    cardSetupError.value = error.message || 'Failed to save card.'
  } finally {
    savingCard.value = false
  }
}

const setDefaultCard = async (paymentMethodId) => {
  settingDefaultCardId.value = paymentMethodId
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/payment-methods/default`, {
      method: 'PUT',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({ payment_method_id: paymentMethodId })
    })

    const data = await response.json()
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Failed to set default card')
    }

    savedCards.value = Array.isArray(data.data?.cards) ? data.data.cards : []
    toastStore.addToast('Default payment method updated.', 'success')
  } catch (error) {
    toastStore.addToast(error.message || 'Failed to set default card', 'error')
  } finally {
    settingDefaultCardId.value = ''
  }
}

const removeCard = async (paymentMethodId) => {
  removingCardId.value = paymentMethodId
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/payment-methods/${paymentMethodId}`, {
      method: 'DELETE',
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    })

    const data = await response.json()
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Failed to remove card')
    }

    savedCards.value = Array.isArray(data.data?.cards) ? data.data.cards : []
    toastStore.addToast('Card removed.', 'info')
  } catch (error) {
    toastStore.addToast(error.message || 'Failed to remove card', 'error')
  } finally {
    removingCardId.value = ''
  }
}

const logActivity = async (type, action, description) => {
  try {
    const token = getAuthToken()
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

const handleDeleteAccount = () => {
  deleteForm.value = { password: '', confirmed: false }
  showDeleteAccountModal.value = true
}

const submitDeleteAccount = async () => {
  deleteAccountLoading.value = true
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/auth/account`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ password: deleteForm.value.password })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Failed to delete account')
    }

    toastStore.addToast('Your account has been permanently deleted.', 'info')
    showDeleteAccountModal.value = false
    authStore.logout()
    router.push({ name: 'login' })
  } catch (error) {
    console.error('Error deleting account:', error)
    toastStore.addToast(error.message || 'Failed to delete account', 'error')
  } finally {
    deleteAccountLoading.value = false
  }
}

const handleSignOut = async () => {
  await authStore.logout()
  toastStore.addToast('Signed out successfully', 'info')
  await router.replace({ name: 'login' })
}

onMounted(() => {
  // Verify user is authenticated before loading
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to access your account', 'error')
    router.push({ name: 'login' })
    return
  }

  notificationSettings.value = {
    ...DEFAULT_NOTIFICATION_PREFERENCES,
    ...loadNotificationPreferences(authStore.user?.id),
  }
  
  fetchActivities()
  authStore.refreshUser()
})
</script>
