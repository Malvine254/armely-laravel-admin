const FILTER_SEGMENTS = {
  q: 'search',
  vendor: 'vendor',
  vendors: 'vendors',
  category: 'category',
  minPrice: 'min-price',
  maxPrice: 'max-price',
  partNumber: 'part-number',
  productType: 'product-type',
  lifecycle: 'lifecycle',
  media: 'media',
  page: 'page',
}

const SEGMENT_FILTERS = Object.fromEntries(
  Object.entries(FILTER_SEGMENTS).map(([key, segment]) => [segment, key])
)

export const buildProductsLocation = (filters = {}) => {
  const filterPath = []
  const searchQuery = String(filters.q || '').trim()

  Object.entries(FILTER_SEGMENTS).forEach(([key, segment]) => {
    if (key === 'q') return
    const rawValue = filters[key]
    if (rawValue === undefined || rawValue === null || String(rawValue).trim() === '') return
    filterPath.push(segment, String(rawValue))
  })

  const query = searchQuery ? { q: searchQuery } : undefined

  return filterPath.length > 0
    ? { name: 'products-filter', params: { filterPath }, query }
    : { name: 'products', query }
}

export const parseProductsRouteFilters = route => {
  const rawPath = route?.params?.filterPath
  const parts = Array.isArray(rawPath)
    ? rawPath.map(String)
    : String(rawPath || '').split('/').filter(Boolean)
  const filters = {}

  for (let index = 0; index < parts.length - 1; index += 2) {
    const key = SEGMENT_FILTERS[parts[index]]
    if (key) filters[key] = parts[index + 1]
  }

  return { ...filters, ...(route?.query || {}) }
}
