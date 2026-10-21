import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const source = await readFile(new URL('../../resources/js/views/catalog/Products.vue', import.meta.url), 'utf8')
const start = source.indexOf('const performSearch = async')
const end = source.indexOf('\nconst clearSearch =', start)
const performSearchSource = source.slice(start, end)
const ref = value => ({ value })
const deferred = () => {
  let resolve, reject
  const promise = new Promise((yes, no) => { resolve = yes; reject = no })
  return { promise, resolve, reject }
}
const response = name => ({ data: { success: true, data: { records: [{ productId: name }], total: 1 } } })

const setup = () => {
  const requests = []
  const fixture = {
    currentPage: ref(1),
    requiresClientSideFiltering: ref(false),
    currentFilters: ref({ vendors: [], categories: [], mediaStatuses: [], priceMin: 100, priceMax: 0 }),
    getCacheKey: () => 'same-page',
    pendingRequests: new Map(),
    activeSearchRequestId: 0,
    error: ref(''),
    dismissSearchSuggestions: () => {},
    loading: ref(false),
    pageLoading: ref(false),
    products: ref([]),
    serverPaged: ref(false),
    searchQuery: ref(''),
    normalizeSearchText: value => value.trim(),
    requestCache: new Map(),
    PRODUCTS_RESULTS_SOFT_TTL_MS: 1000,
    loadProductResultsCache: () => null,
    requiresClientForFilters: () => false,
    CATALOG_REQUEST_REVISION: 'test',
    DEFAULT_BROWSE_MIN_PRICE: 100,
    ITEMS_PER_PAGE: 10,
    getProductsWithRetry: () => {
      const request = deferred()
      requests.push(request)
      return request.promise
    },
    mediaFacetCounts: ref({}),
    mediaFacetCountsLoaded: ref(false),
    resetImgErrorMap: () => {},
    serverTotal: ref(0),
    serverHasMore: ref(false),
    serverTotalIsEstimate: ref(false),
    supplierLookupQueued: ref(false),
    saveProductResultsCache: () => {},
    ENABLE_SERVER_PREFETCH: false,
    queueFacetRefresh: () => {},
    getProductsApiErrorMessage: error => error.message,
    console: { error: () => {} },
  }
  // Execute the component's real request function with controlled API promises.
  const performSearch = new Function(...Object.keys(fixture), `${performSearchSource}; return performSearch`)(...Object.values(fixture))
  return { ...fixture, requests, performSearch }
}

test('auth background refresh clears foreground loading even when the original request completes later', async () => {
  const f = setup()
  const initial = f.performSearch()
  assert.equal(f.loading.value, true)
  f.requestCache.clear()
  f.pendingRequests.clear()
  const restoredAuth = f.performSearch(false, true)
  f.requests[1].resolve(response('customer-product'))
  await restoredAuth
  assert.equal(f.loading.value, false)
  assert.equal(f.pageLoading.value, false)
  assert.equal(f.products.value[0].productId, 'customer-product')
  f.requests[0].resolve(response('stale-guest-product'))
  await initial
  assert.equal(f.products.value[0].productId, 'customer-product')
})

test('a stale request cannot clear the newer pending request or stop its loader', async () => {
  const f = setup()
  const initial = f.performSearch()
  f.pendingRequests.clear()
  const replacement = f.performSearch(false, true)
  const newestPending = f.pendingRequests.get('same-page')
  f.requests[0].resolve(response('old'))
  await initial
  assert.equal(f.loading.value, true)
  assert.equal(f.pendingRequests.get('same-page'), newestPending)
  const duplicate = f.performSearch()
  assert.equal(f.requests.length, 2)
  f.requests[1].resolve(response('new'))
  await Promise.all([replacement, duplicate])
  assert.equal(f.loading.value, false)
  assert.equal(f.pendingRequests.size, 0)
})

test('a failed auth refresh clears loading and shows the failure, not an endless skeleton', async () => {
  const f = setup()
  const initial = f.performSearch()
  f.pendingRequests.clear()
  const replacement = f.performSearch(false, true)
  f.requests[1].reject(new Error('Product service timed out'))
  await replacement
  assert.equal(f.loading.value, false)
  assert.equal(f.error.value, 'Product service timed out')
  f.requests[0].reject(new Error('Stale guest error'))
  await initial
  assert.equal(f.error.value, 'Product service timed out')
})

test('stale unsuccessful responses cannot replace a successful authenticated result with an error', async () => {
  const f = setup()
  const initial = f.performSearch()
  f.pendingRequests.clear()
  const replacement = f.performSearch(false, true)
  f.requests[1].resolve(response('signed-in'))
  await replacement
  f.requests[0].resolve({ data: { success: false } })
  await initial
  assert.equal(f.error.value, '')
  assert.equal(f.loading.value, false)
  assert.equal(f.products.value[0].productId, 'signed-in')
})
