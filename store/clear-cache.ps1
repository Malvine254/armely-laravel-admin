# Clear all Laravel caches - Run this after making code changes
# Usage: powershell -ExecutionPolicy Bypass -File clear-cache.ps1

Write-Host "Clearing Laravel caches..." -ForegroundColor Cyan
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
Write-Host ""
Write-Host "✅ Caches cleared successfully!" -ForegroundColor Green
