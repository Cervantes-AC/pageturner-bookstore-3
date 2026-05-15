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

            $prompt = $this->buildReportPrompt($report->query, $retrievedData);

            if ($this->providerOverride && $this->aiManager->isAvailable($this->providerOverride)) {
                $result = $this->aiManager->generateWithProvider($this->providerOverride, $prompt, 'report_generation', $this->modelOverride);
            } else {
                $result = $this->aiManager->generateWithFallback($prompt, 'report_generation', $this->modelOverride);
            }

            $this->modelOverride = null;
            $this->providerOverride = null;

            $parsed = $this->parseAIResponse($result['content']);

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

        } catch (\Exception $e) {
            Log::error('AI report generation failed: ' . $e->getMessage(), [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
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

INSTRUCTIONS - Respond with ONLY a valid JSON object containing these fields:

1. "title": A professional report title (max 12 words, e.g. "PageTurner Quarterly Sales Performance Report")

2. "executive_summary": 2-3 paragraph executive overview covering the most critical findings, written in formal business language.

3. "introduction": 1 paragraph explaining the purpose, scope, and period covered by this report.

4. "findings": An array of 3-6 finding objects, each with:
   - "section": Section heading (e.g. "Sales Performance", "Inventory Analysis", "User Activity")
   - "content": 2-4 sentences of detailed analysis with specific numbers and percentages
   - "status": "positive", "warning", or "critical"

5. "recommendations": An array of 2-4 recommendation objects, each with:
   - "action": Specific actionable recommendation
   - "rationale": Brief explanation of why this action is needed
   - "priority": "high", "medium", or "low"

6. "conclusion": 1 paragraph synthesizing the findings and providing forward-looking perspective.

Write in formal business tone. Use specific data points (revenue, percentages, counts) throughout. Be accurate—only state what the data supports.
PROMPT;
    }

    protected function parseAIResponse(string $content): array
    {
        $content = trim($content);

        $json = $this->extractJson($content);

        if ($json) {
            $parsed = json_decode($json, true);
            if (is_array($parsed)) {
                return [
                    'title' => $parsed['title'] ?? 'AI-Generated Report',
                    'summary' => $parsed['executive_summary'] ?? $parsed['summary'] ?? $content,
                    'introduction' => $parsed['introduction'] ?? '',
                    'insights' => $parsed['findings'] ?? $parsed['insights'] ?? [],
                    'recommendations' => $parsed['recommendations'] ?? [],
                    'conclusion' => $parsed['conclusion'] ?? '',
                ];
            }
        }

        return [
            'title' => 'AI-Generated Report',
            'summary' => $content,
            'introduction' => '',
            'insights' => [],
            'recommendations' => [],
            'conclusion' => '',
        ];
    }

    protected function extractJson(string $text): ?string
    {
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $text, $matches)) {
            return $matches[1];
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            return substr($text, $start, $end - $start + 1);
        }

        return null;
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
