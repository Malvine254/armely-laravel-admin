<template>
  <div id="app">
    <div
      v-if="showRestrictedBanner"
      class="sticky top-0 z-[10000] border-b border-amber-300 bg-amber-100"
    >
      <div class="mx-auto max-w-7xl px-4 py-3 text-sm text-amber-900">
        <template v-if="authStore.isActivationPending">
          <span class="font-semibold">Activation Required:</span>
          Please activate your account from the email link. Until then, only limited pages and read-only access are available.
        </template>
        <template v-else>
          <span class="font-semibold">Account Suspended:</span>
          Your account is currently restricted. You can browse read-only pages, but actions like creating quotes or placing orders are disabled.
        </template>
      </div>
    </div>
    <router-view />

    <button
      v-if="showBackToTop"
      type="button"
      @click="scrollToTop"
      class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 w-11 h-11 rounded-full shadow-lg flex items-center justify-center text-white z-[10001] transition"
      style="background-color: #2F5597;"
      @mouseenter="$event.currentTarget.style.backgroundColor='#1f4788'"
      @mouseleave="$event.currentTarget.style.backgroundColor='#2F5597'"
      aria-label="Back to top"
      title="Back to top"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
      </svg>
    </button>

    <footer
      v-if="showStoreFooter"
      class="mt-12 border-t border-white/20"
      style="background: linear-gradient(to right, #183a72, #0f2a54);"
    >
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
          <div>
            <p class="font-semibold">Armely Store</p>
            <p class="text-sm" style="color: #cce4f4;">B2B Procurements</p>
          </div>

          <div>
            <p class="font-semibold mb-2">Quick Links</p>
            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm" style="color: #cce4f4;">
              <router-link to="/products" class="hover:text-white transition">Products</router-link>
              <router-link to="/quotes" class="hover:text-white transition">My Quotes</router-link>
              <router-link to="/orders" class="hover:text-white transition">Orders</router-link>
              <router-link to="/account" class="hover:text-white transition">Account</router-link>
              <router-link to="/privacy" class="hover:text-white transition">Privacy</router-link>
              <button type="button" @click="openCookieSettingsModal" class="text-left hover:text-white transition">Cookie Settings</button>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-white/20">
          <p class="text-sm" style="color: #cce4f4;">&copy; {{ currentYear }} Armely Store. All rights reserved.</p>
        </div>
      </div>
    </footer>

    <div
      v-if="showCookieBanner"
      class="fixed inset-x-4 bottom-4 sm:inset-x-auto sm:right-4 sm:bottom-4 sm:max-w-md bg-white rounded-xl shadow-2xl border border-slate-200 p-4 z-[10002]"
    >
      <p class="text-sm font-semibold text-slate-900">Cookie Preferences</p>
      <p class="mt-1 text-sm text-slate-600">
        We use cookies to understand search behavior and suggest your most relevant searches.
      </p>
      <div class="mt-3 flex gap-2">
        <button
          type="button"
          @click="acceptCookies"
          class="px-3 py-2 text-white text-sm font-semibold rounded-lg"
          style="background-color: #2F5597;"
        >
          Accept Cookies
        </button>
        <button
          type="button"
          @click="rejectCookies"
          class="px-3 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700"
        >
          Only Necessary
        </button>
        <button
          type="button"
          @click="openCookieSettingsModal"
          class="px-3 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700"
        >
          Manage
        </button>
      </div>
    </div>

    <div
      v-if="showCookieSettingsModal"
      class="fixed inset-0 bg-black/50 z-[10003] flex items-center justify-center p-4"
    >
      <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-4 mb-6">
          <div>
            <h2 class="text-2xl font-bold text-slate-900">Cookie Settings</h2>
            <p class="text-slate-600 mt-1">Choose which optional cookies you want to enable.</p>
          </div>
          <button type="button" @click="closeCookieSettingsModal" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        </div>

        <div class="space-y-4">
          <section class="rounded-xl border border-slate-200 p-4">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="font-semibold text-slate-900">Necessary Cookies</h3>
                <p class="text-sm text-slate-600 mt-1">Required for core site functionality such as authentication, navigation, and security.</p>
              </div>
              <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full" style="background-color: #dbeafe; color: #1f4788; border: 1px solid #93c5fd;">Always On</span>
            </div>
          </section>

          <section class="rounded-xl border border-slate-200 p-4">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="font-semibold text-slate-900">Search Personalization</h3>
                <p class="text-sm text-slate-600 mt-1">Tracks search patterns to offer personalized suggestions (recent, popular, recommended).</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="cookieForm.searchPersonalization" type="checkbox" class="sr-only peer">
                <div class="w-11 h-6 rounded-full transition-colors bg-slate-300 peer-focus:ring-4 peer-focus:ring-blue-200 peer-checked:bg-[#2F5597] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
              </label>
            </div>
          </section>

          <section class="rounded-xl border border-slate-200 p-4">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="font-semibold text-slate-900">Analytics Cookies</h3>
                <p class="text-sm text-slate-600 mt-1">Helps us understand usage trends to improve product quality and performance.</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="cookieForm.analytics" type="checkbox" class="sr-only peer">
                <div class="w-11 h-6 rounded-full transition-colors bg-slate-300 peer-focus:ring-4 peer-focus:ring-blue-200 peer-checked:bg-[#2F5597] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
              </label>
            </div>
          </section>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
          <button type="button" @click="saveCookieSettings" class="px-5 py-2.5 rounded-lg text-white font-semibold" style="background-color: #2F5597;">Save Preferences</button>
          <button type="button" @click="acceptAllCookies" class="px-5 py-2.5 rounded-lg font-semibold" style="background-color: #dbeafe; color: #1f4788; border: 1px solid #93c5fd;">Accept All</button>
          <button type="button" @click="rejectOptionalCookies" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-semibold">Reject Optional</button>
        </div>
      </div>
    </div>

    <Toasts />
  </div>
</template>

<script setup>
import { computed, ref, reactive, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import Toasts from './components/Toasts.vue'
import { useAuthStore } from './stores/authStore'
import { getCookieConsentStatus, setCookieConsentStatus, getCookiePreferences, setCookiePreferences } from './services/searchInsights'

const route = useRoute()
const authStore = useAuthStore()
const showBackToTop = ref(false)
const showCookieBanner = ref(false)
const showCookieSettingsModal = ref(false)
const currentYear = new Date().getFullYear()
const cookieForm = reactive({
  necessary: true,
  searchPersonalization: false,
  analytics: false,
})

const showRestrictedBanner = computed(() => {
  const hiddenRoutes = ['login', 'register', 'admin-login']
  return authStore.isAuthenticated && authStore.isRestricted && !hiddenRoutes.includes(route.name)
})

const showStoreFooter = computed(() => {
  const routeName = String(route.name || '')
  return !routeName.startsWith('admin-') && routeName !== 'admin-login'
})

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 320
}

const refreshCookieBanner = () => {
  const consentStatus = getCookieConsentStatus()
  showCookieBanner.value = !consentStatus
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const acceptCookies = () => {
  setCookieConsentStatus('accepted')
  showCookieBanner.value = false
}

const rejectCookies = () => {
  setCookieConsentStatus('rejected')
  showCookieBanner.value = false
}

const openCookieSettingsModal = () => {
  const prefs = getCookiePreferences()
  cookieForm.necessary = true
  cookieForm.searchPersonalization = prefs.searchPersonalization
  cookieForm.analytics = prefs.analytics
  showCookieSettingsModal.value = true
}

const closeCookieSettingsModal = () => {
  showCookieSettingsModal.value = false
}

const saveCookieSettings = () => {
  setCookiePreferences({
    searchPersonalization: cookieForm.searchPersonalization,
    analytics: cookieForm.analytics,
  })
  showCookieSettingsModal.value = false
}

const acceptAllCookies = () => {
  cookieForm.searchPersonalization = true
  cookieForm.analytics = true
  saveCookieSettings()
}

const rejectOptionalCookies = () => {
  cookieForm.searchPersonalization = false
  cookieForm.analytics = false
  saveCookieSettings()
}

onMounted(() => {
  handleScroll()
  refreshCookieBanner()
  window.addEventListener('scroll', handleScroll, { passive: true })
  window.addEventListener('cookie-preferences-updated', refreshCookieBanner)
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('cookie-preferences-updated', refreshCookieBanner)
})
</script>

<style>
/* Global styles */
</style>
