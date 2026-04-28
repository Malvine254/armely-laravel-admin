<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    protected $fillable = [
        'user_id',
        'quote_id',
        'status',
        'description',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'items',
        'raw_data',
        'submitted_at',
        'expires_at',
        'approved_by',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'admin_notes',
    ];

    protected $casts = [
        'items' => 'array',
        'raw_data' => 'array',
        'submitted_at' => 'datetime',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(QuoteLineItem::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'quote_id', 'quote_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canConvert(): bool
    {
        return $this->status === 'approved' && !$this->isExpired();
    }

    public function canApprove(): bool
    {
        return in_array($this->status, ['pending_review', 'draft']) && !$this->isExpired();
    }

    public function canReject(): bool
    {
        return in_array($this->status, ['pending_review', 'draft']) && !$this->isExpired();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'pending_review'])
            && !$this->isExpired()
            && !$this->order;
    }
}
