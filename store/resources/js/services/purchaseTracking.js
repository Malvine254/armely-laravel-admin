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

// A submitted quote is a much stronger buying signal than a page visit, so
// track it as its own event (GA4 generate_lead + an Ads conversion) rather
// than letting page-view tracking be the only signal Google Ads sees.
export function trackQuoteSubmission(quote, browser = window) {
  if (typeof browser.gtag !== 'function') return
  if (!quote?.quote_id) return
  const config = browser.storeTracking || {}
  const destinations = [
    [config.ga4Id, 'generate_lead'],
    [config.adsQuoteDestination, 'conversion'],
  ]
  for (const [destination, event] of destinations) {
    if (!destination) continue
    const key = `store-quote:${destination}:${quote.quote_id}`
    try { if (browser.localStorage.getItem(key)) continue } catch { /* Google also deduplicates below. */ }
    browser.gtag('event', event, {
      send_to: destination,
      event_category: 'engagement',
      event_label: 'quote_submit',
      transaction_id: String(quote.quote_id),
      value: Number.isFinite(quote.value) ? quote.value : undefined,
      currency: quote.value !== undefined ? 'USD' : undefined,
    })
    try { browser.localStorage.setItem(key, '1') } catch { /* Storage may be disabled. */ }
  }
}
