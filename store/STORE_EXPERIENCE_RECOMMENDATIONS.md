# Store Experience Improvement Recommendations

## Objective
Bring the store experience closer to high-performing ecommerce flows (AliExpress-style lifecycle engagement) while preserving B2B pricing and quote controls.

## Guiding Principles
- Persist behavior server-side, not only in browser storage.
- Never email inferred or fabricated prices.
- Separate operational emails from lifecycle marketing emails.
- Add idempotency and cooldowns to all automated sends.
- Keep consent and unsubscribe controls server-side and auditable.

## Current Gaps

### 1) Behavioral Tracking Is Mostly Client-Only
- Recently viewed products are browser-local only.
- Favorites are browser-local only.
- Cart state is browser-local only.

Impact:
- Cross-device behavior is lost.
- No reliable backend trigger source for reminders.

### 2) Price-Drop Alerts Are Missing
- Price history exists, but there is no customer subscription model.
- No "watch this item" or "notify me on price drop" workflow.

Impact:
- Revenue opportunities from intent reactivation are lost.

### 3) Lifecycle Campaigns Are Missing
- No abandoned-cart sequence.
- No viewed-item reminder sequence.
- No browse/favorite reminder sequence.

Impact:
- Lower conversion from high-intent sessions.

### 4) Email Preference Model Is Incomplete
- User notification preferences are mostly local UI preferences.
- No robust server-side marketing preference records per user.

Impact:
- Compliance/deliverability risk when scaling campaigns.

### 5) Scheduler/Queue Reliability Is Underdocumented
- Core automations rely on queue workers and scheduler health.
- Runbook-level operational guidance is not clearly documented.

Impact:
- Existing jobs may fail silently in production.

## Architecture Recommendations

### A. Persist Intent Events (Phase 1)
Create backend event streams and snapshots for:
- Product views
- Cart mutations
- Favorites mutations
- Search intent (already partially present)

Suggested tables:
- user_product_views
- user_cart_snapshots
- user_cart_events
- user_favorite_events

Requirements:
- Support guest and authenticated identities.
- Merge guest history into user on login.
- Add retention policy and archive strategy.

### B. Add Alert Subscriptions (Phase 2)
Create customer opt-in tables and APIs:
- price_alert_subscriptions
- reminder_subscriptions (cart, viewed, favorites)

Key fields:
- user_id or visitor identity
- product_id
- trigger_type
- baseline_price
- min_drop_percent / min_drop_amount
- cooldown_minutes
- is_active

### C. Automation Jobs (Phase 3)
Add queue jobs:
- ProcessPriceDropAlertsJob
- ProcessAbandonedCartRemindersJob
- ProcessViewedItemRemindersJob
- ProcessFavoriteItemRemindersJob

Controls:
- Idempotency keys per campaign window.
- Per-user send caps.
- Quiet hours by timezone.

### D. Campaign Templates (Phase 4)
Create dedicated lifecycle templates:
- Cart reminder: 2h, 24h, 72h sequence.
- Viewed item reminder: 24h follow-up.
- Price drop alert: near-real-time or daily digest.

Template rules:
- Include exact stored prices only.
- Include current price, previous price, and change delta where relevant.
- Include unsubscribe and preference links.

### E. Server-Side Preferences and Compliance (Phase 5)
Add:
- email_preferences table
- suppression_events table
- unsubscribe tokens and endpoints

Minimum flags:
- transactional_enabled
- marketing_enabled
- price_alerts_enabled
- cart_reminders_enabled
- browse_reminders_enabled

## Email Accuracy Standards (Must Enforce)

### Rule 1: Never Invent Item-Level Pricing
If unit price or line total is missing from the source record:
- Show "Unavailable" for that line value.
- Keep invoice/quote total from persisted financial totals.
- Do not distribute totals across items heuristically.

### Rule 2: Snapshot Wins Over Live Catalog
For quote/order/invoice emails:
- Prefer stored line items and totals from quote/order/invoice records.
- Do not re-price from current product catalog for customer-facing financial emails.

### Rule 3: Deterministic Totals
- Total displayed in email must equal stored record totals.
- If discrepancy is detected, show a warning internally (logs/monitoring), not guessed values to customers.

### Rule 4: Currency Consistency
- Use one currency per email record.
- Always format with two decimals.

## Suggested Execution Order
1. Implement email accuracy hardening first.
2. Persist viewed/cart/favorite behavior server-side.
3. Add subscription model for alerts/reminders.
4. Add campaign jobs with idempotency and cooldown.
5. Add preference center and unsubscribe controls.
6. Add monitoring dashboards and operational runbooks.

## Definition of Done Per Phase
- Feature flags available.
- Unit/feature tests for trigger logic and suppression logic.
- Delivery logs include reason, source event, and idempotency key.
- Dashboard shows sent, skipped, suppressed, failed counts.

## Notes For Implementation
Use this file as the source checklist before each change set. Every PR should reference:
- Which section it implements.
- Which email accuracy rules were validated.
- What tests prove correctness.
