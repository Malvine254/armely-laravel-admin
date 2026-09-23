<template>
  <div class="fixed inset-0 z-[110] flex items-center justify-center bg-black/50 p-4" @click.self="close" @keydown.esc="close">
    <section role="dialog" aria-modal="true" aria-labelledby="account-details-title" @keydown.tab="trapFocus" class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
      <header class="flex items-center justify-between bg-[#2F5597] px-6 py-4 text-white">
        <h2 id="account-details-title" class="text-lg font-semibold">Account details</h2>
        <button ref="closeButton" type="button" :disabled="busy" aria-label="Close account details" @click="close">Close</button>
      </header>
      <div class="overflow-y-auto p-6 space-y-5">
        <p v-if="loading" role="status">Loading account details...</p>
        <p v-if="error" role="alert" class="rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ error }}</p>
        <p v-if="message" role="status" class="rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ message }}</p>
        <template v-if="account">
          <form class="space-y-4" @submit.prevent="save">
            <div v-for="field in fields" :key="field.key">
              <label :for="`account-${field.key}`" class="mb-1 block text-sm font-medium text-gray-700">{{ field.label }}</label>
              <input :id="`account-${field.key}`" v-model.trim="form[field.key]" :type="field.type" :required="field.key !== 'phone'" :maxlength="field.key === 'phone' ? 50 : 255" :disabled="!canManage || busy" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 disabled:bg-gray-50" />
            </div>
            <p v-if="canManage" class="text-xs text-gray-500">Changing the email clears email verification and signs out existing sessions.</p>
            <button v-if="canManage" :disabled="busy || !dirty" class="rounded-lg bg-[#2F5597] px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">{{ saving ? 'Saving...' : 'Save profile' }}</button>
          </form>
          <dl class="grid grid-cols-1 gap-4 border-t pt-4 sm:grid-cols-2">
            <div v-for="item in details" :key="item.label">
              <dt class="text-xs font-semibold uppercase text-gray-500">{{ item.label }}</dt>
              <dd class="mt-1 break-words text-sm text-gray-900">{{ item.value ?? 'Not provided' }}</dd>
            </div>
          </dl>
          <div v-if="account.company" class="border-t pt-4">
            <h3 class="font-semibold text-gray-900">Company</h3>
            <p class="text-sm text-gray-700">{{ account.company.name }} · {{ account.company.domain }} · {{ account.company.status }}</p>
            <div v-for="address in account.company.addresses" :key="address.id" class="mt-3 rounded-lg bg-gray-50 p-3 text-sm text-gray-700">
              <p class="font-semibold">{{ address.label || address.type }}{{ address.is_default ? ' (default)' : '' }}</p>
              <p>{{ address.contact_name }} {{ address.contact_phone }}</p>
              <p>{{ [address.street_1, address.street_2, address.city, address.state, address.postal_code, address.country].filter(Boolean).join(', ') }}</p>
            </div>
            <p v-if="!account.company.addresses?.length" class="mt-2 text-sm text-gray-500">No saved addresses.</p>
          </div>
          <div v-if="canManage" class="border-t pt-4">
            <h3 class="font-semibold text-gray-900">Password assistance</h3>
            <p class="my-2 text-sm text-gray-600">Email a reset link to {{ account.email }}. It expires in 60 minutes. The current password stays valid until the user completes the reset.</p>
            <button type="button" :disabled="busy || dirty" @click="sendReset" class="rounded-lg border border-[#2F5597] px-4 py-2 text-sm font-semibold text-[#2F5597] disabled:opacity-50">{{ sending ? 'Sending...' : 'Send password reset link' }}</button>
            <p v-if="dirty" class="mt-2 text-xs text-gray-500">Save profile changes before sending a reset link.</p>
          </div>
        </template>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import api from '@/services/api'

const props = defineProps({ userId: { type: [Number, String], required: true } })
const emit = defineEmits(['close', 'updated'])
const account = ref(null)
const canManage = ref(false)
const loading = ref(true)
const saving = ref(false)
const sending = ref(false)
const error = ref('')
const message = ref('')
const closeButton = ref(null)
const previousFocus = document.activeElement
const form = ref({ name: '', email: '', phone: '' })
const fields = [{ key: 'name', label: 'Full name', type: 'text' }, { key: 'email', label: 'Email address', type: 'email' }, { key: 'phone', label: 'Phone', type: 'tel' }]
const busy = computed(() => loading.value || saving.value || sending.value)
const dirty = computed(() => account.value && fields.some(({ key }) => form.value[key] !== (account.value[key] || '')))
const date = value => value ? new Date(value).toLocaleString() : 'Not recorded'
const details = computed(() => account.value ? [
  { label: 'Account ID', value: account.value.id },
  { label: 'Role', value: account.value.role },
  { label: 'Status', value: account.value.status },
  { label: 'Email verified', value: date(account.value.email_verified_at) },
  { label: 'Created', value: date(account.value.created_at) },
  { label: 'Last updated', value: date(account.value.updated_at) },
  { label: 'Password change required', value: account.value.force_password_change ? 'Yes' : 'No' },
  { label: 'Temporary password expires', value: date(account.value.temp_password_expires_at) },
  ...( ['admin', 'super_admin'].includes(account.value.role)
    ? [{ label: 'Permissions', value: account.value.role === 'super_admin' || !account.value.permissions?.length ? 'All permissions' : account.value.permissions.join(', ') }]
    : [{ label: 'Special pricing discount', value: `${account.value.special_pricing_percent || 0}%` }, { label: 'Assigned shipping amount', value: account.value.assigned_shipping_amount || '0.00' }]),
] : [])
function apply(data) {
  account.value = data.data
  canManage.value = data.can_manage
  form.value = Object.fromEntries(fields.map(({ key }) => [key, account.value[key] || '']))
}
function failure(e) { error.value = Object.values(e.response?.data?.errors || {}).flat()[0] || e.response?.data?.message || 'Unable to complete this action. Please try again.' }
function close() {
  if (!busy.value && (!dirty.value || confirm('Discard unsaved profile changes?'))) emit('close')
}
function trapFocus(event) {
  const controls = [...event.currentTarget.querySelectorAll('button:not(:disabled), input:not(:disabled), [tabindex="0"]')]
  const first = controls[0], last = controls[controls.length - 1]
  if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus() }
  else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus() }
}
async function save() {
  saving.value = true; error.value = ''; message.value = ''
  try {
    const { data } = await api.put(`/admin/accounts/${props.userId}`, form.value)
    apply(data); emit('updated'); message.value = 'Account profile updated.'
  } catch (e) { failure(e) }
  finally { saving.value = false }
}
async function sendReset() {
  sending.value = true; error.value = ''; message.value = ''
  try { const { data } = await api.post(`/admin/accounts/${props.userId}/reset-password`); message.value = data.message }
  catch (e) { failure(e) }
  finally { sending.value = false }
}
onMounted(async () => {
  closeButton.value?.focus()
  try { const { data } = await api.get(`/admin/accounts/${props.userId}`); apply(data) }
  catch (e) { failure(e) }
  finally { loading.value = false }
})
onUnmounted(() => previousFocus?.focus())
</script>
