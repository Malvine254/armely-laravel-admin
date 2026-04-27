<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
      <!-- Badge -->
      <div class="text-center mb-6">
        <div class="inline-flex items-center gap-2 bg-amber-600 text-white px-4 py-2 rounded-lg shadow-lg">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <span class="font-bold">Password Change Required</span>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-6">
          <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4" style="background: linear-gradient(135deg, #2f5597, #1f4788);">
            <span class="text-2xl font-bold text-white">AS</span>
          </div>
          <h2 class="text-2xl font-bold text-slate-900">Set Your Password</h2>
          <p class="text-slate-600 mt-2 text-sm">This is your first login. You must set a new password before continuing.</p>
        </div>

        <div v-if="errorMessage" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-700 text-sm">{{ errorMessage }}</p>
        </div>
        <div v-if="successMessage" class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
          <p class="text-emerald-700 text-sm">{{ successMessage }}</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-5">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Current (Temporary) Password</label>
            <input
              v-model="form.currentPassword"
              type="password"
              required
              placeholder="Enter the password you received"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2f5597] focus:border-transparent outline-none transition"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
            <input
              v-model="form.newPassword"
              type="password"
              required
              placeholder="Min 8 characters"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2f5597] focus:border-transparent outline-none transition"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password</label>
            <input
              v-model="form.confirmPassword"
              type="password"
              required
              placeholder="Re-enter new password"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2f5597] focus:border-transparent outline-none transition"
            />
          </div>

          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="w-full text-white py-3 px-4 rounded-lg font-semibold hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-[#2f5597] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-lg"
            style="background: linear-gradient(90deg, #2f5597, #1f4788);"
          >
            <span v-if="loading" class="inline-flex items-center gap-2">
              <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              Updating…
            </span>
            <span v-else>Set New Password & Continue</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({ currentPassword: '', newPassword: '', confirmPassword: '' })
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const isFormValid = computed(() => {
  return (
    form.value.currentPassword.length >= 1 &&
    form.value.newPassword.length >= 8 &&
    form.value.confirmPassword === form.value.newPassword
  )
})

const handleSubmit = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (form.value.newPassword !== form.value.confirmPassword) {
    errorMessage.value = 'New passwords do not match.'
    return
  }

  loading.value = true
  try {
    const response = await api.put('/auth/change-password', {
      current_password: form.value.currentPassword,
      new_password: form.value.newPassword,
      new_password_confirmation: form.value.confirmPassword,
    })

    if (response.data.success) {
      authStore.clearForcePasswordChange()
      successMessage.value = 'Password updated! Redirecting…'
      setTimeout(() => router.push({ name: 'admin-dashboard-page' }), 1200)
    } else {
      errorMessage.value = response.data.message || 'Failed to update password.'
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to update password.'
  } finally {
    loading.value = false
  }
}
</script>
