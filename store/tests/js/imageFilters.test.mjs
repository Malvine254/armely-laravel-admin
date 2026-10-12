import test from 'node:test'
import assert from 'node:assert/strict'
import fs from 'node:fs'
import vm from 'node:vm'
import { parse, compileScript } from '@vue/compiler-sfc'
import { ref, computed, watch } from 'vue'
import { categoryFilterLabel, categoryFilterMatches } from '../../resources/js/services/productRoute.js'

const sidebarSource = fs.readFileSync(new URL('../../resources/js/components/FilterSidebar.vue', import.meta.url), 'utf8')
const { descriptor } = parse(sidebarSource)
const script = compileScript(descriptor, { id: 'image-filter-test' }).content
  .replace(/^import .*$/gm, '')
  .replace('import.meta.env.VITE_MIN_PRICE', '100')
  .replace('export default', 'module.exports =')
const module = { exports: {} }
vm.runInNewContext(script, { module, ref, computed, watch, categoryFilterLabel, categoryFilterMatches })

const mediaOptions = [
  { name: 'Has Reviews', count: 2 },
  { name: 'Has Images', count: 8 },
  { name: 'No Images', count: 5 },
]
const setupSidebar = showImageFilters => module.exports.setup({
  showImageFilters,
  mediaOptions,
  activeFilters: { mediaStatuses: ['No Images', 'Has Reviews'] },
}, { expose() {}, emit() {} })

test('enabled image filters appear and survive active-filter synchronization', () => {
  const sidebar = setupSidebar(true)
  assert.deepEqual(Array.from(sidebar.visibleMediaOptions.value, item => item.name), ['Has Reviews', 'Has Images', 'No Images'])
  assert.deepEqual(Array.from(sidebar.filters.value.mediaStatuses), ['No Images', 'Has Reviews'])
  sidebar.syncFromActiveFilters({ mediaStatuses: ['Has Images'], vendors: ['HP'] })
  assert.deepEqual(Array.from(sidebar.filters.value.mediaStatuses), ['Has Images'])
})

test('disabled image filters are hidden and stripped without removing review filters', () => {
  const sidebar = setupSidebar(false)
  assert.deepEqual(Array.from(sidebar.visibleMediaOptions.value, item => item.name), ['Has Reviews'])
  assert.deepEqual(Array.from(sidebar.filters.value.mediaStatuses), ['Has Reviews'])
})

const catalogSource = fs.readFileSync(new URL('../../resources/js/views/catalog/Products.vue', import.meta.url), 'utf8')
const optionsCode = catalogSource.slice(
  catalogSource.indexOf('const reviewRatingOptions = computed('),
  catalogSource.indexOf('const normalizeSearchText ='),
)
const catalogOptions = enabled => {
  const context = {
    computed, SHOW_IMAGE_FILTERS: enabled,
    products: ref([{ productId: '1' }]),
    getProductReviewStats: () => ({ total: 1, average: 5 }),
    getPrimaryImageUrl: () => '',
    mediaFacetCountsLoaded: ref(true),
    mediaFacetCounts: ref({ hasImages: 17, noImages: 23 }),
  }
  vm.runInNewContext(`${optionsCode}\nresult = reviewRatingOptions.value`, context)
  return Array.from(context.result, item => ({ ...item }))
}

test('catalog supplies image options with server facet counts only when enabled', () => {
  assert.deepEqual(catalogOptions(true).slice(-2), [
    { name: 'Has Images', count: 17 },
    { name: 'No Images', count: 23 },
  ])
  assert.equal(catalogOptions(false).some(item => item.name.includes('Images')), false)
})
