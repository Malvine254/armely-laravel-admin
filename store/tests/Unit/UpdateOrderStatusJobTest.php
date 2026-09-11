<?php

namespace Tests\Unit;

use App\Jobs\UpdateOrderStatusJob;
use App\Models\Order;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class UpdateOrderStatusJobTest extends TestCase
{
    public static function shipmentStatuses(): array
    {
        return [
            'supplier invoiced' => ['invoiced', 'pending'],
            'supplier accepted' => ['accepted', 'pending'],
            'supplier backordered' => ['backordered', 'pending'],
            'carrier shipped' => ['shipped', 'in_transit'],
            'carrier in transit' => ['in_transit', 'in_transit'],
            'carrier delivered' => ['delivered', 'delivered'],
            'carrier returned' => ['returned to shipper', 'returned'],
            'carrier exception' => ['delivery exception', 'pending'],
            'unknown status' => ['unknown', 'pending'],
        ];
    }

    #[DataProvider('shipmentStatuses')]
    public function test_it_maps_external_statuses_to_valid_shipment_statuses(
        string $externalStatus,
        string $expectedStatus
    ): void {
        $job = new UpdateOrderStatusJob(new Order());
        $method = new ReflectionMethod($job, 'normalizeShipmentStatus');

        $this->assertSame($expectedStatus, $method->invoke($job, $externalStatus));
    }
}