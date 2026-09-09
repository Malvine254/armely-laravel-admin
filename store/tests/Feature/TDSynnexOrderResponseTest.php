<?php

namespace Tests\Feature;

use App\Services\TDSynnexService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TDSynnexOrderResponseTest extends TestCase
{
    public function test_root_order_response_exposes_supplier_order_number(): void
    {
        config()->set('tdsynnex.price_availability.customer_no', 'customer');
        config()->set('tdsynnex.price_availability.username', 'user');
        config()->set('tdsynnex.price_availability.password', 'password');

        Http::fake([
            '*' => Http::response(
                '<?xml version="1.0"?><OrderResponse><Code>ACCEPTED</Code><Reason>Accepted</Reason>'
                . '<OrderNumber>1234567890</OrderNumber><PONumber>Q-TEST-1</PONumber></OrderResponse>',
                200,
                ['Content-Type' => 'application/xml']
            ),
        ]);

        $response = app(TDSynnexService::class)->placeOrder([
            'poNumber' => 'Q-TEST-1',
            'poDate' => '2026-09-09',
            'shipTo' => ['companyName' => 'Armely'],
            'poLine' => [[
                'lineNumber' => '1',
                'partNumber' => '1234567',
                'quantity' => 1,
                'unitPrice' => '10.00',
            ]],
        ], 'us', false);

        $this->assertSame('1234567890', $response['orderNumber']);
        $this->assertSame('ACCEPTED', $response['status']);
        $this->assertSame('Q-TEST-1', $response['poNumber']);
    }
}
