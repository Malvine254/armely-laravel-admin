import test from 'node:test'
import assert from 'node:assert/strict'
import { waitForDiscoveryContent } from '../../resources/js/services/discoveryContent.js'

test('polls pending jobs and returns the persisted product', async () => {
  const delays = []
  let requests = 0
  const result = await waitForDiscoveryContent(async () => ({
    content_pending: ++requests < 3,
    data: { productId: '12345', image_url: 'https://images.example.com/verified.jpg' },
  }), () => true, async ms => { delays.push(ms) })
  assert.equal(requests, 3)
  assert.deepEqual(delays, [10000, 10000])
  assert.equal(result.data.productId, '12345')
})

test('bounds polling and tells the user lookup continues in the background', async () => {
  let requests = 0
  await assert.rejects(waitForDiscoveryContent(async () => {
    requests++
    return { content_pending: true }
  }, () => true, async () => {}), /processed in the background/)
  assert.equal(requests, 6)
})

test('does not request content for an outdated search', async () => {
  const result = await waitForDiscoveryContent(() => assert.fail('unexpected request'), () => false)
  assert.equal(result, null)
})

test('discards a result when the search changes during a request', async () => {
  let current = true
  const result = await waitForDiscoveryContent(async () => {
    current = false
    return { content_pending: false }
  }, () => current)
  assert.equal(result, null)
})

test('surfaces request errors without retrying failures', async () => {
  const failure = new Error('Provider unavailable')
  await assert.rejects(waitForDiscoveryContent(async () => { throw failure }, () => true), failure)
})
