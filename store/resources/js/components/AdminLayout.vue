<template>
  <div class="flex h-screen" style="background: linear-gradient(135deg, #0f172a 0%, #0a2948 45%, #1d4c6e 100%);">
    <!-- Mobile Overlay -->
    <div
      v-show="sidebarOpen"
      class="fixed inset-0 bg-black/40 z-40 md:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar Navigation -->
    <div
      :class="[
        'w-64 text-slate-100 shadow-lg flex flex-col fixed inset-y-0 left-0 z-50 transform transition-transform duration-200 md:static md:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
      style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(10, 41, 72, 0.95));"
    >
      <!-- Logo -->
      <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-white">Armely Admin</h1>
          <p class="text-sm text-slate-300 mt-1">Control Panel</p>
        </div>
        <button
          type="button"
          class="md:hidden text-white/90 hover:text-white"
          @click="sidebarOpen = false"
          aria-label="Close sidebar"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation Menu -->
      <nav class="sidebar-nav mt-6 flex-1 overflow-y-auto pb-4">
        <!-- Dashboard -->
        <router-link
          :to="{ name: 'admin-dashboard-page' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('dashboard')
              ? 'bg-white/10 border-cyan-300 text-cyan-300'
              : 'border-transparent hover:bg-white/10 hover:border-cyan-300 text-slate-100'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Dashboard</span>
        </router-link>

        <!-- Quotes Management -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Quotes</p>
        </div>
        <router-link
          :to="{ name: 'admin-quotes' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('quotes')
              ? 'bg-white/10 border-cyan-300 text-cyan-300'
              : 'border-transparent hover:bg-white/10 hover:border-cyan-300 text-slate-100'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Pending Quotes</span>
          <span v-if="stats.pending_quotes > 0" class="ml-auto bg-rose-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
            {{ stats.pending_quotes }}
          </span>
        </router-link>

        <!-- Orders Management -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Orders</p>
        </div>
        <router-link
          :to="{ name: 'admin-orders' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('orders')
              ? 'bg-white/10 border-cyan-300 text-cyan-300'
              : 'border-transparent hover:bg-white/10 hover:border-cyan-300 text-slate-100'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span>All Orders</span>
          <span v-if="stats.processing_orders > 0" class="ml-auto bg-amber-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
            {{ stats.processing_orders }}
          </span>
        </router-link>

        <!-- Customers Management -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Customers</p>
        </div>
        <router-link
          :to="{ name: 'admin-customers' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('customers')
              ? 'bg-white/10 border-cyan-300 text-cyan-300'
              : 'border-transparent hover:bg-white/10 hover:border-cyan-300 text-slate-100'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
          <span>Customers</span>
        </router-link>

        <!-- Support / Chat Escalations -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Support</p>
        </div>
        <router-link
          :to="{ name: 'admin-chat' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('chat')
              ? 'bg-white/10 border-cyan-300 text-cyan-300'
              : 'border-transparent hover:bg-white/10 hover:border-cyan-300 text-slate-100'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z" />
          </svg>
          <span>Chat Escalations</span>
          <span v-if="escalatedChatCount > 0" class="ml-auto bg-rose-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
            {{ escalatedChatCount }}
          </span>
        </router-link>

        <!-- Reports -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Analytics</p>
        </div>
        <router-link
          :to="{ name: 'admin-reports' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('reports')
              ? 'bg-white/10 border-cyan-300 text-cyan-300'
              : 'border-transparent hover:bg-white/10 hover:border-cyan-300 text-slate-100'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Revenue Reports</span>
        </router-link>

        <!-- Invoices Management -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Billing</p>
        </div>
        <router-link
          :to="{ name: 'admin-invoices' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('invoices')
              ? 'bg-white/10 border-cyan-300 text-cyan-300'
              : 'border-transparent hover:bg-white/10 hover:border-cyan-300 text-slate-100'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Invoices</span>
        </router-link>

        <!-- Settings -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">System</p>
        </div>
        <router-link
          :to="{ name: 'admin-settings' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('settings')
              ? 'bg-white/10 border-cyan-300 text-cyan-300'
              : 'border-transparent hover:bg-white/10 hover:border-cyan-300 text-slate-100'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Settings</span>
        </router-link>
      </nav>

      <!-- User Profile -->
      <div class="border-t border-white/10 p-4 flex-shrink-0">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm truncate text-slate-100">{{ currentUser.name || 'Loading...' }}</p>
            <p class="text-xs text-slate-300 truncate">{{ currentUser.email || 'Please wait' }}</p>
          </div>
        </div>
        <button @click="logout" class="w-full bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-400 hover:to-rose-500 py-2 rounded text-sm font-semibold transition text-white">
          Sign Out
        </button>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Top Header -->
      <div class="border-b border-white/10 px-3 sm:px-4 lg:px-5 py-4 shadow-sm" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(10, 41, 72, 0.95));">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <button
              type="button"
              class="md:hidden text-slate-100 hover:text-cyan-300 transition"
              @click="sidebarOpen = true"
              aria-label="Open sidebar"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <h2 class="text-2xl font-bold text-slate-100">
              <slot name="title">Admin Dashboard</slot>
            </h2>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
              <button class="text-slate-100 hover:text-cyan-300 transition relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span class="absolute top-0 right-0 bg-rose-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                  {{ unreadNotifications }}
                </span>
              </button>
            </div>
            <!-- Search -->
            <input
              type="text"
              placeholder="Search..."
              class="px-4 py-2 border border-white/10 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 text-slate-100 placeholder-slate-400"
              style="background: rgba(148, 163, 184, 0.12);"
            />
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <div class="flex-1 overflow-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()

const sidebarOpen = ref(false)

const currentUser = ref({
  name: '',
  email: ''
})

const stats = ref({
  pending_quotes: 0,
  processing_orders: 0
})

const unreadNotifications = ref(0)
const escalatedChatCount = ref(0)

const isActive = (section) => {
  return route.path.includes(`/admin/${section}`)
}

watch(
  () => route.path,
  () => {
    sidebarOpen.value = false
  }
)

const fetchCurrentUser = async () => {
  try {
    const cached = localStorage.getItem('armely_user')
    if (cached) {
      try {
        const parsed = JSON.parse(cached)
        if (parsed?.name || parsed?.email) {
          currentUser.value = {
            name: parsed.name || 'Admin User',
            email: parsed.email || 'No email'
          }
        }
      } catch (e) {
        // Ignore malformed local storage payload.
      }
    }

    const response = await api.get('/auth/me')
    if (response.data.success) {
      const user = response.data.data.user || {}
      currentUser.value = {
        name: user.name || 'Admin User',
        email: user.email || 'No email'
      }
    }
  } catch (error) {
    console.error('Failed to fetch current user:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await api.get('/admin/dashboard/stats')
    if (response.data.success) {
      stats.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const fetchEscalatedCount = async () => {
  try {
    const res = await api.get('/admin/chats/unread-count')
    if (res.data?.success) {
      escalatedChatCount.value = Number(res.data.count || 0)
    }
  } catch {
    // non-critical
  }
}

const logout = async () => {
  try {
    await api.post('/auth/logout')
    localStorage.removeItem('auth_token')
    localStorage.removeItem('armely_user')
    localStorage.removeItem('auth_session_expiry')
    localStorage.removeItem('auth_restricted')
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
    router.push('/login')
  }
}

onMounted(() => {
  fetchCurrentUser()
  fetchStats()
  fetchEscalatedCount()
  // Optionally refresh stats every 30 seconds
  setInterval(fetchStats, 30000)
  setInterval(fetchEscalatedCount, 30000)
})
</script>

<style scoped>
/* Smooth transitions */
a {
  @apply transition-all duration-200;
}

/* Thin, translucent scrollbar for sidebar nav */
.sidebar-nav::-webkit-scrollbar {
  width: 4px;
}
.sidebar-nav::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.20);
  border-radius: 4px;
}
.sidebar-nav::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.40);
}
/* Firefox */
.sidebar-nav {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.20) transparent;
}
</style>
