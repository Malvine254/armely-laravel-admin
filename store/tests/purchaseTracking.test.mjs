import { test } from 'node:test'
import assert from 'node:assert/strict'
import { trackVerifiedPurchases } from '../resources/js/services/purchaseTracking.js'

test('empty results send nothing; confirmed purchases use values and deduplicate reloads', () => {
  const calls = []
  const saved = new Map()
  const browser = {
    storeTracking: { ga4Id: 'G-TEST', adsPurchaseDestination: 'AW-TEST/purchase' },
    gtag: (...args) => calls.push(args),
    localStorage: { getItem: key => saved.get(key), setItem: (key, value) => saved.set(key, value) },
  }
  trackVerifiedPurchases([], browser)
  assert.equal(calls.length, 0)
  const purchases = [{ transaction_id: 'invoice-1', value: 100, total: 115, tax: 10, shipping: 5, currency: 'USD', items: [] }]
  trackVerifiedPurchases(purchases, browser)
  trackVerifiedPurchases(purchases, browser)
  assert.equal(calls.length, 2)
  assert.equal(calls[0][1], 'purchase')
  assert.equal(calls[0][2].value, 100)
  assert.equal(calls[1][1], 'conversion')
  assert.equal(calls[1][2].value, 115)
  assert.equal(calls[1][2].transaction_id, 'invoice-1')
})

test('missing destinations or tag never emit a purchase', () => {
  trackVerifiedPurchases([], {})
  trackVerifiedPurchases([{ transaction_id: '1', value: 10, currency: 'USD' }], {
    gtag: () => assert.fail('No destination configured'),
  })
})
