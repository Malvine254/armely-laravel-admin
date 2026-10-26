import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const source = await readFile(new URL('../../resources/js/pages/AdminSettingsPage.vue', import.meta.url), 'utf8')
const start = source.indexOf('const populateProductContent = async')
const end = source.indexOf('\nconst startProductsSyncWorker =', start)
const actionSource = source.slice(start, end)
const ref = value => ({ value })

const setup = ({ confirm = true, fail = false, timeout = false, stop = false } = {}) => {
  const calls = []
  const confirmations = []
  const toasts = []
  let refreshes = 0
  const fixture = {
    productContentLoading: ref(false),
    catalogWorkerLoading: ref(false),
    catalogStopLoading: ref(false),
    productContentStopRequested: ref(false),
    productContentProgress: ref({ processed: 0, ready: 0, review: 0 }),
    productContentMessage: ref(''),
    productContentHasError: ref(false),
    window: { confirm: message => { confirmations.push(message); return confirm } },
    setTimeout: callback => callback(),
    refreshProductContentStats: async () => { refreshes++ },
    api: {
      post: async (path, payload, options) => {
        calls.push({ path, payload, options })
        if (fail) throw { response: { data: { message: 'AI settings missing.' } } }
        if (timeout) throw { code: 'ECONNABORTED', message: 'timeout of 180000ms exceeded' }
        if (stop) fixture.productContentStopRequested.value = true
        const result = calls.length === 1
          ? { done: false, after_id: 7, processed: true, product_name: 'HP Monitor', result: 'needs_review',
              message: 'No verified evidence.', warnings: ['Bing returned no exact model match.'] }
          : calls.length === 2
            ? { done: false, after_id: 9, processed: true, product_name: 'HP Workstation', result: 'complete', message: 'Saved.' }
            : { done: true, after_id: 9, processed: false, message: 'Catalog scan finished.' }
        return { data: { success: true, data: result } }
      },
    },
    showToast: (message, type) => toasts.push({ message, type }),
  }
  const action = new Function(...Object.keys(fixture), `${actionSource}; return populateProductContent`)(...Object.values(fixture))
  return { ...fixture, run: (mode = 'description') => action(mode), calls, confirmations, toasts, refreshes: () => refreshes }
}

test('settings processes products directly without queue or worker calls, tracks outcomes and advances cursor', async () => {
  const f = setup()
  await f.run()
  assert.equal(f.calls.length, 3)
  assert.ok(f.calls.every(call => call.path === '/admin/products/process-content'))
  assert.deepEqual(f.calls.map(call => call.payload), [
    { mode: 'description', after_id: 0 }, { mode: 'description', after_id: 7 }, { mode: 'description', after_id: 9 },
  ])
  assert.equal(f.calls[0].options.timeout, 180000)
  assert.deepEqual(f.productContentProgress.value, { processed: 2, ready: 1, review: 1 })
  assert.match(f.productContentMessage.value, /1 products ready; 1 need research/)
  assert.match(f.productContentMessage.value, /Bing returned no exact model match/)
  assert.equal(f.productContentHasError.value, false)
  assert.equal(f.productContentLoading.value, false)
  assert.equal(f.refreshes(), 1)
})

test('a stalled cursor stops the scan instead of repeatedly processing a product', async () => {
  const f = setup()
  f.api.post = async () => ({ data: { success: true, data: { done: false, after_id: 0 } } })
  await f.run()
  assert.equal(f.productContentHasError.value, true)
  assert.match(f.productContentMessage.value, /scan did not advance/)
})

test('each button submits only its selected mode', async () => {
  for (const mode of ['description', 'images', 'repair_descriptions']) {
    const f = setup()
    await f.run(mode)
    assert.equal(f.calls[0].payload.mode, mode)
  }
})

test('timeouts stop processing and report unknown current save status, not completion', async () => {
  const f = setup({ timeout: true })
  await f.run()
  assert.equal(f.calls.length, 1)
  assert.match(f.productContentMessage.value, /save status is unknown/)
  assert.match(f.productContentMessage.value, /Earlier saved results are kept/)
  assert.equal(f.productContentHasError.value, true)
  assert.equal(f.productContentLoading.value, false)
})

test('repair explicitly confirms replacing populated AI descriptions with backups and excludes manual content', async () => {
  const f = setup()
  await f.run('repair_descriptions')
  assert.match(f.confirmations[0], /all AI-generated descriptions/)
  assert.match(f.confirmations[0], /Original and previous text are backed up/)
  assert.match(f.confirmations[0], /Manual and supplier descriptions are excluded/)
  assert.match(f.confirmations[0], /Failed repairs keep the previous text flagged for review/)
  assert.ok(f.calls.every(call => call.payload.mode === 'repair_descriptions'))
})

test('cancelled confirmation and existing operations do not submit requests', async () => {
  const cancelled = setup({ confirm: false })
  await cancelled.run()
  assert.deepEqual(cancelled.calls, [])
  for (const flag of ['productContentLoading', 'catalogWorkerLoading', 'catalogStopLoading']) {
    const f = setup()
    f[flag].value = true
    await f.run()
    assert.deepEqual(f.calls, [])
  }
})

test('request errors stay visible and stop the loop without a success message', async () => {
  const f = setup({ fail: true })
  await f.run()
  assert.equal(f.calls.length, 1)
  assert.match(f.productContentMessage.value, /AI settings missing/)
  assert.equal(f.productContentHasError.value, true)
  assert.equal(f.toasts[0].type, 'error')
})

test('stop waits for current product and prevents further requests', async () => {
  const f = setup({ stop: true })
  await f.run()
  assert.equal(f.calls.length, 1)
  assert.equal(f.productContentProgress.value.processed, 1)
  assert.match(f.productContentMessage.value, /Stopped after the current product/)
})

test('settings exposes separate actions, stop, coverage refresh and review link', () => {
  assert.match(source, /@click="populateProductContent\('description'\)"/)
  assert.match(source, /@click="populateProductContent\('repair_descriptions'\)"/)
  assert.match(source, /@click="populateProductContent\('images'\)"/)
  assert.match(source, /Stop After Current Product/)
  assert.match(source, /@click="refreshProductContentStats"/)
  assert.match(source, /to="\/admin\/products"/)
  assert.match(source, /productContentStopRequested.value = true/)
})

test('refresh shows database errors rather than replacing counts with zero', async () => {
  const statsStart = source.indexOf('const refreshProductContentStats = async')
  const statsSource = source.slice(statsStart, start)
  for (const fail of [false, true]) {
    const toasts = []
    const fixture = {
      productContentStatsLoading: ref(false),
      productContentStats: ref(null),
      api: {
        get: async () => {
          if (fail) throw new Error('Database unavailable.')
          return { data: { success: true, stats: { total: 50, without_descriptions: 10 } } }
        },
      },
      showToast: (message, type) => toasts.push({ message, type }),
    }
    const refresh = new Function(...Object.keys(fixture), `${statsSource}; return refreshProductContentStats`)(...Object.values(fixture))
    await refresh()
    assert.equal(fixture.productContentStatsLoading.value, false)
    if (fail) {
      assert.equal(fixture.productContentStats.value, null)
      assert.deepEqual(toasts, [{ message: 'Database unavailable.', type: 'error' }])
    } else {
      assert.equal(fixture.productContentStats.value.without_descriptions, 10)
    }
  }
})
