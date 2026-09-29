<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MelaKnowledgeChunk extends Model
{
    protected $table = 'mela_knowledge_chunks';

    protected $fillable = [
        'page_id',
        'chunk_index',
        'heading',
        'content',
        'metadata',
        'content_hash',
        'embedding',
    ];

    protected $hidden = ['embedding'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(MelaKnowledgePage::class, 'page_id');
    }
}
