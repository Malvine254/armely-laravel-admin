<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductContentRun extends Model
{
    public const ACTIVE = 'running';

    protected $fillable = [
        'mode', 'min_description_length', 'status', 'after_id', 'total_candidates',
        'processed', 'ready', 'needs_review', 'failed', 'current_product_name',
        'started_by', 'heartbeat_at', 'finished_at',
    ];

    protected $casts = [
        'min_description_length' => 'integer',
        'after_id' => 'integer',
        'total_candidates' => 'integer',
        'processed' => 'integer',
        'ready' => 'integer',
        'needs_review' => 'integer',
        'failed' => 'integer',
        'heartbeat_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ProductContentRunItem::class, 'run_id');
    }

    public function toSummary(): array
    {
        return [
            'id' => $this->id,
            'mode' => $this->mode,
            'min_description_length' => $this->min_description_length,
            'status' => $this->status,
            'total_candidates' => $this->total_candidates,
            'processed' => $this->processed,
            'ready' => $this->ready,
            'needs_review' => $this->needs_review,
            'failed' => $this->failed,
            'current_product_name' => $this->current_product_name,
            'started_at' => optional($this->created_at)->toIso8601String(),
            'heartbeat_at' => optional($this->heartbeat_at)->toIso8601String(),
            'finished_at' => optional($this->finished_at)->toIso8601String(),
        ];
    }
}
