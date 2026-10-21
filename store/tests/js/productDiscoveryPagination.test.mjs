import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const source = await readFile(new URL('../../resources/js/views/catalog/Products.vue', import.meta.url), 'utf8')
const ref = value => ({ value })
const extract = (start, end) => source.slice(source.indexOf(start), source.indexOf(end, source.indexOf(start)))
const run = (code, fixture, result) => new Function(...Object.keys(fixture), `${code}; return ${result}`)(...Object.values(fixture))

test('supplier results use the one existing product grid and pagination controls', () => {
  const template = source.slice(0, source.indexOf('<script'))
  assert.equal((template.match(/<ProductCard\b/g) || []).length, 1)
  assert.match(template, /v-for="\(product, productIndex\) in paginatedProducts"/)
  assert.match(template, /@view="viewProductDetails"/)
  assert.doesNotMatch(template, /loadDiscoveryDetails|:discovery=/)
  assert.equal((template.match(/ref="paginationRow"/g) || []).length, 1)
  assert.match(source, /const ITEMS_PER_PAGE = 10/)
})

test('supplier totals and subsequent pages feed existing pagination without being sliced twice', () => {
  const fixture = {
    computed: getter => ({ get value() { return getter() } }),
    discoveryOpen: ref(true),
    discoveryProducts: ref(Array.from({ length: 10 }, (_, index) => ({ productId: `page-two-${index}` }))),
    discoveryTotal: ref(23),
    discoveryTotalIsEstimate: ref(false),
    discoveryCardProduct: product => product,
    currentPage: ref(2),
    sortBy: ref('relevance'),
    searchQuery: ref('HP ZBOOK'),
    currentFilters: ref({ categories: [] }),
    serverPaged: ref(false),
    serverTotal: ref(999),
    ITEMS_PER_PAGE: 10,
    getProductUnitPrice: product => product.productPrice?.[0]?.rsPrice ?? null,
  }
  const code = [
    extract('const filteredProducts = computed', '\nconst requiresClientSideFiltering'),
    extract('const totalProducts = computed', '\nconst totalProductsLabel'),
    extract('const totalPages = computed', '\nconst visibleProductsCount'),
    extract('const paginatedProducts = computed', '\nconst pageNumbers'),
  ].join('\n')
  const result = run(code, fixture, '({ totalProducts, totalPages, paginatedProducts })')
  assert.equal(result.totalProducts.value, 23)
  assert.equal(result.totalPages.value, 3)
  assert.equal(result.paginatedProducts.value.length, 10)
  assert.equal(result.paginatedProducts.value[0].productId, 'page-two-0')
})

test('existing next, previous and numbered controls request supplier pages, not local catalog pages', () => {
  const pages = []
  const fixture = {
    discoveryOpen: ref(true),
    discoveryLoading: ref(false),
    currentPage: ref(2),
    totalPages: ref(3),
    loadDiscoveryPage: page => { pages.push(page); fixture.currentPage.value = page },
    window: { scrollTo() {} },
  }
  const code = extract('const nextPage = () =>', '\n// Prefetch adjacent pages')
  const controls = run(code, fixture, '({ nextPage, previousPage, goToPage })')
  controls.nextPage()
  controls.previousPage()
  controls.goToPage(1)
  assert.deepEqual(pages, [3, 2, 1])
  fixture.discoveryLoading.value = true
  controls.nextPage()
  assert.deepEqual(pages, [3, 2, 1])
})

test('page loading sends the catalog page size, handles errors, and ignores stale responses', async () => {
  const requests = []
  const fixture = {
    discoveryQuery: 'HP ZBOOK',
    discoveryRequestId: 0,
    currentPage: ref(1),
    discoveryContentWarning: ref(''),
    currentFilters: ref({ priceMin: 100, priceMax: 0 }),
    enrichDiscoveryProducts: () => {},
    discoveryError: ref(''),
    discoveryLoading: ref(false),
    discoveryProducts: ref([{ product_id: 'old' }]),
    discoveryTotal: ref(23),
    discoveryTotalIsEstimate: ref(false),
    discoveryMessage: ref(''),
    pageLoading: ref(false),
    ITEMS_PER_PAGE: 10,
    discoveryFailureMessage: () => 'Supplier unavailable',
    api: { get: (url, options) => new Promise((resolve, reject) => requests.push({ url, options, resolve, reject })) },
  }
  const loadPage = run(extract('const loadDiscoveryPage = async', '\nconst enrichDiscoveryProducts'), fixture, 'loadDiscoveryPage')
  const stale = loadPage(2)
  const latest = loadPage(3)
  assert.deepEqual(requests[1].options.params, { search: 'HP ZBOOK', page: 3, per_page: 10, min_price: 100 })
  requests[1].resolve({ data: { data: [{ product_id: 'last-page' }], pagination: { total: 23 }, message: 'Matches' } })
  await latest
  requests[0].resolve({ data: { data: [{ product_id: 'stale-page' }], pagination: { total: 99 } } })
  await stale
  assert.equal(fixture.discoveryProducts.value[0].product_id, 'last-page')
  assert.equal(fixture.discoveryTotal.value, 23)
  assert.equal(fixture.pageLoading.value, false)
  assert.equal(fixture.discoveryLoading.value, false)
  const failed = loadPage(2)
  requests[2].reject(new Error('Network'))
  await failed
  assert.equal(fixture.currentPage.value, 3)
  assert.equal(fixture.discoveryError.value, 'Supplier unavailable')
  assert.equal(fixture.discoveryProducts.value[0].product_id, 'last-page')
})

test('pagination keeps the original controls with a compact non-overlapping page window', () => {
  const code = extract('const pageNumbers = computed', '\n// Add request deduplication')
  for (const width of [320, 500, 640, 1050]) {
    const result = run(code, {
      computed: getter => ({ get value() { return getter() } }),
      paginationWidth: ref(width),
      currentPage: ref(8),
      totalPages: ref(17),
    }, 'pageNumbers')
    const pages = result.value
    assert.ok(pages.includes(8))
    assert.ok(pages.length <= 7)
    assert.ok(pages.length * 48 + (width >= 600 ? 280 : 120) <= width)
  }
})

const setupEnrichment = () => {
  const matches = ['12345', '12346', '12347'].map(productId => ({
    product_id: productId,
    catalog_product: { productId, onlineContentStatus: 'pending', productPrice: [{ rsPrice: 100 }] },
  }))
  const requests = []
  const fixture = {
    discoveryOpen: ref(true),
    discoveryProducts: ref(matches),
    discoveryContentWarning: ref(''),
    discoveryFailureMessage: () => 'Online content is temporarily unavailable.',
    getPrimaryImageUrl: product => product.image_url || '',
    api: { get: url => new Promise((resolve, reject) => requests.push({ url, resolve, reject })) },
  }
  const controls = run(`let discoveryRequestId = 1;\n${extract('const enrichDiscoveryProducts = async', '\nconst availableVendors')}`,
    fixture, '({ enrich: enrichDiscoveryProducts, invalidate: () => discoveryRequestId++ })')
  return { ...fixture, ...controls, matches, requests }
}

test('content loads automatically and sequentially without blocking or repricing cards', async () => {
  const f = setupEnrichment()
  const work = f.enrich(f.matches, 1)
  assert.equal(f.requests.length, 1)
  assert.equal(f.discoveryProducts.value[0].catalog_product.productPrice[0].rsPrice, 100)
  for (let index = 0; index < 3; index++) {
    assert.match(f.requests[index].url, new RegExp(`/products/${12345 + index}/content$`))
    f.requests[index].resolve({ data: { data: {
      ...f.matches[index].catalog_product,
      onlineContentStatus: 'verified',
      image_url: 'https://images.example.com/verified.jpg',
    } } })
    await Promise.resolve()
  }
  await work
  assert.equal(f.discoveryProducts.value[2].catalog_product.image_url, 'https://images.example.com/verified.jpg')
  assert.equal(f.discoveryProducts.value[2].catalog_product.productPrice[0].rsPrice, 100)
  assert.equal(f.discoveryContentWarning.value, '')
})

test('stale content responses cannot modify a new page or start further lookups', async () => {
  const f = setupEnrichment()
  const work = f.enrich(f.matches, 1)
  f.invalidate()
  f.requests[0].resolve({ data: { data: { productId: 'stale' } } })
  await work
  assert.equal(f.requests.length, 1)
  assert.equal(f.discoveryProducts.value[0].catalog_product.productId, '12345')
})

test('rate limits stop automatic lookups explicitly without removing usable cards', async () => {
  const f = setupEnrichment()
  const work = f.enrich(f.matches, 1)
  f.requests[0].reject({ response: { status: 429 } })
  await work
  assert.equal(f.requests.length, 1)
  assert.equal(f.discoveryContentWarning.value, 'Online content is temporarily unavailable.')
  assert.equal(f.discoveryProducts.value.length, 3)
})
