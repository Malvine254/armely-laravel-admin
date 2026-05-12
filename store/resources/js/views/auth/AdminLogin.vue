<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
      <!-- Admin Badge -->
      <div class="text-center mb-6">
        <div class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
          <span class="font-bold">Admin Access Only</span>
        </div>
      </div>

      <!-- Login Card -->
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4" style="background: linear-gradient(135deg, #2f5597, #1f4788);">
            <span class="text-2xl font-bold text-white">AS</span>
          </div>
          <h2 class="text-2xl font-bold text-slate-900">Armely Admin Portal</h2>
          <p class="text-slate-600 mt-2">Sign in to access the control panel</p>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-700 text-sm">{{ errorMessage }}</p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              placeholder="admin@yourcompany.com"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2f5597] focus:border-transparent outline-none transition"
            />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              placeholder="••••••••"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2f5597] focus:border-transparent outline-none transition"
            />
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input v-model="form.remember" type="checkbox" class="w-4 h-4 border-slate-300 rounded focus:ring-2 focus:ring-[#2f5597]" style="accent-color: #2f5597;">
              <span class="ml-2 text-sm text-slate-600">Remember me</span>
            </label>
            <router-link :to="{ name: 'forgot-password' }" class="text-sm font-medium" style="color: #2f5597;">Forgot password?</router-link>
          </div>

          <!-- Submit Button -->
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
              Signing in...
            </span>
            <span v-else>Sign In to Admin Portal</span>
          </button>
        </form>

        <!-- Back to Customer Portal -->
        <div class="mt-6 text-center">
          <router-link to="/login" class="text-sm text-slate-600 hover:text-slate-900">
            ← Back to Customer Login
          </router-link>
        </div>
      </div>

      <!-- Security Notice -->
      <div class="mt-6 text-center text-sm text-slate-600">
        <p>🔒 Secured with industry-standard encryption</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { useCartStore } from '@/stores/cartStore'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const form = ref({
  email: '',
  password: '',
  remember: false
})

const loading = ref(false)
const errorMessage = ref('')

const isFormValid = computed(() => {
  const emailValue = form.value.email.trim()
  const passwordValue = form.value.password
  return emailValue.includes('@') && passwordValue.length >= 8
})

const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const result = await authStore.login({
      email: form.value.email,
      password: form.value.password
    })

    if (result.ok) {
      cartStore.mergeGuestCartIntoCurrentUser()

      // Check if user is actually an admin
      if (!authStore.isAdmin) {
        errorMessage.value = 'Access denied. Admin credentials required.'
        await authStore.logout()
        loading.value = false
        return
      }

      // Redirect using route name so Vue Router keeps the configured /store base path.
      if (result.forcePasswordChange) {
        router.push({ name: 'admin-change-password' })
      } else {
        router.push({ name: 'admin-dashboard-page' })
      }
    } else {
      errorMessage.value = result.message || 'Invalid credentials'
    }
  } catch (error) {
    errorMessage.value = 'Login failed. Please try again.'
    console.error('Login error:', error)
  } finally {
    loading.value = false
  }
}
</script>
