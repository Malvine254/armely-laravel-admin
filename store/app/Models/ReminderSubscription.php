<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderSubscription extends Model
{
    protected $fillable = [
        'identity_key',
        'user_id',
        'product_id',
        'trigger_type',
        'channel',
        'delay_minutes',
        'cooldown_minutes',
        'last_notified_at',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'last_notified_at' => 'datetime',
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
