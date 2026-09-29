<?php

namespace App\Services\Mela\Tools;

use App\Services\Mela\Knowledge\KnowledgeRetriever;

class FindRelevantServicesTool implements MelaTool
{
    public function __construct(private readonly KnowledgeRetriever $retriever)
    {
    }

    public function name(): string
    {
        return 'find_relevant_services';
    }

    public function description(): string
    {
        return 'Finds the Armely services, solutions and products most relevant to a business challenge described in plain language, '
            . 'even when the visitor does not know what the service is called. Use it when a visitor describes a problem, goal or situation '
            . '(for example slow month-end reporting, unmanaged SQL Server databases, wanting to start with AI) so you can explain which capabilities fit and why. '
            . 'Describe the challenge in business terms and include known technologies and context.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'business_challenge' => ['type' => 'string', 'description' => 'The underlying business or technical challenge in plain language.'],
                'technologies' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Technologies or platforms the visitor mentioned.'],
            ],
            'required' => ['business_challenge'],
            'additionalProperties' => false,
        ];
    }

    public function rules(): array
    {
        return [
            'business_challenge' => ['required', 'string', 'min:3', 'max:600'],
            'technologies' => ['sometimes', 'array', 'max:12'],
            'technologies.*' => ['string', 'max:60'],
        ];
    }

    public function execute(array $arguments, ToolContext $context): array
    {
        $query = $arguments['business_challenge'];
        if (!empty($arguments['technologies'])) {
            $query .= ' Technologies: ' . implode(', ', $arguments['technologies']) . '.';
        }

        $search = $this->retriever->search($query, ['service', 'solution', 'product', 'industry', 'case_study'], 8);
        $context->retrievalMs += $search['latency_ms'];
        $context->addSources($search['results']);

        $pages = [];
        foreach ($search['results'] as $result) {
            $url = $result['url'];
            if (!isset($pages[$url])) {
                $pages[$url] = [
                    'name' => $result['title'],
                    'url' => $url,
                    'page_type' => $result['page_type'],
                    'evidence' => [],
                ];
            }
            $pages[$url]['evidence'][] = mb_substr($result['content'], 0, 900);
        }

        return [
            'ok' => true,
            'confidence' => $search['confidence'],
            'note' => $search['confidence'] === 'high'
                ? 'Explain only the matches the evidence supports, and how they relate to the challenge.'
                : 'Matches are weak or missing. Do not claim Armely offers something the evidence does not show.',
            'matches' => array_values(array_slice($pages, 0, 5)),
        ];
    }
}
