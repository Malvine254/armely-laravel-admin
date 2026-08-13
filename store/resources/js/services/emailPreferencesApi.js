import api from './api'

const normalizeLifecyclePreferences = (raw) => {
  const data = raw && typeof raw === 'object' ? raw : {}

  return {
    transactional_enabled: data.transactional_enabled !== false,
    marketing_enabled: data.marketing_enabled !== false,
    price_alerts_enabled: data.price_alerts_enabled !== false,
    cart_reminders_enabled: data.cart_reminders_enabled !== false,
    browse_reminders_enabled: data.browse_reminders_enabled !== false,
    timezone: typeof data.timezone === 'string' ? data.timezone : '',
    quiet_hours_start: Number.isInteger(data.quiet_hours_start) ? data.quiet_hours_start : null,
    quiet_hours_end: Number.isInteger(data.quiet_hours_end) ? data.quiet_hours_end : null,
  }
}

export const fetchLifecycleEmailPreferences = async () => {
  const response = await api.get('/behavior/email-preferences')
  return normalizeLifecyclePreferences(response?.data?.data || {})
}

export const updateLifecycleEmailPreferences = async (payload) => {
  const response = await api.put('/behavior/email-preferences', payload)
  return normalizeLifecyclePreferences(response?.data?.data || {})
}

export const minutesToClock = (minutes) => {
  if (!Number.isInteger(minutes) || minutes < 0 || minutes > 1439) {
    return ''
  }

  const hours = String(Math.floor(minutes / 60)).padStart(2, '0')
  const mins = String(minutes % 60).padStart(2, '0')
  return `${hours}:${mins}`
}

export const clockToMinutes = (value) => {
  const text = String(value || '').trim()
  if (!/^\d{2}:\d{2}$/.test(text)) {
    return null
  }

  const [hoursText, minutesText] = text.split(':')
  const hours = Number(hoursText)
  const minutes = Number(minutesText)

  if (!Number.isInteger(hours) || !Number.isInteger(minutes)) {
    return null
  }

  if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
    return null
  }

  return (hours * 60) + minutes
}
