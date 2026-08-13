import { trackProductViewEvent } from './behaviorTracking'

const KEY = 'armely_recently_viewed_ids'
const LIMIT = 6

export const getRecentlyViewedIds = () => {
  if (typeof window === 'undefined') return []
  try {
    const value = JSON.parse(window.localStorage.getItem(KEY) || '[]')
    return Array.isArray(value) ? value.map(String).filter(Boolean).slice(0, LIMIT) : []
  } catch {
    return []
  }
}

export const rememberViewedProduct = productId => {
  const id = String(productId || '').trim()
  if (!id || typeof window === 'undefined') return
  const ids = [id, ...getRecentlyViewedIds().filter(item => item !== id)].slice(0, LIMIT)
  try {
    window.localStorage.setItem(KEY, JSON.stringify(ids))
  } catch {
    // Recently viewed is optional and must never interrupt product loading.
  }

  trackProductViewEvent(productId)
}
