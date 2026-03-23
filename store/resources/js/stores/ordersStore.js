import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useOrdersStore = defineStore('orders', () => {
  const orders = ref([])
  const STORAGE_KEY = 'armely_orders'

  const loadOrders = () => {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved) {
      try {
        orders.value = JSON.parse(saved)
      } catch (e) {
        console.error('Failed to load orders:', e)
        orders.value = []
      }
    }
  }

  const saveOrders = () => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(orders.value))
  }

  const generateOrderNumber = () => {
    const year = new Date().getFullYear()
    const suffix = String(Date.now()).slice(-6)
    return `ORD-${year}-${suffix}`
  }

  const createFromQuote = (quote) => {
    const order = {
      id: Date.now(),
      orderNumber: generateOrderNumber(),
      status: 'pending',
      items: quote.items || [],
      totalAmount: quote.totalAmount || 0,
      createdAt: new Date().toISOString()
    }

    orders.value.unshift(order)
    saveOrders()
    return order
  }

  const updateStatus = (id, status) => {
    const order = orders.value.find(o => o.id === id)
    if (order) {
      order.status = status
      saveOrders()
    }
  }

  const totalOrders = computed(() => orders.value.length)
  const pendingOrders = computed(() => orders.value.filter(o => o.status === 'pending').length)
  const shippedOrders = computed(() => orders.value.filter(o => o.status === 'shipped').length)
  const deliveredOrders = computed(() => orders.value.filter(o => o.status === 'delivered').length)

  loadOrders()

  return {
    orders,
    totalOrders,
    pendingOrders,
    shippedOrders,
    deliveredOrders,
    createFromQuote,
    updateStatus
  }
})
