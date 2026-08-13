<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavoriteEvent extends Model
{
    protected $fillable = [
        'identity_key',
        'user_id',
        'product_id',
        'event_type',
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
