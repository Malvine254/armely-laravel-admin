<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_number',
        'order_number',
        'status',
        'total_amount',
        'tax_amount',
        'paid_amount',
        'items',
        'raw_data',
        'issued_at',
        'due_at',
        'paid_at',
        'notes',
        'pdf_url',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
    ];

    protected $casts = [
        'items' => 'array',
        'raw_data' => 'array',
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_number', 'order_number');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_at && $this->due_at->isPast();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->paid_at;
    }

    public function getRemainingAmount(): float
    {
        return max(0, floatval($this->total_amount - $this->paid_amount));
    }
}
