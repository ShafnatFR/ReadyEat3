<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Main Dashboard with tabs and data
     */
    public function dashboard(Request $request)
    {
        $tab = $request->get('tab', 'dashboard');
        $theme = session('theme', 'light');

        // Fetch all data for all tabs
        $orders = Order::with(['user', 'items.menu'])->orderBy('pickup_date', 'desc')->get();
        $products = Menu::all();

        // Dashboard statistics
        $stats = $this->getDashboardStats($request);

        // Verification tab data
        $verificationData = $this->getVerificationData($request);

        // Production recap data
        $defaultProductionDate = Order::where('status', 'ready_for_pickup')
            ->orderBy('pickup_date', 'desc')
            ->value('pickup_date') ?? Carbon::tomorrow()->format('Y-m-d');
        $productionDate = $request->get('production_date', $defaultProductionDate);
        $productionRecap = $this->getProductionRecap($productionDate);

        // Pickup data
        $pickupOrders = $this->getPickupOrders($request);

        // Customer data
        $customers = $this->getCustomers($request);

        return view('admin.dashboard', compact(
            'tab',
            'theme',
            'orders',
            'products',
            'stats',
            'verificationData',
            'productionRecap',
            'productionDate',
            'pickupOrders',
            'customers'
        ));
    }

    /**
     * Get dashboard statistics with time filtering - FIXED
     */
    private function getDashboardStats(Request $request)
    {
        $filterType = $request->get('filter_type', 'monthly');

        // FIXED: Set proper default date based on filter type
        $defaultDate = match ($filterType) {
            'daily' => Carbon::now()->format('Y-m-d'),
            'weekly' => Carbon::now()->format('o-\WW'),  // ISO-8601 week format
            'yearly' => Carbon::now()->format('Y'),
            default => Carbon::now()->format('Y-m'),
        };

        $selectedDate = $request->get('selected_date', $defaultDate);

        // Filter orders based on date
        $filteredOrders = $this->filterOrdersByDate(Order::with('items.menu')->get(), $filterType, $selectedDate);
        $completedOrders = $filteredOrders->whereIn('status', ['picked_up', 'ready_for_pickup']);

        // Basic stats
        $revenue = $completedOrders->sum('total_price');
        $totalOrders = $filteredOrders->count();
        $uniqueCustomers = $filteredOrders->pluck('customer_phone')->unique()->count();
        $avgOrderValue = $completedOrders->count() > 0 ? $revenue / $completedOrders->count() : 0;

        // Top products
        $topProducts = $this->getTopProducts($filteredOrders);

        // Category distribution
        $categoryDistribution = $this->getCategoryDistribution($filteredOrders);

        // Revenue trend
        $revenueTrend = $this->getRevenueTrend($completedOrders, $filterType, $selectedDate);

        // Top customers
        $topCustomers = $this->getTopCustomers($filteredOrders);

        return [
            'revenue' => $revenue,
            'totalOrders' => $totalOrders,
            'uniqueCustomers' => $uniqueCustomers,
            'avgOrderValue' => $avgOrderValue,
            'topProducts' => $topProducts,
            'categoryDistribution' => $categoryDistribution,
            'revenueTrend' => $revenueTrend,
            'topCustomers' => $topCustomers,
            'filterType' => $filterType,
            'selectedDate' => $selectedDate,
        ];
    }

    /**
     * Filter orders by date - FIXED for weekly bug
     */
    private function filterOrdersByDate($orders, $filterType, $selectedDate)
    {
        return $orders->filter(function ($order) use ($filterType, $selectedDate) {
            $orderDate = Carbon::parse($order->pickup_date);

            if ($filterType === 'daily') {
                return $order->pickup_date === $selectedDate;
            } elseif ($filterType === 'weekly') {
                // FIXED: Safe parsing with validation
                if (strpos($selectedDate, '-W') !== false) {
                    $parts = explode('-W', $selectedDate);
                    if (count($parts) === 2) {
                        list($year, $week) = $parts;
                        return $orderDate->year == $year && $orderDate->week == $week;
                    }
                }
                // Fallback to monthly if format is invalid
                return substr($order->pickup_date, 0, 7) === substr($selectedDate, 0, 7);
            } elseif ($filterType === 'monthly') {
                return substr($order->pickup_date, 0, 7) === $selectedDate;
            } elseif ($filterType === 'yearly') {
                return (string) $orderDate->year === $selectedDate;
            }
            return false;
        });
    }

    private function getTopProducts($orders)
    {
        $productSales = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productId = $item->menu_id;
                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = ['name' => $item->menu->name ?? 'Unknown', 'quantity' => 0];
                }
                $productSales[$productId]['quantity'] += $item->quantity;
            }
        }

        usort($productSales, fn($a, $b) => $b['quantity'] <=> $a['quantity']);
        return array_slice($productSales, 0, 5);
    }

    private function getCategoryDistribution($orders)
    {
        $categorySales = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $category = $item->menu->category ?? 'Unknown';
                if (!isset($categorySales[$category])) {
                    $categorySales[$category] = 0;
                }
                $categorySales[$category] += $item->quantity;
            }
        }

        $result = [];
        foreach ($categorySales as $category => $count) {
            $result[] = ['label' => $category, 'value' => $count];
        }
        return $result;
    }

    private function getRevenueTrend($completedOrders, $filterType, $selectedDate)
    {
        $revenueOverTime = [];

        if ($filterType === 'monthly' && $selectedDate) {
            list($year, $month) = explode('-', $selectedDate);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $revenueOverTime[] = ['label' => (string) $i, 'value' => 0];
            }

            foreach ($completedOrders as $order) {
                $day = (int) Carbon::parse($order->pickup_date)->format('d');
                if ($day >= 1 && $day <= $daysInMonth) {
                    $revenueOverTime[$day - 1]['value'] += $order->total_price;
                }
            }
        } elseif ($filterType === 'yearly' && $selectedDate) {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            foreach ($months as $month) {
                $revenueOverTime[] = ['label' => $month, 'value' => 0];
            }

            foreach ($completedOrders as $order) {
                $month = (int) Carbon::parse($order->pickup_date)->format('n') - 1;
                $revenueOverTime[$month]['value'] += $order->total_price;
            }
        }

        return $revenueOverTime;
    }

    private function getTopCustomers($orders)
    {
        $customerStats = [];
        foreach ($orders as $order) {
            $phone = $order->customer_phone;
            if (!isset($customerStats[$phone])) {
                $customerStats[$phone] = [
                    'name' => $order->customer_name,
                    'phone' => $phone,
                    'orders' => 0,
                    'total' => 0
                ];
            }
            $customerStats[$phone]['orders']++;
            if (in_array($order->status, ['picked_up', 'ready_for_pickup'])) {
                $customerStats[$phone]['total'] += $order->total_price;
            }
        }

        usort($customerStats, fn($a, $b) => $b['total'] <=> $a['total']);
        return array_slice($customerStats, 0, 5);
    }

    private function getVerificationData(Request $request)
    {
        $statusFilter = $request->get('status_filter', 'All Active');
        $dateFilter = $request->get('date_filter', '');
        $perPage = $request->get('per_page', 20);

        $query = Order::with(['user', 'items.menu']);

        if ($statusFilter === 'All Active') {
            $query->whereIn('status', ['payment_pending', 'ready_for_pickup']);
        } elseif ($statusFilter !== 'All') {
            $query->where('status', $statusFilter);
        }

        if ($dateFilter) {
            $query->where('pickup_date', $dateFilter);
        }

        $orders = $query->orderBy('pickup_date', 'desc')->paginate($perPage);

        return [
            'orders' => $orders,
            'statusFilter' => $statusFilter,
            'dateFilter' => $dateFilter,
            'perPage' => $perPage,
        ];
    }

    private function getProductionRecap($date)
    {
        $orders = Order::with('items.menu')
            ->where('pickup_date', $date)
            ->whereIn('status', ['ready_for_pickup'])
            ->get();

        $recap = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $menuId = $item->menu_id;
                if (!isset($recap[$menuId])) {
                    $recap[$menuId] = [
                        'name' => $item->menu->name ?? 'Unknown',
                        'quantity' => 0,
                        'quota' => $item->menu->daily_limit ?? 0,
                    ];
                }
                $recap[$menuId]['quantity'] += $item->quantity;
            }
        }

        return array_values($recap);
    }

    private function getPickupOrders(Request $request)
    {
        $search = $request->get('search', '');
        $period = $request->get('pickup_period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Order::with('items.menu')
            ->where('status', 'ready_for_pickup');

        // Filter berdasarkan Periode
        if ($period === 'week') {
            $query->whereBetween('pickup_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereMonth('pickup_date', Carbon::now()->month)
                ->whereYear('pickup_date', Carbon::now()->year);
        } elseif ($period === 'custom' && $startDate && $endDate) {
            $query->whereBetween('pickup_date', [$startDate, $endDate]);
        } else {
            // Default: 'today'
            $query->whereDate('pickup_date', Carbon::today());
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('invoice_code', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('pickup_date', 'asc')->get();
    }

    /**
     * Get customers - FIXED to show only customers with completed orders
     */
    private function getCustomers(Request $request = null)
    {
        // FIXED: Only get completed orders for data consistency
        $orders = Order::with('items.menu')
            ->whereIn('status', ['picked_up', 'ready_for_pickup'])
            ->get();

        $customerData = [];

        foreach ($orders as $order) {
            $phone = $order->customer_phone;
            if (!isset($customerData[$phone])) {
                $customerData[$phone] = [
                    'phone' => $phone,
                    'name' => $order->customer_name,
                    'totalOrders' => 0,
                    'totalSpent' => 0,
                    'firstSeen' => $order->pickup_date,
                    'orders' => []
                ];
            }

            $customerData[$phone]['totalOrders']++;
            $customerData[$phone]['totalSpent'] += $order->total_price;
            $customerData[$phone]['orders'][] = $order;

            if ($order->pickup_date < $customerData[$phone]['firstSeen']) {
                $customerData[$phone]['firstSeen'] = $order->pickup_date;
            }
        }

        usort($customerData, fn($a, $b) => $b['totalSpent'] <=> $a['totalSpent']);

        $perPage = $request ? $request->get('per_page', 20) : 20;
        $currentPage = $request ? $request->get('page', 1) : 1;
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = array_slice($customerData, $offset, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            count($customerData),
            $perPage,
            $currentPage,
            ['path' => $request ? $request->url() : url()->current(), 'query' => $request ? $request->query() : []]
        );

        return [
            'customers' => $paginator,
            'perPage' => $perPage,
        ];
    }

    public function acceptOrder(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:payment_pending,ready_for_pickup,picked_up,cancelled',
            'pickup_date' => 'required|date',
            'admin_note' => 'nullable|string',
            'payment_proof' => 'nullable|image|max:2048',
        ]);

        $previousStatus = $order->status;

        $order->status = $request->status;
        $order->pickup_date = $request->pickup_date;
        $order->admin_note = $request->admin_note;

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $order->payment_proof = $path;
        }

        $order->save();

        // ENHANCED: Send email if status changed to ready_for_pickup
        if ($previousStatus !== 'ready_for_pickup' && $order->status === 'ready_for_pickup' && $order->user && $order->user->email) {
            try {
                \Mail::to($order->user->email)->send(new \App\Mail\OrderReadyForPickup($order));
                \Log::info('Order ready email sent', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send order ready email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return back()->with('success', 'Order updated successfully.');
    }

    public function rejectOrder(Request $request, Order $order)
    {
        $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $order->status = 'cancelled';
        $order->admin_note = $request->admin_note;
        $order->save();

        return back()->with('success', 'Order rejected.');
    }

    public function uploadPaymentProof(Request $request, Order $order)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');
        $order->payment_proof = $path;
        $order->save();

        return back()->with('success', 'Payment proof uploaded successfully.');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'required|string',
            'category' => 'required|string',
            'daily_limit' => 'required|integer|min:0',
            'is_available' => 'boolean',
        ]);

        Menu::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'image' => $validated['image_url'],
            'category' => $validated['category'],
            'daily_limit' => $validated['daily_limit'],
            'isAvailable' => $validated['is_available'] ?? true,
        ]);

        return back()->with('success', 'Product created successfully.');
    }

    public function updateProduct(Request $request, Menu $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'required|string',
            'category' => 'required|string',
            'daily_limit' => 'required|integer|min:0',
            'is_available' => 'boolean',
        ]);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'image' => $validated['image_url'],
            'category' => $validated['category'],
            'daily_limit' => $validated['daily_limit'],
            'isAvailable' => $validated['is_available'] ?? $product->isAvailable,
        ]);

        return back()->with('success', 'Product updated successfully.');
    }

    public function deleteProduct(Menu $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted successfully.');
    }

    public function toggleProductAvailability(Menu $product)
    {
        $product->isAvailable = !$product->isAvailable;
        $product->save();

        return back()->with('success', 'Product availability updated.');
    }

    public function markAsCompleted(Order $order)
    {
        $order->status = 'picked_up';
        $order->save();

        return back()->with('success', 'Order marked as completed.');
    }

    public function toggleTheme()
    {
        $theme = session('theme', 'light');
        session(['theme' => $theme === 'light' ? 'dark' : 'light']);

        return back();
    }

    /**
     * Bulk approve orders - P2 Enhancement
     */
    public function bulkApproveOrders(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'required|exists:orders,id',
            'status' => 'required|in:ready_for_pickup',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->order_ids as $orderId) {
                try {
                    $order = Order::findOrFail($orderId);

                    $previousStatus = $order->status;
                    $order->status = $request->status;
                    $order->admin_note = $request->admin_note;
                    $order->save();

                    // Send email if status changed to ready_for_pickup
                    if ($previousStatus !== 'ready_for_pickup' && $order->status === 'ready_for_pickup' && $order->user && $order->user->email) {
                        try {
                            \Mail::to($order->user->email)->send(new \App\Mail\OrderReadyForPickup($order));
                        } catch (\Exception $e) {
                            \Log::warning('Bulk approve email failed', ['order_id' => $order->id]);
                        }
                    }

                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Order #{$orderId}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully approved {$updated} order(s).";
            if (count($errors) > 0) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk approve failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Bulk approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Bulk reject orders - P2 Enhancement
     */
    public function bulkRejectOrders(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'required|exists:orders,id',
            'admin_note' => 'required|string|max:500',
        ]);

        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->order_ids as $orderId) {
                try {
                    $order = Order::findOrFail($orderId);
                    $order->status = 'cancelled';
                    $order->admin_note = $request->admin_note;
                    $order->save();
                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Order #{$orderId}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully rejected {$updated} order(s).";
            if (count($errors) > 0) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk reject failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Bulk rejection failed: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update order status - P2 Enhancement
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'required|exists:orders,id',
            'status' => 'required|in:payment_pending,ready_for_pickup,picked_up,cancelled',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->order_ids as $orderId) {
                try {
                    $order = Order::findOrFail($orderId);
                    $order->status = $request->status;
                    if ($request->admin_note) {
                        $order->admin_note = $request->admin_note;
                    }
                    $order->save();
                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Order #{$orderId}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully updated {$updated} order(s) to " . str_replace('_', ' ', $request->status) . ".";
            if (count($errors) > 0) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk status update failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Bulk status update failed: ' . $e->getMessage());
        }
    }
    // ===== PRINT FEATURES =====

    public function printInvoice(Order $order)
    {
        return view('admin.print.invoice', compact('order'));
    }

    public function printProduction(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $productionRecap = $this->getProductionRecap($date);

        return view('admin.print.production', compact('productionRecap', 'date'));
    }

    public function printPickup(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $pickupOrders = Order::with('items.menu')
            ->whereDate('pickup_date', $date)
            ->whereIn('status', ['ready_for_pickup', 'picked_up'])
            ->orderBy('pickup_date', 'asc')
            ->get();

        return view('admin.print.pickup', compact('pickupOrders', 'date'));
    }
}
