<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center px-4 py-9">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-8 text-center">
        <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg" style="background: linear-gradient(135deg, #2F5597, #1f4788);">
          <span class="text-white font-bold text-2xl">A</span>
        </div>

        <p class="text-sm font-semibold tracking-wide uppercase" style="color: #2F5597;">Armely Store</p>

        <h2 class="text-2xl font-bold text-slate-900 mb-2">Account Activation</h2>

        <div v-if="loading" class="py-4">
          <p class="text-slate-600">Activating your account...</p>
        </div>

        <div v-else>
          <p :class="success ? 'text-green-700' : 'text-amber-700'" class="mb-6">
            {{ message }}
          </p>

          <router-link
            to="/login"
            class="inline-block px-6 py-3 text-white font-semibold rounded-lg transition"
            style="background: linear-gradient(90deg, #2F5597, #1f4788);"
          >
            Go to Login
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const loading = ref(true)
const success = ref(false)
const message = ref('Processing activation link...')

onMounted(async () => {
  const token = route.query.token
  const email = route.query.email

  if (!token || !email) {
    loading.value = false
    success.value = false
    message.value = 'Activation link is missing required information.'
    return
  }

  try {
    const response = await axios.get('/api/v1/auth/activate', {
      params: { token, email }
    })

    success.value = !!response.data?.success
    message.value = response.data?.message || 'Account activated successfully.'
  } catch (error) {
    success.value = false
    message.value = error.response?.data?.message || 'Activation failed. Please request a new activation email.'
  } finally {
    loading.value = false
  }
})
</script>
