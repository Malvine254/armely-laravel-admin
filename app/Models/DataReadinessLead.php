<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataReadinessLead extends Model
{
    protected $table = 'data_readiness_leads';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'company',
        'role',
        'overall_score',
        'dimension_scores',
        'ip_address',
        'country',
        'city',
    ];

    protected $casts = [
        'dimension_scores' => 'array',
    ];
}
