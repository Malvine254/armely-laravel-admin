<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $offers = [
            'T5VPN' => ['sku' => 14506025, 'offer' => 2727.02, 'regular' => 2914.35, 'msrp' => 4644.52],
            'MC9P0' => ['sku' => 14506026, 'offer' => 2593.86, 'regular' => 2759.89, 'msrp' => 4398.36],
        ];

        foreach ($offers as $mpn => $pricing) {
            DB::table('products')
                ->where(function ($query) use ($mpn, $pricing) {
                    $query->where('tdsynnex_sku_no', $pricing['sku'])->orWhere('mfg_part_no', $mpn);
                })
                ->update([
                    'base_price' => $pricing['offer'],
                    'supplier_regular_price' => $pricing['regular'],
                    'retail_price' => $pricing['msrp'],
                    'sale_price' => $pricing['offer'],
                    'is_on_sale' => true,
                    'offer_source' => 'verified_tdsynnex_special',
                    'sale_started_at' => now(),
                    'sale_ended_at' => null,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        DB::table('products')->whereIn('mfg_part_no', ['T5VPN', 'MC9P0'])->update([
            'sale_price' => null,
            'is_on_sale' => false,
            'offer_source' => null,
            'sale_started_at' => null,
            'sale_ended_at' => now(),
        ]);
    }
};
