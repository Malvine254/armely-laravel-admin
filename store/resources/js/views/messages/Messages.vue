<template>
  <div class="min-h-screen bg-gradient-to-b from-[#eef3fb] via-[#f8fbff] to-[#f3f6fb] relative">
    <Navbar />

    <div class="pointer-events-none absolute inset-0">
      <div class="absolute -top-24 -right-20 w-80 h-80 rounded-full blur-3xl opacity-30" style="background: radial-gradient(circle, #7fb3e8 0%, transparent 70%);"></div>
      <div class="absolute top-64 -left-24 w-72 h-72 rounded-full blur-3xl opacity-25" style="background: radial-gradient(circle, #b7d7f2 0%, transparent 70%);"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
      <div class="rounded-3xl text-white p-6 sm:p-8 mb-6 shadow-lg" style="background: linear-gradient(130deg, #234a87 0%, #2F5597 48%, #4f86c6 100%);">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-xs uppercase tracking-[0.18em] text-blue-100 mb-2">Communication Center</p>
            <h1 class="text-3xl sm:text-4xl font-bold leading-tight">Messages</h1>
            <p class="text-blue-100 mt-2 max-w-2xl">Track quote updates, payment reminders, and system notifications in one place.</p>
          </div>
          <button
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 transition font-semibold text-sm"
          >
            Mark All as Read
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6">
          <div class="rounded-2xl bg-white/10 backdrop-blur p-4 border border-white/15">
            <p class="text-xs text-blue-100 uppercase tracking-wide">Total</p>
            <p class="text-2xl font-bold mt-1">{{ totalCount }}</p>
          </div>
          <div class="rounded-2xl bg-white/10 backdrop-blur p-4 border border-white/15">
            <p class="text-xs text-blue-100 uppercase tracking-wide">Unread</p>
            <p class="text-2xl font-bold mt-1">{{ unreadCount }}</p>
          </div>
          <div class="rounded-2xl bg-white/10 backdrop-blur p-4 border border-white/15">
            <p class="text-xs text-blue-100 uppercase tracking-wide">Action Required</p>
            <p class="text-2xl font-bold mt-1">{{ actionableCount }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white/95 rounded-2xl shadow-md border border-[#dbe5f3] p-4 sm:p-5 mb-6 backdrop-blur">
        <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
          <div class="flex flex-wrap gap-2">
            <button
              @click="filterType = null"
              :class="filterType === null ? 'text-white' : 'text-[#2d3d59] bg-[#eef2f8] hover:bg-[#e4ebf4]'"
              class="px-4 py-2 rounded-xl font-semibold text-sm transition"
              :style="filterType === null ? 'background-color: #2F5597;' : ''"
            >All</button>
            <button
              @click="filterType = 'order'"
              :class="filterType === 'order' ? 'text-white' : 'text-[#2d3d59] bg-[#eef2f8] hover:bg-[#e4ebf4]'"
              class="px-4 py-2 rounded-xl font-semibold text-sm transition"
              :style="filterType === 'order' ? 'background-color: #2F5597;' : ''"
            >Orders</button>
            <button
              @click="filterType = 'quote'"
              :class="filterType === 'quote' ? 'text-white' : 'text-[#2d3d59] bg-[#eef2f8] hover:bg-[#e4ebf4]'"
              class="px-4 py-2 rounded-xl font-semibold text-sm transition"
              :style="filterType === 'quote' ? 'background-color: #2F5597;' : ''"
            >Quotes</button>
            <button
              @click="filterType = 'invoice'"
              :class="filterType === 'invoice' ? 'text-white' : 'text-[#2d3d59] bg-[#eef2f8] hover:bg-[#e4ebf4]'"
              class="px-4 py-2 rounded-xl font-semibold text-sm transition"
              :style="filterType === 'invoice' ? 'background-color: #2F5597;' : ''"
            >Invoices</button>
          </div>

          <div class="w-full lg:w-80">
            <div class="relative">
              <svg class="absolute left-3 top-3.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10.5a5.5 5.5 0 11-11 0 5.5 5.5 0 0111 0z"></path>
              </svg>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search message title or text..."
                class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-[#d4deec] focus:outline-none focus:ring-2 focus:ring-[#95b5df]"
              >
            </div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="space-y-4">
        <div v-for="n in 4" :key="n" class="bg-white rounded-2xl border border-[#dfe7f3] p-6 shadow-sm">
          <div class="animate-pulse">
            <div class="h-4 w-48 bg-slate-200 rounded mb-3"></div>
            <div class="h-3 w-full bg-slate-100 rounded mb-2"></div>
            <div class="h-3 w-3/4 bg-slate-100 rounded"></div>
          </div>
        </div>
      </div>

      <div v-else-if="filteredMessages.length > 0" class="space-y-4">
        <article
          v-for="message in filteredMessages"
          :key="message.id"
          class="group bg-white rounded-2xl border border-[#dde5f2] shadow-sm hover:shadow-lg transition overflow-hidden"
          :class="message.status === 'unread' ? 'ring-1 ring-[#cedef2]' : ''"
        >
          <div class="p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
              <div class="flex items-start gap-4 min-w-0">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" :style="getTypeStyle(message.type)">
                  <svg v-if="message.type === 'order'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                  </svg>
                  <svg v-else-if="message.type === 'quote'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <svg v-else-if="message.type === 'invoice'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                  </svg>
                  <svg v-else class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>

                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" :style="getTypeBadgeStyle(message.type)">
                      {{ getTypeLabel(message.type) }}
                    </span>
                    <span
                      v-if="message.priority === 'high' || message.priority === 'urgent'"
                      class="text-xs font-semibold px-2.5 py-1 rounded-full"
                      :class="message.priority === 'urgent' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800'"
                    >
                      {{ message.priority }}
                    </span>
                    <span v-if="message.status === 'unread'" class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#dfeafb] text-[#234a87]">
                      unread
                    </span>
                  </div>

                  <h3 class="text-lg font-bold text-slate-900 truncate">{{ message.title }}</h3>
                  <p class="text-slate-600 mt-1 leading-relaxed">{{ message.message }}</p>

                  <div
                    class="flex flex-wrap items-center gap-3 mt-3 text-sm"
                    :class="message.type === 'invoice' ? 'text-[#2F5597]' : 'text-slate-500'"
                  >
                    <span>{{ message.time_ago }}</span>
                    <span
                      v-if="message.reference_id"
                      class="font-mono text-xs px-2 py-1 rounded"
                      :class="message.type === 'invoice' ? 'bg-[#e5efff] text-[#2F5597]' : 'bg-slate-100 text-slate-600'"
                    >
                      {{ message.reference_id }}
                    </span>
                  </div>

                  <button
                    v-if="message.action_link"
                    @click="openMessageTarget(message)"
                    class="inline-flex items-center gap-2 mt-4 px-3 py-2 rounded-lg text-sm font-semibold border transition"
                    :class="message.type === 'invoice'
                      ? 'border-[#b9d1ec] bg-[#eaf2ff] hover:bg-[#deebff] text-[#1f4788]'
                      : 'border-[#c9d9ef] bg-[#f5f9ff] hover:bg-[#edf4ff] text-[#2F5597]'"
                  >
                    <span>{{ message.action_label || 'Open' }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5m0 0v5m0-5L10 14"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12v7a2 2 0 002 2h7"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <button
                  v-if="message.status === 'unread'"
                  @click.stop="markAsRead(message.id)"
                  class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                  title="Mark as read"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </button>
                <button
                  @click.stop="deleteMessage(message.id)"
                  class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                  title="Delete message"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </article>
      </div>

      <div v-else class="bg-white rounded-2xl border border-[#dde5f2] shadow-sm p-12 text-center">
        <svg class="w-20 h-20 mx-auto mb-4 text-[#b7c7dd]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ searchQuery ? 'No matching messages' : 'No messages yet' }}</h3>
        <p class="text-slate-600">{{ searchQuery ? 'Try a different keyword or clear filters.' : 'You are all caught up for now.' }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { API_BASE_URL } from '../../services/runtimeConfig'
import Navbar from '../../components/Navbar.vue'

const toastStore = useToastStore()
const router = useRouter()
const messages = ref([])
const loading = ref(false)
const filterType = ref(null)
const unreadCount = ref(0)
const searchQuery = ref('')

const totalCount = computed(() => messages.value.length)
const actionableCount = computed(() => messages.value.filter(msg => Boolean(msg.action_link)).length)

const filteredMessages = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()

  return messages.value.filter((msg) => {
    const matchesType = !filterType.value || msg.type === filterType.value
    if (!matchesType) return false

    if (!query) return true

    const haystack = [msg.title, msg.message, msg.reference_id, msg.type]
      .filter(Boolean)
      .join(' ')
      .toLowerCase()

    return haystack.includes(query)
  })
})

const getTypeStyle = (type) => {
  const styles = {
    order: 'background: linear-gradient(145deg, #2f5597 0%, #4f86c6 100%);',
    quote: 'background: linear-gradient(145deg, #0f9f8f 0%, #14b8a6 100%);',
    invoice: 'background: linear-gradient(145deg, #2f5597 0%, #5a8fcb 100%);',
    system: 'background: linear-gradient(145deg, #55637a 0%, #718096 100%);'
  }
  return styles[type] || styles.system
}

const getTypeBadgeStyle = (type) => {
  const styles = {
    order: 'background-color: #e5efff; color: #2F5597;',
    quote: 'background-color: #dcf8f3; color: #0f766e;',
    invoice: 'background-color: #e5efff; color: #2F5597;',
    system: 'background-color: #edf2f7; color: #4a5568;'
  }
  return styles[type] || styles.system
}

const getTypeLabel = (type) => {
  const labels = {
    order: 'Order',
    quote: 'Quote',
    invoice: 'Invoice',
    system: 'System'
  }

  return labels[type] || 'Message'
}

const fetchMessages = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`${API_BASE_URL}/messages`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to fetch messages')
    }

    const data = await response.json()
    messages.value = data.data || []
    unreadCount.value = data.unread_count || 0
  } catch (error) {
    console.error('Error fetching messages:', error)
    toastStore.addToast('Failed to load messages', 'error')
  } finally {
    loading.value = false
  }
}

const markAsRead = async (id) => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`${API_BASE_URL}/messages/${id}/read`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to mark message as read')
    }

    // Update local state
    const message = messages.value.find(m => m.id === id)
    if (message) {
      message.status = 'read'
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
    
    toastStore.addToast('Message marked as read', 'success')
  } catch (error) {
    console.error('Error marking message as read:', error)
    toastStore.addToast('Failed to mark message as read', 'error')
  }
}

const markAllAsRead = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`${API_BASE_URL}/messages/mark-all-read`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to mark all messages as read')
    }

    // Update local state
    messages.value.forEach(msg => {
      msg.status = 'read'
    })
    unreadCount.value = 0
    
    toastStore.addToast('All messages marked as read', 'success')
  } catch (error) {
    console.error('Error marking all messages as read:', error)
    toastStore.addToast('Failed to mark all messages as read', 'error')
  }
}

const deleteMessage = async (id) => {
  if (!confirm('Are you sure you want to delete this message?')) return

  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch(`${API_BASE_URL}/messages/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to delete message')
    }

    // Update local state
    const messageIndex = messages.value.findIndex(m => m.id === id)
    if (messageIndex !== -1) {
      const message = messages.value[messageIndex]
      if (message.status === 'unread') {
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
      messages.value.splice(messageIndex, 1)
    }
    
    toastStore.addToast('Message deleted', 'success')
  } catch (error) {
    console.error('Error deleting message:', error)
    toastStore.addToast('Failed to delete message', 'error')
  }
}

const viewQuoteDetails = (quoteId) => {
  if (!quoteId) {
    toastStore.addToast('Quote ID missing', 'error')
    return
  }
  window.location.href = `/quotes?view=${quoteId}`
}

const formatCurrency = (amount) => {
  if (!amount) return '$0.00'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

const openMessageTarget = async (message) => {
  if (!message?.action_link) {
    return
  }

  if (message.status === 'unread') {
    await markAsRead(message.id)
  }

  try {
    await router.push(message.action_link)
  } catch (error) {
    console.error('Error opening message target:', error)
    toastStore.addToast('Unable to open message link', 'error')
  }
}

onMounted(() => {
  fetchMessages()
})
</script>
