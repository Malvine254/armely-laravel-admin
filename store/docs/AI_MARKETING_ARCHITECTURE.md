# AI Marketing Architecture Assessment

## Existing system

Armely Store is a separate Laravel 12 application under `store/`. It already
provides:

- First-party activity data in `user_product_views`, `user_cart_snapshots`,
  `user_cart_events`, `user_favorite_events`, `search_profiles`, and
  `reminder_subscriptions`.
- Customer/order/product relationships through `users`, `orders`,
  `order_line_items`, and `products`.
- Marketing preferences, scoped unsubscribe tokens, suppression events, and
  idempotency markers through `email_preferences`, `unsubscribe_tokens`, and
  `suppression_events`.
- Azure Graph email delivery in `AzureGraphMailService`, Azure OpenAI in
  `AzureOpenAiChatService`, database-backed queue jobs, and scheduled lifecycle
  work in `store/bootstrap/app.php`.
- Admin APIs and a Vue admin dashboard.

Automated lifecycle messages already exist. The scheduled
`ProcessReminderSubscriptionsJob` evaluates abandoned-cart, product-view,
favorite, and search reminders every 30 minutes; price-drop alerts run hourly.
These are individual workflows, not a general AI campaign engine. Their current
gates include active subscriptions, account status, existing email
preferences, quiet hours, cooldowns, product/cart state, quote checks,
idempotency, and send caps. A particular message may be skipped by any of
those checks. The codebase does not establish that all automatic promotional
email is absent.

## Integration plan

1. Leave the existing lifecycle jobs and transactional mail untouched.
2. Add a separate marketing module behind independent flags. Default every new
   campaign and all automated sending to disabled.
3. Require a distinct customer opt-in for AI-personalized marketing; the
   existing lifecycle marketing preference alone is insufficient because its
   current default is enabled.
4. Build profiles and recommendations only from existing, permitted store
   data. Resolve product IDs and availability/prices from the catalog after any
   AI ranking; AI output cannot authorize sending.
5. Persist campaign decisions and delivery attempts separately from the
   existing lifecycle suppression log. Use unique idempotency keys and database
   reservations for concurrent workers.
6. Reuse the existing email preference, unsubscribe, suppression, Azure OpenAI,
   Azure Graph, queue, and scheduler facilities. Keep this module in dry-run
   mode until approval and delivery workflows are implemented and explicitly
   enabled.
7. Add admin reporting/control endpoints and UI only after authorization and
   data boundaries are defined; add focused tests for hard eligibility rules,
   races, invalid model output, and feature-flag behavior.

## Compatibility and rollout

Use additive migrations, separate tables, and configuration defaults that
cannot send mail. Do not change current customer consent defaults, lifecycle
email behavior, transactional messages, catalog pricing, or existing API
contracts as part of this module. Start with dry-run recommendations, review
results, test approved sends, and only then consider enabling scheduled sends.
Use the global feature flag as a kill switch. Existing scheduler, queue, and
provider failures should be reported explicitly and must not fall through to a
success-shaped campaign result.

## Initial implementation boundary

The initial code slice adds campaign/profile/decision/email-history storage,
an explicit AI-marketing consent control, deterministic product-interest
evaluation, a 30-minute feature-gated evaluation command, and a dry-run audit
record. It excludes previously viewed products and requires active, verified,
consenting customers plus valid in-stock catalog prices. The evaluator blocks
sending even if the auto-send flag is set.

OpenAI campaign generation/ranking, delivery jobs and provider callbacks,
approval and campaign-management UI, decision retries, conversions/clicks,
and full campaign analytics are not implemented in this slice. Do not enable
production automation based on this foundation alone.

## Existing lifecycle send-marker repair

The existing lifecycle idempotency reason is `idem:` plus a full SHA-256
hexadecimal digest (69 characters). The original `suppression_events.reason`
column allowed only 64 characters, causing strict MySQL to reject the marker
after Microsoft Graph had already accepted the email. The additive
`2026_10_21_190000_widen_suppression_event_reason` migration expands it to 96
characters without changing marker keys. Its rollback deliberately retains
the wider column to avoid truncating existing duplicate-send protection.
