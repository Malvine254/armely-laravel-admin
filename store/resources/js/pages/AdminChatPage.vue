<template>
  <AdminLayout>
    <template #title>Chat Escalations</template>

    <div class="flex gap-6 h-[calc(100vh-10rem)] min-h-0 text-slate-100">

      <!-- Left: session list -->
      <div class="w-80 flex-shrink-0 rounded-xl border border-white/10 shadow flex flex-col min-h-0 overflow-hidden backdrop-blur" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.72), rgba(10, 41, 72, 0.72));">
        <div class="px-4 py-4 border-b border-white/10 flex-shrink-0 bg-slate-900/60">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-white">Escalated Chats</h3>
            <span v-if="openSessions.length" class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-semibold">
              {{ openSessions.length }}
            </span>
          </div>
          <div class="flex gap-2">
            <button
              @click="tab = 'open'"
              :class="['flex-1 py-1.5 text-xs font-semibold rounded-md transition',
                tab === 'open' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white' : 'bg-white/5 text-slate-300 hover:bg-white/10']"
            >Open ({{ openSessions.length }})</button>
            <button
              @click="tab = 'resolved'"
              :class="['flex-1 py-1.5 text-xs font-semibold rounded-md transition',
                tab === 'resolved' ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white' : 'bg-white/5 text-slate-300 hover:bg-white/10']"
            >History</button>
          </div>
        </div>

        <div v-if="listLoading" class="flex-1 flex items-center justify-center">
          <div class="w-6 h-6 border-2 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else class="flex-1 overflow-y-auto divide-y divide-white/10">
          <button
            v-for="session in displayedSessions"
            :key="session.id"
            @click="selectSession(session)"
            :class="['w-full text-left px-4 py-3 transition hover:bg-white/5',
              activeSession?.id === session.id ? 'bg-cyan-500/10 border-l-4 border-cyan-400' : '']"
          >
            <div class="flex items-start gap-2.5">
              <div class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-300 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V10a8 8 0 10-16 0v10h5m6 0v-3a3 3 0 00-3-3h-2a3 3 0 00-3 3v3m8 0H9" />
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-slate-100 truncate">{{ session.user?.name || 'Unknown user' }}</p>
                <p class="text-[11px] text-slate-400 truncate">{{ session.user?.email }}</p>
                <p class="text-[11px] text-slate-300 truncate mt-0.5">{{ session.last_message_preview || session.title || 'No messages' }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5">{{ timeAgo(session.escalated_at) }}</p>
              </div>
              <span v-if="session.resolved_at" class="text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-semibold flex-shrink-0">Done</span>
            </div>
          </button>

          <div v-if="!displayedSessions.length" class="px-4 py-8 text-center text-sm text-slate-400">
            {{ tab === 'open' ? 'No open escalations' : 'No chat history yet' }}
          </div>
        </div>
      </div>

      <!-- Right: conversation thread -->
      <div class="flex-1 rounded-xl border border-white/10 shadow flex flex-col min-h-0 overflow-hidden backdrop-blur" style="background: linear-gradient(180deg, rgba(15, 23, 42, 0.72), rgba(10, 41, 72, 0.72));">

        <!-- Empty state -->
        <div v-if="!activeSession" class="flex-1 flex flex-col items-center justify-center text-slate-400 gap-3">
          <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z" />
          </svg>
          <p class="text-sm font-medium">Select a chat to view the conversation</p>
        </div>

        <template v-else>
          <!-- Header -->
          <div class="px-5 py-4 border-b border-white/10 bg-slate-900/60 flex items-center justify-between flex-shrink-0">
            <div>
              <p class="font-bold text-white">{{ activeSession.user?.name || 'Customer' }}</p>
              <p class="text-xs text-slate-400">{{ activeSession.user?.email }} · Session #{{ activeSession.id }}</p>
              <p class="text-xs text-amber-300 font-medium mt-0.5">
                Escalated {{ timeAgo(activeSession.escalated_at) }}
                <span v-if="activeSession.resolved_at" class="ml-2 text-emerald-300">· Resolved {{ timeAgo(activeSession.resolved_at) }}</span>
              </p>
            </div>
            <button
              v-if="!activeSession.resolved_at"
              @click="resolveChat"
              :disabled="resolving"
              class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 transition"
            >
              {{ resolving ? 'Resolving...' : 'Mark Resolved' }}
            </button>
            <span v-else class="px-3 py-1.5 rounded-lg text-sm font-semibold text-emerald-300 bg-emerald-500/20">
              Resolved
            </span>
          </div>

          <!-- Messages -->
          <div ref="messagesEl" class="flex-1 overflow-y-auto px-5 py-4 space-y-3" style="background: linear-gradient(180deg, rgba(2, 6, 23, 0.82), rgba(15, 23, 42, 0.72));">
            <div v-if="messagesLoading" class="flex justify-center py-8">
              <div class="w-6 h-6 border-2 border-cyan-400 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <template v-else>
              <div
                v-for="msg in messages"
                :key="msg.id"
                :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']"
              >
                <!-- Admin label on left side -->
                <div v-if="msg.role === 'admin'" class="flex items-end gap-2 max-w-[80%]">
                  <div class="w-7 h-7 rounded-full bg-cyan-500 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">A</div>
                  <div class="bg-slate-900/90 border border-white/10 text-slate-100 rounded-2xl rounded-bl-none px-4 py-2.5 text-sm shadow-sm">
                    <p class="text-[10px] font-semibold text-cyan-300 mb-1">Admin</p>
                    <p class="whitespace-pre-wrap">{{ msg.text }}</p>
                    <p class="text-[10px] text-slate-500 mt-1">{{ formatTime(msg.created_at) }}</p>
                  </div>
                </div>

                <!-- User bubble on right -->
                <div v-else-if="msg.role === 'user'" class="flex items-end gap-2 max-w-[80%]">
                  <div class="bg-[#2f5597] text-white rounded-2xl rounded-br-none px-4 py-2.5 text-sm shadow-sm">
                    <p class="whitespace-pre-wrap">{{ msg.text }}</p>
                    <p class="text-[10px] text-blue-200 mt-1">{{ formatTime(msg.created_at) }}</p>
                  </div>
                  <div class="w-7 h-7 rounded-full bg-slate-800 text-slate-300 flex items-center justify-center flex-shrink-0 text-xs font-bold">U</div>
                </div>

                <!-- Assistant/system bubble on left -->
                <div v-else class="flex items-end gap-2 max-w-[80%]">
                  <div class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-300 flex items-center justify-center flex-shrink-0 text-xs font-bold">AI</div>
                  <div class="bg-slate-900/90 border border-white/10 text-slate-100 rounded-2xl rounded-bl-none px-4 py-2.5 text-sm shadow-sm">
                    <p class="text-[10px] font-semibold text-slate-400 mb-1">Mela AI</p>
                    <p class="whitespace-pre-wrap">{{ msg.text }}</p>
                    <p class="text-[10px] text-slate-500 mt-1">{{ formatTime(msg.created_at) }}</p>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Reply box (only for open/unresolved) -->
          <div v-if="!activeSession.resolved_at" class="px-5 py-4 border-t border-white/10 flex-shrink-0 bg-slate-900/70">
            <div class="flex gap-3 items-end">
              <textarea
                v-model="replyText"
                @keydown.enter.ctrl.prevent="sendReply"
                placeholder="Type a reply… (Ctrl+Enter to send)"
                rows="2"
                class="flex-1 px-4 py-2.5 border border-white/10 bg-white/5 rounded-xl text-sm text-slate-100 placeholder-slate-500 resize-none focus:outline-none focus:ring-2 focus:ring-cyan-500"
              ></textarea>
              <button
                @click="sendReply"
                :disabled="!replyText.trim() || sending"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 disabled:opacity-50 transition"
              >
                {{ sending ? 'Sending…' : 'Send' }}
              </button>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Your reply is visible to the customer in their Mela AI chat.</p>
          </div>
        </template>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted, watch } from 'vue'
import AdminLayout from '../components/AdminLayout.vue'
import api from '../services/api.js'

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
const displayedSessions = computed(() => tab.value === 'open' ? openSessions.value : resolvedSessions.value)

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
