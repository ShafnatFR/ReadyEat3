<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Tanggal hari ini untuk filter pickup & kuota
        $today = Carbon::today();

        // --- 1. Statistik Utama (Kartu Atas) ---

        // Hitung pesanan yang 'payment_pending' (Butuh Verifikasi WA)
        $verificationNeeded = Order::where('status', 'payment_pending')->count();

        // Hitung pesanan yang 'ready_for_pickup' KHUSUS untuk HARI INI
        $readyPickupToday = Order::whereDate('pickup_date', $today)
            ->where('status', 'ready_for_pickup')
            ->count();

        // Hitung estimasi omset hari ini (dari pesanan yang valid)
        $todaysRevenue = Order::whereDate('created_at', $today)
            ->whereNotIn('status', ['cancelled', 'unpaid'])
            ->sum('total_price');

        // --- 2. Tabel "Perlu Verifikasi" (Tengah) ---
        // Ambil 5 pesanan terbaru yang statusnya payment_pending
        $recentOrders = Order::with('user') // Eager load user agar hemat query
            ->where('status', 'payment_pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // --- 3. Sisa Kuota Menu (Sidebar Kanan) ---
        // Ini query agak kompleks: Menghitung total qty terjual per menu untuk tanggal pickup HARI INI
        $menuQuotas = Menu::where('isAvailable', true) // Hanya menu aktif
            ->get()
            ->map(function ($menu) use ($today) {
                // Hitung berapa porsi menu ini yang sudah dipesan untuk hari ini
                $soldQty = \App\Models\OrderItem::where('menu_id', $menu->id)
                    ->whereHas('order', function ($q) use ($today) {
                    $q->whereDate('pickup_date', $today)
                        ->whereNotIn('status', ['cancelled', 'unpaid']);
                })
                    ->sum('quantity');

                // Asumsi daily_limit ada di tabel menus (sesuai diskusi sebelumnya).
                // Jika belum migrate kolom daily_limit, kita set default 50.
                $limit = $menu->daily_limit ?? 50;

                return [
                    'name' => $menu->name,
                    'sold' => $soldQty,
                    'limit' => $limit,
                    'remaining' => max(0, $limit - $soldQty),
                    'percentage' => $limit > 0 ? ($soldQty / $limit) * 100 : 0
                ];
            });

        return view('admin.dashboard', compact(
            'verificationNeeded',
            'readyPickupToday',
            'todaysRevenue',
            'recentOrders',
            'menuQuotas'
        ));
    }
}