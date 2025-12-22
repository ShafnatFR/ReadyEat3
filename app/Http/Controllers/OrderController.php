<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function create()
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk melanjutkan checkout.');
        }

        $cart = session()->get('cart');

        if (! $cart || ! is_array($cart) || count($cart) == 0) {
            return redirect()->route('menus.index')
                ->with('error', 'Keranjang belanja Anda kosong.');
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = 5000;
        $total = $subtotal + $shipping;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Store order
     */
    public function store(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $validated = $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'payment_proof' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'phone' => 'nullable|string|min:10|max:15',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart');

        if (! $cart || ! is_array($cart) || count($cart) == 0) {
            return back()
                ->with('error', 'Keranjang Anda kosong.');
        }

        DB::beginTransaction();
        try {
            // Calculate total
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shipping = 5000;
            $total = $subtotal + $shipping;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_code' => 'INV-'.strtoupper(uniqid()),
                'status' => 'payment_pending',
                'pickup_date' => $validated['pickup_date'],
                'notes' => $validated['notes'] ?? null,
                'total_price' => $total,
                'customer_name' => Auth::user()->name,
                'customer_phone' => $validated['phone'] ?? Auth::user()->phone ?? '-',
            ]);

            // Create order items
            foreach ($cart as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $id,
                    'quantity' => $details['quantity'],
                    'price_at_purchase' => $details['price'],
                ]);
            }

            // Upload payment proof
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = 'payment_'.$order->id.'_'.time().'.'.$file->extension();
                $path = $file->storeAs('payments', $filename, 'public');

                $order->payment_proof = $path;
                $order->save();

                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $total,
                    'proof_image' => $path,
                    'status' => 'pending',
                ]);
            }

            // Clear cart
            session()->forget('cart');

            DB::commit();

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat memproses pesanan Anda: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show success page
     */
    public function success($id)
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login untuk melihat detail pesanan.');
        }

        try {
            $order = Order::with('items.menu')->findOrFail($id);

            if ($order->user_id !== Auth::id()) {
                abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
            }

            // Generate WhatsApp confirmation URL
            $whatsappUrl = $this->generateAdminWhatsAppUrl($order);

            return view('checkout.success', compact('order', 'whatsappUrl'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('menus.index')
                ->with('error', 'Pesanan tidak ditemukan.');
        }
    }

    /**
     * Generate WhatsApp URL for admin confirmation
     */
    private function generateAdminWhatsAppUrl(Order $order)
    {
        $phoneNumber = '+6285215376975';

        // Build order items list
        $itemsList = '';
        $counter = 1;
        foreach ($order->items as $item) {
            $itemsList .= $counter.'. '.$item->menu->name.' ('.$item->quantity.'x) - Rp '.number_format($item->price_at_purchase * $item->quantity, 0, ',', '.')."\n";
            $counter++;
        }

        // Format pickup date
        $pickupDate = \Carbon\Carbon::parse($order->pickup_date)->format('d F Y');

        // Build message (plain text, no emoji, struk style with separators)
        $separator = "================================\n";
        $line = "------------------------------------------------\n";

        $message = $separator;
        $message .= "KONFIRMASI PESANAN READYEAT\n";
        $message .= $separator;
        $message .= "\n";
        $message .= "ID Pembelian: {$order->invoice_code}\n";
        $message .= "Nama: {$order->customer_name}\n";
        $message .= "Tanggal Pickup: {$pickupDate}\n";
        $message .= "\n";
        $message .= $line;
        $message .= "Pesanan:\n";
        $message .= $line;
        $message .= $itemsList;
        $message .= $line;
        $message .= 'Total Bayar: Rp '.number_format($order->total_price, 0, ',', '.')."\n";
        $message .= $separator;
        $message .= "\n";
        $message .= "PENTING: Mohon sertakan screenshot bukti pembayaran yang telah Anda upload di sistem.\n";
        $message .= "\n";
        $message .= 'Terima kasih!';

        // Encode for URL
        $encodedMessage = urlencode($message);

        // Return WhatsApp URL
        return "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
    }
}
