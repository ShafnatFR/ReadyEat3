<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // Jangan lupa import ini

Route::get('/', function () {
    return view('welcome');
});

// --- Rute Admin ---
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    // Nanti kamu bisa tambahkan rute lain di sini, misal:
    // Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    // Route::post('/verify/{id}', [AdminController::class, 'verify'])->name('admin.verify');
});

// Auth routes (bawaan Laravel/Breeze jika ada)
// require __DIR__.'/auth.php';