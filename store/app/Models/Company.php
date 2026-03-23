<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'domain',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function getDefaultBillingAddress()
    {
        return $this->addresses()
            ->where('is_default', true)
            ->whereIn('type', ['billing', 'both'])
            ->first();
    }

    public function getDefaultShippingAddress()
    {
        return $this->addresses()
            ->where('is_default', true)
            ->whereIn('type', ['shipping', 'both'])
            ->first();
    }
}
