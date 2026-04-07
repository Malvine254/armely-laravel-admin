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
                  @error="selectedImage = ''"
                />
                <div v-else class="text-center text-gray-300">
                  <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <p class="text-xs mt-2">No image available</p>
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
                  <img :src="image" :alt="`Image ${idx + 1}`" class="w-full h-full object-cover" loading="lazy" />
                </button>
              </div>
            </div>

            <!-- Product Info Section — 3 cols -->
            <div class="lg:col-span-3 p-6 lg:p-8 flex flex-col">
              <!-- Status + Vendor badge row -->
              <div class="flex items-center gap-2 mb-3">
                <span v-if="product.discontinueProduct" class="px-2.5 py-0.5 bg-red-50 text-red-600 text-xs font-semibold rounded-full border border-red-200">End of Life</span>
                <span v-else class="px-2.5 py-0.5 text-xs font-semibold rounded-full border" style="background-color: #eef5fc; color: #2F5597; border-color: #bad5f0;">Active</span>
                <span v-if="product.vendorId" class="px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">{{ product.vendorId }}</span>
              </div>

              <!-- Product Name -->
              <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 leading-tight mb-4">{{ product.productName }}</h1>

              <!-- Meta pills -->
              <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-500 mb-5">
                <span><span class="font-medium text-gray-700">SKU</span> {{ product.mfgPartNo || 'N/A' }}</span>
                <span><span class="font-medium text-gray-700">ID</span> {{ product.productId }}</span>
                <span v-if="product.billingModel"><span class="font-medium text-gray-700">Billing</span> {{ product.billingModel }}</span>
              </div>

              <!-- Price highlight card -->
              <div v-if="product.productPrice && product.productPrice.length > 0" class="rounded-xl p-5 mb-5" style="background: linear-gradient(135deg, #eef5fc 0%, #f8fbff 100%); border: 1px solid #d6e8f7;">
                <div class="flex items-baseline gap-2 mb-1">
                  <span class="text-3xl font-extrabold" style="color: #2F5597;">${{ formatPrice(product.productPrice[0].rsPrice) }}</span>
                  <span class="text-sm text-gray-500">/ unit</span>
                </div>
                <div v-if="product.productPrice.length > 1" class="mt-3 pt-3 border-t border-blue-100">
                  <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Volume Pricing</p>
                  <div class="space-y-1.5">
                    <div v-for="(price, idx) in product.productPrice.slice(1, 4)" :key="idx" class="flex items-center justify-between text-sm">
                      <span class="text-gray-600">
                        Qty {{ price.minQty }}<span v-if="price.maxQty"> – {{ price.maxQty }}</span>+
                      </span>
                      <span class="font-semibold text-gray-800">${{ formatPrice(price.rsPrice) }}</span>
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
                <button @click="addToQuote" class="flex-1 px-5 py-3 text-white font-semibold rounded-xl transition-all duration-200 text-sm shadow-sm hover:shadow-md" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#244a85'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">
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
                    <p class="text-sm font-medium text-gray-900">{{ product.mfgPartNo || 'N/A' }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                  <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #eef5fc;">
                    <svg class="w-4 h-4" fill="none" stroke="#2F5597" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-500">Vendor</p>
                    <p class="text-sm font-medium text-gray-900">{{ product.vendorId || 'N/A' }}</p>
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
                v-if="relatedProduct.productImages && relatedProduct.productImages[0]"
                :src="relatedProduct.productImages[0].imageUrl || relatedProduct.productImages[0]"
                :alt="relatedProduct.productName"
                class="w-full h-full object-cover"
                loading="lazy"
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
                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">{{ relatedProduct.productName }}</h3>
                <span v-if="relatedProduct.discontinueProduct" class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded flex-shrink-0">EOL</span>
                <span v-else class="ml-2 px-2 py-1 text-xs font-semibold rounded flex-shrink-0" style="background-color: #cce4f4; color: #2F5597;">Active</span>
              </div>
              <div class="flex items-center justify-between gap-3 text-xs text-gray-600 mb-3">
                <p class="truncate">SKU: {{ relatedProduct.mfgPartNo || 'N/A' }}</p>
                <p class="truncate text-right">Vendor: {{ relatedProduct.vendorId || 'N/A' }}</p>
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
                <p class="text-2xl font-bold" style="color: #2F5597;">${{ formatPrice(relatedProduct.productPrice[0].rsPrice) }}</p>
                <p class="text-xs text-gray-600">Min Qty: {{ relatedProduct.productPrice[0].minQty }}</p>
              </div>
              <div v-else class="mb-4">
                <p class="text-sm text-gray-400">Contact for price</p>
              </div>

              <!-- Features -->
              <div class="mb-4 flex flex-wrap gap-1">
                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ relatedProduct.billingModel || 'N/A' }}</span>
                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ relatedProduct.billingFrequency || 'N/A' }}</span>
              </div>

              <!-- Actions -->
              <div class="flex gap-2 w-full">
                <button @click="navigateToProduct(relatedProduct.productId)" class="flex-1 px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'">View Details</button>
                <button @click.stop="addRelatedToQuote(relatedProduct)" class="px-3 py-2 text-white text-sm font-semibold rounded-lg transition" style="background-color: #2F5597;" @mouseenter="$event.target.style.backgroundColor='#1f4788'" @mouseleave="$event.target.style.backgroundColor='#2F5597'" title="Add to Quote">+</button>
                <button @click.stop="toggleRelatedFavorite(relatedProduct)" class="px-3 py-2 rounded-lg transition border" :style="favoritesStore.isFavorite(relatedProduct.productId) ? { backgroundColor: '#cce4f4', borderColor: '#2F5597', color: '#2F5597' } : { borderColor: '#d1d5db', color: '#4b5563' }" :title="favoritesStore.isFavorite(relatedProduct.productId) ? 'Remove from Favorites' : 'Add to Favorites'">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useCartStore } from '../../stores/cartStore'
import { useFavoritesStore } from '../../stores/favoritesStore'
import { useAuthStore } from '../../stores/authStore'
import Navbar from '../../components/Navbar.vue'
import { API_BASE_URL } from '../../services/runtimeConfig'

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

const productDetailCache = new Map()
const relatedProductsCache = new Map()
const RELATED_PER_PAGE = 8
const relatedPage = ref(1)

const loadRelatedProducts = async (productId, cacheKey, cachedRelated) => {
  if (cachedRelated) {
    relatedProducts.value = cachedRelated
    return
  }

  try {
    const response = await fetch(`${API_BASE_URL}/products/${productId}/related`)
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
  } catch (relatedError) {
    console.warn('Related products fetch failed:', relatedError)
    relatedProducts.value = []
  }
}

const loadProductDetail = async (productId) => {
  if (!productId) return

  const cacheKey = String(productId)
  const cachedProduct = productDetailCache.get(cacheKey)
  const cachedRelated = relatedProductsCache.get(cacheKey)
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
      const response = await fetch(`${API_BASE_URL}/products/${productId}`)
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
    }

  } catch (error) {
    console.error('Error loading product detail:', error)
    product.value = null
    loadError.value = error instanceof Error ? error.message : 'Unable to load this product.'
  } finally {
    isLoading.value = false
  }

  if (product.value) {
    void loadRelatedProducts(productId, cacheKey, cachedRelated)
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

const navigateToProduct = (productId) => {
  router.push({ name: 'product-detail', params: { id: productId } })
}

const goBack = () => {
  router.push({ name: 'products' })
}

const formatPrice = (price) => {
  return parseFloat(price || 0).toFixed(2)
}

const getProductIcon = (productName) => {
  const name = productName.toLowerCase()
  if (name.includes('server') || name.includes('instance')) return 'server'
  if (name.includes('azure') || name.includes('cloud') || name.includes('subscription')) return 'cloud'
  if (name.includes('database') || name.includes('sql')) return 'database'
  return 'default'
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
  if (urls.length > 0) return urls

  const imageFallback = normalizeImages(product.value.images)
  if (imageFallback.length > 0) return imageFallback

  if (product.value.image_url) {
    return [String(product.value.image_url)]
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

const paginatedRelated = computed(() => {
  const start = (relatedPage.value - 1) * RELATED_PER_PAGE
  return relatedProducts.value.slice(start, start + RELATED_PER_PAGE)
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
  const added = cartStore.addItem(product.value, 1)
  if (!added) {
    toastStore.addToast('Account suspended: adding items to quotes is disabled', 'error')
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
  const added = cartStore.addItem(relatedProduct, 1)
  if (!added) {
    toastStore.addToast('Account suspended: adding items to quotes is disabled', 'error')
    return
  }
  toastStore.addToast(`Added "${relatedProduct.productName}" to quote`, 'success')
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

// --- Reviews ---
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

