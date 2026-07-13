<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPriceHistory extends Model
{
    protected $fillable = [
        'product_id', 'supplier_price', 'regular_price', 'sale_price', 'source', 'checked_at',
    ];

    protected $casts = [
        'supplier_price' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'checked_at' => 'datetime',
    ];
}
