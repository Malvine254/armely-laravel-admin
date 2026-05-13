<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100">
    <Navbar />

    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-5 py-8">
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-slate-900">Payment</h1>
          <p class="text-slate-600 mt-1">Review the invoice amount and billing reference.</p>
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

        <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600 mb-2">Payment Handling</h2>
          <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <img
              :src="buildStoreUrl('images/payments/secure-checkout.svg')"
              alt="Secure payment handling"
              class="h-12 w-auto"
            />
            <div>
              <p class="font-semibold text-slate-900">Invoice Delivery</p>
              <p class="text-sm text-slate-600">Your invoice will be sent to you for review and payment coordination.</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl border border-[#d9e6f7] bg-[#f8fbff] p-4 mb-6">
          <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <img
              :src="buildStoreUrl('images/payments/secure-checkout.svg')"
              alt="Secure checkout"
              class="h-12 w-auto"
            />
            <p class="text-sm text-slate-700">
              You can return to your invoices at any time to review balances and download PDF copies.
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
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useToastStore } from '../../stores/toastStore'
import { buildStoreUrl } from '../../services/runtimeConfig'
import Navbar from '../../components/Navbar.vue'
import { usePricingSettings } from '../../composables/usePricingSettings'

const route = useRoute()
const router = useRouter()
const toastStore = useToastStore()
const { loadPricingSettings, formatUsdUsingCurrentCurrency } = usePricingSettings()

const processing = ref(false)

const mode = computed(() => String(route.query.mode || 'invoice'))
const invoiceNumber = computed(() => String(route.query.invoiceNumber || '').trim())
const invoiceNumbers = computed(() => {
  const raw = String(route.query.invoiceNumbers || '').trim()
  if (!raw) return []
  return raw.split(',').map(v => v.trim()).filter(Boolean)
})

const resolvedInvoiceAmount = ref(null)

const parseAmount = (value) => {
  const amount = Number(value)
  return Number.isFinite(amount) ? amount : 0
}

const estimatedAmount = computed(() => {
  if (resolvedInvoiceAmount.value !== null) {
    return resolvedInvoiceAmount.value
  }

  return parseAmount(route.query.amount || 0)
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
  return formatUsdUsingCurrentCurrency(Number(amount || 0))
}

loadPricingSettings()

const resolveInvoiceAmount = async () => {
  resolvedInvoiceAmount.value = null

  if (mode.value !== 'invoice' || !invoiceNumber.value) {
    return
  }

  try {
    const response = await axios.get(`/api/v1/invoices/${invoiceNumber.value}`)
    const invoice = response?.data?.data

    if (!invoice) {
      return
    }

    const totalAmount = parseAmount(invoice.total_amount)
    const paidAmount = parseAmount(invoice.paid_amount)
    resolvedInvoiceAmount.value = Math.max(0, totalAmount - paidAmount)
  } catch (error) {
    console.error('Unable to resolve invoice amount from API:', error)
  }
}

watch(
  [mode, invoiceNumber],
  () => {
    resolveInvoiceAmount()
  },
  { immediate: true }
)

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
