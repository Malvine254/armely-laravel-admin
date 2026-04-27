import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import { API_BASE_URL } from '../services/runtimeConfig'

export const useAuthStore = defineStore('auth', () => {
  const initialToken = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token') || null
  const token = ref(initialToken)
  const user = ref(null)
  const accessRestricted = ref(localStorage.getItem('auth_restricted') === 'true')
  const forcePasswordChange = ref(localStorage.getItem('auth_force_pw') === 'true' || sessionStorage.getItem('auth_force_pw') === 'true')
  const sessionExpiry = ref(null)
  const USER_KEY = 'armely_user'
  const SESSION_EXPIRY_KEY = 'auth_session_expiry'
  const REMEMBER_KEY = 'auth_remember'
  const SESSION_TIMEOUT = 8 * 60 * 60 * 1000 // 8 hours in milliseconds
  const REMEMBER_TIMEOUT = 30 * 24 * 60 * 60 * 1000 // 30 days in milliseconds
  const rememberSession = ref(localStorage.getItem(REMEMBER_KEY) === 'true')

  const clearAuthStorage = () => {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_restricted')
    localStorage.removeItem('auth_force_pw')
    localStorage.removeItem(USER_KEY)
    localStorage.removeItem(SESSION_EXPIRY_KEY)
    localStorage.removeItem(REMEMBER_KEY)
    sessionStorage.removeItem('auth_token')
    sessionStorage.removeItem('auth_restricted')
    sessionStorage.removeItem('auth_force_pw')
    sessionStorage.removeItem(USER_KEY)
    sessionStorage.removeItem(SESSION_EXPIRY_KEY)
    sessionStorage.removeItem(REMEMBER_KEY)
  }

  const getAuthStorage = () => (rememberSession.value ? localStorage : sessionStorage)

  const loadUser = () => {
    const saved = localStorage.getItem(USER_KEY) || sessionStorage.getItem(USER_KEY)
    if (saved) {
      try {
        user.value = JSON.parse(saved)
      } catch (e) {
        console.error('Failed to load user:', e)
        user.value = null
      }
    }
  }

  const loadSessionExpiry = () => {
    const expiry = localStorage.getItem(SESSION_EXPIRY_KEY) || sessionStorage.getItem(SESSION_EXPIRY_KEY)
    if (expiry) {
      sessionExpiry.value = new Date(expiry)
      // Check if session has expired
      if (sessionExpiry.value < new Date()) {
        console.warn('Session has expired')
        logout()
      }
    }
  }

  const saveUser = () => {
    if (user.value) {
      getAuthStorage().setItem(USER_KEY, JSON.stringify(user.value))
    }
  }

  const saveToken = (newToken, remember = false) => {
    rememberSession.value = !!remember
    clearAuthStorage()

    token.value = newToken
    const storage = getAuthStorage()
    storage.setItem('auth_token', newToken)
    storage.setItem(REMEMBER_KEY, String(rememberSession.value))
    
    // Set session expiry time
    const timeout = rememberSession.value ? REMEMBER_TIMEOUT : SESSION_TIMEOUT
    const expiry = new Date(Date.now() + timeout)
    sessionExpiry.value = expiry
    storage.setItem(SESSION_EXPIRY_KEY, expiry.toISOString())
    
    axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
  }

  const setRestricted = (restricted) => {
    accessRestricted.value = !!restricted
    getAuthStorage().setItem('auth_restricted', String(!!restricted))
  }

  const login = async ({ email, password, remember = false }) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/auth/login`, { email, password })
      if (response.data?.success) {
        const payload = response.data.data
        
        // Validate required user fields
        if (!payload.user || !payload.token) {
          throw new Error('Invalid response from server')
        }
        
        user.value = payload.user
                saveToken(payload.token, remember)
                saveUser()
        setRestricted(payload.restricted)
        const forcePw = !!payload.force_password_change
        forcePasswordChange.value = forcePw
        getAuthStorage().setItem('auth_force_pw', String(forcePw))
        startStatusPolling()
        return {
          ok: true,
          user: payload.user,
          restricted: !!payload.restricted,
          forcePasswordChange: forcePw,
          message: response.data?.message,
        }
      }
      return { ok: false, message: response.data?.message || 'Login failed' }
    } catch (error) {
      console.error('Login error:', error)
      const errData = error.response?.data
      return {
        ok: false,
        message: errData?.message || error.message || 'Login failed',
        restrictionReason: errData?.data?.restriction_reason || null,
      }
    }
  }

  const register = async ({ fullName, email, companyName, password, confirmPassword }) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/auth/register`, {
        full_name: fullName,
        email,
        password,
        password_confirmation: confirmPassword,
        company_name: companyName
      })

      if (response.data?.success) {
        return {
          ok: true,
          message: response.data?.message || 'Registration successful. Please activate your account from your email.',
          activationRequired: !!response.data?.data?.activation_required,
          email: response.data?.data?.email || email,
        }
      }

      return {
        ok: false,
        message: response.data?.message || 'Registration pending approval'
      }
    } catch (error) {
      console.error('Register error:', error)
      return { ok: false, message: error.response?.data?.message || error.message || 'Registration failed' }
    }
  }

  const resendActivation = async (email) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/auth/resend-activation`, { email })
      return {
        ok: !!response.data?.success,
        message: response.data?.message || 'If the account exists, an activation email has been sent.'
      }
    } catch (error) {
      console.error('Resend activation error:', error)
      return {
        ok: false,
        message: error.response?.data?.message || error.message || 'Failed to resend activation email'
      }
    }
  }

  const forgotPassword = async (email) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/auth/forgot-password`, { email })
      return {
        ok: !!response.data?.success,
        message: response.data?.message || 'If the email exists, a password reset link has been sent.'
      }
    } catch (error) {
      console.error('Forgot password error:', error)
      return {
        ok: false,
        message: error.response?.data?.message || error.message || 'Failed to send password reset link'
      }
    }
  }

  const resetPassword = async ({ email, token, password, passwordConfirmation }) => {
    try {
      const response = await axios.post(`${API_BASE_URL}/auth/reset-password`, {
        email,
        token,
        password,
        password_confirmation: passwordConfirmation
      })

      return {
        ok: !!response.data?.success,
        message: response.data?.message || 'Password has been reset successfully'
      }
    } catch (error) {
      console.error('Reset password error:', error)
      return {
        ok: false,
        message: error.response?.data?.message || error.message || 'Failed to reset password'
      }
    }
  }

  const logout = async () => {
    stopStatusPolling()
    try {
      // Call backend logout endpoint to invalidate token
      if (token.value) {
        await axios.post(`${API_BASE_URL}/auth/logout`)
      }
    } catch (error) {
      console.warn('Logout error (continuing anyway):', error)
    } finally {
      token.value = null
      user.value = null
      setRestricted(false)
      sessionExpiry.value = null
      rememberSession.value = false
      clearAuthStorage()
      delete axios.defaults.headers.common['Authorization']
    }
  }

  const refreshUser = async () => {
    try {
      const response = await axios.get(`${API_BASE_URL}/auth/me`)
      if (response.data?.success) {
        const payload = response.data.data
        const nextUser = payload?.user || payload
        if (payload?.company && typeof nextUser === 'object') {
          nextUser.company = payload.company
          if (!nextUser.company_name && payload.company?.name) {
            nextUser.company_name = payload.company.name
          }
        }
        if (typeof nextUser === 'object' && payload?.incomplete_fields) {
          nextUser.incomplete_fields = payload.incomplete_fields
        }
        user.value = nextUser
        saveUser()
        setRestricted(!!payload?.restricted)
        return true
      }
    } catch (error) {
      console.error('Failed to refresh user:', error)
      if (error.response?.status === 401 || error.response?.status === 403) {
        await logout()
      }
    }
    return false
  }

  let _statusPollTimer = null

  const startStatusPolling = () => {
    stopStatusPolling()
    _statusPollTimer = setInterval(async () => {
      if (token.value) {
        await refreshUser()
      }
    }, 60 * 1000) // recheck every 60 seconds

    // Also recheck immediately when the tab regains focus
    document.addEventListener('visibilitychange', _onVisibilityChange)
  }

  const stopStatusPolling = () => {
    if (_statusPollTimer) {
      clearInterval(_statusPollTimer)
      _statusPollTimer = null
    }
    document.removeEventListener('visibilitychange', _onVisibilityChange)
  }

  const _onVisibilityChange = () => {
    if (document.visibilityState === 'visible' && token.value) {
      refreshUser()
    }
  }

  const isAuthenticated = computed(() => {
    if (!token.value || !user.value) return false
    if (sessionExpiry.value && new Date() > sessionExpiry.value) {
      console.warn('Session expired')
      logout()
      return false
    }
    return true
  })

  const isRestricted = computed(() => {
    if (!isAuthenticated.value) return false

    const isActivationPending = !user.value?.email_verified_at
    if (isActivationPending) {
      return true
    }

    const userStatus = user.value?.status
    const companyStatus = user.value?.company?.status

    // Prefer live user/company status to avoid stale local restriction flags.
    if (userStatus || companyStatus) {
      const userRestricted = userStatus ? userStatus !== 'active' : false
      const companyRestricted = companyStatus ? companyStatus !== 'approved' : false
      return userRestricted || companyRestricted
    }

    // Fallback only when no status payload is available.
    return accessRestricted.value
  })

  const isAdmin = computed(() => {
    return isAuthenticated.value && ['admin', 'super_admin'].includes(user.value?.role)
  })

  const isActivationPending = computed(() => {
    return isAuthenticated.value && !user.value?.email_verified_at
  })

  const isManager = computed(() => {
    return isAuthenticated.value && ['manager', 'admin', 'super_admin'].includes(user.value?.role)
  })

  const hasFeatureAccess = (feature) => {
    if (!isAuthenticated.value) return false
    
    const featureRoles = {
      'quotes': ['user', 'buyer', 'owner', 'manager', 'admin', 'super_admin'],
      'orders': ['user', 'buyer', 'owner', 'manager', 'admin', 'super_admin'],
      'invoices': ['user', 'buyer', 'owner', 'manager', 'admin', 'super_admin'],
      'messages': ['user', 'buyer', 'owner', 'manager', 'admin', 'super_admin'],
      'admin': ['admin', 'super_admin'],
      'reports': ['owner', 'manager', 'admin', 'super_admin']
    }
    
    const allowedRoles = featureRoles[feature] || ['user', 'buyer', 'owner']
    return allowedRoles.includes(user.value?.role)
  }

  const clearForcePasswordChange = () => {
    forcePasswordChange.value = false
    localStorage.removeItem('auth_force_pw')
    sessionStorage.removeItem('auth_force_pw')
  }

  const getSessionTimeRemaining = () => {
    if (!sessionExpiry.value) return null
    const now = new Date()
    const remaining = sessionExpiry.value - now
    if (remaining < 0) return 0
    return Math.floor(remaining / 1000) // Return seconds
  }

  // Initialize on store creation
  loadUser()
  loadSessionExpiry()

  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    // Verify session is still valid, then start polling
    refreshUser().then(ok => { if (ok) startStatusPolling() }).catch(() => logout())
  }

  return {
    token,
    user,
    accessRestricted,
    sessionExpiry,
    forcePasswordChange,
    isAuthenticated,
    isRestricted,
    isActivationPending,
    isAdmin,
    isManager,
    hasFeatureAccess,
    getSessionTimeRemaining,
    login,
    register,
    resendActivation,
    forgotPassword,
    resetPassword,
    logout,
    refreshUser,
    clearForcePasswordChange,
    startStatusPolling,
    stopStatusPolling,
  }
})
