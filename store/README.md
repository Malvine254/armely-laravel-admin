<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

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
- `SERPAPI_IMAGE_ENABLED=true` plus `SERPAPI_API_KEY` enables the second step:
  search the internet using the exact **title returned by TD SYNNEX**, appending
  `specifications` for web results, and using that title directly for image search.
  These searches also run when Icecat already has data. Matching results must
  corroborate the supplier part number or the title's model identifiers (or all
  meaningful title words when no model identifier exists). No customer-entered
  title or URL is trusted as the product identity.
  A regional part such as `ABC123#ABA` may locate an `ABC123` model page or an
  `ABC123ABA` URL; OpenAI still checks the full supplier configuration before
  approving content or sharing a physical-model photo.
  Explicitly labelled specifications and structured search-result attributes
  supplement Icecat fields; values are not inferred from prose. Descriptions can
  be search excerpts. Sources are linked, and absent specs/images stay marked
  unavailable rather than being generated. Without SerpAPI, the response explicitly
  reports that title-based internet search needs configuration.
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
