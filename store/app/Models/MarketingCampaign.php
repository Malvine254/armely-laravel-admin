<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    protected $fillable = ['type', 'name', 'status', 'priority', 'settings'];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'settings' => 'array',
        ];
    }
}
