<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Quote;
use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class PreflightQuoteConfirmation extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'quote:confirm-preflight
        {quote : Quote internal ID or quote_id value}
        {--submit : Actually submit test order payload to TD SYNNEX API}
        {--skip-api-check : Skip auth/vendor preflight checks}';

    /**
     * The console command description.
     */
    protected $description = 'Validate quote confirmation payload and optionally submit to TD SYNNEX API.';

    public function handle(TDSynnexService $tdsynnexService): int
    {
        $quoteLookup = trim((string) $this->argument('quote'));
        $submit = (bool) $this->option('submit');
        $skipApiCheck = (bool) $this->option('skip-api-check');

        $quote = Quote::query()
            ->with(['user.company.addresses'])
            ->where('id', $quoteLookup)
            ->orWhere('quote_id', $quoteLookup)
            ->first();

        if (!$quote) {
            $this->error("Quote not found for identifier: {$quoteLookup}");
            return self::FAILURE;
        }

        $this->info('Quote confirmation preflight');
        $this->line('Quote ID: ' . (string) $quote->id);
        $this->line('Quote Ref: ' . (string) $quote->quote_id);
        $this->line('Current Status: ' . (string) $quote->status);

        if (method_exists($quote, 'isExpired') && $quote->isExpired()) {
            $this->warn('Quote is expired. Approval should be blocked.');
        }

        if (method_exists($quote, 'canApprove') && !$quote->canApprove()) {
            $this->warn('Quote cannot be approved in current state.');
        }

        $items = $quote->items;
        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
        }
        if (!is_array($items) || empty($items)) {
            $this->error('Quote has no line items to submit.');
            return self::FAILURE;
        }

        $lineItems = array_map(function ($item, $index) {
            $item = is_array($item) ? $item : [];
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $unitPrice = (float) ($item['price'] ?? $item['unitPrice'] ?? 0);

            return [
                'lineNumber' => (string) ($index + 1),
                'partNumber' => $this->resolveTdPartNumber($item),
                'partDescription' => (string) ($item['name'] ?? $item['description'] ?? 'Product'),
                'quantity' => $quantity,
                'unitPrice' => (string) number_format($unitPrice, 2, '.', ''),
                'extendedPrice' => (string) number_format($unitPrice * $quantity, 2, '.', ''),
            ];
        }, $items, array_keys($items));

        $invalidSkuLines = array_values(array_filter($lineItems, function (array $line) {
            $sku = trim((string) ($line['partNumber'] ?? ''));
            return $sku === '' || str_starts_with(strtoupper($sku), 'PART-');
        }));

        if (!empty($invalidSkuLines)) {
            $this->error('Preflight failed: one or more items are missing a real TD SYNNEX SKU.');
            foreach ($invalidSkuLines as $line) {
                $this->line(' - Line ' . ($line['lineNumber'] ?? '?') . ': ' . (string) ($line['partDescription'] ?? 'Unknown product'));
            }
            return self::FAILURE;
        }

        $preflightCompany = $quote->user->company ?? null;
        $preflightShipAddr = $preflightCompany ? $preflightCompany->getDefaultShippingAddress() : null;
        $preflightBillAddr = $preflightCompany ? $preflightCompany->getDefaultBillingAddress() : null;
        $preflightEffShip = $preflightShipAddr ?? $preflightBillAddr;
        $preflightEffBill = $preflightBillAddr ?? $preflightShipAddr;

        $orderData = [
            'poNumber' => (string) $quote->quote_id,
            'poDate' => now()->format('Y-m-d'),
            'shipDate' => now()->addDays(7)->format('Y-m-d'),
            'vendor' => [
                'vendorId' => '12345',
                'vendorName' => 'Armely Store',
            ],
            'billTo' => [
                'companyName' => $preflightCompany->name ?? 'Company',
                'address1' => $preflightEffBill->street_1 ?? '',
                'address2' => $preflightEffBill->street_2 ?? '',
                'city' => $preflightEffBill->city ?? '',
                'state' => $preflightEffBill->state ?? '',
                'postalCode' => $preflightEffBill->postal_code ?? '',
                'country' => $preflightEffBill->country ?? 'US',
                'contactEmail' => $quote->user->email ?? '',
                'contactPhone' => $preflightEffBill->contact_phone ?? $quote->user->phone ?? '',
            ],
            'shipTo' => [
                'companyName' => $preflightCompany->name ?? 'Company',
                'address1' => $preflightEffShip->street_1 ?? '',
                'address2' => $preflightEffShip->street_2 ?? '',
                'city' => $preflightEffShip->city ?? '',
                'state' => $preflightEffShip->state ?? '',
                'postalCode' => $preflightEffShip->postal_code ?? '',
                'country' => $preflightEffShip->country ?? 'US',
                'contactName' => $preflightEffShip->contact_name ?? $quote->user->name ?? '',
                'contactPhone' => $preflightEffShip->contact_phone ?? $quote->user->phone ?? '',
            ],
            'poLine' => $lineItems,
            'poTotal' => (string) number_format((float) ($quote->total_amount ?? 0), 2, '.', ''),
            'poTax' => (string) number_format((float) ($quote->tax_amount ?? 0), 2, '.', ''),
            'poFreight' => '0.00',
        ];

        $this->line('');
        $this->info('Payload preview');
        $this->line('Line count: ' . count($lineItems));
        $this->line('PO Number: ' . $orderData['poNumber']);
        $this->line('PO Total: ' . $orderData['poTotal']);
        $this->line('PO Tax: ' . $orderData['poTax']);
        $this->line('PO Freight: ' . $orderData['poFreight']);

        if (!$skipApiCheck) {
            $this->line('');
            $this->info('API preflight checks');
            try {
                $token = $tdsynnexService->authenticate();
                $this->line('Auth: OK (' . substr($token, 0, 12) . '...)');

                $vendorsResponse = $tdsynnexService->getVendors();
                $vendors = $vendorsResponse['data']['records'] ?? [];
                $this->line('Vendor access: OK (' . count($vendors) . ' vendors)');
            } catch (\Throwable $e) {
                $this->error('API preflight failed: ' . $e->getMessage());
                return self::FAILURE;
            }
        }

        if (!$submit) {
            $this->line('');
            $this->warn('No API order was submitted. Use --submit to place a test order.');
            return self::SUCCESS;
        }

        $this->line('');
        $this->warn('Submitting test order payload to TD SYNNEX API...');

        try {
            $response = $tdsynnexService->placeOrder($orderData);
            $orderNumber = (string) ($response['orderNumber'] ?? $response['orderId'] ?? $response['order_number'] ?? 'UNKNOWN');
            $status = (string) ($response['status'] ?? $response['code'] ?? 'UNKNOWN');

            $this->info('Submission successful');
            $this->line('Returned order number: ' . $orderNumber);
            $this->line('Returned status: ' . $status);
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Submission failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function resolveTdPartNumber(array $item): string
    {
        $directCandidates = [
            $item['partNumber'] ?? null,
            $item['sku'] ?? null,
            $item['mfgPartNo'] ?? null,
            $item['mfg_part_number'] ?? null,
            $item['tdsynnex_sku_no'] ?? null,
            is_array($item['specifications'] ?? null) ? ($item['specifications']['sku'] ?? null) : null,
        ];

        foreach ($directCandidates as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value !== '' && !str_starts_with(strtoupper($value), 'PART-')) {
                return $value;
            }
        }

        $lookupValues = [
            (string) ($item['product_id'] ?? ''),
            (string) ($item['productId'] ?? ''),
            (string) ($item['id'] ?? ''),
            (string) ($item['sku'] ?? ''),
            (string) ($item['partNumber'] ?? ''),
        ];

        foreach ($lookupValues as $lookup) {
            $lookup = trim($lookup);
            if ($lookup === '') {
                continue;
            }

            $productQuery = Product::query()
                ->select(['tdsynnex_sku_no', 'mfg_part_no', 'specifications'])
                ->where('tdsynnex_product_id', $lookup)
                ->orWhere('tdsynnex_sku_no', $lookup)
                ->orWhere('mfg_part_no', $lookup);

            if (ctype_digit($lookup)) {
                $productQuery->orWhere('id', (int) $lookup);
            }

            $product = $productQuery->first();
            if (!$product) {
                continue;
            }

            $specifications = is_array($product->specifications ?? null) ? $product->specifications : [];
            $resolvedCandidates = [
                $product->tdsynnex_sku_no ?? null,
                $specifications['sku'] ?? null,
                $product->mfg_part_no ?? null,
            ];

            foreach ($resolvedCandidates as $resolved) {
                $resolvedValue = trim((string) ($resolved ?? ''));
                if ($resolvedValue !== '') {
                    return $resolvedValue;
                }
            }
        }

        return '';
    }
}
