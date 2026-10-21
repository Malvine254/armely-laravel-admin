import test from 'node:test'
import assert from 'node:assert/strict'

let sequence = 0
const load = async (origin, pathname, config) => {
  globalThis.window = { location: new URL(pathname, origin), storeRuntimeConfig: config }
  return import(`../../resources/js/services/runtimeConfig.js?runtime=${sequence++}`)
}

test('server-rendered environment controls the mount and API without a rebuild', async () => {
  const first = await load('https://shop.example.com', '/products', {
    VITE_APP_BASE_PATH: '/shop/',
    VITE_API_URL: '/shop/api/v1',
  })
  assert.equal(first.APP_BASE_PATH, '/shop/')
  assert.equal(first.API_BASE_URL, '/shop/api/v1')
  const second = await load('https://other.example.com', '/products', {
    VITE_APP_BASE_PATH: '/',
    VITE_API_URL: 'https://api.example.com/api/v1',
  })
  assert.equal(second.APP_BASE_PATH, '/')
  assert.equal(second.API_BASE_URL, 'https://api.example.com/api/v1')
})

test('an empty server API setting derives the API from the configured mount', async () => {
  const runtime = await load('https://shop.example.com', '/shop/products', {
    VITE_APP_BASE_PATH: '/shop/',
    VITE_API_URL: '',
  })
  assert.equal(runtime.API_BASE_URL, 'https://shop.example.com/shop/api/v1')
})

test('pages without server configuration retain runtime mount detection', async () => {
  const runtime = await load('http://localhost:8001', '/store/products', undefined)
  assert.equal(runtime.APP_BASE_PATH, '/store/')
  assert.equal(runtime.API_BASE_URL, 'http://localhost:8001/store/api/v1')
})
