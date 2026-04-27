# 🚀 Admin System - Quick Start Guide

## 30-Second Setup

```bash
# 1. Run migrations (creates admin table)
php artisan migrate

# 2. Seed default admin account
php artisan db:seed --class=AdminSeeder

# 3. Login
# URL: http://localhost/admin/login
# Email: admin@armely.com
# Password: Armely@2024
```

## Access Points

| Page | URL | Purpose |
|------|-----|---------|
| Login | `/admin/login` | Admin authentication |
| Dashboard | `/admin/dashboard` | Main stats & overview |
| Content | `/admin/tables` | Manage all content |
| Admins | `/admin/admins` | User management |
| Reports | `/admin/reports` | Export data |

## Key Routes

```
POST /admin/login                          Login
POST /admin/logout                         Logout
GET  /admin/dashboard                      Dashboard
GET  /admin/tables                         Content management
GET  /admin/admins                         Admin users
GET  /admin/reports                        Reports
```

## Default Credentials

```
Email:    admin@armely.com
Password: Armely@2024
```

⚠️ **Change these immediately after first login!**

## What You Can Do

### 📝 Content Management
- Create/edit/delete blogs with rich text editor
- Manage videos with URLs and thumbnails
- Post job openings and career information
- Share social impact stories with images
- Showcase customer success stories

### 👥 Admin Management
- Create new admin accounts
- Assign roles (Super Admin / Admin)
- Edit admin details and permissions
- Deactivate/activate accounts
- View admin statistics

### 📊 Reports
- Generate reports in PDF, CSV, or Excel
- Filter by date range
- Download formatted documents
- Track all content statistics

## First-Time Checklist

- [ ] Run migrations: `php artisan migrate`
- [ ] Seed admin: `php artisan db:seed --class=AdminSeeder`
- [ ] Visit `/admin/login`
- [ ] Login with default credentials
- [ ] Change admin email and password
- [ ] Create additional admin accounts
- [ ] Start managing content

## Troubleshooting

### Can't login?
- Check email/password are correct
- Clear browser cookies
- Ensure migrations were run

### Page not found?
- Verify routes exist: `php artisan route:list | grep admin`
- Clear cache: `php artisan cache:clear`

### Database error?
- Run migrations: `php artisan migrate`
- Check `.env` database configuration

## Files to Know

```
app/Models/Admin.php                       Admin model
app/Http/Controllers/Admin/                Admin controllers
app/Http/Middleware/AdminMiddleware.php    Route protection
resources/views/admin/                     Admin views
routes/web.php                             Routes
config/auth.php                            Auth configuration
```

## Important Notes

✅ **Admin middleware** protects all admin routes  
✅ **Session-based** authentication  
✅ **Status validation** - deactivated admins are logged out  
✅ **Role-based** - Super Admin vs Admin  
✅ **CSRF protection** - on all forms  
✅ **Password hashing** - bcrypt encryption  

## Next Steps

1. **Complete setup** (see 30-second setup above)
2. **Change default credentials** (first login)
3. **Create team admins** (as needed)
4. **Start managing content** (add blogs, etc.)
5. **Generate reports** (as needed)

## Performance Tips

- Use the modern admin panel for all content management
- Images in `public/images/` are automatically optimized
- CKEditor handles rich text efficiently
- Reports are generated on-demand (no storage needed)

## Security Reminders

🔒 **Always:**
- Change default credentials immediately
- Use strong passwords (min 8 characters)
- Keep admin accounts active/inactive status updated
- Review admin users regularly
- Don't share admin credentials

## Support Files

📖 Full documentation available:
- `ADMIN_MIGRATION_COMPLETE.md` - Comprehensive guide
- `ADMIN_SETUP_CHECKLIST.md` - Step-by-step setup
- `ADMIN_DEPLOYMENT_GUIDE.md` - Production deployment
- `ADMIN_SYSTEM_SUMMARY.md` - Complete overview

---

## 🎉 You're All Set!

The modern admin system is ready to use.

**Admin Panel:** `http://localhost/admin/login`

Happy managing! 🚀
