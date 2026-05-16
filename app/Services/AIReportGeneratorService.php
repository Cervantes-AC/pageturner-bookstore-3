<?php

namespace App\Services;

use App\Models\AIReport;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIReportGeneratorService
{
    protected AIServiceManager $aiManager;

    protected array $dbSchema = [];

    public function __construct(AIServiceManager $aiManager)
    {
        $this->aiManager = $aiManager;
        $this->dbSchema = $this->loadDatabaseSchema();
    }

    protected function loadDatabaseSchema(): array
    {
        return [
            'books' => [
                'columns' => ['id', 'category_id', 'title', 'author', 'publisher', 'publication_year', 'isbn', 'price', 'stock_quantity', 'format', 'description', 'cover_image', 'is_active', 'published_at', 'created_at'],
                'description' => 'Book catalog with pricing, stock, and classification',
                'count' => Book::count(),
            ],
            'categories' => [
                'columns' => ['id', 'name', 'description'],
                'description' => 'Book categories/genres',
                'count' => \App\Models\Category::count(),
            ],
            'orders' => [
                'columns' => ['id', 'user_id', 'total_amount', 'status', 'shipping_name', 'shipping_address', 'created_at'],
                'description' => 'Customer orders with amounts and statuses (pending, processing, completed, cancelled)',
                'count' => Order::count(),
            ],
            'order_items' => [
                'columns' => ['id', 'order_id', 'book_id', 'quantity', 'unit_price'],
                'description' => 'Individual line items within orders',
            ],
            'users' => [
                'columns' => ['id', 'name', 'email', 'role', 'created_at'],
                'description' => 'User accounts with roles (admin, customer, premium)',
                'count' => User::count(),
            ],
            'reviews' => [
                'columns' => ['id', 'user_id', 'book_id', 'rating', 'comment', 'created_at'],
                'description' => 'Book reviews with ratings (1-5)',
                'count' => Review::count(),
            ],
        ];
    }

    protected ?string $modelOverride = null;
    protected ?string $providerOverride = null;

    public function setModel(?string $model): static
    {
        $this->modelOverride = $model;
        return $this;
    }

    public function setProvider(?string $provider): static
    {
        $this->providerOverride = $provider;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->modelOverride;
    }

    public function getProvider(): ?string
    {
        return $this->providerOverride;
    }

    public function generateReport(int $userId, string $query, bool $sync = true): AIReport
    {
        $report = AIReport::create([
            'user_id' => $userId,
            'query' => $query,
            'title' => $this->generateTitle($query),
            'status' => $sync ? 'generating' : 'pending',
        ]);

        if ($sync) {
            $this->processReport($report);
        }

        return $report;
    }

    public function processReport(AIReport $report): void
    {
        try {
            $report->update(['status' => 'generating']);

            $retrievedData = $this->retrieveRelevantData($report->query);
            
            Log::info('Retrieved data for report', [
                'report_id' => $report->id,
                'data_keys' => array_keys($retrievedData),
                'data_size' => strlen(json_encode($retrievedData)),
            ]);

            $prompt = $this->buildReportPrompt($report->query, $retrievedData);

            if ($this->providerOverride && $this->aiManager->isAvailable($this->providerOverride)) {
                $result = $this->aiManager->generateWithProvider($this->providerOverride, $prompt, 'report_generation', $this->modelOverride);
            } else {
                $result = $this->aiManager->generateWithFallback($prompt, 'report_generation', $this->modelOverride);
            }

            $this->modelOverride = null;
            $this->providerOverride = null;

            Log::info('AI response received', [
                'report_id' => $report->id,
                'provider' => $result['provider'],
                'model' => $result['model'] ?? 'unknown',
                'tokens' => $result['tokens'] ?? 0,
                'response_length' => strlen($result['content']),
            ]);

            $parsed = $this->parseAIResponse($result['content']);

            Log::info('Response parsed', [
                'report_id' => $report->id,
                'has_summary' => !empty($parsed['summary']),
                'insights_count' => count($parsed['insights'] ?? []),
                'recommendations_count' => count($parsed['recommendations'] ?? []),
            ]);

            $reportData = $retrievedData;
            if (!empty($parsed['introduction'])) {
                $reportData['_introduction'] = $parsed['introduction'];
            }
            if (!empty($parsed['conclusion'])) {
                $reportData['_conclusion'] = $parsed['conclusion'];
            }

            $report->update([
                'title' => $parsed['title'] ?? $report->title,
                'summary' => $parsed['summary'] ?? null,
                'data' => $reportData,
                'insights' => $parsed['insights'] ?? [],
                'recommendations' => $parsed['recommendations'] ?? [],
                'ai_prompt' => $prompt,
                'ai_raw_response' => $result['content'],
                'provider_used' => $result['provider'],
                'model_used' => $result['model'] ?? null,
                'tokens_used' => $result['tokens'] ?? 0,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            Log::info('Report generation completed', [
                'report_id' => $report->id,
                'status' => 'completed',
            ]);

        } catch (\Exception $e) {
            Log::error('AI report generation failed: ' . $e->getMessage(), [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }

    protected function retrieveRelevantData(string $query): array
    {
        $data = [];
        $lowerQuery = strtolower($query);

        $needs = $this->classifyQuery($lowerQuery);

        if ($needs['sales'] || $needs['overview']) {
            $data['sales_summary'] = $this->getSalesSummary();
        }

        if ($needs['inventory'] || $needs['overview']) {
            $data['inventory_summary'] = $this->getInventorySummary();
        }

        if ($needs['users'] || $needs['overview']) {
            $data['user_summary'] = $this->getUserSummary();
        }

        if ($needs['reviews'] || $needs['overview']) {
            $data['review_summary'] = $this->getReviewSummary();
        }

        if ($needs['trends'] || $needs['overview']) {
            $data['trends'] = $this->getTrends();
        }

        if ($needs['orders'] || $needs['overview']) {
            $data['order_summary'] = $this->getOrderSummary();
        }

        if ($needs['categories'] || $needs['overview']) {
            $data['category_performance'] = $this->getCategoryPerformance();
        }

        if ($needs['bestsellers']) {
            $data['bestsellers'] = $this->getBestsellers();
        }

        if ($needs['inventory_alerts']) {
            $data['low_stock'] = $this->getLowStockBooks();
        }

        return $data;
    }

    protected function classifyQuery(string $query): array
    {
        return [
            'overview' => $this->matchesAny($query, [
                'overview', 'summary', 'all', 'everything', 'general', 'overall',
                'state of', 'health', 'performance', 'business',
            ]) && !$this->matchesAny($query, ['specific', 'particular', 'exact']),

            'sales' => $this->matchesAny($query, [
                'sale', 'revenue', 'income', 'earnings', 'profit', 'financial',
                'money', 'selling', 'sold', 'total amount', 'sales trend',
            ]),

            'inventory' => $this->matchesAny($query, [
                'inventory', 'stock', 'quantity', 'supply', 'warehouse',
                'products available', 'books in stock',
            ]),

            'users' => $this->matchesAny($query, [
                'user', 'customer', 'registration', 'signup', 'account',
                'member', 'new users',
            ]),

            'reviews' => $this->matchesAny($query, [
                'review', 'rating', 'feedback', 'comment', 'sentiment',
            ]),

            'trends' => $this->matchesAny($query, [
                'trend', 'growth', 'monthly', 'weekly', 'daily', 'over time',
                'comparison', 'change', 'increase', 'decrease',
            ]),

            'orders' => $this->matchesAny($query, [
                'order', 'purchase', 'transaction', 'checkout', 'buying',
            ]),

            'categories' => $this->matchesAny($query, [
                'category', 'genre', 'categor', 'department', 'section',
            ]),

            'bestsellers' => $this->matchesAny($query, [
                'bestseller', 'top selling', 'most popular', 'top rated',
                'highest selling', 'popular',
            ]),

            'inventory_alerts' => $this->matchesAny($query, [
                'low stock', 'out of stock', 'restock', 'reorder', 'shortage',
                'running out', 'inventory alert',
            ]),
        ];
    }

    protected function matchesAny(string $query, array $terms): bool
    {
        foreach ($terms as $term) {
            if (str_contains($query, $term)) {
                return true;
            }
        }
        return false;
    }

    protected function generateTitle(string $query): string
    {
        $query = strtolower(trim($query));
        $query = preg_replace('/[^a-z0-9\s]/', '', $query);
        $query = substr($query, 0, 60);

        if (strlen($query) > 50) {
            $query = substr($query, 0, 47) . '...';
        }

        return 'Report: ' . ucfirst($query);
    }

    protected function buildReportPrompt(string $query, array $data): string
    {
        $schemaLines = [];
        foreach ($this->dbSchema as $table => $info) {
            $schemaLines[] = "- {$table} (" . implode(', ', $info['columns']) . "): {$info['description']}";
        }
        $schemaStr = implode("\n", $schemaLines);

        $dataJson = json_encode($data, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a business intelligence analyst for the PageTurner Bookstore system. Generate a professional formal business report based on the provided data.

ADMIN QUERY: {$query}

DATABASE SCHEMA:
{$schemaStr}

RETRIEVED DATA:
{$dataJson}

CRITICAL INSTRUCTIONS - You MUST respond with ONLY a valid JSON object (no markdown, no code blocks, just raw JSON) containing these exact fields:

{
  "title": "A professional report title (max 12 words, e.g. PageTurner Quarterly Sales Performance Report)",
  "executive_summary": "2-3 paragraph executive overview covering the most critical findings, written in formal business language.",
  "introduction": "1 paragraph explaining the purpose, scope, and period covered by this report.",
  "findings": [
    {
      "section": "Section heading (e.g. Sales Performance, Inventory Analysis, User Activity)",
      "content": "2-4 sentences of detailed analysis with specific numbers and percentages",
      "status": "positive or warning or critical"
    }
  ],
  "recommendations": [
    {
      "action": "Specific actionable recommendation",
      "rationale": "Brief explanation of why this action is needed",
      "priority": "high or medium or low"
    }
  ],
  "conclusion": "1 paragraph synthesizing the findings and providing forward-looking perspective."
}

IMPORTANT:
- Write in formal business tone
- Use specific data points (revenue, percentages, counts) from the RETRIEVED DATA
- Be accurate—only state what the data supports
- Return ONLY valid JSON, no additional text before or after
- Do not use markdown code blocks
- Ensure all quotes are properly escaped
PROMPT;
    }

    protected function parseAIResponse(string $content): array
    {
        $content = trim($content);

        // Try to extract JSON from the response
        $json = $this->extractJson($content);

        if ($json) {
            $parsed = json_decode($json, true);
            if (is_array($parsed)) {
                return [
                    'title' => $parsed['title'] ?? 'AI-Generated Report',
                    'summary' => $this->cleanText($parsed['executive_summary'] ?? $parsed['summary'] ?? ''),
                    'introduction' => $this->cleanText($parsed['introduction'] ?? ''),
                    'insights' => $this->cleanInsights($parsed['findings'] ?? $parsed['insights'] ?? []),
                    'recommendations' => $this->cleanRecommendations($parsed['recommendations'] ?? []),
                    'conclusion' => $this->cleanText($parsed['conclusion'] ?? ''),
                ];
            }
        }

        // If JSON extraction failed, try to extract text sections from the response
        Log::warning('Failed to parse AI response as JSON, attempting text extraction', [
            'content_preview' => substr($content, 0, 200),
        ]);

        // Extract sections from plain text response
        $summary = $this->extractSection($content, ['executive summary', 'summary', 'overview']);
        $introduction = $this->extractSection($content, ['introduction', 'purpose']);
        $conclusion = $this->extractSection($content, ['conclusion', 'summary']);
        
        // Extract findings/insights from bullet points or numbered lists
        $insights = $this->extractListItems($content, ['findings', 'insights', 'analysis']);
        $recommendations = $this->extractListItems($content, ['recommendations', 'actions', 'suggestions']);

        return [
            'title' => 'AI-Generated Report',
            'summary' => $summary ?: $this->cleanText(substr($content, 0, 500)),
            'introduction' => $introduction,
            'insights' => $insights,
            'recommendations' => $recommendations,
            'conclusion' => $conclusion,
        ];
    }

    protected function extractSection(string $content, array $keywords): string
    {
        $lines = explode("\n", $content);
        $capturing = false;
        $section = [];

        foreach ($lines as $line) {
            $lowerLine = strtolower($line);
            
            // Check if this line contains a section header
            foreach ($keywords as $keyword) {
                if (str_contains($lowerLine, $keyword)) {
                    $capturing = true;
                    continue 2;
                }
            }

            // If we're capturing and hit another header, stop
            if ($capturing && preg_match('/^#+\s|^[A-Z][^a-z]*:/', $line) && !empty($section)) {
                break;
            }

            // Capture content
            if ($capturing && !empty(trim($line))) {
                $section[] = trim($line);
            }
        }

        return $this->cleanText(implode(' ', $section));
    }

    protected function extractListItems(string $content, array $keywords): array
    {
        $lines = explode("\n", $content);
        $items = [];
        $capturing = false;

        foreach ($lines as $line) {
            $lowerLine = strtolower($line);
            
            // Check if this line contains a section header
            foreach ($keywords as $keyword) {
                if (str_contains($lowerLine, $keyword)) {
                    $capturing = true;
                    continue 2;
                }
            }

            // If we're capturing and hit another header, stop
            if ($capturing && preg_match('/^#+\s|^[A-Z][^a-z]*:/', $line) && !empty($items)) {
                break;
            }

            // Extract bullet points or numbered items
            if ($capturing && preg_match('/^[\s]*[-•*]\s+(.+)$|^[\s]*\d+\.\s+(.+)$/', $line, $matches)) {
                $itemText = $matches[1] ?? $matches[2];
                $items[] = [
                    'section' => 'Finding',
                    'content' => $this->cleanText($itemText),
                    'status' => 'info',
                ];
            }
        }

        return $items;
    }

    protected function cleanText(string $text): string
    {
        // Remove markdown code block markers
        $text = preg_replace('/```(?:json|markdown|text)?\s*\n?/i', '', $text);
        $text = preg_replace('/```\s*$/i', '', $text);
        
        // Remove leading/trailing whitespace
        $text = trim($text);
        
        return $text;
    }

    protected function cleanInsights(array $insights): array
    {
        return array_map(function ($insight) {
            return [
                'section' => $this->cleanText($insight['section'] ?? $insight['finding'] ?? ''),
                'content' => $this->cleanText($insight['content'] ?? $insight['finding'] ?? ''),
                'status' => $insight['status'] ?? 'info',
            ];
        }, $insights);
    }

    protected function cleanRecommendations(array $recommendations): array
    {
        return array_map(function ($rec) {
            return [
                'action' => $this->cleanText($rec['action'] ?? $rec['recommendation'] ?? ''),
                'rationale' => $this->cleanText($rec['rationale'] ?? ''),
                'priority' => $rec['priority'] ?? 'medium',
            ];
        }, $recommendations);
    }

    protected function extractJson(string $text): ?string
    {
        // First, try to extract from markdown code blocks
        if (preg_match('/```(?:json)?\s*\n?(.*?)\n?```/s', $text, $matches)) {
            $candidate = trim($matches[1]);
            if (str_starts_with($candidate, '{') && $this->hasBalancedBraces($candidate)) {
                $decoded = json_decode($candidate, true);
                if ($decoded !== null) {
                    return $candidate;
                }
            }
        }

        // If that fails, try to find JSON object directly
        $start = strpos($text, '{');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escaped = false;
        $bestMatch = null;
        $bestDepth = 0;
        
        for ($i = $start; $i < strlen($text); $i++) {
            $ch = $text[$i];
            
            // Handle escape sequences
            if ($escaped) {
                $escaped = false;
                continue;
            }
            
            if ($ch === '\\') {
                $escaped = true;
                continue;
            }
            
            // Handle string boundaries
            if ($ch === '"') {
                $inString = !$inString;
                continue;
            }
            
            // Count braces only outside strings
            if (!$inString) {
                if ($ch === '{') {
                    $depth++;
                } elseif ($ch === '}') {
                    $depth--;
                    if ($depth === 0) {
                        $json = substr($text, $start, $i - $start + 1);
                        // Validate it's actually valid JSON
                        $decoded = json_decode($json, true);
                        if ($decoded !== null && is_array($decoded)) {
                            return $json;
                        }
                        // If this didn't work, try to find another JSON object
                        $start = strpos($text, '{', $i + 1);
                        if ($start === false) {
                            break;
                        }
                        $depth = 0;
                        $i = $start - 1;
                    }
                }
            }
        }

        return null;
    }

    protected function hasBalancedBraces(string $text): bool
    {
        $depth = 0;
        $inString = false;
        for ($i = 0; $i < strlen($text); $i++) {
            if ($text[$i] === '"' && ($i === 0 || $text[$i - 1] !== '\\')) {
                $inString = !$inString;
            }
            if (!$inString) {
                if ($text[$i] === '{') {
                    $depth++;
                } elseif ($text[$i] === '}') {
                    $depth--;
                }
            }
        }
        return $depth === 0;
    }

    protected function getSalesSummary(): array
    {
        return [
            'total_revenue' => (float) Order::whereIn('status', ['completed', 'processing'])->sum('total_amount'),
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'average_order_value' => (float) Order::whereIn('status', ['completed', 'processing'])->avg('total_amount') ?? 0,
            'revenue_this_month' => (float) Order::whereIn('status', ['completed', 'processing'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'orders_this_month' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    protected function getInventorySummary(): array
    {
        return [
            'total_books' => Book::count(),
            'active_books' => Book::where('is_active', true)->count(),
            'total_stock_units' => (int) Book::sum('stock_quantity'),
            'average_price' => (float) Book::where('is_active', true)->avg('price'),
            'low_stock_count' => Book::where('stock_quantity', '>', 0)->where('stock_quantity', '<', 10)->count(),
            'out_of_stock_count' => Book::where('stock_quantity', '=', 0)->count(),
            'total_inventory_value' => (float) Book::where('is_active', true)
                ->select(DB::raw('SUM(price * stock_quantity) as total'))->value('total') ?? 0,
        ];
    }

    protected function getUserSummary(): array
    {
        return [
            'total_users' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'customers' => User::where('role', 'customer')->count(),
            'premium' => User::where('role', 'premium')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
        ];
    }

    protected function getReviewSummary(): array
    {
        return [
            'total_reviews' => Review::count(),
            'average_rating' => (float) Review::avg('rating') ?? 0,
            'rating_distribution' => Review::selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->orderBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
        ];
    }

    protected function getTrends(): array
    {
        $monthlyRevenue = Order::whereIn('status', ['completed', 'processing'])
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->toArray();

        return [
            'monthly_revenue' => $monthlyRevenue,
        ];
    }

    protected function getOrderSummary(): array
    {
        return [
            'status_breakdown' => Order::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'total_shipped' => Order::whereIn('status', ['completed', 'processing'])->count(),
        ];
    }

    protected function getCategoryPerformance(): array
    {
        return \App\Models\Category::withCount(['books' => function ($q) {
            $q->where('is_active', true);
        }])->orderByDesc('books_count')->limit(10)->get()->map(function ($cat) {
            return [
                'name' => $cat->name,
                'book_count' => $cat->books_count,
            ];
        })->toArray();
    }

    protected function getBestsellers(): array
    {
        return OrderItem::selectRaw('book_id, SUM(quantity) as total_sold')
            ->groupBy('book_id')
            ->orderByDesc('total_sold')
            ->with('book:id,title,author,price')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'title' => $item->book?->title ?? 'Unknown',
                'author' => $item->book?->author ?? 'Unknown',
                'total_sold' => (int) $item->total_sold,
            ])
            ->toArray();
    }

    protected function getLowStockBooks(): array
    {
        return Book::where('stock_quantity', '<', 10)
            ->where('is_active', true)
            ->orderBy('stock_quantity')
            ->limit(20)
            ->get(['id', 'title', 'author', 'stock_quantity', 'price'])
            ->toArray();
    }
}
