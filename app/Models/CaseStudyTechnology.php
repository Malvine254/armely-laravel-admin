<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseStudyTechnology extends Model
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

    /**
     * Seed technologies as [name => slug]. The first four keep their historical
     * slugs (fabric-data, power-platform, ai-cognitive-services,
     * sharepoint-collaboration) so existing filters, white papers, and stored
     * values continue to resolve.
     */
    public static function defaults(): array
    {
        return [
            'Microsoft Fabric and Data' => 'fabric-data',
            'Power BI' => 'power-bi',
            'Power Platform' => 'power-platform',
            'AI and Cognitive Services' => 'ai-cognitive-services',
            'SharePoint and Collaboration' => 'sharepoint-collaboration',
            'Azure Data Platform' => 'azure-data-platform',
            'Snowflake' => 'snowflake',
            'Microsoft Access' => 'microsoft-access',
            'Data Platform Migration' => 'data-platform-migration',
            "Blackbaud Raiser's Edge NXT" => 'blackbaud-raisers-edge-nxt',
            'OpenInvoice' => 'openinvoice',
        ];
    }

    public static function syncDefaults(): void
    {
        $index = 0;
        foreach (self::defaults() as $name => $slug) {
            self::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'source' => 'default',
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
            $index++;
        }
    }
}
