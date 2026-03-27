<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Get all messages for the authenticated user
     */
    public function getMessages(Request $request)
    {
        $status = $request->query('status'); // 'unread', 'read', or null for all
        $type = $request->query('type'); // 'order', 'quote', 'invoice', 'system'
        $limit = $request->query('limit', 20);
        
        $query = Message::where('user_id', $request->user()->id);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $messages = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($message) {
                $action = $this->resolveMessageAction($message);

                return [
                    'id' => $message->id,
                    'type' => $message->type,
                    'title' => $message->title,
                    'message' => $message->message,
                    'reference_id' => $message->reference_id,
                    'status' => $message->status,
                    'priority' => $message->priority,
                    'metadata' => $message->metadata,
                    'read_at' => $message->read_at,
                    'created_at' => $message->created_at,
                    'time_ago' => $this->getTimeAgo($message->created_at),
                    'action_link' => $action['link'],
                    'action_label' => $action['label'],
                ];
            });

        $unreadCount = Message::where('user_id', $request->user()->id)
            ->where('status', 'unread')
            ->count();

        return response()->json([
            'success' => true,
            'data' => $messages,
            'unread_count' => $unreadCount,
            'total' => count($messages),
        ]);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request, $id)
    {
        $message = Message::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $message->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
        ]);
    }

    /**
     * Mark all messages as read
     */
    public function markAllAsRead(Request $request)
    {
        Message::where('user_id', $request->user()->id)
            ->where('status', 'unread')
            ->update([
                'status' => 'read',
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All messages marked as read',
        ]);
    }

    /**
     * Delete a message
     */
    public function deleteMessage(Request $request, $id)
    {
        $message = Message::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(Request $request)
    {
        $count = Message::where('user_id', $request->user()->id)
            ->where('status', 'unread')
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Convert timestamp to human-readable "time ago" format
     */
    private function getTimeAgo($date)
    {
        $now = now();
        $diff = $now->diff($date);

        if ($diff->days > 0) {
            if ($diff->days === 1) return '1 day ago';
            if ($diff->days < 7) return $diff->days . ' days ago';
            if ($diff->days < 30) return floor($diff->days / 7) . ' weeks ago';
            return floor($diff->days / 30) . ' months ago';
        }
        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }
        return 'just now';
    }

    /**
     * Resolve a user-facing deep link for a message.
     */
    private function resolveMessageAction($message): array
    {
        $referenceId = trim((string) ($message->reference_id ?? ''));
        $title = strtolower((string) ($message->title ?? ''));
        $body = strtolower((string) ($message->message ?? ''));

        if ($message->type === 'invoice' && $referenceId !== '') {
            $isPaymentDue = str_contains($title, 'ready for payment')
                || str_contains($title, 'due')
                || str_contains($body, 'ready for payment')
                || str_contains($body, 'pay')
                || str_contains($body, 'due');

            if ($isPaymentDue) {
                return [
                    'link' => '/payment?mode=invoice&invoiceNumber=' . urlencode($referenceId) . '&from=' . urlencode('/messages'),
                    'label' => 'Pay now',
                ];
            }

            return [
                'link' => '/invoices?invoiceNumber=' . urlencode($referenceId),
                'label' => 'View invoice',
            ];
        }

        if ($message->type === 'quote' && $referenceId !== '') {
            return [
                'link' => '/quotes?view=' . urlencode($referenceId),
                'label' => 'View quote',
            ];
        }

        if ($message->type === 'order' && $referenceId !== '') {
            return [
                'link' => '/orders?view=' . urlencode($referenceId),
                'label' => 'View order',
            ];
        }

        return [
            'link' => null,
            'label' => null,
        ];
    }
}
