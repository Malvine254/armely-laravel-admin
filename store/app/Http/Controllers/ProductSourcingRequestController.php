<?php

namespace App\Http\Controllers;

use App\Mail\ProductSourcingRequestSubmitted;
use App\Models\Notification;
use App\Models\ProductSourcingRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProductSourcingRequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search_query' => ['required', 'string', 'max:500'],
            'manufacturer' => ['nullable', 'string', 'max:100'],
            'model_or_part_number' => ['nullable', 'string', 'max:150'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $sourcingRequest = DB::transaction(function () use ($validated, $user) {
            $record = ProductSourcingRequest::create([
                ...$validated,
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

            User::query()
                ->whereIn('role', ['admin', 'super_admin'])
                ->where('status', 'active')
                ->pluck('id')
                ->each(fn ($adminId) => Notification::createNotification(
                    (int) $adminId,
                    'product_sourcing_requested',
                    'New product sourcing request',
                    $user->name . ' requested help sourcing: ' . $record->search_query,
                    (string) $record->id,
                    'product_sourcing_request',
                    'high',
                    ['request_id' => $record->id, 'quantity' => $record->quantity]
                ));

            return $record;
        });

        $sourcingRequest->load('user');
        $adminEmails = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->unique();

        foreach ($adminEmails as $adminEmail) {
            try {
                Mail::to($adminEmail)->send(new ProductSourcingRequestSubmitted($sourcingRequest));
            } catch (Throwable $emailError) {
                // The request is already safely stored; an email transport issue
                // must not make the customer retry and create a duplicate request.
                Log::warning('Failed to email admin about product sourcing request.', [
                    'request_id' => $sourcingRequest->id,
                    'admin_email' => $adminEmail,
                    'error' => $emailError->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $sourcingRequest,
            'message' => 'Your sourcing request was sent to our procurement team.',
        ], 201);
    }
}
