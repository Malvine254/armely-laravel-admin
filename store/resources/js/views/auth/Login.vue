<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4 py-9">
    <div class="w-full max-w-md">
      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">

        <!-- Suspended banner — top of card, full width -->
        <div v-if="accountSuspended" class="bg-red-50 border-b-2 border-red-400 px-6 py-5">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mt-0.5">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-base font-bold text-red-900 leading-tight">Your account has been suspended</p>
              <p class="text-sm text-red-800 mt-1 leading-snug">Access to your account has been suspended by our team.</p>
              <p class="text-sm text-red-700 mt-2">
                Questions? Contact us at
                <a href="mailto:info@armely.com" class="font-semibold underline hover:text-red-900">info@armely.com</a>
              </p>
            </div>
          </div>
        </div>

        <!-- Pending approval banner — top of card, full width -->
        <div v-if="pendingApproval" class="bg-amber-50 border-b-2 border-amber-400 px-6 py-5">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mt-0.5">
              <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-base font-bold text-amber-900 leading-tight">Account pending approval</p>
              <p class="text-sm text-amber-800 mt-1 leading-snug">
                Your account is awaiting review by our team. You'll receive an email at
                <strong class="font-semibold break-all">{{ email || 'your email address' }}</strong>
                once it's approved.
              </p>
              <p class="text-sm text-amber-700 mt-2">
                Questions? Contact us at
                <a href="mailto:info@armely.com" class="font-semibold underline hover:text-amber-900">info@armely.com</a>
              </p>
            </div>
          </div>
        </div>

        <div class="p-8">
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

        <div v-if="showResendActivation" class="mt-5 rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-4 text-center shadow-sm">
          <p class="text-sm text-slate-700 mb-2">Account not activated yet?</p>
          <button
            type="button"
            @click="handleResendActivation"
            :disabled="resendActivationDisabled"
            class="text-sm font-semibold disabled:cursor-not-allowed disabled:opacity-60"
            style="color: #2F5597;"
          >
            {{ resendActivationButtonText }}
          </button>
        </div>
        </div><!-- end p-8 -->
      </div>

      <!-- Help text -->
      <p class="text-center text-slate-600 text-sm mt-6">Having trouble? <a href="#" class="font-semibold" style="color: #2F5597;">Contact support</a></p>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
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
const pendingApproval = ref(false)
const accountSuspended = ref(false)
const loading = ref(false)
const resendActivationLoading = ref(false)
const resendActivationCooldown = ref(0)
let resendActivationTimer = null

const RESEND_ACTIVATION_COOLDOWN_SECONDS = 60

const isFormValid = computed(() => {
  const emailValue = email.value.trim()
  const passwordValue = password.value
  return emailValue.includes('@') && passwordValue.length >= 8
})

const resendActivationDisabled = computed(() => {
  return resendActivationLoading.value || resendActivationCooldown.value > 0
})

const resendActivationButtonText = computed(() => {
  if (resendActivationLoading.value) return 'Sending activation email...'
  if (resendActivationCooldown.value > 0) {
    return `Resend activation email in ${resendActivationCooldown.value}s`
  }
  return 'Resend activation email'
})

const stopResendActivationCooldown = () => {
  if (resendActivationTimer) {
    window.clearInterval(resendActivationTimer)
    resendActivationTimer = null
  }
}

const startResendActivationCooldown = (seconds = RESEND_ACTIVATION_COOLDOWN_SECONDS) => {
  stopResendActivationCooldown()
  resendActivationCooldown.value = Math.max(0, Number(seconds) || RESEND_ACTIVATION_COOLDOWN_SECONDS)

  resendActivationTimer = window.setInterval(() => {
    resendActivationCooldown.value = Math.max(0, resendActivationCooldown.value - 1)
    if (resendActivationCooldown.value === 0) {
      stopResendActivationCooldown()
    }
  }, 1000)
}

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
  return authStore.isAdmin ? { name: 'admin-dashboard-page' } : { name: 'products' }
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
      pendingApproval.value = false
      if (result.restricted) {
        toastStore.addToast(result.message || 'Your account is restricted. You have read-only access.', 'warning')
        router.push({ name: 'account' })
      } else {
        toastStore.addToast('Welcome back!', 'success')
        router.push(resolvePostLoginRoute())
      }
    } else {
      const reason = result.restrictionReason
      if (reason === 'company_not_approved' || reason === 'user_not_active') {
        pendingApproval.value = true
        showResendActivation.value = false
      } else if ((result.message || '').toLowerCase().includes('activate your account')) {
        showResendActivation.value = true
        pendingApproval.value = false
        toastStore.addToast(result.message || 'Login failed', 'warning')
      } else {
        pendingApproval.value = false
        toastStore.addToast(result.message || 'Login failed', 'warning')
      }
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

  if (resendActivationDisabled.value) {
    return
  }

  resendActivationLoading.value = true
  const result = await authStore.resendActivation(email.value)
  resendActivationLoading.value = false

  if (result.ok || result.retryAfter) {
    startResendActivationCooldown(result.retryAfter || RESEND_ACTIVATION_COOLDOWN_SECONDS)
  }

  toastStore.addToast(result.message, result.ok ? 'success' : 'warning')
}

onMounted(() => {
  if (route.query.reason === 'suspended') {
    accountSuspended.value = true
  }

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

onUnmounted(() => {
  stopResendActivationCooldown()
})
</script>
