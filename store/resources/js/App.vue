<template>
  <div id="app">
    <div
      v-if="showRestrictedBanner"
      class="sticky top-0 z-[10000] border-b border-amber-300 bg-amber-100"
    >
      <div class="mx-auto max-w-7xl px-4 py-3 text-sm text-amber-900">
        <template v-if="authStore.isActivationPending">
          <span class="font-semibold">Activation Required:</span>
          Please activate your account from the email link. Until then, only limited pages and read-only access are available.
        </template>
        <template v-else>
          <span class="font-semibold">Account Suspended:</span>
          Your account is currently restricted. You can browse read-only pages, but actions like creating quotes or placing orders are disabled.
        </template>
      </div>
    </div>
    <router-view />
    <Toasts />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Toasts from './components/Toasts.vue'
import { useAuthStore } from './stores/authStore'

const route = useRoute()
const authStore = useAuthStore()

const showRestrictedBanner = computed(() => {
  const hiddenRoutes = ['login', 'register', 'admin-login']
  return authStore.isAuthenticated && authStore.isRestricted && !hiddenRoutes.includes(route.name)
})
</script>

<style>
/* Global styles */
</style>
