import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useAuthStore } from './authStore'
import { trackCartEvent, getBehaviorVisitorToken } from '../services/behaviorTracking'
import { isSupplierOrderable } from '../services/productAvailability'
import api from '../services/api'
import { useToastStore } from './toastStore'
import { cartLines, cartResponse, mergeCartItems, readGuestCartCookie, writeGuestCartCookie, clearGuestCartCookie } from '../services/cartPersistence'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const GUEST_STORAGE_KEY = 'armely_cart_guest'
  const USER_STORAGE_PREFIX = 'armely_cart_user_'
  const GUEST_REVISION_KEY = 'armely_quote_revision_guest'
  const USER_REVISION_PREFIX = 'armely_quote_revision_user_'
  const authStore = useAuthStore()
  const revisionSourceQuoteId = ref(null)
  const cartLoading = ref(false)
  const cartSyncing = ref(false)
  const cartSyncError = ref('')
  const cartConflict = ref(false)
  const toastStore = useToastStore()
  let revision = null
  let scopeVersion = 0
  let editVersion = 0
  let saveQueue = Promise.resolve()

  const reportSyncError = error => {
    console.error('Cart synchronization failed:', error)
    cartConflict.value = error.response?.status === 409
    cartSyncError.value = cartConflict.value
      ? 'Your cart changed on another device. Choose which cart to keep.'
      : 'Your cart is kept on this device, but could not be saved to your account. Please retry.'
    toastStore.addToast(cartSyncError.value, 'error')
  }

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
      quantity: Math.max(1, Math.min(9999, Math.trunc(Number(item.quantity) || 1))),
      addedAt: item.addedAt || new Date().toISOString(),
    }
  }

  const getCurrentUserId = () => {
    const user = authStore.user
    return user?.id || user?.user_id || null
  }

  const getStorageKeyForUser = (userId) => `${USER_STORAGE_PREFIX}${userId}`
  const getRevisionKeyForUser = (userId) => `${USER_REVISION_PREFIX}${userId}`

  const isSuspendedAccount = () => {
    const reason = String(authStore.user?.restriction_reason || '').toLowerCase()
    if (reason === 'company_suspended' || reason === 'user_suspended') {
      return true
    }

    const userStatus = String(authStore.user?.status || '').toLowerCase()
    const companyStatus = String(authStore.user?.company?.status || '').toLowerCase()
    return userStatus === 'suspended' || companyStatus === 'inactive'
  }

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
    try {
      const saved = localStorage.getItem(storageKey)
      if (!saved) return []
      const parsed = JSON.parse(saved)
      return Array.isArray(parsed) ? parsed : []
    } catch (e) {
      console.error(`Failed to load cart for ${storageKey}:`, e)
      return []
    }
  }

  const readSyncState = key => {
    try {
      return JSON.parse(localStorage.getItem(`${key}_sync`) || 'null')
    } catch (error) {
      console.error('Could not read cart sync state:', error)
      toastStore.addToast('Your saved cart state could not be read. Account cart restoration will be retried.', 'error')
      return null
    }
  }

  const writeSyncState = (key, dirty) => {
    try {
      localStorage.setItem(`${key}_sync`, JSON.stringify({ revision, dirty }))
    } catch (error) {
      console.error('Could not cache cart sync state:', error)
      toastStore.addToast('Browser storage is unavailable. Keep this page open until your cart is saved.', 'error')
    }
  }

  const writeCartByKey = (storageKey, cartItems) => {
    try {
      localStorage.setItem(storageKey, JSON.stringify(cartItems))
    } catch (error) {
      console.error('Could not cache cart on this device:', error)
      toastStore.addToast('Browser storage is unavailable. Keep this page open until your cart is saved.', 'error')
    }
  }

  const loadCart = () => {
    let source = readCartByKey(getCurrentStorageKey())
    if (!authStore.isAuthenticated) {
      try {
        const cookieItems = readGuestCartCookie()
        if (cookieItems) source = cookieItems.map(line => ({
          ...source.find(item => String(item.productId) === String(line.productId)),
          ...line,
          productName: source.find(item => String(item.productId) === String(line.productId))?.productName || `Product ${line.productId}`,
        }))
      } catch (error) {
        toastStore.addToast('Your guest cart cookie could not be restored. Using the backup on this device.', 'error')
      }

    }
    const loaded = source
      .map(normalizeCartItem)
      .filter(Boolean)

    items.value = loaded
  }

  const readGuestItems = () => {
    try {
      return readGuestCartCookie() || readCartByKey(GUEST_STORAGE_KEY)
    } catch (error) {
      console.error('Could not restore guest cookies during sign-in:', error)
      toastStore.addToast('Your guest cart cookie could not be read. Merging the backup stored on this device.', 'error')
      return readCartByKey(GUEST_STORAGE_KEY)
    }
  }

  const loadRevisionSource = () => {
    try {
      const saved = localStorage.getItem(getCurrentRevisionKey())
      revisionSourceQuoteId.value = saved && String(saved).trim() ? String(saved).trim() : null
    } catch (error) {
      console.error('Could not restore quote revision:', error)
      toastStore.addToast('Your quote revision could not be restored from this browser.', 'error')
    }
  }

  // Save cart for the current auth context (guest or logged-in user)
  const saveCart = () => {
    editVersion++
    writeCartByKey(getCurrentStorageKey(), items.value)
    if (!authStore.isAuthenticated) {
      try { writeGuestCartCookie(items.value) } catch (error) {
        console.error('Could not save guest cart:', error)
        toastStore.addToast('Your cart could not be saved in cookies. Please enable cookies or sign in.', 'error')
      }
      return
    }
    const key = getCurrentStorageKey()
    writeSyncState(key, true)
    const version = scopeVersion
    const edits = editVersion
    const lines = cartLines(items.value)
    saveQueue = saveQueue.then(async () => {
      if (version !== scopeVersion || revision === null || cartConflict.value) return
      cartSyncing.value = true
      try {
        const data = cartResponse(await api.put('/cart', { items: lines, revision }))
        if (version !== scopeVersion) return
        revision = data.revision
        if (edits === editVersion) {
          items.value = data.items
          writeCartByKey(key, data.items)
        }
        writeSyncState(key, edits !== editVersion)
        cartSyncError.value = ''
      } catch (error) {
        if (version === scopeVersion) reportSyncError(error)
      } finally {
        if (version === scopeVersion) cartSyncing.value = false
      }
    })
  }

  const saveRevisionSource = () => {
    const key = getCurrentRevisionKey()
    try {
      if (revisionSourceQuoteId.value) {
        localStorage.setItem(key, String(revisionSourceQuoteId.value))
      } else {
        localStorage.removeItem(key)
      }
    } catch (error) {
      console.error('Could not save quote revision:', error)
      toastStore.addToast('Your quote revision could not be saved on this device.', 'error')
    }
  }

  const mergeGuestCartIntoCurrentUser = async () => {
    const userId = getCurrentUserId()
    if (!authStore.isAuthenticated || !userId) {
      return
    }
    if (cartLoading.value) return

    const version = scopeVersion
    const key = getStorageKeyForUser(userId)
    cartLoading.value = true
    cartSyncError.value = ''
    cartConflict.value = false
    try {
      await saveQueue
      if (version !== scopeVersion) return
      const cached = readCartByKey(key)
      const savedState = readSyncState(key)
      const guestItems = readGuestItems()
      items.value = mergeCartItems(guestItems, cached)
      let data = cartResponse(await api.get('/cart'))
      if (version !== scopeVersion) return
      if (savedState?.dirty && savedState.revision !== null) {
        if (savedState.revision !== data.revision) {
          const error = new Error('Saved cart revision conflict')
          error.response = { status: 409 }
          revision = data.revision
          items.value = cached
          throw error
        }
        data = cartResponse(await api.put('/cart', { items: cartLines(cached), revision: data.revision }))
        if (version !== scopeVersion) return
      }
      const mergeItems = mergeCartItems(guestItems, !savedState || (savedState.dirty && savedState.revision === null) ? cached : [])
      data = cartResponse(await api.post('/cart/merge', { items: cartLines(mergeItems), visitor_token: getBehaviorVisitorToken() }))
      if (version !== scopeVersion) return
      items.value = data.items
      revision = data.revision
      writeCartByKey(key, data.items)
      writeSyncState(key, false)
      clearGuestCartCookie()
      localStorage.removeItem(GUEST_STORAGE_KEY)
      if (guestItems.length) trackCartEvent({ eventType: 'merge_guest', metadata: { itemCount: data.items.length } })
    } catch (error) {
      if (version === scopeVersion) reportSyncError(error)
    } finally {
      if (version === scopeVersion) cartLoading.value = false
    }
  }

  const resolveCartConflict = async keepLocal => {
    const version = scopeVersion
    cartLoading.value = true
    try {
      await saveQueue
      if (version !== scopeVersion) return
      const latest = cartResponse(await api.get('/cart'))
      if (version !== scopeVersion) return
      let data = keepLocal ? cartResponse(await api.put('/cart', { items: cartLines(items.value), revision: latest.revision })) : latest
      if (version !== scopeVersion) return
      const guest = readGuestItems()
      data = cartResponse(await api.post('/cart/merge', { items: cartLines(guest), visitor_token: getBehaviorVisitorToken() }))
      if (version !== scopeVersion) return
      items.value = data.items
      revision = data.revision
      writeCartByKey(getCurrentStorageKey(), data.items)
      writeSyncState(getCurrentStorageKey(), false)
      clearGuestCartCookie()
      localStorage.removeItem(GUEST_STORAGE_KEY)
      cartConflict.value = false
      cartSyncError.value = ''
    } catch (error) {
      if (version === scopeVersion) reportSyncError(error)
    } finally {
      if (version === scopeVersion) cartLoading.value = false
    }
  }

  let activeCartScope = 'guest'
  const syncCartScope = () => {
    const userId = getCurrentUserId()
    const nextScope = authStore.isAuthenticated && userId ? `user:${userId}` : 'guest'

    if (nextScope === activeCartScope) {
      return
    }

    scopeVersion++
    revision = null
    cartLoading.value = false
    cartSyncing.value = false
    cartSyncError.value = ''
    cartConflict.value = false
    activeCartScope = nextScope

    if (nextScope.startsWith('user:')) {
      loadCart()
      mergeGuestCartIntoCurrentUser()
      loadRevisionSource()
      return
    }

    loadCart()
    loadRevisionSource()
    void refreshGuestProducts()
  }

  const refreshGuestProducts = async () => {
    const version = scopeVersion
    const originalItems = [...items.value]
    const results = await Promise.allSettled(originalItems.map(async item => {
      if (Array.isArray(item.productPrice) && item.productPrice.length) return item
      const response = await api.get(`/products/${encodeURIComponent(item.productId)}`)
      if (!response.data?.data?.productId) throw new Error('Could not restore guest product details')
      return { ...response.data.data, productId: item.productId, quantity: item.quantity }
    }))
    if (version !== scopeVersion) return
    const refreshed = new Map(results.map((result, index) => {
      if (result.status === 'fulfilled') return [String(originalItems[index].productId), result.value]
      console.warn('Could not refresh guest cart product:', result.reason)
      return [String(originalItems[index].productId), { ...originalItems[index], isAvailable: false }]
    }))
    if (results.some(result => result.status === 'rejected')) toastStore.addToast('Some saved product details could not be refreshed. Please reopen those products before requesting a quote.', 'error')
    items.value = items.value.map(item => ({ ...item, ...refreshed.get(String(item.productId)), quantity: item.quantity }))
    writeCartByKey(GUEST_STORAGE_KEY, items.value)
  }

  const canEditCart = () => {
    if (cartLoading.value) {
      toastStore.addToast('Restoring your saved cart. Please try again in a moment.', 'info')
      return false
    }
    return !(authStore.isAuthenticated && isSuspendedAccount())
  }

  // Add item to cart
  const addItem = (product, quantity = 1) => {
    // Only suspended users are blocked from cart writes.
    if (!canEditCart()) {
      return false
    }

    const availableQty = Number(
      product?.availableQuantity ??
      product?.totalQuantity ??
      product?.qty ??
      NaN
    )

    if (Number.isFinite(availableQty) && availableQty <= 0 && !isSupplierOrderable(product)) {
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

    const existingItem = items.value.find(item => String(item.productId) === String(normalizedProduct.productId))
    
    if (existingItem) {
      existingItem.quantity = Math.min(9999, existingItem.quantity + normalizedProduct.quantity)
    } else {
      items.value.push(normalizedProduct)
    }
    saveCart()
    trackCartEvent({
      eventType: 'add',
      productId: Number(normalizedProduct.productId),
      quantity: normalizedProduct.quantity,
      metadata: { cartCount: items.value.length },
    })
    return true
  }

  // Remove item from cart
  const removeItem = (productId) => {
    if (!canEditCart()) return false
    items.value = items.value.filter(item => String(item.productId) !== String(productId))
    saveCart()
    trackCartEvent({ eventType: 'remove', productId: Number(productId), metadata: { cartCount: items.value.length } })
  }

  // Update quantity
  const updateQuantity = (productId, quantity) => {
    if (!canEditCart()) {
      return false
    }

    const item = items.value.find(item => String(item.productId) === String(productId))
    if (item) {
      if (quantity <= 0) {
        removeItem(productId)
      } else {
        item.quantity = Math.max(1, Math.min(9999, Math.trunc(Number(quantity) || 1)))
        saveCart()
        trackCartEvent({ eventType: 'update', productId: Number(productId), quantity, metadata: { cartCount: items.value.length } })
      }
    }
    return true
  }

  // Clear cart
  const clearCart = () => {
    if (!canEditCart()) return false
    items.value = []
    saveCart()
    revisionSourceQuoteId.value = null
    saveRevisionSource()
    trackCartEvent({ eventType: 'clear' })
  }

  const replaceCartItems = (nextItems = []) => {
    if (!canEditCart()) return false
    const normalizedItems = (Array.isArray(nextItems) ? nextItems : [])
      .map(normalizeCartItem)
      .filter(Boolean)
    items.value = normalizedItems
    saveCart()
    trackCartEvent({ eventType: 'replace', metadata: { itemCount: normalizedItems.length } })
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
  if (activeCartScope.startsWith('user:')) void mergeGuestCartIntoCurrentUser()
  else void refreshGuestProducts()

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
    cartLoading,
    cartSyncing,
    cartSyncError,
    cartConflict,
    retryCartSync: mergeGuestCartIntoCurrentUser,
    resolveCartConflict,
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
