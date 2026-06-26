<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.14),_transparent_38%),linear-gradient(180deg,_#f8fafc_0%,_#eff6ff_48%,_#f8fafc_100%)]">
    <Navbar />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14">
      <section class="rounded-[2rem] overflow-hidden border border-blue-100 bg-white/85 backdrop-blur-xl shadow-[0_30px_80px_rgba(15,23,42,0.12)]">
        <div class="grid lg:grid-cols-[1.15fr_0.85fr]">
          <div class="p-8 sm:p-10 lg:p-12 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 text-white relative">
            <div class="absolute inset-0 opacity-30 pointer-events-none bg-[radial-gradient(circle_at_top_right,_rgba(96,165,250,0.35),_transparent_25%),radial-gradient(circle_at_bottom_left,_rgba(14,165,233,0.18),_transparent_30%)]"></div>
            <div class="relative z-10 max-w-2xl">
              <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-100">
                Latest updates
              </span>
              <h1 class="mt-5 text-4xl sm:text-5xl font-semibold leading-tight">
                New offers and announcements, published in one place.
              </h1>
              <p class="mt-5 text-base sm:text-lg text-slate-200/90 max-w-xl">
                This page always shows the newest live post, whether it is a product offer, a company announcement, or a detail-rich update from the team.
              </p>
              <div class="mt-8 flex flex-wrap gap-3">
                <router-link to="/products" class="inline-flex items-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:-translate-y-0.5 hover:shadow-lg">
                  Browse products
                </router-link>
                <a href="#latest-update" class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                  Jump to latest post
                </a>
              </div>
            </div>
          </div>

          <div class="p-8 sm:p-10 lg:p-12 bg-white">
            <div v-if="loading" class="space-y-4">
              <div class="h-5 w-28 rounded-full bg-slate-200 animate-pulse"></div>
              <div class="h-10 w-3/4 rounded-2xl bg-slate-200 animate-pulse"></div>
              <div class="h-4 w-full rounded-full bg-slate-200 animate-pulse"></div>
              <div class="h-4 w-5/6 rounded-full bg-slate-200 animate-pulse"></div>
              <div class="h-64 rounded-3xl bg-slate-100 animate-pulse"></div>
            </div>

            <div v-else-if="!announcement" class="h-full flex flex-col justify-center">
              <span class="inline-flex w-fit items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                No live post yet
              </span>
              <h2 class="mt-4 text-2xl font-semibold text-slate-900">We are preparing the first update.</h2>
              <p class="mt-3 text-slate-600">
                Once an admin publishes an announcement or offer, it will appear here automatically.
              </p>
            </div>

            <div v-else class="space-y-4">
              <div class="flex flex-wrap items-center gap-2">
                <span :class="['inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]', badgeClass]">
                  {{ announcement.type }}
                </span>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                  Published {{ publishedLabel }}
                </span>
              </div>

              <h2 class="text-3xl font-semibold text-slate-950 leading-tight">
                {{ announcement.title }}
              </h2>

              <p v-if="announcement.summary" class="text-slate-600 text-base leading-7">
                {{ announcement.summary }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <section id="latest-update" class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">
        <article class="rounded-[2rem] border border-slate-200 bg-white shadow-[0_20px_60px_rgba(15,23,42,0.08)] overflow-hidden">
          <div class="border-b border-slate-200 px-6 sm:px-8 py-5 bg-slate-50/80 flex items-center justify-between gap-4">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Full details</p>
              <h3 class="mt-1 text-lg font-semibold text-slate-900">Formatted HTML content</h3>
            </div>
            <span v-if="announcement" class="hidden sm:inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
              {{ announcement.slug }}
            </span>
          </div>

          <div class="p-6 sm:p-8">
            <div v-if="announcement" class="announcement-content max-w-none rounded-[1.5rem] border border-slate-100 bg-slate-50/70 p-6 sm:p-8" v-html="announcement.body_html"></div>
            <div v-else class="rounded-[1.5rem] border border-dashed border-slate-300 p-10 text-center text-slate-500">
              No announcement content is available yet.
            </div>
          </div>
        </article>

        <aside class="space-y-6">
          <div class="rounded-[2rem] border border-blue-100 bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.08)]">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">What this page does</p>
            <ul class="mt-4 space-y-3 text-sm text-slate-600 leading-6">
              <li>Shows the newest published announcement or offer automatically.</li>
              <li>Renders trusted HTML so the content can include headings, lists, and links.</li>
              <li>Updates as soon as a new admin post is published.</li>
            </ul>
          </div>

          <div class="rounded-[2rem] bg-gradient-to-br from-blue-600 to-blue-900 p-6 text-white shadow-[0_20px_60px_rgba(37,99,235,0.25)]">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">Need help</p>
            <h3 class="mt-3 text-xl font-semibold">Want the newest offer?</h3>
            <p class="mt-3 text-sm text-blue-100/90 leading-6">
              Check back here for the latest company update or browse the catalog for current product availability.
            </p>
            <router-link to="/products" class="mt-5 inline-flex items-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-blue-900 transition hover:-translate-y-0.5">
              View products
            </router-link>
          </div>
        </aside>
      </section>
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Navbar from '../components/Navbar.vue'
import api from '../services/api'

const loading = ref(true)
const announcement = ref(null)

const loadLatestAnnouncement = async () => {
  loading.value = true
  try {
    const response = await api.get('/announcements/latest')
    if (response.data?.success) {
      announcement.value = response.data.data || null
    }
  } catch (error) {
    console.error('Failed to load latest announcement', error)
  } finally {
    loading.value = false
  }
}

const publishedLabel = computed(() => {
  if (!announcement.value?.published_at) return 'recently'
  const date = new Date(announcement.value.published_at)
  return new Intl.DateTimeFormat('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
  }).format(date)
})

const badgeClass = computed(() => {
  if (announcement.value?.type === 'offer') {
    return 'bg-emerald-50 text-emerald-700'
  }
  return 'bg-blue-50 text-blue-700'
})

onMounted(() => {
  loadLatestAnnouncement()
})
</script>

<style scoped>
.announcement-content :deep(h1),
.announcement-content :deep(h2),
.announcement-content :deep(h3),
.announcement-content :deep(h4) {
  color: #0f172a;
  font-weight: 700;
  line-height: 1.2;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}

.announcement-content :deep(h1) {
  font-size: 2rem;
}

.announcement-content :deep(h2) {
  font-size: 1.5rem;
}

.announcement-content :deep(h3) {
  font-size: 1.25rem;
}

.announcement-content :deep(p) {
  color: #334155;
  line-height: 1.85;
  margin: 0 0 1rem;
}

.announcement-content :deep(ul),
.announcement-content :deep(ol) {
  margin: 1rem 0;
  padding-left: 1.5rem;
  color: #334155;
}

.announcement-content :deep(li) {
  margin: 0.45rem 0;
}

.announcement-content :deep(a) {
  color: #2563eb;
  text-decoration: underline;
  text-underline-offset: 3px;
}

.announcement-content :deep(blockquote) {
  border-left: 4px solid #2563eb;
  background: #eff6ff;
  padding: 1rem 1.25rem;
  margin: 1.25rem 0;
  color: #1e293b;
  border-radius: 0 1rem 1rem 0;
}

.announcement-content :deep(img) {
  border-radius: 1rem;
  max-width: 100%;
  height: auto;
  margin: 1rem 0;
}

.announcement-content :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1rem 0;
  overflow: hidden;
  border-radius: 1rem;
}

.announcement-content :deep(th),
.announcement-content :deep(td) {
  border: 1px solid #e2e8f0;
  padding: 0.75rem 1rem;
  text-align: left;
}

.announcement-content :deep(th) {
  background: #dbeafe;
  color: #1e3a8a;
}
</style>
