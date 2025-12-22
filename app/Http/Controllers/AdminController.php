<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Main Dashboard with tabs
     */
    public function dashboard(Request $request)
    {
        $tab = $request->get('tab', 'dashboard');

        // Fetch data
        $orders = Order::with(['user', 'items.menu'])->orderBy('pickup_date', 'desc')->get();
        $products = Menu::all();

        // Dashboard statistics
        $stats = $this->getDashboardStats($request);

        // Verification data
        $verificationData = $this->getVerificationData($request);

        // Production recap
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

        $defaultDate = match ($filterType) {
            'daily' => Carbon::now()->format('Y-m-d'),
            'weekly' => Carbon::now()->format('o-\WW'),
            'yearly' => Carbon::now()->format('Y'),
            default => Carbon::now()->format('Y-m'),
        };

        $selectedDate = $request->get('selected_date', $defaultDate);

        $filteredOrders = $this->filterOrdersByDate(Order::with('items.menu')->get(), $filterType, $selectedDate);
        $completedOrders = $filteredOrders->whereIn('status', ['picked_up', 'ready_for_pickup']);

        $revenue = $completedOrders->sum('total_price');
        $totalOrders = $filteredOrders->count();
        $uniqueCustomers = $filteredOrders->pluck('customer_phone')->unique()->count();
        $avgOrderValue = $completedOrders->count() > 0 ? $revenue / $completedOrders->count() : 0;

        return [
            'revenue' => $revenue,
            'totalOrders' => $totalOrders,
            'uniqueCustomers' => $uniqueCustomers,
            'avgOrderValue' => $avgOrderValue,
            'filterType' => $filterType,
            'selectedDate' => $selectedDate,
        ];
    }

    private function filterOrdersByDate($orders, $filterType, $selectedDate)
    {
        return $orders->filter(function ($order) use ($filterType, $selectedDate) {
            $orderDate = Carbon::parse($order->pickup_date);

            if ($filterType === 'daily') {
                return $order->pickup_date === $selectedDate;
            } elseif ($filterType === 'weekly') {
                if (strpos($selectedDate, '-W') !== false) {
                    $parts = explode('-W', $selectedDate);
                    if (count($parts) === 2) {
                        list($year, $week) = $parts;
                        return $orderDate->year == $year && $orderDate->week == $week;
                    }
                }
                return substr($order->pickup_date, 0, 7) === substr($selectedDate, 0, 7);
            } elseif ($filterType === 'monthly') {
                return substr($order->pickup_date, 0, 7) === $selectedDate;
            } elseif ($filterType === 'yearly') {
                return (string) $orderDate->year === $selectedDate;
            }
            return false;
        });
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

        $defaultPickupDate = Order::where('status', 'ready_for_pickup')
            ->orderBy('pickup_date', 'desc')
            ->value('pickup_date') ?? Carbon::today()->format('Y-m-d');
        $pickupDate = $request->get('pickup_date', $defaultPickupDate);

        $query = Order::with('items.menu')
            ->where('status', 'ready_for_pickup')
            ->where('pickup_date', $pickupDate);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('invoice_code', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    private function getCustomers(Request $request = null)
    {
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

    /**
     * Order Management
     */
    public function acceptOrder(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:payment_pending,ready_for_pickup,picked_up,cancelled',
            'pickup_date' => 'required|date',
            'admin_note' => 'nullable|string',
            'payment_proof' => 'nullable|image|max:2048',
        ]);

        $order->status = $request->status;
        $order->pickup_date = $request->pickup_date;
        $order->admin_note = $request->admin_note;

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $order->payment_proof = $path;
        }

        $order->save();

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

        return back()->with('success', 'Payment proof uploaded.');
    }

    /**
     * Product Management
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
            'is_available' => $validated['is_available'] ?? true,
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
            'is_available' => $validated['is_available'] ?? $product->is_available,
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
        $product->is_available = !$product->is_available;
        $product->save();

        return back()->with('success', 'Product availability updated.');
    }

    /**
     * Pickup Management
     */
    public function markAsCompleted(Order $order)
    {
        $order->status = 'picked_up';
        $order->save();

        return back()->with('success', 'Order marked as completed.');
    }

    /**
     * Bulk Operations
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
                    $order->status = $request->status;
                    $order->admin_note = $request->admin_note;
                    $order->save();
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
            return back()->with('error', 'Bulk approval failed: ' . $e->getMessage());
        }
    }

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
            return back()->with('error', 'Bulk rejection failed: ' . $e->getMessage());
        }
    }

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

            $message = "Successfully updated {$updated} order(s).";
            if (count($errors) > 0) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk status update failed: ' . $e->getMessage());
        }
    }

    /**
     * Print Features
     */
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
        $pickupOrders = Order::with(['items.menu', 'user'])
            ->whereDate('pickup_date', $date)
            ->whereIn('status', ['ready_for_pickup', 'picked_up'])
            ->orderBy('pickup_date', 'asc')
            ->get();

        return view('admin.print.pickup', compact('pickupOrders', 'date'));
    }
}
