<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Company;
use App\Services\NotificationService;
use App\Services\TDSynnexService;
use App\Models\Message;
use App\Exceptions\TDSynnexApiException;
use App\Jobs\GenerateInvoiceJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Recursively search for order number in API response
     * Looks for common field names that might contain order ID
     */
    private function findOrderNumber($data): ?string
    {
        if (is_array($data)) {
            // Check common field names first
            $commonFields = [
                'orderNumber', 'orderId', 'order_number', 'order_id',
                'poNumber', 'po_number', 'purchaseOrderNumber',
                'salesOrderNumber', 'soNumber', 'so_number',
                'id', 'orderNo', 'order_no',
                'confirmationNumber', 'confirmation_number',
                'referenceNumber', 'reference_number',
                'transactionNumber', 'transaction_number',
                'po', 'so', 'order',
            ];
            
            foreach ($commonFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    return (string)$data[$field];
                }
            }
            
            // Recursively search in nested arrays/objects
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $found = $this->findOrderNumber($value);
                    if ($found) {
                        return $found;
                    }
                }
            }
        } else if (is_object($data)) {
            return $this->findOrderNumber((array)$data);
        }
        
        return null;
    }

    /**
     * Get admin dashboard stats
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $stats = [
                'total_quotes' => Quote::count(),
                'pending_quotes' => Quote::where('status', 'pending_review')->count(),
                'total_orders' => Order::count(),
                'processing_orders' => Order::whereIn('status', ['pending', 'processing', 'confirmed'])->count(),
                'completed_orders' => Order::where('status', 'delivered')->count(),
                'monthly_revenue' => Order::where('status', 'delivered')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_amount'),
                'total_customers' => Company::count(),
                'active_customers' => Company::where('status', 'approved')->count(),
                'pending_invoices' => Invoice::where('status', 'pending')->count(),
                'overdue_invoices' => Invoice::where('status', 'pending')
                    ->where('due_at', '<', now())
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
            ], 500);
        }
    }

    /**
     * Get all customers (paginated)
     */
    public function getCustomers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $status = $request->get('status');
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $query = Company::query()
                // Customer list should include companies that have at least one customer user.
                // This keeps pure admin-only companies out while avoiding accidental full-table filtering.
                ->whereHas('users', function ($userQuery) {
                    $userQuery->whereNotIn('role', ['admin', 'super_admin']);
                });

            if ($status) {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('domain', 'like', "%{$search}%");
                });
            }

            if ($sortBy === 'oldest') {
                $query->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'name') {
                $query->orderBy('name', 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $companies = $query->with(['users' => function ($userQuery) {
                    $userQuery->whereNotIn('role', ['admin', 'super_admin']);
                }])
                ->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $companies->items(),
                'pagination' => [
                    'total' => $companies->total(),
                    'per_page' => $companies->perPage(),
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers',
            ], 500);
        }
    }

    /**
     * Get pending quotes for review
     */
    public function getPendingQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::where('status', 'pending_review')
                ->with('user', 'user.company', 'order');

            if ($search) {
                $quotesQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('quote_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user.company', function ($companyQuery) use ($search) {
                            $companyQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('domain', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $quotesQuery->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'highest') {
                $quotesQuery->orderBy('total_amount', 'desc');
            } elseif ($sortBy === 'lowest') {
                $quotesQuery->orderBy('total_amount', 'asc');
            } else {
                $quotesQuery->orderBy('created_at', 'desc');
            }

            $quotes = $quotesQuery
                ->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $quotes->items(),
                'pagination' => [
                    'total' => $quotes->total(),
                    'per_page' => $quotes->perPage(),
                    'current_page' => $quotes->currentPage(),
                    'last_page' => $quotes->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch pending quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending quotes',
            ], 500);
        }
    }

    /**
     * Approve quote
     */
    public function approveQuote(Request $request, string $quoteId): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $quote = Quote::with('user', 'user.company')->find($quoteId);
            if (!$quote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote not found',
                ], 404);
            }

            if (!$quote->canApprove()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote cannot be approved in its current state',
                ], 400);
            }

            // Prevent duplicate orders for the same quote
            $existingOrder = Order::where('quote_id', $quote->quote_id)->first();
            if ($existingOrder) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quote already approved and submitted',
                    'data' => [
                        'quote' => $quote,
                        'order' => $existingOrder,
                    ],
                ]);
            }

            // Ensure items are properly decoded
            $items = $quote->items;
            if (is_string($items)) {
                $items = json_decode($items, true) ?? [];
            }
            if (!is_array($items)) {
                $items = [];
            }
            
            // Build line items with proper structure
            $lineItems = array_map(function($item, $index) {
                return [
                    'lineNumber' => (string)($index + 1),
                    'partNumber' => (string)($item['partNumber'] ?? $item['id'] ?? $item['sku'] ?? 'PART-' . ($index + 1)),
                    'partDescription' => (string)($item['name'] ?? $item['description'] ?? 'Product'),
                    'quantity' => (int)($item['quantity'] ?? 1),
                    'unitPrice' => (string)number_format((float)($item['price'] ?? $item['unitPrice'] ?? 0), 2, '.', ''),
                    'extendedPrice' => (string)number_format((float)(($item['price'] ?? $item['unitPrice'] ?? 0) * ($item['quantity'] ?? 1)), 2, '.', ''),
                ];
            }, $items, array_keys($items));

            $orderData = [
                'poNumber' => $quote->quote_id,
                'poDate' => now()->format('Y-m-d'),
                'shipDate' => now()->addDays(7)->format('Y-m-d'),
                'vendor' => [
                    'vendorId' => '12345',
                    'vendorName' => 'Armely Store',
                ],
                'billTo' => [
                    'companyName' => $quote->user->company->name ?? 'Company',
                    'address1' => $quote->user->company->address ?? '123 Main St',
                    'city' => $quote->user->company->city ?? 'City',
                    'state' => $quote->user->company->state ?? 'State',
                    'postalCode' => $quote->user->company->postal_code ?? '12345',
                    'country' => $quote->user->company->country ?? 'US',
                    'contactEmail' => $quote->user->email ?? '',
                    'contactPhone' => $quote->user->phone ?? '',
                ],
                'shipTo' => [
                    'companyName' => $quote->user->company->name ?? 'Company',
                    'address1' => $quote->user->company->address ?? '123 Main St',
                    'city' => $quote->user->company->city ?? 'City',
                    'state' => $quote->user->company->state ?? 'State',
                    'postalCode' => $quote->user->company->postal_code ?? '12345',
                    'country' => $quote->user->company->country ?? 'US',
                ],
                'poLine' => $lineItems,
                'poTotal' => (string)number_format((float)($quote->total_amount ?? 0), 2, '.', ''),
                'poTax' => (string)number_format((float)($quote->tax_amount ?? 0), 2, '.', ''),
                'poFreight' => '0.00',
            ];

            \Log::debug('Order data being sent to TD SYNNEX', [
                'lineItems_count' => count($lineItems),
                'lineItems' => $lineItems,
                'quote_items_original' => $items,
                'order_structure' => $orderData,
            ]);

            $tdsynnexService = app(TDSynnexService::class);
            $tdResponse = $tdsynnexService->placeOrder($orderData);
            
            // Log full response for debugging
            \Log::debug('Order submission response details', [
                'response' => $tdResponse,
                'response_keys' => array_keys((array)$tdResponse),
                'response_json' => json_encode($tdResponse),
            ]);
            
            // Recursively search for order number in response
            $orderNumber = $this->findOrderNumber($tdResponse);

            // Fallback: If TD SYNNEX doesn't return order number, generate local one
            if (!$orderNumber) {
                \Log::warning('TD SYNNEX did not return order number, generating local one', [
                    'response' => $tdResponse,
                    'quote_id' => $quote->quote_id,
                ]);
                // Generate order ID: ORD-2026-XXXXXX format
                $orderNumber = 'ORD-' . now()->format('Y') . '-' . strtoupper(substr(md5($quote->quote_id . time()), 0, 6));
            }

            $order = Order::create([
                'user_id' => $quote->user_id,
                'order_number' => $orderNumber,
                'quote_id' => $quote->quote_id,
                'status' => 'pending',
                'total_amount' => $quote->total_amount,
                'tax_amount' => $quote->tax_amount,
                'discount_amount' => $quote->discount_amount,
                'items' => $quote->items,
                'raw_data' => $tdResponse,
                'ordered_at' => now(),
            ]);

            $quote->update([
                'status' => 'approved',
            ]);

            // Generate invoice and send to customer
            GenerateInvoiceJob::dispatch($order);

            // Send approval notification
            $this->notificationService->sendQuoteApprovedNotification($quote);

            Log::info("Quote {$quote->quote_id} approved by admin {$user->id} and submitted as order {$orderNumber}");

            return response()->json([
                'success' => true,
                'message' => 'Quote approved and order submitted successfully',
                'data' => [
                    'quote' => $quote,
                    'order' => $order,
                ],
            ]);
        } catch (TDSynnexApiException $e) {
            Log::error("TD SYNNEX order submission failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order submission failed. Please try again later.',
                'error' => $e->getMessage(),
            ], 502);
        } catch (\Exception $e) {
            Log::error("Failed to approve quote: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve quote',
            ], 500);
        }
    }

    /**
     * Reject quote
     */
    public function rejectQuote(Request $request, string $quoteId): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            $quote = Quote::find($quoteId);
            if (!$quote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote not found',
                ], 404);
            }

            if (!$quote->canReject()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote cannot be rejected in its current state',
                ], 400);
            }

            $quote->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $validated['reason'],
                'approved_by' => $user->id,
            ]);

            // Send rejection notification
            $this->notificationService->sendQuoteRejectedNotification($quote, $validated['reason']);

            Log::info("Quote {$quote->quote_id} rejected by admin {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Quote rejected successfully',
                'data' => $quote,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to reject quote: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject quote',
            ], 500);
        }
    }

    /**
     * Get all quotes
     */
    public function getQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::query()->with('user', 'user.company', 'order');

            if ($search) {
                $quotesQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('quote_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $quotesQuery->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'highest') {
                $quotesQuery->orderBy('total_amount', 'desc');
            } elseif ($sortBy === 'lowest') {
                $quotesQuery->orderBy('total_amount', 'asc');
            } else {
                $quotesQuery->orderBy('created_at', 'desc');
            }

            $quotes = $quotesQuery->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $quotes->items(),
                'pagination' => [
                    'total' => $quotes->total(),
                    'per_page' => $quotes->perPage(),
                    'current_page' => $quotes->currentPage(),
                    'last_page' => $quotes->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quotes',
            ], 500);
        }
    }

    /**
     * Get approved quotes
     */
    public function getApprovedQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::where('status', 'approved')
                ->with('user', 'user.company', 'order');

            if ($search) {
                $quotesQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('quote_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $quotesQuery->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'highest') {
                $quotesQuery->orderBy('total_amount', 'desc');
            } elseif ($sortBy === 'lowest') {
                $quotesQuery->orderBy('total_amount', 'asc');
            } else {
                $quotesQuery->orderBy('created_at', 'desc');
            }

            $quotes = $quotesQuery->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $quotes->items(),
                'pagination' => [
                    'total' => $quotes->total(),
                    'per_page' => $quotes->perPage(),
                    'current_page' => $quotes->currentPage(),
                    'last_page' => $quotes->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch approved quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch approved quotes',
            ], 500);
        }
    }

    /**
     * Get rejected quotes
     */
    public function getRejectedQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $search = $request->get('search');
            $sortBy = $request->get('sortBy', 'newest');

            $quotesQuery = Quote::where('status', 'rejected')
                ->with('user', 'user.company', 'order');

            if ($search) {
                $quotesQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('quote_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($sortBy === 'oldest') {
                $quotesQuery->orderBy('created_at', 'asc');
            } elseif ($sortBy === 'highest') {
                $quotesQuery->orderBy('total_amount', 'desc');
            } elseif ($sortBy === 'lowest') {
                $quotesQuery->orderBy('total_amount', 'asc');
            } else {
                $quotesQuery->orderBy('created_at', 'desc');
            }

            $quotes = $quotesQuery->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $quotes->items(),
                'pagination' => [
                    'total' => $quotes->total(),
                    'per_page' => $quotes->perPage(),
                    'current_page' => $quotes->currentPage(),
                    'last_page' => $quotes->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch rejected quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rejected quotes',
            ], 500);
        }
    }

    /**
     * Get quote statistics
     */
    public function getQuoteStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $stats = [
                'total' => Quote::count(),
                'pending' => Quote::where('status', 'pending_review')->count(),
                'approved' => Quote::where('status', 'approved')->count(),
                'rejected' => Quote::where('status', 'rejected')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch quote stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quote stats',
            ], 500);
        }
    }

    /**
     * Get order status from TD SYNNEX
     */
    public function getOrderStatus(Request $request, string $orderNumber): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Get order to verify it exists
            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            $tdsynnexService = app(TDSynnexService::class);
            $statusResponse = $tdsynnexService->checkPoStatus($orderNumber);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_number' => $orderNumber,
                    'td_synnex_status' => $statusResponse,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch order status: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all orders (admin view)
     */
    public function getAllOrders(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 20);
            $status = $request->get('status');
            $search = $request->get('search');
            $dateRange = $request->get('dateRange', 'all');
            $companyId = $request->get('company_id');

            $query = Order::query()->with('user', 'user.company');

            if ($companyId) {
                $query->whereHas('user', function ($userQuery) use ($companyId) {
                    $userQuery->where('company_id', $companyId);
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            if ($dateRange === 'today') {
                $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
            } elseif ($dateRange === 'week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateRange === 'month') {
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $orders->items(),
                'pagination' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
            ], 500);
        }
    }

    /**
     * Get revenue report
     */
    public function getRevenueReport(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $period = $request->get('period', 'month'); // day, week, month, year
            $limit = $request->get('limit', 12);

            $query = Order::where('status', 'delivered');

            if ($period === 'day') {
                $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
            } elseif ($period === 'week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($period === 'month') {
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            } elseif ($period === 'year') {
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
            }

            $data = [
                'total_revenue' => $query->sum('total_amount'),
                'total_orders' => $query->count(),
                'average_order_value' => $query->avg('total_amount'),
                'total_tax' => $query->sum('tax_amount'),
                'total_shipping' => $query->sum('shipping_amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'period' => $period,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch revenue report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch revenue report',
            ], 500);
        }
    }

    /**
     * Get top customers by revenue
     */
    public function getTopCustomers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $limit = $request->get('limit', 10);

            $topCustomers = User::selectRaw('users.id, users.name, users.email, company_id, COUNT(orders.id) as order_count, SUM(orders.total_amount) as total_spent')
                ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
                ->groupBy('users.id', 'users.name', 'users.email', 'users.company_id')
                ->orderByDesc('total_spent')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topCustomers,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch top customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch top customers',
            ], 500);
        }
    }

    /**
     * Approve a customer company
     */
    public function approveCustomer(Request $request, string $companyId): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $company = Company::find($companyId);
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found',
                ], 404);
            }

            $company->status = 'approved';
            $company->save();

            // Activate all users in the company
            User::where('company_id', $companyId)->update(['status' => 'active']);

            return response()->json([
                'success' => true,
                'message' => 'Customer approved successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to approve customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve customer',
            ], 500);
        }
    }

    /**
     * Bulk approve customer companies
     */
    public function bulkApproveCustomers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'company_ids' => 'required|array|min:1',
                'company_ids.*' => 'required|integer|exists:companies,id'
            ]);

            $companyIds = $request->company_ids;

            // Protect companies that contain admin accounts from customer bulk actions.
            $protectedCompanyIds = Company::whereIn('id', $companyIds)
                ->whereHas('users', function ($q) {
                    $q->whereIn('role', ['admin', 'super_admin']);
                })
                ->pluck('id')
                ->all();

            $targetCompanyIds = array_values(array_diff($companyIds, $protectedCompanyIds));

            if (empty($targetCompanyIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected companies are protected and cannot be modified.',
                ], 400);
            }
            
            // Update company status
            $updatedCount = Company::whereIn('id', $targetCompanyIds)->update(['status' => 'approved']);

            // Activate all users in these companies
            User::whereIn('company_id', $targetCompanyIds)
                ->whereNotIn('role', ['admin', 'super_admin'])
                ->update(['status' => 'active']);

            return response()->json([
                'success' => true,
                'message' => "$updatedCount compan" . ($updatedCount > 1 ? 'ies' : 'y') . " approved successfully",
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to bulk approve customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve customers',
            ], 500);
        }
    }

    /**
     * Bulk delete users or companies
     */
    public function bulkDeleteUsers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Accept either user_ids or company_ids
            if ($request->has('company_ids')) {
                $request->validate([
                    'company_ids' => 'required|array|min:1',
                    'company_ids.*' => 'required|integer|exists:companies,id'
                ]);

                $companyIds = $request->company_ids;

                // Protect companies that contain admin accounts from customer bulk actions.
                $protectedCompanyIds = Company::whereIn('id', $companyIds)
                    ->whereHas('users', function ($q) {
                        $q->whereIn('role', ['admin', 'super_admin']);
                    })
                    ->pluck('id')
                    ->all();

                $targetCompanyIds = array_values(array_diff($companyIds, $protectedCompanyIds));

                if (empty($targetCompanyIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected companies are protected and cannot be deleted.',
                    ], 400);
                }
                
                // Delete all users in these companies
                User::whereIn('company_id', $targetCompanyIds)
                    ->whereNotIn('role', ['admin', 'super_admin'])
                    ->delete();
                
                // Delete companies
                $deletedCount = Company::whereIn('id', $targetCompanyIds)->delete();

                return response()->json([
                    'success' => true,
                    'message' => "$deletedCount compan" . ($deletedCount > 1 ? 'ies' : 'y') . " deleted successfully",
                    'deleted_count' => $deletedCount,
                ]);
            } else {
                $request->validate([
                    'user_ids' => 'required|array|min:1',
                    'user_ids.*' => 'required|integer|exists:users,id'
                ]);

                $userIds = $request->user_ids;
                
                // Prevent admin from deleting themselves
                if (in_array($user->id, $userIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot delete your own account',
                    ], 400);
                }

                // Delete users
                $deletedCount = User::whereIn('id', $userIds)
                    ->where('role', '!=', 'admin') // Prevent deleting other admins
                    ->delete();

                return response()->json([
                    'success' => true,
                    'message' => "$deletedCount user(s) deleted successfully",
                    'deleted_count' => $deletedCount,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete users',
            ], 500);
        }
    }

    /**
     * Bulk suspend or activate user accounts or companies
     */
    public function bulkSuspendUsers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'action' => 'required|in:suspend,activate'
            ]);

            $action = $request->action;
            $status = $action === 'suspend' ? 'suspended' : 'active';

            // Accept either user_ids or company_ids
            if ($request->has('company_ids')) {
                $request->validate([
                    'company_ids' => 'required|array|min:1',
                    'company_ids.*' => 'required|integer|exists:companies,id'
                ]);

                $companyIds = $request->company_ids;

                // Keep admin accounts protected: do not inactivate companies that host admin users.
                $protectedCompanyIds = Company::whereIn('id', $companyIds)
                    ->whereHas('users', function ($q) {
                        $q->whereIn('role', ['admin', 'super_admin']);
                    })
                    ->pluck('id')
                    ->all();

                $targetCompanyIds = array_values(array_diff($companyIds, $protectedCompanyIds));

                // Update company status only for non-protected companies.
                $companyStatus = $action === 'suspend' ? 'inactive' : 'approved';
                $updatedCount = 0;
                if (!empty($targetCompanyIds)) {
                    $updatedCount = Company::whereIn('id', $targetCompanyIds)->update(['status' => $companyStatus]);
                }

                // Always update non-admin customer users for selected companies.
                $updatedUsers = User::whereIn('company_id', $companyIds)
                    ->whereNotIn('role', ['admin', 'super_admin'])
                    ->update(['status' => $status]);

                if ($updatedUsers === 0 && $updatedCount === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No eligible customer accounts were found to update.',
                    ], 400);
                }

                $actionText = $action === 'suspend' ? 'suspended' : 'activated';
                $protectedCount = count($protectedCompanyIds);
                $message = "$updatedUsers customer user(s) $actionText successfully";
                if ($protectedCount > 0) {
                    $message .= ". $protectedCount compan" . ($protectedCount > 1 ? 'ies were' : 'y was') . ' kept active to protect admin access.';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated_count' => $updatedUsers,
                ]);
            } else {
                $request->validate([
                    'user_ids' => 'required|array|min:1',
                    'user_ids.*' => 'required|integer|exists:users,id'
                ]);

                $userIds = $request->user_ids;
                
                // Prevent admin from suspending themselves
                if (in_array($user->id, $userIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot suspend your own account',
                    ], 400);
                }

                // Update user status
                $updatedCount = User::whereIn('id', $userIds)
                    ->whereNotIn('role', ['admin', 'super_admin']) // Prevent affecting other admins
                    ->update(['status' => $status]);

                $actionText = $action === 'suspend' ? 'suspended' : 'activated';
                return response()->json([
                    'success' => true,
                    'message' => "$updatedCount user(s) $actionText successfully",
                    'updated_count' => $updatedCount,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status',
            ], 500);
        }
    }

    /**
     * Bulk delete quotes
     */
    public function bulkDeleteQuotes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'quote_ids' => 'required|array|min:1',
                'quote_ids.*' => 'required|integer|exists:quotes,id'
            ]);

            $quoteIds = $request->quote_ids;
            
            // Delete quotes
            $deletedCount = Quote::whereIn('id', $quoteIds)->delete();

            return response()->json([
                'success' => true,
                'message' => "$deletedCount quote(s) deleted successfully",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quotes',
            ], 500);
        }
    }

    /**
     * Bulk delete orders
     */
    public function bulkDeleteOrders(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'order_ids' => 'required|array|min:1',
                'order_ids.*' => 'required|integer|exists:orders,id'
            ]);

            $orderIds = $request->order_ids;
            
            // Delete orders
            $deletedCount = Order::whereIn('id', $orderIds)->delete();

            return response()->json([
                'success' => true,
                'message' => "$deletedCount order(s) deleted successfully",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete orders',
            ], 500);
        }
    }

    /**
     * Get current admin user
     */
    public function getCurrentUser(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get current user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get current user',
            ], 500);
        }
    }

    /**
     * Get admin settings
     */
    public function getSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'profile' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'api_config' => [
                        'client_id' => config('tdsynnex.client_id', ''),
                        'environment' => config('tdsynnex.environment', 'sandbox'),
                    ],
                    'email_settings' => [
                        'new_orders' => true,
                        'new_quotes' => true,
                        'low_stock' => false,
                        'smtp_host' => config('mail.mailers.smtp.host', ''),
                        'smtp_port' => config('mail.mailers.smtp.port', '587'),
                        'smtp_username' => config('mail.mailers.smtp.username', ''),
                    ],
                    'system_settings' => [
                        'company_name' => 'Armely Store',
                        'support_email' => config('mail.from.address', 'support@armely.com'),
                        'currency' => 'USD',
                        'timezone' => config('app.timezone', 'America/New_York'),
                        'maintenance_mode' => app()->isDownForMaintenance(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get settings',
            ], 500);
        }
    }

    /**
     * Update profile settings
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
                'current_password' => 'sometimes|required_with:new_password',
                'new_password' => 'sometimes|min:8',
            ]);

            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('new_password') && $request->has('current_password')) {
                if (!\Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect',
                    ], 400);
                }
                $user->password = \Hash::make($request->new_password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
            ], 500);
        }
    }

    /**
     * Update API configuration (placeholder)
     */
    public function updateApiConfig(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // In a real implementation, you would save these to a settings table or .env file
            return response()->json([
                'success' => true,
                'message' => 'API configuration updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update API config: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update API configuration',
            ], 500);
        }
    }

    /**
     * Test TD SYNNEX API connection
     */
    public function testApiConnection(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Use TDSynnexService to test connection
            $service = new \App\Services\TDSynnexService();
            $service->authenticate();
            
            return response()->json([
                'success' => true,
                'message' => 'API connection successful! TD SYNNEX service is responding correctly.',
            ]);
        } catch (\Exception $e) {
            Log::error('API connection test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update email settings (placeholder)
     */
    public function updateEmailSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // In a real implementation, save to database
            return response()->json([
                'success' => true,
                'message' => 'Email settings updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update email settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update email settings',
            ], 500);
        }
    }

    /**
     * Update system settings (placeholder)
     */
    public function updateSystemSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Handle maintenance mode
            if ($request->has('maintenance_mode')) {
                $maintenanceMode = $request->boolean('maintenance_mode');
                
                if ($maintenanceMode) {
                    // Enable maintenance mode
                    Artisan::call('down', [
                        '--render' => 'errors::503',
                        '--secret' => config('app.key')
                    ]);
                } else {
                    // Disable maintenance mode
                    Artisan::call('up');
                }
            }

            // In a real implementation, save other settings to database
            // For now, just save to config (you may want to use Settings model)
            
            return response()->json([
                'success' => true,
                'message' => 'System settings updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update system settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update system settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all admin users
     */
    public function getAdminUsers(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify admin role
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $admins = User::whereIn('role', ['admin', 'super_admin'])
                ->orderBy('created_at', 'desc')
                ->get(['id', 'name', 'email', 'role', 'status', 'created_at']);

            return response()->json([
                'success' => true,
                'data' => $admins,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get admin users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get admin users',
            ], 500);
        }
    }

    /**
     * Create new admin user
     */
    public function createAdminUser(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Verify super admin role
            if ($user->role !== 'super_admin' && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'role' => 'required|in:admin,super_admin',
            ]);

            $newAdmin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
                'role' => $request->role,
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin user created successfully',
                'data' => $newAdmin,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create admin user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin user',
            ], 500);
        }
    }

    /**
     * Suspend admin user
     */
    public function suspendAdminUser(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $admin = User::findOrFail($userId);
            $admin->status = 'suspended';
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Admin user suspended',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to suspend admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend admin user',
            ], 500);
        }
    }

    /**
     * Activate admin user
     */
    public function activateAdminUser(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $admin = User::findOrFail($userId);
            $admin->status = 'active';
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Admin user activated',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to activate admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate admin user',
            ], 500);
        }
    }

    /**
     * Delete admin user
     */
    public function deleteAdminUser(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Prevent self-deletion
            if ($currentUser->id == $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account',
                ], 400);
            }

            $admin = User::findOrFail($userId);
            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin user deleted',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admin user',
            ], 500);
        }
    }

    /**
     * Get invoice statistics for dashboard
     */
    public function getInvoiceStats(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $stats = [
                'total' => Invoice::count(),
                'pending' => Invoice::where('status', 'pending')->count(),
                'paid' => Invoice::where('status', 'paid')->count(),
                'overdue' => Invoice::where('status', 'overdue')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get invoice stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice statistics',
            ], 500);
        }
    }

    /**
     * Get paginated list of invoices with optional filtering
     */
    public function getInvoices(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $perPage = (int) $request->get('per_page', 15);
            $page = (int) $request->get('page', 1);
            $search = $request->get('search', '');
            $status = $request->get('status', null);
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $query = Invoice::with(['user', 'user.company']);

            // Apply status filter
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            // Apply search filter
            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Apply sorting
            if (in_array($sortBy, ['invoice_number', 'total_amount', 'due_date', 'created_at'])) {
                $query->orderBy($sortBy, strtoupper($sortOrder) === 'DESC' ? 'desc' : 'asc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $total = $query->count();
            $invoices = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $invoices->lastPage(),
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
            ], 500);
        }
    }

    /**
     * Mark invoice as paid and record payment details
     */
    public function markInvoiceAsPaid(Request $request, $invoiceId): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $validated = $request->validate([
                'payment_date' => 'nullable|date',
                'payment_notes' => 'nullable|string|max:500',
            ]);

            $invoice = Invoice::findOrFail($invoiceId);

            // Update invoice status and payment details
            $invoice->update([
                'status' => 'paid',
                'paid_at' => $validated['payment_date'] ?? now(),
                'notes' => $validated['payment_notes'] ?? $invoice->notes,
                'paid_amount' => $invoice->total_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as paid',
                'data' => $invoice,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to mark invoice as paid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice status',
            ], 500);
        }
    }

    /**
     * Send payment reminder for a specific invoice (admin only).
     */
    public function sendInvoiceReminder(Request $request, $invoiceId): JsonResponse
    {
        try {
            $currentUser = $request->user();

            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $invoice = Invoice::findOrFail($invoiceId);
            $recipient = User::find($invoice->user_id);

            if (!$recipient || !$recipient->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid recipient email found for this invoice user.',
                ], 422);
            }

            $totalAmount = (float) ($invoice->total_amount ?? 0);
            $paidAmount = (float) ($invoice->paid_amount ?? 0);
            $balanceDue = max(0, $totalAmount - $paidAmount);

            if ($invoice->status === 'paid' || $balanceDue <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This invoice is already fully paid. No reminder needed.',
                ], 422);
            }

            $this->notificationService->sendInvoiceReminderNotification($invoice);

            return response()->json([
                'success' => true,
                'message' => "Payment reminder sent to {$recipient->email}",
                'data' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'email' => $recipient->email,
                    'balance_due' => $balanceDue,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to send invoice reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice reminder',
            ], 500);
        }
    }

    /**
     * Get invoice by status filter
     */
    public function getInvoicesByStatus(Request $request, $status): JsonResponse
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $perPage = (int) $request->get('per_page', 15);
            $page = (int) $request->get('page', 1);
            $search = $request->get('search', '');

            $query = Invoice::where('status', $status)->with(['user', 'user.company']);

            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $total = $query->count();
            $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $invoices->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get invoices by status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
            ], 500);
        }
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoicePdf(Request $request, $invoiceId)
    {
        try {
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $invoice = Invoice::with(['user', 'user.company', 'items'])->findOrFail($invoiceId);

            // Prepare data for PDF
            $companyName = $invoice->user->company ? $invoice->user->company->name : 'N/A';
            $paymentNotes = $invoice->notes ? '<p><strong>Payment Notes:</strong> ' . htmlspecialchars($invoice->notes) . '</p>' : '';

            // Generate PDF content - basic HTML structure
            $html = <<<EOT
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Invoice {$invoice->invoice_number}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { border-bottom: 2px solid #2f5597; padding-bottom: 10px; margin-bottom: 20px; }
                    .invoice-details { margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                    th { background-color: #2f5597; color: white; }
                    .total { font-weight: bold; font-size: 16px; }
                    .footer { margin-top: 30px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>INVOICE</h1>
                    <p>Invoice #: {$invoice->invoice_number}</p>
                </div>
                
                <div class="invoice-details">
                    <h3>Billed To:</h3>
                    <p><strong>{$invoice->user->name}</strong><br/>
                    {$invoice->user->email}<br/>
                    {$companyName}</p>
                    
                    <p><strong>Invoice Date:</strong> {$invoice->created_at->format('M d, Y')}<br/>
                    <strong>Due Date:</strong> {$invoice->due_at->format('M d, Y')}<br/>
                    <strong>Status:</strong> {$invoice->status}</p>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
EOT;

            if ($invoice->items) {
                foreach ($invoice->items as $item) {
                    $itemDescription = is_array($item) ? ($item['description'] ?? 'Item') : ($item->description ?? 'Item');
                    $itemQty = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                    $itemPrice = is_array($item) ? ($item['unit_price'] ?? 0) : ($item->unit_price ?? 0);
                    $itemTotal = is_array($item) ? ($item['total'] ?? 0) : ($item->total ?? 0);
                    
                    $html .= <<<EOT
                        <tr>
                            <td>{$itemDescription}</td>
                            <td>{$itemQty}</td>
                            <td>\${$itemPrice}</td>
                            <td>\${$itemTotal}</td>
                        </tr>
EOT;
                }
            }

            $html .= <<<EOT
                    </tbody>
                </table>
                
                <div style="text-align: right; margin-top: 20px;">
                    <p class="total">Total Amount: \${$invoice->total_amount}</p>
                    {$paymentNotes}
                </div>
                
                <div class="footer">
                    <p>Thank you for your business!</p>
                </div>
            </body>
            </html>
EOT;

            // For now, return JSON with PDF content that can be downloaded client-side
            // In production, you'd use a library like mPDF or Dompdf
            return response()->json([
                'success' => true,
                'message' => 'Invoice PDF generated',
                'data' => [
                    'invoice_number' => $invoice->invoice_number,
                    'html' => $html,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice PDF',
            ], 500);
        }
    }

    /**
     * Global admin search across orders, invoices, quotes, and customers.
     */
    public function globalSearch(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $currentUser = $request->user();
        if ($currentUser->role !== 'super_admin' && $currentUser->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $results = [];
        $like = '%' . $q . '%';

        \App\Models\User::where('role', 'customer')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)->orWhere('email', 'like', $like);
            })
            ->limit(4)->get()
            ->each(function ($u) use (&$results) {
                $results[] = ['type' => 'customer', 'icon' => 'fa-user', 'label' => $u->name, 'sub' => $u->email, 'url' => '/admin/customers'];
            });

        \App\Models\Order::where('order_number', 'like', $like)
            ->limit(4)->get()
            ->each(function ($o) use (&$results) {
                $results[] = ['type' => 'order', 'icon' => 'fa-shopping-cart', 'label' => $o->order_number, 'sub' => 'Order · ' . ucfirst($o->status ?? 'unknown'), 'url' => '/admin/orders'];
            });

        \App\Models\Invoice::where('invoice_number', 'like', $like)
            ->orWhere('order_number', 'like', $like)
            ->limit(4)->get()
            ->each(function ($i) use (&$results) {
                $results[] = ['type' => 'invoice', 'icon' => 'fa-file-invoice-dollar', 'label' => $i->invoice_number, 'sub' => 'Invoice · $' . number_format($i->total_amount ?? 0, 2), 'url' => '/admin/invoices'];
            });

        \App\Models\Quote::where('reference_number', 'like', $like)
            ->limit(4)->get()
            ->each(function ($q2) use (&$results) {
                $results[] = ['type' => 'quote', 'icon' => 'fa-file-alt', 'label' => $q2->reference_number ?? 'Quote', 'sub' => 'Quote · ' . ucfirst($q2->status ?? 'unknown'), 'url' => '/admin/quotes/pending'];
            });

        return response()->json(['success' => true, 'results' => array_slice($results, 0, 10)]);
    }
}

