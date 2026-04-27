<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4 py-9">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8">
        <div class="text-center mb-8">
          <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg" style="background: linear-gradient(135deg, #2F5597, #1f4788);">
            <span class="text-white font-bold text-2xl">A</span>
          </div>
          <p class="text-sm font-semibold tracking-wide uppercase" style="color: #2F5597;">Armely Store</p>
          <h2 class="text-3xl font-bold text-slate-900 mb-2">Forgot Password</h2>
          <p class="text-slate-600">Enter your email to receive a reset link</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
            <input
              v-model="email"
              type="email"
              placeholder="you@example.com"
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent transition"
              style="--tw-ring-color: #2F5597;"
            >
          </div>

          <button
            type="submit"
            :disabled="submitting"
            class="w-full px-6 py-3 text-white font-semibold rounded-lg transition disabled:opacity-60"
            style="background: linear-gradient(90deg, #2F5597, #1f4788);"
          >
            {{ submitting ? 'Sending...' : 'Send Reset Link' }}
          </button>
        </form>

        <p class="text-center text-slate-600 mt-6 text-sm">
          Remember your password?
          <router-link to="/login" class="font-semibold" style="color: #2F5597;">Back to Login</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/authStore'
import { useToastStore } from '../../stores/toastStore'

const authStore = useAuthStore()
const toastStore = useToastStore()

const email = ref('')
const submitting = ref(false)

const handleSubmit = async () => {
  if (!email.value || !email.value.includes('@')) {
    toastStore.addToast('Please enter a valid email address', 'warning')
    return
  }

  submitting.value = true
  try {
    const result = await authStore.forgotPassword(email.value)
    toastStore.addToast(result.message, result.ok ? 'success' : 'warning')
  } finally {
    submitting.value = false
  }
}
</script>
