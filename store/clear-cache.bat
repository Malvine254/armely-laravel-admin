@echo off
REM Clear all Laravel caches - Run this after making code changes
REM Usage: Run this batch file from the armely-store directory

echo Clearing Laravel caches...
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
echo.
echo ✅ Caches cleared successfully!
pause
