<template>
  <div class="fixed top-6 right-6 z-[9999] space-y-3">
    <transition-group name="toast" tag="div">
      <div v-for="toast in toastStore.toasts" :key="toast.id" class="flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg border" :class="toastClasses(toast.type)">
        <div class="mt-0.5">
          <svg v-if="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <svg v-else-if="toast.type === 'warning'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
          </svg>
        </div>
        <div class="text-sm font-semibold">
          {{ toast.message }}
        </div>
        <button class="ml-auto text-sm opacity-70 hover:opacity-100 transition" @click="toastStore.removeToast(toast.id)">×</button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useToastStore } from '../stores/toastStore'

const toastStore = useToastStore()

const toastClasses = (type) => {
  if (type === 'success') {
    return 'bg-green-50 border-green-200 text-green-800'
  }
  if (type === 'warning') {
    return 'bg-yellow-50 border-yellow-200 text-yellow-800'
  }
  return 'bg-blue-50 border-blue-200 text-blue-800'
}
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.25s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
