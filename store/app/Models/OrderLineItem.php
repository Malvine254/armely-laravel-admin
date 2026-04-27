<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLineItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'tdsynnex_line_item_id',
        'quantity',
        'unit_price',
        'line_total',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
