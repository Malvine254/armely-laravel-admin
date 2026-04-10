<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Message;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Quote;
use App\Models\Product;
use App\Models\User;
use App\Services\AzureOpenAiChatService;
use App\Services\NotificationService;
use App\Services\TDSynnexService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function __construct(
        private AzureOpenAiChatService $assistantService,
        private NotificationService $notificationService,
        private TDSynnexService $tdsynnexService
    )
    {
    }

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

    public function getChatSessions(Request $request): JsonResponse
    {
        $sessions = ChatSession::where('user_id', $request->user()->id)
            ->with(['messages' => fn ($q) => $q->latest('id')->limit(1)])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->limit(40)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions->map(function (ChatSession $session) {
                $last = $session->messages->first();
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'updated_at' => $session->updated_at,
                    'last_message_at' => $session->last_message_at,
                    'last_message_preview' => $last ? Str::limit((string) $last->content, 80) : null,
                    'last_message_role' => $last?->role,
                    'escalated_to_human' => (bool) $session->escalated_to_human,
                    'escalated_at' => $session->escalated_at,
                    'resolved_at' => $session->resolved_at,
                ];
            })->values(),
        ]);
    }

    public function createChatSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:120',
        ]);

        $requestedTitle = trim((string) ($validated['title'] ?? ''));
        $normalizedTitle = $requestedTitle !== '' ? $requestedTitle : 'New chat';

        // Prevent creating multiple empty sessions for the same user.
        // Reuse the most recently updated empty thread instead.
        $existingEmpty = ChatSession::where('user_id', $request->user()->id)
            ->whereDoesntHave('messages')
            ->orderByDesc('updated_at')
            ->first();

        if ($existingEmpty) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $existingEmpty->id,
                    'title' => $existingEmpty->title,
                    'updated_at' => $existingEmpty->updated_at,
                    'last_message_at' => $existingEmpty->last_message_at,
                    'last_message_preview' => null,
                    'last_message_role' => null,
                    'escalated_to_human' => (bool) $existingEmpty->escalated_to_human,
                    'escalated_at' => $existingEmpty->escalated_at,
                    'resolved_at' => $existingEmpty->resolved_at,
                ],
                'reused_empty_session' => true,
            ]);
        }

        $session = ChatSession::create([
            'user_id' => $request->user()->id,
            'title' => $normalizedTitle,
            'last_message_at' => null,
            'escalated_to_human' => false,
            'escalated_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $session->id,
                'title' => $session->title,
                'updated_at' => $session->updated_at,
                'last_message_at' => $session->last_message_at,
                'last_message_preview' => null,
                'last_message_role' => null,
                'escalated_to_human' => false,
                'escalated_at' => null,
            ],
        ], 201);
    }

    public function getChatSessionMessages(Request $request, int $chatSessionId): JsonResponse
    {
        $session = ChatSession::where('user_id', $request->user()->id)
            ->where('id', $chatSessionId)
            ->firstOrFail();

        $messages = ChatMessage::where('chat_session_id', $session->id)
            ->orderBy('id')
            ->get()
            ->map(function (ChatMessage $message) {
                $senderName = null;

                if ($message->role === 'admin') {
                    $senderName = (string) data_get($message->metadata, 'admin_name', 'Support Team');
                }

                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'text' => $message->content,
                    'actions' => $message->actions ?? [],
                    'product_suggestions' => (array) data_get($message->metadata, 'product_suggestions', []),
                    'sender_name' => $senderName,
                    'created_at' => $message->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'updated_at' => $session->updated_at,
                    'last_message_at' => $session->last_message_at,
                    'escalated_to_human' => (bool) $session->escalated_to_human,
                    'escalated_at' => $session->escalated_at,
                    'resolved_at' => $session->resolved_at,
                ],
                'messages' => $messages,
            ],
        ]);;
    }

    public function escalateChatSession(Request $request, int $chatSessionId): JsonResponse
    {
        $validated = $request->validate([
            'note' => 'nullable|string|max:600',
        ]);

        $session = ChatSession::where('user_id', $request->user()->id)
            ->where('id', $chatSessionId)
            ->firstOrFail();

        $note = trim((string) ($validated['note'] ?? ''));
        $isResolved = !empty($session->resolved_at);

        if (!$session->escalated_to_human || $isResolved) {
            $session->forceFill([
                'escalated_to_human' => true,
                'escalated_at' => now(),
                'resolved_at' => null,
            ])->save();

            $handoffText = $isResolved
                ? 'This ticket was reopened and escalated to human support. A team member will continue this conversation shortly.'
                : 'Your chat was escalated to human support. A team member can continue this conversation shortly.';

            ChatMessage::create([
                'chat_session_id' => $session->id,
                'user_id' => $request->user()->id,
                'role' => 'assistant',
                'content' => $handoffText,
                'actions' => [],
                'metadata' => [
                    'source' => $isResolved ? 'human_escalation_reopen' : 'human_escalation',
                    'note' => $note,
                ],
            ]);

            $session->forceFill([
                'last_message_at' => now(),
            ])->save();

            $admins = User::query()
                ->whereIn('role', ['admin', 'owner', 'manager'])
                ->get(['id']);

            foreach ($admins as $admin) {
                Message::createMessage(
                    (int) $admin->id,
                    'system',
                    'Chat escalated to human support',
                    ($isResolved
                        ? 'A customer reopened and escalated Mela AI chat session #'
                        : 'A customer requested human follow-up in Mela AI chat session #') . $session->id,
                    'CHAT-' . $session->id,
                    'high',
                    [
                        'chat_session_id' => $session->id,
                        'customer_id' => $request->user()->id,
                        'note' => $note,
                        'reopened' => $isResolved,
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Chat escalated to human support',
            'data' => [
                'id' => $session->id,
                'escalated_to_human' => true,
                'escalated_at' => $session->escalated_at,
                'resolved_at' => $session->resolved_at,
            ],
        ]);
    }

    /**
     * Assistant chat endpoint backed by Azure OpenAI with account DB context.
     */
    public function assistantChat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'chat_session_id' => 'nullable|integer',
        ]);

        try {

        $user = $request->user();
        $question = trim((string) $validated['message']);
        $session = $this->resolveOrCreateChatSession($user->id, $validated['chat_session_id'] ?? null);
        $wantsEscalation = $this->isUserEscalationIntent($question);

        $storedUserMessage = ChatMessage::create([
            'chat_session_id' => $session->id,
            'user_id' => $user->id,
            'role' => 'user',
            'content' => $question,
            'actions' => [],
        ]);

        $this->syncSessionTitle($session, $storedUserMessage->content);

        if (!empty($session->resolved_at)) {
            if ($wantsEscalation) {
                $session->forceFill([
                    'escalated_to_human' => true,
                    'escalated_at' => now(),
                    'resolved_at' => null,
                ])->save();

                $handoffReply = 'Understood. I reopened this ticket and escalated it to human support. A team member will continue from here shortly.';

                ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'user_id' => $user->id,
                    'role' => 'assistant',
                    'content' => $handoffReply,
                    'actions' => [],
                    'metadata' => [
                        'source' => 'human_handoff_reopen_requested',
                    ],
                ]);

                $session->forceFill([
                    'last_message_at' => now(),
                ])->save();

                $admins = User::query()
                    ->whereIn('role', ['admin', 'owner', 'manager'])
                    ->get(['id']);

                foreach ($admins as $admin) {
                    Message::createMessage(
                        (int) $admin->id,
                        'system',
                        'Chat escalated to human support',
                        'A customer reopened and escalated Mela AI chat session #' . $session->id,
                        'CHAT-' . $session->id,
                        'high',
                        [
                            'chat_session_id' => $session->id,
                            'customer_id' => $user->id,
                            'source' => 'assistant_reopen_escalation_intent',
                            'reopened' => true,
                        ]
                    );
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'reply' => $handoffReply,
                        'actions' => [],
                        'product_suggestions' => [],
                        'source' => 'human_handoff_reopen_requested',
                        'chat_session' => [
                            'id' => $session->id,
                            'title' => $session->title,
                        ],
                    ],
                ]);
            }

            // Resolved tickets return to AI mode unless user explicitly asks for human support.
            // Keep resolved_at/escalated_at for admin history visibility.
            $session->forceFill([
                'escalated_to_human' => false,
            ])->save();
        }

        if (!(bool) $session->escalated_to_human && $wantsEscalation) {
            $session->forceFill([
                'escalated_to_human' => true,
                'escalated_at' => now(),
                'resolved_at' => null,
            ])->save();

            $handoffReply = 'Understood. I escalated this chat to human support. A team member will continue from here shortly.';

            ChatMessage::create([
                'chat_session_id' => $session->id,
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $handoffReply,
                'actions' => [],
                'metadata' => [
                    'source' => 'human_handoff_requested',
                ],
            ]);

            $session->forceFill([
                'last_message_at' => now(),
            ])->save();

            $admins = User::query()
                ->whereIn('role', ['admin', 'owner', 'manager'])
                ->get(['id']);

            foreach ($admins as $admin) {
                Message::createMessage(
                    (int) $admin->id,
                    'system',
                    'Chat escalated to human support',
                    'A customer requested human follow-up in Mela AI chat session #' . $session->id,
                    'CHAT-' . $session->id,
                    'high',
                    [
                        'chat_session_id' => $session->id,
                        'customer_id' => $user->id,
                        'source' => 'assistant_escalation_intent',
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $handoffReply,
                    'actions' => [],
                    'product_suggestions' => [],
                    'source' => 'human_handoff_requested',
                    'chat_session' => [
                        'id' => $session->id,
                        'title' => $session->title,
                    ],
                ],
            ]);
        }

        if ((bool) $session->escalated_to_human) {
            // Check if admin has replied yet
            $adminMessages = ChatMessage::where('chat_session_id', $session->id)
                ->where('role', 'admin')
                ->exists();

            if (!$adminMessages) {
                // No admin reply yet; send handoff message
                $handoffReply = 'This chat is already escalated to human support. A team member will continue from here shortly.';

                ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'user_id' => $user->id,
                    'role' => 'assistant',
                    'content' => $handoffReply,
                    'actions' => [],
                    'metadata' => [
                        'source' => 'human_handoff_guard',
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'reply' => $handoffReply,
                        'actions' => [],
                        'product_suggestions' => [],
                        'source' => 'human_handoff_guard',
                        'chat_session' => [
                            'id' => $session->id,
                            'title' => $session->title,
                        ],
                    ],
                ]);
            }

            // Admin has replied; just acknowledge message silently
            $session->forceFill([
                'last_message_at' => now(),
            ])->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => 'Your message has been received. A team member will respond shortly.',
                    'actions' => [],
                    'product_suggestions' => [],
                    'source' => 'human_handoff_silent',
                    'chat_session' => [
                        'id' => $session->id,
                        'title' => $session->title,
                    ],
                ],
            ]);
        }

        $context = $this->buildAssistantContext($user, $question, $session->id);
        $directHandled = $this->handleDirectAssistantAction($user, $question, $context);

        if ($directHandled !== null) {
            ChatMessage::create([
                'chat_session_id' => $session->id,
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $directHandled['reply'],
                'actions' => $directHandled['actions'],
                'metadata' => [
                    'source' => 'direct_invoice_reminder',
                    'product_suggestions' => [],
                ],
            ]);

            $session->forceFill([
                'last_message_at' => now(),
            ])->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $directHandled['reply'],
                    'actions' => $directHandled['actions'],
                    'product_suggestions' => [],
                    'source' => 'direct_invoice_reminder',
                    'chat_session' => [
                        'id' => $session->id,
                        'title' => $session->title,
                    ],
                ],
            ]);
        }

        $assistantReply = $this->assistantService->generateReply($question, $context);
        $usedFallback = false;

        if (!$assistantReply) {
            $assistantReply = $this->buildFallbackReply($question, $context);
            $usedFallback = true;
        }

        $actions = $this->buildAssistantActions($question, $context);
        $productSuggestions = (array) ($context['product_suggestions'] ?? []);

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'user_id' => $user->id,
            'role' => 'assistant',
            'content' => $assistantReply,
            'actions' => $actions,
            'metadata' => [
                'source' => $usedFallback ? 'local_fallback' : 'azure_openai',
                'product_suggestions' => $productSuggestions,
            ],
        ]);

        $session->forceFill([
            'last_message_at' => now(),
        ])->save();

        return response()->json([
            'success' => true,
            'data' => [
                'reply' => $assistantReply,
                'actions' => $actions,
                'product_suggestions' => $productSuggestions,
                'source' => $usedFallback ? 'local_fallback' : 'azure_openai',
                'chat_session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                ],
            ],
        ]);
        } catch (\Throwable $e) {
            Log::error('Mela AI assistantChat failed', [
                'user_id' => $request->user()?->id,
                'chat_session_id' => $validated['chat_session_id'] ?? null,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => 'Mela AI is temporarily unavailable. Please try again shortly, or request human support.',
                    'actions' => [
                        [
                            'label' => 'Request human support',
                            'link' => '/messages',
                        ],
                    ],
                    'product_suggestions' => [],
                    'source' => 'assistant_error_fallback',
                    'chat_session' => [
                        'id' => $validated['chat_session_id'] ?? null,
                        'title' => 'New chat',
                    ],
                ],
            ], 200);
        }
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

    private function handleDirectAssistantAction(User $user, string $question, array $context): ?array
    {
        if (!$this->isInvoiceReminderIntent($question)) {
            return null;
        }

        $focusedInvoice = $context['focused_invoice'] ?? null;
        if (!is_array($focusedInvoice) || empty($focusedInvoice['invoice_number'])) {
            return [
                'reply' => 'I can send an invoice reminder email once you include the invoice number. Send a message like: send invoice reminder for INV-202604-00012 to my email.',
                'actions' => [
                    [
                        'label' => 'See all invoices',
                        'link' => '/invoices',
                    ],
                ],
            ];
        }

        $invoiceNumber = (string) $focusedInvoice['invoice_number'];
        $invoice = Invoice::where('user_id', $user->id)
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if (!$invoice) {
            return [
                'reply' => "I could not find invoice {$invoiceNumber} on your account.",
                'actions' => [
                    [
                        'label' => 'See all invoices',
                        'link' => '/invoices',
                    ],
                ],
            ];
        }

        $recipientEmail = trim((string) ($user->email ?? ''));
        if ($recipientEmail === '') {
            return [
                'reply' => "I found {$invoiceNumber}, but your account does not have a valid email address for sending the reminder.",
                'actions' => [
                    [
                        'label' => 'Update profile',
                        'link' => '/profile',
                    ],
                    [
                        'label' => 'See all invoices',
                        'link' => '/invoices',
                    ],
                ],
            ];
        }

        $totalAmount = (float) ($invoice->total_amount ?? 0);
        $paidAmount = (float) ($invoice->paid_amount ?? 0);
        $balanceDue = max(0, $totalAmount - $paidAmount);

        if (strtolower((string) $invoice->status) === 'paid' || $balanceDue <= 0.01) {
            return [
                'reply' => "Invoice {$invoiceNumber} is already fully paid, so no reminder email is needed.",
                'actions' => [
                    [
                        'label' => 'View invoice',
                        'link' => '/invoices?invoiceNumber=' . urlencode($invoiceNumber),
                    ],
                ],
            ];
        }

        try {
            $sent = $this->notificationService->sendInvoiceReminderNotification($invoice);
        } catch (\Throwable $e) {
            Log::error('Assistant failed to send invoice reminder: ' . $e->getMessage(), [
                'invoice_number' => $invoiceNumber,
                'user_id' => $user->id,
            ]);

            return [
                'reply' => "I found {$invoiceNumber}, but the reminder email could not be sent right now. Please try again in a moment.",
                'actions' => [
                    [
                        'label' => 'See all invoices',
                        'link' => '/invoices',
                    ],
                ],
            ];
        }

        if (!$sent) {
            return [
                'reply' => "I found {$invoiceNumber}, but the reminder email could not be sent right now. Please try again in a moment.",
                'actions' => [
                    [
                        'label' => 'See all invoices',
                        'link' => '/invoices',
                    ],
                ],
            ];
        }

        $dueAt = optional($invoice->due_at)?->toDateString();
        $dueText = $dueAt ? " payment is due by {$dueAt}." : '';
        $balanceText = number_format($balanceDue, 2);

        return [
            'reply' => "I sent an invoice reminder for {$invoiceNumber} to your email: {$recipientEmail}. Balance due is \${$balanceText}.{$dueText}",
            'actions' => [
                [
                    'label' => 'View invoice',
                    'link' => '/invoices?invoiceNumber=' . urlencode($invoiceNumber),
                ],
                [
                    'label' => 'Download invoice PDF',
                    'link' => '/api/v1/invoices/' . urlencode($invoiceNumber) . '/pdf',
                ],
                [
                    'label' => 'Pay this invoice',
                    'link' => '/payment?mode=invoice&invoiceNumber=' . urlencode($invoiceNumber) . '&from=' . urlencode('/messages'),
                ],
            ],
        ];
    }

    private function isInvoiceReminderIntent(string $question): bool
    {
        $normalized = strtolower($question);

        return Str::contains($normalized, [
            'send invoice reminder',
            'send reminder',
            'email reminder',
            'remind me about invoice',
            'send the reminder',
            'send this invoice reminder',
        ]);
    }

    private function isUserEscalationIntent(string $message): bool
    {
        $normalized = strtolower(trim($message));
        if ($normalized === '') {
            return false;
        }

        if (Str::contains($normalized, [
            'do not escalate',
            'dont escalate',
            "don't escalate",
            'no need to escalate',
            'no escalation',
            'keep this with ai',
            'use ai',
        ])) {
            return false;
        }

        return Str::contains($normalized, [
            'escalate',
            'human support',
            'human agent',
            'talk to a human',
            'talk to human',
            'speak to a human',
            'speak to human',
            'live agent',
            'real person',
            'support agent',
            'handoff to human',
        ]);
    }

    private function buildAssistantContext($user, string $question, ?int $chatSessionId = null): array
    {
        $recentInvoices = Invoice::where('user_id', $user->id)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get(['invoice_number', 'status', 'total_amount', 'paid_amount', 'due_at', 'order_number']);

        $recentOrders = Order::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get(['order_number', 'quote_id', 'status', 'payment_status', 'total_amount', 'tracking_info', 'created_at']);

        $approvedQuotes = Quote::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('order:id,quote_id,order_number,payment_status,status')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['quote_id', 'status', 'total_amount', 'created_at']);

        $completedPaidQuotes = $approvedQuotes
            ->filter(function (Quote $quote) {
                $paymentStatus = strtolower((string) optional($quote->order)->payment_status);
                return in_array($paymentStatus, ['paid', 'completed'], true);
            })
            ->values()
            ->map(function (Quote $quote) {
                return [
                    'quote_id' => $quote->quote_id,
                    'total_amount' => (float) $quote->total_amount,
                    'order_number' => optional($quote->order)->order_number,
                    'payment_status' => optional($quote->order)->payment_status,
                    'order_status' => optional($quote->order)->status,
                    'created_at' => optional($quote->created_at)?->toIso8601String(),
                ];
            })
            ->all();

        $invoiceNumber = $this->extractInvoiceNumber($question);
        $focusedInvoice = null;
        if ($invoiceNumber !== null) {
            $focusedInvoice = Invoice::where('user_id', $user->id)
                ->where('invoice_number', $invoiceNumber)
                ->first(['invoice_number', 'status', 'total_amount', 'paid_amount', 'due_at', 'order_number']);
        }

        $invoiceSummaries = $recentInvoices->map(function (Invoice $invoice) {
            $remaining = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
            return [
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'order_number' => $invoice->order_number,
                'total_amount' => (float) $invoice->total_amount,
                'paid_amount' => (float) $invoice->paid_amount,
                'remaining_amount' => $remaining,
                'due_at' => optional($invoice->due_at)?->toDateString(),
            ];
        })->all();

        $openInvoiceTotal = collect($invoiceSummaries)
            ->filter(static fn (array $invoice) => (float) ($invoice['remaining_amount'] ?? 0) > 0.01)
            ->sum('remaining_amount');

        $recentChatTurns = [];
        if ($chatSessionId) {
            $recentChatTurns = ChatMessage::where('chat_session_id', $chatSessionId)
                ->orderByDesc('id')
                ->limit(10)
                ->get(['role', 'content'])
                ->reverse()
                ->values()
                ->map(fn (ChatMessage $item) => [
                    'role' => $item->role,
                    'content' => $item->content,
                ])
                ->all();
        }

            $historyPreferences = $this->extractPreferenceKeywordsFromHistory($recentChatTurns);
        $shouldSuggestProducts = $this->isProductDiscoveryIntent($question, $recentChatTurns);
            $searchContext = $this->buildProductSearchContext($question, $recentChatTurns);
        $productSuggestions = $shouldSuggestProducts
                ? $this->searchProductsForAssistant($question, $historyPreferences, 6, $searchContext)
            : [];

        return [
            'customer' => [
                'name' => (string) ($user->name ?? ''),
                'email' => (string) ($user->email ?? ''),
            ],
            'summary' => [
                'open_invoice_count' => collect($invoiceSummaries)
                    ->filter(static fn (array $invoice) => (float) ($invoice['remaining_amount'] ?? 0) > 0.01)
                    ->count(),
                'open_invoice_total' => round((float) $openInvoiceTotal, 2),
                'completed_paid_quote_count' => count($completedPaidQuotes),
            ],
            'recent_invoices' => $invoiceSummaries,
            'recent_orders' => $recentOrders->map(function (Order $order) {
                return [
                    'order_number' => $order->order_number,
                    'quote_id' => $order->quote_id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => (float) $order->total_amount,
                    'tracking_info' => $order->tracking_info,
                    'created_at' => optional($order->created_at)?->toIso8601String(),
                ];
            })->all(),
            'completed_paid_quotes' => $completedPaidQuotes,
            'product_suggestions' => $productSuggestions,
            'history_preferences' => $historyPreferences,
            'recent_chat_turns' => $recentChatTurns,
            'focused_invoice' => $focusedInvoice ? [
                'invoice_number' => $focusedInvoice->invoice_number,
                'status' => $focusedInvoice->status,
                'order_number' => $focusedInvoice->order_number,
                'total_amount' => (float) $focusedInvoice->total_amount,
                'paid_amount' => (float) $focusedInvoice->paid_amount,
                'remaining_amount' => max(0, (float) $focusedInvoice->total_amount - (float) $focusedInvoice->paid_amount),
                'due_at' => optional($focusedInvoice->due_at)?->toDateString(),
            ] : null,
        ];
    }

    private function buildAssistantActions(string $question, array $context): array
    {
        $actions = [];
        $questionLower = strtolower($question);
        $focusedInvoice = $context['focused_invoice'] ?? null;

        if ($focusedInvoice && !empty($focusedInvoice['invoice_number'])) {
            $invoiceNumber = (string) $focusedInvoice['invoice_number'];

            $actions[] = [
                'label' => 'View invoice',
                'link' => '/invoices?invoiceNumber=' . urlencode($invoiceNumber),
            ];

            $actions[] = [
                'label' => 'Download invoice PDF',
                'link' => '/api/v1/invoices/' . urlencode($invoiceNumber) . '/pdf',
            ];

            $remaining = (float) ($focusedInvoice['remaining_amount'] ?? 0);
            if ($remaining > 0.01) {
                $actions[] = [
                    'label' => 'Pay this invoice',
                    'link' => '/payment?mode=invoice&invoiceNumber=' . urlencode($invoiceNumber) . '&from=' . urlencode('/messages'),
                ];
            }
        }

        if (Str::contains($questionLower, ['payment', 'pay', 'invoice'])) {
            $actions[] = [
                'label' => 'Open payments',
                'link' => '/payment',
            ];
            $actions[] = [
                'label' => 'See all invoices',
                'link' => '/invoices',
            ];
        }

        if (Str::contains($questionLower, ['quote', 'requote', 'reorder', 'same quote'])) {
            $actions[] = [
                'label' => 'Open quotes',
                'link' => '/quotes',
            ];
            $actions[] = [
                'label' => 'Browse products',
                'link' => '/products',
            ];
        }

        if (Str::contains($questionLower, ['order', 'track', 'shipping'])) {
            $actions[] = [
                'label' => 'Open orders',
                'link' => '/orders',
            ];
        }

        $topProduct = collect($context['product_suggestions'] ?? [])->first();
        if (is_array($topProduct) && !empty($topProduct['product_id'])) {
            $actions[] = [
                'label' => 'View top product',
                'link' => '/products/' . urlencode((string) $topProduct['product_id']),
            ];
        }

        $deduped = collect($actions)
            ->filter(static fn (array $action) => !empty($action['label']) && !empty($action['link']))
            ->unique(static fn (array $action) => $action['label'] . '|' . $action['link'])
            ->values()
            ->all();

        return $deduped;
    }

    private function buildFallbackReply(string $question, array $context): string
    {
        $focusedInvoice = $context['focused_invoice'] ?? null;
        $openCount = (int) ($context['summary']['open_invoice_count'] ?? 0);
        $openTotal = number_format((float) ($context['summary']['open_invoice_total'] ?? 0), 2);
        $completedPaidQuoteCount = (int) ($context['summary']['completed_paid_quote_count'] ?? 0);

        if ($focusedInvoice && !empty($focusedInvoice['invoice_number'])) {
            $remaining = number_format((float) ($focusedInvoice['remaining_amount'] ?? 0), 2);
            return "I found invoice {$focusedInvoice['invoice_number']}. Remaining balance is \${$remaining}. You can view it, download the PDF, or pay from the actions below.";
        }

        if (Str::contains(strtolower($question), ['invoice', 'payment', 'pay'])) {
            return "You currently have {$openCount} invoice(s) with outstanding balance totaling \${$openTotal}. I can help you open invoices, pay them, or download invoice PDFs.";
        }

        $productSuggestions = (array) ($context['product_suggestions'] ?? []);
        if (!empty($productSuggestions)) {
            $top = $productSuggestions[0];
            $price = number_format((float) ($top['price'] ?? 0), 2);
            $name = (string) ($top['name'] ?? 'recommended product');
            return "I found matching products. Top suggestion is {$name} at \${$price}. Review the suggested product cards for why each one was selected and quick actions.";
        }

        if (Str::contains(strtolower($question), ['quote', 'same quote', 'reorder', 'requote'])) {
            return "You have {$completedPaidQuoteCount} quote(s) that are approved and fully paid. Open Quotes to reorder from completed quotes or create a new quote from the same items.";
        }

        return 'I can help with invoices, payment steps, quote follow-ups, and order tracking. Ask me with an invoice number (for example INV-202604-00012) for direct actions.';
    }

    private function extractInvoiceNumber(string $text): ?string
    {
        if (preg_match('/\bINV-[A-Z0-9-]+\b/i', $text, $matches)) {
            return strtoupper($matches[0]);
        }

        return null;
    }

    private function searchProductsForAssistant(string $question, array $historyPreferences = [], int $limit = 6, array $searchContext = []): array
    {
        $keywords = $this->extractProductSearchKeywords($question);
        $deviceType = strtolower((string) ($searchContext['device_type'] ?? ''));
        $maxBudget = isset($searchContext['max_budget']) ? (float) $searchContext['max_budget'] : null;

        if ($deviceType !== '' && !in_array($deviceType, $keywords, true)) {
            $keywords[] = $deviceType;
        }

        $preferenceKeywords = array_values(array_filter(array_map('strtolower', $historyPreferences)));
        if (!empty($preferenceKeywords)) {
            $keywords = array_values(array_unique(array_merge($keywords, $preferenceKeywords)));
        }

        if (empty($keywords)) {
            return [];
        }

        $searchPhrase = implode(' ', array_slice($keywords, 0, 6));

        $products = Product::query()
            ->where('is_available', true)
            ->where(function ($base) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $like = '%' . $keyword . '%';
                    $base->orWhere('product_name', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('vendor_id', 'like', $like)
                        ->orWhere('mfg_part_no', 'like', $like)
                        ->orWhere('tdsynnex_sku_no', 'like', $like);
                }
            })
            ->limit(120)
            ->get([
                'tdsynnex_product_id',
                'tdsynnex_sku_no',
                'vendor_id',
                'product_name',
                'description',
                'base_price',
                'retail_price',
                'images',
                'is_discontinued',
            ]);

        $candidates = $products->map(function (Product $product) {
            return [
                'source' => 'db',
                'product_id' => (string) ($product->tdsynnex_product_id ?: $product->tdsynnex_sku_no),
                'name' => (string) ($product->product_name ?? ''),
                'sku' => (string) ($product->tdsynnex_sku_no ?? ''),
                'vendor' => (string) ($product->vendor_id ?? ''),
                'price' => (float) ($product->base_price ?? $product->retail_price ?? 0),
                'description' => (string) ($product->description ?? ''),
                'image_url' => $this->extractProductImageUrl($product->images),
                'is_discontinued' => (bool) $product->is_discontinued,
            ];
        })->values();

        if ($candidates->count() < max(3, $limit)) {
            try {
                $remote = $this->tdsynnexService->searchPriceAvailabilityCatalog($searchPhrase, 120);
                foreach ($remote as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $productId = (string) ($item['productId'] ?? $item['sku'] ?? '');
                    if ($productId === '') {
                        continue;
                    }

                    if ($candidates->contains(fn (array $row) => (string) ($row['product_id'] ?? '') === $productId)) {
                        continue;
                    }

                    $price = (float) data_get($item, 'productPrice.0.rsPrice', 0);
                    $images = $item['productImages'] ?? $item['images'] ?? [];

                    $candidates->push([
                        'source' => 'tdsynnex',
                        'product_id' => $productId,
                        'name' => (string) ($item['productName'] ?? ''),
                        'sku' => (string) ($item['sku'] ?? $item['mfgPartNo'] ?? ''),
                        'vendor' => (string) ($item['vendorName'] ?? $item['vendorId'] ?? 'TD SYNNEX'),
                        'price' => $price,
                        'description' => (string) ($item['description'] ?? ''),
                        'image_url' => $this->extractProductImageUrl($images),
                        'is_discontinued' => (bool) ($item['discontinueProduct'] ?? false),
                    ]);
                }
            } catch (\Throwable $e) {
                // Keep assistant responsive even if catalog lookup fails.
            }
        }

        return $candidates
            ->map(function (array $candidate) use ($keywords, $preferenceKeywords, $searchContext, $deviceType, $maxBudget) {
                $name = strtolower((string) ($candidate['name'] ?? ''));
                $description = strtolower((string) ($candidate['description'] ?? ''));
                $vendor = strtolower((string) ($candidate['vendor'] ?? ''));
                $sku = strtolower((string) ($candidate['sku'] ?? ''));
                $isAccessory = $this->isAccessoryLikeProduct($candidate);
                $isDeviceMatch = $this->matchesRequestedDevice($candidate, $deviceType);

                $score = 0;
                $matched = [];

                foreach ($keywords as $keyword) {
                    if ($keyword === '') {
                        continue;
                    }

                    if (str_contains($name, $keyword)) {
                        $score += 5;
                        $matched[] = $keyword;
                    }

                    if (str_contains($vendor, $keyword)) {
                        $score += 4;
                        $matched[] = $keyword;
                    }

                    if (str_contains($description, $keyword)) {
                        $score += 2;
                        $matched[] = $keyword;
                    }

                    if (str_contains($sku, $keyword)) {
                        $score += 2;
                        $matched[] = $keyword;
                    }
                }

                $historyMatches = [];
                foreach ($preferenceKeywords as $pref) {
                    if ($pref === '') {
                        continue;
                    }

                    if (str_contains($name, $pref) || str_contains($vendor, $pref) || str_contains($description, $pref)) {
                        $score += 3;
                        $historyMatches[] = $pref;
                    }
                }

                if (!(bool) ($candidate['is_discontinued'] ?? false)) {
                    $score += 2;
                }

                if ($deviceType !== '') {
                    if ($isDeviceMatch) {
                        $score += 6;
                    } else {
                        $score -= 10;
                    }
                }

                if ($isAccessory) {
                    $score -= ($deviceType !== '') ? 12 : 5;
                }

                $price = (float) ($candidate['price'] ?? 0);
                if ($price > 0 && $price < 1200) {
                    $score += 1;
                }

                if ($maxBudget !== null && $maxBudget > 0) {
                    if ($price > 0 && $price <= $maxBudget) {
                        $score += 5;
                    } elseif ($price > $maxBudget) {
                        $score -= 12;
                    }
                }

                $matched = array_values(array_unique(array_filter($matched)));
                $historyMatches = array_values(array_unique(array_filter($historyMatches)));

                $whyText = 'Matched by catalog relevance';
                if (!empty($matched)) {
                    $whyText = 'Matched on: ' . implode(', ', $matched);
                }
                if (!empty($historyMatches)) {
                    $whyText .= '. Aligned with your recent interest in: ' . implode(', ', $historyMatches);
                }

                if ($maxBudget !== null && $maxBudget > 0 && $price > 0) {
                    if ($price <= $maxBudget) {
                        $whyText .= '. Within your budget of $' . number_format($maxBudget, 0);
                    } else {
                        $whyText .= '. Above your budget of $' . number_format($maxBudget, 0);
                    }
                }

                return [
                    'score' => $score,
                    'product_id' => (string) ($candidate['product_id'] ?? ''),
                    'name' => (string) ($candidate['name'] ?? ''),
                    'sku' => (string) ($candidate['sku'] ?? ''),
                    'vendor' => (string) ($candidate['vendor'] ?? ''),
                    'price' => $price,
                    'description' => Str::limit((string) ($candidate['description'] ?? ''), 180),
                    'image_url' => $candidate['image_url'] ?? null,
                    'is_accessory' => $isAccessory,
                    'device_match' => $isDeviceMatch,
                    'why' => $whyText,
                    'actions' => [
                        [
                            'label' => 'View details',
                            'link' => '/products/' . urlencode((string) ($candidate['product_id'] ?? '')),
                        ],
                        [
                            'label' => 'Find similar',
                            'link' => '/products?search=' . urlencode((string) ($candidate['name'] ?? '')),
                        ],
                        [
                            'label' => 'Request quote',
                            'link' => '/cart',
                        ],
                    ],
                ];
            })
            ->filter(static fn (array $item) => !empty($item['product_id']) && !empty($item['name']) && ($item['score'] ?? 0) > 0)
            ->filter(function (array $item) use ($deviceType, $maxBudget) {
                if ($deviceType !== '' && !($item['device_match'] ?? false)) {
                    return false;
                }

                if ($deviceType !== '' && ($item['is_accessory'] ?? false)) {
                    return false;
                }

                $price = (float) ($item['price'] ?? 0);
                if ($maxBudget !== null && $maxBudget > 0 && $price > $maxBudget) {
                    return false;
                }

                return true;
            })
            ->sortByDesc('score')
            ->values()
            ->take($limit)
            ->map(function (array $item) {
                unset($item['score']);
                unset($item['is_accessory']);
                unset($item['device_match']);
                return $item;
            })
            ->all();
    }

    private function extractProductSearchKeywords(string $question): array
    {
        $normalized = strtolower($question);
        $parts = preg_split('/[^a-z0-9]+/i', $normalized) ?: [];
        $stopWords = [
            'need', 'purchase', 'buy', 'best', 'give', 'me', 'sample', 'list', 'for', 'the',
            'and', 'or', 'with', 'show', 'please', 'can', 'you', 'want', 'from', 'that', 'this',
            'have', 'all', 'more', 'details', 'about', 'find', 'search', 'suggestion', 'suggestions',
            'product', 'products', 'item', 'items', 'hi', 'hello', 'hey', 'to', 'today'
        ];

        $keywords = collect($parts)
            ->map(static fn ($p) => trim((string) $p))
            ->filter(static fn ($p) => strlen($p) >= 3)
            ->filter(static fn ($p) => !in_array($p, $stopWords, true))
            ->unique()
            ->values()
            ->all();

        if (str_contains($normalized, 'dell')) {
            array_unshift($keywords, 'dell');
        }

        if (str_contains($normalized, 'laptop')) {
            $keywords[] = 'laptop';
            $keywords[] = 'notebook';
        }

        return array_values(array_unique($keywords));
    }

    private function isProductDiscoveryIntent(string $question, array $recentChatTurns = []): bool
    {
        $q = strtolower(trim($question));
        if ($q === '') {
            return false;
        }

        $greetings = ['hi', 'hello', 'hey', 'yo', 'good morning', 'good afternoon', 'good evening'];
        if (in_array($q, $greetings, true)) {
            return false;
        }

        $productSignals = [
            'laptop', 'notebook', 'desktop', 'monitor', 'printer', 'server', 'sku', 'model', 'spec',
            'recommend', 'suggest', 'sample list', 'best', 'buy', 'purchase', 'quote', 'dell', 'hp',
            'lenovo', 'microsoft', 'surface', 'apple'
        ];

        $financeSignals = [
            'invoice', 'payment', 'pay', 'balance', 'due', 'download', 'pdf', 'billing', 'receipt'
        ];

        $hasCurrentProductSignal = false;
        foreach ($productSignals as $signal) {
            if (str_contains($q, $signal)) {
                $hasCurrentProductSignal = true;
                break;
            }
        }

        if (!$hasCurrentProductSignal && Str::contains($q, $financeSignals)) {
            return false;
        }

        foreach ($productSignals as $signal) {
            if (str_contains($q, $signal)) {
                return true;
            }
        }

        $recentUserText = collect($recentChatTurns)
            ->filter(static fn (array $turn) => strtolower((string) ($turn['role'] ?? '')) === 'user')
            ->pluck('content')
            ->map(static fn ($t) => strtolower((string) $t))
            ->implode(' ');

        $followUpSignals = [
            'which one', 'which is best', 'recommend one', 'top one', 'best one',
            'can you recommend', 'show more', 'similar options', 'other options', 'another option',
            'under', 'below', 'not more than', 'within budget'
        ];

        $isLikelyProductFollowUp = Str::contains($q, $followUpSignals);

        if ($recentUserText !== '' && $isLikelyProductFollowUp) {
            foreach (['laptop', 'notebook', 'recommend', 'sample list', 'buy', 'purchase', 'dell', 'hp', 'lenovo'] as $signal) {
                if (str_contains($recentUserText, $signal)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function extractPreferenceKeywordsFromHistory(array $recentChatTurns): array
    {
        $userTexts = collect($recentChatTurns)
            ->filter(static fn (array $turn) => strtolower((string) ($turn['role'] ?? '')) === 'user')
            ->pluck('content')
            ->map(static fn ($text) => strtolower((string) $text));

        if ($userTexts->isEmpty()) {
            return [];
        }

        $stopWords = [
            'need', 'purchase', 'buy', 'best', 'give', 'me', 'sample', 'list', 'for', 'the',
            'and', 'or', 'with', 'show', 'please', 'can', 'you', 'want', 'from', 'that', 'this',
            'have', 'all', 'more', 'details', 'about', 'find', 'search', 'suggestion', 'suggestions',
            'product', 'products', 'item', 'items', 'invoice', 'payment', 'quote', 'order', 'hi',
            'hello', 'hey', 'to', 'today'
        ];

        $priorityTerms = [
            'dell', 'lenovo', 'hp', 'microsoft', 'surface', 'apple', 'laptop', 'notebook', 'monitor',
            'printer', 'server', 'desktop', 'workstation', 'gaming', 'business'
        ];

        $tokens = $userTexts
            ->flatMap(function ($text) {
                return preg_split('/[^a-z0-9]+/i', (string) $text) ?: [];
            })
            ->map(static fn ($token) => trim((string) $token))
            ->filter(static fn ($token) => strlen($token) >= 3)
            ->filter(static fn ($token) => !in_array($token, $stopWords, true));

        $freq = [];
        foreach ($tokens as $token) {
            $freq[$token] = ($freq[$token] ?? 0) + 1;
        }

        arsort($freq);

        $selected = [];
        foreach ($freq as $token => $count) {
            if ($count >= 2 || in_array($token, $priorityTerms, true)) {
                $selected[] = $token;
            }
            if (count($selected) >= 6) {
                break;
            }
        }

        return $selected;
    }

    private function buildProductSearchContext(string $question, array $recentChatTurns = []): array
    {
        $texts = collect($recentChatTurns)
            ->filter(static fn (array $turn) => strtolower((string) ($turn['role'] ?? '')) === 'user')
            ->pluck('content')
            ->map(static fn ($text) => (string) $text)
            ->all();

        $texts[] = $question;
        $joined = strtolower(implode(' ', array_filter($texts)));

        $deviceType = null;
        if (str_contains($joined, 'laptop') || str_contains($joined, 'notebook')) {
            $deviceType = 'laptop';
        } elseif (str_contains($joined, 'desktop') || str_contains($joined, 'workstation')) {
            $deviceType = 'desktop';
        }

        $maxBudget = null;
        $patterns = [
            '/(?:under|below|less than|not more than|up to|within)\s*\$?\s*(\d{2,6})/i',
            '/\$\s*(\d{2,6})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $joined, $matches) && !empty($matches[1])) {
                $values = array_map('floatval', $matches[1]);
                $values = array_values(array_filter($values, static fn (float $v) => $v >= 50));
                if (!empty($values)) {
                    $maxBudget = min($values);
                    break;
                }
            }
        }

        return [
            'device_type' => $deviceType,
            'max_budget' => $maxBudget,
        ];
    }

    private function isAccessoryLikeProduct(array $candidate): bool
    {
        $haystack = strtolower(trim(
            (string) ($candidate['name'] ?? '') . ' ' .
            (string) ($candidate['description'] ?? '') . ' ' .
            (string) ($candidate['sku'] ?? '')
        ));

        if ($haystack === '') {
            return false;
        }

        $accessoryTerms = [
            'case', 'cover', 'sleeve', 'bag', 'dock', 'docking', 'adapter', 'cable', 'charger',
            'keyboard', 'mouse', 'headset', 'speaker', 'stand', 'screen protector', 'protector',
            'hub', 'backpack', 'folio', 'power bank', 'battery', 'warranty', 'service plan', 'kit'
        ];

        foreach ($accessoryTerms as $term) {
            if (str_contains($haystack, $term)) {
                return true;
            }
        }

        return false;
    }

    private function matchesRequestedDevice(array $candidate, string $deviceType): bool
    {
        if ($deviceType === '') {
            return true;
        }

        $haystack = strtolower(trim(
            (string) ($candidate['name'] ?? '') . ' ' .
            (string) ($candidate['description'] ?? '')
        ));

        if ($haystack === '') {
            return false;
        }

        if ($deviceType === 'laptop') {
            return str_contains($haystack, 'laptop') || str_contains($haystack, 'notebook');
        }

        if ($deviceType === 'desktop') {
            return str_contains($haystack, 'desktop') || str_contains($haystack, 'workstation');
        }

        return true;
    }

    private function extractProductImageUrl(mixed $images): ?string
    {
        if (!is_array($images)) {
            return null;
        }

        foreach ($images as $image) {
            if (is_string($image) && trim($image) !== '') {
                $url = trim($image);
                if ($this->isValidImageUrl($url)) {
                    return $url;
                }
            }

            if (is_array($image)) {
                $url = trim((string) ($image['imageUrl'] ?? $image['url'] ?? ''));
                if ($this->isValidImageUrl($url)) {
                    return $url;
                }
            }
        }

        return null;
    }

    private function isValidImageUrl(string $url): bool
    {
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return (bool) preg_match('/\.(?:jpg|jpeg|png|webp|gif|avif)(?:\?.*)?$/i', $url);
    }

    private function resolveOrCreateChatSession(int $userId, mixed $chatSessionId): ChatSession
    {
        if (!empty($chatSessionId)) {
            $existing = ChatSession::where('user_id', $userId)
                ->where('id', (int) $chatSessionId)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        return ChatSession::create([
            'user_id' => $userId,
            'title' => 'New chat',
            'last_message_at' => null,
        ]);
    }

    private function syncSessionTitle(ChatSession $session, string $firstUserMessage): void
    {
        if ($session->title !== 'New chat') {
            return;
        }

        $generatedTitle = trim(Str::limit($firstUserMessage, 70, ''));
        if ($generatedTitle === '') {
            return;
        }

        $session->forceFill([
            'title' => $generatedTitle,
        ])->save();
    }

    // -------------------------------------------------------------------------
    // Admin chat methods — admins can see & reply to all escalated sessions
    // -------------------------------------------------------------------------

    public function adminGetEscalatedChats(Request $request): JsonResponse
    {
        $this->requireAdminRole($request);

        $resolved = filter_var($request->query('resolved', false), FILTER_VALIDATE_BOOLEAN);
        $limit = max(1, min(100, (int) $request->query('limit', 60)));

        $query = ChatSession::with([
            'messages' => fn ($q) => $q->latest('id')->limit(1),
            'user:id,name,email',
        ])
            ->where(function ($q) {
                $q->where('escalated_to_human', true)
                    ->orWhereNotNull('resolved_at')
                    ->orWhereHas('messages', function ($messageQuery) {
                        $messageQuery->where('role', 'admin');
                    });
            });

        if ($resolved) {
            // History tab: include resolved sessions and any thread where an admin has replied.
            $query->where(function ($q) {
                $q->whereNotNull('resolved_at')
                    ->orWhereHas('messages', function ($messageQuery) {
                        $messageQuery->where('role', 'admin');
                    });
            });
        } else {
            // Open tab: only currently escalated and unresolved sessions.
            $query->where(function ($q) {
                $q->whereNull('resolved_at')->orWhere('resolved_at', '');
            })->where('escalated_to_human', true);
        }

        $sessions = $query
            ->orderByRaw('COALESCE(last_message_at, updated_at, escalated_at) DESC')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions->map(function (ChatSession $session) {
                $last = $session->messages->first();
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'user' => $session->user ? [
                        'id' => $session->user->id,
                        'name' => $session->user->name,
                        'email' => $session->user->email,
                    ] : null,
                    'escalated_at' => $session->escalated_at,
                    'resolved_at' => $session->resolved_at ?? null,
                    'updated_at' => $session->updated_at,
                    'last_message_preview' => $last ? Str::limit((string) $last->content, 80) : null,
                    'last_message_role' => $last?->role,
                    'last_message_at' => $session->last_message_at,
                ];
            })->values(),
        ]);
    }

    public function adminGetEscalatedCount(Request $request): JsonResponse
    {
        $this->requireAdminRole($request);

        $count = ChatSession::where('escalated_to_human', true)
            ->where(function ($q) {
                $q->whereNull('resolved_at')->orWhere('resolved_at', '');
            })
            ->count();

        return response()->json(['success' => true, 'count' => $count]);
    }

    public function adminGetChatSession(Request $request, int $chatSessionId): JsonResponse
    {
        $this->requireAdminRole($request);

        $session = ChatSession::with('user:id,name,email')
            ->findOrFail($chatSessionId);

        $messages = ChatMessage::where('chat_session_id', $session->id)
            ->orderBy('id')
            ->get()
            ->map(fn (ChatMessage $msg) => [
                'id' => $msg->id,
                'role' => $msg->role,
                'text' => $msg->content,
                'actions' => $msg->actions ?? [],
                'created_at' => $msg->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'escalated_to_human' => (bool) $session->escalated_to_human,
                    'escalated_at' => $session->escalated_at,
                    'resolved_at' => $session->resolved_at ?? null,
                    'updated_at' => $session->updated_at,
                    'user' => $session->user ? [
                        'id' => $session->user->id,
                        'name' => $session->user->name,
                        'email' => $session->user->email,
                    ] : null,
                ],
                'messages' => $messages,
            ],
        ]);
    }

    public function adminReplyToChat(Request $request, int $chatSessionId): JsonResponse
    {
        $this->requireAdminRole($request);

        $validated = $request->validate([
            'message' => 'required|string|max:4000',
        ]);

        $session = ChatSession::findOrFail($chatSessionId);

        $admin = $request->user();
        $messageText = trim((string) $validated['message']);

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'user_id' => $admin->id,
            'role' => 'admin',
            'content' => $messageText,
            'actions' => [],
            'metadata' => [
                'source' => 'admin_reply',
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
            ],
        ]);

        $session->forceFill(['last_message_at' => now()])->save();

        // Notify the customer so they see the admin reply in their chat.
        Message::createMessage(
            (int) $session->user_id,
            'system',
            'Support replied to your chat',
            Str::limit($messageText, 120),
            'CHAT-' . $session->id,
            'normal',
            ['chat_session_id' => $session->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Reply sent',
        ]);
    }

    public function adminResolveChat(Request $request, int $chatSessionId): JsonResponse
    {
        $this->requireAdminRole($request);

        $session = ChatSession::findOrFail($chatSessionId);

        $session->forceFill([
            'resolved_at' => now(),
        ])->save();

        // Leave a system message in the thread so the customer sees it.
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'user_id' => $request->user()->id,
            'role' => 'admin',
            'content' => 'This support conversation has been marked as resolved by our team. Feel free to start a new chat if you need further assistance.',
            'actions' => [],
            'metadata' => [
                'source' => 'admin_resolved',
                'admin_id' => $request->user()->id,
                'admin_name' => $request->user()->name,
            ],
        ]);

        $session->forceFill(['last_message_at' => now()])->save();

        return response()->json(['success' => true, 'message' => 'Chat resolved']);
    }

    private function requireAdminRole(Request $request): void
    {
        $user = $request->user();
        if (!$user || !in_array($user->role ?? '', ['admin', 'owner', 'manager'], true)) {
            abort(403, 'Admin access required');
        }
    }
}
