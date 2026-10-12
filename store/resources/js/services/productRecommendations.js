import { isSupplierOrderable } from './productAvailability.js'

const productId = product => String(product?.productId ?? '').trim()
const recordsFrom = response => {
  const data = response?.data?.data
  if (response?.data?.success === false) throw new Error(response.data.message || 'Could not load recommendations')
  if (Array.isArray(data)) return data
  if (Array.isArray(data?.records)) return data.records
  throw new Error('Invalid recommendation response')
}

export const selectSearchInterests = (terms = [], now = Date.now()) => {
  const seen = new Set()
  return (Array.isArray(terms) ? terms : [])
    .map(entry => {
      const term = String(entry?.term || entry?.termKey || '').trim().replace(/\s+/g, ' ')
      const searchedAt = Date.parse(entry?.lastSearched || '')
      const ageDays = (now - searchedAt) / 86400000
      const count = Math.max(1, Math.min(10, Number(entry?.count) || 1))
      return { term, ageDays, score: (1 + Math.log2(count)) / (1 + Math.max(0, ageDays) / 14) }
    })
    .filter(entry => entry.term.length >= 2 && entry.term.length <= 255 && Number.isFinite(entry.ageDays) && entry.ageDays <= 90)
    .sort((left, right) => right.score - left.score || left.ageDays - right.ageDays)
    .filter(entry => {
      const key = entry.term.toLowerCase()
      if (seen.has(key)) return false
      seen.add(key)
      return true
    })
    .slice(0, 3)
    .map(entry => entry.term)
}

const isRecommendable = product => productId(product)
  && product?.discontinueProduct !== true
  && product?.isAvailable !== false
  && Number(product?.productPrice?.[0]?.rsPrice) > 0
  && (
    Number(product?.availableQuantity ?? product?.totalQuantity ?? product?.quantity ?? product?.qty) !== 0
    || isSupplierOrderable(product)
  )

export const mergeRecommendationGroups = (groups, excludedIds = [], limit = 8) => {
  const seen = new Set(excludedIds.map(String))
  const products = []
  const depth = Math.max(0, ...groups.map(group => group.length))
  // Interleave interests so one broad search cannot consume every suggestion.
  for (let index = 0; index < depth && products.length < limit; index++) {
    for (const group of groups) {
      const product = group[index]
      const id = productId(product)
      if (!isRecommendable(product) || seen.has(id)) continue
      seen.add(id)
      products.push(product)
      if (products.length === limit) break
    }
  }
  return products
}

export const loadProductRecommendations = async ({
  get,
  searchTerms = [],
  viewedIds = [],
  favoriteIds = [],
  cartIds = [],
  personalizationEnabled = false,
}) => {
  const searches = personalizationEnabled ? selectSearchInterests(searchTerms) : []
  const seeds = personalizationEnabled
    ? [...new Set([...cartIds, ...favoriteIds, ...viewedIds].map(String).filter(id => /^\d+$/.test(id)))].slice(0, 2)
    : []
  const personalized = searches.length > 0 || seeds.length > 0
  const params = { hide_zero_price: true, catalog_clean: true, page: 1, per_page: 16 }
  const requests = [
    ...searches.map(search => () => get('/products', { params: { ...params, search } })),
    ...seeds.map(id => () => get(`/products/${encodeURIComponent(id)}/related`)),
  ]
  if (!personalized) requests.push(() => get('/products', { params }))
  const responses = await Promise.allSettled(requests.map(async request => recordsFrom(await request())))
  const failures = responses.filter(response => response.status === 'rejected')
  if (failures.length === responses.length) throw failures[0].reason
  for (const failure of failures) console.warn('Recommendation source failed:', failure.reason)
  const groups = responses.filter(response => response.status === 'fulfilled').map(response => response.value)
  return {
    products: mergeRecommendationGroups(groups, personalizationEnabled ? cartIds : []),
    personalized,
    basedOnSearch: searches.length > 0,
    partialFailure: failures.length > 0,
  }
}
