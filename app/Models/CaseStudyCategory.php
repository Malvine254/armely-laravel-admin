<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CaseStudyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'source',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function defaults(): array
    {
        return [
            'Healthcare',
            'Energy (Oil & Gas)',
            'Government & Public Sector',
            'Legal (Social Services)',
            'Transportation & Logistics',
            'Agriculture/Cannabis',
        ];
    }

    public static function syncDefaults(): void
    {
        foreach (self::defaults() as $index => $name) {
            self::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'source' => 'default',
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
