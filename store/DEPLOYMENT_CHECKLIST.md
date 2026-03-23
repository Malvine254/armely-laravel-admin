# Deployment Checklist: API Performance Optimization

## Pre-Deployment

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
# Run any new migrations (should be none, but just in case)
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
# */5 * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1

# Option 3: systemd timer
# Create /etc/systemd/system/laravel-scheduler.service
```

### Step 6: Verification
```bash
# 1. Check API response time
time curl http://localhost:8000/api/v1/products

# 2. Check logs for errors
tail -f storage/logs/laravel.log

# 3. Verify queue worker
ps aux | grep 'queue:work'

# 4. Verify scheduler
ps aux | grep 'schedule:work'
# OR for cron:
crontab -l | grep 'schedule:run'

# 5. Test price sync job
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
