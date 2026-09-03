<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuickBooksPaymentController;
use App\Http\Controllers\QuoteOrderInvoiceController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StripeController;
use App\Services\TDSynnexService;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchProfileController;
use App\Http\Controllers\ProductSourcingRequestController;
use App\Http\Controllers\AdminImportedProductController;
use App\Http\Controllers\BehaviorEventController;
use App\Http\Controllers\PriceAlertSubscriptionController;
use App\Http\Controllers\EmailPreferenceController;

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
    Route::get('/auth/registration-config', [AuthController::class, 'registrationConfig']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum', 'active.user']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/auth/activate', [AuthController::class, 'activateAccount']);
    Route::post('/auth/resend-activation', [AuthController::class, 'resendActivation']);
    Route::get('/profile-pictures/{path}', [AuthController::class, 'profilePicture'])
        ->where('path', 'profile-pictures/.*')
        ->middleware('throttle:120,1');
    Route::get('/auth/me', [AuthController::class, 'me'])->middleware(['auth:sanctum', 'active.user']);
    Route::match(['put', 'post'], '/auth/update-profile', [AuthController::class, 'updateProfile'])->middleware(['auth:sanctum', 'active.user']);
    Route::delete('/auth/account', [AuthController::class, 'deleteAccount'])->middleware(['auth:sanctum', 'active.user']);
    Route::put('/auth/change-password', [AuthController::class, 'changePassword'])->middleware(['auth:sanctum', 'active.user']);

    // Image proxy — serves external product images through our server to avoid CORS/CSP issues
    Route::get('/img-proxy', [ProductController::class, 'imageProxy']);

    // Products endpoints
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/popular-categories', [ProductController::class, 'popularCategories']);
    Route::get('/products/{productId}', [ProductController::class, 'show']);
    Route::get('/products/{productId}/related', [ProductController::class, 'related']);
    Route::get('/products/sku/{skuNo}', [ProductController::class, 'getBySku']);
    
    // Vendors endpoints
    Route::get('/vendors', [ProductController::class, 'vendors']);
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::get('/menu-categories', [ProductController::class, 'menuCategories']);
    Route::get('/pricing/settings', [QuoteOrderInvoiceController::class, 'getPricingSettings']);
    Route::get('/search-profile', [SearchProfileController::class, 'show']);
    Route::post('/search-profile/terms', [SearchProfileController::class, 'track'])->middleware('throttle:60,1');
    Route::post('/behavior/product-view', [BehaviorEventController::class, 'trackProductView'])->middleware('throttle:120,1');
    Route::post('/behavior/cart-snapshot', [BehaviorEventController::class, 'syncCartSnapshot'])->middleware('throttle:120,1');
    Route::post('/behavior/cart-event', [BehaviorEventController::class, 'trackCartEvent'])->middleware('throttle:180,1');
    Route::post('/behavior/favorite-event', [BehaviorEventController::class, 'trackFavoriteEvent'])->middleware('throttle:120,1');
    Route::get('/behavior/price-alert-subscriptions', [PriceAlertSubscriptionController::class, 'index'])->middleware('throttle:60,1');
    Route::post('/behavior/price-alert-subscriptions', [PriceAlertSubscriptionController::class, 'upsert'])->middleware('throttle:60,1');
    Route::delete('/behavior/price-alert-subscriptions/{productId}', [PriceAlertSubscriptionController::class, 'deactivate'])->middleware('throttle:60,1');
    Route::get('/behavior/email-preferences', [EmailPreferenceController::class, 'show'])->middleware(['auth:sanctum', 'active.user', 'throttle:60,1']);
    Route::put('/behavior/email-preferences', [EmailPreferenceController::class, 'update'])->middleware(['auth:sanctum', 'active.user', 'throttle:60,1']);
    Route::get('/behavior/unsubscribe/{token}', [EmailPreferenceController::class, 'unsubscribe'])->middleware('throttle:30,1');

    // Reviews endpoints (public read, auth write)
    Route::get('/products/reviews/stats', [ReviewController::class, 'stats']);
    Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);
    Route::get('/shares/public/cart/{token}', [QuoteOrderInvoiceController::class, 'getPublicSharedCart']);
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::post('/product-sourcing-requests', [ProductSourcingRequestController::class, 'store'])
            ->middleware('throttle:10,1');
        Route::post('/products/{productId}/reviews', [ReviewController::class, 'store']);
        Route::delete('/products/{productId}/reviews/{reviewId}', [ReviewController::class, 'destroy']);
    });

    // Quotes endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/quotes', [QuoteOrderInvoiceController::class, 'getQuotes']);
        Route::post('/quotes', [QuoteOrderInvoiceController::class, 'createQuote']);
        Route::post('/shares/product', [QuoteOrderInvoiceController::class, 'shareProduct']);
        Route::post('/shares/cart', [QuoteOrderInvoiceController::class, 'shareCart']);
        Route::post('/shares/cart/send-email', [QuoteOrderInvoiceController::class, 'sendCartShareEmail']);
        Route::get('/shares/cart/{messageId}', [QuoteOrderInvoiceController::class, 'getSharedCart']);
        Route::get('/quotes/{quoteId}', [QuoteOrderInvoiceController::class, 'getQuote']);
        Route::get('/quotes/{quoteId}/pdf', [QuoteOrderInvoiceController::class, 'downloadQuotePdf']);
        Route::post('/quotes/{quoteId}/cancel', [QuoteOrderInvoiceController::class, 'cancelQuote']);
        Route::post('/quotes/{quoteId}/convert-to-order', [QuoteOrderInvoiceController::class, 'convertQuoteToOrder']);
    });

    // Orders endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/orders', [QuoteOrderInvoiceController::class, 'getOrders']);
        Route::get('/orders/shipping/live', [QuoteOrderInvoiceController::class, 'getLiveShipping']);
        Route::get('/orders/{orderNumber}', [QuoteOrderInvoiceController::class, 'getOrder']);
        Route::post('/orders/{orderNumber}/cancel', [QuoteOrderInvoiceController::class, 'cancelOrder']);
        Route::post('/orders/{orderNumber}/mark-delivered', [QuoteOrderInvoiceController::class, 'markOrderDelivered']);
    });

    // Invoices endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/invoices', [QuoteOrderInvoiceController::class, 'getInvoices']);
        Route::get('/invoices/{invoiceNumber}', [QuoteOrderInvoiceController::class, 'getInvoice']);
        Route::get('/invoices/{invoiceNumber}/pdf', [QuoteOrderInvoiceController::class, 'downloadInvoicePdf']);
        Route::post('/invoices/combine', [QuoteOrderInvoiceController::class, 'combineInvoices']);
        Route::post('/invoices/{invoiceNumber}/pay', [QuickBooksPaymentController::class, 'createInvoiceCheckoutSession']);
        Route::post('/invoices/{invoiceNumber}/pay-default', [QuickBooksPaymentController::class, 'payInvoiceWithDefaultCard']);
        Route::post('/invoices/pay-multiple', [QuickBooksPaymentController::class, 'createBulkInvoiceCheckoutSession']);
        Route::post('/invoices/pay-multiple-default', [QuickBooksPaymentController::class, 'payBulkInvoicesWithDefaultCard']);
    });

    // Saved payment methods (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/payment-methods', [QuickBooksPaymentController::class, 'listSavedPaymentMethods']);
        Route::post('/payment-methods/consent', [QuickBooksPaymentController::class, 'updatePaymentMethodConsent']);
        Route::post('/payment-methods/setup-intent', [QuickBooksPaymentController::class, 'createSetupIntent']);
        Route::post('/payment-methods/attach', [QuickBooksPaymentController::class, 'attachPaymentMethod']);
        Route::put('/payment-methods/default', [QuickBooksPaymentController::class, 'updateDefaultPaymentMethod']);
        Route::delete('/payment-methods/{paymentMethodId}', [QuickBooksPaymentController::class, 'removePaymentMethod']);
    });

    // Activities endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/activities', [ActivityController::class, 'getActivities']);
        Route::post('/activities/log', [ActivityController::class, 'logActivity']);
    });

    // Messages endpoints (protected)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        Route::get('/messages', [MessageController::class, 'getMessages']);
        Route::get('/messages/chats', [MessageController::class, 'getChatSessions']);
        Route::post('/messages/chats', [MessageController::class, 'createChatSession']);
        Route::post('/messages/chats/bulk-delete', [MessageController::class, 'bulkDeleteChatSessions']);
        Route::get('/messages/chats/{chatSessionId}', [MessageController::class, 'getChatSessionMessages']);
        Route::delete('/messages/chats/{chatSessionId}', [MessageController::class, 'deleteChatSession']);
        Route::post('/messages/chats/{chatSessionId}/escalate', [MessageController::class, 'escalateChatSession']);
        Route::post('/messages/assistant/chat', [MessageController::class, 'assistantChat'])->middleware('throttle:30,1');
        Route::get('/messages/unread-count', [MessageController::class, 'getUnreadCount']);
        Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead']);
        Route::post('/messages/mark-all-read', [MessageController::class, 'markAllAsRead']);
        Route::delete('/messages/{id}', [MessageController::class, 'deleteMessage']);
    });

    // Admin endpoints (protected & requires admin role)
    Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
        // Dashboard stats
        Route::get('/admin/dashboard/stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/admin/products', [AdminImportedProductController::class, 'all']);
        Route::get('/admin/imported-products', [AdminImportedProductController::class, 'index']);
        Route::get('/admin/imported-products/supplier-search', [AdminImportedProductController::class, 'supplierSearch']);
        Route::post('/admin/imported-products/import', [AdminImportedProductController::class, 'importSupplierProduct']);
        Route::put('/admin/imported-products/{product}', [AdminImportedProductController::class, 'update']);
        Route::put('/admin/imported-products/{product}/storefront-pin', [AdminImportedProductController::class, 'updateStorefrontPin']);
        Route::post('/admin/imported-products/{product}/enrich-image', [AdminImportedProductController::class, 'enrichImage']);
        
        // Customer management
        Route::get('/admin/customers', [AdminController::class, 'getCustomers']);
        Route::post('/admin/customers/invite', [AdminController::class, 'inviteCustomerUser']);
        Route::put('/admin/customers/users/{userId}', [AdminController::class, 'updateCustomerUser']);
        Route::post('/admin/customers/users/{userId}/special-pricing', [AdminController::class, 'setUserSpecialPricing']);
        Route::post('/admin/customers/users/{userId}/approve', [AdminController::class, 'approveCustomerUser']);
        Route::post('/admin/customers/{companyId}/approve', [AdminController::class, 'approveCustomer']);
        Route::post('/admin/customers/bulk-approve', [AdminController::class, 'bulkApproveCustomers']);
        Route::post('/admin/customers/bulk-delete', [AdminController::class, 'bulkDeleteUsers']);
        Route::post('/admin/customers/bulk-suspend', [AdminController::class, 'bulkSuspendUsers']);
        Route::post('/admin/customers/resend-invite', [AdminController::class, 'resendCustomerInvite']);

        // Quote management
        Route::get('/admin/quotes/stats', [AdminController::class, 'getQuoteStats']);
        Route::get('/admin/quotes/pending', [AdminController::class, 'getPendingQuotes']);
        Route::get('/admin/quotes/approved', [AdminController::class, 'getApprovedQuotes']);
        Route::get('/admin/quotes/rejected', [AdminController::class, 'getRejectedQuotes']);
        Route::get('/admin/quotes', [AdminController::class, 'getQuotes']);
        Route::get('/admin/quotes/{quoteId}', [AdminController::class, 'getQuoteDetails']);
        Route::post('/admin/quotes/{quoteId}/approve', [AdminController::class, 'approveQuote']);
        Route::post('/admin/quotes/{quoteId}/reject', [AdminController::class, 'rejectQuote']);
        Route::post('/admin/quotes/bulk-delete', [AdminController::class, 'bulkDeleteQuotes']);

        // Order management
        Route::get('/admin/orders', [AdminController::class, 'getAllOrders']);
        Route::get('/admin/orders/tracking', [AdminController::class, 'getConfirmedOrdersTracking']);
        Route::post('/admin/orders/bulk-delete', [AdminController::class, 'bulkDeleteOrders']);
        Route::get('/admin/orders/{orderNumber}/status', [AdminController::class, 'getOrderStatus']);
        Route::post('/admin/orders/{orderNumber}/cancel', [AdminController::class, 'adminCancelOrder']);

        // Reports
        Route::get('/admin/reports/revenue', [AdminController::class, 'getRevenueReport']);
        Route::get('/admin/reports/top-customers', [AdminController::class, 'getTopCustomers']);

        // Settings
        Route::get('/admin/settings', [AdminController::class, 'getSettings']);
        Route::post('/admin/settings/profile', [AdminController::class, 'updateProfile']);
        Route::post('/admin/settings/api', [AdminController::class, 'updateApiConfig']);
        Route::get('/admin/settings/api/test', [AdminController::class, 'testApiConnection']);
        Route::get('/admin/settings/catalog/status', [AdminController::class, 'getCatalogOperationsStatus']);
        Route::post('/admin/settings/catalog/run', [AdminController::class, 'runCatalogOperation']);
        Route::post('/admin/settings/catalog/start-worker', [AdminController::class, 'startProductsSyncWorker']);
        Route::post('/admin/settings/catalog/stop-all', [AdminController::class, 'stopAllBackgroundJobs']);
        Route::post('/admin/settings/email', [AdminController::class, 'updateEmailSettings']);
        Route::post('/admin/settings/price-sync', [AdminController::class, 'updatePriceSyncSettings']);
        Route::post('/admin/settings/price-sync/run-now', [AdminController::class, 'runPriceSyncNow']);
        Route::get('/admin/settings/price-sync/state', [AdminController::class, 'getPriceSyncRunState']);
        Route::get('/admin/settings/lifecycle/metrics', [AdminController::class, 'getLifecycleCampaignMetrics']);
        Route::get('/admin/settings/runtime/health', [AdminController::class, 'getRuntimeHealth']);
        Route::post('/admin/settings/system', [AdminController::class, 'updateSystemSettings']);


        // Activity Logs
        Route::get('/admin/logs/admins', [AdminController::class, 'getAdminActivityLogs']);
        Route::get('/admin/logs/users', [AdminController::class, 'getUserActivityLogs']);
        Route::post('/admin/logs/bulk-delete', [AdminController::class, 'bulkDeleteActivityLogs']);

        // Admin User Management
        Route::get('/admin/users', [AdminController::class, 'getAdminUsers']);
        Route::post('/admin/users', [AdminController::class, 'createAdminUser']);
        Route::post('/admin/users/{userId}/resend-credentials', [AdminController::class, 'resendAdminCredentials']);
        Route::post('/admin/users/{userId}/suspend', [AdminController::class, 'suspendAdminUser']);
        Route::post('/admin/users/{userId}/activate', [AdminController::class, 'activateAdminUser']);
        Route::put('/admin/users/{userId}/permissions', [AdminController::class, 'updateAdminPermissions']);
        Route::put('/admin/users/{userId}/role', [AdminController::class, 'updateAdminRole']);
        Route::post('/admin/users/bulk-suspend', [AdminController::class, 'bulkSuspendAdmins']);
        Route::post('/admin/users/bulk-activate', [AdminController::class, 'bulkActivateAdmins']);
        Route::post('/admin/users/bulk-delete', [AdminController::class, 'bulkDeleteAdmins']);
        Route::delete('/admin/users/{userId}', [AdminController::class, 'deleteAdminUser']);

        // Admin Chat (escalated sessions inbox)
        Route::get('/admin/chats', [MessageController::class, 'adminGetEscalatedChats']);
        Route::get('/admin/chats/unread-count', [MessageController::class, 'adminGetEscalatedCount']);
        Route::get('/admin/chats/{chatSessionId}', [MessageController::class, 'adminGetChatSession']);
        Route::post('/admin/chats/{chatSessionId}/reply', [MessageController::class, 'adminReplyToChat']);
        Route::post('/admin/chats/{chatSessionId}/resolve', [MessageController::class, 'adminResolveChat']);

        // Invoice Management
        Route::get('/admin/invoices/stats', [AdminController::class, 'getInvoiceStats']);
        Route::get('/admin/invoices', [AdminController::class, 'getInvoices']);
        Route::post('/admin/invoices/bulk-delete', [AdminController::class, 'bulkDeleteInvoices']);
        Route::post('/admin/invoices/bulk-mark-paid', [AdminController::class, 'bulkMarkInvoicesPaid']);
        Route::post('/admin/invoices/bulk-cancel', [AdminController::class, 'bulkCancelInvoices']);
        Route::put('/admin/invoices/{invoiceId}', [AdminController::class, 'updateInvoiceCharges']);
        Route::get('/admin/invoices/{status}', [AdminController::class, 'getInvoicesByStatus']);
        Route::post('/admin/invoices/{invoiceId}/mark-paid', [AdminController::class, 'markInvoiceAsPaid']);
        Route::post('/admin/invoices/{invoiceId}/send-reminder', [AdminController::class, 'sendInvoiceReminder']);
        Route::get('/admin/invoices/{invoiceId}/pdf', [AdminController::class, 'downloadInvoicePdf']);
    });
});
