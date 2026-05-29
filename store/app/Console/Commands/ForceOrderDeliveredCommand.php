<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ForceOrderDeliveredCommand extends Command
{
    protected $signature = 'orders:force-delivered
        {reference : Order number, TD order number, quote ID, or tracking number}
        {--at= : Delivered timestamp (any Carbon-parsable datetime). Defaults to now()}
        {--dry-run : Show what would be updated without saving changes}';

    protected $description = 'Force an order to delivered by reference and update related shipment rows.';

    public function handle(): int
    {
        $reference = trim((string) $this->argument('reference'));
        if ($reference === '') {
            $this->error('Reference is required.');
            return self::FAILURE;
        }

        $at = $this->resolveDeliveredAt((string) $this->option('at'));
        if ($at === null) {
            $this->error('Invalid --at datetime value.');
            return self::FAILURE;
        }

        $order = $this->findOrderByReference($reference);
        if (!$order) {
            $this->error("Order not found for reference: {$reference}");
            return self::FAILURE;
        }

        $tracking = is_array($order->tracking_info) ? $order->tracking_info : [];
        $updatedTracking = array_merge($tracking, [
            'shipping_status' => 'delivered',
            'carrier_live_status' => 'delivered',
            'carrier_live_status_normalized' => 'delivered',
            'carrier_live_checked_at' => $at->toIso8601String(),
        ]);

        $shipmentCount = $order->shipments()->count();
        $dryRun = (bool) $this->option('dry-run');

        $this->table(
            ['id', 'order_number', 'quote_id', 'tdsynnex_order_id', 'old_status', 'new_status', 'delivered_at', 'shipments'],
            [[
                $order->id,
                $order->order_number,
                $order->quote_id,
                $order->tdsynnex_order_id,
                $order->status,
                'delivered',
                $at->toDateTimeString(),
                $shipmentCount,
            ]]
        );

        if ($dryRun) {
            $this->warn('Dry run only. No data was changed.');
            return self::SUCCESS;
        }

        $order->status = 'delivered';
        $order->delivered_at = $at;
        $order->tracking_info = $updatedTracking;
        $order->save();

        $order->shipments()->update([
            'status' => 'delivered',
            'delivered_at' => $at,
        ]);

        $order->refresh();

        $this->info('Order marked as delivered successfully.');
        $this->line('Updated tracking_info:');
        $this->line(json_encode($order->tracking_info, JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }

    private function findOrderByReference(string $reference): ?Order
    {
        $order = Order::query()
            ->where('order_number', $reference)
            ->orWhere('tdsynnex_order_id', $reference)
            ->orWhere('quote_id', $reference)
            ->orWhere('tracking_info->tracking_number', $reference)
            ->orWhereHas('shipments', fn ($query) => $query->where('tracking_number', $reference))
            ->first();

        if ($order) {
            return $order;
        }

        $shipment = Shipment::query()
            ->where('tracking_number', $reference)
            ->latest('updated_at')
            ->first();

        return $shipment?->order;
    }

    private function resolveDeliveredAt(string $value): ?Carbon
    {
        $value = trim($value);

        if ($value === '') {
            return now();
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
