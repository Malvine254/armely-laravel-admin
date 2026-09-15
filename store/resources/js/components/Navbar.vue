<template>
  <nav class="sticky top-0 z-[100] isolate overflow-x-clip bg-white shadow-[0_4px_18px_rgba(15,42,82,0.10)]">
    <!-- Utility bar -->
    <div v-if="showUtilityBar" class="hidden bg-[#2F5597] text-white lg:block">
      <div class="mx-auto flex h-10 max-w-[1600px] items-center justify-between px-5 text-xs font-medium">
        <div class="flex items-center gap-2 text-blue-50">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3 4.5 6v5.4c0 4.7 3.2 8.1 7.5 9.6 4.3-1.5 7.5-4.9 7.5-9.6V6L12 3Zm-3 9 2 2 4-5"/></svg>
          <span>Trusted by businesses worldwide</span>
        </div>
        <div class="flex items-center divide-x divide-white/20">
          <button type="button" class="px-4 transition hover:text-cyan-200" @click="goToMessages">Help Center</button>
          <button type="button" class="px-4 transition hover:text-cyan-200" @click="goToOrders">Track Order</button>
          <button type="button" class="px-4 transition hover:text-cyan-200" @click="goToProducts">Quick Order</button>
          <button type="button" class="px-4 transition hover:text-cyan-200" @click="goToCart">Request a Quote</button>
          <button type="button" class="pl-4 transition hover:text-cyan-200" @click="goToMessages">Contact Us</button>
        </div>
      </div>
    </div>
    <div class="w-full px-3 sm:px-4 lg:px-5">
      <div class="flex min-h-20 w-full flex-wrap items-center justify-between gap-x-2 py-3 lg:min-h-24 lg:justify-center lg:gap-x-4 2xl:gap-x-7">
        <!-- Logo Section -->
        <button type="button" class="mr-auto flex flex-shrink-0 cursor-pointer items-center gap-2 transition hover:opacity-95 sm:gap-3 lg:mr-0" aria-label="Go to Armely Store home" @click="goToHome">
          <span class="flex h-16 w-16 items-center justify-center sm:h-20 sm:w-20">
            <img
              :src="normalizeLocalAssetUrl('/images/logo/armely-store-logo.png')"
              alt="Armely Store — Smart technology. Seamless Procurement"
              class="h-full w-full object-contain"
            >
          </span>
        </button>

        <!-- Global catalog search -->
        <form data-nav-search class="relative order-2 mx-3 hidden min-w-[18rem] max-w-[680px] basis-[34rem] flex-1 lg:flex xl:mx-5 2xl:mx-7" role="search" @submit.prevent="submitNavSearch">
          <div class="flex h-12 min-w-0 flex-1 overflow-hidden rounded-lg border border-slate-300 bg-white transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
            <input
              v-model="navSearchQuery"
              type="search"
              class="min-w-0 flex-1 border-0 bg-transparent px-4 text-sm text-slate-800 outline-none placeholder:text-slate-400"
              placeholder="Search products, categories, or part numbers..."
              aria-label="Search store catalog"
              autocomplete="off"
              :aria-expanded="showSearchHistory"
              aria-controls="nav-search-history"
              @focus="openSearchHistory"
              @input="openSearchHistory"
              @keydown.down.prevent="highlightNextHistory"
              @keydown.up.prevent="highlightPreviousHistory"
              @keydown.esc="closeSearchHistory"
            >
            <button type="submit" class="flex w-14 items-center justify-center bg-[#0b3b82] text-white transition hover:bg-blue-700" aria-label="Search">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
            </button>
          </div>
          <div v-if="showSearchHistory && filteredSearchHistory.length" id="nav-search-history" class="absolute inset-x-0 top-full z-[180] mt-2 overflow-hidden rounded-lg border border-slate-200 bg-white py-2 shadow-xl" role="listbox">
            <div class="flex items-center justify-between px-4 pb-1.5 pt-1">
              <span class="text-xs font-semibold uppercase text-slate-500">Recent searches</span>
              <button type="button" class="text-xs font-semibold text-[#2F5597] hover:text-blue-700" @mousedown.prevent @click="clearSearchHistory">Clear all</button>
            </div>
            <div v-for="(term, index) in filteredSearchHistory" :key="term" class="group flex items-center" :class="activeHistoryIndex === index ? 'bg-slate-100' : 'hover:bg-slate-50'" role="option" :aria-selected="activeHistoryIndex === index">
              <button type="button" class="flex min-w-0 flex-1 items-center gap-3 px-4 py-2.5 text-left text-sm text-slate-800" @mousedown.prevent @click="selectSearchHistory(term)">
                <svg class="h-4 w-4 flex-none text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l2.5 1.5M21 12a9 9 0 1 1-3.2-6.9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 4v5h-5"/></svg>
                <span class="truncate">{{ term }}</span>
              </button>
              <button type="button" class="mr-2 flex h-8 w-8 flex-none items-center justify-center text-slate-400 hover:text-slate-700" :aria-label="`Remove ${term} from search history`" title="Remove" @mousedown.prevent @click="removeSearchHistory(term)">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>
              </button>
            </div>
          </div>
        </form>

        <!-- Dynamic category row -->
        <div data-category-menu class="order-4 -mx-3 mt-3 hidden min-h-14 w-[calc(100%+1.5rem)] flex-none items-stretch justify-center bg-[#2F5597] sm:-mx-4 sm:w-[calc(100%+2rem)] lg:-mx-5 lg:w-[calc(100%+2.5rem)] lg:flex">
          <div ref="categoryMenuRef" class="mx-3 flex w-[calc(100%-1.5rem)] items-stretch justify-center sm:mx-5 sm:w-[calc(100%-2.5rem)] lg:mx-8 lg:w-[calc(100%-4rem)] 2xl:mx-10 2xl:w-[calc(100%-5rem)]">
          <div
            v-for="(cat, catIdx) in primaryCategories"
            :key="cat.value"
            class="relative flex min-w-0 flex-1 items-center"
            @mouseenter="openCategoryMenu(cat, $event)"
            @mouseleave="categoryDropdownOpen = null"
          >
            <button
              type="button"
              class="flex h-full w-full min-w-0 items-center justify-center gap-1 border-b-[3px] border-transparent px-1.5 py-2 text-sm font-semibold text-white transition hover:bg-white/10 hover:text-cyan-200 2xl:px-2"
              :class="isCategoryActive(cat) ? 'border-cyan-300 bg-[#244a86] text-white' : ''"
              @click="toggleCategoryDropdownAt(cat, $event)"
              :aria-expanded="categoryDropdownOpen === cat.value"
              :aria-current="isCategoryActive(cat) ? 'page' : undefined"
              aria-haspopup="menu"
            >
              <span class="min-w-0 truncate">{{ cat.name }}</span>
              <svg class="h-3.5 w-3.5 flex-shrink-0 transition-transform" :class="categoryDropdownOpen === cat.value ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
              <div
                v-if="categoryDropdownOpen === cat.value"
                class="mega-menu-panel z-[150] overflow-hidden rounded-xl border border-white/10 bg-white shadow-2xl"
                :style="megaMenuStyle"
              >
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-[#2F5597] to-[#1d3f73] px-6 py-4">
                  <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 flex-none items-center justify-center rounded-lg bg-white/15 text-white">
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="categoryIcon(cat)"></svg>
                    </span>
                    <div>
                      <p class="text-sm font-bold text-white">{{ cat.name }}</p>
                      <p class="text-xs font-medium text-white/70">{{ cat.children?.length || 0 }} vendors available</p>
                    </div>
                  </div>
                  <button
                    type="button"
                    class="hidden flex-none items-center gap-1 rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/25 sm:inline-flex"
                    @click="browseProducts(cat.value)"
                  >
                    View all
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                </div>
                <div class="mega-menu-grid grid max-h-[32rem] grid-cols-3 gap-x-4 gap-y-0.5 overflow-y-auto p-4 sm:grid-cols-4">
                  <button
                    v-for="sub in cat.children"
                    :key="`${cat.value}-${sub.value}`"
                    type="button"
                    class="flex min-w-0 items-center justify-between gap-2 rounded-lg border-l-[3px] border-transparent px-3 py-2 text-left text-sm text-slate-600 transition hover:border-[#2F5597] hover:bg-slate-50 hover:text-[#2F5597]"
                    :class="isVendorActive(sub) ? 'border-[#2F5597] bg-slate-50 font-semibold text-[#2F5597]' : ''"
                    :aria-current="isVendorActive(sub) ? 'page' : undefined"
                    @click="browseCategoryVendor(cat.value, sub.name)"
                  >
                    <span class="min-w-0 flex-1 truncate">{{ sub.name }}</span>
                    <span v-if="sub.count" class="flex-none rounded-full bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400">{{ sub.count }}</span>
                  </button>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-4 py-3">
                  <button
                    type="button"
                    class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-[#2F5597] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#244a86]"
                    @click="browseProducts(cat.value)"
                  >
                    View all in {{ cat.name }}
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </button>
                </div>
              </div>
            </transition>
          </div>

          <!-- More Categories Dropdown -->
          <div v-if="overflowCategories.length > 0" class="relative flex min-w-0 flex-1 items-center" @mouseenter="openMoreCategoriesMenu($event)" @mouseleave="moreCategoriesOpen = false">
            <button
              type="button"
              class="flex h-full w-full items-center justify-center gap-1 border-b-[3px] border-transparent px-1.5 py-2 text-sm font-semibold text-slate-100 transition hover:bg-white/10 hover:text-cyan-300 2xl:px-2"
              :class="hasActiveOverflowCategory ? 'border-cyan-300 bg-[#244a86] text-white' : ''"
              @click="toggleMoreCategoriesAt($event)"
            >
              More Categories
              <svg class="w-3.5 h-3.5 transition-transform" :class="moreCategoriesOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
              <div v-if="moreCategoriesOpen" class="mega-menu-panel z-[150] overflow-hidden rounded-xl border border-white/10 bg-white shadow-2xl" :style="megaMenuStyle">
                <div class="flex items-center gap-3 border-b border-slate-100 bg-gradient-to-r from-[#2F5597] to-[#1d3f73] px-6 py-4">
                  <span class="flex h-9 w-9 flex-none items-center justify-center rounded-lg bg-white/15 text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
                  </span>
                  <div>
                    <p class="text-sm font-bold text-white">More Categories</p>
                    <p class="text-xs font-medium text-white/70">Browse every vendor category</p>
                  </div>
                </div>
                <div class="grid grid-cols-[13rem_minmax(0,1fr)]">
                  <div class="max-h-[32rem] overflow-y-auto border-r border-slate-100 bg-slate-50 py-2">
                    <button
                      v-for="cat in overflowCategories"
                      :key="cat.value"
                      type="button"
                      class="flex w-full min-w-0 items-center justify-between gap-2 border-l-[3px] border-transparent px-4 py-2.5 text-left text-sm font-semibold transition"
                      :class="isCategoryActive(cat) ? 'border-l-[3px] border-[#2F5597] bg-white text-[#2F5597]' : (activeMoreCategory?.value === cat.value ? 'border-l-[3px] border-[#2F5597]/40 bg-white text-[#2F5597]' : 'text-slate-600 hover:bg-white hover:text-[#2F5597]')"
                      :aria-current="isCategoryActive(cat) ? 'page' : undefined"
                      @mouseenter="activeMoreCategoryValue = cat.value"
                      @focus="activeMoreCategoryValue = cat.value"
                      @click="activeMoreCategoryValue = cat.value"
                    >
                      <span class="min-w-0 flex-1 truncate">{{ cat.name }}</span>
                      <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </button>
                  </div>

                  <div class="max-h-[32rem] overflow-y-auto p-4">
                    <template v-if="activeMoreCategory">
                      <div class="mega-menu-grid grid grid-cols-3 gap-x-4 gap-y-0.5">
                        <button
                          v-for="sub in activeMoreCategory.children"
                          :key="`${activeMoreCategory.value}-${sub.value}`"
                          type="button"
                          class="flex min-w-0 items-center justify-between gap-2 rounded-lg border-l-[3px] border-transparent px-3 py-2 text-left text-sm text-slate-600 transition hover:border-[#2F5597] hover:bg-slate-50 hover:text-[#2F5597]"
                          :class="isVendorActive(sub) ? 'border-[#2F5597] bg-slate-50 font-semibold text-[#2F5597]' : ''"
                          :aria-current="isVendorActive(sub) ? 'page' : undefined"
                          @click="browseCategoryVendor(activeMoreCategory.value, sub.name)"
                        >
                          <span class="min-w-0 flex-1 truncate">{{ sub.name }}</span>
                          <span v-if="sub.count" class="flex-none rounded-full bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400">{{ sub.count }}</span>
                        </button>
                      </div>
                      <button
                        type="button"
                        class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-lg bg-[#2F5597] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#244a86]"
                        @click="browseProducts(activeMoreCategory.value)"
                      >
                        View all in {{ activeMoreCategory.name }}
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                      </button>
                    </template>
                  </div>
                </div>
              </div>
            </transition>
          </div>

          </div>
        </div>

        <!-- Right Section Icons -->
        <div class="order-3 ml-auto flex flex-shrink-0 items-center gap-1 text-[#102f61] sm:gap-2 lg:ml-0">
          <button v-if="authStore.isAuthenticated" type="button" class="hidden" aria-label="Orders" @click="goToOrders">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 4h10v3H7V4ZM5 7h14v14H5V7Zm4 4h6m-6 4h6"/></svg>
            <span class="pointer-events-none absolute right-0 top-12 hidden whitespace-nowrap rounded bg-white px-2 py-1 text-xs font-medium text-slate-900 shadow-lg group-hover:block">Orders</span>
          </button>
          <!-- Cart Icon - Always visible (guest + authenticated) -->
          <button type="button" class="group relative order-1 ml-2 flex cursor-pointer items-center gap-2 border-l border-slate-200 py-1 pl-5 pr-2 text-[#102f61] transition hover:text-blue-700 xl:ml-4" @click="goToCart">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m10-9l2 9m-9 0h14m-5-9v9"></path>
            </svg>
            <span v-if="cartStore.cartCount > 0" class="absolute left-9 top-0 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#0b3b82] px-1 text-[11px] font-bold text-white">{{ cartStore.cartCount }}</span>
            <span class="hidden text-left sm:block">
              <span class="block text-sm font-bold">Cart</span>
              <span class="block text-xs font-semibold text-slate-500">{{ formattedCartTotal }}</span>
            </span>
          </button>

          <!-- Authenticated User Features -->
          <template v-if="authStore.isAuthenticated">
            <!-- Favorites Icon - Only for authenticated users -->
            <button type="button" v-if="authStore.isAuthenticated" class="hidden" @click="goToFavorites">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
              <span v-if="favoritesStore.favoriteCount > 0" class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">{{ favoritesStore.favoriteCount }}</span>
              <span class="hidden group-hover:block absolute top-12 right-0 bg-slate-100 px-2 py-1 rounded text-xs whitespace-nowrap text-slate-900">Favorites</span>
            </button>

            <!-- Authenticated Account Menu -->
            <div ref="accountMenuRef" class="relative order-2 ml-2 hidden lg:block">
              <button
                type="button"
                class="flex items-center gap-2 rounded-xl border border-transparent px-2.5 py-2 text-[#102f61] transition hover:border-blue-100 hover:bg-blue-50"
                :class="accountMenuOpen ? 'border-blue-200 bg-blue-50' : ''"
                :aria-expanded="accountMenuOpen ? 'true' : 'false'"
                aria-haspopup="menu"
                @click.stop="accountMenuOpen = !accountMenuOpen"
              >
                <!-- Profile Picture or Initials -->
                <img v-if="userProfilePictureUrl && !userAvatarLoadFailed" :src="userProfilePictureUrl" :alt="authStore.user?.name" class="h-10 w-10 rounded-full border border-blue-100 object-cover" @error="handleUserAvatarError">
                <div v-else class="flex h-10 w-10 items-center justify-center rounded-full border border-blue-100 bg-blue-50 text-xs font-bold text-[#0b3b82]">{{ userInitials }}</div>
                <span class="text-sm font-medium">Hi, {{ userFirstName }} 👋</span>
                <svg class="h-4 w-4 flex-shrink-0 transition-transform" :class="accountMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                </svg>
              </button>
              <!-- Authenticated Dropdown Menu -->
              <div v-if="accountMenuOpen" class="absolute right-0 top-full z-[200] mt-2 w-64 overflow-hidden rounded-xl border border-blue-300/30 bg-gradient-to-b from-[#0b4aa0] to-[#073b89] py-2 shadow-[0_18px_45px_rgba(7,59,137,0.30)] ring-1 ring-black/5" role="menu" @click="accountMenuOpen = false">
                <div class="px-4 py-2 border-b border-white/20">
                  <div class="flex items-center gap-3 mb-2">
                    <img v-if="userProfilePictureUrl && !userAvatarLoadFailed" :src="userProfilePictureUrl" :alt="authStore.user?.name" class="w-10 h-10 rounded-full object-cover border border-slate-400" @error="handleUserAvatarError">
                    <div v-else class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold bg-gradient-to-br from-cyan-400 to-blue-500">{{ userInitials }}</div>
                    <div>
                      <div class="font-semibold text-sm text-white">Hi, {{ userFirstName }} 👋</div>
                      <div class="text-xs text-slate-300">{{ authStore.user?.email }}</div>
                    </div>
                  </div>
                  <div v-if="authStore.user?.company_name" class="text-xs text-slate-300">{{ authStore.user?.company_name }}</div>
                </div>
                <router-link to="/account" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">My Account</router-link>
                <router-link to="/orders" v-if="authStore.hasFeatureAccess('orders')" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">My Orders</router-link>
                <router-link to="/messages" v-if="authStore.hasFeatureAccess('messages')" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">Messages</router-link>
                <router-link to="/favorites" class="flex w-full items-center justify-between px-4 py-2 text-left hover:bg-white/10 transition text-slate-100"><span>Favorites</span><span v-if="favoritesStore.favoriteCount > 0" class="rounded-full bg-rose-500 px-2 py-0.5 text-xs font-semibold text-white">{{ favoritesStore.favoriteCount }}</span></router-link>
                <router-link to="/quotes" v-if="authStore.hasFeatureAccess('quotes')" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">My Quotes</router-link>
                <router-link to="/invoices" v-if="authStore.hasFeatureAccess('invoices')" class="block w-full px-4 py-2 text-left hover:bg-white/10 transition text-slate-100">Invoices</router-link>
                <div class="border-t border-white/20 my-2"></div>
                <button @click="handleLogout" class="w-full px-4 py-2 text-left hover:bg-rose-500/20 transition text-rose-400"><strong>Sign Out</strong></button>
              </div>
            </div>
          </template>

          <!-- Unauthenticated User - Login/Sign Up Buttons -->
          <template v-else>
            <div class="order-2 ml-2 hidden items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-[#102f61] shadow-sm lg:flex">
              <router-link to="/login" class="transition hover:text-blue-700">
                Log In
              </router-link>
              <span class="px-2 text-slate-300" aria-hidden="true">/</span>
              <router-link to="/register" class="transition hover:text-blue-700">
                Sign Up
              </router-link>
            </div>
          </template>

      <!-- Mobile Menu Button -->
          <button
            class="order-3 rounded-lg p-2 text-[#102f61] transition hover:bg-blue-50 hover:text-blue-700 lg:hidden"
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
      <div v-if="mobileMenuOpen" class="lg:hidden pb-4">
        <div class="rounded-lg border border-white/20 overflow-hidden" style="background: #2F5597;">
          <form data-nav-search class="relative border-b border-white/10 p-3 lg:hidden" role="search" @submit.prevent="submitNavSearch">
            <div class="flex h-11 overflow-hidden rounded-lg bg-white">
              <input v-model="navSearchQuery" type="search" class="min-w-0 flex-1 px-3 text-sm text-slate-800 outline-none" placeholder="Search products or part numbers..." aria-label="Search store catalog" autocomplete="off" :aria-expanded="showSearchHistory" aria-controls="mobile-nav-search-history" @focus="openSearchHistory" @input="openSearchHistory" @keydown.down.prevent="highlightNextHistory" @keydown.up.prevent="highlightPreviousHistory" @keydown.esc="closeSearchHistory">
              <button type="submit" class="flex w-11 items-center justify-center bg-blue-600 text-white" aria-label="Search">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
              </button>
            </div>
            <div v-if="showSearchHistory && filteredSearchHistory.length" id="mobile-nav-search-history" class="absolute inset-x-3 top-full z-[180] overflow-hidden rounded-lg border border-slate-200 bg-white py-2 shadow-xl" role="listbox">
              <div class="flex items-center justify-between px-3 pb-1.5 pt-1">
                <span class="text-xs font-semibold uppercase text-slate-500">Recent searches</span>
                <button type="button" class="text-xs font-semibold text-[#2F5597]" @mousedown.prevent @click="clearSearchHistory">Clear all</button>
              </div>
              <div v-for="(term, index) in filteredSearchHistory" :key="term" class="flex items-center" :class="activeHistoryIndex === index ? 'bg-slate-100' : ''" role="option" :aria-selected="activeHistoryIndex === index">
                <button type="button" class="flex min-w-0 flex-1 items-center gap-3 px-3 py-2.5 text-left text-sm text-slate-800" @mousedown.prevent @click="selectSearchHistory(term)">
                  <svg class="h-4 w-4 flex-none text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l2.5 1.5M21 12a9 9 0 1 1-3.2-6.9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 4v5h-5"/></svg>
                  <span class="truncate">{{ term }}</span>
                </button>
                <button type="button" class="mr-1 flex h-9 w-9 flex-none items-center justify-center text-slate-400" :aria-label="`Remove ${term} from search history`" @mousedown.prevent @click="removeSearchHistory(term)">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
              </div>
            </div>
          </form>
          <!-- Products Section -->
          <div class="border-b border-white/10">
            <button type="button" @click="mobileProductsOpen = !mobileProductsOpen" class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-100 hover:bg-white/10 transition">
              <span>Products</span>
              <svg class="w-4 h-4 transition-transform" :class="mobileProductsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div v-if="mobileProductsOpen" style="background: #2F5597;">
              <div v-for="cat in productCategories" :key="cat.value">
                <button
                  type="button"
                  @click="toggleMobileCategory(cat.value)"
                  class="w-full flex items-center justify-between gap-3 px-8 py-2.5 text-sm text-slate-300 hover:bg-white/10 transition"
                  :class="isCategoryActive(cat) ? 'border-l-[3px] border-cyan-300 bg-white/10 font-semibold text-white' : 'border-l-[3px] border-transparent'"
                  :aria-current="isCategoryActive(cat) ? 'page' : undefined"
                >
                  <span class="min-w-0 flex-1 truncate text-left">{{ cat.name }}</span>
                  <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="mobileOpenCategory === cat.value ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
                <div v-if="mobileOpenCategory === cat.value" class="pl-10">
                  <button
                    type="button"
                    @click="browseProducts(cat.value)"
                    class="w-full text-left px-8 py-2 text-sm font-semibold text-cyan-300 hover:bg-white/10 transition"
                  >
                    View all in {{ cat.name }}
                  </button>
                  <button
                    v-for="sub in cat.children"
                    :key="`${cat.value}-${sub.value}`"
                    type="button"
                    @click="browseCategoryVendor(cat.value, sub.name)"
                    class="w-full border-l-[3px] border-transparent px-8 py-2 text-left text-sm text-slate-300 transition hover:bg-white/10"
                    :class="isVendorActive(sub) ? 'border-cyan-300 bg-white/15 font-semibold text-white' : ''"
                    :aria-current="isVendorActive(sub) ? 'page' : undefined"
                  >
                    {{ sub.name }}
                  </button>
                </div>
              </div>
            </div>
          </div>
          <template v-if="authStore.isAuthenticated">
            <div class="px-4 py-3 border-b border-white/10">
              <div class="font-semibold text-white">Hi, {{ userFirstName }} 👋</div>
              <div class="text-xs text-slate-300">{{ authStore.user?.email }}</div>
            </div>
            <button type="button" @click="goToAccount" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">My Account</button>
            <button type="button" @click="goToCart" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">My Quote / Cart</button>
            <button v-if="authStore.hasFeatureAccess('orders')" type="button" @click="goToOrders" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">My Orders</button>
            <button type="button" @click="goToMessages" v-if="authStore.hasFeatureAccess('messages')" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">Messages</button>
            <button type="button" @click="goToFavorites" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">Favorites</button>
            <button v-if="authStore.hasFeatureAccess('quotes')" type="button" @click="router.push({ name: 'quotes' }); closeMobileMenu()" class="w-full text-left px-4 py-3 text-sm text-slate-100 transition hover:bg-white/10">My Quotes</button>
            <button type="button" @click="handleLogout" class="w-full text-left px-4 py-3 text-sm font-semibold text-rose-400 transition hover:bg-rose-500/20">Sign Out</button>
          </template>
          <template v-else>
            <div class="flex items-center px-4 py-3 text-sm font-semibold text-slate-100">
              <router-link to="/login" @click="closeMobileMenu" class="transition hover:text-white">Log In</router-link>
              <span class="px-2 text-slate-500" aria-hidden="true">/</span>
              <router-link to="/register" @click="closeMobileMenu" class="transition hover:text-white">Sign Up</router-link>
            </div>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '../stores/cartStore'
import { useFavoritesStore } from '../stores/favoritesStore'
import { useAuthStore } from '../stores/authStore'
import { useToastStore } from '../stores/toastStore'
import { normalizeLocalAssetUrl, resolveProfilePictureUrl } from '../services/runtimeConfig'
import { buildProductsLocation, parseProductsRouteFilters } from '../services/productRoute'
import api from '../services/api'

const router = useRouter()
const route = useRoute()
// Kept behind a flag so the utility links can be restored without rebuilding the markup.
const showUtilityBar = false
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const toastStore = useToastStore()
const mobileMenuOpen = ref(false)
const categoryDropdownOpen = ref(null)
const moreCategoriesOpen = ref(false)
const activeMoreCategoryValue = ref(null)
const mobileProductsOpen = ref(false)
const mobileOpenCategory = ref(null)
const navSearchQuery = ref(parseProductsRouteFilters(route).q ? String(parseProductsRouteFilters(route).q) : '')
const searchHistory = ref([])
const showSearchHistory = ref(false)
const activeHistoryIndex = ref(-1)
const accountMenuOpen = ref(false)
const userAvatarLoadFailed = ref(false)
const accountMenuRef = ref(null)
const categoryMenuRef = ref(null)
const primaryCategoryLimit = ref(6)
let categoryMenuResizeObserver = null
const SEARCH_HISTORY_KEY = 'armely_products_search_history'
const SEARCH_HISTORY_LIMIT = 12

const normalizeSearchTerm = value => String(value || '').trim().replace(/\s+/g, ' ')
const filteredSearchHistory = computed(() => {
  const query = normalizeSearchTerm(navSearchQuery.value).toLowerCase()
  return searchHistory.value
    .filter(term => !query || term.toLowerCase().includes(query))
    .slice(0, 8)
})

const saveSearchHistory = () => {
  try {
    localStorage.setItem(SEARCH_HISTORY_KEY, JSON.stringify(searchHistory.value))
  } catch {
    // Search remains usable when browser storage is unavailable.
  }
}

const loadSearchHistory = () => {
  try {
    const stored = JSON.parse(localStorage.getItem(SEARCH_HISTORY_KEY) || '[]')
    searchHistory.value = Array.isArray(stored)
      ? stored.map(normalizeSearchTerm).filter(term => term.length > 1).slice(0, SEARCH_HISTORY_LIMIT)
      : []
  } catch {
    searchHistory.value = []
  }
}

const rememberSearch = value => {
  const term = normalizeSearchTerm(value)
  if (term.length < 2) return
  searchHistory.value = [
    term,
    ...searchHistory.value.filter(item => item.toLowerCase() !== term.toLowerCase()),
  ].slice(0, SEARCH_HISTORY_LIMIT)
  saveSearchHistory()
}

const openSearchHistory = () => {
  activeHistoryIndex.value = -1
  showSearchHistory.value = filteredSearchHistory.value.length > 0
}

const closeSearchHistory = () => {
  showSearchHistory.value = false
  activeHistoryIndex.value = -1
}

const selectSearchHistory = term => {
  navSearchQuery.value = term
  submitNavSearch()
}

const removeSearchHistory = term => {
  searchHistory.value = searchHistory.value.filter(item => item !== term)
  saveSearchHistory()
  activeHistoryIndex.value = -1
  showSearchHistory.value = filteredSearchHistory.value.length > 0
}

const clearSearchHistory = () => {
  searchHistory.value = []
  saveSearchHistory()
  closeSearchHistory()
}

const highlightNextHistory = () => {
  if (!showSearchHistory.value || filteredSearchHistory.value.length === 0) return
  activeHistoryIndex.value = (activeHistoryIndex.value + 1) % filteredSearchHistory.value.length
}

const highlightPreviousHistory = () => {
  if (!showSearchHistory.value || filteredSearchHistory.value.length === 0) return
  activeHistoryIndex.value = activeHistoryIndex.value <= 0
    ? filteredSearchHistory.value.length - 1
    : activeHistoryIndex.value - 1
}

const handleDocumentClick = event => {
  if (accountMenuOpen.value && !accountMenuRef.value?.contains(event.target)) {
    accountMenuOpen.value = false
  }
  if (!event.target?.closest?.('[data-category-menu]')) {
    categoryDropdownOpen.value = null
    moreCategoriesOpen.value = false
  }
  if (!event.target?.closest?.('[data-nav-search]')) {
    closeSearchHistory()
  }
}

const submitNavSearch = () => {
  if (activeHistoryIndex.value >= 0 && filteredSearchHistory.value[activeHistoryIndex.value]) {
    navSearchQuery.value = filteredSearchHistory.value[activeHistoryIndex.value]
  }
  const normalizedQuery = normalizeSearchTerm(navSearchQuery.value)
  rememberSearch(normalizedQuery)
  closeSearchHistory()
  router.push(buildProductsLocation(normalizedQuery ? { q: normalizedQuery } : {}))
  closeAll()
}

watch(
  () => normalizeSearchTerm(parseProductsRouteFilters(route).q),
  value => {
    navSearchQuery.value = value ? String(value) : ''
  }
)

const productCategories = ref([])
const MENU_CATEGORIES_STORAGE_KEY = 'store_menu_categories_v8_count_sorted_capped_3000'
const MENU_CATEGORIES_REQUEST_REVISION = 'count-sorted-taxonomy-20260821-1'
const MENU_CATEGORIES_SOFT_TTL_MS = 15 * 60 * 1000
const MENU_CATEGORIES_HARD_TTL_MS = 7 * 24 * 60 * 60 * 1000

const measureCategoryWidth = name => {
  if (typeof document === 'undefined') return 150
  const canvas = measureCategoryWidth.canvas || (measureCategoryWidth.canvas = document.createElement('canvas'))
  const context = canvas.getContext('2d')
  if (!context) return 150
  context.font = '600 14px "Instrument Sans", ui-sans-serif, system-ui, sans-serif'
  return Math.min(240, Math.max(116, Math.ceil(context.measureText(String(name || '')).width) + 46))
}

const recalculatePrimaryCategories = () => {
  const menuWidth = categoryMenuRef.value?.clientWidth || 0
  const categories = productCategories.value
  if (menuWidth <= 0 || categories.length === 0) return

  const moreCategoriesWidth = 154
  const widths = categories.map(category => measureCategoryWidth(category.name))
  const allCategoriesWidth = widths.reduce((total, width) => total + width, 0)

  if (allCategoriesWidth <= menuWidth) {
    primaryCategoryLimit.value = categories.length
    return
  }

  const usableWidth = Math.max(0, menuWidth - moreCategoriesWidth)
  let usedWidth = 0
  let count = 0
  for (const width of widths) {
    if (usedWidth + width > usableWidth) break
    usedWidth += width
    count += 1
  }

  primaryCategoryLimit.value = Math.max(1, count)
}

const primaryCategories = computed(() => productCategories.value.slice(0, primaryCategoryLimit.value))
const overflowCategories = computed(() => productCategories.value.slice(primaryCategoryLimit.value))
const activeCategoryFilter = computed(() => String(parseProductsRouteFilters(route).category || '').trim().toLowerCase())
const normalizeMenuFilterValue = value => String(value || '')
  .trim()
  .toUpperCase()
  .replace(/[^A-Z0-9]+/g, ' ')
  .replace(/\s+/g, ' ')
  .trim()
const activeVendorFilters = computed(() => {
  const filters = parseProductsRouteFilters(route)
  const raw = filters.vendors ?? filters.vendor ?? ''
  return String(raw).split(',').map(normalizeMenuFilterValue).filter(Boolean)
})
const isCategoryActive = category => {
  const active = activeCategoryFilter.value
  if (!active) return false

  return [category?.value, category?.slug, category?.name, category?.segment_code]
    .map(value => String(value || '').trim().toLowerCase())
    .filter(Boolean)
    .includes(active)
}
const isVendorActive = vendor => {
  if (activeVendorFilters.value.length === 0) return false
  const candidates = [vendor?.name, vendor?.value, vendor?.slug]
    .map(normalizeMenuFilterValue)
    .filter(Boolean)
  return candidates.some(candidate => activeVendorFilters.value.includes(candidate))
}
const hasActiveOverflowCategory = computed(() => overflowCategories.value.some(isCategoryActive))
const activeMoreCategory = computed(() => {
  if (overflowCategories.value.length === 0) return null
  return overflowCategories.value.find(cat => cat.value === activeMoreCategoryValue.value) || overflowCategories.value[0]
})

const normalizeMenuCategories = (rows) => {
  if (!Array.isArray(rows)) return []

  return rows
    .map(cat => ({
      id: cat?.id,
      name: cat?.name,
      slug: cat?.slug,
      value: cat?.value ?? cat?.slug ?? cat?.name,
      segment_code: cat?.segment_code,
      count: Number(cat?.count || 0),
      children: Array.isArray(cat?.children)
        ? cat.children
            .map(child => ({
              id: child?.id,
              name: child?.name,
              slug: child?.slug,
              value: child?.value ?? child?.slug ?? child?.name,
              segment_code: child?.segment_code,
              count: Number(child?.count || 0),
              type: child?.type || 'vendor',
            }))
            .filter(child => typeof child.name === 'string' && child.name.trim() !== '' && child.count > 0)
        : [],
    }))
    .filter(cat => typeof cat.name === 'string' && cat.name.trim() !== '' && cat.count > 0)
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
    const { data } = await api.get('/menu-categories', {
      params: { catalog_revision: MENU_CATEGORIES_REQUEST_REVISION },
    })
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
  loadSearchHistory()
  document.addEventListener('click', handleDocumentClick)
  window.addEventListener('resize', handleMegaMenuResize)
  categoryMenuResizeObserver = new ResizeObserver(recalculatePrimaryCategories)
  if (categoryMenuRef.value) categoryMenuResizeObserver.observe(categoryMenuRef.value)
  const cached = loadMenuCategoriesFromStorage()
  if (cached.data.length > 0) {
    productCategories.value = cached.data
  }

  if (cached.stale || cached.data.length === 0) {
    fetchMenuCategories()
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleDocumentClick)
  window.removeEventListener('resize', handleMegaMenuResize)
  categoryMenuResizeObserver?.disconnect()
})

watch(productCategories, () => nextTick(recalculatePrimaryCategories), { deep: true })

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const toggleMoreCategories = () => {
  moreCategoriesOpen.value = !moreCategoriesOpen.value

  if (moreCategoriesOpen.value && !activeMoreCategoryValue.value && overflowCategories.value.length > 0) {
    activeMoreCategoryValue.value = overflowCategories.value[0].value
  }
}

const toggleCategoryDropdown = (category) => {
  if (!Array.isArray(category?.children) || category.children.length === 0) {
    browseProducts(category?.value)
    return
  }

  moreCategoriesOpen.value = false
  categoryDropdownOpen.value = categoryDropdownOpen.value === category.value ? null : category.value
}

// Mega menus are positioned in fixed coordinates computed from the trigger's
// actual position, then clamped to stay fully within the viewport. This works
// for every current category and any future ones added — no index/side
// guesswork required.
const MEGA_MENU_WIDTH = 1024
const MEGA_MENU_MARGIN = 16
const megaMenuStyle = ref({})
let megaMenuTriggerEl = null

const positionMegaMenu = (triggerEl) => {
  if (!triggerEl || typeof window === 'undefined') return
  megaMenuTriggerEl = triggerEl
  const rect = triggerEl.getBoundingClientRect()
  const viewportWidth = window.innerWidth
  const width = Math.min(MEGA_MENU_WIDTH, Math.max(280, viewportWidth - MEGA_MENU_MARGIN * 2))
  let left = rect.left
  left = Math.max(MEGA_MENU_MARGIN, Math.min(left, viewportWidth - width - MEGA_MENU_MARGIN))
  megaMenuStyle.value = {
    position: 'fixed',
    top: `${rect.bottom + 4}px`,
    left: `${left}px`,
    width: `${width}px`,
  }
}

const handleMegaMenuResize = () => {
  if (megaMenuTriggerEl && (categoryDropdownOpen.value || moreCategoriesOpen.value)) {
    positionMegaMenu(megaMenuTriggerEl)
  }
}

const openCategoryMenu = (cat, event) => {
  categoryDropdownOpen.value = cat.value
  positionMegaMenu(event.currentTarget)
}

const toggleCategoryDropdownAt = (category, event) => {
  toggleCategoryDropdown(category)
  if (categoryDropdownOpen.value === category?.value) {
    positionMegaMenu(event.currentTarget)
  }
}

const openMoreCategoriesMenu = (event) => {
  moreCategoriesOpen.value = true
  positionMegaMenu(event.currentTarget)
}

const toggleMoreCategoriesAt = (event) => {
  toggleMoreCategories()
  if (moreCategoriesOpen.value) {
    positionMegaMenu(event.currentTarget)
  }
}

const toggleMobileCategory = (category) => {
  mobileOpenCategory.value = mobileOpenCategory.value === category ? null : category
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

const formattedCartTotal = computed(() => `$${Number(cartStore.cartTotal || 0).toLocaleString(undefined, {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
})}`)

const userProfilePictureUrl = computed(() => {
  return resolveProfilePictureUrl(
    authStore.user?.profile_picture_url,
    authStore.user?.profile_picture
  )
})

watch(userProfilePictureUrl, () => {
  userAvatarLoadFailed.value = false
})

watch(
  () => authStore.user,
  () => {
    userAvatarLoadFailed.value = false
  },
  { deep: true }
)

const handleUserAvatarError = () => {
  userAvatarLoadFailed.value = true
}

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

const goToHome = () => {
  closeAll()
  router.push({ name: 'home' })
}

const browseProducts = (category = null) => {
  const query = category ? { category } : {}
  router.push(buildProductsLocation(query))
  closeAll()
}

// Distinct, recognizable icons per category for the mega menu headers.
const CATEGORY_ICON_PATHS = {
  laptop: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5V15H4V5.5Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2 15h20l-1.5 3.5a1 1 0 0 1-.9.5H4.4a1 1 0 0 1-.9-.5L2 15Z"/>',
  desktop: '<rect x="5" y="3" width="10" height="14" rx="1.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6.5h4M8 10h4M8 13.5h1.5M18 8v8m0 0-2-2m2 2 2-2"/>',
  monitor: '<rect x="3" y="4" width="18" height="12" rx="1.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 20h8M12 16v4"/>',
  networking: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v4m-5 6V9m10 4V9M5 13a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm14 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM12 7a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 13v-1a5 5 0 0 1 10 0v1"/>',
  server: '<rect x="3" y="3" width="18" height="7" rx="1.5" stroke-width="1.8"/><rect x="3" y="13" width="18" height="7" rx="1.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 6.5h.01M7 16.5h.01"/>',
  printer: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 9V3h12v6M6 18h12v3H6v-3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 9H4.5A1.5 1.5 0 0 0 3 10.5V15a1.5 1.5 0 0 0 1.5 1.5H6m12-7.5h1.5A1.5 1.5 0 0 1 21 10.5V15a1.5 1.5 0 0 1-1.5 1.5H18"/>',
  memory: '<rect x="5" y="6" width="14" height="12" rx="1.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 3v3M12 3v3M16 3v3M8 18v3M12 18v3M16 18v3"/>',
  software: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m8 8-4 4 4 4m8-8 4 4-4 4m-2-9-2 14"/>',
  audio: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 10v4a1 1 0 0 0 1 1h2l4 3.5V5.5L7 9H5a1 1 0 0 0-1 1Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 8.5a5 5 0 0 1 0 7m2.5-9.5a8 8 0 0 1 0 12"/>',
  accessories: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 8V5.5a2.5 2.5 0 0 1 5 0V8m-8 0h11a1 1 0 0 1 1 1v9a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V9a1 1 0 0 1 1-1Z"/>',
  default: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7l9-4 9 4-9 4-9-4Zm0 0v10l9 4m0-14v14m9-14v10l-9 4"/>',
}

const categoryIcon = (cat) => {
  const label = String(cat?.name || cat?.value || '').toLowerCase()
  const match = Object.keys(CATEGORY_ICON_PATHS).find((key) => key !== 'default' && label.includes(key))
    || (label.includes('notebook') ? 'laptop' : null)
    || (label.includes('workstation') || label.includes('tower') ? 'desktop' : null)
    || (label.includes('display') ? 'monitor' : null)
    || (label.includes('storage') && label.includes('memory') ? 'memory' : null)
    || (label.includes('storage') ? 'server' : null)
    || (label.includes('scanner') ? 'printer' : null)
    || (label.includes('speaker') || label.includes('headset') ? 'audio' : null)
    || (label.includes('cable') || label.includes('mount') || label.includes('bag') ? 'accessories' : null)
  return CATEGORY_ICON_PATHS[match] || CATEGORY_ICON_PATHS.default
}

const browseCategoryVendor = (category, vendor) => {
  router.push(buildProductsLocation({ category, vendor }))
  closeAll()
}

const closeAll = () => {
  accountMenuOpen.value = false
  mobileMenuOpen.value = false
  categoryDropdownOpen.value = null
  moreCategoriesOpen.value = false
  mobileProductsOpen.value = false
  mobileOpenCategory.value = null
}

const handleLogout = async () => {
  await authStore.logout()
  toastStore.addToast('Logged out successfully', 'success')
  await router.replace({ name: 'login' })
  closeAll()
}

</script>

<style scoped>
.store-menu-scroll {
  scrollbar-color: #38bdf8 #2F5597;
  scrollbar-width: thin;
}

.store-menu-scroll::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.store-menu-scroll::-webkit-scrollbar-track {
  background: #2F5597;
}

.store-menu-scroll::-webkit-scrollbar-thumb {
  background: #38bdf8;
  border-radius: 999px;
}

.store-menu-scroll::-webkit-scrollbar-thumb:hover {
  background: #67e8f9;
}
</style>
