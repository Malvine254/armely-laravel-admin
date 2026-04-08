<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4 py-9">
    <div class="w-full max-w-md">
      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg" style="background: linear-gradient(135deg, #2F5597, #1f4788);">
            <span class="text-white font-bold text-2xl">A</span>
          </div>
          <p class="text-sm font-semibold tracking-wide uppercase" style="color: #2F5597;">Armely Store</p>
          <h2 class="text-3xl font-bold text-slate-900 mb-2">Welcome Back</h2>
          <p class="text-slate-600">Sign in to your Armely Store account</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
            <input v-model="email" type="email" placeholder="you@example.com" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #2F5597;">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
            <input v-model="password" type="password" placeholder="••••••••" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #2F5597;">
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input v-model="rememberMe" type="checkbox" class="w-4 h-4 rounded" style="accent-color: #2F5597;">
              <span class="ml-2 text-sm text-slate-600">Remember me</span>
            </label>
            <router-link to="/forgot-password" class="text-sm font-medium" style="color: #2F5597;">Forgot password?</router-link>
          </div>

          <button type="submit" :disabled="loading || !isFormValid" class="w-full px-6 py-3 text-white font-semibold rounded-lg transition transform hover:scale-105 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2" style="background: linear-gradient(90deg, #2F5597, #1f4788);">
            <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ loading ? 'Signing in…' : 'Sign In' }}
          </button>
        </form>

        <!-- Footer -->
        <p class="text-center text-slate-600 mt-6 text-sm">
          Don't have an account?
          <router-link to="/register" class="font-semibold" style="color: #2F5597;">Sign up for free</router-link>
        </p>
      </div>

      <div v-if="showResendActivation" class="mt-4 text-center">
        <p class="text-sm text-slate-600 mb-2">Account not activated yet?</p>
        <button
          @click="handleResendActivation"
          class="text-sm font-semibold"
          style="color: #2F5597;"
        >
          Resend activation email
        </button>
      </div>

      <!-- Help text -->
      <p class="text-center text-slate-600 text-sm mt-6">Having trouble? <a href="#" class="font-semibold" style="color: #2F5597;">Contact support</a></p>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import { useCartStore } from '../../stores/cartStore'
import { useToastStore } from '../../stores/toastStore'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()
const toastStore = useToastStore()

const email = ref(route.query.email ? String(route.query.email) : '')
const password = ref('')
const rememberMe = ref(false)
const showResendActivation = ref(false)
const loading = ref(false)

const isFormValid = computed(() => {
  const emailValue = email.value.trim()
  const passwordValue = password.value
  return emailValue.includes('@') && passwordValue.length >= 8
})

const resolvePostLoginRoute = () => {
  const redirectFromQuery = route.query.redirect ? String(route.query.redirect) : ''
  const redirectFromStorage = localStorage.getItem('redirectAfterLogin') || ''
  const redirectTarget = redirectFromQuery || redirectFromStorage

  if (redirectFromStorage) {
    localStorage.removeItem('redirectAfterLogin')
  }

  // Route to admin only when the redirect target explicitly asks for an admin page.
  if (redirectTarget.startsWith('/admin')) {
    if (!authStore.isAdmin) {
      return { name: 'products' }
    }

    if (redirectTarget === '/admin' || redirectTarget === '/admin/') return { name: 'admin-dashboard' }
    if (redirectTarget === '/admin/dashboard' || redirectTarget.startsWith('/admin/dashboard?')) return { name: 'admin-dashboard-page' }
    if (redirectTarget.startsWith('/admin/quotes')) return { name: 'admin-quotes' }
    if (redirectTarget.startsWith('/admin/orders')) return { name: 'admin-orders' }
    if (redirectTarget.startsWith('/admin/customers')) return { name: 'admin-customers' }
    if (redirectTarget.startsWith('/admin/reports')) return { name: 'admin-reports' }
    if (redirectTarget.startsWith('/admin/settings')) return { name: 'admin-settings' }
    if (redirectTarget.startsWith('/admin/invoices')) return { name: 'admin-invoices' }
    return { name: 'admin-dashboard-page' }
  }

  if (redirectTarget) return redirectTarget
  return { name: 'products' }
}

const handleLogin = async () => {
  if (!email.value || !password.value) {
    toastStore.addToast('Please enter email and password', 'warning')
    return
  }

  if (!email.value.includes('@')) {
    toastStore.addToast('Please enter a valid email', 'warning')
    return
  }

  if (password.value.length < 8) {
    toastStore.addToast('Password must be at least 8 characters', 'warning')
    return
  }

  loading.value = true
  try {
    const result = await authStore.login({ email: email.value, password: password.value, remember: rememberMe.value })
    if (result.ok) {
      cartStore.mergeGuestCartIntoCurrentUser()
      showResendActivation.value = false
      if (result.restricted) {
        toastStore.addToast(result.message || 'Your account is restricted. You have read-only access.', 'warning')
        router.push({ name: 'account' })
      } else {
        toastStore.addToast('Welcome back!', 'success')
        router.push(resolvePostLoginRoute())
      }
    } else {
      if ((result.message || '').toLowerCase().includes('activate your account')) {
        showResendActivation.value = true
      }
      toastStore.addToast(result.message || 'Login failed', 'warning')
    }
  } catch (error) {
    toastStore.addToast(error.response?.data?.message || 'Login failed', 'warning')
  } finally {
    loading.value = false
  }
}

const handleResendActivation = async () => {
  if (!email.value) {
    toastStore.addToast('Please enter your email first.', 'warning')
    return
  }

  const result = await authStore.resendActivation(email.value)
  toastStore.addToast(result.message, result.ok ? 'success' : 'warning')
}

onMounted(() => {
  if (!route.query.activation) {
    return
  }

  const status = String(route.query.activation)
  const message = route.query.message ? String(route.query.message) : ''

  if (status === 'success') {
    toastStore.addToast(message || 'Account activated successfully. You can now log in.', 'success')
  } else {
    toastStore.addToast(message || 'Activation failed. Please request a new activation link.', 'warning')
  }
})
</script>
