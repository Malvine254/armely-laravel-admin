<template>
  <AdminLayout>
    <template #title>Chat Escalations</template>

    <div class="flex gap-6 h-[calc(100vh-10rem)] min-h-0 text-gray-900">

      <!-- Session list sidebar -->
      <div class="w-80 flex-shrink-0 rounded-2xl border border-gray-200 shadow-sm bg-white flex flex-col min-h-0 overflow-hidden">
        <div class="px-4 py-4 border-b border-gray-200 flex-shrink-0 bg-gray-50">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Escalated Chats</h3>
            <span v-if="openSessions.length" class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-semibold">
              {{ openSessions.length }}
            </span>
          </div>
          <p class="text-[11px] text-gray-500 mb-2">Select a conversation to respond.</p>
          <div class="flex gap-2">
            <button
              @click="tab = 'open'"
              :class="['flex-1 py-1.5 text-xs font-semibold rounded-md transition',
                tab === 'open'
                  ? 'bg-[#2F5597] text-white'
                  : 'border border-[#2F5597]/30 text-[#2F5597] bg-[#2F5597]/10 hover:bg-[#2F5597]/20']"
            >Open ({{ openSessions.length }})</button>
            <button
              @click="tab = 'resolved'"
              :class="['flex-1 py-1.5 text-xs font-semibold rounded-md transition',
                tab === 'resolved'
                  ? 'bg-[#2F5597] text-white'
                  : 'border border-[#2F5597]/30 text-[#2F5597] bg-[#2F5597]/10 hover:bg-[#2F5597]/20']"
            >History</button>
          </div>
        </div>

        <div v-if="listLoading" class="flex-1 flex items-center justify-center">
          <div class="w-6 h-6 border-2 border-[#2F5597] border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else class="flex-1 overflow-y-auto themed-scrollbar p-3">
          <button
            v-for="session in displayedSessions"
            :key="session.id"
            @click="selectSession(session)"
            class="w-full text-left rounded-xl border mb-2 p-2.5 transition"
            :class="activeSession?.id === session.id
              ? 'border-[#2F5597]/30 bg-[#2F5597]/10 ring-1 ring-[#2F5597]/20'
              : 'border-gray-200 bg-white hover:bg-gray-50'"
          >
            <div class="flex items-start gap-2">
              <div
                class="mt-0.5 w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                :class="session.resolved_at ? 'bg-emerald-500/20 text-emerald-600' : 'bg-amber-500/20 text-amber-600'"
              >
                <svg v-if="session.resolved_at" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V10a8 8 0 10-16 0v10h5m6 0v-3a3 3 0 00-3-3h-2a3 3 0 00-3 3v3m8 0H9" />
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                  <p class="text-xs font-semibold text-gray-900 truncate">{{ session.user?.name || 'Unknown user' }}</p>
                  <span
                    class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold flex-shrink-0"
                    :class="session.resolved_at ? 'bg-emerald-500/20 text-emerald-600' : 'bg-amber-500/20 text-amber-600'"
                  >{{ session.resolved_at ? 'Resolved' : 'Escalated' }}</span>
                </div>
                <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ session.last_message_preview || session.title || 'No messages' }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ timeAgo(session.escalated_at) }}</p>
              </div>
            </div>
          </button>

          <p v-if="!displayedSessions.length" class="text-[11px] text-gray-500 px-1 py-8 text-center">
            {{ tab === 'open' ? 'No open escalations' : 'No chat history yet' }}
          </p>
        </div>
      </div>

      <!-- Conversation thread panel -->
      <div class="flex-1 rounded-2xl border border-gray-200 shadow-sm bg-white flex flex-col min-h-0 overflow-hidden">

        <!-- Empty state -->
        <div v-if="!activeSession" class="flex-1 flex flex-col items-center justify-center text-gray-500 gap-3">
          <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z" />
          </svg>
          <p class="text-sm font-medium">Select a chat to view the conversation</p>
        </div>

        <template v-else>
          <!-- Header -->
          <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between flex-shrink-0">
            <div>
              <h2 class="text-lg font-bold text-gray-900">{{ activeSession.user?.name || 'Customer' }}</h2>
              <p class="text-xs text-gray-500">
                {{ activeSession.user?.email }} · Session #{{ activeSession.id }}
                <span v-if="activeSession.resolved_at" class="font-semibold text-emerald-600"> · Resolved {{ timeAgo(activeSession.resolved_at) }}</span>
                <span v-else class="font-semibold text-amber-600"> · Escalated {{ timeAgo(activeSession.escalated_at) }}</span>
              </p>
            </div>
            <div class="flex flex-wrap gap-2">
              <button
                v-if="!activeSession.resolved_at"
                @click="resolveChat"
                :disabled="resolving"
                class="px-3 py-1.5 rounded-full text-xs font-semibold border border-emerald-500/30 text-emerald-600 bg-emerald-500/10 hover:bg-emerald-500/20 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ resolving ? 'Resolving...' : 'Mark Resolved' }}
              </button>
              <span v-else class="px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-600">
                Resolved
              </span>
            </div>
          </div>

          <!-- Messages -->
          <div ref="messagesEl" class="flex-1 overflow-y-auto themed-scrollbar p-4 sm:p-5 space-y-4 bg-gray-50">
            <div v-if="messagesLoading" class="flex justify-center py-8">
              <div class="w-6 h-6 border-2 border-[#2F5597] border-t-transparent rounded-full animate-spin"></div>
            </div>

            <template v-else>
              <div
                v-for="msg in messages"
                :key="msg.id"
                class="flex"
                :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
              >
                <div
                  class="max-w-[90%] rounded-2xl px-4 py-3 shadow-sm"
                  :class="msg.role === 'user'
                    ? 'bg-[#2F5597] text-white rounded-br-md'
                    : 'bg-white border border-gray-200 text-gray-900 rounded-bl-md'"
                >
                  <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ msg.text }}</p>
                  <p
                    class="mt-2 text-[10px] uppercase tracking-wide"
                    :class="msg.role === 'user' ? 'text-blue-100' : 'text-gray-500'"
                  >
                    <template v-if="msg.role === 'admin'">Admin · </template>
                    <template v-else-if="msg.role !== 'user'">Mela AI · </template>
                    {{ formatTime(msg.created_at) }}
                  </p>
                </div>
              </div>

              <!-- Typing indicator while sending -->
              <div v-if="sending" class="flex justify-start">
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-md px-4 py-3">
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
          <form v-if="!activeSession.resolved_at" class="shrink-0 p-4 border-t border-gray-200 bg-white" @submit.prevent="sendReply">
            <div class="flex gap-2 items-stretch">
              <textarea
                v-model="replyText"
                @keydown.enter.ctrl.prevent="sendReply"
                placeholder="Type a reply… (Ctrl+Enter to send)"
                rows="2"
                class="flex-1 resize-none rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 min-h-[92px] focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              ></textarea>
              <button
                type="submit"
                :disabled="!replyText.trim() || sending"
                class="px-4 rounded-xl text-white font-semibold transition self-stretch disabled:opacity-50 disabled:cursor-not-allowed"
                style="background-color: #2F5597;"
              >
                {{ sending ? 'Sending…' : 'Send' }}
              </button>
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
