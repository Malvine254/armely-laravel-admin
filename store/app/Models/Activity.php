<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = ['user_id', 'type', 'action', 'description', 'metadata'];
    
    protected $casts = [
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity
     */
    public static function log(int $userId, string $type, string $action, string $description, ?array $metadata = null): Activity
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
