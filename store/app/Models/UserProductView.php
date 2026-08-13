<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProductView extends Model
{
    protected $fillable = [
        'identity_key',
        'user_id',
        'product_id',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }
}
