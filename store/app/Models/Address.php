<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'label',
        'contact_name',
        'contact_phone',
        'street_1',
        'street_2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
        'is_active',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isComplete(): bool
    {
        return $this->street_1 && $this->city && $this->state && $this->postal_code;
    }
}
