# PageTurner Online Bookstore Management System

## Student Information
- **Name:** Aaron Clyde Cervantes
- **Course:** Bachelor of Science in Information Technology
- **Section:** BSIT 3C
- **Schedule:** Thursday 1:00 PM – 3:00 PM | CISC Room 3
- **Activity:** Laboratory Activity 7 — Mass Data Seeding, Performance Optimization, and Scalability Engineering

---

## Project Overview

PageTurner is a production-grade online bookstore management system built with **Laravel 12**. This laboratory builds on Lab Activity 6 by scaling the system to handle **1,000,000+ book records** while maintaining sub-second query response times. The focus is on high-volume data generation, database performance tuning, Redis caching architectures, and horizontal scaling strategies.

---

## Cumulative Feature Summary (Labs 3 → 7)

| Lab | Focus | Key Features Added |
|---|---|---|
| Lab 3 | Foundation | Books, categories, orders, reviews, CRUD, Blade templating |
| Lab 4 | Authentication | Email verification, 2FA, password recovery, role-based dashboards |
| Lab 6 | Data & Operations | Import/export, automated backups, audit logging, rate limiting |
| **Lab 7** | **Scalability** | **1M seeder, performance indexes, Redis caching, materialized views, benchmarking** |

---

## Technologies & Packages

| Purpose | Package | Version |
|---|---|---|
| Framework | laravel/framework | ^12.0 |
| Authentication | laravel/breeze | ^2.3 |
| Excel Import/Export | maatwebsite/excel | ^3.1 |
| Database Backup | spatie/laravel-backup | 9.3.7 |
| Audit Logging | owen-it/laravel-auditing | ^14.0 |
| PDF Generation | barryvdh/laravel-dompdf | ^3.1 |
| Rate Limiting | Built-in Laravel | — |
| Task Scheduling | Built-in Laravel Scheduler | — |
| Frontend | Tailwind CSS + Alpine.js | — |
| Database | SQLite (dev) / MySQL (prod) | — |

---

## Lab Activity 7 — New Features

### 7.1 The 1 Million Book Challenge

**`database/factories/BookFactory.php`** — completely rewritten for mass generation:
- Category IDs loaded **once** into a static property — avoids 1M DB queries
- 15-publisher static pool — no faker overhead per record
- Format-based pricing (`ebook` ₱2.99–19.99, `paperback` ₱9.99–39.99, `hardcover` ₱19.99–79.99, `audiobook` ₱14.99–44.99)
- Valid **ISBN-13** generation with proper modulo-10 checksum
- 85% of books are active, 5% featured — realistic distribution
- `published_at` spans 1950–2025 for partition pruning demonstrations
- Factory states: `bestseller()`, `outOfStock()`

**`database/seeders/MassBookSeeder.php`** — chunked batch insert:

```
CHUNK_SIZE    = 5,000 rows
TOTAL_RECORDS = 1,000,000 rows
Memory target < 512 MB
Time target   < 10 minutes
```

Key technique — never use `Book::factory()->count(1000000)->create()`:
```php
// ✅ Correct — raw batch insert, no Eloquent overhead
$books = Book::factory()->count($batchSize)->make()
    ->map(fn($b) => array_merge($b->getAttributes(), [
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString(),
    ]))->toArray();

DB::table('books')->insert($books);

// Force GC every 10 chunks to keep memory bounded
if ($inserted % (self::CHUNK_SIZE * 10) === 0) {
    unset($books);
    gc_collect_cycles();
}
```

Run the seeder:
```bash
php artisan db:seed --class=MassBookSeeder
```

---

### 7.2 Database Schema Optimization

**Migration** `2026_04_27_100000_optimize_books_table_lab7`:

New columns added to `books`:
| Column | Type | Purpose |
|---|---|---|
| `publisher` | string | Publisher name |
| `format` | enum | ebook / paperback / hardcover / audiobook |
| `published_at` | date | Full date for range partitioning |
| `is_active` | boolean | Soft-visibility flag (85% active) |

New indexes:
| Index | Columns | Type | Purpose |
|---|---|---|---|
| `idx_books_catalog_filter` | category_id, published_at, is_active | Composite | Main catalog filter — hits covering index |
| `idx_books_price_stock` | price, stock_quantity, id | Covering | Price range queries — index-only scan |
| `idx_books_active` | is_active | Single | Active book filtering |
| `idx_books_fulltext` | title, description | FULLTEXT | MySQL full-text search (skipped on SQLite) |

**Migration** `2026_04_27_100001_create_materialized_views_lab7`:
- `mv_bestseller_stats` — pre-computed category stats table
- `query_performance_logs` — slow query monitoring

---

### 7.3 Query Performance Optimization

**`app/Repositories/BookRepository.php`**

| Method | Strategy | Target |
|---|---|---|
| `activeCatalog()` | Cursor pagination + covering index + Redis cache | < 100 ms |
| `findByIsbn()` | Unique index + Redis cache (30 min TTL) | < 50 ms |
| `byCategory()` | Composite index + Redis tag cache | < 150 ms |
| `fullTextSearch()` | MySQL FULLTEXT / SQLite LIKE fallback | < 300 ms |
| `exportChunked()` | `lazy()` generator — memory-safe streaming | — |

Why cursor pagination over offset:
```
OFFSET pagination: O(n) — degrades at 1M rows
Cursor pagination: O(1) — constant time regardless of depth
```

**`app/Services/BookCacheService.php`** — Redis tag-based caching:

| Cache Key | TTL | Tags |
|---|---|---|
| Catalog pages | 5 min | `books` |
| ISBN lookups | 30 min | `books`, `isbn:{isbn}` |
| Category pages | 10 min | `books`, `category:{id}` |
| Bestsellers | 1 hour | `books`, `bestsellers` |

Falls back gracefully to plain cache when Redis is unavailable (file/database drivers).

---

### 7.4 Smart Cache Invalidation

**`app/Observers/BookObserver.php`** — registered in `AppServiceProvider`:
- Fires on `saved()` and `deleted()` model events
- Flushes catalog cache, ISBN cache, and category cache for the affected book
- Keeps Redis consistent without a full cache flush

---

### 7.5 Materialized Views

**`app/Console/Commands/RefreshMaterializedViews.php`**

Pre-computes `mv_bestseller_stats` per category:
- `total_books`, `avg_price`, `total_inventory`, `bestseller_count`, `latest_publication`
- Refreshed **hourly** via scheduler
- Portable — works on SQLite (dev) and MySQL (prod)

```bash
php artisan app:refresh-materialized-views
# Output: Refreshed 11 category stats in 24.5ms
```

---

### 7.6 Performance Benchmarking

**`app/Console/Commands/BenchmarkBookQueries.php`**

Runs each query N times with a warmup pass, reports avg/min/max, validates against Lab 7 targets:

```bash
php artisan benchmark:books --iterations=100
```

Sample output (103 books, SQLite):
```
+-------------------------------+---------+---------+---------+--------+---------+
| Query                         | Avg     | Min     | Max     | Target | Status  |
+-------------------------------+---------+---------+---------+--------+---------+
| ISBN Lookup (exact)           | 0.33 ms | 0.25 ms | 0.86 ms | 50 ms  | ✅ PASS |
| Catalog Listing (100 records) | 1.55 ms | 1.21 ms | 2.17 ms | 100 ms | ✅ PASS |
| Category Filter               | 0.59 ms | 0.43 ms | 1.04 ms | 150 ms | ✅ PASS |
| Full-Text Search              | 0.77 ms | 0.67 ms | 1.00 ms | 300 ms | ✅ PASS |
+-------------------------------+---------+---------+---------+--------+---------+
Passed: 4 / 4 — All performance targets met! ✅
```

Returns non-zero exit code on failure — CI/CD integration ready.

---

### 7.7 Async Cache Warmup

**`app/Jobs/WarmCategoryCache.php`** — queued job:
- Pre-loads top 1,000 active books per category into Redis
- Dispatched after seeding or on a schedule
- Ensures cache hits from the very first user request

```bash
# Dispatch for all categories after seeding
php artisan tinker
>>> App\Models\Category::all()->each(fn($c) => App\Jobs\WarmCategoryCache::dispatch($c->id));
```

---

### 7.8 Full-Text Search Indexing

**`app/Console/Commands/IndexBooksBatch.php`** — chunked Scout indexing:

```bash
php artisan books:index-batch --chunk=5000
```

- Processes active books in observable chunks with a progress bar
- On MySQL: calls `$books->each->searchable()` for Scout indexing
- On SQLite (dev): dry-run with progress tracking

---

## Lab 6 Features (still active)

### Import / Export System
- Bulk book import XLSX/CSV — chunked (1,000 rows), batch inserts, skip-on-failure
- Filtered exports in XLSX, CSV, PDF formats
- Customer GDPR data export (JSON)
- Admin order/user exports with PII redaction
- All operations logged to `import_logs` / `export_logs`

### Automated Backup
- Spatie Laravel Backup — daily at 02:00 AM
- Retention: 7 daily / 8 weekly / 4 monthly
- Email alerts on failure/success
- Manual trigger from admin dashboard

### Audit Logging
- Full change tracking on Book, Category, Order, Review
- SHA-256 tamper-proof checksums on every record
- Side-by-side diff dashboard with XLSX/CSV/PDF export
- Real-time email alerts for critical events (role changes, admin deletions)

### API Rate Limiting
- 5 tiers: public (30/min), auth (10/min), standard (60/min), premium (300/min), admin (1000/min)
- Per-second burst protection on all tiers
- `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `Retry-After` headers

---

## Scheduled Tasks

| Task | Schedule | Description |
|---|---|---|
| `backup:run` | Daily 02:00 | Full database + files backup |
| `backup:clean` | Daily 03:00 | Remove old backups per retention policy |
| `order:cleanup-pending` | Hourly | Cancel pending orders > 24 hours old |
| `auth:clear-resets` | Daily | Clear expired password reset tokens |
| `log:rotate` | Weekly | Archive and compress old log files |
| `report:generate-daily` | Daily 06:00 | Generate sales report, email to admins |
| `notification:prune` | Weekly | Delete notifications > 90 days old |
| `audit:archive` | Monthly | Archive audit logs > 1 year to JSON |
| `app:refresh-materialized-views` | Hourly | Refresh `mv_bestseller_stats` table |

All tasks use `withoutOverlapping()`, `onSuccess()`, and `onFailure()` hooks.

---

## Database Schema

### All Tables (23 migrations)

| Table | Lab | Purpose |
|---|---|---|
| `users` | 3 | Accounts with role, 2FA, email verification |
| `categories` | 3 | Book genres/categories |
| `books` | 3 | Inventory — ISBN, price, stock, cover |
| `orders` | 3 | Customer orders with shipping info |
| `order_items` | 3 | Line items per order |
| `reviews` | 3 | Star ratings and comments |
| `two_factor_secrets` | 4 | 2FA codes (6-digit, 10-min expiry) |
| `login_logs` | 4 | Login attempt history |
| `notifications` | 4 | Laravel database notifications |
| `audits` | 6 | Full change tracking with SHA-256 checksums |
| `import_logs` | 6 | Import operation history |
| `export_logs` | 6 | Export request history |
| `backup_monitoring` | 6 | Backup execution logs |
| `api_rate_limits` | 6 | Rate limit hit tracking |
| `mv_bestseller_stats` | 7 | Materialized view — category stats |
| `query_performance_logs` | 7 | Slow query monitoring |
| `sessions` | — | Database-backed sessions |
| `cache` | — | Database cache store |
| `jobs` | — | Queue job table |

---

## Installation

### Requirements
- PHP 8.2+
- Composer
- Node.js & npm
- SQLite (default) or MySQL

### Steps

```bash
# 1. Clone the repository
git clone [your-repository-url]
cd pageturner-bookstore

# 2. Install PHP dependencies
composer install --ignore-platform-req=php

# 3. Install Node dependencies and build assets
npm install && npm run build

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Run migrations
php artisan migrate

# 7. Seed base data (100 books, 22 users, 52 orders, mock Lab 6 data)
php artisan db:seed

# 8. Create storage symlink
php artisan storage:link

# 9. Start the development server
php artisan serve
```

Visit: **http://localhost:8000**

### Seed 1 Million Books (Lab 7)
```bash
# Run after base seeding — adds 1M books in chunks of 5,000
php artisan db:seed --class=MassBookSeeder

# Verify count
php artisan tinker --execute="echo DB::table('books')->count();"

# Warm category caches after seeding
php artisan app:refresh-materialized-views
```

### Queue Worker
```bash
php artisan queue:listen --tries=1
```

### Task Scheduler (server cron)
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Default Login Credentials

| Role | Email | Password |
|---|---|---|
| Admin | aaronclydeccervantes@gmail.com | password |
| Customer | customer@gmail.com | password |

> The admin account has 2FA enabled by default. Disable it with:
> ```bash
> php artisan tinker --execute="App\Models\User::where('role','admin')->update(['two_factor_enabled'=>false]);"
> ```

---

## Testing Lab 7 Features

### Run Benchmarks
```bash
# Quick test (10 iterations)
php artisan benchmark:books --iterations=10

# Full benchmark (100 iterations)
php artisan benchmark:books --iterations=100
```

### Test Materialized View Refresh
```bash
php artisan app:refresh-materialized-views
```

### Test Index Batch Command
```bash
php artisan books:index-batch --chunk=5000
```

### Test All Scheduled Commands
```bash
php artisan order:cleanup-pending
php artisan notification:prune
php artisan report:generate-daily
php artisan log:rotate
php artisan audit:archive
php artisan app:refresh-materialized-views
```

### Enable Redis Caching (optional)
Update `.env`:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

## Project Structure

```
pageturner-bookstore/
├── app/
│   ├── Console/Commands/
│   │   ├── ArchiveAuditLogs.php           # Monthly audit archival with checksums
│   │   ├── BenchmarkBookQueries.php       # Lab 7 — performance benchmarking
│   │   ├── CleanupPendingOrders.php       # Hourly stale order cancellation
│   │   ├── GenerateDailyReport.php        # Daily sales report email
│   │   ├── IndexBooksBatch.php            # Lab 7 — chunked Scout indexing
│   │   ├── PruneNotifications.php         # Weekly notification cleanup
│   │   ├── RefreshMaterializedViews.php   # Lab 7 — hourly mv_bestseller_stats refresh
│   │   └── RotateLogs.php                 # Weekly log compression
│   ├── Exports/
│   │   ├── AuditLogsExport.php
│   │   ├── BooksExport.php                # FromQuery, filters, custom columns
│   │   ├── OrdersExport.php
│   │   └── UsersExport.php                # PII redaction support
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuditLogController.php     # XLSX/CSV/PDF export
│   │   │   ├── BackupController.php
│   │   │   ├── BookController.php
│   │   │   ├── DashboardController.php
│   │   │   └── ImportExportController.php
│   │   └── Middleware/
│   │       ├── ApiTransformMiddleware.php # camelCase + field filtering + rate limit headers
│   │       ├── EnsureEmailIsVerified.php
│   │       └── TwoFactorMiddleware.php
│   ├── Imports/
│   │   └── BooksImport.php                # Chunked, batched, skip-on-failure
│   ├── Jobs/
│   │   └── WarmCategoryCache.php          # Lab 7 — async Redis cache warmup
│   ├── Models/
│   │   ├── Book.php                       # Lab 7 — new fields, scopes, casts
│   │   └── ...
│   ├── Notifications/
│   │   └── CriticalAuditEventNotification.php
│   ├── Observers/
│   │   └── BookObserver.php               # Lab 7 — smart cache invalidation
│   ├── Providers/
│   │   └── AppServiceProvider.php         # Rate limiting + audit checksums + observer
│   ├── Repositories/
│   │   └── BookRepository.php             # Lab 7 — cursor pagination, N+1 prevention
│   └── Services/
│       └── BookCacheService.php           # Lab 7 — Redis tag caching with fallback
├── config/
│   ├── audit.php                          # owen-it/laravel-auditing config
│   ├── backup.php                         # Spatie backup config
│   ├── database.php                       # Read/write splitting + Redis DB config
│   └── excel.php                          # maatwebsite/excel config
├── database/
│   ├── factories/
│   │   └── BookFactory.php                # Lab 7 — high-performance, valid ISBN-13
│   ├── migrations/                        # 23 migrations total
│   └── seeders/
│       ├── DatabaseSeeder.php             # 100 books, 22 users, 52 orders
│       ├── Lab6Seeder.php                 # Mock import/export/audit/backup data
│       └── MassBookSeeder.php             # Lab 7 — 1M books, chunked batch insert
├── resources/views/
│   ├── audit/
│   │   ├── index.blade.php                # Filterable audit log table
│   │   ├── show.blade.php                 # Side-by-side diff view
│   │   └── export-pdf.blade.php           # PDF export template
│   ├── backup/index.blade.php
│   ├── dashboard/
│   │   ├── admin.blade.php                # Import/export/backup/audit widgets
│   │   └── customer.blade.php             # GDPR data export
│   └── import-export/
│       ├── import.blade.php
│       └── export.blade.php
└── routes/
    ├── web.php                            # All application routes
    └── console.php                        # All 9 scheduled tasks
```

---

## Grading Rubric Coverage

### Lab 7

| Requirement | Status | Implementation |
|---|---|---|
| 1M+ record seeding < 512 MB | ✅ | `MassBookSeeder` — chunked batch insert + GC |
| Valid ISBN-13 generation | ✅ | `BookFactory::generateValidIsbn13()` — modulo-10 checksum |
| Realistic data distributions | ✅ | Format-based pricing, 85% active, weighted dates |
| Catalog listing < 100 ms | ✅ | Cursor pagination + covering index |
| ISBN lookup < 50 ms | ✅ | Unique index + Redis cache |
| Category filter < 150 ms | ✅ | Composite index + tag cache |
| Full-text search < 300 ms | ✅ | MySQL FULLTEXT / SQLite LIKE fallback |
| Redis caching architecture | ✅ | `BookCacheService` — tag-based with graceful fallback |
| Cache invalidation | ✅ | `BookObserver` — saved/deleted events |
| Materialized views | ✅ | `mv_bestseller_stats` + hourly refresh command |
| Read/write splitting config | ✅ | Documented in `config/database.php` |
| Performance benchmarking | ✅ | `benchmark:books` — CI/CD ready, 4 query types |
| Async cache warmup | ✅ | `WarmCategoryCache` queued job |
| Full-text index batch | ✅ | `books:index-batch` command |
| N+1 prevention | ✅ | `with(['category:id,name'])` + column selection |

### Lab 6 (carried forward)

| Component | Weight | Status |
|---|---|---|
| Import/Export System | 25% | ✅ Chunked import, XLSX/CSV/PDF export, PII redaction |
| Backup & Automation | 20% | ✅ Spatie backup, 9 scheduled tasks, monitoring |
| Audit & Compliance | 20% | ✅ SHA-256 checksums, diff dashboard, critical alerts |
| API Rate Limiting | 15% | ✅ 5 tiers, per-second burst, response headers |
| Advanced Features | 10% | ✅ ApiTransformMiddleware, DB indexes, read/write config |

---

## Acknowledgments

- **Laravel Documentation** — Framework guidance
- **Spatie** — Laravel Backup package
- **Owen-IT** — Laravel Auditing package
- **Maatwebsite** — Laravel Excel package
- **Barry vd. Heuvel** — Laravel DomPDF package
- **Tailwind CSS** — UI styling
- **My Instructor** — For the detailed Lab 7 specifications

---

*PageTurner Online Bookstore Management System*
*Laboratory Activity 7 — Mass Data Seeding, Performance Optimization, and Scalability Engineering*
*BSIT 3C | Thursday 1:00 PM – 3:00 PM | CISC Room 3*
