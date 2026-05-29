<?php

namespace App\Console\Commands;

use App\Jobs\UpdateOrderStatusJob;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class CheckTdOrderStatusCommand extends Command
{
    protected $signature = 'orders:td-status
        {reference : Order number, TD order number, quote ID, or tracking number}
        {--apply : Apply sync update to local order by dispatching UpdateOrderStatusJob synchronously}';

    protected $description = 'Check TD SYNNEX status for a single order reference and optionally apply local sync update.';

    public function handle(TDSynnexService $tdsynnexService): int
    {
        $reference = trim((string) $this->argument('reference'));

        $order = Order::query()
            ->where('order_number', $reference)
            ->orWhere('tdsynnex_order_id', $reference)
            ->orWhere('quote_id', $reference)
            ->orWhere('tracking_info->tracking_number', $reference)
            ->orWhereHas('shipments', fn ($query) => $query->where('tracking_number', $reference))
            ->first();

        if (!$order) {
            $shipment = Shipment::query()
                ->where('tracking_number', $reference)
                ->latest('updated_at')
                ->first();

            if ($shipment) {
                $order = $shipment->order;
            }
        }

        if (!$order) {
            $this->error("Order not found for reference: {$reference}");
            return self::FAILURE;
        }

        $this->line('Local order:');
        $this->table(
            ['id', 'order_number', 'tdsynnex_order_id', 'quote_id', 'status', 'delivered_at'],
            [[
                $order->id,
                $order->order_number,
                $order->tdsynnex_order_id,
                $order->quote_id,
                $order->status,
                optional($order->delivered_at)->toDateTimeString(),
            ]]
        );

        $poNumber = trim((string) ($order->quote_id ?: $order->order_number));
        if ($poNumber === '') {
            $this->error('Cannot query TD status because PO reference is missing (quote_id/order_number empty).');
            return self::FAILURE;
        }

        $this->info("Checking TD SYNNEX PO status using reference: {$poNumber}");
        $response = $tdsynnexService->checkPoStatus($poNumber);

        $rawStatus = $this->deepFindFirstByKeys($response, ['status', 'Status', 'code', 'Code', 'orderStatus', 'OrderStatus', 'poStatus', 'POStatus']);
        $shippingStatus = $this->deepFindFirstByKeys($response, ['shippingStatus', 'shipping_status', 'shipmentStatus', 'ShipmentStatus', 'deliveryStatus', 'DeliveryStatus']);
        $tracking = $this->deepFindFirstByKeys($response, ['tracking_number', 'trackingNumber', 'TrackingNumber', 'carrierTrackingNumber', 'shipmentTrackingNumber', 'proNumber', 'ProNumber']);

        $this->line('TD summary:');
        $this->table(
            ['raw_status', 'shipping_status', 'tracking_number'],
            [[(string) $rawStatus, (string) $shippingStatus, (string) $tracking]]
        );

        if ($this->option('apply')) {
            $this->info('Applying sync update via UpdateOrderStatusJob (sync)...');
            UpdateOrderStatusJob::dispatchSync($order);
            $order->refresh();

            $this->line('Local order after apply:');
            $this->table(
                ['id', 'order_number', 'status', 'delivered_at', 'tracking_info'],
                [[
                    $order->id,
                    $order->order_number,
                    $order->status,
                    optional($order->delivered_at)->toDateTimeString(),
                    json_encode($order->tracking_info),
                ]]
            );
        }

        return self::SUCCESS;
    }

    private function deepFindFirstByKeys(mixed $data, array $keys): mixed
    {
        if (!is_array($data)) {
            return null;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '') {
                return $data[$key];
            }
        }

        foreach ($data as $value) {
            $found = $this->deepFindFirstByKeys($value, $keys);
            if ($found !== null && $found !== '') {
                return $found;
            }
        }

        return null;
    }
}
