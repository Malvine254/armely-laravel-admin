<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

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

## Local Test Scripts

Ad hoc PHP diagnostics and one-off verification scripts should live in `test_php_files/` so the project root stays clean.

- Use `test_php_files/` for temporary checks, schema inspections, and manual test helpers.
- Use subfolders like `test_php_files/scripts/` and `test_php_files/store/` when you want to group scripts by area.
- Keep application code out of this folder so these helpers stay easy to find and remove later.

## Resource downloads and email templates

- Newly generated case study, white paper, and PDF resource download links are signed and expire after 24 hours. Existing links keep their original expiry. Expired or tampered links must be requested again; do not disable signature checks or change `APP_KEY` to troubleshoot them.
- Active admins can download case study and white paper attachments from the admin table and detail modal without submitting a lead form. Protected admin endpoints issue fresh signed links. Legacy document URLs also redirect active admins through these endpoints; anonymous and inactive users cannot use them to bypass gating.
- Email URLs must use Blade's `{{ $url }}` syntax, not `{{ e($url) }}`. Blade escapes once already; escaping twice corrupts query separators and displays entities in text.
- Legacy emailed links with `amp;`-encoded query keys are normalized before signature validation. Normalization does not extend expiry or bypass validation.
- Download failures appear as an access alert on the Case Studies page. Missing files and remote retrieval failures also appear in the application logs.
- Notification templates share `emails.layouts.modern` and the `emails.partials.details` / `button` partials. Pass `plainText => true` with raw values to the details partial. Its legacy mode expects values that have already been escaped (or intentionally constructed safe HTML).
- Main-site emails use the same Armely wordmark as the website header (`public/images/logo/logo-replace-v2.png`). Store email branding remains separate.
- The public wordmark has a targeted `Cross-Origin-Resource-Policy: cross-origin` exception in `public/.htaccess` so external webmail can display it; other resources retain the same-site policy.
- Decode stored entity-encoded content titles with `App\Support\EmailText::decode`, then let Blade escape the result. Never render decoded content as raw HTML.
- After deployment, clear compiled views with `php artisan view:clear` and request a new download email to verify the production file path and link.

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
