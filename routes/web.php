<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController; // Jangan lupa import ini
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

Route::get('/', [MenuController::class, 'home'])->name('home');
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');

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
    // Halaman Checkout (Form Upload Bukti & Pilih Tanggal)
    Route::get('/checkout', [OrderController::class, 'create'])->name('checkout.index');
    
    // Proses Simpan Pesanan (Validasi Kuota & Upload)
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    
    // Halaman Sukses (Opsional, biar UX bagus)
    Route::get('/checkout/success/{id}', [OrderController::class, 'success'])->name('checkout.success');
});