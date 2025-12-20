<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {}

    /**
     * Show checkout page
     */
    public function create()
    {
        $cart = $this->cartService->getValidatedCart();

        if (empty($cart)) {
            return redirect()->route('menus.index')
                ->with('error', 'Keranjang belanja Anda kosong atau item tidak tersedia. Silakan pilih menu kembali.');
        }

        $totals = $this->cartService->calculateTotals($cart);

        return view('checkout.index', array_merge(['cart' => $cart], $totals));
    }

    /**
     * Process checkout
     */
    public function store(StoreOrderRequest $request)
    {
        $cart = $this->cartService->getValidatedCart();

        if (empty($cart)) {
            return redirect()->route('menus.index')
                ->with('error', 'Keranjang Anda kosong. Transaksi dibatalkan.');
        }

        // Quota check (pre-transaction validation)
        if ($error = $this->orderService->checkQuotaAvailability($cart, $request->pickup_date)) {
            return back()->withErrors(['pickup_date' => $error])->withInput();
        }

        try {
            $order = $this->orderService->createOrder(
                $request->validated(),
                $cart,
                $request->file('payment_proof')
            );

            $this->cartService->clear();

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            // Enhanced Error Logging handled in Service, but we log controller context here
            Log::error('Order checkout failed in controller', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $errorMessage = 'Terjadi kesalahan saat memproses pesanan Anda. ';
            if (str_contains($e->getMessage(), 'upload')) {
                $errorMessage .= 'Masalah pada upload bukti pembayaran.';
            } elseif (str_contains($e->getMessage(), 'penuh')) {
                $errorMessage .= 'Menu yang Anda pilih sudah penuh.';
            } else {
                $errorMessage .= 'Silakan coba lagi.';
            }

            return back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Show success page
     */
    public function success($id)
    {
        try {
            $order = Order::with('items.menu')->findOrFail($id);

            // Authorization check
            if ($order->user_id !== Auth::id()) {
                Log::warning('Unauthorized order access', ['order' => $id, 'user' => Auth::id()]);
                abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
            }

            // Generate WhatsApp confirmation message
            $whatsappUrl = $this->generateWhatsAppUrl($order);

            return view('checkout.success', compact('order', 'whatsappUrl'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('menus.index')->with('error', 'Pesanan tidak ditemukan.');
        }
    }

    /**
     * Generate WhatsApp confirmation URL
     */
    private function generateWhatsAppUrl(Order $order): string
    {
        $phoneNumber = '6285215376975'; // WhatsApp number (with country code, no +)

        // Build order items list
        $itemsList = '';
        foreach ($order->items as $index => $item) {
            $itemsList .= ($index + 1).". {$item->menu->name} ({$item->quantity}x) - Rp ".number_format($item->price_at_purchase * $item->quantity, 0, ',', '.')."\n";
        }

        // Build message
        $message = "🛒 *KONFIRMASI PESANAN READYEAT*\n\n";
        $message .= "📋 *ID Pembelian:* {$order->invoice_code}\n";
        $message .= "👤 *Nama:* {$order->customer_name}\n";
        $message .= '📅 *Tanggal Pickup:* '.\Carbon\Carbon::parse($order->pickup_date)->format('d F Y')."\n\n";

        $message .= "🍽️ *Pesanan:*\n{$itemsList}\n";

        $message .= '💰 *Total Bayar:* Rp '.number_format($order->total_price, 0, ',', '.')."\n\n";

        if ($order->notes) {
            $message .= "📝 *Catatan:* {$order->notes}\n\n";
        }

        $message .= "📷 *PENTING:* Mohon sertakan screenshot bukti pembayaran yang telah Anda upload di sistem.\n\n";
        $message .= 'Terima kasih! 🙏';

        // Encode message for URL
        $encodedMessage = urlencode($message);

        return "https://wa.me/{$phoneNumber}?text={$encodedMessage}";
    }
}
