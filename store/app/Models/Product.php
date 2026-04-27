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
        'retail_price',
        'quantity',
        'billing_model',
        'billing_frequency',
        'billing_term',
        'is_available',
        'is_discontinued',
        'is_hardware',
        'category_segment',
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
        'retail_price'       => 'decimal:2',
        'live_price'         => 'decimal:2',
        'live_retail_price'  => 'decimal:2',
        'live_is_available'  => 'boolean',
        'live_is_discontinued' => 'boolean',
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
