<?php

namespace App\Services\Mela\Knowledge;

class VectorCodec
{
    /**
     * @param  array<int, float>  $vector
     */
    public static function pack(array $vector): string
    {
        return pack('g*', ...$vector);
    }

    /**
     * @return array<int, float>
     */
    public static function unpack(string $binary): array
    {
        return array_values(unpack('g*', $binary) ?: []);
    }

    /**
     * Dot product of unit vectors, i.e. cosine similarity.
     *
     * @param  array<int, float>  $a
     * @param  array<int, float>  $b
     */
    public static function dot(array $a, array $b): float
    {
        $sum = 0.0;
        $n = min(count($a), count($b));
        for ($i = 0; $i < $n; $i++) {
            $sum += $a[$i] * $b[$i];
        }

        return $sum;
    }
}
