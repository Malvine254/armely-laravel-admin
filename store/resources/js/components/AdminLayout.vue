<template>
  <div class="flex h-screen bg-gray-50 admin-font">
    <!-- Mobile Overlay -->
    <div
      v-show="sidebarOpen"
      class="fixed inset-0 bg-black/30 z-40 md:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar Navigation -->
    <div
      :class="[
        'w-64 shadow-lg flex flex-col fixed inset-y-0 left-0 z-50 transform transition-transform duration-200 md:static md:translate-x-0 bg-white border-r border-gray-200',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Logo -->
      <div class="px-5 flex items-center justify-between flex-shrink-0 admin-header-band" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
        <div>
          <h1 class="text-xl font-bold text-white leading-tight">Armely Admin</h1>
          <p class="text-[11px] text-white/60 mt-0.5">Control Panel</p>
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
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Dashboard</span>
        </router-link>

        <!-- Quotes Management -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Quotes</p>
        </div>
        <router-link
          :to="{ name: 'admin-quotes' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('quotes')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Pending Quotes</span>
          <span v-if="unseenCounts.pending_quotes > 0" class="ml-auto bg-rose-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
            {{ unseenCounts.pending_quotes }}
          </span>
        </router-link>

        <!-- Orders Management -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders</p>
        </div>
        <router-link
          :to="{ name: 'admin-orders' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('orders')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <span>All Orders</span>
          <span v-if="unseenCounts.processing_orders > 0" class="ml-auto bg-amber-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
            {{ unseenCounts.processing_orders }}
          </span>
        </router-link>
        <router-link
          :to="{ name: 'admin-order-tracking' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('orders/tracking')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
          </svg>
          <span>Order Tracking</span>
        </router-link>

        <!-- Users Management -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Users</p>
        </div>
        <router-link
          :to="{ name: 'AdminUsers' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('users')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
          </svg>
          <span>Admins</span>
        </router-link>
        <router-link
          :to="{ name: 'AdminCustomers' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('customers')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
          </svg>
          <span>Customers</span>
          <span v-if="unseenCounts.pending_users > 0" class="ml-auto bg-amber-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
            {{ unseenCounts.pending_users }}
          </span>
        </router-link>

        <!-- Support / Chat Escalations -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Support</p>
        </div>
        <router-link
          :to="{ name: 'admin-chat' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('chat')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z" />
          </svg>
          <span>Chat Escalations</span>
          <span v-if="unseenCounts.escalated_chat > 0" class="ml-auto bg-rose-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
            {{ unseenCounts.escalated_chat }}
          </span>
        </router-link>

        <!-- Reports -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Analytics</p>
        </div>
        <router-link
          :to="{ name: 'admin-reports' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('reports')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <span>Revenue Reports</span>
        </router-link>

        <!-- Invoices Management -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Billing</p>
        </div>
        <router-link
          :to="{ name: 'admin-invoices' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('invoices')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <span>Invoices</span>
        </router-link>

        <!-- Settings -->
        <div class="mt-4 px-4">
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">System</p>
        </div>
        <router-link
          :to="{ name: 'admin-settings' }"
          :class="[
            'flex items-center px-6 py-3 border-l-4 transition',
            isActive('settings')
              ? 'bg-[#2F5597]/10 border-[#2F5597] text-[#2F5597] font-semibold'
              : 'border-transparent hover:bg-gray-50 hover:border-[#2F5597]/50 text-gray-700'
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
      <div class="border-t border-gray-200 p-4 flex-shrink-0">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm truncate text-gray-900">{{ currentUser.name || 'Loading...' }}</p>
            <p class="text-xs text-gray-500 truncate">{{ currentUser.email || 'Please wait' }}</p>
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
      <div class="px-4 sm:px-5 lg:px-8 admin-header-band shadow-md" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
        <div class="flex justify-between items-center h-full w-full gap-4">
          <div class="flex items-center gap-3 flex-shrink-0">
            <button
              type="button"
              class="md:hidden text-white/80 hover:text-white transition"
              @click="sidebarOpen = true"
              aria-label="Open sidebar"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <h2 class="text-xl font-bold text-white whitespace-nowrap">
              <slot name="title">Admin Dashboard</slot>
            </h2>
          </div>
          <div class="flex items-center gap-3 flex-shrink-0">
            <!-- Search Console -->
            <div class="relative hidden sm:block" ref="searchRef">
              <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-white/50 pointer-events-none z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search quotes, orders, customers…"
                class="pl-9 pr-8 py-2 w-64 lg:w-80 rounded-lg text-sm bg-white/15 text-white placeholder-white/50 border border-white/20 focus:outline-none focus:bg-white/25 focus:border-white/40 transition"
                @keydown.enter="submitSearch"
                @keydown.escape="closeSearch"
                @focus="showSearchDropdown = true"
              />
              <button v-if="searchQuery" @click="clearSearch" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-white/50 hover:text-white/90 transition p-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>

              <!-- Results dropdown -->
              <transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 translate-y-1 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 translate-y-1 scale-95"
              >
                <div v-if="showSearchDropdown && searchQuery.trim()" class="absolute top-full mt-2 left-0 w-72 lg:w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden">
                  <p class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 bg-gray-50">Search in</p>
                  <button @mousedown.prevent="navigateSearch('quotes')" class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-blue-50/60 transition group">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                      <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-900 group-hover:text-[#2F5597]">Quotes</p>
                      <p class="text-xs text-gray-400 truncate">"{{ searchQuery }}"</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-[#2F5597] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                  <button @mousedown.prevent="navigateSearch('orders')" class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-blue-50/60 transition group border-t border-gray-50">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-900 group-hover:text-[#2F5597]">Orders</p>
                      <p class="text-xs text-gray-400 truncate">"{{ searchQuery }}"</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-[#2F5597] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                  <button @mousedown.prevent="navigateSearch('customers')" class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-blue-50/60 transition group border-t border-gray-50">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                      <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-900 group-hover:text-[#2F5597]">Customers</p>
                      <p class="text-xs text-gray-400 truncate">"{{ searchQuery }}"</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-[#2F5597] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                  <div class="px-4 py-2 bg-gray-50/80 border-t border-gray-100 text-[11px] text-gray-400">
                    Press <kbd class="bg-white border border-gray-200 rounded px-1 py-px text-gray-600 font-mono text-[10px]">Enter</kbd> to search in Quotes
                  </div>
                </div>
              </transition>
            </div>

            <!-- Notifications Bell -->
            <div class="relative" ref="notifRef">
              <button
                @click="toggleNotifications"
                class="relative p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span v-if="totalNotifCount > 0" class="absolute -top-0.5 -right-0.5 bg-rose-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 ring-2 ring-[#2F5597]">
                  {{ totalNotifCount > 99 ? '99+' : totalNotifCount }}
                </span>
              </button>

              <!-- Notification Dropdown -->
              <transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 translate-y-1 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 translate-y-1 scale-95"
              >
                <div v-if="showNotifDropdown" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden">
                  <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between" style="background: linear-gradient(135deg, #2F5597, #1e3a6b);">
                    <p class="text-sm font-semibold text-white">Notifications</p>
                    <span class="text-[10px] font-bold bg-white/20 text-white px-2 py-0.5 rounded-full">{{ totalNotifCount }} new</span>
                  </div>
                  <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                    <!-- Pending Quotes -->
                    <router-link v-if="stats.pending_quotes > 0" :to="{ name: 'admin-quotes' }" @click="showNotifDropdown = false" class="flex items-start gap-3 px-4 py-3 hover:bg-blue-50/60 transition group">
                      <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 group-hover:text-[#2F5597]">{{ stats.pending_quotes }} Pending Quote{{ stats.pending_quotes > 1 ? 's' : '' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Quotes awaiting your review and approval</p>
                      </div>
                      <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0">{{ stats.pending_quotes }}</span>
                    </router-link>

                    <!-- Processing Orders -->
                    <router-link v-if="stats.processing_orders > 0" :to="{ name: 'admin-orders' }" @click="showNotifDropdown = false" class="flex items-start gap-3 px-4 py-3 hover:bg-blue-50/60 transition group">
                      <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 group-hover:text-[#2F5597]">{{ stats.processing_orders }} Order{{ stats.processing_orders > 1 ? 's' : '' }} Processing</p>
                        <p class="text-xs text-gray-500 mt-0.5">Orders currently being fulfilled</p>
                      </div>
                      <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0">{{ stats.processing_orders }}</span>
                    </router-link>

                    <!-- Chat Escalations -->
                    <router-link v-if="escalatedChatCount > 0" :to="{ name: 'admin-chat' }" @click="showNotifDropdown = false" class="flex items-start gap-3 px-4 py-3 hover:bg-blue-50/60 transition group">
                      <div class="w-9 h-9 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/></svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 group-hover:text-[#2F5597]">{{ escalatedChatCount }} Chat Escalation{{ escalatedChatCount > 1 ? 's' : '' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Customer chats needing admin response</p>
                      </div>
                      <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0">{{ escalatedChatCount }}</span>
                    </router-link>

                    <!-- Pending Account Approvals -->
                    <router-link v-if="stats.pending_users > 0" :to="{ name: 'AdminCustomers' }" @click="showNotifDropdown = false" class="flex items-start gap-3 px-4 py-3 hover:bg-blue-50/60 transition group">
                      <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 group-hover:text-[#2F5597]">{{ stats.pending_users }} Account{{ stats.pending_users > 1 ? 's' : '' }} Awaiting Approval</p>
                        <p class="text-xs text-gray-500 mt-0.5">New user registrations pending review</p>
                      </div>
                      <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0">{{ stats.pending_users }}</span>
                    </router-link>

                    <!-- Overdue Invoices -->
                    <router-link v-if="stats.overdue_invoices > 0" :to="{ name: 'admin-invoices' }" @click="showNotifDropdown = false" class="flex items-start gap-3 px-4 py-3 hover:bg-blue-50/60 transition group">
                      <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 group-hover:text-[#2F5597]">{{ stats.overdue_invoices }} Overdue Invoice{{ stats.overdue_invoices > 1 ? 's' : '' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Invoices past their due date</p>
                      </div>
                      <span class="bg-red-100 text-red-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0">{{ stats.overdue_invoices }}</span>
                    </router-link>

                    <!-- Empty State -->
                    <div v-if="totalNotifCount === 0" class="px-4 py-8 text-center">
                      <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      <p class="text-sm text-gray-400">All caught up!</p>
                    </div>
                  </div>
                </div>
              </transition>
            </div>

            <!-- User Avatar -->
            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-white/20 text-white text-xs font-bold flex-shrink-0">
              {{ currentUser.name ? currentUser.name.charAt(0).toUpperCase() : 'A' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Page Content -->
      <div class="flex-1 overflow-auto overflow-x-hidden p-4 sm:p-6 lg:p-8 bg-gray-100">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

const NOTIF_SEEN_STORAGE_KEY = 'armely_admin_seen_notifications_v1'

const router = useRouter()
const route = useRoute()

const sidebarOpen = ref(false)
const showNotifDropdown = ref(false)
const notifRef = ref(null)
const searchRef = ref(null)
const searchQuery = ref('')
const showSearchDropdown = ref(false)

const currentUser = ref({
  name: '',
  email: ''
})

const stats = ref({
  pending_quotes: 0,
  processing_orders: 0,
  overdue_invoices: 0,
  pending_users: 0,
})

const escalatedChatCount = ref(0)
const seenBaseline = ref({
  pending_quotes: 0,
  processing_orders: 0,
  escalated_chat: 0,
  overdue_invoices: 0,
  pending_users: 0,
})

const getCurrentCounts = () => ({
  pending_quotes: Number(stats.value.pending_quotes || 0),
  processing_orders: Number(stats.value.processing_orders || 0),
  escalated_chat: Number(escalatedChatCount.value || 0),
  overdue_invoices: Number(stats.value.overdue_invoices || 0),
  pending_users: Number(stats.value.pending_users || 0),
})

const loadSeenBaseline = () => {
  try {
    const raw = localStorage.getItem(NOTIF_SEEN_STORAGE_KEY)
    if (!raw) return

    const parsed = JSON.parse(raw)
    seenBaseline.value = {
      pending_quotes: Number(parsed.pending_quotes || 0),
      processing_orders: Number(parsed.processing_orders || 0),
      escalated_chat: Number(parsed.escalated_chat || 0),
      overdue_invoices: Number(parsed.overdue_invoices || 0),
      pending_users: Number(parsed.pending_users || 0),
    }
  } catch {
    // Ignore malformed local storage payload.
  }
}

const persistSeenBaseline = () => {
  localStorage.setItem(NOTIF_SEEN_STORAGE_KEY, JSON.stringify(seenBaseline.value))
}

const clampBaselineToCurrent = () => {
  const current = getCurrentCounts()
  let changed = false

  ;['pending_quotes', 'processing_orders', 'escalated_chat', 'overdue_invoices', 'pending_users'].forEach((key) => {
    if (seenBaseline.value[key] > current[key]) {
      seenBaseline.value[key] = current[key]
      changed = true
    }
  })

  if (changed) {
    persistSeenBaseline()
  }
}

const markNotificationSeen = (key) => {
  const current = getCurrentCounts()
  seenBaseline.value[key] = current[key]
  persistSeenBaseline()
}

const markAllNotificationsSeen = () => {
  seenBaseline.value = getCurrentCounts()
  persistSeenBaseline()
}

const suppressedByRoute = computed(() => {
  const path = route.path
  return {
    pending_quotes: path.includes('/admin/quotes'),
    processing_orders: path.includes('/admin/orders'),
    escalated_chat: path.includes('/admin/chat'),
    overdue_invoices: path.includes('/admin/invoices'),
    pending_users: path.includes('/admin/customers'),
  }
})

const unseenCounts = computed(() => {
  const current = getCurrentCounts()
  return {
    pending_quotes: suppressedByRoute.value.pending_quotes
      ? 0
      : Math.max(0, current.pending_quotes - Number(seenBaseline.value.pending_quotes || 0)),
    processing_orders: suppressedByRoute.value.processing_orders
      ? 0
      : Math.max(0, current.processing_orders - Number(seenBaseline.value.processing_orders || 0)),
    escalated_chat: suppressedByRoute.value.escalated_chat
      ? 0
      : Math.max(0, current.escalated_chat - Number(seenBaseline.value.escalated_chat || 0)),
    overdue_invoices: suppressedByRoute.value.overdue_invoices
      ? 0
      : Math.max(0, current.overdue_invoices - Number(seenBaseline.value.overdue_invoices || 0)),
    pending_users: suppressedByRoute.value.pending_users
      ? 0
      : Math.max(0, current.pending_users - Number(seenBaseline.value.pending_users || 0)),
  }
})

const totalNotifCount = computed(() => {
  return (unseenCounts.value.pending_quotes || 0)
    + (unseenCounts.value.processing_orders || 0)
    + (unseenCounts.value.escalated_chat || 0)
    + (unseenCounts.value.overdue_invoices || 0)
    + (unseenCounts.value.pending_users || 0)
})

const toggleNotifications = () => {
  const willOpen = !showNotifDropdown.value
  showNotifDropdown.value = willOpen
  if (willOpen) {
    markAllNotificationsSeen()
  }
}

const navigateSearch = (section) => {
  const q = searchQuery.value.trim()
  closeSearch()
  if (!q) return
  const dest = {
    quotes: { name: 'admin-quotes', query: { search: q } },
    orders: { name: 'admin-orders', query: { search: q } },
    customers: { name: 'AdminCustomers', query: { search: q } },
  }
  router.push(dest[section] || dest.quotes)
}

const submitSearch = () => {
  const q = searchQuery.value.trim()
  if (!q) return
  const lower = q.toLowerCase()
  let section = 'quotes'
  if (lower.includes('@') || lower.startsWith('cust')) section = 'customers'
  else if (/^ord/i.test(lower) || /^#?\d{4,}/.test(lower)) section = 'orders'
  navigateSearch(section)
}

const clearSearch = () => {
  searchQuery.value = ''
  showSearchDropdown.value = false
}

const closeSearch = () => {
  showSearchDropdown.value = false
  searchQuery.value = ''
}

const handleClickOutside = (e) => {
  if (notifRef.value && !notifRef.value.contains(e.target)) {
    showNotifDropdown.value = false
  }
  if (searchRef.value && !searchRef.value.contains(e.target)) {
    showSearchDropdown.value = false
  }
}

const isActive = (section) => {
  const path = route.path
  if (section === 'orders') {
    // Don't match /admin/orders/tracking
    return path.includes('/admin/orders') && !path.includes('/admin/orders/tracking')
  }
  return path.includes(`/admin/${section}`)
}

const syncSeenWithCurrentRoute = (path) => {
  if (path.includes('/admin/quotes')) {
    markNotificationSeen('pending_quotes')
  }

  if (path.includes('/admin/orders')) {
    markNotificationSeen('processing_orders')
  }

  if (path.includes('/admin/chat')) {
    markNotificationSeen('escalated_chat')
  }

  if (path.includes('/admin/invoices')) {
    markNotificationSeen('overdue_invoices')
  }

  if (path.includes('/admin/customers')) {
    markNotificationSeen('pending_users')
  }
}

watch(
  () => route.path,
  (path) => {
    sidebarOpen.value = false
    syncSeenWithCurrentRoute(path)
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
      clampBaselineToCurrent()
      syncSeenWithCurrentRoute(route.path)
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
      clampBaselineToCurrent()
      syncSeenWithCurrentRoute(route.path)
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
  loadSeenBaseline()
  fetchCurrentUser()
  fetchStats()
  fetchEscalatedCount()
  document.addEventListener('click', handleClickOutside)
  // Optionally refresh stats every 30 seconds
  setInterval(fetchStats, 30000)
  setInterval(fetchEscalatedCount, 30000)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Unified header band — same height for sidebar logo + top bar */
.admin-header-band {
  height: 72px;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

/* Smooth transitions */
a {
  @apply transition-all duration-200;
}

/* Thin scrollbar for sidebar nav */
.sidebar-nav::-webkit-scrollbar {
  width: 4px;
}
.sidebar-nav::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-nav::-webkit-scrollbar-thumb {
  background: rgba(47, 85, 151, 0.20);
  border-radius: 4px;
}
.sidebar-nav::-webkit-scrollbar-thumb:hover {
  background: rgba(47, 85, 151, 0.40);
}
/* Firefox */
.sidebar-nav {
  scrollbar-width: thin;
  scrollbar-color: rgba(47, 85, 151, 0.20) transparent;
}
</style>
