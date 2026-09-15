// Call only with purchases verified by the authenticated server endpoint.
export function trackVerifiedPurchases(purchases, browser = window) {
  if (typeof browser.gtag !== 'function') return
  const config = browser.storeTracking || {}
  for (const purchase of purchases) {
    if (!purchase.transaction_id || !Number.isFinite(purchase.value) || !purchase.currency) continue
    const destinations = [
      [config.ga4Id, 'purchase', purchase.value],
      [config.adsPurchaseDestination, 'conversion', purchase.total],
    ]
    for (const [destination, event, value] of destinations) {
      if (!destination) continue
      const key = `store-purchase:${destination}:${purchase.transaction_id}`
      try { if (browser.localStorage.getItem(key)) continue } catch { /* Google also deduplicates transaction IDs. */ }
      const { total, ...payload } = purchase
      browser.gtag('event', event, { ...payload, value, send_to: destination })
      try { browser.localStorage.setItem(key, '1') } catch { /* Storage may be disabled. */ }
    }
  }
}
