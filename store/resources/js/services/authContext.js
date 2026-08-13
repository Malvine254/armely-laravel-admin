export const AUTH_CONTEXTS = {
  CUSTOMER: 'customer',
  ADMIN: 'admin',
}

const STORAGE_KEY_SETS = {
  [AUTH_CONTEXTS.CUSTOMER]: {
    token: 'auth_token',
    user: 'armely_user',
    sessionExpiry: 'auth_session_expiry',
    restricted: 'auth_restricted',
    remember: 'auth_remember',
    forcePasswordChange: 'auth_force_pw',
  },
  [AUTH_CONTEXTS.ADMIN]: {
    token: 'admin_auth_token',
    user: 'admin_user',
    sessionExpiry: 'admin_auth_session_expiry',
    restricted: 'admin_auth_restricted',
    remember: 'admin_auth_remember',
    forcePasswordChange: 'admin_auth_force_pw',
  },
}

export const isAdminPath = (path = '') => {
  const normalizedPath = String(path || '').toLowerCase()
  return normalizedPath.includes('/admin')
}

export const getAuthContextForPath = (path = '') => {
  return isAdminPath(path) ? AUTH_CONTEXTS.ADMIN : AUTH_CONTEXTS.CUSTOMER
}

export const getActiveAuthContext = () => {
  if (typeof window === 'undefined') {
    return AUTH_CONTEXTS.CUSTOMER
  }

  return getAuthContextForPath(window.location.pathname)
}

export const getAuthStorageKeys = (context = AUTH_CONTEXTS.CUSTOMER) => {
  return STORAGE_KEY_SETS[context] || STORAGE_KEY_SETS[AUTH_CONTEXTS.CUSTOMER]
}

export const readScopedStorageItem = (keyName, context = getActiveAuthContext()) => {
  const keys = getAuthStorageKeys(context)
  const key = keys[keyName]
  if (!key) return null
  return localStorage.getItem(key) || sessionStorage.getItem(key)
}

export const clearScopedAuthStorage = (context = getActiveAuthContext()) => {
  const keys = Object.values(getAuthStorageKeys(context))
  keys.forEach((key) => {
    localStorage.removeItem(key)
    sessionStorage.removeItem(key)
  })
}
