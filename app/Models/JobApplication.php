<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $table = 'job_applications';

    protected $fillable = [
        'name',
        'email',
        'position',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'role',
        'cv',
        'status',
        'application_date'
    ];

    protected $casts = [
        'application_date' => 'datetime',
    ];
}
