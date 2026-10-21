import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const source = await readFile(new URL('../../resources/js/stores/authStore.js', import.meta.url), 'utf8')
const section = source.slice(source.indexOf('  let refreshInFlight ='), source.indexOf('  let _statusPollTimer ='))
const setup = () => {
  const requests = []
  const state = { user: { name: 'Existing' }, logouts: 0 }
  const token = { value: 'session-one' }
  const axios = { get: (url, options) => {
    let resolve, reject
    const promise = new Promise((yes, no) => { resolve = yes; reject = no })
    requests.push({ url, options, resolve, reject })
    return promise
  } }
  const refreshUser = new Function('token', 'axios', 'API_BASE_URL', 'setUser', 'setRestricted', 'logout', 'console',
    `${section}\nreturn refreshUser`)(token, axios, '/api/v1', user => { state.user = user }, () => {},
    async () => { state.logouts++; token.value = null }, { error: () => {} })
  return { refreshUser, requests, state, token }
}

test('shares overlapping auth refreshes and bounds the HTTP request', async () => {
  const { refreshUser, requests, state } = setup()
  const first = refreshUser()
  assert.equal(refreshUser(), first)
  assert.equal(requests.length, 1)
  assert.equal(requests[0].options.timeout, 15000)
  requests[0].resolve({ data: { success: true, data: { name: 'Updated' } } })
  assert.equal(await first, true)
  assert.equal(state.user.name, 'Updated')
})

test('backs off transient 500s without logging out or discarding the user', async () => {
  const { refreshUser, requests, state } = setup()
  const first = refreshUser()
  requests[0].reject({ response: { status: 500 } })
  assert.equal(await first, false)
  assert.equal(await refreshUser(), false)
  assert.equal(requests.length, 1)
  assert.equal(state.logouts, 0)
  assert.equal(state.user.name, 'Existing')
})

test('ignores old-session responses and allows a new session immediately', async () => {
  const { refreshUser, requests, state, token } = setup()
  const first = refreshUser()
  token.value = 'session-two'
  const second = refreshUser()
  requests[0].resolve({ data: { success: true, data: { name: 'Old' } } })
  assert.equal(await first, false)
  assert.equal(state.user.name, 'Existing')
  requests[1].resolve({ data: { success: true, data: { name: 'New' } } })
  assert.equal(await second, true)
  assert.equal(state.user.name, 'New')
})

test('still logs out an unauthorized session', async () => {
  const { refreshUser, requests, state } = setup()
  const first = refreshUser()
  requests[0].reject({ response: { status: 401 } })
  assert.equal(await first, false)
  assert.equal(state.logouts, 1)
})
