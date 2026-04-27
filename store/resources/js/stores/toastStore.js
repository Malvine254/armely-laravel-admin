import { defineStore } from 'pinia'
import { ref } from 'vue'
import { isNotificationCategoryEnabled } from '../services/notificationPreferences'

export const useToastStore = defineStore('toasts', () => {
  const toasts = ref([])
  let nextId = 1

  const addToast = (message, type = 'info', duration = 3000, options = {}) => {
    const category = typeof options === 'string' ? options : options?.category
    const force = typeof options === 'object' ? options?.force === true : false

    if (!force && type !== 'error' && category && !isNotificationCategoryEnabled(category)) {
      return
    }

    const id = nextId++
    toasts.value.push({ id, message, type, category: category || null })

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
