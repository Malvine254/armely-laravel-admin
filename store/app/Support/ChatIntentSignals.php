<?php

namespace App\Support;

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
            '/\bcheck\b/u',
        ]);
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
            'track', 'tracking', 'shipping', 'delivery',
        ];

        $keywords = collect($parts)
            ->map(static fn ($p) => trim((string) $p))
            ->filter(static fn ($p) => strlen($p) >= 2)
            ->filter(static fn ($p) => !in_array($p, $stopWords, true))
            ->unique()
            ->values()
            ->all();

        $brandExpansions = [
            'hp' => ['hp', 'hewlett-packard', 'hewlett packard'],
            'dell' => ['dell'],
            'lenovo' => ['lenovo'],
            'cisco' => ['cisco', 'meraki'],
        ];

        foreach ($brandExpansions as $brand => $variants) {
            if (str_contains($normalized, $brand)) {
                foreach ($variants as $variant) {
                    if (!in_array($variant, $keywords, true)) {
                        $keywords[] = $variant;
                    }
                }
            }
        }

        if (str_contains($normalized, 'laptop') && !in_array('notebook', $keywords, true)) {
            $keywords[] = 'notebook';
        }
        if (str_contains($normalized, 'notebook') && !in_array('laptop', $keywords, true)) {
            $keywords[] = 'laptop';
        }

        return array_values(array_unique($keywords));
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
