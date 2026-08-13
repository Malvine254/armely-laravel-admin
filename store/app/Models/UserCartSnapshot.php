<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCartSnapshot extends Model
{
    protected $fillable = [
        'identity_key',
        'user_id',
        'items',
        'item_count',
        'total_quantity',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }
}
