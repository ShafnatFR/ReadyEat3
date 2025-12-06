<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function create()
    {
        $cart = session()->get('cart');

        if (!$cart || count($cart) == 0) {
            return redirect()->route('menus.index')->with('error', 'Keranjang kosong!');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = 15000;
        $total = $subtotal + $shipping;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Process checkout
     */
    public function store(Request $request)
    {
        $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'payment_proof' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048',
                'dimensions:min_width=100,min_height=100'
            ],
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        $cart = session()->get('cart');
        $pickupDate = $request->pickup_date;

        // Validate quota
        foreach ($cart as $id => $details) {
            $menu = Menu::find($id);

            $bookedQty = OrderItem::where('menu_id', $id)
                ->whereHas('order', function ($q) use ($pickupDate) {
                    $q->whereDate('pickup_date', $pickupDate)
                        ->where('status', '!=', 'cancelled');
                })
                ->sum('quantity');

            $dailyLimit = $menu->daily_limit ?? 50;

            if (($bookedQty + $details['quantity']) > $dailyLimit) {
                $sisa = max(0, $dailyLimit - $bookedQty);
                return back()->withErrors([
                    'pickup_date' => "Menu '{$menu->name}' penuh. Sisa: {$sisa} porsi."
                ])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shipping = 15000;

            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_code' => 'INV-' . strtoupper(uniqid()),
                'status' => 'payment_pending',
                'pickup_date' => $pickupDate,
                'notes' => $request->notes,
                'total_price' => $subtotal + $shipping,
                'customer_name' => Auth::user()->name,
                'customer_phone' => $request->phone ?? Auth::user()->phone ?? '-',
            ]);

            foreach ($cart as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $id,
                    'quantity' => $details['quantity'],
                    'price_at_purchase' => $details['price'],
                ]);
            }

            if ($request->hasFile('payment_proof')) {
                // Sanitize filename to prevent security issues
                $extension = $request->file('payment_proof')->extension();
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $path = $request->file('payment_proof')->storeAs('payments', $filename, 'public');

                // Save to order
                $order->payment_proof = $path;
                $order->save();

                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $subtotal + $shipping,
                    'proof_image' => $path,
                    'status' => 'pending',
                ]);
            }

            session()->forget('cart');
            DB::commit();

            return redirect()->route('checkout.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error for debugging
            \Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    /**
     * Show success page
     */
    public function success($id)
    {
        $order = Order::with('items.menu')->findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}
