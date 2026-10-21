<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCustomerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'features',
        'summary',
        'confidence',
        'last_activity_at',
        'data_freshness_at',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'confidence' => 'decimal:3',
            'last_activity_at' => 'datetime',
            'data_freshness_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
