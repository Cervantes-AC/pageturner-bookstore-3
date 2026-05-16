# AI Report Generation - Implementation Details

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    Admin Controller                          │
│              (AIReportController.php)                        │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              Report Generator Service                        │
│         (AIReportGeneratorService.php)                       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ 1. Classify Query                                    │   │
│  │ 2. Retrieve Relevant Data                            │   │
│  │ 3. Build AI Prompt                                   │   │
│  │ 4. Call AI Provider                                  │   │
│  │ 5. Parse Response                                    │   │
│  │ 6. Store Report                                      │   │
│  └──────────────────────────────────────────────────────┘   │
└────────────────────┬────────────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        ▼                         ▼
┌──────────────────┐    ┌──────────────────┐
│ AI Service       │    │ Database         │
│ Manager          │    │ Repository       │
│ (Multi-Provider) │    │ (Data Retrieval) │
└──────────────────┘    └──────────────────┘
        │
        ├─► Groq (Primary)
        ├─► OpenRouter (Fallback 1)
        ├─► Gemini (Fallback 2)
        └─► Ollama (Fallback 3)
```

## Data Flow

### 1. Query Classification

**Input**: User query string  
**Process**: Pattern matching against keywords  
**Output**: Classification array with boolean flags

```php
$needs = $this->classifyQuery($lowerQuery);
// Returns:
[
    'overview' => false,
    'sales' => true,
    'inventory' => false,
    'users' => false,
    'reviews' => false,
    'trends' => true,
    'orders' => true,
    'categories' => false,
    'bestsellers' => false,
    'inventory_alerts' => false,
]
```

**Keywords Matched**:
- Sales: "sale", "revenue", "income", "earnings", "profit", "financial"
- Inventory: "inventory", "stock", "quantity", "supply", "warehouse"
- Users: "user", "customer", "registration", "signup", "account"
- Trends: "trend", "growth", "monthly", "weekly", "daily", "over time"
- Orders: "order", "purchase", "transaction", "checkout"
- Bestsellers: "bestseller", "top selling", "most popular", "top rated"
- Alerts: "low stock", "out of stock", "restock", "reorder", "shortage"

### 2. Data Retrieval

**Process**: Conditional data fetching based on classification

```php
if ($needs['sales'] || $needs['overview']) {
    $data['sales_summary'] = $this->getSalesSummary();
}
// Retrieves: total_revenue, total_orders, completed_orders, 
//            pending_orders, cancelled_orders, average_order_value,
//            revenue_this_month, orders_this_month

if ($needs['inventory'] || $needs['overview']) {
    $data['inventory_summary'] = $this->getInventorySummary();
}
// Retrieves: total_books, active_books, total_stock_units,
//            average_price, low_stock_count, out_of_stock_count,
//            total_inventory_value
```

**Data Methods**:
- `getSalesSummary()`: Revenue, orders, AOV
- `getInventorySummary()`: Stock levels, valuation
- `getUserSummary()`: User counts by role
- `getReviewSummary()`: Ratings, distribution
- `getTrends()`: Monthly revenue trends
- `getOrderSummary()`: Order status breakdown
- `getCategoryPerformance()`: Top categories
- `getBestsellers()`: Top 10 books by quantity
- `getLowStockBooks()`: Books with stock < 10

### 3. Prompt Building

**Input**: Query + Retrieved Data  
**Process**: Template-based prompt construction  
**Output**: Formatted prompt with schema and data

```
You are a business intelligence analyst...

ADMIN QUERY: {user_query}

DATABASE SCHEMA:
- books (id, title, author, ...): Book catalog...
- orders (id, user_id, total_amount, ...): Customer orders...
- users (id, name, email, role, ...): User accounts...

RETRIEVED DATA:
{
  "sales_summary": {
    "total_revenue": 45678.50,
    "total_orders": 234,
    "average_order_value": 195.21,
    ...
  },
  "inventory_summary": {
    "total_books": 2345,
    "total_stock_units": 15234,
    ...
  }
}

CRITICAL INSTRUCTIONS - You MUST respond with ONLY a valid JSON object...
```

### 4. AI Provider Call

**Process**: Multi-provider with fallback

```php
if ($this->providerOverride && $this->aiManager->isAvailable($this->providerOverride)) {
    $result = $this->aiManager->generateWithProvider($this->providerOverride, $prompt, 'report_generation', $this->modelOverride);
} else {
    $result = $this->aiManager->generateWithFallback($prompt, 'report_generation', $this->modelOverride);
}
```

**Fallback Chain**:
1. Try Groq (free tier, fast)
2. If fails → Try OpenRouter (multi-model)
3. If fails → Try Gemini (Google)
4. If fails → Try Ollama (local)
5. If all fail → Throw exception

**Result Structure**:
```php
[
    'content' => '{"title": "...", "findings": [...]}',
    'tokens' => 1234,
    'model' => 'llama-3.3-70b-versatile',
    'provider' => 'groq',
]
```

### 5. Response Parsing

**Input**: AI response string  
**Process**: Multi-strategy extraction

```
Strategy 1: Extract JSON from markdown code blocks
  └─ Look for ```json ... ```
  └─ Validate JSON structure
  └─ Return if valid

Strategy 2: Extract JSON object directly
  └─ Find first { character
  └─ Track brace depth
  └─ Extract complete JSON object
  └─ Validate with json_decode()
  └─ Return if valid

Strategy 3: Extract text sections (Fallback)
  └─ Find "executive summary" section
  └─ Find "findings" bullet points
  └─ Find "recommendations" list
  └─ Convert to structured format
  └─ Return extracted data
```

**Output Structure**:
```php
[
    'title' => 'PageTurner Sales Performance Report',
    'summary' => 'Executive summary text...',
    'introduction' => 'Introduction text...',
    'insights' => [
        [
            'section' => 'Sales Performance',
            'content' => 'Detailed analysis with numbers...',
            'status' => 'positive',
        ],
        ...
    ],
    'recommendations' => [
        [
            'action' => 'Increase marketing spend',
            'rationale' => 'Strong ROI on current campaigns',
            'priority' => 'high',
        ],
        ...
    ],
    'conclusion' => 'Conclusion text...',
]
```

### 6. Report Storage

**Database Fields**:
```php
[
    'user_id' => 42,
    'title' => 'PageTurner Sales Performance Report',
    'query' => 'Show me sales trends for the last quarter',
    'summary' => 'Executive summary...',
    'data' => [
        'sales_summary' => [...],
        'inventory_summary' => [...],
        '_introduction' => '...',
        '_conclusion' => '...',
    ],
    'insights' => [
        ['section' => '...', 'content' => '...', 'status' => '...'],
        ...
    ],
    'recommendations' => [
        ['action' => '...', 'rationale' => '...', 'priority' => '...'],
        ...
    ],
    'ai_prompt' => 'Full prompt sent to AI...',
    'ai_raw_response' => 'Raw response from AI...',
    'provider_used' => 'groq',
    'model_used' => 'llama-3.3-70b-versatile',
    'tokens_used' => 1234,
    'status' => 'completed',
    'completed_at' => '2026-05-16 10:30:47',
]
```

## Key Methods

### `classifyQuery(string $query): array`
Analyzes user query to determine what data is needed.

**Parameters**:
- `$query`: User's natural language query

**Returns**: Array of boolean flags indicating data needs

**Example**:
```php
$needs = $this->classifyQuery('show me sales trends');
// ['sales' => true, 'trends' => true, ...]
```

### `retrieveRelevantData(string $query): array`
Fetches data from database based on query classification.

**Parameters**:
- `$query`: User's natural language query

**Returns**: Array of aggregated data

**Example**:
```php
$data = $this->retrieveRelevantData('show me sales trends');
// ['sales_summary' => [...], 'trends' => [...]]
```

### `buildReportPrompt(string $query, array $data): string`
Constructs the prompt to send to AI provider.

**Parameters**:
- `$query`: User's natural language query
- `$data`: Retrieved data from database

**Returns**: Formatted prompt string

**Example**:
```php
$prompt = $this->buildReportPrompt($query, $data);
// Returns: "You are a business intelligence analyst..."
```

### `parseAIResponse(string $content): array`
Extracts structured data from AI response.

**Parameters**:
- `$content`: Raw response from AI provider

**Returns**: Structured array with title, summary, insights, recommendations

**Strategies**:
1. JSON extraction from markdown
2. Direct JSON object extraction
3. Text section extraction (fallback)
4. Bullet point parsing (fallback)

**Example**:
```php
$parsed = $this->parseAIResponse($aiResponse);
// ['title' => '...', 'insights' => [...], 'recommendations' => [...]]
```

### `extractJson(string $text): ?string`
Extracts JSON object from text.

**Parameters**:
- `$text`: Text containing JSON

**Returns**: JSON string or null

**Process**:
1. Try markdown code blocks
2. Try direct JSON extraction
3. Track brace depth
4. Validate with json_decode()

### `extractSection(string $content, array $keywords): string`
Extracts a text section by keywords.

**Parameters**:
- `$content`: Full text content
- `$keywords`: Keywords to search for

**Returns**: Extracted section text

**Example**:
```php
$summary = $this->extractSection($content, ['executive summary', 'summary']);
```

### `extractListItems(string $content, array $keywords): array`
Extracts bullet points or numbered items.

**Parameters**:
- `$content`: Full text content
- `$keywords`: Keywords to search for

**Returns**: Array of extracted items

**Example**:
```php
$findings = $this->extractListItems($content, ['findings', 'insights']);
// [['section' => 'Finding', 'content' => '...', 'status' => 'info'], ...]
```

## Error Handling

### Scenario 1: Provider Unavailable
```
AI Provider Call → Fails
↓
Try Next Provider in Fallback Chain
↓
If All Fail → Throw RuntimeException
↓
Catch in processReport() → Mark as Failed
```

### Scenario 2: Invalid JSON Response
```
AI Response → JSON Extraction Fails
↓
Try Text Extraction Fallback
↓
Extract Sections and Bullet Points
↓
Return Partial Data (Better than Nothing)
```

### Scenario 3: Database Error
```
Data Retrieval → Database Error
↓
Catch Exception → Log Error
↓
Mark Report as Failed
↓
Return Error Message to User
```

## Logging

### Log Levels

**INFO**: Normal operations
```
Retrieved data for report
Response parsed
Report generation completed
```

**WARNING**: Non-critical issues
```
Failed to parse AI response as JSON
AI provider failed, trying fallback
Failed to log AI usage
```

**ERROR**: Critical failures
```
AI report generation failed
All AI providers are unavailable
Database connection error
```

### Log Locations

- **Main Log**: `storage/logs/laravel.log`
- **AI Audit Log**: `storage/logs/ai_audit.log` (if enabled)
- **Database**: `ai_usage_logs` table

### Sample Log Entry

```
[2026-05-16 10:30:45] local.INFO: Retrieved data for report {
  "report_id": 1,
  "data_keys": ["sales_summary", "inventory_summary", "trends"],
  "data_size": 2345
}

[2026-05-16 10:30:47] local.INFO: AI response received {
  "report_id": 1,
  "provider": "groq",
  "model": "llama-3.3-70b-versatile",
  "tokens": 1234,
  "response_length": 5678
}

[2026-05-16 10:30:47] local.INFO: Response parsed {
  "report_id": 1,
  "has_summary": true,
  "insights_count": 4,
  "recommendations_count": 3
}
```

## Performance Optimization

### Query Classification
- **Time**: < 1ms
- **Optimization**: Simple string matching, no database queries

### Data Retrieval
- **Time**: 100-500ms
- **Optimization**: Indexed queries, aggregation at database level

### Prompt Building
- **Time**: < 10ms
- **Optimization**: String concatenation, no external calls

### AI Provider Call
- **Time**: 1-5 seconds
- **Optimization**: Parallel requests (future), caching (future)

### Response Parsing
- **Time**: 10-100ms
- **Optimization**: Regex-based extraction, no external calls

### Total Time
- **Average**: 2-5 seconds
- **P95**: 8-10 seconds
- **P99**: 15-20 seconds

## Database Schema

### ai_reports table
```sql
CREATE TABLE ai_reports (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    title VARCHAR(255),
    query TEXT,
    summary TEXT,
    data JSON,
    insights JSON,
    recommendations JSON,
    ai_prompt TEXT,
    ai_raw_response LONGTEXT,
    provider_used VARCHAR(50),
    model_used VARCHAR(100),
    tokens_used INT,
    status VARCHAR(50),
    error_message TEXT,
    completed_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### ai_usage_logs table
```sql
CREATE TABLE ai_usage_logs (
    id BIGINT PRIMARY KEY,
    provider VARCHAR(50),
    feature VARCHAR(100),
    prompt_hash VARCHAR(32),
    response_hash VARCHAR(32),
    tokens_used INT,
    cost_estimate DECIMAL(8,6),
    success BOOLEAN,
    error_message TEXT,
    user_id BIGINT,
    model_used VARCHAR(100),
    response_time_ms FLOAT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Configuration

### config/ai.php
```php
return [
    'default_provider' => 'groq',
    'fallback_enabled' => true,
    'fallback_chain' => ['groq', 'openrouter', 'gemini', 'ollama'],
    'providers' => [
        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'base_url' => 'https://api.groq.com/openai/v1',
            'model' => 'llama-3.3-70b-versatile',
            'max_tokens' => 4096,
            'temperature' => 0.3,
            'enabled' => true,
        ],
        // ... other providers
    ],
];
```

### Environment Variables
```
GROQ_API_KEY=your_key_here
GROQ_MODEL=llama-3.3-70b-versatile
OPENAI_API_KEY=your_key_here
GEMINI_API_KEY=your_key_here
OPENROUTER_API_KEY=your_key_here
OLLAMA_ENABLED=false
OLLAMA_BASE_URL=http://localhost:11434
```

---

**Last Updated**: May 2026  
**Version**: 1.0
