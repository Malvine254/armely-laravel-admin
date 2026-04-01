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
  if (pathname === '/store/public' || pathname.startsWith('/store/public/')) {
    return '/store/public/'
  }

  if (pathname === '/store' || pathname.startsWith('/store/')) {
    return '/store/'
  }

  // Support nested local paths like /armely-laravel-admin/store/public/... in XAMPP.
  const publicMarker = '/store/public/'
  const publicIndex = pathname.indexOf(publicMarker)
  if (publicIndex >= 0) {
    return pathname.slice(0, publicIndex + publicMarker.length)
  }

  const storeMarker = '/store/'
  const storeIndex = pathname.indexOf(storeMarker)
  if (storeIndex >= 0) {
    return pathname.slice(0, storeIndex + storeMarker.length)
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

const detectRuntimeApiBaseUrl = () => {
  if (typeof window === 'undefined') {
    return '/api/v1'
  }

  const { origin, hostname, port } = window.location
  const basePath = APP_BASE_PATH

  // Local dev: root app runs on :8000 while store API is served on :8001.
  if ((hostname === '127.0.0.1' || hostname === 'localhost') && port === '8000') {
    return `http://${hostname}:8001/api/v1`
  }

  // Local store app served directly on :8001 keeps API at root /api/v1.
  if ((hostname === '127.0.0.1' || hostname === 'localhost') && port === '8001') {
    return `${origin}/api/v1`
  }

  if (basePath !== '/') {
    return `${origin}${basePath}api/v1`
  }

  return `${origin}/api/v1`
}

export const API_BASE_URL = (import.meta.env.VITE_API_URL || detectRuntimeApiBaseUrl())
  .replace(/\/+$/, '')