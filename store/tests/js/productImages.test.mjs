import test from 'node:test'
import assert from 'node:assert/strict'

const loadRuntime = async (origin, pathname) => {
  const url = new URL(pathname, origin)
  globalThis.window = { location: url }
  return import(`../../resources/js/services/runtimeConfig.js?case=${encodeURIComponent(url.href)}`)
}

test('local store images use the active mount rather than production URLs', async () => {
  const runtime = await loadRuntime('http://localhost:8080', '/store/')
  assert.equal(runtime.resolveProductImageUrl('https://armely.com/store/images/products/123.jpg'), 'http://localhost:8080/store/images/products/123.jpg')
  assert.equal(runtime.resolveProductImageUrl('/images/products/123.jpg'), 'http://localhost:8080/store/images/products/123.jpg')
  assert.equal(runtime.resolveProductImageUrl('/store/images/products/123.jpg'), 'http://localhost:8080/store/images/products/123.jpg')
})

test('nested XAMPP and production mounts avoid double store prefixes', async () => {
  const local = await loadRuntime('http://localhost', '/armely-laravel-admin/store/public/')
  assert.equal(local.resolveProductImageUrl('images/products/123.jpg'), 'http://localhost/armely-laravel-admin/store/public/images/products/123.jpg')
  const production = await loadRuntime('https://armely.com', '/store/')
  assert.equal(production.resolveProductImageUrl('/images/products/123.jpg'), 'https://armely.com/store/images/products/123.jpg')
})

test('supplier image paths are not rewritten to nonexistent local files', async () => {
  const runtime = await loadRuntime('http://localhost:8081', '/store/')
  const supplier = 'https://cdn.example.com/images/products/123.jpg'
  assert.equal(runtime.resolveProductImageUrl(supplier), supplier)
})

test('image extraction skips empty entries, accepts path payloads and deduplicates', async () => {
  const runtime = await loadRuntime('http://localhost:8082', '/store/')
  assert.deepEqual(runtime.getProductImageUrls({
    productImages: [{}, { imageUrl: 'https://cdn.example.com/images/products/product.jpg' }],
    images: [{ imagePath: 'images/products/123.jpg' }],
    image_url: '/store/images/products/123.jpg',
  }), [
    'http://localhost:8082/store/images/products/123.jpg',
    'https://cdn.example.com/images/products/product.jpg',
  ])
  assert.deepEqual(runtime.getProductImageUrls({ productImages: {}, images: null }), [])
})
