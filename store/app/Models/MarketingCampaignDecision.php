<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCampaignDecision extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'campaign_type',
        'status',
        'reason',
        'product_ids',
        'model_metadata',
        'idempotency_key',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'product_ids' => 'array',
            'model_metadata' => 'array',
            'decided_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class);
    }
}
