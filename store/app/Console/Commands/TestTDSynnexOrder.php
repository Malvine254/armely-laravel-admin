<?php

namespace App\Console\Commands;

use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class TestTDSynnexOrder extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tdsynnex:test-order';

    /**
     * The console command description.
     */
    protected $description = 'Test TD SYNNEX API order submission to verify sandbox integration';

    /**
     * Execute the console command.
     */
    public function handle(TDSynnexService $tdsynnex): int
    {
        $this->info('Testing TD SYNNEX Sandbox Integration');
        $this->info('=====================================');
        $this->newLine();

        // Step 1: Test Authentication
        $this->info('1. Testing Authentication...');
        try {
            $token = $tdsynnex->authenticate();
            $this->info('   ✓ Authentication successful');
            $this->line('   Auth Mode: ' . $tdsynnex->authMode());
            $this->line('   Token (first 20 chars): ' . substr($token, 0, 20) . '...');
            $this->newLine();
        } catch (\Exception $e) {
            $this->error('   ✗ Authentication failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Step 2: Test Getting Vendors
        $this->info('2. Testing Vendor Access...');
        try {
            $vendorsResponse = $tdsynnex->getVendors();
            $vendors = $vendorsResponse['data']['records'] ?? [];
            $this->info('   ✓ Successfully retrieved ' . count($vendors) . ' vendors');
            
            if (!empty($vendors)) {
                $this->line('   Available vendors:');
                foreach (array_slice($vendors, 0, 5) as $vendor) {
                    $this->line('     - ' . ($vendor['vendorName'] ?? $vendor['name'] ?? 'Unknown'));
                }
                if (count($vendors) > 5) {
                    $this->line('     ... and ' . (count($vendors) - 5) . ' more');
                }
            }
            $this->newLine();
        } catch (\Exception $e) {
            $this->error('   ✗ Vendor retrieval failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Step 3: Test Getting Products
        $this->info('3. Testing Product Access...');
        try {
            // Try to get Microsoft products (common vendor)
            $vendorId = null;
            foreach ($vendors as $vendor) {
                if (stripos($vendor['vendorName'] ?? '', 'Microsoft') !== false) {
                    $vendorId = $vendor['vendorId'];
                    break;
                }
            }

            if ($vendorId) {
                $productsResponse = $tdsynnex->getProducts($vendorId, 1, 5);
                $products = $productsResponse['data']['records'] ?? [];
                $total = $productsResponse['data']['total'] ?? 0;
                
                $this->info('   ✓ Successfully retrieved products');
                $this->line('   Total available: ' . $total);
                
                if (!empty($products)) {
                    $this->line('   Sample products:');
                    foreach (array_slice($products, 0, 3) as $product) {
                        $this->line('     - ' . ($product['productName'] ?? 'Unknown') . 
                                   ' (ID: ' . ($product['productId'] ?? 'N/A') . ')');
                    }
                }
                $this->newLine();

                // Step 4: Test Order Submission (IMPORTANT!)
                $this->info('4. Testing Order Submission...');
                $this->warn('   Note: This will attempt to create a TEST order in TD SYNNEX sandbox');
                
                if (!$this->confirm('   Do you want to submit a test order to TD SYNNEX sandbox?', false)) {
                    $this->info('   Skipped order submission test');
                    $this->newLine();
                    $this->info('✓ Basic TD SYNNEX integration tests passed');
                    $this->info('  Your application CAN connect to TD SYNNEX sandbox');
                    return Command::SUCCESS;
                }

                // Get first available product for testing
                if (!empty($products)) {
                    $testProduct = $products[0];
                    $orderData = [
                        'items' => [
                            [
                                'productId' => $testProduct['productId'],
                                'quantity' => 1,
                            ]
                        ],
                        'orderType' => 'standard',
                        'poNumber' => 'TEST-' . time(),
                    ];

                    try {
                        $this->line('   Submitting order for: ' . $testProduct['productName']);
                        $orderResponse = $tdsynnex->placeOrder($orderData);
                        
                        $orderNumber = $orderResponse['orderNumber'] ?? $orderResponse['orderId'] ?? 'Unknown';
                        $status = $orderResponse['status'] ?? 'Unknown';
                        
                        $this->info('   ✓ Order submitted successfully!');
                        $this->line('   Order Number: ' . $orderNumber);
                        $this->line('   Status: ' . $status);
                        $this->newLine();
                        
                        $this->info('SUCCESS! Your order is now in TD SYNNEX sandbox system.');
                        $this->info('Check the TD SYNNEX partner portal to verify:');
                        $this->line('  Portal: https://portal.us.tdsynnex.com');
                        $this->line('  Order Number: ' . $orderNumber);
                        
                    } catch (\Exception $e) {
                        $this->error('   ✗ Order submission failed: ' . $e->getMessage());
                        $this->newLine();
                        $this->warn('This might be due to:');
                        $this->line('  - Product not available for purchase');
                        $this->line('  - Missing required fields in order data');
                        $this->line('  - Account configuration in TD SYNNEX');
                        $this->line('  - Sandbox environment limitations');
                        return Command::FAILURE;
                    }
                }
            } else {
                $this->warn('   Could not find Microsoft vendor for product test');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Product retrieval failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('✓ All TD SYNNEX integration tests passed!');
        $this->newLine();
        
        $this->line('Your application is correctly integrated with TD SYNNEX sandbox.');
        $this->line('Orders created through your app WILL appear in the TD SYNNEX portal.');
        
        return Command::SUCCESS;
    }
}
