<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductContentRunItem extends Model
{
    protected $fillable = [
        'run_id', 'product_id', 'product_name', 'result', 'old_description', 'new_description',
        'description_source', 'image_url', 'warnings', 'duration_ms',
    ];

    protected $casts = [
        'warnings' => 'array',
        'duration_ms' => 'integer',
    ];

    public function toSummary(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'result' => $this->result,
            'old_description' => $this->old_description,
            'new_description' => $this->new_description,
            'description_changed' => $this->new_description !== null && $this->new_description !== $this->old_description,
            'description_source' => $this->description_source,
            'image_url' => $this->image_url,
            'warnings' => (array) $this->warnings,
            'duration_ms' => $this->duration_ms,
            'processed_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
