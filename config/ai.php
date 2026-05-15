<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    */
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'groq'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    */
    'fallback_enabled' => env('AI_FALLBACK_ENABLED', true),

    'fallback_chain' => [
        env('AI_DEFAULT_PROVIDER', 'groq'),
        'openrouter',
        'gemini',
        'ollama',
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider Credentials & Endpoints
    |--------------------------------------------------------------------------
    */
    'providers' => [

        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'base_url' => 'https://api.groq.com/openai/v1',
            'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
            'max_tokens' => 4096,
            'temperature' => 0.3,
            'enabled' => true,
        ],

        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => 'https://api.openai.com/v1',
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'max_tokens' => 4096,
            'temperature' => 0.3,
            'enabled' => (bool) env('OPENAI_API_KEY'),
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
            'max_tokens' => 4096,
            'temperature' => 0.3,
            'enabled' => (bool) env('GEMINI_API_KEY'),
        ],

        'openrouter' => [
            'api_key' => env('OPENROUTER_API_KEY'),
            'base_url' => 'https://openrouter.ai/api/v1',
            'model' => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),
            'max_tokens' => 4096,
            'temperature' => 0.3,
            'enabled' => (bool) env('OPENROUTER_API_KEY'),
        ],

        'ollama' => [
            'enabled' => env('OLLAMA_ENABLED', false),
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model' => env('OLLAMA_MODEL', 'llama3.2'),
            'max_tokens' => 4096,
            'temperature' => 0.3,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature-to-Provider Mapping
    |--------------------------------------------------------------------------
    */
    'feature_providers' => [
        'report_generation' => env('AI_DEFAULT_PROVIDER', 'groq'),
        'chat' => env('AI_DEFAULT_PROVIDER', 'groq'),
        'content_generation' => env('AI_DEFAULT_PROVIDER', 'groq'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limits & Cost Thresholds
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'groq' => [
            'max_requests_per_minute' => 30,
            'max_tokens_per_day' => 500000,
        ],
        'openai' => [
            'max_requests_per_day' => 200,
            'max_tokens_per_day' => 200000,
        ],
        'gemini' => [
            'max_requests_per_day' => 1500,
        ],
    ],

    'available_models' => [
        'groq' => [
            'llama-3.3-70b-versatile' => 'Llama 3.3 70B (Default)',
            'deepseek-r1-distill-llama-70b' => 'DeepSeek R1 70B',
        ],
        'openrouter' => [
            'openai/gpt-4o-mini' => 'GPT-4o Mini',
            'openai/gpt-4o' => 'GPT-4o',
            'anthropic/claude-3.5-sonnet' => 'Claude 3.5 Sonnet',
            'google/gemini-2.0-flash-001' => 'Gemini 2.0 Flash',
            'meta-llama/llama-3.3-70b-instruct' => 'Llama 3.3 70B',
            'deepseek/deepseek-r1' => 'DeepSeek R1',
        ],
    ],

    'cost_per_token' => [
        'groq' => 0.0000001,
        'openai' => 0.00000015,
        'openrouter' => 0.00000015,
        'gemini' => 0.0000001,
        'ollama' => 0,
    ],

    'cost_alert_threshold' => env('AI_COST_ALERT_THRESHOLD', 0.01),

    /*
    |--------------------------------------------------------------------------
    | Audit Settings
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => true,
        'channel' => 'ai_audit',
        'log_prompts' => env('AI_AUDIT_LOG_PROMPTS', false),
        'log_responses' => env('AI_AUDIT_LOG_RESPONSES', false),
    ],
];
