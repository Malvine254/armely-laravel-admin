<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailPreference extends Model
{
    protected $fillable = [
        'user_id',
        'transactional_enabled',
        'marketing_enabled',
        'price_alerts_enabled',
        'cart_reminders_enabled',
        'browse_reminders_enabled',
        'timezone',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    protected function casts(): array
    {
        return [
            'transactional_enabled' => 'boolean',
            'marketing_enabled' => 'boolean',
            'price_alerts_enabled' => 'boolean',
            'cart_reminders_enabled' => 'boolean',
            'browse_reminders_enabled' => 'boolean',
            'quiet_hours_start' => 'integer',
            'quiet_hours_end' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
