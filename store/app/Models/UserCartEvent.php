<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCartEvent extends Model
{
    protected $fillable = [
        'identity_key',
        'user_id',
        'event_type',
        'product_id',
        'quantity',
        'metadata',
        'event_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'event_at' => 'datetime',
        ];
    }
}
