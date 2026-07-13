import axios from 'axios'
import { API_BASE_URL, buildStoreUrl } from './runtimeConfig'

const api = axios.create({
  baseURL: API_BASE_URL,
  // Never leave the UI waiting indefinitely for a blocked PHP/DB worker.
  timeout: 20000,
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
  return config
})

const clearAuthStorage = () => {
  const keys = ['auth_token', 'armely_user', 'auth_session_expiry', 'auth_restricted', 'auth_remember', 'auth_force_pw']
  keys.forEach(k => { localStorage.removeItem(k); sessionStorage.removeItem(k) })
}

// Handle response errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status
    const message = error.response?.data?.message || ''

    if (status === 401) {
      clearAuthStorage()
      window.location.href = buildStoreUrl('login')
    }

    if (status === 403) {
      const isSuspension = message.toLowerCase().includes('suspend')
        || message.toLowerCase().includes('access is blocked')
        || message.toLowerCase().includes('pending approval')
      if (isSuspension) {
        clearAuthStorage()
        window.location.href = buildStoreUrl('login') + '?reason=suspended'
      }
    }

    return Promise.reject(error)
  }
)

export default api
