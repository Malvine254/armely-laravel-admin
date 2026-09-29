<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MelaKnowledgePage extends Model
{
    protected $table = 'mela_knowledge_pages';

    protected $fillable = [
        'url',
        'title',
        'page_type',
        'content_hash',
        'last_updated',
        'last_indexed_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_updated' => 'datetime',
            'last_indexed_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(MelaKnowledgeChunk::class, 'page_id');
    }
}
