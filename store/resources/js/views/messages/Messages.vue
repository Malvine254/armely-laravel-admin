<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Messages</h1>
        <p class="text-gray-600 text-lg">Stay updated on your orders, quotes, and invoices</p>
      </div>

      <!-- Filter Tabs -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-2 items-center justify-between">
          <div class="flex flex-wrap gap-2">
            <button 
              @click="filterType = null" 
              :class="filterType === null ? 'text-white' : 'text-gray-700 bg-gray-100 hover:bg-gray-200'"
              class="px-4 py-2 rounded-lg font-semibold transition"
              :style="filterType === null ? 'background-color: #2F5597;' : ''"
            >
              All Messages
            </button>
            <button 
              @click="filterType = 'order'" 
              :class="filterType === 'order' ? 'text-white' : 'text-gray-700 bg-gray-100 hover:bg-gray-200'"
              class="px-4 py-2 rounded-lg font-semibold transition"
              :style="filterType === 'order' ? 'background-color: #2F5597;' : ''"
            >
              Orders
            </button>
            <button 
              @click="filterType = 'quote'" 
              :class="filterType === 'quote' ? 'text-white' : 'text-gray-700 bg-gray-100 hover:bg-gray-200'"
              class="px-4 py-2 rounded-lg font-semibold transition"
              :style="filterType === 'quote' ? 'background-color: #2F5597;' : ''"
            >
              Quotes
            </button>
            <button 
              @click="filterType = 'invoice'" 
              :class="filterType === 'invoice' ? 'text-white' : 'text-gray-700 bg-gray-100 hover:bg-gray-200'"
              class="px-4 py-2 rounded-lg font-semibold transition"
              :style="filterType === 'invoice' ? 'background-color: #2F5597;' : ''"
            >
              Invoices
            </button>
          </div>
          <button 
            v-if="unreadCount > 0"
            @click="markAllAsRead" 
            class="px-4 py-2 text-sm text-blue-600 hover:text-blue-800 font-semibold transition"
          >
            Mark All as Read
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block">
          <div class="w-12 h-12 border-4 rounded-full" style="border-color: #2F5597; border-top-color: transparent; animation: spin 1s linear infinite;"></div>
        </div>
      </div>

      <!-- Messages List -->
      <div v-else-if="filteredMessages.length > 0" class="space-y-4">
        <div 
          v-for="message in filteredMessages" 
          :key="message.id"
          class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden"
          :class="message.status === 'unread' ? 'border-l-4' : ''"
          :style="message.status === 'unread' ? 'border-color: #2F5597;' : ''"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-3">
              <div class="flex items-start gap-3">
                <!-- Icon -->
                <div 
                  class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                  :style="getTypeStyle(message.type)"
                >
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

                <!-- Content -->
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold text-gray-900">{{ message.title }}</h3>
                    <span 
                      v-if="message.priority === 'high' || message.priority === 'urgent'"
                      class="px-2 py-1 text-xs font-semibold rounded"
                      :class="message.priority === 'urgent' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800'"
                    >
                      {{ message.priority }}
                    </span>
                  </div>
                  <p class="text-gray-700 mb-2">{{ message.message }}</p>
                  <div class="flex items-center gap-3 text-sm text-gray-500">
                    <span>{{ message.time_ago }}</span>
                    <span v-if="message.reference_id" class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                      {{ message.reference_id }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center gap-2 ml-4">
                <button 
                  v-if="message.status === 'unread'"
                  @click="markAsRead(message.id)"
                  class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                  title="Mark as read"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </button>
                <button 
                  @click="deleteMessage(message.id)"
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
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No Messages</h3>
        <p class="text-gray-600">You're all caught up! No messages to display.</p>
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
import { useToastStore } from '../../stores/toastStore'
import Navbar from '../../components/Navbar.vue'

const toastStore = useToastStore()
const messages = ref([])
const loading = ref(false)
const filterType = ref(null)
const unreadCount = ref(0)

const filteredMessages = computed(() => {
  if (!filterType.value) return messages.value
  return messages.value.filter(msg => msg.type === filterType.value)
})

const getTypeStyle = (type) => {
  const styles = {
    order: 'background-color: #2F5597;',
    quote: 'background-color: #10b981;',
    invoice: 'background-color: #f59e0b;',
    system: 'background-color: #6b7280;'
  }
  return styles[type] || styles.system
}

const fetchMessages = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await fetch('http://127.0.0.1:8000/api/v1/messages', {
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
    const response = await fetch(`http://127.0.0.1:8000/api/v1/messages/${id}/read`, {
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
    const response = await fetch('http://127.0.0.1:8000/api/v1/messages/mark-all-read', {
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
    const response = await fetch(`http://127.0.0.1:8000/api/v1/messages/${id}`, {
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

onMounted(() => {
  fetchMessages()
})
</script>
