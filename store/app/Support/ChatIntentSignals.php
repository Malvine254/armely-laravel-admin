<?php

namespace App\Support;

use Illuminate\Support\Str;

class ChatIntentSignals
{
    public static function normalizeQuestion(string $question): string
    {
        $normalized = mb_strtolower(trim($question));
        $normalized = preg_replace('/\s+/u', ' ', $normalized) ?? $normalized;

        return $normalized;
    }

    public static function isGeneralConversationQuery(string $question): bool
    {
        return self::isGreetingQuery($question)
            || self::isCapabilityQuestion($question)
            || self::isThanksQuery($question);
    }

    public static function isGreetingQuery(string $question): bool
    {
        $q = self::normalizeQuestion($question);

        return $q !== '' && (
            (bool) preg_match('/^(hi|hello|hey|yo|howdy|sup)(?:[\s\p{P}].*)?$/u', $q)
            || (bool) preg_match('/^good (morning|afternoon|evening)(?:[\s\p{P}].*)?$/u', $q)
        );
    }

    public static function isThanksQuery(string $question): bool
    {
        $q = self::normalizeQuestion($question);

        return $q !== '' && (bool) preg_match('/\b(thanks?|thank you|thx|appreciate it)\b/u', $q);
    }

    public static function isCapabilityQuestion(string $question): bool
    {
        $q = self::normalizeQuestion($question);

        return $q !== '' && self::matchesAnyPattern($q, [
            '/\bwhat can you do\b/u',
            '/\bwhat do you do\b/u',
            '/\bwhat do you help with\b/u',
            '/\bwhat are your roles?\b/u',
            '/\bwhat are your capabilities?\b/u',
            '/\bwhat are you capable of\b/u',
            '/\bwhat can you help with\b/u',
            '/\bcapabilities?\b/u',
            '/\bfeatures?\b/u',
            '/\boptions?\b/u',
        ]);
    }

    public static function isQuoteIntentQuery(string $question): bool
    {
        $q = self::normalizeQuestion($question);

        return $q !== '' && self::matchesAnyPattern($q, [
            '/\bquote(s)?\b/u',
            '/\brequote\b/u',
            '/\bquote history\b/u',
            '/\bquote status\b/u',
            '/\bmy quotes\b/u',
            '/\blast quote\b/u',
            '/\blatest quote\b/u',
            '/\bmost recent quote\b/u',
            '/\bfirst quote\b/u',
            '/\blast\s+\d+\s+quotes?\b/u',
        ]);
    }

    public static function isOrderIntentQuery(string $question): bool
    {
        $q = self::normalizeQuestion($question);

        return $q !== '' && self::matchesAnyPattern($q, [
            '/\border(s)?\b/u',
            '/\btracking\b/u',
            '/\btrack(ing)?\b/u',
            '/\bshipping\b/u',
            '/\bdelivery\b/u',
            '/\bshipment\b/u',
            '/\blatest order\b/u',
            '/\bmost recent order\b/u',
            '/\blast order\b/u',
        ]);
    }

    public static function isInvoiceIntentQuery(string $question): bool
    {
        $q = self::normalizeQuestion($question);

        return $q !== '' && self::matchesAnyPattern($q, [
            '/\binvoice(s)?\b/u',
            '/\bpayment(s)?\b/u',
            '/\bbilling\b/u',
            '/\breceipt\b/u',
            '/\bbalance\b/u',
            '/\bdue\b/u',
            '/\boutstanding\b/u',
            '/\bdownload pdf\b/u',
            '/\binvoice pdf\b/u',
            '/\bwhat do i owe\b/u',
        ]);
    }

    public static function isProductLookupIntent(string $question, array $recentChatTurns = []): bool
    {
        $q = self::normalizeQuestion($question);
        if ($q === '') {
            return false;
        }

        if (self::containsAnyPattern($q, [
            '/\bquote(s)?\b/u',
            '/\border(s)?\b/u',
            '/\binvoice(s)?\b/u',
            '/\bpayment(s)?\b/u',
            '/\bbilling\b/u',
            '/\breceipt\b/u',
            '/\bbalance\b/u',
            '/\bdue\b/u',
            '/\btracking\b/u',
            '/\bshipping\b/u',
            '/\bdelivery\b/u',
        ])) {
            return false;
        }

        if (!empty(self::extractProductSearchKeywords($question))) {
            return true;
        }

        if (self::containsAnyPattern($q, [
            '/\bsearch for\b/u',
            '/\bfind(?: me)?\b/u',
            '/\blooking for\b/u',
            '/\blook for\b/u',
            '/\bbrowse\b/u',
            '/\blookup\b/u',
            '/\bcheck for\b/u',
            '/\bcheck the\b/u',
            '/\bavailable\b/u',
            '/\bavailability\b/u',
            '/\bin stock\b/u',
            '/\bproduct table\b/u',
            '/\bproducts table\b/u',
            '/\bproduct catalog\b/u',
            '/\bproducts catalog\b/u',
            '/\bcatalogue?\b/u',
            '/\brecommend\b/u',
            '/\bsuggest(?:ion|ions|ed)?\b/u',
        ])) {
            return true;
        }

        $recentSuggestedProducts = collect($recentChatTurns)
            ->filter(static fn (array $turn) => strtolower((string) ($turn['role'] ?? '')) === 'assistant')
            ->flatMap(static fn (array $turn) => (array) ($turn['product_suggestions'] ?? []))
            ->contains(static fn ($item) => is_array($item) && !empty($item['product_id']));

        return $recentSuggestedProducts && self::containsAnyPattern($q, [
            '/\bit\b/u',
            '/\bthat\b/u',
            '/\bthose\b/u',
            '/\bthem\b/u',
            '/\bsimilar\b/u',
            '/\bmore\b/u',
            '/\banother\b/u',
        ]);
    }

    public static function isCatalogQueryAudit(string $question): bool
    {
        $q = self::normalizeQuestion($question);

        return $q !== '' && self::matchesAnyPattern($q, [
            '/\bwhich (?:search )?query did you use\b/u',
            '/\bwhat (?:search )?query did you use\b/u',
            '/\bwhat did you search(?: for)?\b/u',
            '/\bshow me the (?:search )?query\b/u',
            '/\brepeat the (?:search )?query\b/u',
        ]);
    }

    public static function extractCatalogSearchPhrase(string $question): string
    {
        $phrase = trim($question);
        $phrase = preg_replace(
            '/^\s*(?:please\s+)?(?:(?:can|could|would)\s+you\s+)?(?:i\s+(?:need|want|am\s+looking\s+for)|we\s+(?:need|want)|now\s+(?:do|search)(?:\s+for)?|search(?:\s+the\s+catalog)?(?:\s+for)?(?:\s+me)?|find(?:\s+me)?|look\s+for|lookup|check\s+for|show\s+me|compare)\s+/iu',
            '',
            $phrase
        ) ?? $phrase;
        $phrase = preg_replace(
            '/\s*(?:,|\-|\.|\?)?\s*(?:which\s+one\s+do\s+you\s+recommend|what\s+do\s+you\s+recommend|can\s+you\s+recommend(?:\s+one)?|which\s+(?:one\s+)?is\s+best|show\s+me\s+the\s+best(?:\s+one)?)\s*[?.!]*$/iu',
            '',
            $phrase
        ) ?? $phrase;
        $phrase = preg_replace('/\s*[,;—-]?\s*(?:but\s+)?(?:exclude|excluding|without|do\s+not\s+show|don[’\']t\s+show)\b.*$/iu', '', $phrase) ?? $phrase;
        // Inventory wording describes the request, not the product being searched.
        $phrase = preg_replace('/^\s*(?:the|our)\s+/iu', '', $phrase) ?? $phrase;
        $phrase = preg_replace('/\s+(?:that\s+)?(?:we|you)\s+(?:have|carry|stock|sell)(?:\s+(?:available|in\s+stock))?\s*$/iu', '', $phrase) ?? $phrase;
        $phrase = preg_replace('/\s+(?:(?:options?\s+)?instead(?:\s+please)?|ones?|options?)\s*$/iu', '', $phrase) ?? $phrase;
        $phrase = trim($phrase, " \t\n\r\0\x0B?!.\"");

        $categoryTerms = [
            'monitors' => 'monitor',
            'printers' => 'printer',
            'laptops' => 'laptop',
            'notebooks' => 'notebook',
            'desktops' => 'desktop',
            'servers' => 'server',
            'switches' => 'switch',
            'routers' => 'router',
            'scanners' => 'scanner',
            'projectors' => 'projector',
            'tablets' => 'tablet',
            'workstations' => 'workstation',
        ];
        $lowerPhrase = mb_strtolower($phrase);
        if (isset($categoryTerms[$lowerPhrase])) {
            $phrase = $categoryTerms[$lowerPhrase];
        }

        return preg_replace('/\s+/u', ' ', $phrase) ?? $phrase;
    }

    public static function extractCatalogSearchPhrases(string $question): array
    {
        $phrase = self::extractCatalogSearchPhrase($question);
        if ($phrase === '') {
            return [];
        }

        $parts = preg_split('/\s*(?:,|;|\band\b|\bor\b)\s*/iu', $phrase) ?: [];
        $parts = array_values(array_filter(array_map('trim', $parts)));
        if (count($parts) < 2) {
            return [$phrase];
        }

        $categoryPattern = '/\b(monitor|display|printer|scanner|laptop|notebook|desktop|workstation|server|switch|router|firewall|projector|tablet|ups|storage)s?\b/iu';
        $categoryParts = array_values(array_filter(
            $parts,
            static fn (string $part) => preg_match($categoryPattern, $part) === 1
        ));

        // A shared product type can apply to several brand clauses: "Samsung and LG monitors"
        // becomes two independent searches rather than an impossible Samsung+LG product.
        $brandPattern = '/\b(dell|hp|hewlett[ -]packard|lenovo|cisco|meraki|microsoft|apple|samsung|epson|brother|canon|asus|acer|logitech|jabra|netgear|ubiquiti|fortinet|aruba|juniper|sophos|intel|amd|nvidia|toshiba|xerox|ricoh|lexmark|benq|lg|panasonic|viewsonic|poly|dynabook|msi)\b/iu';
        if (count($categoryParts) >= 1) {
            preg_match($categoryPattern, implode(' ', $categoryParts), $sharedCategoryMatch);
            $sharedCategory = strtolower((string) ($sharedCategoryMatch[1] ?? ''));
            $allPartsAreTyped = collect($parts)->every(
                static fn (string $part) => preg_match($categoryPattern, $part) === 1 || preg_match($brandPattern, $part) === 1
            );

            if ($sharedCategory !== '' && $allPartsAreTyped) {
                $parts = array_map(static function (string $part) use ($categoryPattern, $sharedCategory) {
                    return preg_match($categoryPattern, $part) === 1 ? $part : trim($part . ' ' . $sharedCategory);
                }, $parts);
                $categoryParts = $parts;
            }
        }

        // Split only when every clause names its own product type. This keeps specification
        // phrases such as "monitor with HDMI and USB-C" as one catalog query.
        if (count($categoryParts) !== count($parts)) {
            return [$phrase];
        }

        return array_values(array_unique(array_map(
            static fn (string $part) => self::extractCatalogSearchPhrase($part),
            $parts
        )));
    }

    public static function extractProductSearchKeywords(string $question): array
    {
        $normalized = self::normalizeQuestion($question);
        $parts = preg_split('/[^a-z0-9-]+/i', $normalized) ?: [];
        $stopWords = [
            'need', 'purchase', 'buy', 'best', 'give', 'me', 'my', 'your', 'our', 'its', 'their',
            'sample', 'list', 'for', 'the', 'is', 'it', 'if', 'in', 'on', 'at', 'of', 'be', 'no', 'so',
            'and', 'or', 'with', 'show', 'please', 'can', 'you', 'want', 'from', 'that', 'this',
            'have', 'all', 'more', 'details', 'about', 'find', 'search', 'suggestion', 'suggestions',
            'suggest', 'suggested', 'recommended', 'recommend', 'available', 'current', 'from',
            'product', 'products', 'item', 'items', 'one', 'two', 'three', 'hi', 'hello', 'hey', 'to', 'today',
            'last', 'latest', 'recent', 'newest', 'first', 'earliest', 'oldest', 'previous', 'former',
            'order', 'quote', 'quotes', 'cart', 'make', 'proceed', 'request', 'them', 'those', 'are', 'please',
            'add', 'added', 'placing', 'place', 'good', 'looking', 'look', 'get', 'some', 'any',
            'what', 'which', 'would', 'could', 'should', 'like', 'price', 'under', 'below', 'above',
            'something', 'anything', 'around', 'great', 'nice', 'new', 'do', 'does', 'got',
            'invoice', 'invoices', 'payment', 'payments', 'billing', 'receipt', 'balance', 'due',
            'track', 'tracking', 'shipping', 'delivery', 'use', 'used', 'using', 'query', 'did',
            'check', 'we', 'us', 'carry', 'stock', 'sell', 'instead', 'ones', 'option', 'options', 'now',
            'prefer', 'exclude', 'excluding', 'accessory', 'accessories', 'ii',
        ];

        $keywords = collect($parts)
            ->map(static fn ($p) => trim((string) $p))
            ->filter(static fn ($p) => strlen($p) >= 2)
            ->filter(static fn ($p) => !in_array($p, $stopWords, true))
            ->unique()
            ->values()
            ->all();

        $keywords = array_values(array_unique(array_map(
            static fn (string $keyword) => strtolower(Str::singular($keyword)),
            $keywords
        )));

        if (str_contains($normalized, 'laptop') && !in_array('notebook', $keywords, true)) {
            $keywords[] = 'notebook';
        }
        if (str_contains($normalized, 'notebook') && !in_array('laptop', $keywords, true)) {
            $keywords[] = 'laptop';
        }

        return array_values(array_unique($keywords));
    }

    public static function extractExcludedProductTerms(string $question): array
    {
        if (preg_match('/(?:exclude|excluding|without|do\s+not\s+show|don[’\']t\s+show)\b(.+)$/iu', $question, $matches) !== 1) {
            return [];
        }

        return self::extractProductSearchKeywords((string) ($matches[1] ?? ''));
    }

    private static function matchesAnyPattern(string $question, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (@preg_match($pattern, $question) === 1) {
                return true;
            }
        }

        return false;
    }

    private static function containsAnyPattern(string $question, array $patterns): bool
    {
        return self::matchesAnyPattern($question, $patterns);
    }
}
