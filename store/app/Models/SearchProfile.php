<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchProfile extends Model
{
    protected $fillable = ['identity_key', 'user_id', 'terms'];

    protected function casts(): array
    {
        return ['terms' => 'array'];
    }
}
