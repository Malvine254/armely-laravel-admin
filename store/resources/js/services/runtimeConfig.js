const normalizeBasePath = (value) => {
  if (!value) {
    return '/'
  }

  let basePath = value.trim()
  if (!basePath.startsWith('/')) {
    basePath = `/${basePath}`
  }

  if (!basePath.endsWith('/')) {
    basePath = `${basePath}/`
  }

  return basePath
}

const detectRuntimeBasePath = () => {
  if (typeof window === 'undefined') {
    return '/'
  }

  const pathname = window.location.pathname || '/'
  if (pathname === '/store' || pathname.startsWith('/store/')) {
    return '/store/'
  }

  return '/'
}

const configuredBasePath = import.meta.env.VITE_APP_BASE_PATH || ''

// Prefer explicit env config, then runtime URL detection (for subpath deployments),
// and finally Vite's BASE_URL fallback.
export const APP_BASE_PATH = normalizeBasePath(
  configuredBasePath || detectRuntimeBasePath() || import.meta.env.BASE_URL || '/'
)

export const buildStoreUrl = (path = '') => {
  const normalizedPath = String(path).replace(/^\/+/, '')
  return normalizedPath ? `${APP_BASE_PATH}${normalizedPath}` : APP_BASE_PATH
}

export const API_BASE_URL = (import.meta.env.VITE_API_URL || `${window.location.origin}${buildStoreUrl('api/v1')}`)
  .replace(/\/+$/, '')