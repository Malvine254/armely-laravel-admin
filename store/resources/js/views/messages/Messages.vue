<template>
  <div class="h-dvh overflow-hidden bg-[#eef2f9] text-gray-900 flex flex-col">
    <Navbar />

    <div class="max-w-7xl w-full mx-auto px-3 sm:px-4 lg:px-5 py-3 sm:py-4 flex-1 min-h-0 flex flex-col overflow-hidden">
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 flex-1 min-h-0 overflow-hidden relative">
        <section
          class="rounded-3xl bg-white shadow-[0_4px_24px_rgba(16,36,71,0.06)] overflow-hidden min-h-0 flex flex-col xl:col-span-4 transition-transform duration-300 ease-out xl:static xl:translate-x-0 xl:top-auto xl:bottom-auto xl:left-auto xl:z-auto"
          :class="isHistoryOpenMobile
            ? 'fixed xl:static z-[70] top-[8.75rem] bottom-3 left-3 w-[84vw] max-w-sm translate-x-0 xl:w-auto xl:max-w-none'
            : 'fixed xl:static z-[70] top-[8.75rem] bottom-3 left-3 w-[84vw] max-w-sm -translate-x-[110%] xl:w-auto xl:max-w-none'"
        >
          <div class="px-5 pt-5 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5 mb-5">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-black text-lg" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">M</div>
              <span class="text-xl font-extrabold tracking-tight text-gray-900">Mela</span>
            </div>

            <div class="flex items-center justify-between mb-1.5">
              <h2 class="text-[11px] font-bold text-gray-500 uppercase tracking-[0.08em]">Chat History</h2>
              <div class="flex items-center gap-2">
                <button
                  v-if="chatSessions.length"
                  @click="toggleManageHistory"
                  class="px-3 py-1.5 text-[11px] font-semibold rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition"
                >
                  {{ manageHistoryMode ? 'Done' : 'Manage' }}
                </button>
                <button
                  @click="createNewChatSession"
                  :disabled="manageHistoryMode"
                  class="px-3 py-1.5 text-[11px] font-semibold rounded-lg text-white shadow-sm transition hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed"
                  style="background-color: #2F5597;"
                >
                  + New Chat
                </button>
                <button
                  class="xl:hidden w-8 h-8 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100"
                  @click="closeHistoryPanel"
                  aria-label="Close chat history"
                >
                  <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <p class="text-[11px] text-gray-400">Choose a conversation or start a new one.</p>

            <div class="relative mt-4">
              <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
              </svg>
              <input
                v-model="historySearch"
                type="search"
                placeholder="Search conversations..."
                aria-label="Search conversations"
                class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-3 py-2.5 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2F5597]/30 focus:border-[#2F5597]/40 focus:bg-white transition"
              >
            </div>

            <div v-if="manageHistoryMode" class="mt-3 flex items-center justify-between gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2">
              <p class="text-[11px] font-semibold text-gray-700">
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
                  class="text-[11px] font-semibold text-gray-500 hover:text-rose-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Clear All
                </button>
              </div>
            </div>
          </div>

          <div class="flex-1 min-h-0 overflow-y-auto themed-scrollbar px-3 py-3">
            <template v-for="group in groupedSessions" :key="`group-${group.label}`">
              <p class="px-2 pt-1 pb-2 text-[11px] font-bold text-gray-400">{{ group.label }}</p>

              <button
                v-for="session in group.sessions"
                :key="`chat-session-${session.id}`"
                @click="handleSessionCardClick(session.id)"
                class="w-full text-left rounded-xl mb-1.5 p-3 transition relative"
                :class="manageHistoryMode && selectedHistoryIds.includes(session.id)
                  ? 'bg-rose-50 ring-1 ring-rose-200'
                  : activeChatSessionId === session.id
                  ? 'bg-[#2F5597]/[0.07]'
                  : 'hover:bg-gray-50'"
              >
                <span
                  v-if="activeChatSessionId === session.id && !manageHistoryMode"
                  class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full"
                  style="background-color: #2F5597;"
                ></span>

                <div class="flex items-start gap-2.5">
                  <div
                    v-if="manageHistoryMode"
                    class="mt-0.5 w-5 h-5 rounded-md border flex items-center justify-center flex-shrink-0"
                    :class="selectedHistoryIds.includes(session.id) ? 'bg-rose-600 border-rose-600 text-white' : 'border-gray-300 bg-white text-transparent'"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <div
                    class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                    :class="session.resolved_at ? 'bg-emerald-500/15 text-emerald-600' : session.escalated_to_human ? 'bg-amber-500/15 text-amber-600' : 'bg-[#2F5597]/10 text-[#2F5597]'"
                  >
                    <svg v-if="session.resolved_at" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else-if="session.escalated_to_human" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V10a8 8 0 10-16 0v10h5m6 0v-3a3 3 0 00-3-3h-2a3 3 0 00-3 3v3m8 0H9" />
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.9 9.9 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                  </div>

                  <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                      <p class="text-[13px] font-bold text-gray-900 truncate">{{ session.title || 'New chat' }}</p>
                      <span class="text-[10px] text-gray-400 flex-shrink-0 font-medium">{{ sessionTimeLabel(session) }}</span>
                    </div>
                    <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ session.last_message_preview || 'No messages yet' }}</p>
                    <span v-if="session.resolved_at" class="inline-block mt-1.5 text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 font-semibold">Resolved</span>
                    <span v-else-if="session.escalated_to_human" class="inline-block mt-1.5 text-[10px] px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-600 font-semibold">Human</span>
                  </div>
                </div>
              </button>
            </template>

            <p v-if="!chatSessions.length && !loadingSessions" class="text-[11px] text-gray-400 px-2 py-1">
              No chat sessions yet.
            </p>
            <p v-else-if="chatSessions.length && !groupedSessions.length" class="text-[11px] text-gray-400 px-2 py-1">
              No conversations match "{{ historySearch }}".
            </p>

            <!-- Loading skeleton -->
            <template v-if="loadingSessions && !chatSessions.length">
              <div v-for="i in 4" :key="`skeleton-${i}`" class="w-full rounded-xl bg-white mb-1.5 p-3 animate-pulse">
                <div class="flex items-start gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                  <div class="min-w-0 flex-1 space-y-2">
                    <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-2.5 bg-gray-200 rounded w-1/2"></div>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <div class="shrink-0 px-4 py-3.5 border-t border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0 overflow-hidden" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">
              <img
                v-if="userAvatarUrl && !userAvatarFailed"
                :src="userAvatarUrl"
                :alt="userDisplayName"
                class="w-full h-full object-cover"
                @error="userAvatarFailed = true"
              >
              <span v-else>{{ userInitials }}</span>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-[13px] font-bold text-gray-900 truncate">{{ userDisplayName }}</p>
              <p class="text-[11px] text-gray-400 truncate">{{ userEmail }}</p>
            </div>
            <button
              @click="openAccountSettings"
              class="w-8 h-8 rounded-lg text-gray-400 hover:text-[#2F5597] hover:bg-gray-100 transition flex items-center justify-center flex-shrink-0"
              aria-label="Account settings"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </div>
        </section>

        <section class="xl:col-span-8 rounded-3xl bg-white shadow-[0_4px_24px_rgba(16,36,71,0.06)] overflow-hidden flex flex-col min-h-0">
          <div class="px-5 py-4 border-b border-gray-100 bg-white flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0">
              <button
                class="xl:hidden w-9 h-9 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 flex items-center justify-center flex-shrink-0"
                @click="toggleHistoryPanel"
                aria-label="Toggle chat history"
              >
                <svg class="w-4 h-4 transition-transform duration-300" :class="isHistoryOpenMobile ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>

              <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white font-black text-xl flex-shrink-0" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">M</div>

              <div class="min-w-0">
                <h2 class="text-lg font-extrabold text-gray-900 leading-tight truncate">Mela AI Assistant</h2>
                <p class="text-xs text-gray-500 truncate">
                  Your intelligent partner for products, orders, and support.
                  <span v-if="activeSession?.escalated_to_human" class="font-semibold text-amber-600"> · Escalated to human</span>
                  <span v-if="activeSession?.resolved_at" class="font-semibold text-emerald-600"> · Resolved</span>
                </p>
              </div>
            </div>

            <button
              @click="escalateActiveChat"
              :disabled="!activeChatSessionId || escalating || (activeSession?.escalated_to_human && !activeSession?.resolved_at)"
              class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-[13px] font-bold text-amber-600 bg-amber-50 hover:bg-amber-100 transition disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0 self-start sm:self-auto"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              {{ activeSession?.resolved_at ? (escalating ? 'Reopening...' : 'Reopen to Human') : activeSession?.escalated_to_human ? 'Escalated' : (escalating ? 'Escalating...' : 'Escalate to Human') }}
            </button>
          </div>

          <div ref="chatScrollRef" class="flex-1 min-h-0 overflow-y-auto themed-scrollbar px-4 sm:px-6 py-5 space-y-5 bg-[#fafbfd]">
            <!-- Persistent welcome banner — always shown at the top of every chat -->
            <div class="flex justify-start items-end gap-2.5">
              <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-black text-sm flex-shrink-0" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">M</div>
              <div class="max-w-[88%] sm:max-w-[74%] rounded-2xl rounded-bl-md px-4 py-3 bg-white shadow-[0_1px_3px_rgba(16,36,71,0.08)] text-gray-900">
                <p class="text-sm whitespace-pre-wrap leading-relaxed">I'm Mela AI, your Armely assistant. Ask me about products, quotes, orders, invoices, or anything else you're working on.</p>
              </div>
            </div>

            <div
              v-for="chat in chatMessages"
              :key="chat.id"
              class="flex items-end gap-2.5"
              :class="chat.role === 'user' ? 'justify-end' : 'justify-start'"
            >
              <div
                v-if="chat.role !== 'user'"
                class="w-9 h-9 rounded-full flex items-center justify-center text-white font-black text-sm flex-shrink-0"
                :style="chat.role === 'admin'
                  ? 'background: linear-gradient(135deg, #f0a33c 0%, #d97706 100%);'
                  : 'background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);'"
              >
                {{ chat.role === 'admin' ? 'S' : 'M' }}
              </div>

              <div
                class="max-w-[88%] sm:max-w-[74%] rounded-2xl px-4 py-3"
                :class="chat.role === 'user'
                  ? 'text-white rounded-br-md shadow-[0_2px_8px_rgba(47,85,151,0.25)]'
                  : 'bg-white text-gray-900 rounded-bl-md shadow-[0_1px_3px_rgba(16,36,71,0.08)]'"
                :style="chat.role === 'user' ? 'background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);' : ''"
              >
                <p class="text-sm whitespace-pre-wrap leading-relaxed" v-html="renderMessageHtml(chat.text)"></p>

                <div v-if="chat.attachments?.length" class="mt-2.5 flex flex-wrap gap-2">
                  <a
                    v-for="file in chat.attachments"
                    :key="`attachment-${chat.id}-${file.id}`"
                    :href="file.url"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-2 rounded-xl px-2 py-1.5 max-w-[200px] transition"
                    :class="chat.role === 'user' ? 'bg-white/15 hover:bg-white/25' : 'bg-gray-50 hover:bg-gray-100 border border-gray-100'"
                  >
                    <img
                      v-if="file.is_image && file.url && !file.previewFailed"
                      :src="file.url"
                      :alt="file.name"
                      class="w-9 h-9 rounded-lg object-cover flex-shrink-0"
                      @error="file.previewFailed = true"
                    >
                    <svg v-else class="w-5 h-5 flex-shrink-0" :class="chat.role === 'user' ? 'text-blue-100' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="text-[11px] font-semibold truncate" :class="chat.role === 'user' ? 'text-white' : 'text-gray-700'">{{ file.name }}</span>
                  </a>
                </div>
                <p v-if="chat.degraded" class="mt-2 text-xs font-semibold text-amber-700">
                  Limited response — live assistant services were unavailable.
                </p>

                <div v-if="chat.productSuggestions?.length" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                  <article
                    v-for="product in chat.productSuggestions"
                    :key="`suggestion-${chat.id}-${product.product_id}`"
                    class="rounded-xl border border-gray-100 bg-gray-50 p-2.5"
                  >
                    <div class="flex gap-2">
                      <div class="w-14 h-14 rounded-lg bg-white border border-gray-100 overflow-hidden flex-shrink-0 flex items-center justify-center">
                        <img
                          v-if="product.image_url && !product._imgError"
                          :src="product.image_url"
                          :alt="product.name"
                          class="w-full h-full object-cover"
                          @error="product._imgError = true"
                        >
                        <svg v-else class="w-6 h-6 text-[#2F5597]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                      </div>
                      <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ product.name }}</p>
                        <p class="text-[11px] text-gray-500 truncate" v-if="product.vendor">{{ product.vendor }} · {{ product.sku || 'SKU N/A' }}</p>
                        <p class="text-xs font-bold text-[#2F5597] mt-0.5">{{ formatCurrency(product.price) }}</p>
                      </div>
                    </div>

                    <p class="mt-2 text-[11px] text-gray-600">{{ product.why }}</p>

                    <div class="mt-2 flex flex-wrap gap-1.5">
                      <button
                        v-for="action in product.actions || []"
                        :key="`prod-action-${product.product_id}-${action.label}`"
                        @click="handleProductAction(product, action)"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-[#2F5597] bg-[#2F5597]/10 hover:bg-[#2F5597]/20 transition"
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
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                    :class="chat.role === 'user'
                      ? 'bg-white/20 text-white hover:bg-white/30'
                      : 'text-[#2F5597] bg-[#2F5597]/10 hover:bg-[#2F5597]/20'"
                  >
                    {{ action.label }}
                  </button>
                </div>
                <p class="mt-2 text-[10px] uppercase tracking-wide font-semibold" :class="chat.role === 'user' ? 'text-blue-100' : 'text-gray-400'">
                  {{ getMessageSenderLabel(chat) }} · {{ formatMessageTimestamp(chat.createdAt) }}
                </p>
              </div>

              <div
                v-if="chat.role === 'user'"
                class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[11px] font-bold flex-shrink-0 overflow-hidden"
              >
                <img
                  v-if="userAvatarUrl && !userAvatarFailed"
                  :src="userAvatarUrl"
                  :alt="userDisplayName"
                  class="w-full h-full object-cover"
                  @error="userAvatarFailed = true"
                >
                <span v-else>{{ userInitials }}</span>
              </div>
            </div>

            <!-- Always reserve this row so the typing indicator never shifts messages. -->
            <div class="h-12 flex justify-start items-start gap-2.5" aria-live="polite" aria-atomic="true">
              <template v-if="sendingChat">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-black text-sm flex-shrink-0" style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);">M</div>
                <div
                  class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-[0_1px_3px_rgba(16,36,71,0.08)] pointer-events-none select-none"
                  role="status"
                  aria-label="Mela AI is typing"
                >
                  <div class="flex items-center gap-1">
                    <span class="h-2 w-2 bg-[#2F5597] rounded-full animate-bounce [animation-delay:-0.2s]"></span>
                    <span class="h-2 w-2 bg-[#2F5597] rounded-full animate-bounce [animation-delay:-0.1s]"></span>
                    <span class="h-2 w-2 bg-[#2F5597] rounded-full animate-bounce"></span>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <form
            class="shrink-0 px-4 sm:px-6 pt-4 border-t border-gray-100 bg-white"
            style="padding-bottom: calc(1rem + env(safe-area-inset-bottom));"
            @submit.prevent="sendChatMessage()"
          >
            <div v-if="pendingAttachments.length" class="mb-2.5 flex flex-wrap gap-2">
              <div
                v-for="file in pendingAttachments"
                :key="`pending-${file.id ?? file.tempId}`"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 pl-2 pr-1.5 py-1.5 max-w-[220px]"
              >
                <img
                  v-if="file.is_image && file.url && !file.previewFailed"
                  :src="file.url"
                  :alt="file.name"
                  class="w-8 h-8 rounded-lg object-cover flex-shrink-0"
                  @error="file.previewFailed = true"
                >
                <svg v-else class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <div class="min-w-0">
                  <p class="text-[11px] font-semibold text-gray-700 truncate">{{ file.name }}</p>
                  <p class="text-[10px] text-gray-400">{{ file.uploading ? 'Uploading…' : formatFileSize(file.size_bytes) }}</p>
                </div>
                <button
                  type="button"
                  @click="removePendingAttachment(file)"
                  class="w-6 h-6 rounded-md text-gray-400 hover:text-rose-500 hover:bg-white flex items-center justify-center flex-shrink-0"
                  :aria-label="`Remove ${file.name}`"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <div class="flex gap-2.5 items-end">
              <div class="flex-1 flex items-end gap-2 rounded-2xl border border-gray-200 bg-gray-50 px-3 py-2 focus-within:bg-white focus-within:border-[#2F5597]/40 focus-within:ring-2 focus-within:ring-[#2F5597]/20 transition">
                <input
                  ref="attachmentInputRef"
                  type="file"
                  class="hidden"
                  multiple
                  :accept="ATTACHMENT_ACCEPT"
                  @change="handleAttachmentSelected"
                >
                <button
                  type="button"
                  @click="attachmentInputRef?.click()"
                  :disabled="pendingAttachments.length >= MAX_ATTACHMENTS"
                  :title="pendingAttachments.length >= MAX_ATTACHMENTS ? `Up to ${MAX_ATTACHMENTS} files per message` : 'Attach an image or document'"
                  class="w-8 h-8 rounded-lg text-gray-400 hover:text-[#2F5597] hover:bg-gray-100 flex items-center justify-center flex-shrink-0 transition disabled:opacity-40 disabled:cursor-not-allowed"
                  aria-label="Attach a file"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                  </svg>
                </button>

                <textarea
                  v-model="chatInput"
                  rows="1"
                  :placeholder="isWaitingForHuman
                    ? 'This chat is escalated. Your message will be sent to a human support agent.'
                    : 'Ask Mela AI about products, invoices, payments, quotes, and tracking...'"
                  class="flex-1 resize-none bg-transparent border-0 py-1.5 text-sm text-gray-900 placeholder-gray-400 max-h-40 focus:outline-none focus:ring-0"
                  @input="autoGrowComposer"
                  @keydown.enter.exact.prevent="sendChatMessage()"
                ></textarea>

                <div class="relative flex-shrink-0" data-emoji-menu>
                  <button
                    type="button"
                    @click="showEmojiPicker = !showEmojiPicker"
                    class="w-8 h-8 rounded-lg text-gray-400 hover:text-[#2F5597] hover:bg-gray-100 flex items-center justify-center transition"
                    aria-label="Insert emoji"
                    :aria-expanded="showEmojiPicker"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>

                  <div
                    v-if="showEmojiPicker"
                    class="absolute bottom-11 right-0 z-20 w-56 rounded-xl border border-gray-200 bg-white p-2 shadow-lg grid grid-cols-7 gap-1"
                  >
                    <button
                      v-for="emoji in quickEmojis"
                      :key="`emoji-${emoji}`"
                      type="button"
                      @click="insertEmoji(emoji)"
                      class="w-7 h-7 rounded-md text-base leading-none hover:bg-gray-100 transition"
                    >
                      {{ emoji }}
                    </button>
                  </div>
                </div>
              </div>

              <button
                type="submit"
                :disabled="chatRequestInFlight || uploadingAttachment || (!chatInput.trim() && !readyAttachmentIds.length)"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-white text-sm font-bold shadow-[0_2px_8px_rgba(47,85,151,0.3)] transition hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex-shrink-0"
                style="background: linear-gradient(135deg, #3b6fc4 0%, #2F5597 100%);"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
                Send
              </button>
            </div>

            <div class="mt-2.5 flex items-center justify-between gap-3 text-[11px] text-gray-400">
              <p class="inline-flex items-center gap-1.5 min-w-0">
                <svg class="w-3.5 h-3.5 flex-shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21h6M10 17h4a4 4 0 002-3.5A6 6 0 106 13.5 4 4 0 008 17z" />
                </svg>
                <span class="truncate">Try asking about product availability, order status, invoices, or getting a quote.</span>
              </p>
              <p class="hidden sm:block flex-shrink-0">Press Enter to send · Shift + Enter for new line</p>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '../../stores/toastStore'
import { useAuthStore } from '../../stores/authStore'
import { useCartStore } from '../../stores/cartStore'
import { applyAssistantCartOperation } from '../../services/assistantCart'
import { getAuthStorageKeys } from '../../services/authContext'
import { API_BASE_URL, resolveProductImageUrl, resolveProfilePictureUrl } from '../../services/runtimeConfig'
import Navbar from '../../components/Navbar.vue'
import { usePricingSettings } from '../../composables/usePricingSettings'

const toastStore = useToastStore()
const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const loadCartProduct = async (productId) => {
  const response = await fetch(`${API_BASE_URL}/products/${encodeURIComponent(productId)}`, {
    headers: { Authorization: `Bearer ${getAuthToken()}`, Accept: 'application/json' },
  })
  if (!response.ok) throw new Error('Unable to check product availability')
  const payload = await response.json()
  return payload.data
}

const handleProductAction = async (product, action) => {
  if (action.label !== 'Request quote') return openActionLink(action.link)
  try {
    const applied = await applyAssistantCartOperation(cartStore, {
      type: 'prepare_quote', items: [{ productId: product.product_id, quantity: 1 }],
    }, loadCartProduct)
    if (!applied) throw new Error('Product cannot be added')
    await router.push('/cart?assistant_quote=1')
  } catch {
    toastStore.addToast('Unable to prepare this quote. Check your account and product availability.', 'error')
  }
}
const { loadPricingSettings, formatUsdUsingCurrentCurrency } = usePricingSettings()

const chatMessages = ref([])
const chatInput = ref('')
const sendingChat = ref(false)
const chatRequestInFlight = ref(false)
const chatScrollRef = ref(null)
const chatSessions = ref([])
const activeChatSessionId = ref(null)
const escalating = ref(false)
const pollingInterval = ref(null)
const isHistoryOpenMobile = ref(false)
const manageHistoryMode = ref(false)
const selectedHistoryIds = ref([])
const deletingHistory = ref(false)
const loadingSessions = ref(true)
const historySearch = ref('')
const showEmojiPicker = ref(false)
const quickEmojis = ['👍', '🙏', '👌', '🎉', '✅', '❓', '🙂', '😀', '😅', '🤔', '🔥', '💡', '⚠️', '❤️']
let previousBodyOverflow = ''
let previousHtmlOverflow = ''
let pollingFailureCount = 0

const POLLING_BASE_DELAY_MS = 5000
const POLLING_MAX_DELAY_MS = 60000

const configuredAllowedAssistantHosts = String(import.meta.env.VITE_ASSISTANT_ALLOWED_LINK_HOSTS || '')
  .split(',')
  .map((host) => host.trim().toLowerCase())
  .filter(Boolean)

const assistantAllowedHosts = Array.from(new Set([
  window.location.hostname.toLowerCase(),
  'armely.com',
  'www.armely.com',
  ...configuredAllowedAssistantHosts,
]))

const getAuthToken = () => {
  const tokenKey = getAuthStorageKeys('customer').token
  return localStorage.getItem(tokenKey) || sessionStorage.getItem(tokenKey)
}

const getChatCacheKey = (scope) => {
  const userId = authStore.user?.id || 'guest'
  return `mela-chat:${scope}:${userId}`
}

const readCachedJson = (key, fallback) => {
  if (typeof window === 'undefined') return fallback
  try {
    const raw = sessionStorage.getItem(key)
    return raw ? JSON.parse(raw) : fallback
  } catch (error) {
    return fallback
  }
}

const writeCachedJson = (key, value) => {
  if (typeof window === 'undefined') return
  try {
    sessionStorage.setItem(key, JSON.stringify(value))
  } catch (error) {
    // Ignore storage write failures.
  }
}

const PRODUCT_SUGGESTION_MAX_AGE_MS = 24 * 60 * 60 * 1000

const normalizeProductSuggestions = (suggestions, createdAt = null) => {
  if (!Array.isArray(suggestions)) return []
  const createdTimestamp = createdAt ? new Date(createdAt).getTime() : Number.NaN
  if (Number.isFinite(createdTimestamp) && Date.now() - createdTimestamp > PRODUCT_SUGGESTION_MAX_AGE_MS) {
    return []
  }

  return suggestions.map((item) => {
    const imageUrl = String(item?.image_url || '').trim()
    return {
      ...item,
      image_url: imageUrl ? resolveProductImageUrl(imageUrl) : '',
    }
  })
}

const getCachedSessionMessages = (sessionId) => {
  const messages = readCachedJson(getChatCacheKey(`session:${sessionId}`), [])
  if (!Array.isArray(messages)) return []

  return messages.map((message) => ({
    ...message,
    productSuggestions: normalizeProductSuggestions(message.productSuggestions, message.createdAt),
  }))
}

const clearLegacyPersistentChatCache = () => {
  if (typeof window === 'undefined') return
  for (let index = localStorage.length - 1; index >= 0; index -= 1) {
    const key = localStorage.key(index)
    if (key?.startsWith('mela-chat:')) {
      localStorage.removeItem(key)
    }
  }
}

const cacheSessionMessages = (sessionId, messages) => {
  if (!sessionId) return
  writeCachedJson(getChatCacheKey(`session:${sessionId}`), messages)
}

// The first send of a new chat creates the session server-side, so this upserts rather than
// skipping ids the sidebar has not seen yet.
const updateSessionPreviewInstantly = (sessionId, preview, role = 'user', title = null) => {
  if (!sessionId) return

  const index = chatSessions.value.findIndex((session) => Number(session.id) === Number(sessionId))
  const existing = index >= 0 ? chatSessions.value[index] : null

  const updated = {
    escalated_to_human: false,
    resolved_at: null,
    ...(existing || {}),
    id: existing ? existing.id : Number(sessionId),
    title: title || existing?.title || 'New chat',
    last_message_preview: String(preview || '').slice(0, 80),
    last_message_role: role,
    last_message_at: new Date().toISOString(),
  }

  if (index >= 0) {
    chatSessions.value.splice(index, 1)
  }
  chatSessions.value.unshift(updated)
  writeCachedJson(getChatCacheKey('sessions'), chatSessions.value)
}

const clearCachedSessionMessages = (sessionId) => {
  if (!sessionId || typeof window === 'undefined') return
  sessionStorage.removeItem(getChatCacheKey(`session:${sessionId}`))
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

const activeSession = computed(() => chatSessions.value.find((session) => session.id === activeChatSessionId.value) || null)
const isWaitingForHuman = computed(() => {
  return !!(activeSession.value?.escalated_to_human && !activeSession.value?.resolved_at)
})

const chatWelcomeName = computed(() => {
  const name = (authStore.user?.name || '').trim()
  if (!name) return 'there'
  return name.split(' ')[0]
})

const userDisplayName = computed(() => (authStore.user?.name || '').trim() || 'Your account')
const userEmail = computed(() => (authStore.user?.email || '').trim() || '')

const userInitials = computed(() => {
  const parts = (authStore.user?.name || '').trim().split(/\s+/).filter(Boolean)
  if (!parts.length) return 'ME'
  return (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase()
})

const userAvatarFailed = ref(false)

const userAvatarUrl = computed(() => resolveProfilePictureUrl(
  authStore.user?.profile_picture_url,
  authStore.user?.profile_picture
))

watch(userAvatarUrl, () => {
  userAvatarFailed.value = false
})

const openAccountSettings = () => router.push('/account')

const filteredSessions = computed(() => {
  const term = historySearch.value.trim().toLowerCase()
  if (!term) return chatSessions.value

  return chatSessions.value.filter((session) => {
    const title = String(session.title || '').toLowerCase()
    const preview = String(session.last_message_preview || '').toLowerCase()
    return title.includes(term) || preview.includes(term)
  })
})

const sessionDateGroup = (session) => {
  const raw = session.last_message_at || session.updated_at
  const date = raw ? new Date(raw) : null
  if (!date || Number.isNaN(date.getTime())) return 'Earlier'

  const now = new Date()
  if (date.toDateString() === now.toDateString()) return 'Today'

  const yesterday = new Date(now)
  yesterday.setDate(now.getDate() - 1)
  if (date.toDateString() === yesterday.toDateString()) return 'Yesterday'

  return (now - date) / 86400000 < 7 ? 'This week' : 'Earlier'
}

const groupedSessions = computed(() => {
  const order = ['Today', 'Yesterday', 'This week', 'Earlier']
  const buckets = new Map(order.map((label) => [label, []]))

  filteredSessions.value.forEach((session) => {
    buckets.get(sessionDateGroup(session)).push(session)
  })

  return order
    .map((label) => ({ label, sessions: buckets.get(label) }))
    .filter((group) => group.sessions.length)
})

const sessionTimeLabel = (session) => {
  const raw = session.last_message_at || session.updated_at
  const date = raw ? new Date(raw) : null
  if (!date || Number.isNaN(date.getTime())) return ''

  const now = new Date()
  if (date.toDateString() === now.toDateString()) {
    return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })
  }

  return date.toLocaleDateString([], { month: 'short', day: 'numeric' })
}

const insertEmoji = (emoji) => {
  chatInput.value = `${chatInput.value}${emoji}`
  showEmojiPicker.value = false
}

const MAX_ATTACHMENTS = 4
const MAX_ATTACHMENT_BYTES = 10 * 1024 * 1024
const ATTACHMENT_ACCEPT = 'image/jpeg,image/png,image/webp,image/gif,text/plain,text/csv,text/markdown,application/json,application/pdf'

const attachmentInputRef = ref(null)
const pendingAttachments = ref([])
const uploadingAttachment = computed(() => pendingAttachments.value.some((file) => file.uploading))
const readyAttachmentIds = computed(() => pendingAttachments.value.filter((file) => file.id).map((file) => file.id))

const formatFileSize = (bytes) => {
  const size = Number(bytes || 0)
  if (size < 1024) return `${size} B`
  if (size < 1024 * 1024) return `${Math.round(size / 1024)} KB`
  return `${(size / (1024 * 1024)).toFixed(1)} MB`
}

const removePendingAttachment = (file) => {
  pendingAttachments.value = pendingAttachments.value.filter((item) => item.tempId !== file?.tempId)
}

const uploadAttachment = async (file) => {
  const tempId = `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`

  pendingAttachments.value.push({
    tempId,
    name: file.name,
    size_bytes: file.size,
    is_image: file.type.startsWith('image/'),
    // No blob: preview here — the page CSP allows img-src 'self' only, so the thumbnail uses
    // the server URL once the upload returns.
    url: null,
    uploading: true,
    id: null,
  })

  try {
    const body = new FormData()
    body.append('file', file)

    const response = await fetch(`${API_BASE_URL}/messages/assistant/attachments`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${getAuthToken()}`, Accept: 'application/json' },
      body,
    })

    const payload = await response.json().catch(() => null)
    if (!response.ok || !payload?.data?.id) {
      throw new Error(payload?.message || 'Upload failed')
    }

    // Replace the entry rather than mutating it: the pushed object is the raw target, and
    // mutating a raw target behind a reactive proxy does not trigger an update.
    const index = pendingAttachments.value.findIndex((item) => item.tempId === tempId)
    if (index >= 0) {
      pendingAttachments.value[index] = {
        ...pendingAttachments.value[index],
        ...payload.data,
        name: pendingAttachments.value[index].name || payload.data.name,
        uploading: false,
      }
    }
  } catch (error) {
    removePendingAttachment({ tempId })
    toastStore.addToast(`Could not attach ${file.name}`, 'error')
  }
}

const handleAttachmentSelected = async (event) => {
  const files = Array.from(event.target.files || [])
  event.target.value = ''

  for (const file of files) {
    if (pendingAttachments.value.length >= MAX_ATTACHMENTS) {
      toastStore.addToast(`You can attach up to ${MAX_ATTACHMENTS} files per message`, 'warning')
      break
    }
    if (file.size > MAX_ATTACHMENT_BYTES) {
      toastStore.addToast(`${file.name} is larger than 10 MB`, 'warning')
      continue
    }
    await uploadAttachment(file)
  }
}

const handleDocumentClick = (event) => {
  if (showEmojiPicker.value && !event.target.closest('[data-emoji-menu]')) {
    showEmojiPicker.value = false
  }
}

const autoGrowComposer = (event) => {
  const el = event.target
  el.style.height = 'auto'
  el.style.height = `${Math.min(el.scrollHeight, 160)}px`
}

// Welcome message is now a static banner in the template \u2014 always visible, never needs to be injected.
const ensureChatWelcome = () => {}

const scrollChatToBottom = async (smooth = false) => {
  await nextTick()
  if (!chatScrollRef.value) return
  chatScrollRef.value.scrollTo({
    top: chatScrollRef.value.scrollHeight,
    behavior: smooth ? 'smooth' : 'auto',
  })
}

const refreshChatMessages = async () => {
  // Never run while a send is in-flight — it would clobber the optimistic message.
  if (!activeChatSessionId.value || chatRequestInFlight.value) return null

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

    if (!response.ok) return false

    const payload = await response.json()
    const loadedMessages = payload?.data?.messages || []

    // A send may have started while this session request was in flight. Never let
    // the older response erase the optimistic message that is already on screen.
    if (chatRequestInFlight.value) return null
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

    const serverMessages = loadedMessages.map((item) => ({
      id: item.id,
      role: item.role,
      text: item.text,
      senderName: item.sender_name || null,
      createdAt: item.created_at || null,
      actions: item.actions || [],
      attachments: item.attachments || [],
      productSuggestions: normalizeProductSuggestions(item.product_suggestions || [], item.created_at),
      degraded: !!item.degraded,
    }))

    if (!serverMessages.length) return true

    const clientCount = chatMessages.value.length
    const serverCount = serverMessages.length

    // Reconcile in-place when counts match: replace temp IDs with real DB IDs
    // without touching the array reference — no re-render flicker.
    if (serverCount === clientCount) {
      chatMessages.value.forEach((cm, i) => {
        const sm = serverMessages[i]
        if (!sm) return
        if (typeof cm.id === 'string') {
          // Optimistic message: adopt real ID and timestamp silently
          cm.id = sm.id
          if (sm.createdAt) cm.createdAt = sm.createdAt
          delete cm.optimistic
        }
      })
      cacheSessionMessages(activeChatSessionId.value, chatMessages.value)
      return true
    }

    // Server has new messages (e.g. admin reply) — smart merge: append only what's new.
    if (serverCount > clientCount) {
      const existingIds = new Set(chatMessages.value.map((m) => m.id))
      const added = serverMessages.filter((sm) => !existingIds.has(sm.id))
      if (added.length) {
        chatMessages.value.push(...added)
        cacheSessionMessages(activeChatSessionId.value, chatMessages.value)
        await scrollChatToBottom(true)
      }
      return true
    }

    // Server has fewer messages than client (e.g. after deletion) — full replace.
    chatMessages.value = serverMessages
    cacheSessionMessages(activeChatSessionId.value, serverMessages)
    return true
  } catch {
    return false
  }
}

const scheduleMessagePoll = (delay = POLLING_BASE_DELAY_MS) => {
  if (pollingInterval.value) {
    clearTimeout(pollingInterval.value)
  }

  pollingInterval.value = setTimeout(async () => {
    pollingInterval.value = null
    if (document.hidden) return

    const succeeded = await refreshChatMessages()
    pollingFailureCount = succeeded === false ? pollingFailureCount + 1 : 0
    const nextDelay = Math.min(
      POLLING_BASE_DELAY_MS * (2 ** pollingFailureCount),
      POLLING_MAX_DELAY_MS
    )
    scheduleMessagePoll(nextDelay)
  }, delay)
}

const startMessagePolling = () => {
  pollingFailureCount = 0
  if (!document.hidden) scheduleMessagePoll()
}

const stopMessagePolling = () => {
  if (!pollingInterval.value) return
  clearTimeout(pollingInterval.value)
  pollingInterval.value = null
}

const handleDocumentVisibilityChange = () => {
  if (document.hidden) {
    stopMessagePolling()
    return
  }

  void refreshChatMessages()
  startMessagePolling()
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
      attachments: item.attachments || [],
      productSuggestions: normalizeProductSuggestions(item.product_suggestions || [], item.created_at),
      degraded: !!item.degraded,
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

  const sanitizedLink = sanitizeAssistantLink(link)
  if (!sanitizedLink) {
    toastStore.addToast('Blocked an untrusted link from assistant output.', 'warning')
    return
  }

  if (sanitizedLink.startsWith('/api/')) {
    const token = getAuthToken()
    if (!token) {
      toastStore.addToast('Please log in to continue', 'warning')
      await router.push('/login')
      return
    }

    try {
      const response = await fetch(sanitizedLink, {
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
        const parts = String(sanitizedLink).split('/').filter(Boolean)
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
    if (sanitizedLink.startsWith('/')) {
      await router.push(sanitizedLink)
      return
    }

    const url = new URL(sanitizedLink)
    const isSameOrigin = url.origin === window.location.origin
    if (isSameOrigin) {
      await router.push(`${url.pathname}${url.search}${url.hash}`)
      return
    }

    window.open(sanitizedLink, '_blank', 'noopener')
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

const sanitizeAssistantLink = (rawLink) => {
  const link = String(rawLink || '').trim()
  if (!link) return null

  if (link.startsWith('/')) {
    return link.startsWith('//') ? null : link
  }

  let parsed
  try {
    parsed = new URL(link)
  } catch {
    return null
  }

  if (!['http:', 'https:'].includes(parsed.protocol)) {
    return null
  }

  const hostname = parsed.hostname.toLowerCase()
  const isAllowedHost = assistantAllowedHosts.some((allowed) => hostname === allowed || hostname.endsWith(`.${allowed}`))
  return isAllowedHost ? parsed.toString() : null
}

const renderMessageHtml = (text) => {
  const safe = escapeHtml(text)

  let html = safe
    // Markdown links  [text](url)
    .replace(/\[([^\]]+)\]\(((?:\/|https?:\/\/)[^)\s]+)\)/g, (match, label, url) => {
      const sanitizedLink = sanitizeAssistantLink(url)
      if (!sanitizedLink) {
        return `<span class="text-gray-500">${label}</span>`
      }

      return `<a href="${sanitizedLink}" class="text-[#1d4b8f] font-semibold underline hover:text-[#153a69]" target="_blank" rel="noopener">${label}</a>`
    })
    // Bold  **text**
    .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
    // Newlines
    .replace(/\n/g, '<br>')

  return html
}

const sendChatMessage = async (prefilled = null) => {
  const outgoing = (prefilled ?? chatInput.value).trim()
  const attachmentIds = readyAttachmentIds.value
  if ((!outgoing && !attachmentIds.length) || chatRequestInFlight.value || uploadingAttachment.value) return

  const sentAttachments = pendingAttachments.value.filter((file) => file.id)

  chatRequestInFlight.value = true
  stopMessagePolling()

  // 1. Show user message instantly (optimistic).
  const optimisticId = `user-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`
  chatMessages.value.push({
    id: optimisticId,
    role: 'user',
    text: outgoing,
    createdAt: new Date().toISOString(),
    actions: [],
    attachments: sentAttachments.map((file) => ({
      id: file.id,
      name: file.name,
      url: file.url,
      is_image: file.is_image,
    })),
    productSuggestions: [],
    optimistic: true,
  })
  chatInput.value = ''
  pendingAttachments.value = []

  // A brand new chat has no id yet. Create it before sending so the sidebar lists this
  // conversation straight away instead of only once the assistant has replied.
  const isNewSession = !activeChatSessionId.value
  if (isNewSession) {
    try {
      const createResponse = await fetch(`${API_BASE_URL}/messages/chats`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${getAuthToken()}`,
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        body: JSON.stringify({ title: 'New chat' })
      })
      const created = (await createResponse.json().catch(() => null))?.data
      if (created?.id) activeChatSessionId.value = created.id
    } catch {
      // Not fatal: the send itself creates a session when one is missing.
    }
  }

  // Cache the optimistic state before any network work begins.
  if (activeChatSessionId.value) {
    cacheSessionMessages(activeChatSessionId.value, chatMessages.value)
    updateSessionPreviewInstantly(
      activeChatSessionId.value,
      outgoing || 'Sent an attachment',
      'user',
      isNewSession ? (outgoing || 'Sent an attachment').slice(0, 60) : null
    )
  }

  // Commit one browser frame containing the user bubble before the typing state
  // is enabled. This guarantees the visual order: user message, then typing.
  await scrollChatToBottom()
  await new Promise((resolve) => window.requestAnimationFrame(() => resolve()))
  sendingChat.value = true
  await scrollChatToBottom()

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
        chat_session_id: activeChatSessionId.value,
        // The cart lives in the browser, so the agent only sees it if we send it.
        cart: cartStore.items.map(item => ({
          productId: String(item.productId),
          quantity: Number(item.quantity) || 1
        })),
        attachment_ids: attachmentIds
      })
    })

    if (!response.ok) throw new Error('Mela AI chat request failed')

    const payload = await response.json()
    const assistantPayload = payload?.data || {}
    // Execute only the operations returned for this send, never while loading chat history.
    const operations = (assistantPayload.cart_operations?.length
      ? assistantPayload.cart_operations
      : [assistantPayload.cart_operation].filter(Boolean)
    ).filter(op => ['add_to_cart', 'prepare_quote', 'set_cart_quantity', 'remove_from_cart'].includes(op?.type))

    if (operations.length) {
      let appliedCount = 0
      for (const operation of operations) {
        try {
          if (await applyAssistantCartOperation(cartStore, operation, loadCartProduct)) appliedCount++
        } catch {
          // Keep the conversation visible even if refreshing the product fails.
        }
      }

      if (appliedCount) {
        const toastMessage = {
          prepare_quote: 'Quote ready for review',
          set_cart_quantity: 'Cart quantity updated',
          remove_from_cart: 'Cart updated'
        }[operations[operations.length - 1].type] || 'Products added to cart'
        toastStore.addToast(toastMessage, 'success')
      }

      // The assistant already wrote its reply, so correct it rather than let it overstate.
      if (appliedCount < operations.length) {
        assistantPayload.reply = appliedCount === 0
          ? 'I could not apply that cart change. Please open the cart and check the items and their availability.'
          : `${assistantPayload.reply}\n\n_Note: only ${appliedCount} of ${operations.length} cart changes were applied. Please review your cart._`
      }

      if (appliedCount && operations.some(op => op.type === 'prepare_quote')) {
        assistantPayload.actions = [{ label: 'Review and submit quote', link: '/cart?assistant_quote=1' }]
      }
    }

    if (assistantPayload?.chat_session?.id) {
      activeChatSessionId.value = assistantPayload.chat_session.id
    }

    // During human handoff, the backend intentionally returns no AI reply.
    if (!assistantPayload?.wait_for_human && assistantPayload?.source !== 'human_handoff_waiting') {
      chatMessages.value.push({
        id: `assistant-${Date.now()}`,
        role: 'assistant',
        text: assistantPayload.reply || 'I could not generate a response right now.',
        createdAt: new Date().toISOString(),
        actions: assistantPayload.actions || [],
        productSuggestions: normalizeProductSuggestions(assistantPayload.product_suggestions || []),
        degraded: !!assistantPayload.degraded,
      })
      await scrollChatToBottom(true)
    }

    if (assistantPayload?.degraded) {
      toastStore.addToast('Assistant is in degraded mode. Response may be limited.', 'warning')
    }

    // 4. Update the session sidebar preview in-place — no full reload needed.
    const sessionId = activeChatSessionId.value
    if (sessionId) {
      updateSessionPreviewInstantly(
        sessionId,
        assistantPayload.reply || outgoing,
        assistantPayload.reply ? 'assistant' : 'user',
        assistantPayload.chat_session?.title || null
      )
    }

    // 5. Reconcile temp IDs with real DB IDs (no visible re-render).
    sendingChat.value = false
    await refreshChatMessages()
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
    await scrollChatToBottom()
  } finally {
    sendingChat.value = false
    chatRequestInFlight.value = false
    // 6. Resume polling — new messages (e.g. admin replies) will appear automatically.
    startMessagePolling()
  }
}

onMounted(async () => {
  clearLegacyPersistentChatCache()
  document.addEventListener('visibilitychange', handleDocumentVisibilityChange)
  document.addEventListener('click', handleDocumentClick)
  previousBodyOverflow = document.body.style.overflow || ''
  previousHtmlOverflow = document.documentElement.style.overflow || ''
  document.body.style.overflow = 'hidden'
  document.documentElement.style.overflow = 'hidden'

  const cachedSessions = readCachedJson(getChatCacheKey('sessions'), [])
  if (Array.isArray(cachedSessions) && cachedSessions.length > 0) {
    chatSessions.value = cachedSessions
    loadingSessions.value = false
    // Show cached data immediately, select first session from cache
    if (cachedSessions.length) {
      const firstId = cachedSessions[0].id
      const cachedMsgs = getCachedSessionMessages(firstId)
      if (cachedMsgs.length) {
        activeChatSessionId.value = firstId
        chatMessages.value = cachedMsgs
      }
    }
  }
  ensureChatWelcome()
  // Load pricing + fresh sessions in parallel
  await Promise.all([loadPricingSettings(), fetchChatSessions()])
  loadingSessions.value = false
  if (chatSessions.value.length) {
    await selectChatSession(chatSessions.value[0].id)
  }
  await scrollChatToBottom()
})

onUnmounted(() => {
  stopMessagePolling()
  document.removeEventListener('visibilitychange', handleDocumentVisibilityChange)
  document.removeEventListener('click', handleDocumentClick)
  document.body.style.overflow = previousBodyOverflow
  document.documentElement.style.overflow = previousHtmlOverflow
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
