<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ResourceCategory;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'category',
        'resource_type',
        'file_url',
        'file_name',
        'file_path',
        'thumbnail_url',
        'thumbnail_path',
        'is_published',
        'is_featured',
        'is_noindex',
        'click_count',
        'download_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'is_noindex' => 'boolean',
        'click_count' => 'integer',
        'download_count' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function isVideo(): bool
    {
        return $this->resource_type === 'video';
    }

    public function resourceCategory()
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }
}
