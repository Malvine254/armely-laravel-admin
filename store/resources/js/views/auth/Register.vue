<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4 py-9">
    <div class="w-full max-w-md">
      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="mx-auto mb-4 flex h-28 w-28 items-center justify-center">
            <img
              :src="normalizeLocalAssetUrl('/images/logo/armely-store-logo.png')"
              alt="Armely Store"
              class="h-full w-full object-contain"
            >
          </div>
          <h2 class="text-3xl font-bold text-slate-900 mb-2">Create Account</h2>
          <p class="text-slate-600">Join Armely Store today</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleRegister" class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Company Name</label>
            <input v-model="companyName" type="text" placeholder="Your company" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #2F5597;">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
            <input v-model="email" type="email" placeholder="you@company.com" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #2F5597;">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
            <input v-model="fullName" type="text" placeholder="John Doe" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #2F5597;">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
            <input v-model="password" type="password" placeholder="••••••••" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #2F5597;">
            <p class="text-xs text-slate-500 mt-1">Use at least 8 chars with upper/lowercase, number, and symbol</p>

            <div v-if="password" class="mt-2">
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-slate-600">Password strength</span>
                <span class="text-xs font-semibold" :class="passwordStrengthClass">{{ passwordStrengthLabel }}</span>
              </div>
              <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                <div
                  class="h-2 transition-all duration-200"
                  :class="passwordStrengthBarClass"
                  :style="{ width: passwordStrengthPercent + '%' }"
                ></div>
              </div>
            </div>

            <ul class="mt-2 space-y-1 text-xs">
              <li :class="passwordChecks.minLength ? 'text-green-700' : 'text-slate-500'">{{ passwordChecks.minLength ? 'OK' : 'X' }} At least 8 characters</li>
              <li :class="passwordChecks.hasUpper ? 'text-green-700' : 'text-slate-500'">{{ passwordChecks.hasUpper ? 'OK' : 'X' }} One uppercase letter</li>
              <li :class="passwordChecks.hasLower ? 'text-green-700' : 'text-slate-500'">{{ passwordChecks.hasLower ? 'OK' : 'X' }} One lowercase letter</li>
              <li :class="passwordChecks.hasNumber ? 'text-green-700' : 'text-slate-500'">{{ passwordChecks.hasNumber ? 'OK' : 'X' }} One number</li>
              <li :class="passwordChecks.hasSymbol ? 'text-green-700' : 'text-slate-500'">{{ passwordChecks.hasSymbol ? 'OK' : 'X' }} One symbol</li>
            </ul>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Confirm Password</label>
            <input v-model="confirmPassword" type="password" placeholder="••••••••" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition" style="--tw-ring-color: #2F5597;">
            <p v-if="confirmPassword" :class="passwordChecks.matchesConfirm ? 'text-green-700' : 'text-red-600'" class="text-xs mt-2">
              {{ passwordChecks.matchesConfirm ? 'OK Passwords match' : 'X Passwords do not match yet' }}
            </p>
          </div>

          <label class="flex items-start">
            <input type="checkbox" class="w-4 h-4 rounded mt-1" style="accent-color: #2F5597;">
            <span class="ml-2 text-sm text-slate-600">I agree to the <a href="#" style="color: #2F5597;">Terms of Service</a> and <a href="#" style="color: #2F5597;">Privacy Policy</a></span>
          </label>

          <button
            type="submit"
            :disabled="loading || !canSubmit"
            class="w-full px-6 py-3 text-white font-semibold rounded-lg transition transform hover:scale-105 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2"
            style="background: linear-gradient(90deg, #2F5597, #1f4788);"
          >
            <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span>{{ loading ? 'Creating account...' : 'Create Account' }}</span>
          </button>
        </form>

        <!-- Footer -->
        <p class="text-center text-slate-600 mt-6 text-sm">
          Already have an account?
          <router-link to="/login" class="font-semibold" style="color: #2F5597;">Sign in here</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { normalizeLocalAssetUrl } from '@/services/runtimeConfig'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'

const router = useRouter()
const authStore = useAuthStore()
const toastStore = useToastStore()

const companyName = ref('')
const email = ref('')
const fullName = ref('')
const password = ref('')
const confirmPassword = ref('')
const loading = ref(false)

const isValidEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value || '')

const passwordChecks = computed(() => {
  const value = password.value || ''
  return {
    minLength: value.length >= 8,
    hasUpper: /[A-Z]/.test(value),
    hasLower: /[a-z]/.test(value),
    hasNumber: /\d/.test(value),
    hasSymbol: /[^A-Za-z0-9]/.test(value),
    matchesConfirm: value.length > 0 && value === confirmPassword.value,
  }
})

const passwordStrengthScore = computed(() => {
  const value = password.value || ''
  if (!value) return 0

  let score = 0
  if (value.length >= 8) score += 1
  if (value.length >= 12) score += 1
  if (passwordChecks.value.hasUpper) score += 1
  if (passwordChecks.value.hasLower) score += 1
  if (passwordChecks.value.hasNumber) score += 1
  if (passwordChecks.value.hasSymbol) score += 1
  if (value.length >= 16) score += 1

  return Math.min(score, 7)
})

const passwordStrengthPercent = computed(() => Math.round((passwordStrengthScore.value / 7) * 100))

const passwordStrengthLabel = computed(() => {
  const score = passwordStrengthScore.value
  if (score <= 2) return 'Weak'
  if (score <= 4) return 'Medium'
  if (score <= 6) return 'Strong'
  return 'Very strong'
})

const passwordStrengthClass = computed(() => {
  const score = passwordStrengthScore.value
  if (score <= 2) return 'text-red-600'
  if (score <= 4) return 'text-amber-600'
  if (score <= 6) return 'text-blue-700'
  return 'text-green-700'
})

const passwordStrengthBarClass = computed(() => {
  const score = passwordStrengthScore.value
  if (score <= 2) return 'bg-red-500'
  if (score <= 4) return 'bg-amber-500'
  if (score <= 6) return 'bg-blue-600'
  return 'bg-green-600'
})

const canSubmit = computed(() => {
  return !!companyName.value.trim()
    && !!fullName.value.trim()
    && isValidEmail(email.value)
    && isStrongPassword(password.value)
    && passwordChecks.value.matchesConfirm
})

const isStrongPassword = (value) => {
  if (!value || value.length < 8) return false
  const hasUpper = /[A-Z]/.test(value)
  const hasLower = /[a-z]/.test(value)
  const hasNumber = /\d/.test(value)
  const hasSymbol = /[^A-Za-z0-9]/.test(value)
  return hasUpper && hasLower && hasNumber && hasSymbol
}

const handleRegister = async () => {
  if (loading.value) {
    return
  }

  if (!companyName.value || !email.value || !fullName.value || !password.value) {
    toastStore.addToast('Please fill in all required fields', 'warning')
    return
  }

  if (!isValidEmail(email.value)) {
    toastStore.addToast('Please enter a valid email', 'warning')
    return
  }

  if (!isStrongPassword(password.value)) {
    toastStore.addToast('Password must be at least 8 characters and include uppercase, lowercase, number, and symbol', 'warning')
    return
  }

  if (password.value !== confirmPassword.value) {
    toastStore.addToast('Passwords do not match', 'warning')
    return
  }

  loading.value = true

  try {
    const result = await authStore.register({
      companyName: companyName.value,
      email: email.value,
      fullName: fullName.value,
      password: password.value,
      confirmPassword: confirmPassword.value
    })

    if (result.ok) {
      toastStore.addToast(result.message || 'Registration successful. Please check your email to activate your account.', 'success')
      router.push({ name: 'login', query: { email: email.value } })
    } else {
      toastStore.addToast(result.message || 'Registration failed', 'warning')
    }
  } catch (error) {
    toastStore.addToast(error.response?.data?.message || 'Registration failed', 'warning')
  } finally {
    loading.value = false
  }
}
</script>
