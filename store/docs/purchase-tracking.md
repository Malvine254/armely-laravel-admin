# Store purchase conversion setup

Store visits emit page views only. A payment return triggers an authenticated invoice lookup; only fully paid invoices with a recorded payment date produce purchase payloads. URL parameters never establish payment. QuickBooks payment reconciliation must update invoice status, paid amount, and paid date before tracking can occur. Pending returns retry briefly and retain their URL for a later reload.

## Google configuration required

1. Disable the Google Ads purchase rule based on visiting `/store` (and any GA4 rule that creates purchases from page views). This is an account setting, not fixed by deploying code.
2. Choose one primary purchase conversion: import the GA4 `purchase` event, OR use a direct Google Ads conversion. Do not use both as primary campaign goals for the same sales.
3. GA4 uses `STORE_GA4_ID`. Direct Ads tracking uses `STORE_GOOGLE_ADS_ID` and the exact `STORE_GOOGLE_ADS_PURCHASE_CONVERSION_LABEL` from Google's event snippet. No purchase label is assumed.
4. For sales, use dynamic values, transaction IDs, and the Every counting setting. Clear Laravel config cache and rebuild frontend assets after configuration/deployment.
5. Verify in Tag Assistant: Store visit, cart, canceled payment, and unpaid/partial invoice emit no purchase. A fully paid return emits purchase with the invoice transaction ID, USD currency, and recorded amount. Reloading must not count another sale.

GA4 value excludes tax and shipping; Ads value is the full invoice total. GA4 currently reports one summary item per invoice. Bulk checkout produces one transaction per paid invoice. Local storage suppresses repeat events; stable transaction IDs provide Google-side deduplication. Ad blockers and customers who never return from hosted checkout can prevent browser tracking. A server-side integration would be needed to cover those cases.

References: https://developers.google.com/analytics/devguides/collection/ga4/ecommerce and https://support.google.com/google-ads/answer/16560108
