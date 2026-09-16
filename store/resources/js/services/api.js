import axios from 'axios'
import { API_BASE_URL, buildStoreUrl } from './runtimeConfig'
import { AUTH_CONTEXTS, clearScopedAuthStorage, getActiveAuthContext, getAuthStorageKeys } from './authContext'

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
  const context = getActiveAuthContext()
  const tokenKey = getAuthStorageKeys(context).token
  const token = localStorage.getItem(tokenKey) || sessionStorage.getItem(tokenKey)
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  if (!config.timeout || config.timeout <= 0) {
    config.timeout = DEFAULT_API_TIMEOUT_MS
  }
  return config
})

const redirectToLogin = (reason = 'session-expired') => {
  if (typeof window === 'undefined') return
  if (window.__ARMELY_AUTH_REDIRECTING__) return

  const currentPath = String(window.location.pathname || '').toLowerCase()
  if (currentPath.endsWith('/login') || currentPath.endsWith('/admin/login')) return

  window.__ARMELY_AUTH_REDIRECTING__ = true
  const loginPath = getActiveAuthContext() === AUTH_CONTEXTS.ADMIN ? 'admin/login' : 'login'
  window.location.replace(`${buildStoreUrl(loginPath)}?reason=${encodeURIComponent(reason)}`)
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
    const context = getActiveAuthContext()

    if (status === 401 && !isLogoutRequest) {
      // A request made before login (e.g. an app-mount fetch) can still be in flight
      // when login succeeds and stores a fresh token. If that stale request's 401
      // arrives afterward, it must not wipe out the token that was just saved.
      // Only treat this as a real session expiry if it was sent with the token that
      // is still the active one right now.
      const sentAuthHeader = error.config?.headers?.Authorization || error.config?.headers?.authorization || ''
      const sentToken = String(sentAuthHeader).replace(/^Bearer\s+/i, '')
      const tokenKey = getAuthStorageKeys(context).token
      const currentToken = localStorage.getItem(tokenKey) || sessionStorage.getItem(tokenKey) || ''

      if (sentToken && sentToken === currentToken) {
        clearScopedAuthStorage(context)
        redirectToLogin('unauthorized')
      }
    }

    if (status === 403) {
      const restrictionReason = String(error.response?.data?.data?.restriction_reason || '').toLowerCase()
      const isSuspension = restrictionReason === 'company_suspended'
        || restrictionReason === 'user_suspended'
        || message.toLowerCase().includes('suspended')
      if (isSuspension) {
        clearScopedAuthStorage(context)
        redirectToLogin('suspended')
      }
    }

    return Promise.reject(error)
  }
)

export default api
