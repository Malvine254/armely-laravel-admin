import test from 'node:test'
import assert from 'node:assert/strict'
import { buildProductsLocation, parseProductsRouteFilters, categoryFilterMatches, categoryFilterLabel, resolveCategoryFilterValue } from '../../resources/js/services/productRoute.js'

const categories = [
  { name: 'Printers & Scanners', value: '06', count: 10 },
  { name: 'Laptops & Notebooks', value: '01', count: 12 },
]

test('printer deep links preserve hardware category and searches across route serialization', () => {
  const location = buildProductsLocation({ category: '06', productType: 'hardware', q: 'HP printer 4101' })
  assert.deepEqual(location.params.filterPath, ['category', '06', 'product-type', 'hardware'])
  assert.deepEqual(parseProductsRouteFilters({ params: location.params, query: location.query }), {
    category: '06', productType: 'hardware', q: 'HP printer 4101',
  })
  assert.deepEqual(parseProductsRouteFilters({ params: { filterPath: 'category/06/product-type/hardware' } }), {
    category: '06', productType: 'hardware',
  })
})

test('category codes, names and menu slugs select the same sidebar category and API value', () => {
  for (const selection of ['06', 'Printers & Scanners', 'printers-scanners']) {
    assert.equal(categoryFilterMatches(categories[0], selection), true)
    assert.equal(categoryFilterMatches(categories[1], selection), false)
    assert.equal(resolveCategoryFilterValue(selection, categories), '06')
    assert.equal(categoryFilterLabel(selection, categories), 'Printers & Scanners')
  }
  assert.equal(resolveCategoryFilterValue('06', []), '06')
  assert.equal(categoryFilterLabel('06', []), '06')
  assert.equal(categoryFilterMatches(categories[0], ''), false)
})

test('navbar searches start a new catalog search without leaking category or brand filters', () => {
  const location = buildProductsLocation({ q: 'HP printer toner' })
  assert.equal(location.name, 'products')
  assert.deepEqual(location.query, { q: 'HP printer toner' })
  assert.deepEqual(parseProductsRouteFilters({ params: {}, query: location.query }), { q: 'HP printer toner' })
})
