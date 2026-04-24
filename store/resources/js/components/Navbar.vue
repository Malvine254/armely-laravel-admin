<template>
  <nav class="sticky top-0 z-50 border-b border-white/10" style="background: linear-gradient(135deg, #0f172a 0%, #0a2948 45%, #1d4c6e 100%); box-shadow: 0 16px 45px rgba(2, 6, 23, 0.35);">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5">
      <div class="flex items-center justify-between h-20">
        <!-- Logo Section -->
        <button type="button" class="flex items-center gap-2 sm:gap-3 flex-shrink-0 cursor-pointer transition hover:opacity-95" @click="goToProducts">
          <div class="w-11 h-11 rounded-xl overflow-hidden flex items-center justify-center shadow-lg border border-white/30 bg-white">
            <img
              v-if="showLogoImage"
              :src="logoSrc"
              alt="Armely Store"
              class="w-full h-full object-contain p-1"
              @error="handleLogoError"
            >
            <span v-else class="text-white font-bold text-base">A</span>
          </div>
          <div class="text-left">
            <div class="font-bold text-base sm:text-lg text-white">Armely Store</div>
            <div class="hidden sm:block text-xs text-slate-200">B2B Procurements</div>
          </div>
        </button>

        <!-- Nav Links: Categories - Hidden below xl -->
        <div class="hidden xl:flex items-center justify-center gap-1 flex-1 mx-6 min-w-0">
          <div
            v-for="cat in primaryCategories"
            :key="cat.value"
            class="relative"
            @mouseenter="categoryDropdownOpen = cat.value"
            @mouseleave="categoryDropdownOpen = null"
          >
            <button
              type="button"
              class="flex items-center gap-1.5 flex-shrink-0 px-3 py-2 rounded-lg text-sm font-semibold text-slate-100 hover:text-cyan-300 hover:bg-white/10 transition"
              @click="browseProducts(cat.value)"
            >
              <span>{{ cat.name }}</span>
              <svg class="w-3.5 h-3.5 transition-transform" :class="categoryDropdownOpen === cat.value ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
              <div v-if="categoryDropdownOpen === cat.value" class="absolute left-0 mt-1 w-64 rounded-xl shadow-2xl overflow-hidden z-50 border border-white/20" style="background: linear-gradient(135deg, #0f172a 0%, #0a2948 45%, #1d4c6e 100%);">
                <div class="px-4 py-2.5 border-b border-white/20">
                  <p class="text-xs font-semibold text-white uppercase tracking-widest">{{ cat.name }} Brands</p>
                </div>
                <div class="py-1.5">
                  <button
                    v-for="brand in categoryBrandsMap[cat.value] || []"
                    :key="`${cat.value}-${brand.value}`"
                    type="button"
                    class="w-full text-left px-4 py-2.5 text-sm text-slate-200 hover:bg-white/10 hover:text-cyan-300 transition"
                    @click="browseCategoryVendor(cat.value, brand.value)"
                  >
                    {{ brand.label }}
                  </button>
                  <button
                    type="button"
                    class="w-full text-left px-4 py-2.5 text-sm text-cyan-300 hover:bg-white/10 transition"
                    @click="browseProducts(cat.value)"
                  >
                    View all in {{ cat.name }}
                  </button>
                </div>
              </div>
            </transition>
          </div>

          <!-- More Categories Dropdown -->
          <div v-if="overflowCategories.length > 0" class="relative" @mouseenter="moreCategoriesOpen = true" @mouseleave="moreCategoriesOpen = false">
            <button
              type="button"
              class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-slate-100 hover:text-cyan-300 hover:bg-white/10 transition"
              @click="moreCategoriesOpen = !moreCategoriesOpen"
            >
              More Categories
              <svg class="w-3.5 h-3.5 transition-transform" :class="moreCategoriesOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
              <div v-if="moreCategoriesOpen" class="absolute left-0 mt-1 w-64 rounded-xl shadow-2xl overflow-visible z-50 border border-white/20" style="background: linear-gradient(135deg, #0f172a 0%, #0a2948 45%, #1d4c6e 100%);">
                <div class="px-4 py-2.5 border-b border-white/20">
                  <p class="text-xs font-semibold text-white uppercase tracking-widest">More Categories</p>
                </div>
                <div class="py-1.5">
                  <div
                    v-for="cat in overflowCategories"
                    :key="cat.value"
                    class="relative"
                    @mouseenter="moreCategoryDropdownOpen = cat.value"
                    @mouseleave="moreCategoryDropdownOpen = null"
                  >
                    <button
                      type="button"
                      class="w-full flex items-center justify-between text-left px-4 py-2.5 text-sm text-slate-200 hover:bg-white/10 hover:text-cyan-300 transition"
                      @click="browseProducts(cat.value)"
                    >
                      <span>{{ cat.name }}</span>
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </button>

                    <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 translate-x-1" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 translate-x-1">
                      <div v-if="moreCategoryDropdownOpen === cat.value" class="absolute left-full top-0 ml-1 w-60 rounded-xl shadow-2xl overflow-hidden z-[60] border border-white/20" style="background: linear-gradient(135deg, #0f172a 0%, #0a2948 45%, #1d4c6e 100%);">
                        <div class="px-4 py-2.5 border-b border-white/20">
                          <p class="text-xs font-semibold text-white uppercase tracking-widest">{{ cat.name }} Brands</p>
                        </div>
                        <div class="py-1.5">
                          <button
                            v-for="brand in categoryBrandsMap[cat.value] || []"
                            :key="`${cat.value}-more-${brand.value}`"
                            type="button"
                            class="w-full text-left px-4 py-2.5 text-sm text-slate-200 hover:bg-white/10 hover:text-cyan-300 transition"
                            @click="browseCategoryVendor(cat.value, brand.value)"
                          >
                            {{ brand.label }}
                          </button>
                          <button
                            type="button"
                            class="w-full text-left px-4 py-2.5 text-sm text-cyan-300 hover:bg-white/10 transition"
                            @click="browseProducts(cat.value)"
                          >
                            View all in {{ cat.name }}
                          </button>
                        </div>
                      </div>
                    </transition>
                  </div>
                </div>
              </div>
            </transition>
          </div>

        </div>

        <!-- Right Section Icons -->
        <div class="flex items-center gap-1 sm:gap-2 md:gap-4">
          <!-- Cart Icon - Always visible (guest + authenticated) -->
          <button type="button" class="relative p-1.5 sm:p-2 rounded-lg transition group cursor-pointer text-slate-100 hover:text-cyan-300" @click="goToCart">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m10-9l2 9m-9 0h14m-5-9v9"></path>
            </svg>
            <span v-if="cartStore.cartCount > 0" class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">{{ cartStore.cartCount }}</span>
            <span class="hidden group-hover:block absolute top-12 right-0 bg-slate-100 px-2 py-1 rounded text-xs text-slate-900 whitespace-nowrap">Cart</span>
          </button>

          <!-- Authenticated User Features -->
          <template v-if="authStore.isAuthenticated">
            <!-- Messages Icon -->
            <button v-if="authStore.hasFeatureAccess('messages')" @click="goToMessages" class="hidden xl:flex relative p-2 rounded-lg transition group text-slate-100 hover:text-cyan-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              <span class="hidden group-hover:block absolute top-12 right-0 bg-slate-100 px-2 py-1 rounded text-xs whitespace-nowrap text-slate-900">Messages</span>
            </button>

            <!-- Favorites Icon - Only for authenticated users -->
            <button type="button" v-if="authStore.isAuthenticated" class="hidden xl:flex relative p-2 rounded-lg transition group cursor-pointer text-slate-100 hover:text-cyan-300" @click="goToFavorites">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
              <span v-if="favoritesStore.favoriteCount > 0" class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">{{ favoritesStore.favoriteCount }}</span>
              <span class="hidden group-hover:block absolute top-12 right-0 bg-slate-100 px-2 py-1 rounded text-xs whitespace-nowrap text-slate-900">Favorites</span>
            </button>

            <!-- Authenticated Account Menu -->
            <div class="hidden xl:block relative group">
              <button class="p-2 rounded-lg transition flex items-center gap-2 text-slate-100 hover:text-cyan-300">
                <!-- Profile Picture or Initials -->
                <img v-if="userProfilePictureUrl" :src="userProfilePictureUrl" :alt="authStore.user?.name" class="w-8 h-8 rounded-full object-cover border border-slate-300">
                <div v-else class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs bg-gradient-to-br from-cyan-400 to-blue-500">{{ userInitials }}</div>
                <span class="text-sm font-medium">{{ userFirstName }}</span>
              </button>
              <!-- Authenticated Dropdown Menu -->
              <div class="hidden group-hover:block absolute right-0 mt-0 w-56 rounded-lg shadow-xl py-2 z-10 border border-white/20" style="background: linear-gradient(135deg, #0f172a 0%, #0a2948 45%, #1d4c6e 100%);">
                <div class="px-4 py-2 border-b border-white/20">
                  <div class="flex items-center gap-3 mb-2">
                    <img v-if="userProfilePictureUrl" :src="userProfilePictureUrl" :alt="authStore.user?.name" class="w-10 h-10 rounded-full object-cover border border-slate-400">
                    <div v-else class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold bg-gradient-to-br from-cyan-400 to-blue-500">{{ userInitials }}</div>
                    <div>
                      <div class="font-semibold text-sm text-white">{{ authStore.user?.name }}</div>
                      <div class="text-xs text-slate-300">{{ authStore.user?.email }}</div>
                    </div>
                  </div>
                  <div v-if="authStore.user?.company_name" class="text-xs text-slate-300">{{ authStore.user?.company_name }}</div>
                </div>
                <router-link to="/account" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">My Account</router-link>
                <router-link to="/quotes" v-if="authStore.isAuthenticated" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">My Quotes</router-link>
                <router-link to="/orders" v-if="authStore.isAuthenticated" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">My Orders</router-link>
                <router-link to="/invoices" v-if="authStore.hasFeatureAccess('invoices')" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">Invoices</router-link>
                <div class="border-t border-white/20 my-2"></div>
                <button @click="handleLogout" class="w-full px-4 py-2 text-left hover:bg-rose-500/20 transition text-rose-400"><strong>Sign Out</strong></button>
              </div>
            </div>
          </template>

          <!-- Unauthenticated User - Login/Sign Up Buttons -->
          <template v-else>
            <router-link to="/login" class="hidden xl:inline-block px-4 py-2 rounded-lg font-semibold transition text-sm text-slate-100 border border-slate-300 hover:bg-white/10">
              Log In
            </router-link>
            <router-link to="/register" class="hidden xl:inline-block px-4 py-2 rounded-lg font-semibold transition text-sm text-white bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500">
              Sign Up
            </router-link>
          </template>

      <!-- Mobile Menu Button -->
          <button
            class="xl:hidden p-2 rounded-lg transition text-slate-100 hover:text-cyan-300"
            @click="toggleMobileMenu"
            :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
            aria-label="Toggle mobile menu"
          >
            <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Dropdown Menu -->
      <div v-if="mobileMenuOpen" class="xl:hidden pb-4">
        <div class="rounded-lg border border-white/20 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #0a2948 45%, #1d4c6e 100%);">
          <!-- Products Section -->
          <div class="border-b border-white/10">
            <button type="button" @click="mobileProductsOpen = !mobileProductsOpen" class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-100 hover:bg-white/10 transition">
              <span>Products</span>
              <svg class="w-4 h-4 transition-transform" :class="mobileProductsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div v-if="mobileProductsOpen" class="bg-black/20">
              <button v-for="cat in productCategories" :key="cat.value" type="button" @click="browseProducts(cat.value)" class="w-full text-left px-8 py-2.5 text-sm text-slate-300 hover:bg-white/10 transition">{{ cat.name }}</button>
            </div>
          </div>
          <template v-if="authStore.isAuthenticated">
            <div class="px-4 py-3 border-b border-white/10">
              <div class="font-semibold text-white">{{ authStore.user?.name }}</div>
              <div class="text-xs text-slate-300">{{ authStore.user?.email }}</div>
            </div>
            <button type="button" @click="goToAccount" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">My Account</button>
            <button type="button" @click="goToCart" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">My Quote / Cart</button>
            <button type="button" @click="goToOrders" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">My Orders</button>
            <button type="button" @click="goToMessages" v-if="authStore.hasFeatureAccess('messages')" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">Messages</button>
            <button type="button" @click="goToFavorites" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">Favorites</button>
            <button type="button" @click="handleLogout" class="w-full text-left px-4 py-3 text-sm font-semibold text-rose-400 transition hover:bg-rose-500/20">Sign Out</button>
          </template>
          <template v-else>
            <router-link to="/login" @click="closeMobileMenu" class="block w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">Log In</router-link>
            <router-link to="/register" @click="closeMobileMenu" class="block w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">Sign Up</router-link>
          </template>
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
import { buildStoreUrl } from '../services/runtimeConfig'
import api from '../services/api'

const router = useRouter()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const toastStore = useToastStore()
const mobileMenuOpen = ref(false)
const categoryDropdownOpen = ref(null)
const moreCategoriesOpen = ref(false)
const moreCategoryDropdownOpen = ref(null)
const mobileProductsOpen = ref(false)

const productCategories = ref([])
const MENU_CATEGORIES_STORAGE_KEY = 'store_menu_categories_v2'
const MENU_CATEGORIES_SOFT_TTL_MS = 15 * 60 * 1000
const MENU_CATEGORIES_HARD_TTL_MS = 7 * 24 * 60 * 60 * 1000

const primaryCategories = computed(() => productCategories.value.slice(0, 3))
const overflowCategories = computed(() => productCategories.value.slice(3))

const categoryBrandsMap = computed(() => {
  const map = {}
  for (const cat of productCategories.value) {
    map[cat.name] = cat.brands || []
  }
  return map
})

const normalizeMenuCategories = (rows) => {
  if (!Array.isArray(rows)) return []

  return rows
    .map(cat => ({
      name: cat?.name,
      value: cat?.name,
      brands: Array.isArray(cat?.brands) ? cat.brands : [],
    }))
    .filter(cat => typeof cat.name === 'string' && cat.name.trim() !== '')
}

const loadMenuCategoriesFromStorage = () => {
  if (typeof window === 'undefined') return { data: [], stale: true }

  try {
    const raw = window.localStorage.getItem(MENU_CATEGORIES_STORAGE_KEY)
    if (!raw) return { data: [], stale: true }

    const parsed = JSON.parse(raw)
    const timestamp = Number(parsed?.timestamp || 0)
    if (!timestamp) {
      window.localStorage.removeItem(MENU_CATEGORIES_STORAGE_KEY)
      return { data: [], stale: true }
    }

    const ageMs = Date.now() - timestamp
    if (ageMs > MENU_CATEGORIES_HARD_TTL_MS) {
      window.localStorage.removeItem(MENU_CATEGORIES_STORAGE_KEY)
      return { data: [], stale: true }
    }

    return {
      data: normalizeMenuCategories(parsed?.data),
      stale: ageMs > MENU_CATEGORIES_SOFT_TTL_MS,
    }
  } catch {
    return { data: [], stale: true }
  }
}

const saveMenuCategoriesToStorage = (rows) => {
  if (typeof window === 'undefined') return

  try {
    window.localStorage.setItem(
      MENU_CATEGORIES_STORAGE_KEY,
      JSON.stringify({
        timestamp: Date.now(),
        data: rows,
      })
    )
  } catch {
    // Ignore storage errors (quota/privacy mode)
  }
}

const fetchMenuCategories = async () => {
  try {
    const { data } = await api.get('/menu-categories')
    if (data.success && Array.isArray(data.data)) {
      const normalized = normalizeMenuCategories(data.data)
      if (normalized.length > 0) {
        productCategories.value = normalized
        saveMenuCategoriesToStorage(normalized)
      }
    }
  } catch (e) {
    // Silently fall back — navbar will just show no categories
  }
}

onMounted(() => {
  const cached = loadMenuCategoriesFromStorage()
  if (cached.data.length > 0) {
    productCategories.value = cached.data
  }

  if (cached.stale || cached.data.length === 0) {
    fetchMenuCategories()
  }
})

const logoCandidates = (() => {
  const relativePath = 'images/logo/armely-store-logo.png'
  const candidates = [
    buildStoreUrl(relativePath),
    '/store/images/logo/armely-store-logo.png',
    '/store/public/images/logo/armely-store-logo.png',
    '/images/logo/armely-store-logo.png',
  ]

  if (typeof window !== 'undefined') {
    const origin = window.location.origin
    candidates.push(
      `${origin}/store/images/logo/armely-store-logo.png`,
      `${origin}/store/public/images/logo/armely-store-logo.png`,
      `${origin}/images/logo/armely-store-logo.png`
    )
  }

  return [...new Set(candidates)]
})()

const logoCandidateIndex = ref(0)
const logoSrc = ref(logoCandidates[0])
const showLogoImage = ref(Boolean(logoSrc.value))

const handleLogoError = () => {
  logoCandidateIndex.value += 1
  if (logoCandidateIndex.value < logoCandidates.length) {
    logoSrc.value = logoCandidates[logoCandidateIndex.value]
    return
  }

  showLogoImage.value = false
}

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const userFirstName = computed(() => {
  if (!authStore.user?.name) return 'User'
  const nameParts = authStore.user.name.trim().split(' ')
  return nameParts[0] || 'User'
})

const userInitials = computed(() => {
  if (!authStore.user?.name) return ''
  const parts = authStore.user.name.split(' ').filter(Boolean)
  return parts.slice(0, 2).map(p => p[0].toUpperCase()).join('')
})

const userProfilePictureUrl = computed(() => {
  return authStore.user?.profile_picture_url || null
})

const goToMessages = () => {
  router.push({ name: 'messages' })
  closeMobileMenu()
}

const goToFavorites = () => {
  router.push({ name: 'favorites' })
  closeMobileMenu()
}

const goToCart = () => {
  router.push({ name: 'cart' })
  closeMobileMenu()
}

const goToAccount = () => {
  router.push({ name: 'account' })
  closeMobileMenu()
}

const goToOrders = () => {
  router.push({ name: 'orders' })
  closeMobileMenu()
}

const goToProducts = () => {
  router.push({ name: 'products' })
  closeAll()
}

const browseProducts = (category = null) => {
  const query = category ? { category } : {}
  router.push({ name: 'products', query })
  closeAll()
}

const browseCategoryVendor = (category, vendor) => {
  router.push({ name: 'products', query: { category, vendor } })
  closeAll()
}

const closeAll = () => {
  mobileMenuOpen.value = false
  categoryDropdownOpen.value = null
  moreCategoriesOpen.value = false
  moreCategoryDropdownOpen.value = null
  mobileProductsOpen.value = false
}

const handleLogout = async () => {
  await authStore.logout()
  toastStore.addToast('Logged out successfully', 'success')
  router.push({ name: 'login' })
  closeAll()
}

</script>
