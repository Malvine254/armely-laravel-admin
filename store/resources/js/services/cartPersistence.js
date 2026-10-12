const COOKIE_PREFIX = 'armely_guest_cart'
const CHUNK_SIZE = 3000

export const cartLines = items => items.map(item => ({
  productId: String(item.productId),
  quantity: Math.max(1, Math.min(9999, Math.trunc(Number(item.quantity) || 1))),
}))

export const mergeCartItems = (...groups) => {
  const items = new Map()
  for (const group of groups) {
    for (const item of group) {
      const id = String(item?.productId ?? '').trim()
      if (!id) continue
      const previous = items.get(id)
      items.set(id, { ...previous, ...item, productId: id, quantity: Math.max(Number(previous?.quantity) || 0, Number(item.quantity) || 1) })
    }
  }
  return [...items.values()]
}

const cookie = name => document.cookie.split('; ').find(value => value.startsWith(`${name}=`))?.slice(name.length + 1)
const writeCookie = (name, value, maxAge = 2592000) => {
  document.cookie = `${name}=${value}; Max-Age=${maxAge}; Path=/; SameSite=Lax${window.location.protocol === 'https:' ? '; Secure' : ''}`
}

export const readGuestCartCookie = () => {
  const count = Number(cookie(COOKIE_PREFIX))
  if (cookie(COOKIE_PREFIX) === undefined) return null
  if (!Number.isInteger(count) || count < 1 || count > 20) throw new Error('Invalid guest cart cookie chunk count')
  try {
    let encoded = ''
    for (let index = 0; index < count; index++) {
      const chunk = cookie(`${COOKIE_PREFIX}_${index}`)
      if (chunk === undefined) throw new Error('Guest cart cookie is incomplete')
      encoded += chunk
    }
    const items = JSON.parse(decodeURIComponent(encoded))
    if (!Array.isArray(items) || items.length > 300 || items.some(item =>
      typeof item?.productId !== 'string' || !item.productId.trim() || item.productId.length > 100
      || !Number.isInteger(item.quantity) || item.quantity < 1 || item.quantity > 9999
    ) || new Set(items.map(item => item.productId)).size !== items.length) throw new Error('Invalid guest cart cookie')
    return items
  } catch (error) {
    console.error('Could not restore guest cart cookie:', error)
    throw error
  }
}

export const writeGuestCartCookie = items => {
  const encoded = encodeURIComponent(JSON.stringify(cartLines(items)))
  const count = Math.ceil(encoded.length / CHUNK_SIZE)
  if (count > 20) throw new Error('Guest cart is too large to save in cookies')
  const previousCount = Math.min(Number(cookie(COOKIE_PREFIX)) || 0, 20)
  for (let index = 0; index < count; index++) writeCookie(`${COOKIE_PREFIX}_${index}`, encoded.slice(index * CHUNK_SIZE, (index + 1) * CHUNK_SIZE))
  writeCookie(COOKIE_PREFIX, String(count))
  for (let index = count; index < previousCount; index++) writeCookie(`${COOKIE_PREFIX}_${index}`, '', 0)
  if (cookie(COOKIE_PREFIX) !== String(count)) throw new Error('Cookies are disabled; your guest cart could not be saved')
  for (let index = 0; index < count; index++) {
    if (cookie(`${COOKIE_PREFIX}_${index}`) !== encoded.slice(index * CHUNK_SIZE, (index + 1) * CHUNK_SIZE)) {
      throw new Error('Guest cart cookies could not be fully saved')
    }
  }
}

export const clearGuestCartCookie = () => {
  const count = Number(cookie(COOKIE_PREFIX)) || 0
  for (let index = 0; index < Math.min(count, 20); index++) writeCookie(`${COOKIE_PREFIX}_${index}`, '', 0)
  writeCookie(COOKIE_PREFIX, '', 0)
}

export const cartResponse = response => {
  const data = response?.data?.data
  if (response?.data?.success !== true || !Array.isArray(data?.items) || !Number.isInteger(data?.revision)) {
    throw new Error('Invalid saved-cart response')
  }
  return data
}
