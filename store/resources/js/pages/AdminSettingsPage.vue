<template>
  <AdminLayout>
    <template #title>Settings</template>

    <!-- Tabs -->
    <div class="rounded-xl border-0 shadow-lg bg-white mb-6 overflow-hidden">
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px overflow-x-auto">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
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
        <p class="text-sm text-gray-500 mt-1">Manage TD SYNNEX and QuickBooks credentials and payment URL templates</p>
      </div>
      
      <div class="p-6 space-y-6">
        <div class="bg-[#2F5597]/10 border border-[#2F5597]/20 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-[#2F5597] mt-1"></i>
            <div class="text-sm text-[#2F5597]">
              <p class="font-medium">Integration Settings</p>
              <p class="mt-1 text-[#2F5597]">These values are stored in app settings so production changes can be done from admin without editing code or .env.</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
          <div class="border border-gray-200 rounded-lg bg-gray-50 p-4">
            <h4 class="font-semibold text-gray-900 mb-4">TD SYNNEX</h4>
            <div class="grid grid-cols-1 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">API Client ID</label>
            <input
              v-model="apiConfig.client_id"
              type="text"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              placeholder="Enter your TD SYNNEX Client ID"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">API Client Secret</label>
            <div class="relative">
              <input
                v-model="apiConfig.client_secret"
                :type="showSecret ? 'text' : 'password'"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] pr-12"
                placeholder="••••••••••••••••"
              />
              <button
                @click="showSecret = !showSecret"
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
              >
                <i :class="['fas', showSecret ? 'fa-eye-slash' : 'fa-eye']"></i>
              </button>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">API Environment</label>
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
                  placeholder="Enter QuickBooks Client ID"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QuickBooks Client Secret</label>
                <div class="relative">
                  <input
                    v-model="apiConfig.quickbooks_client_secret"
                    :type="showQuickBooksSecret ? 'text' : 'password'"
                    class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597] pr-12"
                    placeholder="••••••••••••••••"
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
                  placeholder="Enter QuickBooks Company ID"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QuickBooks Payment URL Template</label>
                <input
                  v-model="apiConfig.quickbooks_payment_url_template"
                  type="url"
                  class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                  placeholder="https://.../{invoice_number}?amount={amount}&company={quickbooks_company_id}"
                />
                <p class="text-xs text-gray-500 mt-1">Supports placeholders like {invoice_number}, {amount}, {success_url}, {cancel_url}, {quickbooks_company_id}.</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">QuickBooks Bulk Payment URL Template</label>
                <input
                  v-model="apiConfig.quickbooks_bulk_payment_url_template"
                  type="url"
                  class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                  placeholder="https://.../bulk?invoices={invoice_numbers}&amount={amount}"
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
                placeholder="smtp.example.com"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
              <input
                v-model="emailSettings.smtp_port"
                type="text"
                class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
                placeholder="587"
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
              placeholder="e.g. 4.44"
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
              placeholder="e.g. 7.50"
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
              placeholder="e.g. 12.00"
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
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
                  <div class="flex gap-2">
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
              placeholder="Enter admin name"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input
              v-model="newAdmin.email"
              type="email"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              placeholder="admin@example.com"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input
              v-model="newAdmin.password"
              type="password"
              class="w-full px-4 py-2 border border-gray-200 bg-gray-50 text-gray-900 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F5597]"
              placeholder="Min 8 characters"
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
import { ref, onMounted, onBeforeUnmount } from 'vue'
import AdminLayout from '@/components/AdminLayout.vue'
import api from '@/services/api'

const activeTab = ref('profile')
const isSaving = ref(false)
const catalogActionLoading = ref(false)
const showSecret = ref(false)
const showQuickBooksSecret = ref(false)
const showAddAdminModal = ref(false)
const currentUserId = ref(null)

const tabs = [
  { id: 'profile', label: 'Profile', icon: 'fa-user' },
  { id: 'api', label: 'API Configuration', icon: 'fa-plug' },
  { id: 'catalog', label: 'Catalog Ops', icon: 'fa-boxes-stacked' },
  { id: 'email', label: 'Email & Notifications', icon: 'fa-envelope' },
  { id: 'system', label: 'System', icon: 'fa-cog' },
  { id: 'admins', label: 'Admin Users', icon: 'fa-users-cog' }
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
  client_id: '',
  client_secret: '',
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

const systemSettings = ref({
  company_name: 'Armely Store',
  support_email: 'support@armely.com',
  currency: 'USD',
  currency_rate: 1,
  timezone: 'America/New_York',
  maintenance_mode: false,
  tax_rate_percent: 0,
  profit_rate_percent: 0,
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
})

onBeforeUnmount(() => {
  stopCatalogStatusPolling()
})
</script>
