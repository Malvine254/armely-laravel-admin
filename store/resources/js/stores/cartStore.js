import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useAuthStore } from './authStore'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const GUEST_STORAGE_KEY = 'armely_cart_guest'
  const USER_STORAGE_PREFIX = 'armely_cart_user_'
  const GUEST_REVISION_KEY = 'armely_quote_revision_guest'
  const USER_REVISION_PREFIX = 'armely_quote_revision_user_'
  const authStore = useAuthStore()
  const revisionSourceQuoteId = ref(null)

  const resolveItemKey = (item) => {
    if (!item) return null
    return item.productId ?? item.id ?? item.mfgPartNo ?? item.sku ?? item.partNumber ?? null
  }

  const normalizeCartItem = (item) => {
    if (!item || typeof item !== 'object') {
      return null
    }

    const normalizedProductId = resolveItemKey(item)
    if (normalizedProductId === null || normalizedProductId === undefined || normalizedProductId === '') {
      return null
    }

    return {
      ...item,
      productId: normalizedProductId,
      quantity: Math.max(1, Number(item.quantity || 1)),
      addedAt: item.addedAt || new Date().toISOString(),
    }
  }

  const getCurrentUserId = () => {
    const user = authStore.user
    return user?.id || user?.user_id || null
  }

  const getStorageKeyForUser = (userId) => `${USER_STORAGE_PREFIX}${userId}`
  const getRevisionKeyForUser = (userId) => `${USER_REVISION_PREFIX}${userId}`

  const getCurrentStorageKey = () => {
    const userId = getCurrentUserId()
    if (authStore.isAuthenticated && userId) {
      return getStorageKeyForUser(userId)
    }
    return GUEST_STORAGE_KEY
  }

  const getCurrentRevisionKey = () => {
    const userId = getCurrentUserId()
    if (authStore.isAuthenticated && userId) {
      return getRevisionKeyForUser(userId)
    }
    return GUEST_REVISION_KEY
  }

  const readCartByKey = (storageKey) => {
    const saved = localStorage.getItem(storageKey)
    if (!saved) return []

    try {
      const parsed = JSON.parse(saved)
      return Array.isArray(parsed) ? parsed : []
    } catch (e) {
      console.error(`Failed to load cart for ${storageKey}:`, e)
      return []
    }
  }

  const writeCartByKey = (storageKey, cartItems) => {
    localStorage.setItem(storageKey, JSON.stringify(cartItems))
  }

  const loadCart = () => {
    const loaded = readCartByKey(getCurrentStorageKey())
      .map(normalizeCartItem)
      .filter(Boolean)

    items.value = loaded
  }

  const loadRevisionSource = () => {
    const saved = localStorage.getItem(getCurrentRevisionKey())
    revisionSourceQuoteId.value = saved && String(saved).trim() ? String(saved).trim() : null
  }

  // Save cart for the current auth context (guest or logged-in user)
  const saveCart = () => {
    writeCartByKey(getCurrentStorageKey(), items.value)
  }

  const saveRevisionSource = () => {
    const key = getCurrentRevisionKey()
    if (revisionSourceQuoteId.value) {
      localStorage.setItem(key, String(revisionSourceQuoteId.value))
    } else {
      localStorage.removeItem(key)
    }
  }

  const mergeGuestCartIntoCurrentUser = () => {
    const userId = getCurrentUserId()
    if (!authStore.isAuthenticated || !userId) {
      return
    }

    const userStorageKey = getStorageKeyForUser(userId)
    const guestItems = readCartByKey(GUEST_STORAGE_KEY)
    const userItems = readCartByKey(userStorageKey)

    if (guestItems.length === 0) {
      items.value = userItems
      writeCartByKey(userStorageKey, userItems)
      return
    }

    const mergedByProduct = new Map()

    userItems.forEach((item) => {
      const normalized = normalizeCartItem(item)
      if (!normalized) return
      const key = normalized.productId
      mergedByProduct.set(key, normalized)
    })

    guestItems.forEach((item) => {
      const normalized = normalizeCartItem(item)
      if (!normalized) return

      const key = normalized.productId
      const existing = mergedByProduct.get(key)
      if (existing) {
        existing.quantity = Number(existing.quantity || 0) + Number(normalized.quantity || 0)
      } else {
        mergedByProduct.set(key, normalized)
      }
    })

    const mergedItems = Array.from(mergedByProduct.values())
    items.value = mergedItems
    writeCartByKey(userStorageKey, mergedItems)
    localStorage.removeItem(GUEST_STORAGE_KEY)
  }

  let activeCartScope = 'guest'
  const syncCartScope = () => {
    const userId = getCurrentUserId()
    const nextScope = authStore.isAuthenticated && userId ? `user:${userId}` : 'guest'

    if (nextScope === activeCartScope) {
      return
    }

    const wasGuest = activeCartScope === 'guest'
    activeCartScope = nextScope

    if (wasGuest && nextScope.startsWith('user:')) {
      mergeGuestCartIntoCurrentUser()
      loadRevisionSource()
      return
    }

    loadCart()
    loadRevisionSource()
  }

  // Add item to cart
  const addItem = (product, quantity = 1) => {
    // Suspended accounts can browse but cannot perform quote/cart write actions.
    if (authStore.isAuthenticated && authStore.isRestricted) {
      return false
    }

    const availableQty = Number(
      product?.availableQuantity ??
      product?.totalQuantity ??
      product?.qty ??
      NaN
    )

    if (Number.isFinite(availableQty) && availableQty <= 0) {
      return false
    }

    const normalizedProduct = normalizeCartItem({
      ...product,
      quantity,
    })

    if (!normalizedProduct) {
      console.error('Unable to add item to cart: missing product identifier', product)
      return false
    }

    const existingItem = items.value.find(item => item.productId === normalizedProduct.productId)
    
    if (existingItem) {
      existingItem.quantity += normalizedProduct.quantity
    } else {
      items.value.push(normalizedProduct)
    }
    saveCart()
    return true
  }

  // Remove item from cart
  const removeItem = (productId) => {
    items.value = items.value.filter(item => item.productId !== productId)
    saveCart()
  }

  // Update quantity
  const updateQuantity = (productId, quantity) => {
    if (authStore.isAuthenticated && authStore.isRestricted) {
      return false
    }

    const item = items.value.find(item => item.productId === productId)
    if (item) {
      if (quantity <= 0) {
        removeItem(productId)
      } else {
        item.quantity = quantity
        saveCart()
      }
    }
    return true
  }

  // Clear cart
  const clearCart = () => {
    items.value = []
    saveCart()
    revisionSourceQuoteId.value = null
    saveRevisionSource()
  }

  const replaceCartItems = (nextItems = []) => {
    const normalizedItems = (Array.isArray(nextItems) ? nextItems : [])
      .map(normalizeCartItem)
      .filter(Boolean)
    items.value = normalizedItems
    saveCart()
  }

  const setRevisionSource = (quoteId) => {
    revisionSourceQuoteId.value = quoteId ? String(quoteId) : null
    saveRevisionSource()
  }

  const clearRevisionSource = () => {
    revisionSourceQuoteId.value = null
    saveRevisionSource()
  }

  // Computed properties
  const cartCount = computed(() => {
    return items.value.length
  })

  const cartTotal = computed(() => {
    return items.value.reduce((sum, item) => {
      const price = item.productPrice?.[0]?.rsPrice || 0
      return sum + (price * item.quantity)
    }, 0)
  })

  const isEmpty = computed(() => items.value.length === 0)

  // Load cart on store creation
  activeCartScope = authStore.isAuthenticated && getCurrentUserId() ? `user:${getCurrentUserId()}` : 'guest'
  loadCart()
  loadRevisionSource()

  watch(
    () => [authStore.isAuthenticated, getCurrentUserId()],
    () => {
      syncCartScope()
    }
  )

  return {
    items,
    revisionSourceQuoteId,
    cartCount,
    cartTotal,
    isEmpty,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    replaceCartItems,
    setRevisionSource,
    clearRevisionSource,
    mergeGuestCartIntoCurrentUser
  }
})
