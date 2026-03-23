<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'reference_id',
        'status',
        'priority',
        'metadata',
        'read_at'
    ];
    
    protected $casts = [
        'metadata' => 'json',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if ($this->status !== 'read') {
            $this->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Create a new message
     */
    public static function createMessage(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $referenceId = null,
        string $priority = 'normal',
        ?array $metadata = null
    ): Message {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'reference_id' => $referenceId,
            'priority' => $priority,
            'metadata' => $metadata,
            'status' => 'unread',
        ]);
    }
}
