<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 backdrop-blur-md bg-white/80 border-b border-blue-100 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg">A</span>
            </div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Armely Store</h1>
          </div>
          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">Home</router-link>
            <router-link to="/products" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Products</router-link>
            <router-link to="/quotes" class="text-sm font-semibold text-slate-600 hover:text-slate-900 transition">Quotes</router-link>
          </div>
          <div class="flex items-center space-x-3">
            <router-link to="/login" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 transition">Login</router-link>
            <router-link to="/register" class="px-6 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:shadow-lg hover:shadow-blue-500/25 transition transform hover:scale-105">
              Sign Up
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
        <div class="text-center">
          <h2 class="text-5xl sm:text-6xl font-bold text-slate-900 mb-6 leading-tight">
            Your B2B Hardware
            <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"> Procurement Hub</span>
          </h2>
          <p class="text-xl text-slate-600 mb-8 max-w-2xl mx-auto leading-relaxed">
            Find quality hardware products, manage quotes, and streamline your B2B procurement process.
          </p>
          <div class="flex flex-col sm:flex-row justify-center gap-4">
            <router-link to="/products" class="px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:shadow-xl hover:shadow-blue-500/30 transition transform hover:scale-105 active:scale-95">
              Explore Products
            </router-link>
            <router-link to="/register" class="px-8 py-4 text-base font-semibold text-blue-600 bg-white border-2 border-blue-600 rounded-lg hover:bg-blue-50 transition">
              Get Started Free
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="text-center mb-16">
        <h3 class="text-4xl font-bold text-slate-900 mb-4">Why Choose Armely?</h3>
        <p class="text-lg text-slate-600">Everything you need for efficient B2B procurement</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="group bg-white rounded-xl shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-blue-200 transition transform hover:-translate-y-1">
          <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <h4 class="text-lg font-bold text-slate-900 mb-2">Fast & Reliable</h4>
          <p class="text-slate-600">Real-time access to our complete hardware catalog with instant availability data</p>
        </div>

        <!-- Feature 2 -->
        <div class="group bg-white rounded-xl shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-blue-200 transition transform hover:-translate-y-1">
          <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h4 class="text-lg font-bold text-slate-900 mb-2">Competitive Pricing</h4>
          <p class="text-slate-600">Access wholesale prices and volume discounts directly from our supplier network</p>
        </div>

        <!-- Feature 3 -->
        <div class="group bg-white rounded-xl shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-blue-200 transition transform hover:-translate-y-1">
          <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h4 class="text-lg font-bold text-slate-900 mb-2">Easy Management</h4>
          <p class="text-slate-600">Manage quotes, orders, and inventory all in one intuitive platform</p>
        </div>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
          <div>
            <div class="text-4xl font-bold text-white mb-2">50K+</div>
            <p class="text-blue-100">Hardware Products</p>
          </div>
          <div>
            <div class="text-4xl font-bold text-white mb-2">100+</div>
            <p class="text-blue-100">Brands Available</p>
          </div>
          <div>
            <div class="text-4xl font-bold text-white mb-2">24/7</div>
            <p class="text-blue-100">Platform Support</p>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="bg-gradient-to-r from-blue-50 to-slate-50 rounded-2xl border border-blue-200 p-12 text-center">
        <h3 class="text-3xl font-bold text-slate-900 mb-4">Ready to Transform Your Procurement?</h3>
        <p class="text-lg text-slate-600 mb-8">Join hundreds of businesses using Armely for efficient hardware procurement</p>
        <router-link to="/register" class="inline-block px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:shadow-xl hover:shadow-blue-500/30 transition transform hover:scale-105">
          Create Free Account
        </router-link>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          <div>
            <h4 class="text-white font-bold mb-4">Armely Store</h4>
            <p class="text-sm">Revolutionizing B2B hardware procurement</p>
          </div>
          <div>
            <h4 class="text-white font-bold mb-4">Product</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-blue-400 transition">Features</a></li>
              <li><a href="#" class="hover:text-blue-400 transition">Pricing</a></li>
              <li><a href="#" class="hover:text-blue-400 transition">Security</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-bold mb-4">Company</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-blue-400 transition">About</a></li>
              <li><a href="#" class="hover:text-blue-400 transition">Blog</a></li>
              <li><a href="#" class="hover:text-blue-400 transition">Careers</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-bold mb-4">Legal</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-blue-400 transition">Privacy</a></li>
              <li><a href="#" class="hover:text-blue-400 transition">Terms</a></li>
              <li><a href="#" class="hover:text-blue-400 transition">Contact</a></li>
            </ul>
          </div>
        </div>
        <div class="border-t border-slate-800 pt-8 text-center text-sm">
          <p>&copy; 2026 Armely Store. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
// Home page component
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 0.2;
  }
  50% {
    opacity: 0.3;
  }
}

.animate-pulse {
  animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
