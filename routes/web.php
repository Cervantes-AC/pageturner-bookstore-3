<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TwoFactorController;
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
Route::middleware('auth')->group(function () {

    // Dashboard routes
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('customer.dashboard');
    })->name('dashboard');
    
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');
    
    Route::get('/customer/dashboard', [DashboardController::class, 'customer'])
        ->name('customer.dashboard');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Two-Factor Authentication
    Route::get('/two-factor-challenge', [TwoFactorController::class, 'show'])
        ->name('two-factor.show');
    Route::post('/two-factor-challenge', [TwoFactorController::class, 'verify'])
        ->name('two-factor.verify');
    Route::post('/two-factor-resend', [TwoFactorController::class, 'resend'])
        ->name('two-factor.resend');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])
        ->name('two-factor.enable');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])
        ->name('two-factor.disable');
    Route::get('/two-factor/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])
        ->name('two-factor.recovery-codes');
    Route::post('/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])
        ->name('two-factor.recovery-codes.regenerate');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{book}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{book}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{book}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Reviews (require email verification)
    Route::middleware('verified')->group(function () {
        Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Orders (require email verification)
    Route::middleware('verified')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });
});

// ─── Admin Routes ─────────────────────────────────────────────
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // Category management
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Book management
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
});

require __DIR__.'/auth.php';