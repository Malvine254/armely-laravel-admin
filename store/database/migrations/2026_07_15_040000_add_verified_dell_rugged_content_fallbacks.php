<?php

use App\Support\VerifiedProductContent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([14506025 => 'T5VPN', 14506026 => 'MC9P0'] as $sku => $mpn) {
            $description = VerifiedProductContent::description($sku, $mpn);
            DB::table('products')
                ->where(fn ($query) => $query->where('tdsynnex_sku_no', $sku)->orWhere('mfg_part_no', $mpn))
                ->where(fn ($query) => $query->whereNull('description')->orWhere('description', '')->orWhereColumn('description', 'product_name'))
                ->update(['description' => $description, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        foreach ([14506025 => 'T5VPN', 14506026 => 'MC9P0'] as $sku => $mpn) {
            $description = VerifiedProductContent::description($sku, $mpn);
            DB::table('products')
                ->where(fn ($query) => $query->where('tdsynnex_sku_no', $sku)->orWhere('mfg_part_no', $mpn))
                ->where('description', $description)
                ->update(['description' => DB::raw('product_name'), 'updated_at' => now()]);
        }
    }
};
