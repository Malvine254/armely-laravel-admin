<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceAlertSubscription extends Model
{
    protected $fillable = [
        'identity_key',
        'user_id',
        'product_id',
        'baseline_price',
        'min_drop_amount',
        'min_drop_percent',
        'cooldown_minutes',
        'last_notified_at',
        'source',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'baseline_price' => 'decimal:2',
            'min_drop_amount' => 'decimal:2',
            'min_drop_percent' => 'decimal:2',
            'last_notified_at' => 'datetime',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
