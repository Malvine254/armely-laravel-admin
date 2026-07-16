# Armely Store Project Roadmap

This document records recommended improvements for future implementation. It is a planning backlog, not a statement that the listed work is already complete.

## Guiding Priorities

1. Protect customer, payment, supplier, and administrative workflows.
2. Document and test the existing platform before expanding it.
3. Complete the supplier lifecycle with returns and RMA support.
4. Simplify the architecture so future changes are safer.
5. Add high-value B2B purchasing features and storefront polish.

## UX Additions With the Highest Value

### 1. Quick Order

Build a genuine Quick Order screen for procurement teams.

- Accept pasted SKU and quantity rows.
- Support CSV upload and downloadable templates.
- Validate duplicates, unknown SKUs, quantity limits, stock, and pricing.
- Let users correct invalid rows without losing valid entries.
- Add all valid items to a cart or quote in one action.

Completion criteria: a buyer can submit a large SKU list, resolve validation problems, and create a cart or quote without searching for products individually.

### 2. Product Comparison

- Compare specifications, price, stock, warranty, and lead time.
- Highlight meaningful differences.
- Support shareable and printable comparisons.
- Preserve selected products while navigating the catalog.

Completion criteria: buyers can compare several products side by side and move selected products into a cart or quote.

### 3. Saved Purchasing Lists and Reordering

- Allow users to create and share named purchasing lists.
- Add reorder-from-history actions for previous quotes and orders.
- Support recurring-order templates and reminders.
- Preserve requested quantities and relevant configuration notes.

Completion criteria: returning buyers can rebuild common orders with minimal data entry.

### 4. Quote Collaboration

- Add buyer and admin comments.
- Record revision history and approval history.
- Show who changed pricing, quantities, shipping, and expiration dates.
- Produce downloadable, versioned quote documents.
- Notify participants when action is required.

Completion criteria: every quote has an auditable timeline and earlier versions remain available.

### 5. Stock Freshness and Delivery Estimates

- Display when stock and price were last synchronized.
- Distinguish live, recently cached, delayed, and unavailable supplier data.
- Show estimated ship and delivery dates where supported.
- Explain warehouse or backorder status in plain language.

Completion criteria: users can understand how current the availability information is before submitting a quote or order.

### 6. RMA and Returns Center

- Start return requests from eligible order line items.
- Capture reason, quantity, serial number, condition, notes, and attachments.
- Enforce return windows and non-returnable product rules.
- Add admin approval and rejection workflows.
- Integrate TD SYNNEX RMA creation and status APIs.
- Track return shipping, refunds, credits, and partial returns.
- Notify customers whenever return status changes.

Completion criteria: customers and admins can manage a return from request through supplier resolution without relying on an offline process.

### 7. Persistent Catalog Filter Summary

- Clearly display every active filter.
- Provide reliable per-filter removal and Clear All behavior.
- Show a clear `3,000 products` state when the default catalog is unfiltered.
- Keep the URL, sidebar, result count, and pagination synchronized.
- Prevent hidden development-only filters from affecting production results.

Completion criteria: clearing filters always restores the default catalog and its expected total.

### 8. Recently Viewed Items and Saved Searches

- Add a recently viewed product history.
- Allow authenticated users to save searches and filter combinations.
- Notify users when saved-search stock or pricing materially changes.
- Provide controls to clear history and disable personalization.

Completion criteria: users can return to prior research and monitor purchasing needs without rebuilding searches.

### 9. Accessibility

- Ensure complete keyboard navigation.
- Add visible and consistent focus states.
- Test color contrast and text scaling.
- Add screen-reader names and useful status announcements.
- Manage focus in modals, menus, validation errors, and route changes.
- Test against WCAG 2.2 AA expectations.

Completion criteria: core catalog, cart, quote, checkout, account, and admin workflows can be completed with a keyboard and common screen readers.

### 10. Consistent Loading, Empty, and Error States

- Create reusable skeleton loaders.
- Standardize empty states and recovery actions.
- Use consistent toast severity, wording, duration, and placement.
- Provide retry actions for recoverable failures.
- Preserve user input when requests fail.
- Distinguish no results from service or synchronization failures.

Completion criteria: every asynchronous workflow communicates loading, success, empty, and failure states consistently.

## Recommended Implementation Order

### Phase 1: Security Hardening

- Secure the public image proxy with host allowlists, private-address blocking, MIME validation, size limits, redirect validation, and throttling.
- Remove or restrict the public supplier debug endpoint.
- Add tailored throttling to authentication, sharing, assistant, payment, and other sensitive endpoints.
- Centralize admin-role and permission enforcement in middleware and policies.
- Replace client-visible exception details with safe messages and correlation IDs.

Exit criteria: critical public and administrative attack surfaces have automated security tests and no known high-severity findings.

### Phase 2: Documentation, CI, and Critical Tests

- Create an OpenAPI 3.1 specification for the first-party `/api/v1` API.
- Publish generated interactive API documentation.
- Provide an importable Postman collection and example environments.
- Replace the default Laravel README with Armely Store setup and architecture documentation.
- Establish CI for PHPUnit, frontend tests, linting, builds, migrations, and dependency audits.
- Test authentication, authorization, quotes, payments, webhooks, PO submission, and order status.

Exit criteria: a new developer can set up the project from documentation, and every change receives automated quality checks.

### Phase 3: Complete RMA Workflow

- Translate the supplied TD SYNNEX RMA specifications into an internal integration contract.
- Implement customer and admin return workflows.
- Add supplier RMA creation and status synchronization.
- Add notifications, documents, audit history, refunds, and credit reconciliation.
- Cover complete, partial, rejected, cancelled, and failed returns with tests.

Exit criteria: the full production return lifecycle is supported and documented.

### Phase 4: Architecture Refactoring

- Split large controllers by domain responsibility.
- Extract supplier, payment, catalog, quote, order, invoice, and return services.
- Introduce Form Requests, API Resources, policies, and domain events.
- Standardize API response and error schemas.
- Remove obsolete and duplicated integrations after confirming production usage.

Exit criteria: domain boundaries are clear, controllers are small, and critical business rules have isolated tests.

### Phase 5: B2B Features and Storefront Polish

- Build Quick Order.
- Add product comparison.
- Add saved purchasing lists and reorder tools.
- Complete accessibility improvements.
- Add consistent skeleton, empty, error, and toast patterns.
- Add route-level lazy loading and optimize frontend bundle size.

Exit criteria: the highest-value procurement workflows are polished, accessible, responsive, and performance-tested.

## Recommended Starting Point

Security and documentation should be completed first because they reduce risk across every later feature. The RMA and returns center is the strongest next product addition because the supplier specifications already exist and it completes the order lifecycle.

