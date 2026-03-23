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

export const APP_BASE_PATH = normalizeBasePath(import.meta.env.BASE_URL || import.meta.env.VITE_APP_BASE_PATH || '/')

export const buildStoreUrl = (path = '') => {
  const normalizedPath = String(path).replace(/^\/+/, '')
  return normalizedPath ? `${APP_BASE_PATH}${normalizedPath}` : APP_BASE_PATH
}

export const API_BASE_URL = (import.meta.env.VITE_API_URL || `${window.location.origin}${buildStoreUrl('api/v1')}`)
  .replace(/\/+$/, '')