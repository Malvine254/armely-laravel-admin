<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class CareerAvailability
{
    public static function status(mixed $deadline): string
    {
        if ($deadline === null || trim((string) $deadline) === '') {
            return 'Open';
        }

        try {
            return Carbon::parse($deadline)->endOfDay()->lt(now()) ? 'Closed' : 'Open';
        } catch (\Throwable) {
            return 'Unknown';
        }
    }
}