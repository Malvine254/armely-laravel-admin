<?php

use App\Models\AppSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $current = AppSetting::getValue('pricing.profit_rate_percent');

        // The setting is percentage-based. A saved value of 1.15 was intended as
        // a multiplier; convert it to 15 so customer price = supplier price × 1.15.
        if ($current === null || abs((float) $current - 1.15) < 0.00001) {
            AppSetting::setValue('pricing.profit_rate_percent', 15);
        }
    }

    public function down(): void
    {
        if (abs(AppSetting::getNumber('pricing.profit_rate_percent') - 15) < 0.00001) {
            AppSetting::setValue('pricing.profit_rate_percent', 1.15);
        }
    }
};
