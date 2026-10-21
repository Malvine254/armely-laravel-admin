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

const serverConfig = typeof window !== 'undefined' ? window.storeRuntimeConfig : null
const runtimeEnv = serverConfig || import.meta.env || {}
const configuredBasePath = runtimeEnv.VITE_APP_BASE_PATH || ''

export const APP_BASE_PATH = normalizeBasePath(
  (serverConfig ? configuredBasePath : '') || detectRuntimeBasePath() || configuredBasePath || runtimeEnv.BASE_URL || '/'
)

export const buildStoreUrl = (path = '') => {
  const normalizedPath = String(path).replace(/^\/+/, '')
  return normalizedPath ? `${APP_BASE_PATH}${normalizedPath}` : APP_BASE_PATH
}

export const normalizeLocalAssetUrl = (value) => {
  const rawValue = String(value || '').trim()
  if (!rawValue || typeof window === 'undefined') {
    return rawValue
  }

  try {
    const currentHost = (window.location.hostname || '').toLowerCase()
    const localHosts = ['127.0.0.1', 'localhost']
    const isLocalHost = localHosts.includes(currentHost)

    // Prefix root-relative project assets when the app is mounted under a
    // subpath (including local /store bridge mode), while avoiding
    // double-prefixing URLs that already contain APP_BASE_PATH.
    if (
      rawValue.startsWith('/')
      && !rawValue.startsWith('//')
      // Profile uploads are exposed by the host application's root storage
      // mount. Product images are handled separately by resolveProductImageUrl.
      && !rawValue.startsWith('/storage/')
      && APP_BASE_PATH !== '/'
      && !rawValue.startsWith(APP_BASE_PATH)
    ) {
      return buildStoreUrl(rawValue)
    }

    const assetUrl = new URL(rawValue, window.location.origin)
    const assetHost = assetUrl.hostname.toLowerCase()

    // Older API payloads may contain /store/storage/* even though the shared
    // public-storage mount lives at /storage/*. Repair those URLs at runtime.
    const mountedStoragePrefix = `${APP_BASE_PATH}storage/`.replace(/\/+/g, '/')
    if (
      !isLocalHost
      && assetHost === currentHost
      && APP_BASE_PATH !== '/'
      && assetUrl.pathname.startsWith(mountedStoragePrefix)
    ) {
      const rootStoragePath = assetUrl.pathname.slice(APP_BASE_PATH.length - 1)
      return `${window.location.origin}${rootStoragePath}${assetUrl.search}`
    }

    if (
      isLocalHost &&
      localHosts.includes(assetHost)
    ) {
      // In local bridge mode, backend may return absolute URLs rooted at
      // /storage/*; remap them to /store/storage/* so they resolve correctly.
      if (
        APP_BASE_PATH !== '/' &&
        assetUrl.pathname.startsWith('/storage/') &&
        !assetUrl.pathname.startsWith(APP_BASE_PATH)
      ) {
        const prefixedPath = `${APP_BASE_PATH}${assetUrl.pathname.replace(/^\/+/, '')}`
        return `${window.location.protocol}//${window.location.host}${prefixedPath}${assetUrl.search}`
      }

      // Keep local development assets on the active origin, even when stale
      // storage contains a different localhost port from a prior session.
      return `${window.location.protocol}//${window.location.host}${assetUrl.pathname}${assetUrl.search}`
    }

    return rawValue
  } catch (error) {
    return rawValue
  }
}

export const resolveProfilePictureUrl = (profilePictureUrl, profilePicturePath = null) => {
  const primaryUrl = String(profilePictureUrl || '').trim()
  if (primaryUrl) {
    return normalizeLocalAssetUrl(primaryUrl)
  }

  const rawPath = String(profilePicturePath || '').trim()
  if (!rawPath) {
    return null
  }

  let normalized = rawPath.replace(/\\/g, '/').trim()

  if (/^https?:\/\//i.test(normalized)) {
    return normalizeLocalAssetUrl(normalized)
  }

  normalized = normalized.replace(/^\/+/, '')

  if (normalized.includes('/storage/')) {
    normalized = normalized.slice(normalized.indexOf('/storage/') + '/storage/'.length)
  }

  if (normalized.startsWith('public/')) {
    normalized = normalized.slice('public/'.length)
  }

  if (normalized.startsWith('storage/')) {
    return normalizeLocalAssetUrl(`/${normalized}`)
  }

  return normalizeLocalAssetUrl(`/storage/${normalized}`)
}

/** Resolve locally stored product images without leaking production URLs into local development. */
export const resolveProductImageUrl = (value) => {
  let rawValue = String(value || '').trim()
  if (!rawValue || typeof window === 'undefined') return rawValue

  if (!/^https?:\/\//i.test(rawValue) && !rawValue.startsWith('/')) {
    if (rawValue.startsWith('images/') || rawValue.startsWith('store/images/') || rawValue.startsWith('storage/')) {
      rawValue = `/${rawValue}`
    }
  }

  try {
    const current = window.location
    const currentHost = current.hostname.toLowerCase()
    const isLocal = currentHost === '127.0.0.1' || currentHost === 'localhost'
    const parsed = new URL(rawValue, current.origin)
    const projectHosts = [currentHost, 'localhost', '127.0.0.1', 'armely.com', 'www.armely.com']
    if (!projectHosts.includes(parsed.hostname.toLowerCase())) return rawValue
    const storeImageMarker = '/store/images/products/'
    const rootImageMarker = '/images/products/'
    let productPath = ''

    if (parsed.pathname.includes(storeImageMarker)) {
      productPath = parsed.pathname.slice(parsed.pathname.indexOf(storeImageMarker) + '/store'.length)
    } else if (parsed.pathname.includes(rootImageMarker)) {
      productPath = parsed.pathname.slice(parsed.pathname.indexOf(rootImageMarker))
    }

    // External supplier/CDN images are intentionally left untouched.
    if (!productPath) return normalizeLocalAssetUrl(rawValue)

    if (isLocal) {
      const normalizedProductPath = APP_BASE_PATH !== '/'
        ? buildStoreUrl(productPath)
        : productPath
      return `${current.origin}${normalizedProductPath}${parsed.search}`
    }

    // Preserve already-absolute production store URLs returned by the API.
    if (/^https?:\/\//i.test(rawValue) && parsed.pathname.includes(storeImageMarker)) {
      return rawValue
    }

    return `${current.origin}${buildStoreUrl(productPath)}${parsed.search}`
  } catch {
    return rawValue
  }
}

export const getProductImageUrls = (product) => {
  const values = [
    ...(Array.isArray(product?.productImages) ? product.productImages : []),
    ...(Array.isArray(product?.images) ? product.images : []),
    product?.imageUrl,
    product?.image_url,
    product?.thumbnailUrl,
    product?.thumbnail,
  ]
  const urls = values.map(value => resolveProductImageUrl(
    typeof value === 'string'
      ? value
      : value?.imageUrl || value?.imageURL || value?.image_url || value?.imagePath || value?.url || value?.thumbnailUrl || ''
  )).filter(Boolean)
  const isLocalProductImage = url => url.includes('/images/products/') && (
    url.startsWith('/')
    || (typeof window !== 'undefined' && url.startsWith(`${window.location.origin}/`))
  )
  return [...new Set(urls)].sort((left, right) =>
    Number(isLocalProductImage(right)) - Number(isLocalProductImage(left))
  )
}

const detectRuntimeApiBaseUrl = () => {
  if (typeof window === 'undefined') {
    return '/api/v1'
  }

  const { origin } = window.location
  const basePath = APP_BASE_PATH

  if (basePath !== '/') {
    return `${origin}${basePath}api/v1`
  }

  return `${origin}/api/v1`
}

const shouldUseConfiguredApiBaseUrl = (value) => {
  const configured = String(value || '').trim()
  if (!configured) {
    return false
  }

  // Reject path-like values missing a leading slash (for example
  // `store/api/v1`), because Axios would resolve them relative to the current
  // page and produce routes such as /store/login/store/api/v1.
  if (!configured.startsWith('/') && !/^https?:\/\//i.test(configured)) {
    return false
  }

  if (typeof window === 'undefined') {
    return true
  }

  const hostname = (window.location.hostname || '').toLowerCase()
  const isLocalHost = hostname === '127.0.0.1' || hostname === 'localhost'
  const isConfiguredLocalHost = /^https?:\/\/(127\.0\.0\.1|localhost)(:\d+)?(\/|$)/i.test(configured)
  const configuredPath = (() => {
    try {
      return new URL(configured, window.location.origin).pathname || ''
    } catch {
      return ''
    }
  })()

  // Avoid sending local development traffic to a remote build-time API.
  if (!serverConfig && isLocalHost && /^https?:\/\//i.test(configured) && !isConfiguredLocalHost) {
    return false
  }

  // On real domains, never let a locally baked Vite URL send browser traffic
  // back to the visitor's own machine.
  if (!isLocalHost && isConfiguredLocalHost) {
    return false
  }

  // Whenever the app is mounted below a subpath, force runtime API detection
  // unless the configured URL includes that same subpath. This prevents a
  // bundle built locally with /api/v1 from calling the main site's API after
  // it is deployed at /store.
  if (
    APP_BASE_PATH !== '/'
    && /\/api\/v1$/i.test(configuredPath)
    && !configuredPath.startsWith(`${APP_BASE_PATH}api/`)
  ) {
    return false
  }

  return true
}

const normalizeApiBaseUrl = (value) => {
  const normalized = String(value || '')
    .trim()
    .replace(/([^:]\/)\/+/g, '$1')
    .replace(/\/+$/, '')

  // Guard against misconfigured env values that point to a specific endpoint
  // instead of the API base, e.g. /api/v1/products.
  return normalized.replace(/\/api\/v1\/products$/i, '/api/v1')
}

export const API_BASE_URL = normalizeApiBaseUrl(
  shouldUseConfiguredApiBaseUrl(runtimeEnv.VITE_API_URL)
    ? runtimeEnv.VITE_API_URL
    : detectRuntimeApiBaseUrl()
)
