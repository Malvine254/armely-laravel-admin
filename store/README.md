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

The products page offers **Search outside catalog** both for empty searches and
alongside existing results. It identifies up to ten products using the TD SYNNEX
PriceAvailability API and the configured AP SKU source files, bypassing the local
storefront filters. Select **View online details & image** to enrich that specific
supplier identity. No sourcing request, notification, or product import is created.

- `GET /api/v1/product-discovery?search=...` returns supplier identities only, not
  internal supplier prices.
- `GET /api/v1/product-discovery/details?search=...&product_id=...` accepts only an
  identity from that supplier search and returns online descriptions, available
  specifications, an external HTTPS image URL, sources, and missing-data warnings.
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
  Explicitly labelled specifications and structured search-result attributes
  supplement Icecat fields; values are not inferred from prose. Descriptions can
  be search excerpts. Sources are linked, and absent specs/images stay marked
  unavailable rather than being generated. Without SerpAPI, the response explicitly
  reports that title-based internet search needs configuration.
- Discovery is public, throttled to ten requests per minute per endpoint, and
  online results are cached for ten minutes. Missing content is explicitly marked;
  it is never invented. External images can be blocked or removed by their host.
  These informational results cannot be purchased without catalog verification.

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
