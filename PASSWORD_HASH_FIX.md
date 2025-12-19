# 🔧 Password Hash Error - Fix Guide

## Problem
When logging in, you see:
```
RuntimeException - This password does not use the Bcrypt algorithm.
```

## Cause
The admin table has one or more records with plain text passwords instead of bcrypt hashes. This typically happens with:
- Legacy admin accounts migrated from the old PHP system
- Manually inserted records without proper hashing

## Solution

### Option 1: Use Default Seeded Admin (Recommended)
The seeder creates a properly hashed default admin account:

**Email:** `admin@armely.com`  
**Password:** `Armely@2024`

Try logging in with these credentials first.

### Option 2: Hash Existing Passwords (Recommended for Multiple Admins)

Run this command to automatically hash all plain text passwords:

```bash
php artisan admin:hash-passwords
```

**Output example:**
```
✓ Hashed password for: owuormalvine75@gmail.com
✓ Hashed password for: another.admin@example.com
✓ Already hashed: admin@armely.com
Complete! Hashed: 2, Skipped (already hashed): 1
```

After running this command, you can login with your existing credentials.

### Option 3: Reset Database (Nuclear Option)

If you want to start fresh:

```bash
# This will delete all data!
php artisan migrate:fresh
php artisan db:seed --class=AdminSeeder
```

Then use default credentials:
- Email: `admin@armely.com`
- Password: `Armely@2024`

### Option 4: Update Single Admin Manually

If you only have one problematic account, use Laravel Tinker:

```bash
php artisan tinker
```

Then run:
```php
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

$admin = Admin::where('email', 'owuormalvine75@gmail.com')->first();
$admin->password = Hash::make('YourNewPassword123');
$admin->save();

exit
```

Now login with your new password.

## Prevention for Future

### When Creating Admins
Always hash passwords using Laravel's Hash facade:

```php
// Correct ✅
$admin = Admin::create([
    'email' => 'newadmin@example.com',
    'password' => Hash::make('SecurePassword123'),
]);

// Wrong ❌
$admin = Admin::create([
    'email' => 'newadmin@example.com',
    'password' => 'PlainTextPassword',  // Don't do this!
]);
```

### Through Admin Panel
The admin panel's "Add New Admin" form automatically hashes passwords, so creating admins through the UI is safe.

## Quick Fix Summary

| Situation | Command |
|-----------|---------|
| Have plain text passwords | `php artisan admin:hash-passwords` |
| Want fresh start | `php artisan migrate:fresh && php artisan db:seed --class=AdminSeeder` |
| Single broken account | Use Laravel Tinker (Option 4) |
| Need to login now | Use `admin@armely.com` / `Armely@2024` |

## Verification

After applying a fix, test login by visiting:
```
http://localhost/admin/login
```

If you see the dashboard, the issue is resolved ✅

## Still Having Issues?

1. **Check database:**
   ```bash
   php artisan tinker
   Admin::all() // Shows all admins and passwords
   exit
   ```

2. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Clear cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

4. **Check migrations ran:**
   ```bash
   php artisan migrate:status
   ```

## How the Fix Works

### Hash Command Logic
The `admin:hash-passwords` command:
1. Finds all admin records
2. Checks if password is already hashed (starts with `$2y$`, `$2b$`, or `$2a$`)
3. If not hashed, uses `Hash::make()` to create bcrypt hash
4. Updates database with hashed password
5. Reports how many were hashed vs skipped

### Why It Matters
Laravel's authentication system uses bcrypt algorithm by default. When you try to login:
1. Laravel calls `Hash::check($password, $hashedPassword)`
2. Hash::check only works with bcrypt hashes
3. If password is plain text, it throws the error you saw

## Default Credentials (After Setup)

**Email:** admin@armely.com  
**Password:** Armely@2024  

Change these immediately after first login for security!

## Summary

✅ Run `php artisan admin:hash-passwords`  
✅ Test login at `/admin/login`  
✅ Use default credentials if needed  
✅ Change password after first login  

---

**Status:** Fixed ✅  
**Time to resolve:** < 1 minute  
**No data loss:** Yes (only passwords updated)
