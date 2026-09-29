<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MelaMessage extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'mela_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'meta',
        'latency_ms',
        'prompt_tokens',
        'completion_tokens',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(MelaConversation::class, 'conversation_id');
    }
}
