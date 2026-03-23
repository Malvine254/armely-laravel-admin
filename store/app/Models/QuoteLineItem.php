<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteLineItem extends Model
{
    protected $fillable = [
        'quote_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_percentage',
        'line_total',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateLineTotal(): float
    {
        $subtotal = floatval($this->unit_price) * $this->quantity;
        $discount = $subtotal * (floatval($this->discount_percentage) / 100);
        return $subtotal - $discount;
    }
}
