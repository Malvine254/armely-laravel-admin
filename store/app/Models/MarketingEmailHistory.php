<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingEmailHistory extends Model
{
    protected $table = 'marketing_email_history';

    protected $fillable = [
        'user_id',
        'decision_id',
        'campaign_type',
        'status',
        'idempotency_key',
        'subject',
        'content',
        'product_ids',
        'provider_message_id',
        'failure_reason',
        'scheduled_at',
        'sent_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'product_ids' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaignDecision::class, 'decision_id');
    }
}
