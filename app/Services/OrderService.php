<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderService
{
    /**
     * Calculate order totals
     */
    public function calculateTotals(array $cart): array
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = 15000;
        $total = $subtotal + $shipping;

        return compact('subtotal', 'shipping', 'total');
    }

    /**
     * Validate cart items
     */
    public function validateCartItems(array $cart): array
    {
        $validCart = [];
        $hasInvalidItems = false;

        foreach ($cart as $id => $details) {
            $menu = Menu::find($id);

            if (!$menu || !$menu->isAvailable) {
                $hasInvalidItems = true;
                continue;
            }

            // Update price if changed
            if ($menu->price != $details['price']) {
                $details['price'] = $menu->price;
            }

            $validCart[$id] = $details;
        }

        return [$validCart, $hasInvalidItems];
    }

    /**
     * Check menu quota availability
     */
    public function checkQuota(int $menuId, string $pickupDate, int $requestedQty): array
    {
        $menu = Menu::find($menuId);

        if (!$menu) {
            return [
                'available' => false,
                'message' => "Menu tidak ditemukan",
                'remaining' => 0
            ];
        }

        $bookedQty = OrderItem::where('menu_id', $menuId)
            ->whereHas('order', function ($q) use ($pickupDate) {
                $q->whereDate('pickup_date', $pickupDate)
                    ->whereIn('status', ['payment_pending', 'ready_for_pickup']);
            })
            ->sum('quantity');

        $dailyLimit = $menu->daily_limit ?? 50;
        $remaining = $dailyLimit - $bookedQty;
        $available = ($bookedQty + $requestedQty) <= $dailyLimit;

        return compact('available', 'remaining', 'menu');
    }

    /**
     * Create order with all related data
     */
    public function createOrder(array $data, array $cart): Order
    {
        DB::beginTransaction();
        try {
            $totals = $this->calculateTotals($cart);

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_code' => 'INV-' . strtoupper(uniqid()),
                'status' => 'payment_pending',
                'pickup_date' => $data['pickup_date'],
                'notes' => $data['notes'] ?? null,
                'total_price' => $totals['total'],
                'customer_name' => Auth::user()->name,
                'customer_phone' => $data['phone'] ?? Auth::user()->phone ?? '-',
            ]);

            // Create order items with quota validation
            foreach ($cart as $id => $details) {
                // Lock menu row
                $menu = Menu::where('id', $id)->lockForUpdate()->first();

                if (!$menu) {
                    throw new \Exception("Menu dengan ID {$id} tidak ditemukan.");
                }

                // Recheck quota inside transaction
                $quotaCheck = $this->checkQuota($id, $data['pickup_date'], $details['quantity']);
                if (!$quotaCheck['available']) {
                    throw new \Exception("Menu '{$menu->name}' sudah penuh. Sisa: {$quotaCheck['remaining']} porsi.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                ]);
            }

            DB::commit();
            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(Order $order, $file): string
    {
        // Validate file
        if (!$file->isValid()) {
            throw new \Exception('File upload gagal. File corrupt atau tidak valid.');
        }

        // Sanitize filename
        $extension = $file->extension();
        $filename = 'payment_' . $order->id . '_' . uniqid() . '_' . time() . '.' . $extension;

        // Store file
        $path = $file->storeAs('payments', $filename, 'public');

        if (!$path || !Storage::disk('public')->exists($path)) {
            throw new \Exception('Gagal menyimpan file bukti pembayaran.');
        }

        // Update order
        $order->payment_proof = $path;
        $order->save();

        // Create payment record
        Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_price,
            'proof_image' => $path,
            'status' => 'pending',
        ]);

        return $path;
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, string $status, ?string $adminNote = null): bool
    {
        $order->status = $status;
        if ($adminNote) {
            $order->admin_note = $adminNote;
        }
        return $order->save();
    }
}
