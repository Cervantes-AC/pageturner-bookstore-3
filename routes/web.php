<?php

use App\Http\Controllers\Admin\AIReportController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\Admin\RateLimitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController as UserDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ───────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Book browsing (public)
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Category browsing (public)
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// ─── Authenticated Routes ─────────────────────────────────────
// Email verification is optional for browsing; enforced on orders/reviews
Route::middleware(['auth', '2fa'])->group(function () {

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{book}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{book}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{book}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Orders (email verification required)
    Route::middleware('verified')->group(function () {
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });

    // Reviews - create requires email verification and purchase
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])
        ->middleware('verified')
        ->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // ─── User Data Portability ────────────────────────────────
    Route::get('/my-data/export', [ImportExportController::class, 'exportMyData'])->name('user.export-data');
    Route::get('/my-orders/export', [ImportExportController::class, 'exportMyOrders'])->name('user.export-orders');
    Route::get('/orders/{order}/invoice', [ImportExportController::class, 'downloadInvoice'])->name('orders.invoice');
});

// ─── Admin Routes ─────────────────────────────────────────────
Route::middleware(['auth', 'verified', '2fa', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Category management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Book management
    Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
    Route::post('/books', [AdminBookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [AdminBookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [AdminBookController::class, 'destroy'])->name('books.destroy');

    // User management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // ─── Import/Export ────────────────────────────────────────
    Route::prefix('import-export')->name('import-export.')->group(function () {
        Route::get('/import', [ImportExportController::class, 'importForm'])->name('import');
        Route::post('/import/books', [ImportExportController::class, 'importBooks'])->name('import.books');
        Route::post('/import/users', [ImportExportController::class, 'importUsers'])->name('import.users');
        Route::get('/template', [ImportExportController::class, 'downloadTemplate'])->name('template');

        Route::get('/export', [ImportExportController::class, 'exportForm'])->name('export');
        Route::post('/export/books', [ImportExportController::class, 'exportBooks'])->name('export.books');
        Route::post('/export/orders', [ImportExportController::class, 'exportOrders'])->name('export.orders');
        Route::post('/export/users', [ImportExportController::class, 'exportUsers'])->name('export.users');

        Route::get('/exports', [ImportExportController::class, 'exportLogs'])->name('exports');
        Route::get('/imports', [ImportExportController::class, 'importLogs'])->name('imports');
        Route::get('/exports/{exportLog}/download', [ImportExportController::class, 'downloadExport'])->name('exports.download');
    });

    // ─── Audit Logs ───────────────────────────────────────────
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/export', [AuditController::class, 'export'])->name('export');
        Route::get('/{auditLog}', [AuditController::class, 'show'])->name('show');
    });

    // ─── Backup ───────────────────────────────────────────────
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/run', [BackupController::class, 'run'])->name('run');
        Route::get('/{backupMonitoring}/download', [BackupController::class, 'download'])->name('download');
        Route::get('/{backupMonitoring}', [BackupController::class, 'show'])->name('show');
    });

    // ─── Analytics ────────────────────────────────────────────
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'data'])->name('analytics.data');

    // ─── Rate Limits ──────────────────────────────────────────
    Route::get('/rate-limits', [RateLimitController::class, 'index'])->name('rate-limits.index');

    // ─── AI Reports ───────────────────────────────────────────
    Route::prefix('ai-reports')->name('ai-reports.')->group(function () {
        Route::get('/', [AIReportController::class, 'index'])->name('index');
        Route::get('/create', [AIReportController::class, 'create'])->name('create');
        Route::post('/', [AIReportController::class, 'store'])->name('store');
        Route::get('/{report}', [AIReportController::class, 'show'])->name('show');
        Route::get('/{report}/print', [AIReportController::class, 'showPrint'])->name('print');
        Route::get('/{report}/word', [AIReportController::class, 'downloadWord'])->name('word');
        Route::post('/{report}/regenerate', [AIReportController::class, 'regenerate'])->name('regenerate');
        Route::delete('/{report}', [AIReportController::class, 'destroy'])->name('destroy');
        Route::get('/usage/logs', [AIReportController::class, 'usageLogs'])->name('usage');
    });
});

require __DIR__.'/auth.php';
