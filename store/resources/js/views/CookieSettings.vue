<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
    <Navbar />

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Cookie Settings</h1>
        <p class="text-slate-600 mb-8">Choose which optional cookies you want to enable.</p>

        <div class="space-y-4">
          <section class="rounded-xl border border-slate-200 p-4">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="font-semibold text-slate-900">Necessary Cookies</h2>
                <p class="text-sm text-slate-600 mt-1">
                  Required for core site functionality such as authentication, navigation, and security.
                </p>
              </div>
              <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full" style="background-color: #dbeafe; color: #1f4788; border: 1px solid #93c5fd;">
                Always On
              </span>
            </div>
          </section>

          <section class="rounded-xl border border-slate-200 p-4">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="font-semibold text-slate-900">Search Personalization</h2>
                <p class="text-sm text-slate-600 mt-1">
                  Tracks search patterns to offer personalized suggestions (recent, popular, recommended).
                </p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="form.searchPersonalization" type="checkbox" class="sr-only peer">
                <div class="w-11 h-6 rounded-full transition-colors bg-slate-300 peer-focus:ring-4 peer-focus:ring-blue-200 peer-checked:bg-[#2F5597] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
              </label>
            </div>
          </section>

          <section class="rounded-xl border border-slate-200 p-4">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="font-semibold text-slate-900">Analytics Cookies</h2>
                <p class="text-sm text-slate-600 mt-1">
                  Helps us understand feature usage trends to improve product quality and performance.
                </p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="form.analytics" type="checkbox" class="sr-only peer">
                <div class="w-11 h-6 rounded-full transition-colors bg-slate-300 peer-focus:ring-4 peer-focus:ring-blue-200 peer-checked:bg-[#2F5597] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
              </label>
            </div>
          </section>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
          <button
            type="button"
            @click="saveSettings"
            class="px-5 py-2.5 rounded-lg text-white font-semibold"
            style="background-color: #2F5597;"
          >
            Save Preferences
          </button>
          <button
            type="button"
            @click="acceptAll"
            class="px-5 py-2.5 rounded-lg font-semibold"
            style="background-color: #dbeafe; color: #1f4788; border: 1px solid #93c5fd;"
          >
            Accept All
          </button>
          <button
            type="button"
            @click="rejectOptional"
            class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-semibold"
          >
            Reject Optional
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import { useToastStore } from '../stores/toastStore'
import Navbar from '../components/Navbar.vue'
import { getCookiePreferences, setCookiePreferences } from '../services/searchInsights'

const toastStore = useToastStore()

const form = reactive({
  searchPersonalization: false,
  analytics: false,
})

onMounted(() => {
  const prefs = getCookiePreferences()
  form.searchPersonalization = prefs.searchPersonalization
  form.analytics = prefs.analytics
})

const saveSettings = () => {
  setCookiePreferences({
    searchPersonalization: form.searchPersonalization,
    analytics: form.analytics,
  })
  toastStore.addToast('Cookie preferences saved.', 'success')
}

const acceptAll = () => {
  form.searchPersonalization = true
  form.analytics = true
  saveSettings()
}

const rejectOptional = () => {
  form.searchPersonalization = false
  form.analytics = false
  saveSettings()
}
</script>
