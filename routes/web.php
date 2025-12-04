<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController; // Jangan lupa import ini
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

Route::get('/', [MenuController::class, 'home'])->name('home');
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');

// --- Rute User Authentication (Login, Register, Password Reset) ---
Route::middleware('guest')->group(function () {
    // Login User
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Register User
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
});

// User Logout (requires authentication)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// --- Cart Routes ---
Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/remove', [CartController::class, 'removeCart'])->name('cart.remove');

// --- Rute Guest Admin (Login) ---
Route::prefix('admin')->middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});

// --- Rute Admin (Terproteksi) ---
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Dashboard & Fitur Lainnya (Pastikan middleware cek role admin ditambahkan di controller atau di sini)
    // Untuk keamanan ekstra, kamu bisa tambahkan middleware buatan sendiri 'is_admin' nanti.
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{id}', [OrderController::class, 'success'])->name('checkout.success');
});