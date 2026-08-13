import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useAuthStore } from './authStore'
import { trackFavoriteEvent } from '../services/behaviorTracking'

export const useFavoritesStore = defineStore('favorites', () => {
  const items = ref([])
  const STORAGE_KEY = 'armely_favorites'
  const authStore = useAuthStore()

  const isSuspendedAccount = () => {
    const reason = String(authStore.user?.restriction_reason || '').toLowerCase()
    if (reason === 'company_suspended' || reason === 'user_suspended') {
      return true
    }

    const userStatus = String(authStore.user?.status || '').toLowerCase()
    const companyStatus = String(authStore.user?.company?.status || '').toLowerCase()
    return userStatus === 'suspended' || companyStatus === 'inactive'
  }

  const resolveImageUrl = (product) => {
    const direct = [
      product?.favoriteImageUrl,
      product?.imageUrl,
      product?.imageURL,
      product?.image_url,
      product?.heroImageUrl,
      product?.primaryImageUrl,
      product?.productImage,
      product?.thumbnailUrl,
      product?.image,
      product?.thumbnail,
    ]

    for (const value of direct) {
      const url = String(value || '').trim()
      if (url.length > 0) return url
    }

    const images = Array.isArray(product?.productImages) ? product.productImages : []
    for (const image of images) {
      if (typeof image === 'string') {
        const url = image.trim()
        if (url.length > 0) return url
        continue
      }

      const url = String(
        image?.imageUrl
        || image?.imageURL
        || image?.image_url
        || image?.url
        || image?.source
        || image?.thumbnailUrl
        || ''
      ).trim()
      if (url.length > 0) return url
    }

    return ''
  }

  const normalizeProductForFavorite = (product) => {
    const imageUrl = resolveImageUrl(product)
    const existingImages = Array.isArray(product?.productImages) ? [...product.productImages] : []
    const productImages = existingImages.length > 0
      ? existingImages
      : (imageUrl ? [{ imageUrl }] : [])

    return {
      ...product,
      favoriteImageUrl: imageUrl,
      productImages,
    }
  }

  // Load favorites from localStorage on init
  const loadFavorites = () => {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved) {
      try {
        const parsed = JSON.parse(saved)
        items.value = Array.isArray(parsed)
          ? parsed.map((item) => normalizeProductForFavorite(item))
          : []
      } catch (e) {
        console.error('Failed to load favorites:', e)
        items.value = []
      }
    }
  }

  // Save favorites to localStorage
  const saveFavorites = () => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items.value))
  }

  // Add item to favorites
  const addItem = (product) => {
    if (authStore.isAuthenticated && isSuspendedAccount()) {
      return null
    }

    const exists = items.value.some(item => item.productId === product.productId)
    
    if (!exists) {
      items.value.push({
        ...normalizeProductForFavorite(product),
        addedAt: new Date().toISOString()
      })
      saveFavorites()
      trackFavoriteEvent({
        productId: Number(product.productId),
        eventType: 'add',
        metadata: { favoriteCount: items.value.length },
      })
      return true
    }
    return false
  }

  // Remove item from favorites
  const removeItem = (productId) => {
    if (authStore.isAuthenticated && isSuspendedAccount()) {
      return false
    }

    items.value = items.value.filter(item => item.productId !== productId)
    saveFavorites()
    trackFavoriteEvent({
      productId: Number(productId),
      eventType: 'remove',
      metadata: { favoriteCount: items.value.length },
    })
    return true
  }

  // Toggle favorite
  const toggleFavorite = (product) => {
    if (authStore.isAuthenticated && isSuspendedAccount()) {
      return null
    }

    const exists = items.value.some(item => item.productId === product.productId)
    if (exists) {
      removeItem(product.productId)
      trackFavoriteEvent({ productId: Number(product.productId), eventType: 'toggle', metadata: { state: 'off' } })
      return false
    } else {
      addItem(product)
      trackFavoriteEvent({ productId: Number(product.productId), eventType: 'toggle', metadata: { state: 'on' } })
      return true
    }
  }

  // Check if product is favorite
  const isFavorite = (productId) => {
    return items.value.some(item => item.productId === productId)
  }

  // Clear favorites
  const clearFavorites = () => {
    if (authStore.isAuthenticated && isSuspendedAccount()) {
      return false
    }

    items.value = []
    saveFavorites()
    return true
  }

  // Computed properties
  const favoriteCount = computed(() => items.value.length)

  const isEmpty = computed(() => items.value.length === 0)

  // Load favorites on store creation
  loadFavorites()

  return {
    items,
    favoriteCount,
    isEmpty,
    addItem,
    removeItem,
    toggleFavorite,
    isFavorite,
    clearFavorites
  }
})
