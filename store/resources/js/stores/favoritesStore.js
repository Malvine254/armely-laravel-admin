import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useAuthStore } from './authStore'

export const useFavoritesStore = defineStore('favorites', () => {
  const items = ref([])
  const STORAGE_KEY = 'armely_favorites'
  const authStore = useAuthStore()

  // Load favorites from localStorage on init
  const loadFavorites = () => {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved) {
      try {
        items.value = JSON.parse(saved)
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
    if (authStore.isAuthenticated && authStore.isRestricted) {
      return null
    }

    const exists = items.value.some(item => item.productId === product.productId)
    
    if (!exists) {
      items.value.push({
        ...product,
        addedAt: new Date().toISOString()
      })
      saveFavorites()
      return true
    }
    return false
  }

  // Remove item from favorites
  const removeItem = (productId) => {
    if (authStore.isAuthenticated && authStore.isRestricted) {
      return false
    }

    items.value = items.value.filter(item => item.productId !== productId)
    saveFavorites()
    return true
  }

  // Toggle favorite
  const toggleFavorite = (product) => {
    if (authStore.isAuthenticated && authStore.isRestricted) {
      return null
    }

    const exists = items.value.some(item => item.productId === product.productId)
    if (exists) {
      removeItem(product.productId)
      return false
    } else {
      addItem(product)
      return true
    }
  }

  // Check if product is favorite
  const isFavorite = (productId) => {
    return items.value.some(item => item.productId === productId)
  }

  // Clear favorites
  const clearFavorites = () => {
    if (authStore.isAuthenticated && authStore.isRestricted) {
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
