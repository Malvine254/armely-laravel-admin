<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
    <Navbar />

    <main class="relative max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8 sm:py-9">
      <div class="absolute -top-8 -left-6 w-40 h-40 rounded-full blur-3xl opacity-20" style="background: #2F5597;"></div>
      <div class="absolute top-16 -right-6 w-40 h-40 rounded-full blur-3xl opacity-20" style="background: #1f4788;"></div>

      <section class="relative overflow-hidden rounded-2xl text-white p-7 sm:p-10 shadow-xl border border-white/20" style="background: linear-gradient(135deg, #183a72, #0f2a54);">
        <div class="max-w-3xl">
          <p class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide mb-4" style="background-color: rgba(255,255,255,0.18);">TRUST & PRIVACY</p>
          <h1 class="text-3xl sm:text-4xl font-bold mb-3">Privacy Policy</h1>
          <p class="text-sm sm:text-base leading-relaxed" style="color: #d5e7fb;">
            We are committed to protecting your information and using it responsibly for secure B2B procurement operations.
          </p>
          <p class="mt-4 text-xs sm:text-sm" style="color: #cce4f4;">Last updated: March 26, 2026</p>
        </div>
      </section>

      <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
          <p class="text-xs font-semibold tracking-wide text-slate-500 mb-1">COLLECTION</p>
          <p class="text-sm text-slate-700">Account, company, order, and billing data required to deliver your service.</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
          <p class="text-xs font-semibold tracking-wide text-slate-500 mb-1">USAGE</p>
          <p class="text-sm text-slate-700">Used to process quotes/orders, provide support, and improve reliability.</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
          <p class="text-xs font-semibold tracking-wide text-slate-500 mb-1">PROTECTION</p>
          <p class="text-sm text-slate-700">Protected with technical and organizational safeguards against misuse.</p>
        </div>
      </section>

      <section class="mt-6 space-y-4">
        <article
          v-for="section in privacySections"
          :key="section.title"
          class="bg-white rounded-xl border border-slate-200 shadow-sm p-6"
        >
          <h2 class="text-xl font-semibold text-slate-900 mb-2">{{ section.title }}</h2>
          <p class="text-slate-700 leading-relaxed">{{ section.content }}</p>
        </article>
      </section>

      <section class="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-2">Questions About Privacy?</h3>
        <p class="text-slate-700 mb-4">For privacy requests or questions, please contact your account administrator or support representative.</p>
        <router-link
          to="/account"
          class="inline-flex items-center px-4 py-2 rounded-lg text-white font-semibold transition"
          style="background-color: #2F5597;"
        >
          Go to Account
        </router-link>
      </section>
    </main>
  </div>
</template>

<script setup>
import Navbar from '../components/Navbar.vue'

const privacySections = [
  {
    title: '1. Information We Collect',
    content: 'We collect account details such as name, company information, email address, and contact data. We also collect order, quote, and billing-related information needed to provide B2B procurement services.',
  },
  {
    title: '2. How We Use Information',
    content: 'Your information is used to process quotes and orders, provide support, send operational updates, and improve platform performance and security.',
  },
  {
    title: '3. Data Sharing',
    content: 'We do not sell personal data. We may share information with trusted service providers who help us operate the platform, process transactions, or deliver required communications.',
  },
  {
    title: '4. Cookies & Search Personalization',
    content: 'With your consent, we use cookies to remember search activity and improve your experience with personalized suggestions such as recent, popular, and recommended searches.',
  },
  {
    title: '5. Security',
    content: 'We apply reasonable technical and organizational safeguards to protect data from unauthorized access, disclosure, or misuse.',
  },
  {
    title: '6. Contact',
    content: 'For privacy requests or questions, please contact your account administrator or support representative.',
  },
]
</script>
