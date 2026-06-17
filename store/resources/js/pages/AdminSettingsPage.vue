<template>
  <AdminLayout>
    <template #title>Settings</template>

    <!-- Tabs -->
    <div class="rounded-xl border-0 shadow-lg bg-white mb-6 overflow-hidden sticky top-0 z-10">
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px overflow-x-auto">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="switchTab(tab.id)"
            :class="[
              'px-6 py-4 text-sm font-semibold border-b-2 transition whitespace-nowrap',
              activeTab === tab.id
                ? 'border-[#2F5597] text-[#2F5597] bg-[#2F5597]/10'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-400 hover:bg-gray-50'
            ]"
          >
            <i :class="['fas', tab.icon, 'mr-2']"></i>
            {{ tab.label }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Profile Settings -->
    <div v-if="activeTab === 'profile'" class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Profile Settings</h3>
        <p class="text-sm text-gray-500 mt-1">Update your personal information</p>
      </div>
      
      <div class="p-6 space-y-6">
        <div class="flex items-center gap-6">
          <div class="w-24 h-24 rounded-full flex items-center justify-center text-white text-3xl font-bold" style="background: linear-gradient(135deg, #2F5597, #1e3a6b)">
            {{ getInitials(profile.name) }}
          </div>
          <div>
            <h4 class="font-semibold text-gray-900">{{ profile.name }}</h4>
            <p class="text-sm text-gray-500">{{ profile.email }}</p>
            <p class="text-xs text-gray-400 mt-1">Administrator</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input
              v-model="profile.name"
              type="text"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input
              v-model="profile.email"
              type="email"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
          <h4 class="font-semibold text-gray-900 mb-4">Change Password</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
              <input
                v-model="passwords.current"
                type="password"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
              <input
                v-model="passwords.new"
                type="password"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
          <button
            @click="saveProfile"
            :disabled="isSaving"
            class="px-6 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition disabled:opacity-50"
          >
            <i class="fas fa-save mr-2"></i>Save Changes
          </button>
        </div>
      </div>
    </div>

    <!-- API Configuration -->
    <div v-if="activeTab === 'api'" class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Integrations Configuration</h3>
        <p class="text-sm text-gray-500 mt-1">Manage TD SYNNEX XML mode and QuickBooks payment settings</p>
      </div>
      
      <div class="p-6 space-y-6">
        <div class="bg-[#2F5597]/10 border border-[#2F5597]/20 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-[#2F5597] mt-1"></i>
            <div class="text-sm text-[#2F5597]">
              <p class="font-medium">Integration Settings</p>
              <p class="mt-1 text-[#2F5597]">TD SYNNEX uses XML PriceAvailability for pricing and XML PO submission for confirmed orders.</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
          <div class="border border-gray-200 rounded-lg bg-gray-50 p-4">
            <h4 class="font-semibold text-gray-900 mb-4">TD SYNNEX XML</h4>
            <div class="grid grid-cols-1 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">XML Environment</label>
            <select
              v-model="apiConfig.environment"
              class="w-full px-4 py-2 border border-gray-200 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            >
              <option value="sandbox" class="bg-white">Sandbox (Testing)</option>
              <option value="production" class="bg-white">Production</option>
            </select>
          </div>
            </div>
          </div>

          <div class="border border-gray-200 rounded-lg bg-gray-50 p-4">
            <h4 class="font-semibold text-gray-900 mb-4">QuickBooks</h4>
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QuickBooks Client ID</label>
                <input
                  v-model="apiConfig.quickbooks_client_id"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QuickBooks Client Secret</label>
                <div class="relative">
                  <input
                    v-model="apiConfig.quickbooks_client_secret"
                    :type="showQuickBooksSecret ? 'text' : 'password'"
                    class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] pr-12"
                  />
                  <button
                    @click="showQuickBooksSecret = !showQuickBooksSecret"
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                  >
                    <i :class="['fas', showQuickBooksSecret ? 'fa-eye-slash' : 'fa-eye']"></i>
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QuickBooks Company ID</label>
                <input
                  v-model="apiConfig.quickbooks_company_id"
                  type="text"
                  class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QuickBooks Payment URL Template</label>
                <input
                  v-model="apiConfig.quickbooks_payment_url_template"
                  type="url"
                  class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                />
                <p class="text-xs text-gray-500 mt-1">Supports placeholders like {invoice_number}, {amount}, {success_url}, {cancel_url}, {quickbooks_company_id}.</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QuickBooks Bulk Payment URL Template</label>
                <input
                  v-model="apiConfig.quickbooks_bulk_payment_url_template"
                  type="url"
                  class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                />
                <p class="text-xs text-gray-500 mt-1">Used when paying multiple invoices directly. If blank, users can combine invoices first and pay the combined invoice.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-between items-center border-t border-gray-200 pt-6">
          <button
            @click="testApiConnection"
            class="px-4 py-2 border border-[#2F5597]/30 text-[#2F5597] bg-[#2F5597]/10 rounded-lg hover:bg-[#2F5597]/20 transition"
          >
            <i class="fas fa-plug mr-2"></i>Test Connection
          </button>
          <button
            @click="saveApiConfig"
            :disabled="isSaving"
            class="px-6 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition disabled:opacity-50"
          >
            <i class="fas fa-save mr-2"></i>Save Configuration
          </button>
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'price_sync'" class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Price Availability Sync</h3>
        <p class="text-sm text-gray-500 mt-1">Set when TD SYNNEX price &amp; availability refresh runs automatically. The sync always covers all products.</p>
      </div>

      <div class="p-6 space-y-6">
        <!-- Schedule settings -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Sync Time</label>
            <input
              v-model="priceSyncSettings.time"
              type="time"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
            <select
              v-model="priceSyncSettings.timezone"
              class="w-full px-4 py-2 border border-gray-200 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            >
              <option value="Africa/Nairobi" class="bg-white">Kenya Time (Africa/Nairobi)</option>
              <option value="America/Chicago" class="bg-white">Central Time (America/Chicago)</option>
              <option value="America/New_York" class="bg-white">Eastern Time (America/New_York)</option>
              <option value="UTC" class="bg-white">UTC</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Email</label>
            <input
              v-model="priceSyncSettings.email"
              type="email"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
        </div>

        <div class="rounded-lg border border-[#2F5597]/20 bg-[#2F5597]/10 p-4">
          <p class="text-sm font-semibold text-[#2F5597]">Next Scheduled Run</p>
          <p class="text-sm text-[#2F5597] mt-1">{{ priceSyncSettings.next_run_local || 'Save settings to calculate' }}</p>
          <p class="text-xs text-[#2F5597]/70 mt-1">Runs automatically at the saved time — all products, no configuration needed.</p>
          <p v-if="priceSyncSettings.last_triggered_date" class="text-xs text-[#2F5597]/70 mt-1">Last auto-triggered: {{ priceSyncSettings.last_triggered_date }}</p>
        </div>

        <div
          v-if="priceSyncSettings.scheduler_health && priceSyncSettings.scheduler_health.message"
          class="rounded-lg border p-4"
          :class="{
            'border-emerald-300 bg-emerald-50': priceSyncSettings.scheduler_health.status === 'ok',
            'border-amber-300 bg-amber-50': priceSyncSettings.scheduler_health.status !== 'ok',
          }"
        >
          <p
            class="text-sm font-semibold"
            :class="{
              'text-emerald-700': priceSyncSettings.scheduler_health.status === 'ok',
              'text-amber-700': priceSyncSettings.scheduler_health.status !== 'ok',
            }"
          >
            Deployment Check
          </p>
          <p
            class="text-sm mt-1"
            :class="{
              'text-emerald-700': priceSyncSettings.scheduler_health.status === 'ok',
              'text-amber-700': priceSyncSettings.scheduler_health.status !== 'ok',
            }"
          >
            {{ priceSyncSettings.scheduler_health.message }}
          </p>
          <ul v-if="Array.isArray(priceSyncSettings.scheduler_health.issues) && priceSyncSettings.scheduler_health.issues.length" class="mt-2 space-y-1">
            <li v-for="issue in priceSyncSettings.scheduler_health.issues" :key="issue" class="text-xs text-amber-700">
              - {{ issue }}
            </li>
          </ul>
          <p class="text-xs text-[#2F5597]/70 mt-2">
            Fallback: {{ priceSyncSettings.fallback_enabled ? 'enabled' : 'disabled' }} | Queue: {{ priceSyncSettings.queue_connection || 'unknown' }}
          </p>
        </div>

        <!-- Live Sync Status Panel -->
        <div v-if="syncState.status !== 'idle'" class="rounded-lg border p-4 space-y-2"
          :class="{
            'border-amber-300 bg-amber-50': syncState.status === 'running',
            'border-emerald-300 bg-emerald-50': syncState.status === 'completed',
            'border-orange-300 bg-orange-50': syncState.status === 'completed_with_errors',
            'border-rose-300 bg-rose-50': syncState.status === 'failed',
          }"
        >
          <div class="flex items-center justify-between">
            <p class="text-sm font-semibold"
              :class="{
                'text-amber-700': syncState.status === 'running',
                'text-emerald-700': syncState.status === 'completed',
                'text-orange-700': syncState.status === 'completed_with_errors',
                'text-rose-700': syncState.status === 'failed',
              }"
            >
              <i :class="{
                'fas fa-spinner fa-spin mr-2': syncState.status === 'running',
                'fas fa-check-circle mr-2': syncState.status === 'completed',
                'fas fa-exclamation-triangle mr-2': syncState.status === 'completed_with_errors',
                'fas fa-times-circle mr-2': syncState.status === 'failed',
              }"></i>
              {{ syncState.status === 'running' ? 'Sync Running...' : syncState.status === 'completed' ? 'Last Sync Completed' : syncState.status === 'completed_with_errors' ? 'Completed with Skipped Batches' : 'Last Sync Failed' }}
            </p>
            <span class="text-xs text-gray-500">{{ syncState.updated_at ? new Date(syncState.updated_at).toLocaleString() : '' }}</span>
          </div>
          <p class="text-sm" :class="{
            'text-amber-700': syncState.status === 'running',
            'text-emerald-700': syncState.status === 'completed',
            'text-orange-700': syncState.status === 'completed_with_errors',
            'text-rose-700': syncState.status === 'failed',
          }">
            {{ syncState.message }}
          </p>
          <pre v-if="syncState.output" class="text-xs bg-white/70 rounded p-3 whitespace-pre-wrap font-mono max-h-40 overflow-y-auto border border-gray-100">{{ syncState.output }}</pre>
        </div>

        <div class="flex flex-col md:flex-row justify-end gap-3 border-t border-gray-200 pt-6">
          <button
            @click="runPriceSyncNow"
            :disabled="syncState.status === 'running'"
            class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition disabled:opacity-50"
          >
            <i :class="syncState.status === 'running' ? 'fas fa-spinner fa-spin' : 'fas fa-play'" class="mr-2"></i>
            {{ syncState.status === 'running' ? 'Sync Running...' : 'Run Now (All Products)' }}
          </button>
          <button
            @click="savePriceSyncSettings"
            :disabled="isSaving"
            class="px-6 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition disabled:opacity-50"
          >
            <i class="fas fa-save mr-2"></i>Save Schedule
          </button>
        </div>
      </div>
    </div>

    <!-- Email Settings -->
    <div v-if="activeTab === 'email'" class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Email & Notifications</h3>
        <p class="text-sm text-gray-500 mt-1">Configure email notifications and SMTP settings</p>
      </div>
      
      <div class="p-6 space-y-6">
        <div>
          <h4 class="font-semibold text-gray-900 mb-4">Email Notifications</h4>
          <div class="space-y-3">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="emailSettings.new_orders"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 bg-gray-50 focus:ring-[#2F5597]"
                style="accent-color: #2F5597"
              />
              <div>
                <p class="font-medium text-gray-900">New Orders</p>
                <p class="text-sm text-gray-500">Receive notifications when new orders are placed</p>
              </div>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="emailSettings.new_quotes"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 bg-gray-50 focus:ring-[#2F5597]"
                style="accent-color: #2F5597"
              />
              <div>
                <p class="font-medium text-gray-900">New Quote Requests</p>
                <p class="text-sm text-gray-500">Get notified when customers request quotes</p>
              </div>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="emailSettings.low_stock"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 bg-gray-50 focus:ring-[#2F5597]"
                style="accent-color: #2F5597"
              />
              <div>
                <p class="font-medium text-gray-900">Low Stock Alerts</p>
                <p class="text-sm text-gray-500">Alerts when products are running low</p>
              </div>
            </label>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
          <h4 class="font-semibold text-gray-900 mb-4">SMTP Configuration</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
              <input
                v-model="emailSettings.smtp_host"
                type="text"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
              <input
                v-model="emailSettings.smtp_port"
                type="text"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
              <input
                v-model="emailSettings.smtp_username"
                type="text"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
              <input
                v-model="emailSettings.smtp_password"
                type="password"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
          <button
            @click="saveEmailSettings"
            :disabled="isSaving"
            class="px-6 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition disabled:opacity-50"
          >
            <i class="fas fa-save mr-2"></i>Save Settings
          </button>
        </div>
      </div>
    </div>

    <!-- System Settings -->
    <div v-if="activeTab === 'system'" class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">System Settings</h3>
        <p class="text-sm text-gray-500 mt-1">General system configuration</p>
      </div>
      
      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
            <input
              v-model="systemSettings.company_name"
              type="text"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Support Email</label>
            <input
              v-model="systemSettings.support_email"
              type="email"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
            <select
              v-model="systemSettings.currency"
              class="w-full px-4 py-2 border border-gray-200 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            >
              <option value="USD" class="bg-white">USD ($)</option>
              <option value="EUR" class="bg-white">EUR (€)</option>
              <option value="GBP" class="bg-white">GBP (£)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Currency Rate (vs USD)</label>
            <input
              v-model.number="systemSettings.currency_rate"
              type="number"
              min="0.0001"
              step="0.0001"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
            <p class="text-xs text-gray-500 mt-1">Example: set 4.44 to convert 1 USD to 4.44 in selected currency.</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
            <select
              v-model="systemSettings.timezone"
              class="w-full px-4 py-2 border border-gray-200 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            >
              <option value="America/New_York" class="bg-white">Eastern Time (ET)</option>
              <option value="America/Chicago" class="bg-white">Central Time (CT)</option>
              <option value="America/Denver" class="bg-white">Mountain Time (MT)</option>
              <option value="America/Los_Angeles" class="bg-white">Pacific Time (PT)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Default Tax Rate (%)</label>
            <input
              v-model.number="systemSettings.tax_rate_percent"
              type="number"
              min="0"
              max="100"
              step="0.01"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
            <p class="text-xs text-gray-500 mt-1">Applied when building invoice tax if quote/order tax is not explicitly provided.</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Default Profit Per Item (%)</label>
            <input
              v-model.number="systemSettings.profit_rate_percent"
              type="number"
              min="0"
              max="500"
              step="0.01"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
            <p class="text-xs text-gray-500 mt-1">Markup used to compute invoice subtotal and per-item unit prices.</p>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
          <h4 class="font-semibold text-gray-900 mb-4">Maintenance Mode</h4>
          <label class="flex items-center gap-3 cursor-pointer">
            <input
              v-model="systemSettings.maintenance_mode"
              type="checkbox"
              class="w-5 h-5 rounded border-gray-300 bg-gray-50 focus:ring-[#2F5597]"
              style="accent-color: #2F5597"
            />
            <div>
              <p class="font-medium text-gray-900">Enable Maintenance Mode</p>
              <p class="text-sm text-gray-500">Site will be unavailable to customers during maintenance</p>
            </div>
          </label>
        </div>

        <div class="border-t border-gray-200 pt-6">
          <h4 class="font-semibold text-gray-900 mb-1">Catalog Visibility</h4>
          <p class="text-sm text-gray-500 mb-4">Control which products are shown to customers in the storefront.</p>

          <div class="space-y-3 mb-6">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="systemSettings.catalog_show_out_of_stock"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 bg-gray-50 focus:ring-[#2F5597]"
                style="accent-color: #2F5597"
              />
              <div>
                <p class="font-medium text-gray-900">Show Out-of-Stock Products</p>
                <p class="text-sm text-gray-500">When enabled, products marked unavailable (is_available = false) will appear in the catalog</p>
              </div>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="systemSettings.catalog_show_discontinued"
                type="checkbox"
                class="w-5 h-5 rounded border-gray-300 bg-gray-50 focus:ring-[#2F5597]"
                style="accent-color: #2F5597"
              />
              <div>
                <p class="font-medium text-gray-900">Show Discontinued Products</p>
                <p class="text-sm text-gray-500">When enabled, products flagged as discontinued will also appear</p>
              </div>
            </label>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Price ($)</label>
              <input
                v-model.number="systemSettings.catalog_min_price"
                type="number"
                min="0"
                step="1"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
              <p class="text-xs text-gray-500 mt-1">Products below this price are hidden. Set to 0 to disable.</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Price ($)</label>
              <input
                v-model.number="systemSettings.catalog_max_price"
                type="number"
                min="0"
                step="1"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              />
              <p class="text-xs text-gray-500 mt-1">Products above this price are hidden. Set to 0 to disable.</p>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
          <button
            @click="saveSystemSettings"
            :disabled="isSaving"
            class="px-6 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition disabled:opacity-50"
          >
            <i class="fas fa-save mr-2"></i>Save Settings
          </button>
        </div>
      </div>
    </div>

    <!-- Catalog Operations -->
    <div v-if="activeTab === 'catalog'" class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">TD SYNNEX Catalog Operations</h3>
        <p class="text-sm text-gray-500 mt-1">Monitor catalog freshness and run sync/image actions from the admin UI.</p>
      </div>

      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-xs uppercase tracking-widest text-gray-500">Product Source</p>
            <p class="mt-2 text-lg font-semibold text-gray-900">{{ catalogOperations.products_source || 'unknown' }}</p>
            <p class="mt-1 text-sm" :class="catalogOperations.db_cache_exists ? 'text-emerald-600' : 'text-amber-600'">
              {{ catalogOperations.db_cache_exists ? 'Serving from local DB cache' : 'DB cache missing' }}
            </p>
          </div>

          <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-xs uppercase tracking-widest text-gray-500">Catalog Size</p>
            <p class="mt-2 text-lg font-semibold text-gray-900">{{ formatNumber(catalogOperations.total_products) }} products</p>
            <p class="mt-1 text-sm text-gray-500">Images: {{ formatNumber(catalogOperations.products_with_images) }}</p>
          </div>

          <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <p class="text-xs uppercase tracking-widest text-gray-500">Local Images</p>
            <p class="mt-2 text-lg font-semibold text-gray-900">{{ formatNumber(catalogOperations.products_with_local_images) }}</p>
            <p class="mt-1 text-sm text-gray-500">Pending queue jobs: {{ catalogOperations.pending_products_sync_jobs ?? 'n/a' }}</p>
            <p class="mt-1 text-sm text-rose-600">Failed queue jobs: {{ catalogOperations.failed_products_sync_jobs ?? 0 }}</p>
          </div>
        </div>

        <div class="rounded-lg border border-[#2F5597]/20 bg-[#2F5597]/10 p-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
          <div>
            <p class="text-sm font-semibold text-[#2F5597]">Last catalog sync</p>
            <p class="text-sm text-[#2F5597]">{{ formatDateTime(catalogOperations.last_catalog_sync_at) }}</p>
            <p class="text-xs mt-1" :class="{
              'text-amber-700': catalogOperations.catalog_operation?.status === 'queued',
              'text-[#2F5597]': catalogOperations.catalog_operation?.status === 'running',
              'text-emerald-700': catalogOperations.catalog_operation?.status === 'completed',
              'text-rose-600': catalogOperations.catalog_operation?.status === 'failed',
              'text-gray-500': !catalogOperations.catalog_operation?.status || catalogOperations.catalog_operation?.status === 'idle'
            }">
              Operation: {{ catalogOperations.catalog_operation?.status || 'idle' }}
              <span v-if="catalogOperations.catalog_operation?.message">- {{ catalogOperations.catalog_operation?.message }}</span>
            </p>
          </div>
          <button
            @click="refreshCatalogOperations"
            :disabled="catalogActionLoading"
            class="px-4 py-2 rounded-lg border border-[#2F5597]/30 text-[#2F5597] hover:bg-[#2F5597]/10 transition disabled:opacity-50"
          >
            <i class="fas fa-rotate mr-2"></i>Refresh Status
          </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
          <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <h4 class="text-gray-900 font-semibold">Sync Catalog Now</h4>
            <p class="mt-2 text-sm text-gray-500">Pull the latest TD SYNNEX catalog into the local products table now.</p>
            <button
              @click="runCatalogOperation('sync_catalog')"
              :disabled="catalogActionLoading"
              class="mt-4 w-full px-4 py-2 rounded-lg bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium transition disabled:opacity-50"
            >
              <i class="fas fa-database mr-2"></i>Run Catalog Sync
            </button>
          </div>

          <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <h4 class="text-gray-900 font-semibold">Enrich Missing Images</h4>
            <p class="mt-2 text-sm text-gray-500">Fetch and save missing product images for a small admin-safe batch.</p>
            <button
              @click="runCatalogOperation('enrich_images')"
              :disabled="catalogActionLoading"
              class="mt-4 w-full px-4 py-2 rounded-lg bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-400 hover:to-indigo-500 text-white font-medium transition disabled:opacity-50"
            >
              <i class="fas fa-image mr-2"></i>Enrich Images
            </button>
          </div>

          <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <h4 class="text-gray-900 font-semibold">Download Images Local</h4>
            <p class="mt-2 text-sm text-gray-500">Download current external image URLs into the project folder for local serving.</p>
            <button
              @click="runCatalogOperation('download_images')"
              :disabled="catalogActionLoading"
              class="mt-4 w-full px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-medium transition disabled:opacity-50"
            >
              <i class="fas fa-download mr-2"></i>Download Images
            </button>
          </div>

          <div class="rounded-lg border border-purple-200 bg-purple-50 p-5">
            <h4 class="text-gray-900 font-semibold">Sync Manual Images</h4>
            <p class="mt-2 text-sm text-gray-500">
              After uploading image files to <code class="bg-purple-100 px-1 rounded text-xs">public/images/products/</code> named as
              <code class="bg-purple-100 px-1 rounded text-xs">{tdsynnex_product_id}.jpg</code>, click here to link them to products in the database.
            </p>
            <button
              @click="runCatalogOperation('sync_manual_images')"
              :disabled="catalogActionLoading"
              class="mt-4 w-full px-4 py-2 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-400 hover:to-pink-500 text-white font-medium transition disabled:opacity-50"
            >
              <i class="fas fa-link mr-2"></i>Sync Manual Images
            </button>
          </div>

          <div class="rounded-lg border border-amber-200 bg-amber-50 p-5">
            <h4 class="text-gray-900 font-semibold">Re-index Products</h4>
            <p class="mt-2 text-sm text-gray-500">Backfill <code class="bg-amber-100 px-1 rounded text-xs">category_segment</code> and <code class="bg-amber-100 px-1 rounded text-xs">is_hardware</code> for all rows, then clear all browse caches for instant fast loads.</p>
            <button
              @click="runCatalogOperation('reindex_products')"
              :disabled="catalogActionLoading"
              class="mt-4 w-full px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-medium transition disabled:opacity-50"
            >
              <i class="fas fa-bolt mr-2"></i>Re-index &amp; Clear Cache
            </button>
          </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <h4 class="text-gray-900 font-semibold">Last Command Output</h4>
              <span v-if="catalogActionLoading" class="inline-flex items-center gap-1.5 text-xs text-[#2F5597] animate-pulse">
                <i class="fas fa-circle-notch fa-spin"></i> Running…
              </span>
            </div>
            <button
              v-if="catalogCommandOutput"
              @click="catalogCommandOutput = ''"
              class="text-xs text-gray-500 hover:text-gray-700 transition"
            ><i class="fas fa-trash-can mr-1"></i>Clear</button>
          </div>
          <pre class="mt-3 min-h-[220px] max-h-[600px] w-full overflow-auto whitespace-pre-wrap break-all rounded-lg bg-black/50 p-4 text-xs leading-relaxed text-gray-700 font-mono">{{ catalogCommandOutput || 'No command has been run from this page yet.' }}</pre>
        </div>
      </div>
    </div>

    <!-- Admin Users Management (Super Admin Only) -->
    <div v-if="activeTab === 'admins'" class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Admin Users</h3>
          <p class="text-sm text-gray-500 mt-1">Manage administrator accounts</p>
        </div>
        <button
          @click="showAddAdminModal = true"
          class="px-4 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition"
        >
          <i class="fas fa-user-plus mr-2"></i>Add Admin
        </button>
      </div>
      
      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wide">Name</th>
                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wide">Email</th>
                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wide">Role</th>
                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wide">Created</th>
                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wide">Login Status</th>
                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="admin in adminUsers" :key="admin.id" class="border-b border-gray-200 hover:bg-gray-50 transition">
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" style="background: linear-gradient(135deg, #2F5597, #1e3a6b)">
                      {{ getInitials(admin.name) }}
                    </div>
                    <span class="font-medium text-gray-900">{{ admin.name }}</span>
                  </div>
                </td>
                <td class="py-3 px-4 text-gray-500">{{ admin.email }}</td>
                <td class="py-3 px-4">
                  <span :class="[
                    'px-2 py-1 rounded-full text-xs font-semibold',
                    admin.role === 'super_admin' ? 'bg-violet-500/20 text-violet-700' : 'bg-[#2F5597]/10 text-[#2F5597]'
                  ]">
                    {{ admin.role === 'super_admin' ? 'Super Admin' : 'Admin' }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <span :class="[
                    'px-2 py-1 rounded-full text-xs font-semibold',
                    admin.status === 'active' ? 'bg-emerald-500/20 text-emerald-600' : 'bg-rose-500/20 text-rose-600'
                  ]">
                    {{ admin.status }}
                  </span>
                </td>
                <td class="py-3 px-4 text-gray-500 text-sm">{{ new Date(admin.created_at).toLocaleDateString() }}</td>
                <td class="py-3 px-4">
                  <template v-if="admin.force_password_change">
                    <span v-if="isTempExpired(admin.temp_password_expires_at)" class="px-2 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-600">
                      <i class="fas fa-clock mr-1"></i>Expired
                    </span>
                    <span v-else class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-600">
                      <i class="fas fa-hourglass-half mr-1"></i>Awaiting login · expires {{ tempExpiresIn(admin.temp_password_expires_at) }}
                    </span>
                  </template>
                  <span v-else class="px-2 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-600">
                    <i class="fas fa-check mr-1"></i>Active
                  </span>
                </td>
                <td class="py-3 px-4">
                  <div class="flex gap-2 flex-wrap">
                    <button
                      v-if="admin.force_password_change"
                      @click="resendCredentials(admin.id)"
                      class="px-3 py-1 text-xs border border-[#2F5597]/40 text-[#2F5597] bg-[#2F5597]/10 rounded hover:bg-[#2F5597]/20 transition"
                    >
                      <i class="fas fa-paper-plane mr-1"></i>Resend
                    </button>
                    <button
                      v-if="admin.status === 'active'"
                      @click="suspendAdmin(admin.id)"
                      class="px-3 py-1 text-xs border border-amber-500/40 text-amber-600 bg-amber-500/10 rounded hover:bg-amber-500/20 transition"
                    >
                      <i class="fas fa-pause mr-1"></i>Suspend
                    </button>
                    <button
                      v-else
                      @click="activateAdmin(admin.id)"
                      class="px-3 py-1 text-xs border border-emerald-500/40 text-emerald-600 bg-emerald-500/10 rounded hover:bg-emerald-500/20 transition"
                    >
                      <i class="fas fa-play mr-1"></i>Activate
                    </button>
                    <button
                      v-if="admin.id !== currentUserId"
                      @click="deleteAdmin(admin.id)"
                      class="px-3 py-1 text-xs border border-rose-500/40 text-rose-600 bg-rose-500/10 rounded hover:bg-rose-500/20 transition"
                    >
                      <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Activity Logs (combined) -->
    <div v-if="activeTab === 'activity_logs'" class="rounded-xl border-0 shadow-lg bg-white overflow-hidden">
      <div class="p-6 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">Activity Logs</h3>
        <p class="text-sm text-gray-500 mt-1">Admin actions and user activity across the platform</p>
      </div>

      <!-- Nested tabs -->
      <div class="border-b border-gray-200 px-6">
        <nav class="flex gap-1 -mb-px">
          <button
            @click="activeLogTab = 'admin'; adminLogsPage = 1; fetchAdminLogs()"
            :class="[
              'px-4 py-3 text-sm font-semibold border-b-2 transition whitespace-nowrap',
              activeLogTab === 'admin'
                ? 'border-[#2F5597] text-[#2F5597]'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <i class="fas fa-shield-halved mr-2"></i>Admin Logs
          </button>
          <button
            @click="activeLogTab = 'user'; userLogsPage = 1; fetchUserLogs()"
            :class="[
              'px-4 py-3 text-sm font-semibold border-b-2 transition whitespace-nowrap',
              activeLogTab === 'user'
                ? 'border-[#2F5597] text-[#2F5597]'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <i class="fas fa-clock-rotate-left mr-2"></i>User Activity
          </button>
        </nav>
      </div>

      <!-- ── Admin Logs pane ── -->
      <template v-if="activeLogTab === 'admin'">
        <div class="px-6 py-3 border-b border-gray-200 flex flex-wrap items-center gap-3">
          <input
            v-model="adminLogsSearch"
            @input="adminLogsPage = 1; fetchAdminLogs()"
            type="text"
            placeholder="Search admin logs…"
            class="px-3 py-2 text-sm border border-gray-200 bg-gray-50 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] w-56"
          />
          <button
            @click="fetchAdminLogs"
            :disabled="adminLogsLoading"
            class="px-4 py-2 text-sm bg-[#2F5597] hover:bg-[#1e3a6b] text-white rounded-lg transition disabled:opacity-60 flex items-center gap-2"
          >
            <svg v-if="adminLogsLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <i v-else class="fas fa-sync-alt"></i>
            Refresh
          </button>
          <div class="ml-auto flex items-center gap-2">
            <template v-if="selectedAdminLogs.length > 0">
              <span class="text-sm font-medium text-[#2F5597]">{{ selectedAdminLogs.length }} selected</span>
              <button @click="bulkDeleteLogs('admin_selected')" class="px-3 py-1.5 text-xs bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition font-medium">
                <i class="fas fa-trash mr-1"></i>Delete Selected
              </button>
              <button @click="selectedAdminLogs = []" class="text-xs text-gray-500 hover:text-gray-700 transition">
                <i class="fas fa-times mr-1"></i>Deselect
              </button>
            </template>
            <button @click="bulkDeleteLogs('admin_all')" class="px-3 py-1.5 text-xs border border-rose-500/40 text-rose-600 bg-rose-500/10 hover:bg-rose-500/20 rounded-lg transition font-medium">
              <i class="fas fa-broom mr-1"></i>Clear All
            </button>
          </div>
        </div>

        <div v-if="adminLogsLoading" class="flex justify-center py-12">
          <svg class="animate-spin h-8 w-8 text-[#2F5597]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-left w-10">
                  <input type="checkbox" :checked="allAdminLogsSelected" @change="toggleAllAdminLogs" class="w-4 h-4 rounded border-gray-300" style="accent-color: #2F5597" />
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Time</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Admin</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Action</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Description</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="adminLogs.length === 0">
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                  <i class="fas fa-shield-halved text-4xl mb-3 block text-gray-400"></i>
                  <p>No admin activity logs found</p>
                </td>
              </tr>
              <tr v-for="log in adminLogs" :key="log.id" class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                  <input type="checkbox" :value="log.id" v-model="selectedAdminLogs" class="w-4 h-4 rounded border-gray-300" style="accent-color: #2F5597" />
                </td>
                <td class="px-6 py-3 text-xs text-gray-500 whitespace-nowrap">{{ formatLogTime(log.created_at) }}</td>
                <td class="px-6 py-3">
                  <div class="font-medium text-gray-900 text-sm">{{ log.user?.name || '—' }}</div>
                  <div class="text-xs text-gray-500">{{ log.user?.email }}</div>
                </td>
                <td class="px-6 py-3">
                  <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', logTypeBadge(log.type)]">{{ log.type }}</span>
                </td>
                <td class="px-6 py-3 text-sm font-mono text-gray-700">{{ log.action }}</td>
                <td class="px-6 py-3 text-sm text-gray-700">{{ log.description }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="text-sm text-gray-500">
            <p>Showing {{ adminLogs.length }} of {{ adminLogsTotal }} entries &nbsp;·&nbsp; Page {{ adminLogsPage }} of {{ adminLogsLastPage }}</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <button :disabled="adminLogsPage === 1" @click="adminLogsPage--; fetchAdminLogs()" class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#2F5597] transition disabled:opacity-50 disabled:cursor-not-allowed text-sm">Previous</button>
            <button v-for="page in adminLogsPageNumbers" :key="page" @click="adminLogsPage = page; fetchAdminLogs()"
              :class="['px-3 py-2 rounded-lg border text-sm font-semibold transition', page === adminLogsPage ? 'bg-gradient-to-r from-[#2F5597] to-[#1e3a6b] text-white border-[#2F5597]' : 'border-gray-200 text-gray-500 hover:border-[#2F5597]/50 hover:text-[#2F5597]']">{{ page }}</button>
            <button :disabled="adminLogsPage >= adminLogsLastPage" @click="adminLogsPage++; fetchAdminLogs()" class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#2F5597] transition disabled:opacity-50 disabled:cursor-not-allowed text-sm">Next</button>
          </div>
        </div>
      </template>

      <!-- ── User Activity pane ── -->
      <template v-if="activeLogTab === 'user'">
        <div class="px-6 py-3 border-b border-gray-200 flex flex-wrap items-center gap-3">
          <input
            v-model="userLogsSearch"
            @input="userLogsPage = 1; fetchUserLogs()"
            type="text"
            placeholder="Search user activity…"
            class="px-3 py-2 text-sm border border-gray-200 bg-gray-50 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] w-56"
          />
          <button
            @click="fetchUserLogs"
            :disabled="userLogsLoading"
            class="px-4 py-2 text-sm bg-[#2F5597] hover:bg-[#1e3a6b] text-white rounded-lg transition disabled:opacity-60 flex items-center gap-2"
          >
            <svg v-if="userLogsLoading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <i v-else class="fas fa-sync-alt"></i>
            Refresh
          </button>
          <div class="ml-auto flex items-center gap-2">
            <template v-if="selectedUserLogs.length > 0">
              <span class="text-sm font-medium text-[#2F5597]">{{ selectedUserLogs.length }} selected</span>
              <button @click="bulkDeleteLogs('user_selected')" class="px-3 py-1.5 text-xs bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition font-medium">
                <i class="fas fa-trash mr-1"></i>Delete Selected
              </button>
              <button @click="selectedUserLogs = []" class="text-xs text-gray-500 hover:text-gray-700 transition">
                <i class="fas fa-times mr-1"></i>Deselect
              </button>
            </template>
            <button @click="bulkDeleteLogs('user_all')" class="px-3 py-1.5 text-xs border border-rose-500/40 text-rose-600 bg-rose-500/10 hover:bg-rose-500/20 rounded-lg transition font-medium">
              <i class="fas fa-broom mr-1"></i>Clear All
            </button>
          </div>
        </div>

        <div v-if="userLogsLoading" class="flex justify-center py-12">
          <svg class="animate-spin h-8 w-8 text-[#2F5597]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-left w-10">
                  <input type="checkbox" :checked="allUserLogsSelected" @change="toggleAllUserLogs" class="w-4 h-4 rounded border-gray-300" style="accent-color: #2F5597" />
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Time</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">User</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Role</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Type</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Action</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Description</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="userLogs.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  <i class="fas fa-clock-rotate-left text-4xl mb-3 block text-gray-400"></i>
                  <p>No user activity logs found</p>
                </td>
              </tr>
              <tr v-for="log in userLogs" :key="log.id" class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                  <input type="checkbox" :value="log.id" v-model="selectedUserLogs" class="w-4 h-4 rounded border-gray-300" style="accent-color: #2F5597" />
                </td>
                <td class="px-6 py-3 text-xs text-gray-500 whitespace-nowrap">{{ formatLogTime(log.created_at) }}</td>
                <td class="px-6 py-3">
                  <div class="font-medium text-gray-900 text-sm">{{ log.user?.name || '—' }}</div>
                  <div class="text-xs text-gray-500">{{ log.user?.email }}</div>
                </td>
                <td class="px-6 py-3">
                  <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', log.user?.role === 'super_admin' ? 'bg-violet-500/10 text-violet-700' : log.user?.role === 'admin' ? 'bg-[#2F5597]/10 text-[#2F5597]' : 'bg-gray-100 text-gray-600']">{{ log.user?.role || 'user' }}</span>
                </td>
                <td class="px-6 py-3">
                  <span :class="['px-2 py-0.5 rounded-full text-xs font-semibold', logTypeBadge(log.type)]">{{ log.type }}</span>
                </td>
                <td class="px-6 py-3 text-sm font-mono text-gray-700">{{ log.action }}</td>
                <td class="px-6 py-3 text-sm text-gray-700">{{ log.description }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div class="text-sm text-gray-500">
            <p>Showing {{ userLogs.length }} of {{ userLogsTotal }} entries &nbsp;·&nbsp; Page {{ userLogsPage }} of {{ userLogsLastPage }}</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <button :disabled="userLogsPage === 1" @click="userLogsPage--; fetchUserLogs()" class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#2F5597] transition disabled:opacity-50 disabled:cursor-not-allowed text-sm">Previous</button>
            <button v-for="page in userLogsPageNumbers" :key="page" @click="userLogsPage = page; fetchUserLogs()"
              :class="['px-3 py-2 rounded-lg border text-sm font-semibold transition', page === userLogsPage ? 'bg-gradient-to-r from-[#2F5597] to-[#1e3a6b] text-white border-[#2F5597]' : 'border-gray-200 text-gray-500 hover:border-[#2F5597]/50 hover:text-[#2F5597]']">{{ page }}</button>
            <button :disabled="userLogsPage >= userLogsLastPage" @click="userLogsPage++; fetchUserLogs()" class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#2F5597] transition disabled:opacity-50 disabled:cursor-not-allowed text-sm">Next</button>
          </div>
        </div>
      </template>
    </div>

    <!-- Add Admin Modal -->
    <div v-if="showAddAdminModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="closeAddAdminModal">
      <div class="rounded-xl bg-white border-0 shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="p-6 border-b border-gray-200 text-white" style="background: linear-gradient(135deg, #2F5597, #1e3a6b)">
          <h3 class="text-lg font-semibold text-white">Add New Admin</h3>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input
              v-model="newAdmin.name"
              type="text"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input
              v-model="newAdmin.email"
              type="email"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input
              v-model="newAdmin.password"
              type="password"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
            <select
              v-model="newAdmin.role"
              class="w-full px-4 py-2 border border-gray-200 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
            >
              <option value="admin" class="bg-white">Admin</option>
              <option value="super_admin" class="bg-white">Super Admin</option>
            </select>
          </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
          <button
            @click="closeAddAdminModal"
            class="px-4 py-2 border border-gray-200 text-gray-500 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
          >
            Cancel
          </button>
          <button
            @click="createAdmin"
            class="px-4 py-2 bg-[#2F5597] hover:bg-[#1e3a6b] text-white font-medium rounded-lg transition"
          >
            <i class="fas fa-plus mr-2"></i>Create Admin
          </button>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const activeTab = ref('profile')
const activeLogTab = ref('admin')
const isSaving = ref(false)
const isRunningSync = ref(false)
const syncState = ref({ status: 'idle', message: '', output: '', started_at: null, updated_at: null, finished_at: null })
let syncPollTimer = null
const catalogActionLoading = ref(false)
const showQuickBooksSecret = ref(false)
const showAddAdminModal = ref(false)
const currentUserId = ref(null)

const tabs = [
  { id: 'profile', label: 'Profile', icon: 'fa-user' },
  { id: 'api', label: 'API Configuration', icon: 'fa-plug' },
  { id: 'catalog', label: 'Catalog Ops', icon: 'fa-boxes-stacked' },
  { id: 'price_sync', label: 'Price Sync', icon: 'fa-clock' },
  { id: 'email', label: 'Email & Notifications', icon: 'fa-envelope' },
  { id: 'system', label: 'System', icon: 'fa-cog' },
  { id: 'admins', label: 'Admin Users', icon: 'fa-users-cog' },
  { id: 'activity_logs', label: 'Activity Logs', icon: 'fa-list-check' }
]

const profile = ref({
  name: '',
  email: ''
})

const passwords = ref({
  current: '',
  new: ''
})

const apiConfig = ref({
  environment: 'sandbox',
  quickbooks_client_id: '',
  quickbooks_client_secret: '',
  quickbooks_company_id: '',
  quickbooks_payment_url_template: '',
  quickbooks_bulk_payment_url_template: ''
})

const emailSettings = ref({
  new_orders: true,
  new_quotes: true,
  low_stock: false,
  smtp_host: '',
  smtp_port: '587',
  smtp_username: '',
  smtp_password: ''
})

const priceSyncSettings = ref({
  time: '18:00',
  timezone: 'America/Chicago',
  email: 'malvine.owuor@armely.com',
  fallback_enabled: false,
  queue_connection: '',
  next_run_local: '',
  last_triggered_date: '',
  scheduler_health: {
    status: 'warning',
    message: '',
    issues: [],
  },
})

const systemSettings = ref({
  company_name: 'Armely Store',
  support_email: 'info@armely.com',
  currency: 'USD',
  currency_rate: 1,
  timezone: 'America/New_York',
  maintenance_mode: false,
  tax_rate_percent: 0,
  profit_rate_percent: 0,
  catalog_show_out_of_stock: false,
  catalog_show_discontinued: false,
  catalog_min_price: 100,
  catalog_max_price: 3000,
})

const catalogOperations = ref({
  products_source: '',
  db_cache_exists: false,
  total_products: 0,
  products_with_images: 0,
  products_with_local_images: 0,
  last_catalog_sync_at: null,
  pending_products_sync_jobs: null,
  failed_products_sync_jobs: null,
  catalog_operation: {
    action: null,
    status: 'idle',
    message: '',
    output: '',
    started_at: null,
    updated_at: null,
    finished_at: null,
  },
  priceavailability_enabled: false,
})

const catalogCommandOutput = ref('')
const catalogStatusPollId = ref(null)

const adminUsers = ref([])

// Activity logs
const adminLogs = ref([])
const userLogs = ref([])
const adminLogsLoading = ref(false)
const userLogsLoading = ref(false)
const adminLogsSearch = ref('')
const userLogsSearch = ref('')
const adminLogsTotal = ref(0)
const userLogsTotal = ref(0)
const adminLogsPage = ref(1)
const adminLogsLastPage = ref(1)
const selectedAdminLogs = ref([])
const userLogsPage = ref(1)
const userLogsLastPage = ref(1)
const selectedUserLogs = ref([])

const allAdminLogsSelected = computed(() =>
  adminLogs.value.length > 0 && adminLogs.value.every(l => selectedAdminLogs.value.includes(l.id))
)

const allUserLogsSelected = computed(() =>
  userLogs.value.length > 0 && userLogs.value.every(l => selectedUserLogs.value.includes(l.id))
)


const adminLogsPageNumbers = computed(() => {
  const total = adminLogsLastPage.value
  const current = adminLogsPage.value
  const half = 2
  let start = Math.max(1, current - half)
  let end = Math.min(total, start + 4)
  if (end - start < 4) start = Math.max(1, end - 4)
  const pages = []
  for (let p = start; p <= end; p++) pages.push(p)
  return pages
})

const userLogsPageNumbers = computed(() => {
  const total = userLogsLastPage.value
  const current = userLogsPage.value
  const half = 2
  let start = Math.max(1, current - half)
  let end = Math.min(total, start + 4)
  if (end - start < 4) start = Math.max(1, end - 4)
  const pages = []
  for (let p = start; p <= end; p++) pages.push(p)
  return pages
})

const newAdmin = ref({
  name: '',
  email: '',
  password: '',
  role: 'admin'
})

const getInitials = (name) => {
  if (!name) return 'A'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatDateTime = (date) => {
  if (!date) return 'Not available yet'

  const parsed = new Date(date)
  if (Number.isNaN(parsed.getTime())) return String(date)

  return parsed.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit'
  })
}

const formatNumber = (value) => {
  return Number(value || 0).toLocaleString('en-US')
}

const showToast = (message, type = 'success') => {
  const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500'
  const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'
  
  const toast = document.createElement('div')
  toast.className = `fixed top-4 right-4 ${bgColor} text-gray-900 px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity flex items-center gap-2`
  
  const icon = document.createElement('i')
  icon.className = `fas ${iconClass}`
  
  const span = document.createElement('span')
  span.textContent = message
  
  toast.appendChild(icon)
  toast.appendChild(span)
  document.body.appendChild(toast)
  
  setTimeout(() => {
    toast.style.opacity = '0'
    setTimeout(() => document.body.removeChild(toast), 300)
  }, 3000)
}

const fetchSettings = async () => {
  try {
    const response = await api.get('/admin/settings')
    if (response.data.success) {
      const data = response.data.data
      if (data.profile) profile.value = data.profile
      if (data.api_config) apiConfig.value = { ...apiConfig.value, ...data.api_config }
      if (data.email_settings) emailSettings.value = { ...emailSettings.value, ...data.email_settings }
      if (data.price_sync_settings) priceSyncSettings.value = { ...priceSyncSettings.value, ...data.price_sync_settings }
      if (data.system_settings) systemSettings.value = { ...systemSettings.value, ...data.system_settings }
      if (data.catalog_operations) {
        catalogOperations.value = { ...catalogOperations.value, ...data.catalog_operations }
        const op = catalogOperations.value.catalog_operation || {}
        if (op.output) {
          catalogCommandOutput.value = op.output
        }
        catalogActionLoading.value = ['queued', 'running'].includes(op.status)
      }
    }
  } catch (error) {
    console.error('Failed to fetch settings:', error)
  }
}

const refreshCatalogOperations = async () => {
  try {
    const response = await api.get('/admin/settings/catalog/status')
    if (response.data.success) {
      catalogOperations.value = { ...catalogOperations.value, ...response.data.data }
      const op = catalogOperations.value.catalog_operation || {}
      if (op.output) {
        catalogCommandOutput.value = op.output
      }
      catalogActionLoading.value = ['queued', 'running'].includes(op.status)
    }
  } catch (error) {
    console.error('Failed to fetch catalog operations status:', error)
    showToast(error.response?.data?.message || 'Failed to refresh catalog status', 'error')
  }
}

const startCatalogStatusPolling = () => {
  if (catalogStatusPollId.value) return

  catalogStatusPollId.value = setInterval(async () => {
    await refreshCatalogOperations()

    const status = catalogOperations.value?.catalog_operation?.status
    if (!['queued', 'running'].includes(status)) {
      stopCatalogStatusPolling()
    }
  }, 4000)
}

const stopCatalogStatusPolling = () => {
  if (!catalogStatusPollId.value) return
  clearInterval(catalogStatusPollId.value)
  catalogStatusPollId.value = null
}

const runCatalogOperation = async (action) => {
  catalogActionLoading.value = true
  try {
    const response = await api.post('/admin/settings/catalog/run', { action })
    if (response.data.success) {
      catalogCommandOutput.value = response.data.data?.output || response.data.message || ''
      if (response.data.data?.status) {
        catalogOperations.value = { ...catalogOperations.value, ...response.data.data.status }
      }
      startCatalogStatusPolling()
      showToast(response.data.message || 'Catalog operation queued', 'success')
    }
  } catch (error) {
    console.error('Failed to run catalog operation:', error)
    const message = error.response?.data?.message || 'Failed to run catalog operation'
    catalogCommandOutput.value = message
    stopCatalogStatusPolling()
    catalogActionLoading.value = false
    showToast(message, 'error')
  }
}

const saveProfile = async () => {
  isSaving.value = true
  try {
    const payload = { ...profile.value }
    if (passwords.value.current && passwords.value.new) {
      payload.current_password = passwords.value.current
      payload.new_password = passwords.value.new
    }

    const response = await api.post('/admin/settings/profile', payload)
    if (response.data.success) {
      showToast('Profile updated successfully!', 'success')
      passwords.value = { current: '', new: '' }
    }
  } catch (error) {
    console.error('Failed to save profile:', error)
    showToast(error.response?.data?.message || 'Failed to save profile', 'error')
  } finally {
    isSaving.value = false
  }
}

const saveApiConfig = async () => {
  isSaving.value = true
  try {
    const response = await api.post('/admin/settings/api', apiConfig.value)
    if (response.data.success) {
      if (response.data.data) {
        apiConfig.value = { ...apiConfig.value, ...response.data.data }
      }
      showToast('API configuration saved successfully!', 'success')
    }
  } catch (error) {
    console.error('Failed to save API config:', error)
    showToast(error.response?.data?.message || 'Failed to save API configuration', 'error')
  } finally {
    isSaving.value = false
  }
}

const testApiConnection = async () => {
  try {
    const response = await api.get('/admin/settings/api/test')
    if (response.data.success) {
      const qb = response.data?.data?.quickbooks
      if (qb && qb.configured === false) {
        const missing = Array.isArray(qb.missing) && qb.missing.length > 0
          ? ` Missing: ${qb.missing.join(', ')}`
          : ''
        const invalid = Array.isArray(qb.invalid) && qb.invalid.length > 0
          ? ` Invalid: ${qb.invalid.join(', ')}`
          : ''
        showToast(`${response.data.message}${missing}${invalid}`, 'error')
        return
      }

      showToast(response.data.message || 'API connection successful!', 'success')
    }
  } catch (error) {
    console.error('API connection failed:', error)
    showToast('API connection failed: ' + (error.response?.data?.message || error.message), 'error')
  }
}

const saveEmailSettings = async () => {
  isSaving.value = true
  try {
    const response = await api.post('/admin/settings/email', emailSettings.value)
    if (response.data.success) {
      if (response.data.data) {
        emailSettings.value = { ...emailSettings.value, ...response.data.data }
      }
      showToast('Email settings saved successfully!', 'success')
    }
  } catch (error) {
    console.error('Failed to save email settings:', error)
    showToast(error.response?.data?.message || 'Failed to save email settings', 'error')
  } finally {
    isSaving.value = false
  }
}

const fetchSyncState = async () => {
  try {
    const response = await api.get('/admin/settings/price-sync/state')
    if (response.data.success) {
      syncState.value = response.data.data
    }
  } catch { /* silent */ }
}

const startSyncPolling = () => {
  stopSyncPolling()
  syncPollTimer = setInterval(async () => {
    await fetchSyncState()
    if (!['running'].includes(syncState.value.status)) {
      stopSyncPolling()
    }
  }, 3000)
}

const stopSyncPolling = () => {
  if (syncPollTimer) {
    clearInterval(syncPollTimer)
    syncPollTimer = null
  }
}

const runPriceSyncNow = async () => {
  try {
    const response = await api.post('/admin/settings/price-sync/run-now', {})
    if (response.data.success) {
      syncState.value = {
        status: 'running',
        message: 'Starting — All products...',
        output: `Started at ${new Date().toLocaleString()}\nScope: All products`,
        started_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        finished_at: null
      }
      showToast(response.data.message || 'Price sync started in background.', 'success')
      startSyncPolling()
    } else {
      showToast(response.data.message || 'Price sync failed to start', 'error')
    }
  } catch (error) {
    console.error('Failed to run price sync:', error)
    const msg = error.response?.data?.message || 'Failed to start price sync'
    if (error.response?.status === 409) {
      // Already running — show state and start polling
      if (error.response?.data?.data) syncState.value = error.response.data.data
      startSyncPolling()
    }
    showToast(msg, error.response?.status === 409 ? 'info' : 'error')
  }
}

const savePriceSyncSettings = async () => {
  isSaving.value = true
  try {
    const { time, timezone, email } = priceSyncSettings.value
    const response = await api.post('/admin/settings/price-sync', { time, timezone, email })
    if (response.data.success) {
      if (response.data.data) {
        priceSyncSettings.value = { ...priceSyncSettings.value, ...response.data.data }
      }
      showToast('Price sync schedule saved successfully!', 'success')
    }
  } catch (error) {
    console.error('Failed to save price sync settings:', error)
    showToast(error.response?.data?.message || 'Failed to save price sync settings', 'error')
  } finally {
    isSaving.value = false
  }
}

const saveSystemSettings = async () => {
  isSaving.value = true
  try {
    const response = await api.post('/admin/settings/system', systemSettings.value)
    if (response.data.success) {
      if (response.data.data) {
        systemSettings.value = { ...systemSettings.value, ...response.data.data }
      }
      showToast('System settings saved successfully!', 'success')
    }
  } catch (error) {
    console.error('Failed to save system settings:', error)
    showToast(error.response?.data?.message || 'Failed to save system settings', 'error')
  } finally {
    isSaving.value = false
  }
}

// Admin Management Functions
const fetchAdminUsers = async () => {
  try {
    const response = await api.get('/admin/users')
    if (response.data.success) {
      adminUsers.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch admin users:', error)
  }
}

const closeAddAdminModal = () => {
  showAddAdminModal.value = false
  newAdmin.value = {
    name: '',
    email: '',
    password: '',
    role: 'admin'
  }
}

const createAdmin = async () => {
  if (!newAdmin.value.name || !newAdmin.value.email || !newAdmin.value.password) {
    showToast('Please fill in all required fields', 'error')
    return
  }

  if (newAdmin.value.password.length < 8) {
    showToast('Password must be at least 8 characters', 'error')
    return
  }

  isSaving.value = true
  try {
    const response = await api.post('/admin/users', newAdmin.value)
    if (response.data.success) {
      showToast('Admin user created successfully!', 'success')
      closeAddAdminModal()
      fetchAdminUsers()
    }
  } catch (error) {
    console.error('Failed to create admin:', error)
    showToast(error.response?.data?.message || 'Failed to create admin user', 'error')
  } finally {
    isSaving.value = false
  }
}

const suspendAdmin = async (adminId) => {
  if (!confirm('Are you sure you want to suspend this admin user?')) return

  try {
    const response = await api.post(`/admin/users/${adminId}/suspend`)
    if (response.data.success) {
      showToast('Admin user suspended', 'success')
      fetchAdminUsers()
    }
  } catch (error) {
    console.error('Failed to suspend admin:', error)
    showToast(error.response?.data?.message || 'Failed to suspend admin user', 'error')
  }
}

const activateAdmin = async (adminId) => {
  try {
    const response = await api.post(`/admin/users/${adminId}/activate`)
    if (response.data.success) {
      showToast('Admin user activated', 'success')
      fetchAdminUsers()
    }
  } catch (error) {
    console.error('Failed to activate admin:', error)
    showToast(error.response?.data?.message || 'Failed to activate admin user', 'error')
  }
}

const deleteAdmin = async (adminId) => {
  if (!confirm('Are you sure you want to delete this admin user? This action cannot be undone.')) return

  try {
    const response = await api.delete(`/admin/users/${adminId}`)
    if (response.data.success) {
      showToast('Admin user deleted', 'success')
      fetchAdminUsers()
    }
  } catch (error) {
    console.error('Failed to delete admin:', error)
    showToast(error.response?.data?.message || 'Failed to delete admin user', 'error')
  }
}

const isTempExpired = (expiresAt) => {
  if (!expiresAt) return false
  return new Date(expiresAt) < new Date()
}

const tempExpiresIn = (expiresAt) => {
  if (!expiresAt) return ''
  const ms = new Date(expiresAt) - Date.now()
  if (ms <= 0) return 'now'
  const hrs = Math.floor(ms / 3600000)
  const mins = Math.floor((ms % 3600000) / 60000)
  if (hrs > 0) return `in ${hrs}h ${mins}m`
  return `in ${mins}m`
}

const resendCredentials = async (adminId) => {
  if (!confirm('This will generate a new temporary password and email it to the admin. Continue?')) return
  try {
    const response = await api.post(`/admin/users/${adminId}/resend-credentials`)
    if (response.data.success) {
      showToast(response.data.message, 'success')
      fetchAdminUsers()
    }
  } catch (error) {
    showToast(error.response?.data?.message || 'Failed to resend credentials', 'error')
  }
}

const fetchCurrentUser = async () => {
  try {
    const response = await api.get('/auth/me')
    if (response.data.success) {
      currentUserId.value = response.data.data.user.id
    }
  } catch (error) {
    console.error('Failed to fetch current user:', error)
  }
}

const fetchAdminLogs = async () => {
  adminLogsLoading.value = true
  try {
    const res = await api.get('/admin/logs/admins', {
      params: { per_page: 25, page: adminLogsPage.value, search: adminLogsSearch.value }
    })
    if (res.data.success) {
      adminLogs.value = res.data.data
      adminLogsTotal.value = res.data.pagination?.total ?? res.data.total ?? 0
      adminLogsLastPage.value = res.data.pagination?.last_page ?? 1
      selectedAdminLogs.value = []
    }
  } catch (e) {
    console.error('Failed to fetch admin logs', e)
  } finally {
    adminLogsLoading.value = false
  }
}

const fetchUserLogs = async () => {
  userLogsLoading.value = true
  try {
    const res = await api.get('/admin/logs/users', {
      params: { per_page: 25, page: userLogsPage.value, search: userLogsSearch.value }
    })
    if (res.data.success) {
      userLogs.value = res.data.data
      userLogsTotal.value = res.data.pagination?.total ?? res.data.total ?? 0
      userLogsLastPage.value = res.data.pagination?.last_page ?? 1
      selectedUserLogs.value = []
    }
  } catch (e) {
    console.error('Failed to fetch user logs', e)
  } finally {
    userLogsLoading.value = false
  }
}

const toggleAllAdminLogs = () => {
  if (allAdminLogsSelected.value) {
    selectedAdminLogs.value = []
  } else {
    selectedAdminLogs.value = adminLogs.value.map(l => l.id)
  }
}

const toggleAllUserLogs = () => {
  if (allUserLogsSelected.value) {
    selectedUserLogs.value = []
  } else {
    selectedUserLogs.value = userLogs.value.map(l => l.id)
  }
}

const bulkDeleteLogs = async (scope) => {
  const isAdmin = scope.startsWith('admin')
  const isAll = scope.endsWith('_all')
  const selected = isAdmin ? selectedAdminLogs.value : selectedUserLogs.value

  if (!isAll && selected.length === 0) {
    showToast('No entries selected', 'error')
    return
  }

  const label = isAdmin ? 'admin' : 'user'
  const msg = isAll
    ? `Clear ALL ${label} logs? This cannot be undone.`
    : `Delete ${selected.length} selected log ${selected.length === 1 ? 'entry' : 'entries'}? This cannot be undone.`

  if (!confirm(msg)) return

  try {
    const payload = isAll
      ? { delete_all: true, scope: isAdmin ? 'admins' : 'users' }
      : { ids: selected }
    const res = await api.post('/admin/logs/bulk-delete', payload)
    if (res.data.success) {
      showToast(res.data.message, 'success')
      if (isAdmin) {
        selectedAdminLogs.value = []
        adminLogsPage.value = 1
        fetchAdminLogs()
      } else {
        selectedUserLogs.value = []
        userLogsPage.value = 1
        fetchUserLogs()
      }
    }
  } catch (e) {
    showToast(e.response?.data?.message || 'Failed to delete logs', 'error')
  }
}

const switchTab = (id) => {
  activeTab.value = id
  if (id === 'activity_logs') {
    if (adminLogs.value.length === 0) fetchAdminLogs()
  }
}

const formatLogTime = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })
}

const logTypeBadge = (type) => {
  const map = {
    login: 'bg-[#2F5597]/10 text-[#2F5597]',
    profile: 'bg-violet-500/10 text-violet-700',
    order: 'bg-emerald-500/10 text-emerald-700',
    quote: 'bg-amber-500/10 text-amber-700',
    invoice: 'bg-orange-500/10 text-orange-700',
    settings: 'bg-gray-200 text-gray-700',
  }
  return map[type] || 'bg-gray-100 text-gray-600'
}

onMounted(() => {
  fetchCurrentUser()
  fetchSettings()
  fetchAdminUsers()
  refreshCatalogOperations().then(() => {
    const status = catalogOperations.value?.catalog_operation?.status
    if (['queued', 'running'].includes(status)) {
      startCatalogStatusPolling()
    }
  })
  // Load last sync state and resume polling if still running
  fetchSyncState().then(() => {
    if (syncState.value.status === 'running') {
      startSyncPolling()
    }
  })
})

onBeforeUnmount(() => {
  stopCatalogStatusPolling()
  stopSyncPolling()
})
</script>
