import axios from 'axios'
import { API_BASE_URL, buildStoreUrl } from './runtimeConfig'

const DEFAULT_API_TIMEOUT_MS = 45000
const RETRYABLE_ERROR_CODES = new Set(['ECONNABORTED', 'ERR_NETWORK'])

const isRetryableGetError = (error) => {
  const method = String(error?.config?.method || '').toLowerCase()
  if (method !== 'get') return false

  const code = String(error?.code || '').toUpperCase()
  const message = String(error?.message || '').toLowerCase()
  return RETRYABLE_ERROR_CODES.has(code) || message.includes('timeout') || message.includes('network error')
}

const api = axios.create({
  baseURL: API_BASE_URL,
  // Allow slower catalog endpoints while still failing eventually.
  timeout: DEFAULT_API_TIMEOUT_MS,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  if (!config.timeout || config.timeout <= 0) {
    config.timeout = DEFAULT_API_TIMEOUT_MS
  }
  return config
})

const clearAuthStorage = () => {
  const keys = ['auth_token', 'armely_user', 'auth_session_expiry', 'auth_restricted', 'auth_remember', 'auth_force_pw']
  keys.forEach(k => { localStorage.removeItem(k); sessionStorage.removeItem(k) })
}

const redirectToLogin = (reason = 'session-expired') => {
  if (typeof window === 'undefined') return
  if (window.__ARMELY_AUTH_REDIRECTING__) return

  const currentPath = String(window.location.pathname || '').toLowerCase()
  if (currentPath.endsWith('/login') || currentPath.endsWith('/admin/login')) return

  window.__ARMELY_AUTH_REDIRECTING__ = true
  window.location.replace(`${buildStoreUrl('login')}?reason=${encodeURIComponent(reason)}`)
}

// Handle response errors
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (isRetryableGetError(error) && !error.config?._retry) {
      error.config._retry = true
      return api.request(error.config)
    }

    const status = error.response?.status
    const message = error.response?.data?.message || ''
    const requestUrl = String(error.config?.url || '').toLowerCase()
    const isLogoutRequest = requestUrl.includes('/auth/logout')

    if ((status === 401 || status === 419) && !isLogoutRequest) {
      clearAuthStorage()
      redirectToLogin(status === 419 ? 'session-expired' : 'unauthorized')
    }

    if (status === 403) {
      const restrictionReason = String(error.response?.data?.data?.restriction_reason || '').toLowerCase()
      const isSuspension = restrictionReason === 'company_suspended'
        || restrictionReason === 'user_suspended'
        || message.toLowerCase().includes('suspended')
      if (isSuspension) {
        clearAuthStorage()
        redirectToLogin('suspended')
      }
    }

    return Promise.reject(error)
  }
)

export default api
