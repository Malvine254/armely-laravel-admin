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
    ];

    protected $casts = [
        'specifications' => 'array',
        'images' => 'array',
        'last_synced_at' => 'datetime',
        'base_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
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
