<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchProfile extends Model
{
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = ['identity_key', 'user_id', 'terms'];

    protected function casts(): array
    {
        return ['terms' => 'array'];
    }
}
