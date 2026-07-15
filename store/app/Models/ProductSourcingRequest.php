<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSourcingRequest extends Model
{
    protected $fillable = [
        'user_id', 'search_query', 'manufacturer', 'model_or_part_number',
        'quantity', 'notes', 'status',
    ];

    protected $casts = ['quantity' => 'integer'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
