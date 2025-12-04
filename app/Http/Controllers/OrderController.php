<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class OrderController extends Controller
{
    // Tampilkan Halaman Checkout
    public function create()
    {
        // Cek apakah cart ada isinya
        $cart = session()->get('cart');
        if (!$cart || count($cart) == 0) {
            return redirect()->route('menus.index')->with('error', 'Keranjang Anda kosong!');
        }

        // Hitung total untuk ditampilkan di view
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $shipping = $subtotal > 0 ? 15000 : 0;
        $total = $subtotal + $shipping;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    // Proses Simpan Pesanan (INTI SISTEM)
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'payment_proof' => 'required|image|max:2048', // Maksimal 2MB
        ]);

        $cart = session()->get('cart');
        $pickupDate = $request->pickup_date;

        // 2. Validasi Kuota Harian (Lock Quantity Logic)
        foreach ($cart as $id => $details) {
            $menu = Menu::find($id);
            
            // Hitung total yang sudah dipesan orang lain untuk tanggal tersebut
            $bookedQty = OrderItem::where('menu_id', $id)
                ->whereHas('order', function($q) use ($pickupDate) {
                    $q->whereDate('pickup_date', $pickupDate)
                      ->where('status', '!=', 'cancelled');
                })
                ->sum('quantity');

            // Cek Limit
            $dailyLimit = $menu->daily_limit ?? 50; // Default 50 jika null
            if (($bookedQty + $details['quantity']) > $dailyLimit) {
                // Jika over limit, kembalikan user dengan error
                $sisa = max(0, $dailyLimit - $bookedQty);
                return back()->withErrors([
                    'pickup_date' => "Maaf, menu '{$menu->name}' penuh untuk tanggal {$pickupDate}. Sisa kuota: {$sisa} porsi."
                ])->withInput();
            }
        }

        // 3. Mulai Transaksi Database (Biar aman, kalau error rollback semua)
        DB::beginTransaction();
        try {
            // A. Simpan Order Utama
            $subtotal = 0;
            foreach ($cart as $item) { $subtotal += $item['price'] * $item['quantity']; }
            $shipping = 15000; // Flat rate
            
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_code' => 'INV-' . strtoupper(uniqid()),
                'status' => 'payment_pending', // Status awal: Menunggu Verifikasi
                'pickup_date' => $pickupDate,
                'notes' => $request->notes,
                'total_price' => $subtotal + $shipping,
            ]);

            // B. Simpan Item Detail
            foreach ($cart as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $id,
                    'quantity' => $details['quantity'],
                    'price_at_purchase' => $details['price'],
                ]);
            }

            // C. Upload & Simpan Bukti Bayar
            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('payments', 'public');
                
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $subtotal + $shipping,
                    'proof_image' => $path,
                    'status' => 'pending',
                ]);
            }

            // D. Bersihkan Keranjang
            session()->forget('cart');

            DB::commit();

            // Redirect ke halaman sukses
            return redirect()->route('checkout.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function success($id)
    {
        $order = Order::with('items.menu')->findOrFail($id);
        
        // Pastikan user hanya bisa lihat order miliknya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}