<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\UserAccountController;

// ===== GUEST ROUTES =====

// Homepage & Menu Listing
Route::get('/', [MenuController::class, 'home'])->name('home');
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');

// User Authentication (guest only with rate limiting)
Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
});

// ===== AUTHENTICATED ROUTES =====

// User Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Cart Management (requires authentication for security with rate limiting)
Route::middleware(['auth', 'throttle:30,1'])->group(function () {
    Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'removeCart'])->name('cart.remove');
});

// Checkout (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{id}', [OrderController::class, 'success'])->name('checkout.success');
});

// User Account Management (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/account', [UserAccountController::class, 'index'])->name('account.index');
    Route::patch('/account/profile', [UserAccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::patch('/account/password', [UserAccountController::class, 'updatePassword'])->name('account.password.update');
});

// ===== ADMIN ROUTES =====

// Admin Login (guest only)
Route::prefix('admin')->middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});

// Protected Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Main Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Order Verification
    Route::post('/orders/{order}/accept', [AdminController::class, 'acceptOrder'])->name('admin.orders.accept');
    Route::post('/orders/{order}/reject', [AdminController::class, 'rejectOrder'])->name('admin.orders.reject');
    Route::post('/orders/{order}/upload-proof', [AdminController::class, 'uploadPaymentProof'])->name('admin.orders.upload-proof');

    // Bulk Operations - P2 Enhancement
    Route::post('/orders/bulk-approve', [AdminController::class, 'bulkApproveOrders'])->name('admin.orders.bulk-approve');
    Route::post('/orders/bulk-reject', [AdminController::class, 'bulkRejectOrders'])->name('admin.orders.bulk-reject');
    Route::post('/orders/bulk-status', [AdminController::class, 'bulkUpdateStatus'])->name('admin.orders.bulk-status');

    // Products Management
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');
    Route::post('/products/{product}/toggle', [AdminController::class, 'toggleProductAvailability'])->name('admin.products.toggle');

    // Pickup Management
    Route::post('/orders/{order}/complete', [AdminController::class, 'markAsCompleted'])->name('admin.orders.complete');
});
