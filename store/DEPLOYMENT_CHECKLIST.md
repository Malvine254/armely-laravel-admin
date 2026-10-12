# Deployment Checklist: API Performance Optimization

## Pre-Deployment

### Durable carts and lifecycle email

- Run `php artisan migrate --force` from the **store application**, not the
  parent admin app. Account carts require the new snapshot `revision` column.
- Authenticated carts restore from the database on every login/device. Guest
  carts use compact 30-day cookies plus a browser detail cache. Login/signup
  merges overlapping lines using the **larger quantity**, not a sum. Guest
  cookies are cleared only after the account save is confirmed.
- Failed saves retain the device copy and show a retry state. Concurrent-device
  edits show an explicit choice rather than silently overwriting another cart.
  Pending accounts can preserve carts; suspended accounts remain blocked.
- The legacy authenticated behavior snapshot endpoint now also requires a
  revision; stale frontends cannot silently overwrite a restored account cart.
  Deploy the frontend build and backend together.
- Reminder delivery uses **Microsoft Graph**, not `MAIL_MAILER`. Configure the
  tenant, client ID, secret and sender mailbox through admin integrations or
  `AZURE_TENANT_ID`, `AZURE_CLIENT_ID`, `AZURE_CLIENT_SECRET`, and `FROM_EMAIL`.
  The app registration needs Graph application `Mail.Send` permission with
  tenant admin consent and authorization to send from that mailbox.
- Run `php artisan lifecycle:doctor` for read-only table/configuration,
  scheduler and queue checks. It does not print secrets or send mail.
  A missing/stale scheduler run is a warning, not a healthy result.
- Install a **once-per-minute cron against store/artisan**. Browse, cart,
  search-interest and price-drop campaigns execute synchronously from that
  scheduler; they do not require a database queue worker. Other queued jobs,
  including invoice reminders, still require a worker.
- Search reminders use opted-in stored search profiles for active accounts,
  wait at least 24 hours, and consider searches within seven days. All search
  words must match a real available product. Carted, subsequently quoted or
  purchased products are excluded. Browse email preferences, quiet hours,
  unsubscribe, one campaign send/day, two lifecycle sends/day and a 12-hour
  spacing rule are preserved. Failed transport does not mark a reminder sent.
- Graph 202 means **accepted**, not delivered. Check Exchange message trace,
  mailbox restrictions and spam placement before claiming production delivery.
  Do not run a production scheduler or test-send against customer addresses as
  a smoke test.
- Recipient syntax and existing suppression rules are checked locally; Graph
  validates delivery eligibility. Transient local DNS failures must not block
  otherwise valid email addresses before a Graph request is attempted.
- Validate from the store directory with:
  `php vendor/bin/phpunit --filter "BehaviorFavoriteReminderTest|EmailPreferenceLifecycleTest"`
  and `node --test tests/js/cartPersistence.test.mjs tests/js/cartStore.test.mjs`.

### Storefront image and category checks

- Popular category photos must come from matching hardware, not consumables,
  accessories, licenses, or ambiguous all-in-one printer products.
- Desktop image candidates also exclude networking form factors: products
  such as `CBS110 UNMANAGED 8-PORT GE, DESKTOP, EXT PS` are desktop switches,
  not desktop computers. Cache version v13 invalidates prior selections.
- The Desktops card prefers Lenovo product `15349206` (TC Neo 50s Gen 6)
  and its catalog image. If it is unavailable or its local image is missing,
  selection falls back to another valid desktop. The card still browses the
  desktop category rather than linking directly to that product.
- Popular-product cards show only Laptops, Printers, Monitors, Networking and
  Desktops. The Software card is removed; software catalog access elsewhere
  is unchanged.
- Those five browse filters validate hardware product names against shared
  equipment and accessory/supply rules instead of trusting supplier segment
  tags. Mislabelled real equipment remains discoverable; laptop bags/docks,
  monitor arms, printer toner/trays and desktop switches do not qualify.
- Category results, category counts, brand menus, related products and featured
  offers use the same matching rules. Check both category codes and menu slugs;
  counts must exclude accessories and unavailable equipment. Other categories
  retain their existing tagged and untagged keyword matching.
- Browse, category-count, menu, vendor and featured-offer caches are versioned
  for this correction. Deploy both PHP changes and the latest frontend build;
  frontend catalog/menu revisions also invalidate previously cached responses.
- Printer-category regression checks must reject the mislabelled StarTech USB
  extender `15325364`, Jabra Cam IQ `15325109`, and wireless microphones. A
  label printer such as Wasp WPL305 `1035358` is valid equipment.
- Generic equipment searches (for example `HP printers`) use the same strict
  type rules. Every meaningful search term is required; `HP printer 4101`
  must not return unrelated HP models or Canon equipment. Explicit supply
  searches (`HP printer toner`) and exact part-number searches remain available
  outside equipment-only category filters.
- Category browsing honors pinned products and configured storefront rank
  before image presence/newness. Search/model relevance takes priority over
  images, stock quantity and personalization. Navbar searches start a fresh
  search; sidebar categories refine the current search and show readable,
  selected category names for both code and slug deep links.
- Popular category and brand pages paginate the full eligible supplier database,
  not just the shared capped general-browse pool. Category/menu and brand counts
  include equipment outside that pool, including equipment without photos when
  the existing missing-image setting permits it. General unfiltered browsing
  retains its configured cap. Availability, stock, discontinuation, minimum
  price, hardware matching and explicit user filters remain enforced; this
  expands visibility of existing rows and does not import new products.
- Categories and recommendations remain usable when local images are missing;
  a neutral placeholder is preferable to an unrelated product photo.
- Product photos are resolved consistently on the landing, catalog, and detail
  pages. Supplier/CDN paths stay external; Armely product paths use the active
  `/store` mount locally and in production.
- Build from the `store` directory with `npm run build` and deploy the resulting
  public build assets along with the PHP changes. The category cache key is
  versioned so old representative images are not reused.
- Verify category navigation, favorites, and loading-error retry states.
- Recommendations use the existing personalization preference. With consent,
  recent/frequent searches (up to three, within 90 days) query the catalog
  directly; up to two quote-cart, favorite, or recently viewed products seed
  related-product suggestions. Results retain API relevance, interleave
  interests, exclude unavailable/discontinued/unpriced and already-carted
  items, and do not require photos.
- New visitors or visitors opting out see explicitly general suggestions.
  Personalized empty results are not filled with unrelated products.
  Preference changes refresh recommendations immediately; stale in-flight
  requests cannot restore personalized results after opt-out.
- Recently viewed products are fetched by ID, not restricted to the first
  catalog pages. Existing server search profiles work across signed-in
  sessions; browsing, favorites, and cart signals use the current browser's
  existing stores. No new tracking or recommendation database is required.
- Recommendation regression command:
  `node --test tests/js/productRecommendations.test.mjs`.
- Regression commands from the `store` directory:
  `php vendor/bin/phpunit --filter "CatalogTaxonomyTest|PopularCategoryImagesTest|ProductImageUrlExtractionTest"`
  and `node --test tests/js/productImages.test.mjs`.

- [ ] Backup current database
  ```bash
  php artisan backup:run
  ```

- [ ] Clear all caches
  ```bash
  php artisan cache:clear
  php artisan route:clear
  php artisan config:clear
  ```

- [ ] Run tests
  ```bash
  php artisan test
  ```

## Files Changed

### Backend Changes
- [ ] `config/tdsynnex.php` - Cache TTL increased
- [ ] `app/Http/Controllers/ProductController.php` - Database caching added
- [ ] `app/Jobs/SyncProductPricesJob.php` - NEW file, price sync job
- [ ] `app/Console/Kernel.php` - Scheduler updated

### Frontend Changes
- [ ] `resources/js/views/catalog/Products.vue` - Request deduplication added

### Documentation
- [ ] `API_OPTIMIZATION_GUIDE.md` - NEW, comprehensive guide
- [ ] `OPTIMIZATION_QUICK_REFERENCE.md` - NEW, quick reference
- [ ] `ARCHITECTURE_OPTIMIZATION.md` - NEW, architecture documentation

## Deployment Steps

### Step 1: Code Deployment
```bash
# Pull latest changes
git pull origin main

# Install any new dependencies (if any)
composer install --optimize-autoloader

# Update frontend
npm install && npm run build
```

### Step 2: Database Setup
```bash
# Run required migrations, including cart revision protection
php artisan migrate

# Verify products table
php artisan tinker
>>> Product::count()  # Should show number of products
>>> Product::first()  # Check structure
```

### Step 3: Clear Cache
```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Start Queue Worker
```bash
# Option 1: Development
php artisan queue:work &

# Option 2: Production with Supervisor
# (Create supervisord config for laravel-worker)

# Option 3: Docker/Kubernetes
# Ensure queue container is running
```

### Step 5: Start Scheduler
```bash
# Option 1: Development
php artisan schedule:work &

# Option 2: Production (add to crontab)
# * * * * * cd /path/to/project/store && php artisan schedule:run >> /path/to/scheduler.log 2>&1

# Option 3: systemd timer
# Create /etc/systemd/system/laravel-scheduler.service
```

### Step 6: Verification
```bash
# 1. Check API response time
time curl http://localhost:8000/api/v1/products

# 2. Confirm scheduler fallback is enabled
php artisan tinker --execute="dump(\App\Models\AppSetting::getValue('price_sync.enable_http_fallback', false));"

# 3. Confirm the saved sync timezone is America/Chicago
php artisan tinker --execute="dump(\App\Models\AppSetting::getValue('price_sync.timezone', 'America/Chicago'));"

# 4. Check logs for errors
tail -f storage/logs/laravel.log

# 5. Verify queue worker
ps aux | grep 'queue:work'

# 6. Verify scheduler
ps aux | grep 'schedule:work'
# OR for cron:
crontab -l | grep 'schedule:run'

# 7. Test price sync job
php artisan queue:work --tries=1 --timeout=300
# Should see: "Product price sync completed"
```

## Post-Deployment

### Monitor for 24 hours
- [ ] Check logs for errors
  ```bash
  tail -f storage/logs/laravel.log | grep -i "error\|exception"
  ```

- [ ] Monitor queue status
  ```bash
  php artisan queue:monitor
  ```

- [ ] Check API response times
  ```bash
  # Should be < 1 second for cached products
  curl -w "@curl-format.txt" -o /dev/null -s http://localhost:8000/api/v1/products
  ```

- [ ] Verify database cache working
  ```bash
  php artisan tinker
  >>> Product::whereUpdatedAfter(now()->subHours(2))->count()
  ```

### Performance Baseline
Create a file `PERFORMANCE_BASELINE.md`:
```
First Load Time: ___ ms
Cached Load Time: ___ ms
API Calls (24h): ___
Queue Job Success Rate: ___%
Price Sync Duration: ___s
```

## Rollback Plan

If issues emerge:

### Quick Rollback
```bash
# Revert code
git revert --no-edit HEAD

# Stop queue & scheduler
pkill -f 'queue:work'
pkill -f 'schedule:work'

# Clear everything
php artisan cache:clear

# Restart web service
# (systemctl restart php-fpm / docker restart, etc.)
```

### Keep Database
- Product table will remain with cached data
- Safe to keep after rollback
- Or restore from backup if needed

## Troubleshooting During Deployment

### Queue Worker Won't Start
```bash
# Check PHP version
php -v

# Check required extensions
php -m | grep redis  # if using Redis

# Test queue table
php artisan migrate
php artisan queue:failed --list
```

### Scheduler Won't Run
```bash
# Check cron is enabled
systemctl status cron  # Linux

# Check scheduler permission
ls -la /var/spool/cron/

# Test manually
php artisan schedule:run

# Check Laravel logs
tail -f storage/logs/laravel.log | grep schedule
```

### Products Loading Slow Still
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();  # Should work

# Check products in DB
>>> Product::count()
>>> Product::whereUpdatedAfter(now()->subHours(2))->count()

# Check cache working
>>> cache()->has('tdsynnex:products:*')

# Check logs
grep -i "database cache\|from api\|loaded from" storage/logs/laravel.log
```

### Prices Not Syncing
```bash
# Check queue
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Run manually
php artisan queue:work --tries=1 --timeout=300

# Check logs
grep -i "price sync\|SyncProductPrices" storage/logs/laravel.log
```

## Health Checks

### Create a health check endpoint (optional)
Add to `routes/api.php`:
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => Cache::has('test-key') || cache()->store('database')->has('test-key') ? 'working' : 'disconnected',
        'queue' => true, // Verify manually
        'timestamp' => now()->iso8601(),
        'products_cached' => Product::whereUpdatedAfter(now()->subHours(2))->count()
    ]);
});
```

## Documentation Updates

- [ ] Update team wiki/documentation
- [ ] Update deployment runbook
- [ ] Update monitoring dashboards
- [ ] Update SLAs if needed
  - [ ] Response time SLA: <1s (was 2-5s)
  - [ ] 99% cache hit rate target
  - [ ] 99.5% price sync accuracy

## Communication

### Notify stakeholders
- [ ] Developers: Cache behavior changed
- [ ] DevOps: Queue & Scheduler setup required
- [ ] QA: Performance testing recommended
- [ ] Users: No apparent changes, but faster

### Update documentation
- [ ] README.md - Add setup instructions
- [ ] CONTRIBUTING.md - Explain optimization
- [ ] Deployment guide - Add queue/scheduler section

## Success Criteria

✅ Deployment successful when:
- [ ] API response time < 1 second
- [ ] Queue worker processing jobs
- [ ] Scheduler running price sync every 30 minutes
- [ ] No errors in logs for 24 hours
- [ ] Database caching working (products table has recent data)
- [ ] Performance metrics showing 80%+ improvement
- [ ] File: `PERFORMANCE_BASELINE.md` created with metrics

---

**Last Updated**: February 27, 2026
**Estimated Deployment Time**: 30-45 minutes
**Estimated Downtime**: 0-5 minutes (config cache safe during requests)
