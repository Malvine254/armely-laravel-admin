import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import { createPinia, setActivePinia } from 'pinia'
import { reactive, nextTick } from 'vue'
import { writeGuestCartCookie, readGuestCartCookie } from '../../resources/js/services/cartPersistence.js'

const original = await readFile(new URL('../../resources/js/stores/cartStore.js', import.meta.url), 'utf8')
const source = original
  .replace("from 'pinia'", `from '${import.meta.resolve('pinia')}'`)
  .replace("from 'vue'", `from '${import.meta.resolve('vue')}'`)
  .replace("import { useAuthStore } from './authStore'", 'const useAuthStore = () => globalThis.cartFixture.auth')
  .replace("import { trackCartEvent, getBehaviorVisitorToken } from '../services/behaviorTracking'", "const trackCartEvent = () => {}; const getBehaviorVisitorToken = () => 'test-visitor-token'")
  .replace("import { isSupplierOrderable } from '../services/productAvailability'", 'const isSupplierOrderable = () => false')
  .replace("import api from '../services/api'", 'const api = globalThis.cartFixture.api')
  .replace("import { useToastStore } from './toastStore'", 'const useToastStore = () => ({ addToast: (...args) => globalThis.cartFixture.toasts.push(args) })')
  .replace("from '../services/cartPersistence'", `from '${new URL('../../resources/js/services/cartPersistence.js', import.meta.url).href}'`)

let moduleVersion = 0
const setup = async (signedIn = false) => {
  const storage = new Map()
  const cookies = new Map()
  globalThis.localStorage = {
    getItem: key => storage.get(key) ?? null,
    setItem: (key, value) => storage.set(key, value),
    removeItem: key => storage.delete(key),
  }
  globalThis.window = { location: { protocol: 'https:' } }
  globalThis.document = {
    get cookie() { return [...cookies].map(([key, value]) => `${key}=${value}`).join('; ') },
    set cookie(value) {
      const [pair] = value.split(';')
      const split = pair.indexOf('=')
      if (value.includes('Max-Age=0;')) cookies.delete(pair.slice(0, split))
      else cookies.set(pair.slice(0, split), pair.slice(split + 1))
    },
  }
  const fixture = {
    auth: reactive({ isAuthenticated: signedIn, user: signedIn ? { id: 1 } : null }),
    api: {}, toasts: [], storage,
  }
  globalThis.cartFixture = fixture
  setActivePinia(createPinia())
  fixture.module = await import(`data:text/javascript;base64,${Buffer.from(source + `\n// ${moduleVersion++}`).toString('base64')}`)
  return fixture
}
const response = (items, revision) => ({ data: { success: true, data: { items, revision } } })
const product = { productId: '12', productName: 'Laptop', productPrice: [{ rsPrice: 10 }], totalQuantity: 99, isAvailable: true }
const settle = async () => {
  for (let index = 0; index < 12; index++) await new Promise(resolve => setImmediate(resolve))
}

test('guest writes persist cookies and sign-in merges then clears guest data only after acceptance', async () => {
  const f = await setup()
  f.api.get = async () => response([], 0)
  f.api.post = async (_url, payload) => response(payload.items.map(line => ({ ...product, ...line })), 1)
  const cart = f.module.useCartStore()
  assert.equal(cart.addItem(product, 3), true)
  assert.deepEqual(readGuestCartCookie(), [{ productId: '12', quantity: 3 }])
  f.auth.user = { id: 1 }
  f.auth.isAuthenticated = true
  await nextTick()
  await settle()
  assert.equal(cart.items[0].quantity, 3)
  assert.equal(readGuestCartCookie(), null)
  assert.equal(f.storage.has('armely_cart_guest'), false)
})

test('restoration blocks edits and account saves serialize revisions without losing newer edits', async () => {
  const f = await setup(true)
  let restore
  f.api.get = () => new Promise(resolve => { restore = resolve })
  f.api.post = async () => response([product], 2)
  const puts = []
  f.api.put = (_url, payload) => new Promise(resolve => puts.push({ payload, resolve }))
  const cart = f.module.useCartStore()
  assert.equal(cart.cartLoading, true)
  assert.equal(cart.addItem(product), false)
  await settle()
  restore(response([product], 2))
  await settle()
  cart.updateQuantity('12', 3)
  cart.updateQuantity('12', 4)
  await settle()
  assert.equal(puts.length, 1)
  puts[0].resolve(response([{ ...product, quantity: 3 }], 3))
  await settle()
  assert.deepEqual(JSON.parse(f.storage.get('armely_cart_user_1_sync')), { revision: 3, dirty: true })
  assert.equal(cart.items[0].quantity, 4)
  assert.equal(puts[1].payload.revision, 3)
  puts[1].resolve(response([{ ...product, quantity: 4 }], 4))
  await settle()
  assert.deepEqual(JSON.parse(f.storage.get('armely_cart_user_1_sync')), { revision: 4, dirty: false })
})

test('failed merging retains guest cookies and stale writes expose conflict resolution', async t => {
  t.mock.method(console, 'error', () => {})
  const f = await setup(true)
  writeGuestCartCookie([{ productId: '12', quantity: 2 }])
  f.api.get = async () => response([product], 1)
  f.api.post = async () => { throw new Error('offline') }
  const cart = f.module.useCartStore()
  await settle()
  assert.match(cart.cartSyncError, /retry/i)
  assert.equal(cart.items[0].quantity, 2)
  assert.equal(readGuestCartCookie()[0].quantity, 2)
  f.api.post = async () => response([{ ...product, quantity: 2 }], 2)
  await cart.retryCartSync()
  f.api.put = async () => { const error = new Error('stale'); error.response = { status: 409 }; throw error }
  cart.updateQuantity('12', 3)
  await settle()
  assert.equal(cart.cartConflict, true)
  assert.equal(cart.items[0].quantity, 3)
})

test('cookie-only guests hydrate details without overwriting cart edits', async () => {
  const f = await setup()
  writeGuestCartCookie([{ productId: '12', quantity: 2 }])
  let hydrate
  f.api.get = () => new Promise(resolve => { hydrate = resolve })
  const cart = f.module.useCartStore()
  assert.equal(cart.items[0].quantity, 2)
  cart.updateQuantity('12', 3)
  hydrate({ data: { data: product } })
  await settle()
  assert.equal(cart.items[0].productName, 'Laptop')
  assert.equal(cart.cartTotal, 30)
})
