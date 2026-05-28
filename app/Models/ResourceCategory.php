<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResourceCategory extends Model
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

    public function resources()
    {
        return $this->hasMany(Resource::class, 'category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function syncDefaults(): void
    {
        $existing = self::query()->count();
        $serviceTitles = DB::table('services_lists')
            ->select('title')
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->orderBy('title')
            ->pluck('title')
            ->all();

        $legacyCategories = DB::table('resources')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();

        $defaults = array_values(array_unique(array_filter(array_merge($serviceTitles, $legacyCategories))));

        if ($existing === 0) {
            foreach ($defaults as $index => $name) {
                self::query()->firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'source' => in_array($name, $serviceTitles, true) ? 'service' : 'legacy',
                        'is_active' => true,
                        'sort_order' => $index,
                    ]
                );
            }
            return;
        }

        foreach ($defaults as $index => $name) {
            self::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'source' => in_array($name, $serviceTitles, true) ? 'service' : 'legacy',
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
