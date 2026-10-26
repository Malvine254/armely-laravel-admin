import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const source = await readFile(new URL('../../resources/js/pages/AdminSettingsPage.vue', import.meta.url), 'utf8')
const start = source.indexOf('const populateProductContent = async')
const end = source.indexOf('\nconst startProductsSyncWorker =', start)
const actionSource = source.slice(start, end)
const ref = value => ({ value })

const setup = ({ confirm = true, failQueue = false, failWorker = false, unsuccessfulWorker = false } = {}) => {
  const calls = []
  const toasts = []
  const fixture = {
    productContentLoading: ref(false),
    catalogWorkerLoading: ref(false),
    catalogStopLoading: ref(false),
    productContentMessage: ref(''),
    productContentHasError: ref(false),
    window: { confirm: () => confirm },
    api: {
      post: async path => {
        calls.push(path)
        if (path === '/admin/products/enrich-content' && failQueue) {
          throw { response: { data: { message: 'AI settings missing.' } } }
        }
        if (path.endsWith('/start-worker')) {
          if (failWorker) throw { response: { data: { message: 'Worker launch unavailable.' } } }
          if (unsuccessfulWorker) return { data: { success: false, message: 'Worker launch rejected.' } }
          return { data: { success: true, message: 'Worker started (PID 123).' } }
        }
        return { data: { success: true } }
      },
    },
    showToast: (message, type) => toasts.push({ message, type }),
  }
  const run = new Function(...Object.keys(fixture), `${actionSource}; return populateProductContent`)(...Object.values(fixture))
  return { ...fixture, run, calls, toasts }
}

test('settings queues catalog enrichment before starting the worker and does not claim completion', async () => {
  const f = setup()
  await f.run()
  assert.deepEqual(f.calls, ['/admin/products/enrich-content', '/admin/settings/catalog/start-worker'])
  assert.match(f.productContentMessage.value, /queued/)
  assert.match(f.productContentMessage.value, /Refresh content counts/)
  assert.equal(f.productContentHasError.value, false)
  assert.equal(f.productContentLoading.value, false)
  assert.equal(f.toasts[0].type, 'success')
})

test('cancelled confirmation and duplicate/stop/worker activity do not submit requests', async () => {
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

test('queue errors do not start a worker and remain visible', async () => {
  const f = setup({ failQueue: true })
  await f.run()
  assert.deepEqual(f.calls, ['/admin/products/enrich-content'])
  assert.equal(f.productContentMessage.value, 'AI settings missing.')
  assert.equal(f.productContentHasError.value, true)
  assert.equal(f.productContentLoading.value, false)
  assert.equal(f.toasts[0].type, 'error')
})

test('worker errors explain that enrichment remains queued rather than implying successful population', async () => {
  for (const options of [{ failWorker: true }, { unsuccessfulWorker: true }]) {
    const f = setup(options)
    await f.run()
    assert.equal(f.calls.length, 2)
    assert.match(f.productContentMessage.value, /queued, but worker startup failed/)
    assert.match(f.productContentMessage.value, /Start Products Worker/)
    assert.match(f.productContentMessage.value, /No completed population is confirmed/)
    assert.equal(f.productContentHasError.value, true)
    assert.equal(f.productContentLoading.value, false)
  }
})

test('settings exposes the content action, coverage refresh and product results link in Catalog Ops', () => {
  assert.match(source, /activeTab === 'catalog'/)
  assert.match(source, /@click="populateProductContent"/)
  assert.match(source, /Populate Missing Descriptions & Images/)
  assert.match(source, /@click="refreshProductContentStats"/)
  assert.match(source, /to="\/admin\/products"/)
})

test('refresh loads current database content counts and surfaces failures without replacing counts with zero', async () => {
  const statsStart = source.indexOf('const refreshProductContentStats = async')
  const statsSource = source.slice(statsStart, start)
  for (const fail of [false, true]) {
    const requests = []
    const toasts = []
    const fixture = {
      productContentStatsLoading: ref(false),
      productContentStats: ref(null),
      api: {
        get: async (path, options) => {
          requests.push({ path, options })
          if (fail) throw new Error('Database unavailable.')
          return { data: { success: true, stats: { total: 50, without_descriptions: 10, without_images: 5 } } }
        },
      },
      showToast: (message, type) => toasts.push({ message, type }),
    }
    const refresh = new Function(...Object.keys(fixture), `${statsSource}; return refreshProductContentStats`)(...Object.values(fixture))
    await refresh()
    assert.deepEqual(requests, [{ path: '/admin/products', options: { params: { per_page: 10 } } }])
    assert.equal(fixture.productContentStatsLoading.value, false)
    if (fail) {
      assert.equal(fixture.productContentStats.value, null)
      assert.deepEqual(toasts, [{ message: 'Database unavailable.', type: 'error' }])
    } else {
      assert.equal(fixture.productContentStats.value.without_descriptions, 10)
      assert.deepEqual(toasts, [])
    }
  }
})
