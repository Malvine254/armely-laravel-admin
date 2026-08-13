<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4 py-9">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
        <div class="text-center mb-8">
          <div class="mx-auto mb-4 flex h-28 w-28 items-center justify-center">
            <img
              :src="normalizeLocalAssetUrl('/images/logo/armely-store-logo.png')"
              alt="Armely Store"
              class="h-full w-full object-contain"
            >
          </div>
          <p class="text-sm font-semibold tracking-wide uppercase" style="color: #2F5597;">Armely Store</p>
          <h2 class="text-3xl font-bold text-slate-900 mb-2">Reset Password</h2>
          <p class="text-slate-600">Create a new secure password for your account</p>
        </div>

        <div v-if="!email || !token" class="mb-4 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-800">
          Invalid reset link. Please request a new password reset email.
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
            <input
              v-model="password"
              type="password"
              placeholder="Enter new password"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition"
              style="--tw-ring-color: #2F5597;"
            >
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password</label>
            <input
              v-model="passwordConfirmation"
              type="password"
              placeholder="Confirm new password"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition"
              style="--tw-ring-color: #2F5597;"
            >
          </div>

          <button
            type="submit"
            :disabled="submitting || !email || !token"
            class="w-full px-6 py-3 text-white font-semibold rounded-lg transition disabled:opacity-60"
            style="background: linear-gradient(90deg, #2F5597, #1f4788);"
          >
            {{ submitting ? 'Resetting...' : 'Reset Password' }}
          </button>
        </form>

        <p class="text-center text-slate-600 mt-6 text-sm">
          <router-link to="/login" class="font-semibold" style="color: #2F5597;">Back to Login</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { normalizeLocalAssetUrl } from '@/services/runtimeConfig'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()

const email = computed(() => route.query.email ? String(route.query.email) : '')
const token = computed(() => route.query.token ? String(route.query.token) : '')

const password = ref('')
const passwordConfirmation = ref('')
const submitting = ref(false)

const handleSubmit = async () => {
  if (!email.value || !token.value) {
    toastStore.addToast('Invalid reset link. Please request a new one.', 'warning')
    return
  }

  if (password.value.length < 8) {
    toastStore.addToast('Password must be at least 8 characters', 'warning')
    return
  }

  if (password.value !== passwordConfirmation.value) {
    toastStore.addToast('Passwords do not match', 'warning')
    return
  }

  submitting.value = true
  try {
    const result = await authStore.resetPassword({
      email: email.value,
      token: token.value,
      password: password.value,
      passwordConfirmation: passwordConfirmation.value
    })

    toastStore.addToast(result.message, result.ok ? 'success' : 'warning')

    if (result.ok) {
      router.push({ name: 'login', query: { reset: 'success', email: email.value } })
    }
  } finally {
    submitting.value = false
  }
}
</script>
