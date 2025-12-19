# Admin System Setup Checklist

## Required Actions Before Using Admin Panel

### 1. ✅ Run Database Migrations
```bash
php artisan migrate
```

This will create:
- `admin` table with columns: id, name, email, password, role, status, joined_date, remember_token, created_at, updated_at

### 2. ✅ Seed Default Admin Account
```bash
php artisan db:seed --class=AdminSeeder
```

This creates the default super admin with:
- **Email:** admin@armely.com
- **Password:** Armely@2024

### 3. ✅ Clear Application Cache (Optional but Recommended)
```bash
php artisan cache:clear
php artisan config:clear
```

### 4. ✅ Access Admin Panel
Navigate to: `http://localhost/admin/login` (or your actual domain)

## Directory Permissions

Ensure these directories have proper permissions:

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/
chmod -R 755 public/ckeditor_uploads/
chmod -R 755 public/images/
chmod -R 755 public/pdf/
```

## Verify Installation

1. Visit `/admin/login` - you should see the login form
2. Login with `admin@armely.com` / `Armely@2024`
3. You should see the dashboard with statistics
4. Test navigation through all admin sections

## Post-Installation

After successful login and verification:

1. **Change Default Credentials**
   - Go to Admin Users page
   - Edit the Super Administrator account
   - Change email and password

2. **Create Additional Admins**
   - Go to Admin Users page
   - Click "Add New Admin"
   - Create accounts for other administrators

3. **Start Managing Content**
   - Go to Content Management
   - Begin adding blogs, videos, careers, social impact stories, and customer stories

## Troubleshooting

### Issue: "Table 'xxx' doesn't exist"
**Solution:** Run migrations again
```bash
php artisan migrate:fresh  # Warning: This will delete all data!
```

### Issue: Can't upload images
**Solution:** Check directory permissions and ensure directories exist
```bash
mkdir -p public/ckeditor_uploads
mkdir -p public/images/{blog,videos,social-impact,customers}
chmod -R 755 public/
```

### Issue: Login fails with correct credentials
**Solution:** Clear sessions and cache
```bash
php artisan cache:clear
php artisan session:clear
```

## Important Security Notes

⚠️ **Before Production Deployment:**

1. Change default admin credentials
2. Set `APP_DEBUG=false` in `.env`
3. Set `APP_ENV=production` in `.env`
4. Update `SESSION_DOMAIN` in `.env` to your actual domain
5. Set strong `APP_KEY` (run `php artisan key:generate` if needed)
6. Configure email for password reset notifications
7. Enable HTTPS/SSL
8. Set up regular backups
9. Review and update CORS settings if needed
10. Configure rate limiting for login attempts

## Database Backup

Recommended before any major changes:
```bash
# MySQL
mysqldump -u username -p database_name > backup.sql

# Or use Laravel backup package:
composer require spatie/laravel-backup
```

## Notes

- The admin system uses Laravel's session-based authentication
- All routes are protected by the `admin` middleware
- Database uses custom 'admin' table (not Laravel's default users table)
- Password reset functionality uses plain email (configure SMTP for production)
- All file uploads are stored in `public/` directory for web accessibility

---

**Admin Panel URL:** `/admin/login`  
**Dashboard:** `/admin/dashboard` (after login)  
**Default Email:** admin@armely.com  
**Default Password:** Armely@2024  

**Setup Time:** ~2 minutes  
**Status:** ✅ Ready for deployment
