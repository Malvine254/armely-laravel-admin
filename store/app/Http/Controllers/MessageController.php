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
use App\Models\AppSetting;
use App\Services\AzureOpenAiChatService;
use App\Services\NotificationService;
use App\Services\TDSynnexService;
use App\Support\ChatIntentSignals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
        $lastMessageContentSubquery = ChatMessage::query()
            ->select('content')
            ->whereColumn('chat_session_id', 'chat_sessions.id')
            ->latest('id')
            ->limit(1);

        $lastMessageRoleSubquery = ChatMessage::query()
            ->select('role')
            ->whereColumn('chat_session_id', 'chat_sessions.id')
            ->latest('id')
            ->limit(1);

        $sessions = ChatSession::where('user_id', $request->user()->id)
            ->select(['id', 'title', 'updated_at', 'last_message_at', 'escalated_to_human', 'escalated_at', 'resolved_at'])
            ->selectSub($lastMessageContentSubquery, 'last_message_content')
            ->selectSub($lastMessageRoleSubquery, 'last_message_role')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->limit(40)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions->map(function (ChatSession $session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'updated_at' => $session->updated_at,
                    'last_message_at' => $session->last_message_at,
                    'last_message_preview' => $session->last_message_content
                        ? Str::limit((string) $session->last_message_content, 80)
                        : null,
                    'last_message_role' => $session->last_message_role,
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

    public function deleteChatSession(Request $request, int $chatSessionId): JsonResponse
    {
        $session = ChatSession::where('user_id', $request->user()->id)
            ->where('id', $chatSessionId)
            ->firstOrFail();

        $session->delete();

        return response()->json([
            'success' => true,
            'deleted_count' => 1,
            'deleted_ids' => [$chatSessionId],
        ]);
    }

    public function bulkDeleteChatSessions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chat_session_ids' => 'nullable|array',
            'chat_session_ids.*' => 'integer',
            'clear_all' => 'nullable|boolean',
        ]);

        $clearAll = (bool) ($validated['clear_all'] ?? false);
        $ids = collect((array) ($validated['chat_session_ids'] ?? []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $query = ChatSession::query()->where('user_id', $request->user()->id);

        if (!$clearAll) {
            if ($ids->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No chat sessions selected for deletion.',
                ], 422);
            }

            $query->whereIn('id', $ids->all());
        }

        $deletedIds = $query->pluck('id')->map(fn ($id) => (int) $id)->values();

        if ($deletedIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'deleted_count' => 0,
                'deleted_ids' => [],
            ]);
        }

        ChatSession::whereIn('id', $deletedIds->all())->delete();

        return response()->json([
            'success' => true,
            'deleted_count' => $deletedIds->count(),
            'deleted_ids' => $deletedIds->all(),
        ]);
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

            $this->notifyEscalationAdmins(
                $session,
                $request->user(),
                $note,
                $isResolved,
                $isResolved ? 'manual_escalation_reopen' : 'manual_escalation'
            );
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

        $user = $request->user();
        $session = null;
        $question = trim((string) $validated['message']);

        try {
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

        if ((bool) $session->escalated_to_human && empty($session->resolved_at)) {
            $session->forceFill([
                'last_message_at' => now(),
            ])->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => null,
                    'actions' => [],
                    'product_suggestions' => [],
                    'source' => 'human_handoff_waiting',
                    'wait_for_human' => true,
                    'chat_session' => [
                        'id' => $session->id,
                        'title' => $session->title,
                    ],
                ],
            ]);
        }

        if (!empty($session->resolved_at)) {
            if ($wantsEscalation) {
                $session->forceFill([
                    'escalated_to_human' => true,
                    'escalated_at' => now(),
                    'resolved_at' => null,
                ])->save();

                $handoffReply = 'Absolutely — I\'ve reopened this ticket and escalated it to our support team. A team member will pick up right where we left off. You\'re in good hands! 🙌';

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

                $this->notifyEscalationAdmins(
                    $session,
                    $user,
                    null,
                    true,
                    'assistant_reopen_escalation_intent'
                );

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

            // Resolved chats may use AI unless the user asks to escalate again.
        }

        if (!(bool) $session->escalated_to_human && $wantsEscalation) {
            $session->forceFill([
                'escalated_to_human' => true,
                'escalated_at' => now(),
                'resolved_at' => null,
            ])->save();

            $handoffReply = 'Of course! I\'ve connected you with our support team. A real person will jump in shortly to help you out. In the meantime, feel free to share any details that might help them assist you faster. 🤝';

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

            $this->notifyEscalationAdmins(
                $session,
                $user,
                null,
                false,
                'assistant_escalation_intent'
            );

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

        $context = $this->buildAssistantContext($user, $question, $session->id);
        $queryAuditHandled = $this->handleCatalogQueryAudit($question, $context);

        if ($queryAuditHandled !== null) {
            ChatMessage::create([
                'chat_session_id' => $session->id,
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $queryAuditHandled['reply'],
                'actions' => $queryAuditHandled['actions'],
                'metadata' => [
                    'source' => 'catalog_query_audit',
                    'catalog_search_query' => $context['catalog_search_query'] ?? null,
                    'product_suggestions' => [],
                ],
            ]);

            $session->forceFill(['last_message_at' => now()])->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $queryAuditHandled['reply'],
                    'actions' => $queryAuditHandled['actions'],
                    'product_suggestions' => [],
                    'source' => 'catalog_query_audit',
                    'chat_session' => ['id' => $session->id, 'title' => $session->title],
                ],
            ]);
        }
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

        // Multi-agent orchestration: classify intent, route to specialist agent.
        $chatHistory  = $context['recent_chat_turns'] ?? [];
        $smartIntent  = $this->resolveSmartIntent($question, $context, $chatHistory);
        if ($smartIntent !== null) {
            $context['smart_intent'] = $smartIntent;
        }
        // Product conversations are resolved once, from the database-backed context. Sending
        // them through a second classifier/product agent caused the current turn and history to
        // diverge. Azure remains responsible for account/support conversations only.
        $localProductHandled = $this->handleLocalProductDiscoveryReply($question, $context);
        if ($localProductHandled !== null) {
            $agentResult = [
                'reply'               => $localProductHandled['reply'],
                'actions'             => $localProductHandled['actions'],
                'product_suggestions' => (array) ($localProductHandled['product_suggestions'] ?? []),
                'source'              => $localProductHandled['source'],
                'intent'              => 'product_search',
            ];
        } else {
            $agentResult = $this->assistantService->orchestrate($question, $context, $chatHistory);

            if ($agentResult['source'] === 'unconfigured') {
                $agentResult = [
                    'reply'               => $this->buildFallbackReply($question, $context),
                    'actions'             => $this->buildAssistantActions($question, $context),
                    'product_suggestions' => (array) ($context['product_suggestions'] ?? []),
                    'source'              => 'local_fallback',
                    'intent'              => 'general_support',
                ];
            }
        }

        $assistantReply     = (string) ($agentResult['reply'] ?? '');
        $actions            = (array) ($agentResult['actions'] ?? []);
        $productSuggestions = (array) ($agentResult['product_suggestions'] ?? []);
        $source             = (string) ($agentResult['source'] ?? 'azure_openai');
        $intent             = (string) ($agentResult['intent'] ?? 'general_support');

        if (!$this->shouldIncludeAssistantActions($question, $intent, $context, $productSuggestions)) {
            $actions = [];
        }

        $degraded           = in_array($source, ['local_fallback', 'assistant_error_fallback'], true);

        ChatMessage::create([
            'chat_session_id' => $session->id,
            'user_id' => $user->id,
            'role' => 'assistant',
            'content' => $assistantReply,
            'actions' => $actions,
            'metadata' => [
                'source'              => $source,
                'intent'              => $intent,
                'degraded'            => $degraded,
                'catalog_search_query' => $context['catalog_search_query'] ?? null,
                'product_suggestions' => $productSuggestions,
            ],
        ]);

        $session->forceFill([
            'last_message_at' => now(),
        ])->save();

        return response()->json([
            'success' => true,
            'data' => [
                'reply'               => $assistantReply,
                'actions'             => $actions,
                'product_suggestions' => $productSuggestions,
                'source'              => $source,
                'status'              => $degraded ? 'degraded' : 'ok',
                'degraded'            => $degraded,
                'chat_session' => [
                    'id'    => $session->id,
                    'title' => $session->title,
                ],
            ],
        ]);
        } catch (\Throwable $e) {
            Log::error('Mela AI assistantChat failed', [
                'user_id' => $request->user()?->id,
                'chat_session_id' => $validated['chat_session_id'] ?? null,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            $fallbackReply = 'Oops — Mela AI hit a temporary snag. No worries though! Please try again in a moment, or I can connect you with our support team right away.';
            $fallbackActions = [
                [
                    'label' => 'Request human support',
                    'link' => '/messages',
                ],
            ];
            $fallbackProductSuggestions = [];

            try {
                $allowProductFallback = ChatIntentSignals::isProductLookupIntent($question, []);
                if ($allowProductFallback) {
                    $fallbackSearchContext = $this->buildProductSearchContext($question, []);
                    $fallbackProductSuggestions = $this->searchProductsForAssistant($question, [], 6, $fallbackSearchContext);
                }

                if (!empty($fallbackProductSuggestions)) {
                    $count = count($fallbackProductSuggestions);
                    $fallbackReply = "Mela AI is temporarily unavailable, but I still managed to find {$count} matching product(s) from your catalog! Check them out below, or reach out to our team for more help.";
                    $fallbackActions[] = [
                        'label' => 'Open product search',
                        'link' => '/products?q=' . urlencode(ChatIntentSignals::extractCatalogSearchPhrase($question)),
                    ];
                }
            } catch (\Throwable $fallbackError) {
                Log::warning('Assistant error fallback product search failed', [
                    'user_id' => $request->user()?->id,
                    'chat_session_id' => $validated['chat_session_id'] ?? null,
                    'message' => $fallbackError->getMessage(),
                ]);
            }

            $fallbackActions = collect($fallbackActions)
                ->filter(static fn (array $action) => !empty($action['label']) && !empty($action['link']))
                ->unique(static fn (array $action) => $action['label'] . '|' . $action['link'])
                ->values()
                ->all();

            if ($session && $user) {
                try {
                    ChatMessage::create([
                        'chat_session_id' => $session->id,
                        'user_id' => $user->id,
                        'role' => 'assistant',
                        'content' => $fallbackReply,
                        'actions' => $fallbackActions,
                        'metadata' => [
                            'source' => 'assistant_error_fallback',
                            'degraded' => true,
                            'product_suggestions' => $fallbackProductSuggestions,
                        ],
                    ]);

                    $session->forceFill([
                        'last_message_at' => now(),
                    ])->save();
                } catch (\Throwable $inner) {
                    Log::error('Failed to persist Mela AI fallback message', [
                        'user_id' => $user->id,
                        'chat_session_id' => $session->id,
                        'message' => $inner->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $fallbackReply,
                    'actions' => $fallbackActions,
                    'product_suggestions' => $fallbackProductSuggestions,
                    'source' => 'assistant_error_fallback',
                    'status' => 'degraded',
                    'degraded' => true,
                    'chat_session' => [
                        'id' => $session?->id ?? ($validated['chat_session_id'] ?? null),
                        'title' => $session?->title ?? 'New chat',
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
        $metadataAction = collect((array) data_get($message->metadata, 'actions', []))
            ->first(fn ($action) => !empty($action['label']) && !empty($action['link']));

        if (is_array($metadataAction)) {
            return [
                'link' => (string) $metadataAction['link'],
                'label' => (string) $metadataAction['label'],
            ];
        }

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
        $balanceText = $this->formatAssistantMoney($balanceDue);

        return [
            'reply' => "I sent an invoice reminder for {$invoiceNumber} to your email: {$recipientEmail}. Balance due is {$balanceText}.{$dueText}",
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

        // Require explicit handoff phrasing to avoid accidental escalations.
        return Str::contains($normalized, [
            'please escalate',
            'escalate this',
            'escalate to human',
            'connect me to human support',
            'connect me to a human',
            'transfer me to human support',
            'talk to a human',
            'speak to a human',
            'i want human support',
            'i need human support',
            'live agent',
            'real person',
            'handoff to human',
        ]);
    }

    private function buildAssistantContext($user, string $question, ?int $chatSessionId = null): array
    {
        $hasInvoicesTable = Schema::hasTable('invoices');
        $hasOrdersTable = Schema::hasTable('orders');
        $hasQuotesTable = Schema::hasTable('quotes');
        $hasChatMessagesTable = Schema::hasTable('chat_messages');

        $recentInvoices = $hasInvoicesTable
            ? Invoice::where('user_id', $user->id)
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->limit(8)
                ->get(['invoice_number', 'status', 'total_amount', 'paid_amount', 'due_at', 'order_number', 'items'])
            : collect();

        $recentOrders = $hasOrdersTable
            ? Order::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(['order_number', 'quote_id', 'status', 'payment_status', 'total_amount', 'items', 'tracking_info', 'created_at'])
            : collect();

        $completedPaidQuotes = $hasQuotesTable
            ? Quote::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved', 'completed', 'submitted'])
                ->with('order:id,quote_id,order_number,payment_status,status')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(['quote_id', 'status', 'total_amount', 'created_at'])
                ->map(function (Quote $quote) {
                    return [
                        'quote_id'       => $quote->quote_id,
                        'status'         => $quote->status,
                        'total_amount'   => (float) $quote->total_amount,
                        'order_number'   => optional($quote->order)->order_number,
                        'payment_status' => optional($quote->order)->payment_status,
                        'order_status'   => optional($quote->order)->status,
                        'created_at'     => optional($quote->created_at)?->toIso8601String(),
                    ];
                })
                ->all()
            : [];

        $invoiceNumber = $this->extractInvoiceNumber($question);
        $focusedInvoice = null;
        if ($hasInvoicesTable && $invoiceNumber !== null) {
            $focusedInvoice = Invoice::where('user_id', $user->id)
                ->where('invoice_number', $invoiceNumber)
                ->first(['invoice_number', 'status', 'total_amount', 'paid_amount', 'due_at', 'order_number', 'items']);
        }

        $invoiceSummaries = $recentInvoices->map(function (Invoice $invoice) {
            $remaining = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
            $rawItems = is_array($invoice->items) ? $invoice->items : [];
            $itemLines = collect($rawItems)->map(function ($it) {
                if (!is_array($it)) return null;
                $name = (string) ($it['product_name'] ?? $it['name'] ?? $it['description'] ?? '');
                $qty  = (int) ($it['quantity'] ?? $it['qty'] ?? 1);
                $unit = (float) ($it['unit_price'] ?? $it['price'] ?? 0);
                if ($name === '') return null;
                $priceStr = $unit > 0 ? ' x $' . number_format($unit, 2) : '';
                return "{$name} (qty: {$qty}{$priceStr})";
            })->filter()->values()->all();
            return [
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'order_number' => $invoice->order_number,
                'total_amount' => (float) $invoice->total_amount,
                'paid_amount' => (float) $invoice->paid_amount,
                'remaining_amount' => $remaining,
                'due_at' => optional($invoice->due_at)?->toDateString(),
                'items' => $itemLines,
            ];
        })->all();

        $openInvoiceTotal = collect($invoiceSummaries)
            ->filter(static fn (array $invoice) => (float) ($invoice['remaining_amount'] ?? 0) > 0.01)
            ->sum('remaining_amount');

        $recentChatTurns = [];
        if ($hasChatMessagesTable && $chatSessionId) {
            $recentChatTurns = ChatMessage::where('chat_session_id', $chatSessionId)
                ->orderByDesc('id')
                ->limit(10)
                ->get(['role', 'content', 'metadata'])
                ->reverse()
                ->values()
                ->map(fn (ChatMessage $item) => [
                    'role' => $item->role,
                    'content' => $item->content,
                    'intent' => (string) data_get($item->metadata, 'intent', ''),
                    'source' => (string) data_get($item->metadata, 'source', ''),
                    'catalog_search_query' => (string) data_get($item->metadata, 'catalog_search_query', ''),
                    'product_suggestions' => array_values(array_filter((array) data_get($item->metadata, 'product_suggestions', []))),
                    'has_product_suggestions' => !empty((array) data_get($item->metadata, 'product_suggestions', [])),
                ])
                ->all();
        }

        // The resolved query already contains any intentional refinement. Feeding all earlier
        // nouns back into ranking made new topics inherit stale brands and device types.
        $historyPreferences    = [];
        $isAccountQuestion = ChatIntentSignals::isQuoteIntentQuery($question)
            || ChatIntentSignals::isInvoiceIntentQuery($question)
            || ChatIntentSignals::isOrderIntentQuery($question);
        $isGeneralConversation = ChatIntentSignals::isGeneralConversationQuery($question);
        // The model may refine a catalog request, but it must never create product intent.
        // A deterministic gate prevents unrelated prose from becoming a search query.
        $hasLocalProductIntent = !$isAccountQuestion
            && !$isGeneralConversation
            && ChatIntentSignals::isProductLookupIntent($question, $recentChatTurns);
        $productSearchPlan = !$hasLocalProductIntent
            ? null
            : $this->assistantService->planProductSearch($question, $recentChatTurns);
        $catalogSearchQuery = !$hasLocalProductIntent
            ? ''
            : trim((string) ($productSearchPlan['query'] ?? ''));
        if ($hasLocalProductIntent && $catalogSearchQuery === '') {
            $catalogSearchQuery = $this->resolveConversationalCatalogSearchQuery($question, $recentChatTurns);
        }
        $catalogSearchQueries  = ChatIntentSignals::extractCatalogSearchPhrases($catalogSearchQuery);
        $excludedProductTerms = ChatIntentSignals::extractExcludedProductTerms($question);
        $budgetPriority = (bool) preg_match('/\b(budget(?: friendly)?|affordable|low cost|lower cost|economical|inexpensive|cheapest|value)\b/i', $question);
        $shouldSuggestProducts = $hasLocalProductIntent
            && $this->isProductDiscoveryIntent($catalogSearchQuery, $recentChatTurns);
        // A conversational follow-up can intentionally contain no new catalog terms. Still run
        // it through product search so buildProductSearchContext() can reuse the prior cards.
        $productSearchInputs = $catalogSearchQueries;
        if ($hasLocalProductIntent && empty($productSearchInputs)) {
            $productSearchInputs = [$question];
            $shouldSuggestProducts = true;
        }

        $productSuggestions = collect($productSearchInputs)
            ->flatMap(function (string $searchQuery) use ($historyPreferences, $catalogSearchQueries, $recentChatTurns, $productSearchPlan, $excludedProductTerms, $budgetPriority) {
                // Each independent clause gets its own constraints; otherwise "monitors and
                // printers" would incorrectly require one product to be both categories.
                // Chat history is required for follow-ups such as "which one do you recommend?"
                // so the search can reuse the exact products shown in the preceding answer.
                $searchContext = $this->buildProductSearchContext($searchQuery, $recentChatTurns);
                $plannedProductType = trim((string) ($productSearchPlan['product_type'] ?? ''));
                if ($plannedProductType !== '') {
                    $searchContext['device_type'] = strtolower($plannedProductType);
                    $searchContext['required_category'] = strtolower($plannedProductType);
                }
                $searchContext['excluded_terms'] = $excludedProductTerms;
                $searchContext['budget_priority'] = $budgetPriority || (bool) ($searchContext['budget_priority'] ?? false);
                $perQueryLimit = count($catalogSearchQueries) > 1
                    ? max(2, (int) ceil(6 / count($catalogSearchQueries)))
                    : 6;

                return $this->searchProductsForAssistant($searchQuery, $historyPreferences, $perQueryLimit, $searchContext);
            })
            ->unique(static fn (array $item) => (string) ($item['product_id'] ?? ''))
            ->take(6)
            ->values()
            ->all();

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
                $items = collect(is_array($order->items) ? $order->items : [])
                    ->map(function ($item) {
                        if (!is_array($item)) {
                            return null;
                        }

                        $name = trim((string) ($item['product_name'] ?? $item['name'] ?? $item['description'] ?? ''));
                        if ($name === '') {
                            return null;
                        }

                        $quantity = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));
                        $unitPrice = (float) ($item['unit_price'] ?? $item['unitPrice'] ?? $item['price'] ?? $item['customer_price'] ?? 0);
                        $lineTotal = (float) ($item['line_total'] ?? $item['lineTotal'] ?? 0);
                        if ($unitPrice <= 0 && $lineTotal > 0) {
                            $unitPrice = $lineTotal / $quantity;
                        }

                        return [
                            'name' => $name,
                            'quantity' => $quantity,
                            'unit_price' => round($unitPrice, 2),
                            'line_total' => round($lineTotal > 0 ? $lineTotal : $unitPrice * $quantity, 2),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'order_number' => $order->order_number,
                    'quote_id' => $order->quote_id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => (float) $order->total_amount,
                    'items' => $items,
                    'tracking_info' => $order->tracking_info,
                    'created_at' => optional($order->created_at)?->toIso8601String(),
                ];
            })->all(),
            'completed_paid_quotes' => $completedPaidQuotes,
            'product_suggestions' => $productSuggestions,
            'product_intent' => $shouldSuggestProducts,
            'catalog_search_query' => $catalogSearchQuery,
            'catalog_search_queries' => $catalogSearchQueries,
            'product_search_plan' => $productSearchPlan,
            'history_preferences' => $historyPreferences,
            'recent_chat_turns' => $recentChatTurns,
            'focused_invoice' => $focusedInvoice ? (function () use ($focusedInvoice) {
                $rawItems = is_array($focusedInvoice->items) ? $focusedInvoice->items : [];
                $itemLines = collect($rawItems)->map(function ($it) {
                    if (!is_array($it)) return null;
                    $name = (string) ($it['product_name'] ?? $it['name'] ?? $it['description'] ?? '');
                    $qty  = (int) ($it['quantity'] ?? $it['qty'] ?? 1);
                    $unit = (float) ($it['unit_price'] ?? $it['price'] ?? 0);
                    if ($name === '') return null;
                    $priceStr = $unit > 0 ? ' x $' . number_format($unit, 2) : '';
                    return "{$name} (qty: {$qty}{$priceStr})";
                })->filter()->values()->all();
                return [
                    'invoice_number' => $focusedInvoice->invoice_number,
                    'status' => $focusedInvoice->status,
                    'order_number' => $focusedInvoice->order_number,
                    'total_amount' => (float) $focusedInvoice->total_amount,
                    'paid_amount' => (float) $focusedInvoice->paid_amount,
                    'remaining_amount' => max(0, (float) $focusedInvoice->total_amount - (float) $focusedInvoice->paid_amount),
                    'due_at' => optional($focusedInvoice->due_at)?->toDateString(),
                    'items' => $itemLines,
                ];
            })() : null,
        ];
    }

    private function buildAssistantActions(string $question, array $context): array
    {
        $actions = [];
        $questionLower = strtolower($question);
        $focusedInvoice = $context['focused_invoice'] ?? null;
        $isProductIntent = (bool) ($context['product_intent'] ?? false);

        if ($isProductIntent) {
            $actions[] = [
                'label' => 'Open product search',
                'link' => '/products?q=' . urlencode(trim((string) ($context['catalog_search_query'] ?? $question))),
            ];
            $actions[] = [
                'label' => 'Browse products',
                'link' => '/products',
            ];
        }

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

    private function shouldIncludeAssistantActions(string $question, string $intent, array $context, array $productSuggestions = []): bool
    {
        if ($intent === 'product_search' || !empty($productSuggestions)) {
            return true;
        }

        if (ChatIntentSignals::isDueAmountQuestion($question)) {
            return false;
        }

        $normalized = ChatIntentSignals::normalizeQuestion($question);
        $hasExplicitActionRequest = (bool) preg_match('/\b(open|view|show|go to|take me|navigate|browse|download|pay|track|manage|list|see all)\b/u', $normalized);
        if ($hasExplicitActionRequest) {
            return true;
        }

        $focusedInvoice = $context['focused_invoice'] ?? null;
        if ($focusedInvoice && ChatIntentSignals::isInvoiceIntentQuery($question)) {
            return true;
        }

        return false;
    }

    private function resolveSmartIntent(string $question, array $context, array $chatHistory): ?string
    {
        $q = strtolower(trim($question));
        if ($q === '') {
            return null;
        }

        $recentChatTurns = (array) ($context['recent_chat_turns'] ?? $chatHistory);
        $intent = ChatIntentSignals::classifyAssistantIntent($question, $recentChatTurns);
        if ($intent !== 'general_support') {
            return $intent;
        }

        $accountIntentCount = collect([
            ChatIntentSignals::isQuoteIntentQuery($question),
            ChatIntentSignals::isOrderIntentQuery($question),
            ChatIntentSignals::isInvoiceIntentQuery($question),
        ])->filter()->count();

        if (ChatIntentSignals::isGeneralConversationQuery($question) || ChatIntentSignals::isSmallTalkQuery($question)) {
            return 'general_support';
        }

        // Explicit mixed account questions should stay broad (support summary)
        // rather than being collapsed to the first detected follow-up topic.
        if ($accountIntentCount > 1) {
            return 'general_support';
        }

        $followUpTopic = $this->inferFollowUpTopic($q, $recentChatTurns);
        if ($followUpTopic !== null) {
            return match ($followUpTopic) {
                'quote' => 'quote_management',
                'order' => 'order_status',
                'invoice' => 'invoice_payment',
                default => null,
            };
        }

        return 'general_support';
    }

    private function isGeneralConversationQuery(string $questionLower): bool
    {
        return ChatIntentSignals::isGeneralConversationQuery($questionLower);
    }

    private function isQuoteIntentQuery(string $questionLower): bool
    {
        return ChatIntentSignals::isQuoteIntentQuery($questionLower);
    }

    private function isOrderIntentQuery(string $questionLower): bool
    {
        return ChatIntentSignals::isOrderIntentQuery($questionLower);
    }

    private function isInvoiceIntentQuery(string $questionLower): bool
    {
        return ChatIntentSignals::isInvoiceIntentQuery($questionLower);
    }

    private function isProductLookupIntent(string $question, array $recentChatTurns = []): bool
    {
        return ChatIntentSignals::isProductLookupIntent($question, $recentChatTurns);
    }

    private function buildFallbackReply(string $question, array $context): string
    {
        $customerName = trim((string) ($context['customer']['name'] ?? ''));
        $firstName = $customerName !== '' ? explode(' ', $customerName)[0] : '';
        $nameSuffix = $firstName !== '' ? ", {$firstName}" : '';

        $focusedInvoice = $context['focused_invoice'] ?? null;
        $orders = (array) ($context['recent_orders'] ?? []);
        $quotes = (array) ($context['completed_paid_quotes'] ?? []);
        $invoices = (array) ($context['recent_invoices'] ?? []);
        $openCount = (int) ($context['summary']['open_invoice_count'] ?? 0);
        $openTotal = $this->formatAssistantMoney((float) ($context['summary']['open_invoice_total'] ?? 0));
        $completedPaidQuoteCount = (int) ($context['summary']['completed_paid_quote_count'] ?? 0);
        $isProductIntent = (bool) ($context['product_intent'] ?? false);
        $questionLower = strtolower(trim($question));
        $recentChatTurns = (array) ($context['recent_chat_turns'] ?? []);
        $followUpTopic = $this->inferFollowUpTopic($questionLower, $recentChatTurns);
        $wantsRankedItem = (bool) preg_match('/\b(most recent|latest|newest|last|first|earliest|oldest)\b/', $questionLower);
        $preferEarliest = (bool) preg_match('/\b(first|earliest|oldest)\b/', $questionLower);
        $accountIntentCount = collect([
            ChatIntentSignals::isQuoteIntentQuery($question),
            ChatIntentSignals::isOrderIntentQuery($question),
            ChatIntentSignals::isInvoiceIntentQuery($question),
        ])->filter()->count();

        if (ChatIntentSignals::isDueAmountQuestion($question) || (ChatIntentSignals::isInvoiceIntentQuery($question) && $accountIntentCount > 1)) {
            if ($openCount === 0) {
                return 'You currently have no outstanding balance due. If you want, I can still summarize your latest orders and quotes.';
            }

            return "Your total outstanding balance due is {$openTotal} across {$openCount} open invoice(s). If you want, I can also break this down per invoice and relate each to its order/quote.";
        }

        if ($accountIntentCount > 1) {
            $orderCount = count($orders);
            $quoteCount = count($quotes);
            $latestOrder = $orders[0]['order_number'] ?? null;
            $latestOrderStatus = $orders[0]['status'] ?? null;
            $latestOrderLine = $latestOrder
                ? " Latest order: **{$latestOrder}**" . ($latestOrderStatus ? ' (' . ucfirst((string) $latestOrderStatus) . ').' : '.')
                : '';

            $balanceLine = $openCount > 0
                ? " Outstanding balance: {$openTotal} across {$openCount} open invoice(s)."
                : ' No outstanding balance due right now.';

            return "You currently have {$quoteCount} quote(s) and {$orderCount} order(s).{$latestOrderLine}{$balanceLine}";
        }

        if ($wantsRankedItem && $followUpTopic === 'quote') {
            $quote = $this->pickRankedContextItem($quotes, $preferEarliest);
            if ($quote !== null && !empty($quote['quote_id'])) {
                $quoteAmount = !empty($quote['total_amount']) ? ' for ' . $this->formatAssistantMoney((float) $quote['total_amount']) : '';
                $quoteDate = !empty($quote['created_at']) ? ' from ' . substr((string) $quote['created_at'], 0, 10) : '';
                $quoteOrder = !empty($quote['order_number']) ? " linked to order {$quote['order_number']}" : '';
                $quoteStatus = !empty($quote['status']) ? ' Status: ' . ucfirst((string) $quote['status']) . '.' : '';
                $position = $preferEarliest ? 'first' : 'most recent';

                return "Your {$position} quote is **{$quote['quote_id']}**{$quoteAmount}{$quoteDate}{$quoteOrder}{$quoteStatus}";
            }
        }

        if ($wantsRankedItem && $followUpTopic === 'order') {
            $order = $this->pickRankedContextItem($orders, $preferEarliest);
            if ($order !== null && !empty($order['order_number'])) {
                $orderAmount = !empty($order['total_amount']) ? ' for ' . $this->formatAssistantMoney((float) $order['total_amount']) : '';
                $orderDate = !empty($order['created_at']) ? ' from ' . substr((string) $order['created_at'], 0, 10) : '';
                $orderStatus = !empty($order['status']) ? ' Status: ' . ucfirst((string) $order['status']) . '.' : '';
                $position = $preferEarliest ? 'first' : 'most recent';

                return "Your {$position} order is **{$order['order_number']}**{$orderAmount}{$orderDate}{$orderStatus}";
            }
        }

        if ($wantsRankedItem && $followUpTopic === 'invoice') {
            $invoice = $this->pickRankedContextItem($invoices, $preferEarliest);
            if ($invoice !== null && !empty($invoice['invoice_number'])) {
                $invoiceAmount = !empty($invoice['total_amount']) ? ' for ' . $this->formatAssistantMoney((float) $invoice['total_amount']) : '';
                $invoiceDate = !empty($invoice['due_at']) ? ' due ' . substr((string) $invoice['due_at'], 0, 10) : '';
                $remainingAmount = $this->formatAssistantMoney((float) ($invoice['remaining_amount'] ?? 0));
                $position = $preferEarliest ? 'first' : 'most recent';

                return "Your {$position} invoice is **{$invoice['invoice_number']}**{$invoiceAmount}{$invoiceDate}. The remaining balance is {$remainingAmount}.";
            }
        }

        if ($focusedInvoice && !empty($focusedInvoice['invoice_number'])) {
            $remaining = $this->formatAssistantMoney((float) ($focusedInvoice['remaining_amount'] ?? 0));
            $status = strtolower((string) ($focusedInvoice['status'] ?? 'open'));
            return "I found invoice {$focusedInvoice['invoice_number']} (status: {$status}). The remaining balance is {$remaining}. You can view it, download the PDF, or make a payment using the actions below.";
        }

        if (Str::contains($questionLower, ['invoice', 'payment', 'pay'])) {
            if ($openCount === 0) {
                return "You do not have any outstanding invoices right now. If you would like, I can still help you review past invoices or set up a new order.";
            }
            return "You currently have {$openCount} open invoice(s) totaling {$openTotal}. I can help you view details, download PDFs, or make payments. Tell me which invoice you would like to start with.";
        }

        $productSuggestions = (array) ($context['product_suggestions'] ?? []);
        if (!empty($productSuggestions)) {
            $count = count($productSuggestions);
            $top = $productSuggestions[0];
            $price = $this->formatAssistantMoney((float) ($top['price'] ?? 0));
            $name = (string) ($top['name'] ?? 'a top product');
            return "I found {$count} product(s) that match your search. The top suggestion is **{$name}** at {$price}. Check the product cards below for details, and I can help you request a quote if you would like.";
        }

        if ($isProductIntent) {
            return "I searched the catalog but did not find a strong match for that phrase. Try a brand name like \"Dell\" or \"Cisco\", a model number, or a category like \"laptop\" or \"switch\".";
        }

        if (Str::contains($questionLower, ['quote', 'same quote', 'reorder', 'requote'])) {
            if ($completedPaidQuoteCount > 0) {
                return "You have {$completedPaidQuoteCount} approved and paid quote(s) ready to reorder. I can help you duplicate one or build a new quote from scratch.";
            }
            return "I do not see any completed quotes yet, but I can help you browse products and build a new quote. What are you looking for?";
        }

        if (Str::contains($questionLower, ['order', 'track', 'shipping', 'delivery'])) {
            return "I can pull up your recent orders and tracking info. If you have a specific order number, share it and I will look it up.";
        }

        $greetings = ['hi', 'hello', 'hey', 'yo', 'good morning', 'good afternoon', 'good evening', 'howdy', 'sup', 'whats up'];
        if (in_array($questionLower, $greetings, true) || Str::startsWith($questionLower, ['hi ', 'hello ', 'hey '])) {
            $nameGreet = $firstName !== '' ? ", {$firstName}" : '';
            return "Hey{$nameGreet}. I'm Mela AI, your Armely assistant. I can help with invoices, payments, orders, quotes, and product recommendations.";
        }

        if (Str::contains($questionLower, ['thank', 'thanks', 'thx', 'appreciate'])) {
            return "You're welcome{$nameSuffix}. If you need anything else, just send it over.";
        }

        return "I can help with invoices, payments, quotes, order tracking, and product recommendations. Ask me about a specific invoice, quote, order, or product and I'll jump in.";
    }

    private function inferFollowUpTopic(string $question, array $recentChatTurns = []): ?string
    {
        $topic = $this->detectAccountTopicFromText($question);
        if ($topic !== null) {
            return $topic;
        }

        if (!$this->isContextualFollowUpQuery($question)) {
            return null;
        }

        foreach (array_reverse($recentChatTurns) as $turn) {
            $intent = strtolower(trim((string) ($turn['intent'] ?? '')));
            $intentTopic = match ($intent) {
                'quote_management' => 'quote',
                'order_status' => 'order',
                'invoice_payment' => 'invoice',
                default => null,
            };
            if ($intentTopic !== null) {
                return $intentTopic;
            }

            $text = strtolower(trim((string) ($turn['content'] ?? '')));
            if ($text === '') {
                continue;
            }

            $topic = $this->detectAccountTopicFromText($text);
            if ($topic !== null) {
                return $topic;
            }
        }

        return null;
    }

    private function isContextualFollowUpQuery(string $question): bool
    {
        $q = ChatIntentSignals::normalizeQuestion($question);

        return $q !== '' && (bool) preg_match(
            '/\b(it|that|this|these|those|them|one|ones|same|previous|earlier|former|latter|what about|how about|and the|also|instead|cheaper|more expensive|oldest|newest|latest|first|last|next|details|more|price|cost|how much|product name)\b/u',
            $q
        );
    }

    private function detectAccountTopicFromText(string $text): ?string
    {
        $text = strtolower(trim($text));
        if ($text === '') {
            return null;
        }

        if (Str::contains($text, ['invoice', 'invoices', 'payment', 'payments', 'pay', 'billing', 'balance'])) {
            return 'invoice';
        }

        if (Str::contains($text, ['quote', 'quotes', 'requote', 'reorder', 'same quote'])) {
            return 'quote';
        }

        if (Str::contains($text, ['order', 'orders', 'shipping', 'tracking', 'delivery', 'track'])) {
            return 'order';
        }

        return null;
    }

    private function pickRankedContextItem(array $items, bool $preferEarliest = false): ?array
    {
        $items = array_values(array_filter($items, static fn ($item) => is_array($item)));
        if (empty($items)) {
            return null;
        }

        return $preferEarliest ? $items[array_key_last($items)] : $items[0];
    }

    private function handleLocalProductDiscoveryReply(string $question, array $context): ?array
    {
        $isProductIntent = (bool) ($context['product_intent'] ?? false);
        if (!$isProductIntent) {
            return null;
        }

        $productSuggestions = (array) ($context['product_suggestions'] ?? []);
        $actions = $this->buildAssistantActions($question, $context);
        $customerName = trim((string) ($context['customer']['name'] ?? ''));
        $firstName = $customerName !== '' ? explode(' ', $customerName)[0] : '';
        $greet = $firstName !== '' ? "{$firstName}, " : '';
        $naturalReply = $this->assistantService->generateProductNarration(
            $question,
            $productSuggestions,
            $context,
            (array) ($context['recent_chat_turns'] ?? [])
        );

        if ($naturalReply !== null) {
            return [
                'reply' => $naturalReply,
                'actions' => $actions,
                'product_suggestions' => $productSuggestions,
                'source' => 'product_narration_agent',
            ];
        }

        if (!empty($productSuggestions)) {
            $count = count($productSuggestions);
            $top = $productSuggestions[0];
            $topName = (string) ($top['name'] ?? 'a matching product');
            $topPrice = isset($top['price']) ? $this->formatAssistantMoney((float) $top['price']) : null;
            $topVendor = trim((string) ($top['vendor'] ?? ''));

            $isRecommendationFollowUp = Str::contains(strtolower($question), [
                'which one', 'which is best', 'recommend one', 'do you recommend',
                'your recommendation', 'pick one', 'pick the best', 'choose for me',
            ]);

            if ($isRecommendationFollowUp) {
                $reply = "{$greet}my recommendation is **{$topName}**";
                if ($topPrice && $topPrice !== '$0.00') {
                    $reply .= " at {$topPrice}";
                }
                if ($topVendor !== '') {
                    $reply .= " from {$topVendor}";
                }
                $reason = trim((string) ($top['why'] ?? $top['description'] ?? ''));
                $reply .= $reason !== ''
                    ? '. ' . rtrim($reason, '.') . '.'
                    : '. It is the strongest match among the products shown.';

                return [
                    'reply' => $reply,
                    'actions' => $actions,
                    'product_suggestions' => $productSuggestions,
                    'source' => 'local_product_recommendation_follow_up',
                ];
            }

            $questionLower = strtolower($question);
            $catalogQuery = trim((string) ($context['catalog_search_query'] ?? 'products'));
            $isLowestPriceRequest = (bool) preg_match('/\b(cheapest|lowest price|least expensive|budget(?: friendly)?|affordable|inexpensive|low cost)\b/i', $questionLower);

            if ($isLowestPriceRequest) {
                $subject = Str::plural(trim((string) preg_replace(
                    '/\b(cheapest|lowest price|least expensive|budget(?: friendly)?|affordable|inexpensive|low cost)\b/i',
                    '',
                    $catalogQuery
                )) ?: 'product');
                $reply = "Here are the lowest-priced **{$subject}** I found in the current catalog. ";
                $reply .= "The most affordable match is **{$topName}**";
            } else {
                $reply = $count === 1
                    ? "I found one catalog match for **{$catalogQuery}**: **{$topName}**"
                    : "Here are {$count} catalog matches for **{$catalogQuery}**. The closest match is **{$topName}**";
            }

            if ($topPrice && $topPrice !== '$0.00') {
                $reply .= " at {$topPrice}";
                if ($topVendor !== '') {
                    $reply .= " from {$topVendor}";
                }
                $reply .= '.';
            } elseif (!$isLowestPriceRequest) {
                if ($topVendor !== '') {
                    $reply .= " from {$topVendor}";
                }
                $reply .= '.';
            }

            if ($isLowestPriceRequest && $count > 1) {
                $reply .= ' The remaining cards continue from lower to higher price.';
            }

            return [
                'reply' => $reply,
                'actions' => $actions,
                'product_suggestions' => $productSuggestions,
                'source' => 'local_product_search_match',
            ];
        }

        $understoodQuery = trim((string) ($context['catalog_search_query'] ?? $question));

        return [
            'reply' => "{$greet}I understood your search as **{$understoodQuery}**, but I couldn't find a currently available, priced match. You can broaden the description, add a brand, or give me a model/SKU and I'll search again.",
            'actions' => $actions,
            'product_suggestions' => [],
            'source' => 'local_product_search_no_match',
        ];
    }

    private function handleCatalogQueryAudit(string $question, array $context): ?array
    {
        if (!ChatIntentSignals::isCatalogQueryAudit($question)) {
            return null;
        }

        $query = trim((string) ($context['catalog_search_query'] ?? ''));
        if ($query === '') {
            return [
                'reply' => 'I do not have a previous catalog search query in this conversation yet.',
                'actions' => [['label' => 'Browse catalog', 'link' => '/products']],
            ];
        }

        return [
            'reply' => 'I searched the product database using: **' . $query . '**.',
            'actions' => [['label' => 'Open this search', 'link' => '/products?q=' . urlencode($query)]],
        ];
    }

    private function resolveCatalogSearchQuery(string $question, array $recentChatTurns): string
    {
        if (!ChatIntentSignals::isCatalogQueryAudit($question)) {
            return ChatIntentSignals::extractCatalogSearchPhrase($question);
        }

        foreach (array_reverse($recentChatTurns) as $turn) {
            if (strtolower((string) ($turn['role'] ?? '')) !== 'user') {
                continue;
            }

            $content = trim((string) ($turn['content'] ?? ''));
            if ($content === '' || ChatIntentSignals::isCatalogQueryAudit($content)) {
                continue;
            }

            if (ChatIntentSignals::isProductLookupIntent($content)) {
                return ChatIntentSignals::extractCatalogSearchPhrase($content);
            }
        }

        return '';
    }

    /**
     * Keep context for a terse refinement ("Sony?") without leaking an old product type into
     * an explicit new request ("now do phones"). No category or brand dictionary is required.
     */
    private function resolveConversationalCatalogSearchQuery(string $question, array $recentChatTurns): string
    {
        $current = $this->resolveCatalogSearchQuery($question, $recentChatTurns);
        $currentKeywords = ChatIntentSignals::extractProductSearchKeywords($current);
        $normalizedQuestion = ChatIntentSignals::normalizeQuestion($question);
        $isExplicitNewSearch = (bool) preg_match(
            '/\b(?:i need|i want|find|search|looking for|now do|switch to)\b/u',
            $normalizedQuestion
        );

        if ($current === '' || $isExplicitNewSearch || count($currentKeywords) !== 1) {
            return $current;
        }

        $skippedCurrentTurn = false;
        foreach (array_reverse($recentChatTurns) as $turn) {
            if (strtolower((string) ($turn['role'] ?? '')) !== 'user') {
                continue;
            }

            $content = trim((string) ($turn['content'] ?? ''));
            if (!$skippedCurrentTurn && ChatIntentSignals::normalizeQuestion($content) === $normalizedQuestion) {
                $skippedCurrentTurn = true;
                continue;
            }

            $previous = ChatIntentSignals::extractCatalogSearchPhrase($content);
            $previousKeywords = ChatIntentSignals::extractProductSearchKeywords($previous);
            if ($previous === '' || empty($previousKeywords)) {
                continue;
            }

            return trim($current . ' ' . implode(' ', $previousKeywords));
        }

        return $current;
    }

    private function extractInvoiceNumber(string $text): ?string
    {
        if (preg_match('/\bINV-[A-Z0-9-]+\b/i', $text, $matches)) {
            return strtoupper($matches[0]);
        }

        return null;
    }

    private function getAssistantCurrencyConfig(): array
    {
        if (!Schema::hasTable('app_settings')) {
            return ['code' => 'USD', 'rate' => 1.0, 'symbol' => '$'];
        }

        $code = strtoupper((string) AppSetting::getValue('pricing.currency_code', 'USD'));
        if ($code === '') {
            $code = 'USD';
        }

        $rate = max(0.0001, AppSetting::getNumber('pricing.currency_rate', 1.0));

        $symbol = match ($code) {
            'EUR' => 'EUR ',
            'GBP' => 'GBP ',
            'KES' => 'KES ',
            'USD' => '$',
            default => $code . ' ',
        };

        return [
            'code' => $code,
            'rate' => $rate,
            'symbol' => $symbol,
        ];
    }

    private function formatAssistantMoney(float $amount, int $decimals = 2): string
    {
        $currency = $this->getAssistantCurrencyConfig();
        $converted = $amount * (float) ($currency['rate'] ?? 1.0);

        return (string) ($currency['symbol'] ?? '$') . number_format($converted, $decimals);
    }

    private function searchProductsForAssistant(string $question, array $historyPreferences = [], int $limit = 6, array $searchContext = []): array
    {
        $rawKeywords = $this->extractProductSearchKeywords($question);
        $deviceType = strtolower((string) ($searchContext['device_type'] ?? ''));
        $maxBudget = isset($searchContext['max_budget']) ? (float) $searchContext['max_budget'] : null;
        $budgetPriority = (bool) ($searchContext['budget_priority'] ?? false);
        $requiredBrand = strtolower((string) ($searchContext['required_brand'] ?? ''));
        $requiredCategory = strtolower((string) ($searchContext['required_category'] ?? ''));
        $excludedTerms = array_values(array_filter(array_map('strtolower', (array) ($searchContext['excluded_terms'] ?? []))));
        $transactionalFollowUp = (bool) ($searchContext['transactional_follow_up'] ?? false);
        $recentSuggestedProducts = (array) ($searchContext['recent_suggested_products'] ?? []);

        // Check transactional follow-up using raw question keywords (before deviceType from history is
        // appended) so that "which one do you suggest?" or "add them to cart" correctly re-uses the
        // products already shown rather than running a fresh search with the inherited device type.
        if ($transactionalFollowUp && !empty($recentSuggestedProducts)) {
            return collect($recentSuggestedProducts)
                ->map(function (array $candidate) {
                    return [
                        'product_id' => (string) ($candidate['product_id'] ?? ''),
                        'name' => (string) ($candidate['name'] ?? ''),
                        'sku' => (string) ($candidate['sku'] ?? ''),
                        'vendor' => (string) ($candidate['vendor'] ?? ''),
                        'price' => (float) ($candidate['price'] ?? 0),
                        'description' => Str::limit((string) ($candidate['description'] ?? ''), 180),
                        'image_url' => $candidate['image_url'] ?? null,
                        'why' => 'From your previously suggested products for this conversation.',
                        'actions' => [
                            [
                                'label' => 'View details',
                                'link' => '/products/' . urlencode((string) ($candidate['product_id'] ?? '')),
                            ],
                            [
                                'label' => 'Find similar',
                                'link' => '/products?q=' . urlencode((string) ($candidate['name'] ?? '')),
                            ],
                            [
                                'label' => 'Request quote',
                                'link' => '/cart',
                            ],
                        ],
                    ];
                })
                ->filter(static fn (array $item) => !empty($item['product_id']) && !empty($item['name']))
                ->reject(fn (array $item) => $this->isAccessoryLikeProduct($item))
                ->unique(static fn (array $item) => (string) $item['product_id'])
                ->take($limit)
                ->values()
                ->all();
        }

        // Only the current resolved catalog query controls retrieval. Conversation preferences
        // may influence ranking, but must never broaden the database WHERE clause.
        $keywords = $rawKeywords;

        if ($deviceType !== '' && !in_array($deviceType, $keywords, true)) {
            $keywords[] = $deviceType;
        }

        $preferenceKeywords = array_values(array_filter(array_map('strtolower', $historyPreferences)));

        if (empty($keywords)) {
            return [];
        }

        $searchQueries = $this->buildAssistantSearchQueries($question, $keywords);
        $catalogPhrase = strtolower(ChatIntentSignals::extractCatalogSearchPhrase($question));

        $products = Product::query()
            ->where(function ($availability) {
                $availability->where('is_available', true)
                    ->orWhereNull('is_available');
            })
            ->where(function ($stock) {
                $stock->where('quantity', '>', 0)
                    ->orWhereNull('quantity');
            })
            ->where(function ($active) {
                $active->where('is_discontinued', false)
                    ->orWhereNull('is_discontinued');
            })
            ->where(function ($priced) {
                $priced->where('sale_price', '>', 0)
                    ->orWhere('base_price', '>', 0);
            })
            ->where(function ($base) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $like = '%' . $keyword . '%';
                    $base->orWhere('product_name', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('vendor_id', 'like', $like)
                        ->orWhere('mfg_part_no', 'like', $like)
                        ->orWhere('tdsynnex_sku_no', 'like', $like)
                        ->orWhere('manufacturer', 'like', $like)
                        ->orWhere('category_segment', 'like', $like);
                }
            })
            ->when($catalogPhrase !== '', function ($query) use ($catalogPhrase) {
                $query->orderByRaw(
                    'CASE WHEN LOWER(product_name) = ? THEN 0 WHEN LOWER(product_name) LIKE ? THEN 1 ELSE 2 END',
                    [$catalogPhrase, '%' . $catalogPhrase . '%']
                );
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
                'sale_price',
                'is_on_sale',
                'offer_source',
                'images',
                'is_discontinued',
                'manufacturer',
                'category_segment',
            ]);

        $candidates = $products->map(function (Product $product) {
            $activeOffer = (bool) $product->is_on_sale
                && in_array((string) $product->offer_source, ['manual', 'verified_tdsynnex_special', 'tdsynnex_price_drop'], true)
                && (float) $product->sale_price > 0;
            $price = $activeOffer
                ? (float) $product->sale_price
                : (float) $product->base_price;

            return [
                'source' => 'db',
                'product_id' => (string) ($product->tdsynnex_product_id ?: $product->tdsynnex_sku_no),
                'name' => (string) ($product->product_name ?? ''),
                'sku' => (string) ($product->tdsynnex_sku_no ?? ''),
                'vendor' => (string) ($product->manufacturer ?: $product->vendor_id ?: 'TD SYNNEX'),
                'price' => $price,
                'description' => (string) ($product->description ?? ''),
                'category' => (string) ($product->category_segment ?? ''),
                'image_url' => $this->extractProductImageUrl($product->images),
                'is_discontinued' => (bool) $product->is_discontinued,
            ];
        })->values();

        // Assistant results are intentionally database-only. This prevents remote or demo data
        // from being represented as products currently present in the storefront catalog.
        $allowRemoteCatalogLookup = false;

        if ($allowRemoteCatalogLookup && $candidates->count() < max(3, $limit)) {
            try {
                foreach ($searchQueries as $searchQuery) {
                    $remote = $this->tdsynnexService->searchPriceAvailabilityCatalog($searchQuery, 120);
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

                    if ($candidates->count() >= max(8, $limit * 2)) {
                        break;
                    }
                }
            } catch (\Throwable $e) {
                // Keep assistant responsive even if catalog lookup fails.
            }
        }

        return $candidates
            ->map(function (array $candidate) use ($keywords, $preferenceKeywords, $searchContext, $deviceType, $maxBudget, $requiredBrand, $requiredCategory) {
                $name = strtolower((string) ($candidate['name'] ?? ''));
                $description = strtolower((string) ($candidate['description'] ?? ''));
                $category = strtolower((string) ($candidate['category'] ?? ''));
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

                    if ($this->containsCatalogTerm($name, $keyword)) {
                        $score += 5;
                        $matched[] = $keyword;
                    }

                    if ($this->containsCatalogTerm($vendor, $keyword)) {
                        $score += 4;
                        $matched[] = $keyword;
                    }

                    if ($this->containsCatalogTerm($description, $keyword)) {
                        $score += 2;
                        $matched[] = $keyword;
                    }

                    if ($this->containsCatalogTerm($category, $keyword)) {
                        $score += 4;
                        $matched[] = $keyword;
                    }

                    if ($this->containsCatalogTerm($sku, $keyword)) {
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

                if ($requiredBrand !== '') {
                    $brandMatched = str_contains($name, $requiredBrand)
                        || str_contains($vendor, $requiredBrand)
                        || str_contains($description, $requiredBrand);

                    if ($brandMatched) {
                        $score += 8;
                    } else {
                        $score -= 18;
                    }
                }

                if ($requiredCategory !== '') {
                    $categoryMatched = $this->matchesRequestedCategory($candidate, $requiredCategory);
                    if ($categoryMatched) {
                        $score += 7;
                    } else {
                        $score -= 16;
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

                $whyText = 'Selected based on catalog relevance to your search.';
                if (!empty($matched)) {
                    $whyText = 'Matches your search for: ' . implode(', ', $matched) . '.';
                }
                if (!empty($historyMatches)) {
                    $whyText .= ' Also aligns with your recent interest in ' . implode(', ', $historyMatches) . '.';
                }

                if ($maxBudget !== null && $maxBudget > 0 && $price > 0) {
                    if ($price <= $maxBudget) {
                        $whyText .= ' ✓ Within your ' . $this->formatAssistantMoney($maxBudget, 0) . ' budget.';
                    } else {
                        $whyText .= ' ⚠ Above your ' . $this->formatAssistantMoney($maxBudget, 0) . ' budget.';
                    }
                }

                if ($isDeviceMatch && $deviceType !== '') {
                    $whyText .= ' Confirmed ' . $deviceType . ' match.';
                }

                return [
                    'score' => $score,
                    'matched_keyword_count' => count($matched),
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
                            'link' => '/products?q=' . urlencode((string) ($candidate['name'] ?? '')),
                        ],
                        [
                            'label' => 'Request quote',
                            'link' => '/cart',
                        ],
                    ],
                ];
            })
            ->filter(function (array $item) use ($keywords) {
                if (empty($item['product_id']) || empty($item['name']) || (float) ($item['price'] ?? 0) <= 0) {
                    return false;
                }

                // Descriptive/use-case words improve ranking; they are not all mandatory. The
                // requested device/category and brand are enforced separately below.
                $requiredMatches = 1;

                return (int) ($item['matched_keyword_count'] ?? 0) >= $requiredMatches;
            })
            ->filter(function (array $item) use ($deviceType, $maxBudget) {
                if ($deviceType !== '' && !($item['device_match'] ?? false)) {
                    return false;
                }

                if ($deviceType !== '' && ($item['is_accessory'] ?? false)) {
                    return false;
                }

                if ($deviceType !== '' && $this->isConflictingDeviceProduct($item, $deviceType)) {
                    return false;
                }

                $price = (float) ($item['price'] ?? 0);
                if ($maxBudget !== null && $maxBudget > 0 && $price > $maxBudget) {
                    return false;
                }

                return true;
            })
            ->filter(function (array $item) use ($requiredBrand, $requiredCategory) {
                if ($requiredBrand !== '') {
                    $haystack = strtolower(
                        (string) ($item['name'] ?? '') . ' ' .
                        (string) ($item['vendor'] ?? '') . ' ' .
                        (string) ($item['description'] ?? '')
                    );

                    if (!str_contains($haystack, $requiredBrand)) {
                        return false;
                    }
                }

                if ($requiredCategory !== '') {
                    if (!$this->matchesRequestedCategory($item, $requiredCategory)) {
                        return false;
                    }
                }

                return true;
            })
            ->reject(function (array $item) use ($excludedTerms) {
                if (empty($excludedTerms)) {
                    return false;
                }

                $haystack = strtolower(
                    (string) ($item['name'] ?? '') . ' ' .
                    (string) ($item['description'] ?? '')
                );

                return collect($excludedTerms)->contains(
                    fn (string $term) => $this->containsCatalogTerm($haystack, $term)
                );
            })
            ->sort(function (array $a, array $b) use ($budgetPriority) {
                if ($budgetPriority) {
                    $priceComparison = ((float) ($a['price'] ?? INF)) <=> ((float) ($b['price'] ?? INF));
                    if ($priceComparison !== 0) {
                        return $priceComparison;
                    }
                }

                return ((float) ($b['score'] ?? 0)) <=> ((float) ($a['score'] ?? 0));
            })
            ->values()
            ->take($limit)
            ->map(function (array $item) {
                unset($item['score']);
                unset($item['matched_keyword_count']);
                unset($item['is_accessory']);
                unset($item['device_match']);
                return $item;
            })
            ->all();
    }

    private function extractProductSearchKeywords(string $question): array
    {
        return ChatIntentSignals::extractProductSearchKeywords($question);
    }

    private function buildAssistantSearchQueries(string $question, array $keywords): array
    {
        $raw = strtolower(trim($question));
        $cleaned = preg_replace('/\b(search|find|look\s*for|show|me|please|for\s*me)\b/i', ' ', $raw) ?? $raw;
        $cleaned = preg_replace('/\s+/', ' ', trim((string) $cleaned)) ?? '';

        $queries = [];

        $keywordPhrase = trim(implode(' ', array_slice(array_values(array_filter($keywords)), 0, 8)));
        if ($keywordPhrase !== '') {
            $queries[] = $keywordPhrase;
        }

        if ($cleaned !== '') {
            $queries[] = $cleaned;
        }

        if ($raw !== '') {
            $queries[] = $raw;
        }

        if (!empty($keywords)) {
            $firstKeyword = trim((string) ($keywords[0] ?? ''));
            if ($firstKeyword !== '') {
                $queries[] = $firstKeyword;
            }
        }

        return array_values(array_unique(array_filter($queries, static fn ($q) => is_string($q) && trim($q) !== '')));
    }

    private function isProductDiscoveryIntent(string $question, array $recentChatTurns = []): bool
    {
        $q = strtolower(trim($question));
        if ($q === '') {
            return false;
        }

        if (ChatIntentSignals::isGeneralConversationQuery($question) || ChatIntentSignals::isSmallTalkQuery($question)) {
            return false;
        }

        if (ChatIntentSignals::isProductLookupIntent($question, $recentChatTurns)) {
            return true;
        }

        $greetings = ['hi', 'hello', 'hey', 'yo', 'good morning', 'good afternoon', 'good evening', 'howdy', 'sup', 'whats up', 'thanks', 'thank you', 'thx', 'ok', 'okay', 'bye', 'goodbye'];
        if (in_array($q, $greetings, true)) {
            return false;
        }

        $productSignals = [
            'laptop', 'notebook', 'desktop', 'monitor', 'printer', 'server', 'sku', 'model', 'spec',
            'recommend', 'suggest', 'sample list', 'best', 'buy', 'purchase', 'network',
            'switch', 'router', 'firewall', 'access point', 'wifi', 'wireless', 'catalogue', 'catalog',
            'workstation', 'tablet', 'projector', 'scanner', 'ups', 'storage', 'ssd', 'ram', 'memory',
            'headset', 'webcam', 'docking', 'dock', 'keyboard', 'mouse', 'display',
            'thin client', 'chromebook', 'all-in-one', 'mini pc',
        ];

        $financeSignals = [
            'invoice', 'payment', 'pay', 'balance', 'due', 'download', 'pdf', 'billing', 'receipt',
            'reminder', 'send reminder', 'quote', 'quotes', 'my quotes', 'current quotes', 'open quotes',
        ];

        $hasCurrentProductSignal = false;
        foreach ($productSignals as $signal) {
            if (str_contains($q, $signal)) {
                $hasCurrentProductSignal = true;
                break;
            }
        }

        $hasMeaningfulKeywords = count($this->extractProductSearchKeywords($question)) > 0;

        if (!$hasCurrentProductSignal && !$hasMeaningfulKeywords && Str::contains($q, $financeSignals)) {
            return false;
        }

        if (Str::contains($q, ['do we have', 'availability', 'in stock', 'check for', 'search for'])) {
            return true;
        }

        foreach ($productSignals as $signal) {
            if (str_contains($q, $signal)) {
                return true;
            }
        }

        if ($hasMeaningfulKeywords && !Str::contains($q, $financeSignals)) {
            return false;
        }

        $recentUserText = collect($recentChatTurns)
            ->filter(static fn (array $turn) => strtolower((string) ($turn['role'] ?? '')) === 'user')
            ->pluck('content')
            ->map(static fn ($t) => strtolower((string) $t))
            ->implode(' ');

        $followUpSignals = [
            'which one', 'which is best', 'recommend one', 'top one', 'best one',
            'can you recommend', 'show more', 'similar options', 'other options', 'another option',
            'under', 'below', 'not more than', 'within budget',
            'from the list', 'from your list', 'why did you suggest'
        ];

        $isLikelyProductFollowUp = Str::contains($q, $followUpSignals);

        if ($recentUserText !== '' && $isLikelyProductFollowUp) {
            $recentHasProductSuggestions = collect($recentChatTurns)
                ->contains(static fn (array $turn) => (bool) ($turn['has_product_suggestions'] ?? false));

            if ($recentHasProductSuggestions) {
                return true;
            }

            foreach (['laptop', 'notebook', 'recommend', 'sample list', 'buy', 'purchase', 'printer', 'network'] as $signal) {
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
            'suggest', 'suggested', 'recommended', 'recommend', 'available', 'current',
            'product', 'products', 'item', 'items', 'invoice', 'invoices', 'payment', 'payments',
            'quote', 'quotes', 'order', 'orders', 'one', 'two', 'three', 'hi', 'hello', 'hey', 'to', 'today',
            // question words and common verbs that should never be product search terms
            'which', 'what', 'where', 'when', 'why', 'how', 'who', 'whose',
            'is', 'are', 'was', 'were', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should',
            'be', 'been', 'being', 'get', 'got', 'go', 'going', 'see', 'check', 'tell', 'know', 'show',
            'tied', 'linked', 'associated', 'related', 'connect', 'connected', 'belongs', 'belong',
            'also', 'then', 'there', 'here', 'now', 'just', 'only', 'very', 'too', 'so', 'but', 'if',
        ];

        $priorityTerms = [
            'laptop', 'notebook', 'monitor', 'printer', 'printers', 'server', 'desktop',
            'workstation', 'gaming', 'business', 'network', 'networking', 'switch',
            'router', 'firewall', 'wireless'
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
        // A fresh search is governed only by the current resolved query. History is consulted
        // only when the turn has no search nouns of its own (recommend/pick/add follow-ups).
        $hasCurrentSearchTerms = !empty(ChatIntentSignals::extractProductSearchKeywords($question));
        $texts = [];
        if (!$hasCurrentSearchTerms) {
            $texts = collect($recentChatTurns)
                ->filter(static fn (array $turn) => strtolower((string) ($turn['role'] ?? '')) === 'user')
                ->pluck('content')
                ->map(static fn ($text) => (string) $text)
                ->all();
        }
        $texts[] = $question;
        $joined = strtolower(implode(' ', array_filter($texts)));
        $budgetPriority = (bool) preg_match('/\b(budget(?: friendly)?|affordable|low cost|lower cost|economical|inexpensive|cheapest|value)\b/i', $joined);

        $deviceType = null;
        if (str_contains($joined, 'laptop') || str_contains($joined, 'notebook')) {
            $deviceType = 'laptop';
        } elseif (str_contains($joined, 'desktop') || str_contains($joined, 'workstation')) {
            $deviceType = 'desktop';
        } elseif (str_contains($joined, 'monitor') || str_contains($joined, 'display')) {
            $deviceType = 'monitor';
        } elseif (str_contains($joined, 'printer') || str_contains($joined, 'laserjet')) {
            $deviceType = 'printer';
        } elseif (str_contains($joined, 'server')) {
            $deviceType = 'server';
        } elseif (str_contains($joined, 'switch') && !str_contains($joined, 'nintendo')) {
            $deviceType = 'switch';
        } elseif (str_contains($joined, 'router') || str_contains($joined, 'gateway')) {
            $deviceType = 'router';
        } elseif (str_contains($joined, 'access point') || str_contains($joined, 'wireless ap')) {
            $deviceType = 'access point';
        } elseif (str_contains($joined, 'camera') || str_contains($joined, 'webcam')) {
            $deviceType = 'camera';
        } elseif (str_contains($joined, 'tablet') || str_contains($joined, 'ipad')) {
            $deviceType = 'tablet';
        } elseif (str_contains($joined, 'phone') || str_contains($joined, 'handset')) {
            $deviceType = 'phone';
        } elseif (str_contains($joined, 'scanner') || str_contains($joined, 'barcode reader')) {
            $deviceType = 'scanner';
        } elseif (str_contains($joined, 'projector')) {
            $deviceType = 'projector';
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

        $requiredBrand = null;
        $knownBrands = [
            'dell', 'hp', 'hewlett-packard', 'lenovo', 'cisco', 'meraki', 'microsoft', 'apple',
            'samsung', 'epson', 'brother', 'canon', 'asus', 'acer', 'logitech', 'jabra',
            'netgear', 'ubiquiti', 'fortinet', 'aruba', 'juniper', 'sophos', 'intel',
            'amd', 'nvidia', 'toshiba', 'xerox', 'ricoh', 'lexmark', 'benq', 'lg',
            'panasonic', 'viewsonic', 'poly', 'plantronics', 'dynabook', 'msi',
        ];
        foreach ($knownBrands as $brand) {
            if (str_contains($joined, $brand)) {
                $requiredBrand = $brand;
                break;
            }
        }

        $requiredCategory = null;
        foreach (['monitor', 'display', 'laptop', 'notebook', 'desktop', 'workstation', 'printer', 'server', 'switch', 'router', 'access point', 'firewall', 'scanner', 'projector', 'tablet', 'ups', 'storage'] as $category) {
            if (str_contains($joined, $category)) {
                $requiredCategory = $category;
                break;
            }
        }

        $questionLower = strtolower(trim($question));
        $transactionalSignals = [
            'request quote', 'proceed', 'add to cart', 'add them', 'order them', 'place order',
            'make them quote', 'make quote', 'use suggested', 'best two', 'those two', 'them',
            'which one', 'which do you', 'suggest one', 'recommend one', 'pick one', 'pick the best',
            'what do you think', 'your recommendation', 'your suggestion', 'go with', 'choose for me',
            'what would you', 'what should i', 'which is better', 'which is best', 'what\'s the best',
        ];
        $transactionalFollowUp = Str::contains($questionLower, $transactionalSignals);

        $lastSuggestionTurn = collect($recentChatTurns)
            ->filter(static fn (array $turn) => strtolower((string) ($turn['role'] ?? '')) === 'assistant')
            ->reverse()
            ->first(static fn (array $turn) => !empty($turn['product_suggestions']));

        $recentSuggestedProducts = collect((array) ($lastSuggestionTurn['product_suggestions'] ?? []))
            ->map(function ($item) {
                if (!is_array($item)) {
                    return null;
                }

                return [
                    'product_id' => (string) ($item['product_id'] ?? ''),
                    'name' => (string) ($item['name'] ?? ''),
                    'sku' => (string) ($item['sku'] ?? ''),
                    'vendor' => (string) ($item['vendor'] ?? ''),
                    'price' => (float) ($item['price'] ?? 0),
                    'description' => (string) ($item['description'] ?? ''),
                    'image_url' => $item['image_url'] ?? null,
                ];
            })
            ->filter(static fn ($item) => is_array($item) && !empty($item['product_id']))
            ->unique(static fn (array $item) => (string) $item['product_id'])
            ->take(20)
            ->values()
            ->all();

        return [
            'device_type' => $deviceType,
            'max_budget' => $maxBudget,
            'budget_priority' => $budgetPriority,
            'required_brand' => $requiredBrand,
            'required_category' => $requiredCategory,
            'transactional_follow_up' => $transactionalFollowUp,
            'recent_suggested_products' => $recentSuggestedProducts,
        ];
    }

    private function matchesRequestedCategory(array $candidate, string $requiredCategory): bool
    {
        $category = strtolower(trim($requiredCategory));
        if ($category === '') {
            return true;
        }

        $haystack = strtolower(trim(
            (string) ($candidate['name'] ?? '') . ' ' .
            (string) ($candidate['description'] ?? '') . ' ' .
            (string) ($candidate['category'] ?? '') . ' ' .
            (string) ($candidate['sku'] ?? '')
        ));

        if ($haystack === '') {
            return false;
        }

        $aliases = [
            'monitor' => ['monitor', 'display', 'screen'],
            'laptop' => ['laptop', 'notebook', 'ultrabook'],
            'desktop' => ['desktop', 'workstation', 'mini pc'],
            'printer' => ['printer', 'laserjet', 'inkjet', 'multifunction', 'mfp'],
            'server' => ['server', 'rack', 'tower server'],
            'switch' => ['switch', 'ethernet switch'],
            'router' => ['router', 'gateway'],
            'access point' => ['access point', 'wireless ap', 'wifi ap'],
            'firewall' => ['firewall', 'security appliance'],
            'camera' => ['camera', 'webcam', 'video bar', 'videobar'],
            'tablet' => ['tablet', 'ipad'],
            'phone' => ['phone', 'handset', 'telephone'],
            'scanner' => ['scanner', 'barcode reader', 'barcode scanner'],
            'projector' => ['projector'],
        ];

        $needles = $aliases[$category] ?? [$category];
        foreach ($needles as $needle) {
            if ($this->containsCatalogTerm($haystack, strtolower($needle))) {
                return true;
            }
        }

        return false;
    }

    private function isAccessoryLikeProduct(array $candidate): bool
    {
        $haystack = strtolower(trim(
            (string) ($candidate['name'] ?? '') . ' ' .
            (string) ($candidate['description'] ?? '') . ' ' .
            (string) ($candidate['category'] ?? '') . ' ' .
            (string) ($candidate['sku'] ?? '')
        ));

        if ($haystack === '') {
            return false;
        }

        $accessoryTerms = [
            'case', 'cover', 'sleeve', 'bag', 'dock', 'docking', 'adapter', 'cable', 'charger',
            'keyboard', 'mouse', 'headset', 'speaker', 'stand', 'screen protector', 'protector',
            'hub', 'backpack', 'folio', 'power bank', 'battery', 'warranty', 'service plan', 'kit',
            'mount', 'mounting', 'bracket', 'arm', 'riser', 'shelf', 'tray', 'cart', 'trolley',
            'pdu', 'power distribution', 'power strip', 'surge protector',
            'monitor clip', 'privacy screen', 'privacyview', 'screen filter', 'display filter', 'privacy filter',
            'security lock', 'laptop lock', 'notebook lock', 'wedge lock', 'cable lock',
            'low profile lock', 'holder', 'stylus', 'earbud', 'card reader', 'memory card reader',
            'connect a usb', 'usb type-a device', 'pass-through port',
        ];

        foreach ($accessoryTerms as $term) {
            if (str_contains($haystack, $term)) {
                return true;
            }
        }

        return false;
    }

    private function isConflictingDeviceProduct(array $candidate, string $deviceType): bool
    {
        $name = strtolower((string) ($candidate['name'] ?? ''));
        $conflicts = [
            'camera' => ['laptop', 'notebook', 'desktop pc', 'monitor with camera'],
            'phone' => ['headphone', 'earphone', 'microphone', 'phone-jack', 'phone jack', 'stylus'],
            'tablet' => ['tablet holder', 'tablet stand', 'tablet case', 'tablet dock', 'tablet keyboard'],
            'monitor' => ['monitor stylus', 'monitor mount', 'monitor arm', 'privacy monitor'],
        ];

        foreach ($conflicts[$deviceType] ?? [] as $conflict) {
            if (str_contains($name, $conflict)) {
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
            (string) ($candidate['description'] ?? '') . ' ' .
            (string) ($candidate['category'] ?? '')
        ));

        if ($haystack === '') {
            return false;
        }

        $deviceAliases = [
            'laptop' => ['laptop', 'notebook', 'ultrabook', 'chromebook'],
            'desktop' => ['desktop', 'workstation', 'mini pc', 'all-in-one'],
            'monitor' => ['monitor', 'display', 'screen', 'lcd', 'led monitor'],
            'printer' => ['printer', 'laserjet', 'inkjet', 'multifunction', 'mfp'],
            'server' => ['server', 'rack', 'tower server', 'poweredge'],
            'switch' => ['switch', 'ethernet switch', 'managed switch'],
            'router' => ['router', 'gateway'],
            'access point' => ['access point', 'wireless ap', 'wifi ap'],
            'camera' => ['camera', 'webcam', 'video bar', 'videobar'],
            'tablet' => ['tablet', 'ipad'],
            'phone' => ['phone', 'handset', 'telephone'],
            'scanner' => ['scanner', 'barcode reader', 'barcode scanner'],
            'projector' => ['projector'],
        ];

        $needles = $deviceAliases[$deviceType] ?? [$deviceType];
        foreach ($needles as $needle) {
            if ($this->containsCatalogTerm($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function containsCatalogTerm(string $haystack, string $term): bool
    {
        $term = trim(strtolower($term));
        if ($term === '') {
            return false;
        }

        return preg_match('/(?<![a-z0-9])' . preg_quote($term, '/') . '(?![a-z0-9])/i', strtolower($haystack)) === 1;
    }

    private function extractProductImageUrl(mixed $images): ?string
    {
        $candidates = [];

        if (is_string($images) && trim($images) !== '') {
            $candidates[] = trim($images);
        }

        if (is_array($images)) {
            foreach ($images as $image) {
                if (is_string($image) && trim($image) !== '') {
                    $candidates[] = trim($image);
                    continue;
                }

                if (is_array($image)) {
                    $candidates[] = trim((string) ($image['imageUrl'] ?? $image['imageURL'] ?? $image['imagePath'] ?? $image['image_url'] ?? $image['url'] ?? ''));
                }
            }
        }

        foreach ($candidates as $candidate) {
            $url = $this->normalizeProductImagePath($candidate);
            if ($this->isValidImageUrl($url)) {
                return $this->resolveImageUrl($url);
            }
        }

        return null;
    }

    private function normalizeProductImagePath(string $url): string
    {
        $normalized = trim($url);
        if ($normalized === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $normalized) === 1 || str_starts_with($normalized, '/')) {
            return $normalized;
        }

        if (str_starts_with($normalized, 'images/')) {
            return '/' . $normalized;
        }

        if (str_starts_with($normalized, 'store/images/')) {
            return '/' . $normalized;
        }

        if (str_starts_with($normalized, 'storage/')) {
            return '/' . $normalized;
        }

        return $normalized;
    }

    private function resolveImageUrl(string $url): string
    {
        if (!str_starts_with($url, '/')) {
            return $url;
        }

        $base = rtrim((string) config('app.asset_url', ''), '/');
        return $base !== '' ? $base . $url : $url;
    }

    private function isValidImageUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '/')) {
            return (bool) preg_match('/^\/(?:store\/)?images\/.+\.(?:jpg|jpeg|png|webp|gif|avif|svg)(?:\?.*)?$/i', $url)
                || (bool) preg_match('/^\/storage\/.+\.(?:jpg|jpeg|png|webp|gif|avif|svg)(?:\?.*)?$/i', $url);
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        if ($path === '') {
            return false;
        }

        return (bool) preg_match('/\.(?:jpg|jpeg|png|webp|gif|avif|svg)$/i', $path)
            || str_contains(strtolower($path), '/images/products/')
            || str_contains(strtolower($path), '/store/images/products/')
            || str_contains(strtolower($path), '/storage/');
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

    private function notifyEscalationAdmins(
        ChatSession $session,
        User $customer,
        ?string $note = null,
        bool $reopened = false,
        string $source = 'manual_escalation'
    ): void {
        $adminRecipients = User::query()
            ->whereIn('role', ['admin', 'owner', 'manager'])
            ->where('status', 'active')
            ->get(['id']);

        foreach ($adminRecipients as $admin) {
            Message::createMessage(
                (int) $admin->id,
                'system',
                'Chat escalated to human support',
                ($reopened
                    ? 'A customer reopened and escalated Mela AI chat session #'
                    : 'A customer requested human follow-up in Mela AI chat session #') . $session->id,
                'CHAT-' . $session->id,
                'high',
                [
                    'chat_session_id' => $session->id,
                    'customer_id' => $customer->id,
                    'note' => $note,
                    'source' => $source,
                    'reopened' => $reopened,
                ]
            );
        }

        $this->notificationService->sendChatEscalationNotification(
            $session,
            $customer,
            $note,
            $reopened,
            $source
        );
    }
}
