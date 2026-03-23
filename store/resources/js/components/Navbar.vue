<template>
  <nav class="bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-lg sticky top-0 z-50" style="background: linear-gradient(to right, #2F5597, #1f4788)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <!-- Logo Section -->
        <button type="button" class="flex items-center gap-3 flex-shrink-0 cursor-pointer" @click="goToProducts">
          <div class="w-10 h-10 rounded-lg bg-white overflow-hidden flex items-center justify-center">
            <img src="/images/logo/armely-store-logo.png" alt="Armely Store" class="w-9 h-9 object-contain">
          </div>
          <div class="text-left">
            <div class="font-bold text-lg">Armely Store</div>
            <div class="text-xs" style="color: #cce4f4;">B2B Hardware Procurement</div>
          </div>
        </button>

        <!-- Search Bar - Hidden on mobile -->
        <div class="hidden md:block flex-1 mx-8">
          <div class="relative">
            <svg class="absolute left-3 top-3.5 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #a8d1f5;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search products, vendors, solutions..."
              class="w-full pl-10 pr-4 py-2.5 rounded-lg text-white outline-none focus:ring-2 transition" style="background-color: #3d6ba8; color: white;" :style="{color: 'white'}" @keyup.enter="submitSearch" @focus="$event.target.style.backgroundColor='#2F5597'" @blur="$event.target.style.backgroundColor='#3d6ba8'" placeholder-style="color: #a8d1f5;"
            >
          </div>
        </div>

        <!-- Right Section Icons -->
        <div class="flex items-center gap-2 md:gap-4">
          <!-- Cart Icon - Always visible (guest + authenticated) -->
          <button type="button" class="relative p-2 rounded-lg transition group cursor-pointer" style="color: white;" @click="goToCart" @mouseenter="$event.currentTarget.style.backgroundColor='#3d6ba8'" @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m10-9l2 9m-9 0h14m-5-9v9"></path>
            </svg>
            <span v-if="cartStore.cartCount > 0" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">{{ cartStore.cartCount }}</span>
            <span class="hidden group-hover:block absolute top-12 right-0 bg-white px-2 py-1 rounded text-xs" style="color: #2F5597;">Cart</span>
          </button>

          <!-- Authenticated User Features -->
          <template v-if="authStore.isAuthenticated">
            <!-- Messages Icon -->
            <button v-if="authStore.hasFeatureAccess('messages')" @click="goToMessages" class="relative p-2 rounded-lg transition group" style="color: white;" @mouseenter="$event.target.style.backgroundColor='#3d6ba8'" @mouseleave="$event.target.style.backgroundColor='transparent'">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ unreadCount }}</span>
              <span class="hidden group-hover:block absolute top-12 right-0 bg-white px-2 py-1 rounded text-xs whitespace-nowrap" style="color: #2F5597;">Messages</span>
            </button>

            <!-- Favorites Icon - Only for authenticated users -->
            <button type="button" v-if="authStore.isAuthenticated" class="relative p-2 rounded-lg transition group cursor-pointer" style="color: white;" @click="goToFavorites" @mouseenter="$event.currentTarget.style.backgroundColor='#3d6ba8'" @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
              <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">{{ favoritesStore.favoriteCount }}</span>
              <span class="hidden group-hover:block absolute top-12 right-0 bg-white px-2 py-1 rounded text-xs whitespace-nowrap" style="color: #2F5597;">Favorites</span>
            </button>

            <!-- Authenticated Account Menu -->
            <div class="relative group">
              <button class="p-2 rounded-lg transition flex items-center gap-2" style="color: white;" @mouseenter="$event.currentTarget.style.backgroundColor='#3d6ba8'" @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.88 6.196 9 9 0 015.12 17.804z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20a7 7 0 0110 0"></path>
                </svg>
                <span class="text-sm font-medium">Hi, {{ userFirstName }}</span>
              </button>
              <!-- Authenticated Dropdown Menu -->
              <div class="hidden group-hover:block absolute right-0 mt-0 w-56 bg-white rounded-lg shadow-xl py-2 z-10" style="color: #2F5597;">
                <div class="px-4 py-2 border-b border-gray-200">
                  <div class="font-semibold">{{ authStore.user?.name }}</div>
                  <div class="text-xs text-gray-500">{{ authStore.user?.email }}</div>
                  <div v-if="authStore.user?.company_name" class="text-xs text-gray-600 mt-1">{{ authStore.user?.company_name }}</div>
                </div>
                <router-link to="/account" class="block w-full px-4 py-2 text-left hover:bg-gray-100 transition">My Account</router-link>
                <router-link to="/quotes" v-if="authStore.isAuthenticated" class="block w-full px-4 py-2 text-left hover:bg-gray-100 transition">My Quotes</router-link>
                <router-link to="/invoices" v-if="authStore.hasFeatureAccess('invoices')" class="block w-full px-4 py-2 text-left hover:bg-gray-100 transition">Invoices</router-link>
                <button @click="goToSavedSearches" class="w-full px-4 py-2 text-left hover:bg-gray-100 transition">Saved Searches</button>
                <div class="border-t border-gray-200 my-2"></div>
                <button @click="handleLogout" class="w-full px-4 py-2 text-left hover:bg-gray-100 transition text-red-600"><strong>Sign Out</strong></button>
              </div>
            </div>
          </template>

          <!-- Unauthenticated User - Login/Sign Up Buttons -->
          <template v-else>
            <router-link to="/login" class="px-4 py-2 rounded-lg font-semibold transition text-sm" style="background-color: transparent; border: 2px solid white; color: white;" @mouseenter="$event.target.style.backgroundColor='#3d6ba8'" @mouseleave="$event.target.style.backgroundColor='transparent'">
              Log In
            </router-link>
            <router-link to="/register" class="px-4 py-2 rounded-lg font-semibold transition text-sm text-white" style="background-color: #4CAF50;" @mouseenter="$event.target.style.backgroundColor='#45a049'" @mouseleave="$event.target.style.backgroundColor='#4CAF50'">
              Sign Up
            </router-link>
          </template>

          <!-- Mobile Menu Button -->
          <button class="md:hidden p-2 rounded-lg transition" style="color: white;" @mouseenter="$event.target.style.backgroundColor='#3d6ba8'" @mouseleave="$event.target.style.backgroundColor='transparent'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Search Bar -->
      <div class="md:hidden pb-4">
        <div class="relative">
          <svg class="absolute left-3 top-3.5 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #a8d1f5;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Search products..."
            class="w-full pl-10 pr-4 py-2.5 rounded-lg text-white outline-none focus:ring-2 transition" style="background-color: #3d6ba8;" @keyup.enter="submitSearch" @focus="$event.target.style.backgroundColor='#2F5597'" @blur="$event.target.style.backgroundColor='#3d6ba8'"
          >
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cartStore'
import { useFavoritesStore } from '../stores/favoritesStore'
import { useAuthStore } from '../stores/authStore'
import { useToastStore } from '../stores/toastStore'
import { API_BASE_URL } from '../services/runtimeConfig'

const router = useRouter()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const toastStore = useToastStore()
const searchTerm = ref('')
const unreadCount = ref(0)

const userFirstName = computed(() => {
  if (!authStore.user?.name) return 'User'
  const nameParts = authStore.user.name.trim().split(' ')
  return nameParts[0] || 'User'
})

const goToMessages = () => {
  router.push({ name: 'messages' })
}

const goToFavorites = () => {
  router.push({ name: 'favorites' })
}

const goToCart = () => {
  router.push({ name: 'cart' })
}

const goToAccount = () => {
  router.push({ name: 'account' })
}

const goToProducts = () => {
  router.push({ name: 'products' })
}

const goToSavedSearches = () => {
  toastStore.addToast('Saved Searches feature coming soon', 'info')
}

const handleLogout = async () => {
  await authStore.logout()
  toastStore.addToast('Logged out successfully', 'success')
  router.push({ name: 'login' })
}

const submitSearch = () => {
  const query = searchTerm.value.trim()
  router.push({ name: 'products', query: query ? { q: query } : {} })
}

const fetchUnreadCount = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) return
    
    const response = await fetch(`${API_BASE_URL}/messages/unread-count`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      const data = await response.json()
      unreadCount.value = data.count ?? data.unread_count ?? 0
    }
  } catch (error) {
    console.error('Error fetching unread count:', error)
  }
}

onMounted(() => {
  fetchUnreadCount()
  // Refresh unread count every 30 seconds
  setInterval(fetchUnreadCount, 30000)
})
</script>
