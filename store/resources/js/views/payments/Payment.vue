<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100">
    <Navbar />

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-slate-900">Payment</h1>
          <p class="text-slate-600 mt-1">Choose a payment option and continue to secure checkout.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
          <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Payment Type</p>
            <p class="text-lg font-bold text-slate-900 mt-1">{{ modeLabel }}</p>
          </div>
          <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Reference</p>
            <p class="text-lg font-bold text-slate-900 mt-1 break-all">{{ referenceLabel }}</p>
          </div>
          <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-semibold">Amount</p>
            <p class="text-lg font-bold text-[#2F5597] mt-1">{{ formatCurrency(estimatedAmount) }}</p>
          </div>
        </div>

        <div class="mb-6">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600 mb-3">Select Payment Option</h2>
          <div class="space-y-3">
            <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition"
              :class="selectedOption === 'card' ? 'border-[#2F5597] bg-[#edf3fb]' : 'border-slate-200 hover:border-slate-300'">
              <input v-model="selectedOption" value="card" type="radio" class="mt-1" style="accent-color: #2F5597;" />
              <div>
                <p class="font-semibold text-slate-900">Card Payment</p>
                <p class="text-sm text-slate-600">Use Visa, Mastercard, or AMEX via secure checkout.</p>
                <img
                  src="/images/payments/card-payment.svg"
                  alt="Card payment gateways"
                  class="mt-2 h-12 w-auto rounded-md border border-slate-200"
                />
              </div>
            </label>

            <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition"
              :class="selectedOption === 'bank' ? 'border-[#2F5597] bg-[#edf3fb]' : 'border-slate-200 hover:border-slate-300'">
              <input v-model="selectedOption" value="bank" type="radio" class="mt-1" style="accent-color: #2F5597;" />
              <div>
                <p class="font-semibold text-slate-900">Bank Account</p>
                <p class="text-sm text-slate-600">Pay directly using bank transfer options in checkout.</p>
                <img
                  src="/images/payments/bank-payment.svg"
                  alt="Bank transfer gateway"
                  class="mt-2 h-12 w-auto rounded-md border border-slate-200"
                />
              </div>
            </label>

            <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition"
              :class="selectedOption === 'wallet' ? 'border-[#2F5597] bg-[#edf3fb]' : 'border-slate-200 hover:border-slate-300'">
              <input v-model="selectedOption" value="wallet" type="radio" class="mt-1" style="accent-color: #2F5597;" />
              <div>
                <p class="font-semibold text-slate-900">Wallet / Alternative</p>
                <p class="text-sm text-slate-600">Use available wallet or alternative methods at checkout.</p>
                <img
                  src="/images/payments/wallet-payment.svg"
                  alt="Wallet payment options"
                  class="mt-2 h-12 w-auto rounded-md border border-slate-200"
                />
              </div>
            </label>
          </div>
        </div>

        <div class="rounded-xl border border-[#d9e6f7] bg-[#f8fbff] p-4 mb-6">
          <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <img
              src="/images/payments/secure-checkout.svg"
              alt="Secure checkout"
              class="h-12 w-auto"
            />
            <p class="text-sm text-slate-700">
              You will be redirected to secure payment checkout. Final available methods may vary based on invoice and region.
            </p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-end">
          <button
            @click="goBack"
            type="button"
            class="px-5 py-3 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition"
          >
            Back
          </button>
          <button
            @click="continueToCheckout"
            type="button"
            :disabled="processing"
            class="px-5 py-3 rounded-lg text-white font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed"
            style="background-color: #2F5597;"
          >
            {{ processing ? 'Starting Checkout...' : 'Continue to Payment' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useToastStore } from '../../stores/toastStore'
import Navbar from '../../components/Navbar.vue'

const route = useRoute()
const router = useRouter()
const toastStore = useToastStore()

const processing = ref(false)
const PAYMENT_OPTION_KEY = 'armely_preferred_payment_option'
const allowedPaymentOptions = ['card', 'bank', 'wallet']
const preferredOption = String(localStorage.getItem(PAYMENT_OPTION_KEY) || 'card')
const selectedOption = ref(allowedPaymentOptions.includes(preferredOption) ? preferredOption : 'card')

const mode = computed(() => String(route.query.mode || 'invoice'))
const invoiceNumber = computed(() => String(route.query.invoiceNumber || '').trim())
const invoiceNumbers = computed(() => {
  const raw = String(route.query.invoiceNumbers || '').trim()
  if (!raw) return []
  return raw.split(',').map(v => v.trim()).filter(Boolean)
})

const estimatedAmount = computed(() => {
  const amount = Number(route.query.amount || 0)
  return Number.isFinite(amount) ? amount : 0
})

const modeLabel = computed(() => {
  if (mode.value === 'bulk') return 'Combined Invoices'
  if (mode.value === 'quote') return 'Quote Invoice'
  return 'Single Invoice'
})

const referenceLabel = computed(() => {
  if (mode.value === 'bulk') {
    const count = Number(route.query.count || invoiceNumbers.value.length || 0)
    return `${count} invoice(s)`
  }

  if (mode.value === 'quote') {
    const quoteId = String(route.query.quoteId || '').trim()
    if (quoteId) return `Quote ${quoteId}`
  }

  return invoiceNumber.value || 'N/A'
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(amount || 0))
}

const goBack = () => {
  const from = String(route.query.from || '').trim()
  if (from) {
    router.push(from)
    return
  }

  router.push({ name: 'invoices' })
}

const continueToCheckout = async () => {
  if (processing.value) {
    return
  }

  processing.value = true
  localStorage.setItem(PAYMENT_OPTION_KEY, selectedOption.value)

  try {
    let response

    if (mode.value === 'bulk') {
      if (invoiceNumbers.value.length === 0) {
        throw new Error('No invoices selected for combined payment.')
      }

      response = await axios.post('/api/v1/invoices/pay-multiple', {
        invoice_numbers: invoiceNumbers.value,
      })
    } else {
      if (!invoiceNumber.value) {
        throw new Error('Invoice number is required for payment.')
      }

      response = await axios.post(`/api/v1/invoices/${invoiceNumber.value}/pay`)
    }

    const sessionUrl = response?.data?.data?.session_url

    if (response?.data?.success && sessionUrl) {
      window.location.href = sessionUrl
      return
    }

    throw new Error(response?.data?.message || 'Unable to start checkout.')
  } catch (error) {
    toastStore.addToast(error.response?.data?.message || error.message || 'Failed to start payment', 'error')
  } finally {
    processing.value = false
  }
}
</script>
