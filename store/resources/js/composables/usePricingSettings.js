import { ref } from 'vue'
import api from '../services/api'

const pricingSettings = ref({
  tax_rate_percent: 0,
  profit_rate_percent: 0,
  currency_code: 'USD',
  currency_rate: 1,
})

const pricingLoaded = ref(false)
const pricingLoading = ref(false)

const toNumber = (value, fallback = 0) => {
  const n = Number(value)
  return Number.isFinite(n) ? n : fallback
}

const normalizeCurrencyCode = (value) => {
  const code = String(value || 'USD').trim().toUpperCase()
  return code || 'USD'
}

const loadPricingSettings = async (force = false) => {
  if (pricingLoaded.value && !force) {
    return pricingSettings.value
  }

  if (pricingLoading.value) {
    return pricingSettings.value
  }

  pricingLoading.value = true
  let loadedSuccessfully = false
  try {
    const response = await api.get('/pricing/settings')
    if (response.data?.success && response.data?.data) {
      const data = response.data.data
      pricingSettings.value = {
        tax_rate_percent: Math.max(0, toNumber(data.tax_rate_percent, 0)),
        profit_rate_percent: Math.max(0, toNumber(data.profit_rate_percent, 0)),
        currency_code: normalizeCurrencyCode(data.currency_code || data.currency || 'USD'),
        currency_rate: Math.max(0.0001, toNumber(data.currency_rate, 1)),
      }
      loadedSuccessfully = true
    }
  } catch (error) {
    // Keep defaults so pricing UI still works if endpoint temporarily fails.
    console.warn('Failed to load pricing settings, using defaults', error)
  } finally {
    pricingLoaded.value = loadedSuccessfully
    pricingLoading.value = false
  }

  return pricingSettings.value
}

const getCatalogPriceWithRules = (baseUsdPrice) => {
  const base = Math.max(0, toNumber(baseUsdPrice, 0))
  const profitRate = Math.max(0, toNumber(pricingSettings.value.profit_rate_percent, 0))
  const taxRate = Math.max(0, toNumber(pricingSettings.value.tax_rate_percent, 0))

  const withProfit = base + ((base * profitRate) / 100)
  const withTax = withProfit + ((withProfit * taxRate) / 100)
  return withTax
}

const convertFromUsd = (amountUsd) => {
  const usd = Math.max(0, toNumber(amountUsd, 0))
  const rate = Math.max(0.0001, toNumber(pricingSettings.value.currency_rate, 1))
  return usd * rate
}

const formatWithCurrency = (amountInSelectedCurrency) => {
  const currency = normalizeCurrencyCode(pricingSettings.value.currency_code)
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency,
  }).format(toNumber(amountInSelectedCurrency, 0))
}

const formatUsdUsingCurrentCurrency = (amountUsd) => {
  return formatWithCurrency(convertFromUsd(amountUsd))
}

export function usePricingSettings() {
  return {
    pricingSettings,
    pricingLoaded,
    loadPricingSettings,
    getCatalogPriceWithRules,
    convertFromUsd,
    formatWithCurrency,
    formatUsdUsingCurrentCurrency,
  }
}
