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
        $productionDate = $request->get('production_date', Carbon::tomorrow()->format('Y-m-d'));
        $productionRecap = $this->getProductionRecap($productionDate);

        // Pickup data
        $pickupOrders = $this->getPickupOrders($request);

        // Customer data
        $customers = $this->getCustomers();

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
     * Get dashboard statistics
     */
    private function getDashboardStats(Request $request)
    {
        $filterType = $request->get('filter_type', 'monthly');
        $selectedDate = $request->get('selected_date', Carbon::now()->format('Y-m'));

        // Filter orders based on date
        $filteredOrders = $this->filterOrdersByDate(Order::with('items.menu')->get(), $filterType, $selectedDate);
        $completedOrders = $filteredOrders->where('status', 'Completed');

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
     * Filter orders by date
     */
    private function filterOrdersByDate($orders, $filterType, $selectedDate)
    {
        return $orders->filter(function ($order) use ($filterType, $selectedDate) {
            $orderDate = Carbon::parse($order->pickup_date);

            if ($filterType === 'daily') {
                return $order->pickup_date === $selectedDate;
            } elseif ($filterType === 'weekly') {
                list($year, $week) = explode('-W', $selectedDate);
                return $orderDate->year == $year && $orderDate->week == $week;
            } elseif ($filterType === 'monthly') {
                return substr($order->pickup_date, 0, 7) === $selectedDate;
            } elseif ($filterType === 'yearly') {
                return (string) $orderDate->year === $selectedDate;
            }
            return false;
        });
    }

    /**
     * Get top products
     */
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

    /**
     * Get category distribution
     */
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

    /**
     * Get revenue trend
     */
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

    /**
     * Get top customers
     */
    private function getTopCustomers($orders)
    {
        $customerStats = [];
        foreach ($orders as $order) {
            $phone = $order->customer_phone;
            if (!isset($customerStats[$phone])) {
                $customerStats[$phone] = ['name' => $order->customer_name, 'totalSpent' => 0];
            }
            if ($order->status === 'Completed') {
                $customerStats[$phone]['totalSpent'] += $order->total_price;
            }
        }

        usort($customerStats, fn($a, $b) => $b['totalSpent'] <=> $a['totalSpent']);
        $topCustomers = array_slice($customerStats, 0, 5);

        return array_map(fn($c) => ['label' => $c['name'], 'value' => $c['totalSpent']], $topCustomers);
    }

    /**
     * Get verification data
     */
    private function getVerificationData(Request $request)
    {
        $statusFilter = $request->get('status_filter', 'All Active');
        $dateFilter = $request->get('date_filter', '');

        $query = Order::with(['user', 'items.menu']);

        // Status filter
        if ($statusFilter === 'All Active') {
            $query->whereIn('status', ['Pending Verification', 'Preparing', 'Ready for Pickup']);
        } elseif ($statusFilter !== 'All') {
            $query->where('status', $statusFilter);
        }

        // Date filter
        if ($dateFilter) {
            $query->where('pickup_date', $dateFilter);
        }

        $orders = $query->orderBy('pickup_date', 'desc')->get();

        return [
            'orders' => $orders,
            'statusFilter' => $statusFilter,
            'dateFilter' => $dateFilter,
        ];
    }

    /**
     * Get production recap
     */
    private function getProductionRecap($date)
    {
        $orders = Order::with('items.menu')
            ->where('pickup_date', $date)
            ->whereIn('status', ['Preparing', 'Ready for Pickup'])
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

    /**
     * Get pickup orders
     */
    private function getPickupOrders(Request $request)
    {
        $search = $request->get('search', '');
        $pickupDate = $request->get('pickup_date', Carbon::today()->format('Y-m-d'));

        $query = Order::with('items.menu')
            ->where('status', 'Ready for Pickup')
            ->where('pickup_date', $pickupDate);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('invoice_code', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Get customers
     */
    private function getCustomers()
    {
        $orders = Order::with('items.menu')->get();
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
            if ($order->status === 'Completed') {
                $customerData[$phone]['totalSpent'] += $order->total_price;
            }
            $customerData[$phone]['orders'][] = $order;

            if ($order->pickup_date < $customerData[$phone]['firstSeen']) {
                $customerData[$phone]['firstSeen'] = $order->pickup_date;
            }
        }

        usort($customerData, fn($a, $b) => $b['totalSpent'] <=> $a['totalSpent']);
        return $customerData;
    }

    /**
     * Accept order (verify payment)
     */
    public function acceptOrder(Request $request, Order $order)
    {
        $request->validate([
            'pickup_date' => 'required|date',
            'admin_note' => 'nullable|string',
            'payment_proof' => 'nullable|image|max:2048',
        ]);

        $order->status = 'Preparing';
        $order->pickup_date = $request->pickup_date;
        $order->admin_note = $request->admin_note;

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $order->payment_proof = $path;
        }

        $order->save();

        return back()->with('success', 'Order accepted and moved to preparing status.');
    }

    /**
     * Reject order
     */
    public function rejectOrder(Request $request, Order $order)
    {
        $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $order->status = 'Rejected';
        $order->admin_note = $request->admin_note;
        $order->save();

        return back()->with('success', 'Order rejected.');
    }

    /**
     * Upload payment proof
     */
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

    /**
     * Store product
     */
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

    /**
     * Update product
     */
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

    /**
     * Delete product
     */
    public function deleteProduct(Menu $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product availability
     */
    public function toggleProductAvailability(Menu $product)
    {
        $product->isAvailable = !$product->isAvailable;
        $product->save();

        return back()->with('success', 'Product availability updated.');
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted(Order $order)
    {
        $order->status = 'Completed';
        $order->save();

        return back()->with('success', 'Order marked as completed.');
    }

    /**
     * Toggle dark mode
     */
    public function toggleTheme()
    {
        $theme = session('theme', 'light');
        session(['theme' => $theme === 'light' ? 'dark' : 'light']);

        return back();
    }
}
