import test from 'node:test'
import assert from 'node:assert/strict'
import { cartLines, mergeCartItems, readGuestCartCookie, writeGuestCartCookie, clearGuestCartCookie, cartResponse } from '../../resources/js/services/cartPersistence.js'

const setupCookies = () => {
  const jar = new Map()
  globalThis.window = { location: { protocol: 'https:' } }
  globalThis.document = {
    get cookie() { return [...jar].map(([key, value]) => `${key}=${value}`).join('; ') },
    set cookie(value) {
      const [pair] = value.split(';')
      const separator = pair.indexOf('=')
      const name = pair.slice(0, separator)
      if (value.includes('Max-Age=0;')) jar.delete(name)
      else jar.set(name, pair.slice(separator + 1))
    },
  }
  return jar
}

test('merging guest and account carts keeps the larger quantity with string/numeric IDs', () => {
  assert.deepEqual(mergeCartItems([{ productId: 12, quantity: 2 }], [{ productId: '12', quantity: 5 }]), [{ productId: '12', quantity: 5 }])
  assert.deepEqual(cartLines([{ productId: 12, quantity: 4.7 }]), [{ productId: '12', quantity: 4 }])
})

test('guest cookies round-trip compact lines, chunk large carts, and clear obsolete chunks', () => {
  const jar = setupCookies()
  const items = Array.from({ length: 100 }, (_, index) => ({ productId: `sku-${index}`, quantity: 2, productName: 'Do not store full product metadata' }))
  writeGuestCartCookie(items)
  assert.ok(Number(jar.get('armely_guest_cart')) > 1)
  assert.deepEqual(readGuestCartCookie(), cartLines(items))
  writeGuestCartCookie([])
  assert.deepEqual(readGuestCartCookie(), [])
  assert.equal(jar.size, 2)
  clearGuestCartCookie()
  assert.equal(readGuestCartCookie(), null)
  assert.equal(jar.size, 0)
})

test('incomplete and invalid cookies fail explicitly instead of restoring unsafe carts', () => {
  const jar = setupCookies()
  jar.set('armely_guest_cart', '2')
  jar.set('armely_guest_cart_0', 'abc')
  assert.throws(readGuestCartCookie, /incomplete/)
  jar.set('armely_guest_cart', '1')
  jar.set('armely_guest_cart_0', encodeURIComponent(JSON.stringify([{ productId: 'x', quantity: -5 }])))
  assert.throws(readGuestCartCookie, /Invalid/)
  jar.set('armely_guest_cart', '100')
  assert.throws(readGuestCartCookie, /chunk count/)
})

test('malformed account responses cannot be treated as successful saves', () => {
  assert.throws(() => cartResponse({ data: { success: true, data: { items: [], revision: '1' } } }), /Invalid/)
  assert.deepEqual(cartResponse({ data: { success: true, data: { items: [], revision: 1 } } }), { items: [], revision: 1 })
})
