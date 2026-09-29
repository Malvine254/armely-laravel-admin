<?php

// Central configuration for the Mela AI website assistant.
return [

    'enabled' => env('MELA_ENABLED', true),

    'assistant_name' => 'Mela AI',

    'greeting' => [
        "Hi there! I'm Mela AI, Armely's AI assistant. I can help you explore our enterprise data and AI solutions, guide you through software and licensing options, or connect you directly with a technical specialist.",
        'What challenge are you looking to solve today?',
    ],

    'quick_replies' => [
        '🤖 Enterprise AI & Copilot',
        '📊 Data Modernization & Microsoft Fabric',
        '🔑 Microsoft & Partner Licensing',
        '👤 Speak with a Specialist',
    ],

    'azure_openai' => [
        'endpoint' => env('AZURE_OPENAI_ENDPOINT', ''),
        'api_key' => env('AZURE_OPENAI_API_KEY', ''),
        'api_version' => env('AZURE_OPENAI_API_VERSION', '2024-10-21'),
        'chat_deployment' => env('MELA_CHAT_DEPLOYMENT', env('AZURE_OPENAI_DEPLOYMENT', '')),
        'memory_deployment' => env('MELA_MEMORY_DEPLOYMENT', env('MELA_CHAT_DEPLOYMENT', env('AZURE_OPENAI_DEPLOYMENT', ''))),
        'embedding_deployment' => env('AZURE_OPENAI_EMBEDDING_DEPLOYMENT', ''),
        'embedding_dimensions' => (int) env('MELA_EMBEDDING_DIMENSIONS', 1024),
        'timeout' => (int) env('MELA_MODEL_TIMEOUT', 40),
        'max_retries' => (int) env('MELA_MODEL_MAX_RETRIES', 2),
    ],

    'model' => [
        'temperature' => (float) env('MELA_TEMPERATURE', 0.4),
        'max_output_tokens' => (int) env('MELA_MAX_OUTPUT_TOKENS', 700),
        'max_tool_iterations' => (int) env('MELA_MAX_TOOL_ITERATIONS', 4),
        'memory_max_output_tokens' => (int) env('MELA_MEMORY_MAX_OUTPUT_TOKENS', 900),
    ],

    'memory' => [
        // Recent messages sent verbatim; older ones are folded into the rolling summary.
        'recent_window' => (int) env('MELA_MEMORY_WINDOW', 10),
        'summary_threshold' => (int) env('MELA_SUMMARY_THRESHOLD', 16),
        'max_user_messages_per_conversation' => (int) env('MELA_MAX_MESSAGES_PER_CONVERSATION', 80),
        'conversation_ttl_days' => (int) env('MELA_CONVERSATION_TTL_DAYS', 30),
    ],

    'retrieval' => [
        'top_k' => (int) env('MELA_RETRIEVAL_TOP_K', 6),
        'max_chunks_per_page' => 2,
        'min_score' => (float) env('MELA_RETRIEVAL_MIN_SCORE', 0.30),
        'weak_score' => (float) env('MELA_RETRIEVAL_WEAK_SCORE', 0.40),
        // Score multipliers so evergreen service/product pages outrank blog posts on ties.
        'page_type_weights' => [
            'service' => 1.06,
            'product' => 1.06,
            'solution' => 1.05,
            'about' => 1.04,
            'home' => 1.02,
            'contact' => 1.02,
            'industry' => 1.0,
            'case_study' => 0.98,
            'customer_story' => 0.98,
            'partner' => 0.97,
            'resource' => 0.96,
            'blog' => 0.94,
            'other' => 0.95,
        ],
    ],

    'knowledge' => [
        'base_url' => env('MELA_KNOWLEDGE_BASE_URL', 'https://armely.com'),
        'allowed_hosts' => array_filter(array_map('trim', explode(',', (string) env('MELA_KNOWLEDGE_ALLOWED_HOSTS', 'armely.com,www.armely.com')))),
        'sitemaps' => ['/sitemap.xml'],
        // Pages not always listed in the sitemap.
        'seed_paths' => [
            '/',
            '/company',
            '/contact',
            '/services',
            '/mela-ai',
            '/mela-meeting-assistant',
            '/mela-meeting-assistant-security',
            '/support',
            '/industries',
            '/all-partners',
            '/case-studies',
            '/customer-stories',
        ],
        'exclude_patterns' => [
            '#^/admin#',
            '#^/store#',
            '#^/api/#',
            '#/login#',
            '#/register#',
            '#/password#',
            '#/unsubscribe#',
            '#/download#',
            '#\.(pdf|jpg|jpeg|png|gif|svg|zip|docx?|xlsx?)$#i',
        ],
        // First path segment => page type.
        'page_types' => [
            '' => 'home',
            'services' => 'service',
            'service-details' => 'service',
            'solutions' => 'solution',
            'mela-ai' => 'product',
            'mela-meeting-assistant' => 'product',
            'mela-meeting-assistant-security' => 'product',
            'industries' => 'industry',
            'case-studies' => 'case_study',
            'customer-stories' => 'customer_story',
            'all-partners' => 'partner',
            'white-papers' => 'resource',
            'whitepapers' => 'resource',
            'resources' => 'resource',
            'blog' => 'blog',
            'company' => 'about',
            'career' => 'about',
            'social-impact' => 'about',
            'social-impact-details' => 'about',
            'events' => 'about',
            'contact' => 'contact',
            'support' => 'contact',
        ],
        'max_pages' => (int) env('MELA_KNOWLEDGE_MAX_PAGES', 400),
        'fetch_timeout' => 30,
        'user_agent' => 'ArmelyMelaIndexer/1.0 (+https://armely.com)',
        'chunk_target_chars' => 1100,
        'chunk_max_chars' => 1800,
        'chunk_min_chars' => 160,
        'embedding_batch_size' => 16,
        // off | daily | weekly
        'schedule' => env('MELA_REINDEX_SCHEDULE', 'daily'),
    ],

    'rate_limits' => [
        'per_minute' => (int) env('MELA_RATE_PER_MINUTE', 12),
        'per_day' => (int) env('MELA_RATE_PER_DAY', 200),
        'conversations_per_hour' => (int) env('MELA_CONVERSATIONS_PER_HOUR', 20),
    ],

    'input' => [
        'max_message_chars' => (int) env('MELA_MAX_MESSAGE_CHARS', 2000),
    ],

    'escalation' => [
        'max_per_conversation' => 3,
        'required_fields' => ['name', 'email'],
        'send_visitor_confirmation' => env('MELA_SEND_VISITOR_CONFIRMATION', true),
        'lead_service_type_prefix' => 'Mela AI Website Assistant',
    ],

    'contact_fallback' => [
        'url' => '/contact',
        'email' => 'ask.me@armely.com',
    ],
];
