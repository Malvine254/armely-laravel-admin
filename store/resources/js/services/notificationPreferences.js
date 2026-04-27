import { ref } from 'vue'

const DEFAULT_NOTIFICATION_PREFERENCES = {
  email: true,
  quotes: true,
  orders: true,
  invoices: true,
}

const getCurrentStoredUserId = () => {
  const savedUser = localStorage.getItem('armely_user') || sessionStorage.getItem('armely_user')
  if (!savedUser) {
    return null
  }

  try {
    const parsed = JSON.parse(savedUser)
    return parsed?.id ?? null
  } catch (_) {
    return null
  }
}

const notificationPreferences = ref({ ...DEFAULT_NOTIFICATION_PREFERENCES })

const normalizeNotificationPreferences = (value) => {
  const raw = value && typeof value === 'object' ? value : {}

  return {
    email: raw.email !== false,
    quotes: raw.quotes !== false,
    orders: raw.orders !== false,
    invoices: raw.invoices !== false,
  }
}

const getNotificationStorageKey = (userId = null) => {
  return userId ? `notification_preferences_${userId}` : 'notification_preferences'
}

const loadNotificationPreferences = (userId = null) => {
  const key = getNotificationStorageKey(userId)
  const saved = localStorage.getItem(key)

  if (!saved) {
    notificationPreferences.value = { ...DEFAULT_NOTIFICATION_PREFERENCES }
    return notificationPreferences.value
  }

  try {
    notificationPreferences.value = normalizeNotificationPreferences(JSON.parse(saved))
  } catch (error) {
    console.warn('Invalid notification preferences in localStorage, resetting to defaults.', error)
    notificationPreferences.value = { ...DEFAULT_NOTIFICATION_PREFERENCES }
  }

  return notificationPreferences.value
}

const saveNotificationPreferences = (value, userId = null) => {
  const normalized = normalizeNotificationPreferences(value)
  notificationPreferences.value = normalized

  const key = getNotificationStorageKey(userId)
  localStorage.setItem(key, JSON.stringify(normalized))
  window.dispatchEvent(new CustomEvent('notification-preferences-updated', { detail: normalized }))

  return normalized
}

const getNotificationPreferences = () => {
  return notificationPreferences.value
}

const isNotificationCategoryEnabled = (category) => {
  const prefs = notificationPreferences.value
  if (prefs.email === false) {
    return false
  }

  if (!category) {
    return true
  }

  return prefs[category] !== false
}

loadNotificationPreferences(getCurrentStoredUserId())

export {
  DEFAULT_NOTIFICATION_PREFERENCES,
  notificationPreferences,
  getNotificationStorageKey,
  loadNotificationPreferences,
  saveNotificationPreferences,
  getNotificationPreferences,
  isNotificationCategoryEnabled,
}
