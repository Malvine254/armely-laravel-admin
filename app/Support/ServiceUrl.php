<?php

namespace App\Support;

use Illuminate\Support\Str;

class ServiceUrl
{
    private const TITLE_TO_SLUG = [
        'ai consulting' => 'ai-consulting',
        'ai advisory' => 'ai-advisory',
        'generative ai' => 'generative-ai',
        'ai poc starter' => 'ai-poc-starter',
        'estimate your fabric capacity' => 'estimate-your-fabric-capacity',
        'microsoft fabric' => 'microsoft-fabric',
        'data science and analytics' => 'data-science-and-analytics',
        'data strategy' => 'data-strategy',
        'databricks' => 'databricks',
        'snowflake' => 'snowflake',
        'sql & data warehousing' => 'sql-data-warehousing',
        'api data access' => 'api-data-access',
        'microsoft powerapps' => 'microsoft-powerapps',
        'microsoft power automate' => 'microsoft-power-automate',
        'microsoft power virtual agents' => 'microsoft-power-virtual-agents',
        'microsoft power pages' => 'microsoft-power-pages',
        'microsoft dynamics 365' => 'microsoft-dynamics-365',
        'robotic processing automation' => 'robotic-processing-automation',
        'sharepoint online' => 'sharepoint-online',
        'microsoft 365 governance and adoption' => 'm365-governance',
        'managed services' => 'managed-services',
        'sql server support' => 'sql-server-support',
        'applications support' => 'applications-support',
        'freemiums' => 'freemiums',
    ];

    private const SLUG_ALIASES = [
        'fabric' => 'microsoft-fabric',
        'ai-consulting' => 'ai-consulting',
        'ai-advisory' => 'ai-advisory',
        'generative-ai' => 'generative-ai',
        'ai-poc-starter' => 'ai-poc-starter',
        'estimate-your-fabric-capacity' => 'estimate-your-fabric-capacity',
        'microsoft-fabric' => 'microsoft-fabric',
        'data-science' => 'data-science-and-analytics',
        'data-science-and-analytics' => 'data-science-and-analytics',
        'data-strategy' => 'data-strategy',
        'databricks' => 'databricks',
        'snowflake' => 'snowflake',
        'sql-data-warehousing' => 'sql-data-warehousing',
        'sql-&-data-warehousing' => 'sql-data-warehousing',
        'api-data-access' => 'api-data-access',
        'api-dev' => 'api-data-access',
        'api-development' => 'api-data-access',
        'microsoft-powerapps' => 'microsoft-powerapps',
        'powerapps' => 'microsoft-powerapps',
        'power-apps' => 'microsoft-powerapps',
        'microsoft-power-automate' => 'microsoft-power-automate',
        'powerautomate' => 'microsoft-power-automate',
        'power-automate' => 'microsoft-power-automate',
        'microsoft-power-virtual-agents' => 'microsoft-power-virtual-agents',
        'virtualagents' => 'microsoft-power-virtual-agents',
        'virtual-agents' => 'microsoft-power-virtual-agents',
        'microsoft-power-pages' => 'microsoft-power-pages',
        'powerplatform' => 'microsoft-power-pages',
        'power-platform' => 'microsoft-power-pages',
        'microsoft-dynamics-365' => 'microsoft-dynamics-365',
        'dynamics365' => 'microsoft-dynamics-365',
        'dynamics-365' => 'microsoft-dynamics-365',
        'robotic-processing-automation' => 'robotic-processing-automation',
        'roboticprocessing' => 'robotic-processing-automation',
        'robotic-process-automation' => 'robotic-processing-automation',
        'rpa' => 'robotic-processing-automation',
        'sharepoint-online' => 'sharepoint-online',
        'sharepointonline' => 'sharepoint-online',
        'sharepoint' => 'sharepoint-online',
        'm365-governance' => 'm365-governance',
        'm365' => 'm365-governance',
        'microsoft-365-governance' => 'm365-governance',
        'microsoft-365-governance-and-adoption' => 'm365-governance',
        'managed-services' => 'managed-services',
        'sql-server-support' => 'sql-server-support',
        'applications-support' => 'applications-support',
        'freemiums' => 'freemiums',
    ];

    public static function canonicalSlug(object|array|string|null $service, string $titleKey = 'title', string $slugKey = 'url_name'): string
    {
        $candidate = self::candidateValue($service, $titleKey, $slugKey);
        if ($candidate === '') {
            return '';
        }

        $lower = Str::lower($candidate);
        if (isset(self::TITLE_TO_SLUG[$lower])) {
            return self::TITLE_TO_SLUG[$lower];
        }

        $slug = Str::slug($candidate);
        if ($slug !== '' && isset(self::SLUG_ALIASES[$slug])) {
            return self::SLUG_ALIASES[$slug];
        }

        return $slug;
    }

    public static function path(object|array|string|null $service, string $titleKey = 'title', string $slugKey = 'url_name'): string
    {
        $slug = self::canonicalSlug($service, $titleKey, $slugKey);

        return $slug !== '' ? '/services/' . $slug : '/services';
    }

    public static function url(object|array|string|null $service, string $titleKey = 'title', string $slugKey = 'url_name'): string
    {
        return url(self::path($service, $titleKey, $slugKey));
    }

    private static function candidateValue(object|array|string|null $service, string $titleKey, string $slugKey): string
    {
        if ($service === null) {
            return '';
        }

        if (is_string($service)) {
            return trim($service);
        }

        $title = trim((string) self::value($service, $titleKey));
        if ($title !== '') {
            return $title;
        }

        $slug = trim((string) self::value($service, $slugKey));
        if ($slug !== '') {
            return $slug;
        }

        return trim((string) self::value($service, 'name'));
    }

    private static function value(object|array $service, string $key): mixed
    {
        if (is_array($service)) {
            return $service[$key] ?? null;
        }

        return $service->{$key} ?? null;
    }
}
