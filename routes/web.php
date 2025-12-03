<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController; // Jangan lupa import ini

Route::get('/', function () {
    return view('welcome');
});

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