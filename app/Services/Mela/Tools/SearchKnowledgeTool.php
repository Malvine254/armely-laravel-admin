<?php

namespace App\Services\Mela\Tools;

use App\Services\Mela\Knowledge\KnowledgeRetriever;
use App\Services\Mela\Telemetry\MelaLogger;

class SearchKnowledgeTool implements MelaTool
{
    public const PAGE_TYPES = ['home', 'about', 'service', 'solution', 'product', 'industry', 'case_study', 'customer_story', 'partner', 'resource', 'blog', 'contact'];

    public function __construct(
        private readonly KnowledgeRetriever $retriever,
        private readonly MelaLogger $logger,
    ) {
    }

    public function name(): string
    {
        return 'search_armely_knowledge';
    }

    public function description(): string
    {
        return 'Searches approved Armely website content (services, solutions, products such as Mela, industries, case studies, customer stories, partners, company information, contact details, blog articles and resources). '
            . 'Use it whenever an answer depends on Armely-specific information. Write the query as a self-contained description of what you need to know, '
            . 'including relevant context from earlier in the conversation. Blog results include the author when the page provides one. Returns matching passages with their page URL and a confidence level; '
            . 'if confidence is low or none, do not state Armely-specific facts that the passages do not support.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Self-contained semantic search query.'],
                'page_types' => [
                    'type' => 'array',
                    'items' => ['type' => 'string', 'enum' => self::PAGE_TYPES],
                    'description' => 'Optional preference for kinds of pages. Leave empty unless it clearly helps.',
                ],
            ],
            'required' => ['query'],
            'additionalProperties' => false,
        ];
    }

    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2', 'max:400'],
            'page_types' => ['sometimes', 'array', 'max:6'],
            'page_types.*' => ['string', 'in:' . implode(',', self::PAGE_TYPES)],
        ];
    }

    public function execute(array $arguments, ToolContext $context): array
    {
        $this->logger->event('RETRIEVAL_STARTED', ['conversation_id' => $context->conversation->id]);
        $search = $this->retriever->search($arguments['query'], $arguments['page_types'] ?? []);
        $context->retrievalMs += $search['latency_ms'];
        $context->addSources($search['results']);
        $this->logger->event('RETRIEVAL_COMPLETED', [
            'conversation_id' => $context->conversation->id,
            'results' => count($search['results']),
            'confidence' => $search['confidence'],
            'top_score' => $search['top_score'],
            'mode' => $search['mode'],
            'ms' => $search['latency_ms'],
        ]);

        return [
            'ok' => true,
            'confidence' => $search['confidence'],
            'note' => match ($search['confidence']) {
                'high' => 'Relevant Armely content found.',
                'low' => 'Only loosely related content found. Use it cautiously, consider a better query, and do not fill gaps with assumptions.',
                default => 'No approved Armely content matched. Do not state Armely-specific facts about this; say you do not have confirmed information and offer to connect the visitor with the team if appropriate.',
            },
            'results' => array_map(static fn ($r) => [
                'title' => $r['title'],
                'author' => $r['author'],
                'url' => $r['url'],
                'page_type' => $r['page_type'],
                'section' => $r['section'],
                'content' => mb_substr($r['content'], 0, 1400),
            ], $search['results']),
        ];
    }
}
