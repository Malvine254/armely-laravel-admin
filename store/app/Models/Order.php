<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'quote_id',
        'status',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'items',
        'raw_data',
        'ordered_at',
        'shipped_at',
        'delivered_at',
        'tracking_info',
        'shipping_address_id',
        'billing_address_id',
        'shipping_amount',
        'payment_status',
        'payment_method',
        'cancellation_reason',
        'cancelled_at',
        'tdsynnex_order_id',
    ];

    protected $casts = [
        'items' => 'array',
        'raw_data' => 'array',
        'tracking_info' => 'array',
        'ordered_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'quote_id', 'quote_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'order_number', 'order_number');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(OrderLineItem::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered' && $this->delivered_at;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing', 'confirmed']) 
            && !$this->cancelled_at 
            && (!$this->ordered_at || $this->ordered_at->addDays(7)->isFuture());
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled' || $this->cancelled_at;
    }
}
