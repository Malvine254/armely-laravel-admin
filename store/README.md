<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Catalog-wide AI descriptions and missing images

Store admin **All Products** has separate **Fill Missing Descriptions** and
**Fill Missing Images** buttons, per-product retries, a missing-description filter, coverage counts,
description previews, and last-result warnings. This applies to every saved store
product, not just outside-catalog imports or currently visible storefront items.
Existing descriptions and usable images are not replaced by this process.
Blank descriptions and descriptions identical to the title need enrichment.

You can also run this directly from **Settings > Catalog Ops > AI Product
Descriptions & Images**. Click **Populate Missing Descriptions** or
**Populate Missing Images** and confirm. Each action changes only the selected
content type, with independent 24-hour retry cooldowns and unique jobs. The
All Products page also offers separate bulk and per-product actions.
The Settings buttons process one product per authenticated HTTP request using
`POST /api/v1/admin/products/process-content` (`mode`, `after_id`), saving each
result before advancing. They do **not** queue work or start a detached process.
Keep Settings open: it shows processed, ready and needs-research counts, the last
product's warnings, and **Stop After Current Product**. Closing the page stops
new requests; earlier saves remain. Manual runs bypass retry cooldowns, and a
monotonic product-ID cursor prevents repeatedly processing an unresolved product
in the same scan. **Refresh Content Counts** shows remaining missing content;
**Review Product Results** opens per-product warnings. A finished scan can still
have unresolved products; it does not mean every description was populated.
Each request has a 180-second browser timeout and a 150-second PHP time limit;
hosting proxy limits can be shorter and must accommodate the single-product
lookup. Timeouts stop the browser loop and report unknown current save status.
The database must be online and deployment migrations installed; missing columns
produce an actionable error instead of a queued-success response.

Older descriptions tagged `ai-generated-from-catalog-facts` are replaced with
verified descriptions when evidence exists. When research fails, that tagged
generic text is removed from the description field and retained in
`specifications.description_repair_backup`; manual descriptions are never cleared.
All Products bulk buttons, CLI commands and automatic enrichment still use the
existing database queue and require the hosting-managed worker.
Deploy the separate-attempt timestamps with `php artisan migrate --force`.
The worker launcher uses the PHP CLI executable (not PHP-FPM) and redirects the
entire background process group so the HTTP request does not wait for queue work.
Immediate process exits are reported explicitly. A browser timeout means startup
is unknown, not that the worker definitely failed: refresh counts and inspect
`storage/logs/products-sync-worker.log` before starting another worker. Previously
queued combined enrichment jobs still process both types; the new buttons do not
cancel existing work.
Description/image enrichment sends no email notifications. Existing product,
price and availability synchronization emails remain unchanged.

Direct Bing web and image searches (plus optional Icecat) retrieve evidence using the saved supplier
identity. The existing vision-capable Azure OpenAI deployment verifies descriptions
and candidate images; only a matching image with reported confidence >= 0.95 is
assigned. AI writes an original description using the verified website description,
source references, exact saved title, manufacturer, part number and verified
specifications. General knowledge may explain confirmed features in neutral terms
but must not establish additional product-specific facts. A second AI fact-check
must approve every product-specific claim before generated text is saved. If
generation or fact-checking fails, an already verified website excerpt is retained;
without one, and without website specifications, AI may generate a conservative
fallback from the saved title, manufacturer and part number. General knowledge
can explain the ordinary purpose of a product category clearly identified by
the title, but cannot establish model-specific facts from memory. Ambiguous titles
must remain missing if a useful description cannot be supported. Both generation
paths require a second claim check; failed output is never saved.
Neither path may invent missing specifications, compatibility, warranty or claims.
Website-grounded text is marked `ai-generated-from-verified-web-evidence`.
Title-based text is marked `ai-generated-from-title`, counts as populated, and
displays **AI title-based - review** with an explicit warning that it is not
website-verified. Manual runs and queued jobs use the same fallback policy;
image matching still requires the existing verified evidence/confidence checks.
Older `ai-generated-from-catalog-facts` descriptions are marked **Needs research**,
included in missing-description counts, and eligible for replacement, even during
the normal retry cooldown. Manual and verified descriptions stay untouched. AI output
can still be imperfect: review generated descriptions and image matches.
Enrichment metadata and tagged AI content survive subsequent catalog imports/syncs.
No model-generated image URLs or synthetic product images are used.

Required: the existing store `AZURE_OPENAI_ENDPOINT`, `AZURE_OPENAI_API_KEY`,
`AZURE_OPENAI_DEPLOYMENT`, and `AZURE_OPENAI_API_VERSION` settings. For internet
website/image lookup, `BING_IMAGE_ENABLED` defaults to true and needs no search API
key. No SerpAPI requests are made, including by the legacy image resolver.
Icecat is also supported with the settings below. Without search coverage or a confident
match, the image stays missing and admin shows a warning. AI/provider failures
never manufacture content or report the product as complete.

Deploy and process from the `store` directory:

```sh
php artisan migrate --force
php artisan products:enrich-content --limit=25
php artisan queue:work database --queue=products-sync --timeout=3600 --tries=3
```

Omit `--limit` to request the entire eligible catalog. The admin button submits a
background catalog scan rather than holding the web request open. Run Laravel's
normal `schedule:run` every minute; once AI is configured, the hourly schedule
requests up to 100 missing-content products automatically, including new imports.
The database queue worker must remain running. Unresolved products have a 24-hour
cooldown, with unattempted/oldest products first; per-product admin retries bypass
the cooldown. Unique jobs prevent duplicate pending requests. API usage charges
apply. Queue failures appear in the normal failed-job logs; per-product failures
also appear in admin. A queued request is not a guarantee of description/image
coverage: configuration errors or insufficient facts require review and retry.

## Outside-catalog product discovery

Navigation menu counts use one streamed eligible-product query per cold cache,
then reuse the result for 30 minutes. Category name rules, stock/price filters,
and manufacturer counts are preserved without repeated full-catalog regex scans.
This avoids blocking the single-threaded local PHP server while the menu loads.
When authentication restores during an initial product fetch, the replacement
request owns the loading indicator and clears it on either success or failure.
Stale responses cannot overwrite newer results/errors or remove newer pending
requests. Run the focused regression with `node --test tests/js/productLoading.test.mjs`.

Normal product searches check the database first. If the first page has no local
matches, the products page automatically checks TD SYNNEX and displays matches
labelled **Outside catalog** in the same results area, without an extra click.
It does not fall back on catalog request errors or empty later pages when other
database matches exist. Reloading a supplier page rechecks the database and
resumes the requested supplier page if the entire database search is still empty.
**Search outside catalog** remains available alongside existing results for
explicit broader searches. It identifies products using the TD SYNNEX
PriceAvailability API and the configured AP SKU source files. In-stock matches
with a live price of at least $100 (or the higher configured `IMAGE_SYNC_MIN_PRICE`)
are saved as normal products. No sourcing request or notification is created.
Exact numeric TD SYNNEX SKUs (5-12 digits) are queried directly through
PriceAvailability even when absent from the local AP files. Renewed/refurbished
products remain eligible, but zero-stock and discontinued products are excluded.
This also applies to pinned products in the normal catalog. Title/MPN searches
still depend on the configured AP index to locate
supplier SKUs, so a recently added product missing from that index should be
looked up by its exact TD SYNNEX SKU.
Outside-catalog title searches rank candidates rather than requiring every
configuration word. Brand/family and explicit part identifiers remain required;
generation, OS, capacity, and condition terms improve ranking when present.
Exact configurations lead partial matches, and equipment leads accessories.
Recently verified direct-SKU identities (up to 100, cached for a day) also help
locate products missing from the AP index; matches are rechecked with the supplier
API. These are closest matches, not a guarantee of identical specifications.
Discovery uses the same `ProductCard`, normal View/detail, cart/quote, favorite,
share, and review actions as saved catalog products. There is no separate online
details panel or special card design.
Supplier search replaces the products in the existing results area; it does not
create a second grid. It reuses the existing ten-products-per-page cap, result
count, sort selector, and original Previous/page-number/Next controls. A compact
window of at most seven page buttons prevents overlapping navigation labels.
Supplier batches are checked until enough in-stock items fill the requested
page plus a look-ahead item. The price minimum and optional maximum apply before
pagination, including existing approved offers; customer-specific discounts then
apply exactly once, as in the saved catalog. Checked stock is cached across pages.
Candidate lists are cached for ten
minutes and capped by the same `catalog.storefront_product_limit` setting (and
`storefront.assortment_size` default) as storefront browsing. Pagination totals
count only checked in-stock products, never raw candidate counts. A `+` marks
totals while more candidates remain unchecked; totals become exact once exhausted.
Supplier PriceAvailability prices use the same sell-price basis and customer
discount service as the catalog, followed by the same currency conversion,
formatting, and MSRP comparison in the shared card (no second profit markup).
Only live API prices are displayed: AP fallback or MSRP alone cannot establish
a selling price. Products without a live selling price are excluded; supplier
quantities are displayed only for in-stock, non-discontinued matches.
Responses are private/no-store so customer discounts are not shared, while
supplier lookups remain cached before customer pricing. Enriched image URLs
populate the same card image area.

- `GET /api/v1/product-discovery?search=...` returns supplier identities,
  customer-facing `productPrice`, `pricing_available`, supplier quantities, and
  `catalog_product`, the canonical normal-product payload used by cards and carts.
  Raw supplier/internal payload fields are not returned.
  Pass `page` and `per_page` (default 10, maximum 100) for pagination metadata;
  the products page uses 10. Without these parameters the original top-ten
  response remains compatible.
- `GET /api/v1/product-discovery/products/{productId}/content` enriches an already
  saved supplier identity and returns its updated canonical product payload.
  The browser requests current-page content sequentially after displaying priced,
  purchasable cards, ignores stale page responses, and stops on rate limits.
  It passes `min_price`/`max_price` filters to supplier search. Content is also
  refreshed automatically when opening a pending product's normal details page.
- `GET /api/v1/product-discovery/details?search=...&product_id=...` accepts only an
  identity from that supplier search and returns online descriptions, available
  specifications, an external HTTPS image URL, sources, and missing-data warnings.
  Send the same `page` and `per_page` values for a product on a later page.
- Configure TD SYNNEX credentials and AP files as for the existing supplier search.
  Enable `ICECAT_ENABLED` and configure `ICECAT_USERNAME`, `ICECAT_PASSWORD` and,
  when required by the account, `ICECAT_APP_KEY` for structured online descriptions,
  specifications, and images. Coverage depends on the account and product.
- Direct Bing HTML search is enabled by default (`BING_IMAGE_ENABLED=true`).
  It searches the exact **title returned by TD SYNNEX**, manufacturer and part
  number, appending `specifications` for web results.
  These searches also run when Icecat already has data. Matching results must
  corroborate the supplier part number or the title's model identifiers (or all
  meaningful title words when no model identifier exists). No customer-entered
  title or URL is trusted as the product identity.
  A regional part such as `ABC123#ABA` may locate an `ABC123` model page or an
  `ABC123ABA` URL; OpenAI still checks the full supplier configuration before
  approving content or sharing a physical-model photo.
  Explicitly labelled specifications in search-result excerpts
  supplement Icecat fields; values are not inferred from prose. Descriptions can
  be search excerpts. Sources are linked, and absent specs/images stay marked
  unavailable rather than being generated. Bing source-page URLs and original image
  URLs are preserved for AI verification. Bing redirect destinations are decoded,
  but only public HTTPS evidence is accepted. Captchas, HTTP errors and empty or
  unrecognized result pages surface warnings; no access controls are bypassed.
  Direct search depends on Bing availability and HTML layout and does not
  guarantee results. Disabling Bing explicitly surfaces a configuration warning.
- Configure the existing `AZURE_OPENAI_*` credentials/deployment with vision chat
  support. OpenAI compares supplier identity with the actual candidate images and
  online evidence. It can only approve existing descriptions/specification indices
  and select a corroborated image; it cannot invent values or URLs. Images require
  model-reported confidence of at least 0.95. This is a conservative acceptance
  rule, not a guarantee of correctness or coverage. Uncertain/conflicting models,
  accessories and placeholders are rejected. Missing providers or verification
  failures leave placeholders/supplier text and an explicit content warning.
  Verified descriptions, specifications, sources and images persist with the
  product, without overwriting manual offers or adding another profit markup.
- Discovery is public, throttled to ten searches/details per minute and 100 saved
  content requests per minute (ten cards per search), and
  online results are cached for ten minutes. Missing content is explicitly marked;
  it is never invented. External images can be blocked or removed by their host.
  Live supplier verification and normal persistence enable the existing purchase
  flow; cart and checkout retain their usual availability and pricing checks.

Deploy from the `store` directory with `php artisan migrate --force` and
`npm run build`. The notification repair migration restores numeric MySQL
`notifications.id` auto-increment after imports that stripped it, preserving
existing rows. Back up production before migrations; nonstandard ID/key schemas
stop with an explicit error instead of being converted silently.
The storefront pin migration also resumes imported or partially migrated schemas
by adding only missing pin columns/indexes, preserving existing pin values.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
