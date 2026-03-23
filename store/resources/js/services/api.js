import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1'

const api = axios.create({
  baseURL: API_BASE_URL,
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

// Handle response errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('auth_token')
      localStorage.removeItem('armely_user')
      localStorage.removeItem('auth_session_expiry')
      localStorage.removeItem('auth_restricted')
      localStorage.removeItem('auth_remember')
      sessionStorage.removeItem('auth_token')
      sessionStorage.removeItem('armely_user')
      sessionStorage.removeItem('auth_session_expiry')
      sessionStorage.removeItem('auth_restricted')
      sessionStorage.removeItem('auth_remember')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api
