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

const categoryKey = value => String(value || '').trim().toLowerCase()
const categorySlug = value => categoryKey(value).replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')

export const categoryFilterMatches = (category, selection) => {
  const key = categoryKey(selection)
  return key !== '' && [category?.name, category?.value, category?.slug, category?.segment_code, categorySlug(category?.name)]
    .some(value => categoryKey(value) === key)
}

export const resolveCategoryFilterValue = (selection, categories = []) => {
  const category = categories.find(item => categoryFilterMatches(item, selection))
  return String(category?.value || selection || '').trim()
}

export const categoryFilterLabel = (selection, categories = []) => {
  return categories.find(item => categoryFilterMatches(item, selection))?.name || String(selection || '')
}

export const buildProductsLocation = (filters = {}) => {
  const filterPath = []
  const searchQuery = String(filters.q || '').trim()
  const vendorValues = filters.vendors !== undefined
    ? String(filters.vendors || '').split(',').map(value => value.trim()).filter(Boolean)
    : (filters.vendor !== undefined ? [String(filters.vendor).trim()].filter(Boolean) : [])

  Object.entries(FILTER_SEGMENTS).forEach(([key, segment]) => {
    if (key === 'q' || key === 'vendor' || key === 'vendors') return
    const rawValue = filters[key]
    if (rawValue === undefined || rawValue === null || String(rawValue).trim() === '') return
    filterPath.push(segment, String(rawValue))
  })

  if (vendorValues.length === 1) {
    filterPath.unshift('vendor', vendorValues[0])
  } else if (vendorValues.length > 1) {
    filterPath.unshift('vendors', vendorValues.join(','))
  }

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
