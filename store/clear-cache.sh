#!/bin/bash
# Clear all Laravel caches - Run this after making code changes
# Usage: bash clear-cache.sh

echo "Clearing Laravel caches..."
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
echo ""
echo "✅ Caches cleared successfully!"
