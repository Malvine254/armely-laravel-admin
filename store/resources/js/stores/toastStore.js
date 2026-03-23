import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toasts', () => {
  const toasts = ref([])
  let nextId = 1

  const addToast = (message, type = 'info', duration = 3000) => {
    const id = nextId++
    toasts.value.push({ id, message, type })

    setTimeout(() => {
      removeToast(id)
    }, duration)
  }

  const removeToast = (id) => {
    toasts.value = toasts.value.filter(toast => toast.id !== id)
  }

  return {
    toasts,
    addToast,
    removeToast
  }
})
