import test from 'node:test'
import assert from 'node:assert/strict'
import { loadProductRecommendations, mergeRecommendationGroups, selectSearchInterests } from '../../resources/js/services/productRecommendations.js'

const now = Date.now()
const term = (value, days = 0, count = 1) => ({
  term: value,
  count,
  lastSearched: new Date(now - days * 86400000).toISOString(),
})
const product = (id, extra = {}) => ({
  productId: id,
  productName: `Product ${id}`,
  isAvailable: true,
  totalQuantity: 10,
  productPrice: [{ rsPrice: 500 }],
  ...extra,
})
const response = rows => ({ data: { success: true, data: { records: rows } } })

test('search interests balance recency and frequency and ignore stale or invalid terms', () => {
  assert.deepEqual(selectSearchInterests([
    term('printer', 80, 1),
    term('laptop', 0, 3),
    term('Laptop', 1, 2),
    term('router', 2, 4),
    term('monitor', 10, 1),
    term('ancient', 91, 100),
    { term: 'invalid date', lastSearched: 'invalid' },
    term('x'),
  ], now), ['router', 'laptop', 'monitor'])
})

test('recent intent takes precedence over a much older frequently repeated search', () => {
  assert.equal(selectSearchInterests([term('printer', 70, 10), term('laptop')], now)[0], 'laptop')
})

test('recommendations query the actual search rather than filtering the first catalog pages', async () => {
  const calls = []
  const result = await loadProductRecommendations({
    personalizationEnabled: true,
    searchTerms: [term('Dell laptop')],
    get: async (url, options) => {
      calls.push({ url, params: options.params })
      assert.equal(options.params.search, 'Dell laptop')
      return response([product(900001, { productName: 'Dell business laptop', productImages: [] })])
    },
  })
  assert.equal(calls.length, 1)
  assert.equal(calls[0].params.per_page, 16)
  assert.equal(result.personalized, true)
  assert.equal(result.basedOnSearch, true)
  assert.equal(result.products[0].productId, 900001)
})

test('multiple interests are interleaved while duplicate products are suppressed', () => {
  const rows = mergeRecommendationGroups([
    [product(1), product(2), product(3)],
    [product('1'), product(4), product(5)],
  ])
  assert.deepEqual(rows.map(row => row.productId), [1, 2, 4, 3, 5])
})

test('cart, favorite and view activity reuse related products with a bounded request count', async () => {
  const calls = []
  const result = await loadProductRecommendations({
    personalizationEnabled: true,
    viewedIds: ['3', '4', '5'],
    favoriteIds: [2],
    cartIds: [1],
    get: async url => { calls.push(url); return response([product(1), product(6)]) },
  })
  assert.deepEqual(calls, ['/products/1/related', '/products/2/related'])
  assert.deepEqual(result.products.map(row => row.productId), [6])
  assert.equal(result.personalized, true)
  assert.equal(result.basedOnSearch, false)
})

test('personalization disabled never uses search or activity signals', async () => {
  const calls = []
  const result = await loadProductRecommendations({
    searchTerms: [term('laptop')],
    viewedIds: ['3'],
    favoriteIds: [2],
    cartIds: [1],
    get: async (url, options) => { calls.push({ url, params: options.params }); return response([product(1)]) },
  })
  assert.equal(result.personalized, false)
  assert.equal(calls.length, 1)
  assert.equal(calls[0].url, '/products')
  assert.equal(calls[0].params.search, undefined)
})

test('new visitors receive general suggestions without hundreds of catalog records', async () => {
  const result = await loadProductRecommendations({
    personalizationEnabled: true,
    get: async (url, options) => {
      assert.equal(url, '/products')
      assert.equal(options.params.page, 1)
      assert.equal(options.params.per_page, 16)
      return response([product(1)])
    },
  })
  assert.equal(result.personalized, false)
  assert.equal(result.products.length, 1)
})

test('empty personalized matches are not padded with unrelated catalog products', async () => {
  let requests = 0
  const result = await loadProductRecommendations({
    personalizationEnabled: true,
    searchTerms: [term('specific part number')],
    get: async (url, options) => {
      requests++
      assert.equal(options.params.search, 'specific part number')
      return response([])
    },
  })
  assert.equal(requests, 1)
  assert.equal(result.personalized, true)
  assert.deepEqual(result.products, [])
})

test('unavailable, discontinued, unpriced and already-carted items are excluded', () => {
  const result = mergeRecommendationGroups([[
    product(1, { isAvailable: false }),
    product(2, { discontinueProduct: true }),
    product(3, { productPrice: [{ rsPrice: 0 }] }),
    product(4, { totalQuantity: 0 }),
    product(5),
    product(6),
  ]], ['5'])
  assert.deepEqual(result.map(row => row.productId), [6])
})

test('failed sources report partial results without unrelated fallback', async () => {
  const result = await loadProductRecommendations({
    personalizationEnabled: true,
    searchTerms: [term('laptop'), term('monitor')],
    get: async (url, options) => {
      if (options.params.search === 'laptop') throw new Error('Catalog temporarily unavailable')
      return response([product(6)])
    },
  })
  assert.equal(result.partialFailure, true)
  assert.equal(result.personalized, true)
  assert.deepEqual(result.products.map(row => row.productId), [6])
})

test('total failure and malformed responses reject instead of returning success-shaped defaults', async () => {
  await assert.rejects(loadProductRecommendations({ get: async () => { throw new Error('Offline') } }), /Offline/)
  await assert.rejects(loadProductRecommendations({ get: async () => ({ data: { data: {} } }) }), /Invalid recommendation response/)
})

test('recommendation output is capped at eight and independent per request', async () => {
  const get = async (url, options) => response(Array.from({ length: 16 }, (_, index) => product(index + (options.params.search === 'laptop' ? 100 : 200))))
  const first = await loadProductRecommendations({ get, personalizationEnabled: true, searchTerms: [term('laptop')] })
  const second = await loadProductRecommendations({ get, personalizationEnabled: true, searchTerms: [term('printer')] })
  assert.equal(first.products.length, 8)
  assert.equal(second.products.length, 8)
  assert.equal(first.products[0].productId, 100)
  assert.equal(second.products[0].productId, 200)
})
