import api from './api'

const postSafe = async (url, payload) => {
  try {
    await api.post(url, payload)
  } catch {
    // Tracking is best-effort and must never interrupt UX.
  }
}

const normalizeProductId = (value) => {
  const numeric = Number(value)
  if (!Number.isFinite(numeric) || numeric <= 0) return null
  return Math.trunc(numeric)
}

export const trackProductViewEvent = (productId) => {
  const normalized = normalizeProductId(productId)
  if (!normalized) return

  void postSafe('/behavior/product-view', {
    product_id: normalized,
    viewed_at: new Date().toISOString(),
  })
}

export const trackCartEvent = ({ eventType, productId = null, quantity = null, metadata = null }) => {
  if (!eventType) return

  const payload = {
    event_type: eventType,
    event_at: new Date().toISOString(),
  }

  const normalizedProductId = normalizeProductId(productId)
  if (normalizedProductId) payload.product_id = normalizedProductId

  const normalizedQuantity = Number(quantity)
  if (Number.isFinite(normalizedQuantity) && normalizedQuantity > 0) {
    payload.quantity = Math.trunc(normalizedQuantity)
  }

  if (metadata && typeof metadata === 'object') {
    payload.metadata = metadata
  }

  void postSafe('/behavior/cart-event', payload)
}

export const syncCartSnapshot = (items = []) => {
  const normalizedItems = (Array.isArray(items) ? items : [])
    .map((item) => {
      const productId = normalizeProductId(item?.productId ?? item?.id)
      const quantity = Number(item?.quantity ?? 1)

      if (!productId || !Number.isFinite(quantity) || quantity <= 0) {
        return null
      }

      return {
        productId,
        quantity: Math.trunc(quantity),
      }
    })
    .filter(Boolean)

  void postSafe('/behavior/cart-snapshot', {
    items: normalizedItems,
  })
}

export const trackFavoriteEvent = ({ productId, eventType, metadata = null }) => {
  const normalized = normalizeProductId(productId)
  if (!normalized || !eventType) return

  const payload = {
    product_id: normalized,
    event_type: eventType,
    event_at: new Date().toISOString(),
  }

  if (metadata && typeof metadata === 'object') {
    payload.metadata = metadata
  }

  void postSafe('/behavior/favorite-event', payload)
}
