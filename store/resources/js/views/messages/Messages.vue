<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top_right,_#dce9fb_0%,_#eef4fd_35%,_#f6f9ff_100%)]">
    <Navbar />

    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-4 sm:py-5 h-[calc(100dvh-9.5rem)] md:h-[calc(100dvh-5rem)] flex flex-col overflow-hidden">
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 flex-1 min-h-0 overflow-hidden relative">
        <section
          class="rounded-2xl border border-[#d6e2f3] bg-white/95 shadow-sm backdrop-blur overflow-hidden min-h-0 flex flex-col xl:col-span-4 transition-transform duration-300 ease-out xl:relative xl:translate-x-0"
          :class="isHistoryOpenMobile
            ? 'fixed z-[70] top-[8.75rem] bottom-3 left-3 w-[84vw] max-w-sm translate-x-0 xl:static xl:w-auto xl:max-w-none'
            : 'fixed z-[70] top-[8.75rem] bottom-3 left-3 w-[84vw] max-w-sm -translate-x-[110%] xl:static xl:w-auto xl:max-w-none'"
        >
          <div class="px-4 py-4 border-b border-[#e4ebf5] bg-[#f7fbff]">
            <div class="flex items-center justify-between mb-2">
              <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Chat History</h2>
              <div class="flex items-center gap-2">
                <button
                  v-if="chatSessions.length"
                  @click="toggleManageHistory"
                  class="px-2.5 py-1.5 text-[11px] font-semibold rounded-md border border-[#c7d8ef] text-[#1d4b8f] bg-white hover:bg-[#edf4ff]"
                >
                  {{ manageHistoryMode ? 'Done' : 'Manage' }}
                </button>
                <button
                  @click="createNewChatSession"
                  :disabled="manageHistoryMode"
                  class="px-2.5 py-1.5 text-[11px] font-semibold rounded-md text-white disabled:opacity-50 disabled:cursor-not-allowed"
                  style="background: linear-gradient(135deg, #1d4b8f 0%, #3f78c7 100%);"
                >
                  + New Chat
                </button>
                <button
                  class="xl:hidden w-8 h-8 rounded-md border border-[#d6e2f3] text-slate-600 hover:bg-[#edf4ff]"
                  @click="closeHistoryPanel"
                  aria-label="Close chat history"
                >
                  <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <p class="text-[11px] text-slate-500">Choose a conversation or start a new one.</p>

            <div v-if="manageHistoryMode" class="mt-3 flex items-center justify-between gap-2 rounded-xl border border-[#d8e4f4] bg-white px-3 py-2">
              <p class="text-[11px] font-semibold text-slate-700">
                {{ selectedHistoryCount ? `${selectedHistoryCount} selected` : 'Select chats to delete' }}
              </p>
              <div class="flex items-center gap-2">
                <button
                  @click="deleteSelectedChats"
                  :disabled="!selectedHistoryCount || deletingHistory"
                  class="px-2.5 py-1.5 rounded-md text-[11px] font-semibold bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ deletingHistory ? 'Deleting...' : 'Delete Selected' }}
                </button>
                <button
                  @click="clearAllChats"
                  :disabled="!chatSessions.length || deletingHistory"
                  class="text-[11px] font-semibold text-slate-500 hover:text-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Clear All
                </button>
              </div>
            </div>
          </div>

          <div class="flex-1 min-h-0 overflow-y-auto themed-scrollbar p-3">
            <button
              v-for="session in chatSessions"
              :key="`chat-session-${session.id}`"
              @click="handleSessionCardClick(session.id)"
              class="w-full text-left rounded-xl border mb-2 p-2.5 transition"
              :class="manageHistoryMode && selectedHistoryIds.includes(session.id)
                ? 'border-red-200 bg-red-50 ring-1 ring-red-200'
                : activeChatSessionId === session.id
                ? 'border-[#bcd3f3] bg-[#eaf2ff] ring-1 ring-[#c5dbf7]'
                : 'border-[#e2eaf5] bg-white hover:bg-[#f6faff]'"
            >
              <div class="flex items-start gap-2">
                <div
                  v-if="manageHistoryMode"
                  class="mt-0.5 w-5 h-5 rounded-md border flex items-center justify-center flex-shrink-0"
                  :class="selectedHistoryIds.includes(session.id) ? 'bg-red-600 border-red-600 text-white' : 'border-[#c6d6ec] bg-white text-transparent'"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div
                  class="mt-0.5 w-7 h-7 rounded-full flex items-center justify-center"
                  :class="session.resolved_at ? 'bg-green-100 text-green-700' : session.escalated_to_human ? 'bg-amber-100 text-amber-700' : 'bg-[#e6efff] text-[#215192]'"
                >
                  <svg v-if="session.resolved_at" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg v-else-if="session.escalated_to_human" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V10a8 8 0 10-16 0v10h5m6 0v-3a3 3 0 00-3-3h-2a3 3 0 00-3 3v3m8 0H9" />
                  </svg>
                  <svg v-else-if="session.last_message_role === 'user'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9.003 9.003 0 0112 15a9.003 9.003 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21l-3-2-3 2 .75-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-semibold text-slate-900 truncate">{{ session.title || 'New chat' }}</p>
                    <span v-if="session.resolved_at" class="text-[10px] px-1.5 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">Resolved</span>
                    <span v-else-if="session.escalated_to_human" class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">Human</span>
                  </div>
                  <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ session.last_message_preview || 'No messages yet' }}</p>
                </div>
              </div>
            </button>

            <p v-if="!chatSessions.length" class="text-[11px] text-slate-500 px-1 py-1">
              No chat sessions yet.
            </p>
          </div>
        </section>

        <section class="xl:col-span-8 rounded-2xl border border-[#d6e2f3] bg-white/95 shadow-sm backdrop-blur overflow-hidden flex flex-col min-h-0">
          <div class="px-5 py-4 border-b border-[#e4ebf5] bg-[#f8fbff] flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <button
                class="xl:hidden inline-flex items-center gap-2 px-2.5 py-1.5 rounded-md border border-[#c9d9ef] text-[#1d4b8f] bg-white hover:bg-[#edf4ff] mb-2"
                @click="toggleHistoryPanel"
                aria-label="Toggle chat history"
              >
                <svg class="w-4 h-4 transition-transform duration-300" :class="isHistoryOpenMobile ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="text-xs font-semibold">Chat History</span>
              </button>
              <h2 class="text-lg font-bold text-slate-900">Mela AI Assistant</h2>
              <p class="text-xs text-slate-600">
                {{ activeSessionLabel }}
                <span v-if="activeSession?.escalated_to_human" class="font-semibold text-amber-700"> · Escalated to human</span>
                <span v-if="activeSession?.resolved_at" class="font-semibold text-green-700"> · Resolved</span>
              </p>
            </div>
            <div class="flex flex-wrap gap-2">
              <button
                @click="escalateActiveChat"
                :disabled="!activeChatSessionId || escalating || (activeSession?.escalated_to_human && !activeSession?.resolved_at)"
                class="px-3 py-1.5 rounded-full text-xs font-semibold border border-amber-300 text-amber-800 bg-amber-50 hover:bg-amber-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ activeSession?.resolved_at ? (escalating ? 'Reopening...' : 'Reopen to Human') : activeSession?.escalated_to_human ? 'Escalated' : (escalating ? 'Escalating...' : 'Escalate to Human') }}
              </button>
              <button
                v-for="prompt in quickPrompts"
                :key="prompt"
                @click="sendChatMessage(prompt)"
                class="px-3 py-1.5 rounded-full text-xs font-semibold border border-[#c9d9ef] text-[#1d4b8f] bg-white hover:bg-[#edf4ff]"
              >
                {{ prompt }}
              </button>
            </div>
          </div>

          <div ref="chatScrollRef" class="flex-1 min-h-0 overflow-y-auto themed-scrollbar p-4 sm:p-5 space-y-4" style="background: linear-gradient(180deg, #fbfdff 0%, #f4f8ff 100%);">
            <div
              v-for="chat in chatMessages"
              :key="chat.id"
              class="flex"
              :class="chat.role === 'user' ? 'justify-end' : 'justify-start'"
            >
              <div
                class="max-w-[90%] sm:max-w-[78%] rounded-2xl px-4 py-3 shadow-sm"
                :class="chat.role === 'user'
                  ? 'bg-[#1f4f92] text-white rounded-br-md'
                  : 'bg-white border border-[#dbe5f3] text-slate-800 rounded-bl-md'"
              >
                <p class="text-sm whitespace-pre-wrap leading-relaxed" v-html="renderMessageHtml(chat.text)"></p>

                <div v-if="chat.productSuggestions?.length" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                  <article
                    v-for="product in chat.productSuggestions"
                    :key="`suggestion-${chat.id}-${product.product_id}`"
                    class="rounded-xl border border-[#d6e2f3] bg-[#f9fcff] p-2.5"
                  >
                    <div class="flex gap-2">
                      <div class="w-14 h-14 rounded-lg bg-white border border-[#dbe6f4] overflow-hidden flex-shrink-0 flex items-center justify-center">
                        <img
                          v-if="product.image_url"
                          :src="product.image_url"
                          :alt="product.name"
                          class="w-full h-full object-cover"
                        >
                        <svg v-else class="w-5 h-5 text-[#8ea6c9]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4a3 3 0 014.2 0l4.8 4.8M14 14l1.6-1.6a3 3 0 014.2 0L20 13M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ product.name }}</p>
                        <p class="text-[11px] text-slate-500 truncate" v-if="product.vendor">{{ product.vendor }} · {{ product.sku || 'SKU N/A' }}</p>
                        <p class="text-xs font-semibold text-[#1f4f92] mt-0.5">{{ formatCurrency(product.price) }}</p>
                      </div>
                    </div>

                    <p class="mt-2 text-[11px] text-slate-600">{{ product.why }}</p>

                    <div class="mt-2 flex flex-wrap gap-1.5">
                      <button
                        v-for="action in product.actions || []"
                        :key="`prod-action-${product.product_id}-${action.label}`"
                        @click="openActionLink(action.link)"
                        class="px-2 py-1 rounded-md text-[11px] font-semibold border border-[#c9d9ef] text-[#1d4b8f] bg-white hover:bg-[#eef4ff]"
                      >
                        {{ action.label }}
                      </button>
                    </div>
                  </article>
                </div>

                <div v-if="chat.actions?.length" class="mt-3 flex flex-wrap gap-2">
                  <button
                    v-for="action in chat.actions"
                    :key="`${chat.id}-${action.label}-${action.link}`"
                    @click="openActionLink(action.link)"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-[#c9d9ef] text-[#1d4b8f] bg-[#f4f8ff] hover:bg-[#e8f1ff]"
                  >
                    {{ action.label }}
                  </button>
                </div>
                <p class="mt-2 text-[10px] uppercase tracking-wide" :class="chat.role === 'user' ? 'text-blue-200' : 'text-slate-400'">
                  {{ getMessageSenderLabel(chat) }} · {{ formatMessageTimestamp(chat.createdAt) }}
                </p>
              </div>
            </div>

            <div v-if="sendingChat" class="flex justify-start">
              <div class="bg-white border border-[#dbe5f3] rounded-2xl rounded-bl-md px-4 py-3">
                <div class="flex items-center gap-1">
                  <span class="h-2 w-2 bg-[#7ea3d4] rounded-full animate-bounce [animation-delay:-0.2s]"></span>
                  <span class="h-2 w-2 bg-[#7ea3d4] rounded-full animate-bounce [animation-delay:-0.1s]"></span>
                  <span class="h-2 w-2 bg-[#7ea3d4] rounded-full animate-bounce"></span>
                </div>
              </div>
            </div>
          </div>

          <form class="shrink-0 p-4 border-t border-[#e4ebf5] bg-white" @submit.prevent="sendChatMessage()">
            <div class="flex gap-2 items-stretch">
              <textarea
                v-model="chatInput"
                rows="2"
                placeholder="Ask Mela AI about products, invoices, payments, quotes, and tracking..."
                class="flex-1 resize-none rounded-xl border border-[#d5e0ef] px-3 py-2.5 text-sm min-h-[92px] focus:outline-none focus:ring-2 focus:ring-[#96b8e3]"
                @keydown.enter.exact.prevent="sendChatMessage()"
              ></textarea>
              <button
                type="submit"
                :disabled="sendingChat || !chatInput.trim()"
                class="px-4 rounded-xl text-white font-semibold transition min-h-[92px] disabled:opacity-50 disabled:cursor-not-allowed"
                style="background: linear-gradient(135deg, #1d4b8f 0%, #3f78c7 100%);"
              >
                Send
              </button>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useAuthStore } from '../../stores/authStore'
import { API_BASE_URL } from '../../services/runtimeConfig'
import Navbar from '../../components/Navbar.vue'
import { usePricingSettings } from '../../composables/usePricingSettings'

const toastStore = useToastStore()
const router = useRouter()
const authStore = useAuthStore()
const { loadPricingSettings, formatUsdUsingCurrentCurrency } = usePricingSettings()

const chatMessages = ref([])
const chatInput = ref('')
const sendingChat = ref(false)
const chatScrollRef = ref(null)
const chatSessions = ref([])
const activeChatSessionId = ref(null)
const escalating = ref(false)
const pollingInterval = ref(null)
const isHistoryOpenMobile = ref(false)
const manageHistoryMode = ref(false)
const selectedHistoryIds = ref([])
const deletingHistory = ref(false)

const getAuthToken = () => localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token')

const getChatCacheKey = (scope) => {
  const userId = authStore.user?.id || 'guest'
  return `mela-chat:${scope}:${userId}`
}

const readCachedJson = (key, fallback) => {
  if (typeof window === 'undefined') return fallback
  try {
    const raw = localStorage.getItem(key)
    return raw ? JSON.parse(raw) : fallback
  } catch (error) {
    return fallback
  }
}

const writeCachedJson = (key, value) => {
  if (typeof window === 'undefined') return
  try {
    localStorage.setItem(key, JSON.stringify(value))
  } catch (error) {
    // Ignore storage write failures.
  }
}

const getCachedSessionMessages = (sessionId) => readCachedJson(getChatCacheKey(`session:${sessionId}`), [])

const cacheSessionMessages = (sessionId, messages) => {
  if (!sessionId) return
  writeCachedJson(getChatCacheKey(`session:${sessionId}`), messages)
}

const clearCachedSessionMessages = (sessionId) => {
  if (!sessionId || typeof window === 'undefined') return
  localStorage.removeItem(getChatCacheKey(`session:${sessionId}`))
}

const selectedHistoryCount = computed(() => selectedHistoryIds.value.length)

const toggleHistoryPanel = () => {
  isHistoryOpenMobile.value = !isHistoryOpenMobile.value
}

const closeHistoryPanel = () => {
  isHistoryOpenMobile.value = false
}

const toggleManageHistory = () => {
  manageHistoryMode.value = !manageHistoryMode.value
  if (!manageHistoryMode.value) {
    selectedHistoryIds.value = []
  }
}

const handleSessionCardClick = async (sessionId) => {
  if (manageHistoryMode.value) {
    toggleSessionSelection(sessionId)
    return
  }

  await selectChatSession(sessionId)
}

const toggleSessionSelection = (sessionId) => {
  if (selectedHistoryIds.value.includes(sessionId)) {
    selectedHistoryIds.value = selectedHistoryIds.value.filter((id) => id !== sessionId)
    return
  }

  selectedHistoryIds.value = [...selectedHistoryIds.value, sessionId]
}

const syncChatSessionStateAfterDelete = async (deletedIds = []) => {
  const deletedSet = new Set((deletedIds || []).map((id) => Number(id)))
  chatSessions.value = chatSessions.value.filter((session) => !deletedSet.has(Number(session.id)))
  selectedHistoryIds.value = selectedHistoryIds.value.filter((id) => !deletedSet.has(Number(id)))

  deletedIds.forEach((id) => clearCachedSessionMessages(id))
  writeCachedJson(getChatCacheKey('sessions'), chatSessions.value)

  if (deletedSet.has(Number(activeChatSessionId.value))) {
    stopMessagePolling()
    activeChatSessionId.value = null
    chatMessages.value = []

    const nextSession = chatSessions.value[0]
    if (nextSession?.id) {
      await selectChatSession(nextSession.id)
    } else {
      ensureChatWelcome()
    }
  }
}

const deleteChatSessions = async ({ ids = [], clearAll = false } = {}) => {
  if (deletingHistory.value) return

  const targetIds = clearAll ? chatSessions.value.map((session) => session.id) : ids
  if (!clearAll && targetIds.length === 0) return

  const confirmed = window.confirm(
    clearAll
      ? 'Delete all chat history? This cannot be undone.'
      : `Delete ${targetIds.length} selected chat${targetIds.length === 1 ? '' : 's'}? This cannot be undone.`
  )

  if (!confirmed) return

  try {
    deletingHistory.value = true
    const token = getAuthToken()
    const response = await fetch(
      clearAll ? `${API_BASE_URL}/messages/chats/bulk-delete` : `${API_BASE_URL}/messages/chats/bulk-delete`,
      {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        body: JSON.stringify({
          chat_session_ids: clearAll ? [] : targetIds,
          clear_all: clearAll,
        })
      }
    )

    if (!response.ok) {
      throw new Error('Failed to delete chat history')
    }

    const payload = await response.json()
    const deletedIds = payload?.deleted_ids || []
    await syncChatSessionStateAfterDelete(deletedIds)

    if (clearAll) {
      manageHistoryMode.value = false
      toastStore.addToast('All chat history deleted', 'success')
    } else {
      toastStore.addToast('Selected chats deleted', 'success')
    }
  } catch (error) {
    console.error('Error deleting chat sessions:', error)
    toastStore.addToast('Failed to delete chat history', 'error')
  } finally {
    deletingHistory.value = false
  }
}

const deleteSelectedChats = async () => {
  await deleteChatSessions({ ids: selectedHistoryIds.value })
}

const clearAllChats = async () => {
  await deleteChatSessions({ clearAll: true })
}

const quickPrompts = [
  'Best Dell laptop sample list',
  'Show unpaid invoices',
  'Download invoice PDF',
  'Track my latest order'
]

const activeSession = computed(() => chatSessions.value.find((session) => session.id === activeChatSessionId.value) || null)

const activeSessionLabel = computed(() => {
  if (!activeSession.value) {
    return 'Start a chat to begin with Mela AI'
  }

  return `Chat #${activeSession.value.id}`
})

const ensureChatWelcome = () => {
  if (chatMessages.value.length > 0) return

  chatMessages.value.push({
    id: `assistant-welcome-${Date.now()}`,
    role: 'assistant',
    text: 'Hi, I am Mela AI. I can suggest products with reasons, provide quote and payment guidance, and escalate to human support if needed.',
    createdAt: new Date().toISOString(),
    actions: [
      { label: 'Browse products', link: '/products' },
      { label: 'Open quotes', link: '/quotes' },
      { label: 'Open invoices', link: '/invoices' }
    ],
    productSuggestions: []
  })
}

const scrollChatToBottom = async () => {
  await nextTick()
  if (!chatScrollRef.value) return
  chatScrollRef.value.scrollTop = chatScrollRef.value.scrollHeight
}

const refreshChatMessages = async () => {
  if (!activeChatSessionId.value) return

  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/messages/chats/${activeChatSessionId.value}`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      }
    })

    if (!response.ok) return

    const payload = await response.json()
    const loadedMessages = payload?.data?.messages || []
    const updatedSession = payload?.data?.session || {}

    const sessionIndex = chatSessions.value.findIndex((s) => s.id === activeChatSessionId.value)
    if (sessionIndex >= 0) {
      chatSessions.value[sessionIndex] = {
        ...chatSessions.value[sessionIndex],
        escalated_to_human: !!updatedSession.escalated_to_human,
        resolved_at: updatedSession.resolved_at ?? null,
        last_message_at: updatedSession.last_message_at ?? chatSessions.value[sessionIndex].last_message_at,
      }
    }

    const newMessages = loadedMessages.map((item) => ({
      id: item.id,
      role: item.role,
      text: item.text,
      senderName: item.sender_name || null,
      createdAt: item.created_at || null,
      actions: item.actions || [],
      productSuggestions: item.product_suggestions || []
    }))

    const shouldScroll = newMessages.length > chatMessages.value.length
    chatMessages.value = newMessages
    cacheSessionMessages(activeChatSessionId.value, newMessages)

    if (shouldScroll) {
      await scrollChatToBottom()
    }
  } catch (error) {
    // Silence transient polling errors.
  }
}

const startMessagePolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value)
  }

  pollingInterval.value = setInterval(() => {
    refreshChatMessages()
  }, 2000)
}

const stopMessagePolling = () => {
  if (!pollingInterval.value) return
  clearInterval(pollingInterval.value)
  pollingInterval.value = null
}

const fetchChatSessions = async () => {
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/messages/chats`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to fetch chat sessions')
    }

    const payload = await response.json()
    chatSessions.value = payload?.data || []
    writeCachedJson(getChatCacheKey('sessions'), chatSessions.value)
  } catch (error) {
    console.error('Error fetching chat sessions:', error)
  }
}

const createNewChatSession = async () => {
  try {
    const existingEmpty = chatSessions.value.find((session) => !session.last_message_preview)
    if (existingEmpty?.id) {
      await selectChatSession(existingEmpty.id)
      return
    }

    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/messages/chats`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({ title: 'New chat' })
    })

    if (!response.ok) {
      throw new Error('Failed to create chat session')
    }

    const payload = await response.json()
    const created = payload?.data
    if (!created?.id) {
      throw new Error('Invalid chat session payload')
    }

    activeChatSessionId.value = created.id
    chatMessages.value = []
    ensureChatWelcome()
    await fetchChatSessions()
    await selectChatSession(created.id)
    await scrollChatToBottom()
  } catch (error) {
    console.error('Error creating chat session:', error)
    toastStore.addToast('Failed to create new chat', 'error')
  }
}

const selectChatSession = async (sessionId) => {
  if (!sessionId) return

  try {
    stopMessagePolling()
    const cachedMessages = getCachedSessionMessages(sessionId)
    if (Array.isArray(cachedMessages) && cachedMessages.length > 0) {
      activeChatSessionId.value = sessionId
      chatMessages.value = cachedMessages
      ensureChatWelcome()
      void scrollChatToBottom()
    }

    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/messages/chats/${sessionId}`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to load chat session')
    }

    const payload = await response.json()
    const loadedMessages = payload?.data?.messages || []

    activeChatSessionId.value = sessionId
    chatMessages.value = loadedMessages.map((item) => ({
      id: item.id,
      role: item.role,
      text: item.text,
      senderName: item.sender_name || null,
      createdAt: item.created_at || null,
      actions: item.actions || [],
      productSuggestions: item.product_suggestions || []
    }))
    cacheSessionMessages(sessionId, chatMessages.value)

    ensureChatWelcome()
    await scrollChatToBottom()
    closeHistoryPanel()
    startMessagePolling()
  } catch (error) {
    console.error('Error selecting chat session:', error)
    toastStore.addToast('Failed to load chat history', 'error')
  }
}

const escalateActiveChat = async () => {
  if (!activeChatSessionId.value || escalating.value) return

  try {
    escalating.value = true
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/messages/chats/${activeChatSessionId.value}/escalate`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({ note: 'Requested from Mela AI chat interface' })
    })

    if (!response.ok) {
      throw new Error('Failed to escalate chat')
    }

    toastStore.addToast('Chat escalated to human support', 'success')
    await fetchChatSessions()
    startMessagePolling()
  } catch (error) {
    console.error('Error escalating chat:', error)
    toastStore.addToast('Failed to escalate chat', 'error')
  } finally {
    escalating.value = false
  }
}

const openActionLink = async (link) => {
  if (!link) return

  if (link.startsWith('/api/')) {
    const token = getAuthToken()
    if (!token) {
      toastStore.addToast('Please log in to continue', 'warning')
      await router.push('/login')
      return
    }

    try {
      const response = await fetch(link, {
        method: 'GET',
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/pdf, application/json'
        }
      })

      if (!response.ok) {
        throw new Error(`Request failed (${response.status})`)
      }

      const blob = await response.blob()
      const blobUrl = window.URL.createObjectURL(blob)
      const fileName = (() => {
        const parts = String(link).split('/').filter(Boolean)
        if (parts.length >= 2 && parts[parts.length - 1] === 'pdf') {
          return `${parts[parts.length - 2]}.pdf`
        }
        return 'download.pdf'
      })()

      const anchor = document.createElement('a')
      anchor.href = blobUrl
      anchor.download = fileName
      document.body.appendChild(anchor)
      anchor.click()
      anchor.remove()
      window.URL.revokeObjectURL(blobUrl)
    } catch (error) {
      console.error('Error opening secure API link:', error)
      toastStore.addToast('Unable to download file. Please try again.', 'error')
    }
    return
  }

  try {
    await router.push(link)
  } catch (error) {
    console.error('Error opening action link:', error)
    toastStore.addToast('Unable to open action link', 'error')
  }
}

const formatCurrency = (value) => {
  return formatUsdUsingCurrentCurrency(Number(value || 0))
}

const getMessageSenderLabel = (chat) => {
  if (!chat) return 'Support Team'
  if (chat.role === 'user') return 'You'
  if (chat.role === 'admin') {
    const name = String(chat.senderName || '').trim()
    return name ? `Support: ${name}` : 'Support Team'
  }
  return 'Assistant'
}

const formatMessageTimestamp = (value) => {
  if (!value) return 'Now'

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return 'Now'

  const now = new Date()
  const sameDay = date.toDateString() === now.toDateString()
  const yesterday = new Date(now)
  yesterday.setDate(now.getDate() - 1)
  const sameYesterday = date.toDateString() === yesterday.toDateString()
  const dayDiff = Math.floor((now - date) / (1000 * 60 * 60 * 24))

  const timeLabel = date.toLocaleTimeString([], {
    hour: 'numeric',
    minute: '2-digit',
  })

  if (sameDay) {
    return `Today ${timeLabel}`
  }

  if (sameYesterday) {
    return `Yesterday ${timeLabel}`
  }

  if (dayDiff < 7) {
    const weekday = date.toLocaleDateString([], { weekday: 'short' })
    return `${weekday} ${timeLabel}`
  }

  if (dayDiff < 28) {
    const weeks = Math.max(1, Math.floor(dayDiff / 7))
    const weekday = date.toLocaleDateString([], { weekday: 'short' })
    return `${weeks}w ${weekday} ${timeLabel}`
  }

  return date.toLocaleString([], {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  })
}

const escapeHtml = (text) => {
  return String(text || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
}

const renderMessageHtml = (text) => {
  const safe = escapeHtml(text)

  const markdownLinked = safe.replace(
    /\[([^\]]+)\]\(((?:\/|https?:\/\/)[^)\s]+)\)/g,
    '<a href="$2" class="text-[#1d4b8f] font-semibold underline hover:text-[#153a69]">$1</a>'
  )

  return markdownLinked.replace(/\n/g, '<br>')
}

const sendChatMessage = async (prefilled = null) => {
  const outgoing = (prefilled ?? chatInput.value).trim()
  if (!outgoing || sendingChat.value) return

  chatMessages.value.push({
    id: `user-${Date.now()}`,
    role: 'user',
    text: outgoing,
    createdAt: new Date().toISOString(),
    actions: [],
    productSuggestions: []
  })

  chatInput.value = ''
  await scrollChatToBottom()

  sendingChat.value = true
  try {
    const token = getAuthToken()
    const response = await fetch(`${API_BASE_URL}/messages/assistant/chat`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({
        message: outgoing,
        chat_session_id: activeChatSessionId.value
      })
    })

    if (!response.ok) {
      throw new Error('Mela AI chat request failed')
    }

    const payload = await response.json()
    const assistantPayload = payload?.data || {}

    if (assistantPayload?.chat_session?.id) {
      activeChatSessionId.value = assistantPayload.chat_session.id
    }

    chatMessages.value.push({
      id: `assistant-${Date.now()}`,
      role: 'assistant',
      text: assistantPayload.reply || 'I could not generate a response right now.',
      createdAt: assistantPayload.created_at || new Date().toISOString(),
      actions: assistantPayload.actions || [],
      productSuggestions: assistantPayload.product_suggestions || []
    })
    cacheSessionMessages(activeChatSessionId.value, chatMessages.value)

    await fetchChatSessions()
  } catch (error) {
    console.error('Error sending chat message:', error)
    chatMessages.value.push({
      id: `assistant-error-${Date.now()}`,
      role: 'assistant',
      text: 'I could not process that right now. Please try again in a moment.',
      createdAt: new Date().toISOString(),
      actions: [],
      productSuggestions: []
    })
    toastStore.addToast('Mela AI is temporarily unavailable', 'error')
  } finally {
    sendingChat.value = false
    await scrollChatToBottom()
  }
}

onMounted(async () => {
  await loadPricingSettings()
  const cachedSessions = readCachedJson(getChatCacheKey('sessions'), [])
  if (Array.isArray(cachedSessions) && cachedSessions.length > 0) {
    chatSessions.value = cachedSessions
  }
  ensureChatWelcome()
  await fetchChatSessions()
  if (chatSessions.value.length) {
    await selectChatSession(chatSessions.value[0].id)
  }
  await scrollChatToBottom()
})

onUnmounted(() => {
  stopMessagePolling()
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
