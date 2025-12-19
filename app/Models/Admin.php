<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'joined_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'joined_date' => 'date',
    ];

    public function isSuperAdmin(): bool
    {
        return strtolower($this->role) === 'super admin';
    }

    public function isActive(): bool
    {
        return strtolower($this->status) === 'active';
    }
}
