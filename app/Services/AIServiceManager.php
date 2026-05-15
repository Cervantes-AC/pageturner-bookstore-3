<?php

namespace App\Services;

use App\Models\AIUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIServiceManager
{
    protected array $config;
    protected array $providers;

    public function __construct()
    {
        $this->config = config('ai');
        $this->providers = $this->config['providers'];
    }

    public function generate(string $prompt, string $feature = 'report_generation', ?string $model = null): array
    {
        $provider = $this->getProviderForFeature($feature);

        return $this->callProvider($provider, $prompt, $feature, $model);
    }

    public function generateWithFallback(string $prompt, string $feature = 'report_generation', ?string $model = null): array
    {
        $chain = $this->config['fallback_chain'];

        foreach ($chain as $provider) {
            if (!$this->isProviderAvailable($provider)) {
                continue;
            }

            try {
                return $this->callProvider($provider, $prompt, $feature, $model);
            } catch (\Exception $e) {
                Log::warning("AI provider {$provider} failed: " . $e->getMessage(), [
                    'feature' => $feature,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        throw new \RuntimeException('All AI providers are unavailable. Please try again later.');
    }

    public function generateWithProvider(string $provider, string $prompt, string $feature = 'report_generation', ?string $model = null): array
    {
        return $this->callProvider($provider, $prompt, $feature, $model);
    }

    public function isAvailable(string $provider): bool
    {
        return $this->isProviderAvailable($provider);
    }

    protected function isProviderAvailable(string $provider): bool
    {
        $config = $this->providers[$provider] ?? null;

        if (!$config || !($config['enabled'] ?? false)) {
            return false;
        }

        if (in_array($provider, ['groq', 'openai', 'openrouter']) && empty($config['api_key'])) {
            return false;
        }

        if ($provider === 'gemini' && empty($config['api_key'])) {
            return false;
        }

        return true;
    }

    protected function getProviderForFeature(string $feature): string
    {
        return $this->config['feature_providers'][$feature] ?? $this->config['default_provider'];
    }

    protected function callProvider(string $provider, string $prompt, string $feature, ?string $model = null): array
    {
        $startTime = microtime(true);

        $result = match ($provider) {
            'groq' => $this->callGroq($prompt, $model),
            'openrouter' => $this->callOpenRouter($prompt, $model),
            'openai' => $this->callOpenAI($prompt),
            'gemini' => $this->callGemini($prompt),
            'ollama' => $this->callOllama($prompt),
            default => throw new \InvalidArgumentException("Unknown provider: {$provider}"),
        };

        $responseTime = (microtime(true) - $startTime) * 1000;

        $this->logUsage($provider, $feature, $prompt, $result, $responseTime);

        return $result;
    }

    protected function callGroq(string $prompt, ?string $model = null): array
    {
        $config = $this->providers['groq'];
        $model = $model ?: $config['model'];

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $config['api_key'],
            'Content-Type' => 'application/json',
        ])->post($config['base_url'] . '/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a data analysis assistant for a bookstore management system. Generate structured reports with clear insights and actionable recommendations. Always respond with valid JSON when requested.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $config['max_tokens'],
            'temperature' => $config['temperature'],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Groq API error: ' . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';
        $tokens = $data['usage']['total_tokens'] ?? 0;

        return [
            'content' => $content,
            'tokens' => $tokens,
            'model' => $data['model'] ?? $config['model'],
            'provider' => 'groq',
        ];
    }

    protected function callOpenAI(string $prompt): array
    {
        $config = $this->providers['openai'];

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $config['api_key'],
            'Content-Type' => 'application/json',
        ])->post($config['base_url'] . '/chat/completions', [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => 'You are a data analysis assistant for a bookstore management system. Generate structured reports with clear insights and actionable recommendations. Always respond with valid JSON when requested.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $config['max_tokens'],
            'temperature' => $config['temperature'],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';
        $tokens = $data['usage']['total_tokens'] ?? 0;

        return [
            'content' => $content,
            'tokens' => $tokens,
            'model' => $data['model'] ?? $config['model'],
            'provider' => 'openai',
        ];
    }

    protected function callOpenRouter(string $prompt, ?string $model = null): array
    {
        $config = $this->providers['openrouter'];
        $model = $model ?: $config['model'];

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $config['api_key'],
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url', 'http://localhost'),
        ])->post($config['base_url'] . '/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a data analysis assistant for a bookstore management system. Generate structured reports with clear insights and actionable recommendations. Always respond with valid JSON when requested.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $config['max_tokens'],
            'temperature' => $config['temperature'],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('OpenRouter API error: ' . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';
        $tokens = $data['usage']['total_tokens'] ?? 0;

        return [
            'content' => $content,
            'tokens' => $tokens,
            'model' => $data['model'] ?? $model,
            'provider' => 'openrouter',
        ];
    }

    protected function callGemini(string $prompt): array
    {
        $config = $this->providers['gemini'];

        $response = Http::withoutVerifying()->post($config['base_url'] . '/models/' . $config['model'] . ':generateContent?key=' . $config['api_key'], [
            'contents' => [
                [
                    'parts' => [
                        ['text' => "You are a data analysis assistant for a bookstore management system. Generate structured reports with clear insights and actionable recommendations.\n\n" . $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'maxOutputTokens' => $config['max_tokens'],
                'temperature' => $config['temperature'],
            ],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Gemini API error: ' . $response->body());
        }

        $data = $response->json();
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $tokens = $data['usageMetadata']['totalTokenCount'] ?? 0;

        return [
            'content' => $content,
            'tokens' => $tokens,
            'model' => $config['model'],
            'provider' => 'gemini',
        ];
    }

    protected function callOllama(string $prompt): array
    {
        $config = $this->providers['ollama'];

        $response = Http::withoutVerifying()->timeout(120)->post($config['base_url'] . '/api/chat', [
            'model' => $config['model'],
            'messages' => [
                ['role' => 'system', 'content' => 'You are a data analysis assistant for a bookstore management system. Generate structured reports with clear insights and actionable recommendations. Always respond with valid JSON when requested.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'options' => [
                'num_predict' => $config['max_tokens'],
                'temperature' => $config['temperature'],
            ],
            'stream' => false,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Ollama API error: ' . $response->body());
        }

        $data = $response->json();
        $content = $data['message']['content'] ?? '';

        return [
            'content' => $content,
            'tokens' => 0,
            'model' => $config['model'],
            'provider' => 'ollama',
        ];
    }

    protected function logUsage(string $provider, string $feature, string $prompt, array $result, float $responseTimeMs): void
    {
        try {
            $costPerToken = $this->config['cost_per_token'][$provider] ?? 0;
            $tokens = $result['tokens'] ?? 0;

            AIUsageLog::create([
                'provider' => $provider,
                'feature' => $feature,
                'prompt_hash' => md5($prompt),
                'response_hash' => md5($result['content'] ?? ''),
                'tokens_used' => $tokens,
                'cost_estimate' => $tokens * $costPerToken,
                'success' => true,
                'user_id' => auth()->id(),
                'model_used' => $result['model'] ?? null,
                'response_time_ms' => $responseTimeMs,
            ]);

            if (config('ai.audit.enabled')) {
                $auditData = [
                    'feature' => $feature,
                    'provider' => $provider,
                    'model' => $result['model'] ?? null,
                    'tokens' => $tokens,
                    'cost' => $tokens * $costPerToken,
                    'response_time_ms' => round($responseTimeMs, 2),
                    'user_id' => auth()->id(),
                ];
                if (config('ai.audit.log_prompts')) {
                    $auditData['prompt_preview'] = substr($prompt, 0, 500);
                }
                if (config('ai.audit.log_responses')) {
                    $auditData['response_preview'] = substr($result['content'] ?? '', 0, 500);
                }
                Log::channel('ai_audit')->info('AI Provider Call', $auditData);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to log AI usage: ' . $e->getMessage());
        }
    }
}
