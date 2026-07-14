import api from './api'

const CONSENT_COOKIE_NAME = 'armely_cookie_consent'
const PREFERENCES_COOKIE_NAME = 'armely_cookie_preferences'
const STORAGE_KEY = 'armely_search_profile_storage'
const PREFERENCES_STORAGE_KEY = 'armely_cookie_preferences_storage'
const CONSENT_STORAGE_KEY = 'armely_cookie_consent_storage'
const MAX_TERMS = 20

const safeStorageGet = (key) => {
  if (typeof window === 'undefined') return null
  try {
    return window.localStorage.getItem(key)
  } catch {
    return null
  }
}

const safeStorageSet = (key, value) => {
  if (typeof window === 'undefined') return false
  try {
    window.localStorage.setItem(key, value)
    return true
  } catch {
    // A full or disabled storage area must never interrupt search.
    try {
      window.localStorage.removeItem(key)
    } catch {
      // Storage is unavailable.
    }
    return false
  }
}

const DEFAULT_COOKIE_PREFERENCES = {
  necessary: true,
  searchPersonalization: false,
  analytics: false,
}

const getCookieValue = (name) => {
  if (typeof document === 'undefined') return null
  const cookies = document.cookie ? document.cookie.split('; ') : []
  const cookie = cookies.find((entry) => entry.startsWith(`${name}=`))
  if (!cookie) return null
  return decodeURIComponent(cookie.split('=').slice(1).join('='))
}

const setCookieValue = (name, value, days = 365) => {
  if (typeof document === 'undefined') return
  const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString()
  document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`
}

const parseProfile = (value) => {
  if (!value) return null
  try {
    const parsed = JSON.parse(value)
    if (!parsed || !Array.isArray(parsed.terms)) return null
    return parsed
  } catch (error) {
    return null
  }
}

const parsePreferences = (value) => {
  if (!value) return null
  try {
    const parsed = JSON.parse(value)
    if (!parsed || typeof parsed !== 'object') return null
    return {
      necessary: true,
      searchPersonalization: parsed.searchPersonalization === true,
      analytics: parsed.analytics === true,
    }
  } catch (error) {
    return null
  }
}

const getDefaultProfile = () => ({
  terms: [],
  updatedAt: new Date().toISOString(),
})

let cachedProfile = getDefaultProfile()
let profileLoadPromise = null

// Remove the legacy browser profile. Profiles now live in the database.
if (typeof window !== 'undefined') {
  try {
    window.localStorage.removeItem(STORAGE_KEY)
  } catch {
    // Storage may be unavailable or full.
  }
  try {
    document.cookie = 'armely_search_profile=; Max-Age=0; path=/; SameSite=Lax'
  } catch {
    // Ignore legacy cookie cleanup failures.
  }
}

export const getCookieConsentStatus = () => {
  const cookieConsent = getCookieValue(CONSENT_COOKIE_NAME)
  if (cookieConsent === 'accepted' || cookieConsent === 'rejected') {
    return cookieConsent
  }

  if (typeof window !== 'undefined') {
    const storedConsent = safeStorageGet(CONSENT_STORAGE_KEY)
    if (storedConsent === 'accepted' || storedConsent === 'rejected') {
      return storedConsent
    }
  }

  return null
}

export const getCookiePreferences = () => {
  if (typeof window === 'undefined') return { ...DEFAULT_COOKIE_PREFERENCES }

  const cookiePrefs = parsePreferences(getCookieValue(PREFERENCES_COOKIE_NAME))
  if (cookiePrefs) {
    safeStorageSet(PREFERENCES_STORAGE_KEY, JSON.stringify(cookiePrefs))
    return cookiePrefs
  }

  const localPrefs = parsePreferences(safeStorageGet(PREFERENCES_STORAGE_KEY))
  if (localPrefs) return localPrefs

  const legacyConsent = getCookieValue(CONSENT_COOKIE_NAME)
  if (legacyConsent === 'accepted') {
    return {
      necessary: true,
      searchPersonalization: true,
      analytics: true,
    }
  }

  return { ...DEFAULT_COOKIE_PREFERENCES }
}

export const setCookiePreferences = (preferences) => {
  const normalized = {
    necessary: true,
    searchPersonalization: preferences?.searchPersonalization === true,
    analytics: preferences?.analytics === true,
  }

  const serialized = JSON.stringify(normalized)
  setCookieValue(PREFERENCES_COOKIE_NAME, serialized)

  if (typeof window !== 'undefined') {
    safeStorageSet(PREFERENCES_STORAGE_KEY, serialized)
  }

  const consent = normalized.searchPersonalization || normalized.analytics ? 'accepted' : 'rejected'
  setCookieValue(CONSENT_COOKIE_NAME, consent)

  if (typeof window !== 'undefined') {
    safeStorageSet(CONSENT_STORAGE_KEY, consent)
  }

  if (typeof window !== 'undefined') {
    window.dispatchEvent(new CustomEvent('cookie-preferences-updated', {
      detail: {
        preferences: normalized,
        consent,
      },
    }))
  }

  return normalized
}

export const hasTrackingConsent = () => getCookiePreferences().searchPersonalization === true

export const setCookieConsentStatus = (status) => {
  if (status === 'accepted') {
    setCookiePreferences({ searchPersonalization: true, analytics: true })
    return
  }

  setCookiePreferences({ searchPersonalization: false, analytics: false })
}

const normalizeProfile = (profile) => ({
  terms: (Array.isArray(profile?.terms) ? profile.terms : []).slice(0, MAX_TERMS),
  updatedAt: profile?.updatedAt || new Date().toISOString(),
})

const readProfile = () => cachedProfile

export const loadSearchProfile = async (force = false) => {
  if (!hasTrackingConsent()) {
    cachedProfile = getDefaultProfile()
    return cachedProfile
  }
  if (profileLoadPromise && !force) return profileLoadPromise

  profileLoadPromise = api.get('/search-profile')
    .then((response) => {
      cachedProfile = normalizeProfile(response.data?.data)
      return cachedProfile
    })
    .catch(() => cachedProfile)
    .finally(() => { profileLoadPromise = null })

  return profileLoadPromise
}

const normalizeTerm = (term) => (term || '').trim().replace(/\s+/g, ' ')

export const trackSearchTerm = (term) => {
  if (!hasTrackingConsent()) return

  const normalized = normalizeTerm(term)
  if (!normalized || normalized.length < 2) return

  const termKey = normalized.toLowerCase()
  const now = new Date()
  const hour = String(now.getHours())
  const day = String(now.getDay())

  const profile = readProfile()
  const existingIndex = profile.terms.findIndex((entry) => entry.termKey === termKey)

  if (existingIndex >= 0) {
    const existing = profile.terms[existingIndex]
    existing.count += 1
    existing.lastSearched = now.toISOString()
    existing.term = normalized
    existing.hourWeights = existing.hourWeights || {}
    existing.dayWeights = existing.dayWeights || {}
    existing.hourWeights[hour] = (existing.hourWeights[hour] || 0) + 1
    existing.dayWeights[day] = (existing.dayWeights[day] || 0) + 1
    profile.terms[existingIndex] = existing
  } else {
    profile.terms.push({
      term: normalized,
      termKey,
      count: 1,
      lastSearched: now.toISOString(),
      hourWeights: { [hour]: 1 },
      dayWeights: { [day]: 1 },
    })
  }

  profile.terms = profile.terms
    .sort((a, b) => {
      if (b.count !== a.count) return b.count - a.count
      return new Date(b.lastSearched) - new Date(a.lastSearched)
    })
    .slice(0, MAX_TERMS)

  profile.updatedAt = now.toISOString()
  cachedProfile = normalizeProfile(profile)

  // Tracking is intentionally non-blocking so product search is never delayed.
  api.post('/search-profile/terms', { term: normalized })
    .then((response) => {
      cachedProfile = normalizeProfile(response.data?.data)
    })
    .catch(() => {
      // Keep the in-memory profile for this page when persistence is unavailable.
    })
}

const uniqueByTerm = (items) => {
  const seen = new Set()
  return items.filter((item) => {
    if (seen.has(item.termKey)) return false
    seen.add(item.termKey)
    return true
  })
}

export const getSearchSuggestions = (input = '') => {
  if (!hasTrackingConsent()) {
    return {
      popular: [],
      recent: [],
      recommended: [],
    }
  }

  const profile = readProfile()
  const query = normalizeTerm(input).toLowerCase()
  const now = new Date()
  const hour = String(now.getHours())
  const day = String(now.getDay())

  const base = query
    ? profile.terms.filter((item) => item.termKey.includes(query))
    : profile.terms

  const popular = [...base]
    .sort((a, b) => b.count - a.count)
    .slice(0, 5)

  const recent = [...base]
    .sort((a, b) => new Date(b.lastSearched) - new Date(a.lastSearched))
    .slice(0, 5)

  const recommended = [...base]
    .sort((a, b) => {
      const scoreA = (a.hourWeights?.[hour] || 0) * 2 + (a.dayWeights?.[day] || 0) + a.count * 0.25
      const scoreB = (b.hourWeights?.[hour] || 0) * 2 + (b.dayWeights?.[day] || 0) + b.count * 0.25
      return scoreB - scoreA
    })
    .slice(0, 5)

  return {
    popular: uniqueByTerm(popular),
    recent: uniqueByTerm(recent),
    recommended: uniqueByTerm(recommended),
  }
}

export const getSearchProfileTerms = (limit = 20) => {
  if (!hasTrackingConsent()) return []

  const profile = readProfile()
  if (!Array.isArray(profile.terms) || profile.terms.length === 0) return []

  const normalizedLimit = Number.isFinite(Number(limit))
    ? Math.max(1, Math.min(50, Number(limit)))
    : 20

  return profile.terms
    .filter((entry) => entry && typeof entry.termKey === 'string' && entry.termKey.length > 1)
    .sort((a, b) => {
      if ((b.count || 0) !== (a.count || 0)) return (b.count || 0) - (a.count || 0)
      return new Date(b.lastSearched || 0) - new Date(a.lastSearched || 0)
    })
    .slice(0, normalizedLimit)
    .map((entry) => ({
      term: String(entry.term || entry.termKey),
      termKey: String(entry.termKey),
      count: Number(entry.count || 0),
      lastSearched: entry.lastSearched || null,
      hourWeights: entry.hourWeights || {},
      dayWeights: entry.dayWeights || {},
    }))
}
