<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteOrderInvoiceController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StripeController;
use App\Services\TDSynnexService;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Debug endpoint to test TD SYNNEX service
Route::get('/debug/test-service', function () {
    try {
        $service = new TDSynnexService();
        $service->authenticate();
        return response()->json([
            'success' => true,
            'message' => 'Authentication successful'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::prefix('v1')->group(function () {
    Route::post('/stripe/webhook', [StripeController::class, 'webhook']);
    // Auth endpoints
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'active.user']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/auth/activate', [AuthController::class, 'activateAccount']);
    Route::post('/auth/resend-activation', [AuthController::class, 'resendActivation']);
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware(['auth:sanctum', 'active.user']);
    Route::put('/auth/update-profile', [AuthController::class, 'updateProfile'])->middleware(['auth:sanctum', 'active.user']);
    Route::put('/auth/change-password', [AuthController::class, 'changePassword'])->middleware(['auth:sanctum', 'active.user']);

    // Products endpoints
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{productId}', [ProductController::class, 'show']);
    Route::get('/products/{productId}/related', [ProductController::class, 'related']);
    Route::get('/products/sku/{skuNo}', [ProductController::class, 'getBySku']);
    
    // Vendors endpoints
    Route::get('/vendors', [ProductController::class, 'vendors']);

    // Quotes endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/quotes', [QuoteOrderInvoiceController::class, 'getQuotes']);
        Route::post('/quotes', [QuoteOrderInvoiceController::class, 'createQuote']);
        Route::get('/quotes/{quoteId}', [QuoteOrderInvoiceController::class, 'getQuote']);
        Route::get('/quotes/{quoteId}/pdf', [QuoteOrderInvoiceController::class, 'downloadQuotePdf']);
        Route::post('/quotes/{quoteId}/cancel', [QuoteOrderInvoiceController::class, 'cancelQuote']);
        Route::post('/quotes/{quoteId}/convert-to-order', [QuoteOrderInvoiceController::class, 'convertQuoteToOrder']);
    });

    // Orders endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/orders', [QuoteOrderInvoiceController::class, 'getOrders']);
        Route::get('/orders/{orderNumber}', [QuoteOrderInvoiceController::class, 'getOrder']);
        Route::post('/orders/{orderNumber}/cancel', [QuoteOrderInvoiceController::class, 'cancelOrder']);
    });

    // Invoices endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/invoices', [QuoteOrderInvoiceController::class, 'getInvoices']);
        Route::get('/invoices/{invoiceNumber}', [QuoteOrderInvoiceController::class, 'getInvoice']);
        Route::get('/invoices/{invoiceNumber}/pdf', [QuoteOrderInvoiceController::class, 'downloadInvoicePdf']);
        Route::post('/invoices/combine', [QuoteOrderInvoiceController::class, 'combineInvoices']);
        Route::post('/invoices/{invoiceNumber}/pay', [StripeController::class, 'createInvoiceCheckoutSession']);
        Route::post('/invoices/pay-multiple', [StripeController::class, 'createBulkInvoiceCheckoutSession']);
    });

    // Activities endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/activities', [ActivityController::class, 'getActivities']);
        Route::post('/activities/log', [ActivityController::class, 'logActivity']);
    });

    // Messages endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/messages', [MessageController::class, 'getMessages']);
        Route::get('/messages/unread-count', [MessageController::class, 'getUnreadCount']);
        Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead']);
        Route::post('/messages/mark-all-read', [MessageController::class, 'markAllAsRead']);
        Route::delete('/messages/{id}', [MessageController::class, 'deleteMessage']);
    });

    // Admin endpoints (protected & requires admin role)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        // Dashboard stats
        Route::get('/admin/dashboard/stats', [AdminController::class, 'getDashboardStats']);
        
        // Customer management
        Route::get('/admin/customers', [AdminController::class, 'getCustomers']);
        Route::post('/admin/customers/{companyId}/approve', [AdminController::class, 'approveCustomer']);
        Route::post('/admin/customers/bulk-approve', [AdminController::class, 'bulkApproveCustomers']);
        Route::post('/admin/customers/bulk-delete', [AdminController::class, 'bulkDeleteUsers']);
        Route::post('/admin/customers/bulk-suspend', [AdminController::class, 'bulkSuspendUsers']);

        // Quote management
        Route::get('/admin/quotes/stats', [AdminController::class, 'getQuoteStats']);
        Route::get('/admin/quotes/pending', [AdminController::class, 'getPendingQuotes']);
        Route::get('/admin/quotes/approved', [AdminController::class, 'getApprovedQuotes']);
        Route::get('/admin/quotes/rejected', [AdminController::class, 'getRejectedQuotes']);
        Route::get('/admin/quotes', [AdminController::class, 'getQuotes']);
        Route::post('/admin/quotes/{quoteId}/approve', [AdminController::class, 'approveQuote']);
        Route::post('/admin/quotes/{quoteId}/reject', [AdminController::class, 'rejectQuote']);
        Route::post('/admin/quotes/bulk-delete', [AdminController::class, 'bulkDeleteQuotes']);

        // Order management
        Route::get('/admin/orders', [AdminController::class, 'getAllOrders']);
        Route::post('/admin/orders/bulk-delete', [AdminController::class, 'bulkDeleteOrders']);
        Route::get('/admin/orders/{orderNumber}/status', [AdminController::class, 'getOrderStatus']);

        // Reports
        Route::get('/admin/reports/revenue', [AdminController::class, 'getRevenueReport']);
        Route::get('/admin/reports/top-customers', [AdminController::class, 'getTopCustomers']);

        // Settings
        Route::get('/admin/settings', [AdminController::class, 'getSettings']);
        Route::post('/admin/settings/profile', [AdminController::class, 'updateProfile']);
        Route::post('/admin/settings/api', [AdminController::class, 'updateApiConfig']);
        Route::get('/admin/settings/api/test', [AdminController::class, 'testApiConnection']);
        Route::post('/admin/settings/email', [AdminController::class, 'updateEmailSettings']);
        Route::post('/admin/settings/system', [AdminController::class, 'updateSystemSettings']);

        // Admin User Management
        Route::get('/admin/users', [AdminController::class, 'getAdminUsers']);
        Route::post('/admin/users', [AdminController::class, 'createAdminUser']);
        Route::post('/admin/users/{userId}/suspend', [AdminController::class, 'suspendAdminUser']);
        Route::post('/admin/users/{userId}/activate', [AdminController::class, 'activateAdminUser']);
        Route::delete('/admin/users/{userId}', [AdminController::class, 'deleteAdminUser']);

        // Invoice Management
        Route::get('/admin/invoices/stats', [AdminController::class, 'getInvoiceStats']);
        Route::get('/admin/invoices', [AdminController::class, 'getInvoices']);
        Route::get('/admin/invoices/{status}', [AdminController::class, 'getInvoicesByStatus']);
        Route::post('/admin/invoices/{invoiceId}/mark-paid', [AdminController::class, 'markInvoiceAsPaid']);
        Route::post('/admin/invoices/{invoiceId}/send-reminder', [AdminController::class, 'sendInvoiceReminder']);
        Route::get('/admin/invoices/{invoiceId}/pdf', [AdminController::class, 'downloadInvoicePdf']);
    });
});
