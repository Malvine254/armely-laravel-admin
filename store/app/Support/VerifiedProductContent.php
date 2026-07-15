<?php

namespace App\Support;

final class VerifiedProductContent
{
    private const PRODUCTS = [
        '14506025' => [
            'mpn' => 'T5VPN',
            'description' => 'Dell Pro Rugged RB14250 laptop with 512 GB M.2 2230 PCIe NVMe SSD, Windows 11 Pro, Intel Core Ultra 5 135U (12 cores, up to 4.4 GHz, 15 W), and 16 GB DDR5-5600 memory (2 x 8 GB, non-ECC SoDIMM).',
        ],
        '14506026' => [
            'mpn' => 'MC9P0',
            'description' => 'Dell Pro Rugged 14 RB14250 laptop with 512 GB M.2 2230 PCIe NVMe SSD, Windows 11 Pro, Intel Core Ultra 7 165U (12 cores, up to 4.9 GHz, 15 W), and 16 GB DDR5-5600 memory (2 x 8 GB, non-ECC SoDIMM).',
        ],
    ];

    public static function description(string|int|null $sku, ?string $mpn = null): ?string
    {
        $sku = trim((string) $sku);
        if ($sku !== '' && isset(self::PRODUCTS[$sku])) {
            return self::PRODUCTS[$sku]['description'];
        }

        $mpn = strtoupper(trim((string) $mpn));
        foreach (self::PRODUCTS as $product) {
            if ($mpn !== '' && $mpn === $product['mpn']) {
                return $product['description'];
            }
        }

        return null;
    }

    public static function meaningfulDescription(?string $description, ?string $productName, string|int|null $sku, ?string $mpn = null): string
    {
        $description = trim((string) $description);
        $productName = trim((string) $productName);
        if ($description !== '' && strcasecmp($description, $productName) !== 0) {
            return $description;
        }

        return self::description($sku, $mpn) ?? $description;
    }
}
