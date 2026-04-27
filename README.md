# PageTurner Online Bookstore Management System

## Student Information
- **Name:** Aaron Clyde Cervantes
- **Course:** Bachelor of Science in Information Technology
- **Activity:** Laboratory Activity 6 — Data Portability, Automated Operations, and Advanced System Architecture

---

## Project Overview

PageTurner is a production-grade online bookstore management system built with **Laravel 12**. This laboratory extends the system from Lab Activity 4 with enterprise data management capabilities: bulk import/export, automated backup scheduling, comprehensive audit logging, tiered API rate limiting, and advanced data transformation middleware.

---

## What's New in Lab Activity 6

| Feature | Description |
|---|---|
| 📥 Bulk Import | Excel/CSV book imports with chunked processing, validation, and error reporting |
| 📤 Data Export | Filtered exports in XLSX, CSV, and PDF formats |
| 🔄 Automated Backups | Spatie Laravel Backup with scheduling, monitoring, and email alerts |
| 📋 Audit Logging | Full change tracking with tamper-proof SHA-256 checksums |
| 🚦 Rate Limiting | 5-tier system with per-second burst protection and response headers |
| 🔀 API Transform | Middleware for snake_case → camelCase conversion and field filtering |
| 🗄️ DB Optimization | Performance indexes and read/write splitting configuration |
| 📊 Enhanced Dashboards | Import/export status, backup health, audit summary, system metrics |

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

## Features

### 4.1 Import / Export System

#### Book Import
- Upload XLSX or CSV files via the admin panel
- Required columns: `ISBN`, `Title`, `Author`, `Price`, `Stock`, `Category`, `Description`
- Validation rules: ISBN uniqueness, title max 255 chars, price 0–9999.99, stock ≥ 0, category must exist
- Chunked processing at 1,000 rows per chunk (`WithChunkReading`)
- Batch database inserts at 1,000 rows (`WithBatchInserts`)
- Skip-on-failure with detailed per-row error reports (`SkipsOnFailure`)
- Duplicate handling: choose to **skip** or **update** existing books by ISBN
- Downloadable import template (pre-formatted XLSX)
- All operations logged to `import_logs` table

#### Book Export
- Filter by category, price range, stock status, and date range
- Choose which columns to include in the export
- Formats: XLSX, CSV
- Uses `FromQuery` concern for memory-efficient chunked exports

#### Order Export (Admin)
- Filter by status, date range
- Exports customer name, email, total, item count, shipping info

#### User Export (Admin Only)
- Full user list with role, verification status, 2FA status, order count
- Optional **PII redaction** (GDPR compliance) — replaces name/email with `[REDACTED]`

#### Audit Log Export
- Filter by user, event type, date range
- Formats: XLSX, CSV, **PDF**

#### Customer Data Portability
- **Export My Data** — GDPR-compliant JSON download of full profile + order history
- **Export My Orders** — XLSX/CSV download of personal order history

---

### 4.2 Automated Backup & Maintenance

#### Backup Configuration (Spatie Laravel Backup)
- **Source:** Full database dump + uploaded book cover images
- **Storage:** Local disk (encrypted with AES-256)
- **Retention policy:**
  - Keep all backups for 7 days
  - Keep daily backups for 16 days
  - Keep weekly backups for 8 weeks
  - Keep monthly backups for 4 months
- **Health checks:** Max age 1 day, max storage 5,000 MB
- **Email notifications:** On backup failure, success, and unhealthy status
- **Manual trigger:** Admin dashboard "Run Backup Now" button

#### Scheduled Tasks

| Task | Schedule | Description |
|---|---|---|
| `backup:run` | Daily at 02:00 | Full database + files backup |
| `backup:clean` | Daily at 03:00 | Remove old backups per retention policy |
| `order:cleanup-pending` | Hourly | Cancel pending orders older than 24 hours |
| `auth:clear-resets` | Daily | Clear expired password reset tokens |
| `log:rotate` | Weekly | Archive and gzip compress old log files |
| `report:generate-daily` | Daily at 06:00 | Generate sales report and email to admins |
| `notification:prune` | Weekly | Delete notifications older than 90 days |
| `audit:archive` | Monthly | Archive audit logs older than 1 year to JSON |

All tasks use `withoutOverlapping()`, `onSuccess()`, and `onFailure()` hooks with full logging.

---

### 4.3 Audit Logging & Compliance

#### Audited Models
- `Book` — create, update, delete (excludes cover_image)
- `Category` — create, update, delete
- `Order` — create, update (status transitions)
- `Review` — create, delete

#### Audit Log Structure
```json
{
  "id": 42,
  "user_id": 1,
  "event": "updated",
  "auditable_type": "App\\Models\\Book",
  "auditable_id": 5,
  "old_values": { "price": 549, "stock_quantity": 88 },
  "new_values": { "price": 599, "stock_quantity": 75 },
  "url": "/admin/books/5",
  "ip_address": "127.0.0.1",
  "user_agent": "Mozilla/5.0...",
  "checksum": "98356080363c3dcd...",
  "created_at": "2026-04-27T10:30:00Z"
}
```

#### Security Features
- **Globally excluded fields:** `password`, `remember_token`, `two_factor_recovery_codes`, `two_factor_secret`, API tokens, payment fields
- **Tamper-proof checksums:** Every audit record gets a SHA-256 hash of its immutable fields on creation
- **Retention policy:** 1 year online → monthly archive to `storage/app/archives/` as JSON with per-record checksums and an overall archive hash
- **Real-time alerts:** Email sent to all admins when a critical event occurs (role changes, admin deletions)

#### Audit Dashboard (Admin Only)
- Filter by user, event type, model, date range
- Side-by-side **Before / After** diff visualization
- Export to XLSX, CSV, or PDF

---

### 4.4 API Rate Limiting

#### Tiers

| Tier | Limit | Burst (per second) | Scope |
|---|---|---|---|
| `public` | 30 req/min | 5/sec | Visitors (by IP) |
| `auth` | 10 req/min | 2/sec | Login, register, password reset |
| `standard` | 60 req/min | 5/sec | Authenticated customers |
| `premium` | 300 req/min | 10/sec | Premium/VIP users |
| `admin` | 1,000 req/min | 20/sec | Administrators |

#### Response Headers
Every response includes:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
Retry-After: 45   (only on 429 responses)
```

#### 429 Response Format
```json
{
  "message": "Rate limit exceeded. Public API allows 30 requests/minute.",
  "retry_after": 60
}
```

---

### 4.5 API Transform Middleware

The `ApiTransformMiddleware` applies to all JSON responses:

- **snake_case → camelCase** conversion of all response keys
- **Field filtering** via `?fields=id,title,price` query parameter
- **X-RateLimit headers** injected on every response

---

### 4.6 Database Architecture

#### Performance Indexes
Migration `2026_04_20_030002_add_indexes_for_performance` adds indexes on:
- `books.isbn`, `books.category_id`
- `orders.user_id`, `orders.status`, `orders.created_at`
- `order_items.order_id`, `order_items.book_id`
- `reviews.book_id`, `reviews.user_id`

#### Read/Write Splitting (MySQL)
Configured in `config/database.php` — uncomment to activate with replicas:
```php
'read'   => ['host' => [env('DB_READ_HOST_1'), env('DB_READ_HOST_2')]],
'write'  => ['host' => [env('DB_WRITE_HOST')]],
'sticky' => true,
```

---

## Database Schema

| Table | Purpose |
|---|---|
| `users` | Accounts with role, 2FA, email verification |
| `categories` | Book genres/categories |
| `books` | Inventory with ISBN, price, stock, cover |
| `orders` | Customer orders with shipping info |
| `order_items` | Line items per order |
| `reviews` | Star ratings and comments |
| `two_factor_secrets` | 2FA codes (6-digit, 10-min expiry) |
| `login_logs` | Login attempt history |
| `notifications` | Laravel database notifications |
| `audits` | Full change tracking with checksums |
| `import_logs` | Import operation history |
| `export_logs` | Export request history |
| `backup_monitoring` | Backup execution logs |
| `api_rate_limits` | Rate limit hit tracking |
| `sessions` | Database-backed sessions |
| `jobs` | Queue job table |

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

# 6. Run migrations and seed the database
php artisan migrate:fresh --seed

# 7. Create storage symlink (for book cover images)
php artisan storage:link

# 8. Start the development server
php artisan serve
```

Visit: **http://localhost:8000**

### Queue Worker (for background jobs)
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

> **Note:** The admin account has 2FA enabled by default. Either check your email for the code, or disable it via tinker:
> ```bash
> php artisan tinker --execute="App\Models\User::where('role','admin')->update(['two_factor_enabled'=>false]);"
> ```

---

## Usage Guide

### Admin Panel

**Import Books**
1. Go to **Admin → Import**
2. Download the template, fill in your data
3. Upload the XLSX/CSV file
4. Choose duplicate handling (skip or update)
5. View results in the import log table

**Export Data**
1. Go to **Admin → Export**
2. Select data type (Books / Orders / Users)
3. Apply filters and choose format (XLSX / CSV)
4. File downloads immediately

**Audit Logs**
1. Go to **Admin → Audit**
2. Filter by user, event, model, or date range
3. Click **View** on any row for the full Before/After diff
4. Export filtered results to XLSX, CSV, or PDF

**Backup**
1. Go to **Admin → Backup**
2. View backup history with status and file size
3. Click **Run Backup Now** for an immediate backup
4. Automated backups run daily at 02:00 AM

### Customer Panel

**Export My Data (GDPR)**
- Dashboard → **Export My Data** — downloads a JSON file with your full profile and order history

**Export Order History**
- Dashboard → **Orders** (select format) — downloads your orders as XLSX or CSV

---

## Project Structure

```
pageturner-bookstore/
├── app/
│   ├── Console/Commands/
│   │   ├── ArchiveAuditLogs.php       # Monthly audit archival with checksums
│   │   ├── CleanupPendingOrders.php   # Hourly stale order cancellation
│   │   ├── GenerateDailyReport.php    # Daily sales report email
│   │   ├── PruneNotifications.php     # Weekly notification cleanup
│   │   └── RotateLogs.php             # Weekly log compression
│   ├── Exports/
│   │   ├── AuditLogsExport.php
│   │   ├── BooksExport.php            # FromQuery, filters, custom columns
│   │   ├── OrdersExport.php
│   │   └── UsersExport.php            # PII redaction support
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuditLogController.php # XLSX/CSV/PDF export
│   │   │   ├── BackupController.php
│   │   │   ├── DashboardController.php
│   │   │   └── ImportExportController.php
│   │   └── Middleware/
│   │       └── ApiTransformMiddleware.php  # camelCase + field filtering + headers
│   ├── Imports/
│   │   └── BooksImport.php            # Chunked, batched, skip-on-failure
│   ├── Models/
│   │   ├── BackupMonitoring.php
│   │   ├── ExportLog.php
│   │   └── ImportLog.php
│   ├── Notifications/
│   │   └── CriticalAuditEventNotification.php
│   └── Providers/
│       └── AppServiceProvider.php     # Rate limiting + audit checksums + alerts
├── config/
│   ├── audit.php                      # owen-it/laravel-auditing config
│   ├── backup.php                     # Spatie backup config
│   └── excel.php                      # maatwebsite/excel config
├── database/
│   ├── migrations/                    # 21 migrations total
│   └── seeders/
│       ├── DatabaseSeeder.php         # 100 books, 22 users, 52 orders
│       └── Lab6Seeder.php             # Mock import/export/audit/backup data
├── resources/views/
│   ├── audit/
│   │   ├── index.blade.php            # Filterable audit log table
│   │   ├── show.blade.php             # Side-by-side diff view
│   │   └── export-pdf.blade.php       # PDF export template
│   ├── backup/index.blade.php
│   ├── dashboard/
│   │   ├── admin.blade.php            # Import/export/backup/audit widgets
│   │   └── customer.blade.php         # GDPR data export
│   └── import-export/
│       ├── import.blade.php
│       └── export.blade.php
└── routes/
    ├── web.php                        # All application routes
    └── console.php                    # All scheduled tasks
```

---

## Testing the Lab 6 Features

### Import Testing
```bash
# Download the template from /admin/import/template
# Fill with test data and upload at /admin/import
# Test malformed file handling by uploading invalid data
```

### Backup Testing
```bash
php artisan backup:run
php artisan backup:clean
php artisan schedule:run
```

### Audit Testing
```bash
# Log in as admin, edit a book — check /admin/audit-logs
# Change a user's role — triggers critical email alert
php artisan audit:archive   # archives records > 1 year old
```

### Rate Limiting Testing
```bash
# Hit /books rapidly to trigger the 30 req/min public limit
# Check response headers: X-RateLimit-Limit, X-RateLimit-Remaining
# A 429 response includes Retry-After header
```

### Scheduled Tasks Testing
```bash
php artisan order:cleanup-pending
php artisan notification:prune
php artisan report:generate-daily
php artisan log:rotate
```

---

## Grading Rubric Coverage

| Component | Weight | Status |
|---|---|---|
| Import/Export System | 25% | ✅ Chunked import, batch inserts, skip-on-failure, XLSX/CSV/PDF export, PII redaction, logs |
| Backup & Automation | 20% | ✅ Spatie backup, 8 scheduled tasks, withoutOverlapping, onSuccess/onFailure, monitoring |
| Audit & Compliance | 20% | ✅ owen-it auditing, SHA-256 checksums, diff dashboard, PDF export, critical email alerts |
| API Rate Limiting | 15% | ✅ 5 tiers, per-second burst, X-RateLimit headers, JSON 429 responses |
| Advanced Features | 10% | ✅ ApiTransformMiddleware, DB indexes, read/write splitting config |
| Documentation & Testing | 10% | ✅ This README + inline code comments + seeders for test data |

---

## Security Features

| Feature | Implementation |
|---|---|
| Email Verification | Required before placing orders or writing reviews |
| Two-Factor Authentication | Email-based 6-digit codes with 10-minute expiry |
| Password Hashing | bcrypt via Laravel Hash facade |
| CSRF Protection | All forms protected with `@csrf` |
| Role-Based Access Control | Admin/Customer roles with policy-based authorization |
| Rate Limiting | 5-tier system with per-second burst protection |
| Audit Checksums | SHA-256 hash on every audit record |
| Sensitive Field Exclusion | Passwords, tokens, 2FA secrets never logged |
| Login Attempt Logging | IP, user agent, success/failure reason tracked |
| PII Redaction | GDPR-compliant user export with redaction option |

---

## Acknowledgments

- **Laravel Documentation** — Framework guidance
- **Spatie** — Laravel Backup package
- **Owen-IT** — Laravel Auditing package
- **Maatwebsite** — Laravel Excel package
- **Barry vd. Heuvel** — Laravel DomPDF package
- **Tailwind CSS** — UI styling
- **My Instructor** — For the detailed Lab 6 specifications

---

*PageTurner Online Bookstore Management System*
*Laboratory Activity 6 — Data Portability, Automated Operations, and Advanced System Architecture*
