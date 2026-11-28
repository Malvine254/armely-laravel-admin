<?php

namespace App\Support;

/**
 * SSRF guard for the image proxy: resolves a URL's host and returns an
 * address to pin the connection to, but only when every address the host
 * resolves to is on the public internet (no loopback, private, link-local,
 * cloud-metadata or other reserved ranges).
 */
class PublicImageHostResolver
{
    private const ALLOWED_PORTS = [80, 443, 8080, 8443];

    public function resolve(string $url): ?string
    {
        $host = strtolower(trim((string) parse_url($url, PHP_URL_HOST), '[]'));
        if ($host === '' || $host === 'localhost' || str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
            return null;
        }

        $port = parse_url($url, PHP_URL_PORT);
        if ($port !== null && !in_array((int) $port, self::ALLOWED_PORTS, true)) {
            return null;
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : $this->lookup($host);
        if ($addresses === []) {
            return null;
        }

        foreach ($addresses as $address) {
            if (!filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return null;
            }
        }

        return str_contains($addresses[0], ':') ? '[' . $addresses[0] . ']' : $addresses[0];
    }

    /** @return list<string> */
    protected function lookup(string $host): array
    {
        $records = @dns_get_record($host, DNS_A | DNS_AAAA) ?: [];
        $addresses = array_values(array_filter(array_map(
            static fn (array $record): ?string => $record['ip'] ?? $record['ipv6'] ?? null,
            $records
        )));

        if ($addresses === []) {
            $fallback = gethostbyname($host);
            $addresses = $fallback !== $host ? [$fallback] : [];
        }

        return $addresses;
    }
}
