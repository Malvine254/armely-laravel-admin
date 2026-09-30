<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MelaConversation extends Model
{
    use HasUuids;

    protected $table = 'mela_conversations';

    protected $fillable = [
        'token_hash',
        'memory',
        'summary',
        'summarized_through_id',
        'escalation_status',
        'user_message_count',
        'ip_hash',
        'user_agent',
        'landing_page',
        'country_code',
        'last_activity_at',
    ];

    protected $hidden = ['token_hash', 'ip_hash'];

    protected function casts(): array
    {
        return [
            'memory' => 'array',
            'last_activity_at' => 'datetime',
            'summarized_through_id' => 'integer',
            'user_message_count' => 'integer',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MelaMessage::class, 'conversation_id');
    }

    public function tokenMatches(string $token): bool
    {
        return $token !== '' && hash_equals($this->token_hash, hash('sha256', $token));
    }
}
