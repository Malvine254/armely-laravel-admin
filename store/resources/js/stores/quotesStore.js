import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useQuotesStore = defineStore('quotes', () => {
  const quotes = ref([])
  const STORAGE_KEY = 'armely_quotes'

  const loadQuotes = () => {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved) {
      try {
        quotes.value = JSON.parse(saved)
      } catch (e) {
        console.error('Failed to load quotes:', e)
        quotes.value = []
      }
    }
  }

  const saveQuotes = () => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(quotes.value))
  }

  const generateQuoteNumber = () => {
    const year = new Date().getFullYear()
    const suffix = String(Date.now()).slice(-6)
    return `QT-${year}-${suffix}`
  }

  const addQuoteFromCart = (cartItems, totals) => {
    const quote = {
      id: Date.now(),
      quoteNumber: generateQuoteNumber(),
      status: 'pending_review',
      items: cartItems.map(item => ({
        productId: item.productId,
        productName: item.productName,
        vendorId: item.vendorId,
        mfgPartNo: item.mfgPartNo,
        quantity: item.quantity,
        unitPrice: item.productPrice?.[0]?.rsPrice || 0,
        lineTotal: (item.productPrice?.[0]?.rsPrice || 0) * item.quantity
      })),
      subtotal: totals.subtotal,
      taxAmount: totals.taxAmount || 0,
      discountAmount: totals.discountAmount || 0,
      totalAmount: totals.totalAmount,
      createdAt: new Date().toISOString()
    }

    quotes.value.unshift(quote)
    saveQuotes()
    return quote
  }

  const updateStatus = (id, status) => {
    const quote = quotes.value.find(q => q.id === id)
    if (quote) {
      quote.status = status
      saveQuotes()
    }
  }

  const removeQuote = (id) => {
    quotes.value = quotes.value.filter(q => q.id !== id)
    saveQuotes()
  }

  const getQuote = (id) => {
    return quotes.value.find(q => q.id === id) || null
  }

  const quoteCount = computed(() => quotes.value.length)

  loadQuotes()

  return {
    quotes,
    quoteCount,
    addQuoteFromCart,
    updateStatus,
    removeQuote,
    getQuote
  }
})
