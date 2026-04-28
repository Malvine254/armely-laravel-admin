<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <!-- Back Button -->
      <button @click="goBack" class="mb-6 flex items-center gap-2 text-sm transition" style="color: #2F5597;" @mouseenter="$event.target.style.opacity='0.7'" @mouseleave="$event.target.style.opacity='1'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Products
      </button>

      <!-- Product Detail Container -->
      <div v-if="product">
        <!-- Top section: image + key info side by side -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="grid grid-cols-1 lg:grid-cols-5 gap-0">

            <!-- Product Image Section — 2 cols -->
            <div class="lg:col-span-2 bg-gray-50 p-6 lg:p-8 flex flex-col items-center justify-center">
              <div class="w-full aspect-square max-w-sm flex items-center justify-center rounded-xl bg-white border border-gray-100 shadow-sm p-6">
                <img
                  v-if="selectedImage"
                  :src="selectedImage"
                  :alt="product.productName"
                  class="max-w-full max-h-full object-contain transition-transform duration-300 hover:scale-105"
                  loading="eager"
                  fetchpriority="high"
                  decoding="async"
                  sizes="(min-width: 1024px) 40vw, 100vw"
                  @error="selectedImage = ''"
                />
                <div v-else class="text-center flex flex-col items-center justify-center gap-3">
                  <div class="w-24 h-24 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                    <span class="text-3xl font-bold text-slate-400 select-none">{{ (product.vendorName || product.manufacturer || 'P').charAt(0).toUpperCase() }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-slate-500">{{ product.vendorName || product.manufacturer || 'Product' }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Image not available</p>
                  </div>
                </div>
              </div>

              <!-- Thumbnails -->
              <div v-if="normalizedImages.length > 1" class="flex gap-2 mt-4 justify-center flex-wrap">
                <button
                  v-for="(image, idx) in normalizedImages"
                  :key="`thumb-${idx}`"
                  class="w-14 h-14 rounded-lg border-2 overflow-hidden transition-all duration-200 flex-shrink-0"
                  :class="selectedImage === image ? 'border-blue-600 ring-2 ring-blue-100 shadow-md' : 'border-gray-200 hover:border-gray-400'"
                  @click="selectedImage = image"
                  type="button"
                >
                  <img :src="image" :alt="`Image ${idx + 1}`" class="w-full h-full object-cover" loading="lazy" decoding="async" />
                </button>
              </div>
            </div>

            <!-- Product Info Section — 3 cols -->
            <div class="lg:col-span-3 p-6 lg:p-8 flex flex-col">
              <!-- Status + Vendor badge row -->
              <div class="flex items-center gap-2 mb-3">
                <span v-if="product.discontinueProduct" class="px-2.5 py-0.5 bg-red-50 text-red-600 text-xs font-semibold rounded-full border border-red-200">End of Life</span>
                <span v-else class="px-2.5 py-0.5 text-xs font-semibold rounded-full border" style="background-color: #eef5fc; color: #2F5597; border-color: #bad5f0;">Active</span>
                <span v-if="getProductVendor(product) !== 'N/A'" class="px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">{{ getProductVendor(product) }}</span>
              </div>

              <!-- Product Name -->
              <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-4">{{ product.productName }}</h1>

              <!-- Meta pills -->
              <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-500 mb-5">
                <span><span class="font-medium text-gray-700">SKU</span> {{ getProductSku(product) }}</span>
                <span><span class="font-medium text-gray-700">ID</span> {{ product.productId }}</span>
                <span v-if="product.billingModel"><span class="font-medium text-gray-700">Billing</span> {{ product.billingModel }}</span>
              </div>
              <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex items-center gap-3">
                  <span class="text-sm font-semibold" :class="getStockTone(product)">{{ getStockLabel(product) }}</span>
                  <span class="text-sm text-slate-500">{{ getWarehouseSummary(product) }}</span>
                </div>
                <div class="text-sm text-slate-600">
                  <span class="font-medium text-slate-700">Availability:</span> {{ getProductMetaPrimary(product) }} / {{ getProductMetaSecondary(product) }}
                </div>
              </div>

              <!-- Price highlight card -->
              <div v-if="product.productPrice && product.productPrice.length > 0" class="rounded-xl p-5 mb-5" style="background: linear-gradient(135deg, #eef5fc 0%, #f8fbff 100%); border: 1px solid #d6e8f7;">
                <div class="flex items-baseline gap-2 mb-1">
                  <span class="text-3xl font-extrabold" style="color: #2F5597;">{{ formatAdjustedCurrency(product.productPrice[0].rsPrice) }}</span>
                  <span class="text-sm text-gray-500">/ unit</span>
                </div>
                <div v-if="product.productPrice.length > 1" class="mt-3 pt-3 border-t border-blue-100">
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Volume Pricing</p>
                  <div class="space-y-1.5">
                    <div v-for="(price, idx) in product.productPrice.slice(1, 4)" :key="idx" class="flex items-center justify-between text-sm">
                      <span class="text-gray-600">
                        Qty {{ price.minQty }}<span v-if="price.maxQty"> – {{ price.maxQty }}</span>+
                      </span>
                      <span class="font-semibold text-gray-800">{{ formatAdjustedCurrency(price.rsPrice) }}</span>
                    </div>
                  </div>
                  <p v-if="product.productPrice.length > 4" class="text-xs text-gray-400 mt-2">+{{ product.productPrice.length - 4 }} more tiers</p>
                </div>
              </div>
              <div v-else class="rounded-xl p-5 mb-5 bg-gray-50 border border-gray-200 text-center">
                <p class="text-sm text-gray-500">Contact us for pricing</p>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-3 mt-auto">
                <button
                  @click="addToQuote"
                  :disabled="isOutOfStock(product)"
                  class="flex-1 px-5 py-3 text-white font-semibold rounded-xl transition-all duration-200 text-sm shadow-sm hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                  style="background-color: #2F5597;"
                  @mouseenter="!isOutOfStock(product) && ($event.target.style.backgroundColor='#244a85')"
                  @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                >
                  <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add to Quote
                  </span>
                </button>
                <button @click="addToFavorite" class="px-5 py-3 border-2 font-semibold rounded-xl transition-all duration-200 text-sm" :class="isFavorite ? 'bg-red-50 border-red-300 text-red-500' : 'border-gray-300 text-gray-600 hover:border-gray-400 hover:bg-gray-50'">
                  <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" :fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    {{ isFavorite ? 'Saved' : 'Save' }}
                  </span>
                </button>
                <button @click="openShareModal(product)" class="px-4 py-3 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition-all duration-200" title="Share Product">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C9.886 12.511 11.326 12 12.889 12c2.87 0 5.322 1.723 6.296 4.182m-16.338 0A6.986 6.986 0 019.111 12c1.563 0 3.003.511 4.205 1.342M15 6a3 3 0 11-6 0 3 3 0 016 0zm6 14a2 2 0 11-4 0 2 2 0 014 0zM7 20a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </button>
              </div>
            </div>

          </div>
        </div>

        <!-- Description + Details tabs section -->
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <!-- Tab bar -->
          <div class="flex border-b border-gray-200">
            <button
              v-if="productDescription"
              @click="activeTab = 'description'"
              class="px-6 py-3.5 text-sm font-semibold transition-colors relative"
              :class="activeTab === 'description' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700'"
            >
              Description
              <span v-if="activeTab === 'description'" class="absolute bottom-0 left-0 right-0 h-0.5 rounded-full" style="background-color: #2F5597;"></span>
            </button>
            <button
              @click="activeTab = 'specs'"
              class="px-6 py-3.5 text-sm font-semibold transition-colors relative"
              :class="activeTab === 'specs' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700'"
            >
              Specifications
              <span v-if="activeTab === 'specs'" class="absolute bottom-0 left-0 right-0 h-0.5 rounded-full" style="background-color: #2F5597;"></span>
            </button>
            <button
              @click="activeTab = 'reviews'"
              class="px-6 py-3.5 text-sm font-semibold transition-colors relative"
              :class="activeTab === 'reviews' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700'"
            >
              Reviews
              <span v-if="reviewStats.total > 0" class="ml-1 text-xs font-normal text-gray-400">({{ reviewStats.total }})</span>
              <span v-if="activeTab === 'reviews'" class="absolute bottom-0 left-0 right-0 h-0.5 rounded-full" style="background-color: #2F5597;"></span>
            </button>
          </div>

          <!-- Tab content -->
          <div class="p-6 lg:p-8">
            <!-- Description Tab -->
            <div v-if="activeTab === 'description' && productDescription">
              <div class="relative">
                <div
                  class="prose prose-sm max-w-none text-gray-700 leading-relaxed overflow-hidden transition-all duration-300"
                  :class="{ 'max-h-40': !descriptionExpanded && isDescriptionLong }"
                  v-html="productDescription"
                ></div>
                <div v-if="isDescriptionLong && !descriptionExpanded" class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
              </div>
              <button
                v-if="isDescriptionLong"
                @click="descriptionExpanded = !descriptionExpanded"
                class="mt-3 text-sm font-semibold transition-colors flex items-center gap-1"
                style="color: #2F5597;"
                @mouseenter="$event.target.style.opacity='0.7'"
                @mouseleave="$event.target.style.opacity='1'"
              >
                {{ descriptionExpanded ? 'Show Less' : 'Read More' }}
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': descriptionExpanded }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
              </button>
            </div>

            <!-- Specs Tab -->
            <div v-if="activeTab === 'specs'">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #eef5fc;">
                    <svg class="w-4 h-4" fill="none" stroke="#2F5597" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">SKU / Part No.</p>
                    <p class="text-sm font-medium text-gray-900">{{ getProductSku(product) }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #eef5fc;">
                    <svg class="w-4 h-4" fill="none" stroke="#2F5597" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Vendor</p>
                    <p class="text-sm font-medium text-gray-900">{{ getProductVendor(product) }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #eef5fc;">
                    <svg class="w-4 h-4" fill="none" stroke="#2F5597" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Product ID</p>
                    <p class="text-sm font-medium text-gray-900">{{ product.productId }}</p>
                  </div>
                </div>
                <div v-if="product.billingModel" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #eef5fc;">
                    <svg class="w-4 h-4" fill="none" stroke="#2F5597" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Billing Model</p>
                    <p class="text-sm font-medium text-gray-900">{{ product.billingModel }}</p>
                  </div>
                </div>
                <div v-if="product.billingFrequency" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #eef5fc;">
                    <svg class="w-4 h-4" fill="none" stroke="#2F5597" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Billing Frequency</p>
                    <p class="text-sm font-medium text-gray-900">{{ product.billingFrequency }}</p>
                  </div>
                </div>
                <div v-if="product.billingTerm" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #eef5fc;">
                    <svg class="w-4 h-4" fill="none" stroke="#2F5597" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Billing Term</p>
                    <p class="text-sm font-medium text-gray-900">{{ product.billingTerm }}</p>
                  </div>
                </div>
              </div>

              <!-- Categories -->
              <div v-if="product.productCategories && product.productCategories.length > 0" class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Categories</p>
                <div class="flex flex-wrap gap-2">
                  <span v-for="(cat, idx) in product.productCategories.slice(0, 8)" :key="idx" class="px-3 py-1 text-xs font-medium rounded-full" style="background-color: #eef5fc; color: #2F5597;">{{ typeof cat === 'object' ? cat.categoryName : cat }}</span>
                  <span v-if="product.productCategories.length > 8" class="px-3 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">+{{ product.productCategories.length - 8 }}</span>
                </div>
              </div>
            </div>

            <!-- Reviews Tab -->
            <div v-if="activeTab === 'reviews'">
              <!-- Reviews Summary -->
              <div class="flex flex-col sm:flex-row gap-6 mb-8">
                <!-- Average Rating -->
                <div class="flex flex-col items-center justify-center px-6 py-4 bg-gray-50 rounded-xl min-w-[140px]">
                  <span class="text-4xl font-bold text-gray-900">{{ reviewStats.average.toFixed(1) }}</span>
                  <div class="flex items-center gap-0.5 mt-1">
                    <svg v-for="star in 5" :key="'avg-' + star" class="w-4 h-4" :class="star <= Math.round(reviewStats.average) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                  </div>
                  <span class="text-xs text-gray-500 mt-1">{{ reviewStats.total }} {{ reviewStats.total === 1 ? 'review' : 'reviews' }}</span>
                </div>
                <!-- Star Breakdown -->
                <div class="flex-1 space-y-1.5">
                  <div v-for="star in [5, 4, 3, 2, 1]" :key="'bar-' + star" class="flex items-center gap-2">
                    <span class="text-xs text-gray-600 w-6 text-right">{{ star }}★</span>
                    <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                      <div class="h-full rounded-full transition-all duration-500" style="background-color: #2F5597;" :style="{ width: reviewStats.total > 0 ? ((reviewStats.breakdown[star] || 0) / reviewStats.total * 100) + '%' : '0%' }"></div>
                    </div>
                    <span class="text-xs text-gray-500 w-6">{{ reviewStats.breakdown[star] || 0 }}</span>
                  </div>
                </div>
              </div>

              <!-- Write Review Button -->
              <div class="mb-6">
                <button
                  v-if="authStore.isAuthenticated && !showReviewForm"
                  @click="showReviewForm = true"
                  class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition"
                  style="background-color: #2F5597;"
                  @mouseenter="$event.target.style.backgroundColor='#1f4788'"
                  @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                >
                  Write a Review
                </button>
                <p v-else-if="!authStore.isAuthenticated" class="text-sm text-gray-500">
                  <button @click="router.push({ name: 'login', query: { redirect: route.fullPath } })" class="font-semibold underline" style="color: #2F5597;">Log in</button> to write a review.
                </p>
              </div>

              <!-- Review Form -->
              <div v-if="showReviewForm" class="mb-8 p-5 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Write Your Review</h3>
                <!-- Star Picker -->
                <div class="mb-4">
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Rating *</label>
                  <div class="flex gap-1">
                    <button v-for="star in 5" :key="'pick-' + star" type="button" @click="reviewForm.rating = star" class="p-0.5 transition-transform hover:scale-110">
                      <svg class="w-7 h-7" :class="star <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </button>
                  </div>
                </div>
                <!-- Title -->
                <div class="mb-4">
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Title (optional)</label>
                  <input v-model="reviewForm.title" type="text" maxlength="150" placeholder="Summarize your experience" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:outline-none" style="focus:ring-color: #2F5597;" />
                </div>
                <!-- Body -->
                <div class="mb-4">
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Review *</label>
                  <textarea v-model="reviewForm.body" rows="4" maxlength="5000" placeholder="Share your experience with this product..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:outline-none resize-none"></textarea>
                </div>
                <!-- Image Upload -->
                <div class="mb-4">
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5">Images (optional, up to 5)</label>
                  <div class="flex flex-wrap gap-3 mb-2">
                    <div v-for="(preview, idx) in reviewImagePreviews" :key="'img-' + idx" class="relative w-20 h-20 rounded-lg overflow-hidden border border-gray-200">
                      <img :src="preview" class="w-full h-full object-cover" />
                      <button @click="removeReviewImage(idx)" type="button" class="absolute top-0.5 right-0.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">×</button>
                    </div>
                    <label v-if="reviewForm.images.length < 5" class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center cursor-pointer hover:border-gray-400 transition-colors">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                      <span class="text-[10px] text-gray-400 mt-0.5">Add</span>
                      <input type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" multiple class="hidden" @change="handleReviewImageUpload" />
                    </label>
                  </div>
                  <p class="text-[11px] text-gray-400">Max 3 MB each. JPEG, PNG, GIF, or WebP.</p>
                </div>
                <!-- Actions -->
                <div class="flex gap-3">
                  <button
                    @click="submitReview"
                    :disabled="isSubmittingReview || !reviewForm.rating || !reviewForm.body.trim()"
                    class="px-5 py-2 text-white text-sm font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background-color: #2F5597;"
                    @mouseenter="!isSubmittingReview && ($event.target.style.backgroundColor='#1f4788')"
                    @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                  >
                    {{ isSubmittingReview ? 'Submitting...' : 'Submit Review' }}
                  </button>
                  <button @click="cancelReviewForm" class="px-5 py-2 text-sm font-semibold text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                  </button>
                </div>
                <p v-if="reviewError" class="mt-3 text-sm text-red-600">{{ reviewError }}</p>
              </div>

              <!-- Review List -->
              <div v-if="reviews.length > 0" class="space-y-5">
                <div v-for="review in reviews" :key="review.id" class="p-5 bg-white border border-gray-100 rounded-xl">
                  <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background-color: #2F5597;">
                        {{ review.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-gray-900">{{ review.user?.name || 'Anonymous' }}</p>
                        <p class="text-xs text-gray-400">{{ formatReviewDate(review.created_at) }}</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <div class="flex gap-0.5">
                        <svg v-for="star in 5" :key="'r-' + review.id + '-' + star" class="w-3.5 h-3.5" :class="star <= review.rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                      </div>
                      <button
                        v-if="authStore.user && authStore.user.id === review.user_id"
                        @click="deleteReview(review.id)"
                        class="text-xs text-red-400 hover:text-red-600 transition-colors ml-2"
                        title="Delete your review"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      </button>
                    </div>
                  </div>
                  <p v-if="review.title" class="mt-3 text-sm font-semibold text-gray-800">{{ review.title }}</p>
                  <p class="mt-1.5 text-sm text-gray-600 leading-relaxed">{{ review.body }}</p>
                  <!-- Review Images -->
                  <div v-if="review.images && review.images.length > 0" class="mt-3 flex flex-wrap gap-2">
                    <a v-for="(img, idx) in review.images" :key="'ri-' + review.id + '-' + idx" :href="img" target="_blank" class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200 hover:border-blue-300 transition-colors">
                      <img :src="img" class="w-full h-full object-cover" loading="lazy" />
                    </a>
                  </div>
                </div>
              </div>
              <div v-else-if="!isLoadingReviews" class="text-center py-8 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p class="text-sm">No reviews yet. Be the first to share your experience!</p>
              </div>
              <div v-if="isLoadingReviews" class="text-center py-6">
                <div class="w-8 h-8 border-3 border-gray-200 rounded-full animate-spin mx-auto" style="border-block-start-color: #2F5597;"></div>
              </div>
              <!-- Load More -->
              <div v-if="reviewsNextPage" class="mt-5 text-center">
                <button @click="loadMoreReviews" :disabled="isLoadingReviews" class="px-5 py-2 text-sm font-semibold border border-gray-300 rounded-lg hover:bg-gray-50 transition disabled:opacity-50" style="color: #2F5597;">
                  Load More Reviews
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-else-if="isLoading" class="bg-white rounded-lg shadow-lg p-12 text-center">
        <div class="w-12 h-12 border-4 border-gray-200 rounded-full animate-spin mx-auto mb-4" style="border-block-start-color: #2F5597;"></div>
        <p class="text-gray-600 font-semibold">Loading product details...</p>
      </div>

      <!-- Error State -->
      <div v-else class="bg-white rounded-lg shadow-lg p-12 text-center">
        <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-red-50 flex items-center justify-center text-red-600">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"></path>
          </svg>
        </div>
        <p class="text-gray-900 font-semibold mb-2">Unable to load product details</p>
        <p class="text-gray-600 mb-6">{{ loadError || 'The product could not be loaded at this time.' }}</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <button @click="retryLoadProduct" class="px-5 py-2.5 text-white font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
            Try Again
          </button>
          <button @click="goBack" class="px-5 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
            Back to Products
          </button>
        </div>
      </div>

      <!-- Related Products Section -->
      <div v-if="product && relatedProducts.length > 0" class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 lg:p-8">
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-xl font-bold text-gray-900">Related Products</h2>
          <span class="text-sm text-gray-500">Showing {{ paginatedRelated.length }} of {{ relatedProducts.length }}</span>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div v-for="relatedProduct in paginatedRelated" :key="relatedProduct.productId"
               class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-lg transition"
               style="border: 1px solid rgb(229, 231, 235);"
               @mouseenter="$event.currentTarget.style.borderColor='#cce4f5'"
               @mouseleave="$event.currentTarget.style.borderColor='rgb(229, 231, 235)'">
            <!-- Product Image -->
            <div class="bg-gradient-to-br from-gray-200 to-gray-300 h-40 flex items-center justify-center relative overflow-hidden" style="background: linear-gradient(135deg, rgb(229, 231, 235), rgb(209, 213, 219));">
              <img
                v-if="getPrimaryImageUrl(relatedProduct)"
                :src="getPrimaryImageUrl(relatedProduct)"
                :alt="relatedProduct.productName"
                class="w-full h-full object-cover"
                loading="lazy"
                decoding="async"
                sizes="(min-width: 1024px) 240px, (min-width: 640px) 50vw, 100vw"
                @error="event => event.target.style.display = 'none'"
              />
              <template v-else>
                <div class="absolute inset-0 opacity-10">
                  <div class="absolute top-2 right-2 w-12 h-12 bg-blue-400 rounded-full"></div>
                  <div class="absolute bottom-4 left-2 w-8 h-8 bg-blue-300 rounded-full"></div>
                </div>
                <div class="relative z-10 text-center">
                  <svg v-if="getProductIcon(relatedProduct.productName) === 'server'" class="w-16 h-16 mx-auto mb-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-3 8h-2V5h2v6z"/>
                  </svg>
                  <svg v-else-if="getProductIcon(relatedProduct.productName) === 'cloud'" class="w-16 h-16 mx-auto mb-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4c-1.48 0-2.85.43-4.01 1.17l1.46 1.46C10.21 5.23 11.08 5 12 5c3.04 0 5.5 2.46 5.5 5.5v.5H19c2.05 0 3.71 1.66 3.71 3.71 0 1.71-1.04 2.86-2.36 3.41z"/>
                  </svg>
                  <svg v-else-if="getProductIcon(relatedProduct.productName) === 'database'" class="w-16 h-16 mx-auto mb-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3c-4.97 0-9 2.16-9 4.5S7.03 12 12 12s9-2.16 9-4.5S16.97 3 12 3zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3zm0 7c-4.97 0-9 2.16-9 4.5S7.03 24 12 24s9-2.16 9-4.5-4.03-4.5-9-4.5zm0 5c-3.314 0-6-1.343-6-3s2.686-3 6-3 6 1.343 6 3-2.686 3-6 3z"/>
                  </svg>
                  <svg v-else class="w-16 h-16 mx-auto mb-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                  </svg>
                </div>
              </template>
            </div>

            <!-- Product Info -->
            <div class="p-4">
              <div class="flex items-start justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2" :title="buildProductHoverDetails(relatedProduct)">{{ relatedProduct.productName }}</h3>
                <span v-if="relatedProduct.discontinueProduct" class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded flex-shrink-0">EOL</span>
                <span v-else class="ml-2 px-2 py-1 text-xs font-semibold rounded flex-shrink-0" style="background-color: #cce4f4; color: #2F5597;">Active</span>
              </div>
              <div class="flex items-center justify-between gap-3 text-xs text-gray-600 mb-3">
                <p class="truncate" :title="`SKU: ${getProductSku(relatedProduct)}`">SKU: {{ getProductSku(relatedProduct) }}</p>
                <p class="truncate text-right" :title="`Vendor: ${getProductVendor(relatedProduct)}`">Vendor: {{ getProductVendor(relatedProduct) }}</p>
              </div>

              <div class="flex items-center gap-1 mb-3">
                <svg
                  v-for="star in 5"
                  :key="`related-rating-${relatedProduct.productId}-${star}`"
                  class="w-3.5 h-3.5"
                  :class="star <= Math.round(getRelatedReviewStats(relatedProduct.productId).average) ? 'text-yellow-400' : 'text-gray-300'"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-xs text-gray-500 ml-1">
                  {{ getRelatedReviewStats(relatedProduct.productId).total > 0
                    ? `${getRelatedReviewStats(relatedProduct.productId).average.toFixed(1)} (${getRelatedReviewStats(relatedProduct.productId).total})`
                    : 'No reviews' }}
                </span>
              </div>

              <!-- Pricing -->
              <div v-if="relatedProduct.productPrice && relatedProduct.productPrice.length > 0" class="mb-4">
                <p class="text-2xl font-bold" style="color: #2F5597;">{{ formatAdjustedCurrency(relatedProduct.productPrice[0].rsPrice) }}</p>
                <p class="text-xs text-gray-600">Min Qty: {{ relatedProduct.productPrice[0].minQty }}</p>
              </div>
              <div v-else class="mb-4">
                <p class="text-sm text-gray-400">Contact for price</p>
              </div>

              <!-- Features -->
              <div class="mb-4 flex flex-wrap gap-1">
                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ getProductMetaPrimary(relatedProduct) }}</span>
                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ getProductMetaSecondary(relatedProduct) }}</span>
              </div>
              <div class="mb-4 flex items-center justify-between gap-3 text-xs">
                <span class="font-semibold" :class="getStockTone(relatedProduct)">{{ getStockLabel(relatedProduct) }}</span>
                <span class="text-gray-500">{{ getWarehouseSummary(relatedProduct) }}</span>
              </div>

              <!-- Actions -->
              <div class="flex gap-2 w-full">
                <button @click="navigateToProduct(relatedProduct.productId)" class="flex-1 px-3 py-2 text-white text-sm font-semibold rounded-lg transition inline-flex items-center justify-center gap-1" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                  </svg>
                  <span>View</span>
                </button>
                <button
                  @click.stop="addRelatedToQuote(relatedProduct)"
                  :disabled="isOutOfStock(relatedProduct)"
                  class="px-3 py-2 text-white text-sm font-semibold rounded-lg transition disabled:cursor-not-allowed disabled:opacity-60"
                  style="background-color: #2F5597;"
                  @mouseenter="!isOutOfStock(relatedProduct) && ($event.target.style.backgroundColor='#1f4788')"
                  @mouseleave="$event.target.style.backgroundColor='#2F5597'"
                  :title="isOutOfStock(relatedProduct) ? 'Out of stock' : 'Add to Quote'"
                  aria-label="Add to Quote"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L5.4 5M7 13l-1.2 6.4A1 1 0 006.8 21h10.4a1 1 0 001-.8L20 13M9 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                  </svg>
                </button>
                <button @click.stop="toggleRelatedFavorite(relatedProduct)" class="px-3 py-2 rounded-lg transition border" :style="favoritesStore.isFavorite(relatedProduct.productId) ? { backgroundColor: '#cce4f4', borderColor: '#2F5597', color: '#2F5597' } : { borderColor: '#d1d5db', color: '#4b5563' }" :title="favoritesStore.isFavorite(relatedProduct.productId) ? 'Remove from Favorites' : 'Add to Favorites'">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                </button>
                <button @click.stop="openShareModal(relatedProduct)" class="px-3 py-2 rounded-lg transition border border-gray-300 text-gray-600 hover:bg-gray-50" title="Share Product">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C9.886 12.511 11.326 12 12.889 12c2.87 0 5.322 1.723 6.296 4.182m-16.338 0A6.986 6.986 0 019.111 12c1.563 0 3.003.511 4.205 1.342M15 6a3 3 0 11-6 0 3 3 0 016 0zm6 14a2 2 0 11-4 0 2 2 0 014 0zM7 20a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="relatedTotalPages > 1" class="flex items-center justify-center gap-2 mt-6 pt-5 border-t border-gray-100">
          <button
            @click="relatedPage > 1 && (relatedPage -= 1)"
            :disabled="relatedPage === 1"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm"
          >
            ← Previous
          </button>
          <div class="flex gap-1">
            <button
              v-for="page in relatedPageNumbers"
              :key="'rp-' + page"
              @click="relatedPage = page"
              :class="['px-3 py-2 rounded-lg transition text-sm', page === relatedPage ? 'text-white font-semibold' : 'border border-gray-300 text-gray-700 hover:bg-gray-50']"
              :style="page === relatedPage ? { backgroundColor: '#2F5597' } : {}"
            >
              {{ page }}
            </button>
          </div>
          <button
            @click="relatedPage < relatedTotalPages && (relatedPage += 1)"
            :disabled="relatedPage === relatedTotalPages"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition text-sm"
          >
            Next →
          </button>
        </div>
      </div>

      <div v-if="showShareModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-900/45" @click="closeShareModal"></div>
        <div class="relative w-full max-w-lg rounded-2xl border bg-white shadow-2xl" style="border-color:#cfe0f5;">
          <div class="px-5 py-4 border-b" style="border-color:#e2e8f0;">
            <h3 class="text-lg font-bold" style="color:#2F5597;">Share Product</h3>
            <p class="text-sm text-slate-600 mt-1">{{ sharingProduct?.productName || 'Selected product' }}</p>
          </div>

          <div class="p-5 space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Recipient Email (optional)</label>
              <input
                v-model="shareRecipientEmail"
                type="email"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                style="border-color:#cbd5e1;"
                placeholder="user@company.com"
              >
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Note (optional)</label>
              <textarea
                v-model="shareNote"
                rows="3"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                style="border-color:#cbd5e1;"
                placeholder="Add a message for the recipient"
              ></textarea>
            </div>

            <div v-if="shareGeneratedLink" class="rounded-lg border p-3" style="border-color:#bfdbfe;background:#eff6ff;">
              <p class="text-xs font-semibold text-slate-700 mb-1">Share Link</p>
              <p class="text-xs break-all text-slate-700">{{ shareGeneratedLink }}</p>
              <div class="mt-3 flex flex-wrap gap-2">
                <button @click="copyShareGeneratedLink" type="button" class="px-3 py-2 text-xs font-semibold rounded-lg text-white" style="background-color:#2F5597;">Copy Link</button>
                <button @click="sendShareLinkByEmail" type="button" class="px-3 py-2 text-xs font-semibold rounded-lg border" style="border-color:#2F5597;color:#2F5597;">Send to Email</button>
              </div>
            </div>
          </div>

          <div class="px-5 py-4 border-t flex justify-end gap-2" style="border-color:#e2e8f0;">
            <button @click="closeShareModal" type="button" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">Close</button>
            <button @click="submitProductShare" type="button" :disabled="shareSubmitting" class="px-4 py-2 text-sm font-semibold rounded-lg text-white disabled:opacity-60" style="background-color:#2F5597;">
              {{ shareSubmitting ? 'Generating...' : (shareGeneratedLink ? 'Regenerate Link' : 'Generate Link') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useCartStore } from '../../stores/cartStore'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useAuthStore } from '../../stores/authStore'
import Navbar from '../../components/Navbar.vue'
import { API_BASE_URL, buildStoreUrl } from '../../services/runtimeConfig'
import { usePricingSettings } from '../../composables/usePricingSettings'
import api from '../../services/api'

const router = useRouter()
const route = useRoute()
const product = ref(null)
const relatedProducts = ref([])
const toastStore = useToastStore()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()
const authStore = useAuthStore()
const selectedImage = ref('')
const isLoading = ref(false)
const activeTab = ref('description')
const loadError = ref('')
const descriptionExpanded = ref(false)
const { loadPricingSettings, getCatalogPriceWithRules, convertFromUsd, formatWithCurrency } = usePricingSettings()

// Reviews state
const reviews = ref([])
const reviewStats = ref({ total: 0, average: 0, breakdown: { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 } })
const showReviewForm = ref(false)
const isSubmittingReview = ref(false)
const isLoadingReviews = ref(false)
const reviewsNextPage = ref(null)
const reviewError = ref('')
const reviewForm = ref({ rating: 0, title: '', body: '', images: [] })
const reviewImagePreviews = ref([])
const relatedReviewStatsByProduct = ref({})
const pendingRelatedReviewStats = new Set()
const showShareModal = ref(false)
const sharingProduct = ref(null)
const shareRecipientEmail = ref('')
const shareNote = ref('')
const shareGeneratedLink = ref('')
const shareSubmitting = ref(false)

const productDetailCache = new Map()
const relatedProductsCache = new Map()
const RELATED_PER_PAGE = 8
const PRODUCT_DETAIL_CACHE_TTL_MS = 15 * 60 * 1000
const PRODUCT_DETAIL_FETCH_TIMEOUT_MS = 30000
const PRODUCT_DETAIL_STORAGE_PREFIX = 'product_detail_v1:'
const PRODUCT_RELATED_STORAGE_PREFIX = 'product_related_v1:'
const relatedPage = ref(1)

const fetchWithTimeout = async (url, options = {}, timeoutMs = PRODUCT_DETAIL_FETCH_TIMEOUT_MS) => {
  const controller = new AbortController()
  const timeoutId = setTimeout(() => controller.abort(), timeoutMs)

  try {
    return await fetch(url, {
      ...options,
      signal: controller.signal,
    })
  } catch (error) {
    if (error?.name === 'AbortError') {
      throw new Error('Request timed out while loading product details.')
    }
    throw error
  } finally {
    clearTimeout(timeoutId)
  }
}

const loadCachedPayload = (prefix, key) => {
  if (typeof window === 'undefined') return null

  try {
    const raw = window.sessionStorage.getItem(`${prefix}${key}`)
    if (!raw) return null

    const parsed = JSON.parse(raw)
    const timestamp = Number(parsed?.timestamp || 0)
    if (!timestamp || Date.now() - timestamp > PRODUCT_DETAIL_CACHE_TTL_MS) {
      window.sessionStorage.removeItem(`${prefix}${key}`)
      return null
    }

    return parsed?.data ?? null
  } catch {
    return null
  }
}

const saveCachedPayload = (prefix, key, data) => {
  if (typeof window === 'undefined') return

  try {
    window.sessionStorage.setItem(
      `${prefix}${key}`,
      JSON.stringify({
        timestamp: Date.now(),
        data,
      })
    )
  } catch {
    // Ignore storage write errors.
  }
}

const getRelatedFilterQuery = () => {
  const params = new URLSearchParams()
  const returnTo = sanitizeReturnTo(route.query.returnTo || '/products')

  try {
    const parsed = new URL(returnTo, window.location.origin)
    const source = parsed.searchParams
    const mappings = [
      ['q', 'q'],
      ['search', 'search'],
      ['minPrice', 'min_price'],
      ['min_price', 'min_price'],
      ['maxPrice', 'max_price'],
      ['max_price', 'max_price'],
      ['hide_zero_price', 'hide_zero_price'],
      ['catalog_clean', 'catalog_clean'],
      ['productType', 'product_type'],
      ['product_type', 'product_type'],
      ['category', 'category'],
    ]

    mappings.forEach(([from, to]) => {
      const value = source.get(from)
      if (value !== null && value !== '') {
        params.set(to, value)
      }
    })

    mappings.forEach(([from, to]) => {
      const value = route.query[from]
      if (!params.has(to) && value !== undefined && value !== null && value !== '') {
        params.set(to, Array.isArray(value) ? value[0] : value)
      }
    })
  } catch {
    // Ignore invalid returnTo values; sanitizeReturnTo already keeps navigation safe.
  }

  return params
}

const getRelatedCacheKey = (productId) => {
  const query = getRelatedFilterQuery().toString()
  return query ? `${productId}?${query}` : String(productId)
}

const loadRelatedProducts = async (productId, cacheKey, cachedRelated) => {
  if (cachedRelated) {
    relatedProducts.value = cachedRelated
    return
  }

  try {
    const query = getRelatedFilterQuery().toString()
    const relatedUrl = `${API_BASE_URL}/products/${productId}/related${query ? `?${query}` : ''}`
    const response = await fetchWithTimeout(relatedUrl)
    if (!response.ok) {
      relatedProducts.value = []
      relatedProductsCache.set(cacheKey, [])
      return
    }

    const json = await response.json()
    const data = json.data || json
    const loadedRelated = data.records || data || []
    relatedProducts.value = loadedRelated
    relatedProductsCache.set(cacheKey, loadedRelated)
    saveCachedPayload(PRODUCT_RELATED_STORAGE_PREFIX, cacheKey, loadedRelated)
  } catch (relatedError) {
    console.warn('Related products fetch failed:', relatedError)
    relatedProducts.value = []
  }
}

const fetchReviews = async (productId, page = 1) => {
  if (!productId) return
  isLoadingReviews.value = true
  try {
    const response = await fetch(`${API_BASE_URL}/products/${productId}/reviews?page=${page}`)
    if (!response.ok) throw new Error('Failed to load reviews')
    const json = await response.json()

    if (page === 1) {
      reviews.value = json.data || []
    } else {
      reviews.value.push(...(json.data || []))
    }

    reviewsNextPage.value = (json.meta && json.meta.current_page < json.meta.last_page) ? json.meta.current_page + 1 : null

    if (json.stats) {
      reviewStats.value = json.stats
    }
  } catch (err) {
    console.warn('Failed to fetch reviews:', err)
  } finally {
    isLoadingReviews.value = false
  }
}

const loadProductDetail = async (productId) => {
  if (!productId) return

  const cacheKey = String(productId)
  const relatedCacheKey = getRelatedCacheKey(productId)
  const cachedProduct = productDetailCache.get(cacheKey)
    ?? loadCachedPayload(PRODUCT_DETAIL_STORAGE_PREFIX, cacheKey)
  const cachedRelated = relatedProductsCache.get(relatedCacheKey)
    ?? loadCachedPayload(PRODUCT_RELATED_STORAGE_PREFIX, relatedCacheKey)

  if (cachedProduct) {
    productDetailCache.set(cacheKey, cachedProduct)
  }

  if (cachedRelated) {
    relatedProductsCache.set(relatedCacheKey, cachedRelated)
  }

  isLoading.value = true
  loadError.value = ''
  descriptionExpanded.value = false
  relatedPage.value = 1

  if (cachedProduct) {
    product.value = cachedProduct
  } else {
    product.value = null
    selectedImage.value = ''
  }

  if (cachedRelated) {
    relatedProducts.value = cachedRelated
  } else {
    relatedProducts.value = []
  }

  try {
    if (!cachedProduct) {
      const response = await fetchWithTimeout(`${API_BASE_URL}/products/${productId}`)
      if (!response.ok) {
        throw new Error(response.status === 404 ? 'Product not found.' : `Failed to fetch product (${response.status}).`)
      }

      const json = await response.json()
      const loadedProduct = json.data || json
      if (!loadedProduct || (typeof loadedProduct === 'object' && Object.keys(loadedProduct).length === 0)) {
        throw new Error('Product not found.')
      }

      product.value = loadedProduct
      productDetailCache.set(cacheKey, loadedProduct)
      saveCachedPayload(PRODUCT_DETAIL_STORAGE_PREFIX, cacheKey, loadedProduct)
    }

  } catch (error) {
    console.error('Error loading product detail:', error)
    product.value = null
    loadError.value = error instanceof Error ? error.message : 'Unable to load this product.'
  } finally {
    isLoading.value = false
  }

  if (product.value) {
    void loadRelatedProducts(productId, relatedCacheKey, cachedRelated)
    void fetchReviews(productId)
  }
}

const retryLoadProduct = () => {
  const productId = route.params.id
  if (!productId) return
  loadProductDetail(productId)
}

watch(
  () => route.params.id,
  (productId) => {
    if (!productId) return
    loadProductDetail(productId)
  },
  { immediate: true }
)

const sanitizeReturnTo = (value) => {
  const candidate = String(value || '').trim()
  if (!candidate || !candidate.startsWith('/') || candidate.startsWith('//') || candidate.startsWith('/\\')) {
    return '/products'
  }

  try {
    const parsed = new URL(candidate, window.location.origin)
    if (parsed.origin !== window.location.origin) {
      return '/products'
    }

    if (!parsed.pathname.startsWith('/products')) {
      return '/products'
    }

    return `${parsed.pathname}${parsed.search}${parsed.hash}`
  } catch {
    return '/products'
  }
}

const navigateToProduct = (productId) => {
  const returnTo = sanitizeReturnTo(route.query.returnTo || '/products')

  router.push({
    name: 'product-detail',
    params: { id: productId },
    query: {
      returnTo,
    },
  })
}

const goBack = () => {
  const returnTo = sanitizeReturnTo(route.query.returnTo)
  router.push(returnTo)
}

const formatAdjustedCurrency = (baseUsdPrice) => {
  const adjustedUsd = getCatalogPriceWithRules(Number(baseUsdPrice || 0))
  return formatWithCurrency(convertFromUsd(adjustedUsd))
}

const getProductIcon = (productName) => {
  const name = productName.toLowerCase()
  if (name.includes('server') || name.includes('instance')) return 'server'
  if (name.includes('azure') || name.includes('cloud') || name.includes('subscription')) return 'cloud'
  if (name.includes('database') || name.includes('sql')) return 'database'
  return 'default'
}

const getProductSku = (item) => {
  return String(
    item?.mfgPartNo ||
    item?.mfg_part_no ||
    item?.tdsynnexSkuNo ||
    item?.tdsynnex_sku_no ||
    item?.skuNo ||
    item?.sku_no ||
    'N/A'
  )
}

const getAvailableQuantity = (item) => {
  const qty = Number(
    item?.availableQuantity ??
    item?.totalQuantity ??
    item?.qty ??
    NaN
  )

  return Number.isFinite(qty) ? Math.max(0, qty) : null
}

const isOutOfStock = (item) => getAvailableQuantity(item) === 0

const getStockRank = (item) => {
  const qty = getAvailableQuantity(item)
  if (qty === null) return 1
  return qty <= 0 ? 2 : 0
}

const getAvailabilityByWarehouse = (item) => {
  return Array.isArray(item?.AvailabilityByWarehouse) ? item.AvailabilityByWarehouse : []
}

const getStockLabel = (item) => {
  const qty = getAvailableQuantity(item)
  if (qty !== null) {
    if (qty > 0) return `Stock: ${qty}`
    return 'Out of stock'
  }

  return 'Stock: Check availability'
}

const getStockTone = (item) => {
  const qty = getAvailableQuantity(item)
  if (qty === null) return 'text-amber-600'
  return qty > 0 ? 'text-emerald-600' : 'text-red-600'
}

const getWarehouseSummary = (item) => {
  const warehouses = getAvailabilityByWarehouse(item)
  if (warehouses.length > 0) {
    return `${warehouses.length} warehouse${warehouses.length === 1 ? '' : 's'}`
  }

  const qty = getAvailableQuantity(item)
  if (qty !== null) {
    return qty > 0 ? 'Available now' : 'Request quote'
  }

  return 'No live count'
}

const getProductMetaPrimary = (item) => {
  const billingModel = String(item?.billingModel || '').trim()
  if (billingModel) return billingModel

  const qty = getAvailableQuantity(item)
  if (qty !== null) {
    return qty > 0 ? 'In Stock' : 'Out of Stock'
  }

  return item?.discontinueProduct ? 'Legacy Product' : 'Catalog Product'
}

const getProductMetaSecondary = (item) => {
  const billingFrequency = String(item?.billingFrequency || '').trim()
  if (billingFrequency) return billingFrequency

  const qty = getAvailableQuantity(item)
  if (qty !== null) {
    return qty > 0 ? `${qty} available` : 'Request quote'
  }

  return 'Request quote'
}

const getPrimaryImageUrl = (item) => {
  const candidates = []

  const appendUrl = (value) => {
    const rawUrl = String(value || '').trim()
    if (!rawUrl) return
    const url = rawUrl.startsWith('/images/') ? buildStoreUrl(rawUrl) : rawUrl
    candidates.push(url)
  }

  const appendImages = (images) => {
    if (!Array.isArray(images)) return

    images.forEach((image) => {
      if (typeof image === 'string') {
        appendUrl(image)
        return
      }

      if (image && typeof image === 'object') {
        appendUrl(image.imageUrl || image.imageURL || image.image_url || image.url || image.thumbnailUrl)
      }
    })
  }

  appendImages(item?.productImages)
  appendImages(item?.images)
  appendUrl(item?.image_url)
  appendUrl(item?.thumbnailUrl)
  appendUrl(item?.thumbnail)

  if (candidates.length === 0) return ''

  const localCandidate = candidates.find((url) => url.startsWith('/images/') || url.includes('/images/products/'))
  return localCandidate || candidates[0]
}

const getProductVendor = (item) => {
  return String(
    item?.vendorId ||
    item?.vendor_id ||
    item?.vendorName ||
    item?.vendor_name ||
    item?.manufacturerName ||
    item?.manufacturer_name ||
    'N/A'
  )
}

const buildProductHoverDetails = (item) => {
  const lines = [
    item?.productName || 'Product',
    `SKU: ${getProductSku(item)}`,
    `Vendor: ${getProductVendor(item)}`,
  ]

  const price = Number(item?.productPrice?.[0]?.rsPrice || 0)
  if (price > 0) {
    lines.push(`Price: ${formatAdjustedCurrency(price)}`)
  }

  return lines.join('\n')
}

const normalizeImages = (source) => {
  if (!Array.isArray(source)) return []

  const seen = new Set()
  const urls = []

  source.forEach((entry) => {
    let url = ''
    if (typeof entry === 'string') {
      url = entry.trim()
    } else if (entry && typeof entry === 'object') {
      url = String(entry.imageUrl || entry.url || '').trim()
    }

    if (!url || seen.has(url)) return
    seen.add(url)
    urls.push(url)
  })

  return urls
}

const normalizedImages = computed(() => {
  if (!product.value) return []

  const urls = normalizeImages(product.value.productImages)
  if (urls.length > 0) {
    return [...urls].sort((left, right) => {
      const leftLocal = left.startsWith('/images/') || left.includes('/images/products/')
      const rightLocal = right.startsWith('/images/') || right.includes('/images/products/')
      if (leftLocal === rightLocal) return 0
      return leftLocal ? -1 : 1
    })
  }

  const imageFallback = normalizeImages(product.value.images)
  if (imageFallback.length > 0) return imageFallback

  const primary = getPrimaryImageUrl(product.value)
  if (primary) {
    return [primary]
  }

  return []
})

const productDescription = computed(() => {
  if (!product.value) return ''
  const desc = (product.value.description || '').trim()
  const name = (product.value.productName || '').trim()
  // Only show description if it's meaningful (different from product name)
  if (!desc || desc === name) return ''
  // Sanitize: allow only safe formatting tags, strip everything else
  return desc.replace(/<(?!\/?(br|b|i|strong|em|p|ul|ol|li)\b)[^>]*>/gi, '')
})

watch(normalizedImages, (images) => {
  selectedImage.value = images[0] || ''
}, { immediate: true })

watch(productDescription, (desc) => {
  activeTab.value = desc ? 'description' : 'specs'
}, { immediate: true })

onMounted(() => {
  loadPricingSettings()
})

const isFavorite = computed(() => {
  if (!product.value) return false
  return favoritesStore.isFavorite(product.value.productId)
})

const isDescriptionLong = computed(() => {
  if (!productDescription.value) return false
  // Strip HTML tags to measure text length
  const text = productDescription.value.replace(/<[^>]*>/g, '')
  return text.length > 300
})

const relatedTotalPages = computed(() => Math.ceil(relatedProducts.value.length / RELATED_PER_PAGE))

const sortedRelatedProducts = computed(() => {
  const source = relatedProducts.value
  const indexed = source.map((item, index) => ({ item, index }))
  indexed.sort((left, right) => {
    const rankDiff = getStockRank(left.item) - getStockRank(right.item)
    if (rankDiff !== 0) return rankDiff
    return left.index - right.index
  })
  return indexed.map(({ item }) => item)
})

const paginatedRelated = computed(() => {
  const start = (relatedPage.value - 1) * RELATED_PER_PAGE
  return sortedRelatedProducts.value.slice(start, start + RELATED_PER_PAGE)
})

const relatedPageNumbers = computed(() => {
  const pages = []
  for (let i = 1; i <= relatedTotalPages.value; i++) {
    pages.push(i)
  }
  return pages
})

const getRelatedReviewStats = (productId) => {
  const key = String(productId || '')
  const stats = relatedReviewStatsByProduct.value[key]
  if (!stats) {
    return { total: 0, average: 0 }
  }

  return {
    total: Number(stats.total || 0),
    average: Number(stats.average || 0)
  }
}

const loadRelatedReviewStats = async (items = []) => {
  const ids = Array.from(new Set(
    (Array.isArray(items) ? items : [])
      .map((item) => String(item?.productId || '').trim())
      .filter(Boolean)
  ))

  const idsToFetch = ids.filter((id) => {
    if (relatedReviewStatsByProduct.value[id]) return false
    if (pendingRelatedReviewStats.has(id)) return false
    return true
  })

  if (idsToFetch.length === 0) return

  await Promise.all(idsToFetch.map(async (id) => {
    pendingRelatedReviewStats.add(id)
    try {
      const response = await fetch(`${API_BASE_URL}/products/${encodeURIComponent(id)}/reviews?per_page=1`)
      if (!response.ok) throw new Error('Failed to load review stats')
      const json = await response.json()
      const stats = json.stats || {}

      relatedReviewStatsByProduct.value = {
        ...relatedReviewStatsByProduct.value,
        [id]: {
          total: Number(stats.total || 0),
          average: Number(stats.average || 0)
        }
      }
    } catch (statsError) {
      relatedReviewStatsByProduct.value = {
        ...relatedReviewStatsByProduct.value,
        [id]: {
          total: 0,
          average: 0
        }
      }
      console.warn('Failed to load related review stats:', id, statsError)
    } finally {
      pendingRelatedReviewStats.delete(id)
    }
  }))
}

watch(paginatedRelated, (visibleRelated) => {
  void loadRelatedReviewStats(visibleRelated)
}, { immediate: true })

const addToQuote = () => {
  if (!product.value) return
  if (isOutOfStock(product.value)) {
    toastStore.addToast(`"${product.value.productName}" is out of stock and cannot be added to quote`, 'error')
    return
  }
  const added = cartStore.addItem(product.value, 1)
  if (!added) {
    toastStore.addToast('This product cannot be added to quote right now', 'error')
    return
  }

  toastStore.addToast(`Added "${product.value.productName}" to quote`, 'success')
}

const addToFavorite = () => {
  if (!product.value) return
  
  // Check if user is authenticated
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to add items to favorites', 'info')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  const isNowFavorite = favoritesStore.toggleFavorite(product.value)
  if (isNowFavorite === null) {
    toastStore.addToast('Account suspended: favorites are read-only', 'error')
    return
  }

  if (isNowFavorite) {
    toastStore.addToast(`Added "${product.value.productName}" to favorites`, 'success')
  } else {
    toastStore.addToast(`Removed "${product.value.productName}" from favorites`, 'info')
  }
}

const addRelatedToQuote = (relatedProduct) => {
  if (isOutOfStock(relatedProduct)) {
    toastStore.addToast(`"${relatedProduct.productName}" is out of stock and cannot be added to quote`, 'error')
    return
  }
  const added = cartStore.addItem(relatedProduct, 1)
  if (!added) {
    toastStore.addToast('This product cannot be added to quote right now', 'error')
    return
  }
  toastStore.addToast(`Added "${relatedProduct.productName}" to quote`, 'success')
}

const getPrimaryImage = (item) => {
  const images = normalizeImages(item?.productImages)
  if (images.length > 0) return images[0]

  const fallback = normalizeImages(item?.images)
  if (fallback.length > 0) return fallback[0]

  return String(item?.image_url || '').trim()
}

const openShareModal = (item) => {
  if (!item) return

  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to share products', 'info')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  sharingProduct.value = item
  shareRecipientEmail.value = ''
  shareNote.value = ''
  shareGeneratedLink.value = ''
  showShareModal.value = true
}

const closeShareModal = () => {
  showShareModal.value = false
  sharingProduct.value = null
  shareSubmitting.value = false
}

const submitProductShare = async () => {
  const item = sharingProduct.value
  if (!item) return

  const recipientEmail = shareRecipientEmail.value.trim()
  shareSubmitting.value = true

  try {
    const response = await api.post('/shares/product', {
      recipient_email: recipientEmail || null,
      note: shareNote.value.trim(),
      product: {
        productId: item.productId,
        productName: item.productName,
        mfgPartNo: getProductSku(item) === 'N/A' ? '' : getProductSku(item),
        vendorId: getProductVendor(item) === 'N/A' ? '' : getProductVendor(item),
        description: item.description || '',
        imageUrl: getPrimaryImage(item) || '',
        price: Number(item.productPrice?.[0]?.rsPrice || 0),
      },
    })

    const shareUrl = String(response.data?.data?.share_url || '').trim()
    shareGeneratedLink.value = shareUrl
    toastStore.addToast('Share link generated. Use Copy Link or Send to Email.', 'success')
  } catch (error) {
    console.error('Failed to share product:', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to share product', 'error')
  } finally {
    shareSubmitting.value = false
  }
}

const copyShareGeneratedLink = async () => {
  const link = shareGeneratedLink.value.trim()
  if (!link) return

  try {
    if (navigator?.clipboard?.writeText) {
      await navigator.clipboard.writeText(link)
      toastStore.addToast('Share link copied to clipboard', 'success')
      return
    }
  } catch (error) {
    console.warn('Clipboard copy failed:', error)
  }

  window.prompt('Copy this share link:', link)
}

const sendShareLinkByEmail = () => {
  const link = shareGeneratedLink.value.trim()
  if (!link) {
    toastStore.addToast('Generate the share link first', 'warning')
    return
  }

  const recipient = encodeURIComponent(shareRecipientEmail.value.trim())
  const productName = sharingProduct.value?.productName || 'Shared product'
  const note = shareNote.value.trim()
  const bodyParts = [
    'I wanted to share this product with you:',
    productName,
    '',
  ]

  if (note) {
    bodyParts.push(`Note: ${note}`, '')
  }

  bodyParts.push(link)
  const subject = `Shared product: ${productName}`
  const body = bodyParts.join('\n')
  window.location.href = `mailto:${recipient}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`
}

const toggleRelatedFavorite = (relatedProduct) => {
  if (!authStore.isAuthenticated) {
    toastStore.addToast('Please log in to add items to favorites', 'info')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  const isNowFavorite = favoritesStore.toggleFavorite(relatedProduct)
  if (isNowFavorite === null) {
    toastStore.addToast('Account suspended: favorites are read-only', 'error')
    return
  }
  toastStore.addToast(isNowFavorite ? `Added "${relatedProduct.productName}" to favorites` : `Removed "${relatedProduct.productName}" from favorites`, isNowFavorite ? 'success' : 'info')
}


const loadMoreReviews = () => {
  if (!reviewsNextPage.value || !product.value) return
  fetchReviews(product.value.productId, reviewsNextPage.value)
}

const submitReview = async () => {
  if (!product.value || isSubmittingReview.value) return
  if (!reviewForm.value.rating || !reviewForm.value.body.trim()) return

  isSubmittingReview.value = true
  reviewError.value = ''

  try {
    const formData = new FormData()
    formData.append('rating', reviewForm.value.rating)
    formData.append('body', reviewForm.value.body)
    if (reviewForm.value.title.trim()) {
      formData.append('title', reviewForm.value.title)
    }
    reviewForm.value.images.forEach((file, idx) => {
      formData.append(`images[${idx}]`, file)
    })

    const token = authStore.token
    const response = await fetch(`${API_BASE_URL}/products/${product.value.productId}/reviews`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      },
      body: formData,
    })

    if (!response.ok) {
      const errorJson = await response.json().catch(() => ({}))
      if (response.status === 409) {
        reviewError.value = 'You have already reviewed this product.'
      } else {
        reviewError.value = errorJson.message || 'Failed to submit review.'
      }
      return
    }

    toastStore.addToast('Review submitted successfully!', 'success')
    cancelReviewForm()
    // Refresh reviews
    await fetchReviews(product.value.productId)
  } catch (err) {
    reviewError.value = 'Something went wrong. Please try again.'
    console.error('Submit review error:', err)
  } finally {
    isSubmittingReview.value = false
  }
}

const deleteReview = async (reviewId) => {
  if (!product.value || !confirm('Delete your review?')) return

  try {
    const token = authStore.token
    const response = await fetch(`${API_BASE_URL}/products/${product.value.productId}/reviews/${reviewId}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      },
    })

    if (!response.ok) throw new Error('Failed to delete review')
    toastStore.addToast('Review deleted', 'info')
    await fetchReviews(product.value.productId)
  } catch (err) {
    toastStore.addToast('Failed to delete review', 'error')
    console.error('Delete review error:', err)
  }
}

const handleReviewImageUpload = (event) => {
  const files = Array.from(event.target.files || [])
  const remaining = 5 - reviewForm.value.images.length
  const toAdd = files.slice(0, remaining)

  toAdd.forEach((file) => {
    if (file.size > 3 * 1024 * 1024) {
      toastStore.addToast(`"${file.name}" exceeds 3 MB limit`, 'error')
      return
    }
    reviewForm.value.images.push(file)
    const reader = new FileReader()
    reader.onload = (e) => reviewImagePreviews.value.push(e.target.result)
    reader.readAsDataURL(file)
  })

  event.target.value = ''
}

const removeReviewImage = (index) => {
  reviewForm.value.images.splice(index, 1)
  reviewImagePreviews.value.splice(index, 1)
}

const cancelReviewForm = () => {
  showReviewForm.value = false
  reviewForm.value = { rating: 0, title: '', body: '', images: [] }
  reviewImagePreviews.value = []
  reviewError.value = ''
}

const formatReviewDate = (dateStr) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>
