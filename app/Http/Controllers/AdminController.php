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
        $today = Carbon::today();

        $verificationNeeded = Order::where('status', 'payment_pending')->count();
        $readyPickupToday = Order::whereDate('pickup_date', $today)
            ->where('status', 'ready_for_pickup')
            ->count();
        $todaysRevenue = Order::whereDate('created_at', $today)
            ->whereNotIn('status', ['cancelled', 'unpaid'])
            ->sum('total_price');

        $recentOrders = Order::with('user')
            ->where('status', 'payment_pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $menuQuotas = Menu::where('isAvailable', true)
            ->get()
            ->map(function ($menu) use ($today) {
                $soldQty = \App\Models\OrderItem::where('menu_id', $menu->id)
                    ->whereHas('order', function ($q) use ($today) {
                        $q->whereDate('pickup_date', $today)
                            ->whereNotIn('status', ['cancelled', 'unpaid']);
                    })
                    ->sum('quantity');

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
