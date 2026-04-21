<?php

namespace App\Support;

class ManufacturerNormalizer
{
    /**
     * Canonical name keyed by every known raw variant (lowercase for lookup).
     * Longer/more-specific keys must appear before shorter ones so they match first.
     */
    private static array $map = [
        'belkin international inc'   => 'Belkin',
        'belkin international'       => 'Belkin',
        'belkin'                     => 'Belkin',
        'apc by schneider electric'  => 'APC',
        'apc'                        => 'APC',
        'schneider electric'         => 'APC',
        'hewlett packard enterprise' => 'HPE',
        'hpe aruba networks'         => 'HPE Aruba',
        'hpe aruba'                  => 'HPE Aruba',
        'aruba networks'             => 'HPE Aruba',
        'hp inc.'                    => 'HP',
        'hp inc'                     => 'HP',
        'epson print'                => 'Epson',
        'epson projection'           => 'Epson',
        'epson'                      => 'Epson',
        'lexmark warranties'         => 'Lexmark',
        'lexmark'                    => 'Lexmark',
        'fortinet inc.'              => 'Fortinet',
        'fortinet inc'               => 'Fortinet',
        'fortinet'                   => 'Fortinet',
        'acer america corporation'   => 'Acer',
        'acer america'               => 'Acer',
        'acer'                       => 'Acer',
        'eaton corporation'          => 'Eaton',
        'eaton'                      => 'Eaton',
        'identiv'                    => 'Identiv',
        'intel corporation'          => 'Intel',
        'intel'                      => 'Intel',
        'ibm storage media'          => 'IBM',
        'ibm'                        => 'IBM',
        'sony corporation'           => 'Sony',
        'sony'                       => 'Sony',
        'sole source technology'     => null, // remove — reseller, not a brand
        'add-on'                     => null, // remove — not a brand
        'add on'                     => null,
        'yubikey'                    => 'Yubico',
        'yubico'                     => 'Yubico',
        'western digital'            => 'WD',
        'wd'                         => 'WD',
        'cisco systems'              => 'Cisco',
        'cisco'                      => 'Cisco',
        'cisco meraki'               => 'Meraki',
        'meraki'                     => 'Meraki',
        'logitech inc'               => 'Logitech',
        'logitech'                   => 'Logitech',
        'kingston technology'        => 'Kingston',
        'kingston'                   => 'Kingston',
        'tripp lite'                 => 'Tripp Lite',
        'tripplite'                  => 'Tripp Lite',
        'kensington'                 => 'Kensington',
        'startech.com'               => 'StarTech',
        'startech'                   => 'StarTech',
        'cyberpower'                 => 'CyberPower',
        'cyber power'                => 'CyberPower',
        'panasonic corporation'      => 'Panasonic',
        'panasonic'                  => 'Panasonic',
        'jabra'                      => 'Jabra',
        'poly'                       => 'Poly',
        'plantronics'                => 'Poly',  // Poly acquired Plantronics
        'ubiquiti networks'          => 'Ubiquiti',
        'ubiquiti'                   => 'Ubiquiti',
        'seagate technology'         => 'Seagate',
        'seagate'                    => 'Seagate',
        'crucial'                    => 'Crucial',
        'micron technology'          => 'Micron',
        'micron'                     => 'Micron',
        'netgear'                    => 'Netgear',
        'brother industries'         => 'Brother',
        'brother'                    => 'Brother',
        'samsung electronics'        => 'Samsung',
        'samsung'                    => 'Samsung',
    ];

    /**
     * Return the canonical manufacturer name, or the original trimmed value if no
     * mapping exists. Returns null for entries that should be removed (resellers etc.).
     */
    public static function normalize(string $raw): ?string
    {
        $key = strtolower(trim($raw));

        if ($key === '') {
            return null;
        }

        if (array_key_exists($key, self::$map)) {
            return self::$map[$key]; // may be null (remove)
        }

        // Partial-prefix fallback: "BELKIN INTERNATIONAL INC DBA ..."
        foreach (self::$map as $pattern => $canonical) {
            if (str_starts_with($key, $pattern)) {
                return $canonical;
            }
        }

        return trim($raw); // keep as-is, preserve original casing
    }
}
