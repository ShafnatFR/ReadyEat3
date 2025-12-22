<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class OrderService
{
    /**
     * Create a new order with transaction handling.
     */
    public function createOrder(array $data, array $cart, $paymentProofFile): Order
    {
        return DB::transaction(function () use ($data, $cart, $paymentProofFile) {
            $pickupDate = $data['pickup_date'];

            // Calculate totals
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shipping = config('orders.shipping_fee', 15000);
            $total = $subtotal + $shipping;

            // Create Order Record
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_code' => 'INV-' . strtoupper(uniqid()),
                'status' => 'payment_pending',
                'pickup_date' => $pickupDate,
                'notes' => $data['notes'] ?? null,
                'total_price' => $total,
                'customer_name' => Auth::user()->name,
                'customer_phone' => $data['phone'] ?? Auth::user()->phone ?? '-',
            ]);

            // Process Items with Locking and Quota Check
            foreach ($cart as $id => $details) {
                $this->processOrderItem($order, $id, $details, $pickupDate);
            }

            // Handle File Upload
            if ($paymentProofFile) {
                $this->uploadPaymentProof($order, $paymentProofFile);
            }

            // Log Success
            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'invoice_code' => $order->invoice_code,
                'user_id' => Auth::id(),
                'total' => $total
            ]);

            // Dispatch Email (Silent failure handled in controller or queued job)
            try {
                \Mail::to(Auth::user()->email)->send(new \App\Mail\OrderConfirmation($order));
                Log::info('Order confirmation email sent', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                Log::warning('Failed to send order confirmation email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            return $order;
        });
    }

    /**
     * Process individual order item with quota check/lock.
     */
    protected function processOrderItem(Order $order, int $menuId, array $details, string $pickupDate): void
    {
        // Lock the menu row
        $menu = Menu::where('id', $menuId)->lockForUpdate()->first();

        if (!$menu) {
            throw new Exception("Menu dengan ID {$menuId} tidak ditemukan saat pembuatan order.");
        }

        // Recheck quota inside transaction with lock
        $bookedQtyInTransaction = OrderItem::where('menu_id', $menuId)
            ->whereHas('order', function ($q) use ($pickupDate) {
                $q->whereDate('pickup_date', $pickupDate)
                    ->whereIn('status', ['payment_pending', 'ready_for_pickup']);
            })
            ->lockForUpdate()
            ->sum('quantity');

        $dailyLimit = $menu->daily_limit ?? config('orders.default_daily_limit', 50);

        if (($bookedQtyInTransaction + $details['quantity']) > $dailyLimit) {
            throw new Exception("Race condition detected: Menu '{$menu->name}' sudah penuh saat pembuatan order.");
        }

        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $menuId,
            'quantity' => $details['quantity'],
            'price_at_purchase' => $details['price'],
        ]);
    }

    /**
     * Handle payment proof upload.
     */
    protected function uploadPaymentProof(Order $order, $file): void
    {
        try {
            if (!$file->isValid()) {
                throw new Exception('File upload gagal. File corrupt atau tidak valid.');
            }

            $extension = $file->extension();
            $filename = 'payment_' . $order->id . '_' . uniqid() . '_' . time() . '.' . $extension;
            $path = $file->storeAs('payments', $filename, 'public');

            if (!$path || !Storage::disk('public')->exists($path)) {
                throw new Exception('Gagal menyimpan file bukti pembayaran.');
            }

            $order->payment_proof = $path;
            $order->save();

            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'proof_image' => $path,
                'status' => 'pending',
            ]);

        } catch (\Exception $e) {
            Log::error('Payment proof upload failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw new Exception('Gagal mengupload bukti pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Check quota for validation phase (before transaction).
     */
    public function checkQuotaAvailability(array $cart, string $pickupDate): ?string
    {
        foreach ($cart as $id => $details) {
            $menu = Menu::find($id);
            if (!$menu)
                continue;

            $bookedQty = OrderItem::where('menu_id', $id)
                ->whereHas('order', function ($q) use ($pickupDate) {
                    $q->whereDate('pickup_date', $pickupDate)
                        ->whereIn('status', ['payment_pending', 'ready_for_pickup']);
                })
                ->sum('quantity');

            $dailyLimit = $menu->daily_limit ?? config('orders.default_daily_limit', 50);
            $requestedQty = $details['quantity'];

            if (($bookedQty + $requestedQty) > $dailyLimit) {
                $sisa = max(0, $dailyLimit - $bookedQty);
                return "Menu '{$menu->name}' untuk tanggal tersebut sudah penuh. Kuota tersisa: {$sisa} porsi.";
            }
        }
        return null; // All good
    }
}
