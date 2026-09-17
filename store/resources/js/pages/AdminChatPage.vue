<template>
  <AdminLayout>
    <template #title>Chat Escalations</template>

    <div class="flex gap-6 h-[calc(100vh-10rem)] min-h-0 text-gray-900">

      <!-- Session list sidebar -->
      <div class="w-80 flex-shrink-0 rounded-3xl bg-white shadow-[0_4px_24px_rgba(16,36,71,0.06)] flex flex-col min-h-0 overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
          <div class="flex items-center gap-2.5 mb-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-black text-lg" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">M</div>
            <span class="text-xl font-extrabold tracking-tight text-gray-900">Mela</span>
          </div>

          <div class="flex items-center justify-between mb-1.5">
            <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-[0.08em]">Escalated Chats</h3>
            <span v-if="openSessions.length" class="bg-rose-500 text-white text-[10px] rounded-full px-2 py-0.5 font-bold">
              {{ openSessions.length }}
            </span>
          </div>
          <p class="text-[11px] text-gray-400">Select a conversation to respond.</p>

          <div class="relative mt-4">
            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
            </svg>
            <input
              v-model="sessionSearch"
              type="search"
              placeholder="Search conversations..."
              aria-label="Search conversations"
              class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-3 py-2.5 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2F5597]/30 focus:border-[#2F5597]/40 focus:bg-white transition"
            >
          </div>

          <div class="flex gap-2 mt-3">
            <button
              @click="tab = 'open'"
              :class="['flex-1 py-2 text-[11px] font-bold rounded-lg transition',
                tab === 'open' ? 'bg-[#2F5597] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']"
            >Open ({{ openSessions.length }})</button>
            <button
              @click="tab = 'resolved'"
              :class="['flex-1 py-2 text-[11px] font-bold rounded-lg transition',
                tab === 'resolved' ? 'bg-[#2F5597] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']"
            >History</button>
          </div>
        </div>

        <div v-if="listLoading" class="flex-1 flex items-center justify-center">
          <div class="w-6 h-6 border-2 border-[#2F5597] border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else class="flex-1 overflow-y-auto themed-scrollbar px-3 py-3">
          <template v-for="group in groupedSessions" :key="`admin-group-${group.label}`">
            <p class="px-2 pt-1 pb-2 text-[11px] font-bold text-gray-400">{{ group.label }}</p>

            <button
              v-for="session in group.sessions"
              :key="session.id"
              @click="selectSession(session)"
              class="w-full text-left rounded-xl mb-1.5 p-3 transition relative"
              :class="activeSession?.id === session.id ? 'bg-[#2F5597]/[0.07]' : 'hover:bg-gray-50'"
            >
            <span
              v-if="activeSession?.id === session.id"
              class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full"
              style="background-color: #2F5597;"
            ></span>

            <div class="flex items-start gap-2.5">
              <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden text-white text-[10px] font-bold" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">
                <img
                  v-if="customerAvatar(session.user) && !failedAvatars.includes(session.user?.id)"
                  :src="customerAvatar(session.user)"
                  :alt="session.user?.name || 'Customer'"
                  class="w-full h-full object-cover"
                  @error="failedAvatars.push(session.user?.id)"
                >
                <span v-else>{{ initialsOf(session.user?.name) }}</span>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                  <p class="text-[13px] font-bold text-gray-900 truncate">{{ session.user?.name || 'Unknown user' }}</p>
                  <span class="text-[10px] text-gray-400 flex-shrink-0 font-medium">{{ timeAgo(session.escalated_at) }}</span>
                </div>
                <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ session.last_message_preview || session.title || 'No messages' }}</p>
                <span
                  class="inline-block mt-1.5 text-[10px] px-2 py-0.5 rounded-full font-semibold"
                  :class="session.resolved_at ? 'bg-emerald-500/15 text-emerald-600' : 'bg-amber-500/15 text-amber-600'"
                >{{ session.resolved_at ? 'Resolved' : 'Escalated' }}</span>
              </div>
            </div>
          </button>
          </template>

          <p v-if="!displayedSessions.length" class="text-[11px] text-gray-400 px-2 py-8 text-center">
            {{ sessionSearch.trim() ? `No conversations match "${sessionSearch}".` : (tab === 'open' ? 'No open escalations' : 'No chat history yet') }}
          </p>
        </div>

        <div class="shrink-0 px-4 py-3.5 border-t border-gray-100 flex items-center gap-3">
          <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0 overflow-hidden" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">
            <img
              v-if="adminAvatarUrl && !failedAvatars.includes('self')"
              :src="adminAvatarUrl"
              :alt="adminName"
              class="w-full h-full object-cover"
              @error="failedAvatars.push('self')"
            >
            <span v-else>{{ initialsOf(adminName) }}</span>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-[13px] font-bold text-gray-900 truncate">{{ adminName }}</p>
            <p class="text-[11px] text-gray-400 truncate">{{ adminEmail }}</p>
          </div>
        </div>
      </div>

      <!-- Conversation thread panel -->
      <div class="flex-1 rounded-3xl bg-white shadow-[0_4px_24px_rgba(16,36,71,0.06)] flex flex-col min-h-0 overflow-hidden">

        <!-- Empty state -->
        <div v-if="!activeSession" class="flex-1 flex flex-col items-center justify-center text-gray-500 gap-3">
          <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z" />
          </svg>
          <p class="text-sm font-medium">Select a chat to view the conversation</p>
        </div>

        <template v-else>
          <!-- Header -->
          <div class="px-5 py-4 border-b border-gray-100 bg-white flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white font-black text-lg flex-shrink-0 overflow-hidden" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">
                <img
                  v-if="customerAvatar(activeSession.user) && !failedAvatars.includes(activeSession.user?.id)"
                  :src="customerAvatar(activeSession.user)"
                  :alt="activeSession.user?.name || 'Customer'"
                  class="w-full h-full object-cover"
                  @error="failedAvatars.push(activeSession.user?.id)"
                >
                <span v-else>{{ initialsOf(activeSession.user?.name) }}</span>
              </div>
              <div class="min-w-0">
                <h2 class="text-lg font-extrabold text-gray-900 leading-tight truncate">{{ activeSession.user?.name || 'Customer' }}</h2>
                <p class="text-xs text-gray-500 truncate">
                  {{ activeSession.user?.email }} · Session #{{ activeSession.id }}
                  <span v-if="activeSession.resolved_at" class="font-semibold text-emerald-600"> · Resolved {{ timeAgo(activeSession.resolved_at) }}</span>
                  <span v-else class="font-semibold text-amber-600"> · Escalated {{ timeAgo(activeSession.escalated_at) }}</span>
                </p>
              </div>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
              <button
                v-if="!activeSession.resolved_at"
                @click="resolveChat"
                :disabled="resolving"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-[13px] font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ resolving ? 'Resolving...' : 'Mark Resolved' }}
              </button>
              <span v-else class="inline-flex items-center px-4 py-2.5 rounded-full text-[13px] font-bold bg-emerald-50 text-emerald-600">
                Resolved
              </span>
            </div>
          </div>

          <!-- Messages -->
          <div ref="messagesEl" class="flex-1 overflow-y-auto themed-scrollbar px-4 sm:px-6 py-5 space-y-5 bg-[#fafbfd]">
            <div v-if="messagesLoading" class="flex justify-center py-8">
              <div class="w-6 h-6 border-2 border-[#2F5597] border-t-transparent rounded-full animate-spin"></div>
            </div>

            <template v-else>
              <div
                v-for="msg in messages"
                :key="msg.id"
                class="flex items-end gap-2.5"
                :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
              >
                <div
                  v-if="msg.role !== 'user'"
                  class="w-9 h-9 rounded-full flex items-center justify-center text-white font-black text-sm flex-shrink-0 overflow-hidden"
                  :style="msg.role === 'admin'
                    ? 'background: linear-gradient(135deg, #f0a33c 0%, #d97706 100%);'
                    : 'background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);'"
                >
                  <img
                    v-if="msg.role === 'admin' && agentAvatar(msg) && !failedAvatars.includes(`msg-${msg.id}`)"
                    :src="agentAvatar(msg)"
                    :alt="msg.sender_name || 'Support'"
                    class="w-full h-full object-cover"
                    @error="failedAvatars.push(`msg-${msg.id}`)"
                  >
                  <span v-else>{{ msg.role === 'admin' ? initialsOf(msg.sender_name || adminName) : 'M' }}</span>
                </div>

                <div
                  class="max-w-[80%] rounded-2xl px-4 py-3"
                  :class="msg.role === 'user'
                    ? 'text-white rounded-br-md shadow-[0_2px_8px_rgba(47,85,151,0.25)]'
                    : 'bg-white text-gray-900 rounded-bl-md shadow-[0_1px_3px_rgba(16,36,71,0.08)]'"
                  :style="msg.role === 'user' ? 'background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);' : ''"
                >
                  <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ msg.text }}</p>

                  <div v-if="msg.attachments?.length" class="mt-2.5 flex flex-wrap gap-2">
                    <div
                      v-for="file in msg.attachments"
                      :key="`admin-attachment-${msg.id}-${file.id}`"
                      class="inline-flex items-center gap-2 rounded-xl px-2 py-1.5 max-w-[200px]"
                      :class="msg.role === 'user' ? 'bg-white/15' : 'bg-gray-50 border border-gray-100'"
                    >
                      <svg class="w-4 h-4 flex-shrink-0" :class="msg.role === 'user' ? 'text-blue-100' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                      </svg>
                      <span class="text-[11px] font-semibold truncate" :class="msg.role === 'user' ? 'text-white' : 'text-gray-700'">{{ file.name }}</span>
                    </div>
                  </div>

                  <p
                    class="mt-2 text-[10px] uppercase tracking-wide font-semibold"
                    :class="msg.role === 'user' ? 'text-blue-100' : 'text-gray-400'"
                  >
                    <template v-if="msg.role === 'admin'">Admin · </template>
                    <template v-else-if="msg.role !== 'user'">Mela AI · </template>
                    {{ formatTime(msg.created_at) }}
                  </p>
                </div>

                <div
                  v-if="msg.role === 'user'"
                  class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[11px] font-bold flex-shrink-0 overflow-hidden"
                >
                  <img
                    v-if="customerAvatar(activeSession.user) && !failedAvatars.includes(activeSession.user?.id)"
                    :src="customerAvatar(activeSession.user)"
                    :alt="activeSession.user?.name || 'Customer'"
                    class="w-full h-full object-cover"
                    @error="failedAvatars.push(activeSession.user?.id)"
                  >
                  <span v-else>{{ initialsOf(activeSession.user?.name) }}</span>
                </div>
              </div>

              <!-- Typing indicator while sending -->
              <div v-if="sending" class="flex justify-start items-end gap-2.5">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-black text-sm flex-shrink-0 overflow-hidden" style="background: linear-gradient(135deg, #f0a33c 0%, #d97706 100%);">
                  <img
                    v-if="adminAvatarUrl && !failedAvatars.includes('self')"
                    :src="adminAvatarUrl"
                    :alt="adminName"
                    class="w-full h-full object-cover"
                    @error="failedAvatars.push('self')"
                  >
                  <span v-else>{{ initialsOf(adminName) }}</span>
                </div>
                <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-[0_1px_3px_rgba(16,36,71,0.08)]">
                  <div class="flex items-center gap-1">
                    <span class="h-2 w-2 bg-[#2F5597] rounded-full animate-bounce [animation-delay:-0.2s]"></span>
                    <span class="h-2 w-2 bg-[#2F5597] rounded-full animate-bounce [animation-delay:-0.1s]"></span>
                    <span class="h-2 w-2 bg-[#2F5597] rounded-full animate-bounce"></span>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Reply box (only for open/unresolved) -->
          <form v-if="!activeSession.resolved_at" class="shrink-0 px-4 sm:px-6 pt-4 pb-4 border-t border-gray-100 bg-white" @submit.prevent="sendReply">
            <div class="flex gap-2.5 items-end">
              <div class="flex-1 flex items-end gap-2 rounded-2xl border border-gray-200 bg-gray-50 px-3 py-2 focus-within:bg-white focus-within:border-[#2F5597]/40 focus-within:ring-2 focus-within:ring-[#2F5597]/20 transition">
                <textarea
                  v-model="replyText"
                  @keydown.enter.ctrl.prevent="sendReply"
                  @input="autoGrowReply"
                  placeholder="Type a reply…"
                  rows="1"
                  class="flex-1 resize-none bg-transparent border-0 py-1.5 text-sm text-gray-900 placeholder-gray-400 max-h-40 focus:outline-none focus:ring-0"
                ></textarea>
              </div>
              <button
                type="submit"
                :disabled="!replyText.trim() || sending"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-white text-sm font-bold shadow-[0_2px_8px_rgba(47,85,151,0.3)] transition hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex-shrink-0"
                style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
                {{ sending ? 'Sending…' : 'Send' }}
              </button>
            </div>

            <div class="mt-2.5 flex items-center justify-between gap-3 text-[11px] text-gray-400">
              <p class="inline-flex items-center gap-1.5 min-w-0">
                <svg class="w-3.5 h-3.5 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21h6M10 17h4a4 4 0 002-3.5A6 6 0 106 13.5 4 4 0 008 17z" />
                </svg>
                <span class="truncate">Your reply is sent to the customer as Support.</span>
              </p>
              <p class="hidden sm:block flex-shrink-0">Press Ctrl + Enter to send</p>
            </div>
          </form>
        </template>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted, watch } from 'vue'
import AdminLayout from '../components/AdminLayout.vue'
import api from '../services/api.js'
import { resolveProfilePictureUrl } from '../services/runtimeConfig'
import { useAuthStore } from '../stores/authStore'

const tab = ref('open')
const listLoading = ref(true)
const messagesLoading = ref(false)
const sending = ref(false)
const resolving = ref(false)

const allSessions = ref([])
const resolvedSessions = ref([])
const activeSession = ref(null)
const messages = ref([])
const replyText = ref('')
const messagesEl = ref(null)

let pollTimer = null

const openSessions = computed(() => allSessions.value.filter((s) => !s.resolved_at))

const sessionSearch = ref('')
const failedAvatars = ref([])

const displayedSessions = computed(() => {
  const source = tab.value === 'open' ? openSessions.value : resolvedSessions.value
  const term = sessionSearch.value.trim().toLowerCase()
  if (!term) return source

  return source.filter((session) => {
    const name = String(session.user?.name || '').toLowerCase()
    const email = String(session.user?.email || '').toLowerCase()
    const preview = String(session.last_message_preview || session.title || '').toLowerCase()
    return name.includes(term) || email.includes(term) || preview.includes(term)
  })
})

const initialsOf = (name) => {
  const parts = String(name || '').trim().split(/\s+/).filter(Boolean)
  if (!parts.length) return '?'
  return (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase()
}

const groupedSessions = computed(() => {
  const order = ['Today', 'Yesterday', 'This week', 'Earlier']
  const buckets = new Map(order.map((label) => [label, []]))

  displayedSessions.value.forEach((session) => {
    const raw = session.last_message_at || session.escalated_at || session.updated_at
    const date = raw ? new Date(raw) : null
    let label = 'Earlier'

    if (date && !Number.isNaN(date.getTime())) {
      const now = new Date()
      const yesterday = new Date(now)
      yesterday.setDate(now.getDate() - 1)

      if (date.toDateString() === now.toDateString()) label = 'Today'
      else if (date.toDateString() === yesterday.toDateString()) label = 'Yesterday'
      else if ((now - date) / 86400000 < 7) label = 'This week'
    }

    buckets.get(label).push(session)
  })

  return order
    .map((label) => ({ label, sessions: buckets.get(label) }))
    .filter((group) => group.sessions.length)
})

const customerAvatar = (user) => resolveProfilePictureUrl(user?.profile_picture_url, user?.profile_picture)

const authStore = useAuthStore()
const adminName = computed(() => (authStore.user?.name || '').trim() || 'Support')
const adminEmail = computed(() => (authStore.user?.email || '').trim() || '')
const adminAvatarUrl = computed(() => resolveProfilePictureUrl(
  authStore.user?.profile_picture_url,
  authStore.user?.profile_picture
))

// Replies from other agents carry their own avatar; fall back to the signed-in admin's.
const agentAvatar = (msg) => msg?.sender_avatar_url || adminAvatarUrl.value

const autoGrowReply = (event) => {
  const el = event.target
  el.style.height = 'auto'
  el.style.height = `${Math.min(el.scrollHeight, 160)}px`
}

const timeAgo = (dateStr) => {
  if (!dateStr) return ''
  const diff = Date.now() - new Date(dateStr).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'just now'
  if (mins < 60) return `${mins}m ago`
  const hrs = Math.floor(mins / 60)
  if (hrs < 24) return `${hrs}h ago`
  return `${Math.floor(hrs / 24)}d ago`
}

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const loadSessions = async () => {
  try {
    const [openRes, resolvedRes] = await Promise.all([
      api.get('/admin/chats', { params: { resolved: false, limit: 60 } }),
      api.get('/admin/chats', { params: { resolved: true, limit: 200 } }),
    ])
    allSessions.value = openRes.data?.data || []
    resolvedSessions.value = resolvedRes.data?.data || []
  } catch (err) {
    console.error('Failed to load chat sessions', err)
  } finally {
    listLoading.value = false
  }
}

const selectSession = async (session) => {
  activeSession.value = session
  messages.value = []
  messagesLoading.value = true
  replyText.value = ''
  try {
    const res = await api.get(`/admin/chats/${session.id}`)
    const data = res.data?.data || {}
    activeSession.value = { ...session, ...data.session }
    messages.value = data.messages || []
    await nextTick()
    scrollToBottom()
  } catch (err) {
    console.error('Failed to load messages', err)
  } finally {
    messagesLoading.value = false
  }
}

const sendReply = async () => {
  if (!replyText.value.trim() || !activeSession.value || sending.value) return
  sending.value = true
  const text = replyText.value.trim()
  replyText.value = ''
  try {
    await api.post(`/admin/chats/${activeSession.value.id}/reply`, { message: text })
    // Reload messages to show the new reply
    await selectSession(activeSession.value)
  } catch (err) {
    console.error('Failed to send reply', err)
    replyText.value = text // restore on error
  } finally {
    sending.value = false
  }
}

const resolveChat = async () => {
  if (!activeSession.value || resolving.value) return
  resolving.value = true
  try {
    await api.post(`/admin/chats/${activeSession.value.id}/resolve`)
    await loadSessions()
    await selectSession(activeSession.value)
  } catch (err) {
    console.error('Failed to resolve chat', err)
  } finally {
    resolving.value = false
  }
}

const scrollToBottom = () => {
  if (messagesEl.value) {
    messagesEl.value.scrollTop = messagesEl.value.scrollHeight
  }
}

watch(messages, async () => {
  await nextTick()
  scrollToBottom()
})

onMounted(() => {
  loadSessions()
  // Poll every 30s so new escalations appear without manual refresh
  pollTimer = setInterval(loadSessions, 30000)
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>

<style scoped>
.themed-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #7fa4d6 #e8f0fb;
}

.themed-scrollbar::-webkit-scrollbar {
  inline-size: 10px;
}

.themed-scrollbar::-webkit-scrollbar-track {
  background: linear-gradient(180deg, #edf3fb 0%, #e4edf9 100%);
  border-radius: 999px;
}

.themed-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #8bb0de 0%, #5f8fcb 100%);
  border-radius: 999px;
  border: 2px solid #e8f0fb;
}

.themed-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #6f9ad1 0%, #3f78c7 100%);
}
</style>
