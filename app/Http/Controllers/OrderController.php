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
            'payment_proof' => 'required|image|max:2048',
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
                $path = $request->file('payment_proof')->store('payments', 'public');

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
            return back()->with('error', 'Error: ' . $e->getMessage());
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
