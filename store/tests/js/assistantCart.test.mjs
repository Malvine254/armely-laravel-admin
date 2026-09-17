import test from 'node:test'
import assert from 'node:assert/strict'
import { applyAssistantCartOperation } from '../../resources/js/services/assistantCart.js'

const operation = (type = 'add_to_cart') => ({ type, items: [{ productId: '123', quantity: 5 }] })
const product = { productId: 123, productName: 'Lenovo laptop', totalQuantity: 10, productPrice: [{ rsPrice: 1125 }] }
const cart = () => ({
  items: [],
  addItem(item, quantity) { this.items.push({ ...item, quantity }); return true },
  updateQuantity(id, quantity) { this.items.find(row => row.productId === id).quantity = quantity; return true },
  removeItem(id) { this.items = this.items.filter(row => row.productId !== id) },
})

test('removing a line drops it without touching the rest of the cart', async () => {
  const store = cart()
  store.items = [{ productId: 123, quantity: 5 }, { productId: 456, quantity: 2 }]
  assert.equal(await applyAssistantCartOperation(store, operation('remove_from_cart'), async () => { throw new Error('must not load') }), true)
  assert.deepEqual(store.items, [{ productId: 456, quantity: 2 }])
})

test('removing a product that is not in the cart reports failure', async () => {
  const store = cart()
  assert.equal(await applyAssistantCartOperation(store, operation('remove_from_cart'), async () => product), false)
})

test('adds five units using refreshed product details and pricing', async () => {
  const store = cart()
  assert.equal(await applyAssistantCartOperation(store, operation(), async () => product), true)
  assert.equal(store.items[0].quantity, 5)
  assert.equal(store.items[0].productPrice[0].rsPrice, 1125)
})

test('quote preparation does not duplicate products already added to the cart', async () => {
  const store = cart()
  store.items = [{ ...product, productId: '123', quantity: 5 }, { productId: 456, quantity: 2 }]
  assert.equal(await applyAssistantCartOperation(store, operation('prepare_quote'), async () => product), true)
  assert.equal(store.items.length, 2)
  assert.equal(store.items[0].quantity, 5)
  assert.equal(store.items[1].quantity, 2)
})

test('setting the quantity replaces the existing line instead of stacking on it', async () => {
  const store = cart()
  store.items = [{ ...product, productId: '123', quantity: 1 }]
  assert.equal(await applyAssistantCartOperation(store, operation('set_cart_quantity'), async () => product), true)
  assert.equal(store.items.length, 1)
  assert.equal(store.items[0].quantity, 5)
})

test('unavailable products and account restrictions never report success', async () => {
  for (const overrides of [{ totalQuantity: 0 }, { isAvailable: false }, { discontinueProduct: true }]) {
    const store = cart()
    assert.equal(await applyAssistantCartOperation(store, operation(), async () => ({ ...product, ...overrides })), false)
    assert.equal(store.items.length, 0)
  }
  assert.equal(await applyAssistantCartOperation({ items: [], addItem: () => false }, operation(), async () => product), false)
})

test('invalid or ambiguous operations cannot modify the cart', async () => {
  for (const value of [null, { type: 'delete' }, { ...operation(), items: [] }, { ...operation(), items: [...operation().items, ...operation().items] }, { ...operation(), items: [{ productId: 123, quantity: 0 }] }]) {
    assert.equal(await applyAssistantCartOperation(cart(), value, async () => { throw new Error('must not load') }), false)
  }
})
