# Manual Image Upload Guide

## Workflow

1. **Get list of products needing images:**
   ```bash
   php artisan products:list-missing-images
   ```
   This shows all products without images and their product IDs for naming.

2. **Export to CSV (optional):**
   ```bash
   php artisan products:list-missing-images --export=products-need-images.csv
   ```
   This creates a CSV file you can use with image management tools.

3. **Save images with the correct naming convention:**
   - Place image files in this folder: `public/uploads/products/`
   - **Name format: `{PRODUCT_ID}.jpg`**
   - Example: `15204339.jpg`

4. **Sync images to database:**
   ```bash
   php artisan products:sync-manual-images
   ```
   This matches images by product ID and updates the database.

5. **Verify sync (dry-run):**
   ```bash
   php artisan products:sync-manual-images --dry-run
   ```
   This shows what would be updated without making changes.

## Supported Image Formats

- `.jpg` / `.jpeg`
- `.png`
- `.gif`
- `.webp`

## Database Storage

Images are stored in the `products.images` JSON column with this structure:

```json
[
  {
    "fileName": "15204339.jpg",
    "imagePath": "uploads/products/15204339.jpg",
    "imageUrl": "http://localhost:8001/store/uploads/products/15204339.jpg",
    "source": "manual",
    "addedAt": "2026-05-04T10:30:00Z"
  }
]
```

## Commands Reference

### List Missing Images (Product IDs)
```bash
# Show all products needing images with their product IDs
php artisan products:list-missing-images

# Show first 100 products
php artisan products:list-missing-images --limit=100

# Export to CSV
php artisan products:list-missing-images --export=missing.csv

# Include SKU if preferred
php artisan products:list-missing-images --by-sku
```

### Sync Manual Images (by Product ID)
```bash
# Match images by product ID (default, recommended)
php artisan products:sync-manual-images

# Preview without updating
php artisan products:sync-manual-images --dry-run

# Use custom folder
php artisan products:sync-manual-images --folder=custom/path

# Suppress verbose output
php artisan products:sync-manual-images --quiet-output

# Match by SKU instead (if you named files by SKU)
php artisan products:sync-manual-images --by-sku
```

## Tips

- **Use product ID for naming** — it's the primary matching method
- Keep image file sizes reasonable (< 5MB recommended)
- Use PNG or WebP for better compression
- Always run `--dry-run` first to verify changes
- Check search results after sync to confirm images display
