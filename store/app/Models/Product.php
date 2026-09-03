<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'tdsynnex_product_id',
        'tdsynnex_sku_no',
        'vendor_id',
        'product_name',
        'mfg_part_no',
        'description',
        'base_price',
        'supplier_regular_price',
        'retail_price',
        'sale_price',
        'is_on_sale',
        'offer_source',
        'sale_started_at',
        'sale_ended_at',
        'quantity',
        'billing_model',
        'billing_frequency',
        'billing_term',
        'is_available',
        'is_discontinued',
        'is_hardware',
        'category_segment',
        'is_storefront_curated',
        'storefront_score',
        'storefront_rank',
        'storefront_curated_at',
        'is_storefront_pinned',
        'storefront_pinned_at',
        'image_enrichment_attempted_at',
        'search_imported_at',
        'search_import_query',
        'search_import_review_status',
        'search_import_reviewed_at',
        'search_import_reviewed_by',
        'manufacturer',
        'specifications',
        'images',
        'last_synced_at',
        // Shadow columns — updated every 2 hours, applied to main columns at midnight
        'live_price',
        'live_retail_price',
        'live_quantity',
        'live_is_available',
        'live_is_discontinued',
        'live_checked_at',
    ];

    protected $casts = [
        'specifications'     => 'array',
        'images'             => 'array',
        'last_synced_at'     => 'datetime',
        'live_checked_at'    => 'datetime',
        'base_price'         => 'decimal:2',
        'supplier_regular_price' => 'decimal:2',
        'retail_price'       => 'decimal:2',
        'sale_price'         => 'decimal:2',
        'is_on_sale'         => 'boolean',
        'sale_started_at'    => 'datetime',
        'sale_ended_at'      => 'datetime',
        'live_price'         => 'decimal:2',
        'live_retail_price'  => 'decimal:2',
        'live_is_available'  => 'boolean',
        'live_is_discontinued' => 'boolean',
        'is_storefront_curated' => 'boolean',
        'storefront_score' => 'decimal:2',
        'storefront_curated_at' => 'datetime',
        'is_storefront_pinned' => 'boolean',
        'storefront_pinned_at' => 'datetime',
        'image_enrichment_attempted_at' => 'datetime',
        'search_imported_at' => 'datetime',
        'search_import_reviewed_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function quoteLineItems(): HasMany
    {
        return $this->hasMany(QuoteLineItem::class);
    }

    public function orderLineItems(): HasMany
    {
        return $this->hasMany(OrderLineItem::class);
    }
}
