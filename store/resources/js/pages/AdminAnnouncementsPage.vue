<template>
  <AdminLayout>
    <template #title>Tables</template>

    <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
      <div class="rounded-2xl bg-white shadow-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Content editor</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">
              {{ form.id ? 'Edit announcement or offer' : 'Create a new post' }}
            </h2>
          </div>
          <button
            type="button"
            @click="resetForm"
            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
          >
            New post
          </button>
        </div>

        <div class="p-6 space-y-5">
          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Title</label>
              <input
                v-model="form.title"
                type="text"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                placeholder="New year hardware offer"
              >
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Type</label>
              <select
                v-model="form.type"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
              >
                <option value="announcement">Announcement</option>
                <option value="offer">Offer</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Short summary</label>
            <textarea
              v-model="form.summary"
              rows="3"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
              placeholder="A short description that appears above the HTML body."
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">HTML details</label>
            <textarea
              v-model="form.body_html"
              rows="12"
              class="w-full rounded-xl border border-slate-200 bg-slate-950 px-4 py-3 font-mono text-sm text-slate-100 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
              placeholder="<h2>Special pricing</h2><p>Use <strong>bold</strong>, lists, links, and more.</p>"
            ></textarea>
            <p class="mt-2 text-xs text-slate-500">
              HTML is rendered directly on the public page, so keep the content trusted and admin-authored.
            </p>
          </div>

          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Published at</label>
              <input
                v-model="form.published_at"
                type="datetime-local"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
              >
            </div>

            <div class="flex items-end">
              <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                <input
                  v-model="form.is_published"
                  type="checkbox"
                  class="size-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                >
                Publish immediately
              </label>
            </div>
          </div>

          <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-5">
            <p class="text-sm text-slate-500">
              {{ form.id ? `Editing #${form.id}` : 'This post will become the newest live update once published.' }}
            </p>
            <button
              type="button"
              @click="saveAnnouncement"
              :disabled="saving"
              class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
              {{ saving ? 'Saving...' : 'Save post' }}
            </button>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="rounded-2xl bg-white shadow-lg border border-slate-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-slate-200">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Live preview</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">How it will look publicly</h2>
          </div>
          <div class="p-6">
            <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
              <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em]" :class="form.type === 'offer' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700'">
                  {{ form.type }}
                </span>
                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-500">
                  {{ form.is_published ? 'Published' : 'Draft' }}
                </span>
              </div>

              <h3 class="mt-4 text-2xl font-semibold text-slate-950">
                {{ form.title || 'Announcement title preview' }}
              </h3>

              <p v-if="form.summary" class="mt-3 text-sm leading-6 text-slate-600">
                {{ form.summary }}
              </p>

              <div v-if="form.body_html" class="announcement-preview mt-5 rounded-2xl border border-slate-200 bg-white p-4" v-html="form.body_html"></div>
              <div v-else class="mt-5 rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                Add HTML to see the preview here.
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl bg-white shadow-lg border border-slate-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between gap-4">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Recent posts</p>
              <h2 class="mt-1 text-lg font-semibold text-slate-900">Newest items first</h2>
            </div>
            <button
              type="button"
              @click="loadAnnouncements"
              class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
            >
              Refresh
            </button>
          </div>

          <div class="divide-y divide-slate-100">
            <div v-if="loading" class="p-6 space-y-3">
              <div v-for="i in 3" :key="i" class="animate-pulse rounded-2xl border border-slate-200 p-4">
                <div class="h-4 w-28 rounded-full bg-slate-200"></div>
                <div class="mt-3 h-5 w-3/4 rounded-full bg-slate-200"></div>
                <div class="mt-3 h-4 w-full rounded-full bg-slate-200"></div>
              </div>
            </div>

            <div v-else-if="announcements.length === 0" class="p-8 text-center text-slate-500">
              No announcements have been created yet.
            </div>

            <article
              v-for="item in announcements"
              :key="item.id"
              class="p-6"
            >
              <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em]" :class="item.type === 'offer' ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700'">
                      {{ item.type }}
                    </span>
                    <span :class="['rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em]', item.is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700']">
                      {{ item.is_published ? 'Published' : 'Draft' }}
                    </span>
                  </div>
                  <h3 class="mt-3 text-lg font-semibold text-slate-950 break-words">
                    {{ item.title }}
                  </h3>
                  <p v-if="item.summary" class="mt-2 text-sm text-slate-600 leading-6">
                    {{ item.summary }}
                  </p>
                  <p class="mt-3 text-xs text-slate-500">
                    {{ formatDate(item.published_at || item.created_at) }}
                  </p>
                </div>

                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    @click="editAnnouncement(item)"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    @click="deleteAnnouncement(item)"
                    class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>
  </AdminLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import AdminLayout from '../components/AdminLayout.vue'
import api from '@/services/api'
import { useToastStore } from '@/stores/toastStore'

const toastStore = useToastStore()

const loading = ref(false)
const saving = ref(false)
const announcements = ref([])

const form = ref({
  id: null,
  title: '',
  type: 'announcement',
  summary: '',
  body_html: '',
  published_at: '',
  is_published: true,
})

const formatDate = (value) => {
  if (!value) return 'Not published yet'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return 'Invalid date'
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  }).format(date)
}

const normalizeAnnouncement = (item) => ({
  ...item,
  published_at: item?.published_at ? new Date(item.published_at).toISOString() : null,
  created_at: item?.created_at ? new Date(item.created_at).toISOString() : null,
  updated_at: item?.updated_at ? new Date(item.updated_at).toISOString() : null,
})

const loadAnnouncements = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/announcements', { params: { limit: 25 } })
    if (response.data?.success) {
      announcements.value = (response.data.data || []).map(normalizeAnnouncement)
    }
  } catch (error) {
    console.error('Failed to load announcements', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to load announcements', 'error')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    title: '',
    type: 'announcement',
    summary: '',
    body_html: '',
    published_at: '',
    is_published: true,
  }
}

const editAnnouncement = (item) => {
  form.value = {
    id: item.id,
    title: item.title || '',
    type: item.type || 'announcement',
    summary: item.summary || '',
    body_html: item.body_html || '',
    published_at: item.published_at ? toLocalInputValue(item.published_at) : '',
    is_published: Boolean(item.is_published),
  }
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const toLocalInputValue = (value) => {
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return ''
  const pad = (num) => String(num).padStart(2, '0')
  return [
    date.getFullYear(),
    pad(date.getMonth() + 1),
    pad(date.getDate()),
  ].join('-') + `T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

const toIsoFromLocalInput = (value) => {
  if (!value) return null
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return null
  return date.toISOString()
}

const saveAnnouncement = async () => {
  if (!form.value.title.trim() || !form.value.body_html.trim()) {
    toastStore.addToast('Title and HTML details are required.', 'error')
    return
  }

  saving.value = true
  try {
    const payload = {
      title: form.value.title,
      type: form.value.type,
      summary: form.value.summary,
      body_html: form.value.body_html,
      is_published: form.value.is_published,
      published_at: toIsoFromLocalInput(form.value.published_at),
    }

    const response = form.value.id
      ? await api.put(`/admin/announcements/${form.value.id}`, payload)
      : await api.post('/admin/announcements', payload)

    if (response.data?.success) {
      toastStore.addToast(response.data.message || 'Announcement saved successfully.', 'success')
      resetForm()
      await loadAnnouncements()
    }
  } catch (error) {
    console.error('Failed to save announcement', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to save announcement', 'error')
  } finally {
    saving.value = false
  }
}

const deleteAnnouncement = async (item) => {
  if (!window.confirm(`Delete "${item.title}"? This cannot be undone.`)) return

  try {
    const response = await api.delete(`/admin/announcements/${item.id}`)
    if (response.data?.success) {
      toastStore.addToast(response.data.message || 'Announcement deleted.', 'success')
      if (form.value.id === item.id) {
        resetForm()
      }
      await loadAnnouncements()
    }
  } catch (error) {
    console.error('Failed to delete announcement', error)
    toastStore.addToast(error.response?.data?.message || 'Failed to delete announcement', 'error')
  }
}

onMounted(() => {
  loadAnnouncements()
})
</script>

<style scoped>
.announcement-preview :deep(h1),
.announcement-preview :deep(h2),
.announcement-preview :deep(h3),
.announcement-preview :deep(h4) {
  color: #0f172a;
  font-weight: 700;
  line-height: 1.2;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

.announcement-preview :deep(p) {
  color: #334155;
  line-height: 1.7;
  margin: 0 0 0.75rem;
}

.announcement-preview :deep(ul),
.announcement-preview :deep(ol) {
  margin: 0.75rem 0;
  padding-left: 1.25rem;
}

.announcement-preview :deep(a) {
  color: #2563eb;
  text-decoration: underline;
  text-underline-offset: 3px;
}

.announcement-preview :deep(img) {
  border-radius: 0.75rem;
  max-width: 100%;
  height: auto;
}
</style>
